<?php
/**
 * Username Generator API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 *
 * Note: the previous version of this file had an orphaned `<td>...</td>`
 * pair (no `<tr>` opener) immediately after the `custom_words` row, which
 * duplicated the `max_length` description.  The row was removed.
 */

$spec = [
    'slug'    => 'username-generator',
    'title'   => '👤 Username Generator API',
    'tagline' => 'Create unique usernames using themed word combinations',
    'crumb'   => 'Username Generator',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Username Generator API creates unique, memorable usernames for users, applications, and services. Generate usernames using themed word combinations with support for 20+ languages and various themes including fantasy, tech, nature, gaming, and more.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🌍 20+ Languages</h4>
                        <p>Support for multiple languages including English, Spanish, French, German, Italian, Japanese, Chinese, and more</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎨 17+ Themes</h4>
                        <p>Various themes including fantasy, tech, nature, gaming, space, mythology, and more</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔢 Flexible Patterns</h4>
                        <p>Configurable patterns with adjectives, nouns, numbers, and separators</p>
                    </div>
                    <div class="feature-card">
                        <h4>📏 Length Control</h4>
                        <p>Set minimum and maximum character length constraints</p>
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

                <!-- Generate Username Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate Usernames
                    </h3>
                    <p>Generate unique usernames based on specified language, theme, and pattern preferences.</p>

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
                                <td><code>language</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>en</td>
                                <td>Language code for word selection (en, es, fr, de, it, ja, zh, etc.)</td>
                            </tr>
                            <tr>
                                <td><code>theme</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>general</td>
                                <td>Theme for word selection (general, fantasy, tech, nature, gaming, space, etc.)</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>10</td>
                                <td>Number of usernames to generate (1-100)</td>
                            </tr>
                            <tr>
                                <td><code>pattern</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>adj_noun</td>
                                <td>Username pattern: "adj_noun", "noun_noun", "adj_adj_noun", "noun_adj_num"</td>
                            </tr>
                            <tr>
                                <td><code>separator</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>_</td>
                                <td>Character to separate word components (_ or -)</td>
                            </tr>
                            <tr>
                                <td><code>include_numbers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Append random numbers to usernames</td>
                            </tr>
                            <tr>
                                <td><code>number_range</code></td>
                                <td>object</td>
                                <td><span class="optional">Optional</span></td>
                                <td>{"min": 1, "max": 999}</td>
                                <td>Range for random numbers (e.g., {"min": 10, "max": 99})</td>
                            </tr>
                            <tr>
                                <td><code>capitalize</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>none</td>
                                <td>Capitalization style: "none", "first", "all", "camel"</td>
                            </tr>
                            <tr>
                                <td><code>min_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>4</td>
                                <td>Minimum username length</td>
                            </tr>
                            <tr>
                                <td><code>max_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>20</td>
                                <td>Maximum username length</td>
                            </tr>
                            <tr>
                                <td><code>custom_words</code></td>
                                <td>array</td>
                                <td><span class="optional">Optional</span></td>
                                <td>[]</td>
                                <td>Custom words to incorporate into usernames</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Available Languages</h4>
                    <div class="lang-grid">
                        <div class="lang-item">🇺🇸 en - English</div>
                        <div class="lang-item">🇪🇸 es - Spanish</div>
                        <div class="lang-item">🇫🇷 fr - French</div>
                        <div class="lang-item">🇩🇪 de - German</div>
                        <div class="lang-item">🇮🇹 it - Italian</div>
                        <div class="lang-item">🇵🇹 pt - Portuguese</div>
                        <div class="lang-item">🇳🇱 nl - Dutch</div>
                        <div class="lang-item">🇷🇺 ru - Russian</div>
                        <div class="lang-item">🇯🇵 ja - Japanese</div>
                        <div class="lang-item">🇨🇳 zh - Chinese</div>
                        <div class="lang-item">🇰🇷 ko - Korean</div>
                        <div class="lang-item">🇸🇦 ar - Arabic</div>
                        <div class="lang-item">🇮🇳 hi - Hindi</div>
                        <div class="lang-item">🇹🇭 th - Thai</div>
                        <div class="lang-item">🇻🇳 vi - Vietnamese</div>
                        <div class="lang-item">🇮🇩 id - Indonesian</div>
                        <div class="lang-item">🇹🇷 tr - Turkish</div>
                        <div class="lang-item">🇵🇱 pl - Polish</div>
                        <div class="lang-item">🇸🇪 sv - Swedish</div>
                        <div class="lang-item">🇩🇰 da - Danish</div>
                    </div>

                    <h4>Available Themes</h4>
                    <div class="theme-list">
                        <div class="theme-item"><strong>general</strong> - Universal words for any context</div>
                        <div class="theme-item"><strong>fantasy</strong> - Magical creatures, spells, mythical elements</div>
                        <div class="theme-item"><strong>tech</strong> - Programming, computers, digital concepts</div>
                        <div class="theme-item"><strong>nature</strong> - Animals, plants, natural elements</div>
                        <div class="theme-item"><strong>gaming</strong> - Games, weapons, power-ups, achievements</div>
                        <div class="theme-item"><strong>space</strong> - Planets, stars, cosmic phenomena</div>
                        <div class="theme-item"><strong>mythology</strong> - Gods, heroes, legendary figures</div>
                        <div class="theme-item"><strong>food</strong> - Culinary terms, dishes, ingredients</div>
                        <div class="theme-item"><strong>colors</strong> - Color names and shades</div>
                        <div class="theme-item"><strong>music</strong> - Instruments, genres, musical terms</div>
                        <div class="theme-item"><strong>sports</strong> - Sports, equipment, athletes</div>
                        <div class="theme-item"><strong>abstract</strong> - Concepts, emotions, ideas</div>
                        <div class="theme-item"><strong>pirate</strong> - Nautical, pirate-themed terms</div>
                        <div class="theme-item"><strong>sci-fi</strong> - Futuristic, scientific terms</div>
                        <div class="theme-item"><strong>medieval</strong> - Medieval, knight, castle terms</div>
                        <div class="theme-item"><strong>cyberpunk</strong> - Cyberpunk, neon, futuristic terms</div>
                        <div class="theme-item"><strong>professional</strong> - Business, formal terms</div>
                    </div>

                    <h4>Pattern Types</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Pattern</th>
                                <th>Structure</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>adj_noun</code></td>
                                <td>adjective + noun</td>
                                <td>brave_wolf, swift_fox</td>
                            </tr>
                            <tr>
                                <td><code>noun_noun</code></td>
                                <td>noun + noun</td>
                                <td>dragon_phoenix, castle_knight</td>
                            </tr>
                            <tr>
                                <td><code>adj_adj_noun</code></td>
                                <td>adjective + adjective + noun</td>
                                <td>dark_stormy_night</td>
                            </tr>
                            <tr>
                                <td><code>noun_adj_num</code></td>
                                <td>noun + adjective + number</td>
                                <td>wizard_epic_42</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Capitalization Styles</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Style</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>none</code></td>
                                <td>brave_wolf_42</td>
                            </tr>
                            <tr>
                                <td><code>first</code></td>
                                <td>Brave_wolf_42</td>
                            </tr>
                            <tr>
                                <td><code>all</code></td>
                                <td>BRAVE_WOLF_42</td>
                            </tr>
                            <tr>
                                <td><code>camel</code></td>
                                <td>braveWolf42</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Basic Generation</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "language": "en",
    "theme": "fantasy",
    "count": 10,
    "pattern": "adj_noun"
  }'</div>

                    <h4>Example Request - Custom Pattern with Numbers</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "language": "en",
    "theme": "tech",
    "count": 15,
    "pattern": "adj_adj_noun",
    "separator": "-",
    "include_numbers": true,
    "number_range": {"min": 10, "max": 99}
  }'</div>

                    <h4>Example Request - Camel Case with Constraints</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "language": "en",
    "theme": "nature",
    "count": 5,
    "pattern": "noun_adj_num",
    "capitalize": "camel",
    "min_length": 8,
    "max_length": 20
  }'</div>

                    <h4>Example Request - Spanish with Custom Words</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "language": "es",
    "theme": "general",
    "count": 8,
    "pattern": "adj_noun",
    "custom_words": ["dragon", "fenix", "sol"]
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "usernames": [
      "brave_wolf",
      "swift_fox",
      "dark_dragon",
      "epic_phoenix",
      "mystic_unicorn",
      "fierce_tiger",
      "noble_eagle",
      "silent_shadow",
      "golden_lion",
      "crystal_storm"
    ],
    "count": 10,
    "settings": {
      "language": "en",
      "theme": "fantasy",
      "pattern": "adj_noun",
      "separator": "_",
      "include_numbers": false,
      "capitalize": "none"
    }
  },
  "message": "Usernames generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Unsupported language code",
  "code": "UNSUPPORTED_LANGUAGE",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>
            </div>

            <!-- Use Cases -->
            <div class="section">
                <h2>💡 Use Cases</h2>

                <div class="categories-grid">
                    <div class="category-item">
                        <h4>🌐 Social Media</h4>
                        <p>Generate unique usernames for social media platforms, forums, and online communities.</p>
                    </div>
                    <div class="category-item">
                        <h4>🎮 Gaming Platforms</h4>
                        <p>Create gaming handles and character names for multiplayer games and platforms.</p>
                    </div>
                    <div class="category-item">
                        <h4>📧 Email Accounts</h4>
                        <p>Generate professional or creative email addresses for new user registrations.</p>
                    </div>
                    <div class="category-item">
                        <h4>💼 Professional Profiles</h4>
                        <p>Create professional usernames for business platforms and networking sites.</p>
                    </div>
                    <div class="category-item">
                        <h4>🏢 Application Users</h4>
                        <p>Provide username suggestions during user registration flows in web and mobile apps.</p>
                    </div>
                    <div class="category-item">
                        <h4>🎨 Creative Projects</h4>
                        <p>Generate character names for stories, screenplays, or creative writing projects.</p>
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
                            <td><code>UNSUPPORTED_LANGUAGE</code></td>
                            <td>Specified language code is not supported</td>
                        </tr>
                        <tr>
                            <td><code>UNSUPPORTED_THEME</code></td>
                            <td>Specified theme is not available for the language</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_PATTERN</code></td>
                            <td>Pattern type is invalid</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_RANGE</code></td>
                            <td>Length range or number range is invalid</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_COUNT</code></td>
                            <td>Count is outside valid range (1-100)</td>
                        </tr>
                        <tr>
                            <td><code>GENERATION_ERROR</code></td>
                            <td>Error occurred during username generation</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>Registration Form Integration</h3>
                <div class="code-block">// Generate username suggestions for new user registration
const response = await fetch('<?php echo htmlspecialchars($baseUrl); ?>', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    language: 'en',
    theme: 'general',
    count: 5,
    pattern: 'adj_noun',
    include_numbers: true
  })
});

const data = await response.json();
data.data.usernames.forEach(username => {
  console.log('Suggestion:', username);
});</div>

                <h3>Game Character Name Generator</h3>
                <div class="code-block">// Generate fantasy character names for RPG
fetch('<?php echo htmlspecialchars($baseUrl); ?>', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    theme: 'fantasy',
    count: 20,
    pattern: 'adj_noun',
    capitalize: 'first'
  })
});</div>
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