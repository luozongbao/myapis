# 🔐 Password Generator API

Generate secure passwords with configurable length, character sets, and
security options, plus a built-in strength analyzer. Randomness comes from
PHP's `random_int()`. Part of the [MyAPIs](../../README.md) project — this
directory contains the API endpoint only. The web UI and the full API
documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET/POST /api/password-generator/` → [`index.php`](index.php) |
| Web UI | [`public/tools/password-generator.php`](../../public/tools/password-generator.php) |
| Full API spec & error docs | [`public/api-specs/password-generator.php`](../../public/api-specs/password-generator.php) |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# Generate 1 strong password (POST, JSON or form-encoded)
curl -X POST "$BASE_URL/api/password-generator/" \
  -H "Content-Type: application/json" \
  -d '{"min_length": 12, "max_length": 16, "count": 1, "include_symbols": true}'

# GET works too
curl "$BASE_URL/api/password-generator/?min_length=12&max_length=16&include_symbols=true&count=1"

# Analyze an existing password
curl "$BASE_URL/api/password-generator/?action=analyze&password=MyPassword123!"
```

## ⚙️ Parameters

| Name | Type | Default | Description |
|---|---|---|---|
| `min_length` | int | `8` | Minimum length (1–128) |
| `max_length` | int | `16` | Maximum length (≤ 128, ≥ `min_length`) |
| `count` | int | `5` | Number of passwords (1–100) |
| `include_lowercase` | bool | `true` | Include `a–z` |
| `include_uppercase` | bool | `true` | Include `A–Z` |
| `include_numbers` | bool | `true` | Include `0–9` |
| `include_symbols` | bool | `false` | Include `!@#$%^&*()_+-=[]{}|;:,.<>?` |
| `exclude_ambiguous` | bool | `false` | Remove confusing chars (`0O1lI|`) |
| `no_repeated_chars` | bool | `false` | No character appears twice |
| `must_include_each_type` | bool | `true` | At least one char from each selected type |
| `custom_symbols` | string | `""` | Replace the default symbol set |

See [`public/api-specs/password-generator.php`](../../public/api-specs/password-generator.php)
for the full parameter and validation documentation.

## 📦 Example Response

```json
{
  "success": true,
  "data": {
    "passwords": [
      { "password": "4;O:+j4vZ}9OY8j", "length": 15, "strength": "very strong", "score": 7 }
    ],
    "count": 1,
    "options_used": {
      "min_length": 12, "max_length": 16, "count": 1,
      "include_lowercase": true, "include_uppercase": true,
      "include_numbers": true, "include_symbols": true,
      "exclude_ambiguous": false, "no_repeated_chars": false,
      "must_include_each_type": true, "custom_symbols": ""
    }
  },
  "generation_info": {
    "length_range": "12-16 characters",
    "character_types": {
      "lowercase": "included", "uppercase": "included",
      "numbers": "included", "symbols": "included"
    },
    "security_options": {
      "exclude_ambiguous": "disabled",
      "no_repeated_chars": "disabled",
      "must_include_each_type": "enabled"
    }
  },
  "timestamp": "2026-09-08 09:41:12"
}
```

## 💪 Strength Scoring

| Points | Strength |
|---|---|
| ≥ 7 | very strong |
| 5 – 6 | strong |
| 3 – 4 | medium |
| < 3 | weak |

Scoring is `+1` for length ≥ 8, another `+1` for length ≥ 12, `+1` each for
lowercase/uppercase/numbers, and `+2` for symbols. The analyze action also
returns character-type details and improvement tips.

## ⏱️ Rate Limit

`60` requests per minute per client. Exceeding it returns `HTTP 429`.

## 📁 Files

```
password-generator/
├── index.php          # API endpoint (no web UI here)
└── README.md          # This file
```

