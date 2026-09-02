<?php
/**
 * Health Calculator API
 *
 * Required: ?calculator=bmi|bmr|intake|water (also accepted in JSON body)
 *
 * All numeric formulas and multipliers live in HealthCalculator; the
 * HTTP layer just collects input, calls the right method and shapes
 * the JSON response.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

api_send_headers();
if (api_handle_preflight()) {
    exit;
}
api_register_exception_handler();

/**
 * Pure health calculations.
 */
class HealthCalculator
{
    private const ACTIVITY_MULTIPLIERS = [
        'sedentary' => 1.2,
        'light'     => 1.375,
        'moderate'  => 1.55,
        'active'    => 1.725,
        'extra'     => 1.9,
    ];

    private const GOAL_ADJUSTMENTS = [
        'maintain'  => 0,
        'lose'      => -500,
        'lose-fast' => -1000,
        'gain'      => 500,
        'gain-fast' => 1000,
    ];

    private const WATER_ACTIVITY = [
        'sedentary' => 1.0,
        'light'     => 1.2,
        'moderate'  => 1.4,
        'active'    => 1.6,
        'extra'     => 1.8,
    ];

    private const WATER_CLIMATE = [
        'cold'      => 0.9,
        'temperate' => 1.0,
        'hot'       => 1.3,
        'very-hot'  => 1.5,
    ];

    private const WATER_HEALTH = [
        'normal'       => 1.0,
        'fever'        => 1.3,
        'diarrhea'     => 1.5,
        'kidney'       => 0.8,
        'heart'        => 0.9,
        'pregnancy'    => 1.3,
        'breastfeeding'=> 1.5,
    ];

    public static function calculateBMI(float $weight, float $height): float
    {
        if ($height > 3) { // cm → m
            $height /= 100;
        }
        return round($weight / ($height * $height), 2);
    }

    public static function getBMICategory(float $bmi): string
    {
        if ($bmi < 18.5) {
            return 'Underweight';
        }
        if ($bmi < 25) {
            return 'Normal weight';
        }
        if ($bmi < 30) {
            return 'Overweight';
        }
        return 'Obese';
    }

    public static function getBMIAdvice(string $category): string
    {
        $advice = [
            'Underweight'   => 'Consider consulting with a healthcare provider about gaining weight in a healthy way.',
            'Normal weight' => 'Great! Maintain your current lifestyle with a balanced diet and regular exercise.',
            'Overweight'    => 'Consider adopting a healthier diet and increasing physical activity.',
            'Obese'         => 'It\'s recommended to consult with a healthcare provider for a comprehensive weight management plan.',
        ];
        return $advice[$category] ?? 'Consult with a healthcare provider for personalized advice.';
    }

    public static function calculateBMR(float $weight, float $height, int $age, string $gender): int
    {
        if ($height <= 3) { // m → cm
            $height *= 100;
        }
        $bmr = $gender === 'male'
            ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
            : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        return (int) round($bmr);
    }

    public static function calculateDailyIntake(
        float $weight,
        float $height,
        int $age,
        string $gender,
        string $activity,
        string $goal
    ): array {
        $bmr                 = self::calculateBMR($weight, $height, $age, $gender);
        $maintenanceCalories = (int) round($bmr * self::ACTIVITY_MULTIPLIERS[$activity]);
        $adjustment          = self::GOAL_ADJUSTMENTS[$goal] ?? 0;
        $target              = $maintenanceCalories + $adjustment;

        $protein    = (int) round($weight * 1.6);
        $proteinCals = $protein * 4;
        $fatCals     = (int) round($target * 0.25);
        $fat         = (int) round($fatCals / 9);
        $carbCals    = max(0, $target - $proteinCals - $fatCals);
        $carbs       = (int) round($carbCals / 4);

        return [
            'calories'    => $target,
            'protein'     => $protein,
            'carbs'       => $carbs,
            'fat'         => $fat,
            'bmr'         => $bmr,
            'maintenance' => $maintenanceCalories,
        ];
    }

    public static function getIntakeAdvice(string $goal, int $calories): string
    {
        $advice = [
            'maintain'  => "To maintain your current weight, aim for $calories calories per day with balanced nutrition and regular exercise.",
            'lose'      => "To lose 0.5kg per week, aim for $calories calories per day. This creates a safe caloric deficit.",
            'lose-fast' => "To lose 1kg per week, aim for $calories calories per day. Ensure adequate nutrition and consider consulting a healthcare provider.",
            'gain'      => "To gain 0.5kg per week, aim for $calories calories per day with a focus on protein and strength training.",
            'gain-fast' => "To gain 1kg per week, aim for $calories calories per day. Focus on nutrient-dense, high-calorie foods.",
        ];
        return $advice[$goal] ?? 'Consult with a healthcare provider for personalized nutrition advice.';
    }

    public static function calculateWaterIntake(
        float $weight,
        int $age,
        string $gender,
        string $activity,
        string $climate,
        string $healthCondition
    ): int {
        // 35 ml/kg — EFSA Dietary Reference Values for water (2010)
        $intake = $weight * 35;

        if ($age > 65) {
            $intake *= 1.1;
        } elseif ($age < 18) {
            $intake *= 0.9;
        }
        if ($gender === 'male') {
            $intake *= 1.1;
        }

        $intake *= self::WATER_ACTIVITY[$activity]      ?? 1.0;
        $intake *= self::WATER_CLIMATE[$climate]        ?? 1.0;
        $intake *= self::WATER_HEALTH[$healthCondition] ?? 1.0;

        return (int) round($intake);
    }

    public static function getWaterBreakdown(int $total): array
    {
        $fromFood   = (int) round($total * 0.2);
        $fromDrinks = $total - $fromFood;
        $glasses    = round($fromDrinks / 250, 1);
        return [
            'total'      => $total,
            'fromDrinks' => $fromDrinks,
            'fromFood'   => $fromFood,
            'glasses'    => $glasses,
        ];
    }

    public static function getWaterAdvice(
        int $total,
        float $glasses,
        string $activity,
        string $climate,
        string $healthCondition
    ): string {
        $advice = "Aim for approximately {$total}ml ({$glasses} glasses) of water daily. ";

        if (in_array($activity, ['active', 'extra'], true)) {
            $advice .= "Since you're very active, drink extra water before, during, and after exercise. ";
        }
        if (in_array($climate, ['hot', 'very-hot'], true)) {
            $advice .= "Hot climate increases your water needs - drink regularly throughout the day. ";
        }
        switch ($healthCondition) {
            case 'fever':
                $advice .= "Fever increases fluid loss - drink extra water and consult a healthcare provider. ";
                break;
            case 'kidney':
            case 'heart':
                $advice .= "Please consult your healthcare provider about appropriate fluid intake for your condition. ";
                break;
            case 'pregnancy':
            case 'breastfeeding':
                $advice .= "Increased fluid needs during this time are normal - ensure adequate hydration. ";
                break;
        }
        $advice .= 'Spread intake throughout the day and listen to your body\'s thirst signals.';
        return $advice;
    }

    public static function convertUnits(float $weight, float $height, string $unit): array
    {
        if ($unit === 'imperial') {
            $weight *= 0.453592; // lb → kg
            $height *= 2.54;     // in → cm
        }
        return [$weight, $height];
    }

    public static function validateInput(array $input, string $calculator): array
    {
        $required = match ($calculator) {
            'bmi'    => ['weight', 'height'],
            'bmr'    => ['weight', 'height', 'age', 'gender', 'activity'],
            'intake' => ['weight', 'height', 'age', 'gender', 'activity', 'goal'],
            'water'  => ['weight', 'age', 'gender', 'activity', 'climate', 'healthCondition'],
            default  => null,
        };

        if ($required === null) {
            return ['Invalid calculator type'];
        }
        $errors = [];
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                $errors[] = ucfirst($field) . " is required for {$calculator} calculation";
            }
        }
        return $errors;
    }

    public static function getActivityMultiplier(string $activity): float
    {
        return self::ACTIVITY_MULTIPLIERS[$activity] ?? 1.2;
    }

    public static function availableCalculators(): array
    {
        return ['bmi', 'bmr', 'intake', 'water'];
    }
}

// ---------------------------------------------------------------------------
// HTTP layer
// ---------------------------------------------------------------------------
$calculator = (string) (api_input('calculator') ?? '');
$unit       = (string) (api_input('unit') ?? 'metric');

if ($calculator === '') {
    api_json([
        'success'             => false,
        'message'             => 'Calculator type is required',
        'availableCalculators' => HealthCalculator::availableCalculators(),
    ], 400);
}

$input = $_GET;
foreach (api_json_body() as $k => $v) {
    $input[$k] = $v;
}

$errors = HealthCalculator::validateInput($input, $calculator);
if (!empty($errors)) {
    api_json([
        'success' => false,
        'message' => implode(', ', $errors),
        'errors'  => $errors,
    ], 400);
}

try {
    $weight = (float) $input['weight'];

    if ($calculator === 'water') {
        if ($unit === 'imperial') {
            $weight *= 0.453592;
        }
        if ($weight <= 0) {
            throw new InvalidArgumentException('Weight must be a positive value');
        }
        if ($weight > 1000) {
            throw new InvalidArgumentException('Please check your weight value - it seems unrealistic');
        }
        $height = 0.0;
    } else {
        $height = (float) $input['height'];
        [$weight, $height] = HealthCalculator::convertUnits($weight, $height, $unit);
        if ($weight <= 0 || $height <= 0) {
            throw new InvalidArgumentException('Weight and height must be positive values');
        }
        if ($weight > 1000 || $height > 300) {
            throw new InvalidArgumentException('Please check your height and weight values - they seem unrealistic');
        }
    }

    switch ($calculator) {
        case 'bmi': {
            $bmi      = HealthCalculator::calculateBMI($weight, $height);
            $category = HealthCalculator::getBMICategory($bmi);
            $result   = [
                'bmi'      => $bmi,
                'category' => $category,
                'advice'   => HealthCalculator::getBMIAdvice($category),
            ];
            break;
        }
        case 'bmr': {
            $age      = api_int($input['age']);
            $gender   = (string) $input['gender'];
            $activity = (string) $input['activity'];
            if ($age <= 0 || $age > 120) {
                throw new InvalidArgumentException('Age must be between 1 and 120 years');
            }
            $bmr           = HealthCalculator::calculateBMR($weight, $height, $age, $gender);
            $dailyCalories = (int) round($bmr * HealthCalculator::getActivityMultiplier($activity));
            $result = [
                'bmr'    => $bmr,
                'detail' => "Daily calories needed: $dailyCalories",
                'advice' => "Your BMR is $bmr calories per day. With your activity level, you need approximately $dailyCalories calories daily to maintain your current weight.",
            ];
            break;
        }
        case 'intake': {
            $age      = api_int($input['age']);
            $gender   = (string) $input['gender'];
            $activity = (string) $input['activity'];
            $goal     = (string) $input['goal'];
            if ($age <= 0 || $age > 120) {
                throw new InvalidArgumentException('Age must be between 1 and 120 years');
            }
            $intake    = HealthCalculator::calculateDailyIntake($weight, $height, $age, $gender, $activity, $goal);
            $breakdown = "Protein: {$intake['protein']}g • Carbs: {$intake['carbs']}g • Fat: {$intake['fat']}g<br>";
            $breakdown .= "BMR: {$intake['bmr']} cal • Maintenance: {$intake['maintenance']} cal";
            $result = [
                'calories' => $intake['calories'],
                'breakdown' => $breakdown,
                'advice'   => HealthCalculator::getIntakeAdvice($goal, $intake['calories']),
                'macros'   => [
                    'protein' => $intake['protein'],
                    'carbs'   => $intake['carbs'],
                    'fat'     => $intake['fat'],
                ],
            ];
            break;
        }
        case 'water': {
            $age             = api_int($input['age']);
            $gender          = (string) $input['gender'];
            $activity        = (string) $input['activity'];
            $climate         = (string) $input['climate'];
            $healthCondition = (string) $input['healthCondition'];
            if ($age <= 0 || $age > 120) {
                throw new InvalidArgumentException('Age must be between 1 and 120 years');
            }
            $totalIntake = HealthCalculator::calculateWaterIntake($weight, $age, $gender, $activity, $climate, $healthCondition);
            $breakdown   = HealthCalculator::getWaterBreakdown($totalIntake);
            $advice      = HealthCalculator::getWaterAdvice($totalIntake, $breakdown['glasses'], $activity, $climate, $healthCondition);

            $breakdownText  = "Total: {$totalIntake}ml • From drinks: {$breakdown['fromDrinks']}ml • From food: {$breakdown['fromFood']}ml<br>";
            $breakdownText .= "Approximately {$breakdown['glasses']} glasses (250ml each)";

            $result = [
                'amount'    => $totalIntake . 'ml/day',
                'breakdown' => $breakdownText,
                'advice'    => $advice,
                'details'   => $breakdown,
            ];
            break;
        }
        default:
            throw new InvalidArgumentException('Invalid calculator type');
    }

    api_json([
        'success'    => true,
        'data'       => $result,
        'calculator' => $calculator,
        'timestamp'  => date('Y-m-d H:i:s'),
    ]);
} catch (Throwable $e) {
    api_json([
        'success' => false,
        'message' => $e->getMessage(),
    ], 400);
}