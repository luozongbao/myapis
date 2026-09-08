# 🔳 QR Code Generator API

Generate QR codes for text, URLs, vCards, calendar events, Wi-Fi credentials,
and phone numbers, rendered through the [goQR.me](https://goqr.me/) service.
Part of the [MyAPIs](../../README.md) project — this directory contains the
API endpoint and the payload builder only. The web UI and the full API
documentation live in `public/`.

| What | Where |
|---|---|
| API endpoint | `GET/POST /api/qr-code-generator/` → [`index.php`](index.php) |
| Payload / QR builder | [`QrCodeGenerator.php`](QrCodeGenerator.php) |
| Web UI | [`public/tools/qr-code-generator.php`](../../public/tools/qr-code-generator.php) |
| Full API spec & error docs | [`public/api-specs/qr-code-generator.php`](../../public/api-specs/qr-code-generator.php) |

## 🚀 Quick Start

```bash
# Set BASE_URL to where you host the API:
#   local Docker default → http://localhost:8080
#   production           → https://<your-domain>
BASE_URL="http://localhost:8080"

# URL → JSON response with a base64 data-URL (default output is JSON)
curl "$BASE_URL/api/qr-code-generator/?type=url&url=https://example.com&format=json&size=400"

# Text → direct PNG image (use format=image)
curl "$BASE_URL/api/qr-code-generator/?type=text&text=Hello%20World&format=image" \
  --output qr.png

# Wi-Fi → direct SVG download (format=image + file_type=svg)
curl "$BASE_URL/api/qr-code-generator/?type=wifi&ssid=CafeWiFi&password=beans2024&encryption=WPA&format=image&file_type=svg" \
  --output wifi.svg
```

Two parameters control the output — don't confuse them:

| Name | Default | Values | Meaning |
|---|---|---|---|
| `format` | `json` | `image`, `png`, `svg` → image bytes; `json`, `data` → JSON envelope | **Response mode** |
| `file_type` | `png` | `png`, `gif`, `jpeg`, `jpg`, `svg`, `eps` | **Image format** used for the QR |

## ⚙️ Parameters (summary)

| Name | Type | Default | Description |
|---|---|---|---|
| `type` | string | `text` | `text`, `url`, `vcard`, `event`, `wifi`, `phone` |
| `size` | int | `300` | QR size in px (10–1000, square) |
| `ecc` | string | `M` | Error correction: `L`, `M`, `Q`, `H` |
| `qzone`, `margin` | int | `2`, `1` | Quiet zone / margin |
| `color`, `bgcolor` | string | `0-0-0`, `255-255-255` | `R-G-B` (0–255) or 3/6-char hex |
| `charset-source`, `charset-target` | string | `UTF-8` | Input / output charset |

Required fields depend on `type` (e.g. `url` → `url`; `wifi` → `ssid` +
optional `password`/`encryption`/`hidden`; `vcard` → `first_name`+`last_name`
or `organization`; `event` → `summary`+`start`). Full tables, the vCard/event
field list, and validation errors are in
[`public/api-specs/qr-code-generator.php`](../../public/api-specs/qr-code-generator.php).

## 📦 Example Response (`format=json`)

```json
{
  "success": true,
  "message": "QR code generated successfully",
  "type": "text",
  "payload": "Hello",
  "qr_url": "data:image/png;base64,iVBORw0KGgo…",
  "goqr_url": "https://api.qrserver.com/v1/create-qr-code/?data=Hello&size=150x150&ecc=M&format=png…",
  "file_type": "png",
  "params": {
    "size": 150, "ecc": "M", "format": "png", "qzone": 2, "margin": 1,
    "charset-source": "UTF-8", "charset-target": "UTF-8",
    "color": "0-0-0", "bgcolor": "255-255-255"
  }
}
```

Direct image responses have no JSON body, and no QR response includes a
`timestamp` field.

## ⏱️ Rate Limit

`30` requests per minute per client. Exceeding it returns `HTTP 429`.

## 📁 Files

```
qr-code-generator/
├── index.php              # API endpoint (no web UI here)
├── QrCodeGenerator.php    # Payload builders + goQR.me URL/fetch logic
└── README.md              # This file
```

---

**Note**: QR images are generated through the third-party [goQR.me](https://goqr.me/)
service, so the deployment must be able to reach `api.qrserver.com` over HTTPS.
