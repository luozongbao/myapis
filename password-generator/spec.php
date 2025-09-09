<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Generator API Documentation</title>
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

        .security-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
            margin: 5px;
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
            <h1>🔐 Password Generator API</h1>
            <p>Generate cryptographically secure passwords with customizable complexity</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../">← Back to Main</a>
                <span>/</span>
                <a href="./">Password Generator</a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Password Generator API creates cryptographically secure passwords with customizable complexity options. Built with security best practices, it provides reliable password generation for applications requiring strong authentication.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🔒 Cryptographically Secure</h4>
                        <p>Uses PHP's secure random_bytes() function for true randomness</p>
                    </div>
                    <div class="feature-card">
                        <h4>⚙️ Customizable Character Sets</h4>
                        <p>Control uppercase, lowercase, numbers, and special characters</p>
                    </div>
                    <div class="feature-card">
                        <h4>📊 Password Strength Analysis</h4>
                        <p>Automatic strength scoring and detailed feedback</p>
                    </div>
                    <div class="feature-card">
                        <h4>📏 Flexible Length</h4>
                        <p>Generate passwords from 4 to 128 characters long</p>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <span class="security-badge">🛡️ Cryptographically Secure</span>
                    <span class="security-badge">🎯 Zero Logging</span>
                    <span class="security-badge">⚡ High Performance</span>
                </div>
            </div>

            <!-- Base URL -->
            <div class="section">
                <h2>🌐 Base URL</h2>
                <div class="code-block">
https://api.lorwongam.com/password-generator/api/
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

                <!-- Generate Password Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate Password
                    </h3>
                    <p>Generate a cryptographically secure password with customizable options.</p>

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
                                <td><code>length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>12</td>
                                <td>Password length (4-128 characters)</td>
                            </tr>
                            <tr>
                                <td><code>uppercase</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include uppercase letters (A-Z)</td>
                            </tr>
                            <tr>
                                <td><code>lowercase</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include lowercase letters (a-z)</td>
                            </tr>
                            <tr>
                                <td><code>numbers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include numbers (0-9)</td>
                            </tr>
                            <tr>
                                <td><code>symbols</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Include special characters (!@#$%^&*)</td>
                            </tr>
                            <tr>
                                <td><code>exclude_similar</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Exclude similar characters (0,O,l,1,i,I)</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>1</td>
                                <td>Number of passwords to generate (1-10)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Basic Password</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/password-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "length": 16,
    "uppercase": true,
    "lowercase": true,
    "numbers": true,
    "symbols": false
  }'
                    </div>

                    <h4>Example Request - High Security Password</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/password-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "length": 24,
    "uppercase": true,
    "lowercase": true,
    "numbers": true,
    "symbols": true,
    "exclude_similar": true
  }'
                    </div>

                    <h4>Example Request - Multiple Passwords</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/password-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "length": 12,
    "uppercase": true,
    "lowercase": true,
    "numbers": true,
    "symbols": true,
    "count": 5
  }'
                    </div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>Single Password Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "password": "Kx7mN9pQw2Yv8zR3",
    "length": 16,
    "strength": {
      "score": 4,
      "level": "Very Strong",
      "feedback": "Excellent password with good character variety"
    },
    "character_sets": {
      "uppercase": true,
      "lowercase": true,
      "numbers": true,
      "symbols": false
    },
    "entropy": 95.42
  },
  "message": "Password generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Multiple Passwords Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "passwords": [
      {
        "password": "Kx7mN9pQw2Yv",
        "strength": {
          "score": 4,
          "level": "Very Strong"
        }
      },
      {
        "password": "Zf5bH8nMv3Qp",
        "strength": {
          "score": 4,
          "level": "Very Strong"
        }
      }
    ],
    "count": 2,
    "settings": {
      "length": 12,
      "character_sets": {
        "uppercase": true,
        "lowercase": true,
        "numbers": true,
        "symbols": false
      }
    }
  },
  "message": "Passwords generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">
{
  "success": false,
  "error": "Invalid password length. Must be between 4 and 128 characters",
  "code": "INVALID_LENGTH",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>
            </div>

            <!-- Password Strength -->
            <div class="section">
                <h2>💪 Password Strength Analysis</h2>
                <p>Each generated password includes automatic strength analysis based on length, character variety, and entropy.</p>

                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Score</th>
                            <th>Level</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Very Weak</td>
                            <td>Short length, single character type</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Weak</td>
                            <td>Short length, limited character variety</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Moderate</td>
                            <td>Adequate length, good character variety</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Strong</td>
                            <td>Good length, multiple character types</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Very Strong</td>
                            <td>Long length, all character types, high entropy</td>
                        </tr>
                    </tbody>
                </table>
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
                            <td><code>INVALID_LENGTH</code></td>
                            <td>Password length is outside the valid range (4-128)</td>
                        </tr>
                        <tr>
                            <td><code>NO_CHARACTER_SETS</code></td>
                            <td>At least one character set must be enabled</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_COUNT</code></td>
                            <td>Password count is outside the valid range (1-10)</td>
                        </tr>
                        <tr>
                            <td><code>GENERATION_ERROR</code></td>
                            <td>Error occurred during password generation</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Security Notes -->
            <div class="section">
                <h2>🛡️ Security Features</h2>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8;">
                    <li><strong>Cryptographically Secure:</strong> Uses PHP's <code>random_bytes()</code> for true randomness</li>
                    <li><strong>No Logging:</strong> Generated passwords are not stored or logged anywhere</li>
                    <li><strong>HTTPS Only:</strong> All communications are encrypted</li>
                    <li><strong>High Entropy:</strong> Calculated entropy values help assess true password strength</li>
                    <li><strong>Similar Character Exclusion:</strong> Option to exclude visually similar characters</li>
                </ul>
            </div>

            <!-- Rate Limits -->
            <div class="section">
                <h2>🚦 Rate Limits</h2>
                <p>Currently, there are no rate limits imposed on this API. However, please use it responsibly and avoid excessive requests that might impact service availability for other users.</p>
            </div>

            <!-- Try It Out -->
            <div class="try-it">
                <h3>🎯 Ready to Try?</h3>
                <p>Test the Password Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../" class="btn">Try Web Interface</a>
                <a href="api/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
