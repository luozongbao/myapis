<?php
/**
 * PromptPay QR Code Generator API
 *
 * Thin HTTP entry point. PromptPayAPI class lives in ./PromptPayAPI.php.
 *
 * Parameters:
 *   - target   (required) Phone number / Tax ID / e-Wallet ID
 *   - amount   (optional) THB amount, numeric
 *   - size     (optional) 50–1000 px (default 300)
 *   - format   image | json | base64   (default image)
 *
 * Backward-compatible response shapes are preserved.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/PromptPayAPI.php';

api_send_headers();
if (api_handle_preflight()) {
    exit;
}
api_register_exception_handler();

try {
    $target = trim((string) (api_input('target') ?? ''));
    $amount = trim((string) (api_input('amount') ?? ''));
    $size   = api_int(api_input('size'), 300);
    $format = strtolower((string) (api_input('format') ?? 'image'));

    if ($size < 50 || $size > 1000) {
        $size = 300;
    }

    if ($target === '') {
        api_json([
            'error'   => 'Missing required parameter: target',
            'message' => 'Please provide a phone number, tax ID, or e-wallet ID',
        ], 400);
    }

    $api     = new PromptPayAPI();
    $amountF = $amount !== '' ? (float) $amount : null;

    switch ($format) {
        case 'image': {
            $image = $api->generateQrCodeData($target, $amountF, $size);
            header('Content-Type: image/png');
            header('Content-Disposition: inline; filename="promptpay-qr.png"');
            header('Cache-Control: public, max-age=3600');
            echo $image;
            exit;
        }

        case 'json': {
            $payload     = $api->generatePayload($target, $amountF);
            $qrData      = $api->generateQrCodeData($target, $amountF, $size);
            $base64Image = 'data:image/png;base64,' . base64_encode($qrData);

            api_json([
                'success'     => true,
                'message'     => 'QR code generated successfully',
                'payload'     => $payload,
                'qr_url'      => $base64Image,
                'target'      => $target,
                'amount'      => $amountF,
                'target_type' => strlen($target) >= 15
                    ? 'ewallet'
                    : (strlen($target) >= 13 ? 'tax_id' : 'phone'),
                'qr_size'     => $size,
            ]);
        }

        case 'base64': {
            $qrData = $api->generateQrCodeData($target, $amountF, $size);
            api_json([
                'success'      => true,
                'image_base64' => 'data:image/png;base64,' . base64_encode($qrData),
                'payload'      => $api->generatePayload($target, $amountF),
                'target'       => $target,
                'amount'       => $amountF,
                'size'         => $size,
            ]);
        }

        default:
            api_json([
                'error'   => 'Invalid format parameter',
                'message' => 'Supported formats: image, json, base64',
            ], 400);
    }
} catch (Throwable $e) {
    api_json([
        'error'   => 'Internal server error',
        'message' => $e->getMessage(),
    ], 500);
}