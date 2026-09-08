# 🎯 Username Generator API

Generate themed, pronounceable usernames by combining adjectives and nouns from
curated word lists. Part of the [MyAPIs](../../README.md) project — this
directory contains the API endpoint and the theme word lists only. The web UI
and the full API documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET/POST /api/username-generator/` → [`index.php`](index.php) |
| Web UI | [`public/tools/username-generator.php`](../../public/tools/username-generator.php) |
| Full API spec & error docs | [`public/api-specs/username-generator.php`](../../public/api-specs/username-generator.php) |
| Theme word lists | [`wordlists.php`](wordlists.php) |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# List available themes (9 themes with descriptions)
curl "$BASE_URL/api/username-generator/?action=themes"

# Generate 1 fantasy username
curl -X POST "$BASE_URL/api/username-generator/" \
  -H "Content-Type: application/json" \
  -d '{"themes": ["Fantasy"], "count": 1, "min_length": 6, "max_length": 10}'

# GET works too; themes can be comma-separated
curl "$BASE_URL/api/username-generator/?themes=Professional,Things&count=5"

# `theme` (singular) is still accepted as a backward-compatible alias
curl "$BASE_URL/api/username-generator/?theme=Fantasy&count=2"
```

## 🎮 Available Themes

`Fantasy`, `Professional`, `Science and Space`, `Computer Technology`,
`Elements and Chemistry`, `Things`, `Body and Health`, `Nature`, and
`Space and Time`. Fetch the live list with descriptions via
`?action=themes` (see Quick Start).

## ⚙️ Parameters (summary)

| Name | Type | Default | Description |
|---|---|---|---|
| `themes` | string/array | `["Fantasy"]` | One or more theme names |
| `theme` | string | — | Deprecated alias for a single theme |
| `min_length` | int | `6` | Minimum length (1–50) |
| `max_length` | int | `20` | Maximum length (≤ 50, ≥ `min_length`) |
| `count` | int | `10` | Number of usernames (1–50) |
| `include_numbers` | bool | `false` | Append random numbers |
| `include_symbols` | bool | `false` | Append `_`, `-`, or `.` |
| `capitalize` | bool | `true` | Capitalize word parts |
| `avoid_repetition` | bool | `true` | Avoid repeated word combinations |
| `use_all_adjectives` | bool | `false` | Use every theme's adjectives with the selected nouns |
| `use_general_adjectives` | bool | `false` | Add general descriptors (colors, sizes, shapes…) |
| `custom_words` | string | `""` | Comma-separated extra words |

See [`public/api-specs/username-generator.php`](../../public/api-specs/username-generator.php)
for the full parameter and validation documentation.

## 📦 Example Response

```json
{
  "success": true,
  "data": {
    "usernames": ["ToxicHero"],
    "count": 1,
    "options_used": {
      "themes": ["Fantasy"], "min_length": 6, "max_length": 10, "count": 1,
      "include_numbers": false, "include_symbols": false, "capitalize": true,
      "avoid_repetition": true, "use_all_adjectives": false,
      "use_general_adjectives": false, "custom_words": ""
    }
  },
  "generation_info": {
    "themes": ["Fantasy"],
    "theme_count": 1,
    "length_range": "6-10 characters",
    "features": {
      "numbers": "excluded", "symbols": "excluded", "capitalization": "enabled"
    }
  },
  "timestamp": "2026-09-08 09:41:13"
}
```

## ⏱️ Rate Limit

`60` requests per minute per client. Exceeding it returns `HTTP 429`.

## 📁 Files

```
username-generator/
├── index.php          # API endpoint (no web UI here)
├── wordlists.php      # Theme adjectives/nouns data
└── README.md          # This file
```

