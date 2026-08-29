<?php
/**
 * QR Code Generator API
 * REST API endpoint for generating QR codes using goQR.me API
 *
 * Supported content types:
 *   - text        : Plain text / long text
 *   - vcard       : Business VCard (vCard 3.0 / 4.0)
 *   - event       : Event (vCalendar / iCalendar)
 *   - url         : Website URL
 *   - wifi        : Wi-Fi network credentials
 *   - phone       : Phone number (tel: URI)
 *
 * Supports all goQR.me parameters:
 *   data, size, charset-source, charset-target, ecc,
 *   color, bgcolor, margin, qzone, format
 *
 * @see https://goqr.me/api/doc/create-qr-code/
 */

// Set CORS headers for cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

class QrCodeGenerator {

    /**
     * Allowed goQR.me output formats
     */
    const ALLOWED_FORMATS = ['png', 'gif', 'jpeg', 'jpg', 'svg', 'eps'];

    /**
     * Allowed ECC levels
     */
    const ALLOWED_ECC = ['L', 'M', 'Q', 'H'];

    /**
     * Allowed charsets
     */
    const ALLOWED_CHARSETS = ['UTF-8', 'ISO-8859-1'];

    /**
     * Build the payload string for a given type and fields.
     *
     * @param string $type   Content type
     * @param array  $fields Raw form fields
     * @return string        Payload string that will be encoded into the QR
     * @throws InvalidArgumentException
     */
    public function buildPayload($type, array $fields) {
        switch (strtolower($type)) {
            case 'text':
                return $this->buildText($fields);

            case 'vcard':
                return $this->buildVCard($fields);

            case 'event':
                return $this->buildEvent($fields);

            case 'url':
                return $this->buildUrl($fields);

            case 'wifi':
                return $this->buildWifi($fields);

            case 'phone':
                return $this->buildPhone($fields);

            default:
                throw new InvalidArgumentException(
                    "Unsupported type '{$type}'. Allowed: text, vcard, event, url, wifi, phone"
                );
        }
    }

    // ---------------------------------------------------------------
    // Payload builders for each content type
    // ---------------------------------------------------------------

    private function buildText(array $f) {
        $text = trim((string)($f['text'] ?? ''));
        if ($text === '') {
            throw new InvalidArgumentException('Field "text" is required for type=text');
        }
        return $text;
    }

    private function buildUrl(array $f) {
        $url = trim((string)($f['url'] ?? ''));
        if ($url === '') {
            throw new InvalidArgumentException('Field "url" is required for type=url');
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }
        return $url;
    }

    private function buildPhone(array $f) {
        $phone = trim((string)($f['phone'] ?? ''));
        if ($phone === '') {
            throw new InvalidArgumentException('Field "phone" is required for type=phone');
        }
        return 'tel:' . preg_replace('/[^\d+]/', '', $phone);
    }

    /**
     * Build a vCard 3.0 string from the supplied fields.
     * Supports both individual name fields and an "organization" field.
     */
    private function buildVCard(array $f) {
        $get = function ($key) use ($f) {
            return isset($f[$key]) ? trim((string)$f[$key]) : '';
        };

        $firstName  = $get('first_name');
        $lastName   = $get('last_name');
        $org        = $get('organization');
        $title      = $get('title');
        $workEmail  = $get('work_email');
        $homeEmail  = $get('home_email');
        $workPhone  = $get('work_phone');
        $homePhone  = $get('home_phone');
        $mobile     = $get('mobile');
        $fax        = $get('fax');
        $website    = $get('website');
        $address    = $get('address');
        $city       = $get('city');
        $region     = $get('region');
        $postcode   = $get('postcode');
        $country    = $get('country');
        $note       = $get('note');

        if ($firstName === '' && $lastName === '' && $org === '') {
            throw new InvalidArgumentException(
                'vCard requires at least First Name + Last Name, or Organization'
            );
        }

        $fullName = trim($firstName . ' ' . $lastName);
        if ($fullName === '') {
            $fullName = $org;
        }

        $lines = [];
        $lines[] = 'BEGIN:VCARD';
        $lines[] = 'VERSION:3.0';
        $lines[] = 'N:' . $this->vEscape($lastName) . ';' . $this->vEscape($firstName) . ';;;';
        $lines[] = 'FN:' . $this->vEscape($fullName);

        if ($org !== '')   { $lines[] = 'ORG:' . $this->vEscape($org); }
        if ($title !== '') { $lines[] = 'TITLE:' . $this->vEscape($title); }

        if ($workPhone !== '') { $lines[] = 'TEL;TYPE=WORK,VOICE:' . $this->vEscape($workPhone); }
        if ($homePhone !== '') { $lines[] = 'TEL;TYPE=HOME,VOICE:' . $this->vEscape($homePhone); }
        if ($mobile !== '')    { $lines[] = 'TEL;TYPE=CELL,VOICE:' . $this->vEscape($mobile); }
        if ($fax !== '')       { $lines[] = 'TEL;TYPE=FAX:' . $this->vEscape($fax); }

        if ($workEmail !== '') { $lines[] = 'EMAIL;TYPE=WORK:' . $this->vEscape($workEmail); }
        if ($homeEmail !== '') { $lines[] = 'EMAIL;TYPE=HOME:' . $this->vEscape($homeEmail); }

        if ($website !== '')   { $lines[] = 'URL:' . $this->vEscape($website); }

        if ($address !== '' || $city !== '' || $region !== '' || $postcode !== '' || $country !== '') {
            $adr = implode(';', [
                $this->vEscape(''),              // PO Box
                $this->vEscape(''),              // Extended
                $this->vEscape($address),
                $this->vEscape($city),
                $this->vEscape($region),
                $this->vEscape($postcode),
                $this->vEscape($country),
            ]);
            $lines[] = 'ADR;TYPE=WORK:' . $adr;
        }

        if ($note !== '') { $lines[] = 'NOTE:' . $this->vEscape($note); }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines);
    }

    /**
     * Build a vCalendar (iCalendar) event string.
     */
    private function buildEvent(array $f) {
        $summary  = trim((string)($f['summary'] ?? ''));
        $location = trim((string)($f['location'] ?? ''));
        $description = trim((string)($f['description'] ?? ''));
        $start    = trim((string)($f['start'] ?? ''));
        $end      = trim((string)($f['end'] ?? ''));

        if ($summary === '') {
            throw new InvalidArgumentException('Field "summary" is required for type=event');
        }
        if ($start === '') {
            throw new InvalidArgumentException('Field "start" is required for type=event');
        }

        $dtStart = $this->formatIcsDate($start, false);
        $dtEnd   = $end !== '' ? $this->formatIcsDate($end, false) : $this->formatIcsDate($start, true);
        $dtStamp = gmdate('Ymd\THis\Z');
        $uid     = bin2hex(random_bytes(8)) . '@myapis.local';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//myapis//QR Code Generator//EN',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . $dtStamp,
            'DTSTART:' . $dtStart,
            'DTEND:'   . $dtEnd,
            'SUMMARY:' . $this->vEscape($summary),
        ];

        if ($location !== '') {
            $lines[] = 'LOCATION:' . $this->vEscape($location);
        }
        if ($description !== '') {
            $lines[] = 'DESCRIPTION:' . $this->vEscape($description);
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines);
    }

    /**
     * Build a Wi-Fi configuration string per the widely used
     * "WIFI:T:WPA;S:<ssid>;P:<password>;H:<true|false>;" spec.
     */
    private function buildWifi(array $f) {
        $ssid       = trim((string)($f['ssid'] ?? ''));
        $password   = trim((string)($f['password'] ?? ''));
        $encryption = strtoupper(trim((string)($f['encryption'] ?? 'WPA')));
        $hidden     = !empty($f['hidden']) ? 'true' : 'false';

        if ($ssid === '') {
            throw new InvalidArgumentException('Field "ssid" is required for type=wifi');
        }

        $allowedEnc = ['WPA', 'WEP', 'nopass'];
        if (!in_array($encryption, $allowedEnc, true)) {
            throw new InvalidArgumentException(
                "Field 'encryption' must be one of: " . implode(', ', $allowedEnc)
            );
        }

        // Escape special characters per the WIFI: spec
        $escape = function ($s) {
            return str_replace(['\\', ';', ',', ':', '"'], ['\\\\', '\\;', '\\,', '\\:', '\\"'], $s);
        };

        $payload = 'WIFI:T:' . $encryption . ';S:' . $escape($ssid) . ';';
        if ($encryption !== 'nopass' && $password !== '') {
            $payload .= 'P:' . $escape($password) . ';';
        }
        $payload .= 'H:' . $hidden . ';';

        return $payload;
    }

    // ---------------------------------------------------------------
    // goQR.me integration
    // ---------------------------------------------------------------

    /**
     * Build the goQR.me API URL from the payload + goQR parameters.
     */
    public function buildQrUrl($payload, array $params = []) {
        $defaults = [
            'size'          => 300,
            'ecc'           => 'M',
            'format'        => 'png',
            'qzone'         => 2,
            'margin'        => 1,
            'charset-source' => 'UTF-8',
            'charset-target' => 'UTF-8',
            'color'         => '0-0-0',
            'bgcolor'       => '255-255-255',
        ];

        $params = array_merge($defaults, array_intersect_key($params, $defaults));

        // Sanitise
        $size  = max(10, min(1000, (int)$params['size']));
        $ecc   = in_array($params['ecc'], self::ALLOWED_ECC, true) ? $params['ecc'] : 'M';
        $fmt   = strtolower($params['format']);
        $fmt   = in_array($fmt, self::ALLOWED_FORMATS, true) ? $fmt : 'png';
        $qzone = max(0, min(100, (int)$params['qzone']));
        $margin = max(0, min(50, (int)$params['margin']));
        $cs     = in_array($params['charset-source'], self::ALLOWED_CHARSETS, true) ? $params['charset-source'] : 'UTF-8';
        $ct     = in_array($params['charset-target'], self::ALLOWED_CHARSETS, true) ? $params['charset-target'] : 'UTF-8';
        $color   = $this->sanitizeColor($params['color'], '0-0-0');
        $bgcolor = $this->sanitizeColor($params['bgcolor'], '255-255-255');

        $query = [
            'data'           => $payload,
            'size'           => $size . 'x' . $size,
            'ecc'            => $ecc,
            'format'         => $fmt,
            'qzone'          => $qzone,
            'margin'         => $margin,
            'charset-source' => $cs,
            'charset-target' => $ct,
            'color'          => $color,
            'bgcolor'        => $bgcolor,
        ];

        return 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query($query);
    }

    /**
     * Fetch the QR image bytes from goQR.me
     */
    public function fetchQrImage($qrUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $qrUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MyAPIs-QR-Generator/1.0');

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $code !== 200) {
            throw new RuntimeException('Failed to fetch QR code from goQR.me: ' . ($err ?: 'HTTP ' . $code));
        }
        return $body;
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Escape a value for inclusion in a vCard/vCalendar text field.
     */
    private function vEscape($value) {
        $value = str_replace(['\\', "\n", "\r"], ['\\\\', '\\n', ''], $value);
        // iCalendar line breaks inside text fields
        return $value;
    }

    /**
     * Convert a user-supplied datetime into the basic ICS format
     * "YYYYMMDDTHHMMSSZ" (UTC).  Accepts both "YYYY-MM-DD HH:MM" and
     * "YYYY-MM-DDTHH:MM" and "YYYY-MM-DD".
     */
    private function formatIcsDate($input, $addOneHour) {
        $input = trim($input);
        $input = str_replace('T', ' ', $input);
        $input = str_replace('/', '-', $input);

        $ts = strtotime($input);
        if ($ts === false) {
            throw new InvalidArgumentException("Invalid date '{$input}'. Use YYYY-MM-DD or YYYY-MM-DD HH:MM.");
        }
        if ($addOneHour) {
            $ts += 3600;
        }
        return gmdate('Ymd\THis\Z', $ts);
    }

    /**
     * Stop users from injecting weird characters into the colour params.
     */
    private function sanitizeColor($value, $default) {
        $value = (string)$value;
        // Hex form: 3 or 6 chars [a-fA-F0-9]
        if (preg_match('/^[a-fA-F0-9]{3}$/', $value) || preg_match('/^[a-fA-F0-9]{6}$/', $value)) {
            return $value;
        }
        // Decimal form: 0-255-0-255-0-255
        if (preg_match('/^\d{1,3}-\d{1,3}-\d{1,3}$/', $value)) {
            $parts = array_map('intval', explode('-', $value));
            foreach ($parts as $p) {
                if ($p < 0 || $p > 255) {
                    return $default;
                }
            }
            return $value;
        }
        return $default;
    }
}

// ---------------------------------------------------------------
// Request handling
// ---------------------------------------------------------------

try {
    $type   = strtolower(trim($_REQUEST['type'] ?? 'text'));
    $format = strtolower(trim($_REQUEST['format'] ?? 'json'));

    // All goQR parameters are read from the request (with safe defaults
    // applied inside buildQrUrl()).
    $goqr = [
        'size'           => $_REQUEST['size']            ?? 300,
        'ecc'            => $_REQUEST['ecc']             ?? 'M',
        'format'         => $_REQUEST['gformat']         ?? 'png',
        'qzone'          => $_REQUEST['qzone']           ?? 2,
        'margin'         => $_REQUEST['margin']          ?? 1,
        'charset-source' => $_REQUEST['charset_source']  ?? 'UTF-8',
        'charset-target' => $_REQUEST['charset_target']  ?? 'UTF-8',
        'color'          => $_REQUEST['color']           ?? '0-0-0',
        'bgcolor'        => $_REQUEST['bgcolor']         ?? '255-255-255',
    ];

    // Pull every field the user submitted (apart from the API control
    // keys) so the payload builder can pick the ones it needs.
    $reserved = [
        'type', 'format', 'size', 'ecc', 'gformat', 'qzone', 'margin',
        'charset_source', 'charset_target', 'color', 'bgcolor',
    ];
    $fields = [];
    foreach ($_REQUEST as $key => $value) {
        if (!in_array($key, $reserved, true)) {
            $fields[$key] = $value;
        }
    }

    // Also accept a JSON body for convenience
    if (empty($fields) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $type = $type ?: ($decoded['type'] ?? 'text');
                $fields = $decoded;
                // Override goQR params from JSON if supplied
                foreach ($goqr as $k => $v) {
                    if (isset($decoded[$k])) {
                        $goqr[$k] = $decoded[$k];
                    }
                }
            }
        }
    }

    $api = new QrCodeGenerator();
    $payload = $api->buildPayload($type, $fields);
    $qrUrl   = $api->buildQrUrl($payload, $goqr);

    if ($format === 'image' || $format === 'png') {
        $image = $api->fetchQrImage($qrUrl);
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="qr-code.png"');
        header('Cache-Control: public, max-age=3600');
        echo $image;
        exit;
    }

    if ($format === 'json' || $format === 'data') {
        $image   = $api->fetchQrImage($qrUrl);
        $base64  = 'data:image/png;base64,' . base64_encode($image);

        header('Content-Type: application/json');
        echo json_encode([
            'success'   => true,
            'message'   => 'QR code generated successfully',
            'type'      => $type,
            'payload'   => $payload,
            'qr_url'    => $base64,
            'goqr_url'  => $qrUrl,
            'params'    => [
                'size'           => (int)$goqr['size'],
                'ecc'            => $goqr['ecc'],
                'format'         => strtolower($goqr['format']),
                'qzone'          => (int)$goqr['qzone'],
                'margin'         => (int)$goqr['margin'],
                'charset-source' => $goqr['charset-source'],
                'charset-target' => $goqr['charset-target'],
                'color'          => $goqr['color'],
                'bgcolor'        => $goqr['bgcolor'],
            ],
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Unknown output format
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'error'   => 'Invalid format parameter',
        'message' => 'Supported formats: image, json',
    ]);
    exit;

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'error'   => 'Bad request',
        'message' => $e->getMessage(),
    ]);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error'   => 'Internal server error',
        'message' => $e->getMessage(),
    ]);
}
