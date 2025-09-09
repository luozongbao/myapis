<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Calculator API Documentation</title>
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
            <h1>🏥 Health Calculator API</h1>
            <p>Comprehensive health calculations with BMI, BMR, Daily Intake, and Water Intake</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../">← Back to Main</a>
                <span>/</span>
                <a href="./">Health Calculator</a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Health Calculator API provides comprehensive health-related calculations including BMI (Body Mass Index), BMR (Basal Metabolic Rate), Daily Caloric Intake, and Water Intake requirements. All calculations are based on scientifically proven formulas and provide detailed recommendations.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🧮 BMI Calculator</h4>
                        <p>Calculate Body Mass Index with WHO standard categories and health recommendations</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔥 BMR Calculator</h4>
                        <p>Basal Metabolic Rate calculation using the accurate Mifflin-St Jeor equation</p>
                    </div>
                    <div class="feature-card">
                        <h4>🍽️ Daily Intake Calculator</h4>
                        <p>Personalized caloric needs with detailed macronutrient breakdown</p>
                    </div>
                    <div class="feature-card">
                        <h4>💧 Water Intake Calculator</h4>
                        <p>Daily water requirements based on multiple health and environmental factors</p>
                    </div>
                </div>
            </div>

            <!-- Base URL -->
            <div class="section">
                <h2>🌐 Base URL</h2>
                <div class="code-block">
https://api.lorwongam.com/health-calculator/api/
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

                <!-- Unified Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Unified Health Calculator
                    </h3>
                    <p>Calculate BMI, BMR, Daily Intake, or Water Intake based on the calculation type specified.</p>

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
                                <td><code>type</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>Calculation type: "bmi", "bmr", "daily-intake", or "water-intake"</td>
                            </tr>
                            <tr>
                                <td><code>weight</code></td>
                                <td>number</td>
                                <td><span class="required">Required</span></td>
                                <td>Weight in kilograms</td>
                            </tr>
                            <tr>
                                <td><code>height</code></td>
                                <td>number</td>
                                <td><span class="required">Required</span></td>
                                <td>Height in centimeters or meters</td>
                            </tr>
                            <tr>
                                <td><code>age</code></td>
                                <td>number</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>Age in years (required for BMR, Daily Intake, Water Intake)</td>
                            </tr>
                            <tr>
                                <td><code>gender</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>"male" or "female" (required for BMR, Daily Intake, Water Intake)</td>
                            </tr>
                            <tr>
                                <td><code>activity</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Activity level: "sedentary", "light", "moderate", "active", "extra"</td>
                            </tr>
                            <tr>
                                <td><code>goal</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>For Daily Intake: "maintain", "lose", "lose-fast", "gain", "gain-fast"</td>
                            </tr>
                            <tr>
                                <td><code>climate</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>For Water Intake: "cold", "temperate", "hot", "very-hot"</td>
                            </tr>
                            <tr>
                                <td><code>health_condition</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>For Water Intake: "pregnant", "breastfeeding", "fever", "vomiting", "diarrhea"</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - BMI Calculation</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/health-calculator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "bmi",
    "weight": 70,
    "height": 175
  }'
                    </div>

                    <h4>Example Request - Daily Intake Calculation</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/health-calculator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "daily-intake",
    "weight": 70,
    "height": 175,
    "age": 30,
    "gender": "male",
    "activity": "moderate",
    "goal": "maintain"
  }'
                    </div>

                    <h4>Example Request - Water Intake Calculation</h4>
                    <div class="code-block">
curl -X POST "https://api.lorwongam.com/health-calculator/api/" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "water-intake",
    "weight": 70,
    "height": 175,
    "age": 30,
    "gender": "male",
    "activity": "moderate",
    "climate": "temperate"
  }'
                    </div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>BMI Response Example</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "bmi": 22.86,
    "category": "Normal weight",
    "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise."
  },
  "message": "BMI calculated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Daily Intake Response Example</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "bmr": 1705,
    "maintenanceCalories": 2643,
    "targetCalories": 2643,
    "macronutrients": {
      "protein": {
        "grams": 112,
        "calories": 448,
        "percentage": 17
      },
      "fat": {
        "grams": 73,
        "calories": 661,
        "percentage": 25
      },
      "carbs": {
        "grams": 384,
        "calories": 1534,
        "percentage": 58
      }
    },
    "advice": "Based on your moderate activity level and maintenance goal..."
  },
  "message": "Daily intake calculated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">
{
  "success": false,
  "error": "Missing required parameter: weight",
  "code": "MISSING_PARAMETER",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>
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
                            <td><code>MISSING_PARAMETER</code></td>
                            <td>Required parameter is missing</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_TYPE</code></td>
                            <td>Invalid calculation type specified</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_VALUE</code></td>
                            <td>Parameter value is invalid or out of range</td>
                        </tr>
                        <tr>
                            <td><code>CALCULATION_ERROR</code></td>
                            <td>Error occurred during calculation</td>
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
                <p>Test the Health Calculator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../" class="btn">Try Web Interface</a>
                <a href="../api/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
