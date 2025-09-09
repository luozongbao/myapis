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

class RandomGenerator {
    
    // Card definitions
    private $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
    private $ranks = ['Ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King'];
    private $suitSymbols = [
        'Hearts' => '♥',
        'Diamonds' => '♦',
        'Clubs' => '♣',
        'Spades' => '♠'
    ];
    
    public function generateNumber($min = 1, $max = 100) {
        // Validate inputs
        if (!is_numeric($min) || !is_numeric($max)) {
            throw new Exception('Min and max values must be numeric');
        }
        
        $min = (int)$min;
        $max = (int)$max;
        
        if ($min > $max) {
            throw new Exception('Minimum value cannot be greater than maximum value');
        }
        
        $number = random_int($min, $max);
        
        return [
            'type' => 'number',
            'result' => $number,
            'range' => ['min' => $min, 'max' => $max],
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => true
        ];
    }
    
    public function rollDice($sides = 6, $count = 1) {
        // Validate inputs
        if (!is_numeric($sides) || !is_numeric($count)) {
            throw new Exception('Sides and count must be numeric');
        }
        
        $sides = (int)$sides;
        $count = (int)$count;
        
        if ($sides < 2 || $sides > 100) {
            throw new Exception('Dice sides must be between 2 and 100');
        }
        
        if ($count < 1 || $count > 10) {
            throw new Exception('Dice count must be between 1 and 10');
        }
        
        $rolls = [];
        $total = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $roll = random_int(1, $sides);
            $rolls[] = $roll;
            $total += $roll;
        }
        
        return [
            'type' => 'dice',
            'result' => $count === 1 ? $rolls[0] : $rolls,
            'total' => $total,
            'dice_config' => ['sides' => $sides, 'count' => $count],
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => true
        ];
    }
    
    public function flipCoin($count = 1) {
        // Validate input
        if (!is_numeric($count)) {
            throw new Exception('Count must be numeric');
        }
        
        $count = (int)$count;
        
        if ($count < 1 || $count > 10) {
            throw new Exception('Coin count must be between 1 and 10');
        }
        
        $flips = [];
        $heads = 0;
        $tails = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $flip = random_int(0, 1) === 0 ? 'Heads' : 'Tails';
            $flips[] = $flip;
            
            if ($flip === 'Heads') {
                $heads++;
            } else {
                $tails++;
            }
        }
        
        return [
            'type' => 'coin',
            'result' => $count === 1 ? $flips[0] : $flips,
            'statistics' => ['heads' => $heads, 'tails' => $tails],
            'count' => $count,
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => true
        ];
    }
    
    public function drawCard($count = 1, $withJokers = false) {
        // Validate input
        if (!is_numeric($count)) {
            throw new Exception('Count must be numeric');
        }
        
        $count = (int)$count;
        
        $maxCount = $withJokers ? 54 : 52;
        if ($count < 1 || $count > $maxCount) {
            throw new Exception('Card count must be between 1 and ' . $maxCount);
        }
        
        // Build deck
        $deck = [];
        foreach ($this->suits as $suit) {
            foreach ($this->ranks as $rank) {
                $deck[] = [
                    'rank' => $rank,
                    'suit' => $suit,
                    'symbol' => $this->suitSymbols[$suit],
                    'display' => $rank . ' of ' . $suit,
                    'short' => $rank . $this->suitSymbols[$suit],
                    'color' => in_array($suit, ['Hearts', 'Diamonds']) ? 'red' : 'black'
                ];
            }
        }
        
        // Add jokers if requested
        if ($withJokers) {
            $deck[] = [
                'rank' => 'Joker',
                'suit' => 'Red',
                'symbol' => '🃏',
                'display' => 'Red Joker',
                'short' => 'Red🃏',
                'color' => 'red'
            ];
            $deck[] = [
                'rank' => 'Joker',
                'suit' => 'Black',
                'symbol' => '🃏',
                'display' => 'Black Joker',
                'short' => 'Black🃏',
                'color' => 'black'
            ];
        }
        
        // Shuffle deck and draw cards
        shuffle($deck);
        $drawnCards = array_slice($deck, 0, $count);
        
        return [
            'type' => 'card',
            'result' => $count === 1 ? $drawnCards[0] : $drawnCards,
            'deck_info' => [
                'total_cards' => count($deck),
                'with_jokers' => $withJokers,
                'cards_drawn' => $count
            ],
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => true
        ];
    }
    
    public function generateRandom($type, $params = []) {
        switch (strtolower($type)) {
            case 'number':
                $min = isset($params['min']) ? $params['min'] : 1;
                $max = isset($params['max']) ? $params['max'] : 100;
                return $this->generateNumber($min, $max);
                
            case 'dice':
                $sides = isset($params['sides']) ? $params['sides'] : 6;
                $count = isset($params['count']) ? $params['count'] : 1;
                return $this->rollDice($sides, $count);
                
            case 'coin':
                $count = isset($params['count']) ? $params['count'] : 1;
                return $this->flipCoin($count);
                
            case 'card':
                $count = isset($params['count']) ? $params['count'] : 1;
                $withJokers = isset($params['with_jokers']) ? (bool)$params['with_jokers'] : false;
                return $this->drawCard($count, $withJokers);
                
            default:
                throw new Exception('Invalid random type. Supported types: number, dice, coin, card');
        }
    }
}

// Main API logic
try {
    $generator = new RandomGenerator();
    $response = ['success' => false];
    
    // Get request method and data
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Handle GET and POST requests
    if ($method === 'GET') {
        $data = $_GET;
    } elseif ($method === 'POST') {
        $data = $input ?: $_POST;
    } else {
        throw new Exception('Only GET and POST methods are supported');
    }
    
    // Determine action type
    $type = isset($data['type']) ? $data['type'] : 'number';
    
    // Generate random based on type
    switch (strtolower($type)) {
        case 'number':
            $min = isset($data['min']) ? (int)$data['min'] : 1;
            $max = isset($data['max']) ? (int)$data['max'] : 100;
            $response = $generator->generateNumber($min, $max);
            break;
            
        case 'dice':
            $sides = isset($data['sides']) ? (int)$data['sides'] : 6;
            $count = isset($data['count']) ? (int)$data['count'] : 1;
            $response = $generator->rollDice($sides, $count);
            break;
            
        case 'coin':
            $count = isset($data['count']) ? (int)$data['count'] : 1;
            $response = $generator->flipCoin($count);
            break;
            
        case 'card':
            $count = isset($data['count']) ? (int)$data['count'] : 1;
            $withJokers = isset($data['with_jokers']) ? (bool)$data['with_jokers'] : false;
            $response = $generator->drawCard($count, $withJokers);
            break;
            
        case 'all':
            // Generate all types at once
            $response = [
                'type' => 'all',
                'results' => [
                    'number' => $generator->generateNumber(1, 100),
                    'dice' => $generator->rollDice(6, 1),
                    'coin' => $generator->flipCoin(1),
                    'card' => $generator->drawCard(1)
                ],
                'timestamp' => date('Y-m-d H:i:s'),
                'success' => true
            ];
            break;
            
        default:
            throw new Exception('Invalid type. Supported types: number, dice, coin, card, all');
    }
    
    // Add API info if not already present
    if (!isset($response['api_info'])) {
        $response['api_info'] = [
            'version' => '1.0',
            'endpoint' => '/randomizer/api/',
            'supported_types' => ['number', 'dice', 'coin', 'card', 'all']
        ];
    }
    
} catch (Exception $e) {
    http_response_code(400);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'api_info' => [
            'version' => '1.0',
            'endpoint' => '/randomizer/api/',
            'supported_types' => ['number', 'dice', 'coin', 'card', 'all']
        ]
    ];
} catch (Error $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => 'Internal server error',
        'timestamp' => date('Y-m-d H:i:s'),
        'api_info' => [
            'version' => '1.0',
            'endpoint' => '/randomizer/api/',
            'supported_types' => ['number', 'dice', 'coin', 'card', 'all']
        ]
    ];
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
?>
