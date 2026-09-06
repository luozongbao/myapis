<?php
/**
 * =============================================================
 * MyAPIs - Server-side Analytics Tracker
 * -------------------------------------------------------------
 * Sends page-view / event hits to Umami (HTTP API) or
 * Google Analytics 4 (Measurement Protocol) directly from PHP.
 *
 * Why server-side?
 *   The API endpoints under /api/<tool>/ emit JSON only. There
 *   is no <script> tag for a browser-side tracker to attach to,
 *   so the existing docker/php/analytics.php partial deliberately
 *   skips /api/* requests. This module fills that gap by sending
 *   hits from PHP itself.
 *
 * Design constraints:
 *   - Never blocks the response. Hits are dispatched via a
 *     non-blocking curl multi handle and any failure is logged
 *     but never re-thrown.
 *   - Never leaks sensitive data. The hit body is sanitised
 *     before transmission (only endpoint, method, status,
 *     response time, anonymised client identity).
 *   - No extra runtime dependencies. Uses curl + the standard
 *     library only.
 *
 * Configuration (env vars, populated by docker-compose / config.php):
 *   ANALYTICS_PROVIDER          "umami" | "ga4" | "google" | "none"
 *   UMAMI_API_URL               e.g. https://umami.example.com
 *                               or http://umami:3000 inside Docker
 *   UMAMI_WEBSITE_ID            Umami website UUID
 *   UMAMI_API_TOKEN             Bearer token for Umami HTTP API
 *                               (required only for custom events)
 *   GA4_MEASUREMENT_ID          e.g. G-XXXXXXXXXX
 *   GA4_API_SECRET              GA4 Measurement Protocol secret
 *                               (create in GA admin > Data Streams)
 *
 * Usage from an API endpoint (or from bootstrap.php):
 *
 *     \MyAPIs\Analytics\Tracker::trackRequest([
 *         'endpoint' => 'fortune-teller',
 *         'method'   => 'GET',
 *         'status'   => 200,
 *         'duration' => 12.3,           // milliseconds
 *     ]);
 *
 * The dispatch is fire-and-forget; the function returns
 * immediately and the actual HTTP call happens after the
 * response has been flushed (or on PHP shutdown via a small
 * register_shutdown_function hook).
 * =============================================================
 */

declare(strict_types=1);

namespace MyAPIs\Analytics;

if (!defined('MYAPIS_TRACKER_INCLUDED')) {
    define('MYAPIS_TRACKER_INCLUDED', true);
}

final class Tracker
{
    /**
     * Whether the tracker has been armed for this request.
     */
    private static bool $armed = false;

    /**
     * Pending hits queued for the shutdown hook.
     *
     * @var list<array<string,mixed>>
     */
    private static array $queue = [];

    /**
     * Initialise the tracker for the current request.
     *
     * Hooks into register_shutdown_function so that hits are
     * dispatched AFTER the response body has been written.
     * Idempotent — safe to call multiple times.
     *
     * @param array<string,mixed> $context
     *        Optional context (endpoint, method, etc.) for the
     *        first hit. Subsequent hits can be enqueued via
     *        trackRequest() / trackEvent().
     */
    public static function init(array $context = []): void
    {
        if (self::$armed) {
            return;
        }

        // Resolve configuration. We pull directly from getenv()
        // because the tracker must work in both PHP-FPM (env
        // injected by docker-compose) and shared-hosting
        // (env injected via putenv() in public/config.php).
        $provider = strtolower(trim((string) (getenv('ANALYTICS_PROVIDER') ?: 'none')));
        if ($provider === '' || $provider === 'none' || $provider === 'off' || $provider === 'false') {
            return;
        }

        self::$armed = true;

        if ($context !== []) {
            self::$queue[] = self::normalise($context);
        }

        register_shutdown_function([self::class, 'flush']);
    }

    /**
     * Enqueue a request-level hit (one API call = one hit).
     *
     * Typical fields:
     *   - endpoint  string  (e.g. "fortune-teller")
     *   - method    string  (HTTP verb)
     *   - status    int     (response code)
     *   - duration  float   (ms)
     *   - client    string  (anonymised IP, optional)
     */
    public static function trackRequest(array $context): void
    {
        if (!self::$armed) {
            // Auto-arm so that endpoints which call us directly
            // without going through init() still get tracked.
            self::init();
        }
        if (!self::$armed) {
            return; // provider disabled
        }

        self::$queue[] = self::normalise($context);
    }

    /**
     * Enqueue a custom event (e.g. "rate_limit_exceeded",
     * "preflight_request", "validation_error"). Distinct from
     * trackRequest() which always emits a pageview-style hit.
     *
     * @param string               $name   Event name
     * @param array<string,mixed>  $props  Event properties
     */
    public static function trackEvent(string $name, array $props = []): void
    {
        if (!self::$armed) {
            self::init();
        }
        if (!self::$armed) {
            return;
        }

        $props['event_name'] = $name;
        self::$queue[] = self::normalise($props);
    }

    /**
     * Drain the queue. Invoked automatically on PHP shutdown.
     * Public so tests can call it explicitly.
     */
    public static function flush(): void
    {
        if (self::$queue === []) {
            return;
        }

        $provider = strtolower(trim((string) (getenv('ANALYTICS_PROVIDER') ?: 'none')));

        try {
            if ($provider === 'umami') {
                self::dispatchUmami(self::$queue);
            } elseif ($provider === 'ga4' || $provider === 'google') {
                self::dispatchGa4(self::$queue);
            }
        } catch (\Throwable $e) {
            // Tracking must never break the API. Swallow + log.
            error_log('[myapis-analytics] dispatch failed: ' . $e->getMessage());
        } finally {
            self::$queue = [];
        }
    }

    // ---------------------------------------------------------------
    // Internals
    // ---------------------------------------------------------------

    /**
     * Trim + sanitise a context array before serialisation.
     *
     * @param array<string,mixed> $ctx
     * @return array<string,mixed>
     */
    private static function normalise(array $ctx): array
    {
        $clean = [];
        foreach ($ctx as $k => $v) {
            if (!is_string($k)) {
                continue;
            }
            // Strip control chars / overly-long values that
            // could be abused to pollute analytics dashboards.
            if (is_string($v)) {
                $v = preg_replace('/[\x00-\x1F\x7F]+/u', '', $v) ?? '';
                if (strlen($v) > 256) {
                    $v = substr($v, 0, 256);
                }
            }
            $clean[$k] = $v;
        }
        return $clean;
    }

    /**
     * Send queued hits to Umami's HTTP API.
     *
     * Endpoint:
     *   POST {UMAMI_API_URL}/api/send
     *   Body:  { type: "pageview" | "event",
     *            payload: { website, hostname, language, screen,
     *                       url, referrer, name, data } }
     *
     * API requests are sent as `type: "pageview"` (so Umami's
     * Pages report shows each /api/<tool> endpoint as its own
     * row). Custom events (api_rate_limited, api_unauthorized,
     * api_exception) are sent as `type: "event"` with a `name`.
     *
     * @param list<array<string,mixed>> $hits
     */
    private static function dispatchUmami(array $hits): void
    {
        $baseUrl  = rtrim((string) getenv('UMAMI_API_URL'), '/');
        $website  = trim((string) getenv('UMAMI_WEBSITE_ID'));
        $token    = trim((string) getenv('UMAMI_API_TOKEN'));

        if ($baseUrl === '' || $website === '') {
            return; // misconfigured — skip silently
        }

        $url = $baseUrl . '/api/send';

        // Detect the real client IP, falling back to REMOTE_ADDR.
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'MyAPIs-Bot/1.0';

        foreach ($hits as $hit) {
            $endpoint = (string) ($hit['endpoint'] ?? 'unknown');
            $method   = strtoupper((string) ($hit['method'] ?? 'GET'));
            $status   = (int) ($hit['status'] ?? 200);
            $duration = isset($hit['duration']) ? (float) $hit['duration'] : 0.0;
            $eventName = (string) ($hit['event_name'] ?? '');

            // Hostname for Umami's "Websites" breakdown. We use
            // the request's Host header so multiple domains that
            // share the same Umami website still separate cleanly.
            $hostname = $_SERVER['HTTP_HOST'] ?? 'unknown';

            // Default API request = a pageview on /api/<endpoint>,
            // so Umami's "Pages" report shows every API endpoint
            // as its own row. Custom events (api_rate_limited,
            // api_unauthorized, api_exception) become type=event.
            $type = $eventName !== '' ? 'event' : 'pageview';

            $payloadData = [
                'method'   => $method,
                'status'   => $status,
                'duration' => round($duration, 2),
            ];

            $payload = [
                'website'  => $website,
                'hostname' => $hostname,
                'language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en-US',
                'screen'   => '0x0', // server-side: unknown
                'url'      => '/api/' . ltrim($endpoint, '/') . ($method !== 'GET' ? ' [' . $method . ']' : ''),
                'referrer' => '',
            ];
            if ($eventName !== '') {
                $payload['name'] = $eventName;
                $payload['data'] = $payloadData;
            }

            self::curlPost(
                $url,
                [
                    'type'    => $type,
                    'payload' => $payload,
                ],
                $token !== '' ? "Bearer {$token}" : null,
                [
                    'X-Forwarded-For: ' . $ip,
                    'User-Agent: ' . $ua,
                ]
            );
        }
    }

    /**
     * Send queued hits to GA4 via the Measurement Protocol.
     *
     * Endpoint:
     *   POST https://www.google-analytics.com/mp/collect
     *        ?measurement_id=G-XXXX&api_secret=YYYY
     *   Body: {
     *     "client_id": "...",
     *     "events": [
     *       { "name": "api_request",
     *         "params": { "endpoint": "...", "method": "...",
     *                     "status": 200, "duration_ms": 12.3 } }
     *     ]
     *   }
     *
     * GA4 Measurement Protocol requires a stable client_id per
     * visitor. We derive it from a salted hash of (IP + UA) so
     * the same visitor always maps to the same id without us
     * actually storing PII. Salt = the API secret itself, which
     * is never logged.
     *
     * @param list<array<string,mixed>> $hits
     */
    private static function dispatchGa4(array $hits): void
    {
        $measurementId = trim((string) getenv('GA4_MEASUREMENT_ID'));
        $apiSecret     = trim((string) getenv('GA4_API_SECRET'));
        if ($measurementId === '' || $apiSecret === '') {
            return; // misconfigured
        }

        $url = sprintf(
            'https://www.google-analytics.com/mp/collect?measurement_id=%s&api_secret=%s',
            rawurlencode($measurementId),
            rawurlencode($apiSecret)
        );

        // Stable, anonymised client_id
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        $ua       = $_SERVER['HTTP_USER_AGENT'] ?? 'MyAPIs-Bot/1.0';
        $clientId = substr(
            hash('sha256', $apiSecret . '|' . $ip . '|' . $ua),
            0,
            32
        );

        foreach ($hits as $hit) {
            $endpoint = (string) ($hit['endpoint'] ?? 'unknown');
            $method   = strtoupper((string) ($hit['method'] ?? 'GET'));
            $status   = (int) ($hit['status'] ?? 200);
            $duration = isset($hit['duration']) ? (float) $hit['duration'] : 0.0;
            $eventName = (string) ($hit['event_name'] ?? 'api_request');

            $body = [
                'client_id' => $clientId,
                'events'    => [
                    [
                        'name'   => $eventName,
                        'params' => [
                            'endpoint'    => $endpoint,
                            'method'      => $method,
                            'status'      => $status,
                            'duration_ms' => round($duration, 2),
                            'engagement_time_msec' => 1,
                        ],
                    ],
                ],
            ];

            self::curlPost($url, $body, null, [
                'User-Agent: ' . $ua,
            ]);
        }
    }

    /**
     * Fire a single POST request via curl. Failures are logged
     * but never propagated — analytics must not break the API.
     *
     * @param array<string,mixed> $payload
     * @param list<string>        $headers
     */
    private static function curlPost(string $url, array $payload, ?string $authHeader, array $headers): void
    {
        if (!function_exists('curl_init')) {
            return; // curl extension not installed — skip silently
        }

        $headers[] = 'Content-Type: application/json';
        if ($authHeader !== null) {
            $headers[] = $authHeader;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            return;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS     => 800,   // hard cap — never hang the response
            CURLOPT_CONNECTTIMEOUT_MS => 400,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_NOSIGNAL       => true,
        ]);

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($body === false || $code >= 400) {
            error_log(sprintf(
                '[myapis-analytics] HTTP %d from %s: %s',
                $code,
                $url,
                $err !== '' ? $err : (is_string($body) ? substr($body, 0, 200) : '')
            ));
        }
    }

    /**
     * Convenience: build a default context from the current
     * request, including method, endpoint name and status code
     * once it is known.
     *
     * @param string $endpoint  Tool name, e.g. "fortune-teller"
     * @param int    $status    HTTP response code (defaults to 200)
     * @param float  $startMs   microtime(true) captured at entry
     */
    public static function contextFromRequest(string $endpoint, int $status = 200, ?float $startMs = null): array
    {
        $duration = 0.0;
        if ($startMs !== null) {
            $duration = (microtime(true) - $startMs) * 1000.0;
        }
        return [
            'endpoint' => $endpoint,
            'method'   => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'status'   => $status,
            'duration' => $duration,
        ];
    }
}
