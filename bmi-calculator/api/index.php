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

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Support both JSON POST and GET parameters
$weight = $input['weight'] ?? $_GET['weight'] ?? null;
$height = $input['height'] ?? $_GET['height'] ?? null;
$unit = $input['unit'] ?? $_GET['unit'] ?? 'metric'; // metric or imperial

// Validate input
if (!$weight || !$height) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required parameters',
        'message' => 'Both weight and height are required',
        'usage' => [
            'metric' => 'weight in kg, height in cm or m',
            'imperial' => 'weight in lbs, height in inches'
        ]
    ]);
    exit();
}

// Validate that weight and height are numeric
if (!is_numeric($weight) || !is_numeric($height)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid input',
        'message' => 'Weight and height must be numeric values'
    ]);
    exit();
}

// Convert imperial to metric if needed
if ($unit === 'imperial') {
    // Convert pounds to kg
    $weight = $weight * 0.453592;
    // Convert inches to meters
    $height = $height * 0.0254;
} else {
    // Ensure height is in meters
    if ($height > 3) {
        $height = $height / 100;
    }
}

// Validate converted values
if ($weight <= 0 || $height <= 0) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid values',
        'message' => 'Weight and height must be positive values'
    ]);
    exit();
}

if ($height > 3 || $weight > 1000) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Unrealistic values',
        'message' => 'Please check your height and weight values'
    ]);
    exit();
}

// Calculate BMI
$bmi = calculateBMI($weight, $height);
$category = getBMICategory($bmi);
$advice = getBMIAdvice($category);

// Return response
$response = [
    'success' => true,
    'data' => [
        'bmi' => $bmi,
        'category' => $category,
        'advice' => $advice,
        'input' => [
            'weight' => round($weight, 2),
            'height' => round($height, 2),
            'unit' => $unit
        ]
    ],
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
