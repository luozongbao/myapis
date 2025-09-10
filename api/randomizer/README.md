# Random Generator API & Web Interface

A comprehensive random generator with multiple types of randomization including numbers, dice, coins, and cards. Features both a REST API and a responsive web interface with beautiful animations for each type of random generation.

## 🎲 Features

### 🎯 Random Generation API
- **Number Generator**: User-defined min/max range with full integer support
- **Dice Rolling**: Configurable dice sides (4, 6, 8, 10, 12, 20, 100) and multiple dice support
- **Coin Flipping**: Single or multiple coin tosses with statistics
- **Card Drawing**: Standard 52-card deck with optional jokers, multiple card draws
- **Batch Operations**: Generate all types at once or specific configurations
- **Cryptographically Secure**: Uses PHP's `random_int()` for true randomness

### 🌐 Web Interface
- **Modern Responsive Design**: Beautiful gradient UI that works on all devices
- **Interactive Type Selection**: Visual toggle between different random generators
- **Animated Results**: Unique animations for each type (bounce, flip, roll, shuffle)
- **Real-time Configuration**: Dynamic controls for each generator type
- **Visual Representations**: Cards show actual suit symbols, dice show dots, coins show heads/tails
- **Error Handling**: User-friendly error messages and validation
- **Keyboard Support**: Generate with Enter key

## 📦 Installation

1. Clone or download the project to your web server
2. Ensure PHP is installed (version 7.0 or higher recommended)
3. Place the files in your web server's document root or subdirectory
4. Ensure the web server has read permissions for all files

## 🎮 Generator Types

### 1. Number Generator
- **Range**: Any integer within PHP's supported range
- **Parameters**: `min`, `max`
- **Example**: Generate random number between 1 and 1000
- **Animation**: Bounce effect on result display

### 2. Dice Rolling
- **Supported Dice**: D4, D6, D8, D10, D12, D20, D100
- **Multiple Dice**: Roll up to 10 dice at once
- **Parameters**: `sides`, `count`
- **Features**: Individual results and total sum
- **Animation**: Rolling effect with visual dice

### 3. Coin Flipping
- **Results**: Heads or Tails
- **Multiple Coins**: Flip up to 10 coins at once
- **Parameters**: `count`
- **Statistics**: Count of heads and tails
- **Animation**: Flipping effect with coin visual

### 4. Card Drawing
- **Standard Deck**: 52 cards with proper suits and ranks
- **Optional Jokers**: Red and Black jokers available
- **Multiple Cards**: Draw up to 52 cards at once
- **Parameters**: `count`, `with_jokers`
- **Visual Cards**: Proper card display with suit symbols
- **Animation**: Shuffling effect

## 🔧 API Usage

### Base Endpoint
```
GET/POST /randomizer/api/index.php
```

### Number Generator
```bash
# Generate number between 1 and 100
curl "http://yourserver/randomizer/api/?type=number&min=1&max=100"

# Custom range
curl "http://yourserver/randomizer/api/?type=number&min=50&max=500"
```

### Dice Rolling
```bash
# Roll single D6
curl "http://yourserver/randomizer/api/?type=dice&sides=6&count=1"

# Roll 3 D20s
curl "http://yourserver/randomizer/api/?type=dice&sides=20&count=3"
```

### Coin Flipping
```bash
# Single coin flip
curl "http://yourserver/randomizer/api/?type=coin&count=1"

# Multiple coins
curl "http://yourserver/randomizer/api/?type=coin&count=5"
```

### Card Drawing
```bash
# Draw single card
curl "http://yourserver/randomizer/api/?type=card&count=1"

# Draw 5 cards with jokers
curl "http://yourserver/randomizer/api/?type=card&count=5&with_jokers=true"
```

### All Types at Once
```bash
# Generate all types simultaneously
curl "http://yourserver/randomizer/api/?type=all"
```

## 📋 API Response Format

### Successful Response
```json
{
    "type": "number",
    "result": 42,
    "range": {
        "min": 1,
        "max": 100
    },
    "timestamp": "2025-09-09 12:34:56",
    "success": true,
    "api_info": {
        "version": "1.0",
        "endpoint": "/randomizer/api/",
        "supported_types": ["number", "dice", "coin", "card", "all"]
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": "Min and max values must be numeric",
    "timestamp": "2025-09-09 12:34:56",
    "api_info": {
        "version": "1.0",
        "endpoint": "/randomizer/api/",
        "supported_types": ["number", "dice", "coin", "card", "all"]
    }
}
```

## 🎨 Web Interface Features

### Interactive Controls
- **Type Selection**: Visual buttons with icons for each generator type
- **Dynamic Forms**: Controls change based on selected type
- **Input Validation**: Real-time validation and error prevention
- **Responsive Design**: Works on desktop, tablet, and mobile devices

### Animation System
- **Number**: Bounce animation with scale effect
- **Dice**: Rolling rotation animation
- **Coin**: 3D flip animation
- **Card**: Shuffle animation with card visuals

### Visual Elements
- **Cards**: Accurate playing card representation with proper suits
- **Dice**: Visual dice with appropriate styling
- **Coins**: Golden coin design with heads/tails indication
- **Results**: Large, clear display with contextual information

## 🛡️ Security & Validation

### Input Validation
- **Number ranges**: Validated against PHP integer limits
- **Dice configuration**: Sides limited to reasonable values (2-100)
- **Count limits**: Prevent excessive resource usage
- **Type checking**: Strict parameter validation

### Randomness Quality
- **Cryptographic Function**: Uses `random_int()` for true randomness
- **No Predictability**: Each generation is independent
- **Fair Distribution**: Uniform probability across all possible values

## 🔄 HTTP Methods

### GET Requests
```bash
GET /randomizer/api/?type=number&min=1&max=100
```

### POST Requests
```bash
POST /randomizer/api/
Content-Type: application/json

{
    "type": "dice",
    "sides": 20,
    "count": 2
}
```

## 📱 Mobile Responsiveness

- **Adaptive Layout**: Single column on mobile devices
- **Touch-Friendly**: Large buttons and touch targets
- **Optimized Animations**: Smooth performance on mobile devices
- **Readable Text**: Appropriate font sizes for all screen sizes

## ⚡ Performance

- **Lightweight**: Minimal dependencies, pure PHP backend
- **Fast Generation**: Optimized algorithms for quick results
- **Efficient Frontend**: Vanilla JavaScript with CSS animations
- **Scalable**: Can handle multiple concurrent requests

## 🔍 Error Handling

### Common Errors
- **Invalid Range**: Min value greater than max value
- **Out of Bounds**: Values outside supported ranges
- **Invalid Type**: Unsupported generator type
- **Server Errors**: Graceful handling of internal errors

### Error Prevention
- **Frontend Validation**: Prevent invalid inputs before API calls
- **Range Checking**: Automatic adjustment of related values
- **User Feedback**: Clear error messages and guidance

## 🚀 Getting Started

1. **Setup**: Place files in web server directory
2. **Test API**: Visit `/randomizer/api/?type=number` to test
3. **Use Interface**: Open `/randomizer/` in web browser
4. **Customize**: Modify parameters and generate random values

## 📊 Examples

### Number Generation
- Random lottery numbers: `min=1, max=49`
- Percentage values: `min=0, max=100`
- Large ranges: `min=1000000, max=9999999`

### Gaming Applications
- D&D dice rolls: Various sided dice combinations
- Card games: Draw specific number of cards
- Random events: Coin flips for decisions

### Development Testing
- Random test data generation
- Mock API responses
- User ID generation

## 🔗 Integration

### JavaScript Frontend
```javascript
async function getRandomNumber(min, max) {
    const response = await fetch(`/randomizer/api/?type=number&min=${min}&max=${max}`);
    return await response.json();
}
```

### PHP Backend
```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://yourserver/randomizer/api/?type=dice&sides=6&count=2");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
```

## 📄 License

This project is open source and available under standard web development practices.

## 🤝 Contributing

Contributions are welcome! Please ensure all changes maintain the existing API compatibility and include appropriate documentation.

## 📞 Support

For issues or questions, please refer to the API documentation or check the error messages returned by the API for guidance.
