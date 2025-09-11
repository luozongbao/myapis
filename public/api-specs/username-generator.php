<?php
// Generate dynamic base URL based on current server
function getBaseUrl($toolName) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . '/api/' . $toolName . '/';
}
$baseUrl = getBaseUrl('username-generator');
?>
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
                <a href="../index.php">← Back to Main</a>
                <span>/</span>
                <a href="../username-generator.php">Username Generator</a>
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
                
                <h3>🆕 What's New in v2.1</h3>
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🎨 Multi-Theme Selection</h4>
                        <p>Select multiple themes simultaneously using the new <code>themes</code> array parameter</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔄 Backward Compatibility</h4>
                        <p>Legacy <code>theme</code> parameter still supported for single-theme selection</p>
                    </div>
                    <div class="feature-card">
                        <h4>🧹 Cleaner Interface</h4>
                        <p>Removed unused <code>use_case</code> parameter for simpler API</p>
                    </div>
                    <div class="feature-card">
                        <h4>📚 Enhanced Themes</h4>
                        <p>9 comprehensive themes with thousands of curated words</p>
                    </div>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🎭 9 Comprehensive Themes</h4>
                        <p>Fantasy, Professional, Science, Tech, Chemistry, Things, Health, Nature, and Space-Time themed word sets</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔄 Multi-Theme Selection</h4>
                        <p>Select multiple themes simultaneously for diverse combinations</p>
                    </div>
                    <div class="feature-card">
                        <h4>📚 Rich Word Database</h4>
                        <p>100+ general adjectives plus thousands of themed words across all categories</p>
                    </div>
                    <div class="feature-card">
                        <h4>📊 Bulk Generation</h4>
                        <p>Generate up to 50 usernames in a single request</p>
                    </div>
                </div>
            </div>

            <!-- Themes -->
            <div class="section">
                <h2>🎨 Available Themes</h2>
                <div class="theme-list">
                    <div class="theme-item">
                        <h5>🧙 Fantasy</h5>
                        <p>Epic and mythical usernames for gaming (Epic, Shadow, Warrior, Dragon, Wizard)</p>
                    </div>
                    <div class="theme-item">
                        <h5>💼 Professional</h5>
                        <p>Business and LinkedIn-ready usernames (Smart, Expert, Developer, Manager, Director)</p>
                    </div>
                    <div class="theme-item">
                        <h5>🔬 Science and Space</h5>
                        <p>Space exploration and scientific terms (Stellar, Galaxy, Quantum, Atom, Einstein)</p>
                    </div>
                    <div class="theme-item">
                        <h5>💻 Computer Technology</h5>
                        <p>Programming and tech-focused (Digital, Algorithm, Framework, Docker, JavaScript)</p>
                    </div>
                    <div class="theme-item">
                        <h5>⚗️ Elements and Chemistry</h5>
                        <p>Chemistry and periodic elements (Hydrogen, Carbon, Molecular, Crystal, Plasma)</p>
                    </div>
                    <div class="theme-item">
                        <h5>🏠 Things</h5>
                        <p>Everyday objects and items (Fork, Table, Chair, Lamp, Knife)</p>
                    </div>
                    <div class="theme-item">
                        <h5>💪 Body and Health</h5>
                        <p>Health and anatomy themed (Heart, Brain, Strong, Healthy, Muscle)</p>
                    </div>
                    <div class="theme-item">
                        <h5>🌿 Nature</h5>
                        <p>Landscape, fruits and animals (Mountain, Grape, Fox, Wolf, Banana)</p>
                    </div>
                    <div class="theme-item">
                        <h5>⏰ Space and Time</h5>
                        <p>Concepts of space and time (Metric, Meter, Hour, Space, Time, Centi)</p>
                    </div>
                </div>
            </div>

            <!-- Base URL -->
            <div class="section">
                <h2>🌐 Base URL</h2>
                <div class="code-block">
<?php echo $baseUrl; ?>
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

                <!-- Get Available Themes Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span>
                        <span class="url">/?action=themes</span>
                        Get Available Themes
                    </h3>
                    <p>Retrieve all available themes with descriptions.</p>

                    <h4>Example Request</h4>
                    <div class="code-block">
curl "<?php echo $baseUrl; ?>?action=themes"
                    </div>

                    <h4>Response</h4>
                    <div class="code-block">
{
  "success": true,
  "themes": [
    "Fantasy",
    "Professional", 
    "Science and Space",
    "Computer Technology",
    "Elements and Chemistry",
    "Things",
    "Body and Health",
    "Nature",
    "Space and Time"
  ],
  "theme_descriptions": {
    "Fantasy": "Epic and mythical usernames for gaming and fantasy lovers",
    "Professional": "Suitable for business, LinkedIn, and professional networks",
    "Science and Space": "Science and space exploration themed usernames",
    "Computer Technology": "Tech and programming themed usernames",
    "Elements and Chemistry": "Science-inspired usernames with elements and compounds",
    "Things": "Everyday objects and items themed usernames", 
    "Body and Health": "Body parts and health-themed usernames",
    "Nature": "Nature-inspired usernames with plants, animals, and landscapes",
    "Space and Time": "Usernames inspired by concepts of space and time"
  }
}
                    </div>
                </div>

                <!-- Generate Username Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="method get">GET</span>
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
                                <td><code>themes</code></td>
                                <td>array</td>
                                <td><span class="optional">Optional</span></td>
                                <td>["Fantasy"]</td>
                                <td><strong>NEW:</strong> Multiple theme selection - Array of themes: "Fantasy", "Professional", "Science and Space", "Computer Technology", "Elements and Chemistry", "Things", "Body and Health", "Nature", "Space and Time"</td>
                            </tr>
                            <tr>
                                <td><code>theme</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"Fantasy"</td>
                                <td><strong>Deprecated:</strong> Single theme selection (use <code>themes</code> instead)</td>
                            </tr>
                            <tr>
                                <td><code>min_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>6</td>
                                <td>Minimum username length (3-50)</td>
                            </tr>
                            <tr>
                                <td><code>max_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>20</td>
                                <td>Maximum username length (3-50)</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>10</td>
                                <td>Number of usernames to generate (1-50)</td>
                            </tr>
                            <tr>
                                <td><code>include_numbers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Add random numbers to usernames</td>
                            </tr>
                            <tr>
                                <td><code>include_symbols</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Add symbols like _, -, .</td>
                            </tr>
                            <tr>
                                <td><code>capitalize</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Capitalize words</td>
                            </tr>
                            <tr>
                                <td><code>avoid_repetition</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Avoid duplicate word combinations</td>
                            </tr>
                            <tr>
                                <td><code>use_all_adjectives</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Use adjectives from all themes</td>
                            </tr>
                            <tr>
                                <td><code>use_general_adjectives</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Add general adjectives (colors, shapes, sizes, etc.)</td>
                            </tr>
                            <tr>
                                <td><code>custom_words</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>""</td>
                                <td>Comma-separated custom words</td>
                            </tr>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>null</td>
                                <td>Maximum username length (excludes numbers)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Multi-Theme Username (NEW)</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "themes": ["Fantasy", "Computer Technology"],
    "min_length": 8,
    "max_length": 15,
    "count": 5,
    "include_numbers": true
  }'
                    </div>

                    <h4>Example Request - Science & Chemistry Mix</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "themes": ["Science and Space", "Elements and Chemistry"],
    "min_length": 10,
    "max_length": 18,
    "use_general_adjectives": true,
    "count": 10
  }'
                    </div>

                    <h4>Example Request - Professional Username</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "themes": ["Professional"],
    "min_length": 8,
    "max_length": 15,
    "capitalize": true,
    "avoid_repetition": true,
    "use_general_adjectives": true,
    "count": 8
  }'
                    </div>

                    <h4>Example Request - Creative All-Theme Mix</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "themes": ["Nature", "Things", "Body and Health"],
    "use_all_adjectives": true,
    "custom_words": "Dragon,Phoenix",
    "include_symbols": true,
    "count": 15
  }'
                    </div>

                    <h4>Example GET Request - Multi-Theme</h4>
                    <div class="code-block">
curl "<?php echo $baseUrl; ?>?themes=Fantasy,Professional&count=5&include_numbers=true"
                    </div>

                    <h4>Example GET Request - Single Theme (Backward Compatible)</h4>
                    <div class="code-block">
curl "<?php echo $baseUrl; ?>?theme=Nature&min_length=10&max_length=15&count=8"
                    </div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>Multi-Theme Username Response (NEW)</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "usernames": [
      "QuantumWarrior",
      "CyberDragon",
      "StellarNinja",
      "AlgorithmKnight",
      "CosmicHunter"
    ],
    "count": 5,
    "options_used": {
      "themes": ["Fantasy", "Computer Technology"],
      "min_length": 8,
      "max_length": 15,
      "count": 5,
      "include_numbers": false,
      "include_symbols": false,
      "capitalize": true,
      "avoid_repetition": true,
      "use_all_adjectives": false,
      "use_general_adjectives": false,
      "custom_words": ""
    }
  },
  "generation_info": {
    "themes": ["Fantasy", "Computer Technology"],
    "theme_count": 2,
    "length_range": "8-15 characters",
    "features": {
      "numbers": "excluded",
      "symbols": "excluded",
      "capitalization": "enabled"
    }
  },
  "timestamp": "2025-09-11 12:34:56"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Science & Chemistry Mix Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "usernames": [
      "AtomicNebula",
      "MolecularQuasar", 
      "CrystallineGalaxy",
      "HydrogenStar",
      "QuantumCarbon"
    ],
    "count": 5,
    "options_used": {
      "themes": ["Science and Space", "Elements and Chemistry"],
      "min_length": 10,
      "max_length": 18,
      "use_general_adjectives": true,
      "count": 5
    }
  },
  "generation_info": {
    "themes": ["Science and Space", "Elements and Chemistry"],
    "theme_count": 2,
    "length_range": "10-18 characters",
    "features": {
      "numbers": "excluded",
      "symbols": "excluded", 
      "capitalization": "enabled"
    }
  },
  "timestamp": "2025-09-11 14:22:15"
}
                    </div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">
{
  "success": false,
  "error": "Validation failed",
  "messages": [
    "Invalid theme selected: invalidtheme"
  ]
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
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/username-generator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
