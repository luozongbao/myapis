<?php
/**
 * QR Code Generator — Universal QR via goQR.me
 *
 * Supported types: text, vcard, event, url, wifi, phone
 * Supported formats: png, gif, jpeg, svg, eps
 *
 * @author MyAPIs Team
 * @since  2.5.0 (refactor — ISSUE-013, ISSUE-024)
 * @see    https://goqr.me/api/doc/create-qr-code/
 */

declare(strict_types=1);

require_once __DIR__ . "/../_includes/Cors.php";
require_once __DIR__ . "/../_includes/ErrorHandler.php";
require_once __DIR__ . "/../_includes/Validator.php";

Cors::handle();
ErrorHandler::register();

final class QrCodeGenerator
{
    private const ALLOWED_FORMATS  = ["png", "gif", "jpeg", "jpg", "svg", "eps"];
    private const ALLOWED_ECC      = ["L", "M", "Q", "H"];
    private const ALLOWED_CHARSETS = ["UTF-8", "ISO-8859-1"];
    public const MIME_BY_FORMAT = [
        "png"  => "image/png",
        "gif"  => "image/gif",
        "jpeg" => "image/jpeg",
        "jpg"  => "image/jpeg",
        "svg"  => "image/svg+xml",
        "eps"  => "application/postscript",
    ];

    /**
     * @param array<string,mixed> $fields
     * @return array{payload:string,type:string}
     */
    public function buildPayload(string $type, array $fields): array
    {
        $payload = match (strtolower($type)) {
            "text"   => $this->buildText($fields),
            "vcard"  => $this->buildVCard($fields),
            "event"  => $this->buildEvent($fields),
            "url"    => $this->buildUrl($fields),
            "wifi"   => $this->buildWifi($fields),
            "phone"  => $this->buildPhone($fields),
            default  => throw new ValidationException(
                "Unsupported type \"{$type}\". Allowed: text, vcard, event, url, wifi, phone"
            ),
        };
        return ["payload" => $payload, "type" => strtolower($type)];
    }

    // ----- payload types -----

    /**
     * @param array<string,mixed> $f
     */
    private function buildText(array $f): string
    {
        $text = trim((string) ($f["text"] ?? ""));
        if ($text === "") {
            throw new ValidationException("Field \"text\" is required for type=text");
        }
        return $text;
    }

    /**
     * @param array<string,mixed> $f
     */
    private function buildUrl(array $f): string
    {
        $url = trim((string) ($f["url"] ?? ""));
        if ($url === "") {
            throw new ValidationException("Field \"url\" is required for type=url");
        }
        if (!preg_match("#^https?://#i", $url)) {
            $url = "https://" . ltrim($url, "/");
        }
        return $url;
    }

    /**
     * @param array<string,mixed> $f
     */
    private function buildPhone(array $f): string
    {
        $phone = trim((string) ($f["phone"] ?? ""));
        if ($phone === "") {
            throw new ValidationException("Field \"phone\" is required for type=phone");
        }
        return "tel:" . preg_replace("/[^\d+]/", "", $phone);
    }

    /**
     * @param array<string,mixed> $f
     */
    private function buildVCard(array $f): string
    {
        $get = static fn (string $k): string => isset($f[$k]) ? trim((string) $f[$k]) : "";

        $firstName = $get("first_name");
        $lastName  = $get("last_name");
        $org       = $get("organization");
        $title     = $get("title");
        $note      = $get("note");

        if ($firstName === "" && $lastName === "" && $org === "") {
            throw new ValidationException(
                "vCard requires at least First Name + Last Name, or Organization"
            );
        }

        $fullName = trim($firstName . " " . $lastName);
        if ($fullName === "") $fullName = $org;

        $lines = [
            "BEGIN:VCARD",
            "VERSION:3.0",
            "N:" . $this->vEscape($lastName) . ";" . $this->vEscape($firstName) . ";;;",
            "FN:" . $this->vEscape($fullName),
        ];
        if ($org   !== "") $lines[] = "ORG:"   . $this->vEscape($org);
        if ($title !== "") $lines[] = "TITLE:" . $this->vEscape($title);

        // Legacy single fields -> merged with dynamic lists
        $emails = [];
        if ($get("work_email") !== "") $emails[] = ["type" => "WORK", "value" => $get("work_email")];
        if ($get("home_email") !== "") $emails[] = ["type" => "HOME", "value" => $get("home_email")];
        $emails = array_merge($emails, $this->collectDynamicItems($f, "emails"));

        $phones = [];
        if ($get("work_phone") !== "") $phones[] = ["type" => "WORK,VOICE", "value" => $get("work_phone")];
        if ($get("home_phone") !== "") $phones[] = ["type" => "HOME,VOICE", "value" => $get("home_phone")];
        if ($get("mobile")     !== "") $phones[] = ["type" => "CELL,VOICE", "value" => $get("mobile")];
        if ($get("fax")        !== "") $phones[] = ["type" => "FAX",        "value" => $get("fax")];
        $phones = array_merge($phones, $this->collectDynamicItems($f, "phones"));

        $urls = [];
        if ($get("website") !== "") $urls[] = ["value" => $get("website")];
        $urls = array_merge($urls, $this->collectDynamicItems($f, "urls"));

        $addresses = [];
        if ($get("address") !== "" || $get("city") !== "" || $get("region") !== ""
            || $get("postcode") !== "" || $get("country") !== "") {
            $addresses[] = [
                "type"    => "WORK",
                "po_box"  => "",
                "ext"     => "",
                "street"  => $get("address"),
                "city"    => $get("city"),
                "region"  => $get("region"),
                "postcode"=> $get("postcode"),
                "country" => $get("country"),
            ];
        }
        $addresses = array_merge($addresses, $this->collectDynamicItems($f, "addresses"));

        foreach ($emails as $e) {
            if (empty($e["value"])) continue;
            $type = !empty($e["type"]) ? $e["type"] : "INTERNET";
            $lines[] = "EMAIL;TYPE=" . $this->vEscape($type) . ":" . $this->vEscape($e["value"]);
        }
        foreach ($phones as $p) {
            if (empty($p["value"])) continue;
            $type = !empty($p["type"]) ? $p["type"] : "VOICE";
            $lines[] = "TEL;TYPE=" . $this->vEscape($type) . ":" . $this->vEscape($p["value"]);
        }
        foreach ($urls as $u) {
            if (empty($u["value"])) continue;
            $lines[] = "URL:" . $this->vEscape($u["value"]);
        }
        foreach ($addresses as $a) {
            $hasAny = false;
            foreach (["po_box","ext","street","city","region","postcode","country"] as $k) {
                if (!empty($a[$k])) { $hasAny = true; break; }
            }
            if (!$hasAny) continue;
            $type = !empty($a["type"]) ? $a["type"] : "WORK";
            $adr = implode(";", [
                $this->vEscape($a["po_box"]   ?? ""),
                $this->vEscape($a["ext"]      ?? ""),
                $this->vEscape($a["street"]   ?? ""),
                $this->vEscape($a["city"]     ?? ""),
                $this->vEscape($a["region"]   ?? ""),
                $this->vEscape($a["postcode"] ?? ""),
                $this->vEscape($a["country"]  ?? ""),
            ]);
            $lines[] = "ADR;TYPE=" . $this->vEscape($type) . ":" . $adr;
        }
        if ($note !== "") $lines[] = "NOTE:" . $this->vEscape($note);

        $lines[] = "END:VCARD";
        return implode("\r\n", $lines);
    }

    /**
     * Collect dynamic vCard items from raw $_GET + $_POST.
     *
     * @return array<int,array<string,string>>
     */
    private function collectDynamicItems(array $allFields, string $key): array
    {
        $items = [];
        $source = array_merge($_GET, $_POST);
        if (isset($source[$key]) && is_array($source[$key])) {
            foreach ($source[$key] as $entry) {
                if (is_array($entry)) {
                    $items[] = array_map("strval", $entry);
                } elseif (is_string($entry) && trim($entry) !== "") {
                    $items[] = ["value" => $entry];
                }
            }
        }
        return $items;
    }

    /**
     * @param array<string,mixed> $f
     */
    private function buildEvent(array $f): string
    {
        $summary    = trim((string) ($f["summary"] ?? ""));
        $location   = trim((string) ($f["location"] ?? ""));
        $description = trim((string) ($f["description"] ?? ""));
        $start      = trim((string) ($f["start"] ?? ""));
        $end        = trim((string) ($f["end"] ?? ""));

        if ($summary === "") throw new ValidationException("Field \"summary\" is required for type=event");
        if ($start   === "") throw new ValidationException("Field \"start\" is required for type=event");

        $dtStart = $this->formatIcsDate($start, false);
        $dtEnd   = $end !== "" ? $this->formatIcsDate($end, false) : $this->formatIcsDate($start, true);
        $dtStamp = gmdate("Ymd\\THis\\Z");
        $uid     = bin2hex(random_bytes(8)) . "@myapis.local";

        $lines = [
            "BEGIN:VCALENDAR",
            "VERSION:2.0",
            "PRODID:-//myapis//QR Code Generator//EN",
            "BEGIN:VEVENT",
            "UID:" . $uid,
            "DTSTAMP:" . $dtStamp,
            "DTSTART:" . $dtStart,
            "DTEND:"   . $dtEnd,
            "SUMMARY:" . $this->vEscape($summary),
        ];
        if ($location    !== "") $lines[] = "LOCATION:"    . $this->vEscape($location);
        if ($description !== "") $lines[] = "DESCRIPTION:" . $this->vEscape($description);
        $lines[] = "END:VEVENT";
        $lines[] = "END:VCALENDAR";

        return implode("\r\n", $lines);
    }

    /**
     * @param array<string,mixed> $f
     */
    private function buildWifi(array $f): string
    {
        $ssid       = trim((string) ($f["ssid"] ?? ""));
        $password   = trim((string) ($f["password"] ?? ""));
        $encryption = strtoupper(trim((string) ($f["encryption"] ?? "WPA")));
        $hidden     = !empty($f["hidden"]) ? "true" : "false";

        if ($ssid === "") {
            throw new ValidationException("Field \"ssid\" is required for type=wifi");
        }
        $allowedEnc = ["WPA", "WEP", "nopass"];
        if (!in_array($encryption, $allowedEnc, true)) {
            throw new ValidationException(
                "Field \"encryption\" must be one of: " . implode(", ", $allowedEnc)
            );
        }

        $escape = static function (string $s): string {
            return str_replace(
                ["\\", ";", ",", ":", "\""],
                ["\\\\", "\\;", "\\,", "\\:", "\\\""],
                $s
            );
        };

        $payload = "WIFI:T:" . $encryption . ";S:" . $escape($ssid) . ";";
        if ($encryption !== "nopass" && $password !== "") {
            $payload .= "P:" . $escape($password) . ";";
        }
        $payload .= "H:" . $hidden . ";";
        return $payload;
    }

    // ----- goQR.me integration -----

    /**
     * Build the goQR.me API URL.
     *
     * @param array<string,mixed> $params
     */
    public function buildQrUrl(string $payload, array $params = []): string
    {
        $defaults = [
            "size" => 300, "ecc" => "M", "format" => "png",
            "qzone" => 2, "margin" => 1,
            "charset-source" => "UTF-8", "charset-target" => "UTF-8",
            "color" => "0-0-0", "bgcolor" => "255-255-255",
        ];
        $params = array_merge($defaults, array_intersect_key($params, $defaults));

        $size   = max(10, min(1000, (int) $params["size"]));
        $ecc    = in_array($params["ecc"], self::ALLOWED_ECC, true) ? $params["ecc"] : "M";
        $fmt    = strtolower((string) $params["format"]);
        $fmt    = in_array($fmt, self::ALLOWED_FORMATS, true) ? $fmt : "png";
        $qzone  = max(0, min(100, (int) $params["qzone"]));
        $margin = max(0, min(50,  (int) $params["margin"]));
        $cs     = in_array($params["charset-source"], self::ALLOWED_CHARSETS, true) ? $params["charset-source"] : "UTF-8";
        $ct     = in_array($params["charset-target"], self::ALLOWED_CHARSETS, true) ? $params["charset-target"] : "UTF-8";
        $color   = $this->sanitizeColor($params["color"], "0-0-0");
        $bgcolor = $this->sanitizeColor($params["bgcolor"], "255-255-255");

        $query = [
            "data"           => $payload,
            "size"           => $size . "x" . $size,
            "ecc"            => $ecc,
            "format"         => $fmt,
            "qzone"          => $qzone,
            "margin"         => $margin,
            "charset-source" => $cs,
            "charset-target" => $ct,
            "color"          => $color,
            "bgcolor"        => $bgcolor,
        ];

        return "https://api.qrserver.com/v1/create-qr-code/?" . http_build_query($query);
    }

    /**
     * Fetch the QR image bytes from goQR.me.
     */
    public function fetchQrImage(string $qrUrl): string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $qrUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => "MyAPIs-QR-Generator/1.0",
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $code !== 200) {
            throw new RuntimeException("Failed to fetch QR code from goQR.me: " . ($err !== "" ? $err : "HTTP " . $code));
        }
        return $body;
    }

    /**
     * @param array<string,mixed> $f
     */
    private function vEscape(string $value): string
    {
        return str_replace(["\\", "\n", "\r"], ["\\\\", "\\n", ""], $value);
    }

    private function formatIcsDate(string $input, bool $addOneHour): string
    {
        $input = trim($input);
        $input = str_replace("T", " ", $input);
        $input = str_replace("/", "-", $input);

        $ts = strtotime($input);
        if ($ts === false) {
            throw new ValidationException("Invalid date \"{$input}\". Use YYYY-MM-DD or YYYY-MM-DD HH:MM.");
        }
        if ($addOneHour) $ts += 3600;
        return gmdate("Ymd\\THis\\Z", $ts);
    }

    private function sanitizeColor(string $value, string $default): string
    {
        if (preg_match("/^[a-fA-F0-9]{3}$/", $value) || preg_match("/^[a-fA-F0-9]{6}$/", $value)) {
            return $value;
        }
        if (preg_match("/^\d{1,3}-\d{1,3}-\d{1,3}$/", $value)) {
            foreach (explode("-", $value) as $p) {
                $n = (int) $p;
                if ($n < 0 || $n > 255) return $default;
            }
            return $value;
        }
        return $default;
    }
}

ErrorHandler::wrap(static function (): void {
    $input = Validator::readInput();

    $type     = strtolower((string) ($input["type"]   ?? "text"));
    $format   = strtolower((string) ($input["format"] ?? "json"));
    $fileType = strtolower((string) ($input["file_type"] ?? ($input["gformat"] ?? "png")));
    if (!in_array($fileType, ["png","gif","jpeg","jpg","svg","eps"], true)) {
        $fileType = "png";
    }

    $goqr = [
        "size"           => $input["size"]            ?? 300,
        "ecc"            => $input["ecc"]             ?? "M",
        "format"         => $fileType,
        "qzone"          => $input["qzone"]           ?? 2,
        "margin"         => $input["margin"]          ?? 1,
        "charset-source" => $input["charset_source"]  ?? "UTF-8",
        "charset-target" => $input["charset_target"]  ?? "UTF-8",
        "color"          => $input["color"]           ?? "0-0-0",
        "bgcolor"        => $input["bgcolor"]         ?? "255-255-255",
    ];

    $reserved = [
        "type","format","file_type","size","ecc","gformat","qzone","margin",
        "charset_source","charset_target","color","bgcolor",
    ];
    $fields = [];
    foreach ($input as $key => $value) {
        if (!in_array($key, $reserved, true)) $fields[$key] = $value;
    }

    $api     = new QrCodeGenerator();
    $built   = $api->buildPayload($type, $fields);
    $payload = $built["payload"];
    $qrUrl   = $api->buildQrUrl($payload, $goqr);

    if ($format === "image" || $format === "png" || $format === "svg") {
        $image = $api->fetchQrImage($qrUrl);
        if (!headers_sent()) {
            header("Content-Type: " . (QrCodeGenerator::MIME_BY_FORMAT[$fileType] ?? "image/png"));
            header("Content-Disposition: inline; filename=\"qr-code.{$fileType}\"");
            header("Cache-Control: public, max-age=3600");
        }
        echo $image;
        exit;
    }

    if ($format === "json" || $format === "data") {
        $image  = $api->fetchQrImage($qrUrl);
        $mime   = QrCodeGenerator::MIME_BY_FORMAT[$fileType] ?? "image/png";
        $base64 = "data:" . $mime . ";base64," . base64_encode($image);
        ErrorHandler::success([
            "type"      => $built["type"],
            "payload"   => $payload,
            "qr_url"    => $base64,
            "goqr_url"  => $qrUrl,
            "file_type" => $fileType,
            "params"    => [
                "size"           => (int)   $goqr["size"],
                "ecc"            => (string)$goqr["ecc"],
                "format"         => strtolower((string) $goqr["format"]),
                "qzone"          => (int)   $goqr["qzone"],
                "margin"         => (int)   $goqr["margin"],
                "charset-source" => (string)$goqr["charset-source"],
                "charset-target" => (string)$goqr["charset-target"],
                "color"          => (string)$goqr["color"],
                "bgcolor"        => (string)$goqr["bgcolor"],
            ],
        ]);
    }

    throw new ValidationException("Invalid format. Supported: image, json, svg");
});
