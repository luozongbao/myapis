# Deployment Architecture

> แผนภาพ Deployment & Infrastructure สำหรับ MyAPIs

---

## Deployment Topologies

### A. Docker Compose (แนะนำ, production-ready)

```
┌──────────────────────────────────────────────────┐
│                   Docker Host                    │
│                                                  │
│  ┌──────────────────────────────────────────┐    │
│  │  myapis-nginx (nginx:1.27-alpine)        │    │
│  │  - port 8080 → 80                        │    │
│  │  - volumes: ./:/var/www/html:ro          │    │
│  │           ./docker/nginx/default.conf    │    │
│  └──────────────────┬───────────────────────┘    │
│                     │ (FastCGI 9000)             │
│  ┌──────────────────▼───────────────────────┐    │
│  │  myapis-php (php:8.2-fpm-alpine)         │    │
│  │  - WORKDIR /var/www/html                 │    │
│  │  - entrypoint.sh (render php.ini)        │    │
│  │  - healthcheck: php-fpm-healthcheck      │    │
│  └──────────────────────────────────────────┘    │
│                                                  │
│  Network: myapis-net (bridge)                    │
└──────────────────────────────────────────────────┘
```

**Optional** (commented-out):
- `myapis-umami-db` (postgres:16-alpine)
- `myapis-umami` (ghcr.io/umami-software/umami)

ดูไฟล์: [`docker-compose.yml`](../../docker-compose.yml)

---

### B. Shared Hosting (Hostinger / cPanel)

```
┌──────────────────────────────────────────────────┐
│                Shared Host (cPanel)              │
│                                                  │
│  public_html/                                    │
│  ├── .htaccess        (root rewrite)             │
│  ├── api/             (REST endpoints)           │
│  ├── public/          (web UI)                   │
│  │   ├── .htaccess    (Apache config)            │
│  │   ├── config.php   (analytics — manual edit) │
│  │   └── ...                                     │
│  └── docs/  README.md  Dockerfile  ❌ ignored    │
│                                                  │
│  Apache 2.4 + mod_rewrite + PHP 8.1/8.2 (mod_php) │
└──────────────────────────────────────────────────┘
```

ดูรายละเอียดที่ [`README.md`](../../README.md#-shared-hosting-deployment-hostinger--cpanel)

---

### C. VPS / Bare-Metal (Nginx + PHP-FPM)

```
┌──────────────────────────────────────────────────┐
│                  VPS (Ubuntu 22.04)              │
│                                                  │
│  /etc/nginx/sites-enabled/myapis.conf            │
│  /var/www/myapis/                                │
│  ├── api/                                       │
│  ├── public/                                    │
│  └── ...                                        │
│                                                  │
│  PHP 8.2-FPM (systemd service)                   │
│  Nginx (systemd service)                         │
│                                                  │
│  Optional: Certbot (Let's Encrypt)               │
└──────────────────────────────────────────────────┘
```

ดู Nginx config ใน [`README.md`](../../README.md#-nginx-configuration) หรือ [`docker/nginx/default.conf`](../../docker/nginx/default.conf)

---

### D. PHP Built-in Server (Dev Only)

```bash
php -S localhost:8000 -t public/
```

ใช้สำหรับ local development เท่านั้น — **ไม่แนะนำ** production

---

## Infrastructure Components

### Production Checklist

- [x] HTTPS (TLS) — ผ่าน Cloudflare / Caddy / Let's Encrypt
- [x] Security headers — Nginx config
- [x] CORS — application-level
- [x] Body size limit — Nginx `client_max_body_size`
- [x] File permission — `www-data:www-data`
- [x] Health check — `php-fpm-healthcheck` binary
- [x] Restart policy — `unless-stopped`
- [x] Timezone — ผ่าน `TZ` env
- [ ] Rate limiting — **TODO** (ดู Issue ที่กำลังจะเปิด)
- [ ] Monitoring (Prometheus) — **TODO**
- [ ] Log aggregation — **TODO**
- [ ] Backup — ไม่มี state, ไม่ต้อง backup

---

## Network Ports

| Service | Internal Port | External Port | Notes |
|---------|--------------|---------------|-------|
| Nginx | 80 | `${WEB_PORT:-8080}` | HTTP only (TLS ที่ proxy) |
| PHP-FPM | 9000 | - | internal only (FastCGI) |
| Umami (optional) | 3000 | `${UMAMI_PORT:-3000}` | web UI ของ analytics |
| Umami DB | 5432 | - | internal only |

---

## Environment Variables

ดูฉบับเต็มที่ [`example.env`](../../example.env):

### Application
| Var | Default | Notes |
|-----|---------|-------|
| `PROJECT_NAME` | `myapis` | Docker prefix |
| `TZ` | `Asia/Bangkok` | timezone |
| `APP_ENV` | `development` | `production` ปิด error display |

### PHP
| Var | Default | Notes |
|-----|---------|-------|
| `PHP_MEMORY_LIMIT` | `256M` | |
| `PHP_UPLOAD_MAX_FILESIZE` | `10M` | |
| `PHP_POST_MAX_SIZE` | `10M` | |
| `PHP_DATE_TIMEZONE` | `Asia/Bangkok` | |

### Nginx
| Var | Default | Notes |
|-----|---------|-------|
| `NGINX_CLIENT_MAX_BODY_SIZE` | `10M` | |

### Analytics
| Var | Default | Notes |
|-----|---------|-------|
| `ANALYTICS_PROVIDER` | `none` | `none` / `umami` / `ga4` |
| `UMAMI_SCRIPT_URL` | - | full URL เช่น `https://cloud.umami.is/script.js` |
| `UMAMI_WEBSITE_ID` | - | UUID |
| `GA4_MEASUREMENT_ID` | - | `G-XXXXXXXXXX` |

---

## Scaling Strategy

### Vertical Scaling (แนะนำเป็นอันดับแรก)

| Resource | Start | Scale Up |
|----------|-------|----------|
| CPU | 1 vCPU | 2–4 vCPU |
| RAM | 1 GB | 2–4 GB |
| Disk | 10 GB | 20 GB |

### Horizontal Scaling

ใส่ Nginx load balancer ข้างหน้า + หลาย PHP-FPM instances:

```
        Internet
            │
            ▼
   ┌────────────────┐
   │  LB (Nginx/HA) │
   └────┬───────┬───┘
        │       │
        ▼       ▼
   ┌────────┐ ┌────────┐
   │ PHP-1  │ │ PHP-2  │
   └────────┘ └────────┘
```

> ระบบ stateless อยู่แล้ว ขยายได้ทันที แค่ spawn PHP container เพิ่ม

---

## Disaster Recovery

| Scenario | Recovery |
|----------|---------|
| Container crash | Docker auto-restart (`unless-stopped`) |
| Disk full | ลบ old logs, image prune |
| Config wrong | Rollback image tag, redeploy |
| Data loss | **ไม่มี data** — restore คือ git pull + rebuild |

RPO = 0 (no data to lose), RTO = ~5 นาที (rebuild + restart)
