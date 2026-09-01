<?php
/**
 * Analytics — Single source of truth for the analytics include decision.
 *
 * The actual snippet (Umami / GA4) lives at `public/analytics.php` because
 * it must work in both Docker (env via docker-compose) AND shared-hosting
 * (env via putenv from public/config.php). Analytics::emit() resolves
 * the correct path and requires it; it does NOT duplicate the logic.
 *
 * Design decisions (per PM, ISSUE-013):
 *   - explicit require — emit() does the require_once itself (testable)
 *   - no auto-prepend — caller decides when to call
 *   - skips CLI, /api/*, and Accept: application/json automatically
 *
 * Usage (e.g. in any public/<tool>.php):
 *
 *     require_once __DIR__ . '/../api/_includes/Analytics.php';
 *     Analytics::emit();
 *
 * @author  Dev (เดฟ)
 * @since   2.5.0
 * @see     docs/issues/open/ISSUE-013-shared-classes.md
 * @see     public/analytics.php (the runtime snippet)
 */

declare(strict_types=1);

final class Analytics
{
    /** @var bool|null Cached decision so we don't run the env checks twice. */
    private static ?bool $cached = null;

    /**
     * Decide whether the analytics snippet should run for the current request.
     */
    public static function shouldEmit(): bool
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        // Skip CLI invocations — they're never user-facing.
        if (PHP_SAPI === 'cli') {
            return self::$cached = false;
        }

        // Skip API endpoints — analytics is for HTML pages only.
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (is_string($uri) && strpos($uri, '/api/') === 0) {
            return self::$cached = false;
        }

        // Skip if the client asked specifically for JSON (e.g. fetch() call).
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (is_string($accept) && stripos($accept, 'application/json') !== false) {
            return self::$cached = false;
        }

        // Skip if no analytics provider is configured.
        $provider = strtolower(trim((string) (getenv('ANALYTICS_PROVIDER') ?: 'none')));
        if (!in_array($provider, ['umami', 'ga4', 'google'], true)) {
            return self::$cached = false;
        }

        return self::$cached = true;
    }

    /**
     * Explicit require for the runtime analytics snippet. No-op when
     * `shouldEmit()` returns false. Idempotent (uses require_once).
     */
    public static function emit(): void
    {
        if (!self::shouldEmit()) {
            return;
        }

        $snippet = self::resolveSnippetPath();
        if ($snippet === null) {
            return;
        }
        require_once $snippet;
    }

    /**
     * Locate `public/analytics.php` regardless of where the caller sits
     * in the tree (api/_includes, public/, public/api-specs/…).
     *
     * @internal
     */
    private static function resolveSnippetPath(): ?string
    {
        // This file lives at api/_includes/Analytics.php.
        // The runtime snippet is at <project-root>/public/analytics.php.
        $candidate = dirname(__DIR__, 2) . '/public/analytics.php';
        if (is_file($candidate)) {
            return $candidate;
        }
        // Fallback: same directory (in case of unusual layouts).
        $sibling = __DIR__ . '/analytics.php';
        if (is_file($sibling)) {
            return $sibling;
        }
        return null;
    }

    /**
     * Reset the cached decision. Useful for tests.
     *
     * @internal
     */
    public static function resetCache(): void
    {
        self::$cached = null;
    }
}
