<?php
/**
 * Random Generator API — documentation page.
 *
 * Shared layout (HTML head, styles, header banner, breadcrumb, container
 * wrapper) lives in public/includes/apispec_layout.php so this file only
 * contains the tool-specific content sections.
 *
 * Note: the previous version of this file had two duplicate `<tr>` rows
 * for the `count` parameter.  The single combined row below documents the
 * full range and defaults.
 */

$spec = [
    'slug'    => 'randomizer',
    'title'   => '🎲 Random Generator API',
    'tagline' => 'Generate random numbers, dice rolls, coin flips, and card draws',
    'crumb'   => 'Random Generator',
];
require __DIR__ . '/../includes/apispec_layout.php';
?>

            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Random Generator API provides cryptographically secure random number generation, dice rolls, coin flips, card draws, and weighted selection. Perfect for games, simulations, statistical sampling, and decision-making applications.</p>

                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🎲 Multiple Generator Types</h4>
                        <p>Numbers, dice, coins, cards, lists, and weighted selection</p>
                    </div>
                    <div class="feature-card">
                        <h4>🔒 Cryptographically Secure</h4>
                        <p>Uses secure random number generation for true randomness</p>
                    </div>
                    <div class="feature-card">
                        <h4>📊 Multiple Results</h4>
                        <p>Generate up to 100 results in a single request</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎯 Customizable Ranges</h4>
                        <p>Full control over min/max values and constraints</p>
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

                <!-- Unified Random Generator Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method get">GET</span> / <span class="method post">POST</span>
                        <span class="url">/?type=...</span>
                        Random Generator
                    </h3>
                    <p>All five randomizer types are served from the same URL. Pick a value for <code>type</code> and supply any extra parameters as either query-string (GET) or JSON-body (POST) fields. Both work the same way.</p>

                    <h4>Type Values</h4>
                    <table class="parameter-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>What it does</th>
                                <th>Extra params</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>number</code></td>
                                <td>One integer in <code>[min, max]</code></td>
                                <td><code>min</code> (1), <code>max</code> (100)</td>
                            </tr>
                            <tr>
                                <td><code>dice</code></td>
                                <td>Roll <code>count</code> dice with <code>sides</code> faces</td>
                                <td><code>sides</code> (6, 2–100), <code>count</code> (1, 1–10)</td>
                            </tr>
                            <tr>
                                <td><code>coin</code></td>
                                <td>Flip <code>count</code> coins</td>
                                <td><code>count</code> (1, 1–10)</td>
                            </tr>
                            <tr>
                                <td><code>card</code></td>
                                <td>Draw <code>count</code> random cards</td>
                                <td><code>count</code> (1, 1–52 or 1–54 with jokers), <code>with_jokers</code> (false)</td>
                            </tr>
                            <tr>
                                <td><code>all</code></td>
                                <td>Bundle of one number, one dice roll, one coin flip and one card draw</td>
                                <td>—</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request — Number</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=number&min=1&max=10"</div>

                    <h4>Example Request — Dice</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=dice&sides=6&count=2"</div>

                    <h4>Example Request — Coin</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=coin&count=3"</div>

                    <h4>Example Request — Card</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=card&count=2&with_jokers=true"</div>

                    <h4>Example Request — All</h4>
                    <div class="code-block">curl "<?php echo htmlspecialchars($baseUrl); ?>?type=all"</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Number Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "type":      "number",
  "result":    10,
  "range":     { "min": 1, "max": 10 },
  "timestamp": "2025-09-09T12:00:00Z",
  "success":   true,
  "api_info": {
    "version":         "1.0",
    "endpoint":        "/randomizer/api/",
    "supported_types": ["number", "dice", "coin", "card", "all"]
  }
}</div>
                </div>

                <h3>Dice Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "type":        "dice",
  "result":      [5, 5],
  "total":       10,
  "dice_config": { "sides": 6, "count": 2 },
  "timestamp":   "2025-09-09T12:00:00Z",
  "success":     true,
  "api_info":    { "...": "..." }
}</div>
                    <p>When <code>count=1</code> the <code>result</code> is a single integer instead of an array.</p>
                </div>

                <h3>Coin Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "type":       "coin",
  "result":     ["Tails", "Heads", "Heads"],
  "statistics": { "heads": 2, "tails": 1 },
  "count":      3,
  "timestamp":  "2025-09-09T12:00:00Z",
  "success":    true,
  "api_info":   { "...": "..." }
}</div>
                    <p>When <code>count=1</code> the <code>result</code> is a single string (<code>"Heads"</code> or <code>"Tails"</code>).</p>
                </div>

                <h3>Card Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "type":      "card",
  "result": [
    {
      "rank":    "8",
      "suit":    "Hearts",
      "symbol":  "♥",
      "display": "8 of Hearts",
      "short":   "8♥",
      "color":   "red"
    },
    {
      "rank":    "4",
      "suit":    "Hearts",
      "symbol":  "♥",
      "display": "4 of Hearts",
      "short":   "4♥",
      "color":   "red"
    }
  ],
  "deck_info": {
    "total_cards": 52,
    "with_jokers": false,
    "cards_drawn": 2
  },
  "timestamp": "2025-09-09T12:00:00Z",
  "success":   true,
  "api_info":  { "...": "..." }
}</div>
                    <p>When <code>count=1</code> the <code>result</code> is a single card object. With <code>with_jokers=true</code> the deck has 54 cards (52 + 2 jokers).</p>
                </div>

                <h3>All Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "type": "all",
  "results": {
    "number": { "type": "number", "result": 33, "...": "..." },
    "dice":   { "type": "dice",   "result": 4,   "...": "..." },
    "coin":   { "type": "coin",   "result": "...", "...": "..." },
    "card":   { "type": "card",   "result": { "...": "..." }, "...": "..." }
  },
  "timestamp": "2025-09-09T12:00:00Z",
  "success":   true,
  "api_info":  { "...": "..." }
}</div>
                </div>

                <h3>Error Response</h3>
                <p>The API returns HTTP 400 with a free-form <code>error</code> string and the same <code>api_info</code> block.</p>
                <div class="error-box">
                    <div class="code-block">{
  "success":   false,
  "error":     "Invalid type. Supported types: number, dice, coin, card, all",
  "timestamp": "2025-09-09T12:00:00Z",
  "api_info":  { "version": "1.0", "endpoint": "/randomizer/api/", "supported_types": ["number", "dice", "coin", "card", "all"] }
}</div>
                </div>
                <p>Other triggers and their messages:</p>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Trigger</th>
                            <th>HTTP</th>
                            <th><code>error</code> text</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>type</code> missing or unsupported</td>
                            <td>400</td>
                            <td><em>Invalid type. Supported types: number, dice, coin, card, all</em></td>
                        </tr>
                        <tr>
                            <td><code>min &gt; max</code></td>
                            <td>400</td>
                            <td><em>Minimum value cannot be greater than maximum value</em></td>
                        </tr>
                        <tr>
                            <td>dice <code>sides</code> outside 2–100</td>
                            <td>400</td>
                            <td><em>Dice sides must be between 2 and 100</em></td>
                        </tr>
                        <tr>
                            <td>dice <code>count</code> outside 1–10</td>
                            <td>400</td>
                            <td><em>Dice count must be between 1 and 10</em></td>
                        </tr>
                        <tr>
                            <td>coin <code>count</code> outside 1–10</td>
                            <td>400</td>
                            <td><em>Coin count must be between 1 and 10</em></td>
                        </tr>
                        <tr>
                            <td>card <code>count</code> outside 1–52 (or 1–54 with jokers)</td>
                            <td>400</td>
                            <td><em>Card count must be between 1 and 52</em> (or <em>1 and 54</em>)</td>
                        </tr>
                        <tr>
                            <td>Any other unexpected failure</td>
                            <td>500</td>
                            <td><em>Internal server error</em></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Generator Types -->
            <div class="section">
                <h2>🎯 Generator Types</h2>

                <h3>🔢 Number Generator</h3>
                <p>Single integer between <code>min</code> and <code>max</code> (inclusive). Defaults: min=1, max=100.</p>

                <h3>🎲 Dice Roller</h3>
                <p>Roll <code>count</code> dice (1–10) with <code>sides</code> faces (2–100). Defaults: sides=6, count=1.</p>

                <h3>🪙 Coin Flip</h3>
                <p>Flip <code>count</code> coins (1–10). Returns the per-flip result and a heads/tails tally. Default count=1.</p>

                <h3>🃏 Card Draw</h3>
                <p>Draw <code>count</code> cards from a 52-card deck, optionally extended to 54 with <code>with_jokers=true</code>. Each card carries <code>rank</code>, <code>suit</code>, <code>symbol</code>, <code>display</code>, <code>short</code> and <code>color</code>.</p>

                <h3>🎁 All-in-One</h3>
                <p>Returns one number (1–100), one dice roll (6-sided), one coin flip and one card draw in a single response.</p>
            </div>

            <!-- Use Cases -->
            <div class="section">
                <h2>💡 Use Cases</h2>

                <div class="categories-grid">
                    <div class="category-item">
                        <h4>🎮 Game Development</h4>
                        <p>Dice rolls, card draws, and random events for board games, RPGs, and video games.</p>
                    </div>
                    <div class="category-item">
                        <h4>📊 Statistical Sampling</h4>
                        <p>Random sampling for surveys, A/B testing, and statistical analysis with weighted options.</p>
                    </div>
                    <div class="category-item">
                        <h4>🎁 Giveaways &amp; Contests</h4>
                        <p>Fair random selection of winners from participant lists with cryptographic verification.</p>
                    </div>
                    <div class="category-item">
                        <h4>🔐 Security &amp; Tokens</h4>
                        <p>Generate secure random tokens, nonces, and identifiers for authentication systems.</p>
                    </div>
                    <div class="category-item">
                        <h4>🎲 Decision Making</h4>
                        <p>Coin flips and random selection for unbiased decision-making tools and applications.</p>
                    </div>
                    <div class="category-item">
                        <h4>🧪 Simulations</h4>
                        <p>Monte Carlo simulations, random testing data, and probabilistic modeling scenarios.</p>
                    </div>
                </div>
            </div>

            <!-- Error Conditions -->
            <div class="section">
                <h2>⚠️ Error Conditions</h2>
                <p>The API returns the same human-readable <code>error</code> string shown above — no numeric error codes. Triggers are summarised in the table inside the <em>Response Format → Error Response</em> section.</p>
            </div>

            <!-- Rate Limits -->
            <div class="section">
                <h2>🚦 Rate Limits</h2>
                <p>Currently, there are no rate limits imposed on this API. However, please use it responsibly and avoid excessive requests that might impact service availability for other users.</p>
            </div>

            <!-- Try It Out -->
            <div class="try-it">
                <h3>🎯 Ready to Try?</h3>
                <p>Test the Random Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/randomizer/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>