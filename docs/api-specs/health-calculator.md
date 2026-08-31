# Health Calculator API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/health-calculator/`
> **Source**: `api/health-calculator/index.php`

---

## Overview

Health Calculator API ให้บริการคำนวณค่าดัชนีสุขภาพ 4 ฟังก์ชัน ได้แก่ BMI, BMR, Daily Calorie Intake, Water Intake
พร้อมคำแนะนำเบื้องต้น ไม่ใช่การวินิจฉัยทางการแพทย์

---

## Common

- **Methods**: `GET`, `POST`, `OPTIONS`
- **Content-Type**: `application/json` (request + response)
- **CORS**: เปิด (`*`)
- **Cache**: ❌ ไม่แนะนำให้ cache (ขึ้นกับ input)

---

## Endpoint

### `GET/POST /api/health-calculator/`

ต้องมี query parameter `type` เพื่อเลือกฟังก์ชัน:

| Type | คำอธิบาย |
|------|---------|
| `bmi` | Body Mass Index |
| `bmr` | Basal Metabolic Rate |
| `daily-intake` | Daily Calorie & Macronutrient Intake |
| `water-intake` | Daily Water Intake |

> ถ้าไม่ระบุ `type` ค่า default = `bmi`

---

## 1. BMI Calculation

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"bmi"` |
| `weight` | float | ✅ | - | น้ำหนัก (kg) |
| `height` | float | ✅ | - | ส่วนสูง (cm หรือ m — ถ้า > 3 ถือว่า cm) |

### Example

```bash
curl "https://example.com/api/health-calculator/?type=bmi&weight=70&height=175"
```

### Response (200)

```json
{
  "success": true,
  "type": "bmi",
  "result": {
    "bmi": 22.86,
    "category": "Normal weight",
    "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise."
  },
  "input": { "weight": 70, "height": 175 },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

### Categories

| BMI | Category |
|-----|---------|
| < 18.5 | Underweight |
| 18.5–24.9 | Normal weight |
| 25–29.9 | Overweight |
| ≥ 30 | Obese |

---

## 2. BMR Calculation

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"bmr"` |
| `weight` | float | ✅ | - | น้ำหนัก (kg) |
| `height` | float | ✅ | - | ส่วนสูง (cm หรือ m) |
| `age` | int | ✅ | - | อายุ (year) |
| `gender` | string | ✅ | - | `"male"` / `"female"` |
| `activity` | string | ❌ | `"sedentary"` | sedentary/light/moderate/active/extra |

### Formula (Mifflin-St Jeor)

```
male:   BMR = (10 × weight) + (6.25 × height) - (5 × age) + 5
female: BMR = (10 × weight) + (6.25 × height) - (5 × age) - 161
```

### Response (200)

```json
{
  "success": true,
  "type": "bmr",
  "result": {
    "bmr": 1700,
    "activity_multiplier": 1.2,
    "daily_calories_maintain": 2040,
    "advice": "Your BMR is 1700 calories per day..."
  },
  "input": { "weight": 70, "height": 175, "age": 30, "gender": "male", "activity": "sedentary" },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## 3. Daily Intake Calculation

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"daily-intake"` |
| `weight` | float | ✅ | - | น้ำหนัก (kg) |
| `height` | float | ✅ | - | ส่วนสูง (cm หรือ m) |
| `age` | int | ✅ | - | อายุ |
| `gender` | string | ✅ | - | `"male"` / `"female"` |
| `activity` | string | ❌ | `"sedentary"` | sedentary/light/moderate/active/extra |
| `goal` | string | ❌ | `"maintain"` | maintain/lose/lose-fast/gain/gain-fast |

### Goals

| Goal | Calorie Adjustment |
|------|-------------------|
| `maintain` | 0 |
| `lose` | -500 (≈ 0.5 kg/week) |
| `lose-fast` | -1000 (≈ 1 kg/week) |
| `gain` | +500 |
| `gain-fast` | +1000 |

### Response (200)

```json
{
  "success": true,
  "type": "daily-intake",
  "result": {
    "calories": 2040,
    "protein": 112,
    "carbs": 281,
    "fat": 57,
    "bmr": 1700,
    "maintenance": 2040,
    "advice": "To maintain your current weight, aim for 2040 calories per day..."
  },
  "input": { ... },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

### Macronutrient Formula

- **Protein**: 1.6 g × weight (kg)  → 4 kcal/g
- **Fat**: 25% ของ total calories → 9 kcal/g
- **Carbs**: residual → 4 kcal/g

---

## 4. Water Intake Calculation

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `type` | string | ✅ | - | `"water-intake"` |
| `weight` | float | ✅ | - | น้ำหนัก (kg) |
| `age` | int | ✅ | - | อายุ |
| `gender` | string | ✅ | - | `"male"` / `"female"` |
| `activity` | string | ❌ | `"sedentary"` | sedentary/light/moderate/active/extra |
| `climate` | string | ❌ | `"temperate"` | cold/temperate/hot/very-hot |
| `health_condition` | string | ❌ | `"normal"` | normal/fever/diarrhea/kidney/heart/pregnancy/breastfeeding |

### Formula (Base)

```
Base = weight × 35 mL/kg
Adjust for: age, gender, activity, climate, health_condition
```

### Response (200)

```json
{
  "success": true,
  "type": "water-intake",
  "result": {
    "total_ml": 2695,
    "from_drinks_ml": 2156,
    "from_food_ml": 539,
    "glasses_250ml": 8.6,
    "advice": "Aim for approximately 2695ml (8.6 glasses) of water daily..."
  },
  "input": { ... },
  "timestamp": "2026-08-31T10:00:00+07:00"
}
```

---

## Error Responses

| HTTP | `error` Code | Cause |
|------|-------------|-------|
| 400 | `INVALID_INPUT` | Missing/invalid parameter |
| 405 | `METHOD_NOT_ALLOWED` | ไม่ใช่ GET/POST/OPTIONS |

```json
{
  "success": false,
  "error": "INVALID_INPUT",
  "message": "Weight is required and must be positive",
  "field": "weight"
}
```

---

## Notes

- สูตร BMR อ้างอิง **Mifflin-St Jeor Equation** (1990)
- สูตร Water Intake อ้างอิง **EFSA Scientific Opinion on Dietary Reference Values for water** (2010)
- Protein recommendation อ้างอิง **International Society of Sports Nutrition** (2017)
- ไม่ใช่การวินิจฉัยทางการแพทย์ ใช้เพื่อการศึกษาเท่านั้น
