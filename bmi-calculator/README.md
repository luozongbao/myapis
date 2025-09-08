# BMI Calculator API & Web Interface

A comprehensive Body Mass Index (BMI) calculator with both a REST API and a responsive web interface. Calculate BMI using metric or imperial units and get health recommendations based on WHO standards.

## Features

### 🎯 BMI Calculator API
- **Multiple input formats**: JSON POST requests and GET parameters
- **Unit support**: Metric (kg/cm) and Imperial (lbs/inches)
- **Comprehensive validation**: Input sanitization and range checking
- **Health categorization**: WHO standard BMI categories
- **Personalized advice**: Health recommendations for each category
- **CORS enabled**: Cross-origin request support
- **Error handling**: Detailed error messages with HTTP status codes

### 🌐 Web Interface
- **Responsive design**: Works on desktop, tablet, and mobile devices
- **Modern UI**: Beautiful gradient design with smooth animations
- **Unit switching**: Toggle between metric and imperial units
- **Real-time validation**: Client-side input validation
- **Color-coded results**: Visual feedback based on BMI category
- **Loading states**: User feedback during API calls
- **Error handling**: User-friendly error messages

## Installation

1. Clone or download the project to your web server
2. Ensure PHP is installed (version 7.0 or higher recommended)
3. Place the files in your web server's document root or subdirectory
4. Ensure the web server has read permissions for all files

## Usage

### Web Interface

1. Open `index.php` in your web browser
2. Select your preferred unit system (Metric or Imperial)
3. Enter your weight and height
4. Click "Calculate BMI" to get your results

### API Usage

#### Endpoint
```
POST /api/
GET /api/
```

#### Parameters
- `weight` (required): Weight value (kg for metric, lbs for imperial)
- `height` (required): Height value (cm or m for metric, inches for imperial)
- `unit` (optional): "metric" or "imperial" (default: "metric")

#### Example Requests

**POST Request (JSON):**
```bash
curl -X POST http://your-domain.com/bmi-calculator/api/ \
  -H "Content-Type: application/json" \
  -d '{"weight": 70, "height": 175, "unit": "metric"}'
```

**GET Request:**
```bash
curl "http://your-domain.com/bmi-calculator/api/?weight=154&height=69&unit=imperial"
```

#### Example Response
```json
{
    "success": true,
    "data": {
        "bmi": 22.86,
        "category": "Normal weight",
        "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise.",
        "input": {
            "weight": 70,
            "height": 1.75,
            "unit": "metric"
        }
    },
    "timestamp": "2025-09-08 12:34:56"
}
```

## BMI Categories

| BMI Range | Category | Color Code |
|-----------|----------|------------|
| < 18.5 | Underweight | Blue |
| 18.5 - 24.9 | Normal weight | Green |
| 25.0 - 29.9 | Overweight | Yellow |
| ≥ 30.0 | Obese | Red |

## File Structure

```
bmi-calculator/
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

This BMI calculator is for informational purposes only and should not be used as a substitute for professional medical advice. BMI is a screening tool and may not be accurate for all individuals, including athletes with high muscle mass or elderly individuals. Please consult with a healthcare professional for personalized health advice.

## License

This project is open source and available under the MIT License.

## Contributing

Feel free to submit issues, feature requests, or pull requests to improve this BMI calculator.

## Support

For support or questions, please open an issue in the project repository.
