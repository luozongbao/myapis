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
                        <span class="method get">GET</span> / <span class="method post">POST</span>
                        <span class="url">/</span>
                        Unified Health Calculator
                    </h3>
                    <p>The same endpoint handles all four calculators. Pass the calculator name in <code>calculator</code>; the other fields depend on which calculator you choose. Parameters may be supplied via the query string (GET) or as a JSON body (POST) — POSTed keys override query-string keys.</p>

                    <h4>Common Parameters</h4>
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
                                <td>One of <code>bmi</code>, <code>bmr</code>, <code>intake</code>, <code>water</code></td>
                            </tr>
                            <tr>
                                <td><code>unit</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>metric</code> (default) or <code>imperial</code>. For <code>imperial</code>, <code>weight</code> is treated as pounds and <code>height</code> as inches (only relevant for non-water calculators).</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Calculator-Specific Parameters</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Used by</th>
                                <th>Required</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>weight</code></td>
                                <td>all</td>
                                <td><span class="required">Required</span></td>
                                <td>Weight in kg (or lb if <code>unit=imperial</code>)</td>
                            </tr>
                            <tr>
                                <td><code>height</code></td>
                                <td>bmi, bmr, intake</td>
                                <td><span class="required">Required*</span></td>
                                <td>Height in cm (or in if <code>unit=imperial</code>). Ignored for water.</td>
                            </tr>
                            <tr>
                                <td><code>age</code></td>
                                <td>bmr, intake, water</td>
                                <td><span class="required">Required*</span></td>
                                <td>Age in years (1–120)</td>
                            </tr>
                            <tr>
                                <td><code>gender</code></td>
                                <td>bmr, intake, water</td>
                                <td><span class="required">Required*</span></td>
                                <td><code>male</code> or <code>female</code></td>
                            </tr>
                            <tr>
                                <td><code>activity</code></td>
                                <td>bmr, intake, water</td>
                                <td><span class="required">Required*</span></td>
                                <td><code>sedentary</code>, <code>light</code>, <code>moderate</code>, <code>active</code>, <code>extra</code></td>
                            </tr>
                            <tr>
                                <td><code>goal</code></td>
                                <td>intake</td>
                                <td><span class="required">Required</span></td>
                                <td><code>maintain</code>, <code>lose</code>, <code>lose-fast</code>, <code>gain</code>, <code>gain-fast</code></td>
                            </tr>
                            <tr>
                                <td><code>climate</code></td>
                                <td>water</td>
                                <td><span class="required">Required</span></td>
                                <td><code>cold</code>, <code>temperate</code>, <code>hot</code>, <code>very-hot</code></td>
                            </tr>
                            <tr>
                                <td><code>healthCondition</code></td>
                                <td>water</td>
                                <td><span class="required">Required</span></td>
                                <td><code>normal</code>, <code>fever</code>, <code>diarrhea</code>, <code>kidney</code>, <code>heart</code>, <code>pregnancy</code>, <code>breastfeeding</code> (note the camelCase spelling)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request — BMI</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?calculator=bmi&weight=70&height=175"</div>

                    <h4>Example Request — BMR (POST JSON)</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "calculator": "bmr",
    "weight": 70,
    "height": 175,
    "age": 30,
    "gender": "male",
    "activity": "moderate"
  }'</div>

                    <h4>Example Request — Daily Intake</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?calculator=intake&weight=70&height=175&age=30&gender=male&activity=moderate&goal=maintain"</div>

                    <h4>Example Request — Water Intake</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?calculator=water&weight=70&age=30&gender=male&activity=moderate&climate=temperate&healthCondition=normal"</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response — BMI</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "bmi": 22.86,
    "category": "Normal weight",
    "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise."
  },
  "calculator": "bmi",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Success Response — BMR</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "bmr": 1649,
    "detail": "Daily calories needed: 2556",
    "advice": "Your BMR is 1649 calories per day. With your activity level, you need approximately 2556 calories daily to maintain your current weight."
  },
  "calculator": "bmr",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Success Response — Daily Intake</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "calories": 2556,
    "breakdown": "Protein: 112g • Carbs: 367g • Fat: 71g<br>BMR: 1649 cal • Maintenance: 2556 cal",
    "advice": "To maintain your current weight, aim for 2556 calories per day with balanced nutrition and regular exercise.",
    "macros": {
      "protein": 112,
      "carbs":   367,
      "fat":     71
    }
  },
  "calculator": "intake",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Success Response — Water Intake</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "amount":    "3773ml/day",
    "breakdown": "Total: 3773ml • From drinks: 3018ml • From food: 755ml<br>Approximately 12.1 glasses (250ml each)",
    "advice":    "Aim for approximately 3773ml (12.1 glasses) of water daily. Spread intake throughout the day...",
    "details": {
      "total":      3773,
      "fromDrinks": 3018,
      "fromFood":   755,
      "glasses":    12.1
    }
  },
  "calculator": "water",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Error Responses</h3>
                <p>All error responses are JSON with HTTP 400 (or 404 in rare routing edge cases). The shape varies by trigger:</p>

                <div class="error-box">
                    <p><strong>Calculator type missing</strong></p>
                    <div class="code-block">{
  "success": false,
  "message": "Calculator type is required",
  "availableCalculators": ["bmi", "bmr", "intake", "water"]
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Missing required field(s)</strong> — message is a comma-joined list, <code>errors</code> is the same list as an array</p>
                    <div class="code-block">{
  "success": false,
  "message": "Height is required for bmr calculation, Age is required for bmr calculation, ...",
  "errors":  ["Height is required for bmr calculation", "Age is required for bmr calculation", "..."]
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Invalid value (out of range / unrealistic)</strong></p>
                    <div class="code-block">{
  "success": false,
  "message": "Please check your height and weight values - they seem unrealistic"
}</div>
                </div>
            </div>

            <!-- Error Codes -->
            <div class="section">
                <h2>⚠️ Error Conditions</h2>
                <p>The API does not return a numeric <code>code</code> field. Errors are identified by the human-readable <code>message</code> string.</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Trigger</th>
                            <th>HTTP</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Missing <code>calculator</code></td>
                            <td>400</td>
                            <td>Returns <code>availableCalculators</code> to help clients pick</td>
                        </tr>
                        <tr>
                            <td>One or more required fields missing for the chosen calculator</td>
                            <td>400</td>
                            <td>Returns comma-joined <code>message</code> and parallel <code>errors</code> array</td>
                        </tr>
                        <tr>
                            <td>Age out of range (must be 1–120)</td>
                            <td>400</td>
                            <td><code>message</code>: <em>"Age must be between 1 and 120 years"</em></td>
                        </tr>
                        <tr>
                            <td>Unrealistic weight / height</td>
                            <td>400</td>
                            <td><code>message</code>: <em>"Please check your height and weight values - they seem unrealistic"</em></td>
                        </tr>
                        <tr>
                            <td>Negative or zero weight / height</td>
                            <td>400</td>
                            <td><code>message</code>: <em>"Weight and height must be positive values"</em></td>
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
