<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Username Generator API Documentation</title>
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

        .theme-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .theme-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #e9ecef;
        }

        .theme-item h5 {
            color: #333;
            margin-bottom: 8px;
        }

        .theme-item p {
            color: #666;
            font-size: 0.9em;
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
            <h1>👤 Username Generator API</h1>
            <p>Create unique usernames using themed word combinations</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../">← Back to Main</a>
                <span>/</span>
                <a href="./">Username Generator</a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Username Generator API creates unique usernames by combining words from various themed categories. Perfect for user registration systems, game character names, or any application requiring creative username suggestions.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🎭 6 Themed Categories</h4>
                        <p>Animals, Colors, Nature, Tech, Space, and Fantasy themed word sets</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔄 Cross-Theme Combinations</h4>
                        <p>Mix words from different themes for creative combinations</p>
                    </div>
                    <div class="feature-card">
                        <h4>📚 Rich Word Database</h4>
                        <p>100+ general adjectives plus hundreds of themed words</p>
                    </div>
                    <div class="feature-card">
                        <h4>📊 Bulk Generation</h4>
                        <p>Generate multiple username suggestions in a single request</p>
                    </div>
                </div>
            </div>

            <!-- Themes -->
            <div class="section">
                <h2>🎨 Available Themes</h2>
                <div class="theme-list">
                    <div class="theme-item">
                        <h5>🐾 Animals</h5>
                        <p>Wildlife and domestic animals</p>
                    </div>
                    <div class="theme-item">
                        <h5>🌈 Colors</h5>
                        <p>Colors and color variations</p>
                    </div>
                    <div class="theme-item">
                        <h5>🌿 Nature</h5>
                        <p>Natural elements and phenomena</p>
                    </div>
                    <div class="theme-item">
                        <h5>💻 Technology</h5>
                        <p>Tech terms and digital concepts</p>
                    </div>
                    <div class="theme-item">
                        <h5>🚀 Space</h5>
                        <p>Celestial bodies and space terms</p>
                    </div>
                    <div class="theme-item">
                        <h5>🧙 Fantasy</h5>
                        <p>Mythical creatures and magical elements</p>
                    </div>
                </div>
            </div>

            <!-- Base URL -->
            <div class="section">
                <h2>🌐 Base URL</h2>
                <div class="code-block">
https://api.lorwongam.com/username-generator/api/
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

                <!-- Generate Username Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate Username
                    </h3>
                    <p>Generate unique usernames based on specified themes and options.</p>

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
                                <td><code>theme</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"random"</td>
                                <td>Theme: "animals", "colors", "nature", "tech", "space", "fantasy", "random"</td>
                            </tr>
                            <tr>
                                <td><code>format</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"adjective_noun"</td>
                                <td>Format: "adjective_noun", "noun_adjective", "noun_only"</td>
                            </tr>
                            <tr>
                                <td><code>separator</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"_"</td>
                                <td>Word separator: "_", "-", "", "." (empty for no separator)</td>
                            </tr>
                            <tr>
                                <td><code>numbers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Append random numbers to username</td>
                            </tr>
                            <tr>
                                <td><code>case_style</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"lowercase"</td>
                                <td>Case style: "lowercase", "uppercase", "title", "camel"</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>1</td>
                                <td>Number of usernames to generate (1-20)</td>
                            </tr>
                            <tr>
                                <td><code>max_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>null</td>
                                <td>Maximum username length (excludes numbers)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Simple Username</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/username-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "theme": "animals",
    "format": "adjective_noun",
    "separator": "_"
  }'
                    </div>

                    <h4>Example Request - Gaming Username</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/username-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "theme": "fantasy",
    "format": "adjective_noun",
    "separator": "",
    "numbers": true,
    "case_style": "title",
    "count": 5
  }'
                    </div>

                    <h4>Example Request - Professional Username</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/username-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "theme": "tech",
    "format": "noun_adjective",
    "separator": "-",
    "case_style": "lowercase",
    "max_length": 15
  }'
                    </div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>Single Username Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "username": "swift_panther",
    "theme": "animals",
    "format": "adjective_noun",
    "length": 13,
    "components": {
      "adjective": "swift",
      "noun": "panther"
    },
    "settings": {
      "separator": "_",
      "case_style": "lowercase",
      "numbers": false
    }
  },
  "message": "Username generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Multiple Usernames Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "usernames": [
      {
        "username": "MysticDragon99",
        "theme": "fantasy",
        "length": 13,
        "components": {
          "adjective": "mystic",
          "noun": "dragon"
        }
      },
      {
        "username": "AncientWizard42",
        "theme": "fantasy",
        "length": 15,
        "components": {
          "adjective": "ancient",
          "noun": "wizard"
        }
      }
    ],
    "count": 2,
    "settings": {
      "theme": "fantasy",
      "format": "adjective_noun",
      "separator": "",
      "case_style": "title",
      "numbers": true
    }
  },
  "message": "Usernames generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">
{
  "success": false,
  "error": "Invalid theme specified. Must be one of: animals, colors, nature, tech, space, fantasy, random",
  "code": "INVALID_THEME",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>
            </div>

            <!-- Format Options -->
            <div class="section">
                <h2>🎯 Format Options</h2>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Format</th>
                            <th>Structure</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>adjective_noun</code></td>
                            <td>Adjective + Noun</td>
                            <td>swift_panther</td>
                        </tr>
                        <tr>
                            <td><code>noun_adjective</code></td>
                            <td>Noun + Adjective</td>
                            <td>panther_swift</td>
                        </tr>
                        <tr>
                            <td><code>noun_only</code></td>
                            <td>Noun only</td>
                            <td>panther</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Case Style Options</h3>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Style</th>
                            <th>Description</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>lowercase</code></td>
                            <td>All lowercase</td>
                            <td>swift_panther</td>
                        </tr>
                        <tr>
                            <td><code>uppercase</code></td>
                            <td>All uppercase</td>
                            <td>SWIFT_PANTHER</td>
                        </tr>
                        <tr>
                            <td><code>title</code></td>
                            <td>First letter of each word capitalized</td>
                            <td>Swift_Panther</td>
                        </tr>
                        <tr>
                            <td><code>camel</code></td>
                            <td>CamelCase (no separators)</td>
                            <td>SwiftPanther</td>
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
                            <td><code>INVALID_THEME</code></td>
                            <td>Theme parameter is not valid</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_FORMAT</code></td>
                            <td>Format parameter is not valid</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_CASE_STYLE</code></td>
                            <td>Case style parameter is not valid</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_COUNT</code></td>
                            <td>Count is outside the valid range (1-20)</td>
                        </tr>
                        <tr>
                            <td><code>GENERATION_ERROR</code></td>
                            <td>Error occurred during username generation</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Rate Limits -->
            <div class="section">
                <h2>🚦 Rate Limits</h2>
                <p>Currently, there are no rate limits imposed on this API. However, please use it responsibly and avoid excessive requests that might impact service availability for other users.</p>
            </div>

            <!-- Try It Out -->
            <div class="try-it">
                <h3>🎯 Ready to Try?</h3>
                <p>Test the Username Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../" class="btn">Try Web Interface</a>
                <a href="../api/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
