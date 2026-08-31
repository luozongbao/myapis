# Password Generator API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/password-generator/`
> **Source**: `api/password-generator/index.php`

---

## Overview

สร้างรหัสผ่านแบบ **Cryptographically Secure** ด้วย `random_int()`
พร้อมวิเคราะห์ strength ของรหัสผ่านที่ส่งเข้ามา

---

## Common

- **Methods**: `GET`, `POST`, `OPTIONS`
- **Content-Type**: `application/json`
- **CORS**: เปิด
- **RNG**: `random_int()` (CSPRNG)

---

## Modes

API มี 2 mode ผ่าน query parameter `action`:

| Action | คำอธิบาย |
|--------|---------|
| `generate` (default) | สร้างรหัสผ่านใหม่ |
| `analyze` | วิเคราะห์ strength ของรหัสผ่านที่ส่งมา |

---

## 1. Generate Passwords

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `action` | string | ❌ | `generate` | `"generate"` |
| `min_length` | int | ❌ | 8 | ความยาวขั้นต่ำ (≥ 1) |
| `max_length` | int | ❌ | 16 | ความยาวสูงสุด (≤ 128) |
| `count` | int | ❌ | 5 | จำนวนรหัสผ่าน (1–100) |
| `include_lowercase` | bool | ❌ | true | a–z |
| `include_uppercase` | bool | ❌ | true | A–Z |
| `include_numbers` | bool | ❌ | true | 0–9 |
| `include_symbols` | bool | ❌ | false | `!@#$%^&*()_+-=[]{}\|;:,.<>?` |
| `exclude_ambiguous` | bool | ❌ | false | ตัด `0 O 1 l I | \` ` |
| `no_repeated_chars` | bool | ❌ | false | ไม่ให้ตัวอักษรซ้ำ |
| `must_include_each_type` | bool | ❌ | true | บังคับให้มีอย่างน้อย 1 ตัวจากแต่ละประเภทที่เลือก |
| `custom_symbols` | string | ❌ | `""` | override ชุดสัญลักษณ์เริ่มต้น |

### Example

```bash
curl -X POST "https://example.com/api/password-generator/" \
  -H "Content-Type: application/json" \
  -d '{
    "min_length": 16,
    "max_length": 20,
    "count": 3,
    "include_symbols": true,
    "exclude_ambiguous": true
  }'
```

### Response (200)

```json
{
  "success": true,
  "action": "generate",
  "options": { "min_length": 16, "max_length": 20, "count": 3, "...": "..." },
  "passwords": [
    {
      "password": "T9k!mP2x@RbL8nQa",
      "length": 16,
      "strength": "very strong",
      "score": 8
    },
    {
      "password": "Y4#cFv7&Hj2L9Np",
      "length": 18,
      "strength": "very strong",
      "score": 8
    }
  ],
  "count": 3,
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

### Strength Score

| Score | Strength |
|-------|---------|
| ≥ 7 | very strong |
| 5–6 | strong |
| 3–4 | medium |
| < 3 | weak |

**Score Composition**: length ≥ 8 (1pt) + length ≥ 12 (1pt) + has_lowercase (1pt) + has_uppercase (1pt) + has_numbers (1pt) + has_symbols (2pt)

---

## 2. Analyze Password

### Request

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `action` | string | ✅ | `"analyze"` |
| `password` | string | ✅ | รหัสผ่านที่ต้องการวิเคราะห์ |

### Example

```bash
curl "https://example.com/api/password-generator/?action=analyze&password=Hello123"
```

### Response (200)

```json
{
  "success": true,
  "action": "analyze",
  "analysis": {
    "length": 8,
    "has_lowercase": true,
    "has_uppercase": true,
    "has_numbers": true,
    "has_symbols": false,
    "strength": "medium",
    "score": 4
  },
  "tips": [
    "Consider using 12+ characters for stronger passwords",
    "Include special characters (!@#$%^&*)"
  ],
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## Error Responses

| HTTP | Cause |
|------|-------|
| 400 | `min_length > max_length` |
| 400 | `count` นอกช่วง 1–100 |
| 400 | ไม่ได้เลือก character type ใดเลย |
| 400 | `max_length > 128` |
| 400 | `analyze` mode แต่ไม่ส่ง `password` |

```json
{
  "success": false,
  "error": "VALIDATION_ERROR",
  "message": "Validation failed",
  "messages": [
    "Minimum length cannot be greater than maximum length",
    "Count must be between 1 and 100"
  ]
}
```

---

## Security Notes

- ใช้ `random_int()` ซึ่งเป็น CSPRNG (Cryptographically Secure Pseudo-Random Number Generator) ใน PHP
- **ห้ามใช้ `rand()` หรือ `mt_rand()`** สำหรับ security-sensitive operations
- รหัสผ่านที่สร้างขึ้นไม่ถูกบันทึก/เก็บในเซิร์ฟเวอร์
- หาก `no_repeated_chars=true` และ charset มีขนาดเล็กกว่า length อาจใช้เวลานาน (max 1000 attempts)
