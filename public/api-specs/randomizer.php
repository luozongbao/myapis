<?php
// Generate dynamic base URL based on current server
function getBaseUrl($toolName) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . '/api/' . $toolName . '/';
}
$baseUrl = getBaseUrl('randomizer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Generator API Documentation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .nav {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            color: #333;
            font-size: 1.8em;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .section h3 {
            color: #444;
            font-size: 1.3em;
            margin-bottom: 15px;
            margin-top: 25px;
        }

        .endpoint {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .method {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9em;
            margin-right: 10px;
        }

        .method.post {
            background: #007bff;
        }

        .method.get {
            background: #28a745;
        }

        .url {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-left: 10px;
        }

        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin: 15px 0;
        }

        .parameter-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .parameter-table th,
        .parameter-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .parameter-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .parameter-table td {
            color: #555;
        }

        .required {
            color: #dc3545;
            font-weight: bold;
        }

        .optional {
            color: #6c757d;
        }

        .response-box {
            background: #f0f8f0;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .error-box {
            background: #fdf2f2;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .feature-card h4 {
            color: #333;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            font-size: 0.9em;
        }

        .try-it {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }

        .try-it h3 {
            margin-bottom: 15px;
        }

        .try-it p {
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .type-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #e9ecef;
        }

        .type-item h5 {
            color: #333;
            margin-bottom: 8px;
        }

        .type-item p {
            color: #666;
            font-size: 0.9em;
        }

        .security-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
            margin: 5px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
            
            .code-block {
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎲 Random Generator API</h1>
            <p>Generate random numbers, dice rolls, coin flips, and card draws</p>
        </div>

        <!-- Navigation -->
        <div class="nav">
            <div class="breadcrumb">
                <a href="../index.php">← Back to Main</a>
                <span>/</span>
                <a href="../randomizer.php">Random Generator</a>
                <span>/</span>
                <span>API Documentation</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Overview -->
            <div class="section">
                <h2>📖 Overview</h2>
                <p>The Random Generator API provides cryptographically secure random generation for various use cases including numbers, dice rolls, coin flips, and card draws. Perfect for games, statistical sampling, or any application requiring reliable randomness.</p>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🔢 Number Generation</h4>
                        <p>Generate random integers and floats within specified ranges</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎲 Dice Rolling</h4>
                        <p>Simulate rolling various dice types (D4, D6, D8, D10, D12, D20, D100)</p>
                    </div>
                    <div class="feature-card">
                        <h4>🪙 Coin Flipping</h4>
                        <p>Generate random coin flips with customizable options</p>
                    </div>
                    <div class="feature-card">
                        <h4>🃏 Card Drawing</h4>
                        <p>Draw random playing cards from a standard 52-card deck</p>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <span class="security-badge">🔒 Cryptographically Secure</span>
                    <span class="security-badge">⚡ High Performance</span>
                    <span class="security-badge">🎯 Multiple Types</span>
                </div>
            </div>

            <!-- Generation Types -->
            <div class="section">
                <h2>🎯 Generation Types</h2>
                <div class="types-grid">
                    <div class="type-item">
                        <h5>🔢 Numbers</h5>
                        <p>Integers and decimals</p>
                    </div>
                    <div class="type-item">
                        <h5>🎲 Dice</h5>
                        <p>D4, D6, D8, D10, D12, D20, D100</p>
                    </div>
                    <div class="type-item">
                        <h5>🪙 Coins</h5>
                        <p>Heads or Tails</p>
                    </div>
                    <div class="type-item">
                        <h5>🃏 Cards</h5>
                        <p>52-card deck</p>
                    </div>
                </div>
            </div>

            <!-- Base URL -->
            <div class="section">
                <h2>🌐 Base URL</h2>
                <div class="code-block">
<?php echo $baseUrl; ?>
                </div>
            </div>

            <!-- Authentication -->
            <div class="section">
                <h2>🔐 Authentication</h2>
                <p>No authentication required. This is a public API that can be accessed without any API keys or tokens.</p>
            </div>

            <!-- Endpoints -->
            <div class="section">
                <h2>📡 API Endpoints</h2>

                <!-- Unified Generator Endpoint -->
                <div class="endpoint">
                    <h3>
                        <span class="method post">POST</span>
                        <span class="url">/</span>
                        Generate Random Values
                    </h3>
                    <p>Generate random values based on the specified type and parameters.</p>

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
                                <td><code>type</code></td>
                                <td>string</td>
                                <td><span class="required">Required</span></td>
                                <td>Generation type: "number", "dice", "coin", "card", "all"</td>
                            </tr>
                            <tr>
                                <td><code>min</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>Minimum value (for number type, default: 1)</td>
                            </tr>
                            <tr>
                                <td><code>max</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>Maximum value (for number type, default: 100)</td>
                            </tr>
                            <tr>
                                <td><code>sides</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional*</span></td>
                                <td>Number of sides on dice (for dice type, 2-100, default: 6)</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Number of items to generate (dice: 1-10, coin: 1-10, card: 1-52, default: 1)</td>
                            </tr>
                            <tr>
                                <td><code>with_jokers</code></td>
                                <td>boolean</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Include jokers in card deck (for card type, default: false)</td>
                            </tr>
                            <tr>
                                <td><code>coin_count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Number of coins to flip (1-10)</td>
                            </tr>
                            <tr>
                                <td><code>card_count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Number of cards to draw (1-52)</td>
                            </tr>
                            <tr>
                                <td><code>count</code></td>
                                <td>integer</td>
                                <td><span class="optional">Optional</span></td>
                                <td>Number of results to generate (1-100)</td>
                            </tr>
                        </tbody>
                    </table>

                    <h4>Example Request - Random Number</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "number",
    "min": 1,
    "max": 100
  }'
                    </div>

                    <h4>Example Request - Dice Roll</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dice",
    "sides": 20,
    "count": 3
  }'
                    </div>

                    <h4>Example Request - Coin Flip</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "coin",
    "count": 5
  }'
                    </div>

                    <h4>Example Request - Card Draw</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "card",
    "count": 5,
    "with_jokers": true
  }'
                    </div>

                    <h4>Example Request - Generate All Types</h4>
                    <div class="code-block">
curl -X POST "<?php echo $baseUrl; ?>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "all"
  }'
                    </div>
                </div>
            </div>

            <!-- Response Format -->
            <div class="section">
                <h2>📊 Response Format</h2>

                <h3>Success Response Examples</h3>
                
                <div class="response-box">
                    <h4>Random Numbers Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "type": "number",
    "results": [42, 17, 89, 3, 76],
    "count": 5,
    "settings": {
      "min": 1,
      "max": 100,
      "decimal_places": 0
    }
  },
  "message": "Random numbers generated successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Dice Roll Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "type": "dice",
    "results": [
      {
        "die": 1,
        "value": 18,
        "dice_type": "d20"
      },
      {
        "die": 2,
        "value": 7,
        "dice_type": "d20"
      },
      {
        "die": 3,
        "value": 20,
        "dice_type": "d20"
      }
    ],
    "total": 45,
    "dice_count": 3,
    "dice_type": "d20"
  },
  "message": "Dice rolled successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Coin Flip Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "type": "coin",
    "results": [
      {
        "coin": 1,
        "result": "heads"
      },
      {
        "coin": 2,
        "result": "tails"
      },
      {
        "coin": 3,
        "result": "heads"
      }
    ],
    "summary": {
      "heads": 2,
      "tails": 1
    },
    "coin_count": 3
  },
  "message": "Coins flipped successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <div class="response-box">
                    <h4>Card Draw Response</h4>
                    <div class="code-block">
{
  "success": true,
  "data": {
    "type": "card",
    "results": [
      {
        "card": 1,
        "rank": "King",
        "suit": "Hearts",
        "value": "K♥",
        "unicode": "🂾"
      },
      {
        "card": 2,
        "rank": "7",
        "suit": "Spades",
        "value": "7♠",
        "unicode": "🂧"
      }
    ],
    "card_count": 2,
    "remaining_cards": 50
  },
  "message": "Cards drawn successfully",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>

                <h3>Error Response</h3>
                <div class="error-box">
                    <div class="code-block">
{
  "success": false,
  "error": "Invalid generation type. Must be one of: number, dice, coin, card",
  "code": "INVALID_TYPE",
  "timestamp": "2025-09-09T12:00:00Z"
}
                    </div>
                </div>
            </div>

            <!-- Dice Types -->
            <div class="section">
                <h2>🎲 Dice Types</h2>
                <table class="parameter-table">
                    <thead>
                        <tr>
                            <th>Dice Type</th>
                            <th>Sides</th>
                            <th>Range</th>
                            <th>Common Use</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>d4</code></td>
                            <td>4</td>
                            <td>1-4</td>
                            <td>Damage dice in RPGs</td>
                        </tr>
                        <tr>
                            <td><code>d6</code></td>
                            <td>6</td>
                            <td>1-6</td>
                            <td>Standard board game die</td>
                        </tr>
                        <tr>
                            <td><code>d8</code></td>
                            <td>8</td>
                            <td>1-8</td>
                            <td>RPG weapon damage</td>
                        </tr>
                        <tr>
                            <td><code>d10</code></td>
                            <td>10</td>
                            <td>1-10</td>
                            <td>Percentile systems</td>
                        </tr>
                        <tr>
                            <td><code>d12</code></td>
                            <td>12</td>
                            <td>1-12</td>
                            <td>Heavy weapon damage</td>
                        </tr>
                        <tr>
                            <td><code>d20</code></td>
                            <td>20</td>
                            <td>1-20</td>
                            <td>D&D ability checks</td>
                        </tr>
                        <tr>
                            <td><code>d100</code></td>
                            <td>100</td>
                            <td>1-100</td>
                            <td>Percentage rolls</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Card Information -->
            <div class="section">
                <h2>🃏 Playing Card Information</h2>
                <p>The card drawing feature uses a standard 52-card deck with the following specifications:</p>
                
                <h3>Suits</h3>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>Hearts (♥):</strong> Red suit</li>
                    <li><strong>Diamonds (♦):</strong> Red suit</li>
                    <li><strong>Clubs (♣):</strong> Black suit</li>
                    <li><strong>Spades (♠):</strong> Black suit</li>
                </ul>

                <h3>Ranks</h3>
                <ul style="color: #555; font-size: 1.1em; line-height: 1.8; margin-left: 20px;">
                    <li><strong>Number Cards:</strong> 2, 3, 4, 5, 6, 7, 8, 9, 10</li>
                    <li><strong>Face Cards:</strong> Jack (J), Queen (Q), King (K)</li>
                    <li><strong>Ace:</strong> A (can be high or low depending on game rules)</li>
                </ul>

                <h3>Unicode Support</h3>
                <p>Each card includes Unicode playing card symbols for easy display in applications.</p>
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
                            <td>Generation type is not supported</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_RANGE</code></td>
                            <td>Min/Max values are invalid or min > max</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_DICE_TYPE</code></td>
                            <td>Dice type is not supported</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_COUNT</code></td>
                            <td>Count parameter is outside valid range</td>
                        </tr>
                        <tr>
                            <td><code>INVALID_DECIMAL_PLACES</code></td>
                            <td>Decimal places is outside valid range (0-10)</td>
                        </tr>
                        <tr>
                            <td><code>GENERATION_ERROR</code></td>
                            <td>Error occurred during random generation</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Use Cases -->
            <div class="section">
                <h2>🎯 Common Use Cases</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <h4>🎮 Gaming Applications</h4>
                        <p>Dice rolls, card games, random events, loot generation</p>
                    </div>
                    <div class="feature-card">
                        <h4>📊 Statistical Sampling</h4>
                        <p>Random sampling, A/B testing, survey randomization</p>
                    </div>
                    <div class="feature-card">
                        <h4>🎰 Casino & Gambling</h4>
                        <p>Fair randomization for gambling applications</p>
                    </div>
                    <div class="feature-card">
                        <h4>🧪 Research & Testing</h4>
                        <p>Random data generation, simulation, testing scenarios</p>
                    </div>
                </div>
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
