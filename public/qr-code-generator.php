<?php
/**
 * QR Code Generator - Frontend
 * Web UI for generating QR codes with multiple content types
 */

$error = '';
$success = false;
$qrCodeUrl = '';
$payload = '';
$selectedType = $_POST['type'] ?? 'text';

// ----------------------------------------------------------------
// Pull form fields for all supported types
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
    'last_name'    => $_POST['last_name']    ?? '',
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
$ecc    = $_POST['ecc']    ?? 'M';
$size   = (int)($_POST['size'] ?? 300);
$qzone  = (int)($_POST['qzone'] ?? 2);
$margin = (int)($_POST['margin'] ?? 1);
$color  = $_POST['color']  ?? '0-0-0';
$bgcolor = $_POST['bgcolor'] ?? '255-255-255';

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
         'margin' => $margin, 'color' => $color, 'bgcolor' => $bgcolor],
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
            $payload   = $data['payload'];
        } else {
            $error = $data['message'] ?? $data['error'] ?? 'Unknown error occurred';
        }
    }
}
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

        .type-btn:hover {
            transform: translateY(-2px);
            background: #e8ecf5;
        }

        .type-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 20px rgba(102,126,234,0.35);
        }

        .type-btn .ico {
            font-size: 1.6em;
            display: block;
            margin-bottom: 6px;
        }

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

        .qr-display img {
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

        .qr-placeholder .ico {
            font-size: 3em;
            margin-bottom: 10px;
        }

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

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .breadcrumb a:hover { text-decoration: underline; }

        .breadcrumb .right {
            margin-left: auto;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

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
            .row { grid-template-columns: 1fr; }
        }
    </style>
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
                <span class="badge">📥 Instant Download</span>
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
                    <div class="section-title">Personal</div>
                    <div class="row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" placeholder="John"
                                   value="<?= htmlspecialchars($fields['first_name']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" placeholder="Doe"
                                   value="<?= htmlspecialchars($fields['last_name']) ?>">
                        </div>
                    </div>

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

                    <div class="section-title">Contact</div>
                    <div class="row">
                        <div class="form-group">
                            <label for="work_email">Work Email</label>
                            <input type="email" id="work_email" name="work_email"
                                   value="<?= htmlspecialchars($fields['work_email']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="home_email">Personal Email</label>
                            <input type="email" id="home_email" name="home_email"
                                   value="<?= htmlspecialchars($fields['home_email']) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="work_phone">Work Phone</label>
                            <input type="tel" id="work_phone" name="work_phone"
                                   value="<?= htmlspecialchars($fields['work_phone']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="home_phone">Home Phone</label>
                            <input type="tel" id="home_phone" name="home_phone"
                                   value="<?= htmlspecialchars($fields['home_phone']) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="mobile">Mobile</label>
                            <input type="tel" id="mobile" name="mobile"
                                   value="<?= htmlspecialchars($fields['mobile']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="fax">Fax</label>
                            <input type="tel" id="fax" name="fax"
                                   value="<?= htmlspecialchars($fields['fax']) ?>">
                        </div>
                    </div>

                    <div class="section-title">Online</div>
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" placeholder="https://example.com"
                               value="<?= htmlspecialchars($fields['website']) ?>">
                    </div>

                    <div class="section-title">Address</div>
                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <input type="text" id="address" name="address"
                               value="<?= htmlspecialchars($fields['address']) ?>">
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city"
                                   value="<?= htmlspecialchars($fields['city']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="region">Region / State</label>
                            <input type="text" id="region" name="region"
                                   value="<?= htmlspecialchars($fields['region']) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" id="postcode" name="postcode"
                                   value="<?= htmlspecialchars($fields['postcode']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country"
                                   value="<?= htmlspecialchars($fields['country']) ?>">
                        </div>
                    </div>

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
                    ⚙️ Appearance &amp; Dimension Settings
                </button>
                <div class="advanced" id="advancedPanel">
                    <div class="row">
                        <div class="form-group">
                            <label for="size">Size (px)</label>
                            <input type="number" id="size" name="size" min="50" max="1000" step="10"
                                   value="<?= htmlspecialchars((string)$size) ?>">
                        </div>
                        <div class="form-group">
                            <label for="ecc">Error Correction</label>
                            <select id="ecc" name="ecc">
                                <option value="L" <?= $ecc === 'L' ? 'selected' : '' ?>>L — Low (~7%)</option>
                                <option value="M" <?= $ecc === 'M' ? 'selected' : '' ?>>M — Medium (~15%)</option>
                                <option value="Q" <?= $ecc === 'Q' ? 'selected' : '' ?>>Q — Quartile (~25%)</option>
                                <option value="H" <?= $ecc === 'H' ? 'selected' : '' ?>>H — High (~30%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="margin">Margin (px)</label>
                            <input type="number" id="margin" name="margin" min="0" max="50"
                                   value="<?= htmlspecialchars((string)$margin) ?>">
                        </div>
                        <div class="form-group">
                            <label for="qzone">Quiet Zone (modules)</label>
                            <input type="number" id="qzone" name="qzone" min="0" max="100"
                                   value="<?= htmlspecialchars((string)$qzone) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="color">Foreground (data)</label>
                            <input type="text" id="color" name="color" placeholder="0-0-0 or 000000"
                                   value="<?= htmlspecialchars($color) ?>">
                        </div>
                        <div class="form-group">
                            <label for="bgcolor">Background</label>
                            <input type="text" id="bgcolor" name="bgcolor" placeholder="255-255-255 or ffffff"
                                   value="<?= htmlspecialchars($bgcolor) ?>">
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
                    <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="Generated QR Code">
                    <div>
                        <a href="<?= htmlspecialchars($qrCodeUrl) ?>" download="qr-code.png" class="download-btn">⬇️ Download PNG</a>
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
        // Type selector
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

        // Advanced toggle
        const advToggle = document.getElementById('advancedToggle');
        const advPanel  = document.getElementById('advancedPanel');
        advToggle.addEventListener('click', () => {
            advPanel.classList.toggle('open');
        });
        // Open advanced panel if user submitted with non-default values
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        advPanel.classList.add('open');
        <?php endif; ?>
    </script>
</body>
</html>
