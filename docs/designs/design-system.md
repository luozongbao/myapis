# MyAPIs — Design System & Visual Language

> เป้าหมาย: สร้าง visual language เดียว ใช้ร่วมกันทั้ง **Website pages**, **API Tools**
> และ **API Specs documents** เพื่อให้ทุกหน้ามีลุคเดียวกันและบำรุงรักษาง่าย
> (Goal 01 ข้อ 1 — เปลี่ยนจาก inline `<style>` มาเป็น `style.css` ไฟล์เดียว)

เอกสารนี้คือ **source of truth** สำหรับสี / ตัวอักษร / ระยะห่าง / component ก่อนลงมือ
implement ไฟล์จริงที่ `public/assets/css/style.css` ดู mockup ได้ที่ `docs/designs/mockups/`

---

## 1. Design Principles

| หลักการ | คำอธิบาย |
| --- | --- |
| **Card-first** | เนื้อหาทุกชิ้น (tool, endpoint, feature) วางใน card เพื่อให้สแกนง่าย |
| **Developer-friendly** | code block ใช้ monospace พื้นเข้ม อ่านง่าย เน้น endpoint/method |
| **Gradient accent** | ใช้ gradient ม่วง–น้ำเงินเป็นเอกลักษณ์ brand อยู่แล้ว (คงไว้) |
| **Responsive** | grid ยุบเป็นคอลัมน์เดียวเมื่อจอแคบ (mobile-first) |
| **Accessible** | contrast ผ่าน WCAG AA, focus state ชัดเจน, รองรับ `prefers-reduced-motion` |

---

## 2. Design Tokens (CSS Custom Properties)

นำไปใส่เป็น `:root {}` ใน `style.css` — ทุก component ใช้ตัวแปรเหล่านี้เท่านั้น
(ห้าม hard-code สีโดยตรง) เพื่อให้เปลี่ยน theme ได้ที่เดียว

```css
:root {
  /* ---- Brand ---- */
  --color-primary: #667eea;
  --color-primary-strong: #5a4bd1;
  --color-accent: #764ba2;
  --gradient-brand: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

  /* ---- Neutrals ---- */
  --color-bg: #f4f5fb;
  --color-surface: #ffffff;
  --color-surface-alt: #f8f9fa;
  --color-text: #1f2430;
  --color-text-muted: #5b6472;
  --color-border: #e6e8f0;

  /* ---- Semantic ---- */
  --color-success: #28a745;
  --color-danger: #dc3545;
  --color-warning: #b7791f;
  --color-info: #17a2b8;

  /* ---- Code ---- */
  --color-code-bg: #1e2433;
  --color-code-text: #e2e8f0;

  /* ---- Typography ---- */
  --font-sans: 'Segoe UI', system-ui, -apple-system, Tahoma, Geneva, Verdana, sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', 'Courier New', monospace;

  /* ---- Spacing (4px scale) ---- */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 24px;
  --space-6: 32px;
  --space-7: 48px;
  --space-8: 64px;

  /* ---- Radius ---- */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 20px;
  --radius-full: 999px;

  /* ---- Shadow ---- */
  --shadow-sm: 0 1px 3px rgba(16, 24, 40, 0.08);
  --shadow-md: 0 8px 24px rgba(16, 24, 40, 0.10);
  --shadow-lg: 0 15px 35px rgba(16, 24, 40, 0.12);

  /* ---- Layout ---- */
  --container-max: 1200px;
  --container-narrow: 800px;

  /* ---- Motion ---- */
  --transition: 0.2s ease;
}
```

### 2.1 Typography Scale

| Token | ขนาด | ใช้กับ |
| --- | --- | --- |
| `--text-xs` | 0.75rem | label เล็ก, meta |
| `--text-sm` | 0.875rem | ข้อความรอง, breadcrumb |
| `--text-base` | 1rem | body |
| `--text-lg` | 1.125rem | subtitle |
| `--text-xl` | 1.5rem | card title |
| `--text-2xl` | 2rem | section title |
| `--text-3xl` | 2.5rem | page title (hero) |

### 2.2 Breakpoints

```css
/* Mobile-first */
@media (min-width: 576px) { /* small tablet */ }
@media (min-width: 768px) { /* tablet */ }
@media (min-width: 992px) { /* laptop */ }
@media (min-width: 1200px) { /* desktop */ }
```

---

## 3. Layout & Components

ทุก class ด้านล่างคือ **API ที่ `style.css` ต้องมี** เพื่อให้ header/footer และทุกหน้า
ใช้ร่วมกันได้ (page structure ดู `page-structure.md`)

### 3.1 Layout

| Class | หน้าที่ |
| --- | --- |
| `.container` | กล่องกลางหน้า `max-width: var(--container-max)` |
| `.container--narrow` | สำหรับหน้า tool ที่แคบกว่า (`--container-narrow`) |
| `.site-header` | header แถบบน (sticky) |
| `.site-nav` | เมนูภายใน header |
| `.site-nav__links` | กลุ่มลิงก์เมนู |
| `.site-nav__link` | ลิงก์เมนู; `.is-active` สำหรับหน้าปัจจุบัน |
| `.site-nav__toggle` | ปุ่ม hamburger (mobile) |
| `.breadcrumb` | เส้นทาง breadcrumb |
| `.main` | พื้นที่เนื้อหา `<main>` |
| `.site-footer` | footer ล่าง |
| `.site-footer__links` | กลุ่มลิงก์ footer |

### 3.2 Cards (Homepage + Tools)

| Class | หน้าที่ |
| --- | --- |
| `.card` | card พื้นฐาน |
| `.tool-card` | card หน้า homepage หนึ่ง tool |
| `.tool-card__icon` | emoji ไอคอน |
| `.tool-card__title` | ชื่อ tool |
| `.tool-card__desc` | คำอธิบาย |
| `.tool-card__features` | รายการ feature (มี ✓) |
| `.tool-card__actions` | แถวปุ่ม Try/API/Docs |
| `.stats` | แถบสถิติ |
| `.stats__grid` | grid ของสถิติ |
| `.stat__number` | ตัวเลขสถิติ |
| `.stat__label` | ป้ายกำกับสถิติ |

### 3.3 Buttons & Badges

| Class | หน้าที่ |
| --- | --- |
| `.btn` | ปุ่มพื้นฐาน |
| `.btn--primary` | gradient brand |
| `.btn--secondary` | พื้นเทา |
| `.btn--ghost` | ใส่กรอบอย่างเดียว |
| `.btn--sm` | ขนาดเล็ก (ใช้ใน breadcrumb/nav) |
| `.badge` | ป้ายสถานะ |
| `.badge--success` / `.badge--warning` / `.badge--danger` | สีตาม semantic |

### 3.4 Code & Tables (API Specs)

| Class | หน้าที่ |
| --- | --- |
| `.code-block` | กล่อง code พื้นเข้ม |
| `code` | inline code |
| `.table` | ตาราง parameter |
| `.required` | ป้าย Required (แดง) |
| `.optional` | ป้าย Optional (เทา) |
| `.method` | method badge (GET/POST) |
| `.method--get` | เขียว |
| `.method--post` | น้ำเงิน |
| `.url` | แสดง endpoint URL (monospace) |
| `.endpoint` | card หนึ่ง endpoint |
| `.section` | section ในเอกสาร |
| `.response-box` | กล่องตัวอย่าง success |
| `.error-box` | กล่องตัวอย่าง error |
| `.features-grid` / `.feature-card` | grid + card ของ feature |
| `.lang-grid` / `.lang-item` | grid ภาษา |
| `.categories-grid` / `.category-item` | grid หมวดหมู่ |
| `.try-it` | CTA กล่องลองใช้ |

### 3.5 Forms & Tool-specific (คง class เดิมจากหน้าเดิม)

หน้า tool เดิมใช้ class เหล่านี้ — **ต้อง preserve ไว้ใน `style.css`** เพื่อไม่ให้ layout
พังตอน refactor:

- `.language-toggle`, `.language-btn`, `.language-btn.active`
- `.fortune-btn`, `.fortune-card`, `.fortune-card.show`, `.fortune-text`, `.fortune-id`
- `.placeholder`, `.loading` (+ `@keyframes dots`)
- `.checkbox-item`, `input[type=checkbox]`, `input[type=number]`, `select`, `.field`, `.label`
- `.tool-actions`, `.tool-features` (alias ของ `.tool-card__*` ให้เข้ากันได้)

> หมายเหตุ: ใน `style.css` ควรเขียน `.tool-actions` และ `.tool-card__actions` ให้มี
> style ร่วมกัน (group selector) เพื่อลดความซ้ำซ้อน

---

## 4. Accessibility & Interaction

- Focus state: `:focus-visible { outline: 3px solid var(--color-primary); outline-offset: 2px; }`
- Hover: card ยกขึ้น `translateY(-4px)` + shadow เพิ่ม (มี `@media (prefers-reduced-motion: reduce)` ปิด animation)
- สี link ต้องมี `text-decoration: underline` อย่างน้อยตอน hover/focus
- Contrast: ข้อความบนพื้น gradient ใช้สีขาว และรองพื้นหลังสีเข้มไว้เสมอ
- เป้าหมาย tap target ≥ 44×44px บน mobile

---

## 5. Mockup Reference

| ไฟล์ | สาธิต |
| --- | --- |
| `mockups/style.css` | stylesheet ต้นแบบเต็ม (copy ไป `public/assets/css/style.css` ได้เลย) |
| `mockups/homepage.html` | หน้าแรก + tool cards + stats + footer |
| `mockups/tool-page.html` | หน้า tool ตัวอย่าง (fortune-teller) |
| `mockups/api-specs.html` | หน้า API documentation ตัวอย่าง |
