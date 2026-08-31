# 🧰 Tool Catalog

> รายการเครื่องมือ/Feature ทั้งหมดของ MyAPIs — เป็น **Single Source of Truth** สำหรับทีม

> **อัปเดตล่าสุด**: 2026-08-31 · **จำนวน**: 7 เครื่องมือ

---

## สารบัญเครื่องมือ

| # | ชื่อ | หมวด | Endpoint | Spec | UI |
|---|------|-----|---------|------|-----|
| 1 | Health Calculator | Utility | `/api/health-calculator/` | [ดู](../api-specs/health-calculator.md) | [`public/health-calculator.php`](../../public/health-calculator.php) |
| 2 | Password Generator | Security | `/api/password-generator/` | [ดู](../api-specs/password-generator.md) | [`public/password-generator.php`](../../public/password-generator.php) |
| 3 | Username Generator | Utility | `/api/username-generator/` | [ดู](../api-specs/username-generator.md) | [`public/username-generator.php`](../../public/username-generator.php) |
| 4 | PromptPay QR Generator | Thailand-Specific | `/api/promptpay-qr-generator/` | [ดู](../api-specs/promptpay-qr-generator.md) | [`public/promptpay-qr-generator.php`](../../public/promptpay-qr-generator.php) |
| 5 | QR Code Generator | Utility | `/api/qr-code-generator/` | [ดู](../api-specs/qr-code-generator.md) | [`public/qr-code-generator.php`](../../public/qr-code-generator.php) |
| 6 | Fortune Teller | Entertainment | `/api/fortune-teller/` | [ดู](../api-specs/fortune-teller.md) | [`public/fortune-teller.php`](../../public/fortune-teller.php) |
| 7 | Randomizer | Utility | `/api/randomizer/` | [ดู](../api-specs/randomizer.md) | [`public/randomizer.php`](../../public/randomizer.php) |

> **กฎ**: ทุกครั้งที่เพิ่ม/ลบเครื่องมือ ต้องอัปเดตตารางนี้ + `README.md` + `public/index.php` ทั้ง 3 ที่ใน PR เดียวกัน

---

## 1. 🏥 Health Calculator

**คำอธิบาย**: คำนวณดัชนีสุขภาพ 4 ฟังก์ชัน ได้แก่ BMI, BMR, Daily Calorie Intake, Water Intake

**Endpoint**: `GET/POST /api/health-calculator/?type={bmi|bmr|daily-intake|water-intake}&...`

**Input หลัก**:
- `weight` (kg), `height` (cm), `age` (year), `gender` (`male`/`female`)
- `activity` (sedentary/light/moderate/active/extra)
- `goal` (maintain/lose/lose-fast/gain/gain-fast) — สำหรับ daily-intake
- `climate`, `healthCondition` — สำหรับ water-intake

**Output หลัก**: JSON พร้อมค่าที่คำนวณได้ + คำแนะนำสั้น ๆ

**Edge Cases**:
- ส่ง height > 3 ถือว่าเป็น cm (auto convert เป็น m)
- ส่ง height ≤ 3 ถือว่าเป็น m (auto convert เป็น cm)
- weight = 0 หรือติดลบ → error

**Spec**: [`docs/api-specs/health-calculator.md`](../api-specs/health-calculator.md)

---

## 2. 🔐 Password Generator

**คำอธิบาย**: สร้างรหัสผ่านแบบ Cryptographically Secure ใช้ `random_int()`

**Endpoint**: `GET/POST /api/password-generator/?action={generate|analyze}&...`

**Modes**:
- `action=generate` (default) — สร้างรหัสผ่านจำนวน N ตัว
- `action=analyze` — วิเคราะห์ strength ของรหัสผ่านที่ส่งมา

**Input หลัก**:
- `min_length`, `max_length`, `count`
- `include_lowercase`, `include_uppercase`, `include_numbers`, `include_symbols`
- `exclude_ambiguous`, `no_repeated_chars`, `must_include_each_type`
- `custom_symbols` (string ที่ override ชุดสัญลักษณ์เริ่มต้น)
- `password` (สำหรับ `analyze`)

**Output หลัก**:
- `generate`: array ของรหัสผ่าน พร้อม strength/score
- `analyze`: `{length, has_*, strength, score, tips[]}`

**Edge Cases**:
- ไม่เลือก character type ใดเลย → error "At least one character type"
- `min_length > max_length` → error
- `count > 100` → error
- `no_repeated_chars=true` + ขนาด charset น้อยกว่า length → อาจ loop 1000 ครั้งแล้ว fail

**Spec**: [`docs/api-specs/password-generator.md`](../api-specs/password-generator.md)

---

## 3. 👤 Username Generator

**คำอธิบาย**: สร้าง Username จากชุดคำตาม Theme (Fantasy, Professional, Science, Tech, Chemistry, Things, Body, Nature, Space)

**Endpoint**: `GET/POST /api/username-generator/?...`

**Themes** (8 ชุด):
- `Fantasy`, `Professional`, `Science and Space`, `Computer Technology`
- `Elements and Chemistry`, `Things`, `Body and Health`, `Nature`, `Space and Time`

**Input หลัก**:
- `themes[]` (array) — เลือกได้หลาย theme
- `min_length`, `max_length`, `count`
- `separator` (`_`/`-`/`.`/etc.)
- `style` (`adjective-noun` / `noun-noun`)
- `use_general_adjectives`, `include_numbers`, `include_symbols`

**Output หลัก**: array ของ `{username, theme, length}`

**Spec**: [`docs/api-specs/username-generator.md`](../api-specs/username-generator.md)

---

## 4. 💳 PromptPay QR Generator

**คำอธิบาย**: สร้าง QR Code ตามมาตรฐาน EMV QRCPS ของ PromptPay (Thailand)

**Endpoint**: `GET/POST /api/promptpay-qr-generator/?target={...}&amount={...}&size={...}&format={image|json|base64}`

**Input หลัก**:
- `target` — เบอร์โทร / Tax ID / E-Wallet ID
- `amount` (optional) — ถ้ามี amount = dynamic, ถ้าไม่มี = static
- `size` — ขนาด QR (50–1000 px, default 300)
- `format` — `image` (default) / `json` / `base64`

**Output หลัก**:
- `image`: binary PNG
- `json`: `{success, payload, qr_url (base64), target_type, qr_size}`
- `base64`: `{success, image_base64, payload, target, amount, size}`

**Target Type Detection** (auto):
- `length >= 15` → eWallet ID
- `length >= 13` → Tax ID
- อื่น ๆ → Phone Number (auto-prepend `66` ถ้าขึ้นต้นด้วย `0`)

**Edge Cases**:
- `target` ว่าง → error
- ตัวอักษรที่ไม่ใช่ตัวเลขถูก strip ออก
- `size` นอกช่วง 50–1000 → fallback 300

**Spec**: [`docs/api-specs/promptpay-qr-generator.md`](../api-specs/promptpay-qr-generator.md)

---

## 5. 📱 QR Code Generator

**คำอธิบาย**: สร้าง QR Code อเนกประสงค์ รองรับ 6 ประเภท content

**Endpoint**: `GET/POST /api/qr-code-generator/?type={text|vcard|event|url|wifi|phone}&...`

**Content Types**:
1. `text` — ข้อความธรรมดา
2. `vcard` — นามบัตร (vCard 3.0)
3. `event` — กิจกรรม (vCalendar / iCalendar)
4. `url` — URL เว็บไซต์ (auto prepend `https://` ถ้าขาด)
5. `wifi` — Wi-Fi credentials
6. `phone` — เบอร์โทร (`tel:` URI)

**goQR.me Parameters** (เพิ่มเติม):
- `size` (10–1000, default 300)
- `ecc` (`L`/`M`/`Q`/`H`, default `M`)
- `color`, `bgcolor` (hex เช่น `000000`, `ffffff`)
- `margin`, `qzone`
- `format` (`png`/`gif`/`jpeg`/`svg`/`eps`)

**Output**: binary image ตาม format ที่เลือก หรือ JSON ตาม `format` parameter

**Spec**: [`docs/api-specs/qr-code-generator.md`](../api-specs/qr-code-generator.md)

---

## 6. 🔮 Fortune Teller

**คำอธิบาย**: สุ่มคำทำนายจาก 52 ไฟล์ JSON ใน `api/fortune-teller/predictions/`

**Endpoint**: `GET /api/fortune-teller/` (ไม่มี parameter)

**Output หลัก**:
```json
{
  "success": true,
  "fortune": { ... },        // เนื้อหาคำทำนาย (TH/EN/ZH)
  "timestamp": "...",
  "total_fortunes": 52
}
```

**Edge Cases**:
- ถ้าไฟล์ `predictions/<id>.json` หาย → fallback error JSON
- ไม่มี query string รับ (เป็นสุ่มตลอด)

**หมายเหตุ**: ดูดวงเป็น **entertainment only** — ต้องมี disclaimer ทุกหน้าที่แสดงผล

**Spec**: [`docs/api-specs/fortune-teller.md`](../api-specs/fortune-teller.md)

---

## 7. 🎲 Randomizer

**คำอธิบาย**: สุ่มแบบต่าง ๆ ผ่าน API เดียว

**Endpoint**: `GET/POST /api/randomizer/?type={number|dice|coin|card}&...`

**Types**:
1. `number` — `min`, `max` (default 1–100)
2. `dice` — `sides` (2–100, default 6), `count` (1–10)
3. `coin` — `count` (1–10) → Heads/Tails
4. `card` — `count` (1–52 หรือ 54), `with_jokers` (boolean)

**Output หลัก**:
- ทุก type มี `success: true`, `type`, `result`, `timestamp`
- `dice` มี `total` + `dice_config`
- `coin` มี `statistics` (`heads`, `tails`)
- `card` มี `deck_info` + `card.display`, `card.symbol`, `card.color`

**Edge Cases**:
- `min > max` → error
- `sides < 2 || > 100` → error
- `count > 10` → error (สำหรับ dice/coin)
- `type` ไม่รู้จัก → error

**Spec**: [`docs/api-specs/randomizer.md`](../api-specs/randomizer.md)

---

## วิธีเพิ่มเครื่องมือใหม่ (Adding a New Tool)

เมื่อต้องการเพิ่ม Tool ใหม่ ต้องทำตามลำดับนี้:

1. **SA** เขียน [`docs/api-specs/<new-tool>.md`](../api-specs/) — input/output schema
2. **SA** สร้าง Issue ใน [`docs/issues/open/`](../issues/open/) พร้อม Labels `feature`, `api:<new-tool>`
3. **Designer** ออกแบบ UI ใน Figma / HTML Mockup
4. **Dev** สร้าง `api/<new-tool>/index.php` ตาม Spec
5. **Dev** สร้าง `public/<new-tool>.php` (Web UI) + เชื่อม API
6. **Dev** สร้าง `public/api-specs/<new-tool>.php` (rendered spec)
7. **Dev** เพิ่ม Card ใน `public/index.php` + อัปเดตตารางใน `README.md`
8. **QA** ทดสอบตาม Acceptance Criteria
9. **PM** ตรวจ PR ก่อน Merge

> ห้าม merge ถ้าขาด Spec, UI, Implementation, Test หรือ Docs ข้อใดข้อหนึ่ง (Definition of Done)
