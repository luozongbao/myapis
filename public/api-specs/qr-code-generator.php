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
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate QR Code
                    </h3>
                    <p>Generate a QR code based on the specified data type and content.</p>

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
                                <td><span class="required">Required</span></td>
                                <td>-</td>
                                <td>QR code type: "text", "url", "vcard", "event", "wifi", "phone", "email", "sms"</td>
                            </tr>
                            <tr>
                                <td><code>data</code></td>
                                <td>object/string</td>
                                <td><span class="required">Required</span></td>
                                <td>-</td>
                                <td>Data to encode (structure depends on type)</td>
                            </tr>
                            <tr>
                                <td><code>size</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>10</td>
                                <td>QR code size in pixels (1-50, where each unit = ~30px)</td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>png</td>
                                <td>Image format: "png", "gif", "jpeg", "jpg", "svg"</td>
                            </tr>
                            <tr>
                                <td><code>ecc</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>L</td>
                                <td>Error correction: "L" (~7%), "M" (~15%), "Q" (~25%), "H" (~30%)</td>
                            </tr>
                            <tr>
                                <td><code>color</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>000000</td>
                                <td>Foreground color in hex (without #)</td>
                            </tr>
                            <tr>
                                <td><code bgcolor</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>ffffff</td>
                                <td>Background color in hex (without #)</td>
                            </tr>
                            <tr>
                                <td><code>margin</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>4</td>
                                <td>Margin in pixels (0-50)</td>
                            </tr>
                            <tr>
                                <td><code>response_format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>image</td>
                                <td>Response format: "image" (direct) or "json" (base64 encoded)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Type-Specific Data Structures</h4>

                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Data Structure</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>text</td>
                                <td><code>{"text": "Your text here"}</code></td>
                                <td>Simple plain text</td>
                            </tr>
                            <tr>
                                <td>url</td>
                                <td><code>{"url": "https://example.com"}</code></td>
                                <td>Website URL</td>
                            </tr>
                            <tr>
                                <td>vcard</td>
                                <td><code>{"name": "John Doe", "phone": "+1234567890", "email": "john@example.com", "org": "Company"}</code></td>
                                <td>Contact card</td>
                            </tr>
                            <tr>
                                <td>event</td>
                                <td><code>{"summary": "Meeting", "start": "2025-12-01T10:00", "end": "2025-12-01T11:00", "location": "Office"}</code></td>
                                <td>Calendar event</td>
                            </tr>
                            <tr>
                                <td>wifi</td>
                                <td><code>{"ssid": "MyWiFi", "password": "secret123", "security": "WPA"}</code></td>
                                <td>Wi-Fi credentials</td>
                            </tr>
                            <tr>
                                <td>phone</td>
                                <td><code>{"phone": "+1234567890"}</code></td>
                                <td>Phone number</td>
                            </tr>
                            <tr>
                                <td>email</td>
                                <td><code>{"email": "user@example.com", "subject": "Hello", "body": "Message"}</code></td>
                                <td>Email with subject</td>
                            </tr>
                            <tr>
                                <td>sms</td>
                                <td><code>{"phone": "+1234567890", "message": "Hello"}</code></td>
                                <td>SMS message</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Text QR Code</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "text",
    "data": {
      "text": "Hello, World!"
    },
    "size": 10,
    "format": "png",
    "response_format": "json"
  }'</div>

                    <h4>Example Request - URL QR Code</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "url",
    "data": {
      "url": "https://github.com"
    },
    "size": 15,
    "ecc": "M",
    "color": "0066cc"
  }'</div>

                    <h4>Example Request - vCard QR Code</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "vcard",
    "data": {
      "name": "John Doe",
      "phone": "+1234567890",
      "email": "john@example.com",
      "org": "Acme Corp"
    },
    "size": 12,
    "ecc": "Q"
  }'</div>

                    <h4>Example Request - WiFi QR Code</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "wifi",
    "data": {
      "ssid": "MyHomeWiFi",
      "password": "MyPassword123",
      "security": "WPA"
    },
    "size": 10,
    "format": "png"
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response (JSON)</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
    "type": "url",
    "size": 15,
    "format": "png",
    "content": "https://github.com",
    "dimensions": {
      "width": 450,
      "height": 450
    }
  },
  "message": "QR code generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Success Response (Direct Image)</h3>
                <div class="response-box">
                    <p>When <code>response_format</code> is set to "image", the API returns the QR code image directly with the appropriate Content-Type header.</p>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Invalid QR code type",
  "code": "INVALID_TYPE",
  "timestamp": "2025-09-09T12:00:00Z"
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
                        <p>Link to websites, landing pages, app downloads, or promotional content with scannable QR codes.</p>
                    </div>
                    <div class="category-item">
                        <h4>📇 Contact Sharing</h4>
                        <p>Share contact information instantly with vCard QR codes for business cards or networking events.</p>
                    </div>
                    <div class="category-item">
                        <h4>📅 Event Management</h4>
                        <p>Add events to calendars with QR codes containing event details, dates, and locations.</p>
                    </div>
                    <div class="category-item">
                        <h4>📶 Wi-Fi Access</h4>
                        <p>Share Wi-Fi credentials without typing. Guests scan and connect automatically.</p>
                    </div>
                    <div class="category-item">
                        <h4>📞 Quick Contact</h4>
                        <p>Enable one-tap calling or messaging with phone and SMS QR codes.</p>
                    </div>
                    <div class="category-item">
                        <h4>✉️ Email Integration</h4>
                        <p>Pre-fill email forms with recipient, subject, and body for quick communication.</p>
                    </div>
                </div>
            </div>

            <!-- Best Practices -->
            <div class="section">
                <h2>✨ Best Practices</h2>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>Size Selection:</strong> Use size 10-15 for most web display, 20+ for print materials</li>
                    <li><strong>Error Correction:</strong> Use H level when overlaying logos or in harsh environments</li>
                    <li><strong>Testing:</strong> Always test QR codes with multiple scanner apps before deployment</li>
                    <li><strong>Contrast:</strong> Ensure sufficient contrast between foreground and background colors</li>
                    <li><strong>Quiet Zone:</strong> Maintain adequate margin (default 4px) for reliable scanning</li>
                    <li><strong>Data Limits:</strong> Keep data concise; QR codes become dense and harder to scan with too much data</li>
                </ul>
            </div>

            <!-- Error Codes -->
            <div class="section">
                <h2>⚠️ Error Codes</h2>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>INVALID_TYPE</code></td>
                            <td>Specified QR code type is not supported</td>
                        </tr>
                        <tr>
                            <td><code>MISSING_DATA</code></td>
                            <td>Required data parameter is missing</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_DATA</code></td>
                            <td>Data structure is invalid for the specified type</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_SIZE</code></td>
                            <td>Size is outside valid range (1-50)</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_FORMAT</code></td>
                            <td>Image format is not supported</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_ECC</code></td>
                            <td>Error correction level is invalid</td>
                        </tr>
                        <tr>
                            <td><code>QR_GENERATION_ERROR</code></td>
                            <td>Error occurred during QR code generation</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>HTML Display</h3>
                <div class="code-block">&lt;img src="data:image/png;base64,{base64_data}" alt="QR Code" /&gt;</div>

                <h3>JavaScript Fetch</h3>
                <div class="code-block">fetch('<?php echo htmlspecialchars($baseUrl); ?>', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    type: 'url',
    data: { url: 'https://example.com' },
    response_format: 'json'
  })
})
.then(response =&gt; response.json())
.then(data =&gt; {
  document.getElementById('qr').src = data.data.qr_code;
});</div>
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