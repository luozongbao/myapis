# Health Calculator API & Web Interface

A comprehensive health calculator with both a REST API and a responsive web interface. Calculate BMI, Basal Metabolic Rate (BMR), and Daily Caloric Intake using metric or imperial units with personalized health recommendations.

## Features

### 🎯 Health Calculator API
- **BMI Calculator**: Body Mass Index calculation with WHO standard categories
- **BMR Calculator**: Basal Metabolic Rate using Mifflin-St Jeor equation
- **Daily Intake Calculator**: Personalized caloric needs based on goals
- **Multiple input formats**: JSON POST requests and GET parameters
- **Unit support**: Metric (kg/cm) and Imperial (lbs/inches)
- **Comprehensive validation**: Input sanitization and range checking
- **Health categorization**: WHO standard BMI categories and health recommendations
- **Activity level factors**: Sedentary to extra active lifestyle adjustments
- **Goal-based calculations**: Weight maintenance, loss, or gain targets
- **CORS enabled**: Cross-origin request support
- **Error handling**: Detailed error messages with HTTP status codes

### 🌐 Web Interface
- **Multi-calculator interface**: Switch between BMI, BMR, and Daily Intake calculators
- **Responsive design**: Works on desktop, tablet, and mobile devices
- **Modern UI**: Beautiful gradient design with smooth animations
- **Unit switching**: Toggle between metric and imperial units
- **Real-time validation**: Client-side input validation
- **Color-coded results**: Visual feedback based on health categories
- **Loading states**: User feedback during API calls
- **Error handling**: User-friendly error messages
- **Detailed results**: Comprehensive breakdowns and recommendations

## Installation

1. Clone or download the project to your web server
2. Ensure PHP is installed (version 7.0 or higher recommended)
3. Place the files in your web server's document root or subdirectory
4. Ensure the web server has read permissions for all files

## Usage

### Web Interface

1. Open `index.php` in your web browser
2. Select your preferred calculator (BMI, BMR, or Daily Intake)
3. Select your preferred unit system (Metric or Imperial)
4. Fill in the required fields for your chosen calculator
5. Click the calculate button to get your results

### API Usage

#### Endpoint
```
POST /api/
GET /api/
```

#### Common Parameters
- `calculator` (required): "bmi", "bmr", or "intake"
- `weight` (required): Weight value (kg for metric, lbs for imperial)
- `height` (required): Height value (cm or m for metric, inches for imperial)
- `unit` (optional): "metric" or "imperial" (default: "metric")

#### BMI Calculator Parameters
- `weight` (required): Weight value
- `height` (required): Height value

#### BMR Calculator Parameters
- `weight` (required): Weight value
- `height` (required): Height value
- `age` (required): Age in years
- `gender` (required): "male" or "female"
- `activity` (required): Activity level ("sedentary", "light", "moderate", "active", "extra")

#### Daily Intake Calculator Parameters
- `weight` (required): Weight value
- `height` (required): Height value
- `age` (required): Age in years
- `gender` (required): "male" or "female"
- `activity` (required): Activity level ("sedentary", "light", "moderate", "active", "extra")
- `goal` (required): Goal ("maintain", "lose", "lose-fast", "gain", "gain-fast")

#### Example Requests

**BMI Calculator (POST):**
```bash
curl -X POST http://your-domain.com/health-calculator/api/ \
  -H "Content-Type: application/json" \
  -d '{"calculator": "bmi", "weight": 70, "height": 175, "unit": "metric"}'
```

**BMR Calculator (POST):**
```bash
curl -X POST http://your-domain.com/health-calculator/api/ \
  -H "Content-Type: application/json" \
  -d '{"calculator": "bmr", "weight": 70, "height": 175, "age": 30, "gender": "male", "activity": "moderate", "unit": "metric"}'
```

**Daily Intake Calculator (POST):**
```bash
curl -X POST http://your-domain.com/health-calculator/api/ \
  -H "Content-Type: application/json" \
  -d '{"calculator": "intake", "weight": 70, "height": 175, "age": 30, "gender": "male", "activity": "moderate", "goal": "maintain", "unit": "metric"}'
```

**GET Request Example:**
```bash
curl "http://your-domain.com/health-calculator/api/?calculator=bmi&weight=154&height=69&unit=imperial"
```

#### Example Responses

**BMI Response:**
```json
{
    "success": true,
    "data": {
        "bmi": 22.86,
        "category": "Normal weight",
        "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise."
    },
    "calculator": "bmi",
    "timestamp": "2025-09-09 12:34:56"
}
```

**BMR Response:**
```json
{
    "success": true,
    "data": {
        "bmr": 1743,
        "detail": "Daily calories needed: 2700",
        "advice": "Your BMR is 1743 calories per day. With your activity level, you need approximately 2700 calories daily to maintain your current weight."
    },
    "calculator": "bmr",
    "timestamp": "2025-09-09 12:34:56"
}
```

**Daily Intake Response:**
```json
{
    "success": true,
    "data": {
        "calories": 2700,
        "breakdown": "Protein: 112g • Carbs: 338g • Fat: 75g<br>BMR: 1743 cal • Maintenance: 2700 cal",
        "advice": "To maintain your current weight, aim for 2700 calories per day with balanced nutrition and regular exercise.",
        "macros": {
            "protein": 112,
            "carbs": 338,
            "fat": 75
        }
    },
    "calculator": "intake",
    "timestamp": "2025-09-09 12:34:56"
}
```

## Health Categories & Activity Levels

### BMI Categories
| BMI Range | Category | Color Code |
|-----------|----------|------------|
| < 18.5 | Underweight | Blue |
| 18.5 - 24.9 | Normal weight | Green |
| 25.0 - 29.9 | Overweight | Yellow |
| ≥ 30.0 | Obese | Red |

### Activity Levels
| Level | Description | Multiplier |
|-------|-------------|------------|
| Sedentary | Little or no exercise | 1.2 |
| Light | Light exercise 1-3 days/week | 1.375 |
| Moderate | Moderate exercise 3-5 days/week | 1.55 |
| Active | Hard exercise 6-7 days/week | 1.725 |
| Extra Active | Very hard exercise, physical job | 1.9 |

### Weight Goals
| Goal | Description | Caloric Adjustment |
|------|-------------|-------------------|
| Maintain | Maintain current weight | 0 calories |
| Lose | Lose 0.5 kg/week | -500 calories |
| Lose Fast | Lose 1 kg/week | -1000 calories |
| Gain | Gain 0.5 kg/week | +500 calories |
| Gain Fast | Gain 1 kg/week | +1000 calories |

## File Structure

```
health-calculator/
├── index.php          # Web interface
├── api/
│   └── index.php      # REST API endpoint
└── README.md          # This file
```

## API Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad Request (missing or invalid parameters) |
| 500 | Internal Server Error |

## Requirements

- PHP 7.0 or higher
- Web server (Apache, Nginx, etc.)
- cURL extension (for API calls from web interface)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Security Features

- Input validation and sanitization
- Range checking for realistic values
- CORS protection
- Error message sanitization

## Health Disclaimer

This Health Calculator is for informational purposes only and should not be used as a substitute for professional medical advice. The calculations provided (BMI, BMR, Daily Intake) are estimates based on standard formulas and may not be accurate for all individuals, including:

- Athletes with high muscle mass
- Elderly individuals
- Pregnant or breastfeeding women
- Individuals with medical conditions
- People with metabolic disorders

BMI is a screening tool and may not accurately reflect body composition. BMR and caloric intake calculations are estimates and individual needs may vary significantly. Please consult with a healthcare professional, registered dietitian, or certified nutritionist for personalized health and nutrition advice.

## License

This project is open source and available under the MIT License.

## Contributing

Feel free to submit issues, feature requests, or pull requests to improve this BMI calculator.

## Support

For support or questions, please open an issue in the project repository.
