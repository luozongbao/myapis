<?php
/**
 * API Bootstrap — shared headers, CORS, OPTIONS handling and helpers.
 *
 * Each API entry point requires this file once. It centralises:
 *   - Content-Type / CORS headers
 *   - Preflight (OPTIONS) short-circuit
 *   - JSON response helpers
 *   - Input readers (GET / POST / JSON body)
 *
 * Designed for backward compatibility with the existing
 * /api/<tool>/index.php endpoints — the on-the-wire format
 * (Content-Type, status codes, JSON shape) is unchanged.
 */

declare(strict_types=1);

/**
 * Send CORS + content-type headers.
 *
 * @param string $contentType Default: application/json
 */
function api_send_headers(string $contentType = 'application/json; charset=UTF-8'): void
{
    header('Content-Type: ' . $contentType);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
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