# 🏗️ Architecture Overview

> ภาพรวม High-level ของระบบ MyAPIs

---

## 1. Stack สรุปสั้น

| Layer | Technology | Why |
|-------|-----------|-----|
| **Runtime** | PHP 8.2 (FPM) | พร้อมใช้ JSON, mbstring, gd, intl, opcache |
| **Web Server** | Nginx 1.27 (alpine) | Lightweight, fast |
| **Container** | Docker + Docker Compose v2 | reproducible environment |
| **External QR** | `api.qrserver.com` (goQR.me) | ไม่ต้อง bundle lib |
| **Analytics** | Umami หรือ GA4 (optional) | opt-in ผ่าน env |
| **ไม่มี** | Database, Composer, Node.js | เพื่อ portability |

---

## 2. High-Level Diagram

```
┌────────────────────────────────────────────────────────────┐
│                       Clients                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Web Browser │  │ Mobile App   │  │ cURL / SDK   │      │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘      │
└─────────┼─────────────────┼─────────────────┼──────────────┘
          │                 │                 │
          │  HTTPS          │  HTTPS          │  HTTPS
          ▼                 ▼                 ▼
┌────────────────────────────────────────────────────────────┐
│                Reverse Proxy (optional)                    │
│        Cloudflare / Nginx / Caddy (TLS termination)        │
└─────────────────────────┬──────────────────────────────────┘
                          │
                          ▼
┌────────────────────────────────────────────────────────────┐
│                  Nginx (port 80)                           │
│  ┌────────────────────────────────────────────────────┐    │
│  │ /api/<tool>/      → fastcgi → PHP-FPM             │    │
│  │ /api/<tool>/...   → 404 (multi-segment blocked)   │    │
│  │ /*.php            → fastcgi → PHP-FPM             │    │
│  │ /<anything-else>  → serve from public/             │    │
│  │ /assets/*         → serve static + cache 7d       │    │
│  │ /.env, /README.md → 404 (blocked)                 │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────┬──────────────────────────────────┘
                          │ FastCGI (port 9000)
                          ▼
┌────────────────────────────────────────────────────────────┐
│                   PHP-FPM 8.2                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │  auto_prepend_file: docker/php/analytics.php      │    │
│  │                                                    │    │
│  │  Request routing:                                  │    │
│  │    /api/health-calculator/   → api/health-calc…    │    │
│  │    /health-calculator.php    → public/health…      │    │
│  │    /api-specs/.../           → public/api-specs/…  │    │
│  └────────────────────────────────────────────────────┘    │
└─────────────────────────┬──────────────────────────────────┘
                          │
                          ▼
┌────────────────────────────────────────────────────────────┐
│           File System (read-only volume)                   │
│                                                            │
│  /var/www/html/                                            │
│  ├── api/              ← REST endpoints                    │
│  │   ├── health-calculator/index.php                     │
│  │   ├── password-generator/index.php                     │
│  │   └── ...                                             │
│  ├── public/          ← web UI + API specs                │
│  │   ├── index.php    (landing page)                      │
│  │   ├── health-calculator.php                           │
│  │   ├── api-specs/health-calculator.php                 │
│  │   └── assets/                                         │
│  └── docker/          ← config (php.ini, nginx.conf)      │
└────────────────────────────────────────────────────────────┘

                          │
                          ▼ (optional, for QR)
┌────────────────────────────────────────────────────────────┐
│               External: api.qrserver.com                   │
│                  (goQR.me — render QR images)              │
└────────────────────────────────────────────────────────────┘
```

---

## 3. Layer Responsibilities

### 3.1 Nginx Layer (Reverse Proxy + Static)
- TLS termination (ถ้าใช้ HTTPS)
- Routing ตาม URL pattern
- Block sensitive files (`.env`, `README.md`, multi-segment `/api/`)
- Cache static assets 7 วัน
- Security headers (`X-Frame-Options`, `X-Content-Type-Options`, etc.)
- Body size limit (`client_max_body_size`)

### 3.2 PHP-FPM Layer (Application)
- รัน PHP ผ่าน FastCGI
- ทุก response ถูก prepend ด้วย `docker/php/analytics.php` (ถ้าตั้งค่า)
- CORS headers (เพิ่มในแต่ละ API)
- Business logic + validation

### 3.3 Application Code Layer
- **Stateless** — ไม่มี session, ไม่มี DB
- **Class per tool** — เช่น `PasswordGenerator`, `RandomGenerator`, `PromptPayAPI`
- **No Composer** — ใช้ PHP Standard Library เท่านั้น

### 3.4 Static Layer (Web UI)
- HTML/CSS/JS ล้วน — render ฝั่ง server
- ไม่มี framework (jQuery/React/Vue)
- Inline styling (ปัจจุบัน) — จะ refactor เป็น external CSS ใน Issue ที่กำลังจะเปิด

---

## 4. Request Flow (ตัวอย่าง)

### 4.1 เรียก `/api/health-calculator/?weight=70&height=175&type=bmi`

```
1. Client → HTTPS → Reverse Proxy (Cloudflare)
2. Proxy → HTTP → Nginx (port 80)
3. Nginx:
   a. match `location ~ ^/api/([^/]+)/?$`
   b. fastcgi_pass → PHP-FPM:9000
   c. set SCRIPT_FILENAME = /var/www/html/api/health-calculator/index.php
4. PHP-FPM:
   a. load php.ini (rendered from template with env vars)
   b. auto_prepend_file = docker/php/analytics.php
   c. analytics.php checks provider — skip (Accept: JSON)
   d. include api/health-calculator/index.php
   e. script reads $_GET → validate → call calculateBMI()
   f. echo JSON
5. Nginx:
   a. add CORS headers (script sets them)
   b. add security headers
   c. return to client
6. Client: parse JSON, show BMI
```

### 4.2 เปิดหน้า `https://example.com/health-calculator.php`

```
1. Client → Nginx
2. Nginx: location / → serve from /var/www/html/public/
3. Nginx: location ~ \.php$ → fastcgi → PHP-FPM
4. PHP-FPM: render public/health-calculator.php (HTML with form)
5. (HTML form action calls /api/.../ via fetch/ajax)
```

---

## 5. Data Flow

### 5.1 Input Sources
- **HTTP GET query string** — `$_GET`
- **HTTP POST form data** — `$_POST`
- **HTTP POST JSON body** — `json_decode(file_get_contents('php://input'), true)`

### 5.2 Data Sanitization
- **Type casting** — `(int)`, `(float)`, `(bool)`, `(string)`
- **String sanitize** — `trim()`, `strip_tags()`, `htmlspecialchars()`
- **Numeric** — `filter_var($x, FILTER_VALIDATE_FLOAT)`
- **No SQL** — ไม่มี query
- **No shell** — ไม่มี `exec()`, `system()`

### 5.3 Output Format
- **JSON** สำหรับ API (header: `Content-Type: application/json`)
- **HTML** สำหรับ Web UI (header: `Content-Type: text/html; charset=UTF-8`)
- **PNG** สำหรับ QR images (header: `Content-Type: image/png`)
- **vCard/iCalendar text** สำหรับ QR content types (header: `Content-Type: text/plain`)

---

## 6. Why ไม่มี Database

| Decision | Rationale |
|----------|----------|
| ไม่มี DB | APIs เป็น **stateless utility** — ไม่ต้อง persist ข้อมูลผู้ใช้ |
| ไม่มี cache layer | Hot data (fortune, theme words) อยู่ใน PHP array / JSON file — load จาก memory เร็วพอ |
| ไม่มี queue | ทุกงานทำเสร็จใน < 200ms |
| ไม่มี external service ยกเว้น QR | Single point of failure ต้องน้อยที่สุด |

ผลลัพธ์:
- ✅ Deploy ง่าย (1 container PHP + 1 container Nginx)
- ✅ Cold start < 2s
- ✅ Scale แนวนอนได้ทันที
- ⚠️ Trade-off: ไม่มี analytics/log ของ request (ยกเว้น PHP error log)

---

## 7. Failure Modes & Mitigation

| Failure | Impact | Mitigation |
|---------|--------|-----------|
| PHP-FPM down | API/UI ทั้งหมด down | Nginx healthcheck + restart policy |
| Nginx down | API/UI ทั้งหมด down | Reverse proxy healthcheck |
| goQR.me down | QR API คืน 500 | แสดง error JSON ที่เข้าใจง่าย (ดู `qr-code-generator.md`) |
| File permission wrong | 404 / 500 | Dockerfile `chown www-data:www-data /var/www/html` |
| `.env` invalid | Analytics off | `analytics.php` fallback → `public/config.php` → none |

---

## 8. Performance Characteristics

| Scenario | Expected Latency |
|----------|-----------------|
| API call (no QR) | 5–30 ms |
| API call (with QR) | 200–500 ms (depends on goQR.me) |
| Web UI page load | 50–150 ms |
| Landing page (`public/index.php`) | 100–300 ms |

Bottleneck ส่วนใหญ่อยู่ที่ **goQR.me** หากใช้ QR API

---

## 9. Future Architecture Considerations

ถ้าต้อง scale ขึ้น:

| Concern | Current | Future Option |
|---------|---------|---------------|
| Rate limiting | ❌ | Nginx `limit_req` หรือ middleware |
| Caching | ❌ | Redis สำหรับ username theme / fortune |
| Auth | ❌ | API Key + DB (Postgres) |
| Background jobs | ❌ | Sidekiq-style queue |
| Observability | Basic | Prometheus + Grafana + Sentry |

ดู Roadmap ใน [`requirements/product-brief.md`](../requirements/product-brief.md#7-roadmap-ระดับสูง-high-level-roadmap)
