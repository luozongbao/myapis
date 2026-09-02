<?php
/**
 * QR Code Generator API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 *
 * QR generation is delegated to the third-party goQR.me REST API.
 */

$spec = [
    'slug'    => 'qr-code-generator',
    'title'   => '📱 QR Code Generator API',
    'tagline' => 'Generate QR codes for text, URLs, vCards, events, Wi-Fi &amp; phone numbers',
    'crumb'   => 'QR Code Generator',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The QR Code Generator API creates QR codes for various data types including plain text, URLs, contact information (vCard), calendar events, Wi-Fi credentials, and phone numbers. Powered by goQR.me REST API for reliable, high-quality QR code generation.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>📝 Multiple Data Types</h4>
                        <p>Support for text, URLs, vCards, events, Wi-Fi, and phone numbers</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎨 Customizable Output</h4>
                        <p>Choose size, format (PNG, GIF, JPEG, SVG), and error correction level</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔒 Error Correction</h4>
                        <p>Built-in error correction (L, M, Q, H levels) for damaged codes</p>
                    </div>
                    <div class="feature-card">
                        <h4>📦 Flexible Responses</h4>
                        <p>Direct image output or base64 encoded JSON response</p>
                    </div>
                </div>

                <div class="info-box">
                    <strong>Powered by goQR.me:</strong> QR code generation uses the goQR.me REST API, a reliable and free service for QR code generation. Supports all standard QR code types and formats.
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
                <p>No authentication required. This is a public API that can be accessed without any API keys or tokens.</p>
            </div>

            <!-- Endpoints -->
            <div class="section">
                <h2>📡 API Endpoints</h2>

                <!-- Generate QR Code Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span> / <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate QR Code
                    </h3>
                    <p>Generate a QR code for one of six content types. Parameters can be supplied via the query string or as a JSON body; the same names are used in both. The <code>format</code> response mode decides whether you get the image bytes directly (<code>image</code>/<code>png</code>/<code>svg</code>) or a JSON wrapper with a base64-encoded payload (<code>json</code>/<code>data</code>).</p>

                    <h4>Request Parameters</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Default</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>type</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>text</code></td>
                                <td>One of <code>text</code>, <code>vcard</code>, <code>event</code>, <code>url</code>, <code>wifi</code>, <code>phone</code></td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>json</code></td>
                                <td>Response mode: <code>image</code>/<code>png</code>/<code>svg</code> for raw bytes, <code>json</code>/<code>data</code> for a JSON envelope with base64</td>
                            </tr>
                            <tr>
                                <td><code>file_type</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>png</code></td>
                                <td>Output format passed to goQR.me: <code>png</code>, <code>gif</code>, <code>jpeg</code>, <code>jpg</code>, <code>svg</code>, <code>eps</code>. Anything else silently falls back to <code>png</code>. The legacy alias <code>gformat</code> is also accepted.</td>
                            </tr>
                            <tr>
                                <td><code>size</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>300</td>
                                <td>Pixel size (10–1000). Values outside this range are clamped.</td>
                            </tr>
                            <tr>
                                <td><code>ecc</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>M</code></td>
                                <td>Error correction level: <code>L</code>, <code>M</code>, <code>Q</code>, <code>H</code>. Anything else falls back to <code>M</code>.</td>
                            </tr>
                            <tr>
                                <td><code>qzone</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>2</td>
                                <td>Quiet zone size (0–100, clamped).</td>
                            </tr>
                            <tr>
                                <td><code>margin</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>1</td>
                                <td>Margin (0–50, clamped).</td>
                            </tr>
                            <tr>
                                <td><code>charset_source</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>UTF-8</code></td>
                                <td>Source charset: <code>UTF-8</code> or <code>ISO-8859-1</code></td>
                            </tr>
                            <tr>
                                <td><code>charset_target</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>UTF-8</code></td>
                                <td>Target charset: <code>UTF-8</code> or <code>ISO-8859-1</code></td>
                            </tr>
                            <tr>
                                <td><code>color</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>0-0-0</code></td>
                                <td>Foreground colour as <code>R-G-B</code> with hyphen separators (e.g. <code>0-0-0</code>)</td>
                            </tr>
                            <tr>
                                <td><code>bgcolor</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>255-255-255</code></td>
                                <td>Background colour as <code>R-G-B</code></td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Type-Specific Fields</h4>
                    <p>Fields other than the ones above are forwarded to the payload builder for the chosen type.</p>

                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Required field(s)</th>
                                <th>Optional fields</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>text</code></td>
                                <td><code>text</code></td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td><code>url</code></td>
                                <td><code>url</code> (auto-prefixes <code>https://</code> if missing)</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td><code>phone</code></td>
                                <td><code>phone</code></td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td><code>wifi</code></td>
                                <td><code>ssid</code></td>
                                <td><code>password</code>, <code>encryption</code> (<code>WPA</code>/<code>WEP</code>/<code>nopass</code>), <code>hidden</code></td>
                            </tr>
                            <tr>
                                <td><code>event</code></td>
                                <td><code>summary</code>, <code>start</code></td>
                                <td><code>end</code>, <code>location</code>, <code>description</code></td>
                            </tr>
                            <tr>
                                <td><code>vcard</code></td>
                                <td>at least one of: name part, nickname, <code>organization</code></td>
                                <td>legacy fields: <code>first_name</code>, <code>middle_name</code>, <code>last_name</code>, <code>prefix</code>, <code>suffix</code>, <code>nickname</code>, <code>organization</code>, <code>title</code>, <code>note</code>, <code>work_email</code>, <code>home_email</code>, <code>work_phone</code>, <code>home_phone</code>, <code>mobile</code>, <code>fax</code>, <code>website</code>, <code>address</code>, <code>city</code>, <code>region</code>, <code>postcode</code>, <code>country</code>. Dynamic lists: <code>names[][type/value]</code>, <code>nicknames[][type/value]</code>, <code>emails[][type/value]</code>, <code>phones[][type/value]</code>, <code>urls[][value]</code>, <code>addresses[][...]</code></td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request — Text</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=text&text=Hello%20World&format=image"</div>

                    <h4>Example Request — URL</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=url&url=https://github.com&size=300&ecc=M&format=json"</div>

                    <h4>Example Request — Wi-Fi</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=wifi&ssid=MyWiFi&password=pass123&encryption=WPA&format=json"</div>

                    <h4>Example Request — Event</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=event&summary=Meeting&start=2025-12-01T10:00&end=2025-12-01T11:00&location=Office&format=json"</div>

                    <h4>Example Request — Phone</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=phone&phone=+1234567890&format=image"</div>

                    <h4>Example Request — vCard (POST JSON)</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>?format=json" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "vcard",
    "first_name": "John",
    "last_name":  "Doe",
    "organization": "Acme Corp",
    "emails":    [{"type": "WORK", "value": "john@acme.com"}],
    "phones":    [{"type": "CELL,VOICE", "value": "+1234567890"}]
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>JSON envelope (<code>format=json</code> or <code>format=data</code>)</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success":   true,
  "message":   "QR code generated successfully",
  "type":      "wifi",
  "payload":   "WIFI:T:WPA;S:MyWiFi;P:pass123;H:false;",
  "qr_url":    "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  "goqr_url":  "https://api.qrserver.com/v1/create-qr-code/?data=WIFI%3AT%3AWPA%3B...",
  "file_type": "png",
  "params": {
    "size":           300,
    "ecc":            "M",
    "format":         "png",
    "qzone":          2,
    "margin":         1,
    "charset-source": "UTF-8",
    "charset-target": "UTF-8",
    "color":          "0-0-0",
    "bgcolor":        "255-255-255"
  }
}</div>
                </div>
                <p>The <code>qr_url</code> field is a fully-qualified <code>data:</code> URL (MIME + base64) so you can drop it straight into an <code>&lt;img src&gt;</code> tag. <code>goqr_url</code> is the underlying goQR.me URL for debugging.</p>

                <h3>Direct image (<code>format=image</code> / <code>png</code> / <code>svg</code>)</h3>
                <p>The API returns the raw image bytes with the appropriate <code>Content-Type</code> header (<code>image/png</code>, <code>image/svg+xml</code>, etc.) and <code>Content-Disposition: inline; filename="qr-code.&lt;ext&gt;"</code>.</p>

                <h3>Error Response</h3>
                <p>All errors come back as JSON with HTTP 400 (bad request) or 500 (upstream failure).</p>

                <div class="error-box">
                    <p><strong>Unsupported <code>type</code></strong></p>
                    <div class="code-block">{
  "error":   "Bad request",
  "message": "Unsupported type 'banana'. Allowed: text, vcard, event, url, wifi, phone"
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Missing required field for a type</strong></p>
                    <div class="code-block">{
  "error":   "Bad request",
  "message": "vCard requires at least one name part, a nickname, or Organization"
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Invalid <code>format</code> response mode</strong></p>
                    <div class="code-block">{
  "error":   "Invalid format parameter",
  "message": "Supported formats: image, json, svg"
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Upstream failure</strong> (goQR.me unreachable / non-200)</p>
                    <div class="code-block">{
  "error":   "Internal server error",
  "message": "Failed to fetch QR code from goQR.me: HTTP 502"
}</div>
                </div>
            </div>

            <!-- Error Correction Levels -->
            <div class="section">
                <h2>🔧 Error Correction Levels</h2>
                <p>QR codes include built-in error correction that allows them to be read even when partially damaged. Choose the appropriate level based on your use case:</p>

                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Recovery Capacity</th>
                            <th>Use Case</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>L (Low)</td>
                            <td>~7%</td>
                            <td>Clean environments, small QR codes</td>
                        </tr>
                        <tr>
                            <td>M (Medium)</td>
                            <td>~15%</td>
                            <td>General purpose, balanced</td>
                        </tr>
                        <tr>
                            <td>Q (Quartile)</td>
                            <td>~25%</td>
                            <td>Outdoor use, potential damage</td>
                        </tr>
                        <tr>
                            <td>H (High)</td>
                            <td>~30%</td>
                            <td>Harsh environments, logos overlay</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Use Cases -->
            <div class="section">
                <h2>💡 Common Use Cases</h2>

                <div class="categories-grid">
                    <div class="category-item">
                        <h4>🔗 Marketing &amp; URLs</h4>
                        <p>Link to websites, landing pages, app downloads, or promotional content.</p>
                    </div>
                    <div class="category-item">
                        <h4>📇 Contact Sharing</h4>
                        <p>vCard QR codes for business cards or networking events.</p>
                    </div>
                    <div class="category-item">
                        <h4>📅 Event Management</h4>
                        <p>Embed events into calendars with summary, start, end and location.</p>
                    </div>
                    <div class="category-item">
                        <h4>📶 Wi-Fi Access</h4>
                        <p>Share WPA/WEP/nopass credentials for guests to auto-connect.</p>
                    </div>
                    <div class="category-item">
                        <h4>📞 Quick Call</h4>
                        <p>Encode a phone number so a scan starts a call on most phones.</p>
                    </div>
                    <div class="category-item">
                        <h4>📝 Plain Text</h4>
                        <p>Encode arbitrary text snippets, serial numbers, or short messages.</p>
                    </div>
                </div>
            </div>

            <!-- Best Practices -->
            <div class="section">
                <h2>✨ Best Practices</h2>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>Sizing:</strong> Use 300px for most web display, 600–1000px for print. Defaults are tuned for typical web use.</li>
                    <li><strong>Error correction:</strong> Use <code>H</code> when overlaying logos or printing on curved/harsh surfaces.</li>
                    <li><strong>Colours:</strong> Always keep the background lighter than the foreground and use high contrast.</li>
                    <li><strong>Quiet zone:</strong> Keep <code>qzone</code> ≥ 2 so scanners can latch onto the code reliably.</li>
                    <li><strong>Output type:</strong> Use <code>format=image</code> (or <code>png</code>/<code>svg</code>) when you only want the bytes; use <code>format=json</code> when you need the raw payload string alongside the image.</li>
                    <li><strong>Payload inspection:</strong> The <code>payload</code> field in JSON responses is the raw string that was encoded — useful for debugging or generating equivalent codes yourself.</li>
                </ul>
            </div>

            <!-- Error Codes -->
            <div class="section">
                <h2>⚠️ Error Codes</h2>
                <p>The API does not return a numeric <code>code</code> field. Every error has the shape <code>{ "error": "&lt;title&gt;", "message": "&lt;details&gt;" }</code> with an appropriate HTTP status:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>HTTP status</th>
                            <th><code>error</code></th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>400</code></td>
                            <td><code>Bad request</code></td>
                            <td>Unsupported <code>type</code>, or a required type-specific field is missing / malformed</td>
                        </tr>
                        <tr>
                            <td><code>400</code></td>
                            <td><code>Invalid format parameter</code></td>
                            <td><code>format</code> is not one of <code>image</code>, <code>json</code>, <code>svg</code>, <code>png</code>, <code>data</code></td>
                        </tr>
                        <tr>
                            <td><code>500</code></td>
                            <td><code>Internal server error</code></td>
                            <td>goQR.me call failed (non-200 response, network error, malformed payload)</td>
                        </tr>
                    </tbody>
                </table>
                <p>Note: an unsupported <code>file_type</code> (e.g. <code>bmp</code>) is <em>not</em> an error — the server silently falls back to <code>png</code> so old clients keep working.</p>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>HTML display</h3>
                <p>Just point an <code>&lt;img&gt;</code> tag at the JSON endpoint's <code>qr_url</code> field:</p>
                <div class="code-block">&lt;img src="https://your-host/api/qr-code-generator/?type=url&url=https://example.com&format=json"
     alt="QR code" /&gt;</div>

                <h3>JavaScript fetch (JSON)</h3>
                <div class="code-block">fetch('/api/qr-code-generator/?type=url&url=https://example.com&format=json')
  .then(r =&gt; r.json())
  .then(data =&gt; {
    document.getElementById('qr').src = data.qr_url;   // data:image/png;base64,...
    console.log('Encoded payload:', data.payload);     // "https://example.com"
  });</div>

                <h3>JavaScript fetch (raw bytes)</h3>
                <div class="code-block">fetch('/api/qr-code-generator/?type=wifi&ssid=Demo&encryption=nopass&format=image')
  .then(r =&gt; r.blob())
  .then(blob =&gt; {
    document.getElementById('qr').src = URL.createObjectURL(blob);
  });</div>

                <h3>POST JSON body</h3>
                <div class="code-block">fetch('/api/qr-code-generator/?format=json', {
  method:  'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    type:    'vcard',
    first_name: 'John',
    last_name:  'Doe',
    organization: 'Acme Corp',
    emails: [{ type: 'WORK', value: 'john@acme.com' }]
  })
})
  .then(r =&gt; r.json())
  .then(data =&gt; console.log(data.qr_url));</div>
            </div>

            <!-- Rate Limits -->
            <div class="section">
                <h2>🚦 Rate Limits</h2>
                <p>Currently, there are no rate limits imposed on this API. However, please use it responsibly and avoid excessive requests that might impact service availability for other users.</p>
            </div>

            <!-- Try It Out -->
            <div class="try-it">
                <h3>🎯 Ready to Try?</h3>
                <p>Test the QR Code Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/qr-code-generator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>