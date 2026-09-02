<?php
/**
 * Username Generator API
 *
 * GET/POST endpoints:
 *   - (default)        Generate usernames
 *   - ?action=themes   List available themes + descriptions
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

// Rate-limit this endpoint using the policy defined in api_config.php.
$configs = require __DIR__ . '/../includes/api_config.php';
api_rate_limit('api:username-generator', $configs['username-generator'] ?? null);

/**
 * Pure username generation logic — no HTTP concerns.
 */
class UsernameGenerator
{
    /** @var array<string, array{adjectives: string[], nouns: string[]}> */
    private array $themes;

    public function __construct(array $themes)
    {
        $this->themes = $themes;
    }

    /** @return string[] */
    public function getAvailableThemes(): array
    {
        return array_keys($this->themes);
    }

    public function generateUsernames(array $options = []): array
    {
        $defaults = [
            'themes'              => ['Fantasy'],
            'min_length'          => 6,
            'max_length'          => 20,
            'count'               => 10,
            'include_numbers'     => false,
            'include_symbols'     => false,
            'capitalize'          => true,
            'avoid_repetition'    => true,
            'use_all_adjectives'  => false,
            'use_general_adjectives' => false,
            'custom_words'        => '',
        ];
        $options = array_merge($defaults, $options);

        $themes = $this->resolveThemes($options['themes']);
        $customWords = $this->parseCustomWords($options['custom_words']);

        $usernames = [];
        $seen = [];

        $maxIterations = $options['count'] * 10; // safety
        $iterations = 0;

        while (count($usernames) < $options['count'] && $iterations < $maxIterations) {
            $iterations++;

            $theme = $themes[array_rand($themes)];
            $themeWords = $this->themes[$theme];

            $adjectivePool = $themeWords['adjectives'];
            if ($options['use_all_adjectives']) {
                $adjectivePool = array_merge($adjectivePool, $customWords);
            }
            if ($options['use_general_adjectives']) {
                $adjectivePool = array_merge($adjectivePool, $this->generalAdjectives());
            }

            $nounPool = array_merge($themeWords['nouns'], $customWords);

            if (empty($adjectivePool) || empty($nounPool)) {
                continue;
            }

            $adj = $adjectivePool[array_rand($adjectivePool)];
            $noun = $nounPool[array_rand($nounPool)];

            $username = $this->compose($adj, $noun, $options);

            if (strlen($username) < $options['min_length'] || strlen($username) > $options['max_length']) {
                continue;
            }

            if ($options['avoid_repetition'] && isset($seen[$username])) {
                continue;
            }

            $seen[$username] = true;
            $usernames[] = $username;
        }

        return $usernames;
    }

    /** @param mixed $rawThemes */
    private function resolveThemes($rawThemes): array
    {
        if (!is_array($rawThemes)) {
            $rawThemes = [(string) $rawThemes];
        }
        $resolved = [];
        foreach ($rawThemes as $theme) {
            if (isset($this->themes[$theme])) {
                $resolved[] = $theme;
            }
        }
        return $resolved ?: ['Fantasy'];
    }

    private function parseCustomWords(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        $parts = preg_split('/[,\s]+/', $raw) ?: [];
        return array_values(array_filter($parts, static fn($w) => $w !== ''));
    }

    private function compose(string $adj, string $noun, array $options): string
    {
        $username = $adj . $noun;
        if ($options['capitalize']) {
            $username = ucfirst($username);
        } else {
            $username = strtolower($username);
        }
        if ($options['include_numbers']) {
            $username .= (string) random_int(0, 999);
        }
        if ($options['include_symbols']) {
            $symbols = ['_', '-', '.'];
            $username .= $symbols[array_rand($symbols)];
        }
        return $username;
    }

    /** @return string[] */
    private function generalAdjectives(): array
    {
        return [
            'Big', 'Small', 'Fast', 'Slow', 'Bright', 'Dark', 'Hot', 'Cool',
            'Sharp', 'Smooth', 'Rough', 'Hard', 'Soft', 'New', 'Old',
        ];
    }

    public function validateOptions(array $options): array
    {
        $errors = [];

        if (isset($options['min_length']) && (!is_numeric($options['min_length']) || $options['min_length'] < 1)) {
            $errors[] = 'Minimum length must be at least 1 character';
        }
        if (isset($options['max_length']) && (!is_numeric($options['max_length']) || $options['max_length'] > 50)) {
            $errors[] = 'Maximum length cannot exceed 50 characters';
        }
        if (isset($options['min_length'], $options['max_length']) && $options['min_length'] > $options['max_length']) {
            $errors[] = 'Minimum length cannot be greater than maximum length';
        }
        if (isset($options['count']) && (!is_numeric($options['count']) || $options['count'] < 1 || $options['count'] > 50)) {
            $errors[] = 'Count must be between 1 and 50';
        }

        $themes = is_array($options['themes'] ?? null) ? $options['themes'] : [];
        $invalid = [];
        foreach ($themes as $theme) {
            if (!array_key_exists($theme, $this->themes)) {
                $invalid[] = $theme;
            }
        }
        if (!empty($invalid)) {
            $errors[] = 'Invalid themes: ' . implode(', ', $invalid);
        }

        return $errors;
    }
}

// ---------------------------------------------------------------------------
// HTTP layer
// ---------------------------------------------------------------------------
$themes = require __DIR__ . '/wordlists.php';
$generator = new UsernameGenerator($themes);

// Themes listing action
if (api_input('action') === 'themes') {
    api_json([
        'success'            => true,
        'themes'             => $generator->getAvailableThemes(),
        'theme_descriptions' => [
            'Fantasy'              => 'Epic and mythical usernames for gaming and fantasy lovers',
            'Professional'         => 'Suitable for business, LinkedIn, and professional networks',
            'Science and Space'    => 'Science and space exploration themed usernames',
            'Computer Technology'  => 'Tech and programming themed usernames',
            'Elements and Chemistry' => 'Science-inspired usernames with elements and compounds',
            'Things'               => 'Everyday objects and items themed usernames',
            'Body and Health'      => 'Body parts and health-themed usernames',
            'Nature'               => 'Nature-inspired usernames with plants, animals, and landscapes',
            'Space and Time'       => 'Usernames inspired by concepts of space and time',
        ],
    ]);
}

// Themes resolution (multi-theme via array, comma-string, or single theme)
$themesInput = api_input('themes');
if ($themesInput !== null) {
    if (is_array($themesInput)) {
        $resolved = $themesInput;
    } else {
        $resolved = array_map('trim', explode(',', (string) $themesInput));
    }
} elseif (api_input('theme') !== null) {
    $resolved = [api_input('theme')];
} else {
    $resolved = ['Fantasy'];
}

$options = [
    'themes'                 => $resolved,
    'min_length'             => api_int(api_input('min_length'), 6),
    'max_length'             => api_int(api_input('max_length'), 20),
    'count'                  => api_int(api_input('count'), 10),
    'include_numbers'        => api_bool(api_input('include_numbers'), false),
    'include_symbols'        => api_bool(api_input('include_symbols'), false),
    'capitalize'             => api_bool(api_input('capitalize'), true),
    'avoid_repetition'       => api_bool(api_input('avoid_repetition'), true),
    'use_all_adjectives'     => api_bool(api_input('use_all_adjectives'), false),
    'use_general_adjectives' => api_bool(api_input('use_general_adjectives'), false),
    'custom_words'           => (string) (api_input('custom_words') ?? ''),
];

$errors = $generator->validateOptions($options);
if (!empty($errors)) {
    api_error('Validation failed', 400, ['messages' => $errors]);
}

try {
    $usernames = $generator->generateUsernames($options);
    if (empty($usernames)) {
        api_error('No usernames could be generated', 400, [
            'message' => 'Try adjusting your length constraints or other options',
        ]);
    }

    $response = [
        'success' => true,
        'data'    => [
            'usernames'    => $usernames,
            'count'        => count($usernames),
            'options_used' => $options,
        ],
        'generation_info' => [
            'themes'       => $options['themes'],
            'theme_count'  => count($options['themes']),
            'length_range' => $options['min_length'] . '-' . $options['max_length'] . ' characters',
            'features'     => [
                'numbers'        => $options['include_numbers'] ? 'included' : 'excluded',
                'symbols'        => $options['include_symbols'] ? 'included' : 'excluded',
                'capitalization' => $options['capitalize']    ? 'enabled'  : 'disabled',
            ],
        ],
        'timestamp' => date('Y-m-d H:i:s'),
    ];

    if (count($usernames) < $options['count']) {
        $response['warning'] = [
            'message'    => 'Generated fewer usernames than requested due to restrictive constraints',
            'requested'  => $options['count'],
            'generated'  => count($usernames),
            'suggestion' => 'Try increasing max_length, decreasing min_length, or using more themes for better results',
        ];
    }

    api_json($response);
} catch (Throwable $e) {
    api_error('Internal server error', 500, [
        'message' => 'Failed to generate usernames',
    ]);
}