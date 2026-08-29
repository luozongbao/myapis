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
| 👤 **Username Generator** | Create unique usernames with multi-theme support (Fantasy, Professional, Science, Tech, Chemistry, Things, Body & Health) | [Try Tool](public/username-generator.php) | [API](api/username-generator/) | [Full Specs](public/api-specs/username-generator.php) |
| 💳 **PromptPay QR Generator** | Generate EMV-compliant PromptPay QR codes | [Try Tool](public/promptpay-qr-generator.php) | [API](api/promptpay-qr-generator/) | [Full Specs](public/api-specs/promptpay-qr-generator.php) |
| 📱 **QR Code Generator** | Universal QR code generator (text, URL, vCard, event, Wi-Fi, phone) powered by [goQR.me](https://goqr.me/api/doc/create-qr-code/) | [Try Tool](public/qr-code-generator.php) | [API](api/qr-code-generator/) | [Full Specs](public/api-specs/qr-code-generator.php) |
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
- **Docker** & **Docker Compose** (recommended, easiest setup)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/luozongbao/myapis.git
   cd myapis
   ```

2. **Set up web server**

   **Option A: Docker (Recommended) 🐳**
   ```bash
   # Copy the example env file and adjust as needed
   cp example.env .env

   # Build and start the stack
   docker compose up -d --build

   # Open http://localhost:8080
   ```
   The stack ships with PHP-FPM 8.2 and Nginx 1.27. All required
   PHP extensions (gd, intl, mbstring, opcache, bcmath) are installed
   automatically. The application becomes available on the port
   defined by `WEB_PORT` in `.env` (default `8080`).

   **Option B: Using PHP built-in server (Development)**
   ```bash
   php -S localhost:8000
   ```
   Then open `http://localhost:8000/public/`.

   **Option C: Apache/Nginx (Production)**
   - Copy files to your web server's document root
   - Ensure PHP is configured and enabled
   - Set appropriate file permissions
   - See `docs/nginx-conf/` for ready-to-use Nginx vhost samples
   - The provided `.htaccess` already rewrites requests into `/public/`


  **Apache .htaccess settings**
  ``` htaccess
  # Root .htaccess - Redirect all requests to public directory
  # Place this file in the project root (/var/www/api/)

  RewriteEngine On

  # Redirect everything to public directory except already in public
  RewriteCond %{REQUEST_URI} !^/public/
  RewriteCond %{REQUEST_URI} !^/api/
  RewriteRule ^(.*)$ public/$1 [L]

  # Security - Block access to sensitive files in root
  <Files ~ "^\.(htaccess|gitignore|env)">
      Order allow,deny
      Deny from all
  </Files>

  <Files ~ "(README\.md|composer\.(json|lock))$">
      Order allow,deny
      Deny from all
  </Files>

  # Block direct access to old structure directories
  <Directory "health-calculator">
      Order deny,allow
      Deny from all
  </Directory>

  <Directory "password-generator">
      Order deny,allow
      Deny from all
  </Directory>

  <Directory "username-generator">
      Order deny,allow
      Deny from all
  </Directory>

  <Directory "promptpay-qr-generator">
      Order deny,allow
      Deny from all
  </Directory>

  <Directory "fortune-teller">
      Order deny,allow
      Deny from all
  </Directory>

  <Directory "randomizer">
      Order deny,allow
      Deny from all
  </Directory>


  ```


  **nginx configuration settings**
  ``` conf
  server {
      listen 80;
      server_name domain.com;

      root /var/www/myapis/public;
      index index.php;

      # Clean URLs for tools (remove .php extension)
      location / {
          try_files $uri $uri/ @rewrite;
      }

      location @rewrite {
          rewrite ^/([^/]+)/?$ /$1.php last;
          rewrite ^/([^/]+)-specs/?$ /$1-specs.php last;
      }

      # API routing with clean URLs
      location /api/ {
          root /var/www/myapis/;

          # Route /api/tool-name/ to /api/tool-name/index.php
          location ~ ^/api/([^/]+)/?$ {
              try_files $uri $uri/ /api/$1/index.php?$query_string;
          }

          # Handle direct PHP file access in API
          location ~ ^/api/(.+)\.php$ {
              root /var/www/myapis/;
              try_files $uri =404;
              fastcgi_split_path_info ^(.+\.php)(/.+)$;
              fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
              fastcgi_index index.php;
              fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
              include fastcgi_params;

              # CORS headers for API endpoints
              add_header Access-Control-Allow-Origin *;
              add_header Access-Control-Allow-Methods "GET, POST, OPTIONS";
              add_header Access-Control-Allow-Headers "Content-Type, Authorization";
          }
      }

      # PHP processing for public files
      location ~ \.php$ {
          try_files $uri =404;
          fastcgi_split_path_info ^(.+\.php)(/.+)$;
          fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
          fastcgi_index index.php;
          fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
          include fastcgi_params;
      }

      # Static assets caching
      location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg)$ {
          expires 30d;
          add_header Cache-Control "public, no-transform";
      }

      # Block access to sensitive files
      location ~ /\. {
          deny all;
      }

      location ~ /(README\.md|composer\.(json|lock)|\.git)$ {
          deny all;
      }
  }

  ```

3. **Access the application**
   - Docker: `http://localhost:${WEB_PORT}/` (e.g. `http://localhost:8080/`)
   - Built-in / Apache / Nginx: `http://localhost:8000/public/`
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
curl -X POST "http://localhost:8080/api/health-calculator/" \
  -H "Content-Type: application/json" \
  -d '{"weight": 70, "height": 175, "unit": "metric", "type": "bmi"}'
```

### Password Generator
```bash
curl -X POST "http://localhost:8080/api/password-generator/" \
  -H "Content-Type: application/json" \
  -d '{"length": 16, "uppercase": true, "lowercase": true, "numbers": true, "symbols": true}'
```

### Fortune Teller
```bash
curl "http://localhost:8080/api/fortune-teller/?lang=en"
```

> 💡 If you are using the built-in PHP server (`php -S`), replace
> `http://localhost:8080` with `http://localhost:8000` and add the
> `/public/` prefix as described in the Quick Start section.

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
├── docker/                  # Docker configuration files
│   ├── nginx/default.conf   # Nginx vhost (public + api routing)
│   └── php/
│       ├── php.ini          # PHP runtime overrides
│       └── opcache.ini      # Opcache tuning
├── docker-compose.yml       # PHP-FPM + Nginx stack definition
├── Dockerfile               # PHP-FPM image with required extensions
├── example.env              # Sample environment variables
├── .dockerignore            # Files excluded from the image
├── .htaccess                # Apache rewrite rules (root → public)
├── README.md                # This file
└── RELEASE.md               # Release notes
```

## 🐳 Docker Deployment

The project ships with a complete, production-ready Docker stack
(PHP-FPM 8.2 + Nginx 1.27 on Alpine).

### Quick commands

```bash
# 1. Copy and (optionally) edit environment variables
cp example.env .env

# 2. Build images and start the stack in the background
docker compose up -d --build

# 3. Follow the logs
docker compose logs -f

# 4. Stop the stack
docker compose down
```

After `docker compose up -d` finishes, the app is available at
`http://localhost:${WEB_PORT:-8080}/`.

### Environment variables

All variables are read from `.env` (see [example.env](example.env)):

| Variable | Default | Description |
|----------|---------|-------------|
| `PROJECT_NAME` | `myapis` | Prefix used for container names and networks |
| `TZ` | `UTC` | Container / PHP timezone |
| `WEB_PORT` | `8080` | Host port mapped to Nginx (port 80) |
| `PHP_MEMORY_LIMIT` | `256M` | PHP `memory_limit` |
| `PHP_UPLOAD_MAX_FILESIZE` | `10M` | PHP `upload_max_filesize` |
| `PHP_POST_MAX_SIZE` | `10M` | PHP `post_max_size` |
| `PHP_DATE_TIMEZONE` | `UTC` | PHP `date.timezone` |
| `NGINX_CLIENT_MAX_BODY_SIZE` | `10M` | Nginx `client_max_body_size` |
| `APP_ENV` | `development` | `development` shows errors, `production` hides them |

### Common tasks

```bash
# Rebuild only the PHP image after changing the Dockerfile
docker compose build php

# Open a shell inside the PHP container
docker compose exec php sh

# Tail Nginx access / error logs
docker compose logs -f nginx
```

### URL routing in Docker

The bundled Nginx vhost serves everything from `public/` and proxies
API requests to PHP-FPM:

- `http://localhost:8080/` → `public/index.php`
- `http://localhost:8080/health-calculator.php` → tool page
- `http://localhost:8080/api/health-calculator/` → `api/health-calculator/index.php`
- `http://localhost:8080/api/fortune-teller/?lang=en` → fortune API

> 🛡️ Requests for hidden files, `.env`, `README.md`, etc. are
> explicitly denied by the Nginx configuration.

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
- **🐳 Docker Ready** with PHP-FPM 8.2 + Nginx 1.27
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
