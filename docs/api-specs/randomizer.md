# Randomizer API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/randomizer/`
> **Source**: `api/randomizer/index.php`

---

## Overview

API สำหรับสุ่มค่าต่าง ๆ 4 ประเภท: ตัวเลข, ลูกเต๋า, เหรียญ, ไพ่
ใช้ `random_int()` (CSPRNG) ทุก operation

---

## Common

- **Methods**: `GET`, `POST`, `OPTIONS`
- **Content-Type**: `application/json`
- **CORS**: เปิด
- **RNG**: `random_int()` (CSPRNG)

---

## Endpoint

### `GET/POST /api/randomizer/`

ต้องมี query parameter `type` เพื่อเลือกประเภท:

| Type | คำอธิบาย |
|------|---------|
| `number` (default) | ตัวเลข |
| `dice` | ลูกเต๋า |
| `coin` | เหรียญ |
| `card` | ไพ่ |

---

## 1. Number

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ❌ | `number` | `"number"` |
| `min` | int | ❌ | 1 | ค่าต่ำสุด |
| `max` | int | ❌ | 100 | ค่าสูงสุด |

### Example

```bash
curl "https://example.com/api/randomizer/?type=number&min=1&max=1000"
```

### Response (200)

```json
{
  "success": true,
  "type": "number",
  "result": 742,
  "range": { "min": 1, "max": 1000 },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## 2. Dice

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"dice"` |
| `sides` | int | ❌ | 6 | จำนวนหน้า (2–100) |
| `count` | int | ❌ | 1 | จำนวนลูก (1–10) |

### Example

```bash
curl "https://example.com/api/randomizer/?type=dice&sides=20&count=3"
```

### Response (200)

```json
{
  "success": true,
  "type": "dice",
  "result": [14, 8, 17],
  "total": 39,
  "dice_config": { "sides": 20, "count": 3 },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

> ถ้า `count=1` ผลลัพธ์จะเป็นตัวเลขเดี่ยว (ไม่ใช่ array)

---

## 3. Coin

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"coin"` |
| `count` | int | ❌ | 1 | จำนวนเหรียญ (1–10) |

### Example

```bash
curl "https://example.com/api/randomizer/?type=coin&count=5"
```

### Response (200)

```json
{
  "success": true,
  "type": "coin",
  "result": ["Heads", "Tails", "Heads", "Heads", "Tails"],
  "statistics": { "heads": 3, "tails": 2 },
  "count": 5,
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## 4. Card

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"card"` |
| `count` | int | ❌ | 1 | จำนวนไพ่ (1–52 หรือ 54 ถ้า `with_jokers`) |
| `with_jokers` | bool | ❌ | false | เพิ่ม Joker 2 ใบ |

### Example

```bash
curl "https://example.com/api/randomizer/?type=card&count=5&with_jokers=true"
```

### Response (200)

```json
{
  "success": true,
  "type": "card",
  "result": [
    {
      "rank": "Ace",
      "suit": "Spades",
      "symbol": "♠",
      "display": "Ace of Spades",
      "short": "A♠",
      "color": "black"
    },
    {
      "rank": "Joker",
      "suit": "Red",
      "symbol": "🃏",
      "display": "Red Joker",
      "short": "Red🃏",
      "color": "red"
    }
  ],
  "deck_info": {
    "total_cards": 54,
    "with_jokers": true,
    "cards_drawn": 5
  },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

### Suits

| Suit | Symbol | Color |
|------|--------|-------|
| Hearts | ♥ | red |
| Diamonds | ♦ | red |
| Clubs | ♣ | black |
| Spades | ♠ | black |

---

## Error Responses

| HTTP | Cause |
|------|-------|
| 400 | `min > max` |
| 400 | `sides < 2` หรือ `> 100` |
| 400 | `count` นอกช่วง 1–10 (dice/coin) หรือ 1–54 (card) |
| 400 | `type` ไม่รู้จัก |
| 405 | Method อื่นที่ไม่ใช่ GET/POST/OPTIONS |

```json
{
  "success": false,
  "error": "Invalid random type. Supported types: number, dice, coin, card"
}
```

---

## Notes

- ใช้ `random_int()` (CSPRNG) ทุก operation — ปลอดภัยสำหรับ cryptographic ใช้
- การสับไพ่ใช้ `shuffle()` (ไม่ใช่ CSPRNG) — เพราะไม่กระทบ security และต้องการ permutation ทั้ง deck
- ไม่มี seed — ทุก call เป็น independent
