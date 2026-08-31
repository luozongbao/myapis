# QR Code Generator API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/qr-code-generator/`
> **Source**: `api/qr-code-generator/index.php`

---

## Overview

สร้าง **QR Code อเนกประสงค์** ที่รองรับ 6 ประเภท content ผ่าน API เดียว
พร้อม parameters ครบทุกตัวที่ goQR.me รองรับ

---

## Common

- **Methods**: `GET`, `POST`, `OPTIONS`
- **CORS**: เปิด
- **External**: ใช้ `api.qrserver.com` (goQR.me)

---

## Content Types

| Type | Required Fields | Output |
|------|----------------|--------|
| `text` | `text` | ข้อความธรรมดา |
| `url` | `url` | URL (auto prepend `https://` ถ้าขาด) |
| `vcard` | `first_name`/`last_name` หรือ `organization` | vCard 3.0 |
| `event` | `summary`, `start` | vCalendar / iCalendar |
| `wifi` | `ssid` | `WIFI:T:WPA;S:...;P:...;H:false;` |
| `phone` | `phone` | `tel:<number>` |

---

## goQR.me Parameters

| Parameter | Type | Default | Range / Values |
|-----------|------|---------|---------------|
| `size` | int | 300 | 10–1000 |
| `ecc` | string | `M` | `L` / `M` / `Q` / `H` (error correction) |
| `color` | string | `000000` | hex (no `#`) |
| `bgcolor` | string | `ffffff` | hex |
| `margin` | int | 4 | px |
| `qzone` | int | 4 | px |
| `format` | string | `png` | `png`/`gif`/`jpeg`/`jpg`/`svg`/`eps` |
| `charset_source` | string | `UTF-8` | `UTF-8` / `ISO-8859-1` |
| `charset_target` | string | `UTF-8` | `UTF-8` / `ISO-8859-1` |

---

## 1. Text

```bash
curl "https://example.com/api/qr-code-generator/?type=text&text=Hello%20World&size=200"
```

### Response
- `Content-Type: image/png` (binary)

---

## 2. URL

```bash
curl "https://example.com/api/qr-code-generator/?type=url&url=example.com&size=200"
```
> ถ้า URL ไม่ขึ้นต้นด้วย `http://` หรือ `https://` จะ auto prepend `https://`

---

## 3. vCard

### Fields

| Field | Required | Notes |
|-------|----------|-------|
| `first_name` | ⚠️* | ต้องมี name หรือ organization |
| `last_name` | ⚠️* | |
| `organization` | ⚠️* | ใช้แทน name ได้ |
| `title` | ❌ | ตำแหน่งงาน |
| `work_email` | ❌ | (legacy field) |
| `home_email` | ❌ | |
| `emails[][type]` / `emails[][value]` | ❌ | dynamic list |
| `work_phone`, `home_phone`, `mobile`, `fax` | ❌ | legacy |
| `phones[][type]` / `phones[][value]` | ❌ | dynamic list |
| `website`, `urls[][value]` | ❌ | |
| `address`, `city`, `region`, `postcode`, `country` | ❌ | legacy single address |
| `addresses[][type]` / `addresses[][street]` / ... | ❌ | dynamic list |
| `note` | ❌ | |

> ⚠️ *ต้องมี (first_name + last_name) หรือ organization อย่างน้อย 1 อย่าง

### Example

```bash
curl "https://example.com/api/qr-code-generator/?type=vcard&first_name=John&last_name=Doe&work_email=john@example.com&mobile=0812345678"
```

### Output (vCard 3.0)
```
BEGIN:VCARD
VERSION:3.0
N:Doe;John;;;
FN:John Doe
EMAIL;TYPE=WORK:john@example.com
TEL;TYPE=CELL,VOICE:0812345678
END:VCARD
```

---

## 4. Event

### Fields

| Field | Required | Notes |
|-------|----------|-------|
| `summary` | ✅ | ชื่องาน |
| `start` | ✅ | start datetime (Y-m-d H:i:s หรือ ISO 8601) |
| `end` | ❌ | default = start + 1h |
| `location` | ❌ | |
| `description` | ❌ | |

### Example

```bash
curl "https://example.com/api/qr-code-generator/?type=event&summary=Meeting&start=2026-09-01%2010:00:00&end=2026-09-01%2011:00:00&location=Office"
```

### Output (iCalendar)
```
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//myapis//QR Code Generator//EN
BEGIN:VEVENT
UID:abc123@myapis.local
DTSTAMP:20260831T030000Z
DTSTART:20260901T100000Z
DTEND:20260901T110000Z
SUMMARY:Meeting
LOCATION:Office
END:VEVENT
END:VCALENDAR
```

---

## 5. WiFi

### Fields

| Field | Required | Default | Values |
|-------|----------|---------|--------|
| `ssid` | ✅ | - | network name |
| `password` | ❌ | `""` | (ไม่ต้องใส่ถ้า encryption=nopass) |
| `encryption` | ❌ | `WPA` | `WPA` / `WEP` / `nopass` |
| `hidden` | ❌ | `false` | boolean |

### Example

```bash
curl "https://example.com/api/qr-code-generator/?type=wifi&ssid=MyWiFi&password=secret123&encryption=WPA"
```

### Output
```
WIFI:T:WPA;S:MyWiFi;P:secret123;H:false;
```

> Special chars (`\`, `;`, `,`, `:`, `"`) จะถูก escape อัตโนมัติ

---

## 6. Phone

```bash
curl "https://example.com/api/qr-code-generator/?type=phone&phone=0812345678"
```

### Output
```
tel:0812345678
```

---

## Error Responses

| HTTP | Cause |
|------|-------|
| 400 | Missing required field |
| 400 | `encryption` ไม่ถูกต้อง |
| 500 | goQR.me API down |

```json
{
  "error": "INVALID_ARGUMENT",
  "message": "Field \"text\" is required for type=text"
}
```

---

## Reference

- [goQR.me API Documentation](https://goqr.me/api/doc/create-qr-code/)
- [vCard 3.0 Spec (RFC 2426)](https://datatracker.ietf.org/doc/html/rfc2426)
- [iCalendar (RFC 5545)](https://datatracker.ietf.org/doc/html/rfc5545)
- [WiFi QR Code Spec](https://github.com/zxing/zxing/wiki/Barcode-Contents#wi-fi-network-config-android-ios-11)
