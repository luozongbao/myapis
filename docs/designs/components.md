# 🎨 MyAPIs Design System — Component Library & Tokens

> **Status**: v1.0 (Sprint 1, ISSUE-012 ready for review)
> **Owner**: ยู (UX/UI Designer)
> **Tests**: WCAG 2.1 AA pass (24/24 contrast pairs), 22/22 a11y checks
> **Coverage**: 4 CSS files, ~30KB total, zero CSS framework

---

## 📦 What's in the box

```
public/assets/css/
├── design-tokens.css   (6.5KB) — :root vars (single source of truth)
├── base.css            (4.3KB) — reset + typography + skip-link
├── components.css     (11.5KB) — buttons, forms, cards, alerts, badges
└── tool-page.css       (7.5KB) — page layout (tool + landing)
```

Preview at `public/assets/preview.html` (Lighthouse-ready).

### Why components.css is 11.5KB (above the 10KB guideline)
The form section alone is ~5KB because every input type (text, number, email, tel, password, search, url, date, select, textarea, range, checkbox, radio) gets full state coverage (default / hover / focus / invalid / disabled). This is a deliberate trade-off:
- ✅ Migration is a 1-line `<link>` swap; no per-file CSS edits
- ✅ Designers can iterate on tokens without touching form code
- ⚠️ Slightly above the original AC — acceptable for the foundation file

If we need to hit strict 10KB in the future, we can split into `forms.css` (~5KB) + `components.css` (~7KB). Not done now because PM spec listed exactly 4 files.

---

## 🎨 Design Tokens (`design-tokens.css`)

### Philosophy

- **Decorative vs. interactive separation.** Brand gradient (`#667eea → #764ba2`) is for hero/heading accents only — fails 4.5:1 contrast on white, so it never appears as text. All interactive elements use AA-compliant primaries (`#2563eb` for buttons, `#1d4ed8` for links).
- **8-point spacing grid.** `space-1` (4px) through `space-10` (80px). No half-units.
- **Typography stack with i18n fallbacks.** Noto Sans Thai → system Asian fonts (PingFang SC / MS YaHei / Hiragino Sans GB) → system Latin → ui-sans-serif. Works on Thai, English, and Chinese without re-bundling fonts in CSS.

### Color palette (verified WCAG AA)

| Token | Hex | AA ratio vs white | Use |
|-------|-----|-------------------|-----|
| `--color-text` | `#1f2937` | **14.7:1** AAA | Body |
| `--color-text-muted` | `#4b5563` | **7.6:1** AAA | Subtitles |
| `--color-text-subtle` | `#6b7280` | **4.8:1** AA | Placeholders, hints |
| `--color-text-link` | `#1d4ed8` | **6.7:1** AA | Links |
| `--color-primary` | `#2563eb` | **5.2:1** AA | Buttons |
| `--color-success` | `#15803d` | **5.0:1** AA | Success states |
| `--color-error` | `#b91c1c` | **6.5:1** AA | Errors |
| `--color-warning` | `#a16207` | **4.9:1** AA | Warnings (close) |
| `--color-info` | `#1e40af` | **8.7:1** AA | Info banners |
| `--color-border` | `#e5e7eb` | — | Default border |
| `--color-border-focus` | `#2563eb` | **5.2:1** | Focus outline |

Brand (decorative only, **not** for text):
- `--color-brand-1` `#667eea`, `--color-brand-2` `#764ba2`, `--color-brand-gradient` (135deg)

### Spacing scale (8pt grid)

`--space-0` 0 → `--space-1` 0.25rem → `--space-2` 0.5rem → `--space-3` 0.75rem → `--space-4` 1rem → `--space-5` 1.5rem → `--space-6` 2rem → `--space-7` 2.5rem → `--space-8` 3rem → `--space-9` 4rem → `--space-10` 5rem

### Typography scale

`--text-xs` 0.75rem → `--text-sm` 0.875rem → `--text-base` 1rem (default) → `--text-lg` 1.125rem → `--text-xl` 1.25rem → `--text-2xl` 1.5rem → `--text-3xl` 1.875rem → `--text-4xl` 2.5rem

Line-heights: `--leading-tight` 1.25, `--leading-snug` 1.4, `--leading-normal` 1.5, `--leading-relaxed` 1.7 (Thai bodies).

### Radius · Shadow · Z-index · Transition

- Radius: `sm` 6px, `default` 8px, `md` 10px, `lg` 12px, `xl` 20px (matches old card radius), `pill` 9999px
- Shadow: `sm` / `default` / `md` / `lg` / `xl` (largest matches the old hero shadow)
- Z-index: `base` 1, `dropdown` 10, `sticky` 100, `overlay` 500, `modal` 1000, `toast` 2000
- Transition: `150ms ease` (default), `200ms` (default), `300ms` (slow). All zero out under `prefers-reduced-motion`.

### Forced colors + reduced motion

```css
@media (prefers-reduced-motion: reduce) { /* transitions → 0ms */ }
@media (forced-colors: active)        { /* use CanvasText, Canvas, etc. */ }
```

---

## 🧩 Component Catalog (`components.css`)

### `.btn` — Button

Variants: `.btn-primary`, `.btn-secondary`, `.btn-ghost`, `.btn-danger`, `.btn-success`
Sizes: `.btn-sm` (36px), default (44px), `.btn-lg` (52px)
Modifiers: `.btn-block`, `.btn-icon`, `[disabled]`, `.is-loading`

```html
<button type="submit" class="btn btn-primary btn-lg btn-block">
  <span class="spinner" aria-hidden="true"></span>
  กำลังคำนวณ...
</button>
```

- Min height 44px (WCAG 2.5.5 target size)
- Visible `:focus-visible` ring (3px, var(--color-focus-ring))
- `prefers-reduced-motion`: hover translate disabled
- Disabled state has `cursor: not-allowed` + 65% opacity

### `.card` — Surface

```html
<div class="card card-center">
  <h2 class="card-title">Title</h2>
  <p class="card-subtitle">Subtitle</p>
  <!-- content -->
</div>
```

Modifiers: `.card-tight` (space-5 padding), `.card-loose` (space-8), `.card-center` (text-align center).
Default padding `space-7` (40px) — closest match to old "30-40px padding" in legacy pages.

### `.form-field` (alias `.form-group`) — Form wrapper

```html
<div class="form-field">
  <label for="email">อีเมล <span class="required" aria-hidden="true">*</span></label>
  <input type="email" id="email" name="email" required
         aria-required="true" aria-describedby="email-help">
  <small id="email-help" class="form-help">ใช้สำหรับติดต่อกลับเท่านั้น</small>
</div>
```

- `<label>` always paired with `for=`
- `aria-required="true"` on every required input
- `aria-describedby` points to help text
- Invalid state via `aria-invalid="true"` or `.is-invalid` (red border + red focus ring)
- `<fieldset>` + `<legend>` for grouped controls (radio, multi-checkbox)

### Form layout

- `.form-row` — 1 column mobile, 2 columns ≥600px
- `.form-row-3` — 1 column mobile, 3 columns ≥600px (e.g. weight / height / age)

### `.alert` — Status banner

```html
<div class="alert alert-error" role="alert">
  <span class="alert-icon" aria-hidden="true">✕</span>
  <div><strong>ผิดพลาด:</strong> น้ำหนักต้องเป็นค่าบวก</div>
</div>
```

Variants: `.alert-success`, `.alert-error` / `.alert-danger`, `.alert-warning`, `.alert-info`

- Success/info → `role="status"` `aria-live="polite"` (announces without interrupting)
- Error/warning → `role="alert"` (assertive announcement)
- Left border (4px) colored to match severity
- Icon `<span aria-hidden="true">` — `aria-label`/text inside provides meaning

### `.badge` — Pill / tag

Variants: `.badge` (default), `.badge-primary`, `.badge-success`, `.badge-error`, `.badge-warning`, `.badge-info`
Use inside `.badge-row` (flex, gap-2, wrap) for multiple pills.

### `.result` — Tool output block

```html
<div class="result" role="status" aria-live="polite">
  <div class="result-value">22.86</div>
  <div class="result-label">BMI</div>
  <p class="result-advice">น้ำหนักปกติ</p>
</div>
```

The live region ensures screen readers announce new results when the user submits a form.

### `.tool-selector` — Tab interface

```html
<div class="tool-selector" role="tablist" aria-label="เลือกเครื่องมือ">
  <button role="tab" aria-selected="true" data-type="bmi">BMI</button>
  <button role="tab" aria-selected="false" data-type="bmr">BMR</button>
</div>
```

- Selected state has white bg + visible border (lift effect)
- Hover state on non-selected tabs uses semi-transparent overlay

### `.language-toggle` / `.language-btn`

Used in fortune-teller page. Uses `aria-pressed="true|false"` (toggle pattern).

### `.spinner` — Loading indicator

```html
<span class="spinner" aria-hidden="true"></span>
```

Has `aria-hidden="true"` because container around it (button or status) provides the label.

### `.empty-state` — Empty state copy

Italic muted text. Used in result panels when no data exists yet.

---

## 🎨 Page Layout (`tool-page.css`)

### `.tool-page` — Tool page wrapper

```html
<body>
  <a class="skip-link" href="#main">ข้ามไปยังเนื้อหา</a>
  <main id="main" tabindex="-1" class="tool-page">
    <header class="tool-header">
      <h1>🏥 Health Calculator</h1>
      <p>คำนวณค่าสุขภาพพื้นฐาน</p>
    </header>
    <!-- content -->
  </main>
</body>
```

### `.container` — Width wrapper

- Default: `--container-md` (800px) — fits 600-800px tools like fortune-teller, health-calc
- `.container-sm` (600px) — narrow tools like promptpay-qr
- `.container-lg` (1200px) — wide tools like password-generator, landing

### `.tool-header` — Page title card

Centered, 20px radius, gradient text effect on h1.

### `.tool-body` — Two-column form + result

- 1 column < 900px (mobile)
- 1.2fr / 1fr ≥ 900px (tablets and up)

### Landing page (`.landing`)

- `.landing-header` — hero card with gradient h1
- `.tool-grid` — responsive 1 / 2 / 3 columns at 600px / 900px breakpoints
- `.tool-card` — each tool entry with icon, title, description, and `.tool-actions` row of buttons
- `.status-badge` — top-of-page "Live" pill

### Print stylesheet

Hides interactive chrome (buttons, language toggles, footer); keeps content + borders for a clean printout.

---

## ♿ Accessibility Checklist (per page)

- [x] `<html lang="...">` on every page — `lang="th"` for Thai, `lang="en"`, `lang="zh"`
- [x] `<title>` + `<meta name="description">` + `<meta name="viewport">`
- [x] Skip link as first focusable element, lands on `<main tabindex="-1">`
- [x] Single `<h1>` per page; no level skips (verified by automated test)
- [x] Every form input has a `<label for="id">` — no placeholders-only
- [x] Required inputs use `required` + `aria-required="true"`
- [x] Help text linked via `aria-describedby`
- [x] Invalid inputs use `aria-invalid="true"` + red border + red focus ring
- [x] Result/status regions use `role="status"` + `aria-live="polite"`
- [x] Error banners use `role="alert"` (assertive)
- [x] Tab interfaces use `role="tablist"` + `role="tab"` + `aria-selected`
- [x] Toggle buttons use `aria-pressed`
- [x] Decorative icons use `aria-hidden="true"`
- [x] Visible `:focus-visible` outline (3px solid)
- [x] Color contrast ≥ 4.5:1 verified for every text/background pair (24/24)
- [x] Honors `prefers-reduced-motion` and `forced-colors`
- [x] Buttons ≥ 44×44px tap target (WCAG 2.5.5)
- [x] Mobile-first responsive at 600 / 768 / 900 px breakpoints

---

## 🌏 i18n / Thai/EN/ZH Notes

### Font loading

Add to `<head>` of any page:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">
```

Fallback order in `--font-sans`: Noto Sans Thai → system Asian fonts → system Latin → generic sans.

### Line height

TH/CH glyphs need extra leading. Default body line-height is `--leading-relaxed` (1.7). Headings use `--leading-snug` (1.4). Never below 1.4 for TH.

### Word breaking

`body { word-break: break-word; overflow-wrap: anywhere; }` prevents TH/CH overflow.

### Numbers

Use `0-9` (international) for input forms. UI display can use Thai numerals but only in prose, never in input fields or numeric results.

---

## 📋 Handoff Checklist (for Dev on ISSUE-002)

For each tool page:

1. Replace inline `<style>...</style>` block with `<link>` tags:
   ```html
   <link rel="stylesheet" href="/assets/css/design-tokens.css">
   <link rel="stylesheet" href="/assets/css/base.css">
   <link rel="stylesheet" href="/assets/css/components.css">
   <link rel="stylesheet" href="/assets/css/tool-page.css">
   ```
2. Replace `class="container"` widths:
   - 600px → `class="container container-sm"`
   - 800px → `class="container"` (default)
   - 1200px → `class="container container-lg"`
3. Map old inline selectors to token classes:
   - `body` background → covered by `.tool-page` or `.landing`
   - `.form-group` → `.form-field` (alias kept, both work)
   - `.btn` `style="..."` → add `.btn-primary` / `.btn-secondary` modifier class
   - custom hex colors → `var(--color-...)` tokens
4. Remove inline `style="padding: 30px"` etc. → use `.card-tight` / `.card-loose`
5. Test each page in mobile (375px) + tablet (768px) + desktop (1200px)

For landing page (`index.php`):

1. Add `class="landing"` to `<body>`
2. Header card → `.landing-header`
3. Tool list → `.tool-grid` + `.tool-card` for each
4. Each `.tool-card` has a `.tool-card-icon` (emoji), `.tool-card-title`, `.tool-card-desc`, and `.tool-actions` row

---

## 🔍 Verification Results (2026-08-31)

- **File sizes** (target: each < 10KB; components.css accepts ~1.5KB over for form coverage):
  - design-tokens.css: **6.5KB** ✓
  - base.css: **4.3KB** ✓
  - components.css: **11.5KB** ⚠️ (justified above)
  - tool-page.css: **7.5KB** ✓
- **WCAG AA contrast** (24 text/background pairs tested): **24/24 pass**
- **Manual a11y audit** (preview.html): **22/22 checks pass**
- **Preview URL**: `http://web.local/assets/preview.html`
- **Files served via HTTP/200** with correct `Content-Type: text/css` ✓
- ⚠️ `Cache-Control: public, max-age=604800` not yet set — DevOps task (their nginx config)

---

## 🔗 Related

- Issue #002: [ISSUE-002-extract-css.md](../issues/ISSUE-002-extract-css.md) — migrate inline styles
- Issue #009: [ISSUE-009-a11y.md](../issues/ISSUE-009-a11y.md) — accessibility improvements
- Issue #007: Thai / ZH / EN specs
- [WCAG 2.1 AA Quickref](https://www.w3.org/WAI/WCAG21/quickref/)
- [Inclusive Components](https://inclusive-components.design/)
- [8-Point Grid](https://spec.fm/specifics/8-pt-grid)

---

_Maintained by ยู (Designer). Last updated 2026-08-31._
