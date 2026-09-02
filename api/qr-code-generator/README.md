# QR Code Generator API & Web Interface

A comprehensive QR code generator that supports a wide range of content types — plain text, URLs, vCards, calendar events, Wi-Fi credentials, and phone numbers — with full access to goQR.me rendering options (size, error correction, colors, formats). Includes both a REST API and a responsive web interface.

## 🎯 Features

### 🔢 QR Code Generation API
- **Multiple content types**: `text`, `url`, `vcard`, `event`, `wifi`, `phone`
- **Smart payload builders**: Automatically produces valid vCard 3.0, iCalendar (vCalendar 2.0) and `WIFI:` strings
- **Full goQR.me parameter support**: `size`, `ecc`, `format`, `qzone`, `margin`, `charset-source`, `charset-target`, `color`, `bgcolor`
- **Multiple output formats**: PNG, GIF, JPEG, SVG, EPS — returned directly or base64-encoded in JSON
- **CORS enabled**: Cross-origin request support
- **Validation**: Clear error messages for invalid types/missing fields
- **Dynamic field support**: Repeating items for vCard (`emails[]`, `phones[]`, `urls[]`, `addresses[]`)

### 🌐 Web Interface
- **Interactive form**: Generate QR codes from a clean, modern UI
- **Live preview**: See the result as soon as you adjust settings
- **Content-type templates**: Quick switch between text, URL, vCard, event, Wi-Fi and phone templates
- **Style controls**: Foreground/background colors, size, error-correction level, margin and quiet zone
- **Download support**: Save QR codes as PNG, SVG or any chosen format
- **Responsive design**: Works perfectly on mobile and desktop
- **Error handling**: User-friendly validation messages

## 📦 Installation

1. Clone or download the project to your web server
2. Ensure PHP is installed (version 7.0 or higher recommended)
3. Install required PHP extensions:
   - cURL extension (for calls to goQR.me)
4. Place the files in your web server's document root or subdirectory
5. Ensure the web server has read permissions for all files

## 🧩 Supported Content Types

| `type`    | Required Fields                                                                                              | Output Payload                     |
|-----------|--------------------------------------------------------------------------------------------------------------|------------------------------------|
| `text`    | `text`                                                                                                       | Raw string                         |
| `url`     | `url`                                                                                                        | `https://...` (auto-prefixed)      |
| `phone`   | `phone`                                                                                                      | `tel:...`                          |
| `wifi`    | `ssid`, optional `password`, `encryption` (`WPA`/`WEP`/`nopass`), `hidden`                                 | `WIFI:T:WPA;S:...;P:...;H:...;`    |
| `vcard`   | `first_name`+`last_name` or `organization`; optional `title`, `note`, contact & address fields               | `BEGIN:VCARD ... END:VCARD`        |
| `event`   | `summary`, `start`, optional `end`, `location`, `description`                                               | `BEGIN:VCALENDAR ... END:VCALENDAR`|

## ⚙️ goQR.me Rendering Parameters

| Parameter        | Type    | Default     | Description                                                                 |
|------------------|---------|-------------|-----------------------------------------------------------------------------|
| `size`           | integer | `300`       | QR image size in pixels (10–1000), used for both width and height           |
| `ecc`            | string  | `M`         | Error-correction level: `L`, `M`, `Q`, `H`                                 |
| `format` / `file_type` | string  | `png`        | Output format: `png`, `gif`, `jpeg`/`jpg`, `svg`, `eps`            |
| `qzone`          | integer | `2`         | Quiet-zone (white border) size in modules (0–100)                           |
| `margin`         | integer | `1`         | Margin around the QR code in modules (0–50)                                 |
| `charset-source` | string  | `UTF-8`     | Charset of the input data (`UTF-8`, `ISO-8859-1`)                           |
| `charset-target` | string  | `UTF-8`     | Charset to encode into the QR code (`UTF-8`, `ISO-8859-1`)                  |
| `color`          | string  | `0-0-0`     | Foreground color (`R-G-B`, each 0–255, or 3/6-char hex)                     |
| `bgcolor`        | string  | `255-255-255` | Background color (`R-G-B` or hex)                                        |

## 🔧 Usage

### Web Interface

1. Open `index.php` in your web browser
2. Pick a content type (`text`, `url`, `wifi`, etc.)
3. Fill in the required fields for that type
4. Optionally adjust size, error-correction level, colors and format
5. Click **Generate QR Code** to preview the image
6. Download the QR code or copy the encoded payload

### API Usage

#### Endpoint
```
GET  /qr-code-generator/api/
POST /qr-code-generator/api/   (application/json or form-urlencoded)
```

#### Common Output Modes

| `format`     | Behaviour                                                              |
|--------------|------------------------------------------------------------------------|
| `image`      | Returns the QR image bytes with the proper `Content-Type`              |
| `png`/`svg`  | Shortcut for `image` with `file_type=png` or `file_type=svg`           |
| `json`       | Returns JSON with a base64 data URL and the original goQR URL          |
| `data`       | Alias of `json`                                                         |

#### Example Requests

**Generate URL QR (JSON response):**
```bash
curl "http://your-domain.com/qr-code-generator/api/?type=url&url=https://example.com&format=json&size=400"
```

**Generate Text QR (direct PNG image):**
```bash
curl "http://your-domain.com/qr-code-generator/api/?type=text&text=Hello%20World&size=300" \
  --output qr.png
```

**Generate SVG QR for print:**
```bash
curl "http://your-domain.com/qr-code-generator/api/?type=url&url=https://example.com&file_type=svg" \
  --output qr.svg
```

**Generate vCard QR:**
```bash
curl -X POST http://your-domain.com/qr-code-generator/api/ \
  -H "Content-Type: application/json" \
  -d '{
    "type": "vcard",
    "first_name": "John",
    "last_name": "Doe",
    "organization": "Acme Co.",
    "work_email": "john.doe@example.com",
    "mobile": "+66800000000",
    "website": "https://example.com",
    "format": "json"
  }'
```

**Generate Wi-Fi QR (network auto-connect):**
```bash
curl "http://your-domain.com/qr-code-generator/api/?type=wifi&ssid=CoffeeShop&password=hello123&encryption=WPA&file_type=png" \
  --output wifi.png
```

**Generate Calendar Event QR:**
```bash
curl -X POST http://your-domain.com/qr-code-generator/api/ \
  -H "Content-Type: application/json" \
  -d '{
    "type": "event",
    "summary": "Team Standup",
    "start": "2026-09-02 09:30",
    "end":   "2026-09-02 10:00",
    "location": "Meeting Room A",
    "format": "json"
  }'
```

**Custom Colors & Size:**
```bash
# Brand-style QR with a colored foreground and light background
curl "http://your-domain.com/qr-code-generator/api/?type=text&text=Scan%20Me&size=500&color=29-78-216&bgcolor=240-249-255&format=json"
```

#### Example JSON Response
```json
{
    "success": true,
    "message": "QR code generated successfully",
    "type": "url",
    "payload": "https://example.com",
    "qr_url": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
    "goqr_url": "https://api.qrserver.com/v1/create-qr-code/?data=https%3A%2F%2Fexample.com&...",
    "file_type": "png",
    "params": {
        "size": 400,
        "ecc": "M",
        "format": "png",
        "qzone": 2,
        "margin": 1,
        "charset-source": "UTF-8",
        "charset-target": "UTF-8",
        "color": "0-0-0",
        "bgcolor": "255-255-255"
    }
}
```

## 🧱 Project Structure

```
qr-code-generator/
├── index.php          # Web interface
└── README.md          # This documentation
```

## 🛡️ Error Handling

The API returns standard HTTP status codes with a JSON body describing the failure:

| Status | Meaning                                                       |
|--------|---------------------------------------------------------------|
| `400`  | Invalid `type`, missing required field, or invalid `format`   |
| `500`  | Failed to fetch the QR image from goQR.me                     |

Example:
```json
{
  "error": "Invalid format parameter",
  "message": "Supported formats: image, json, svg"
}
```

## 💡 Use Cases

- **Marketing materials**: Custom-colored QR codes that match your brand
- **Business cards**: Encode vCard data so contacts save instantly
- **Wi-Fi sharing**: Print a Wi-Fi QR on a sign so guests can tap-to-connect
- **Event promotion**: Generate calendar event QRs for flyers or posters
- **Print-ready assets**: Use SVG output for crisp, scalable print graphics

## 🔗 Integration

### Embed in Website (image)
```html
<img src="https://your-domain.com/qr-code-generator/api/?type=url&url=https%3A%2F%2Fexample.com&size=200"
     alt="QR code" />
```

### Programmatic Generation (PHP)
```php
<?php
$url = 'https://your-domain.com/qr-code-generator/api/'
     . '?type=url&url=https%3A%2F%2Fexample.com&format=json';
$json = json_decode(file_get_contents($url), true);
echo '<img src="' . htmlspecialchars($json['qr_url']) . '" alt="QR code" />';
```

### JavaScript / Fetch
```javascript
fetch('/qr-code-generator/api/?type=text&text=Hello&format=json')
  .then(r => r.json())
  .then(data => {
    document.querySelector('#qr').src = data.qr_url;
  });
```

---

**Note**: QR images are generated through the third-party [goQR.me](https://goqr.me/api/) service. Make sure your deployment can reach `api.qrserver.com` over HTTPS.
