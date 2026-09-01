<?php
/**
 * Health Calculator — BMI / BMR / Daily Intake / Water Intake
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

final class HealthCalculator
{
    private const ACTIVITY_MULTIPLIERS = [
        "sedentary" => 1.2,
        "light"     => 1.375,
        "moderate"  => 1.55,
        "active"    => 1.725,
        "extra"     => 1.9,
    ];

    private const GOAL_ADJUSTMENTS = [
        "maintain"   => 0,
        "lose"       => -500,
        "lose-fast"  => -1000,
        "gain"       => 500,
        "gain-fast"  => 1000,
    ];

    private const CLIMATE_MULTIPLIERS = [
        "cold"      => 0.9,
        "temperate" => 1.0,
        "hot"       => 1.3,
        "very-hot"  => 1.5,
    ];

    private const HEALTH_MULTIPLIERS = [
        "normal"       => 1.0,
        "fever"        => 1.3,
        "diarrhea"     => 1.5,
        "kidney"       => 0.8,
        "heart"        => 0.9,
        "pregnancy"    => 1.3,
        "breastfeeding"=> 1.5,
    ];

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function compute(array $input): array
    {
        $calculator = (string) ($input["calculator"] ?? "");
        $unit       = (string) ($input["unit"] ?? "metric");

        $weight = (float) ($input["weight"] ?? 0);
        $height = isset($input["height"]) ? (float) $input["height"] : 0.0;

        if ($unit === "imperial") {
            $weight = $weight * 0.453592;
            $height = $height * 2.54;
        }

        if ($weight <= 0 || ($calculator !== "water" && $height <= 0)) {
            throw new ValidationException("Weight and height must be positive values");
        }
        if ($weight > 1000 || ($calculator !== "water" && $height > 300)) {
            throw new ValidationException("Please check your height and weight values - they seem unrealistic");
        }

        switch ($calculator) {
            case "bmi":
                return $this->computeBmi($weight, $height);
            case "bmr":
                return $this->computeBmr($weight, $height, $input);
            case "intake":
                return $this->computeIntake($weight, $height, $input);
            case "water":
                return $this->computeWater($weight, $input);
            default:
                throw new ValidationException(
                    "Invalid calculator type. Available: bmi, bmr, intake, water"
                );
        }
    }

    /**
     * @return array<string,mixed>
     */
    private function computeBmi(float $weight, float $height): array
    {
        $h = $height > 3 ? $height / 100 : $height;
        $bmi = round($weight / ($h * $h), 2);
        $category = $this->getBmiCategory($bmi);
        return [
            "bmi"      => $bmi,
            "category" => $category,
            "advice"   => $this->getBmiAdvice($category),
        ];
    }

    private function getBmiCategory(float $bmi): string
    {
        if ($bmi < 18.5) return "Underweight";
        if ($bmi < 25)   return "Normal weight";
        if ($bmi < 30)   return "Overweight";
        return "Obese";
    }

    private function getBmiAdvice(string $category): string
    {
        $advice = [
            "Underweight"   => "Consider consulting with a healthcare provider about gaining weight in a healthy way.",
            "Normal weight" => "Great! Maintain your current lifestyle with a balanced diet and regular exercise.",
            "Overweight"    => "Consider adopting a healthier diet and increasing physical activity.",
            "Obese"         => "It is recommended to consult with a healthcare provider for a comprehensive weight management plan.",
        ];
        return $advice[$category] ?? "Consult with a healthcare provider for personalized advice.";
    }

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function computeBmr(float $weight, float $height, array $input): array
    {
        $age      = (int) ($input["age"] ?? 0);
        $gender   = (string) ($input["gender"] ?? "");
        $activity = (string) ($input["activity"] ?? "sedentary");

        if ($age <= 0 || $age > 120) {
            throw new ValidationException("Age must be between 1 and 120 years");
        }

        $bmr = $gender === "male"
            ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
            : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        $bmr = round($bmr, 0);
        $dailyCalories = round($bmr * $this->activityMultiplier($activity), 0);

        return [
            "bmr"    => $bmr,
            "detail" => "Daily calories needed: $dailyCalories",
            "advice" => "Your BMR is $bmr calories per day. With your activity level, you need approximately $dailyCalories calories daily to maintain your current weight.",
        ];
    }

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function computeIntake(float $weight, float $height, array $input): array
    {
        $age      = (int) ($input["age"] ?? 0);
        $gender   = (string) ($input["gender"] ?? "");
        $activity = (string) ($input["activity"] ?? "sedentary");
        $goal     = (string) ($input["goal"] ?? "maintain");

        if ($age <= 0 || $age > 120) {
            throw new ValidationException("Age must be between 1 and 120 years");
        }

        $bmr = $gender === "male"
            ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
            : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        $maintenance = round($bmr * $this->activityMultiplier($activity), 0);
        $adjustment  = self::GOAL_ADJUSTMENTS[$goal] ?? 0;
        $targetKcal  = $maintenance + $adjustment;

        $protein    = round($weight * 1.6, 0);
        $proteinCal = $protein * 4;
        $fatCal     = round($targetKcal * 0.25, 0);
        $fat        = round($fatCal / 9, 0);
        $carbCal    = $targetKcal - $proteinCal - $fatCal;
        $carbs      = round($carbCal / 4, 0);

        $adviceMap = [
            "maintain"  => "To maintain your current weight, aim for $targetKcal calories per day with balanced nutrition and regular exercise.",
            "lose"      => "To lose 0.5kg per week, aim for $targetKcal calories per day. This creates a safe caloric deficit.",
            "lose-fast" => "To lose 1kg per week, aim for $targetKcal calories per day. Ensure adequate nutrition and consider consulting a healthcare provider.",
            "gain"      => "To gain 0.5kg per week, aim for $targetKcal calories per day with a focus on protein and strength training.",
            "gain-fast" => "To gain 1kg per week, aim for $targetKcal calories per day. Focus on nutrient-dense, high-calorie foods.",
        ];

        return [
            "calories" => $targetKcal,
            "advice"   => $adviceMap[$goal] ?? "Consult with a healthcare provider for personalized nutrition advice.",
            "macros"   => [
                "protein" => $protein,
                "carbs"   => $carbs,
                "fat"     => $fat,
            ],
            "meta" => [
                "bmr"         => round($bmr, 0),
                "maintenance" => $maintenance,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function computeWater(float $weight, array $input): array
    {
        $age        = (int) ($input["age"] ?? 0);
        $gender     = (string) ($input["gender"] ?? "");
        $activity   = (string) ($input["activity"] ?? "sedentary");
        $climate    = (string) ($input["climate"] ?? "temperate");
        $healthCond = (string) ($input["healthCondition"] ?? "normal");

        if ($age <= 0 || $age > 120) {
            throw new ValidationException("Age must be between 1 and 120 years");
        }

        // EFSA 2010 — 35ml/kg base
        $intake = $weight * 35;
        if ($age > 65)          $intake *= 1.1;
        elseif ($age < 18)      $intake *= 0.9;
        if ($gender === "male") $intake *= 1.1;
        $intake *= self::ACTIVITY_MULTIPLIERS[$activity] ?? 1.0;
        $intake *= self::CLIMATE_MULTIPLIERS[$climate]   ?? 1.0;
        $intake *= self::HEALTH_MULTIPLIERS[$healthCond] ?? 1.0;
        $intake = round($intake, 0);

        $fromFood   = round($intake * 0.2, 0);
        $fromDrinks = $intake - $fromFood;
        $glasses    = round($fromDrinks / 250, 1);

        return [
            "amount"  => $intake . "ml/day",
            "advice"  => $this->getWaterAdvice($intake, $glasses, $activity, $climate, $healthCond),
            "details" => [
                "total"      => $intake,
                "fromDrinks" => $fromDrinks,
                "fromFood"   => $fromFood,
                "glasses"    => $glasses,
            ],
        ];
    }

    private function getWaterAdvice(int $total, float $glasses, string $activity, string $climate, string $health): string
    {
        $advice = "Aim for approximately {$total}ml ({$glasses} glasses) of water daily. ";
        if ($activity === "active" || $activity === "extra") {
            $advice .= "Since you are very active, drink extra water before, during, and after exercise. ";
        }
        if ($climate === "hot" || $climate === "very-hot") {
            $advice .= "Hot climate increases your water needs - drink regularly throughout the day. ";
        }
        if ($health === "fever") {
            $advice .= "Fever increases fluid loss - drink extra water and consult a healthcare provider. ";
        } elseif ($health === "kidney" || $health === "heart") {
            $advice .= "Please consult your healthcare provider about appropriate fluid intake for your condition. ";
        } elseif ($health === "pregnancy" || $health === "breastfeeding") {
            $advice .= "Increased fluid needs during this time are normal - ensure adequate hydration. ";
        }
        $advice .= "Spread intake throughout the day and listen to your body thirst signals.";
        return $advice;
    }

    private function activityMultiplier(string $activity): float
    {
        return self::ACTIVITY_MULTIPLIERS[$activity] ?? 1.2;
    }
}

ErrorHandler::wrap(static function (): void {
    $input = Validator::readInput();
    $input["calculator"] = $input["calculator"] ?? ($_GET["calculator"] ?? null);
    $input["unit"]       = $input["unit"]       ?? ($_GET["unit"]       ?? "metric");

    $errors = Validator::requireKeys($input, ["calculator"]);
    if (!empty($errors)) {
        throw new ValidationException(implode("; ", $errors));
    }

    $calc = new HealthCalculator();
    $data = $calc->compute($input);

    ErrorHandler::success(array_merge(
        ["calculator" => (string) $input["calculator"]],
        $data
    ));
});
