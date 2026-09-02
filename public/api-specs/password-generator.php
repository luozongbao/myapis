<?php
/**
 * Password Generator API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 */

$spec = [
    'slug'    => 'password-generator',
    'title'   => '🔐 Password Generator API',
    'tagline' => 'Generate cryptographically secure passwords with customizable complexity',
    'crumb'   => 'Password Generator',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

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

                <!-- Generate Passwords Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span> / <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate Passwords
                    </h3>
                    <p>Generate one or more passwords using the supplied options. Parameters may be supplied via the query string (GET) or as a JSON body (POST). When <code>action=analyze</code> is supplied, the same endpoint analyses a single password instead.</p>

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
                                <td><code>min_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>8</td>
                                <td>Minimum length (1–128)</td>
                            </tr>
                            <tr>
                                <td><code>max_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>16</td>
                                <td>Maximum length (1–128). Must be &gt;= <code>min_length</code></td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>5</td>
                                <td>Number of passwords to generate (1–100)</td>
                            </tr>
                            <tr>
                                <td><code>include_lowercase</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include lowercase letters (a-z)</td>
                            </tr>
                            <tr>
                                <td><code>include_uppercase</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include uppercase letters (A-Z)</td>
                            </tr>
                            <tr>
                                <td><code>include_numbers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include numbers (0-9)</td>
                            </tr>
                            <tr>
                                <td><code>include_symbols</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Include special characters (default set: <code>!@#$%^&amp;*()_+-=[]{}|;:,.&lt;&gt;?</code>)</td>
                            </tr>
                            <tr>
                                <td><code>exclude_ambiguous</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Exclude ambiguous characters (<code>0</code>, <code>O</code>, <code>1</code>, <code>l</code>, <code>I</code>, <code>|</code>, <code>`</code>) from the pool</td>
                            </tr>
                            <tr>
                                <td><code>no_repeated_chars</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Prevent repeated characters in a single password</td>
                            </tr>
                            <tr>
                                <td><code>must_include_each_type</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Ensure at least one character from each enabled type appears</td>
                            </tr>
                            <tr>
                                <td><code>custom_symbols</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>""</td>
                                <td>Override the default symbol pool when <code>include_symbols</code> is on</td>
                            </tr>
                            <tr>
                                <td><code>action</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>—</td>
                                <td>Set to <code>analyze</code> to score a single password instead of generating</td>
                            </tr>
                            <tr>
                                <td><code>password</code></td>
                                <td>string</td>
                                <td><span class="required">Required (analyze only)</span></td>
                                <td>—</td>
                                <td>Password to analyse when <code>action=analyze</code></td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request — Basic Passwords (GET)</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?min_length=12&max_length=16&count=1&include_symbols=true"</div>

                    <h4>Example Request — High Security (POST JSON)</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "min_length": 24,
    "max_length": 24,
    "count": 1,
    "include_symbols": true,
    "exclude_ambiguous": true,
    "must_include_each_type": true
  }'</div>

                    <h4>Example Request — Analyse a password</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?action=analyze&password=Kx7mN9pQw2Yv8zR3"</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Generate Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "passwords": [
      {
        "password": "chocuqpj5LBD",
        "length": 12,
        "strength": "strong",
        "score": 5
      }
    ],
    "count": 1,
    "options_used": {
      "min_length": 10,
      "max_length": 12,
      "count": 1,
      "include_lowercase": true,
      "include_uppercase": true,
      "include_numbers": true,
      "include_symbols": false,
      "exclude_ambiguous": false,
      "no_repeated_chars": false,
      "must_include_each_type": true,
      "custom_symbols": ""
    }
  },
  "generation_info": {
    "length_range":     "10-12 characters",
    "character_types": {
      "lowercase": "included",
      "uppercase": "included",
      "numbers":   "included",
      "symbols":   "excluded"
    },
    "security_options": {
      "exclude_ambiguous":      "disabled",
      "no_repeated_chars":      "disabled",
      "must_include_each_type": "enabled"
    }
  },
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Analyze Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "analysis": {
    "length":        16,
    "has_lowercase": true,
    "has_uppercase": true,
    "has_numbers":   true,
    "has_symbols":   false,
    "strength":      "strong",
    "score":         5
  },
  "tips": [
    "Great! Your password meets all security recommendations"
  ],
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Error Responses</h3>
                <p>Errors come back as JSON with HTTP 400 and a free-form <code>error</code> string. Validation failures additionally expose a <code>messages</code> array.</p>

                <div class="error-box">
                    <p><strong>Option validation failed</strong></p>
                    <div class="code-block">{
  "success": false,
  "error":    "Validation failed",
  "messages": [
    "Minimum length cannot be greater than maximum length",
    "Count must be between 1 and 100"
  ]
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Missing password for analyze</strong></p>
                    <div class="code-block">{
  "success": false,
  "error": "Password is required for analysis"
}</div>
                </div>

                <div class="error-box">
                    <p><strong>Charset collapsed to nothing</strong></p>
                    <div class="code-block">{
  "success": false,
  "error":   "No character types selected"
}</div>
                </div>
            </div>

            <!-- Strength scoring -->
            <div class="section">
                <h2>💪 Strength Scoring</h2>
                <p>Every generated password is re-scored through the same <code>analyzePassword()</code> algorithm used by the analyse action. The score is the sum of these rules:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Rule</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>length &gt;= 8</td><td>+1</td></tr>
                        <tr><td>length &gt;= 12</td><td>+1</td></tr>
                        <tr><td>contains lowercase</td><td>+1</td></tr>
                        <tr><td>contains uppercase</td><td>+1</td></tr>
                        <tr><td>contains numbers</td><td>+1</td></tr>
                        <tr><td>contains symbols</td><td>+2</td></tr>
                    </tbody>
                </table>
                <p>The numeric score (0–7) is then mapped to a lowercase <code>strength</code> label:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Score</th>
                            <th>Strength label</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>0–2</td><td><code>weak</code></td></tr>
                        <tr><td>3–4</td><td><code>medium</code></td></tr>
                        <tr><td>5–6</td><td><code>strong</code></td></tr>
                        <tr><td>7</td><td><code>very strong</code></td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Error conditions -->
            <div class="section">
                <h2>⚠️ Error Conditions</h2>
                <p>The API returns human-readable <code>error</code> strings rather than numeric codes. Common triggers:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Trigger</th>
                            <th>HTTP</th>
                            <th><code>error</code> text</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>min_length</code> &lt; 1</td><td>400</td><td><em>Minimum length must be at least 1 character</em></td></tr>
                        <tr><td><code>max_length</code> &gt; 128</td><td>400</td><td><em>Maximum length cannot exceed 128 characters</em></td></tr>
                        <tr><td><code>min_length</code> &gt; <code>max_length</code></td><td>400</td><td><em>Minimum length cannot be greater than maximum length</em></td></tr>
                        <tr><td><code>count</code> outside 1–100</td><td>400</td><td><em>Count must be between 1 and 100</em></td></tr>
                        <tr><td>All four character types disabled</td><td>400</td><td><em>At least one character type must be selected</em></td></tr>
                        <tr><td><code>action=analyze</code> without <code>password</code></td><td>400</td><td><em>Password is required for analysis</em></td></tr>
                        <tr><td>No password produced (e.g. impossible combo)</td><td>400</td><td><em>No passwords could be generated</em></td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Security Notes -->
            <div class="section">
                <h2>🛡️ Security Features</h2>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8;">
                    <li><strong>Secure randomness:</strong> Length and characters are picked with <code>random_int()</code></li>
                    <li><strong>No logging:</strong> Generated passwords are not persisted anywhere on the server</li>
                    <li><strong>Character variety:</strong> By default <code>must_include_each_type</code> guarantees a mix of types</li>
                    <li><strong>Ambiguous-character exclusion:</strong> Optional filter for visually similar glyphs</li>
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
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/password-generator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
