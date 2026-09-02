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
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate PromptPay QR Code
                    </h3>
                    <p>Generate a PromptPay QR code for the specified recipient and optional amount.</p>

                    <h4>Request Parameters</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>target</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>PromptPay target (phone number, tax ID, or e-wallet ID)</td>
                            </tr>
                            <tr>
                                <td><code>amount</code></td>
                                <td>number</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Payment amount in Thai Baht (THB)</td>
                            </tr>
                            <tr>
                                <td><code>size</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>QR code size in pixels (50-1000, default: 300)</td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Output format: <code>"image"</code>, <code>"json"</code>, or <code>"base64"</code> / <code>"data"</code> (default: <code>"image"</code>)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>ID Format Guidelines</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>ID Type</th>
                                <th>Format</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Mobile Number</td>
                                <td>+66XXXXXXXXX or 0XXXXXXXXX</td>
                                <td>+66812345678 or 0812345678</td>
                            </tr>
                            <tr>
                                <td>Tax ID</td>
                                <td>13-digit number</td>
                                <td>1234567890123</td>
                            </tr>
                            <tr>
                                <td>e-Wallet ID</td>
                                <td>15-digit number</td>
                                <td>123456789012345</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Mobile Number with Amount</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "0812345678",
    "amount": 100.50,
    "size": 300,
    "format": "json"
  }'</div>

                    <h4>Example Request - Tax ID without Amount</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "1234567890123",
    "size": 400,
    "format": "json"
  }'</div>

                    <h4>Example Request - e-Wallet ID with Large Amount</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "123456789012345",
    "amount": 2500,
    "size": 500,
    "format": "base64"
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response (Base64 Format)</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
    "promptpay_id": "0812345678",
    "amount": 100.50,
    "currency": "THB",
    "emv_qr_data": "00020101021129370016A000000677010111011300...",
    "size": 300,
    "format": "base64"
  },
  "message": "PromptPay QR code generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Success Response (Data Format)</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "emv_qr_data": "00020101021129370016A00000067701011101130081234567803021.02540TH63041234",
    "promptpay_id": "0812345678",
    "amount": null,
    "currency": "THB",
    "size": 300,
    "format": "data"
  },
  "message": "PromptPay QR data generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Invalid PromptPay ID format",
  "code": "INVALID_ID",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>
            </div>

            <!-- EMV QR Code Structure -->
            <div class="section">
                <h2>🔧 EMV QR Code Structure</h2>
                <p>The generated QR codes follow the EMV® QR Code Specification for Payment Systems. The data format includes:</p>

                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Description</th>
                            <th>Example Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Payload Format Indicator</td>
                            <td>Version of the QR code format</td>
                            <td>01</td>
                        </tr>
                        <tr>
                            <td>Point of Initiation Method</td>
                            <td>Static or dynamic QR code</td>
                            <td>11 (Static), 12 (Dynamic)</td>
                        </tr>
                        <tr>
                            <td>Merchant Account Information</td>
                            <td>PromptPay identification data</td>
                            <td>Contains PromptPay ID</td>
                        </tr>
                        <tr>
                            <td>Transaction Amount</td>
                            <td>Payment amount (if specified)</td>
                            <td>100.50</td>
                        </tr>
                        <tr>
                            <td>Transaction Currency</td>
                            <td>ISO 4217 currency code</td>
                            <td>764 (THB)</td>
                        </tr>
                        <tr>
                            <td>Country Code</td>
                            <td>ISO 3166-1 country code</td>
                            <td>TH</td>
                        </tr>
                        <tr>
                            <td>CRC</td>
                            <td>Checksum for data integrity</td>
                            <td>4-digit checksum</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Usage Guidelines -->
            <div class="section">
                <h2>📋 Usage Guidelines</h2>

                <div class="warning-box">
                    <strong>Important:</strong> Always validate PromptPay IDs before generating QR codes. Invalid IDs may result in failed payments.
                </div>

                <h3>Best Practices</h3>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>ID Validation:</strong> Ensure mobile numbers and tax IDs are valid Thai formats</li>
                    <li><strong>Amount Precision:</strong> Use up to 2 decimal places for amounts</li>
                    <li><strong>QR Code Size:</strong> Use appropriate sizes for display medium (300px for web, 500px+ for print)</li>
                    <li><strong>Error Handling:</strong> Always check the response for errors before displaying QR codes</li>
                    <li><strong>Testing:</strong> Test QR codes with actual PromptPay apps before production use</li>
                </ul>

                <h3>Mobile Number Formats</h3>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li>Thai mobile numbers start with 06, 08, or 09</li>
                    <li>Can include +66 country code or start with 0</li>
                    <li>Total length: 10 digits (with 0) or 11 digits (with +66)</li>
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
                            <td><code>INVALID_ID</code></td>
                            <td>PromptPay ID format is invalid</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_AMOUNT</code></td>
                            <td>Amount is negative or exceeds maximum limit</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_SIZE</code></td>
                            <td>QR code size is outside valid range (50-1000px)</td>
                        </tr>
                        <tr>
                            <td><code>QR_GENERATION_ERROR</code></td>
                            <td>Error occurred during QR code generation</td>
                        </tr>
                        <tr>
                            <td><code>MISSING_PARAMETER</code></td>
                            <td>Required parameter is missing</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>HTML Image Display</h3>
                <div class="code-block">&lt;img src="data:image/png;base64,{base64_data}" alt="PromptPay QR Code" /&gt;</div>

                <h3>JavaScript Integration</h3>
                <div class="code-block">fetch('<?php echo htmlspecialchars($baseUrl); ?>', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    id: '0812345678',
    amount: 100.50
  })
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    document.getElementById('qr-image').src = data.data.qr_code;
  }
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
                <p>Test the PromptPay QR Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/promptpay-qr-generator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
