# ใบงาน 00 — Style Inventory & Refactor Strategy

> เป้าหมาย: สรุป **ทุก class / style ที่ต้องมี** ไว้ในที่เดียว (single source of truth)
> เพื่อให้ใบงาน 01–02 ทำแบบ **ทีละไฟล์** ได้โดยไม่ต้องไล่อ่านทุกหน้าซ้ำ

---

## 1. กลยุทธ์ refactor (สำคัญ)

1. สร้าง `public/assets/css/style.css` = **base** (design tokens + reset + ส่วนประกอบร่วม
   site-header / site-footer / breadcrumb + docs elements) จาก mockup
2. **Refactor ทีละไฟล์** (15 ไฟล์): อ่าน inline `<style>` ของไฟล์นั้น → นำ class เฉพาะหน้า
   มา append ลง `style.css` **โดย scope ด้วย `$bodyClass`** → ลบ `<style>` ออกจากหน้า
   → เปลี่ยนไปใช้ `require header.php/footer.php` → lint
3. class ที่ชื่อซ้ำกันแต่หน้าตาต่างกัน (`.container`, `.header`, `.loading`, `.error`, `.info`,
   `.form-group`, `.download-btn`, `label`, `button`) **ต้อง scope ตามหน้าเสมอ** เช่น
   `.page-health .container { ... }`

### bodyClass ที่ใช้ scope

| หน้า | bodyClass |
| --- | --- |
| `public/index.php` | `page-home` |
| `public/fortune-teller.php` | `page-fortune` |
| `public/health-calculator.php` | `page-health` |
| `public/password-generator.php` | `page-password` |
| `public/username-generator.php` | `page-username` |
| `public/randomizer.php` | `page-randomizer` |
| `public/promptpay-qr-generator.php` | `page-promptpay` |
| `public/qr-code-generator.php` | `page-qrcode` |
| `public/api-specs/*.php` (7) | `page-docs` |

---

## 2. Base style.css (unscoped — ใช้ร่วมทุกหน้า)

จาก `docs/designs/mockups/style.css`:
- **Tokens** `:root` (brand `#667eea→#764ba2`, neutrals, semantic, code, spacing, radius, shadow)
- **Reset** `*`, `body`, `img`, `a`, `:focus-visible`
- **Layout**: `.container` (default 1200px), `.container--narrow` (800px), `.main`
- **Site header/nav**: `.site-header`, `.site-brand`, `.site-nav`, `.site-nav__toggle`, `.site-nav__links`, `.site-nav__link(.is-active)`
- **Breadcrumb** (shared): `.breadcrumb`, `.breadcrumb__sep`
- **Site footer**: `.site-footer`, `.site-footer__*`
- **Docs elements**: `.section`, `.endpoint`, `.method(--get/--post)`, `.url`, `.code-block`, `code`, `.table`, `.required`, `.optional`, `.response-box`, `.error-box`, `.features-grid`, `.feature-card`, `.lang-grid`, `.lang-item`, `.categories-grid`, `.category-item`, `.try-it`
- **Responsive + reduced-motion**

## 3. Per-page inventory (class เฉพาะหน้า → scope)

### `page-home` (`index.php`)
body: gradient brand, `padding:20px`. `.container` 1200px.
`.header` (hero: gradient, ขาว, rounded), `.status-badge`, `.tools-grid`, `.tool-card` (cursor, 3px transparent border, hover `translateY(-10px)` + `::before` shine sweep), `.tool-icon`, `.tool-title`, `.tool-description`, `.tool-features`, `.tool-actions`, `.btn`, `.btn--primary`, `.btn--secondary`, `.stats`, `.stats-title`, `.stats-grid`, `.stat-item`, `.stat-number` (gradient text), `.stat-label`, `.footer`, `.footer-links`

### `page-fortune` (`fortune-teller.php`)
`.container` 800px, `background:rgba(255,255,255,0.95)`. `.controls` (padding 30px, `#f8f9fa`), `.language-toggle`, `.language-btn(.active)` (brand), `.fortune-btn` (`#ff6b6b→#ee5a24`, radius 50px), `.fortune-display` (padding 40px, min-height 300px), `.fortune-card` (`#f093fb→#f5576c`, opacity 0 → `.show` opacity 1), `.fortune-text`, `.fortune-id`, `.placeholder`, `.loading` (`::after` dots), `.fortune-categories` (`#e3f2fd`), `.categories-title`, `.categories-list`

### `page-health` (`health-calculator.php`)
body flex center. `.container` 600px, padding 40px. `.form-group`, `label`, inputs (15px, `2px #e1e5e9`), `.unit-selector`, `.unit-option(.active)`, `.calculator-selector`, `.calc-option(.active)`, `.calculator-section(.hidden)`, `.hidden`, `.calculate-btn`, `.result(.show,.normal,.underweight,.overweight,.obese)`, `.bmi-value` (3em), `.bmi-category`, `.bmi-advice`, `.result-section(.hidden)`, `.bmr-title/.intake-title` (1.5em), `.bmr-value/.intake-calories` (2.5em, `#667eea`), `.bmr-detail/.intake-breakdown`, `.bmr-advice/.intake-advice`, `.water-title`, `.water-amount` (2.5em, `#2196F3`), `.water-breakdown`, `.water-advice`, `.loading(.show)`, `.error(.show)`

### `page-password` (`password-generator.php`)
`.container` grid `1fr 1fr`. `.form-section/.results-section` (white card), `.form-row`, `.checkbox-group`, `.checkbox-item(.checked)`, `.generate-btn`, `.results-header`, `.copy-all-btn`, `.password-grid`, `.password-item`, `.password-header`, `.password-text` (mono, `user-select:all`), `.password-actions`, `.copy-btn(.copied)/.analyze-btn`, `.password-info`, `.strength-indicator`, `.strength-weak/-medium/-strong/-very-strong`, `.password-analysis`, `.analysis-details`, `.analysis-item`, `.analysis-tips`, `.loading(.show)`, `.loading-spinner` (+spin), `.error(.show)`, `.generation-info`, `.security-tips`

### `page-username` (`username-generator.php`)
= `page-password` + `.username-grid`, `.username-item`, `.username-text`, `.theme-description`, `.option-description`

### `page-randomizer` (`randomizer.php`)
`.container` grid `1fr 1fr`. `.control-section/.results-section`, `.type-selector`, `.type-button(.active)` (+`::before` sweep), `.type-icon`, `.controls-container(.active)`, `.control-group`, `.generate-btn(.loading)`, `.result-container`, `.result-display` (`#f5f7fa→#c3cfe2`), `.result-value` (3em, slideIn), `.result-info`, `.animate-bounce/-flip/-roll/-shuffle`, `.card-display`, `.card-visual(.red,.black)` (120×168), `.card-rank`, `.card-suit`, `.dice-container`, `.dice-visual` (60×60), `.coin-visual` (100px circle), `.placeholder`, `.error-message`
keyframes: `fadeIn, spin, slideIn, bounce, flip, roll, shuffle`

### `page-promptpay` (`promptpay-qr-generator.php`)
body flat `#f5f5f5`, font Arial, max-width 600px. `.container` (white card). `.form-group`, `label`, inputs (green focus `#4CAF50`), `button` (`#4CAF50`), `.qr-container` (`#1A3763`), `.qr-image`, `.payload-info`, `.error` (red), `.info` (`#e3f2fd`), `.download-btn` (`#2196F3`), `.warning` (`#fff3cd`), `.api-info` (+h3/code/ul/li)

### `page-qrcode` (`qr-code-generator.php`)
`.container` grid `1.2fr 1fr`. `.header`, `.badge-row`, `.badge`, `.form-section/.preview-section`, `.type-selector`, `.type-btn(.active)`, `.ico`, `.field-group(.active)`, `.row`, `.row-3`, `.section-title`, `.checkbox-row`, `.dyn-list`, `.dyn-row(.name/.nickname/.email/.phone/.url/.address)`, `.add-row`, `.remove-row`, `.color-picker-row`, `.swatch`, `.advanced-toggle`, `.advanced(.open)`, `.generate-btn`, `.qr-display`, `.qr-placeholder`, `.download-btn` (`#28a745`), `.payload-info`, `.error`, `.info`, `.breadcrumb`, `.right`, `.pill`

### `page-docs` (api-specs 7 ไฟล์)
local `.nav`, `.content`, `.method.get/.post` (มีทั้ง `.method--get` และ `.method.get`), `.parameter-table`, `.features-grid`, `.feature-card`, `.lang-grid`, `.lang-item`, `.categories-grid`, `.category-item`, `.try-it`, `.response-box`, `.error-box`, `.endpoint`, `.url`, `.code-block`, `.required`, `.optional` — (ส่วนใหญ่ทับกับ base ข้อ 2 แล้ว, เหลือ alias `.method.get/.post` + `.nav/.content`)

---

## 4. class ขัดแย้งที่ต้อง scope (เช็กลิสต์)

`.container` (6 layout), `.header` (3 แบบ), `.loading` (4 แบบ), `.error` (3 แบบ), `.info` (2 แบบ), `.form-group` (3 แบบ), `.download-btn` (2 แบบ), `label`/`button` (bare, 2 แบบ) — **ห้าม** ใช้ unscoped

## 5. ลำดับการ refactor (ต่อจากใบงาน 01/02)

1. `index.php`
2. `fortune-teller.php`, `health-calculator.php`, `password-generator.php`, `username-generator.php`, `randomizer.php`, `promptpay-qr-generator.php`, `qr-code-generator.php`
3. `api-specs/*.php` (7)
