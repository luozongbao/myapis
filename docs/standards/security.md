# 🔒 Security Standards

> Checklist Security สำหรับ MyAPIs — ทุก PR ต้องตรวจ

---

## 1. OWASP Top 10 Coverage

| # | Risk | Mitigation |
|---|------|-----------|
| A01 | Broken Access Control | ไม่มี auth — เป็น public utility |
| A02 | Cryptographic Failures | ใช้ `random_int()`, HTTPS-only in prod |
| A03 | Injection | ไม่มี SQL — ไม่มี shell command |
| A04 | Insecure Design | ดู FRD/NFR + Spec-driven dev |
| A05 | Security Misconfiguration | Docker security headers + .htaccess |
| A06 | Vulnerable Components | PHP 8.2 ล่าสุด, deps น้อย |
| A07 | Auth Failures | N/A (no auth) |
| A08 | Software & Data Integrity | ตรวจ SHA ของ external (goQR.me) — TODO |
| A09 | Logging Failures | PHP error_log + Umami analytics |
| A10 | SSRF | ไม่มี user-controlled URL fetch ที่เป็นอันตราย |

---

## 2. Input Validation Checklist

ทุก input ต้อง:

- [ ] มี type cast ก่อนใช้
- [ ] มี range check (min/max)
- [ ] มี required check ถ้าจำเป็น
- [ ] ถูก sanitize ก่อน echo
- [ ] ถูก log ถ้าเป็น suspicious input

### Sanitization Functions

| Context | Function |
|---------|---------|
| HTML body | `htmlspecialchars($s, ENT_QUOTES, 'UTF-8')` |
| URL | `urlencode($s)` + `filter_var($url, FILTER_VALIDATE_URL)` |
| SQL | N/A (ไม่มี DB) |
| Shell | N/A (ไม่มี shell exec) |
| JSON output | `json_encode($s, JSON_HEX_TAG \| JSON_HEX_APOS \| JSON_HEX_AMP \| JSON_HEX_QUOT)` |
| Filename | `basename($s)` + `preg_match('/^[a-z0-9_-]+$/i', $s)` |

---

## 3. Cryptography Standards

### ✅ ใช้

| Function | ใช้สำหรับ |
|---------|---------|
| `random_int()` | Password, security token |
| `password_hash($pwd, PASSWORD_DEFAULT)` | เก็บ password (ถ้ามี) |
| `password_verify($pwd, $hash)` | ตรวจ password |
| `hash_hmac('sha256', $data, $key)` | Signing |
| `openssl_encrypt()` / `openssl_decrypt()` | Symmetric encryption |

### ❌ ห้ามใช้

| Function | เหตุผล |
|---------|--------|
| `rand()`, `mt_rand()` | Predictable (ยกเว้น fortune-teller — non-security) |
| `md5()`, `sha1()` | Deprecated for security |
| `crc32()` | Collision-prone |
| `mt_srand()` | Seed predictable |
| `uniqid()` | ไม่ unique พอ |
| `serialize()` (เก็บใน file) | Object injection |

---

## 4. Output Encoding

### HTML Body
```php
<?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?>
```

### HTML Attribute
```php
<div data-value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>">
```

### JavaScript String
```php
const data = <?= json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
```

### URL Parameter
```php
<a href="?q=<?= urlencode($query) ?>">
```

---

## 5. HTTP Security Headers

ทุก response จาก Nginx ต้องมี:

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
X-XSS-Protection: 1; mode=block
```

ดู config ที่ [`docker/nginx/default.conf`](../../docker/nginx/default.conf)

### ที่แนะนำเพิ่ม (เมื่อใช้ HTTPS)

```
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cloud.umami.is; ...
Permissions-Policy: geolocation=(), microphone=()
```

---

## 6. CORS Policy

| Method | Origin |
|--------|--------|
| GET | `*` (public read) |
| POST | `*` (public utility) |
| OPTIONS | `*` (preflight) |

> ⚠️ ถ้าเพิ่ม auth ในอนาคก็ต้อง restrict origin

---

## 7. File System

### Read-Only Volumes
ใน Docker:
```yaml
volumes:
  - ./:/var/www/html:ro    # ← ro = read-only
```

> ⚠️ ใน production ต้อง ro — dev เท่านั้นที่ rw

### File Upload
- ❌ ไม่มี file upload (ปัจจุบัน)
- ถ้าจะเพิ่ม: validate extension, size, MIME type, scan antivirus

### Path Traversal
- ✅ `basename()` ใช้ทุกครั้งที่รับ filename
- ✅ Nginx block `^/api/.+/.+\.php$`

---

## 8. Third-Party Risk

### goQR.me (`api.qrserver.com`)
- ⚠️ Payload ที่ส่งไป goQR.me อาจถูกบันทึก
- ❌ ห้ามส่ง PII (Personally Identifiable Information) ผ่าน QR generator
- ✅ ส่งแต่ public data (URL, SSID, event details)

### Analytics (Umami / GA4)
- ⚠️ Analytics script จะ track IP, user-agent, referrer
- ต้องแจ้ง Privacy Policy ที่หน้าเว็บ (TODO)
- ต้องเปิดให้ user opt-out (TODO)

---

## 9. Privacy by Design

MyAPIs เก็บข้อมูลผู้ใช้ = **0** โดย default:

- ❌ ไม่มี user account
- ❌ ไม่มี session
- ❌ ไม่มี cookie
- ❌ ไม่มี DB
- ❌ ไม่ log request body (ยกเว้น error log)

### Exception
- Analytics (opt-in ผ่าน env var)
- External QR (goQR.me เห็น payload)

---

## 10. Dependency Security

- ❌ ไม่ใช้ Composer (ลด attack surface)
- ✅ PHP 8.2 ล่าสุด
- ✅ Nginx 1.27 ล่าสุด
- ✅ Alpine Linux base image (มี security patches)
- 📋 TODO: ตั้ง Dependabot / Renovate ใน GitHub

---

## 11. Secret Management

| สถานที่ | สิ่งที่เก็บ |
|--------|-----------|
| `.env` (Docker) | analytics IDs, app config |
| `public/config.php` (shared hosting) | analytics IDs |
| `docker-compose.yml` | database password (commented-out) |

### กฎ
- ❌ ห้าม commit secrets
- ✅ `.gitignore` มี `.env`, `public/config.php`
- ✅ ใช้ `.env.example` แทน
- ⚠️ ถ้า secret รั่ว → rotate ทันที

---

## 12. Incident Response

ถ้าเจอช่องโหว่:

1. **Stop the bleed** — disable endpoint ที่มีปัญหา
2. **Document** — สร้าง `docs/issues/open/ISSUE-SEC-XXX.md`
3. **Patch** — hotfix branch → review → merge → deploy
4. **Notify** — ถ้าเป็น PII → notify stakeholders
5. **Post-mortem** — เขียนใน `RELEASE.md` (ถ้า public)

---

## 13. Security PR Checklist

PR ที่กระทบ security ต้อง:

- [ ] ผ่าน Security review (Dev อีกคนตรวจ)
- [ ] ไม่เพิ่ม dependency ใหม่ที่ไม่จำเป็น
- [ ] ไม่ลด security level ของ endpoint เดิม
- [ ] เพิ่ม/อัปเดต test
- [ ] อัปเดต security header / .htaccess ถ้าเกี่ยวข้อง
- [ ] อัปเดต Issue ด้วย checklist
