<?php

/**
 * QrCodeGenerator class — extracted from the original index.php
 * and re-housed in its own file so the HTTP entry point stays thin.
 *
 * Behaviour is byte-for-byte identical to the previous implementation.
 */


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

        $org        = $get('organization');
        $note       = $get('note');

        // ----------------------------------------------------------------
        // Collect dynamic name parts (like the email/phone lists).
        // Supported types: first_name, middle_name, last_name, prefix,
        // suffix, nickname.
        //
        // Legacy single-value fields (first_name, last_name) are also
        // accepted for backwards compatibility and merged into the list.
        // ----------------------------------------------------------------
        $nameMap = [
            'first_name'  => ['vcard' => 'GIVEN',      'label' => 'First Name'],
            'middle_name' => ['vcard' => 'ADDITIONAL', 'label' => 'Middle Name'],
            'last_name'   => ['vcard' => 'FAMILY',     'label' => 'Last Name'],
            'prefix'      => ['vcard' => 'PREFIX',     'label' => 'Prefix'],
            'suffix'      => ['vcard' => 'SUFFIX',     'label' => 'Suffix'],
        ];
        $nickMap = [
            'nickname'    => ['vcard' => 'NICKNAME',   'label' => 'Nick Name'],
        ];

        $nameParts = [];   // ordered: prefix, first, middle, last, suffix
        $nicknames = [];

        // Legacy single-value fields go first so they end up at the top
        // of the rendered list.
        foreach (['prefix', 'first_name', 'middle_name', 'last_name', 'suffix'] as $legacyKey) {
            $val = $get($legacyKey);
            if ($val !== '') {
                $nameParts[] = ['type' => $legacyKey, 'value' => $val];
            }
        }
        if ($get('nickname') !== '') {
            $nicknames[] = ['type' => 'nickname', 'value' => $get('nickname')];
        }

        // Merge in dynamic rows (names[][type]=first_name&names[][value]=...)
        $nameParts  = array_merge($nameParts,  $this->collectDynamicItems($f, 'names'));
        $nicknames  = array_merge($nicknames,  $this->collectDynamicItems($f, 'nicknames'));

        // Normalise: any unknown type defaults to "first_name" so the
        // vCard still has a GIVEN field rather than dropping the value.
        $pickValue = function (array $row) use ($nameMap) {
            $v = isset($row['value']) ? trim((string)$row['value']) : '';
            if ($v === '') { return null; }
            $t = isset($row['type']) ? strtolower(trim((string)$row['type'])) : 'first_name';
            if (!isset($nameMap[$t])) { $t = 'first_name'; }
            return ['type' => $t, 'value' => $v];
        };
        $pickNick = function (array $row) {
            $v = isset($row['value']) ? trim((string)$row['value']) : '';
            if ($v === '') { return null; }
            $t = isset($row['type']) ? strtolower(trim((string)$row['type'])) : 'nickname';
            return ['type' => $t, 'value' => $v];
        };

        $cleanedNames = [];
        foreach ($nameParts as $row) {
            $p = $pickValue(is_array($row) ? $row : ['value' => $row]);
            if ($p !== null) { $cleanedNames[] = $p; }
        }
        $cleanedNicks = [];
        foreach ($nicknames as $row) {
            $p = $pickNick(is_array($row) ? $row : ['value' => $row]);
            if ($p !== null) { $cleanedNicks[] = $p; }
        }

        // Build N field in vCard order: Family;Given;Additional;Prefix;Suffix
        $pickOne = function ($type) use ($cleanedNames) {
            foreach ($cleanedNames as $n) {
                if ($n['type'] === $type) { return $n['value']; }
            }
            return '';
        };
        $family     = $pickOne('last_name');
        $given      = $pickOne('first_name');
        $additional = $pickOne('middle_name');
        $prefix     = $pickOne('prefix');
        $suffix     = $pickOne('suffix');

        // Build a human-friendly full name
        $displayOrder = ['prefix', 'first_name', 'middle_name', 'last_name', 'suffix'];
        $fullName = '';
        foreach ($displayOrder as $k) {
            foreach ($cleanedNames as $n) {
                if ($n['type'] === $k) {
                    $fullName = $fullName === '' ? $n['value'] : $fullName . ' ' . $n['value'];
                    break;
                }
            }
        }

        // Check that we have at least something useful
        $hasName = $family !== '' || $given !== '' || $additional !== '' || $prefix !== '' || $suffix !== '';
        $hasNicks = !empty($cleanedNicks);

        if (!$hasName && $org === '' && !$hasNicks) {
            throw new InvalidArgumentException(
                'vCard requires at least one name part, a nickname, or Organization'
            );
        }
        if ($fullName === '') {
            $fullName = $org;
        }
        if ($fullName === '' && $hasNicks) {
            $fullName = $cleanedNicks[0]['value'];
        }

        $lines = [];
        $lines[] = 'BEGIN:VCARD';
        $lines[] = 'VERSION:3.0';
        $lines[] = 'N:' . $this->vEscape($family) . ';' . $this->vEscape($given) . ';' . $this->vEscape($additional) . ';' . $this->vEscape($prefix) . ';' . $this->vEscape($suffix);
        $lines[] = 'FN:' . $this->vEscape($fullName);

        if (!empty($cleanedNicks)) {
            $nickValues = array_map(function ($n) { return $n['value']; }, $cleanedNicks);
            $lines[] = 'NICKNAME:' . $this->vEscape(implode(',', $nickValues));
        }

        if ($org !== '')   { $lines[] = 'ORG:' . $this->vEscape($org); }

        // Honour the explicit Title from a dynamic name row OR a legacy
        // "title" field.  The first one wins so the user has full control.
        $titleFromName = null;
        foreach ($cleanedNames as $n) {
            if ($n['type'] === 'title') { $titleFromName = $n['value']; break; }
        }
        $finalTitle = $titleFromName !== null ? $titleFromName : $get('title');
        if ($finalTitle !== '') { $lines[] = 'TITLE:' . $this->vEscape($finalTitle); }

        // ----------------------------------------------------------------
        // Backwards-compatible single fields.  They are merged into the
        // dynamic collections so the same data ends up in the payload.
        // ----------------------------------------------------------------
        $legacyEmails = [];
        if ($get('work_email') !== '') { $legacyEmails[] = ['type' => 'WORK',  'value' => $get('work_email')]; }
        if ($get('home_email') !== '') { $legacyEmails[] = ['type' => 'HOME',  'value' => $get('home_email')]; }

        $legacyPhones = [];
        if ($get('work_phone') !== '') { $legacyPhones[] = ['type' => 'WORK,VOICE', 'value' => $get('work_phone')]; }
        if ($get('home_phone') !== '') { $legacyPhones[] = ['type' => 'HOME,VOICE', 'value' => $get('home_phone')]; }
        if ($get('mobile')     !== '') { $legacyPhones[] = ['type' => 'CELL,VOICE', 'value' => $get('mobile')]; }
        if ($get('fax')        !== '') { $legacyPhones[] = ['type' => 'FAX',        'value' => $get('fax')]; }

        $legacyUrls = [];
        if ($get('website') !== '') { $legacyUrls[] = ['value' => $get('website')]; }

        $legacyAddresses = [];
        if ($get('address') !== '' || $get('city') !== '' || $get('region') !== ''
            || $get('postcode') !== '' || $get('country') !== '') {
            $legacyAddresses[] = [
                'type'    => 'WORK',
                'po_box'  => '',
                'ext'     => '',
                'street'  => $get('address'),
                'city'    => $get('city'),
                'region'  => $get('region'),
                'postcode'=> $get('postcode'),
                'country' => $get('country'),
            ];
        }

        $emails   = array_merge($legacyEmails,   $this->collectDynamicItems($f, 'emails'));
        $phones   = array_merge($legacyPhones,   $this->collectDynamicItems($f, 'phones'));
        $urls     = array_merge($legacyUrls,     $this->collectDynamicItems($f, 'urls'));
        $addresses= array_merge($legacyAddresses,$this->collectDynamicItems($f, 'addresses'));

        foreach ($emails as $e) {
            if (empty($e['value'])) { continue; }
            $type = $this->sanitizeType(!empty($e['type']) ? $e['type'] : 'INTERNET');
            $lines[] = 'EMAIL;TYPE=' . $type . ':' . $this->vEscape($e['value']);
        }

        foreach ($phones as $p) {
            if (empty($p['value'])) { continue; }
            $type = $this->sanitizeType(!empty($p['type']) ? $p['type'] : 'VOICE');
            $lines[] = 'TEL;TYPE=' . $type . ':' . $this->vEscape($p['value']);
        }

        foreach ($urls as $u) {
            if (empty($u['value'])) { continue; }
            $lines[] = 'URL:' . $this->vEscape($u['value']);
        }

        foreach ($addresses as $a) {
            // Skip entirely empty addresses
            $hasAny = false;
            foreach (['po_box', 'ext', 'street', 'city', 'region', 'postcode', 'country'] as $k) {
                if (!empty($a[$k])) { $hasAny = true; break; }
            }
            if (!$hasAny) { continue; }
            $type = !empty($a['type']) ? $a['type'] : 'WORK';
            $adr = implode(';', [
                $this->vEscape($a['po_box']   ?? ''),
                $this->vEscape($a['ext']      ?? ''),
                $this->vEscape($a['street']   ?? ''),
                $this->vEscape($a['city']     ?? ''),
                $this->vEscape($a['region']   ?? ''),
                $this->vEscape($a['postcode'] ?? ''),
                $this->vEscape($a['country']  ?? ''),
            ]);
            $lines[] = 'ADR;TYPE=' . $this->vEscape($type) . ':' . $adr;
        }

        if ($note !== '') { $lines[] = 'NOTE:' . $this->vEscape($note); }

        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines);
    }

    /**
     * Collect dynamic vCard items from the request payload.
     *
     * Supports two input shapes:
     *   1) emails[][type]=WORK&emails[][value]=...
     *   2) emails[0][type]=WORK&emails[0][value]=...
     *
     * @return array<int,array<string,string>>
     */
    private function collectDynamicItems(array $allFields, string $key) {
        $items = [];
        // Look at the raw request (since dynamic lists may be POSTed with
        // bracketed keys that PHP populates into $_REQUEST)
        $source = array_merge($_GET, $_POST);

        if (isset($source[$key]) && is_array($source[$key])) {
            foreach ($source[$key] as $entry) {
                if (is_array($entry)) {
                    $items[] = array_map('strval', $entry);
                } elseif (is_string($entry) && trim($entry) !== '') {
                    // Bare string — treat as a default-typed value
                    $items[] = ['value' => $entry];
                }
            }
        }
        return $items;
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
     * Normalise a vCard TYPE parameter value (e.g. "work,voice" or a
     * custom type).  vCard 3.0 (RFC 2426) type values are case-insensitive
     * tokens restricted to ALPHA / DIGIT / "-" (iana-token or "X-" x-name),
     * and may be comma-separated.  We uppercase for convention and strip
     * anything that is not a valid token character so custom values still
     * produce well-formed output.
     */
    private function sanitizeType($type) {
        $type = strtoupper(trim((string)$type));
        if ($type === '') { return ''; }
        $parts = array_map('trim', explode(',', $type));
        $clean = [];
        foreach ($parts as $p) {
            $p = preg_replace('/[^A-Z0-9\-]/', '', $p);
            if ($p !== '') { $clean[] = $p; }
        }
        return implode(',', $clean);
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
