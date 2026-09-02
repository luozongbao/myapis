# 🚀 MyAPIs — Developer's Tools Collection

A comprehensive collection of developer tools and APIs designed to streamline your development workflow. Each tool provides both a beautiful web interface and a robust REST API for easy integration.

### 🎯 Key Features

- **🌐 Modern Web Interfaces**: Beautiful, responsive designs that work on all devices
- **🔌 REST APIs**: Well-documented APIs with JSON responses
- **📚 Interactive API Documentation**: Server-agnostic documentation with dynamic URLs
- **🔒 Security First**: Cryptographically secure random generation
- **🌍 Multi-language Support**: Thai, Chinese, and English support where applicable
- **📱 Mobile Responsive**: Optimized for desktop, tablet, and mobile
- **⚡ Fast & Lightweight**: Pure PHP implementation with minimal dependencies
- **🔄 CORS Enabled**: Cross-origin request support for web applications
- **🏗️ Clean Architecture**: Organized public/api structure for easy deployment
- **🌐 Dynamic URLs**: Server-agnostic URLs that adapt to any hosting environment

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

   **Option C: Apache / Shared hosting (e.g. Hostinger, cPanel)**
   - Upload the project to your web root (see
     [🌐 Shared Hosting Deployment](#-shared-hosting-deployment-hostinger--cpanel)
     for a full Hostinger / cPanel walkthrough)
   - Ensure PHP ≥ 7.4 is enabled in the control panel
   - The bundled `.htaccess` already rewrites requests into `/public/`
   - No Docker, no root access, no `.env` required — see the shared
     hosting section for the `config.php` step

   **Option D: Nginx (VPS / Production)**
   - Copy files to your web server's document root
   - Ensure PHP-FPM is configured and enabled
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

## 🌐 Shared Hosting Deployment (Hostinger / cPanel)

Most shared-hosting providers (Hostinger, SiteGround, Bluehost,
Namecheap, etc.) give you a cPanel (or hPanel on Hostinger), **no
Docker**, **no root shell**, and **no `.env` file routing** — so the
Docker stack above won't work, but the rest of MyAPIs will run
fine as long as PHP ≥ 7.4 is available and `mod_rewrite` is on.

The short version: upload the project, point your domain at
`/public/`, and (optionally) drop a `config.php` to enable
analytics. The long version follows.

### Prerequisites

| Item | Minimum | Recommended |
|---|---|---|
| PHP | 7.4 | 8.1 or 8.2 |
| PHP extensions | `json`, `mbstring` | + `gd` (PromptPay QR), `intl`, `bcmath` |
| Apache module | `mod_rewrite` | (almost always on by default) |
| Disk space | 20 MB | 50 MB |
| `.env` support | ❌ not available | Use `public/config.php` instead |

> ✅ **Hostinger** *Business* and *Cloud* plans ship PHP 8.1/8.2
> with all of the above by default. The *Single* / *Premium* shared
> plans ship PHP 8.0+; the PromptPay QR generator may need `gd`
> enabled manually from **hPanel → Advanced → PHP Configuration**.

### Step 1 — Prepare the project locally

The repo contains files that shared hosting must never see
(`docker/`, `docker-compose.yml`, `Dockerfile`, `.dockerignore`,
`example.env`, `docs/`, `README.md`, `RELEASE.md`). You have two
options:

**Option 1 — upload only what you need (recommended for Hostinger Single/Premium)**

```text
public_html/
└── myapis/                 ← your project root on the server
    ├── .htaccess           ← root rewrite file (already in repo)
    ├── api/                ← REST endpoints
    ├── public/             ← web UIs
    │   ├── .htaccess       ← already in repo
    │   ├── index.php
    │   ├── health-calculator.php
    │   └── … (all tool pages)
    └── config.php          ← optional analytics config (see Step 4)
```

If your provider puts you directly in `public_html/`, copy `api/`
and `public/` **plus both `.htaccess` files** into the web root —
the root `.htaccess` will forward everything into `public/`.

**Option 2 — upload everything** (easier, fine for Business / Cloud plans)

Just upload the whole repo. The bundled `.htaccess` already blocks
direct access to `README.md`, `RELEASE.md`, `.env`, `docker/`, etc.
You can safely skip the `docs/` and `.dockerignore` files.

### Step 2 — Upload to Hostinger (hPanel)

1. Log in to <https://hpanel.hostinger.com/>.
2. Go to **Files → File Manager** (or use FTP / SSH if your plan
   supports it). The recommended host is `public_html/` (or a
   sub-folder if you're hosting multiple sites).
3. Upload the project as a `.zip`, then **Extract here**.
4. Make sure the files end up at the path you want — for
   `https://yourdomain.com/` to work, the files must live in
   `public_html/` directly. For `https://yourdomain.com/myapis/`,
   put them in `public_html/myapis/`.

### Step 3 — Pick the PHP version

1. hPanel → **Advanced → PHP Configuration** (or **MultiPHP
   Manager** on cPanel).
2. Select the directory where you uploaded MyAPIs and switch to
   **PHP 8.1** or **8.2**.
3. Enable the extensions you need: `gd`, `intl`, `mbstring`,
   `bcmath`. On Hostinger they're toggles; on cPanel use
   `Select PHP Version → Extensions`.

### Step 4 — (Optional) Configure analytics without `.env`

Shared hosting can't read `.env` and doesn't run
`auto_prepend_file`. Every page in `public/` and `public/api-specs/`
already `require`s `public/analytics.php` for you, so all you need
to do is **drop a `public/config.php`** that calls `putenv()`:

````php
// filepath: public/config.php
// public/analytics.php reads these via getenv() fallbacks.
// Copy from public/config.php.example and edit your values.
putenv('ANALYTICS_PROVIDER=umami');
putenv('UMAMI_SCRIPT_URL=https://cloud.umami.is/script.js');
putenv('UMAMI_WEBSITE_ID=YOUR-UUID-HERE');
// For GA4 instead, use:
//   putenv('ANALYTICS_PROVIDER=ga4');
//   putenv('GA4_MEASUREMENT_ID=G-XXXXXXXXXX');
````

The fallback order in `public/analytics.php` is:
1. Environment variables (works on Docker / VPS)
2. `public/config.php` (works on shared hosting)
3. Else: nothing emitted

> 🔒 **Don't commit `public/config.php`** — it contains private IDs.
> A [`public/config.php.example`](public/config.php.example) ships as
> a template; copy it to `public/config.php` and edit your values.

> 🔌 **Why every page `require`s `analytics.php` directly:**
> on shared hosting there is no `auto_prepend_file`, so PHP won't
> run a file just because it exists in `public/`. Each page
> includes it explicitly with `require __DIR__ . '/analytics.php';`
> right before `</head>`. The `require` is wrapped in
> `file_exists()` so it is safe to delete the analytics files at
> any time.

### Step 5 — Set file permissions

The default Hostinger / cPanel permissions usually work, but if
you see *403 Forbidden* errors:

- **Files**: `644` (`-rw-r--r--`)
- **Directories**: `755` (`drwxr-xr-x`)
- **Owner**: your FTP user (e.g. `u123456789`)

From File Manager, right-click a folder → **Permissions** → set to
`755` and tick **Apply to subdirectories**.

### Step 6 — Point your domain at `/public/`

You have two clean choices on shared hosting:

#### 6a. Host the whole repo at the domain root (easiest)

Upload the whole project to `public_html/`. The root
`.htaccess` already rewrites every request into `public/`:

```apache
# (this is the shipped root .htaccess)
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/public/
RewriteCond %{REQUEST_URI} !^/api/
RewriteRule ^(.*)$ public/$1 [L]
```

Open `https://yourdomain.com/` — you should land on the MyAPIs
landing page.

#### 6b. Host only the `public/` folder at the domain root

Upload `public/` directly into `public_html/` and upload `api/`
**one level above** (i.e. into the account home, *not* into
`public_html/`). Then add a small `public_html/.htaccess` to
route `/api/`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/api/
RewriteCond %{REQUEST_URI} !^/assets/
RewriteRule ^(.*)$ public/$1 [L]
```

This is the most efficient layout — web roots and `/api/` requests
both go to the right place without any double-rewrite.

### Step 7 — Verify

```bash
# From your laptop
curl -sI https://yourdomain.com/            # → HTTP/2 200
curl -s  https://yourdomain.com/ | grep -i 'umami\|gtag'
                                            # → only if analytics is enabled
curl -s  https://yourdomain.com/api/health-calculator/ | head -c 80
                                            # → {"success":true,...}
```

If the landing page loads but the API returns *404*, double-check
Step 6b — `api/` must be reachable at `/api/<tool>/`.

### Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| *403 Forbidden* on `/` | Directory permissions / missing `.htaccess` | Set dirs to `755`, files to `644`; confirm both `.htaccess` files uploaded |
| *500 Internal Server Error* | PHP version too old, or `mod_rewrite` off | Switch to PHP 8.1+ (Step 3); ask support to enable `mod_rewrite` |
| *Class 'GdImage' not found* | `gd` extension disabled | Enable `gd` in hPanel PHP Configuration |
| Analytics script not appearing | `ANALYTICS_PROVIDER=none` | Edit `public/config.php` and set the provider explicitly |
| *File not found* on `/api/...` | `api/` not in the right place | Step 6b — upload `api/` one level above `public_html/` |
| QR code returns blank image | `allow_url_fopen` disabled | Ask hosting support, or generate via the API in `format=json` instead |
| **Umami script not firing on Hostinger** | `analytics.php` exists in `public/` but is never `require`d | Each page already `require`s `public/analytics.php` in this release. If you still see no script tag, see the **Why is my Umami script not appearing?** checklist below |

#### Why is my Umami script not appearing? (Hostinger checklist)

Walk through this list in order — every step is something I have
seen cause a silent failure on shared hosting:

1. **Open your page → *View Source* → search for `umami`** (or
   `gtag`). If you see the `<script>` tag, the snippet is fine and
   the problem is on the Umami side (wrong Website ID, wrong domain
   allowlist, ad-blocker, etc.). If you see *nothing*, continue.
2. **Confirm `public/analytics.php` exists** on the server (it
   ships in the repo, but make sure File Manager didn't drop it).
3. **Confirm `public/config.php` exists** and contains the three
   `putenv('…')` lines for your provider. Without it the partial
   sees no env vars and emits nothing.
4. **Check the values you put in `config.php`**:
   - `ANALYTICS_PROVIDER` must be exactly `umami` (lowercase, no
     spaces, no quotes around the value).
   - `UMAMI_SCRIPT_URL` must be the **exact** URL Umami gave you.
     Common values: `https://cloud.umami.is/script.js`,
     `https://us.umami.is/script.js`, or
     `https://your-umami-domain.com/script.js`.
   - `UMAMI_WEBSITE_ID` must be the UUID shown on the Umami
     dashboard (looks like `11111111-2222-3333-4444-555555555555`),
     **not** the website name and **not** the API token.
5. **Check that the file is UTF-8 plain text**. Hostinger File
   Manager sometimes saves files as UTF-8 BOM, which makes the
   first line a no-op and PHP may then choke on the BOM inside
   `putenv()`. Re-upload via FTP in **binary** mode if in doubt.
6. **Make sure Cloudflare / your CDN is not caching the old HTML**.
   Purge the cache for `/`, `/health-calculator.php`, etc.
7. **Open `public/analytics.php` in the browser directly** —
   it should output a single comment line (`<!-- MyAPIs Analytics
   … -->`) or nothing. If you get a *blank page* the PHP parser
   failed; if you get the script tag it works.
8. **Run this one-liner from your laptop** to see exactly what the
   server sends:
   ```bash
   curl -s https://yourdomain.com/ | grep -E 'umami|gtag'
   ```

### Hosting-specific notes

- **Hostinger Single / Premium**: shared IP, no SSH. Use **File
  Manager** or FTP. `gd` is sometimes disabled by default — enable
  it in **hPanel → PHP Configuration**.
- **Hostinger Business / Cloud**: SSH is available; `scp -r
  ./myapis u123@host:/domains/yourdomain.com/public_html/`
  uploads the project in one shot.
- **SiteGround (cPanel)**: use **File Manager** or **SSH Terminal**.
  Go to **cPanel → MultiPHP Manager** to change the PHP version
  per-directory.
- **Namecheap (cPanel)**: same as SiteGround; the bundled
  `.htaccess` works out of the box.
- **Cloudflare in front**: enable **Development Mode** during the
  first deploy to bypass cache and confirm analytics is firing,
  then re-enable caching once everything looks right.

### What you do **not** get on shared hosting

- ❌ Docker (the `🐳 Docker Deployment` section does not apply)
- ❌ `.env` files (use `public/config.php` instead — Step 4)
- ❌ `auto_prepend_file` (every page in `public/` and
  `public/api-specs/` already `require`s `public/analytics.php`
  for you, so analytics fires without it; new pages you add must
  include it themselves — see the snippet in the
  [📈 Analytics → Tracking scope](#tracking-scope) section)

---

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
- **QR Code Generator API**: `POST /api/qr-code-generator/` - Generate QR codes for text, URL, vCard, event, Wi-Fi, and phone (powered by [goQR.me](https://goqr.me/api/doc/create-qr-code/))
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

### QR Code Generator
```bash
# Plain text → PNG (JSON response with base64 image)
curl -X POST "http://localhost:8080/api/qr-code-generator/?format=json" \
  -d "type=text" -d "text=Hello, world!" -d "size=300x300"

# Wi-Fi credentials → direct SVG download
curl "http://localhost:8080/api/qr-code-generator/?format=image" \
  -d "type=wifi" -d "ssid=CafeWiFi" -d "password=beans2024" \
  -d "encryption=WPA" -d "file_type=svg" -d "color=cc0066" \
  --output qr.svg

# Business vCard with multiple dynamic emails / phones / addresses
curl -X POST "http://localhost:8080/api/qr-code-generator/?format=json" \
  -d "type=vcard" \
  -d "first_name=Jane" -d "last_name=Doe" \
  -d "emails[0][type]=WORK" -d "emails[0][value]=jane@acme.com" \
  -d "phones[0][type]=CELL,VOICE" -d "phones[0][value]=+66811234567" \
  -d "addresses[0][type]=WORK" -d "addresses[0][street]=123 Sukhumvit" \
  -d "addresses[0][city]=Bangkok" -d "addresses[0][country]=Thailand"
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
│   ├── qr-code-generator.php # QR Code Generator web interface (text, URL, vCard, event, Wi-Fi, phone)
│   ├── fortune-teller.php   # Fortune Teller web interface
│   ├── randomizer.php       # Random Generator web interface
│   └── api-specs/           # API documentation pages
│       ├── health-calculator.php
│       ├── password-generator.php
│       ├── username-generator.php
│       ├── promptpay-qr-generator.php
│       ├── qr-code-generator.php
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
│   ├── qr-code-generator/   # QR Code Generator API (powered by goQR.me)
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
│       ├── opcache.ini      # Opcache tuning
│       └── analytics.php    # Tracking snippet (auto-prepended)
├── docker-compose.yml       # PHP-FPM + Nginx stack definition
├── Dockerfile               # PHP-FPM image with required extensions
├── example.env              # Sample environment variables
├── .dockerignore            # Files excluded from the image
├── .htaccess                # Apache rewrite rules (root → public)
├── public/
│   └── config.php.example   # Shared-hosting analytics template
│                           # (copy to public/config.php; gitignored)
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
| `ANALYTICS_PROVIDER` | `none` | Tracking snippet to inject: `umami`, `ga4` (Google Analytics 4), or `none` |
| `UMAMI_SCRIPT_URL` | _(empty)_ | URL to the Umami tracker script (e.g. `http://umami:3000/script.js`) |
| `UMAMI_WEBSITE_ID` | _(empty)_ | Umami website UUID shown in the dashboard |
| `GA4_MEASUREMENT_ID` | _(empty)_ | Google Analytics 4 measurement ID (e.g. `G-XXXXXXXXXX`) |
| `UMAMI_PORT` | `3000` | Host port mapped to the optional Umami web container |
| `UMAMI_DB_NAME` / `UMAMI_DB_USER` / `UMAMI_DB_PASSWORD` | `umami` | PostgreSQL credentials for Umami |
| `UMAMI_APP_SECRET` | _(change me)_ | Long random string used by Umami (`openssl rand -hex 32`) |

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

## 📈 Analytics / Visitor Tracking

Every HTML response automatically prepends a tracking snippet
chosen by the `ANALYTICS_PROVIDER` environment variable. The
snippet is implemented by [`docker/php/analytics.php`](docker/php/analytics.php)
and wired in via `auto_prepend_file` in
[`docker/php/php.ini.tpl`](docker/php/php.ini.tpl), so you do not
need to edit any of the tool pages individually. JSON API
responses (`/api/*` or `Accept: application/json`) are skipped so
the tracker never pollutes JSON output.

Supported providers:

| Provider | When to use | Required variables |
|----------|-------------|--------------------|
| `umami` | Self-hosted, cookie-less, works in China | `UMAMI_SCRIPT_URL`, `UMAMI_WEBSITE_ID` |
| `ga4` / `google` | Standard Google Analytics 4 (gtag.js) | `GA4_MEASUREMENT_ID` |
| `none` (default) | Disabled — nothing is emitted | — |

### Option A — Umami (recommended, self-hosted)

Umami is privacy-friendly, does not set cookies, and works in
countries that block Google. The compose file ships with an
optional Umami + PostgreSQL block ready to enable.

1. **Enable the Umami service** in [`docker-compose.yml`](docker-compose.yml):
   uncomment the `umami-db` and `umami` services at the bottom
   (and the matching `volumes:` block).
2. **Set the variables** in `.env`:
   ```env
   ANALYTICS_PROVIDER=umami
   UMAMI_SCRIPT_URL=http://umami:3000/script.js
   UMAMI_WEBSITE_ID=         # fill in after step 3
   UMAMI_APP_SECRET=$(openssl rand -hex 32)
   ```
3. **Create your account & website**:
   - Open `http://localhost:${UMAMI_PORT:-3000}`
   - Default login: `admin` / `umami`
   - Add a website (name + domain `localhost` is fine for dev)
   - Copy the **Website ID** into `UMAMI_WEBSITE_ID`
4. **Restart so PHP picks up the new env values**:
   ```bash
   docker compose up -d --build
   ```
5. **Verify**:
   ```bash
   curl -s http://localhost:8080/ | grep -i umami
   # → <script async defer data-website-id="..." src="http://umami:3000/script.js"></script>
   ```
   Then visit the Umami dashboard — page views should appear within
   a few seconds.

### Option B — Google Analytics 4

1. Create a GA4 property in the
   [Google Analytics admin](https://analytics.google.com/) and copy
   the **Measurement ID** (format `G-XXXXXXXXXX`).
2. Add to `.env`:
   ```env
   ANALYTICS_PROVIDER=ga4
   GA4_MEASUREMENT_ID=G-XXXXXXXXXX
   ```
3. Restart: `docker compose up -d --build`.
4. Verify with `curl -s http://localhost:8080/ | grep gtag`.

### Option C — Umami Cloud / Externally-Hosted Umami

Use this when you don't want to run the Umami stack alongside
MyAPIs — for example when you subscribe to the managed
[Umami Cloud](https://cloud.umami.is/) service, or when Umami is
already running on a separate server you control.

The PHP side stays identical to **Option A**; the only difference
is where the tracker script and the dashboard live.

#### C.1 — Umami Cloud (managed SaaS)

1. Sign up at <https://cloud.umami.is/> and create a website.
   Umami will give you:
   - A **Website ID** (UUID)
   - A **Script URL** — typically
     `https://cloud.umami.is/script.js`
2. Add to `.env`:
   ```env
   ANALYTICS_PROVIDER=umami
   UMAMI_SCRIPT_URL=https://cloud.umami.is/script.js
   UMAMI_WEBSITE_ID=<your-website-id>
   ```
3. Restart: `docker compose up -d --build`.
4. Verify:
   ```bash
   curl -s http://localhost:8080/ | grep -i umami
   # → <script async defer data-website-id="..." src="https://cloud.umami.is/script.js"></script>
   ```
5. Log into <https://cloud.umami.is/> — page views should appear
   within a few seconds.

#### C.2 — Self-hosted Umami on a separate server

If you already operate Umami on another host (or a different VPS
from MyAPIs), point MyAPIs at it instead of spinning up the
containerised stack from Option A.

1. On the Umami server, add a website for `your-domain.com` and
   note the **Website ID**.
2. In MyAPIs `.env`:
   ```env
   ANALYTICS_PROVIDER=umami
   UMAMI_SCRIPT_URL=https://umami.your-domain.com/script.js
   UMAMI_WEBSITE_ID=<your-website-id>
   ```
3. Restart: `docker compose up -d --build`.
4. Verify:
   ```bash
   curl -s http://localhost:8080/ | grep -i umami
   ```

> 💡 **Do not enable the optional `umami` / `umami-db` services**
> in [docker-compose.yml](docker-compose.yml) when using Option C —
> you would be paying for (and maintaining) an instance you are not
> actually using. Leave them commented out.

#### Why choose Option C over Option A?

| | Option A (bundled Umami) | Option C (external Umami) |
|---|---|---|
| **Best for** | Single-host dev/staging, air-gapped setups | Production, multi-app setups, managed-service users |
| **Infrastructure** | One `docker compose` brings up everything | Umami runs separately (managed or another VPS) |
| **Data ownership** | Your own DB on your host | Your own DB (self-hosted) or Umami Inc. (Cloud) |
| **Maintenance** | You upgrade the image with `docker compose pull` | Handled by the Umami Cloud team (C.1) or by you on the Umami host (C.2) |
| **Failure isolation** | An Umami outage cannot take MyAPIs down | Same — MyAPIs stays up even if Umami is offline |
| **Cookie/GDPR** | Cookie-less in both cases | Cookie-less in both cases |

### Tracking scope

- ✅ **HTML pages** in `public/` (landing page + every tool)
- ❌ `/api/*` JSON endpoints — skipped (would corrupt responses)
- ❌ CLI invocations — skipped
- ❌ Requests with `Accept: application/json` — skipped
- 🏢 **Shared hosting** — see
   [🌐 Shared Hosting Deployment](#-shared-hosting-deployment-hostinger--cpanel);
   use `public/config.php` (template:
   [`public/config.php.example`](public/config.php.example))

### Disabling analytics

- **Docker / VPS**: set `ANALYTICS_PROVIDER=none` (the default) in
  `.env` and restart PHP.
- **Shared hosting**: edit `public/config.php` and set
  `putenv('ANALYTICS_PROVIDER=none');`.

In both cases the prepended file short-circuits and emits nothing.

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

- **7** Active Tools
- **7** API Endpoints
- **7** Interactive Documentation Pages
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

## 🚀 Latest Updates (v2.5.0)

### 📇 Dynamic vCard Builder (QR Code Generator)
- **Dynamic structured names** — add/remove unlimited
  `formatted / prefix / first / middle / last / suffix` rows,
  rendered in the correct order in the generated vCard
- **Dynamic nicknames** — multiple `nickname[]` entries collapse
  into a single `NICKNAME:` line in the vCard
- **Per-row contact types** — every email row now carries its own
  `WORK` / `HOME` / `INTERNET` selector, and every phone row carries
  its own `CELL,VOICE` / `WORK,VOICE` / `HOME,VOICE` / `FAX` /
  `VOICE` selector
- **Backward compatible** — legacy single fields (`work_email`,
  `home_phone`, etc.) still work
- **Per-tool docs** migrated to
  [`api/qr-code-generator/README.md`](api/qr-code-generator/README.md)

### 🌐 Shared-Hosting Deployment (Hostinger / cPanel)
- New **[🌐 Shared Hosting Deployment](#-shared-hosting-deployment-hostinger--cpanel)**
  section: prerequisites, two project-layout strategies, hPanel
  walkthrough, PHP-version / extension enablement, file-permission
  cheat sheet, two clean domain-mapping strategies, troubleshooting
  table, and hosting-specific notes for Hostinger Single / Premium /
  Business / Cloud, SiteGround, Namecheap, and Cloudflare
- **Analytics without `.env`** — drop a `public/config.php` that
  calls `putenv()` (shared-hosting fallback in
  [`docker/php/analytics.php`](docker/php/analytics.php))
- **`public/config.php.example`** ships as a copy-and-edit template
- All `public/` pages now include
  [`public/analytics.php`](public/analytics.php) so the snippet is
  present on every tool page without manual edits

### ⚙️ Environment Configuration
- [`example.env`](example.env) is now **tracked in the repo** with
  every variable documented (ports, PHP limits, timezone, analytics
  provider, Umami / GA4 credentials). `.env` itself stays
  git-ignored.

### 🧹 Cleanup
- Removed the stale `docs/nginx-conf/lab01.conf` … `lab04.conf` and
  `docs/requirements/myapi-2.0.md` files

## � Previous Updates (v2.4.0)

### 📈 Analytics & Visitor Tracking
- **Pluggable tracking provider** controlled by `ANALYTICS_PROVIDER`
  in `.env` — supports Umami (self-hosted, cookie-less, works in
  China) and Google Analytics 4 out of the box
- **Zero-touch injection** via `auto_prepend_file` in
  [`docker/php/php.ini.tpl`](docker/php/php.ini.tpl) and a new
  [`docker/php/analytics.php`](docker/php/analytics.php) partial —
  no need to edit every tool page
- **Optional Umami service** added (commented by default) to
  [`docker-compose.yml`](docker-compose.yml) — uncomment to spin up
  Umami + PostgreSQL alongside the API
- **JSON / API responses are excluded** so the tracker never pollutes
  API output
- **Full docs**: see the [📈 Analytics / Visitor Tracking](#-analytics--visitor-tracking)
  section below

## � Previous Updates (v2.3.x)

### 📱 QR Code Generator (v2.3.0 + v2.3.1)
- **6 Content Types**: Plain text, URL, vCard, Event, Wi-Fi, Phone — all powered by the [goQR.me](https://goqr.me/api/doc/create-qr-code/) API
- **SVG Output**: New `file_type=svg` parameter returns scalable vector QR codes (ideal for print)
- **Native Color Pickers**: Foreground / background controlled with `<input type="color">` paired with a hex text field
- **Dynamic vCard Fields**: Add unlimited emails, phones, URLs, and structured addresses (street / city / region / postcode / country)
- **Sticky Form**: All fields (including dynamic rows) repopulate after submission
- **Documentation**: API specs page ([`public/api-specs/qr-code-generator.php`](public/api-specs/qr-code-generator.php)) links to goQR.me docs and includes 9 curl examples

---

## 🏗️ v2.0.0 — Major Architecture Restructuring

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
