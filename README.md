# 🚀 MyAPIs - Dev### 🎯 Key Features

- **🌐 Modern Web Interfaces**: Beautiful, responsive designs that work on all devices
- **🔌 REST APIs**: Well-documented APIs with JSON responses
- **📚 Interactive API Documentation**: Server-agnostic documentation with dynamic URLs
- **🔒 Security First**: Cryptographically secure random generation
- **🌍 Multi-language Support**: Thai, Chinese, and English support where applicable
- **📱 Mobile Responsive**: Optimized for desktop, tablet, and mobile
- **⚡ Fast & Lightweight**: Pure PHP implementation with minimal dependencies
- **🔄 CORS Enabled**: Cross-origin request support for web applications
- **🏗️ Clean Architecture**: Organized public/api structure for easy deployment
- **🌐 Dynamic URLs**: Server-agnostic URLs that adapt to any hosting environmentls Collection

A comprehensive collection of developer tools and APIs designed to streamline your development workflow. Each tool provides both a beautiful web interface and a robust REST API for easy integration.

## 🌟 Features

### 📊 Available Tools

| Tool | Description | Web Interface | API | Documentation |
|------|-------------|---------------|-----|---------------|
| 🏥 **Health Calculator** | Calculate BMI, BMR, Daily Intake, and Water Intake with health recommendations | [Try Tool](public/health-calculator.php) | [API](api/health-calculator/) | [Full Specs](public/api-specs/health-calculator.php) |
| 🔐 **Password Generator** | Generate cryptographically secure passwords | [Try Tool](public/password-generator.php) | [API](api/password-generator/) | [Full Specs](public/api-specs/password-generator.php) |
| 👤 **Username Generator** | Create unique usernames using word combinations | [Try Tool](public/username-generator.php) | [API](api/username-generator/) | [Full Specs](public/api-specs/username-generator.php) |
| 💳 **PromptPay QR Generator** | Generate EMV-compliant PromptPay QR codes | [Try Tool](public/promptpay-qr-generator.php) | [API](api/promptpay-qr-generator/) | [Full Specs](public/api-specs/promptpay-qr-generator.php) |
| 🔮 **Fortune Teller** | Get multilingual fortune predictions | [Try Tool](public/fortune-teller.php) | [API](api/fortune-teller/) | [Full Specs](public/api-specs/fortune-teller.php) |
| 🎲 **Random Generator** | Generate random numbers, dice, coins, and cards | [Try Tool](public/randomizer.php) | [API](api/randomizer/) | [Full Specs](public/api-specs/randomizer.php) |

### 🎯 Key Features

- **🌐 Modern Web Interfaces**: Beautiful, responsive designs that work on all devices
- **🔌 REST APIs**: Well-documented APIs with JSON responses
- **� Comprehensive API Documentation**: Interactive documentation for all endpoints
- **�🔒 Security First**: Cryptographically secure random generation
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
   - Open your browser and navigate to `http://localhost:8000/public/` (or your server URL + `/public/`)
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

- **Health Calculator API**: `POST /api/health-calculator/` - Calculate BMI, BMR, Daily Intake, and Water Intake
- **Password Generator API**: `POST /api/password-generator/` - Generate secure passwords
- **Username Generator API**: `POST /api/username-generator/` - Create unique usernames
- **PromptPay QR API**: `POST /api/promptpay-qr-generator/` - Generate PromptPay QR codes
- **Fortune Teller API**: `GET /api/fortune-teller/` - Get random fortune predictions
- **Random Generator API**: `POST /api/randomizer/` - Generate random numbers, dice, etc.

## 🛠️ Usage Examples

### Health Calculator
```bash
curl -X POST "http://localhost:8000/api/health-calculator/" \
  -H "Content-Type: application/json" \
  -d '{"weight": 70, "height": 175, "unit": "metric", "type": "bmi"}'
```

### Password Generator
```bash
curl -X POST "http://localhost:8000/api/password-generator/" \
  -H "Content-Type: application/json" \
  -d '{"length": 16, "uppercase": true, "lowercase": true, "numbers": true, "symbols": true}'
```

### Fortune Teller
```bash
curl "http://localhost:8000/api/fortune-teller/?lang=en"
```

## 📁 Project Structure

```
myapis/
├── public/                   # Web interfaces and documentation
│   ├── index.php            # Main landing page
│   ├── health-calculator.php # Health Calculator web interface
│   ├── password-generator.php # Password Generator web interface
│   ├── username-generator.php # Username Generator web interface
│   ├── promptpay-qr-generator.php # PromptPay QR Generator web interface
│   ├── fortune-teller.php   # Fortune Teller web interface
│   ├── randomizer.php       # Random Generator web interface
│   └── api-specs/           # API documentation pages
│       ├── health-calculator.php
│       ├── password-generator.php
│       ├── username-generator.php
│       ├── promptpay-qr-generator.php
│       ├── fortune-teller.php
│       └── randomizer.php
├── api/                     # REST API implementations
│   ├── health-calculator/   # Health Calculator API
│   │   └── index.php
│   ├── password-generator/  # Password Generator API
│   │   └── index.php
│   ├── username-generator/  # Username Generator API
│   │   └── index.php
│   ├── promptpay-qr-generator/ # PromptPay QR Generator API
│   │   └── index.php
│   ├── fortune-teller/      # Fortune Teller API
│   │   ├── index.php
│   │   └── predictions/     # Fortune data files
│   └── randomizer/          # Random Generator API
│       └── index.php
├── README.md                # This file
└── RELEASE.md               # Release notes
```

## 🔧 Development

### Adding a New Tool

1. Create the API implementation in `api/your-tool-name/index.php`
2. Create the web interface in `public/your-tool-name.php`
3. Create API documentation in `public/api-specs/your-tool-name.php`
4. Update the main `public/index.php` to include your tool in the grid
5. Test both web interface and API endpoints

### Code Standards

- Follow PSR standards for PHP code
- Use consistent error handling across APIs
- Implement proper input validation and sanitization
- Ensure mobile-responsive web interfaces
- Document all API endpoints thoroughly
- Use dynamic URLs for server-agnostic deployment
- Maintain separation between public interfaces and API logic

## 📊 Statistics

- **6** Active Tools
- **6** API Endpoints  
- **6** Interactive Documentation Pages
- **Clean Architecture** with public/api separation
- **Dynamic URLs** for any server environment
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

---

## 🚀 Latest Updates (v2.0.0)

### 🏗️ Major Architecture Restructuring
- **Clean Separation**: New `public/` and `api/` directory structure
- **Dynamic URLs**: Server-agnostic documentation that works on any domain
- **Enhanced Documentation**: Interactive API specs with corrected parameters
- **Improved Navigation**: Seamless integration between tools, APIs, and documentation
- **Bug Fixes**: Resolved all web interface issues and API endpoint problems

### 📚 Enhanced Documentation  
- **Accurate API Specs**: All parameters and examples verified against actual implementations
- **Interactive Examples**: Working code samples with dynamic server URLs
- **Centralized Docs**: All API documentation organized in `public/api-specs/`
- **Better UX**: Improved navigation and user experience across all interfaces
