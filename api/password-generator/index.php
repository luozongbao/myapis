<?php
/**
 * Password Generator — Generate + Analyze
 *
 * @author MyAPIs Team
 * @since  2.5.0 (refactor — ISSUE-013, ISSUE-024)
 */

declare(strict_types=1);

require_once __DIR__ . '/../_includes/Cors.php';
require_once __DIR__ . '/../_includes/ErrorHandler.php';
require_once __DIR__ . '/../_includes/Validator.php';

Cors::handle();
ErrorHandler::register();

final class PasswordGenerator
{
    private const LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const NUMBERS   = '0123456789';
    private const SYMBOLS   = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    private const AMBIGUOUS = '0O1lI|`';

    /**
     * @param array<string,mixed> $options
     * @return array<int,string>
     */
    public function generatePasswords(array $options): array
    {
        $charset = $this->buildCharset($options);
        if ($charset === '') {
            throw new ValidationException('No character types selected');
        }

        $passwords = [];
        for ($i = 0; $i < (int) $options['count']; $i++) {
            $length = $options['min_length'] === $options['max_length']
                ? (int) $options['min_length']
                : random_int((int) $options['min_length'], (int) $options['max_length']);
            $password = $this->generateSinglePassword($charset, $length, $options);
            if ($password !== '') {
                $passwords[] = $password;
            }
        }
        return $passwords;
    }

    /**
     * @param array<string,mixed> $options
     */
    private function buildCharset(array $options): string
    {
        $charset = '';
        if (!empty($options['include_lowercase'])) {
            $charset .= self::LOWERCASE;
        }
        if (!empty($options['include_uppercase'])) {
            $charset .= self::UPPERCASE;
        }
        if (!empty($options['include_numbers'])) {
            $charset .= self::NUMBERS;
        }
        if (!empty($options['include_symbols'])) {
            $charset .= $options['custom_symbols'] !== '' ? (string) $options['custom_symbols'] : self::SYMBOLS;
        }
        if (!empty($options['exclude_ambiguous']) && $charset !== '') {
            $charset = str_replace(str_split(self::AMBIGUOUS), '', $charset);
        }
        return $charset;
    }

    /**
     * @param array<string,mixed> $options
     */
    private function generateSinglePassword(string $charset, int $length, array $options): string
    {
        $password  = '';
        $used      = [];
        $attempts  = 0;
        $maxTry    = 1000;

        while (strlen($password) < $length && $attempts < $maxTry) {
            $attempts++;
            $char = $charset[random_int(0, strlen($charset) - 1)];
            if (!empty($options['no_repeated_chars']) && in_array($char, $used, true)) {
                continue;
            }
            $password .= $char;
            if (!empty($options['no_repeated_chars'])) {
                $used[] = $char;
            }
        }

        if (!empty($options['must_include_each_type'])) {
            $password = $this->ensureRequiredTypes($password, $options);
        }
        return $password;
    }

    /**
     * @param array<string,mixed> $options
     */
    private function ensureRequiredTypes(string $password, array $options): string
    {
        $arr    = str_split($password);
        $needed = [];

        if (!empty($options['include_lowercase']) && !preg_match('/[a-z]/', $password)) {
            $needed[] = self::LOWERCASE[random_int(0, strlen(self::LOWERCASE) - 1)];
        }
        if (!empty($options['include_uppercase']) && !preg_match('/[A-Z]/', $password)) {
            $needed[] = self::UPPERCASE[random_int(0, strlen(self::UPPERCASE) - 1)];
        }
        if (!empty($options['include_numbers']) && !preg_match('/[0-9]/', $password)) {
            $needed[] = self::NUMBERS[random_int(0, strlen(self::NUMBERS) - 1)];
        }
        if (!empty($options['include_symbols']) && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $set = $options['custom_symbols'] !== '' ? (string) $options['custom_symbols'] : self::SYMBOLS;
            $needed[] = $set[random_int(0, strlen($set) - 1)];
        }

        foreach ($needed as $c) {
            if (count($arr) > 0) {
                $arr[random_int(0, count($arr) - 1)] = $c;
            }
        }
        return implode('', $arr);
    }

    /**
     * @return array<string,mixed>
     */
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
        $score += $analysis['length']  >= 8  ? 1 : 0;
        $score += $analysis['length']  >= 12 ? 1 : 0;
        $score += $analysis['has_lowercase'] ? 1 : 0;
        $score += $analysis['has_uppercase'] ? 1 : 0;
        $score += $analysis['has_numbers']   ? 1 : 0;
        $score += $analysis['has_symbols']   ? 2 : 0;

        $analysis['strength'] = match (true) {
            $score >= 7 => 'very strong',
            $score >= 5 => 'strong',
            $score >= 3 => 'medium',
            default     => 'weak',
        };
        $analysis['score'] = $score;
        return $analysis;
    }

    /**
     * @param array<string,mixed> $options
     * @return array<int,string>
     */
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
            $options['include_lowercase'] ?? null,
            $options['include_uppercase'] ?? null,
            $options['include_numbers']   ?? null,
            $options['include_symbols']   ?? null,
        ]);
        if (empty($hasType)) {
            $errors[] = 'At least one character type must be selected';
        }
        return $errors;
    }

    /**
     * @param array<string,mixed> $analysis
     * @return array<int,string>
     */
    public function getStrengthTips(array $analysis): array
    {
        $tips = [];
        if ($analysis['length'] < 8)  $tips[] = 'Use at least 8 characters for better security';
        if ($analysis['length'] < 12) $tips[] = 'Consider using 12+ characters for stronger passwords';
        if (!$analysis['has_lowercase']) $tips[] = 'Include lowercase letters (a-z)';
        if (!$analysis['has_uppercase']) $tips[] = 'Include uppercase letters (A-Z)';
        if (!$analysis['has_numbers'])   $tips[] = 'Include numbers (0-9)';
        if (!$analysis['has_symbols'])   $tips[] = 'Include special characters (!@#$%^&*)';
        if (empty($tips))                $tips[] = 'Great! Your password meets all security recommendations';
        return $tips;
    }
}

ErrorHandler::wrap(static function (): void {
    $generator = new PasswordGenerator();
    $input     = Validator::readInput();

    // Special action: analyze an existing password
    if (isset($_GET['action']) && $_GET['action'] === 'analyze') {
        $password = (string) ($input['password'] ?? '');
        if ($password === '') {
            throw (new ValidationException('Password is required for analysis'));
        }
        $analysis = $generator->analyzePassword($password);
        ErrorHandler::success([
            'analysis' => $analysis,
            'tips'     => $generator->getStrengthTips($analysis),
        ]);
    }

    // Build options with defaults
    $options = [
        'min_length'            => 8,
        'max_length'            => 16,
        'count'                 => 5,
        'include_lowercase'     => true,
        'include_uppercase'     => true,
        'include_numbers'       => true,
        'include_symbols'       => false,
        'exclude_ambiguous'     => false,
        'no_repeated_chars'     => false,
        'must_include_each_type'=> true,
        'custom_symbols'        => '',
    ];
    foreach ($options as $key => $default) {
        if (is_bool($default)) {
            $options[$key] = filter_var($input[$key] ?? $default, FILTER_VALIDATE_BOOLEAN);
        } elseif (is_int($default)) {
            $options[$key] = (int) ($input[$key] ?? $default);
        } else {
            $options[$key] = (string) ($input[$key] ?? $default);
        }
    }

    $errors = $generator->validateOptions($options);
    if (!empty($errors)) {
        throw (new ValidationException(implode('; ', $errors)))->withDetails(['errors' => $errors]);
    }

    $passwords = $generator->generatePasswords($options);
    if (empty($passwords)) {
        throw (new ValidationException('No passwords could be generated'))->withDetails([
            'hint' => 'Try adjusting your length/range requirements',
        ]);
    }

    $passwordData = [];
    foreach ($passwords as $pw) {
        $a = $generator->analyzePassword($pw);
        $passwordData[] = [
            'password' => $pw,
            'length'   => $a['length'],
            'strength' => $a['strength'],
            'score'    => $a['score'],
        ];
    }

    ErrorHandler::success([
        'passwords'        => $passwordData,
        'count'            => count($passwords),
        'options_used'     => $options,
        'generation_info'  => [
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
                'must_include_each_type' => $options['must_include_each_type']? 'enabled' : 'disabled',
            ],
        ],
    ]);
});
