# 🚀 Deployment Runbook

> ขั้นตอน Deploy MyAPIs ไป Production

---

## 1. Pre-Deploy Checklist

- [ ] Tag version ใน `RELEASE.md`
- [ ] Git tag: `git tag v<version>`
- [ ] CI build pass (ถ้ามี)
- [ ] Documentation updated
- [ ] Env vars prepared
- [ ] DNS ชี้มา server แล้ว
- [ ] TLS cert พร้อม (Let's Encrypt / Cloudflare)

---

## 2. Deploy ด้วย Docker Compose

### 2.1 เตรียม Server

```bash
# Server: Ubuntu 22.04 + Docker installed

# Login
ssh user@server

# Install Docker (one-liner)
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
```

### 2.2 Deploy Code

```bash
# 1. Clone
git clone https://github.com/<org>/myapis.git /opt/myapis
cd /opt/myapis

# 2. Checkout tag
git checkout v<version>

# 3. Prepare env
cp example.env .env
$EDITOR .env
#   - APP_ENV=production
#   - TZ=Asia/Bangkok
#   - WEB_PORT=80  (internal)
#   - ANALYTICS_PROVIDER=umami (optional)
#   - UMAMI_SCRIPT_URL=...
#   - UMAMI_WEBSITE_ID=...

# 4. Build + start
docker compose pull   # ถ้าใช้ registry
docker compose up -d

# 5. Verify
curl -i http://localhost/  # ผ่าน reverse proxy
docker compose ps
docker compose logs --tail=100
```

### 2.3 Reverse Proxy (Caddy / Nginx)

#### Caddyfile
```caddy
api.example.com {
    reverse_proxy localhost:8080
    encode zstd gzip
    header {
        Strict-Transport-Security "max-age=31536000; includeSubDomains"
        X-Frame-Options "SAMEORIGIN"
        X-Content-Type-Options "nosniff"
        Referrer-Policy "strict-origin-when-cross-origin"
    }
}
```

#### Nginx + Certbot
```nginx
server {
    listen 443 ssl http2;
    server_name api.example.com;

    ssl_certificate /etc/letsencrypt/live/api.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.example.com/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

server {
    listen 80;
    server_name api.example.com;
    return 301 https://api.example.com$request_uri;
}
```

---

## 3. Deploy แบบ Shared Hosting (Hostinger / cPanel)

ดูวิธีเต็มที่ [`README.md`](../../README.md#-shared-hosting-deployment-hostinger--cpanel)

สรุปสั้น:

```bash
# 1. บีบอัด project
zip -r myapis.zip api/ public/ -x "*.md" "docker*"

# 2. อัปโหลดผ่าน cPanel File Manager
#    - แตกไฟล์ที่ public_html/
#    - ย้ายไฟล์ทั้งหมดเข้า public_html/api/ และ public_html/public/ โดยตรง
#       (ไม่ต้องมี wrapper public/ สำหรับ shared hosting)

# 3. ตั้ง PHP version: 8.1+
#    cPanel → MultiPHP Manager → เลือก 8.2

# 4. (Optional) ตั้ง analytics
$EDITOR public_html/public/config.php
#   ใส่ UMAMI_SCRIPT_URL + UMAMI_WEBSITE_ID

# 5. Test
curl https://yourdomain.com/api/health-calculator/?type=bmi
```

⚠️ ใน shared hosting ต้อง:
- ลบ `docker/`, `docs/`, `prompts/`, `.git/`, `Dockerfile` ออก
- ใช้ `.htaccess` สำหรับ Apache (Nginx config ใช้ไม่ได้)
- ตั้ง `post_max_size`/`upload_max_filesize` ผ่าน `php.ini` หรือ `.user.ini`

---

## 4. Deploy บน VPS (Bare-Metal)

```bash
# 1. Install Nginx + PHP-FPM
sudo apt update
sudo apt install -y nginx php8.2-fpm php8.2-cli php8.2-mbstring php8.2-intl php8.2-gd php8.2-bcmath php8.2-opcache

# 2. Copy files
sudo mkdir -p /var/www/myapis
sudo cp -r api/ public/ /var/www/myapis/
sudo chown -R www-data:www-data /var/www/myapis

# 3. Nginx config (เหมือน docker/nginx/default.conf)
sudo cp docker/nginx/default.conf /etc/nginx/sites-available/myapis
sudo ln -s /etc/nginx/sites-available/myapis /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# 4. (Optional) Umami
# ตาม https://umami.is/docs/install
```

---

## 5. ค่า Environment สำหรับ Production

`.env` template:

```bash
# ──────────── Application ────────────
PROJECT_NAME=myapis
TZ=Asia/Bangkok
APP_ENV=production                   # ปิด error display

# ──────────── Web ────────────
WEB_PORT=8080                         # exposed port

# ──────────── PHP ────────────
PHP_MEMORY_LIMIT=256M
PHP_UPLOAD_MAX_FILESIZE=10M
PHP_POST_MAX_SIZE=10M
PHP_DATE_TIMEZONE=Asia/Bangkok

# ──────────── Nginx ────────────
NGINX_CLIENT_MAX_BODY_SIZE=10M

# ──────────── Analytics ────────────
ANALYTICS_PROVIDER=umami              # none | umami | ga4
UMAMI_SCRIPT_URL=https://cloud.umami.is/script.js
UMAMI_WEBSITE_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
# GA4_MEASUREMENT_ID=G-XXXXXXXXXX     # ถ้าใช้ GA4
```

---

## 6. หลัง Deploy

### Smoke Test

```bash
# 1. Landing page
curl -i https://api.example.com/

# 2. Health check API
curl -i "https://api.example.com/api/health-calculator/?type=bmi&weight=70&height=175"

# 3. Static asset
curl -I https://api.example.com/assets/css/...

# 4. QR (external dependency)
curl -i "https://api.example.com/api/qr-code-generator/?type=text&text=DeployOk" -o check.png
file check.png

# 5. CORS preflight
curl -X OPTIONS https://api.example.com/api/health-calculator/ -i
```

### Monitor

```bash
# Containers
docker compose ps
docker stats --no-stream

# Logs
docker compose logs --tail=200 -f

# Disk
df -h

# Memory
free -m
```

---

## 7. Update Strategy

### Patch Update (e.g., 2.5.0 → 2.5.1)
- No downtime — rolling update
- `docker compose pull && docker compose up -d`
- Verify after update

### Minor Update (e.g., 2.5 → 2.6)
- Brief downtime (~1 min) — ถ้าต้องแก้ env var
- หรือ rolling restart

### Major Update (e.g., 2.x → 3.x)
- Breaking change → **อ่าน `RELEASE.md` ก่อน**
- Plan rollback
- Deploy ใน staging ก่อน
- Schedule maintenance window

### Rollback
```bash
# Pin previous version
git checkout v<previous-version>

# Restart
docker compose up -d --build

# Verify
curl https://api.example.com/
```

---

## 8. Zero-Downtime Deploy (แนะนำ)

ใช้ 2 stacks + load balancer:

```bash
# Stack A (running)
docker compose --project-name myapis-a up -d

# Stack B (new)
docker compose --project-name myapis-b up -d -p 8081

# Health check Stack B
curl http://localhost:8081/

# Switch LB → 8081
$EDITOR /etc/nginx/conf.d/load-balance.conf
#   upstream myapis { server 127.0.0.1:8080; server 127.0.0.1:8081; }

# Drain Stack A
docker compose --project-name myapis-a down
```

---

## 9. Rollback Checklist

ถ้า deploy พัง:

1. **Acknowledge** — ตั้ง status page (ถ้ามี)
2. **Rollback** — `git checkout <previous-tag> && docker compose up -d --build`
3. **Verify** — `curl https://api.example.com/`
4. **Post-mortem** — เขียนใน `RELEASE.md` + Issue
5. **Communicate** — แจ้ง stakeholders
