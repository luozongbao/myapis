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

class PasswordGenerator {
    
    // Character sets
    private $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    private $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $numbers = '0123456789';
    private $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    private $ambiguous = '0O1lI|`'; // Characters that might be confused
    
    public function generatePasswords($options = []) {
        // Default options
        $defaults = [
            'min_length' => 8,
            'max_length' => 16,
            'count' => 5,
            'include_lowercase' => true,
            'include_uppercase' => true,
            'include_numbers' => true,
            'include_symbols' => false,
            'exclude_ambiguous' => false,
            'no_repeated_chars' => false,
            'must_include_each_type' => true,
            'custom_symbols' => ''
        ];

        $options = array_merge($defaults, $options);
        $passwords = [];

        // Build character set
        $charset = $this->buildCharset($options);
        
        if (empty($charset)) {
            throw new Exception('No character types selected');
        }

        // Generate requested number of passwords
        for ($i = 0; $i < $options['count']; $i++) {
            $length = $this->getRandomLength($options['min_length'], $options['max_length']);
            $password = $this->generateSinglePassword($charset, $length, $options);
            
            if ($password) {
                $passwords[] = $password;
            }
        }

        return $passwords;
    }

    private function buildCharset($options) {
        $charset = '';
        
        // Add character types based on options
        if ($options['include_lowercase']) {
            $charset .= $this->lowercase;
        }
        
        if ($options['include_uppercase']) {
            $charset .= $this->uppercase;
        }
        
        if ($options['include_numbers']) {
            $charset .= $this->numbers;
        }
        
        if ($options['include_symbols']) {
            if (!empty($options['custom_symbols'])) {
                $charset .= $options['custom_symbols'];
            } else {
                $charset .= $this->symbols;
            }
        }

        // Remove ambiguous characters if requested
        if ($options['exclude_ambiguous']) {
            $charset = str_replace(str_split($this->ambiguous), '', $charset);
        }

        return $charset;
    }

    private function getRandomLength($min, $max) {
        if ($min === $max) {
            return $min;
        }
        return random_int($min, $max);
    }

    private function generateSinglePassword($charset, $length, $options) {
        $password = '';
        $used_chars = [];
        $attempts = 0;
        $max_attempts = 1000;

        while (strlen($password) < $length && $attempts < $max_attempts) {
            $attempts++;
            $char = $charset[random_int(0, strlen($charset) - 1)];
            
            // Check for repeated characters if option is enabled
            if ($options['no_repeated_chars'] && in_array($char, $used_chars)) {
                continue;
            }
            
            $password .= $char;
            if ($options['no_repeated_chars']) {
                $used_chars[] = $char;
            }
        }

        // Ensure password meets requirements
        if ($options['must_include_each_type']) {
            $password = $this->ensureRequiredTypes($password, $options);
        }

        return $password;
    }

    private function ensureRequiredTypes($password, $options) {
        $required_chars = [];
        $password_array = str_split($password);

        // Check what types are required and missing
        if ($options['include_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $required_chars[] = $this->lowercase[random_int(0, strlen($this->lowercase) - 1)];
        }
        
        if ($options['include_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $required_chars[] = $this->uppercase[random_int(0, strlen($this->uppercase) - 1)];
        }
        
        if ($options['include_numbers'] && !preg_match('/[0-9]/', $password)) {
            $required_chars[] = $this->numbers[random_int(0, strlen($this->numbers) - 1)];
        }
        
        if ($options['include_symbols'] && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $symbol_set = !empty($options['custom_symbols']) ? $options['custom_symbols'] : $this->symbols;
            $required_chars[] = $symbol_set[random_int(0, strlen($symbol_set) - 1)];
        }

        // Replace random positions with required characters
        foreach ($required_chars as $req_char) {
            if (count($password_array) > 0) {
                $random_position = random_int(0, count($password_array) - 1);
                $password_array[$random_position] = $req_char;
            }
        }

        return implode('', $password_array);
    }

    public function analyzePassword($password) {
        $analysis = [
            'length' => strlen($password),
            'has_lowercase' => preg_match('/[a-z]/', $password) ? true : false,
            'has_uppercase' => preg_match('/[A-Z]/', $password) ? true : false,
            'has_numbers' => preg_match('/[0-9]/', $password) ? true : false,
            'has_symbols' => preg_match('/[^a-zA-Z0-9]/', $password) ? true : false,
            'strength' => 'weak'
        ];

        // Calculate strength score
        $score = 0;
        $score += $analysis['length'] >= 8 ? 1 : 0;
        $score += $analysis['length'] >= 12 ? 1 : 0;
        $score += $analysis['has_lowercase'] ? 1 : 0;
        $score += $analysis['has_uppercase'] ? 1 : 0;
        $score += $analysis['has_numbers'] ? 1 : 0;
        $score += $analysis['has_symbols'] ? 2 : 0;

        // Determine strength
        if ($score >= 7) {
            $analysis['strength'] = 'very strong';
        } elseif ($score >= 5) {
            $analysis['strength'] = 'strong';
        } elseif ($score >= 3) {
            $analysis['strength'] = 'medium';
        } else {
            $analysis['strength'] = 'weak';
        }

        $analysis['score'] = $score;
        return $analysis;
    }

    public function validateOptions($options) {
        $errors = [];

        if (isset($options['min_length']) && (!is_numeric($options['min_length']) || $options['min_length'] < 1)) {
            $errors[] = 'Minimum length must be at least 1 character';
        }

        if (isset($options['max_length']) && (!is_numeric($options['max_length']) || $options['max_length'] > 128)) {
            $errors[] = 'Maximum length cannot exceed 128 characters';
        }

        if (isset($options['min_length']) && isset($options['max_length']) && $options['min_length'] > $options['max_length']) {
            $errors[] = 'Minimum length cannot be greater than maximum length';
        }

        if (isset($options['count']) && (!is_numeric($options['count']) || $options['count'] < 1 || $options['count'] > 100)) {
            $errors[] = 'Count must be between 1 and 100';
        }

        // Check if at least one character type is selected
        $has_char_type = false;
        if (!empty($options['include_lowercase']) || 
            !empty($options['include_uppercase']) || 
            !empty($options['include_numbers']) || 
            !empty($options['include_symbols'])) {
            $has_char_type = true;
        }
        
        if (!$has_char_type) {
            $errors[] = 'At least one character type must be selected';
        }

        return $errors;
    }

    public function getStrengthTips($analysis) {
        $tips = [];
        
        if ($analysis['length'] < 8) {
            $tips[] = 'Use at least 8 characters for better security';
        }
        
        if ($analysis['length'] < 12) {
            $tips[] = 'Consider using 12+ characters for stronger passwords';
        }
        
        if (!$analysis['has_lowercase']) {
            $tips[] = 'Include lowercase letters (a-z)';
        }
        
        if (!$analysis['has_uppercase']) {
            $tips[] = 'Include uppercase letters (A-Z)';
        }
        
        if (!$analysis['has_numbers']) {
            $tips[] = 'Include numbers (0-9)';
        }
        
        if (!$analysis['has_symbols']) {
            $tips[] = 'Include special characters (!@#$%^&*)';
        }

        if (empty($tips)) {
            $tips[] = 'Great! Your password meets all security recommendations';
        }

        return $tips;
    }
}

// Initialize generator
$generator = new PasswordGenerator();

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Support both JSON POST and GET parameters
$options = [];
$options['min_length'] = intval($input['min_length'] ?? $_GET['min_length'] ?? 8);
$options['max_length'] = intval($input['max_length'] ?? $_GET['max_length'] ?? 16);
$options['count'] = intval($input['count'] ?? $_GET['count'] ?? 5);
$options['include_lowercase'] = filter_var($input['include_lowercase'] ?? $_GET['include_lowercase'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['include_uppercase'] = filter_var($input['include_uppercase'] ?? $_GET['include_uppercase'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['include_numbers'] = filter_var($input['include_numbers'] ?? $_GET['include_numbers'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['include_symbols'] = filter_var($input['include_symbols'] ?? $_GET['include_symbols'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['exclude_ambiguous'] = filter_var($input['exclude_ambiguous'] ?? $_GET['exclude_ambiguous'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['no_repeated_chars'] = filter_var($input['no_repeated_chars'] ?? $_GET['no_repeated_chars'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['must_include_each_type'] = filter_var($input['must_include_each_type'] ?? $_GET['must_include_each_type'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['custom_symbols'] = $input['custom_symbols'] ?? $_GET['custom_symbols'] ?? '';

// Handle password analysis request
if (isset($_GET['action']) && $_GET['action'] === 'analyze') {
    $password = $input['password'] ?? $_GET['password'] ?? '';
    
    if (empty($password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Password is required for analysis'
        ]);
        exit();
    }

    $analysis = $generator->analyzePassword($password);
    $tips = $generator->getStrengthTips($analysis);

    echo json_encode([
        'success' => true,
        'analysis' => $analysis,
        'tips' => $tips,
        'timestamp' => date('Y-m-d H:i:s')
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

// Generate passwords
try {
    $passwords = $generator->generatePasswords($options);
    
    if (empty($passwords)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'No passwords could be generated',
            'message' => 'Try adjusting your requirements'
        ]);
        exit();
    }

    // Analyze each password
    $password_data = [];
    foreach ($passwords as $password) {
        $analysis = $generator->analyzePassword($password);
        $password_data[] = [
            'password' => $password,
            'length' => $analysis['length'],
            'strength' => $analysis['strength'],
            'score' => $analysis['score']
        ];
    }

    // Return successful response
    $response = [
        'success' => true,
        'data' => [
            'passwords' => $password_data,
            'count' => count($passwords),
            'options_used' => $options
        ],
        'generation_info' => [
            'length_range' => $options['min_length'] . '-' . $options['max_length'] . ' characters',
            'character_types' => [
                'lowercase' => $options['include_lowercase'] ? 'included' : 'excluded',
                'uppercase' => $options['include_uppercase'] ? 'included' : 'excluded',
                'numbers' => $options['include_numbers'] ? 'included' : 'excluded',
                'symbols' => $options['include_symbols'] ? 'included' : 'excluded'
            ],
            'security_options' => [
                'exclude_ambiguous' => $options['exclude_ambiguous'] ? 'enabled' : 'disabled',
                'no_repeated_chars' => $options['no_repeated_chars'] ? 'enabled' : 'disabled',
                'must_include_each_type' => $options['must_include_each_type'] ? 'enabled' : 'disabled'
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
        'message' => $e->getMessage()
    ]);
}
?>
