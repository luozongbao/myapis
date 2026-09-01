<?php
/**
 * PromptPay QR Generator — EMV QRCPS-compliant QR for Thai PromptPay
 *
 * Supports 3 response formats:
 *   - image  → binary PNG
 *   - json   → JSON envelope with base64 QR
 *   - base64 → JSON envelope with base64-only QR
 *
 * @author MyAPIs Team
 * @since  2.5.0 (refactor — ISSUE-013, ISSUE-024)
 */

declare(strict_types=1);

require_once __DIR__ . "/../_includes/Cors.php";
require_once __DIR__ . "/../_includes/ErrorHandler.php";
require_once __DIR__ . "/../_includes/Validator.php";

Cors::handle();
ErrorHandler::register();

final class PromptPayAPI
{
    // EMV QRCPS tag IDs
    private const ID_PAYLOAD_FORMAT              = "00";
    private const ID_POI_METHOD                  = "01";
    private const ID_MERCHANT_INFORMATION_BOT    = "29";
    private const ID_TRANSACTION_CURRENCY        = "53";
    private const ID_TRANSACTION_AMOUNT          = "54";
    private const ID_COUNTRY_CODE                = "58";
    private const ID_CRC                         = "63";

    private const PAYLOAD_FORMAT_EMV_QRCPS_MERCHANT_PRESENTED_MODE = "01";
    private const POI_METHOD_STATIC  = "11";
    private const POI_METHOD_DYNAMIC = "12";
    private const MERCHANT_INFORMATION_TEMPLATE_ID_GUID = "00";
    private const BOT_ID_MERCHANT_PHONE_NUMBER = "01";
    private const BOT_ID_MERCHANT_TAX_ID       = "02";
    private const BOT_ID_MERCHANT_EWALLET_ID    = "03";
    private const GUID_PROMPTPAY         = "A000000677010111";
    private const TRANSACTION_CURRENCY_THB = "764";
    private const COUNTRY_CODE_TH         = "TH";

    /**
     * Generate the EMV QRCPS payload string for the given target.
     *
     * @return array{payload:string,target_type:string}
     */
    public function generate(string $target, ?float $amount = null): array
    {
        $target = $this->sanitizeTarget($target);
        if ($target === "") {
            throw new ValidationException("target is required");
        }

        $targetType = $this->detectTargetType($target);

        $data = [
            $this->field(self::ID_PAYLOAD_FORMAT, self::PAYLOAD_FORMAT_EMV_QRCPS_MERCHANT_PRESENTED_MODE),
            $this->field(self::ID_POI_METHOD, $amount !== null ? self::POI_METHOD_DYNAMIC : self::POI_METHOD_STATIC),
            $this->field(self::ID_MERCHANT_INFORMATION_BOT, $this->serialize([
                $this->field(self::MERCHANT_INFORMATION_TEMPLATE_ID_GUID, self::GUID_PROMPTPAY),
                $this->field($targetType, $this->formatTarget($target)),
            ])),
            $this->field(self::ID_COUNTRY_CODE, self::COUNTRY_CODE_TH),
            $this->field(self::ID_TRANSACTION_CURRENCY, self::TRANSACTION_CURRENCY_THB),
        ];

        if ($amount !== null && $amount > 0) {
            $data[] = $this->field(self::ID_TRANSACTION_AMOUNT, $this->formatAmount($amount));
        }

        $dataToCrc = $this->serialize($data) . self::ID_CRC . "04";
        $data[] = $this->field(self::ID_CRC, $this->crc16($dataToCrc));

        return [
            "payload"     => $this->serialize($data),
            "target_type" => $this->targetTypeName($targetType),
        ];
    }

    private function detectTargetType(string $target): string
    {
        $len = strlen($target);
        if ($len >= 15) return self::BOT_ID_MERCHANT_EWALLET_ID;
        if ($len >= 13) return self::BOT_ID_MERCHANT_TAX_ID;
        return self::BOT_ID_MERCHANT_PHONE_NUMBER;
    }

    private function targetTypeName(string $id): string
    {
        return match ($id) {
            self::BOT_ID_MERCHANT_EWALLET_ID => "ewallet",
            self::BOT_ID_MERCHANT_TAX_ID     => "tax_id",
            default                          => "phone",
        };
    }

    private function field(string $id, string $value): string
    {
        return $id . substr("00" . strlen($value), -2) . $value;
    }

    private function serialize(array $xs): string
    {
        return implode("", $xs);
    }

    private function sanitizeTarget(string $str): string
    {
        return preg_replace("/[^0-9]/", "", $str) ?? "";
    }

    private function formatTarget(string $target): string
    {
        $str = $this->sanitizeTarget($target);
        if (strlen($str) >= 13) {
            return $str;
        }
        $str = preg_replace("/^0/", "66", $str) ?? $str;
        $str = "0000000000000" . $str;
        return substr($str, -13);
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, ".", "");
    }

    private function crc16(string $data): string
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ $polynomial) : ($crc << 1);
            }
        }
        return strtoupper(dechex($crc & 0xFFFF));
    }

    /**
     * Build the goQR.me URL for for the the given payload.
     */
    public function buildQrUrl(string $payload, int $size): string
    {
        $params = [
            "data"            => $payload,
            "size"            => $size . "x" . $size,
            "ecc"             => "M",
            "format"          => "png",
            "qzone"           => 1,
            "charset-source"  => "UTF-8",
            "charset-target"  => "UTF-8",
        ];
        return "https://api.qrserver.com/v1/create-qr-code/?" . http_build_query($params);
    }

    /**
     * Fetch the binary PNG from goQR.me.
     */
    public function fetchQrPng(string $payload, int $size): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->buildQrUrl($payload, $size),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $data = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($data === false || $err !== "" || $code !== 200) {
            throw new RuntimeException("Failed to generate QR code: " . ($err !== "" ? $err : "HTTP " . $code));
        }
        return $data;
    }
}

ErrorHandler::wrap(static function (): void {
    $input = Validator::readInput();
    $target = trim((string) ($input["target"] ?? ""));
    $amountRaw = $input["amount"] ?? null;
    $amount = ($amountRaw === null || $amountRaw === "") ? null : (float) $amountRaw;
    $size = (int) ($input["size"] ?? 300);
    $size = max(50, min(1000, $size));
    $format = strtolower((string) ($input["format"] ?? "image"));

    if (!in_array($format, ["image", "json", "base64"], true)) {
        throw new ValidationException("Invalid format. Supported: image, json, base64");
    }

    $api = new PromptPayAPI();
    $generated = $api->generate($target, $amount);
    $payload = $generated["payload"];

    if ($format === "image") {
        $png = $api->fetchQrPng($payload, $size);
        if (!headers_sent()) {
            header("Content-Type: image/png");
            header("Content-Disposition: inline; filename=\"promptpay-qr.png\"");
            header("Cache-Control: public, max-age=3600");
        }
        echo $png;
        exit;
    }

    // json / base64 — use new envelope via ErrorHandler::success
    $png = $api->fetchQrPng($payload, $size);
    $b64 = "data:image/png;base64," . base64_encode($png);

    if ($format === "json") {
        ErrorHandler::success([
            "payload"     => $payload,
            "qr_url"      => $b64,
            "target"      => $target,
            "amount"      => $amount,
            "target_type" => $generated["target_type"],
            "qr_size"     => $size,
        ]);
    }

    // base64
    ErrorHandler::success([
        "image_base64" => $b64,
        "payload"      => $payload,
        "target"       => $target,
        "amount"       => $amount,
        "size"         => $size,
    ]);
});
