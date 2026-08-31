# 📊 Monitoring Runbook

> วิธี monitor + alert ของ MyAPIs

---

## 1. Health Check

### PHP-FPM (Docker)

```bash
# Built-in healthcheck
docker compose ps    # STATUS: Up (healthy)
docker inspect --format '{{json .State.Health.Status }}' myapis-php
```

Config ใน Dockerfile:
```dockerfile
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s \
  CMD php-fpm-healthcheck || exit 1
```

### External Health Endpoint (แนะนำเพิ่ม)

ถ้าต้องการ endpoint ที่ public:

```php
<?php
// public/health.php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'version' => '1.0.0',
    'php' => PHP_VERSION,
    'timestamp' => date('c'),
]);
```

ใช้ได้ทั้ง curl, browser, uptime monitors

---

## 2. Logs

### PHP-FPM Logs (Docker)

```bash
docker compose logs -f php
docker compose logs --tail=500 php > php-error.log
```

ถ้าเก็บใน volume:
```yaml
volumes:
  - php-logs:/var/log/php-fpm
```

### Nginx Logs

```bash
docker compose logs -f nginx
docker compose exec nginx tail -f /var/log/nginx/access.log
docker compose exec nginx tail -f /var/log/nginx/error.log
```

### Application Logs (Custom)

ใน PHP:
```php
error_log('[myapis] Login from IP: ' . $_SERVER['REMOTE_ADDR']);
```

→ จะไปอยู่ใน PHP-FPM error log

---

## 3. Metrics

### Built-in (Docker stats)

```bash
docker stats --no-stream
```

Output:
```
CONTAINER    CPU %    MEM USAGE / LIMIT    NET I/O    BLOCK I/O
myapis-php   0.50%    45 MB / 256 MB       ...
myapis-nginx 0.10%    8 MB / 64 MB        ...
```

### Prometheus (อนาคต - Issue ใน Roadmap)

เพิ่ม `/metrics` endpoint:

```php
<?php
// public/metrics.php
header('Content-Type: text/plain');

echo "# HELP myapis_requests_total Total requests\n";
echo "# TYPE myapis_requests_total counter\n";
echo "myapis_requests_total{service=\"php\",tool=\"health-calculator\"} 1234\n";
```

แล้วใช้ [Prometheus](https://prometheus.io/) + Grafana dashboard

ดู Issue: `ISSUE-XXX-add-prometheus-metrics.md`

---

## 4. Analytics

MyAPIs ใช้ **Umami** หรือ **GA4**:

### Setup Umami

```env
ANALYTICS_PROVIDER=umami
UMAMI_SCRIPT_URL=https://cloud.umami.is/script.js  # หรือ self-host
UMAMI_WEBSITE_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

### Setup GA4

```env
ANALYTICS_PROVIDER=ga4
GA4_MEASUREMENT_ID=G-XXXXXXXXXX
```

### ปิด Analytics
```env
ANALYTICS_PROVIDER=none
```

### Self-host Umami

ใน `docker-compose.yml`:
```yaml
myapis-umami:
  image: ghcr.io/umami-software/umami:postgresql-latest
  ports:
    - "3000:3000"
  environment:
    DATABASE_URL: postgresql://umami:umami@myapis-umami-db:5432/umami
  depends_on:
    - myapis-umami-db

myapis-umami-db:
  image: postgres:16-alpine
  environment:
    POSTGRES_DB: umami
    POSTGRES_USER: umami
    POSTGRES_PASSWORD: umami
  volumes:
    - umami-data:/var/lib/postgresql/data
```

---

## 5. Uptime Monitoring

### External Services

| Service | Free Tier | Notes |
|---------|-----------|-------|
| [UptimeRobot](https://uptimerobot.com/) | 50 checks | Simple |
| [BetterStack](https://betterstack.com/) | 10 checks | Modern UI |
| [Pingdom](https://www.pingdom.com/) | 1 check | Premium |
| [Healthchecks.io](https://healthchecks.io/) | 20 checks | Self-hosted option |

### ตั้งค่า

Monitor `https://<domain>/health.php` (หรือ landing page) ทุก 5 นาที:
- Method: `GET`
- Expected: 200 + body contain `ok`
- Timeout: 10s
- Alert: email + Slack

---

## 6. Alerting

### Critical (ตอบ ≤ 15 นาที)
- Service down (HTTP 5xx ≥ 5 min)
- Disk usage > 90%
- Memory > 90%
- SSL cert expire ≤ 14 days

### Warning (ตอบ ≤ 4 ชม.)
- Slow response (p95 > 1s) ≥ 10 min
- Disk > 80%
- Error rate spike (≥ 2x baseline)

### Info (ไม่ต้องตอบ)
- Deploy finished
- Daily summary

---

## 7. Backup & Disaster Recovery

### Backup
- ❌ **ไม่มี state** → ไม่ต้อง backup runtime data
- ✅ Backup `docker-compose.yml`, `.env`, `docs/`, `prompts/` (ทั้งหมดอยู่ใน git)
- ✅ Backup Umami database (ถ้า self-host):
  ```bash
  docker exec myapis-umami-db pg_dump umami > umami-$(date +%F).sql
  ```

### Disaster Recovery
- **RPO** = 0 (no user data to lose)
- **RTO** = ~5 นาที (deploy ใหม่ + restart)

ดูเพิ่มที่ [`deployment.md`](deployment.md#9-rollback-checklist)

---

## 8. Dashboard (แนะนำ)

### Grafana + Prometheus (เมื่อมี `/metrics`)

Dashboard panels:
- Request rate (per tool)
- Response time (p50, p95, p99)
- Error rate
- Memory / CPU usage
- Disk usage
- QR call success rate (goQR.me availability)

### Umami Dashboard
- Page views (per tool)
- Top referrers
- Geographic location
- Device type
