# 🔌 API Design Guidelines

> มาตรฐานการออกแบบ REST API สำหรับ MyAPIs

---

## 1. URL Structure

### Pattern
```
/api/<tool>/<resource>?<params>
```

### Rules
- ✅ ใช้ `kebab-case` สำหรับ tool name
- ✅ Resource เป็น ไม่ซับซ้อน (ห้าม nested เกิน 2 ระดับ)
- ✅ Query string สำหรับ filter / param
- ❌ ห้ามใช้ verb ใน URL (เช่น `/api/getUser`)
- ❌ ห้ามใช้ file extension ใน URL (`.json`, `.php`)

### ตัวอย่าง
| ✅ Good | ❌ Bad |
|--------|--------|
| `/api/health-calculator/?type=bmi` | `/api/health_calculator/` |
| `/api/password-generator/?action=analyze` | `/api/getPassword/` |
| `/api/qr-code-generator/?type=text&size=300` | `/api/qr/text/300` |

---

## 2. HTTP Methods

| Method | ใช้เมื่อ |
|--------|---------|
| `GET` | ดึงข้อมูล / คำนวณ (idempotent) |
| `POST` | ส่ง payload ใหญ่ / sensitive (ซ่อนใน body) |
| `OPTIONS` | CORS preflight (ตอบ 200/204) |

> MyAPIs ไม่มี `PUT/DELETE/PATCH` เพราะไม่มี state

---

## 3. HTTP Status Codes

| Code | ใช้เมื่อ | Example |
|------|---------|---------|
| `200` | Success | BMI calculated |
| `204` | Success, no body (OPTIONS preflight) | Preflight |
| `400` | Validation error | Missing required param |
| `405` | Method not allowed | PUT /api/... |
| `500` | Internal error | goQR.me down |

ห้ามใช้:
- `404` สำหรับ API (ดีกว่าคือ 400 พร้อม message ชัดเจน)
- `403` สำหรับ API (no auth)
- `301/302` redirect (API ไม่ควร redirect)

---

## 4. Request Parameters

### รับทั้ง GET และ POST
- **GET**: query string — ใช้สำหรับ simple params
- **POST**: JSON body — ใช้สำหรับ complex data

```php
$input = json_decode(file_get_contents('php://input'), true);
$value = $input['key'] ?? $_GET['key'] ?? null;
```

### Naming
- ใช้ `snake_case` เสมอ
- ห้ามตัวย่อที่ไม่ชัดเจน

### Boolean
- รับ `true/false`, `1/0`, `yes/no` (แล้วแต่ client)
- ใช้ `filter_var($x, FILTER_VALIDATE_BOOLEAN)` แปลงเป็น bool

### Optional vs Required
- Required → return 400 ถ้าขาด
- Optional → ใช้ default value

---

## 5. Response Format

### Success
```json
{
  "success": true,
  "type": "bmi",                      // optional, identifies response shape
  "result": {                          // payload หลัก
    "bmi": 22.86,
    "category": "Normal weight",
    "advice": "..."
  },
  "input": { "weight": 70, "height": 175 },  // optional echo back
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

### Error
```json
{
  "success": false,
  "error": "VALIDATION_ERROR",         // short error code
  "message": "Weight is required",     // human-readable
  "details": {                          // optional, structured
    "field": "weight",
    "received": ""
  },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## 6. CORS Headers

ทุก API response ต้องมี:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

และตอบ preflight:

```php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);  // or 204
    exit;
}
```

---

## 7. Content Negotiation

### Input
- รับ `application/json` (POST) หรือ `application/x-www-form-urlencoded` (POST form)
- Fallback: query string (GET)

### Output
- Default: `application/json; charset=UTF-8`
- ยกเว้น:
  - QR APIs: `image/png` (default) — `application/json` เมื่อ `format=json|base64`

---

## 8. Idempotency

- ทุก API **idempotent** ตามปกติ (เพราะเป็น utility)
- ⚠️ ยกเว้น: การ generate random (แต่ละ call ได้ผลต่างกัน — by design)

---

## 9. Versioning

### ปัจจุบัน
- ❌ ไม่มี version ใน URL (URL = `/api/<tool>/`)
- API ต้อง **backward compatible**

### ถ้าจำเป็นต้องเปลี่ยน Breaking Change
- เพิ่ม version ใหม่: `/api/v2/<tool>/`
- คง v1 ไว้อย่างน้อย 6 เดือน
- ประกาศใน [`RELEASE.md`](../../RELEASE.md) + Issue

---

## 10. Rate Limiting (อนาคต)

ปัจจุบันไม่มี แต่ออกแบบให้เพิ่มได้:

- Header: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- HTTP `429 Too Many Requests` เมื่อเกิน

> ดู Issue ที่กำลังจะเปิดเรื่อง Rate Limit

---

## 11. Documentation Requirements

API ใหม่ทุกตัวต้องมี:
- `docs/api-specs/<tool>.md` — markdown source of truth
- `public/api-specs/<tool>.php` — rendered HTML version

ทั้งสองไฟล์ต้อง **ตรงกัน** — ถ้าแก้ไฟล์หนึ่งต้องแก้อีกไฟล์ใน PR เดียวกัน

---

## 12. Tool-Specific Conventions

### QR Codes
- `format=image` (default) → binary image
- `format=json` → JSON + base64 image ใน `qr_url`
- `format=base64` → JSON เฉพาะ base64

### Random Generators (password, username, randomizer)
- `count` parameter อยู่ในช่วง 1–100
- `min_*` ≤ `max_*` (validate)
- Output เป็น array ของ `{value, metadata}`

### Calculator (health-calculator)
- รับ height ทั้ง cm (> 3) และ m (≤ 3) — auto-detect
- Output มี category + advice เสมอ
