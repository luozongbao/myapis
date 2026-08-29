<?php
// Generate dynamic base URL based on current server
function getBaseUrl($toolName) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . '/api/' . $toolName . '/';
}
$baseUrl = getBaseUrl('qr-code-generator');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator API Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header p  { font-size: 1.2em; opacity: 0.9; }

        .nav {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
            color: #666;
            flex-wrap: wrap;
        }

        .breadcrumb a { color: #667eea; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .content { padding: 40px; }

        .section { margin-bottom: 40px; }
        .section h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .section h3 {
            color: #444;
            font-size: 1.3em;
            margin-bottom: 15px;
            margin-top: 25px;
        }
        .section h4 {
            color: #555;
            margin: 15px 0 8px;
        }

        .endpoint {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .method {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9em;
            margin-right: 10px;
        }
        .method.post { background: #007bff; }
        .method.get  { background: #28a745; }

        .url {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-left: 10px;
            word-break: break-all;
        }

        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin: 15px 0;
            white-space: pre;
        }

        .parameter-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .parameter-table th, .parameter-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        .parameter-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .parameter-table td { color: #555; font-size: 0.92em; }
        .parameter-table code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
        }

        .required { color: #dc3545; font-weight: bold; }
        .optional { color: #6c757d; }

        .response-box {
            background: #f0f8f0;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            color: #0c5460;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .feature-card h4 { color: #333; margin-bottom: 10px; }
        .feature-card p  { color: #666; font-size: 0.9em; }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }

        @media (max-width: 768px) {
            .header h1 { font-size: 2em; }
            .content  { padding: 20px; }
            .code-block { font-size: 0.8em; }
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1>📱 QR Code Generator API</h1>
            <p>Generate QR codes for text, URLs, vCards, events, Wi-Fi &amp; phone numbers</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../index.php">← Back to Main</a>
                <span>/</span>
                <a href="../qr-code-generator.php">QR Code Generator</a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">

            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>
                    The QR Code Generator API produces high-quality QR codes from a wide variety of content
                    types.  It assembles standards-compliant payloads (vCard 3.0, iCalendar, WIFI:, tel: …)
                    and then asks <a href="https://goqr.me/api/doc/create-qr-code/" target="_blank" rel="noopener">goQR.me</a>
                    to render the picture — so all configuration parameters from the official goQR.me spec
                    are supported.
                </p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>📝 Plain Text</h4>
                        <p>Any short or long text — perfect for notes, messages or short URLs.</p>
                    </div>
                    <div class="feature-card">
                        <h4>👤 Business vCard</h4>
                        <p>vCard 3.0 with full personal, organisational and address fields.</p>
                    </div>
                    <div class="feature-card">
                        <h4>📅 Events</h4>
                        <p>iCalendar (vCalendar) events with start/end, location and description.</p>
                    </div>
                    <div class="feature-card">
                        <h4>🌐 Websites</h4>
                        <p>Plain URL — the QR opens the link when scanned.</p>
                    </div>
                    <div class="feature-card">
                        <h4>📶 Wi-Fi</h4>
                        <p>WIFI: payload with SSID, password, encryption type and hidden flag.</p>
                    </div>
                    <div class="feature-card">
                        <h4>📞 Phone Numbers</h4>
                        <p>tel: URI — scanners prompt the user to call the number.</p>
                    </div>
                </div>

                <div class="info-box">
                    <strong>Underlying engine:</strong> All rendering is delegated to the
                    <a href="https://goqr.me/api/doc/create-qr-code/" target="_blank" rel="noopener">goQR.me <code>create-qr-code</code> API</a>.
                    This service is provided free of charge by Foundata GmbH and supports size, ECC, colour,
                    background, margin, quiet zone, charset and format parameters — all of which you can pass
                    straight through to this endpoint.
                </div>
            </div>

            <!-- Base URL -->
            <div class="section">
                <h2>🌐 Base URL</h2>
                <div class="code-block"><?php echo htmlspecialchars($baseUrl); ?></div>
            </div>

            <!-- Authentication -->
            <div class="section">
                <h2>🔐 Authentication</h2>
                <p>No authentication required.  The endpoint is public and CORS-enabled.</p>
            </div>

            <!-- Endpoints -->
            <div class="section">
                <h2>📡 Endpoints</h2>

                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate a QR code
                    </h3>
                    <p>Generate a QR code with a payload of any supported content type.</p>

                    <h4>Core Parameters</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>type</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>One of: <code>text</code>, <code>url</code>, <code>vcard</code>, <code>event</code>, <code>wifi</code>, <code>phone</code></td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Response format — <code>image</code> (raw PNG) or <code>json</code> (default)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Type-Specific Fields</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr><th>Type</th><th>Field</th><th>Description</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><code>text</code></td>  <td><code>text</code></td>     <td>Plain text payload</td></tr>
                            <tr><td><code>url</code></td>   <td><code>url</code></td>      <td>Website URL (auto-prefixed with <code>https://</code> if missing)</td></tr>
                            <tr><td><code>phone</code></td> <td><code>phone</code></td>    <td>Phone number (any format)</td></tr>

                            <tr><td rowspan="17"><code>vcard</code></td>
                                <td><code>first_name</code></td>   <td>First name (required with last name, or supply organization)</td></tr>
                            <tr><td><code>last_name</code></td>    <td>Last name</td></tr>
                            <tr><td><code>organization</code></td> <td>Company</td></tr>
                            <tr><td><code>title</code></td>        <td>Job title</td></tr>
                            <tr><td><code>work_email</code></td>   <td>Work email <em>(single)</em></td></tr>
                            <tr><td><code>home_email</code></td>   <td>Personal email <em>(single)</em></td></tr>
                            <tr><td><code>work_phone</code></td>   <td>Work phone <em>(single)</em></td></tr>
                            <tr><td><code>home_phone</code></td>   <td>Home phone <em>(single)</em></td></tr>
                            <tr><td><code>mobile</code></td>       <td>Mobile phone <em>(single)</em></td></tr>
                            <tr><td><code>fax</code></td>          <td>Fax <em>(single)</em></td></tr>
                            <tr><td><code>website</code></td>      <td>Personal / business website <em>(single)</em></td></tr>
                            <tr><td><code>address</code></td>      <td>Street address <em>(single)</em></td></tr>
                            <tr><td><code>city</code></td>         <td>City <em>(single)</em></td></tr>
                            <tr><td><code>region</code></td>       <td>Region / state / province <em>(single)</em></td></tr>
                            <tr><td><code>postcode</code></td>     <td>Postal / ZIP code <em>(single)</em></td></tr>
                            <tr><td><code>country</code></td>      <td>Country <em>(single)</em></td></tr>
                            <tr><td><code>note</code></td>         <td>Free-form note</td></tr>

                            <tr><td rowspan="5"><code>event</code></td>
                                <td><code>summary</code></td>     <td>Event title (required)</td></tr>
                            <tr><td><code>start</code></td>       <td>Start date/time (<code>YYYY-MM-DD</code> or <code>YYYY-MM-DD HH:MM</code>, required)</td></tr>
                            <tr><td><code>end</code></td>         <td>End date/time (defaults to +1h after start)</td></tr>
                            <tr><td><code>location</code></td>    <td>Event location</td></tr>
                            <tr><td><code>description</code></td> <td>Event description</td></tr>

                            <tr><td rowspan="4"><code>wifi</code></td>
                                <td><code>ssid</code></td>        <td>Network name (required)</td></tr>
                            <tr><td><code>password</code></td>    <td>Wi-Fi password</td></tr>
                            <tr><td><code>encryption</code></td>  <td><code>WPA</code> (default), <code>WEP</code>, or <code>nopass</code></td></tr>
                            <tr><td><code>hidden</code></td>      <td><code>1</code> for hidden network, <code>0</code> otherwise</td></tr>
                        </tbody>
                    </table>

                    <h4>Appearance / goQR.me Parameters</h4>
                    <p>All parameters documented at
                        <a href="https://goqr.me/api/doc/create-qr-code/" target="_blank" rel="noopener">goQR.me</a>
                        are supported and forwarded as-is.
                    </p>
                    <table class="parameter-table">
                        <thead>
                            <tr><th>Parameter</th><th>Default</th><th>Description</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><code>size</code></td>           <td>300</td>          <td>Pixel size: <code>[int]x[int]</code> (50–1000)</td></tr>
                            <tr><td><code>ecc</code></td>            <td>M</td>            <td>Error correction: <code>L</code> (7%), <code>M</code> (15%), <code>Q</code> (25%), <code>H</code> (30%)</td></tr>
                            <tr><td><code>qzone</code></td>          <td>2</td>            <td>Quiet zone in modules (0–100, recommended ≥1)</td></tr>
                            <tr><td><code>margin</code></td>         <td>1</td>            <td>Pixel margin around the QR (0–50)</td></tr>
                            <tr><td><code>color</code></td>          <td>0-0-0</td>        <td>Foreground RGB — decimal <code>R-G-B</code> or hex (<code>000000</code>)</td></tr>
                            <tr><td><code>bgcolor</code></td>        <td>255-255-255</td>  <td>Background RGB</td></tr>
                            <tr><td><code>charset_source</code></td> <td>UTF-8</td>        <td><code>UTF-8</code> or <code>ISO-8859-1</code></td></tr>
                            <tr><td><code>charset_target</code></td> <td>UTF-8</td>        <td><code>UTF-8</code> or <code>ISO-8859-1</code></td></tr>
                            <tr><td><code>file_type</code></td>       <td>png</td>          <td>Renderer output format: <code>png</code>, <code>svg</code>, <code>gif</code>, <code>jpeg</code> or <code>eps</code>. (Legacy alias: <code>gformat</code>.) See <a href="https://goqr.me/api/doc/create-qr-code/" target="_blank" rel="noopener">goQR.me</a></td></tr>
                        </tbody>
                    </table>

                    <h4>Dynamic vCard Fields (vCard type only)</h4>
                    <p>For <code>type=vcard</code> you can supply multiple emails, phones, URLs and
                        structured addresses using array notation. Each entry is emitted as a
                        separate <code>EMAIL;TYPE=…</code>, <code>TEL;TYPE=…</code>, <code>URL</code> or
                        <code>ADR;TYPE=…</code> line in the vCard payload.</p>
                    <table class="parameter-table">
                        <thead>
                            <tr><th>Field Pattern</th><th>Sub-keys</th><th>Description</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>emails[i][value]</code></td>
                                <td><code>type</code>: <code>WORK</code> / <code>HOME</code> / <code>INTERNET</code></td>
                                <td>One email per row. Multiple rows supported via index <code>i = 0, 1, 2 …</code></td>
                            </tr>
                            <tr>
                                <td><code>phones[i][value]</code></td>
                                <td><code>type</code>: <code>CELL,VOICE</code> / <code>WORK,VOICE</code> / <code>HOME,VOICE</code> / <code>FAX</code> / <code>VOICE</code></td>
                                <td>One phone per row.</td>
                            </tr>
                            <tr>
                                <td><code>urls[i][value]</code></td>
                                <td><code>label</code>: optional display label (not embedded in vCard)</td>
                                <td>One URL per row.</td>
                            </tr>
                            <tr>
                                <td><code>addresses[i][…]</code></td>
                                <td><code>type</code> (<code>WORK</code> / <code>HOME</code> / <code>OTHER</code>), <code>street</code>, <code>po_box</code>, <code>city</code>, <code>region</code>, <code>postcode</code>, <code>country</code></td>
                                <td>One full address per row.</td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="margin-top: 10px;">Indexes can be sparse — the server normalises and
                        packs them densely before emitting the vCard. Legacy single fields
                        (<code>work_email</code>, <code>home_phone</code>, …) are still accepted
                        for backward compatibility and are merged with the dynamic arrays.</p>
                </div>
            </div>

            <!-- Examples -->
            <div class="section">
                <h2>💡 Example Requests</h2>

                <h3>1) Plain Text</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=text" \
  -d "text=Hello%20World%21"</div>

                <h3>2) Website URL</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=url" \
  -d "url=https://github.com/luozongbao/myapis"</div>

                <h3>3) Business vCard</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=vcard" \
  -d "first_name=John" \
  -d "last_name=Doe" \
  -d "organization=Acme%20Inc." \
  -d "title=CTO" \
  -d "work_email=john@acme.example" \
  -d "mobile=%2B66812345678" \
  -d "website=https://acme.example" \
  -d "address=123%20Main%20St" \
  -d "city=Bangkok" \
  -d "country=Thailand"</div>

                <h3>4) Calendar Event</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=event" \
  -d "summary=Quarterly%20Review" \
  -d "start=2026-09-15%2014:00" \
  -d "end=2026-09-15%2016:00" \
  -d "location=Boardroom%2C%204F" \
  -d "description=Agenda%3A%20Roadmap%2C%20KPIs"</div>

                <h3>5) Wi-Fi Network</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=wifi" \
  -d "ssid=CafeFreeWiFi" \
  -d "password=drinkmorecoffee" \
  -d "encryption=WPA"</div>

                <h3>6) Phone Number</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=phone" \
  -d "phone=%2B66%2081%20234%205678"</div>

                <h3>7) Direct PNG Image with Custom Dimension</h3>
                <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?format=image&type=text&text=Scan%20me&size=500x500&ecc=H&qzone=4" \
  --output qr.png</div>

                <h3>8) SVG Output with Custom Colours</h3>
                <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?format=image&type=text&text=Hello" \
  -d "file_type=svg" \
  -d "color=cc0066" \
  -d "bgcolor=ffffcc" \
  -d "size=400x400" \
  --output qr.svg</div>

                <h3>9) Dynamic vCard with Multiple Emails / Phones / Addresses</h3>
                <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -d "type=vcard" \
  -d "first_name=Jane" -d "last_name=Doe" \
  -d "emails[0][type]=WORK"    -d "emails[0][value]=jane@acme.com" \
  -d "emails[1][type]=HOME"    -d "emails[1][value]=jane@home.com" \
  -d "phones[0][type]=CELL,VOICE" -d "phones[0][value]=+66811234567" \
  -d "phones[1][type]=WORK,VOICE" -d "phones[1][value]=+6623456789" \
  -d "addresses[0][type]=WORK" -d "addresses[0][street]=123 Sukhumvit" \
  -d "addresses[0][city]=Bangkok" -d "addresses[0][country]=Thailand"</div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>JSON response (default)</h3>
                <div class="response-box">
<div class="code-block">{
  "success": true,
  "message": "QR code generated successfully",
  "type": "vcard",
  "payload": "BEGIN:VCARD\r\nVERSION:3.0\r\nN:Doe;John;;;\r\nFN:John Doe\r\nORG:Acme Inc.\r\nTITLE:CTO\r\nTEL;TYPE=CELL,VOICE:+66812345678\r\nEMAIL;TYPE=WORK:john@acme.example\r\nURL:https://acme.example\r\nADR;TYPE=WORK:;;123 Main St;Bangkok;;;Thailand\r\nEND:VCARD",
  "qr_url": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  "goqr_url": "https://api.qrserver.com/v1/create-qr-code/?...",
  "params": {
    "size": 300,
    "ecc": "M",
    "format": "png",
    "qzone": 2,
    "margin": 1,
    "charset-source": "UTF-8",
    "charset-target": "UTF-8",
    "color": "0-0-0",
    "bgcolor": "255-255-255"
  },
  "file_type": "png"
}</div>
                </div>

                <h3>Image response (<code>format=image</code>)</h3>
                <p>Returns the raw PNG bytes directly (Content-Type <code>image/png</code>).</p>
            </div>

            <!-- Error codes -->
            <div class="section">
                <h2>⚠️ Error Codes</h2>
                <table class="parameter-table">
                    <thead>
                        <tr><th>HTTP</th><th>Description</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>200</td><td>Success</td></tr>
                        <tr><td>400</td><td>Bad Request — missing required field or unknown <code>type</code></td></tr>
                        <tr><td>500</td><td>Internal Server Error — goQR.me upstream issue</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Reference -->
            <div class="section">
                <h2>🔗 Reference</h2>
                <p>Full parameter reference lives at the official goQR.me documentation:
                    <a class="btn" href="https://goqr.me/api/doc/create-qr-code/" target="_blank" rel="noopener">goQR.me API ↗</a>
                </p>
            </div>

        </div>
    </div>
</body>
</html>
