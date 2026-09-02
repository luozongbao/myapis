<?php
/**
 * QR Code Generator API
 *
 * Thin HTTP entry point. The QrCodeGenerator class lives in
 * ./QrCodeGenerator.php so it can be unit-tested independently and
 * kept out of the request flow.
 *
 * Supported content types: text, vcard, event, url, wifi, phone
 * Supported goQR.me params : size, ecc, format, qzone, margin,
 *                             charset-source/target, color, bgcolor
 *
 * @see https://goqr.me/api/doc/create-qr-code/
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/QrCodeGenerator.php';

api_send_headers();
if (api_handle_preflight()) {
    exit;
}
api_register_exception_handler();

try {
    $type   = strtolower(trim((string) (api_input('type') ?? 'text')));
    $format = strtolower(trim((string) (api_input('format') ?? 'json')));

    // File type selector: png (default), svg, gif, jpeg, eps.
    // Accepts both file_type= and the legacy gformat= alias.
    $fileType = strtolower(trim((string) (api_input('file_type') ?? api_input('gformat') ?? 'png')));
    if (!in_array($fileType, ['png', 'svg', 'gif', 'jpeg', 'jpg', 'eps'], true)) {
        $fileType = 'png';
    }

    // goQR.me parameters
    $goqr = [
        'size'           => api_input('size')           ?? 300,
        'ecc'            => api_input('ecc')            ?? 'M',
        'format'         => $fileType,
        'qzone'          => api_input('qzone')          ?? 2,
        'margin'         => api_input('margin')         ?? 1,
        'charset-source' => api_input('charset_source') ?? 'UTF-8',
        'charset-target' => api_input('charset_target') ?? 'UTF-8',
        'color'          => api_input('color')          ?? '0-0-0',
        'bgcolor'        => api_input('bgcolor')        ?? '255-255-255',
    ];

    // Pull every other field for the payload builder
    $reserved = [
        'type', 'format', 'file_type', 'size', 'ecc', 'gformat', 'qzone', 'margin',
        'charset_source', 'charset_target', 'color', 'bgcolor',
    ];
    $fields = [];
    foreach (($_GET + $_POST) as $key => $value) {
        if (!in_array($key, $reserved, true)) {
            $fields[$key] = $value;
        }
    }

    // Accept a JSON body as well
    if (empty($fields) && api_method() === 'POST') {
        $body = api_json_body();
        if (!empty($body)) {
            $type   = $type ?: strtolower((string) ($body['type'] ?? 'text'));
            $fields = $body;
            foreach ($goqr as $k => $v) {
                if (isset($body[$k])) {
                    $goqr[$k] = $body[$k];
                }
            }
        }
    }

    $api     = new QrCodeGenerator();
    $payload = $api->buildPayload($type, $fields);
    $qrUrl   = $api->buildQrUrl($payload, $goqr);

    $mimeMap = [
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'eps'  => 'application/postscript',
    ];
    $mime = $mimeMap[$fileType] ?? 'image/png';

    // Direct image response
    if (in_array($format, ['image', 'png', 'svg'], true)) {
        $image = $api->fetchQrImage($qrUrl);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="qr-code.' . $fileType . '"');
        header('Cache-Control: public, max-age=3600');
        echo $image;
        exit;
    }

    // JSON response (with base64-embedded image)
    if (in_array($format, ['json', 'data'], true)) {
        $image  = $api->fetchQrImage($qrUrl);
        $base64 = 'data:' . $mime . ';base64,' . base64_encode($image);

        api_json([
            'success'   => true,
            'message'   => 'QR code generated successfully',
            'type'      => $type,
            'payload'   => $payload,
            'qr_url'    => $base64,
            'goqr_url'  => $qrUrl,
            'file_type' => $fileType,
            'params'    => [
                'size'           => (int) $goqr['size'],
                'ecc'            => (string) $goqr['ecc'],
                'format'         => strtolower((string) $goqr['format']),
                'qzone'          => (int) $goqr['qzone'],
                'margin'         => (int) $goqr['margin'],
                'charset-source' => (string) $goqr['charset-source'],
                'charset-target' => (string) $goqr['charset-target'],
                'color'          => (string) $goqr['color'],
                'bgcolor'        => (string) $goqr['bgcolor'],
            ],
        ]);
    }

    api_json([
        'error'   => 'Invalid format parameter',
        'message' => 'Supported formats: image, json, svg',
    ], 400);

} catch (InvalidArgumentException $e) {
    api_json([
        'error'   => 'Bad request',
        'message' => $e->getMessage(),
    ], 400);
} catch (Throwable $e) {
    api_json([
        'error'   => 'Internal server error',
        'message' => $e->getMessage(),
    ], 500);
}