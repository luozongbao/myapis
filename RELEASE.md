# 📋 MyAPIs Release Notes

## Current Release: Version 1.3.1

**Release Date**: September 9, 2025  
**Status**: Stable Release  

---

## 📚 Version 1.3.1 - API Documentation Enhancement
*Released: September 9, 2025*

### 🌟 New Features

#### 📖 Comprehensive API Documentation
- **Interactive API Specs**: Added `spec.php` files for all tools with comprehensive documentation
- **Enhanced Navigation**: Added navigation links to API documentation from all web interfaces
- **Consistent Design**: Beautiful, responsive documentation pages with consistent styling
- **Complete Examples**: Detailed request/response examples for all API endpoints
- **Error Documentation**: Comprehensive error codes and troubleshooting guides
- **Integration Examples**: Code examples in multiple programming languages

#### 🔗 Improved User Experience
- **Main Interface Enhancement**: Added "API Docs" buttons to all tool cards
- **Tool Navigation**: Added breadcrumb navigation and quick links to API resources
- **Updated Project Structure**: Documentation reflects new file organization
- **Enhanced README**: Updated main README with documentation links and features

### 📁 New Files Added
- `health-calculator/spec.php` - Health Calculator API Documentation
- `password-generator/spec.php` - Password Generator API Documentation  
- `username-generator/spec.php` - Username Generator API Documentation
- `promptpay-qr-generator/spec.php` - PromptPay QR Generator API Documentation
- `fortune-teller/spec.php` - Fortune Teller API Documentation
- `randomizer/spec.php` - Random Generator API Documentation

### 🔄 Enhancements
- **Main README Updated**: Added documentation column to tools table
- **Interface Improvements**: All tools now have consistent navigation
- **Project Structure**: Updated documentation to reflect spec.php additions
- **User Flow**: Seamless navigation between tools, APIs, and documentation

---

## 💧 Version 1.2.0 - Water Intake Calculator Addition
*Released: September 9, 2025*

### 🌟 New Features

#### 🏥 Health Calculator - Water Intake Calculator
- **Daily Water Intake Calculator**: Personalized water requirements based on multiple factors
- **Comprehensive Factors**: Weight, age, gender, activity level, climate, and health conditions
- **Smart Adjustments**: Automatic adjustments for different climates and health conditions
- **Detailed Breakdown**: Shows water from drinks vs food, number of glasses needed
- **Health Condition Support**: Special calculations for pregnancy, breastfeeding, fever, etc.
- **Climate Awareness**: Adjustments for cold, temperate, hot, and very hot climates
- **API Integration**: Full REST API support with detailed responses

### 🔄 Enhancements
- **Updated Interface**: Added fourth calculator tab for water intake
- **Enhanced Documentation**: Complete API documentation with water intake examples
- **Improved Validation**: Added validation for water intake specific parameters

---

## �🚀 Version 1.1.0 - Health Calculator Enhancement
*Released: September 9, 2025*

### 🌟 Major Updates

#### 🏥 Health Calculator (Formerly BMI Calculator)
- **Enhanced Functionality**: Expanded from simple BMI calculator to comprehensive health calculator
- **BMI Calculator**: Body Mass Index calculation with WHO standard categories
- **BMR Calculator**: Basal Metabolic Rate calculation using Mifflin-St Jeor equation
- **Daily Intake Calculator**: Personalized caloric needs with macronutrient breakdown
- **Activity Level Integration**: 5 activity levels from sedentary to extra active
- **Goal-Based Calculations**: Support for weight maintenance, loss, or gain targets
- **Improved UI**: Multi-tab interface for seamless switching between calculators
- **Enhanced API**: Unified endpoint supporting all calculation types
- **Better Documentation**: Comprehensive API documentation with examples for all calculators

### 🔄 Breaking Changes
- **Folder Renamed**: `bmi-calculator/` → `health-calculator/`
- **API Updates**: New parameter structure for BMR and Daily Intake calculations
- **URL Changes**: All links updated to reflect new folder structure

---

## 🎉 Version 1.0.0 - Initial Release
*Released: September 9, 2025*

### 🌟 New Features

#### 🚀 Core Platform
- **Landing Page**: Beautiful gradient-based responsive homepage with tool grid
- **Unified Design**: Consistent UI/UX across all tools and APIs
- **Mobile Responsive**: Full mobile optimization for all interfaces
- **Statistics Dashboard**: Real-time platform statistics display

#### 🏥 Health Calculator
- **Multi-Calculator Interface**: BMI, BMR, and Daily Intake calculators in one tool
- **BMI Calculator**: Body Mass Index with WHO standard categories
- **BMR Calculator**: Basal Metabolic Rate using Mifflin-St Jeor equation
- **Daily Intake Calculator**: Personalized caloric needs with macronutrient breakdown
- **Activity Level Support**: 5 activity levels from sedentary to extra active
- **Goal-Based Calculations**: Weight maintenance, loss, or gain targets
- **Multi-Unit Support**: Metric (kg/cm) and Imperial (lbs/inches)
- **Health Insights**: Personalized recommendations for each calculation type
- **REST API**: Comprehensive JSON-based health calculation endpoints

#### 🔐 Password Generator
- **Advanced Generation**: Cryptographically secure password creation
- **Customizable Options**: Length control (1-128 characters)
- **Character Sets**: Lowercase, uppercase, numbers, symbols
- **Security Features**: Exclude ambiguous characters, prevent repetition
- **Batch Generation**: Generate multiple passwords simultaneously
- **Strength Analysis**: Real-time password strength evaluation

#### 👤 Username Generator
- **Themed Categories**: 6 different word themes for username generation
- **Cross-Theme Combinations**: Mix themes for unique usernames
- **Large Word Pool**: 100+ adjectives and themed nouns
- **Bulk Generation**: Create multiple usernames at once
- **API Integration**: RESTful API for programmatic access

#### 💳 PromptPay QR Generator
- **EMV Compliance**: Full EMV QR Code standard implementation
- **Multiple ID Types**: Phone numbers, Tax IDs, e-Wallet IDs
- **QR Code Generation**: High-quality QR code image output
- **Base64 Support**: Direct base64 image encoding
- **Thai Payment System**: Native PromptPay integration

#### 🔮 Fortune Teller
- **Multilingual Support**: Thai, Chinese, and English predictions
- **52 Unique Fortunes**: Diverse fortune database
- **Life Categories**: Love, Career, Health, Finance, General
- **Beautiful Interface**: Mystical design with smooth animations
- **Random Selection**: Cryptographically secure fortune picking

#### 🎲 Random Generator
- **Multiple Generators**: Numbers, dice, coins, playing cards
- **Animated Results**: Beautiful CSS animations for interactions
- **Range Control**: Flexible number range generation
- **Visual Feedback**: Interactive dice, coin flip, and card animations
- **Secure Randomization**: Cryptographically secure random generation

### 🔧 Technical Features

#### 🛠️ Architecture
- **Pure PHP**: No external dependencies required
- **RESTful APIs**: Consistent JSON-based API responses
- **Error Handling**: Comprehensive error management across all tools
- **Input Validation**: Robust input sanitization and validation
- **CORS Support**: Cross-origin request handling

#### 🔒 Security
- **Secure Random**: `random_int()` for cryptographic security
- **Input Sanitization**: SQL injection and XSS prevention
- **Error Responses**: Secure error handling without information leakage
- **Validation**: Comprehensive input range and type validation

#### 📱 User Experience
- **Responsive Design**: Mobile-first approach
- **Loading States**: User feedback during API operations
- **Copy Functionality**: One-click copying for generated content
- **Visual Feedback**: Color-coded results and status indicators
- **Smooth Animations**: CSS transitions and hover effects

### 📊 Platform Statistics
- **Tools**: 6 active tools
- **APIs**: 12 REST endpoints
- **Languages**: 3 supported languages (Thai, Chinese, English)
- **Response Time**: Average < 100ms
- **Uptime Target**: 99.9%

### 🔄 API Endpoints

| Tool | Method | Endpoint | Description |
|------|--------|----------|-------------|
| Health Calculator | POST | `/health-calculator/api/` | Calculate BMI, BMR, and Daily Intake |
| Password Generator | POST | `/password-generator/api/` | Generate secure passwords |
| Username Generator | POST | `/username-generator/api/` | Create unique usernames |
| PromptPay QR | POST | `/promptpay-qr-generator/api/` | Generate PromptPay QR codes |
| Fortune Teller | GET | `/fortune-teller/api/` | Get random fortune prediction |
| Random Generator | POST | `/randomizer/api/` | Generate random numbers/objects |

### 📁 File Structure
```
myapis/
├── 📄 index.php (Main landing page)
├── 📝 README.md (Project documentation)
├── 📋 RELEASE.md (This file)
├── 📁 health-calculator/ (Health calculation tool)
├── 📁 password-generator/ (Password generation tool)
├── 📁 username-generator/ (Username generation tool)
├── 📁 promptpay-qr-generator/ (QR code generation tool)
├── 📁 fortune-teller/ (Fortune prediction tool)
└── 📁 randomizer/ (Random generation tool)
```

### 🧪 Testing
- Manual testing completed for all web interfaces
- API endpoint testing with various input scenarios
- Cross-browser compatibility verified
- Mobile responsiveness tested on multiple devices
- Security testing for input validation

### 📋 Known Issues
- None reported for this initial release

### 🔮 Future Roadmap

#### Version 1.1.0 (Planned)
- **User Authentication**: Optional user accounts for saving preferences
- **Rate Limiting**: API rate limiting for production use
- **Analytics**: Basic usage analytics and reporting
- **Database Integration**: Optional database storage for generated content
- **Themes**: Multiple UI themes and customization options

#### Version 1.2.0 (Planned)
- **Additional Tools**: Text manipulation, URL shortener, Hash generator
- **API Documentation**: Interactive API documentation with Swagger
- **Webhook Support**: Webhook integration for external services
- **Bulk Operations**: Enhanced bulk generation capabilities
- **Export Features**: PDF/CSV export for generated content

#### Version 2.0.0 (Future)
- **Microservices**: Split tools into individual microservices
- **Docker Support**: Containerization for easy deployment
- **Advanced Analytics**: Detailed usage analytics and insights
- **Plugin System**: Extensible plugin architecture
- **Enterprise Features**: Advanced security and management features

### 🔧 System Requirements

#### Minimum Requirements
- **PHP**: 7.0 or higher
- **Web Server**: Apache, Nginx, or PHP built-in server
- **Memory**: 128MB RAM
- **Storage**: 50MB disk space

#### Recommended Requirements
- **PHP**: 8.0 or higher
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Extensions**: GD library for QR code generation
- **Memory**: 256MB RAM
- **Storage**: 100MB disk space

### 🚀 Deployment

#### Development
```bash
git clone https://github.com/luozongbao/myapis.git
cd myapis
php -S localhost:8000
```

#### Production
- Deploy to web server document root
- Configure virtual host/server block
- Set appropriate file permissions (644 for files, 755 for directories)
- Enable PHP and required extensions

### 👥 Contributors

- **Lead Developer**: [luozongbao](https://github.com/luozongbao)
- **Repository**: [myapis](https://github.com/luozongbao/myapis)

### 📞 Support

For support, bug reports, or feature requests:
- **Issues**: [GitHub Issues](https://github.com/luozongbao/myapis/issues)
- **Documentation**: Individual tool README files
- **Contact**: Repository maintainer via GitHub

### 📝 Changelog Format

This changelog follows the format:
- 🌟 **New Features**: New functionality and tools
- 🔧 **Technical Improvements**: Architecture and performance updates
- 🐛 **Bug Fixes**: Issue resolutions
- 📚 **Documentation**: Documentation updates
- 🚨 **Breaking Changes**: Compatibility-breaking updates
- 🔒 **Security**: Security-related updates

---

**Thank you for using MyAPIs! 🎉**

*For the latest updates and releases, please check the [GitHub repository](https://github.com/luozongbao/myapis).*
