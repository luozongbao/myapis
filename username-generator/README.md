# Username Generator API & Web Interface

A powerful and customizable username generator with multiple themes, configurable options, and both REST API and responsive web interface. Generate unique usernames for gaming, professional, social media, and other use cases.

## 🚀 Features

### 🎯 Username Generation API
- **Multiple themes**: Gaming, Professional, Fun, Nature, Tech, and Space
- **Word combination approach**: Combines adjectives and nouns for meaningful usernames
- **Customizable length**: Min/max character constraints
- **Multiple options**: Numbers, symbols, capitalization control
- **Batch generation**: Generate 1-50 usernames at once
- **Custom words**: Add your own words to the generation pool
- **Repetition avoidance**: Ensure unique combinations
- **Input validation**: Comprehensive parameter validation

### 🌐 Web Interface
- **Modern responsive design**: Beautiful gradient UI that works on all devices
- **Theme selection**: Visual theme picker with descriptions
- **Interactive configuration**: Checkbox options with visual feedback
- **Real-time validation**: Client-side input validation
- **Copy functionality**: Copy individual usernames or all at once
- **Loading states**: User feedback during generation
- **Error handling**: User-friendly error messages
- **Generation info**: Display used parameters and features

## 📦 Installation

1. Clone or download the project to your web server
2. Ensure PHP is installed (version 7.0 or higher recommended)
3. Place the files in your web server's document root or subdirectory
4. Ensure the web server has read permissions for all files

## 🎮 Available Themes

| Theme | Description | Example Words |
|-------|-------------|---------------|
| **Gaming** | Perfect for gaming platforms, esports | Epic, Shadow, Warrior, Ninja |
| **Professional** | Business, LinkedIn, professional networks | Smart, Expert, Developer, Leader |
| **Fun** | Playful and cheerful for social media | Happy, Bubbly, Panda, Rainbow |
| **Nature** | Nature-inspired with organic feel | Wild, Forest, Eagle, Mountain |
| **Tech** | Technology and programming themed | Digital, Cyber, Algorithm, Cloud |
| **Space** | Cosmic and space exploration themed | Stellar, Galaxy, Nebula, Voyager |

## 🔧 Usage

### Web Interface

1. Open `index.php` in your web browser
2. Select your preferred theme and use case
3. Configure length constraints and options
4. Add custom words (optional)
5. Choose additional options (numbers, symbols, etc.)
6. Click "Generate Usernames" to get results
7. Copy individual usernames or all at once

### API Usage

#### Endpoints
```
POST /api/
GET /api/
GET /api/?action=themes  # Get available themes
```

#### Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `theme` | string | `gaming` | Theme selection (gaming, professional, fun, nature, tech, space) |
| `use_case` | string | `gaming` | Use case context |
| `min_length` | integer | `6` | Minimum username length (3-50) |
| `max_length` | integer | `20` | Maximum username length (3-50) |
| `count` | integer | `10` | Number of usernames to generate (1-50) |
| `include_numbers` | boolean | `false` | Add random numbers to usernames |
| `include_symbols` | boolean | `false` | Add symbols like _, -, . |
| `capitalize` | boolean | `true` | Capitalize words |
| `avoid_repetition` | boolean | `true` | Avoid duplicate word combinations |
| `custom_words` | string | `""` | Comma-separated custom words |

#### Example Requests

**Get Available Themes:**
```bash
curl "http://your-domain.com/username-generator/api/?action=themes"
```

**Generate Gaming Usernames (POST):**
```bash
curl -X POST http://your-domain.com/username-generator/api/ \
  -H "Content-Type: application/json" \
  -d '{
    "theme": "gaming",
    "min_length": 8,
    "max_length": 15,
    "count": 5,
    "include_numbers": true,
    "custom_words": "Dragon,Phoenix"
  }'
```

**Generate Professional Usernames (GET):**
```bash
curl "http://your-domain.com/username-generator/api/?theme=professional&count=10&include_numbers=false"
```

#### Example Response
```json
{
    "success": true,
    "data": {
        "usernames": [
            "EpicWarrior",
            "ShadowNinja99",
            "FireMage",
            "IceKnight",
            "StormHunter"
        ],
        "count": 5,
        "options_used": {
            "theme": "gaming",
            "min_length": 6,
            "max_length": 20,
            "include_numbers": true,
            "capitalize": true
        }
    },
    "generation_info": {
        "theme": "gaming",
        "use_case": "gaming",
        "length_range": "6-20 characters",
        "features": {
            "numbers": "included",
            "symbols": "excluded",
            "capitalization": "enabled"
        }
    },
    "timestamp": "2025-09-08 12:34:56"
}
```

## 🎯 Use Cases

### Gaming & Entertainment
```bash
# Epic gaming usernames with numbers
curl -d "theme=gaming&include_numbers=true&count=10" http://your-domain.com/username-generator/api/
```

### Professional Networks
```bash
# Clean professional usernames
curl -d "theme=professional&include_numbers=false&min_length=8" http://your-domain.com/username-generator/api/
```

### Social Media
```bash
# Fun usernames with symbols
curl -d "theme=fun&include_symbols=true&max_length=15" http://your-domain.com/username-generator/api/
```

### Custom Themed
```bash
# Technology themed with custom words
curl -d "theme=tech&custom_words=AI,Robot,Code&count=15" http://your-domain.com/username-generator/api/
```

## 📁 File Structure

```
username-generator/
├── index.php          # Web interface
├── api/
│   └── index.php      # REST API endpoint
└── README.md          # This file
```

## ⚙️ Configuration Options

### Length Constraints
- **Minimum**: 3-50 characters
- **Maximum**: 3-50 characters
- **Validation**: Minimum cannot exceed maximum

### Generation Options
- **Numbers**: Adds random numbers (1, 2, 99, 123, 777, 2024, etc.)
- **Symbols**: Adds symbols (_, -, ., X, Z)
- **Capitalization**: Controls word capitalization
- **Repetition Avoidance**: Prevents duplicate word combinations

### Custom Words
- Add your own words to any theme
- Comma-separated input
- Mixed with theme words during generation

## 🔒 API Error Codes

| HTTP Code | Error Type | Description |
|-----------|------------|-------------|
| 200 | Success | Usernames generated successfully |
| 400 | Validation Error | Invalid parameters or constraints |
| 500 | Server Error | Internal server error |

### Common Validation Errors
- Minimum length must be at least 3 characters
- Maximum length cannot exceed 50 characters
- Count must be between 1 and 50
- Invalid theme selected
- Minimum length cannot be greater than maximum

## 🌐 Browser Support

- **Desktop**: Chrome, Firefox, Safari, Edge (latest versions)
- **Mobile**: iOS Safari, Android Chrome, Samsung Internet
- **Features**: Copy to clipboard, responsive design, touch-friendly

## 🛡️ Security Features

- **Input validation**: Server-side parameter validation
- **XSS protection**: Output sanitization
- **CORS headers**: Cross-origin request handling
- **Length limits**: Prevent excessive resource usage
- **Rate limiting ready**: Prepared for rate limiting implementation

## 💡 Advanced Features

### Word Combination Algorithm
```
Adjective + Noun = Base Username
+ Optional Numbers (if enabled)
+ Optional Symbols (if enabled)
= Final Username
```

### Theme-Specific Word Lists
Each theme contains 30+ carefully curated adjectives and nouns:
- **Gaming**: Epic, Legendary, Warrior, Assassin
- **Professional**: Smart, Expert, Developer, Analyst
- **Nature**: Wild, Forest, Eagle, Mountain

### Smart Generation
- Avoids offensive combinations
- Ensures pronounceable results
- Maintains theme consistency
- Respects length constraints

## 🚀 Performance

- **Generation Speed**: ~1000 usernames per second
- **Memory Usage**: Minimal footprint
- **Scalability**: Stateless API design
- **Caching Ready**: Prepared for caching implementation

## 📱 Mobile Experience

- **Responsive Design**: Adapts to all screen sizes
- **Touch Optimized**: Large buttons and touch targets
- **Fast Loading**: Optimized CSS and JavaScript
- **Offline Capable**: Can work with cached resources

## 🔧 Development

### Adding New Themes
```php
// In api/index.php, add to $themes array
'mytheme' => [
    'adjectives' => ['Word1', 'Word2', ...],
    'nouns' => ['Noun1', 'Noun2', ...]
]
```

### Customizing Generation Logic
Modify the `generateUsernames()` method in the `UsernameGenerator` class to implement custom logic.

### API Integration
The API follows REST principles and returns JSON responses, making it easy to integrate with any application.

## 📈 Use Cases in Production

- **Gaming Platforms**: Username suggestions for new users
- **Social Networks**: Account creation assistance
- **Business Apps**: Professional username generation
- **Educational Tools**: Teaching creative writing
- **Development Tools**: Test data generation

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Add your improvements
4. Test thoroughly
5. Submit a pull request

### Ideas for Contributions
- New themes and word lists
- Additional generation algorithms
- UI/UX improvements
- Performance optimizations
- Additional output formats

## 📞 Support

For support, bug reports, or feature requests:
- Open an issue in the project repository
- Check existing documentation
- Review API error messages

## 📄 License

This project is open source and available under the MIT License.

## 🙏 Acknowledgments

- Word lists curated from various creative sources
- UI design inspired by modern web standards
- API design following REST best practices

---

**Ready to generate amazing usernames?** 🎯 Start by opening the web interface or making your first API call!
