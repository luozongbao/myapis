<?php
/**
 * =============================================================
 * MyAPIs — Rate Limiter (file-based, no Composer required)
 * =============================================================
 *
 * Implements a sliding-window counter per (bucket, identity) pair.
 * State is persisted in a small JSON file on local disk so the
 * limiter works on shared-hosting / Docker / single-VPS setups
 * without needing Redis or Memcached.
 *
 * Features
 * --------
 *  - Sliding window with millisecond resolution
 *  - Per-IP, per-API-key and per-route buckets
 *  - Automatic file expiration (TTL) to keep storage small
 *  - Atomic-ish writes via LOCK_EX + rename-on-commit
 *  - Optional blacklist / whitelist
 *  - Returns retry-after and X-RateLimit-* headers
 *
 * Storage layout
 * --------------
 *   <storage_dir>/
 *     bucket__<sha1>.json
 *
 * Each file contains:
 *   {
 *     "window_start": float,        // ms timestamp of first hit
 *     "hits": [float, ...],         // ms timestamps inside the window
 *     "fails": int,                 // consecutive failed requests
 *     "banned_until": float|null    // ms timestamp, null if not banned
 *   }
 *
 * The whole file is rewritten on every hit. Because the files
 * are tiny (a few KB at most) and we use flock(), this is fast
 * enough for the modest load MyAPIs targets. For high-traffic
 * deployments swap the storage backend for Redis (see the
 * `swapStorage()` helper).
 *
 * Configuration
 * -------------
 *   RateLimiter::configure([
 *       'storage_dir' => '/var/www/html/storage/ratelimit',
 *       'default_policy' => ['limit' => 60, 'window' => 60], // 60 req/min
 *   ]);
 *
 *   if (!RateLimiter::hit('api:password-generator')) {
 *       RateLimiter::sendLimitResponse();
 *       exit;
 *   }
 *
 * @version 1.0.0
 */

declare(strict_types=1);

final class RateLimiter
{
    /**
     * Active configuration. Each key is documented inline.
     *
     * @var array<string,mixed>
     */
    private static array $config = [
        'storage_dir'         => null,        // auto-detected if null
        'default_policy'      => [
            'limit'  => 60,                  // requests per window
            'window' => 60,                  // window length in seconds
        ],
        'global_policy'       => [
            'limit'  => 0,                   // 0 = disabled
            'window' => 60,
        ],
        'fail_policy'         => [
            'fail_limit' => 10,              // # of failures before ban
            'ban_window' => 300,             // ban duration in seconds
        ],
        'trust_cf_connecting' => false,       // trust CF-Connecting-IP header
        'trust_x_forwarded'   => false,       // trust X-Forwarded-For header
        'proxies'             => [],          // explicit trusted proxy IPs
        'enabled'             => true,        // master kill-switch
    ];

    /**
     * Per-route named policies. Routes not listed fall back to
     * default_policy. Pass `'*'` to set a wildcard default.
     *
     * Format:
     *   'api:password-generator' => ['limit' => 30, 'window' => 60],
     *
     * @var array<string, array{limit:int,window:int}>
     */
    private static array $policies = [];

    /**
     * Permanently blacklisted identities — IPs or API keys.
     *
     * @var array<string, true>
     */
    private static array $blacklist = [];

    /**
     * Permanently whitelisted identities — bypass all limits.
     *
     * @var array<string, true>
     */
    private static array $whitelist = [];

    /**
     * Last decision, so HTTP layer can render headers.
     *
     * @var array<string,int>|null
     */
    private static ?array $lastDecision = null;

    // -----------------------------------------------------------------
    // Configuration helpers
    // -----------------------------------------------------------------

    /**
     * Merge user-supplied configuration over the defaults.
     *
     * @param array<string,mixed> $config
     */
    public static function configure(array $config): void
    {
        foreach ($config as $k => $v) {
            if ($k === 'policies' && is_array($v)) {
                foreach ($v as $name => $policy) {
                    self::$policies[$name] = $policy;
                }
                continue;
            }
            if ($k === 'blacklist' && is_array($v)) {
                foreach ($v as $id) {
                    self::$blacklist[(string) $id] = true;
                }
                continue;
            }
            if ($k === 'whitelist' && is_array($v)) {
                foreach ($v as $id) {
                    self::$whitelist[(string) $id] = true;
                }
                continue;
            }
            self::$config[$k] = $v;
        }

        if (empty(self::$config['storage_dir'])) {
            self::$config['storage_dir'] = self::detectStorageDir();
        }
    }

    /**
     * Register a named policy for one or more routes.
     *
     * @param string|array<string> $routes
     * @param array{limit:int,window:int} $policy
     */
    public static function policy($routes, array $policy): void
    {
        foreach ((array) $routes as $r) {
            self::$policies[$r] = $policy;
        }
    }

    /**
     * Permanently blacklist an identity (IP or API key).
     *
     * @param string|array<string> $ids
     */
    public static function blacklist($ids): void
    {
        foreach ((array) $ids as $id) {
            self::$blacklist[(string) $id] = true;
        }
    }

    /**
     * Permanently whitelist an identity (IP or API key).
     *
     * @param string|array<string> $ids
     */
    public static function whitelist($ids): void
    {
        foreach ((array) $ids as $id) {
            self::$whitelist[(string) $id] = true;
        }
    }

    /**
     * Clear in-memory policy / blacklist state. Useful in tests.
     */
    public static function reset(): void
    {
        self::$config['policies']    = [];
        self::$policies              = [];
        self::$blacklist             = [];
        self::$whitelist             = [];
        self::$lastDecision          = null;
    }

    // -----------------------------------------------------------------
    // Core API
    // -----------------------------------------------------------------

    /**
     * Register one hit against the given bucket. Returns true when
     * the request is allowed, false when it should be rejected.
     *
     * @param string $bucket   Logical bucket name, e.g. "api:password-generator"
     * @param string|null $identity  Optional explicit identity (API key).
     *                               Falls back to the client IP.
     */
    public static function hit(string $bucket, ?string $identity = null): bool
    {
        if (empty(self::$config['enabled'])) {
            self::$lastDecision = self::infiniteDecision();
            return true;
        }

        $identity = $identity !== null && $identity !== ''
            ? $identity
            : self::clientIp();

        if (isset(self::$whitelist[$identity])) {
            self::$lastDecision = self::infiniteDecision();
            return true;
        }
        if (isset(self::$blacklist[$identity])) {
            self::$lastDecision = [
                'allowed'      => 0,
                'limit'        => 0,
                'remaining'    => 0,
                'reset'        => 0,
                'retry_after'  => 86400,
            ];
            return false;
        }

        $policy = self::policyFor($bucket);
        $state  = self::load($bucket, $identity);

        // Banned?
        if ($state['banned_until'] !== null && $state['banned_until'] > self::now()) {
            self::$lastDecision = [
                'allowed'     => 0,
                'limit'       => $policy['limit'],
                'remaining'   => 0,
                'reset'       => (int) ceil(($state['banned_until'] - self::now()) / 1000),
                'retry_after' => (int) ceil(($state['banned_until'] - self::now()) / 1000),
            ];
            return false;
        }

        // Sliding window — drop hits outside the window
        $cutoff       = self::now() - ($policy['window'] * 1000);
        $windowHits   = array_values(array_filter(
            $state['hits'],
            static fn($t) => $t > $cutoff
        ));

        if (count($windowHits) >= $policy['limit']) {
            // Over-limit
            $oldest       = $windowHits[0];
            $reset        = (int) ceil(($oldest + ($policy['window'] * 1000) - self::now()) / 1000);
            self::$lastDecision = [
                'allowed'     => 0,
                'limit'       => $policy['limit'],
                'remaining'   => 0,
                'reset'       => max(1, $reset),
                'retry_after' => max(1, $reset),
            ];
            // Persist the (un-modifed) state so the file does not drift
            self::save($bucket, $identity, [
                'window_start' => $state['window_start'],
                'hits'         => $windowHits,
                'fails'        => $state['fails'],
                'banned_until' => $state['banned_until'],
            ]);
            return false;
        }

        // Allowed — record the hit
        $windowHits[] = self::now();
        $windowStart  = $state['window_start'] ?? $windowHits[0];

        self::$lastDecision = [
            'allowed'     => 1,
            'limit'       => $policy['limit'],
            'remaining'   => max(0, $policy['limit'] - count($windowHits)),
            'reset'       => $policy['window'],
            'retry_after' => 0,
        ];

        self::save($bucket, $identity, [
            'window_start' => $windowStart,
            'hits'         => $windowHits,
            'fails'        => 0, // successful hit resets the failure streak
            'banned_until' => $state['banned_until'],
        ]);

        return true;
    }

    /**
     * Register a failed request (e.g. 4xx). When the failure
     * streak exceeds the configured threshold the identity is
     * auto-banned for the configured duration.
     *
     * @param string $bucket
     * @param string|null $identity
     */
    public static function fail(string $bucket, ?string $identity = null): void
    {
        if (empty(self::$config['enabled'])) {
            return;
        }

        $identity = $identity !== null && $identity !== ''
            ? $identity
            : self::clientIp();

        if (isset(self::$whitelist[$identity])) {
            return;
        }

        $state  = self::load($bucket, $identity);
        $fails  = ($state['fails'] ?? 0) + 1;

        $banMs = 0;
        if ($fails >= self::$config['fail_policy']['fail_limit']) {
            $banMs = self::$config['fail_policy']['ban_window'] * 1000;
        }

        self::save($bucket, $identity, [
            'window_start' => $state['window_start'] ?? self::now(),
            'hits'         => $state['hits'] ?? [],
            'fails'        => $fails,
            'banned_until' => $banMs > 0 ? self::now() + $banMs : null,
        ]);
    }

    /**
     * Emit the X-RateLimit-* and Retry-After headers based on the
     * last `hit()` decision. Always safe to call even when no
     * decision is available.
     */
    public static function sendHeaders(): void
    {
        if (self::$lastDecision === null) {
            return;
        }
        $d = self::$lastDecision;
        header('X-RateLimit-Limit: ' . $d['limit']);
        header('X-RateLimit-Remaining: ' . $d['remaining']);
        header('X-RateLimit-Reset: ' . $d['reset']);
        if ($d['retry_after'] > 0) {
            header('Retry-After: ' . $d['retry_after']);
        }
    }

    /**
     * Render a 429 JSON response using the last decision and exit.
     */
    public static function sendLimitResponse(): void
    {
        $d = self::$lastDecision ?? [
            'limit'       => 0,
            'remaining'   => 0,
            'reset'       => 60,
            'retry_after' => 60,
        ];

        self::sendHeaders();
        http_response_code(429);
        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode([
            'success'     => false,
            'error'       => 'Too many requests',
            'message'     => 'Rate limit exceeded. Please slow down and retry later.',
            'retry_after' => $d['retry_after'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // -----------------------------------------------------------------
    // Internals
    // -----------------------------------------------------------------

    /**
     * Look up the policy for a bucket, falling back to defaults.
     *
     * @return array{limit:int,window:int}
     */
    private static function policyFor(string $bucket): array
    {
        if (isset(self::$policies[$bucket])) {
            return self::$policies[$bucket];
        }
        return self::$config['default_policy'];
    }

    /**
     * Resolve the client IP, optionally trusting reverse-proxy headers.
     */
    public static function clientIp(): string
    {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if (self::$config['trust_cf_connecting'] && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return self::normaliseIp((string) $_SERVER['HTTP_CF_CONNECTING_IP']);
        }

        if (self::$config['trust_x_forwarded'] && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $first = trim(explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
            if (self::isTrustedProxy($remote) && $first !== '') {
                return self::normaliseIp($first);
            }
        }
        return self::normaliseIp($remote);
    }

    /**
     * Trim surrounding whitespace and validate IP format.
     */
    private static function normaliseIp(string $ip): string
    {
        $ip = trim($ip);
        if ($ip === '') {
            return '0.0.0.0';
        }
        // Strip IPv4-mapped IPv6 prefix
        if (str_starts_with($ip, '::ffff:')) {
            $ip = substr($ip, 7);
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }

    /**
     * Decide whether a given REMOTE_ADDR is in the trusted-proxy list.
     */
    private static function isTrustedProxy(string $ip): bool
    {
        $proxies = self::$config['proxies'] ?? [];
        if (in_array('*', $proxies, true)) {
            return true;
        }
        foreach ($proxies as $proxy) {
            if ($proxy === $ip) {
                return true;
            }
            if (str_contains($proxy, '/') && self::cidrMatch($ip, $proxy)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Very small CIDR matcher (IPv4 only — IPv6 not needed for
     * typical reverse-proxy setups). Returns false on malformed
     * input.
     */
    private static function cidrMatch(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return false;
        }
        [$subnet, $bits] = explode('/', $cidr, 2);
        $bits = (int) $bits;
        if ($bits < 0 || $bits > 32) {
            return false;
        }
        $ipLong     = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false) {
            return false;
        }
        $mask = $bits === 0 ? 0 : (-1 << (32 - $bits)) & 0xFFFFFFFF;
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    /**
     * Read the JSON state file for (bucket, identity). Missing or
     * malformed files are treated as empty state.
     *
     * @return array{window_start:float|null,hits:array<float>,fails:int,banned_until:float|null}
     */
    private static function load(string $bucket, string $identity): array
    {
        $path = self::path($bucket, $identity);
        if (!is_file($path)) {
            return self::emptyState();
        }
        $raw = @file_get_contents($path);
        if (!is_string($raw) || $raw === '') {
            return self::emptyState();
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return self::emptyState();
        }
        return [
            'window_start' => isset($data['window_start']) ? (float) $data['window_start'] : null,
            'hits'         => isset($data['hits']) && is_array($data['hits'])
                                ? array_map('floatval', $data['hits'])
                                : [],
            'fails'        => isset($data['fails']) ? (int) $data['fails'] : 0,
            'banned_until' => isset($data['banned_until']) && $data['banned_until'] !== null
                                ? (float) $data['banned_until']
                                : null,
        ];
    }

    /**
     * Atomically write the state file. Uses LOCK_EX + a temp file
     * to avoid corruption under concurrent writers.
     *
     * @param array{window_start:float|null,hits:array<float>,fails:int,banned_until:float|null} $state
     */
    private static function save(string $bucket, string $identity, array $state): void
    {
        $path = self::path($bucket, $identity);
        $dir  = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $tmp  = $path . '.tmp.' . bin2hex(random_bytes(4));
        $json = json_encode($state, JSON_UNESCAPED_UNICODE);
        $fh   = @fopen($tmp, 'wb');
        if (!$fh) {
            return;
        }
        @flock($fh, LOCK_EX);
        @fwrite($fh, $json);
        @fflush($fh);
        @flock($fh, LOCK_UN);
        @fclose($fh);
        @rename($tmp, $path);
    }

    /**
     * Resolve the on-disk path for a (bucket, identity) tuple.
     */
    private static function path(string $bucket, string $identity): string
    {
        $dir = rtrim((string) self::$config['storage_dir'], '/');
        $key = sha1($bucket . '|' . $identity);
        return $dir . '/bucket__' . $key . '.json';
    }

    /**
     * Millisecond timestamp helper.
     */
    private static function now(): float
    {
        return (float) (int) (microtime(true) * 1000);
    }

    /**
     * @return array{window_start:null,hits:array<never>,fails:int,banned_until:null}
     */
    private static function emptyState(): array
    {
        return [
            'window_start' => null,
            'hits'         => [],
            'fails'        => 0,
            'banned_until' => null,
        ];
    }

    /**
     * Decision for whitelisted / disabled callers — limit looks infinite.
     *
     * @return array<string,int>
     */
    private static function infiniteDecision(): array
    {
        return [
            'allowed'     => 1,
            'limit'       => 999999,
            'remaining'   => 999999,
            'reset'       => 0,
            'retry_after' => 0,
        ];
    }

    /**
     * Guess a sensible default storage directory based on the
     * current working directory (project-local) and the OS.
     */
    private static function detectStorageDir(): string
    {
        // 1) Explicit env override always wins
        $env = getenv('RATELIMIT_STORAGE_DIR');
        if (is_string($env) && $env !== '') {
            return rtrim($env, '/');
        }
        // 2) Project-local ./storage/ratelimit if writable
        $candidates = [
            getcwd() . '/storage/ratelimit',
            getcwd() . '/../storage/ratelimit',
            dirname(__DIR__, 3) . '/storage/ratelimit',
            sys_get_temp_dir() . '/myapis-ratelimit',
        ];
        foreach ($candidates as $candidate) {
            $dir = (string) $candidate;
            if (is_dir($dir) && is_writable($dir)) {
                return rtrim($dir, '/');
            }
            if (!is_dir($dir) && @mkdir($dir, 0775, true) && is_writable($dir)) {
                return rtrim($dir, '/');
            }
        }
        // 3) Last-resort fallback (ephemeral but always writable)
        return rtrim(sys_get_temp_dir(), '/') . '/myapis-ratelimit';
    }

    /**
     * Garbage-collect expired state files. Cheap O(n) scan;
     * the caller decides when (cron / daily / per-N-requests).
     *
     * @return int Number of files removed
     */
    public static function gc(int $olderThanSeconds = 3600): int
    {
        $dir = rtrim((string) self::$config['storage_dir'], '/');
        if (!is_dir($dir)) {
            return 0;
        }
        $cutoff = time() - $olderThanSeconds;
        $removed = 0;
        foreach ((array) glob($dir . '/bucket__*.json') as $file) {
            if (!is_string($file) || !is_file($file)) {
                continue;
            }
            if (filemtime($file) >= $cutoff) {
                continue;
            }
            $raw = @file_get_contents($file);
            if (!is_string($raw)) {
                continue;
            }
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                @unlink($file);
                $removed++;
                continue;
            }
            $bannedUntil = isset($data['banned_until']) && $data['banned_until'] !== null
                ? (int) ($data['banned_until'] / 1000)
                : 0;
            $latestHit   = !empty($data['hits']) ? max(array_map('intval', $data['hits'])) / 1000 : 0;
            $latest      = max($bannedUntil, (int) $latestHit);
            if ($latest < $cutoff) {
                @unlink($file);
                $removed++;
            }
        }
        return $removed;
    }
}