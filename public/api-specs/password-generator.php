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
                                <td><code>min_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>8</td>
                                <td>Minimum password length (1-128 characters)</td>
                            </tr>
                            <tr>
                                <td><code>max_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>16</td>
                                <td>Maximum password length (1-128 characters)</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>5</td>
                                <td>Number of passwords to generate (1-100)</td>
                            </tr>
                            <tr>
                                <td><code>include_uppercase</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include uppercase letters (A-Z)</td>
                            </tr>
                            <tr>
                                <td><code>include_lowercase</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Include lowercase letters (a-z)</td>
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
                                <td>Include special characters (!@#$%^&amp;*)</td>
                            </tr>
                            <tr>
                                <td><code>exclude_ambiguous</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Exclude ambiguous characters (0,O,l,1,i,I)</td>
                            </tr>
                            <tr>
                                <td><code>no_repeated_chars</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Prevent repeated characters in password</td>
                            </tr>
                            <tr>
                                <td><code>must_include_each_type</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Ensure at least one character from each selected type</td>
                            </tr>
                            <tr>
                                <td><code>custom_symbols</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>""</td>
                                <td>Custom symbol set to use instead of default</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Basic Password</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "min_length": 12,
    "max_length": 16,
    "include_uppercase": true,
    "include_lowercase": true,
    "include_numbers": true,
    "include_symbols": false
  }'</div>

                    <h4>Example Request - High Security Password</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "min_length": 24,
    "max_length": 24,
    "include_uppercase": true,
    "include_lowercase": true,
    "include_numbers": true,
    "include_symbols": true,
    "exclude_ambiguous": true,
    "must_include_each_type": true
  }'</div>

                    <h4>Example Request - Multiple Passwords</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "min_length": 12,
    "max_length": 12,
    "include_uppercase": true,
    "include_lowercase": true,
    "include_numbers": true,
    "include_symbols": true,
    "count": 5
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <h4>Single Password Response</h4>
                    <div class="code-block">{
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
}</div>
                </div>

                <div class="response-box">
                    <h4>Multiple Passwords Response</h4>
                    <div class="code-block">{
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
}</div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Invalid password length. Must be between 4 and 128 characters",
  "code": "INVALID_LENGTH",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
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
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/password-generator/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>
