# 🔮 Fortune Teller API

Random fortune predictions available in Thai (ไทย), Chinese (中文), and English.
Part of the [MyAPIs](../../README.md) project — this directory contains the API
endpoint and the prediction data files only. The web UI and the full API
documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET /api/fortune-teller/` → [`index.php`](index.php) |
| Web UI | [`public/tools/fortune-teller.php`](../../public/tools/fortune-teller.php) |
| Full API spec & error docs | [`public/api-specs/fortune-teller.php`](../../public/api-specs/fortune-teller.php) |
| Prediction data | [`predictions/`](predictions/) — `1.json` … `52.json` |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# Random fortune (52 predictions, picked with random_int)
curl "$BASE_URL/api/fortune-teller/"

# A specific prediction
curl "$BASE_URL/api/fortune-teller/?id=1"
```

There is **no language parameter** — every response always contains the fortune
in all three languages (`thai`, `chinese`, `english`).

## ⚙️ Parameters

| Name | Type | Default | Description |
|---|---|---|---|
| `id` | int | — (random) | Pick a specific fortune (`1` – `52`). Omit for a random one. |

See [`public/api-specs/fortune-teller.php`](../../public/api-specs/fortune-teller.php)
for the complete parameter table, HTTP error codes, and rate-limit details.

## 📦 Example Response

```json
{
  "success": true,
  "fortune": {
    "id": 1,
    "thai": "วันนี้เป็นวันที่ดีสำหรับการเริ่มต้นใหม่ โชคลาภจะมาหาคุณในเรื่องเงินทอง…",
    "chinese": "今天是新开始的好日子，财运亨通…",
    "english": "Today is a good day for new beginnings…"
  },
  "timestamp": "2026-09-08 09:41:12",
  "total_fortunes": 52
}
```

## ⏱️ Rate Limit

`120` requests per minute per client (see the rate-limit table in the root
[`README.md`](../../README.md)). Exceeding it returns `HTTP 429`.

## 📁 Files

```
fortune-teller/
├── index.php          # API endpoint (no web UI here)
├── predictions/       # Fortune data: 1.json … 52.json
└── README.md          # This file
```

---

**Note**: Fortune content is for entertainment purposes only. All predictions
are original content; none should be used as the basis for important life
decisions.
