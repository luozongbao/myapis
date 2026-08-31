# PromptPay QR Generator API

> **Version**: 1.0 · **Base URL**: `https://<host>/api/promptpay-qr-generator/`
> **Source**: `api/promptpay-qr-generator/index.php`

---

## Overview

สร้าง **EMV QRCPS-compliant QR Code สำหรับ PromptPay** (ระบบชำระเงินของประเทศไทย)
รองรับทั้ง Phone Number, Tax ID, และ E-Wallet ID

> ⚠️ **Disclaimer**: API นี้สร้าง QR Payload ตามมาตรฐานเท่านั้น ไม่ได้ทำธุรกรรมจริง — ต้องใช้แอปธนาคารสแกนเพื่อโอนเงิน

---

## Common

- **Methods**: `GET`, `POST`, `OPTIONS`
- **CORS**: เปิด
- **External**: ใช้ `api.qrserver.com` (goQR.me) สำหรับ render QR image — **ต้องมีอินเทอร์เน็ต**

---

## Endpoint

### `GET/POST /api/promptpay-qr-generator/`

### Request

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `target` | string | ✅ | - | เบอร์โทร / Tax ID / E-Wallet ID (ตัวเลขเท่านั้น) |
| `amount` | float | ❌ | `null` | จำนวนเงิน (THB) — ถ้ามี = dynamic, ถ้าไม่มี = static |
| `size` | int | ❌ | 300 | ขนาด QR (50–1000 px) |
| `format` | string | ❌ | `image` | `image` / `json` / `base64` |

### Target Type Detection (auto)

| Length | Type | Format |
|--------|------|--------|
| ≥ 15 | E-Wallet ID | `000000000000000<id>` (padded to 15) |
| ≥ 13 | Tax ID | `0<id>` (padded to 13) |
| < 13 | Phone Number | `0066<id>` (strip leading `0`, prefix `66`) |

ตัวอักษรที่ไม่ใช่ตัวเลขจะถูก strip ออกอัตโนมัติ

---

## Format `image` (default)

### Response

- **Content-Type**: `image/png`
- **Body**: binary PNG image

```bash
curl -o qr.png "https://example.com/api/promptpay-qr-generator/?target=0812345678&amount=100"
```

---

## Format `json`

### Response (200)

```json
{
  "success": true,
  "message": "QR code generated successfully",
  "payload": "00020101021129370016A000000677010111011300668123456785802TH5303764540510005802TH6304ABCD",
  "qr_url": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  "target": "0812345678",
  "amount": 100,
  "target_type": "phone",
  "qr_size": 300
}
```

---

## Format `base64`

### Response (200)

```json
{
  "success": true,
  "image_base64": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
  "payload": "00020101021129370016A000000677010111011300668123456785802TH5303764540510005802TH6304ABCD",
  "target": "0812345678",
  "amount": 100,
  "size": 300
}
```

---

## Error Responses

| HTTP | Cause |
|------|-------|
| 400 | `target` missing |
| 400 | `format` ไม่ถูกต้อง (ต้องเป็น image/json/base64) |
| 500 | goQR.me API down / network error |
| 500 | CRC calculation error (ส่ง target ที่ผิดพลาด) |

```json
{
  "error": "Missing required parameter: target",
  "message": "Please provide a phone number, tax ID, or e-wallet ID"
}
```

---

## EMV QRCPS Payload Structure

Payload ที่สร้างขึ้นประกอบด้วย TLV (Tag-Length-Value):

| Tag | Name | Value |
|-----|------|-------|
| `00` | Payload Format | `01` (EMV QRCPS Merchant Presented Mode) |
| `01` | POI Method | `11` (Static) / `12` (Dynamic — ถ้ามี amount) |
| `29` | Merchant Information (BOT) | GUID `A000000677010111` + target |
| `58` | Country Code | `TH` |
| `53` | Transaction Currency | `764` (THB) |
| `54` | Transaction Amount | (optional) |
| `63` | CRC | calculated |

GUID `A000000677010111` = PromptPay (BOT = Bank of Thailand)

---

## Reference

- [EMVCo QR Code Specification](https://www.emvco.com/emv-technologies/qrcodes/)
- [PromptPay Standard (BOT)](https://www.bot.or.th/Thai/PaymentSystems/StandardPS/Documents/Thai_QR_Code.pdf)
- [goQR.me API](https://goqr.me/api/doc/create-qr-code/)
