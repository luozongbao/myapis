# 🔧 Troubleshooting Runbook

> แก้ปัญหาที่พบบ่อย

---

## 1. Container Issues

### ❌ Container อยู่ในสถานะ Restarting

```bash
docker compose ps
# STATUS: Restarting (1) X seconds ago

docker compose logs php --tail=100
```

**สาเหตุที่พบบ่อย:**
- `php.ini` render fail — env var invalid
- Port conflict

**วิธีแก้:**
```bash
# ดู logs ทั้งหมด
docker compose logs php

# ตรวจ env
docker compose exec php sh
printenv | grep PHP_

# Restart
docker compose restart php
```

---

### ❌ "Permission denied" ใน PHP

```bash
# Error log
[error] FastCGI sent in stderr: "Primary script unknown"
```

**วิธีแก้:**
```bash
docker compose exec php chmod -R 755 /var/www/html
docker compose exec php chown -R www-data:www-data /var/www/html
```

ถ้าใช้ bind mount:
```bash
# ใน Dockerfile
RUN chown -R www-data:www-data /var/www/html
```

---

## 2. Nginx Issues

### ❌ 404 ทุก API

**อาการ:** `curl http://localhost:8080/api/health-calculator/` → 404

**ตรวจ:**
```bash
# 1. ไฟล์อยู่ไหม
docker compose exec php ls -la api/health-calculator/index.php

# 2. Nginx config
docker compose exec nginx cat /etc/nginx/conf.d/default.conf

# 3. ใน config ควรเห็น:
#    location ~ ^/api/([^/]+)/?$ {
#        try_files $uri /api/$1/index.php?$args;
#        fastcgi_pass php:9000;
#        ...
#    }
```

**วิธีแก้:**
- Restart Nginx: `docker compose restart nginx`
- ตรวจ `docker/nginx/default.conf` → syntax

---

### ❌ 502 Bad Gateway

**อาการ:** Nginx ขึ้น 502

**สาเหตุ:** PHP-FPM down หรือ network issue

**วิธีแก้:**
```bash
# ตรวจ PHP
docker compose ps
docker compose logs php --tail=50

# Restart PHP
docker compose restart php

# ตรวจ network
docker network inspect myapis-net
```

---

### ❌ 413 Payload Too Large

**อาการ:** อัปโหลดไฟล์แล้วได้ 413

**วิธีแก้:**
```bash
# เพิ่มใน .env
NGINX_CLIENT_MAX_BODY_SIZE=50M
PHP_UPLOAD_MAX_FILESIZE=50M
PHP_POST_MAX_SIZE=50M

# Restart
docker compose restart
```

---

## 3. PHP Errors

### ❌ 500 Internal Server Error

**ขั้นตอน:**
```bash
# 1. ดู error log
docker compose logs php --tail=200

# 2. ดู error ของ Nginx
docker compose logs nginx --tail=200

# 3. ดู PHP-FPM log ใน container
docker compose exec php cat /usr/local/etc/php-fpm.conf | grep error_log
docker compose exec php ls /usr/local/var/log/

# 4. ลองรัน script ตรง ๆ
docker compose exec php php -r 'echo "hello\n";'
```

**สาเหตุที่พบบ่อย:**
- Syntax error — `php -l api/health-calculator/index.php`
- Missing `use`/`require`
- File not readable
- Extension not installed

---

### ❌ "Class not found"

```bash
# Verify autoloader / require
docker compose exec php php -l api/health-calculator/index.php

# ตรวจ namespace / use statements
```

---

### ❌ JSON parse fail

**Error:** `JSON_THROW_ON_ERROR` throws JsonException

**สาเหตุ:**
- Response contain BOM
- Encoding mismatch
- Infinite recursion

**วิธีแก้:**
```php
// escape unicode + UTF-8
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

// ถ้า fail — escape sequence
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | ...);
```

---

## 4. API-Specific Issues

### ❌ QR API return 500

**สาเหตุที่เป็นไปได้:**
- goQR.me down
- Network issue
- Invalid parameters

**ตรวจ:**
```bash
# Test goQR.me ตรง ๆ
curl -I "https://api.qrserver.com/v1/create-qr-code/?data=test&size=100x100"

# Test จาก container
docker compose exec php curl -I "https://api.qrserver.com/"
```

**ถ้า goQR.me down:**
- แสดง error JSON ที่เข้าใจง่าย:
  ```json
  {
    "success": false,
    "error": "QR_SERVICE_UNAVAILABLE",
    "message": "External QR service is currently down. Try again later."
  }
  ```

---

### ❌ PromptPay return "Invalid target"

**ตรวจ:**
- phone = 10 digits, start with 0 → ต้อง pad to 13 (+66)
- tax_id = 13 digits
- e-wallet = 15 digits (e.g., 0812345678901)

ดู spec: [`docs/api-specs/promptpay-qr-generator.md`](../api-specs/promptpay-qr-generator.md)

---

### ❌ Fortune file not found

**สาเหตุ:** path ผิด

```php
// ✅ ถูก
$file = __DIR__ . "/predictions/{$id}.json";

// ❌ ผิด (relative)
$file = "predictions/{$id}.json";
```

---

### ❌ Password Generator return "Invalid charset"

**ตรวจ:**
- `include_uppercase=1` etc. ต้อง true อย่างน้อย 1 ตัว
- ถ้าเลือก 0 ทั้งหมด → return 400

---

## 5. Performance Issues

### ❌ Response ช้า (>1s)

**ตรวจ:**
```bash
# 1. Time it
time curl "http://localhost:8080/api/health-calculator/?type=bmi&weight=70&height=175"

# 2. Resource usage
docker stats --no-stream

# 3. PHP slow log (ถ้าเปิด)
docker compose exec php cat /usr/local/etc/php-fpm.d/zz-docker.conf
```

**สาเหตุที่พบบ่อย:**
- QR API calls goQR.me (~200–500ms) — ไม่สามารถแก้ได้
- Cold start opcache (first request) — disable opcache หรือ prime
- DB/file I/O (ถ้ามี — ปัจจุบันไม่มี)

---

### ❌ Memory ใกล้ limit

**วิธีแก้:**
```bash
# ใน .env
PHP_MEMORY_LIMIT=512M

# Restart
docker compose restart php
```

---

## 6. Network Issues

### ❌ CORS Error ใน Browser

**อาการ:** `No 'Access-Control-Allow-Origin' header`

**ตรวจ:**
```bash
curl -I http://localhost:8080/api/health-calculator/ | grep -i cors
```

ควรเห็น:
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, OPTIONS
```

**ถ้าไม่มี** → script ไม่ได้ emit headers → ตรวจ `api/<tool>/index.php`

---

### ❌ Mixed Content (HTTPS → HTTP)

**อาการ:** Blocked by browser

**แก้:** ใช้ HTTPS เท่านั้น หรือ self-host analytics ผ่าน HTTPS

---

## 7. Common Pitfalls

### ❌ ลืมตั้ง `Content-Type`

→ Browser อาจเดา MIME ผิด

```php
header('Content-Type: application/json; charset=UTF-8');
```

### ❌ echo UTF-8 BOM

→ JSON parse fail

```php
// ตรวจไฟล์ .php ว่าไม่มี BOM
file -i api/health-calculator/index.php
// ควรเป็น: charset=utf-8 (ไม่มี bom)
```

### ❌ ใช้ `rand()` แทน `random_int()`

สำหรับ security-sensitive → password / token

### ❌ Inline styles ทุกที่ → แก้ Issue ที่กำลังจะเปิด

---

## 8. ยังแก้ไม่ได้?

1. เช็ค Issues ที่คล้ายกัน: [`docs/issues/open/`](../issues/open/)
2. อ่าน [`README.md`](../../README.md)
3. สร้าง issue ใหม่ใน `docs/issues/open/ISSUE-XXX-...md` พร้อม:
   - Error message
   - Steps to reproduce
   - Environment (Docker / shared hosting / etc.)
   - Logs

---

## 9. Quick Commands Cheatsheet

```bash
# ─── Docker ───
docker compose ps
docker compose logs -f --tail=100
docker compose restart
docker compose exec php sh
docker compose exec php php -l <file>
docker compose exec php php -r 'echo ini_get("memory_limit");'
docker stats --no-stream

# ─── Network ───
curl -i <url>
curl -X OPTIONS <url> -i
curl -I <url>
nslookup api.qrserver.com

# ─── Logs ───
tail -f /var/log/nginx/*.log
docker compose logs nginx | grep -i error

# ─── Git ───
git status --short
git log --oneline -10
git diff HEAD~1
```
