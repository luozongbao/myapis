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

// BMI Calculator Functions
function calculateBMI($weight, $height) {
    // Convert height from cm to meters if needed
    if ($height > 3) {
        $height = $height / 100;
    }
    
    $bmi = $weight / ($height * $height);
    return round($bmi, 2);
}

function getBMICategory($bmi) {
    if ($bmi < 18.5) {
        return 'Underweight';
    } elseif ($bmi >= 18.5 && $bmi < 25) {
        return 'Normal weight';
    } elseif ($bmi >= 25 && $bmi < 30) {
        return 'Overweight';
    } else {
        return 'Obese';
    }
}

function getBMIAdvice($category) {
    $advice = [
        'Underweight' => 'Consider consulting with a healthcare provider about gaining weight in a healthy way.',
        'Normal weight' => 'Great! Maintain your current lifestyle with a balanced diet and regular exercise.',
        'Overweight' => 'Consider adopting a healthier diet and increasing physical activity.',
        'Obese' => 'It\'s recommended to consult with a healthcare provider for a comprehensive weight management plan.'
    ];
    
    return $advice[$category] ?? 'Consult with a healthcare provider for personalized advice.';
}

// BMR Calculator Functions
function calculateBMR($weight, $height, $age, $gender) {
    // Ensure height is in cm
    if ($height <= 3) {
        $height = $height * 100;
    }
    
    // Mifflin-St Jeor Equation
    if ($gender === 'male') {
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
    } else {
        $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
    }
    
    return round($bmr, 0);
}

function getActivityMultiplier($activity) {
    $multipliers = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'extra' => 1.9
    ];
    
    return $multipliers[$activity] ?? 1.2;
}

function getBMRAdvice($bmr, $activity) {
    $dailyCalories = round($bmr * getActivityMultiplier($activity), 0);
    return "Your BMR is $bmr calories per day. With your activity level, you need approximately $dailyCalories calories daily to maintain your current weight.";
}

// Daily Intake Calculator Functions
function calculateDailyIntake($weight, $height, $age, $gender, $activity, $goal) {
    $bmr = calculateBMR($weight, $height, $age, $gender);
    $maintenanceCalories = round($bmr * getActivityMultiplier($activity), 0);
    
    $adjustments = [
        'maintain' => 0,
        'lose' => -500,      // 0.5 kg per week
        'lose-fast' => -1000, // 1 kg per week
        'gain' => 500,       // 0.5 kg per week
        'gain-fast' => 1000  // 1 kg per week
    ];
    
    $adjustment = $adjustments[$goal] ?? 0;
    $targetCalories = $maintenanceCalories + $adjustment;
    
    // Calculate macronutrient breakdown (basic recommendation)
    $protein = round($weight * 1.6, 0); // 1.6g per kg body weight
    $proteinCals = $protein * 4;
    
    $fatCals = round($targetCalories * 0.25, 0); // 25% of calories from fat
    $fat = round($fatCals / 9, 0);
    
    $carbCals = $targetCalories - $proteinCals - $fatCals;
    $carbs = round($carbCals / 4, 0);
    
    return [
        'calories' => $targetCalories,
        'protein' => $protein,
        'carbs' => $carbs,
        'fat' => $fat,
        'bmr' => $bmr,
        'maintenance' => $maintenanceCalories
    ];
}

function getIntakeAdvice($goal, $calories) {
    $advice = [
        'maintain' => "To maintain your current weight, aim for $calories calories per day with balanced nutrition and regular exercise.",
        'lose' => "To lose 0.5kg per week, aim for $calories calories per day. This creates a safe caloric deficit.",
        'lose-fast' => "To lose 1kg per week, aim for $calories calories per day. Ensure adequate nutrition and consider consulting a healthcare provider.",
        'gain' => "To gain 0.5kg per week, aim for $calories calories per day with a focus on protein and strength training.",
        'gain-fast' => "To gain 1kg per week, aim for $calories calories per day. Focus on nutrient-dense, high-calorie foods."
    ];
    
    return $advice[$goal] ?? 'Consult with a healthcare provider for personalized nutrition advice.';
}

// Utility Functions
function convertUnits($weight, $height, $unit) {
    if ($unit === 'imperial') {
        // Convert pounds to kg
        $weight = $weight * 0.453592;
        // Convert inches to cm
        $height = $height * 2.54;
    }
    
    return [$weight, $height];
}

function validateInput($input, $calculator) {
    $errors = [];
    
    switch ($calculator) {
        case 'bmi':
            if (!isset($input['weight']) || !isset($input['height'])) {
                $errors[] = 'Weight and height are required for BMI calculation';
            }
            break;
            
        case 'bmr':
            $required = ['weight', 'height', 'age', 'gender', 'activity'];
            foreach ($required as $field) {
                if (!isset($input[$field])) {
                    $errors[] = ucfirst($field) . ' is required for BMR calculation';
                }
            }
            break;
            
        case 'intake':
            $required = ['weight', 'height', 'age', 'gender', 'activity', 'goal'];
            foreach ($required as $field) {
                if (!isset($input[$field])) {
                    $errors[] = ucfirst($field) . ' is required for daily intake calculation';
                }
            }
            break;
            
        default:
            $errors[] = 'Invalid calculator type';
    }
    
    return $errors;
}

// Main logic
$input = json_decode(file_get_contents('php://input'), true);

// Support both JSON POST and GET parameters
if (!$input) {
    $input = $_GET;
}

$calculator = $input['calculator'] ?? $_GET['calculator'] ?? 'bmi';
$unit = $input['unit'] ?? $_GET['unit'] ?? 'metric';

// Validate input
$errors = validateInput($input, $calculator);
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors),
        'errors' => $errors
    ]);
    exit();
}

try {
    $weight = floatval($input['weight']);
    $height = floatval($input['height']);
    
    // Convert units if needed
    list($weight, $height) = convertUnits($weight, $height, $unit);
    
    // Validate converted values
    if ($weight <= 0 || $height <= 0) {
        throw new Exception('Weight and height must be positive values');
    }
    
    if ($weight > 1000 || $height > 300) {
        throw new Exception('Please check your height and weight values - they seem unrealistic');
    }
    
    $result = [];
    
    switch ($calculator) {
        case 'bmi':
            $bmi = calculateBMI($weight, $height);
            $category = getBMICategory($bmi);
            $advice = getBMIAdvice($category);
            
            $result = [
                'bmi' => $bmi,
                'category' => $category,
                'advice' => $advice
            ];
            break;
            
        case 'bmr':
            $age = intval($input['age']);
            $gender = $input['gender'];
            $activity = $input['activity'];
            
            if ($age <= 0 || $age > 120) {
                throw new Exception('Age must be between 1 and 120 years');
            }
            
            $bmr = calculateBMR($weight, $height, $age, $gender);
            $dailyCalories = round($bmr * getActivityMultiplier($activity), 0);
            
            $result = [
                'bmr' => $bmr,
                'detail' => "Daily calories needed: $dailyCalories",
                'advice' => getBMRAdvice($bmr, $activity)
            ];
            break;
            
        case 'intake':
            $age = intval($input['age']);
            $gender = $input['gender'];
            $activity = $input['activity'];
            $goal = $input['goal'];
            
            if ($age <= 0 || $age > 120) {
                throw new Exception('Age must be between 1 and 120 years');
            }
            
            $intake = calculateDailyIntake($weight, $height, $age, $gender, $activity, $goal);
            
            $breakdown = "Protein: {$intake['protein']}g • Carbs: {$intake['carbs']}g • Fat: {$intake['fat']}g<br>";
            $breakdown .= "BMR: {$intake['bmr']} cal • Maintenance: {$intake['maintenance']} cal";
            
            $result = [
                'calories' => $intake['calories'],
                'breakdown' => $breakdown,
                'advice' => getIntakeAdvice($goal, $intake['calories']),
                'macros' => [
                    'protein' => $intake['protein'],
                    'carbs' => $intake['carbs'],
                    'fat' => $intake['fat']
                ]
            ];
            break;
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $result,
        'calculator' => $calculator,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
