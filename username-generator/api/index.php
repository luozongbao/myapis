<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class UsernameGenerator {
    
    // Word lists for different themes
    private $themes = [
        'gaming' => [
            'adjectives' => ['Epic', 'Legendary', 'Shadow', 'Dark', 'Fire', 'Ice', 'Storm', 'Lightning', 'Mystic', 'Cyber', 'Neon', 'Plasma', 'Toxic', 'Savage', 'Elite', 'Pro', 'Mega', 'Ultra', 'Super', 'Alpha', 'Beta', 'Omega', 'Prime', 'Stealth', 'Phantom', 'Ghost', 'Demon', 'Angel', 'Dragon', 'Phoenix'],
            'nouns' => ['Warrior', 'Hunter', 'Assassin', 'Mage', 'Ninja', 'Samurai', 'Knight', 'Wizard', 'Ranger', 'Sniper', 'Fighter', 'Striker', 'Destroyer', 'Guardian', 'Champion', 'Hero', 'Legend', 'Master', 'Lord', 'King', 'Emperor', 'Slayer', 'Reaper', 'Beast', 'Wolf', 'Tiger', 'Eagle', 'Hawk', 'Viper', 'Scorpion']
        ],
        'professional' => [
            'adjectives' => ['Smart', 'Wise', 'Bright', 'Sharp', 'Quick', 'Swift', 'Clever', 'Creative', 'Innovative', 'Dynamic', 'Strategic', 'Efficient', 'Productive', 'Reliable', 'Focused', 'Skilled', 'Expert', 'Advanced', 'Premium', 'Quality', 'Modern', 'Fresh', 'Clean', 'Clear', 'Solid', 'Strong', 'Stable', 'Secure', 'Professional', 'Corporate'],
            'nouns' => ['Dev', 'Coder', 'Tech', 'Logic', 'Code', 'Data', 'Mind', 'Brain', 'Think', 'Idea', 'Solution', 'Builder', 'Maker', 'Creator', 'Designer', 'Engineer', 'Analyst', 'Consultant', 'Expert', 'Specialist', 'Manager', 'Leader', 'Director', 'Chief', 'Pro', 'Ace', 'Star', 'Elite', 'Prime', 'Core']
        ],
        'fun' => [
            'adjectives' => ['Happy', 'Jolly', 'Cheerful', 'Bouncy', 'Bubbly', 'Giggly', 'Silly', 'Funny', 'Crazy', 'Wild', 'Zany', 'Wacky', 'Quirky', 'Peppy', 'Zippy', 'Snappy', 'Perky', 'Spunky', 'Lively', 'Energetic', 'Vibrant', 'Colorful', 'Bright', 'Sunny', 'Rainbow', 'Magic', 'Wonder', 'Amazing', 'Awesome', 'Cool'],
            'nouns' => ['Panda', 'Penguin', 'Koala', 'Bunny', 'Puppy', 'Kitten', 'Bear', 'Fox', 'Duck', 'Frog', 'Bee', 'Butterfly', 'Star', 'Moon', 'Sun', 'Cloud', 'Rainbow', 'Smile', 'Dream', 'Wish', 'Magic', 'Wonder', 'Joy', 'Bliss', 'Cheer', 'Spark', 'Glow', 'Shine', 'Twinkle', 'Bubble']
        ],
        'nature' => [
            'adjectives' => ['Wild', 'Forest', 'Mountain', 'Ocean', 'River', 'Sky', 'Earth', 'Green', 'Blue', 'Golden', 'Silver', 'Crystal', 'Pure', 'Fresh', 'Natural', 'Organic', 'Living', 'Growing', 'Flowing', 'Shining', 'Glowing', 'Peaceful', 'Calm', 'Serene', 'Tranquil', 'Gentle', 'Soft', 'Warm', 'Cool', 'Misty'],
            'nouns' => ['Tree', 'Leaf', 'Branch', 'Root', 'Flower', 'Rose', 'Lily', 'Oak', 'Pine', 'Willow', 'Eagle', 'Falcon', 'Deer', 'Wolf', 'Bear', 'Lion', 'Tiger', 'Whale', 'Dolphin', 'Shark', 'Stone', 'Rock', 'Mountain', 'Valley', 'River', 'Lake', 'Ocean', 'Beach', 'Island', 'Forest']
        ],
        'tech' => [
            'adjectives' => ['Digital', 'Virtual', 'Cyber', 'Tech', 'Smart', 'AI', 'Quantum', 'Nano', 'Micro', 'Macro', 'Meta', 'Neo', 'Next', 'Future', 'Advanced', 'Modern', 'High', 'Fast', 'Quick', 'Instant', 'Real', 'Live', 'Active', 'Dynamic', 'Interactive', 'Responsive', 'Adaptive', 'Intelligent', 'Automated', 'Optimized'],
            'nouns' => ['Bot', 'AI', 'Code', 'Data', 'Byte', 'Bit', 'Pixel', 'Node', 'Core', 'Chip', 'Circuit', 'System', 'Network', 'Cloud', 'Server', 'Database', 'Algorithm', 'Function', 'Method', 'Class', 'Object', 'Array', 'String', 'Integer', 'Boolean', 'Variable', 'Constant', 'Interface', 'Framework', 'Library']
        ],
        'space' => [
            'adjectives' => ['Cosmic', 'Stellar', 'Galactic', 'Solar', 'Lunar', 'Astral', 'Celestial', 'Orbital', 'Nebula', 'Quantum', 'Infinite', 'Eternal', 'Universal', 'Interstellar', 'Supernova', 'Meteor', 'Comet', 'Asteroid', 'Plasma', 'Photon', 'Neutron', 'Proton', 'Electron', 'Atomic', 'Nuclear', 'Fusion', 'Zero', 'Dark', 'Bright', 'Distant'],
            'nouns' => ['Star', 'Planet', 'Moon', 'Sun', 'Galaxy', 'Nebula', 'Cosmos', 'Universe', 'Orbit', 'Satellite', 'Rocket', 'Shuttle', 'Station', 'Explorer', 'Voyager', 'Pioneer', 'Discovery', 'Mission', 'Launch', 'Flight', 'Journey', 'Quest', 'Adventure', 'Expedition', 'Traveler', 'Navigator', 'Pilot', 'Commander', 'Captain', 'Admiral']
        ]
    ];

    // General adjectives (colors, shapes, sizes, etc.)
    private $general_adjectives = [
        // Colors
        'Red', 'Blue', 'Green', 'Yellow', 'Purple', 'Orange', 'Pink', 'Black', 'White', 'Gray', 'Brown', 'Silver', 'Gold', 'Violet', 'Crimson', 'Azure', 'Emerald', 'Amber', 'Ruby', 'Sapphire',
        // Shapes & Sizes
        'Round', 'Square', 'Big', 'Small', 'Tiny', 'Huge', 'Giant', 'Mini', 'Mega', 'Micro', 'Large', 'Little', 'Tall', 'Short', 'Wide', 'Thin', 'Thick', 'Long', 'Curved', 'Straight',
        // Textures & Materials
        'Smooth', 'Rough', 'Soft', 'Hard', 'Fluffy', 'Silky', 'Fuzzy', 'Glossy', 'Matte', 'Shiny', 'Metallic', 'Wooden', 'Stone', 'Crystal', 'Glass', 'Steel', 'Iron', 'Velvet', 'Marble', 'Diamond',
        // Temperatures & Weather
        'Hot', 'Cold', 'Warm', 'Cool', 'Frozen', 'Burning', 'Icy', 'Fiery', 'Sunny', 'Cloudy', 'Stormy', 'Misty', 'Foggy', 'Windy', 'Rainy', 'Snowy', 'Frosty', 'Blazing', 'Chilly', 'Tropical',
        // Time & Speed
        'Fast', 'Slow', 'Quick', 'Rapid', 'Swift', 'Speedy', 'Instant', 'Ancient', 'Modern', 'Old', 'New', 'Young', 'Fresh', 'Vintage', 'Classic', 'Retro', 'Future', 'Past', 'Present', 'Eternal',
        // Qualities & States
        'Perfect', 'Pure', 'Simple', 'Complex', 'Easy', 'Tough', 'Gentle', 'Bold', 'Calm', 'Active', 'Quiet', 'Loud', 'Bright', 'Dark', 'Light', 'Heavy', 'Empty', 'Full', 'Open', 'Closed'
    ];

    // Numbers and symbols for additional options
    private $numbers = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '69', '99', '123', '777', '2024', '2025'];
    private $symbols = ['_', '-', '.', 'X', 'Z'];

    public function generateUsernames($options = []) {
        // Default options
        $defaults = [
            'theme' => 'gaming',
            'use_case' => 'gaming',
            'min_length' => 6,
            'max_length' => 20,
            'count' => 10,
            'include_numbers' => false,
            'include_symbols' => false,
            'capitalize' => true,
            'avoid_repetition' => true,
            'use_all_adjectives' => false,
            'use_general_adjectives' => false,
            'custom_words' => []
        ];

        $options = array_merge($defaults, $options);
        $usernames = [];
        $used_combinations = [];

        // Get word lists
        $theme_words = $this->themes[$options['theme']] ?? $this->themes['gaming'];
        
        // Handle "Use All Adjectives" option
        if ($options['use_all_adjectives']) {
            // Combine all adjectives from all themes
            $adjectives = [];
            foreach ($this->themes as $theme_data) {
                $adjectives = array_merge($adjectives, $theme_data['adjectives']);
            }
            // Remove duplicates and keep only unique adjectives
            $adjectives = array_unique($adjectives);
            // Use nouns from selected theme only
            $nouns = $theme_words['nouns'];
        } else {
            // Use both adjectives and nouns from selected theme
            $adjectives = $theme_words['adjectives'];
            $nouns = $theme_words['nouns'];
        }

        // Add general adjectives if requested
        if ($options['use_general_adjectives']) {
            $adjectives = array_merge($adjectives, $this->general_adjectives);
            // Remove duplicates
            $adjectives = array_unique($adjectives);
        }

        // Add custom words if provided
        if (!empty($options['custom_words'])) {
            $custom_array = array_map('trim', explode(',', $options['custom_words']));
            $adjectives = array_merge($adjectives, $custom_array);
            $nouns = array_merge($nouns, $custom_array);
        }

        $attempts = 0;
        $max_attempts = $options['count'] * 10; // Prevent infinite loops

        while (count($usernames) < $options['count'] && $attempts < $max_attempts) {
            $attempts++;
            
            // Generate base username
            $adjective = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            
            // Create combination key for avoiding repetition
            $combination_key = strtolower($adjective . '_' . $noun);
            
            if ($options['avoid_repetition'] && in_array($combination_key, $used_combinations)) {
                continue;
            }

            $username = $adjective . $noun;

            // Add numbers if requested
            if ($options['include_numbers']) {
                if (rand(0, 1)) { // 50% chance
                    $username .= $this->numbers[array_rand($this->numbers)];
                }
            }

            // Add symbols if requested
            if ($options['include_symbols']) {
                if (rand(0, 2) === 0) { // 33% chance
                    $symbol = $this->symbols[array_rand($this->symbols)];
                    $position = rand(0, 1);
                    if ($position === 0) {
                        $username = $adjective . $symbol . $noun;
                    } else {
                        $username = $username . $symbol;
                    }
                }
            }

            // Apply capitalization
            if (!$options['capitalize']) {
                $username = strtolower($username);
            }

            // Check length constraints
            if (strlen($username) >= $options['min_length'] && strlen($username) <= $options['max_length']) {
                $usernames[] = $username;
                if ($options['avoid_repetition']) {
                    $used_combinations[] = $combination_key;
                }
            }
        }

        return $usernames;
    }

    public function getAvailableThemes() {
        return array_keys($this->themes);
    }

    public function validateOptions($options) {
        $errors = [];

        if (isset($options['min_length']) && (!is_numeric($options['min_length']) || $options['min_length'] < 3)) {
            $errors[] = 'Minimum length must be at least 3 characters';
        }

        if (isset($options['max_length']) && (!is_numeric($options['max_length']) || $options['max_length'] > 50)) {
            $errors[] = 'Maximum length cannot exceed 50 characters';
        }

        if (isset($options['min_length']) && isset($options['max_length']) && $options['min_length'] > $options['max_length']) {
            $errors[] = 'Minimum length cannot be greater than maximum length';
        }

        if (isset($options['count']) && (!is_numeric($options['count']) || $options['count'] < 1 || $options['count'] > 50)) {
            $errors[] = 'Count must be between 1 and 50';
        }

        if (isset($options['theme']) && !array_key_exists($options['theme'], $this->themes)) {
            $errors[] = 'Invalid theme selected';
        }

        return $errors;
    }
}

// Initialize generator
$generator = new UsernameGenerator();

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Support both JSON POST and GET parameters
$options = [];
$options['theme'] = $input['theme'] ?? $_GET['theme'] ?? 'gaming';
$options['use_case'] = $input['use_case'] ?? $_GET['use_case'] ?? 'gaming';
$options['min_length'] = intval($input['min_length'] ?? $_GET['min_length'] ?? 6);
$options['max_length'] = intval($input['max_length'] ?? $_GET['max_length'] ?? 20);
$options['count'] = intval($input['count'] ?? $_GET['count'] ?? 10);
$options['include_numbers'] = filter_var($input['include_numbers'] ?? $_GET['include_numbers'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['include_symbols'] = filter_var($input['include_symbols'] ?? $_GET['include_symbols'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['capitalize'] = filter_var($input['capitalize'] ?? $_GET['capitalize'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['avoid_repetition'] = filter_var($input['avoid_repetition'] ?? $_GET['avoid_repetition'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['use_all_adjectives'] = filter_var($input['use_all_adjectives'] ?? $_GET['use_all_adjectives'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['use_general_adjectives'] = filter_var($input['use_general_adjectives'] ?? $_GET['use_general_adjectives'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['custom_words'] = $input['custom_words'] ?? $_GET['custom_words'] ?? '';

// Handle special request for available themes
if (isset($_GET['action']) && $_GET['action'] === 'themes') {
    echo json_encode([
        'success' => true,
        'themes' => $generator->getAvailableThemes(),
        'theme_descriptions' => [
            'gaming' => 'Perfect for gaming platforms, esports, and online games',
            'professional' => 'Suitable for business, LinkedIn, and professional networks',
            'fun' => 'Playful and cheerful usernames for social media',
            'nature' => 'Nature-inspired usernames with organic feel',
            'tech' => 'Technology and programming themed usernames',
            'space' => 'Cosmic and space exploration themed usernames'
        ]
    ]);
    exit();
}

// Validate options
$validation_errors = $generator->validateOptions($options);

if (!empty($validation_errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Validation failed',
        'messages' => $validation_errors
    ]);
    exit();
}

// Generate usernames
try {
    $usernames = $generator->generateUsernames($options);
    
    if (empty($usernames)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'No usernames could be generated',
            'message' => 'Try adjusting your length constraints or other options'
        ]);
        exit();
    }

    // Return successful response
    $response = [
        'success' => true,
        'data' => [
            'usernames' => $usernames,
            'count' => count($usernames),
            'options_used' => $options
        ],
        'generation_info' => [
            'theme' => $options['theme'],
            'use_case' => $options['use_case'],
            'length_range' => $options['min_length'] . '-' . $options['max_length'] . ' characters',
            'features' => [
                'numbers' => $options['include_numbers'] ? 'included' : 'excluded',
                'symbols' => $options['include_symbols'] ? 'included' : 'excluded',
                'capitalization' => $options['capitalize'] ? 'enabled' : 'disabled'
            ]
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => 'Failed to generate usernames'
    ]);
}
?>
