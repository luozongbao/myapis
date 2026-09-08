# 💳 PromptPay QR Generator API

Generate EMV-standard PromptPay QR codes for Thai phone numbers, tax IDs, and
e-wallet IDs, with optional payment amounts. Part of the
[MyAPIs](../../README.md) project — this directory contains the API endpoint
and the PromptPay payload builder only. The web UI and the full API
documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET/POST /api/promptpay-qr-generator/` → [`index.php`](index.php) |
| Payload builder | [`PromptPayAPI.php`](PromptPayAPI.php) |
| Web UI | [`public/tools/promptpay-qr-generator.php`](../../public/tools/promptpay-qr-generator.php) |
| Full API spec & error docs | [`public/api-specs/promptpay-qr-generator.php`](../../public/api-specs/promptpay-qr-generator.php) |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# Direct PNG image (default output)
curl "$BASE_URL/api/promptpay-qr-generator/?target=0812345678&amount=100" \
  --output promptpay-qr.png

# JSON envelope (base64 data-URL inside)
curl "$BASE_URL/api/promptpay-qr-generator/?target=0812345678&amount=100&format=json"

# Tax ID (13 digits), JSON
curl "$BASE_URL/api/promptpay-qr-generator/?target=1234567890123&format=json"

# e-Wallet ID (15+ digits), no amount (customer enters it later)
curl "$BASE_URL/api/promptpay-qr-generator/?target=123456789012345"
```

## ⚙️ Parameters

| Name | Type | Default | Description |
|---|---|---|---|
| `target` | string | — (required) | Phone number (e.g. `0812345678`), tax ID (13 digits), or e-wallet ID (15+ digits). Length decides the target type |
| `amount` | number | — | Optional payment amount in Thai Baht (`.00` is appended to the payload) |
| `size` | int | `300` | QR image size in pixels |
| `format` | string | `image` | `image` (PNG binary), `json` (JSON + data-URL), or `base64` (JSON with raw `image_base64`) |

See [`public/api-specs/promptpay-qr-generator.php`](../../public/api-specs/promptpay-qr-generator.php)
for the target validation rules, amount limits, and error codes.

## 📦 Example Response (`format=json`)

```json
{
  "success": true,
  "message": "QR code generated successfully",
  "payload": "00020101021229370016A000000677010111011300668123456785802TH53037645406100.006304BB8A",
  "qr_url": "data:image/png;base64,iVBORw0KGgo…",
  "target": "0812345678",
  "amount": 100,
  "target_type": "phone",
  "qr_size": 300
}
```

`format=base64` returns the same idea but with an `image_base64` field instead
of `qr_url`. Direct image responses have no timestamp; none of the PromptPay
responses include a `timestamp` field.

## ⏱️ Rate Limit

`30` requests per minute per client. Exceeding it returns `HTTP 429`.

## 📁 Files

```
promptpay-qr-generator/
├── index.php          # API endpoint (no web UI here)
├── PromptPayAPI.php   # EMV payload builder + target validation
└── README.md          # This file
```

---

**Disclaimer**: Thai Baht only, for use within Thailand's PromptPay ecosystem.
Verify generated codes with your banking app before production use.
