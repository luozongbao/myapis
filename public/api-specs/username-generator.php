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
                <p>The Username Generator API produces memorable, themed usernames by pairing adjectives and nouns from one or more built-in word lists. Pick from nine curated themes (Fantasy, Professional, Nature, …), filter by length, optionally append numbers or symbols, and decide whether to deduplicate results. The actual word pool is English-only — there is no <code>language</code> parameter.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🎨 9 Themes</h4>
                        <p>Fantasy, Professional, Science and Space, Computer Technology, Elements and Chemistry, Things, Body and Health, Nature, Space and Time — combine as many as you want in one request.</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔀 Flexible Mixing</h4>
                        <p>One <code>theme</code>, an array <code>themes[]</code>, or a comma-separated <code>themes</code> string — they all work and can be combined.</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔢 Optional Numbers &amp; Symbols</h4>
                        <p>Toggle random numbers and symbols independently; control the output length range.</p>
                    </div>
                    <div class="feature-card">
                        <h4>📜 Catalogue Endpoint</h4>
                        <p>Hit <code>?action=themes</code> to get the full theme list and descriptions without generating anything.</p>
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
                        <span class="method get">GET</span> / <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate Usernames
                    </h3>
                    <p>Generate themed usernames. Parameters can be supplied via the query string or as a JSON POST body (same names in both). The default action generates usernames; pass <code>?action=themes</code> to fetch the theme catalogue instead.</p>

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
                                <td><code>action</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>generate</code></td>
                                <td>Set to <code>themes</code> to return the catalogue of themes + descriptions instead of generating usernames. Any unknown value is treated as <code>generate</code>.</td>
                            </tr>
                            <tr>
                                <td><code>theme</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>Fantasy</code></td>
                                <td>A single theme name. Mutually compatible with <code>themes[]</code> and the comma form.</td>
                            </tr>
                            <tr>
                                <td><code>themes[]</code></td>
                                <td>string[]</td>
                                <td><span class="optional">Optional</span></td>
                                <td>—</td>
                                <td>Repeated query parameter (e.g. <code>themes[]=Fantasy&amp;themes[]=Nature</code>) or an array in JSON body. Combine as many as you want.</td>
                            </tr>
                            <tr>
                                <td><code>themes</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>—</td>
                                <td>Comma-separated alternative to <code>themes[]</code>, e.g. <code>themes=Fantasy,Nature</code>.</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>10</td>
                                <td>How many usernames to produce (1–50).</td>
                            </tr>
                            <tr>
                                <td><code>min_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>6</td>
                                <td>Lower length bound in characters (3–30).</td>
                            </tr>
                            <tr>
                                <td><code>max_length</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>20</td>
                                <td>Upper length bound in characters (4–50). Must be ≥ <code>min_length</code>.</td>
                            </tr>
                            <tr>
                                <td><code>include_numbers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Append a random number suffix to each username.</td>
                            </tr>
                            <tr>
                                <td><code>include_symbols</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Append a random symbol suffix to each username.</td>
                            </tr>
                            <tr>
                                <td><code>capitalize</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>When true, render each component with leading capitals (e.g. <code>CelestialLeviathan</code>). When false, output is lowercase (e.g. <code>celestial_leviathan</code>).</td>
                            </tr>
                            <tr>
                                <td><code>avoid_repetition</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>true</td>
                                <td>Drop duplicates within the same response. Set false to allow repeats.</td>
                            </tr>
                            <tr>
                                <td><code>use_all_adjectives</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Pool adjectives from every theme instead of only the ones you selected.</td>
                            </tr>
                            <tr>
                                <td><code>use_general_adjectives</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Mix in the always-available general-purpose adjective list alongside the chosen themes.</td>
                            </tr>
                            <tr>
                                <td><code>custom_words</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td><code>""</code></td>
                                <td>A comma-separated string of extra words to mix into the pool (the endpoint expects a flat string, not an array).</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Available Themes</h4>
                    <p>Theme names are case-sensitive and written with title-case + spaces, exactly as below. The simplest way to confirm is to call <code>?action=themes</code> at runtime.</p>
                    <div class="theme-list">
                        <div class="theme-item"><strong>Fantasy</strong> — Epic and mythical usernames for gaming and fantasy lovers</div>
                        <div class="theme-item"><strong>Professional</strong> — Suitable for business, LinkedIn, and professional networks</div>
                        <div class="theme-item"><strong>Science and Space</strong> — Science and space exploration themed usernames</div>
                        <div class="theme-item"><strong>Computer Technology</strong> — Tech and programming themed usernames</div>
                        <div class="theme-item"><strong>Elements and Chemistry</strong> — Science-inspired usernames with elements and compounds</div>
                        <div class="theme-item"><strong>Things</strong> — Everyday objects and items themed usernames</div>
                        <div class="theme-item"><strong>Body and Health</strong> — Body parts and health-themed usernames</div>
                        <div class="theme-item"><strong>Nature</strong> — Nature-inspired usernames with plants, animals, and landscapes</div>
                        <div class="theme-item"><strong>Space and Time</strong> — Usernames inspired by concepts of space and time</div>
                    </div>

                    <h4>Example Request — Default (10 fantasy usernames)</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?count=10"</div>

                    <h4>Example Request — Multi-theme with numbers and symbols</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?themes[]=Fantasy&themes[]=Nature&count=5&include_numbers=true&include_symbols=true&max_length=18"</div>

                    <h4>Example Request — POST JSON</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "themes": ["Computer Technology", "Professional"],
    "count":  8,
    "min_length": 8,
    "max_length": 24,
    "include_numbers": true,
    "use_general_adjectives": true
  }'</div>

                    <h4>Example Request — List themes</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?action=themes"</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Generation success</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "usernames": [
      "CelestialLeviathan",
      "ElectricThunder",
      "StormyDragon"
    ],
    "count": 3,
    "options_used": {
      "themes": ["Fantasy"],
      "min_length": 6,
      "max_length": 20,
      "count": 3,
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
    "themes": ["Fantasy"],
    "theme_count": 1,
    "length_range": "6-20 characters",
    "features": {
      "numbers": "excluded",
      "symbols": "excluded",
      "capitalization": "enabled"
    }
  },
  "timestamp": "2026-09-02 15:41:07"
}</div>
                </div>

                <h3><code>action=themes</code> success</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "themes": [
    "Fantasy", "Professional", "Science and Space", "Computer Technology",
    "Elements and Chemistry", "Things", "Body and Health", "Nature", "Space and Time"
  ],
  "theme_descriptions": {
    "Fantasy":              "Epic and mythical usernames for gaming and fantasy lovers",
    "Professional":         "Suitable for business, LinkedIn, and professional networks",
    "Science and Space":    "Science and space exploration themed usernames",
    "Computer Technology":  "Tech and programming themed usernames",
    "Elements and Chemistry": "Science-inspired usernames with elements and compounds",
    "Things":               "Everyday objects and items themed usernames",
    "Body and Health":      "Body parts and health-themed usernames",
    "Nature":               "Nature-inspired usernames with plants, animals, and landscapes",
    "Space and Time":       "Usernames inspired by concepts of space and time"
  }
}</div>
                </div>

                <h3>Validation error</h3>
                <p>All 400-class responses share this shape: <code>{ success:false, error:"Validation failed", messages:[...] }</code>. HTTP 400 is returned.</p>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error":   "Validation failed",
  "messages": ["Count must be between 1 and 50"]
}</div>
                </div>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error":   "Validation failed",
  "messages": ["Invalid themes: banana"]
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
                <p>The API does not return a numeric <code>code</code> field. All validation errors come back with HTTP 400 and the shape <code>{ success:false, error:"Validation failed", messages:[...] }</code>. Typical messages include:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Sample message</th>
                            <th>Cause</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>"Count must be between 1 and 50"</code></td>
                            <td><code>count</code> is outside the allowed range</td>
                        </tr>
                        <tr>
                            <td><code>"Invalid themes: &lt;names&gt;"</code></td>
                            <td>One or more supplied theme names do not exist (typo, wrong case, unsupported)</td>
                        </tr>
                        <tr>
                            <td><code>"min_length must be ..."</code></td>
                            <td><code>min_length</code>/<code>max_length</code> values are out of bounds or <code>max_length</code> &lt; <code>min_length</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Integration Examples -->
            <div class="section">
                <h2>🔗 Integration Examples</h2>

                <h3>Registration form</h3>
                <div class="code-block">// Suggest 5 professional usernames with a numeric fallback.
fetch('/api/username-generator/?themes[]=Professional&count=5&include_numbers=true')
  .then(r =&gt; r.json())
  .then(data =&gt; {
    if (!data.success) {
      data.messages.forEach(m =&gt; console.warn(m));
      return;
    }
    data.data.usernames.forEach(name =&gt; {
      console.log('Suggestion:', name);
    });
  });</div>

                <h3>Themed character names</h3>
                <div class="code-block">// Generate fantasy character names for an RPG.
fetch('/api/username-generator/', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    themes: ['Fantasy', 'Space and Time'],
    count:  20,
    capitalize: true,
    max_length: 22
  })
})
  .then(r =&gt; r.json())
  .then(data =&gt; data.data.usernames);</div>

                <h3>Fetch the theme catalogue</h3>
                <div class="code-block">fetch('/api/username-generator/?action=themes')
  .then(r =&gt; r.json())
  .then(data =&gt; {
    console.log(data.themes);              // ["Fantasy", "Professional", ...]
    console.log(data.theme_descriptions);  // { Fantasy: "...", Professional: "...", ... }
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