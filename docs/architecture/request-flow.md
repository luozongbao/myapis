# Request Flow & Routing

> เอกสารอธิบายการเดินทางของ HTTP Request ตั้งแต่ Client จนถึง PHP

---

## Routing Matrix

| URL Pattern | Nginx Location | File Served | Layer |
|------------|----------------|------------|-------|
| `/` | `location /` | `public/index.php` | Web UI |
| `/{tool}.php` | `location ~ \.php$` | `public/{tool}.php` | Web UI |
| `/api-specs/{tool}.php` | `location ~ \.php$` | `public/api-specs/{tool}.php` | API Spec |
| `/assets/*` | `location ~* \.(css\|js\|...)` | `public/assets/*` | Static |
| `/api/{tool}/` | `location ~ ^/api/([^/]+)/?$` | `api/{tool}/index.php` | REST API |
| `/api/{tool}/{anything}.php` | `location ~ ^/api/.+/.+\.php$` | **403 Forbidden** | Blocked |
| `/.env` | `location ~ /\.` | **404** | Blocked |
| `/README.md` | `location ~* (README\.md\|...)` | **404** | Blocked |

---

## 1. Web UI Request (`/health-calculator.php`)

```
Client Request: GET /health-calculator.php

Nginx:
  match `location ~ \.php$`
  fastcgi_pass → PHP-FPM:9000
  SCRIPT_FILENAME = /var/www/html/public/health-calculator.php

PHP-FPM:
  load php.ini
  auto_prepend_file: docker/php/analytics.php
  analytics:
    - provider = umami
    - URI not /api/*, Accept not JSON
    - emit <script async ...></script>
  require public/health-calculator.php
  render HTML form + JS

Response: text/html (200)
```

---

## 2. API Request (`/api/health-calculator/?type=bmi&weight=70&height=175`)

```
Client Request: GET /api/health-calculator/?type=bmi&weight=70&height=175

Nginx:
  match `location ~ ^/api/([^/]+)/?$`
  capture $1 = "health-calculator"
  fastcgi_pass → PHP-FPM:9000
  SCRIPT_FILENAME = /var/www/html/api/health-calculator/index.php

PHP-FPM:
  load php.ini
  auto_prepend_file: docker/php/analytics.php
  analytics:
    - URI starts with /api/ → SKIP
  require api/health-calculator/index.php
  header('Content-Type: application/json')
  header('Access-Control-Allow-Origin: *')
  parse $_GET → validate → calculate → encode JSON

Response: application/json (200)
```

---

## 3. Preflight (CORS)

```
Client Request: OPTIONS /api/health-calculator/

Nginx:
  match `location ~ ^/api/([^/]+)/?$`
  fastcgi_pass → PHP-FPM

PHP-FPM:
  script:
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      http_response_code(200);
      exit();
    }

Response: 200 (no body)
Headers: Access-Control-Allow-Origin: *
         Access-Control-Allow-Methods: GET, POST, OPTIONS
         Access-Control-Allow-Headers: Content-Type
```

---

## 4. QR Image Request (`/api/qr-code-generator/?type=text&text=Hello`)

```
Client Request: GET /api/qr-code-generator/?type=text&text=Hello

PHP-FPM:
  script:
    payload = buildPayload('text', { text: 'Hello' }) → "Hello"
    qrUrl = buildQrUrl(payload, { size: 300, ... })
    qrUrl = "https://api.qrserver.com/v1/create-qr-code/?data=Hello&size=300x300&..."

  cURL GET → goQR.me
  binary response (PNG)

Response: image/png (binary, 200)
```

---

## 5. Multi-segment API (BLOCKED)

```
Client Request: GET /api/health-calculator/admin/index.php

Nginx:
  match `location ~ ^/api/.+/.+\.php$` → DENY ALL

Response: 403 Forbidden
```

> ป้องกัน path traversal / arbitrary PHP execution ภายใต้ `/api/`

---

## 6. Static Asset

```
Client Request: GET /assets/css/style.css

Nginx:
  match `location ~* \.(css|js|...)`
  root /var/www/html/public
  serve file
  Cache-Control: public, max-age=604800
  Expires: +7 days

Response: text/css (200, cached)
```

---

## 7. Analytics Prepend (Web UI only)

`docker/php/analytics.php` ถูก prepend ก่อนทุก response:

```
PHP-FPM lifecycle:
  1. Container start: render php.ini from template
  2. Request comes in
  3. auto_prepend_file runs FIRST
     - if PHP_SAPI === 'cli' → return (no output)
     - if URI starts with /api/ → return (no output)
     - if Accept: application/json → return (no output)
     - else: emit <script src="...umami..."></script>
  4. Main script runs
  5. Output sent to client
```

ดูเพิ่มที่ [`docker/php/analytics.php`](../../docker/php/analytics.php)

---

## 8. Error Flow

```
PHP script throws Exception
  ↓
catch block
  ↓
http_response_code(400 or 500)
  ↓
echo json_encode([...error info...])
  ↓
PHP-FPM returns to Nginx
  ↓
Nginx adds CORS + security headers
  ↓
Client receives structured error JSON
```

**ห้าม**: ใช้ `php die()` หรือ exit() ก่อนตั้ง header → จะทำให้ header ไม่ครบ
