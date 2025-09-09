<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptPay QR Generator API Documentation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

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
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

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

        .method.post {
            background: #007bff;
        }

        .method.get {
            background: #28a745;
        }

        .url {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-left: 10px;
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
        }

        .parameter-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .parameter-table th,
        .parameter-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .parameter-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .parameter-table td {
            color: #555;
        }

        .required {
            color: #dc3545;
            font-weight: bold;
        }

        .optional {
            color: #6c757d;
        }

        .response-box {
            background: #f0f8f0;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .error-box {
            background: #fdf2f2;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .feature-card h4 {
            color: #333;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            font-size: 0.9em;
        }

        .try-it {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }

        .try-it h3 {
            margin-bottom: 15px;
        }

        .try-it p {
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            color: #856404;
        }

        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            color: #0c5460;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
            
            .code-block {
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>💳 PromptPay QR Generator API</h1>
            <p>Generate EMV-compliant PromptPay QR codes for Thai payment system</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../">← Back to Main</a>
                <span>/</span>
                <a href="./">PromptPay QR Generator</a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
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
                <div class="code-block">
https://api.lorwongam.com/promptpay-qr-generator/api/
                </div>
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
                                <td><code>id</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>PromptPay ID (mobile number, tax ID, or e-Wallet ID)</td>
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
                                <td>QR code size in pixels (default: 300, max: 1000)</td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Output format: "base64" or "data" (default: "base64")</td>
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
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/promptpay-qr-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "id": "0812345678",
    "amount": 100.50,
    "size": 300
  }'
                    </div>

                    <h4>Example Request - Tax ID without Amount</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/promptpay-qr-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "id": "1234567890123",
    "size": 400
  }'
                    </div>

                    <h4>Example Request - e-Wallet ID with Large Amount</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/promptpay-qr-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "id": "123456789012345",
    "amount": 2500,
    "size": 500,
    "format": "data"
  }'
                    </div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response (Base64 Format)</h3>
                <div class="response-box">
                    <div class="code-block">
{
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
}
                    </div>
                </div>

                <h3>Success Response (Data Format)</h3>
                <div class="response-box">
                    <div class="code-block">
{
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
}
                    </div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">
{
  "success": false,
  "error": "Invalid PromptPay ID format",
  "code": "INVALID_ID",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
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
                <div class="code-block">
&lt;img src="data:image/png;base64,{base64_data}" alt="PromptPay QR Code" /&gt;
                </div>

                <h3>JavaScript Integration</h3>
                <div class="code-block">
fetch('https://api.lorwongam.com/promptpay-qr-generator/api/', {
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
});
                </div>
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
                <a href="../" class="btn">Try Web Interface</a>
                <a href="../api/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
