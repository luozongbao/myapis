# 📋 MyAPIs Release Notes

## Current Release: Version 2.1.1

**Release Date**: September 11, 2025  
**Status**: Stable Release  

---

## 🔧 Version 2.1.1 - Username Generator Interface Cleanup
*Released: September 11, 2025*

### 🧹 Interface Improvements

#### 📝 Removed Unused Use Case Field
- **Removed `use_case` parameter**: Eliminated the unused "Use Case" dropdown from web interface
- **API Cleanup**: Removed `use_case` from API parameters and response data  
- **Simplified Interface**: Cleaner, more focused interface without confusing non-functional options
- **Documentation Updated**: Updated README.md to reflect parameter changes
- **Backward Compatibility**: Existing API calls will continue to work (parameter simply ignored)

### 📚 Documentation Updates
- **Parameter Documentation**: Updated API parameter table to remove `use_case`
- **Example Requests**: Updated all code examples to exclude the removed parameter
- **Response Examples**: Updated JSON response examples without `use_case` field

---

## 🎯 Version 2.1.0 - Username Generator Enhanced Multi-Theme Support
*Released: September 11, 2025*

### 🌟 Major New Features

#### 🎨 Multi-Theme Username Generation
- **Multiple Theme Selection**: Users can now select multiple themes simultaneously for more diverse username combinations
- **7 Comprehensive Themes**: Updated theme collection with complete word lists:
  - **Fantasy**: Epic, mythical usernames for gaming (Epic, Shadow, Warrior, Dragon, Wizard)
  - **Professional**: Business and LinkedIn-ready usernames (Smart, Expert, Developer, Manager, Director)
  - **Science and Space**: Space exploration and scientific terms (Stellar, Galaxy, Quantum, Atom, Einstein)
  - **Computer Technology**: Programming and tech-focused (Digital, Algorithm, Framework, Docker, JavaScript)
  - **Elements and Chemistry**: Chemistry and periodic elements (Hydrogen, Carbon, Molecular, Crystal, Plasma)
  - **Things**: Everyday objects and items (Fork, Table, Chair, Lamp, Knife)
  - **Body and Health**: Health and anatomy themed (Heart, Brain, Strong, Healthy, Muscle)
  - **Nature**: Lanscape fruites and animals (Mountain, Grape, Fox, Wolf, Banana)
  - **Space and Time**: Usernames inspired by concepts of space and time (Metric, Meter, Hour, Space, Time, Centi)

#### 🔧 Enhanced API Capabilities
- **Multi-Theme Parameter**: New `themes` array parameter allows combining multiple themes
- **Backward Compatibility**: Legacy `theme` parameter still supported for single-theme selection
- **GET Support**: Multi-theme selection via comma-separated values in GET requests
- **Enhanced Response**: Generation info includes selected themes count and theme list

#### 🖥️ Improved Web Interface
- **Checkbox Theme Selection**: Replaced dropdown with intuitive checkboxes for multi-theme selection
- **Visual Theme Indicators**: Better visual feedback for selected themes
- **Multi-Theme Description**: Helpful tooltips explaining multi-theme benefits
- **Enhanced Results Display**: Shows all selected themes in generation information

### 🛠️ Technical Improvements

#### 📊 API Enhancements
- **Flexible Input Handling**: Supports both single theme and multiple themes in same API
- **Improved Validation**: Enhanced theme validation with specific error messages
- **Word Deduplication**: Automatic removal of duplicate words when combining themes
- **Extended Word Lists**: Added hundreds of new words across all theme categories

#### 🌐 Web Interface Updates
- **Dynamic Theme Loading**: Themes loaded dynamically from API for consistency
- **Improved JavaScript**: Better error handling and form validation
- **Enhanced UX**: More intuitive multi-selection interface
- **Responsive Design**: Checkbox layout adapts to different screen sizes

### 📚 Documentation Updates
- **Complete API Documentation**: Updated all examples to show multi-theme usage
- **New Use Cases**: Added examples for science, chemistry, and health-themed usernames
- **Backward Compatibility Guide**: Clear migration path from single to multi-theme
- **Enhanced README**: Updated project documentation with new theme descriptions

---

## � Version 2.0.1 - Major Architecture Restructuring
*Released: September 10, 2025*

### 🌟 Major Changes

#### 🏗️ Complete Project Restructuring
- **New Architecture**: Reorganized from individual tool folders to clean `public/` and `api/` separation
- **Clean URLs**: Beautiful, organized URL structure with `/public/` for interfaces and `/api/` for endpoints
- **Enhanced Navigation**: Streamlined access to tools, APIs, and documentation
- **Better Organization**: Logical separation of concerns for easier maintenance and deployment

#### �📚 Dynamic API Documentation System
- **Server-Agnostic URLs**: Documentation automatically adapts to any server domain using PHP `$_SERVER` variables
- **Centralized Documentation**: All API specs moved to `public/api-specs/` directory
- **Interactive Examples**: Working code samples that use the current server's URL
- **No More Hardcoded URLs**: Eliminated hardcoded domain references throughout the project

#### 🔧 API Accuracy Corrections
- **Parameter Verification**: All API documentation parameters verified against actual implementations
- **Corrected Examples**: Fixed numerous parameter name mismatches and incorrect examples
- **Consistent Responses**: Standardized response formats across all tools
- **Updated Endpoints**: All API endpoints updated to new `/api/tool-name/` format

### 🐛 Bug Fixes

#### 🌐 Web Interface Corrections
- **Fixed API Calls**: Updated all JavaScript fetch() calls to use correct API endpoints
- **Navigation Fixes**: Corrected all internal links to work with new structure
- **Username Generator**: Fixed API endpoint calls and response handling
- **PromptPay QR Generator**: Resolved API communication issues
- **Random Generator**: Fixed randomization API calls
- **Fortune Teller**: Resolved prediction file path issues

#### 🔗 File Path Corrections
- **Fortune Teller API**: Fixed predictions directory path from `/../predictions/` to `/predictions/`
- **Asset Links**: Updated all asset references to work with new structure
- **Documentation Links**: Corrected all cross-references between tools and docs

### 📁 New File Structure
```
myapis/
├── public/                   # User-facing interfaces
│   ├── index.php            # Main landing page
│   ├── *.php                # Individual tool interfaces
│   └── api-specs/           # API documentation
├── api/                     # Backend API implementations
│   └── */index.php          # Individual API endpoints
├── README.md                # Updated project documentation
└── RELEASE.md               # This file
```

### 🔄 Breaking Changes
- **URL Structure**: All URLs changed from `/tool-name/` to `/public/tool-name.php`
- **API Endpoints**: All APIs moved from `/tool-name/api/` to `/api/tool-name/`
- **Documentation**: API docs moved from `/tool-name/spec.php` to `/public/api-specs/tool-name.php`
- **Navigation**: All internal links updated to reflect new structure

### ✅ Verification & Testing
- **API Testing**: All endpoints verified with curl and browser testing
- **Web Interface Testing**: All tools tested for functionality and user experience
- **Documentation Accuracy**: All examples and parameters verified against actual code
- **Cross-Browser Testing**: Verified compatibility across modern browsers
- **Mobile Testing**: Ensured responsive design works on all devices

### 🛠️ Technical Improvements
- **Cleaner Architecture**: Better separation of concerns between frontend and backend
- **Easier Deployment**: Simplified deployment with clear public/api structure
- **Better Maintainability**: More organized codebase for easier updates
- **Enhanced Security**: Improved input validation and error handling
- **Performance**: Optimized file structure for better loading times

---

## 💧 Version 1.3.1 - API Documentation Enhancement
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
| Health Calculator | POST | `/api/health-calculator/` | Calculate BMI, BMR, Daily Intake, and Water Intake |
| Password Generator | POST | `/api/password-generator/` | Generate secure passwords |
| Username Generator | POST | `/api/username-generator/` | Create unique usernames |
| PromptPay QR | POST | `/api/promptpay-qr-generator/` | Generate PromptPay QR codes |
| Fortune Teller | GET | `/api/fortune-teller/` | Get random fortune prediction |
| Random Generator | POST | `/api/randomizer/` | Generate random numbers/objects |

### 📁 File Structure
```
myapis/
├── 📄 public/index.php (Main landing page)
├── � public/*.php (Tool web interfaces)  
├── 📁 public/api-specs/ (API documentation)
├── 📁 api/*/ (API implementations)
├── � README.md (Project documentation)
└── � RELEASE.md (This file)
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
# Access at http://localhost:8000/public/
```

#### Production
- Deploy to web server document root
- Configure virtual host/server block to point to the project root (not public folder)
- Access web interfaces via `/public/` path
- API endpoints available at `/api/` path
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
