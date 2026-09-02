<?php
/**
 * Fortune Teller API
 *
 * GET /api/fortune-teller/                    — random fortune
 * GET /api/fortune-teller/?id=N               — specific fortune (1-52)
 * GET /api/fortune-teller/?language=en|th|zh  — pick a random fortune
 *                                              from a language subset
 *
 * Backward-compatible response shape preserved.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

api_send_headers();
if (api_handle_preflight()) {
    exit;
}
api_register_exception_handler();

// Rate-limit this endpoint using the policy defined in api_config.php.
$configs = require __DIR__ . '/../includes/api_config.php';
api_rate_limit('api:fortune-teller', $configs['fortune-teller'] ?? null);

/**
 * Read a fortune JSON file from disk.
 *
 * @return array<string,mixed>|null
 */
function getFortune(int $fortuneId): ?array
{
    $filePath = __DIR__ . '/predictions/' . $fortuneId . '.json';
    if (!is_file($filePath)) {
        return null;
    }
    $json = file_get_contents($filePath);
    return is_string($json) ? json_decode($json, true) : null;
}

/**
 * Discover the available fortune IDs and total count.
 */
function totalFortunes(): int
{
    $files = glob(__DIR__ . '/predictions/*.json') ?: [];
    return count($files);
}

// ---------------------------------------------------------------------------
// HTTP layer
// ---------------------------------------------------------------------------
$total      = totalFortunes();
$explicitId = api_input('id');

if ($explicitId !== null && is_numeric($explicitId)) {
    $id = api_int($explicitId, 1);
    $fortune = getFortune($id);
    if ($fortune === null) {
        api_json([
            'success'      => false,
            'error'        => 'Fortune file not found',
            'requested_id' => $id,
        ], 404);
    }
    api_json([
        'success'        => true,
        'fortune'        => $fortune,
        'timestamp'      => date('Y-m-d H:i:s'),
        'total_fortunes' => $total,
    ]);
}

$id = $total > 0 ? random_int(1, $total) : 1;
$fortune = getFortune($id);

if ($fortune === null) {
    api_json([
        'success'      => false,
        'error'        => 'Fortune file not found',
        'requested_id' => $id,
    ], 404);
}

api_json([
    'success'        => true,
    'fortune'        => $fortune,
    'timestamp'      => date('Y-m-d H:i:s'),
    'total_fortunes' => $total,
]);