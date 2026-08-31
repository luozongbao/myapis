# 🛠️ Local Development Runbook

> วิธีตั้ง environment สำหรับ dev บน local

---

## Prerequisites

- **Docker** ≥ 20.10 + Docker Compose v2
- **Git** ≥ 2.30
- **cURL** สำหรับ test API
- **jq** (optional แต่แนะนำ)

ตรวจสอบ:
```bash
docker --version
docker compose version
git --version
curl --version
jq --version
```

---

## 1. Quick Start (Docker)

```bash
cd /path/to/myapis

# 1. Copy env
cp example.env .env

# 2. Edit .env (optional)
$EDITOR .env

# 3. Start
docker compose up -d

# 4. Check logs
docker compose logs -f php
docker compose logs -f nginx

# 5. Test
curl http://localhost:8080/
curl http://localhost:8080/api/health-calculator/?type=bmi
```

---

## 2. Alternative: PHP Built-in Server

ถ้าไม่มี Docker (เช่น shared-hosting dev):

```bash
cd public/
php -S localhost:8000

# Test
curl http://localhost:8000/
curl "http://localhost:8000/api-specs/health-calculator.php"
```

⚠️ PHP built-in server **ไม่** serve `.env`, multi-segment paths เหมือน Nginx — ใช้ dev เท่านั้น

---

## 3. Hot Reload

### PHP
- ❌ ไม่มี hot reload ที่จำเป็น — PHP-FPM ไม่ cache opcode (opcache disabled ใน dev)
- ✅ แก้ไฟล์แล้ว refresh browser ทันที

### Frontend (HTML/CSS/JS)
- ไม่มี bundler → แก้ไฟล์แล้ว refresh

---

## 4. Project Layout (Dev)

```bash
# ดู structure
tree -L 2 -I 'node_modules|vendor|.git'

# ดูไฟล์ที่แก้ล่าสุด
git status --short

# ดู branch
git branch --show-current
git log --oneline -10
```

---

## 5. Useful Commands

### Docker
```bash
# Enter PHP container
docker compose exec php sh

# Check PHP version
docker compose exec php php -v

# Lint specific file
docker compose exec php php -l api/health-calculator/index.php

# Run PHP script in container
docker compose exec php php -r 'echo PHP_VERSION;'

# Restart PHP-FPM
docker compose restart php

# View real-time logs
docker compose logs -f --tail 100

# Clean rebuild
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Test API
```bash
# BMI endpoint
curl -s "http://localhost:8080/api/health-calculator/?type=bmi&weight=70&height=175" | jq .

# Password generator
curl -s "http://localhost:8080/api/password-generator/?length=16&include_specials=1" | jq .

# QR (returns PNG)
curl -s "http://localhost:8080/api/qr-code-generator/?type=text&text=Hello" -o test.png
file test.png

# Fortune
curl -s "http://localhost:8080/api/fortune-teller/" | jq .
```

### Test CORS Preflight
```bash
curl -X OPTIONS http://localhost:8080/api/health-calculator/ \
  -H "Origin: https://example.com" \
  -H "Access-Control-Request-Method: GET" \
  -i
```

---

## 6. Debugging

### Enable Debug Mode
ใน `.env`:
```env
APP_ENV=development         # แสดง error message
```

ใน `php.ini.tpl`:
```
display_errors = On          # dev only
display_startup_errors = On
log_errors = On
```

### PHP-FPM Logs
```bash
# Real-time
docker compose logs -f php

# Past errors
docker compose exec php cat /usr/local/etc/php-fpm.d/www.conf | grep log
docker compose exec php tail -f /var/log/php-fpm/error.log 2>/dev/null || true
```

### Nginx Logs
```bash
# Access
docker compose exec nginx tail -f /var/log/nginx/access.log

# Error
docker compose exec nginx tail -f /var/log/nginx/error.log

# Or from host (logs from `volumes:` mount)
tail -f docker/nginx/logs/*.log  # ถ้ามี mount
```

### Xdebug (Optional)
เพิ่มใน `docker/php/php.ini.tpl`:
```ini
[Xdebug]
xdebug.mode=develop,debug
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.start_with_request=yes
```

แล้วใน VS Code `launch.json`:
```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug",
      "type": "php",
      "request": "launch",
      "port": 9003
    }
  ]
}
```

---

## 7. Common Dev Tasks

### เพิ่ม Tool ใหม่

```bash
# 1. สร้าง directories
mkdir -p api/<tool-name>/
mkdir -p public/

# 2. สร้างไฟล์
touch api/<tool-name>/index.php
touch public/<tool-name>.php
touch public/api-specs/<tool-name>.php
touch docs/api-specs/<tool-name>.md

# 3. เขียน class + endpoint + UI
$EDITOR api/<tool-name>/index.php

# 4. ทดสอบ
php -l api/<tool-name>/index.php
curl "http://localhost:8080/api/<tool-name>/" -i
```

### แก้ API Spec

```bash
# ห้ามแก้ public/api-specs/ อย่างเดียว — ต้องแก้ docs/api-specs/ (source of truth) ด้วย
$EDITOR docs/api-specs/<tool>.md
$EDITOR public/api-specs/<tool>.php   # sync

# เพิ่ม issue เพื่อ prevent drift
```

---

## 8. Troubleshooting

| ปัญหา | วิธีแก้ |
|-------|---------|
| 404 ทุกอย่าง | ตรวจ `docker/nginx/default.conf` → `fastcgi_pass` ถูกไหม |
| 500 Internal | ดู PHP error log → แก้ code |
| Permission denied | `docker compose exec php chmod -R 755 /var/www/html` |
| Port 8080 in use | เปลี่ยน `WEB_PORT` ใน `.env` |
| Container restart loop | ดู logs → php.ini invalid? → แก้ env vars |

ดูเพิ่มที่ [`troubleshooting.md`](troubleshooting.md)
