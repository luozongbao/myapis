# Username Generator API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/username-generator/`
> **Source**: `api/username-generator/index.php`

---

## Overview

สร้าง Username จากชุดคำตาม Theme ที่เลือก — รองรับ 9 themes และสามารถผสมหลาย themes เข้าด้วยกันได้

---

## Common

- **Methods**: `GET`, `POST`, `OPTIONS`
- **Content-Type**: `application/json`
- **CORS**: เปิด
- **RNG**: `random_int()` (CSPRNG)

---

## Themes

| Key | คำอธิบาย | ตัวอย่าง Output |
|-----|---------|----------------|
| `Fantasy` | นักรบ, เวทมนตร์, อัศวิน | `EpicMage`, `ShadowHunter` |
| `Professional` | ธุรกิจ, อาชีพ | `SmartCoder`, `BrightAnalyst` |
| `Science and Space` | ดาราศาสตร์, ฟิสิกส์ | `StellarPhoton`, `CosmicVoyager` |
| `Computer Technology` | เทคโนโลยี, programming | `CyberCode`, `NeuralNetwork` |
| `Elements and Chemistry` | ธาตุ, เคมี | `AtomicOxygen`, `IonicHelium` |
| `Things` | สิ่งของ | `TinyNeedle`, `SmoothCrystal` |
| `Body and Health` | ร่างกาย, สุขภาพ | `StrongHeart`, `SwiftBrain` |
| `Nature` | ธรรมชาติ | `WildForest`, `CrystalRiver` |
| `Space and Time` | เวลา, มิติ | `SwiftSecond`, `QuantumMoment` |

---

## Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `themes[]` | array | ❌ | `["Fantasy"]` | เลือก 1+ themes |
| `min_length` | int | ❌ | 6 | ความยาวขั้นต่ำ |
| `max_length` | int | ❌ | 20 | ความยาวสูงสุด |
| `count` | int | ❌ | 5 | จำนวน username (1–100) |
| `separator` | string | ❌ | `""` | ตัวคั่น เช่น `_`, `-`, `.` |
| `style` | string | ❌ | `adjective-noun` | `adjective-noun` หรือ `noun-noun` |
| `use_general_adjectives` | bool | ❌ | true | ใช้ general adjectives (สี/รูปทรง) |
| `include_numbers` | bool | ❌ | false | ต่อท้ายด้วยตัวเลข |
| `include_symbols` | bool | ❌ | false | ใส่สัญลักษณ์ `_`, `-`, `.`, `X`, `Z` |

### Multiple Themes

ส่ง `themes[]=Fantasy&themes[]=Professional` (GET) หรือ `"themes": ["Fantasy", "Professional"]` (POST)

---

## Example

```bash
curl "https://example.com/api/username-generator/?themes[]=Fantasy&themes[]=Professional&count=3&separator=_&style=adjective-noun"
```

### Response (200)

```json
{
  "success": true,
  "usernames": [
    {
      "username": "Epic_Wizard",
      "theme": "Fantasy",
      "style": "adjective-noun",
      "length": 11
    },
    {
      "username": "Sharp_Dev",
      "theme": "Professional",
      "style": "adjective-noun",
      "length": 9
    },
    {
      "username": "Legendary_Knight",
      "theme": "Fantasy",
      "style": "adjective-noun",
      "length": 16
    }
  ],
  "count": 3,
  "options": {
    "themes": ["Fantasy", "Professional"],
    "min_length": 6,
    "max_length": 20,
    "separator": "_",
    "style": "adjective-noun"
  },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## Error Responses

| HTTP | Cause |
|------|-------|
| 400 | `min_length > max_length` |
| 400 | theme ที่ส่งมาไม่มีในระบบ |

```json
{
  "success": false,
  "error": "INVALID_THEME",
  "message": "Theme 'unknown' not found",
  "available_themes": ["Fantasy", "Professional", "..."]
}
```

---

## Notes

- Username **ตัวพิมพ์ใหญ่** ตัวแรกของแต่ละคำ (CamelCase) เป็น default
- ใช้ `random_int()` สำหรับสุ่ม — ไม่มี seed
- หากต้องการ uniqueness ให้ตรวจสอบเองฝั่ง client
