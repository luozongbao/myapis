# ⚙️ Non-Functional Requirements (NFR)

> คุณสมบัติที่ระบบ **ต้องมี** นอกเหนือจากฟังก์ชัน — วัดผลได้ เช่น เร็ว ปลอดภัย ใช้งานได้

---

## NFR-001 · Performance

| Metric | Target | วิธีวัด |
|--------|--------|------|
| API Response Time (p95) | < 200 ms | `curl -w "%{time_total}"` |
| Landing Page Load (LCP) | < 1.5 s | Lighthouse |
| First Contentful Paint | < 1.0 s | Lighthouse |
| Time to Interactive | < 2.0 s | Lighthouse |

---

## NFR-002 · Scalability

- **Stateless** — ทุก API ไม่เก็บ state ในเซิร์ฟเวอร์ สามารถ scale แนวนอนได้
- **No Database** — ใช้ static JSON / ไฟล์ในเครื่อง
- **Lightweight** — PHP-FPM + Nginx รับได้ ≥ 100 concurrent requests ที่ 1 vCPU 1 GB RAM

---

## NFR-003 · Security

### Authentication / Authorization
- ❌ **ไม่มี** — เป็น public utility API
- ✅ **CORS เปิด** สำหรับทุก endpoint
- ✅ **Read-only input** — รับแต่ข้อมูลที่ไม่เป็น PII

### Input Validation
- ทุก input ต้อง validate (ตาม FR-007)
- ใช้ `filter_var()` + type casting
- **HTML Escape** ทุก string ที่จะ echo กลับในหน้า HTML
- **SQL Injection** — N/A (ไม่มี DB)
- **Command Injection** — ไม่มีการเรียก shell command

### Cryptography
- ใช้ `random_int()` (CSPRNG) สำหรับ password และ security-sensitive
- **ห้าม** เขียน crypto เอง (เช่น hash, encrypt) — ใช้ `password_hash()`, `hash_hmac()`, `openssl_*`
- PromptPay CRC-16 ใช้ algorithm ตามมาตรฐาน EMV QRCPS (verified)

### HTTPS
- ✅ Production ต้องใช้ HTTPS (reverse proxy: Cloudflare / Caddy / Nginx)
- ❌ HTTP only ได้เฉพาะ local dev

### Security Headers (Nginx)
- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `X-XSS-Protection: 1; mode=block` (legacy)

---

## NFR-004 · Reliability

| Metric | Target |
|--------|--------|
| Uptime | ≥ 99% (non-critical tool) |
| Mean Time To Recovery | < 1 ชั่วโมง |
| Graceful Degradation | API ที่ depend external (QR goQR.me) ต้องแสดง error ที่เข้าใจได้ |

---

## NFR-005 · Maintainability

- **PSR-12** coding style (PHP)
- **Single Responsibility** — 1 tool = 1 PHP class
- **No magic strings** — constants/Config เท่านั้น
- **DocBlock** ทุก public method
- **README per tool** — สรุปการใช้งานสั้น ๆ

---

## NFR-006 · Portability

ต้องรันได้บน:
- ✅ PHP 7.4, 8.0, 8.1, 8.2, 8.3
- ✅ Linux (Ubuntu, Alpine)
- ✅ macOS (local dev)
- ✅ Windows (WSL2 / XAMPP)
- ✅ Shared hosting (Hostinger, cPanel)
- ✅ VPS (Nginx + PHP-FPM)
- ✅ Docker (สแต็กทางการ)

---

## NFR-007 · Observability

- **Logs** — PHP error log ผ่าน stderr (Docker) หรือ syslog (Nginx)
- **Analytics** — Umami หรือ GA4 ตาม config (optional)
- **Health Check** — `php-fpm-healthcheck` ใน Docker stack
- **No APM** — เวอร์ชันปัจจุบันไม่มี APM (Application Performance Monitoring)

---

## NFR-008 · Accessibility (a11y)

Web UI ทุกหน้าต้อง:
- ใช้ semantic HTML (`<header>`, `<nav>`, `<main>`, `<footer>`)
- มี `<label>` กับทุก input
- Contrast ratio ≥ 4.5:1
- Lighthouse Accessibility ≥ 90

---

## NFR-009 · Internationalization (i18n)

- Hard-coded strings ใน PHP ใช้ array `$messages['en']`, `$messages['th']`, `$messages['zh']`
- ไม่ผูกกับ locale ของ server — client เลือกผ่าน `?lang=` หรือ `Accept-Language`
- Default = `en` ถ้าไม่ระบุ

---

## NFR-010 · Dependency Management

- ❌ **ไม่ใช้ Composer** (intentional — เพื่อ portability)
- ❌ **ไม่มี Node.js / npm**
- ใช้ PHP Standard Library เท่านั้น
- External APIs (goQR.me) เป็น optional — ถ้า down ระบบอื่นยังทำงาน

---

## NFR-011 · Compatibility Matrix

| PHP Version | สถานะ |
|------------|------|
| 7.4 | ✅ รองรับ |
| 8.0 | ✅ รองรับ |
| 8.1 | ✅ รองรับ |
| 8.2 | ✅ รองรับ (แนะนำ) |
| 8.3 | ✅ รองรับ |
| 8.4+ | ⚠️ ทดสอบก่อนใช้งานจริง |

| Web Server | สถานะ |
|-----------|------|
| Nginx 1.27 | ✅ (แนะนำ, มี config ใน `docker/`) |
| Apache 2.4 | ✅ (`.htaccess` รองรับ) |
| PHP built-in server | ✅ (dev only) |
| Caddy | ⚠️ ต้องเขียน config เอง |

---

## NFR-012 · Privacy

- **ไม่เก็บ log ของ request** (ยกเว็น PHP error log)
- **ไม่ติดตามผู้ใช้** (เว้นแต่เปิด analytics)
- **ไม่ส่งข้อมูลไป third-party** ยกเว้น:
  - goQR.me สำหรับสร้าง QR (เฉพาะ payload ที่ encode)
  - Analytics provider (ถ้าตั้งค่า)
