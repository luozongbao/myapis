<?php
/**
 * QR Code Generator - Frontend
 * Web UI for generating QR codes with multiple content types
 *
 * Features:
 *   - Multiple content types: text, URL, vCard, event, Wi-Fi, phone
 *   - File type selector: PNG / SVG
 *   - Foreground & background colour pickers
 *   - Dynamic vCard fields (add unlimited emails / phones / URLs / addresses)
 */

$error = '';
$success = false;
$qrCodeUrl = '';
$qrMime    = 'image/png';
$payload   = '';
$selectedType = $_POST['type'] ?? 'text';
$fileType     = $_POST['file_type'] ?? 'png';

// ----------------------------------------------------------------
// Pull simple form fields
// ----------------------------------------------------------------
$fields = [
    'text'         => $_POST['text']         ?? '',
    'url'          => $_POST['url']          ?? '',
    'phone'        => $_POST['phone']        ?? '',
    'summary'      => $_POST['summary']      ?? '',
    'location'     => $_POST['location']     ?? '',
    'description'  => $_POST['description']  ?? '',
    'start'        => $_POST['start']        ?? '',
    'end'          => $_POST['end']          ?? '',
    'ssid'         => $_POST['ssid']         ?? '',
    'password'     => $_POST['password']     ?? '',
    'encryption'   => $_POST['encryption']   ?? 'WPA',
    'hidden'       => isset($_POST['hidden']) ? 1 : 0,
    'first_name'   => $_POST['first_name']   ?? '',
    'middle_name'  => $_POST['middle_name']  ?? '',
    'last_name'    => $_POST['last_name']    ?? '',
    'prefix'       => $_POST['prefix']       ?? '',
    'suffix'       => $_POST['suffix']       ?? '',
    'nickname'     => $_POST['nickname']     ?? '',
    'organization' => $_POST['organization'] ?? '',
    'title'        => $_POST['title']        ?? '',
    'work_email'   => $_POST['work_email']   ?? '',
    'home_email'   => $_POST['home_email']   ?? '',
    'work_phone'   => $_POST['work_phone']   ?? '',
    'home_phone'   => $_POST['home_phone']   ?? '',
    'mobile'       => $_POST['mobile']       ?? '',
    'fax'          => $_POST['fax']          ?? '',
    'website'      => $_POST['website']      ?? '',
    'address'      => $_POST['address']      ?? '',
    'city'         => $_POST['city']         ?? '',
    'region'       => $_POST['region']       ?? '',
    'postcode'     => $_POST['postcode']     ?? '',
    'country'      => $_POST['country']      ?? '',
    'note'         => $_POST['note']         ?? '',
];

// QR appearance parameters
$ecc     = $_POST['ecc']     ?? 'M';
$size    = (int)($_POST['size']  ?? 300);
$qzone   = (int)($_POST['qzone'] ?? 2);
$margin  = (int)($_POST['margin'] ?? 1);
$color   = $_POST['color']   ?? '000000';
$bgcolor = $_POST['bgcolor'] ?? 'ffffff';

// ----------------------------------------------------------------
// Handle form submission
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Build request to our own API endpoint
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'];
    $apiUrl   = $protocol . '://' . $host . '/api/qr-code-generator/?format=json';

    $postData = array_merge(
        ['type' => $selectedType, 'ecc' => $ecc, 'size' => $size, 'qzone' => $qzone,
         'margin' => $margin, 'color' => $color, 'bgcolor' => $bgcolor,
         'file_type' => $fileType],
        $fields
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($response === false || !empty($curlErr)) {
        $error = 'Failed to connect to API: ' . $curlErr;
    } else {
        $data = json_decode($response, true);
        if ($httpCode === 200 && $data && !empty($data['success'])) {
            $success   = true;
            $qrCodeUrl = $data['qr_url'];
            $qrMime    = strtolower($data['file_type'] ?? 'png');
            $payload   = $data['payload'];
        } else {
            $error = $data['message'] ?? $data['error'] ?? 'Unknown error occurred';
        }
    }
}

// Pre-populate dynamic vCard rows from the previous submission so the
// form is "sticky" (matches the behaviour of the simple fields).
$dynEmails    = $_POST['emails']    ?? [];
$dynPhones    = $_POST['phones']    ?? [];
$dynUrls      = $_POST['urls']      ?? [];
$dynAddresses = $_POST['addresses'] ?? [];
$dynNames     = $_POST['names']     ?? [];
$dynNicknames = $_POST['nicknames'] ?? [];

if (!is_array($dynEmails))    { $dynEmails    = []; }
if (!is_array($dynPhones))    { $dynPhones    = []; }
if (!is_array($dynUrls))      { $dynUrls      = []; }
if (!is_array($dynAddresses)) { $dynAddresses = []; }
if (!is_array($dynNames))     { $dynNames     = []; }
if (!is_array($dynNicknames)) { $dynNicknames = []; }

// On first load (GET), seed each list with a single empty row so the UI
// shows them by default.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $dynEmails    = [[]];
    $dynPhones    = [[]];
    $dynUrls      = [[]];
    $dynAddresses = [[]];
    $dynNames     = [['type' => 'first_name']];
    $dynNicknames = [[]];
}

// Helpers to read a value from a posted dynamic row
$dVal = function ($row, $key, $default = '') {
    if (!is_array($row)) { return $default; }
    return isset($row[$key]) ? (string)$row[$key] : $default;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
            align-items: start;
        }

        .header {
            grid-column: 1 / -1;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto;
        }

        .header .badge-row {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-block;
            background: #f0f0f0;
            color: #555;
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .form-section, .preview-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 10px;
            margin-bottom: 25px;
        }

        .type-btn {
            background: #f5f7fa;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 15px 10px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #555;
            transition: all 0.25s ease;
            font-size: 0.9em;
        }

        .type-btn:hover { transform: translateY(-2px); background: #e8ecf5; }
        .type-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 20px rgba(102,126,234,0.35);
        }
        .type-btn .ico { font-size: 1.6em; display: block; margin-bottom: 6px; }

        .field-group { display: none; animation: fadeIn 0.3s ease; }
        .field-group.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .row-3 {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 0.95em;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #667eea;
            outline: none;
        }

        .form-group textarea { min-height: 90px; resize: vertical; }

        .section-title {
            font-size: 1.1em;
            font-weight: 700;
            color: #444;
            margin: 20px 0 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #667eea;
            display: inline-block;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95em;
            color: #555;
        }

        .checkbox-row input { width: auto; }

        /* ============== Dynamic vCard rows ============== */
        .dyn-list {
            margin-bottom: 10px;
        }
        .dyn-row {
            background: #f8f9fc;
            border: 1px solid #e3e7ee;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            position: relative;
            animation: fadeIn 0.25s ease;
        }
        .dyn-row .row { gap: 10px; margin-bottom: 0; }

        .add-row {
            background: #f0f3f9;
            border: 2px dashed #b6c2d6;
            color: #4a5568;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 6px;
            width: 100%;
            transition: all 0.2s;
        }
        .add-row:hover {
            background: #e3e9f3;
            border-color: #667eea;
            color: #667eea;
        }

        .remove-row {
            background: #fff;
            border: 1px solid #f1c4c4;
            color: #c0392b;
            border-radius: 8px;
            width: 38px;
            height: 38px;
            cursor: pointer;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .remove-row:hover {
            background: #fde8e8;
            border-color: #c0392b;
        }

        .dyn-row.address .row { grid-template-columns: 2fr 1fr 1fr; gap: 10px; }
        .dyn-row.address .row + .row { grid-template-columns: 1fr 1fr 1fr; gap: 10px; }

        /* ============== Color picker ============== */
        .color-picker-row {
            display: grid;
            grid-template-columns: 60px 1fr;
            gap: 10px;
            align-items: center;
        }
        .color-picker-row input[type="color"] {
            width: 60px;
            height: 42px;
            padding: 2px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            cursor: pointer;
            background: white;
        }
        .color-picker-row input[type="text"] {
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
        }
        .swatch {
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 4px;
            vertical-align: middle;
            margin-right: 6px;
            border: 1px solid #ccc;
        }

        /* ============== Advanced ============== */
        .advanced-toggle {
            background: #f8f9fa;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            color: #555;
            margin: 10px 0;
            width: 100%;
            text-align: left;
        }
        .advanced-toggle:hover { background: #e9ecef; }

        .advanced { display: none; }
        .advanced.open { display: block; }

        .generate-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.05em;
            font-weight: 700;
            cursor: pointer;
            margin-top: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102,126,234,0.4);
        }

        .preview-section { position: sticky; top: 20px; }

        .qr-display {
            text-align: center;
            padding: 25px;
            background: #f9fafc;
            border-radius: 14px;
            border: 2px dashed #d0d7e3;
        }

        .qr-display img, .qr-display svg {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            background: white;
            padding: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .qr-placeholder {
            padding: 60px 20px;
            color: #999;
            font-size: 0.95em;
        }
        .qr-placeholder .ico { font-size: 3em; margin-bottom: 10px; }

        .download-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 11px 22px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .download-btn:hover { background: #1e7e34; }

        .payload-info {
            margin-top: 18px;
            padding: 12px;
            background: #f1f3f5;
            border-radius: 8px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #444;
            max-height: 160px;
            overflow-y: auto;
            text-align: left;
        }

        .error {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9em;
            line-height: 1.5;
        }

        .breadcrumb {
            grid-column: 1 / -1;
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
            color: #666;
            flex-wrap: wrap;
        }
        .breadcrumb a { color: #667eea; text-decoration: none; font-weight: 600; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .right { margin-left: auto; display: flex; gap: 10px; flex-wrap: wrap; }
        .breadcrumb .pill {
            padding: 6px 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #ddd;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .container { grid-template-columns: 1fr; }
            .preview-section { position: static; }
            .row, .row-3 { grid-template-columns: 1fr; }
            .dyn-row.address .row,
            .dyn-row.address .row + .row { grid-template-columns: 1fr; }
        }
    </style>
<?php /** MyAPIs Analytics (Hostinger / shared-hosting friendly) */ if (file_exists(__DIR__ . "/analytics.php")) { require __DIR__ . "/analytics.php"; } ?>
</head>
<body>
    <div class="container">

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">← Back to Main</a>
            <span>/</span>
            <span>QR Code Generator</span>
            <div class="right">
                <a href="/api/qr-code-generator/" class="pill">🔗 API</a>
                <a href="api-specs/qr-code-generator.php" class="pill">📚 API Docs</a>
            </div>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>📱 QR Code Generator</h1>
            <p>Create professional QR codes for text, URLs, contacts, events, Wi-Fi networks, and phone numbers — powered by the goQR.me API.</p>
            <div class="badge-row">
                <span class="badge">⚡ Powered by goQR.me</span>
                <span class="badge">🎨 Customisable</span>
                <span class="badge">📥 PNG &amp; SVG</span>
                <span class="badge">📱 Mobile Ready</span>
            </div>
        </div>

        <!-- Form -->
        <div class="form-section">
            <div class="info">
                <strong>ℹ️ Quick start:</strong> Pick a content type below, fill in the fields, then hit <em>Generate</em>.
                The raw payload (what the QR encodes) is shown beside the preview.
            </div>

            <form method="POST" id="qrForm">
                <!-- Type selector -->
                <div class="type-selector">
                    <div class="type-btn <?= $selectedType === 'text' ? 'active' : '' ?>"  data-type="text">  <span class="ico">📝</span> Text</div>
                    <div class="type-btn <?= $selectedType === 'url' ? 'active' : '' ?>"   data-type="url">   <span class="ico">🌐</span> URL</div>
                    <div class="type-btn <?= $selectedType === 'vcard' ? 'active' : '' ?>" data-type="vcard"> <span class="ico">👤</span> vCard</div>
                    <div class="type-btn <?= $selectedType === 'event' ? 'active' : '' ?>" data-type="event"> <span class="ico">📅</span> Event</div>
                    <div class="type-btn <?= $selectedType === 'wifi' ? 'active' : '' ?>"  data-type="wifi">  <span class="ico">📶</span> Wi-Fi</div>
                    <div class="type-btn <?= $selectedType === 'phone' ? 'active' : '' ?>" data-type="phone"> <span class="ico">📞</span> Phone</div>
                </div>

                <input type="hidden" name="type" id="typeInput" value="<?= htmlspecialchars($selectedType) ?>">

                <!-- ============= TEXT ============= -->
                <div class="field-group <?= $selectedType === 'text' ? 'active' : '' ?>" data-group="text">
                    <div class="form-group">
                        <label for="text">Plain Text / Long Text</label>
                        <textarea id="text" name="text" placeholder="Type any text, URL, message or note..."><?= htmlspecialchars($fields['text']) ?></textarea>
                    </div>
                </div>

                <!-- ============= URL ============= -->
                <div class="field-group <?= $selectedType === 'url' ? 'active' : '' ?>" data-group="url">
                    <div class="form-group">
                        <label for="url">Website URL</label>
                        <input type="url" id="url" name="url" placeholder="https://example.com"
                               value="<?= htmlspecialchars($fields['url']) ?>">
                    </div>
                </div>

                <!-- ============= PHONE ============= -->
                <div class="field-group <?= $selectedType === 'phone' ? 'active' : '' ?>" data-group="phone">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="+66 81 234 5678"
                               value="<?= htmlspecialchars($fields['phone']) ?>">
                    </div>
                </div>

                <!-- ============= VCARD ============= -->
                <div class="field-group <?= $selectedType === 'vcard' ? 'active' : '' ?>" data-group="vcard">
                    <div class="section-title">👤 Name</div>
                    <div class="dyn-list" data-list="names">
                        <?php
                        $nameOptions = [
                            'first_name'  => 'First Name',
                            'middle_name' => 'Middle Name',
                            'last_name'   => 'Last Name',
                            'prefix'      => 'Prefix (e.g. Mr., Dr.)',
                            'suffix'      => 'Suffix (e.g. Jr., PhD)',
                        ];
                        foreach ($dynNames as $i => $row):
                            $curType = $dVal($row, 'type', 'first_name');
                            if (!isset($nameOptions[$curType])) { $curType = 'first_name'; }
                        ?>
                            <div class="dyn-row name" data-row>
                                <div class="row-3">
                                    <div class="form-group" style="margin: 0;">
                                        <input type="text" name="names[<?= $i ?>][value]"
                                               placeholder="e.g. John"
                                               value="<?= htmlspecialchars($dVal($row, 'value')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <select name="names[<?= $i ?>][type]">
                                            <?php foreach ($nameOptions as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $curType === $val ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="remove-row" data-remove title="Remove name part">✕</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-row" data-add="names">+ Add another name part</button>

                    <div class="section-title">🏷️ Nick Names</div>
                    <div class="dyn-list" data-list="nicknames">
                        <?php foreach ($dynNicknames as $i => $row): ?>
                            <div class="dyn-row nickname" data-row>
                                <div class="row-3">
                                    <div class="form-group" style="margin: 0;">
                                        <input type="text" name="nicknames[<?= $i ?>][value]"
                                               placeholder="e.g. Johnny"
                                               value="<?= htmlspecialchars($dVal($row, 'value')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <input type="hidden" name="nicknames[<?= $i ?>][type]" value="nickname">
                                        <input type="text" value="Nick Name" disabled style="background:#eef0f5; color:#666;">
                                    </div>
                                    <button type="button" class="remove-row" data-remove title="Remove nickname">✕</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-row" data-add="nicknames">+ Add another nick name</button>

                    <div class="section-title">Organisation</div>
                    <div class="row">
                        <div class="form-group">
                            <label for="organization">Company</label>
                            <input type="text" id="organization" name="organization"
                                   value="<?= htmlspecialchars($fields['organization']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="title">Job Title</label>
                            <input type="text" id="title" name="title"
                                   value="<?= htmlspecialchars($fields['title']) ?>">
                        </div>
                    </div>

                    <div class="section-title">📧 Email Addresses</div>
                    <div class="dyn-list" data-list="emails">
                        <?php foreach ($dynEmails as $i => $row): ?>
                            <div class="dyn-row email" data-row>
                                <div class="row-3">
                                    <div class="form-group" style="margin: 0;">
                                        <input type="email" name="emails[<?= $i ?>][value]"
                                               placeholder="name@example.com"
                                               value="<?= htmlspecialchars($dVal($row, 'value')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <select name="emails[<?= $i ?>][type]">
                                            <?php
                                            $emailTypes = ['WORK', 'HOME', 'INTERNET'];
                                            $cur = $dVal($row, 'type', 'WORK');
                                            foreach ($emailTypes as $t): ?>
                                                <option value="<?= $t ?>" <?= $cur === $t ? 'selected' : '' ?>><?= $t ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="remove-row" data-remove title="Remove email">✕</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-row" data-add="emails">+ Add another email</button>

                    <div class="section-title">📞 Phone Numbers</div>
                    <div class="dyn-list" data-list="phones">
                        <?php foreach ($dynPhones as $i => $row): ?>
                            <div class="dyn-row phone" data-row>
                                <div class="row-3">
                                    <div class="form-group" style="margin: 0;">
                                        <input type="tel" name="phones[<?= $i ?>][value]"
                                               placeholder="+66 81 234 5678"
                                               value="<?= htmlspecialchars($dVal($row, 'value')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <select name="phones[<?= $i ?>][type]">
                                            <?php
                                            $phoneTypes = ['CELL,VOICE' => 'Mobile', 'WORK,VOICE' => 'Work', 'HOME,VOICE' => 'Home', 'FAX' => 'Fax', 'VOICE' => 'Voice'];
                                            $cur = $dVal($row, 'type', 'CELL,VOICE');
                                            foreach ($phoneTypes as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $cur === $val ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="remove-row" data-remove title="Remove phone">✕</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-row" data-add="phones">+ Add another phone</button>

                    <div class="section-title">🔗 Websites / URLs</div>
                    <div class="dyn-list" data-list="urls">
                        <?php foreach ($dynUrls as $i => $row): ?>
                            <div class="dyn-row url" data-row>
                                <div class="row-3">
                                    <div class="form-group" style="margin: 0;">
                                        <input type="url" name="urls[<?= $i ?>][value]"
                                               placeholder="https://example.com"
                                               value="<?= htmlspecialchars($dVal($row, 'value')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <input type="text" name="urls[<?= $i ?>][label]"
                                               placeholder="Label (optional)"
                                               value="<?= htmlspecialchars($dVal($row, 'label')) ?>">
                                    </div>
                                    <button type="button" class="remove-row" data-remove title="Remove URL">✕</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-row" data-add="urls">+ Add another URL</button>

                    <div class="section-title">🏠 Addresses</div>
                    <div class="dyn-list" data-list="addresses">
                        <?php foreach ($dynAddresses as $i => $row): ?>
                            <div class="dyn-row address" data-row>
                                <div class="row-3" style="margin-bottom: 10px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">Type</label>
                                        <select name="addresses[<?= $i ?>][type]">
                                            <?php
                                            $addrTypes = ['WORK' => 'Work', 'HOME' => 'Home', 'OTHER' => 'Other'];
                                            $cur = $dVal($row, 'type', 'WORK');
                                            foreach ($addrTypes as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= $cur === $val ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div></div>
                                    <button type="button" class="remove-row" data-remove title="Remove address">✕</button>
                                </div>
                                <div class="row">
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">Street</label>
                                        <input type="text" name="addresses[<?= $i ?>][street]"
                                               value="<?= htmlspecialchars($dVal($row, 'street')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">PO Box</label>
                                        <input type="text" name="addresses[<?= $i ?>][po_box]"
                                               value="<?= htmlspecialchars($dVal($row, 'po_box')) ?>">
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 8px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">City</label>
                                        <input type="text" name="addresses[<?= $i ?>][city]"
                                               value="<?= htmlspecialchars($dVal($row, 'city')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">Region / State</label>
                                        <input type="text" name="addresses[<?= $i ?>][region]"
                                               value="<?= htmlspecialchars($dVal($row, 'region')) ?>">
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 8px;">
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">Postcode</label>
                                        <input type="text" name="addresses[<?= $i ?>][postcode]"
                                               value="<?= htmlspecialchars($dVal($row, 'postcode')) ?>">
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label style="font-size:0.8em; color:#666;">Country</label>
                                        <input type="text" name="addresses[<?= $i ?>][country]"
                                               value="<?= htmlspecialchars($dVal($row, 'country')) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="add-row" data-add="addresses">+ Add another address</button>

                    <div class="section-title">Notes</div>
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea id="note" name="note"><?= htmlspecialchars($fields['note']) ?></textarea>
                    </div>
                </div>

                <!-- ============= EVENT ============= -->
                <div class="field-group <?= $selectedType === 'event' ? 'active' : '' ?>" data-group="event">
                    <div class="form-group">
                        <label for="summary">Event Title *</label>
                        <input type="text" id="summary" name="summary" placeholder="Team Meeting"
                               value="<?= htmlspecialchars($fields['summary']) ?>">
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="start">Start *</label>
                            <input type="datetime-local" id="start" name="start"
                                   value="<?= htmlspecialchars($fields['start']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="end">End</label>
                            <input type="datetime-local" id="end" name="end"
                                   value="<?= htmlspecialchars($fields['end']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="Room 304, Building A"
                               value="<?= htmlspecialchars($fields['location']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"><?= htmlspecialchars($fields['description']) ?></textarea>
                    </div>
                </div>

                <!-- ============= WIFI ============= -->
                <div class="field-group <?= $selectedType === 'wifi' ? 'active' : '' ?>" data-group="wifi">
                    <div class="form-group">
                        <label for="ssid">Network Name (SSID) *</label>
                        <input type="text" id="ssid" name="ssid" placeholder="MyWiFiNetwork"
                               value="<?= htmlspecialchars($fields['ssid']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" id="password" name="password" placeholder="supersecret"
                               value="<?= htmlspecialchars($fields['password']) ?>">
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="encryption">Encryption</label>
                            <select id="encryption" name="encryption">
                                <option value="WPA"    <?= $fields['encryption'] === 'WPA'    ? 'selected' : '' ?>>WPA / WPA2</option>
                                <option value="WEP"    <?= $fields['encryption'] === 'WEP'    ? 'selected' : '' ?>>WEP</option>
                                <option value="nopass" <?= $fields['encryption'] === 'nopass' ? 'selected' : '' ?>>No Password</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="checkbox-row">
                                <input type="checkbox" id="hidden" name="hidden" value="1" <?= $fields['hidden'] ? 'checked' : '' ?>>
                                <label for="hidden" style="margin: 0;">Hidden network</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============= APPEARANCE ============= -->
                <button type="button" class="advanced-toggle" id="advancedToggle">
                    ⚙️ Appearance &amp; File Settings
                </button>
                <div class="advanced" id="advancedPanel">

                    <div class="row">
                        <div class="form-group">
                            <label for="file_type">File Type</label>
                            <select id="file_type" name="file_type">
                                <option value="png"  <?= $fileType === 'png'  ? 'selected' : '' ?>>PNG — raster image</option>
                                <option value="svg"  <?= $fileType === 'svg'  ? 'selected' : '' ?>>SVG — scalable vector (ideal for print)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="size">Size (px)</label>
                            <input type="number" id="size" name="size" min="50" max="1000" step="10"
                                   value="<?= htmlspecialchars((string)$size) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="ecc">Error Correction</label>
                            <select id="ecc" name="ecc">
                                <option value="L" <?= $ecc === 'L' ? 'selected' : '' ?>>L — Low (~7%)</option>
                                <option value="M" <?= $ecc === 'M' ? 'selected' : '' ?>>M — Medium (~15%)</option>
                                <option value="Q" <?= $ecc === 'Q' ? 'selected' : '' ?>>Q — Quartile (~25%)</option>
                                <option value="H" <?= $ecc === 'H' ? 'selected' : '' ?>>H — High (~30%)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="margin">Margin (px)</label>
                            <input type="number" id="margin" name="margin" min="0" max="50"
                                   value="<?= htmlspecialchars((string)$margin) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label for="qzone">Quiet Zone (modules)</label>
                            <input type="number" id="qzone" name="qzone" min="0" max="100"
                                   value="<?= htmlspecialchars((string)$qzone) ?>">
                        </div>
                        <div class="form-group"></div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>
                                <span class="swatch" id="color_swatch" style="background:#000000;"></span>
                                Foreground (data modules)
                            </label>
                            <div class="color-picker-row">
                                <input type="color" id="color_picker" value="#<?= htmlspecialchars($color) ?>">
                                <input type="text" id="color" name="color" maxlength="6" placeholder="000000"
                                       value="<?= htmlspecialchars($color) ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                <span class="swatch" id="bgcolor_swatch" style="background:#ffffff;"></span>
                                Background
                            </label>
                            <div class="color-picker-row">
                                <input type="color" id="bgcolor_picker" value="#<?= htmlspecialchars($bgcolor) ?>">
                                <input type="text" id="bgcolor" name="bgcolor" maxlength="6" placeholder="FFFFFF"
                                       value="<?= htmlspecialchars($bgcolor) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="generate-btn">🚀 Generate QR Code</button>
            </form>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </div>

        <!-- Preview -->
        <div class="preview-section">
            <div class="qr-display">
                <?php if ($qrCodeUrl): ?>
                    <?php if ($qrMime === 'svg'): ?>
                        <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="Generated QR Code" style="image-rendering: auto;">
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="Generated QR Code">
                    <?php endif; ?>
                    <div>
                        <a href="<?= htmlspecialchars($qrCodeUrl) ?>"
                           download="qr-code.<?= htmlspecialchars($qrMime ?: 'png') ?>"
                           class="download-btn">⬇️ Download <?= strtoupper(htmlspecialchars($qrMime ?: 'png')) ?></a>
                    </div>
                <?php else: ?>
                    <div class="qr-placeholder">
                        <div class="ico">📱</div>
                        <div>Your QR code will appear here.</div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($payload): ?>
                <div class="payload-info">
                    <strong style="display:block;margin-bottom:6px;color:#333;">Encoded Payload:</strong>
                    <?= nl2br(htmlspecialchars($payload)) ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script>
    (function() {
        // ----------------------------------------------------------------
        // Type selector
        // ----------------------------------------------------------------
        const typeButtons = document.querySelectorAll('.type-btn');
        const typeInput   = document.getElementById('typeInput');
        const groups      = document.querySelectorAll('.field-group');

        typeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                typeButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const type = btn.dataset.type;
                typeInput.value = type;
                groups.forEach(g => g.classList.toggle('active', g.dataset.group === type));
            });
        });

        // ----------------------------------------------------------------
        // Advanced toggle
        // ----------------------------------------------------------------
        const advToggle = document.getElementById('advancedToggle');
        const advPanel  = document.getElementById('advancedPanel');
        advToggle.addEventListener('click', () => advPanel.classList.toggle('open'));
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        advPanel.classList.add('open');
        <?php endif; ?>

        // ----------------------------------------------------------------
        // Color picker ↔ hex text sync
        // ----------------------------------------------------------------
        const syncColor = (picker, text, swatch) => {
            const isHex = s => /^#?[0-9a-fA-F]{6}$/.test(s);
            const strip = s => s.replace(/^#/, '');

            picker.addEventListener('input', () => {
                text.value = strip(picker.value).toUpperCase();
                if (swatch) { swatch.style.background = picker.value; }
            });
            text.addEventListener('input', () => {
                const v = strip(text.value);
                if (isHex('#' + v)) {
                    picker.value = '#' + v;
                    if (swatch) { swatch.style.background = '#' + v; }
                }
            });
        };
        syncColor(
            document.getElementById('color_picker'),
            document.getElementById('color'),
            document.getElementById('color_swatch')
        );
        syncColor(
            document.getElementById('bgcolor_picker'),
            document.getElementById('bgcolor'),
            document.getElementById('bgcolor_swatch')
        );

        // ----------------------------------------------------------------
        // Dynamic vCard rows
        // ----------------------------------------------------------------
        const TEMPLATES = {
            names: (i) => `
                <div class="dyn-row name" data-row>
                    <div class="row-3">
                        <div class="form-group" style="margin: 0;">
                            <input type="text" name="names[${i}][value]" placeholder="e.g. John">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <select name="names[${i}][type]">
                                <option value="first_name" selected>First Name</option>
                                <option value="middle_name">Middle Name</option>
                                <option value="last_name">Last Name</option>
                                <option value="prefix">Prefix (e.g. Mr., Dr.)</option>
                                <option value="suffix">Suffix (e.g. Jr., PhD)</option>
                            </select>
                        </div>
                        <button type="button" class="remove-row" data-remove title="Remove name part">✕</button>
                    </div>
                </div>`,
            nicknames: (i) => `
                <div class="dyn-row nickname" data-row>
                    <div class="row-3">
                        <div class="form-group" style="margin: 0;">
                            <input type="text" name="nicknames[${i}][value]" placeholder="e.g. Johnny">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <input type="hidden" name="nicknames[${i}][type]" value="nickname">
                            <input type="text" value="Nick Name" disabled style="background:#eef0f5; color:#666;">
                        </div>
                        <button type="button" class="remove-row" data-remove title="Remove nickname">✕</button>
                    </div>
                </div>`,
            emails: (i) => `
                <div class="dyn-row email" data-row>
                    <div class="row-3">
                        <div class="form-group" style="margin: 0;">
                            <input type="email" name="emails[${i}][value]" placeholder="name@example.com">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <select name="emails[${i}][type]">
                                <option value="WORK">WORK</option>
                                <option value="HOME">HOME</option>
                                <option value="INTERNET">INTERNET</option>
                            </select>
                        </div>
                        <button type="button" class="remove-row" data-remove title="Remove email">✕</button>
                    </div>
                </div>`,
            phones: (i) => `
                <div class="dyn-row phone" data-row>
                    <div class="row-3">
                        <div class="form-group" style="margin: 0;">
                            <input type="tel" name="phones[${i}][value]" placeholder="+66 81 234 5678">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <select name="phones[${i}][type]">
                                <option value="CELL,VOICE">Mobile</option>
                                <option value="WORK,VOICE">Work</option>
                                <option value="HOME,VOICE">Home</option>
                                <option value="FAX">Fax</option>
                                <option value="VOICE">Voice</option>
                            </select>
                        </div>
                        <button type="button" class="remove-row" data-remove title="Remove phone">✕</button>
                    </div>
                </div>`,
            urls: (i) => `
                <div class="dyn-row url" data-row>
                    <div class="row-3">
                        <div class="form-group" style="margin: 0;">
                            <input type="url" name="urls[${i}][value]" placeholder="https://example.com">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <input type="text" name="urls[${i}][label]" placeholder="Label (optional)">
                        </div>
                        <button type="button" class="remove-row" data-remove title="Remove URL">✕</button>
                    </div>
                </div>`,
            addresses: (i) => `
                <div class="dyn-row address" data-row>
                    <div class="row-3" style="margin-bottom: 10px;">
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">Type</label>
                            <select name="addresses[${i}][type]">
                                <option value="WORK">Work</option>
                                <option value="HOME">Home</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div></div>
                        <button type="button" class="remove-row" data-remove title="Remove address">✕</button>
                    </div>
                    <div class="row">
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">Street</label>
                            <input type="text" name="addresses[${i}][street]">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">PO Box</label>
                            <input type="text" name="addresses[${i}][po_box]">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 8px;">
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">City</label>
                            <input type="text" name="addresses[${i}][city]">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">Region / State</label>
                            <input type="text" name="addresses[${i}][region]">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 8px;">
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">Postcode</label>
                            <input type="text" name="addresses[${i}][postcode]">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label style="font-size:0.8em; color:#666;">Country</label>
                            <input type="text" name="addresses[${i}][country]">
                        </div>
                    </div>
                </div>`,
        };

        const reindex = (list) => {
            const kind = list.dataset.list;
            const rows = list.querySelectorAll('[data-row]');
            rows.forEach((row, i) => {
                row.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/(\w+)\[\d+\]/, '$1[' + i + ']');
                });
            });
        };

        document.querySelectorAll('[data-add]').forEach(btn => {
            btn.addEventListener('click', () => {
                const kind = btn.dataset.add;
                const list = document.querySelector('[data-list="' + kind + '"]');
                if (!list || !TEMPLATES[kind]) { return; }
                const i = list.querySelectorAll('[data-row]').length;
                list.insertAdjacentHTML('beforeend', TEMPLATES[kind](i));
                bindRemoveHandlers();
            });
        });

        const bindRemoveHandlers = () => {
            document.querySelectorAll('[data-remove]').forEach(btn => {
                btn.onclick = () => {
                    const row = btn.closest('[data-row]');
                    const list = row.parentElement;
                    row.remove();
                    reindex(list);
                };
            });
        };
        bindRemoveHandlers();
    })();
    </script>
</body>
</html>
