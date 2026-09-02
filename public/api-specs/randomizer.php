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
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Random Generator
                    </h3>
                    <p>Generate random data based on the specified type. Supports numbers, dice, coins, cards, lists, and weighted selection.</p>

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
                                <td><code>type</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>-</td>
                                <td>Generator type: "number", "dice", "coin", "card", "list", "weighted"</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>1</td>
                                <td>Number of results to generate (1-100)</td>
                            </tr>
                            <tr>
                                <td><code>min</code></td>
                                <td>number</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>1</td>
                                <td>Minimum value (for type=number)</td>
                            </tr>
                            <tr>
                                <td><code>max</code></td>
                                <td>number</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>100</td>
                                <td>Maximum value (for type=number)</td>
                            </tr>
                            <tr>
                                <td><code>decimal</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>0</td>
                                <td>Decimal places (0-10, for type=number)</td>
                            </tr>
                            <tr>
                                <td><code>unique</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Ensure all results are unique (for type=number)</td>
                            </tr>
                            <tr>
                                <td><code>count_dice</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>1</td>
                                <td>Number of dice to roll (1-20, for type=dice)</td>
                            </tr>
                            <tr>
                                <td><code>dice_sides</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>6</td>
                                <td>Number of sides on each die (2-100, for type=dice)</td>
                            </tr>
                            <tr>
                                <td><code>card_deck</code></td>
                                <td>string</td>
                                <td><span class="optional">Optional</span></td>
                                <td>standard</td>
                                <td>Deck type: "standard" (52 cards) or "poker" (52 cards with jokers)</td>
                            </tr>
                            <tr>
                                <td><code>card_jokers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>false</td>
                                <td>Include jokers in the deck</td>
                            </tr>
                            <tr>
                                <td><code>items</code></td>
                                <td>array</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>-</td>
                                <td>Array of items for list or weighted selection</td>
                            </tr>
                            <tr>
                                <td><code>weights</code></td>
                                <td>array</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>-</td>
                                <td>Array of weights corresponding to items (for type=weighted)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Random Number</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "number",
    "min": 1,
    "max": 100,
    "count": 5,
    "unique": true
  }'</div>

                    <h4>Example Request - Random Dice</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dice",
    "count_dice": 3,
    "dice_sides": 6,
    "count": 5
  }'</div>

                    <h4>Example Request - Coin Flip</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "coin",
    "count": 10
  }'</div>

                    <h4>Example Request - Card Draw</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "card",
    "count": 5,
    "card_deck": "standard"
  }'</div>

                    <h4>Example Request - List Randomization</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "list",
    "items": ["apple", "banana", "cherry", "date", "elderberry"],
    "count": 3
  }'</div>

                    <h4>Example Request - Weighted Selection</h4>
                    <div class="code-block">curl -X POST "<?php echo htmlspecialchars($baseUrl); ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "weighted",
    "items": ["rare", "common", "legendary"],
    "weights": [5, 80, 15],
    "count": 10
  }'</div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Number Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "type": "number",
    "results": [42, 17, 89, 3, 56],
    "min": 1,
    "max": 100,
    "count": 5,
    "unique": true
  },
  "message": "Random numbers generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Dice Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "type": "dice",
    "results": [
      { "rolls": [3, 5, 2], "total": 10 },
      { "rolls": [6, 1, 4], "total": 11 },
      { "rolls": [2, 3, 5], "total": 10 }
    ],
    "count_dice": 3,
    "dice_sides": 6,
    "count": 3
  },
  "message": "Dice rolled successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Coin Flip Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "type": "coin",
    "results": ["heads", "tails", "heads", "heads", "tails"],
    "count": 5,
    "statistics": {
      "heads": 3,
      "tails": 2
    }
  },
  "message": "Coins flipped successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Card Draw Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "type": "card",
    "results": [
      { "rank": "A", "suit": "♠", "name": "Ace of Spades", "value": 1 },
      { "rank": "K", "suit": "♥", "name": "King of Hearts", "value": 13 },
      { "rank": "7", "suit": "♦", "name": "7 of Diamonds", "value": 7 },
      { "rank": "Q", "suit": "♣", "name": "Queen of Clubs", "value": 12 },
      { "rank": "5", "suit": "♠", "name": "5 of Spades", "value": 5 }
    ],
    "count": 5,
    "deck": "standard"
  },
  "message": "Cards drawn successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>List Randomization Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "type": "list",
    "results": ["cherry", "apple", "elderberry"],
    "count": 3,
    "original": ["apple", "banana", "cherry", "date", "elderberry"]
  },
  "message": "List randomized successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Weighted Selection Response</h3>
                <div class="response-box">
                    <div class="code-block">{
  "success": true,
  "data": {
    "type": "weighted",
    "results": ["common", "common", "rare", "common", "legendary", "common", "common", "common", "rare", "common"],
    "count": 10,
    "weights": [5, 80, 15]
  },
  "message": "Weighted selection completed",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">{
  "success": false,
  "error": "Invalid generator type",
  "code": "INVALID_TYPE",
  "timestamp": "2025-09-09T12:00:00Z"
}</div>
                </div>
            </div>

            <!-- Generator Types -->
            <div class="section">
                <h2>🎯 Generator Types</h2>

                <h3>🔢 Number Generator</h3>
                <p>Generate random numbers within a specified range. Supports integers and decimals with optional uniqueness constraint.</p>
                <ul style="margin-left: 20px; color: #555; line-height: 1.8;">
                    <li>Range: any numeric min/max values</li>
                    <li>Decimal places: 0-10</li>
                    <li>Unique mode ensures no duplicates</li>
                </ul>

                <h3>🎲 Dice Roller</h3>
                <p>Roll multiple dice with configurable number of sides. Perfect for tabletop games and simulations.</p>
                <ul style="margin-left: 20px; color: #555; line-height: 1.8;">
                    <li>Roll 1-20 dice at once</li>
                    <li>Support for 2-100 sided dice</li>
                    <li>Returns individual rolls and totals</li>
                </ul>

                <h3>🪙 Coin Flip</h3>
                <p>Flip one or more coins with statistical breakdown.</p>
                <ul style="margin-left: 20px; color: #555; line-height: 1.8;">
                    <li>Returns "heads" or "tails" for each flip</li>
                    <li>Includes count statistics in response</li>
                </ul>

                <h3>🃏 Card Draw</h3>
                <p>Draw random cards from standard 52-card deck with optional jokers.</p>
                <ul style="margin-left: 20px; color: #555; line-height: 1.8;">
                    <li>Standard 52-card deck (no jokers)</li>
                    <li>Option to include jokers (54 cards)</li>
                    <li>Returns rank, suit, and value</li>
                </ul>

                <h3>📋 List Randomizer</h3>
                <p>Randomly select or shuffle items from an array.</p>
                <ul style="margin-left: 20px; color: #555; line-height: 1.8;">
                    <li>Select N random items without replacement</li>
                    <li>Shuffle entire array</li>
                    <li>Perfect for giveaways and random selection</li>
                </ul>

                <h3>⚖️ Weighted Selection</h3>
                <p>Select items with weighted probabilities. Each item has a relative weight determining selection chance.</p>
                <ul style="margin-left: 20px; color: #555; line-height: 1.8;">
                    <li>Weights are relative (not percentages)</li>
                    <li>Higher weight = higher probability</li>
                    <li>Great for loot boxes, gacha systems, A/B testing</li>
                </ul>
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
                            <td><code>INVALID_TYPE</code></td>
                            <td>Specified generator type is not supported</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_RANGE</code></td>
                            <td>Min value is greater than or equal to max value</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_COUNT</code></td>
                            <td>Count is outside valid range or exceeds unique possibilities</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_DICE</code></td>
                            <td>Dice count or sides outside valid range</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_ITEMS</code></td>
                            <td>Items array is empty or invalid</td>
                        </tr>
                        <tr>
                            <td><code>WEIGHTS_MISMATCH</code></td>
                            <td>Weights array length doesn't match items array length</td>
                        </tr>
                        <tr>
                            <td><code>GENERATION_ERROR</code></td>
                            <td>Error occurred during random generation</td>
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
                <p>Test the Random Generator API with our interactive web interface or start integrating it into your application.</p>
                <a href="../index.php" class="btn">Try Web Interface</a>
                <a href="/api/randomizer/" class="btn btn-secondary">Test API Endpoint</a>
            </div>
        </div>
    </div>
</body>
</html>