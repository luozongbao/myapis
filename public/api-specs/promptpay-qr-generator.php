<?php
/**
 * PromptPay QR Generator API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 *
 * Note: the previous version of this file had a malformed `<tr>` row
 * (missing `<tr><td>` opener for the second `format` entry).  The single
 * combined row below documents the canonical format options.
 */

$spec = [
    'slug'    => 'promptpay-qr-generator',
    'title'   => '💳 PromptPay QR Generator API',
    'tagline' => 'Generate EMV-compliant PromptPay QR codes for Thai payment system',
    'crumb'   => 'PromptPay QR Generator',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The PromptPay QR Generator API creates EMV-compliant QR codes for Thailand's PromptPay payment system. Generate QR codes for mobile numbers, tax IDs, or e-Wallet IDs with optional payment amounts.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>📱 Multiple ID Types</h4>
                        <p>Support for mobile numbers, tax IDs, and e-Wallet IDs</p>
                    </div>
                    <div class="feature-card">
                        <h4>💰 Optional Amounts</h4>
                        <p>Generate QR codes with or without predefined payment amounts</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔧 EMV Compliant</h4>
                        <p>Follows EMV QR Code specification for payment systems</p>
                    </div>
                    <div class="feature-card">
                        <h4>🖼️ Multiple Formats</h4>
                        <p>Base64 image output and raw QR code data</p>
                    </div>
                </div>

                <div class="info-box">
                    <strong>About PromptPay:</strong> PromptPay is Thailand's national e-payment system that allows real-time money transfers using mobile numbers or tax identification numbers.
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
                        Generate PromptPay QR Code
                    </h3>
                    <p>Generate a PromptPay QR code for the specified recipient and optional amount. Parameters can be supplied as a query string or as a JSON POST body (the same names are used in both).</p>

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
                                <td><code>target</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>—</td>
                                <td>Mobile number (Thai national or <code>+66</code>), 13-digit Tax ID, or 15-digit e-Wallet ID</td>
                            </tr>
                            <tr>
                                <td><code>amount</code></td>
                                <td>number</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>null</code></td>
                                <td>Payment amount in Thai Baht (THB). Omit or leave blank to leave the QR amount open for the payer to fill in.</td>
                            </tr>
                            <tr>
                                <td><code>size</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>300</td>
                                <td>Pixel size passed to goQR.me (10–1000, clamped).</td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>image</code></td>
                                <td>Response mode: <code>image</code> returns raw PNG bytes; <code>json</code> returns a JSON envelope with metadata; <code>base64</code> returns a slim JSON wrapper containing only the image data URI.</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Target auto-detection</h4>
                    <p>The server inspects <code>target</code> to pick the PromptPay merchant-account prefix and reports the result as <code>target_type</code> in JSON responses:</p>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Detected <code>target_type</code></th>
                                <th>Format</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>phone</code></td>
                                <td>10-digit Thai mobile (<code>0XXXXXXXXX</code>) or <code>+66XXXXXXXXX</code></td>
                                <td><code>0812345678</code></td>
                            </tr>
                            <tr>
                                <td><code>tax</code></td>
                                <td>13-digit Tax ID</td>
                                <td><code>1234567890123</code></td>
                            </tr>
                            <tr>
                                <td><code>ewallet</code></td>
                                <td>15-digit e-Wallet ID</td>
                                <td><code>123456789012345</code></td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request — GET (JSON)</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?target=0812345678&amount=100.50&size=300&format=json"</div>

                    <h4>Example Request — POST (image bytes)</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "0812345678",
    "amount": 100.50,
    "size": 300
  }' --output qr.png</div>

                    <h4>Example Request — Tax ID without amount (base64)</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "1234567890123",
    "size": 400,
    "format": "base64"
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3><code>format=json</code></h3>
                <p>Full JSON envelope with the raw EMV payload string alongside the encoded image:</p>
                <div class="response-box">
                    <div class="code-block">{
  "success":     true,
  "message":     "QR code generated successfully",
  "payload":     "00020101021229370016A000000677010111011300668123456785802TH53037645406100.506304F88B",
  "qr_url":      "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA...",
  "target":      "0812345678",
  "amount":      100.5,
  "target_type": "phone",
  "qr_size":     300
}</div>
                </div>
                <p><code>amount</code> is <code>null</code> when the caller did not provide one. <code>target_type</code> is one of <code>phone</code>, <code>tax</code>, or <code>ewallet</code>.</p>

                <h3><code>format=base64</code></h3>
                <p>Slimmer JSON wrapper containing only the image data URI and the EMV payload. Note that this shape uses the key <code>image_base64</code> and <code>size</code> (not <code>qr_size</code>):</p>
                <div class="response-box">
                    <div class="code-block">{
  "success":      true,
  "image_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA...",
  "payload":      "00020101021229370016A000000677010111011300668123456785802TH53037645406100.506304F88B",
  "target":       "0812345678",
  "amount":       100.5,
  "size":         300
}</div>
                </div>

                <h3><code>format=image</code> (default)</h3>
                <p>The response body is the raw PNG bytes with <code>Content-Type: image/png</code> and <code>Content-Disposition: inline; filename="promptpay-qr.png"</code>. No JSON envelope is sent.</p>

                <h3>Error Response</h3>
                <p>All errors are JSON with HTTP 400 and the shape <code>{ "error": "...", "message": "..." }</code>:</p>

                <div class="error-box">
                    <p><strong>Missing target</strong></p>
                    <div class="code-block">{
  "error":   "Missing required parameter: target",
  "message": "Please provide a phone number, tax ID, or e-wallet ID"
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Unsupported <code>format</code></strong></p>
                    <div class="code-block">{
  "error":   "Invalid format parameter",
  "message": "Supported formats: image, json, base64"
}</div>
                </div>

                <p class="info-box"><strong>Note:</strong> A target that doesn't match any of the three recognised patterns is <em>not</em> rejected — the server just generates a QR with the literal string and reports <code>"target_type": "phone"</code>. Strict ID validation is the caller's responsibility.</p>
            </div>

            <!-- EMV QR Code Structure -->
            <div class="section">
                <h2>🔧 EMV QR Code Structure</h2>
                <p>The generated QR codes follow the EMV® QR Code Specification for Payment Systems. The <code>payload</code> field in JSON responses is the exact TLV string the server encoded.</p>

                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>TLV Tag</th>
                            <th>Meaning</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>00</code></td>
                            <td>Payload Format Indicator (always <code>01</code>)</td>
                            <td><code>00020101</code></td>
                        </tr>
                        <tr>
                            <td><code>01</code></td>
                            <td>Point of Initiation Method — <code>11</code> static, <code>12</code> dynamic (dynamic is used whenever an amount is supplied)</td>
                            <td><code>0112</code></td>
                        </tr>
                        <tr>
                            <td><code>29</code></td>
                            <td>Merchant Account Information (PromptPay ID, prefixed with the AID <code>A000000677010111</code>)</td>
                            <td><code>29370016A00000067701011101130066812345678</code></td>
                        </tr>
                        <tr>
                            <td><code>54</code></td>
                            <td>Transaction Amount (only when supplied)</td>
                            <td><code>540100.50</code></td>
                        </tr>
                        <tr>
                            <td><code>53</code></td>
                            <td>Transaction Currency (always <code>764</code> = THB)</td>
                            <td><code>5303764</code></td>
                        </tr>
                        <tr>
                            <td><code>58</code></td>
                            <td>Country Code (always <code>TH</code>)</td>
                            <td><code>5802TH</code></td>
                        </tr>
                        <tr>
                            <td><code>63</code></td>
                            <td>CRC checksum</td>
                            <td><code>6304F88B</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Usage Guidelines -->
            <div class="section">
                <h2>📋 Usage Guidelines</h2>

                <div class="warning-box">
                    <strong>Important:</strong> Validate PromptPay IDs in your own code before calling the API. The server only rejects an empty/missing <code>target</code> — any other input is accepted verbatim.
                </div>

                <h3>Best Practices</h3>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>ID validation:</strong> Strip whitespace and <code>+66</code> prefixes yourself; check digit length matches 10 (phone), 13 (tax), or 15 (ewallet).</li>
                    <li><strong>Amount precision:</strong> Up to 2 decimal places (e.g. <code>100.50</code>).</li>
                    <li><strong>Sizing:</strong> 300px is good for web, 500–1000px for print.</li>
                    <li><strong>Testing:</strong> Always scan the QR with a real PromptPay app before going to production.</li>
                </ul>

                <h3>Mobile Number Formats</h3>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li>Thai mobile numbers start with <code>06</code>, <code>08</code>, or <code>09</code>.</li>
                    <li>You can pass either <code>0XXXXXXXXX</code> or <code>+66XXXXXXXXX</code>; the server normalises to the <code>66</code> prefix internally.</li>
                    <li>Total length: 10 digits (with leading <code>0</code>) or 11 digits (with <code>+66</code>).</li>
                </ul>
            </div>

            <!-- Error Codes -->
            <div class="section">
                <h2>⚠️ Error Codes</h2>
                <p>There is no numeric <code>code</code> field. Every error response is JSON with HTTP 400 and the shape <code>{ "error": "&lt;title&gt;", "message": "&lt;details&gt;" }</code>:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th><code>error</code></th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>Missing required parameter: target</code></td>
                            <td>No <code>target</code> was supplied at all</td>
                        </tr>
                        <tr>
                            <td><code>Invalid format parameter</code></td>
                            <td><code>format</code> is not one of <code>image</code>, <code>json</code>, <code>base64</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>HTML image display</h3>
                <p>Point an <code>&lt;img&gt;</code> tag straight at the endpoint with the default <code>image</code> format:</p>
                <div class="code-block">&lt;img src="/api/promptpay-qr-generator/?target=0812345678&amp;amount=100.50&amp;size=400"
     alt="PromptPay QR" /&gt;</div>

                <h3>JavaScript fetch (JSON)</h3>
                <div class="code-block">fetch('/api/promptpay-qr-generator/?target=0812345678&amount=100.50&format=json')
  .then(r =&gt; r.json())
  .then(data =&gt; {
    if (data.success) {
      document.getElementById('qr').src = data.qr_url;  // data:image/png;base64,...
      console.log('Detected:', data.target_type);       // "phone"
      console.log('Payload:',  data.payload);           // raw EMV TLV string
    }
  });</div>

                <h3>JavaScript fetch (raw PNG)</h3>
                <div class="code-block">fetch('/api/promptpay-qr-generator/?target=0812345678')
  .then(r =&gt; r.blob())
  .then(blob =&gt; URL.createObjectURL(blob))
  .then(url =&gt; document.getElementById('qr').src = url);</div>
            </div>

            <!-- Rate Limits -->
            <div class="section">
                <h2>🚦 Rate Limits</h2>
                <p>Currently, there are no rate limits imposed on this API. However, please use it responsibly and avoid excessive requests that might impact service availability for other users.</p>
            </div>

            <!-- Try It Out -->
            <div class="try-it">
                <h3>🎯 Ready to Try?</h3>
                <p>Test the PromptPay QR Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/promptpay-qr-generator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
