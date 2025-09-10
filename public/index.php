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
            font-family: 'Segoe UI', Tahoma, G                <div class="tool-actions">
                    <a href="promptpay-qr-generator.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/promptpay-qr-generator/" class="btn btn-secondary">API</a>
                    <a href="api-specs/promptpay-qr-generator.php" class="btn btn-secondary">API Docs</a>
                </div>, Verdana, sans-serif;
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
            <!-- Health Calculator -->
            <div class="tool-card">
                <div class="tool-icon">🏥</div>
                <h3 class="tool-title">Health Calculator</h3>
                <p class="tool-description">Comprehensive health metrics calculator providing BMI, BMR, daily caloric intake, and water requirements with personalized recommendations based on your goals and lifestyle</p>
                <ul class="tool-features">
                    <li>BMI Calculator with WHO classification</li>
                    <li>BMR Calculator (Mifflin-St Jeor equation)</li>
                    <li>Daily Caloric Intake with activity levels</li>
                    <li>Water Intake with climate adjustments</li>
                    <li>Metric & Imperial unit support</li>
                    <li>Goal-based recommendations (lose/gain/maintain)</li>
                    <li>Health condition considerations</li>
                    <li>Comprehensive REST API with JSON responses</li>
                </ul>
                <div class="tool-actions">
                    <a href="health-calculator.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/health-calculator/" class="btn btn-secondary">API</a>
                    <a href="api-specs/health-calculator.php" class="btn btn-secondary">API Docs</a>
                </div>
            </div>

            <!-- Password Generator -->
            <div class="tool-card">
                <div class="tool-icon">🔐</div>
                <h3 class="tool-title">Password Generator</h3>
                <p class="tool-description">Advanced cryptographically secure password generator with real-time strength analysis, customizable character sets, and enterprise-grade security standards for bulletproof authentication</p>
                <ul class="tool-features">
                    <li>Cryptographically secure random generation</li>
                    <li>Customizable length (4-128 characters)</li>
                    <li>Multiple character sets (letters, numbers, symbols)</li>
                    <li>Real-time password strength analysis</li>
                    <li>Entropy calculation and security scoring</li>
                    <li>Bulk password generation support</li>
                    <li>Copy-to-clipboard functionality</li>
                    <li>Mobile-responsive interface</li>
                </ul>
                <div class="tool-actions">
                    <a href="password-generator.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/password-generator/" class="btn btn-secondary">API</a>
                    <a href="api-specs/password-generator.php" class="btn btn-secondary">API Docs</a>
                </div>
            </div>

            <!-- Username Generator -->
            <div class="tool-card">
                <div class="tool-icon">👤</div>
                <h3 class="tool-title">Username Generator</h3>
                <p class="tool-description">Creative username generator featuring 7 themed categories with intelligent word combinations, perfect for gaming, social media, and professional platforms with guaranteed uniqueness</p>
                <ul class="tool-features">
                    <li>7 themed categories (Nature, Tech, Space, etc.)</li>
                    <li>Cross-theme intelligent combinations</li>
                    <li>100+ curated adjectives and nouns</li>
                    <li>Bulk generation (up to 50 usernames)</li>
                    <li>Availability checking suggestions</li>
                    <li>Gaming & social media optimized</li>
                    <li>Professional username options</li>
                    <li>JSON API for integration</li>
                </ul>
                <div class="tool-actions">
                    <a href="username-generator.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/username-generator/" class="btn btn-secondary">API</a>
                    <a href="api-specs/username-generator.php" class="btn btn-secondary">API Docs</a>
                </div>
            </div>

            <!-- PromptPay QR Generator -->
            <div class="tool-card">
                <div class="tool-icon">💳</div>
                <h3 class="tool-title">PromptPay QR Generator</h3>
                <p class="tool-description">Professional EMV-compliant PromptPay QR code generator for Thailand's national payment system, supporting phone numbers, tax IDs, and e-wallets with instant mobile-ready output</p>
                <ul class="tool-features">
                    <li>EMV QR Code Standard (4.0) compliant</li>
                    <li>Phone number (13-digit) support</li>
                    <li>Tax ID (13-digit) integration</li>
                    <li>e-Wallet ID compatibility</li>
                    <li>Custom amount specification</li>
                    <li>Base64 encoded image output</li>
                    <li>PNG format with customizable size</li>
                    <li>Real-time QR code preview</li>
                </ul>
                <div class="tool-actions">
                    <a href="promptpay-qr-generator.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/promptpay-qr-generator/" class="btn btn-secondary">API</a>
                    <a href="api-specs/promptpay-qr-generator.php" class="btn btn-secondary">API Docs</a>
                </div>
            </div>

            <!-- Fortune Teller -->
            <div class="tool-card">
                <div class="tool-icon">🔮</div>
                <h3 class="tool-title">Fortune Teller</h3>
                <p class="tool-description">Mystical multilingual fortune teller featuring 52 unique predictions across love, career, health, wealth, and luck with beautiful Thai, Chinese, and English interfaces for global accessibility</p>
                <ul class="tool-features">
                    <li>52 unique fortune predictions</li>
                    <li>Multilingual support (Thai, Chinese, English)</li>
                    <li>5 life categories (Love, Career, Health, Wealth, Luck)</li>
                    <li>Random prediction selection algorithm</li>
                    <li>Beautiful cultural-themed interface</li>
                    <li>Mobile-responsive design</li>
                    <li>JSON API with language selection</li>
                    <li>Share-friendly prediction format</li>
                </ul>
                <div class="tool-actions">
                    <a href="fortune-teller.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/fortune-teller/" class="btn btn-secondary">API</a>
                    <a href="api-specs/fortune-teller.php" class="btn btn-secondary">API Docs</a>
                </div>
            </div>

            <!-- Random Generator -->
            <div class="tool-card">
                <div class="tool-icon">🎲</div>
                <h3 class="tool-title">Random Generator</h3>
                <p class="tool-description">Comprehensive randomization toolkit with cryptographically secure number generation, dice rolling, coin flipping, and card drawing featuring stunning animations and true randomness</p>
                <ul class="tool-features">
                    <li>Random number generation (custom ranges)</li>
                    <li>Dice rolling (1-20 sided dice, multiple)</li>
                    <li>Coin flipping with animated results</li>
                    <li>Card drawing from standard 52-card deck</li>
                    <li>Cryptographically secure randomness</li>
                    <li>Beautiful CSS animations</li>
                    <li>History tracking of results</li>
                    <li>Multiple operations in single session</li>
                </ul>
                <div class="tool-actions">
                    <a href="randomizer.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/randomizer/" class="btn btn-secondary">API</a>
                    <a href="api-specs/randomizer.php" class="btn btn-secondary">API Docs</a>
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
                    <div class="stat-number">18</div>
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
