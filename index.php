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
        }

        .tool-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            border-color: #667eea;
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
            padding: 8px 0;
            color: #555;
            position: relative;
            padding-left: 25px;
        }

        .tool-features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }

        .tool-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e1e5e9;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #667eea;
        }

        .stats-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            border: 2px solid #e1e5e9;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
            color: #666;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: #764ba2;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5em;
            }

            .header p {
                font-size: 1.1em;
            }

            .tools-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .tool-card {
                padding: 25px;
            }

            .tool-buttons {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #667eea;
            color: white;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
            margin-left: 10px;
        }

        .new-badge {
            background: #28a745;
        }

        .popular-badge {
            background: #ffc107;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚀 MyAPIs</h1>
            <p>A comprehensive collection of developer tools and APIs for everyday tasks. Generate passwords, create usernames, calculate BMI, generate QR codes, and more!</p>
        </div>

        <!-- Statistics -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">4</div>
                    <div class="stat-label">Available Tools</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">API Endpoints</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Open Source</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">Free Usage</div>
                </div>
            </div>
        </div>

        <!-- Tools Grid -->
        <div class="tools-grid">
            <!-- BMI Calculator -->
            <a href="bmi-calculator/" class="tool-card">
                <div class="tool-icon">⚖️</div>
                <h2 class="tool-title">BMI Calculator <span class="badge">Health</span></h2>
                <p class="tool-description">Calculate Body Mass Index with metric and imperial units support, including health recommendations.</p>
                <ul class="tool-features">
                    <li>Metric & Imperial units</li>
                    <li>Health category classification</li>
                    <li>Personalized advice</li>
                    <li>REST API & Web interface</li>
                </ul>
                <div class="tool-buttons">
                    <div class="btn btn-primary">Open Tool</div>
                    <a href="bmi-calculator/api/" class="btn btn-secondary">API Docs</a>
                </div>
            </a>

            <!-- Password Generator -->
            <a href="password-generator/" class="tool-card">
                <div class="tool-icon">🔐</div>
                <h2 class="tool-title">Password Generator <span class="badge popular-badge">Popular</span></h2>
                <p class="tool-description">Generate secure passwords with customizable options, strength analysis, and security features.</p>
                <ul class="tool-features">
                    <li>Customizable length (1-128 chars)</li>
                    <li>Character type selection</li>
                    <li>Strength analysis</li>
                    <li>Cryptographically secure</li>
                </ul>
                <div class="tool-buttons">
                    <div class="btn btn-primary">Open Tool</div>
                    <a href="password-generator/api/" class="btn btn-secondary">API Docs</a>
                </div>
            </a>

            <!-- Username Generator -->
            <a href="username-generator/" class="tool-card">
                <div class="tool-icon">🎯</div>
                <h2 class="tool-title">Username Generator <span class="badge new-badge">Enhanced</span></h2>
                <p class="tool-description">Create unique usernames with multiple themes, custom options, and creative word combinations.</p>
                <ul class="tool-features">
                    <li>6 different themes</li>
                    <li>Cross-theme combinations</li>
                    <li>General adjectives pool</li>
                    <li>Custom word injection</li>
                </ul>
                <div class="tool-buttons">
                    <div class="btn btn-primary">Open Tool</div>
                    <a href="username-generator/api/" class="btn btn-secondary">API Docs</a>
                </div>
            </a>

            <!-- PromptPay QR Generator -->
            <a href="promptpay-qr-generator/" class="tool-card">
                <div class="tool-icon">💰</div>
                <h2 class="tool-title">PromptPay QR Generator <span class="badge">Thailand</span></h2>
                <p class="tool-description">Generate PromptPay QR codes for Thailand's payment system with phone numbers, tax IDs, and e-wallets.</p>
                <ul class="tool-features">
                    <li>Phone, Tax ID, e-Wallet support</li>
                    <li>EMV QR standard compliant</li>
                    <li>Multiple output formats</li>
                    <li>Bank app compatible</li>
                </ul>
                <div class="tool-buttons">
                    <div class="btn btn-primary">Open Tool</div>
                    <a href="promptpay-qr-generator/api/" class="btn btn-secondary">API Docs</a>
                </div>
            </a>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-links">
                <a href="https://github.com/luozongbao/myapis" class="footer-link">📚 Documentation</a>
                <a href="https://github.com/luozongbao/myapis" class="footer-link">🐛 Report Issues</a>
                <a href="https://github.com/luozongbao/myapis" class="footer-link">⭐ Star on GitHub</a>
                <a href="https://github.com/luozongbao/myapis" class="footer-link">🤝 Contribute</a>
            </div>
            <p>&copy; 2025 MyAPIs - Open Source Developer Tools. Built with ❤️ for the developer community.</p>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all tool cards
            document.querySelectorAll('.tool-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Add click tracking for analytics (if needed)
            document.querySelectorAll('.tool-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    const toolName = this.querySelector('.tool-title').textContent.trim();
                    console.log(`Tool accessed: ${toolName}`);
                    // Add analytics tracking here if needed
                });
            });

            // Add copy functionality for API links
            document.querySelectorAll('.btn-secondary').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.open(this.href, '_blank');
                });
            });
        });

        // Add some fun interactions
        document.querySelectorAll('.stat-number').forEach(stat => {
            stat.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.transition = 'transform 0.3s ease';
            });
            
            stat.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
