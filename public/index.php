<?php
/**
 * MyAPIs — landing page (index.php)
 *
 * Renders the homepage that lists every available tool. Tool
 * entries live in a single $TOOLS array so adding or removing a
 * tool is a one-line change.
 *
 * Static assets live in public/assets/css and public/assets/js;
 * both are referenced relatively so the page works under any
 * sub-path (e.g. /public/, /myapis/, /).
 */
declare(strict_types=1);

$TOOLS = [
    [
        'slug'        => 'health-calculator',
        'icon'        => '🏥',
        'name'        => 'Health Calculator',
        'description' => 'Comprehensive health metrics calculator providing BMI, BMR, daily caloric intake, and water requirements with personalized recommendations based on your goals and lifestyle',
        'features'    => [
            'BMI Calculator with WHO classification',
            'BMR Calculator (Mifflin-St Jeor equation)',
            'Daily Caloric Intake with activity levels',
            'Water Intake with climate adjustments',
            'Metric & Imperial unit support',
            'Goal-based recommendations (lose/gain/maintain)',
            'Health condition considerations',
            'Comprehensive REST API with JSON responses',
        ],
    ],
    [
        'slug'        => 'password-generator',
        'icon'        => '🔐',
        'name'        => 'Password Generator',
        'description' => 'Advanced cryptographically secure password generator with real-time strength analysis, customizable character sets, and enterprise-grade security standards for bulletproof authentication',
        'features'    => [
            'Cryptographically secure random generation',
            'Customizable length (4-128 characters)',
            'Multiple character sets (letters, numbers, symbols)',
            'Real-time password strength analysis',
            'Entropy calculation and security scoring',
            'Bulk password generation support',
            'Copy-to-clipboard functionality',
            'Mobile-responsive interface',
        ],
    ],
    [
        'slug'        => 'username-generator',
        'icon'        => '👤',
        'name'        => 'Username Generator',
        'description' => 'Creative username generator featuring 9 themed categories with intelligent word combinations, perfect for gaming, social media, and professional platforms with guaranteed uniqueness',
        'features'    => [
            '9 themed categories (Nature, Tech, Space, etc.)',
            'Cross-theme intelligent combinations',
            '100+ curated adjectives and nouns',
            'Bulk generation (up to 50 usernames)',
            'Availability checking suggestions',
            'Gaming & social media optimized',
            'Professional username options',
            'JSON API for integration',
        ],
    ],
    [
        'slug'        => 'promptpay-qr-generator',
        'icon'        => '💳',
        'name'        => 'PromptPay QR Generator',
        'description' => 'Professional EMV-compliant PromptPay QR code generator for Thailand\'s national payment system, supporting phone numbers, tax IDs, and e-wallets with instant mobile-ready output',
        'features'    => [
            'EMV QR Code Standard (4.0) compliant',
            'Phone number (13-digit) support',
            'Tax ID (13-digit) integration',
            'e-Wallet ID compatibility',
            'Custom amount specification',
            'Base64 encoded image output',
            'PNG format with customizable size',
            'Real-time QR code preview',
        ],
    ],
    [
        'slug'        => 'fortune-teller',
        'icon'        => '🔮',
        'name'        => 'Fortune Teller',
        'description' => 'Mystical multilingual fortune teller featuring 52 unique predictions across love, career, health, wealth, and luck with beautiful Thai, Chinese, and English interfaces for global accessibility',
        'features'    => [
            '52 unique fortune predictions',
            'Multilingual support (Thai, Chinese, English)',
            '5 life categories (Love, Career, Health, Wealth, Luck)',
            'Random prediction selection algorithm',
            'Beautiful cultural-themed interface',
            'Mobile-responsive design',
            'JSON API with language selection',
            'Share-friendly prediction format',
        ],
    ],
    [
        'slug'        => 'qr-code-generator',
        'icon'        => '📱',
        'name'        => 'QR Code Generator',
        'description' => 'Universal QR code generator powered by the goQR.me API. Create QR codes for plain text, URLs, business vCards, calendar events, Wi-Fi networks, and phone numbers with full control over size, error correction, and colours',
        'features'    => [
            'Plain Text &amp; long-form messages',
            'Business vCard (3.0) with full contact fields',
            'iCalendar events with start/end times',
            'Website URLs with auto https:// prefix',
            'Wi-Fi credentials (WPA/WEP/None, hidden flag)',
            'Phone numbers (tel: URI)',
            'Custom size (50-1000px), ECC, margin &amp; quiet zone',
            'JSON / raw-image output with base64 data URL',
        ],
    ],
    [
        'slug'        => 'randomizer',
        'icon'        => '🎲',
        'name'        => 'Random Generator',
        'description' => 'Comprehensive randomization toolkit with cryptographically secure number generation, dice rolling, coin flipping, and card drawing featuring stunning animations and true randomness',
        'features'    => [
            'Random number generation (custom ranges)',
            'Dice rolling (1-20 sided dice, multiple)',
            'Coin flipping with animated results',
            'Card drawing from standard 52-card deck',
            'Cryptographically secure randomness',
            'Beautiful CSS animations',
            'History tracking of results',
            'Multiple operations in single session',
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyAPIs - Developer Tools Collection</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <script src="/assets/js/index.js" defer></script>
<?php /** MyAPIs Analytics (Hostinger / shared-hosting friendly) */ if (file_exists(__DIR__ . "/analytics.php")) { require __DIR__ . "/analytics.php"; } ?>
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
            <?php foreach ($TOOLS as $tool): ?>
            <div class="tool-card" data-tool="<?php echo htmlspecialchars($tool['slug'], ENT_QUOTES); ?>">
                <div class="tool-icon"><?php echo $tool['icon']; ?></div>
                <h3 class="tool-title"><?php echo htmlspecialchars($tool['name']); ?></h3>
                <p class="tool-description"><?php echo htmlspecialchars($tool['description']); ?></p>
                <ul class="tool-features">
                    <?php foreach ($tool['features'] as $feature): ?>
                    <li><?php echo htmlspecialchars($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="tool-actions">
                    <a href="tools/<?php echo htmlspecialchars($tool['slug']); ?>.php" class="btn btn-primary">Try Tool</a>
                    <a href="/api/<?php echo htmlspecialchars($tool['slug']); ?>/" class="btn btn-secondary">API</a>
                    <a href="api-specs/<?php echo htmlspecialchars($tool['slug']); ?>.php" class="btn btn-secondary">API Docs</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Statistics -->
        <div class="stats">
            <h2 class="stats-title">📊 Platform Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" data-stat="tools"><?php echo count($TOOLS); ?></div>
                    <div class="stat-label">Active Tools</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-stat="endpoints">21</div>
                    <div class="stat-label">API Endpoints</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-stat="uptime">100</div>
                    <div class="stat-label">% Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">PHP</div>
                    <div class="stat-label">Technology</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php $footer_variant = 'glass'; ?>
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>

    </body>
</html>