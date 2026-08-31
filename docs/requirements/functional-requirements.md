# 📋 Functional Requirements (FRD)

> Functional Requirements ของ MyAPIs — สิ่งที่ระบบ **ต้องทำ** (What the system must do)

---

## FR-001 · ระบบต้องมี 7 เครื่องมือพื้นฐาน

| ID | ชื่อ | ต้องมีฟังก์ชัน |
|----|------|---------------|
| FR-001-1 | Health Calculator | คำนวณ BMI, BMR, Daily Intake, Water Intake |
| FR-001-2 | Password Generator | สร้างรหัสผ่าน + วิเคราะห์ strength |
| FR-001-3 | Username Generator | สร้าง Username หลาย themes |
| FR-001-4 | PromptPay QR Generator | สร้าง QR PromptPay (THB) |
| FR-001-5 | QR Code Generator | สร้าง QR แบบ text/url/vcard/event/wifi/phone |
| FR-001-6 | Fortune Teller | สุ่มคำทำนายจาก 52 ไฟล์ JSON |
| FR-001-7 | Randomizer | สุ่ม number/dice/coin/card |

---

## FR-002 · ทุกเครื่องมือต้องมี 3 ส่วนครบ

- [x] **REST API Endpoint** ที่ `api/<tool>/index.php` (รับ GET/POST, คืน JSON)
- [x] **Web UI** ที่ `public/<tool>.php` (responsive, ใช้งานได้ทั้ง mobile/desktop)
- [x] **API Specification** ที่ `docs/api-specs/<tool>.md` + `public/api-specs/<tool>.php`

---

## FR-003 · API Response Format ต้องสอดคล้องกัน

ทุก API ต้องคืน JSON ในรูปแบบ:

### Success
```json
{
  "success": true,
  "data": { ... },            // payload หลัก
  "timestamp": "ISO-8601"
}
```

### Error
```json
{
  "success": false,
  "error": "Short error code",
  "message": "Human-readable explanation",
  "details": { ... }          // optional, structured details
}
```

### HTTP Status Code
- `200` — success
- `400` — bad request (validation error)
- `405` — method not allowed
- `500` — internal server error

---

## FR-004 · รองรับ CORS

ทุก API Endpoint ต้อง:
- ตอบ `Access-Control-Allow-Origin: *`
- รองรับ `OPTIONS` preflight
- อนุญาต methods: `GET, POST, OPTIONS`
- อนุญาต headers: `Content-Type, Authorization`

---

## FR-005 · ใช้ Cryptographically Secure RNG

สำหรับฟังก์ชันสุ่ม (password, username, randomizer):
- ใช้ `random_int()` (PHP) เท่านั้น **ห้าม** ใช้ `rand()` หรือ `mt_rand()` สำหรับ security-sensitive
- ยกเว้นฟังก์ชันที่ไม่กระทบ security เช่น fortune-teller (อนุโลมใช้ `rand()` ได้)

---

## FR-006 · รองรับ i18n ตามความเหมาะสม

| Tool | ภาษาที่รองรับ |
|------|-------------|
| Health Calculator | TH, EN |
| Password Generator | EN (labels เท่านั้น) |
| Username Generator | EN (output เป็นภาษาอังกฤษล้วน) |
| PromptPay QR | TH, EN (UI); payload เป็น THB only |
| QR Code Generator | TH, EN |
| Fortune Teller | TH, EN, ZH (multilingual content) |
| Randomizer | EN (labels เท่านั้น) |

---

## FR-007 · Validation ทุก Input

ทุก input ต้องผ่าน validation:
- **Type check** — string/int/float/boolean ตามที่ Spec ระบุ
- **Range check** — min/max ตาม Spec
- **Required check** — ถ้า required และขาด → HTTP 400
- **Sanitization** — strip HTML/script tag สำหรับ string input

ตัวอย่างข้อความ error:
```json
{
  "success": false,
  "error": "VALIDATION_ERROR",
  "message": "Weight must be a positive number",
  "details": { "field": "weight", "received": "-5" }
}
```

---

## FR-008 · Landing Page (public/index.php)

- แสดงการ์ด 7 เครื่องมือ พร้อม:
  - Icon / Emoji
  - ชื่อ
  - คำอธิบายสั้น ๆ
  - ปุ่ม: **Try Tool**, **API**, **API Docs**
- แสดงสถานะระบบ (Active Tools, API Endpoints, Uptime)

---

## FR-009 · API Spec Page

ทุกหน้า `public/api-specs/<tool>.php` ต้องแสดง:
- Endpoint URL (dynamic base URL)
- Methods ที่รองรับ
- Parameters พร้อม type/required/description
- Example Request
- Example Response (success + error)
- Status codes ที่เป็นไปได้

---

## FR-010 · Error Logging

API ทุกตัวต้อง log error ที่เกิดขึ้นในระบบ โดย:
- **Dev mode** (`APP_ENV=development`) — log ลง PHP error log + แสดงใน response (controlled)
- **Production mode** (`APP_ENV=production`) — log ลง PHP error log เท่านั้น ไม่ส่งรายละเอียดกลับ

---

## FR-011 · Multi-format Response (สำหรับ QR Tools)

QR Code Generator และ PromptPay QR Generator ต้องรองรับ `format` parameter:
- `image` (default) — คืน binary image
- `json` — คืน JSON + base64 image
- `base64` — คืน JSON เฉพาะ base64

---

## FR-012 · Health Check Endpoint

API ทั้งหมดต้องตอบ `OPTIONS` ด้วย HTTP 200/204 เพื่อให้ทำ health check ผ่าน CORS preflight ได้

---

## FR-013 · Default Values

ทุก optional parameter ต้องมี default value ที่เหมาะสม เช่น:
- `health-calculator` → `gender=male`, `activity=sedentary`, `goal=maintain`
- `password-generator` → `count=5`, `min_length=8`, `max_length=16`
- `randomizer` → `type=number`, `min=1`, `max=100`

---

## FR-014 · Backward Compatibility

API ที่ออกแบบแล้วเปิดให้บริการ ห้าม:
- เปลี่ยนชื่อ parameter ที่มีอยู่
- เปลี่ยน response structure
- ลบฟิลด์ใน response

หากจำเป็นต้องเปลี่ยน → ต้องเพิ่ม parameter ใหม่หรือเวอร์ชัน API (`/api/v2/<tool>/`) และคงของเดิมไว้อย่างน้อย 6 เดือน
