# 🚀 MyAPIs - Developer Tools Collection

A comprehensive collection of developer tools and APIs designed to streamline your development workflow. Each tool provides both a beautiful web interface and a robust REST API for easy integration.

## 🌟 Features

### 📊 Available Tools

| Tool | Description | Web Interface | API |
|------|-------------|---------------|-----|
| ⚖️ **BMI Calculator** | Calculate Body Mass Index with WHO standard categories | [Try Tool](bmi-calculator/) | [API Docs](bmi-calculator/api/) |
| 🔐 **Password Generator** | Generate cryptographically secure passwords | [Try Tool](password-generator/) | [API Docs](password-generator/api/) |
| 👤 **Username Generator** | Create unique usernames using word combinations | [Try Tool](username-generator/) | [API Docs](username-generator/api/) |
| 💳 **PromptPay QR Generator** | Generate EMV-compliant PromptPay QR codes | [Try Tool](promptpay-qr-generator/) | [API Docs](promptpay-qr-generator/api/) |
| 🔮 **Fortune Teller** | Get multilingual fortune predictions | [Try Tool](fortune-teller/) | [API Docs](fortune-teller/api/) |
| 🎲 **Random Generator** | Generate random numbers, dice, coins, and cards | [Try Tool](randomizer/) | [API Docs](randomizer/api/) |

### 🎯 Key Features

- **🌐 Modern Web Interfaces**: Beautiful, responsive designs that work on all devices
- **🔌 REST APIs**: Well-documented APIs with JSON responses
- **🔒 Security First**: Cryptographically secure random generation
- **🌍 Multi-language Support**: Thai, Chinese, and English support where applicable
- **📱 Mobile Responsive**: Optimized for desktop, tablet, and mobile
- **⚡ Fast & Lightweight**: Pure PHP implementation with minimal dependencies
- **🔄 CORS Enabled**: Cross-origin request support for web applications

## 🚀 Quick Start

### Prerequisites

- PHP 7.0 or higher
- Web server (Apache, Nginx, or built-in PHP server)
- Optional: GD extension for QR code generation

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/luozongbao/myapis.git
   cd myapis
   ```

2. **Set up web server**
   
   **Option A: Using PHP built-in server (Development)**
   ```bash
   php -S localhost:8000
   ```
   
   **Option B: Apache/Nginx**
   - Copy files to your web server's document root
   - Ensure PHP is configured and enabled
   - Set appropriate file permissions

3. **Access the application**
   - Open your browser and navigate to `http://localhost:8000` (or your server URL)
   - Explore the tools and their APIs

## 📖 API Documentation

All tools provide RESTful APIs with consistent response formats:

### Common Response Format
```json
{
  "success": true,
  "data": { ... },
  "message": "Success message",
  "timestamp": "2025-09-09T12:00:00Z"
}
```

### Error Response Format
```json
{
  "success": false,
  "error": "Error description",
  "code": "ERROR_CODE",
  "timestamp": "2025-09-09T12:00:00Z"
}
```

### Individual Tool APIs

Each tool has its own API endpoint and documentation:

- **BMI Calculator API**: `POST /bmi-calculator/api/` - Calculate BMI with health recommendations
- **Password Generator API**: `POST /password-generator/api/` - Generate secure passwords
- **Username Generator API**: `POST /username-generator/api/` - Create unique usernames
- **PromptPay QR API**: `POST /promptpay-qr-generator/api/` - Generate PromptPay QR codes
- **Fortune Teller API**: `GET /fortune-teller/api/` - Get random fortune predictions
- **Random Generator API**: `POST /randomizer/api/` - Generate random numbers, dice, etc.

## 🛠️ Usage Examples

### BMI Calculator
```bash
curl -X POST "http://localhost:8000/bmi-calculator/api/" \
  -H "Content-Type: application/json" \
  -d '{"weight": 70, "height": 175, "unit": "metric"}'
```

### Password Generator
```bash
curl -X POST "http://localhost:8000/password-generator/api/" \
  -H "Content-Type: application/json" \
  -d '{"length": 16, "uppercase": true, "lowercase": true, "numbers": true, "symbols": true}'
```

### Fortune Teller
```bash
curl "http://localhost:8000/fortune-teller/api/?lang=en"
```

## 📁 Project Structure

```
myapis/
├── index.php                 # Main landing page
├── README.md                 # This file
├── RELEASE.md                # Release notes
├── bmi-calculator/           # BMI Calculator tool
│   ├── index.php            # Web interface
│   ├── api/index.php        # REST API
│   └── README.md            # Tool documentation
├── password-generator/       # Password Generator tool
│   ├── index.php            # Web interface
│   ├── api/index.php        # REST API
│   └── README.md            # Tool documentation
├── username-generator/       # Username Generator tool
│   ├── index.php            # Web interface
│   ├── api/index.php        # REST API
│   └── README.md            # Tool documentation
├── promptpay-qr-generator/   # PromptPay QR Generator tool
│   ├── index.php            # Web interface
│   ├── api/index.php        # REST API
│   ├── assets/              # Static assets
│   └── README.md            # Tool documentation
├── fortune-teller/           # Fortune Teller tool
│   ├── index.php            # Web interface
│   ├── api/index.php        # REST API
│   ├── predictions/         # Fortune data files
│   └── README.md            # Tool documentation
└── randomizer/               # Random Generator tool
    ├── index.php            # Web interface
    ├── api/index.php        # REST API
    └── README.md            # Tool documentation
```

## 🔧 Development

### Adding a New Tool

1. Create a new directory for your tool
2. Add `index.php` for the web interface
3. Add `api/index.php` for the REST API
4. Create tool-specific `README.md` with documentation
5. Update the main `index.php` to include your tool in the grid

### Code Standards

- Follow PSR standards for PHP code
- Use consistent error handling across APIs
- Implement proper input validation and sanitization
- Ensure mobile-responsive web interfaces
- Document all API endpoints thoroughly

## 📊 Statistics

- **6** Active Tools
- **12** API Endpoints
- **100%** Uptime Target
- **PHP** Technology Stack

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-tool`)
3. Commit your changes (`git commit -m 'Add amazing tool'`)
4. Push to the branch (`git push origin feature/amazing-tool`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 🔗 Links

- **Repository**: [GitHub](https://github.com/luozongbao/myapis)
- **Live Demo**: [MyAPIs Platform](https://api.lorwongam.com)
- **Documentation**: Each tool includes comprehensive README files
- **Issues**: [Report bugs or request features](https://github.com/luozongbao/myapis/issues)

## 💬 Support

- Check individual tool README files for specific documentation
- Open an issue on GitHub for bug reports or feature requests
- Contact the maintainer for questions or collaboration

---

**Built with ❤️ for developers by developers**
