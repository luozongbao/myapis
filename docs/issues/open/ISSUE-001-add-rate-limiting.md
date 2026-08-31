# ISSUE-001: Add Rate Limiting to API Endpoints

> **Type**: feature / security
> **Priority**: P1 - High
> **MoSCoW**: Should
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบัน MyAPIs ไม่มี rate limiting ทำให้ client เดียวสามารถ call API ได้ไม่จำกัด → เสี่ยงต่อ:
- Abuse (ส่ง request นับแสงครั้ง)
- Cost — QR API (goQR.me) มี rate limit ที่ 30 req/min ฟรี
- DOS บน PHP-FPM

## 👤 User Story

As a site owner,
I want ทุก API endpoint มี rate limiting,
So that ป้องกัน abuse และ protect downstream services.

## 📦 Scope

### In Scope
- ✅ Implement rate limiting ที่ application layer (PHP) — ง่าย ไม่ depend infra
- ✅ 100 requests/minute per IP (default)
- ✅ 429 response พร้อม `Retry-After` header
- ✅ Headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- ✅ Configurable ผ่าน `.env`: `RATE_LIMIT_PER_MINUTE=100`
- ✅ Skip rate limit สำหรับ health check endpoint

### Out of Scope
- ❌ IP-based whitelist (อาจเพิ่มทีหลัง)
- ❌ User-based limiting (no auth)
- ❌ Persistence (ถ้า restart counter หาย OK)
- ❌ Distributed rate limiting (Redis)

## ✅ Acceptance Criteria

- [ ] Request เกิน limit → HTTP 429 + `Retry-After` header
- [ ] Response แต่ละ request มี rate limit headers
- [ ] Counter reset ทุก 1 minute (sliding window OK)
- [ ] Configurable ผ่าน env var
- [ ] ไม่กระทบ performance (overhead < 5ms)
- [ ] Test ทุก endpoint — ทั้ง GET + OPTIONS
- [ ] Test cross-request (เกิน limit, reset, success อีกครั้ง)

## 🔧 Technical Approach

### Option A: Application Layer (แนะนำ)

```php
// api/_includes/RateLimiter.php (ไฟล์ใหม่)
class RateLimiter
{
    public function check(string $clientId): bool
    {
        // ใช้ file-based cache หรือ APCu
        $key = "rate_limit:{$clientId}";
        $count = apcu_inc($key, 1, $ttl = 60);

        if ($count === 1 || $count === false) {
            apcu_store($key, 1, $ttl);
            $count = 1;
        }

        if ($count > $this->limit) {
            throw new RateLimitException("Rate limit exceeded");
        }

        return $count;
    }
}
```

ใช้ storage:
- **APCu** (แนะนำ — เร็วมาก, in-memory)
- **File cache** (fallback — ช้ากว่า แต่ไม่ต้องลง extension)
- **Redis** (overkill สำหรับ use case นี้)

### Option B: Nginx Layer (ไม่แนะนำ)
ใช้ `limit_req` + `limit_req_zone` — แต่ shared hosting (Apache) ใช้ไม่ได้

### ตำแหน่ง Apply

ตั้งใน `api/<tool>/index.php` ทุกไฟล์:
```php
require_once __DIR__ . '/../_includes/RateLimiter.php';

try {
    RateLimiter::check($_SERVER['REMOTE_ADDR']);
} catch (RateLimitException $e) {
    http_response_code(429);
    header('Retry-After: 60');
    echo json_encode(['success' => false, 'error' => 'RATE_LIMIT_EXCEEDED', 'message' => $e->getMessage()]);
    exit;
}
```

**หรือ** ทำ Shared `api/_includes/bootstrap.php` — include ทุก API ผ่าน Nginx config

## 📋 Tasks

### Analysis (SA)
- [ ] Update `docs/api-specs/RATE_LIMIT_HEADERS.md` (หรือ add section ทุก spec)

### Implementation (Dev)
- [ ] สร้าง `api/_includes/RateLimiter.php`
- [ ] (Optional) สร้าง `api/_includes/bootstrap.php`
- [ ] ปรับ `docker/nginx/default.conf` (ถ้าใช้ Nginx layer)
- [ ] ปรับ `api/<tool>/index.php` ทุกไฟล์ (7 ไฟล์)
- [ ] เพิ่ม `RATE_LIMIT_PER_MINUTE` ใน `example.env`
- [ ] ปรับ `docker/php/php.ini.tpl` ถ้าต้อง enable apcu
- [ ] ปรับ `Dockerfile` เพิ่ม `apcu` extension

### Docs (Dev/SA)
- [ ] Update `docs/api-specs/*.md` — เพิ่ม Rate Limit section
- [ ] Update `docs/runbooks/troubleshooting.md`
- [ ] Update `README.md` — ใหม่ env var

### QA (QA)
- [ ] Test ทุก endpoint
- [ ] Test burst (>100 req/min)
- [ ] Test config (เปลี่ยน env var)
- [ ] Regression test ทุกอย่างยัง work

### Review
- [ ] SA review (security & API design)
- [ ] DevOps review (Docker layer)
- [ ] PM approval (breaking change?)
- [ ] Merge

## 🔗 Dependencies

- **Required**: ISSUE-008 (lày tests ก่อน refactor)

## 📝 Notes

- ต้องดูว่า deployment แบบ shared hosting ตั้งค่าได้ไหม (ไม่ใช้ APCu)
- ถ้า APCu ใช้ไม่ได้ ให้ใช้ file-based fallback

## 🔖 Labels

`feature`, `security`, `enhancement`, `api`
