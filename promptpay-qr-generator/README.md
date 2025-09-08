# PromptPay QR Code Generator

A comprehensive PromptPay QR code generator with both a REST API and web interface. Generate QR codes for Thailand's PromptPay payment system using phone numbers, tax IDs, or e-wallet IDs.

## Features

### 🎯 PromptPay API
- **Multiple target types**: Phone numbers, tax IDs, and e-wallet IDs
- **Amount support**: Optional payment amounts
- **QR code generation**: High-quality QR codes with customizable sizes
- **EMV QR standard**: Compliant with PromptPay specifications
- **Multiple output formats**: JSON, direct image, and data URLs
- **CORS enabled**: Cross-origin request support
- **Validation**: Input sanitization and format checking

### 🌐 Web Interface
- **User-friendly form**: Simple input for target and amount
- **Real-time generation**: Instant QR code creation
- **Download support**: Save QR codes as PNG images
- **Responsive design**: Works on all devices
- **Error handling**: Clear error messages
- **Preview**: Display generated QR codes with payload information

## Installation

1. Clone or download the project to your web server
2. Ensure PHP is installed (version 7.0 or higher recommended)
3. Install required PHP extensions:
   - GD extension (for QR code image generation)
   - cURL extension (for API calls)
4. Place the files in your web server's document root or subdirectory
5. Ensure the web server has read/write permissions

## Usage

### Web Interface

1. Open `index.php` in your web browser
2. Enter the target (phone number, tax ID, or e-wallet ID)
3. Optionally enter an amount
4. Click "Generate QR Code"
5. Download or scan the generated QR code

### API Usage

#### Endpoint
```
POST /api/
GET /api/
```

#### Parameters
- `target` (required): Phone number (13 digits), Tax ID (13 digits), or e-wallet ID (15+ digits)
- `amount` (optional): Payment amount in Thai Baht
- `size` (optional): QR code size in pixels (default: 300)
- `format` (optional): Output format - "json", "png", or "data" (default: "png")

#### Target Format Examples
- **Phone Number**: `0812345678` or `66812345678`
- **Tax ID**: `1234567890123`
- **e-Wallet ID**: `123456789012345`

#### Example Requests

**Generate QR for phone number with amount:**
```bash
curl -X POST http://your-domain.com/promptpay-qr-generator/api/ \
  -d "target=0812345678&amount=100&size=400&format=json"
```

**Generate QR for tax ID (GET request):**
```bash
curl "http://your-domain.com/promptpay-qr-generator/api/?target=1234567890123&format=json"
```

**Get direct PNG image:**
```bash
curl "http://your-domain.com/promptpay-qr-generator/api/?target=0812345678&amount=50" \
  --output promptpay-qr.png
```

#### Example JSON Response
```json
{
    "success": true,
    "message": "QR code generated successfully",
    "payload": "00020101021129370016A000000677010111011300812345678520454995303764540310063040D6A",
    "qr_url": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
    "target": "0812345678",
    "amount": "100",
    "target_type": "phone",
    "qr_size": 300
}
```

## Target Types

| Type | Length | Format | Example |
|------|--------|--------|---------|
| Phone Number | 10-13 digits | Thai mobile number | `0812345678` |
| Tax ID | 13 digits | Thai tax identification | `1234567890123` |
| e-Wallet ID | 15+ digits | Digital wallet identifier | `123456789012345` |

## Output Formats

### JSON Response (`format=json`)
Returns detailed information including base64-encoded QR code image.

### PNG Image (`format=png` or default)
Returns the QR code as a direct PNG image download.

### Data URL (`format=data`)
Returns a data URL that can be used directly in HTML img tags.

## File Structure

```
promptpay-qr-generator/
├── index.php          # Web interface
├── api/
│   └── index.php      # REST API endpoint
└── README.md          # This file
```

## QR Code Specifications

- **Standard**: EMV QR Code Specification for Payment Systems
- **Format**: PNG image
- **Default size**: 300x300 pixels
- **Encoding**: UTF-8
- **Error correction**: Medium level

## API Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad Request (invalid target format or parameters) |
| 500 | Internal Server Error |

## Requirements

- PHP 7.0 or higher
- GD extension (for image generation)
- cURL extension (for web interface API calls)
- Web server (Apache, Nginx, etc.)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Security Features

- Input validation and sanitization
- Target format verification
- Amount range checking
- XSS protection
- CORS headers configuration

## PromptPay Integration

This generator creates QR codes compatible with:
- **Thai banking apps**: SCB Easy, Krungthai NEXT, Bangkok Bank Mobile Banking
- **e-Wallet apps**: TrueMoney Wallet, Rabbit LINE Pay, ShopeePay
- **Point-of-sale systems**: Supporting PromptPay standard
- **Mobile payment apps**: Any app supporting EMV QR standards

## Usage Examples

### Personal Payments
```bash
# Generate QR for receiving payment to your phone number
curl "http://your-domain.com/promptpay-qr-generator/api/?target=0812345678&amount=500"
```

### Business Payments
```bash
# Generate QR for business tax ID
curl "http://your-domain.com/promptpay-qr-generator/api/?target=1234567890123&format=json"
```

### Dynamic Amounts
```bash
# Generate QR without amount (customer enters amount)
curl "http://your-domain.com/promptpay-qr-generator/api/?target=0812345678"
```

## Legal Compliance

- Compliant with Bank of Thailand PromptPay specifications
- Follows EMV QR Code standards
- Supports Thai Baht currency only
- Valid for use within Thailand's payment ecosystem

## Troubleshooting

### Common Issues

1. **Invalid target format**: Ensure phone numbers are 10-13 digits, tax IDs are exactly 13 digits
2. **QR code not readable**: Check if the target format is correct and follows PromptPay standards
3. **Image generation fails**: Verify GD extension is installed and enabled
4. **API connection errors**: Check cURL extension and network connectivity

### Testing QR Codes

Test generated QR codes with:
- Thai banking mobile apps
- PromptPay-compatible e-wallet apps
- QR code readers that support EMV standards

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit issues, feature requests, or pull requests.

## Disclaimer

This tool is for educational and development purposes. Ensure compliance with local banking regulations and PromptPay terms of service when using in production environments.

## Support

For support or questions, please open an issue in the project repository.
