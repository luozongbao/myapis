<?php
/**
 * Health Calculator API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 */

$spec = [
    'slug'    => 'health-calculator',
    'title'   => '🏥 Health Calculator API',
    'tagline' => 'Comprehensive health calculations with BMI, BMR, Daily Intake, and Water Intake',
    'crumb'   => 'Health Calculator',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

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
                                <td><code>calculator</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>Calculation type: "bmi", "bmr", "intake", or "water"</td>
                            </tr>
                            <tr>
                                <td><code>weight</code></td>
                                <td>number</td>
                                <td><span class="required">Required</span></td>
                                <td>Weight in kilograms (or pounds if unit=imperial)</td>
                            </tr>
                            <tr>
                                <td><code>height</code></td>
                                <td>number</td>
                                <td><span class="required">Required</span></td>
                                <td>Height in centimeters (or inches if unit=imperial). Not required for water calculator.</td>
                            </tr>
                            <tr>
                                <td><code>unit</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"metric" (default) or "imperial" - Unit system for weight/height conversion</td>
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
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "calculator": "bmi",
    "weight": 70,
    "height": 175
  }'</div>

                    <h4>Example Request - Daily Intake Calculation</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "calculator": "intake",
    "weight": 70,
    "height": 175,
    "age": 30,
    "gender": "male",
    "activity": "moderate",
    "goal": "maintain"
  }'</div>

                    <h4>Example Request - Water Intake Calculation</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "calculator": "water",
    "weight": 70,
    "age": 30,
    "gender": "male",
    "activity": "moderate",
    "climate": "temperate"
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>BMI Response Example</h4>
                    <div class="code-block">{
  "success": true,
  "data": {
    "bmi": 22.86,
    "category": "Normal weight",
    "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise."
  },
  "message": "BMI calculated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <div class="response-box">
                    <h4>Daily Intake Response Example</h4>
                    <div class="code-block">{
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
}</div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Missing required parameter: weight",
  "code": "MISSING_PARAMETER",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
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
                <a href="../health-calculator.php" class="btn">Try Web Interface</a>
                <a href="/api/health-calculator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
