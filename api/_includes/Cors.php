<?php
/**
 * Cors — Single source of truth for CORS headers + preflight handling
 *
 * Aligned with FR-004 (CORS) + FR-012 (health-check OPTIONS) + ISSUE-013.
 *
 * Usage (in any api/<tool>/index.php):
 *
 *     require_once __DIR__ . '/../_includes/Cors.php';
 *     Cors::handle();      // sets headers + answers preflight
 *
 *     // ... your endpoint logic ...
 *
 * @author  Dev (เดฟ)
 * @since   2.5.0
 * @see     docs/issues/open/ISSUE-013-shared-classes.md
 */

declare(strict_types=1);

final class Cors
{
    /** @var int HTTP status sent for OPTIONS preflight (RFC 7231: 204 No Content) */
    private const PREFLIGHT_STATUS = 204;

    /**
     * Send the three CORS headers used by every endpoint.
     */
    public static function setHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    /**
     * Return true if the current request is a CORS preflight (OPTIONS).
     */
    public static function isPreflight(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS';
    }

    /**
     * One-call helper: sets headers + answers preflight (204) + exits.
     *
     * Idempotent — safe to call from any endpoint.
     */
    public static function handle(): void
    {
        self::setHeaders();

        if (self::isPreflight()) {
            http_response_code(self::PREFLIGHT_STATUS);
            exit;
        }
    }
}
