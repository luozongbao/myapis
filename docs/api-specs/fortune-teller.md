# Fortune Teller API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/fortune-teller/`
> **Source**: `api/fortune-teller/index.php`

---

## Overview

API สุ่มคำทำนายจาก **52 ไฟล์ JSON** ใน `api/fortune-teller/predictions/`
รองรับเนื้อหา 3 ภาษา (TH / EN / ZH)

> 🎭 **Entertainment Only** — ไม่ใช่การพยากรณ์จริง ใช้เพื่อความบันเทิงเท่านั้น

---

## Common

- **Methods**: `GET`, `OPTIONS` (ไม่รับ POST)
- **Content-Type**: `application/json; charset=UTF-8`
- **CORS**: เปิด
- **Cache**: ❌ ไม่ควร cache (เป็นการสุ่ม)

---

## Endpoint

### `GET /api/fortune-teller/`

ไม่มี parameter ใด ๆ — สุ่มคำทำนาย 1 ใบจาก 52 ใบทุกครั้ง

---

## Example

```bash
curl "https://example.com/api/fortune-teller/"
```

### Response (200)

```json
{
  "success": true,
  "fortune": {
    "id": 12,
    "category": "love",
    "content": {
      "th": "ความรักกำลังจะมาถึง เปิดใจรับและอย่ากลัวที่จะรัก",
      "en": "Love is on its way. Open your heart and do not be afraid to love.",
      "zh": "爱情即将到来。敞开心扉，不要害怕去爱。"
    },
    "lucky_numbers": [7, 14, 23],
    "lucky_color": "pink"
  },
  "timestamp": "2026-08-31T10:00:00+07:00",
  "total_fortunes": 52
}
```

---

## Fortune File Structure

แต่ละไฟล์ `api/fortune-teller/predictions/<id>.json` มี schema:

```json
{
  "id": 12,
  "category": "love",
  "content": {
    "th": "...",
    "en": "...",
    "zh": "..."
  },
  "lucky_numbers": [7, 14, 23],
  "lucky_color": "pink"
}
```

### Categories

| Category | ความหมาย |
|----------|---------|
| `love` | ความรัก |
| `career` | การงาน |
| `health` | สุขภาพ |
| `wealth` | การเงิน |
| `general` | ทั่วไป |

---

## Error Responses

| HTTP | Cause |
|------|-------|
| 500 | ไฟล์ `predictions/<id>.json` หาย |

```json
{
  "success": false,
  "error": "Fortune file not found",
  "requested_id": 37
}
```

---

## Adding New Fortunes

1. สร้างไฟล์ `api/fortune-teller/predictions/<next-id>.json`
2. ตรวจสอบว่า `id` ใน JSON ตรงกับชื่อไฟล์
3. เขียน `content` ครบ 3 ภาษา (TH / EN / ZH)
4. เพิ่ม category ที่ใช้ใน `docs/standards/api-design.md` (ถ้าเป็น category ใหม่)
5. อัปเดต `total_fortunes` ใน response (default ตอนนี้คือ hard-coded `52` — ต้องแก้ใน code หากเพิ่ม)

> ⚠️ **TODO (Issue ที่กำลังจะเปิด)**: ทำให้ `total_fortunes` auto-calculate จาก glob ไฟล์

---

## Notes

- ใช้ `rand()` (ไม่ใช่ `random_int()`) เพราะไม่ใช่ security-sensitive
- สุ่มเลข 1–52 ทุกครั้ง (uniform distribution)
- ไม่มี rate limit (เรียกบ่อยแค่ไหนก็ได้ — แต่ไม่แนะนำ)
