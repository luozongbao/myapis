# 🩺 Health Calculator API

BMI, BMR, daily caloric intake, and daily water intake calculations in metric
or imperial units. Part of the [MyAPIs](../../README.md) project — this
directory contains the API endpoint only. The web UI and the full API
documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET/POST /api/health-calculator/` → [`index.php`](index.php) |
| Web UI | [`public/tools/health-calculator.php`](../../public/tools/health-calculator.php) |
| Full API spec & error docs | [`public/api-specs/health-calculator.php`](../../public/api-specs/health-calculator.php) |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# BMI (metric)
curl -X POST "$BASE_URL/api/health-calculator/" \
  -H "Content-Type: application/json" \
  -d '{"calculator": "bmi", "weight": 70, "height": 175, "unit": "metric"}'

# GET works too (imperial)
curl "$BASE_URL/api/health-calculator/?calculator=bmi&weight=154&height=69&unit=imperial"
```

## ⚙️ Parameters

`calculator` is required and must be one of `bmi`, `bmr`, `intake`, or `water`.

| Calculator | Required parameters |
|---|---|
| `bmi` | `weight`, `height` |
| `bmr` | `weight`, `height`, `age`, `gender`, `activity` |
| `intake` | `weight`, `height`, `age`, `gender`, `activity`, `goal` |
| `water` | `weight`, `age`, `gender`, `activity`, `climate`, `healthCondition` |

- `weight` / `height` — kg/cm for `unit=metric`, lbs/inches for `unit=imperial`
- `unit` — `metric` or `imperial` (default `metric`)
- `gender` — `male` or `female`
- `activity` — `sedentary`, `light`, `moderate`, `active`, `extra`
- `goal` — `maintain`, `lose`, `lose-fast`, `gain`, `gain-fast`
- `climate` — `cold`, `temperate`, `hot`, `very-hot`
- `healthCondition` — `normal`, `fever`, `diarrhea`, `kidney`, `heart`, `pregnancy`, `breastfeeding`

See [`public/api-specs/health-calculator.php`](../../public/api-specs/health-calculator.php)
for the complete parameter tables (BMI categories, activity multipliers, water
intake factors), validation rules, and HTTP error codes.

## 📦 Example Response (BMI)

```json
{
  "success": true,
  "data": {
    "bmi": 22.86,
    "category": "Normal weight",
    "advice": "Great! Maintain your current lifestyle with a balanced diet and regular exercise."
  },
  "calculator": "bmi",
  "timestamp": "2026-09-08 09:41:13"
}
```

## ⏱️ Rate Limit

`60` requests per minute per client. Exceeding it returns `HTTP 429`.

## 📁 Files

```
health-calculator/
├── index.php          # API endpoint (no web UI here)
└── README.md          # This file
```

---

**Disclaimer**: Calculations are estimates based on standard formulas and are
for informational purposes only — not a substitute for professional medical
advice.
