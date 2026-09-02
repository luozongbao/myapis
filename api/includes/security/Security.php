<?php
/**
 * =============================================================
 * MyAPIs — Security helper bundle
 * =============================================================
 *
 * Companion to RateLimiter.php. Provides small, focused helpers
 * that are useful in every API endpoint:
 *
 *   - Security::sendHeaders()    common security response headers
 *   - Security::enforceJson()    validate / cap JSON body
 *   - Security::safeString()     string validator / sanitiser
 *   - Security::safeInt()        int validator with bounds
 *   - Security::safeEnum()       enum-style whitelist
 *   - Security::containsMalicious() pattern probe
 *   - Security::generateApiKey() opaque random token
 *   - Security::verifyHmac()     HMAC signature check
 *   - Security::clientFingerprint() stable per-client token
 *
 * All helpers are intentionally defensive and zero-allocation
 * where possible. No external dependencies.
 */

declare(strict_types=1);

final class Security
{
    /**
     * Cache of headers already sent, so we never double-emit.
     *
     * @var array<string, true>
     */
    private static array $sentHeaders = [];

    /**
     * Cache of pattern probe results to avoid repeated regex work.
     *
     * @var array<string, bool>
     */
    private static array $probeCache = [];

    // -----------------------------------------------------------------
    // Headers
    // -----------------------------------------------------------------

    /**
     * Emit common defensive response headers. Idempotent — calling
     * twice is safe.
     *
     * @param array<string,string> $extra  additional headers
     * @param bool $includeHsts            include Strict-Transport-Security
     */
    public static function sendHeaders(array $extra = [], bool $includeHsts = false): void
    {
        $defaults = [
            'X-Content-Type-Options'    => 'nosniff',
            'X-Frame-Options'           => 'SAMEORIGIN',
            'X-XSS-Protection'          => '1; mode=block',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin',
            'Permissions-Policy'        => 'geolocation=(), microphone=(), camera=(), payment=()',
            'Cross-Origin-Opener-Policy'=> 'same-origin',
        ];
        if ($includeHsts) {
            $defaults['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }
        foreach (array_merge($defaults, $extra) as $name => $value) {
            if (isset(self::$sentHeaders[$name])) {
                continue;
            }
            header($name . ': ' . $value);
            self::$sentHeaders[$name] = true;
        }
    }

    /**
     * Add (or replace) a single header.
     */
    public static function header(string $name, string $value): void
    {
        header($name . ': ' . $value);
        self::$sentHeaders[$name] = true;
    }

    // -----------------------------------------------------------------
    // Input hardening
    // -----------------------------------------------------------------

    /**
     * Enforce a maximum JSON body size and depth. Returns the
     * decoded array, or null when the body is missing / invalid.
     *
     * @return array<string,mixed>|null
     */
    public static function enforceJson(int $maxBytes = 65536, int $maxDepth = 8): ?array
    {
        $raw = file_get_contents('php://input', false, null, 0, $maxBytes + 1);
        if (!is_string($raw) || $raw === '') {
            return null;
        }
        if (strlen($raw) > $maxBytes) {
            return null;
        }
        // Pre-flight: detect obviously malicious payloads by content
        if (self::containsMalicious($raw)) {
            return null;
        }
        // json_decode does not expose depth, so we decode and inspect
        $decoded = json_decode($raw, true, $maxDepth, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            return null;
        }
        return $decoded;
    }

    /**
     * Validate / clamp a string. Returns the cleaned value, or
     * $default when the value is missing / invalid.
     *
     * - Strips null bytes and control characters
     * - Optionally enforces min/max length
     * - Optionally enforces an allowed-character pattern
     */
    public static function safeString(
        mixed $value,
        string $default = '',
        int $minLen = 0,
        int $maxLen = 1024,
        ?string $allowedRegex = null
    ): string {
        if (!is_scalar($value)) {
            return $default;
        }
        $str = (string) $value;
        // Null bytes + control characters except \t \n \r
        $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str) ?? '';
        if (strlen($str) < $minLen) {
            return $default;
        }
        if (strlen($str) > $maxLen) {
            $str = substr($str, 0, $maxLen);
        }
        if ($allowedRegex !== null && !preg_match($allowedRegex, $str)) {
            return $default;
        }
        return $str;
    }

    /**
     * Validate / clamp an integer. Returns $default when the value
     * is not numeric or outside the bounds.
     */
    public static function safeInt(mixed $value, int $default = 0, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int
    {
        if (is_int($value)) {
            return max($min, min($max, $value));
        }
        if (!is_numeric($value)) {
            return $default;
        }
        $n = (int) $value;
        if ($n < $min || $n > $max) {
            return $default;
        }
        return $n;
    }

    /**
     * Validate that a value is one of a fixed set of allowed values.
     */
    public static function safeEnum(mixed $value, array $allowed, ?string $default = null): ?string
    {
        if (!is_scalar($value)) {
            return $default;
        }
        $str = (string) $value;
        return in_array($str, $allowed, true) ? $str : $default;
    }

    /**
     * Quickly scan a string for known attack patterns.
     * Returns true if any pattern matched.
     */
    public static function containsMalicious(string $value): bool
    {
        if (isset(self::$probeCache[$value])) {
            return self::$probeCache[$value];
        }
        // Trim the cache so it cannot grow unbounded
        if (count(self::$probeCache) > 256) {
            self::$probeCache = [];
        }

        $patterns = [
            // SQL injection probes (single-line)
            "/\bunion\s+select\b/i",
            "/\bor\s+1\s*=\s*1\b/i",
            "/\bdrop\s+table\b/i",
            "/\binsert\s+into\b/i",
            "/\bdelete\s+from\b/i",
            "/\bupdate\s+\w+\s+set\b/i",
            "/--\s*$/m",
            "/\bexec\s*\(/i",
            // Path traversal
            "/\.\.\/(\.\.\/)*/i",
            "/%2e%2e%2f/i",
            // XSS / HTML injection
            "/<script\b[^>]*>/i",
            "/javascript\s*:/i",
            "/on\w+\s*=\s*[\"']/i",
            // Shell injection
            "/[;&|`]\s*(rm|wget|curl|nc|bash|sh)\b/i",
            // PHP injection
            "/<\?php\b/i",
            "/\beval\s*\(/i",
            "/\b(system|passthru|shell_exec|popen|proc_open)\s*\(/i",
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value) === 1) {
                return self::$probeCache[$value] = true;
            }
        }
        return self::$probeCache[$value] = false;
    }

    // -----------------------------------------------------------------
    // Auth / signing
    // -----------------------------------------------------------------

    /**
     * Generate a cryptographically random opaque API key.
     *
     * @param int $bytes Number of random bytes (16 → 32-char hex)
     */
    public static function generateApiKey(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    /**
     * Generate an HMAC signature for a payload using a shared
     * secret. Returns the hex digest.
     */
    public static function hmac(string $payload, string $secret, string $algo = 'sha256'): string
    {
        return hash_hmac($algo, $payload, $secret);
    }

    /**
     * Constant-time HMAC verification. Returns true when the
     * supplied signature matches.
     */
    public static function verifyHmac(
        string $payload,
        string $secret,
        string $expectedSignature,
        string $algo = 'sha256'
    ): bool {
        $expected = self::hmac($payload, $secret, $algo);
        return hash_equals($expected, strtolower($expectedSignature));
    }

    /**
     * Build a stable, opaque fingerprint for the current client.
     * Useful for per-client rate-limit buckets without trusting
     * user-supplied identifiers.
     */
    public static function clientFingerprint(): string
    {
        $parts = [
            $_SERVER['REMOTE_ADDR']      ?? '',
            $_SERVER['HTTP_USER_AGENT']  ?? '',
            $_SERVER['HTTP_ACCEPT']      ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        ];
        return sha1(implode('|', $parts));
    }

    /**
     * Pull an API key from common header locations.
     * Returns null when no key is present.
     */
    public static function readApiKey(): ?string
    {
        $headers = [
            $_SERVER['HTTP_X_API_KEY']       ?? null,
            $_SERVER['HTTP_X_AUTHORIZATION'] ?? null,
            $_SERVER['HTTP_AUTHORIZATION']   ?? null,
        ];
        foreach ($headers as $raw) {
            if (!is_string($raw) || $raw === '') {
                continue;
            }
            if (stripos($raw, 'Bearer ') === 0) {
                $raw = substr($raw, 7);
            }
            if (stripos($raw, 'ApiKey ') === 0) {
                $raw = substr($raw, 7);
            }
            $raw = trim($raw);
            if ($raw !== '') {
                return $raw;
            }
        }
        return null;
    }

    // -----------------------------------------------------------------
    // Misc
    // -----------------------------------------------------------------

    /**
     * Wipe the in-memory caches (mostly for tests).
     */
    public static function flush(): void
    {
        self::$sentHeaders = [];
        self::$probeCache  = [];
    }
}