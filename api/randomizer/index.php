<?php
/**
 * Randomizer API
 *
 * Supported `type` values:
 *   - number  (?min=1&max=100)
 *   - dice    (?sides=6&count=1)
 *   - coin    (?count=1)
 *   - card    (?count=1&with_jokers=0)
 *   - all     (returns one of each)
 *
 * Response shapes are preserved exactly.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

api_send_headers();
if (api_handle_preflight()) {
    exit;
}
api_register_exception_handler();

/**
 * Pure random generation logic.
 */
class RandomGenerator
{
    private array $suits      = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
    private array $ranks      = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
    private array $suitSymbols = [
        'Hearts'   => '♥',
        'Diamonds' => '♦',
        'Clubs'    => '♣',
        'Spades'   => '♠',
    ];

    public function generateNumber(int $min = 1, int $max = 100): array
    {
        if ($min > $max) {
            throw new InvalidArgumentException('Minimum value cannot be greater than maximum value');
        }
        return [
            'type'      => 'number',
            'result'    => random_int($min, $max),
            'range'     => ['min' => $min, 'max' => $max],
            'timestamp' => date('Y-m-d H:i:s'),
            'success'   => true,
        ];
    }

    public function rollDice(int $sides = 6, int $count = 1): array
    {
        if ($sides < 2 || $sides > 100) {
            throw new InvalidArgumentException('Dice sides must be between 2 and 100');
        }
        if ($count < 1 || $count > 10) {
            throw new InvalidArgumentException('Dice count must be between 1 and 10');
        }

        $rolls = [];
        $total = 0;
        for ($i = 0; $i < $count; $i++) {
            $roll = random_int(1, $sides);
            $rolls[] = $roll;
            $total += $roll;
        }

        return [
            'type'         => 'dice',
            'result'       => $count === 1 ? $rolls[0] : $rolls,
            'total'        => $total,
            'dice_config'  => ['sides' => $sides, 'count' => $count],
            'timestamp'    => date('Y-m-d H:i:s'),
            'success'      => true,
        ];
    }

    public function flipCoin(int $count = 1): array
    {
        if ($count < 1 || $count > 10) {
            throw new InvalidArgumentException('Coin count must be between 1 and 10');
        }

        $flips = [];
        $heads = $tails = 0;
        for ($i = 0; $i < $count; $i++) {
            $flip = random_int(0, 1) === 0 ? 'Heads' : 'Tails';
            $flips[] = $flip;
            $flip === 'Heads' ? $heads++ : $tails++;
        }

        return [
            'type'       => 'coin',
            'result'     => $count === 1 ? $flips[0] : $flips,
            'statistics' => ['heads' => $heads, 'tails' => $tails],
            'count'      => $count,
            'timestamp'  => date('Y-m-d H:i:s'),
            'success'    => true,
        ];
    }

    public function drawCard(int $count = 1, bool $withJokers = false): array
    {
        $maxCount = $withJokers ? 54 : 52;
        if ($count < 1 || $count > $maxCount) {
            throw new InvalidArgumentException('Card count must be between 1 and ' . $maxCount);
        }

        $deck = [];
        foreach ($this->suits as $suit) {
            foreach ($this->ranks as $rank) {
                $deck[] = [
                    'rank'    => $rank,
                    'suit'    => $suit,
                    'symbol'  => $this->suitSymbols[$suit],
                    'display' => $rank . ' of ' . $suit,
                    'short'   => $rank . $this->suitSymbols[$suit],
                    'color'   => in_array($suit, ['Hearts', 'Diamonds'], true) ? 'red' : 'black',
                ];
            }
        }
        if ($withJokers) {
            $deck[] = ['rank' => 'Joker', 'suit' => 'Red',   'symbol' => '🃏', 'display' => 'Red Joker',   'short' => 'Red🃏',   'color' => 'red'];
            $deck[] = ['rank' => 'Joker', 'suit' => 'Black', 'symbol' => '🃏', 'display' => 'Black Joker', 'short' => 'Black🃏', 'color' => 'black'];
        }

        shuffle($deck);
        $drawn = array_slice($deck, 0, $count);

        return [
            'type'      => 'card',
            'result'    => $count === 1 ? $drawn[0] : $drawn,
            'deck_info' => [
                'total_cards'  => count($deck),
                'with_jokers'  => $withJokers,
                'cards_drawn'  => $count,
            ],
            'timestamp' => date('Y-m-d H:i:s'),
            'success'   => true,
        ];
    }

    public function generateAll(): array
    {
        return [
            'type'      => 'all',
            'results'   => [
                'number' => $this->generateNumber(1, 100),
                'dice'   => $this->rollDice(6, 1),
                'coin'   => $this->flipCoin(1),
                'card'   => $this->drawCard(1),
            ],
            'timestamp' => date('Y-m-d H:i:s'),
            'success'   => true,
        ];
    }
}

// ---------------------------------------------------------------------------
// HTTP layer
// ---------------------------------------------------------------------------
$apiInfo = [
    'version'         => '1.0',
    'endpoint'        => '/randomizer/api/',
    'supported_types' => ['number', 'dice', 'coin', 'card', 'all'],
];

try {
    $generator = new RandomGenerator();

    if (!in_array(api_method(), ['GET', 'POST'], true)) {
        throw new InvalidArgumentException('Only GET and POST methods are supported');
    }

    $type = (string) (api_input('type') ?? 'number');

    switch (strtolower($type)) {
        case 'number':
            $response = $generator->generateNumber(
                api_int(api_input('min'), 1),
                api_int(api_input('max'), 100),
            );
            break;

        case 'dice':
            $response = $generator->rollDice(
                api_int(api_input('sides'), 6),
                api_int(api_input('count'), 1),
            );
            break;

        case 'coin':
            $response = $generator->flipCoin(api_int(api_input('count'), 1));
            break;

        case 'card':
            $response = $generator->drawCard(
                api_int(api_input('count'), 1),
                api_bool(api_input('with_jokers'), false),
            );
            break;

        case 'all':
            $response = $generator->generateAll();
            break;

        default:
            throw new InvalidArgumentException('Invalid type. Supported types: number, dice, coin, card, all');
    }

    if (!isset($response['api_info'])) {
        $response['api_info'] = $apiInfo;
    }

    api_json($response);
} catch (InvalidArgumentException $e) {
    api_json([
        'success'    => false,
        'error'      => $e->getMessage(),
        'timestamp'  => date('Y-m-d H:i:s'),
        'api_info'   => $apiInfo,
    ], 400);
} catch (Throwable $e) {
    api_json([
        'success'    => false,
        'error'      => 'Internal server error',
        'timestamp'  => date('Y-m-d H:i:s'),
        'api_info'   => $apiInfo,
    ], 500);
}