<?php
/**
 * Randomizer — Number / Dice / Coin / Card
 *
 * @author MyAPIs Team
 * @since  2.5.0 (refactor — ISSUE-013, ISSUE-024)
 */

declare(strict_types=1);

require_once __DIR__ . "/../_includes/Cors.php";
require_once __DIR__ . "/../_includes/ErrorHandler.php";
require_once __DIR__ . "/../_includes/Validator.php";

Cors::handle();
ErrorHandler::register();

final class RandomGenerator
{
    private const SUITS = ["Hearts", "Diamonds", "Clubs", "Spades"];
    private const RANKS = [
        "Ace", "2", "3", "4", "5", "6", "7", "8", "9", "10",
        "Jack", "Queen", "King",
    ];
    private const SUIT_SYMBOLS = [
        "Hearts"   => "♥",
        "Diamonds" => "♦",
        "Clubs"    => "♣",
        "Spades"   => "♠",
    ];

    /**
     * @return array<string,mixed>
     */
    public function generateNumber(int $min = 1, int $max = 100): array
    {
        if ($min > $max) {
            throw new ValidationException("Minimum value cannot be greater than maximum value");
        }
        return [
            "type"  => "number",
            "result" => random_int($min, $max),
            "range" => ["min" => $min, "max" => $max],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function rollDice(int $sides = 6, int $count = 1): array
    {
        if ($sides < 2 || $sides > 100) {
            throw new ValidationException("Dice sides must be between 2 and 100");
        }
        if ($count < 1 || $count > 10) {
            throw new ValidationException("Dice count must be between 1 and 10");
        }
        $rolls = [];
        $total = 0;
        for ($i = 0; $i < $count; $i++) {
            $roll = random_int(1, $sides);
            $rolls[] = $roll;
            $total += $roll;
        }
        return [
            "type"       => "dice",
            "result"     => $count === 1 ? $rolls[0] : $rolls,
            "total"      => $total,
            "dice_config" => ["sides" => $sides, "count" => $count],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function flipCoin(int $count = 1): array
    {
        if ($count < 1 || $count > 10) {
            throw new ValidationException("Coin count must be between 1 and 10");
        }
        $flips = [];
        $heads = 0;
        $tails = 0;
        for ($i = 0; $i < $count; $i++) {
            $flip = random_int(0, 1) === 0 ? "Heads" : "Tails";
            $flips[] = $flip;
            $flip === "Heads" ? $heads++ : $tails++;
        }
        return [
            "type"       => "coin",
            "result"     => $count === 1 ? $flips[0] : $flips,
            "statistics" => ["heads" => $heads, "tails" => $tails],
            "count"      => $count,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function drawCard(int $count = 1, bool $withJokers = false): array
    {
        $maxCount = $withJokers ? 54 : 52;
        if ($count < 1 || $count > $maxCount) {
            throw new ValidationException("Card count must be between 1 and $maxCount");
        }

        $deck = [];
        foreach (self::SUITS as $suit) {
            foreach (self::RANKS as $rank) {
                $deck[] = [
                    "rank"   => $rank,
                    "suit"   => $suit,
                    "symbol" => self::SUIT_SYMBOLS[$suit],
                    "display" => "$rank of $suit",
                    "short"  => $rank . self::SUIT_SYMBOLS[$suit],
                    "color"  => in_array($suit, ["Hearts", "Diamonds"], true) ? "red" : "black",
                ];
            };
        }
        if ($withJokers) {
            $deck[] = ["rank" => "Joker", "suit" => "Red",   "symbol" => "🃏", "display" => "Red Joker",   "short" => "Red🃏",   "color" => "red"];
            $deck[] = ["rank" => "Joker", "suit" => "Black", "symbol" => "🃏", "display" => "Black Joker", "short" => "Black🃏", "color" => "black"];
        }
        shuffle($deck);
        $drawn = array_slice($deck, 0, $count);
        return [
            "type"      => "card",
            "result"    => $count === 1 ? $drawn[0] : $drawn,
            "deck_info" => [
                "total_cards" => count($deck),
                "with_jokers" => $withJokers,
                "cards_drawn" => $count,
            ],
        ];
    }
}

ErrorHandler::wrap(static function (): void {
    $input = Validator::readInput();
    $type   = strtolower((string) ($input["type"] ?? "number"));
    $gen    = new RandomGenerator();

    switch ($type) {
        case "number":
            $min = (int) ($input["min"] ?? 1);
            $max = (int) ($input["max"] ?? 100);
            $data = $gen->generateNumber($min, $max);
            break;
        case "dice":
            $sides = (int) ($input["sides"] ?? 6);
            $count = (int) ($input["count"] ?? 1);
            $data = $gen->rollDice($sides, $count);
            break;
        case "coin":
            $count = (int) ($input["count"] ?? 1);
            $data = $gen->flipCoin($count);
            break;
        case "card":
            $count = (int) ($input["count"] ?? 1);
            $withJokers = filter_var($input["with_jokers"] ?? false, FILTER_VALIDATE_BOOLEAN);
            $data = $gen->drawCard($count, $withJokers);
            break;
        case "all":
            $data = [
                "type"    => "all",
                "results" => [
                    "number" => $gen->generateNumber(1, 100),
                    "dice"   => $gen->rollDice(6, 1),
                    "coin"   => $gen->flipCoin(1),
                    "card"   => $gen->drawCard(1),
                ],
            ];
            break;
        default:
            throw new ValidationException("Invalid type. Supported: number, dice, coin, card, all");
    }

    ErrorHandler::success($data);
});
