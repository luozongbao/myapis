<?php
/**
 * Password Generator API
 *
 * GET/POST endpoints:
 *   - (default)            Generate passwords
 *   - ?action=analyze      Analyse a single password
 *
 * Backward-compatible response shapes are preserved exactly.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

api_send_headers();
if (api_handle_preflight()) {
    exit;
}
api_register_exception_handler();

/**
 * Pure password generation / analysis logic.
 * No HTTP concerns live here so the class is trivially unit-testable.
 */
class PasswordGenerator
{
    private string $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    private string $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private string $numbers   = '0123456789';
    private string $symbols   = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    private string $ambiguous = '0O1lI|`';

    public function generatePasswords(array $options = []): array
    {
        $defaults = [
            'min_length'             => 8,
            'max_length'             => 16,
            'count'                  => 5,
            'include_lowercase'      => true,
            'include_uppercase'      => true,
            'include_numbers'        => true,
            'include_symbols'        => false,
            'exclude_ambiguous'      => false,
            'no_repeated_chars'      => false,
            'must_include_each_type' => true,
            'custom_symbols'         => '',
        ];
        $options = array_merge($defaults, $options);

        $charset = $this->buildCharset($options);
        if ($charset === '') {
            throw new RuntimeException('No character types selected');
        }

        $passwords = [];
        for ($i = 0; $i < $options['count']; $i++) {
            $length   = $this->getRandomLength($options['min_length'], $options['max_length']);
            $password = $this->generateSinglePassword($charset, $length, $options);
            if ($password !== null && $password !== '') {
                $passwords[] = $password;
            }
        }
        return $passwords;
    }

    private function buildCharset(array $options): string
    {
        $charset = '';
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
            $charset .= !empty($options['custom_symbols']) ? $options['custom_symbols'] : $this->symbols;
        }
        if ($options['exclude_ambiguous']) {
            $charset = str_replace(str_split($this->ambiguous), '', $charset);
        }
        return $charset;
    }

    private function getRandomLength(int $min, int $max): int
    {
        return $min === $max ? $min : random_int($min, $max);
    }

    private function generateSinglePassword(string $charset, int $length, array $options): ?string
    {
        $password    = '';
        $used        = [];
        $attempts    = 0;
        $maxAttempts = 1000;

        while (strlen($password) < $length && $attempts < $maxAttempts) {
            $attempts++;
            $char = $charset[random_int(0, strlen($charset) - 1)];
            if ($options['no_repeated_chars'] && in_array($char, $used, true)) {
                continue;
            }
            $password .= $char;
            if ($options['no_repeated_chars']) {
                $used[] = $char;
            }
        }

        if ($options['must_include_each_type']) {
            $password = $this->ensureRequiredTypes($password, $options);
        }
        return $password !== '' ? $password : null;
    }

    private function ensureRequiredTypes(string $password, array $options): string
    {
        $required      = [];
        $passwordArray = str_split($password);

        if ($options['include_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $required[] = $this->lowercase[random_int(0, strlen($this->lowercase) - 1)];
        }
        if ($options['include_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $required[] = $this->uppercase[random_int(0, strlen($this->uppercase) - 1)];
        }
        if ($options['include_numbers'] && !preg_match('/[0-9]/', $password)) {
            $required[] = $this->numbers[random_int(0, strlen($this->numbers) - 1)];
        }
        if ($options['include_symbols'] && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $symbolSet = !empty($options['custom_symbols']) ? $options['custom_symbols'] : $this->symbols;
            $required[] = $symbolSet[random_int(0, strlen($symbolSet) - 1)];
        }

        foreach ($required as $reqChar) {
            if (count($passwordArray) > 0) {
                $pos = random_int(0, count($passwordArray) - 1);
                $passwordArray[$pos] = $reqChar;
            }
        }

        return implode('', $passwordArray);
    }

    public function analyzePassword(string $password): array
    {
        $analysis = [
            'length'        => strlen($password),
            'has_lowercase' => (bool) preg_match('/[a-z]/', $password),
            'has_uppercase' => (bool) preg_match('/[A-Z]/', $password),
            'has_numbers'   => (bool) preg_match('/[0-9]/', $password),
            'has_symbols'   => (bool) preg_match('/[^a-zA-Z0-9]/', $password),
            'strength'      => 'weak',
        ];

        $score  = 0;
        $score += $analysis['length'] >= 8  ? 1 : 0;
        $score += $analysis['length'] >= 12 ? 1 : 0;
        $score += $analysis['has_lowercase'] ? 1 : 0;
        $score += $analysis['has_uppercase'] ? 1 : 0;
        $score += $analysis['has_numbers']   ? 1 : 0;
        $score += $analysis['has_symbols']   ? 2 : 0;

        if ($score >= 7) {
            $analysis['strength'] = 'very strong';
        } elseif ($score >= 5) {
            $analysis['strength'] = 'strong';
        } elseif ($score >= 3) {
            $analysis['strength'] = 'medium';
        }
        $analysis['score'] = $score;

        return $analysis;
    }

    public function getStrengthTips(array $analysis): array
    {
        $tips = [];
        if ($analysis['length'] < 8)  { $tips[] = 'Use at least 8 characters for better security'; }
        if ($analysis['length'] < 12) { $tips[] = 'Consider using 12+ characters for stronger passwords'; }
        if (!$analysis['has_lowercase']) { $tips[] = 'Include lowercase letters (a-z)'; }
        if (!$analysis['has_uppercase']) { $tips[] = 'Include uppercase letters (A-Z)'; }
        if (!$analysis['has_numbers'])   { $tips[] = 'Include numbers (0-9)'; }
        if (!$analysis['has_symbols'])   { $tips[] = 'Include special characters (!@#$%^&*)'; }
        if (empty($tips)) {
            $tips[] = 'Great! Your password meets all security recommendations';
        }
        return $tips;
    }

    public function validateOptions(array $options): array
    {
        $errors = [];

        if (isset($options['min_length']) && (!is_numeric($options['min_length']) || $options['min_length'] < 1)) {
            $errors[] = 'Minimum length must be at least 1 character';
        }
        if (isset($options['max_length']) && (!is_numeric($options['max_length']) || $options['max_length'] > 128)) {
            $errors[] = 'Maximum length cannot exceed 128 characters';
        }
        if (isset($options['min_length'], $options['max_length']) && $options['min_length'] > $options['max_length']) {
            $errors[] = 'Minimum length cannot be greater than maximum length';
        }
        if (isset($options['count']) && (!is_numeric($options['count']) || $options['count'] < 1 || $options['count'] > 100)) {
            $errors[] = 'Count must be between 1 and 100';
        }

        $hasType = array_filter([
            $options['include_lowercase'] ?? false,
            $options['include_uppercase'] ?? false,
            $options['include_numbers']   ?? false,
            $options['include_symbols']   ?? false,
        ]);
        if (empty($hasType)) {
            $errors[] = 'At least one character type must be selected';
        }
        return $errors;
    }
}

// ---------------------------------------------------------------------------
// HTTP layer
// ---------------------------------------------------------------------------
$generator = new PasswordGenerator();

$options = [
    'min_length'             => api_int(api_input('min_length'), 8),
    'max_length'             => api_int(api_input('max_length'), 16),
    'count'                  => api_int(api_input('count'), 5),
    'include_lowercase'      => api_bool(api_input('include_lowercase'), true),
    'include_uppercase'      => api_bool(api_input('include_uppercase'), true),
    'include_numbers'        => api_bool(api_input('include_numbers'), true),
    'include_symbols'        => api_bool(api_input('include_symbols'), false),
    'exclude_ambiguous'      => api_bool(api_input('exclude_ambiguous'), false),
    'no_repeated_chars'      => api_bool(api_input('no_repeated_chars'), false),
    'must_include_each_type' => api_bool(api_input('must_include_each_type'), true),
    'custom_symbols'         => (string) (api_input('custom_symbols') ?? ''),
];

// Analyse action
if ((api_input('action')) === 'analyze') {
    $password = (string) (api_input('password') ?? '');
    if ($password === '') {
        api_error('Password is required for analysis', 400);
    }

    $analysis = $generator->analyzePassword($password);
    api_json([
        'success'   => true,
        'analysis'  => $analysis,
        'tips'      => $generator->getStrengthTips($analysis),
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
}

// Validate
$errors = $generator->validateOptions($options);
if (!empty($errors)) {
    api_error('Validation failed', 400, ['messages' => $errors]);
}

// Generate
try {
    $passwords = $generator->generatePasswords($options);
    if (empty($passwords)) {
        api_error('No passwords could be generated', 400, [
            'message' => 'Try adjusting your requirements',
        ]);
    }

    $passwordData = [];
    foreach ($passwords as $password) {
        $a = $generator->analyzePassword($password);
        $passwordData[] = [
            'password' => $password,
            'length'   => $a['length'],
            'strength' => $a['strength'],
            'score'    => $a['score'],
        ];
    }

    api_json([
        'success'         => true,
        'data'            => [
            'passwords'    => $passwordData,
            'count'        => count($passwords),
            'options_used' => $options,
        ],
        'generation_info' => [
            'length_range'     => $options['min_length'] . '-' . $options['max_length'] . ' characters',
            'character_types'  => [
                'lowercase' => $options['include_lowercase'] ? 'included' : 'excluded',
                'uppercase' => $options['include_uppercase'] ? 'included' : 'excluded',
                'numbers'   => $options['include_numbers']   ? 'included' : 'excluded',
                'symbols'   => $options['include_symbols']   ? 'included' : 'excluded',
            ],
            'security_options' => [
                'exclude_ambiguous'      => $options['exclude_ambiguous']      ? 'enabled' : 'disabled',
                'no_repeated_chars'      => $options['no_repeated_chars']      ? 'enabled' : 'disabled',
                'must_include_each_type' => $options['must_include_each_type'] ? 'enabled' : 'disabled',
            ],
        ],
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
} catch (Throwable $e) {
    api_error('Internal server error', 500, ['message' => $e->getMessage()]);
}