<?php
/**
 * Fortune Teller API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 */

$spec = [
    'slug'    => 'fortune-teller',
    'title'   => '🔮 Fortune Teller API',
    'tagline' => 'Get multilingual fortune predictions covering all aspects of life',
    'crumb'   => 'Fortune Teller',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Fortune Teller API provides random fortune predictions in multiple languages. With 52 unique fortunes covering various life aspects, it's perfect for entertainment apps, daily motivation services, or cultural applications.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🌍 Multilingual Support</h4>
                        <p>Thai, Chinese (Simplified), and English language options</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎯 52 Unique Fortunes</h4>
                        <p>Carefully curated predictions covering all aspects of life</p>
                    </div>
                    <div class="feature-card">
                        <h4>📚 5 Life Categories</h4>
                        <p>Love, Career, Health, Finance, and General life advice</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎲 Random Selection</h4>
                        <p>Cryptographically secure random fortune selection</p>
                    </div>
                </div>
            </div>

            <!-- Languages -->
            <div class="section">
                <h2>🌍 Supported Languages</h2>
                <div class="lang-grid">
                    <div class="lang-item">
                        <h5>🇹🇭 Thai</h5>
                        <p>ภาษาไทย (th)</p>
                    </div>
                    <div class="lang-item">
                        <h5>🇨🇳 Chinese</h5>
                        <p>简体中文 (zh)</p>
                    </div>
                    <div class="lang-item">
                        <h5>🇺🇸 English</h5>
                        <p>English (en)</p>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="section">
                <h2>📋 Fortune Categories</h2>
                <div class="categories-grid">
                    <div class="category-item">
                        <h6>💕 Love &amp; Relationships</h6>
                        <p>Romance, relationships, marriage</p>
                    </div>
                    <div class="category-item">
                        <h6>💼 Career &amp; Work</h6>
                        <p>Job prospects, business success</p>
                    </div>
                    <div class="category-item">
                        <h6>🏥 Health &amp; Wellness</h6>
                        <p>Physical and mental health</p>
                    </div>
                    <div class="category-item">
                        <h6>💰 Finance &amp; Wealth</h6>
                        <p>Money, investments, prosperity</p>
                    </div>
                    <div class="category-item">
                        <h6>🌟 General Life</h6>
                        <p>Overall luck and life guidance</p>
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

                <!-- Get Fortune Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span>
                        <span class="url">/</span>
                        Get Random Fortune
                    </h3>
                    <p>Retrieve a random fortune prediction in the specified language.</p>

                    <h4>Parameters</h4>
                    <p>This endpoint doesn't require any parameters. It returns a random fortune from the collection of 52 fortunes, with predictions in all three languages (Thai, Chinese, and English).</p>

                    <h4>Example Request - Get Random Fortune</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>"</div>

                    <h4>Example Request - POST Method</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>"</div>
                </div>

                <!-- POST Method Alternative -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Get Random Fortune (Alternative)
                    </h3>
                    <p>Alternative POST method for getting fortune predictions with JSON request body.</p>

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
                                <td><code>lang</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>"en"</td>
                                <td>Language code: "th", "zh", or "en"</td>
                            </tr>
                            <tr>
                                <td><code>id</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>random</td>
                                <td>Specific fortune ID (1-52)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example POST Request</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "lang": "th"
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>English Fortune Example</h4>
                    <div class="code-block">{
  "success": true,
  "data": {
    "id": 7,
    "fortune": "Today brings unexpected opportunities. Trust your instincts when making important decisions, as they will guide you toward success.",
    "category": "general",
    "language": "en",
    "language_name": "English"
  },
  "message": "Fortune retrieved successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <div class="response-box">
                    <h4>Thai Fortune Example</h4>
                    <div class="code-block">{
  "success": true,
  "data": {
    "id": 15,
    "fortune": "ความรักที่แท้จริงกำลังจะมาถึง อดทนรอคอยและเปิดใจให้กับคนใหม่ที่เข้ามาในชีวิต",
    "category": "love",
    "language": "th",
    "language_name": "Thai"
  },
  "message": "Fortune retrieved successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <div class="response-box">
                    <h4>Chinese Fortune Example</h4>
                    <div class="code-block">{
  "success": true,
  "data": {
    "id": 23,
    "fortune": "事业运势渐入佳境，与同事合作将带来意想不到的成果，把握机会展现自己的才华。",
    "category": "career",
    "language": "zh",
    "language_name": "Chinese"
  },
  "message": "Fortune retrieved successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Invalid language code. Supported languages: th, zh, en",
  "code": "INVALID_LANGUAGE",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>
            </div>

            <!-- Fortune Categories -->
            <div class="section">
                <h2>🎯 Fortune Categories</h2>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Example Topics</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>love</code></td>
                            <td>Love and relationships</td>
                            <td>Romance, marriage, soulmates, heartbreak recovery</td>
                        </tr>
                        <tr>
                            <td><code>career</code></td>
                            <td>Career and professional life</td>
                            <td>Job opportunities, promotions, business ventures</td>
                        </tr>
                        <tr>
                            <td><code>health</code></td>
                            <td>Health and wellness</td>
                            <td>Physical health, mental wellness, lifestyle changes</td>
                        </tr>
                        <tr>
                            <td><code>finance</code></td>
                            <td>Money and financial matters</td>
                            <td>Investments, savings, financial planning, prosperity</td>
                        </tr>
                        <tr>
                            <td><code>general</code></td>
                            <td>General life guidance</td>
                            <td>Overall luck, life decisions, personal growth</td>
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
                            <td><code>INVALID_LANGUAGE</code></td>
                            <td>Language code is not supported</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_ID</code></td>
                            <td>Fortune ID is outside the valid range (1-52)</td>
                        </tr>
                        <tr>
                            <td><code>FORTUNE_NOT_FOUND</code></td>
                            <td>Specified fortune ID does not exist</td>
                        </tr>
                        <tr>
                            <td><code>FILE_ERROR</code></td>
                            <td>Error reading fortune data files</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>JavaScript/AJAX</h3>
                <div class="code-block">fetch('<?php echo htmlspecialchars($baseUrl); ?>?lang=en')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Fortune:', data.data.fortune);
      console.log('Category:', data.data.category);
    }
  });</div>

                <h3>PHP</h3>
                <div class="code-block">$response = file_get_contents('<?php echo htmlspecialchars($baseUrl); ?>?lang=th');
$data = json_decode($response, true);

if ($data['success']) {
    echo "Fortune: " . $data['data']['fortune'];
    echo "Category: " . $data['data']['category'];
}</div>

                <h3>Python</h3>
                <div class="code-block">import requests

response = requests.get('<?php echo htmlspecialchars($baseUrl); ?>?lang=zh')
data = response.json()

if data['success']:
    print(f"Fortune: {data['data']['fortune']}")
    print(f"Category: {data['data']['category']}")</div>
            </div>

            <!-- Rate Limits -->
            <div class="section">
                <h2>🚦 Rate Limits</h2>
                <p>Currently, there are no rate limits imposed on this API. However, please use it responsibly and avoid excessive requests that might impact service availability for other users.</p>
            </div>

            <!-- Cultural Notes -->
            <div class="section">
                <h2>🏛️ Cultural Considerations</h2>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>Respectful Content:</strong> All fortunes are designed to be positive and respectful across cultures</li>
                    <li><strong>Cultural Sensitivity:</strong> Translations maintain cultural context appropriate for each language</li>
                    <li><strong>Entertainment Purpose:</strong> This API is designed for entertainment and should not be used for serious life decisions</li>
                    <li><strong>Diverse Perspectives:</strong> Fortunes cover universal human experiences across different cultures</li>
                </ul>
            </div>

            <!-- Try It Out -->
            <div class="try-it">
                <h3>🎯 Ready to Try?</h3>
                <p>Test the Fortune Teller API with our interactive web interface or start integrating it into your application.</p>
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/fortune-teller/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
