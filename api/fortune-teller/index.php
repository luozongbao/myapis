<?php
/**
 * Fortune Teller — Random fortune from 52 JSON files (TH/EN/ZH)
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

final class FortuneTeller
{
    private const TOTAL_FORTUNES = 52;

    /**
     * @return array<string,mixed>
     */
    public function pickRandom(): array
    {
        $id = random_int(1, self::TOTAL_FORTUNES);
        $file = __DIR__ . "/predictions/{$id}.json";

        if (!is_file($file)) {
            throw new NotFoundException("Fortune file for ID {$id} not found");
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new RuntimeException("Unable to read fortune file {$id}");
        }

        try {
            $fortune = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException("Invalid fortune JSON: " . $e->getMessage());
        }

        return [
            "fortune"        => $fortune,
            "total_fortunes" => self::TOTAL_FORTUNES,
        ];
    }
}

ErrorHandler::wrap(static function (): void {
    $teller = new FortuneTeller();
    ErrorHandler::success($teller->pickRandom());
});
