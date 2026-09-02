<?php
/**
 * API Bootstrap — shared headers, CORS, OPTIONS handling and helpers.
 *
 * Each API entry point requires this file once. It centralises:
 *   - Content-Type / CORS headers
 *   - Preflight (OPTIONS) short-circuit
 *   - JSON response helpers
 *   - Input readers (GET / POST / JSON body)
 *   - Security helpers (headers, input hardening, HMAC, API keys)
 *   - Rate limiting (sliding-window, file-based, no Redis required)
 *
 * Designed for backward compatibility with the existing
 * /api/<tool>/index.php endpoints — the on-the-wire format
 * (Content-Type, status codes, JSON shape) is unchanged.
 *
 * Security is opt-in: existing endpoints that only call
 * api_send_headers()/api_handle_preflight() continue to work
 * exactly as before. To enable rate limiting for an endpoint,
 * call api_rate_limit('api:<name>') after the preflight check.
 */

declare(strict_types=1);

require_once __DIR__ . '/security/RateLimiter.php';
require_once __DIR__ . '/security/Security.php';

/**
 * Send CORS + content-type headers, plus the common security
 * headers. Idempotent — safe to call multiple times.
 *
 * @param string $contentType Default: application/json
 */
function api_send_headers(string $contentType = 'application/json; charset=UTF-8'): void
{
    header('Content-Type: ' . $contentType);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-API-Key, X-Signature, Authorization');
    // Defensive headers — the web server already emits X-Frame-Options
    // and similar, but emitting them here ensures shared-hosting /
    // built-in PHP server installs are equally protected.
    Security::sendHeaders();
}

/**
 * Handle CORS preflight requests. Returns true if the request was
 * a preflight and was answered here, false otherwise.
 *
 * Callers should normally abort with `exit` when this returns true.
 */
function api_handle_preflight(): bool
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'OPTIONS') {
        return false;
    }

    // 204 is the canonical "no content" preflight response, but some
    // existing endpoints historically used 200. We keep 200 here to
    // preserve the previous behaviour.
    http_response_code(200);
    return true;
}

/**
 * Read the current HTTP method in a normalised form.
 */
function api_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

/**
 * Decode the JSON request body once per request.
 *
 * @return array<string,mixed>
 */
function api_json_body(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $raw = file_get_contents('php://input');
    if (!is_string($raw) || $raw === '') {
        return $cache = [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $cache = [];
    }
    return $cache = $decoded;
}

/**
 * Return a request value by key. Looks at:
 *   1. JSON body (for POST)
 *   2. $_POST
 *   3. $_GET
 *
 * @param string $key
 * @param mixed  $default
 */
function api_input(string $key, $default = null)
{
    $body = api_json_body();
    if (array_key_exists($key, $body)) {
        return $body[$key];
    }
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}

/**
 * Emit a JSON success response and terminate the script.
 *
 * @param mixed                $data       Payload to encode
 * @param int                  $statusCode HTTP status code
 * @param array<string,mixed>  $extra      Extra top-level fields to merge in
 */
function api_json($data, int $statusCode = 200, array $extra = []): void
{
    if ($extra) {
        if (is_array($data)) {
            $data = array_merge($data, $extra);
        } else {
            $payload = ['data' => $data];
            $payload = array_merge($payload, $extra);
            $data = $payload;
        }
    }

    http_response_code($statusCode);
    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

/**
 * Emit a JSON error response and terminate the script.
 *
 * @param string               $message
 * @param int                  $statusCode
 * @param array<string,mixed>  $extra
 */
function api_error(string $message, int $statusCode = 400, array $extra = []): void
{
    api_json(
        array_merge(['success' => false, 'error' => $message], $extra),
        $statusCode
    );
}

/**
 * Cast a value to int with a default fallback.
 */
function api_int($value, int $default = 0): int
{
    if (is_numeric($value)) {
        return (int) $value;
    }
    return $default;
}

/**
 * Cast a value to bool — accepts the usual truthy strings.
 */
function api_bool($value, bool $default = false): bool
{
    if (is_bool($value)) {
        return $value;
    }
    if ($value === null || $value === '') {
        return $default;
    }
    if (is_string($value)) {
        $v = strtolower(trim($value));
        if (in_array($v, ['1', 'true', 'yes', 'on'], true)) {
            return true;
        }
        if (in_array($v, ['0', 'false', 'no', 'off'], true)) {
            return false;
        }
    }
    return (bool) $value;
}

/**
 * Register a global exception/error handler that converts PHP fatals
 * into a JSON response, so APIs never leak HTML stack traces.
 */
function api_register_exception_handler(): void
{
    set_exception_handler(function (Throwable $e): void {
        error_log('[api] Uncaught exception: ' . $e->getMessage());
        api_error('Internal server error: ' . $e->getMessage(), 500);
    });
}

// =====================================================================
// Security layer — rate limit + input hardening
// =====================================================================
//
// These helpers are no-ops until the first call to
// `api_security_init()` (typically from api_config.php or the
// endpoint itself). Once initialised they configure the
// RateLimiter with sensible defaults that match this stack.
//
// Per-tool limits can be tuned via `api_rate_limit()` or via the
// `api_config.php` file that each endpoint may include.

/**
 * Initialise the security layer. Safe to call multiple times —
 * only the first call has effect.
 *
 * @param array<string,mixed> $overrides  Per-call overrides merged
 *                                        over the defaults / config.
 */
function api_security_init(array $overrides = []): void
{
    static $bootstrapped = false;
    if ($bootstrapped) {
        return;
    }
    $bootstrapped = true;

    $defaults = [
        'enabled'             => filter_var(getenv('SECURITY_ENABLED') ?: 'true', FILTER_VALIDATE_BOOLEAN),
        'storage_dir'         => getenv('RATELIMIT_STORAGE_DIR') ?: null,
        'trust_cf_connecting' => filter_var(getenv('TRUST_CF_CONNECTING_IP') ?: 'false', FILTER_VALIDATE_BOOLEAN),
        'trust_x_forwarded'   => filter_var(getenv('TRUST_X_FORWARDED_FOR') ?: 'false', FILTER_VALIDATE_BOOLEAN),
        'proxies'             => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) (getenv('TRUSTED_PROXIES') ?: ''))
        ))),
        'default_policy'      => [
            'limit'  => (int) (getenv('RATELIMIT_DEFAULT_LIMIT') ?: 60),
            'window' => (int) (getenv('RATELIMIT_DEFAULT_WINDOW') ?: 60),
        ],
        'global_policy'       => [
            'limit'  => (int) (getenv('RATELIMIT_GLOBAL_LIMIT') ?: 0),
            'window' => (int) (getenv('RATELIMIT_GLOBAL_WINDOW') ?: 60),
        ],
        'fail_policy'         => [
            'fail_limit' => (int) (getenv('SECURITY_FAIL_LIMIT') ?: 10),
            'ban_window' => (int) (getenv('SECURITY_BAN_WINDOW') ?: 300),
        ],
        'policies'   => [],
        'blacklist'  => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) (getenv('SECURITY_BLACKLIST') ?: ''))
        ))),
        'whitelist'  => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) (getenv('SECURITY_WHITELIST') ?: ''))
        ))),
    ];

    RateLimiter::configure(array_replace_recursive($defaults, $overrides));
}

/**
 * Convenience: enforce a rate limit for a named bucket. Returns
 * true when the request is allowed; emits a 429 and exits when it
 * is not. Also forwards X-RateLimit-* headers to the client.
 *
 * @param string $bucket  e.g. "api:password-generator"
 * @param array{limit?:int,window?:int}|null $policy Override for this call
 */
function api_rate_limit(string $bucket, ?array $policy = null): bool
{
    api_security_init();

    if ($policy !== null) {
        RateLimiter::policy($bucket, $policy);
    }

    $apiKey = Security::readApiKey();
    $identity = $apiKey !== null ? 'key:' . $apiKey : null;

    if (!RateLimiter::hit($bucket, $identity)) {
        RateLimiter::sendLimitResponse();
        // sendLimitResponse() already calls exit, but be explicit.
        exit;
    }

    // Allowed — emit headers so well-behaved clients can self-throttle
    RateLimiter::sendHeaders();
    return true;
}

/**
 * Mark the current request as a failure. Useful for endpoints
 * that want to trigger the auto-ban on repeated bad requests.
 */
function api_rate_limit_fail(string $bucket): void
{
    api_security_init();
    $apiKey = Security::readApiKey();
    $identity = $apiKey !== null ? 'key:' . $apiKey : null;
    RateLimiter::fail($bucket, $identity);
}

/**
 * Validate the JSON body against size/depth/content rules.
 * Returns the decoded array, or null on failure.
 *
 * @return array<string,mixed>|null
 */
function api_safe_json_body(int $maxBytes = 65536, int $maxDepth = 8): ?array
{
    return Security::enforceJson($maxBytes, $maxDepth);
}

/**
 * Verify an HMAC signature carried in `X-Signature`. Returns true
 * when valid (or when no signature is required / no key is set).
 *
 * When the endpoint requires a signature but none is supplied,
 * this returns false and the caller should emit a 401.
 */
function api_verify_signature(string $secret, ?string $algo = 'sha256', bool $required = false): bool
{
    $sig = $_SERVER['HTTP_X_SIGNATURE'] ?? null;
    if (!is_string($sig) || $sig === '') {
        return !$required;
    }
    $payload = file_get_contents('php://input') ?: '';
    return Security::verifyHmac($payload, $secret, $sig, $algo ?? 'sha256');
}

/**
 * Send a 401 response for missing / invalid authentication.
 */
function api_unauthorized(string $reason = 'Unauthorized'): void
{
    api_json(['success' => false, 'error' => $reason], 401);
    exit;
}