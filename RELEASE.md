# 📋 MyAPIs Release Notes

## Current Release: Version 2.4.0

**Release Date**: August 29, 2026
**Status**: Stable Release

---

## 📈 Version 2.4.0 - Analytics & Visitor Tracking
*Released: August 29, 2026*

### 🌟 New Feature

#### 📊 Pluggable Visitor Tracking
- **`docker/php/analytics.php`**: new partial that emits the correct
  `<script>` snippet based on `ANALYTICS_PROVIDER`
  - `umami` — self-hosted, cookie-less, works in China
  - `ga4` (alias `google`) — Google Analytics 4 via gtag.js
  - `none` (default) — nothing is emitted
- **`auto_prepend_file`** directive added to
  [`docker/php/php.ini.tpl`](docker/php/php.ini.tpl) so the snippet is
  injected into **every HTML response** without touching the tool
  pages individually
- **JSON safety**: the snippet is automatically skipped when:
  - The request path starts with `/api/`
  - The `Accept` header contains `application/json`
  - The script is invoked from the CLI
- **Safe HTML**: every dynamic value (`UMAMI_SCRIPT_URL`,
  `UMAMI_WEBSITE_ID`, `GA4_MEASUREMENT_ID`) is run through
  `htmlspecialchars()` before being echoed

#### 🐳 Optional Self-Hosted Umami Service
- [`docker-compose.yml`](docker-compose.yml) now ships a **commented-out**
  `umami-db` + `umami` service block at the bottom (plus the matching
  `volumes:` declaration)
- Enable it with one edit to get Umami + PostgreSQL running next to
  MyAPIs, accessible on `http://localhost:${UMAMI_PORT:-3000}`
- Default login: `admin` / `umami`

#### ⚙️ Configuration Surface (`example.env`)
- **`ANALYTICS_PROVIDER`** — `none` (default), `umami`, `ga4`, `google`
- **`UMAMI_SCRIPT_URL`** — full URL to the Umami tracker
  (`http://umami:3000/script.js` works out of the box when the
  compose Umami service is enabled)
- **`UMAMI_WEBSITE_ID`** — UUID from the Umami dashboard
- **`GA4_MEASUREMENT_ID`** — `G-XXXXXXXXXX` from the GA4 admin
- Existing Umami infra variables (`UMAMI_PORT`, `UMAMI_DB_*`,
  `UMAMI_APP_SECRET`) preserved and now documented in README

#### 🐳 PHP Container Wiring
- `docker-compose.yml` forwards `ANALYTICS_PROVIDER`,
  `UMAMI_SCRIPT_URL`, `UMAMI_WEBSITE_ID`, `GA4_MEASUREMENT_ID` to
  the `php` service so the partial can read them via `getenv()`

### 📚 Documentation
- New **📈 Analytics / Visitor Tracking** section in
  [`README.md`](README.md) with:
  - Provider comparison table
  - Step-by-step Umami + GA4 setup instructions
  - **Option C** for Umami Cloud / externally-hosted Umami, including
    C.1 (managed SaaS) and C.2 (separate server) plus a comparison
    table with Option A
  - Tracking-scope clarification (HTML pages vs. `/api/*`)
  - Verification `curl` commands
- README env-vars table extended with every analytics variable
- README **Latest Updates** bumped to v2.4.0

#### 🌐 Shared Hosting (Hostinger / cPanel) Deployment Guide
- Brand-new **🌐 Shared Hosting Deployment (Hostinger / cPanel)**
  section in the README covering every step from upload to verify:
  - Prerequisites table (PHP version, extensions, `mod_rewrite`)
  - Two project layouts (upload only `public/` + `api/`, or the whole
    repo — including a recommended Hostinger Single/Premium layout)
  - hPanel upload walkthrough (File Manager / FTP / SSH)
  - PHP version + extension enablement on hPanel / MultiPHP Manager
  - **Analytics without `.env`** — drop a `public/config.php` that
    calls `putenv()` (shared-hosting fallback in
    [`docker/php/analytics.php`](docker/php/analytics.php))
  - File-permissions cheat sheet (644 / 755)
  - Two clean domain-mapping strategies (root repo upload vs.
    `public/`-only upload with separate `api/` folder)
  - Verification curl commands
  - **Troubleshooting table** (403, 500, missing `gd`, 404 on `/api/`,
    blank QR images, etc.)
  - Hosting-specific notes for Hostinger Single / Premium / Business
    / Cloud, SiteGround, Namecheap, and Cloudflare
  - "What you do **not** get on shared hosting" honest checklist

#### 🆕 Shared-Hosting Analytics Fallback
- [`docker/php/analytics.php`](docker/php/analytics.php) now tries
  to `require_once` `public/config.php`, `../config.php`, or
  `config.php` (next to the partial) **before** reading env vars.
  This means shared-hosting deployments that can't use `.env` or
  `auto_prepend_file` can still configure analytics by including
  the partial manually.
- [`public/config.php.example`](public/config.php.example) ships as
  a copy-and-edit template for Umami / GA4 / disabled configurations.

### 📁 New / Updated Files
- `docker/php/analytics.php` — new tracking partial + shared-hosting
  `config.php` fallback
- `docker/php/php.ini.tpl` — adds `auto_prepend_file`
- `docker-compose.yml` — adds analytics env forwarding + optional
  Umami / PostgreSQL service block
- `example.env` — documents `ANALYTICS_PROVIDER`, `UMAMI_SCRIPT_URL`,
  `UMAMI_WEBSITE_ID`, `GA4_MEASUREMENT_ID`
- `public/config.php.example` — **new** shared-hosting analytics
  template
- `README.md` — new analytics section (with **Option C** Umami Cloud
  / externally-hosted), env table, full **🌐 Shared Hosting
  Deployment** section, project-structure update, version banner
- `RELEASE.md` — this entry

### ✅ Verification
- `php -l docker/php/analytics.php` passes
- `php -l public/config.php.example` passes
- `docker compose config` validates with the optional Umami block
  both commented and uncommented
- Manual checks once running:
  - `curl -s http://localhost:8080/ | grep -E 'umami|gtag'` → returns
    the matching `<script>` tag when the provider is enabled
  - `curl -s http://localhost:8080/api/health-calculator/ | grep -E 'umami|gtag'`
    → returns nothing (API paths are excluded)
  - On shared hosting: drop a `public/config.php` that sets the
    provider via `putenv()`, hit any tool page, view-source, and
    the matching `<script>` tag is present
- Switching `ANALYTICS_PROVIDER=none` and restarting removes every
  snippet without code changes

---

## 📱 Version 2.3.1 - QR Code Generator: SVG, Color Pickers, Dynamic vCard
*Released: August 29, 2026*

### 🌟 Enhancements

#### 📐 SVG File-Type Support
- **New `file_type` parameter**: Forwarded to goQR.me as the `format` field, validated
  against `['png', 'svg', 'gif', 'jpeg', 'jpg', 'eps']`
- **MIME-aware responses**: SVG returns `image/svg+xml`, PNG `image/png`, etc.
- **Frontend dropdown**: New "PNG / SVG" selector inside the Appearance panel
- **Backward compatibility**: Legacy `gformat` parameter still accepted

#### 🎨 Native Colour Pickers
- **Replaced plain hex inputs** with paired `<input type="color">` + hex text field
  for both foreground (`color`) and background (`bgcolor`)
- **Live swatch preview** next to each label
- **Bi-directional sync**: typing a hex value updates the picker and swatch instantly,
  and picking a colour updates the hex text
- **No API changes**: backend already accepted decimal RGB and 3/6-char hex values

#### 📇 Dynamic vCard Fields
- **Add/remove unlimited emails**: `emails[i][value]` + `emails[i][type]`
  (`WORK` / `HOME` / `INTERNET`)
- **Add/remove unlimited phones**: `phones[i][value]` + `phones[i][type]`
  (`CELL,VOICE` / `WORK,VOICE` / `HOME,VOICE` / `FAX` / `VOICE`)
- **Add/remove unlimited URLs**: `urls[i][value]` + optional `urls[i][label]`
- **Add/remove structured addresses**: `addresses[i][type]` + `street` + `po_box`
  + `city` + `region` + `postcode` + `country`
- **`reindex()` JavaScript**: keeps bracket numbers dense after row removal
- **`collectDynamicItems()` backend**: reads both `$_GET` and `$_POST` and merges
  with legacy single fields (`work_email`, `home_phone`, etc.) for full
  backward compatibility

#### 📚 API Documentation Updates
- **New parameter table**: documents the array field patterns and accepted sub-keys
- **Two new curl examples**: SVG with custom colours (Example 8) and dynamic vCard
  with multiple entries (Example 9)
- **Response example** now includes the `file_type` field

### 🧪 Verification
- All three new features exercised via Docker stack (`docker compose up -d --build`)
- Direct PNG output: 300×300 with `cc0066` foreground → `image/png`
- Direct SVG output: 400×400 with `0066cc` foreground → `image/svg+xml`
- Wi-Fi + SVG + custom colour: payload `WIFI:T:WPA;S:Cafe WiFi;P:beans2024;H:false;`
- Dynamic vCard payload contains 3 emails, 3 phones, 2 URLs, 2 addresses

---

## 📱 Version 2.3.0 - QR Code Generator (Initial Release)
*Released: August 29, 2026*

### 🌟 Major New Feature

#### 📱 Universal QR Code Generator
- **Six content types** powered by the [goQR.me](https://goqr.me/api/doc/create-qr-code/) API:
  - **Plain Text / Long Text** — any payload, no length restriction
  - **Website URL** — auto-prefixes `https://` when missing
  - **Business vCard (vCard 3.0)** — personal, organisational, contact, and address
    fields including multiple emails, phones, fax, website and free-form note
  - **Event (iCalendar)** — summary, start, end, location, description
  - **Wi-Fi** — SSID, password, encryption (WPA / WEP / nopass), hidden flag
  - **Phone Number** — `tel:` URI
- **goQR.me parameter passthrough**: `size`, `ecc`, `qzone`, `margin`, `color`,
  `bgcolor`, `charset-source`, `charset-target`, `format`
- **Two response modes**:
  - `format=image` — raw PNG bytes (default goQR.me format)
  - `format=json` — `{success, type, payload, qr_url, goqr_url, params, file_type}`
- **CORS enabled** for cross-origin consumption
- **Validation**: every input is whitelisted; unknown types / sizes / ECC
  levels return HTTP 400 with a clear error message

#### 🖥️ Web Interface
- **Type selector grid** — six cards (Text, URL, vCard, Event, Wi-Fi, Phone)
  with icons and gradient active state
- **Sticky form**: all simple fields repopulate after submission so users
  can iteratively tweak
- **Payload preview**: encoded payload is rendered below the QR image so
  users can verify exactly what will be scanned
- **Breadcrumb + header badges** matching the existing tool style
  (`#667eea → #764ba2` gradient)
- **Download button**: one-click download of the generated image with the
  correct file extension

#### 📚 API Specifications Page
- **`public/api-specs/qr-code-generator.php`** — comprehensive table of
  every parameter, accepted values, and defaults
- **Five external links** to the goQR.me `create-qr-code` documentation
- **Seven curl examples** covering each content type and the direct image
  download pattern
- **JSON response example** with all fields shown

#### 🧭 Navigation & Discoverability
- **Landing page card** (`public/index.php`) — adds a "📱 QR Code Generator"
  card with eight feature bullets and Try/API/Docs buttons
- **README.md tools table** — adds the QR row linking to web UI, API, and specs
- **Total tools: 7** (up from 6)

### 📁 New / Updated Files
- `api/qr-code-generator/index.php` — REST endpoint
- `public/qr-code-generator.php` — Web UI
- `public/api-specs/qr-code-generator.php` — API documentation
- `public/index.php` — landing page card added
- `README.md` — tools table, project structure, statistics, usage examples
- `RELEASE.md` — this file

### ✅ Verification
- PHP lint via `docker exec myapis-php php -l <file>` passes for all three files
- HTTP 200 on `http://localhost:8080/qr-code-generator.php`
- HTTP 200 on `http://localhost:8080/api-specs/qr-code-generator.php`
- HTTP 200 on `http://localhost:8080/api/qr-code-generator/?format=json` (POST)
- Each content type produces a standards-compliant payload:
  - Text → raw string
  - URL → `https://...`
  - vCard → `BEGIN:VCARD ... END:VCARD`
  - Event → `BEGIN:VCALENDAR ... END:VCALENDAR`
  - Wi-Fi → `WIFI:T:WPA;S:<ssid>;P:<password>;H:<true|false>;`
  - Phone → `tel:<number>`

---

## 🐳 Version 2.2.0 - Docker Deployment & Operational Tooling
*Released: August 29, 2026*

### 🌟 Major New Features

#### 🐳 One-Command Docker Deployment
- **`docker-compose.yml`**: Production-ready stack orchestrating PHP-FPM 8.2 and Nginx 1.27 (both Alpine-based for minimal footprint)
- **`Dockerfile`**: Builds a custom PHP-FPM image with all required extensions pre-installed (`gd`, `intl`, `mbstring`, `opcache`, `bcmath`)
- **`example.env`**: Centralised configuration for ports, PHP limits, timezone, and environment
- **`.dockerignore`**: Keeps build context lean and reproducible

#### ⚙️ Configurable via Environment Variables
- `WEB_PORT` – host port mapped to Nginx (default `8080`)
- `PROJECT_NAME` – prefix for container names / networks
- `TZ` / `PHP_DATE_TIMEZONE` – consistent timezone inside PHP and the container
- `PHP_MEMORY_LIMIT`, `PHP_UPLOAD_MAX_FILESIZE`, `PHP_POST_MAX_SIZE` – PHP runtime limits
- `NGINX_CLIENT_MAX_BODY_SIZE` – request size limit
- `APP_ENV` – `development` shows errors, `production` hides them

#### 🛡️ Hardened Nginx vhost
- Dedicated location block routes `/api/<tool>/` to `api/<tool>/index.php` through PHP-FPM
- Sensitive files (`.env`, `README.md`, `RELEASE.md`, `composer.*`, hidden files) are denied
- Static asset caching for images / CSS / JS
- Standard security headers (`X-Frame-Options`, `X-XSS-Protection`, `X-Content-Type-Options`, `Referrer-Policy`)
- `server_tokens off` to hide Nginx version

#### 🩺 Production Touches
- PHP-FPM healthcheck via the `php-fpm-healthcheck` helper script
- `depends_on: { php: { condition: service_healthy } }` ensures Nginx waits for PHP
- Bind-mounted source code (read-only for Nginx) for easy development iteration

### 📁 New / Updated Files
- `docker-compose.yml` – stack definition
- `Dockerfile` – PHP-FPM image recipe
- `docker/nginx/default.conf` – Nginx vhost (public + api routing)
- `docker/php/php.ini` – PHP runtime overrides
- `docker/php/opcache.ini` – Opcache tuning
- `example.env` – sample environment variables
- `.dockerignore` – Docker build context exclusions
- `README.md` – new "Docker Deployment" section and updated structure

### 🚀 Quick start
```bash
cp example.env .env
docker compose up -d --build
open http://localhost:${WEB_PORT:-8080}
```

### ✅ Verification
- `docker compose config` validates without errors
- Nginx vhost reviewed for correct `alias` / `fastcgi_pass` / `try_files` semantics
- PHP-FPM healthcheck wired so Nginx only starts once PHP is ready
- All existing tool URLs continue to work without code changes

---

## 🔧 Version 2.1.2 - Username Generator Interface Cleanup & Generation Improvements
*Released: September 11, 2025*

### 🧹 Interface Improvements

#### 📝 Removed Unused Use Case Field
- **Removed `use_case` parameter**: Eliminated the unused "Use Case" dropdown from web interface
- **API Cleanup**: Removed `use_case` from API parameters and response data  
- **Simplified Interface**: Cleaner, more focused interface without confusing non-functional options
- **Documentation Updated**: Updated README.md to reflect parameter changes
- **Backward Compatibility**: Existing API calls will continue to work (parameter simply ignored)

### ⚡ Generation Algorithm Improvements

#### 🎯 Smart Generation for Restrictive Constraints
- **Intelligent Retry Logic**: Dynamically adjusts maximum attempts based on length constraint difficulty
- **Guaranteed Count**: Now generates the full requested count of usernames when constraints allow
- **Adaptive Scaling**: 
  - Very restrictive constraints (≤5 char range): 50× more attempts
  - Moderately restrictive (≤10 char range): 25× more attempts  
  - Normal constraints: 15× more attempts
- **Warning System**: Provides helpful warnings when constraints are too restrictive to generate full count
- **Better User Experience**: Users get the exact number of usernames they requested (when possible)

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
  - **Nature**: Landscape fruits and animals (Mountain, Grape, Fox, Wolf, Banana)
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
