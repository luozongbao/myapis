<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Function to read a fortune from JSON file
function getFortune($fortuneId) {
    $filePath = __DIR__ . '/../predictions/' . $fortuneId . '.json';
    
    if (!file_exists($filePath)) {
        return null;
    }
    
    $jsonContent = file_get_contents($filePath);
    return json_decode($jsonContent, true);
}

// Get a random fortune ID (1-52)
$randomId = rand(1, 52);
$fortune = getFortune($randomId);

if ($fortune === null) {
    // Fallback in case file doesn't exist
    echo json_encode([
        'success' => false,
        'error' => 'Fortune file not found',
        'requested_id' => $randomId
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Return the fortune as JSON
echo json_encode([
    'success' => true,
    'fortune' => $fortune,
    'timestamp' => date('Y-m-d H:i:s'),
    'total_fortunes' => 52
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
