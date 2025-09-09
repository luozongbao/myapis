<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyAPIs - Developer Tools Collection</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 3em;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #666;
            font-size: 1.3em;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9em;
            font-weight: bold;
            margin-top: 15px;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .tool-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .tool-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            border-color: #667eea;
        }

        .tool-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .tool-card:hover::before {
            left: 100%;
        }

        .tool-icon {
            font-size: 3em;
            margin-bottom: 20px;
            text-align: center;
        }

        .tool-title {
            font-size: 1.5em;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        .tool-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: center;
        }

        .tool-features {
            list-style: none;
            margin-bottom: 25px;
        }

        .tool-features li {
            color: #555;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .tool-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
        }

        .tool-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e9ecef;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .stats {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 50px;
        }

        .stats-title {
            text-align: center;
            font-size: 2em;
            color: #333;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #666;
            font-size: 1.1em;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .footer p {
            color: white;
            font-size: 1.1em;
            margin-bottom: 15px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #ffd700;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .tools-grid {
                grid-template-columns: 1fr;
            }
            
            .tool-actions {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚀 MyAPIs</h1>
            <p>A comprehensive collection of developer tools and APIs designed to streamline your development workflow</p>
            <div class="status-badge">✅ All Systems Operational</div>
        </div>

        <!-- Tools Grid -->
        <div class="tools-grid">
            <!-- BMI Calculator -->
            <div class="tool-card">
                <div class="tool-icon">⚖️</div>
                <h3 class="tool-title">BMI Calculator</h3>
                <p class="tool-description">Calculate Body Mass Index with WHO standard categories and health recommendations</p>
                <ul class="tool-features">
                    <li>WHO standard BMI categories</li>
                    <li>Metric and Imperial units</li>
                    <li>Health recommendations</li>
                    <li>REST API endpoint</li>
                </ul>
                <div class="tool-actions">
                    <a href="bmi-calculator/" class="btn btn-primary">Try Tool</a>
                    <a href="bmi-calculator/api/" class="btn btn-secondary">API</a>
                </div>
            </div>

            <!-- Password Generator -->
            <div class="tool-card">
                <div class="tool-icon">🔐</div>
                <h3 class="tool-title">Password Generator</h3>
                <p class="tool-description">Generate cryptographically secure passwords with customizable complexity options</p>
                <ul class="tool-features">
                    <li>Cryptographically secure</li>
                    <li>Customizable character sets</li>
                    <li>Password strength analysis</li>
                    <li>Multiple formats support</li>
                </ul>
                <div class="tool-actions">
                    <a href="password-generator/" class="btn btn-primary">Try Tool</a>
                    <a href="password-generator/api/" class="btn btn-secondary">API</a>
                </div>
            </div>

            <!-- Username Generator -->
            <div class="tool-card">
                <div class="tool-icon">👤</div>
                <h3 class="tool-title">Username Generator</h3>
                <p class="tool-description">Create unique usernames using word combinations with themed approaches</p>
                <ul class="tool-features">
                    <li>6 themed word categories</li>
                    <li>Cross-theme combinations</li>
                    <li>100+ general adjectives</li>
                    <li>Bulk generation support</li>
                </ul>
                <div class="tool-actions">
                    <a href="username-generator/" class="btn btn-primary">Try Tool</a>
                    <a href="username-generator/api/" class="btn btn-secondary">API</a>
                </div>
            </div>

            <!-- PromptPay QR Generator -->
            <div class="tool-card">
                <div class="tool-icon">💳</div>
                <h3 class="tool-title">PromptPay QR Generator</h3>
                <p class="tool-description">Generate EMV-compliant PromptPay QR codes for Thai payment system</p>
                <ul class="tool-features">
                    <li>EMV QR standard compliant</li>
                    <li>Phone/Tax ID/e-Wallet support</li>
                    <li>Base64 image generation</li>
                    <li>Multiple output formats</li>
                </ul>
                <div class="tool-actions">
                    <a href="promptpay-qr-generator/" class="btn btn-primary">Try Tool</a>
                    <a href="promptpay-qr-generator/api/" class="btn btn-secondary">API</a>
                </div>
            </div>

            <!-- Fortune Teller -->
            <div class="tool-card">
                <div class="tool-icon">🔮</div>
                <h3 class="tool-title">Fortune Teller</h3>
                <p class="tool-description">Get multilingual fortune predictions covering all aspects of life</p>
                <ul class="tool-features">
                    <li>52 unique fortune predictions</li>
                    <li>Thai, Chinese & English support</li>
                    <li>5 life categories coverage</li>
                    <li>Beautiful multilingual interface</li>
                </ul>
                <div class="tool-actions">
                    <a href="fortune-teller/" class="btn btn-primary">Try Tool</a>
                    <a href="fortune-teller/api/" class="btn btn-secondary">API</a>
                </div>
            </div>

            <!-- Random Generator -->
            <div class="tool-card">
                <div class="tool-icon">🎲</div>
                <h3 class="tool-title">Random Generator</h3>
                <p class="tool-description">Generate random numbers, roll dice, flip coins, and draw cards with beautiful animations</p>
                <ul class="tool-features">
                    <li>Number ranges & dice rolling</li>
                    <li>Coin flips & card drawing</li>
                    <li>Animated visual results</li>
                    <li>Cryptographically secure</li>
                </ul>
                <div class="tool-actions">
                    <a href="randomizer/" class="btn btn-primary">Try Tool</a>
                    <a href="randomizer/api/" class="btn btn-secondary">API</a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats">
            <h2 class="stats-title">📊 Platform Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">Active Tools</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">12</div>
                    <div class="stat-label">API Endpoints</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100</div>
                    <div class="stat-label">% Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">PHP</div>
                    <div class="stat-label">Technology</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Built with ❤️ for developers by developers</p>
            <div class="footer-links">
                <a href="#documentation">📚 Documentation</a>
                <a href="https://github.com/luozongbao/myapis">🔗 GitHub</a>
                <a href="#api-status">📡 API Status</a>
                <a href="#support">💬 Support</a>
            </div>
        </div>
    </div>

    <script>
        // Add smooth scrolling and interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add click tracking for tool cards
            const toolCards = document.querySelectorAll('.tool-card');
            toolCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A') return; // Don't interfere with button clicks
                    
                    const primaryButton = card.querySelector('.btn-primary');
                    if (primaryButton) {
                        window.location.href = primaryButton.href;
                    }
                });
            });

            // Add loading animation to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const originalText = this.textContent;
                    this.textContent = 'Loading...';
                    setTimeout(() => {
                        this.textContent = originalText;
                    }, 1000);
                });
            });

            // Update stats dynamically
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                if (stat.textContent === '100') {
                    setInterval(() => {
                        stat.style.color = stat.style.color === 'rgb(76, 175, 80)' ? '' : '#4CAF50';
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>
