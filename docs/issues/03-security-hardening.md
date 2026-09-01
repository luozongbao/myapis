# ใบงาน 03 — Security Hardening

> **สอดคล้อง goal01 ข้อ 3** — "เพิ่ม Security ป้องกันการ Attack"
> ต่อยอดจากใบงาน 01 (style.css) และ 02 (header/footer + analytics idempotent)
> โครงสร้างอ้างอิง: [`docs/designs/file-structures.md`](../designs/file-structures.md)

---

## 🎯 Objective

ปิดช่องโหว่ 3 ระดับ: **Web server (Nginx)** → **PHP runtime** → **Application code**

---

## 📁 ไฟล์ที่เกี่ยวข้อง

| ไฟล์ | ประเภทงาน |
| --- | --- |
| `docker/nginx/default.conf` | security headers, CSP, rate-limit, deny sensitive paths |
| `docker/php/php.ini.tpl` | PHP hardening directives |
| `public/includes/helpers.php` | เพิ่ม input-validation helpers (ถ้าทำ) |
| `api/*/index.php` (7 ไฟล์) | input validation, `random_int`, path-traversal fix |
| `public/includes/analytics.php` + `docker/php/analytics.php` | idempotent (ทำแล้วใน 02 — ตรวจซ้ำ) |
| `.gitignore` | ตรวจว่า `config.php` / `.env` ถูก ignore |

---

## 📋 งาน

### 1. Nginx — security headers + CSP (`docker/nginx/default.conf`)

#### 1.1 เพิ่ม header (ขยายจากของเดิม)

- [ ] คง header เดิม (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`)
- [ ] เพิ่ม header ใหม่:

```nginx
    # Security headers (ขยาย)
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data: https://api.qrserver.com; font-src 'self'; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;
    add_header Cross-Origin-Opener-Policy "same-origin" always;
    add_header Cross-Origin-Resource-Policy "same-site" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
```

> ⚠️ **CSP + Analytics**: หลัง refactor ทุกหน้าใช้ `style.css`/`app.js` เอง → `script-src 'self'`
> ใช้ได้ แต่ถ้าเปิด analytics ต้อง**เพิ่ม domain** ลงใน `script-src` (และ `connect-src` สำหรับ Umami):
> - Umami: `script-src 'self' https://<umami-host>` + `connect-src 'self' https://<umami-host>`
> - GA4: `script-src 'self' https://www.googletagmanager.com https://www.google-analytics.com` + `connect-src ... https://www.google-analytics.com`
> - ภาพ QR มาจาก `https://api.qrserver.com` → มีใน `img-src` แล้ว

> ⚠️ **HSTS** บังคับ HTTPS — ถ้า dev ใช้ `http://localhost` ให้ comment บรรทัด HSTS ไว้ตอน dev
> แล้วเปิดเมื่อ deploy ด้วย HTTPS

#### 1.2 Block direct access to sensitive paths

- [ ] เพิ่ม rule (วาง**ก่อน** `location ~ \.php$` ที่อยู่ท้ายไฟล์):

```nginx
    # Block direct access to config.php and includes/ partials
    location ~ ^/includes/ {
        deny all;
    }

    location ~ /(^|/)config\.php$ {
        deny all;
    }
```

- [ ] เพิ่ม `config.php` เข้าไปในรายการ deny เดิม (รวมกับ README/RELEASE/.env):

```nginx
    location ~* (README\.md|RELEASE\.md|composer\.(json|lock)|\.env|config\.php) {
        deny all;
    }
```

#### 1.3 Rate limiting (กัน DoS/abuse บน API)

- [ ] เพิ่ม zone ที่ระดับบนสุดของ `default.conf` (conf.d ถูก include ใน `http` context):

```nginx
# Rate limiting zones
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
limit_req_zone $binary_remote_addr zone=general:10m rate=30r/s;
```

- [ ] ใส่ `limit_req` ใน API location:

```nginx
    location ~ ^/api/([^/]+)/?$ {
        limit_req zone=api burst=20 nodelay;
        fastcgi_pass php:9000;
        # ... (ของเดิม)
    }
```

- [ ] ใส่ `limit_req zone=general burst=50 nodelay;` ใน `location /` (ไม่บังคับ)

#### 1.4 Host allow-list (กัน Host-header injection)

- [ ] เพิ่ม (แก้ `example.com` เป็นโดเมนจริง; uncomment เมื่อ deploy):

```nginx
    # Reject requests with unexpected Host header
    # if ($host !~* ^(localhost|127\.0\.0\.1|example\.com|www\.example\.com)$) {
    #     return 444;
    # }
```

### 2. PHP runtime hardening (`docker/php/php.ini.tpl`)

- [ ] เพิ่ม directives ต่อท้าย template:

```ini
; ---- Security hardening ----
expose_php = Off
allow_url_include = Off
allow_url_fopen = Off
session.use_strict_mode = 1
session.cookie_httponly = 1
session.cookie_samesite = Lax
; session.cookie_secure = 1   ; เปิดเมื่อใช้ HTTPS เท่านั้น

; ปิดฟังก์ชันอันตราย (verify ว่า app ไม่ได้ใช้ก่อน)
disable_functions = exec,shell_exec,system,passthru,proc_open,popen
```

- [ ] (Optional) จำกัดไฟล์ที่อ่านได้:

```ini
; open_basedir = /var/www/html:/tmp
```

> ⚠️ `disable_functions` — ตรวจก่อนว่า 7 tool ไม่ได้ใช้ฟังก์ชันในลิสต์ (ปัจจุบันไม่ใช้)
> ⚠️ `allow_url_fopen = Off` — app อ่านเฉพาะไฟล์ local (`predictions/*.json`) จึงปลอดภัย;
>   ภาพ QR ถูกโหลดฝั่ง browser (ไม่ผ่าน PHP) จึงไม่กระทบ

### 3. Application level

#### 3.1 Output escaping (XSS) — ต่อจากใบงาน 02

- [ ] audit ทุก `echo` / `<?=` ของค่าจาก `$_SERVER`, `$_GET`, `$_POST`, `$baseUrl` → ผ่าน `e()`
- [ ] โดยเฉพาะ `getBaseUrl()` ที่ใช้ `$_SERVER['HTTP_HOST']` ต้อง `<?= e($baseUrl) ?>` เสมอ

#### 3.2 API input validation (7 ไฟล์)

- [ ] ใช้ whitelist + `filter_var` กับทุก input (แทนการเชื่อค่าโดยตรง):

```php
// ตัวอย่าง: fortune-teller
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'en';
if (!in_array($lang, ['th', 'zh', 'en'], true)) {
    $lang = 'en';
}

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if ($id !== null) {
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if ($id === false || $id < 1 || $id > 52) {
        $id = null;
    }
}
```

- [ ] **Path traversal fix (สำคัญ)** — `api/fortune-teller/index.php` สร้าง path จาก `$id`:

```php
// เดิม (เสี่ยงถ้ารับ id จากผู้ใช้โดยไม่ validate):
// $filePath = __DIR__ . '/predictions/' . $fortuneId . '.json';

// ปลอดภัย: validate ก่อนใช้
$fortuneId = filter_var($fortuneId, FILTER_VALIDATE_INT);
if ($fortuneId === false || $fortuneId < 1 || $fortuneId > 52) {
    // error response
}
$filePath = __DIR__ . '/predictions/' . $fortuneId . '.json';
```

- [ ] `health-calculator`: `height`, `weight`, `age` → `FILTER_VALIDATE_FLOAT/INT` + clamp ค่าขั้นต่ำ/สูงสุด
- [ ] `password-generator` / `username-generator`: `length`, `count` → int + clamp (ดู min/max ในหน้าเว็บ), booleans → `filter_var(..., FILTER_VALIDATE_BOOLEAN)`
- [ ] `randomizer`: `min`/`max`/`count`/`sides` → int + ตรวจ `min <= max`
- [ ] `promptpay-qr-generator` / `qr-code-generator`: ตรวจ/encode ค่า input ก่อนสร้าง QR

#### 3.3 ใช้ `random_int` แทน `rand()`/`mt_rand()`

- [ ] audit ทุก `api/*/index.php` — เปลี่ยน `rand(`/`mt_rand(` → `random_int(` (fortune ใช้ `rand(1,52)` อยู่)
- [ ] ยืนยัน README claim "cryptographically secure" เป็นจริงทุกจุด

#### 3.4 JSON + CORS consistency

- [ ] ทุก endpoint ตั้ง `header('Content-Type: application/json; charset=UTF-8');` ก่อน echo (มีแล้ว — ตรวจครบ)
- [ ] คง `Access-Control-Allow-Origin: *` (public API) — หรือเปลี่ยนเป็น allow-list เฉพาะ origin ที่ต้องการ

#### 3.5 Secrets protection

- [ ] `.gitignore` มี `.env` และ `public/config.php` (ตรวจ — ปัจจุบันมี `.env` แต่**เพิ่ม `public/config.php`**):

```gitignore
# Environment / secrets
.env
.env.*
!.env.example
public/config.php
```

- [ ] ตรวจว่าไม่มี secret ค้างใน repo (grep `putenv('ANALYTICS_PROVIDER=umami')` ใน `config.php.example` เป็นค่าตัวอย่าง — ปลอดภัย)

### 4. ตรวจ idempotent analytics (ทวนจาก 02)

- [ ] `public/includes/analytics.php` และ `docker/php/analytics.php` ใช้ return-early guard แล้ว
- [ ] ยืนยันว่าเมื่อ Docker `auto_prepend_file` ทำงาน + header include → snippet ปรากฏครั้งเดียว

---

## ✅ Acceptance Criteria

1. `curl -I` เห็น header ใหม่ครบ (CSP, HSTS*, Permissions-Policy, COOP, CORP)
2. `GET /config.php` และ `GET /includes/header.php` → `403/404` (ไม่ leak)
3. API `/api/fortune-teller/?id=../../etc/passwd` → ตอบ `success:false` หรือ default (ไม่ error 500, ไม่ leak path)
4. `/api/fortune-teller/?id=999` → ตอบ error/default อย่างปลอดภัย (ไม่ fatal)
5. ไม่มี `rand(`/`mt_rand(` ใน `api/*` (grep ว่าง)
6. เปิดใช้ analytics แล้ว snippet ไม่ซ้ำ

## 🔍 วิธีตรวจสอบ

```bash
# 1) compose + nginx config ใช้ได้
docker compose config -q
docker compose up -d --build

# 2) headers
curl -I http://localhost:8080 | grep -iE 'content-security|strict-transport|permissions-policy'

# 3) sensitive path ถูกบล็อก
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/config.php
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/includes/header.php

# 4) path traversal (ควร 200 + JSON error, ไม่ใช่ 500/leak)
curl -s 'http://localhost:8080/api/fortune-teller/?id=../../etc/passwd'

# 5) input เกินช่วง
curl -s 'http://localhost:8080/api/fortune-teller/?id=999'

# 6) ไม่มี rand() เหลือ
grep -rnE '\b(rand|mt_rand)\(' api --include='*.php' || echo "clean ✅"
```

## ⚠️ หมายเหตุ / ความเสี่ยง

- **CSP อาจบล็อก analytics** → ต้องขยาย `script-src`/`connect-src` เมื่อเปิด analytics (ดู 1.1)
- **HSTS** ใน dev ใช้ http → comment HSTS ตอน dev
- **`disable_functions`/`open_basedir`** → verify กับทุก tool ก่อน commit (ป้องกัน breaking change)
- **Rate limit** ตั้ง `rate`/`burst` ให้เหมาะกับ traffic จริง กัน block ผู้ใช้ปกติ
- ลำดับ `location` ใน nginx สำคัญ: วาง deny rules **ก่อน** `location ~ \.php$`
