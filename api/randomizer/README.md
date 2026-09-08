# 🎲 Randomizer API

Generate random numbers, dice rolls, coin flips, and card draws. Randomness is
provided by PHP's `random_int()` (cryptographically secure). Part of the
[MyAPIs](../../README.md) project — this directory contains the API endpoint
only. The web UI and the full API documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET/POST /api/randomizer/` → [`index.php`](index.php) |
| Web UI | [`public/tools/randomizer.php`](../../public/tools/randomizer.php) |
| Full API spec & error docs | [`public/api-specs/randomizer.php`](../../public/api-specs/randomizer.php) |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# Random number between 1 and 100
curl "$BASE_URL/api/randomizer/?type=number&min=1&max=100"

# Roll two D20s
curl "$BASE_URL/api/randomizer/?type=dice&sides=20&count=2"

# Flip five coins
curl "$BASE_URL/api/randomizer/?type=coin&count=5"

# Draw 5 cards (with jokers)
curl "$BASE_URL/api/randomizer/?type=card&count=5&with_jokers=true"

# Everything at once
curl "$BASE_URL/api/randomizer/?type=all"
```

## ⚙️ Parameters (summary)

| Name | Used by | Notes |
|---|---|---|
| `type` | all | `number`, `dice`, `coin`, `card`, `all` (required) |
| `min`, `max` | number | Range bounds (must be numeric, `min` ≤ `max`) |
| `sides` | dice | Any integer from 2–100 |
| `count` | dice / coin / card | Number of items to generate |
| `with_jokers` | card | `true` adds the two jokers to the deck |

Every parameter and validation rule is documented in
[`public/api-specs/randomizer.php`](../../public/api-specs/randomizer.php).

## 📦 Example Response

```json
{
  "type": "number",
  "result": 81,
  "range": { "min": 1, "max": 100 },
  "timestamp": "2026-09-08 09:41:12",
  "success": true,
  "api_info": {
    "version": "1.0",
    "endpoint": "/api/randomizer/",
    "supported_types": ["number", "dice", "coin", "card", "all"]
  }
}
```

## ❌ Example Error

```json
{
  "success": false,
  "error": "Minimum value cannot be greater than maximum value",
  "timestamp": "2026-09-08 09:35:44",
  "api_info": {
    "version": "1.0",
    "endpoint": "/api/randomizer/",
    "supported_types": ["number", "dice", "coin", "card", "all"]
  }
}
```

## ⏱️ Rate Limit

`120` requests per minute per client. Exceeding it returns `HTTP 429`.

## 📁 Files

```
randomizer/
├── index.php          # API endpoint (no web UI here)
└── README.md          # This file
```

