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
                <h2>🌍 Languages Returned in Every Response</h2>
                <p>Every fortune is returned as an object with three pre-translated fields. There is no single-language query parameter — clients pick the language on the client side.</p>
                <div class="lang-grid">
                    <div class="lang-item">
                        <h5>🇹🇭 Thai</h5>
                        <p><code>fortune.thai</code></p>
                    </div>
                    <div class="lang-item">
                        <h5>🇨🇳 Chinese</h5>
                        <p><code>fortune.chinese</code></p>
                    </div>
                    <div class="lang-item">
                        <h5>🇺🇸 English</h5>
                        <p><code>fortune.english</code></p>
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

                <!-- Get Random Fortune -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span>
                        <span class="url">/</span>
                        Get Random Fortune
                    </h3>
                    <p>Retrieve a random fortune. Without any query parameter, the API uses <code>random_int()</code> over the number of available prediction files and returns the chosen one with all three language fields.</p>

                    <h4>Parameters</h4>
                    <p>This endpoint takes no parameters.</p>

                    <h4>Example Request</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>"</div>
                </div>

                <!-- Get Fortune by ID -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span>
                        <span class="url">/?id=N</span>
                        Get Specific Fortune by ID
                    </h3>
                    <p>Retrieve a specific fortune by its integer ID. IDs are 1-based and correspond to filenames under <code>predictions/&lt;id&gt;.json</code>.</p>

                    <h4>Parameters</h4>
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
                                <td><code>id</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>random</td>
                                <td>Specific fortune ID. IDs that do not match an existing prediction file return HTTP 404.</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?id=7"</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response (Random)</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "fortune": {
    "id": 45,
    "thai": "...",
    "chinese": "...",
    "english": "..."
  },
  "timestamp": "2025-09-09T12:00:00Z",
  "total_fortunes": 52
}</div>
                </div>

                <p>The <code>total_fortunes</code> value is discovered at runtime by counting <code>*.json</code> files in the <code>predictions/</code> directory — so it grows automatically as you add new fortunes.</p>

                <h3>Error Response — ID Not Found</h3>
                <div class="error-box">
                    <p>When <code>?id=N</code> points at a missing prediction file, the API returns HTTP 404:</p>
                    <div class="code-block">{
  "success": false,
  "error": "Fortune file not found",
  "requested_id": 999
}</div>
                </div>
            </div>

            <!-- Fortune Data Shape -->
            <div class="section">
                <h2>📄 Fortune Object</h2>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>id</code></td>
                            <td>integer</td>
                            <td>1-based identifier matching the prediction filename</td>
                        </tr>
                        <tr>
                            <td><code>thai</code></td>
                            <td>string</td>
                            <td>Thai prediction text</td>
                        </tr>
                        <tr>
                            <td><code>chinese</code></td>
                            <td>string</td>
                            <td>Simplified Chinese prediction text</td>
                        </tr>
                        <tr>
                            <td><code>english</code></td>
                            <td>string</td>
                            <td>English prediction text</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Error Codes -->
            <div class="section">
                <h2>⚠️ Error Responses</h2>
                <p>The API has only one error condition: requesting an <code>id</code> that does not exist as a prediction file.</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>HTTP Status</th>
                            <th><code>error</code> message</th>
                            <th>Trigger</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>404</td>
                            <td><code>Fortune file not found</code></td>
                            <td><code>?id=N</code> for an N that has no matching <code>predictions/N.json</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>JavaScript/AJAX</h3>
                <div class="code-block">fetch('<?php echo htmlspecialchars($baseUrl); ?>')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('English:', data.fortune.english);
      console.log('Thai:',    data.fortune.thai);
      console.log('Chinese:', data.fortune.chinese);
    }
  });</div>

                <h3>PHP</h3>
                <div class="code-block">$response = file_get_contents('<?php echo htmlspecialchars($baseUrl); ?>');
$data = json_decode($response, true);

if ($data['success']) {
    echo "EN: " . $data['fortune']['english'];
    echo "TH: " . $data['fortune']['thai'];
    echo "ZH: " . $data['fortune']['chinese'];
}</div>

                <h3>Python</h3>
                <div class="code-block">import requests

response = requests.get('<?php echo htmlspecialchars($baseUrl); ?>')
data = response.json()

if data['success']:
    print("EN:", data['fortune']['english'])
    print("TH:", data['fortune']['thai'])
    print("ZH:", data['fortune']['chinese'])</div>
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
