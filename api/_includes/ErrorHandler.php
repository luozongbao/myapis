<?php
/**
 * ErrorHandler — Central response emitter (success + error envelopes)
 *
 * Aligned with FR-003 (envelope) + FR-010 (error logging) + ISSUE-013
 * + ISSUE-024 (standardize response envelope).
 *
 * Envelopes (ISO-8601 timestamps, JSON_UNESCAPED_UNICODE):
 *
 *   Success →
 *     { "success": true,  "data": {...}, "timestamp": "..." }
 *
 *   Error   →
 *     { "success": false, "error": "CODE", "message": "...",
 *       "details": {...}?, "timestamp": "..." }
 *
 * Exception → HTTP mapping (per PM decision, ISSUE-013 comments):
 *
 *   ValidationException → 400
 *   AuthException       → 401
 *   NotFoundException   → 404
 *   \Throwable          → 500
 *
 * Usage:
 *
 *     require_once __DIR__ . '/../_includes/ErrorHandler.php';
 *
 *     ErrorHandler::register();   // optional global handlers
 *
 *     ErrorHandler::wrap(function () use ($input) {
 *         Validator::cast($input, [...rules...]);
 *         // ... do work ...
 *         ErrorHandler::success(['result' => $data]);
 *     });
 *
 * @author  Dev (เดฟ)
 * @since   2.5.0
 * @see     docs/issues/open/ISSUE-013-shared-classes.md
 * @see     docs/issues/open/ISSUE-024-standardize-response-envelope.md
 * @see     docs/requirements/functional-requirements.md (FR-003, FR-010)
 */

declare(strict_types=1);

// ---------------------------------------------------------------
// Custom exception hierarchy
// ---------------------------------------------------------------

/**
 * 400 Bad Request — input validation failed.
 * Provide structured details via `withDetails()` for the envelope.
 */
class ValidationException extends \InvalidArgumentException
{
    /** @var array<string,mixed>|null */
    private ?array $details = null;

    public function withDetails(array $details): self
    {
        $this->details = $details;
        return $this;
    }

    /** @return array<string,mixed>|null */
    public function getDetails(): ?array
    {
        return $this->details;
    }
}

/**
 * 401 Unauthorized — caller has not authenticated.
 */
class AuthException extends \RuntimeException
{
}

/**
 * 404 Not Found — requested resource does not exist.
 */
class NotFoundException extends \RuntimeException
{
}

// ---------------------------------------------------------------
// ErrorHandler — response + exception handling
// ---------------------------------------------------------------

final class ErrorHandler
{
    /**
     * Send a successful response envelope and exit.
     *
     * @param mixed $data    payload; will be JSON-encoded as-is
     * @param int   $code    HTTP status (default 200)
     */
    public static function success(mixed $data, int $code = 200): never
    {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=UTF-8');
        }
        echo json_encode(
            [
                'success'   => true,
                'data'      => $data,
                'timestamp' => date('c'),
            ],
            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        exit;
    }

    /**
     * Send an error envelope and exit.
     *
     * @param int                  $code    HTTP status (400, 401, 404, 500, …)
     * @param string               $error   Short error code (e.g. "VALIDATION_ERROR")
     * @param string               $message Human-readable message
     * @param array<string,mixed>|null $details Optional structured details
     */
    public static function send(int $code, string $error, string $message, ?array $details = null): never
    {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=UTF-8');
        }
        $payload = [
            'success'   => false,
            'error'     => $error,
            'message'   => $message,
            'timestamp' => date('c'),
        ];
        if ($details !== null) {
            $payload['details'] = $details;
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * Wrap a callback with full exception handling (per PM-approved mapping).
     *
     * The callback is responsible for calling ErrorHandler::success() when
     * the work succeeds. Any thrown exception is converted to an
     * appropriate error envelope.
     *
     * @param callable $fn function (): void
     */
    public static function wrap(callable $fn): void
    {
        try {
            $fn();
        } catch (ValidationException $e) {
            self::send(400, 'VALIDATION_ERROR', $e->getMessage(), $e->getDetails());
        } catch (AuthException $e) {
            self::send(401, 'UNAUTHORIZED', $e->getMessage());
        } catch (NotFoundException $e) {
            self::send(404, 'NOT_FOUND', $e->getMessage());
        } catch (\Throwable $e) {
            // Log full trace, hide details from client in production.
            error_log('[myapis] ' . get_class($e) . ': ' . $e->getMessage());
            $env = strtolower((string) (getenv('APP_ENV') ?: 'production'));
            $message = ($env === 'development' || $env === 'dev')
                ? $e->getMessage()
                : 'Internal server error';
            self::send(500, 'INTERNAL_ERROR', $message);
        }
    }

    /**
     * Install PHP's global error + exception handlers so that any
     * uncaught throwable produces a JSON envelope instead of HTML.
     *
     * Call once near the top of the endpoint.
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    // ---------------------------------------------------------------
    // Internal: global handlers
    // ---------------------------------------------------------------

    /** @internal — used by set_exception_handler() */
    public static function handleException(\Throwable $e): void
    {
        try {
            if ($e instanceof ValidationException) {
                self::send(400, 'VALIDATION_ERROR', $e->getMessage(), $e->getDetails());
            } elseif ($e instanceof AuthException) {
                self::send(401, 'UNAUTHORIZED', $e->getMessage());
            } elseif ($e instanceof NotFoundException) {
                self::send(404, 'NOT_FOUND', $e->getMessage());
            } else {
                error_log('[myapis] ' . get_class($e) . ': ' . $e->getMessage());
                $env = strtolower((string) (getenv('APP_ENV') ?: 'production'));
                $message = ($env === 'development' || $env === 'dev')
                    ? $e->getMessage()
                    : 'Internal server error';
                self::send(500, 'INTERNAL_ERROR', $message);
            }
        } catch (\Throwable $inner) {
            // Last-resort: emit a minimal JSON envelope so the connection
            // is never left hanging on an HTML stack trace.
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json; charset=UTF-8');
            }
            echo json_encode([
                'success'   => false,
                'error'     => 'INTERNAL_ERROR',
                'message'   => 'Internal server error',
                'timestamp' => date('c'),
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /** @internal — used by set_error_handler() */
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
}
