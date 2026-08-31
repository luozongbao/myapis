# ISSUE-002: Refactor Inline CSS to External Stylesheets

> **Type**: refactor / tech-debt
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบันไฟล์ `public/<tool>.php` ทุกไฟล์มี `<style>` inline ขนาดใหญ่ → ทำให้:
- ❌ Cache ไม่ได้ — ทุก page ต้อง download CSS ซ้ำ
- ❌ Maintenance ยาก — แก้สีต้องแก้ 7 ที่
- ❌ ไม่ follow single-source-of-truth
- ❌ Devs เพิ่ม feature ใหม่ต้อง copy-paste CSS

## 👤 User Story

As a developer,
I want ทุก CSS อยู่ใน external file,
So that maintain ง่าย และ cache ได้

As a user,
I want page โหลดเร็วขึ้น,
So that ประสบการณ์ดีขึ้น

## 📦 Scope

### In Scope
- ✅ สร้าง design system (design tokens: colors, spacing, typography)
- ✅ สร้าง `public/assets/css/design-tokens.css`
- ✅ สร้าง `public/assets/css/base.css` (reset + globals)
- ✅ สร้าง `public/assets/css/components.css` (forms, buttons, cards, alert)
- ✅ สร้าง `public/assets/css/tool-page.css` (หน้าที่ทุก tool share)
- ✅ ปรับ `public/<tool>.php` ทั้ง 7 ไฟล์ — include external CSS
- ✅ ปรับ `public/index.php` (landing page)

### Out of Scope
- ❌ ไม่ migrate ไป CSS framework (Bootstrap, Tailwind)
- ❌ ไม่เพิ่ม dark mode (อาจเพิ่มทีหลัง)
- ❌ ไม่ทำ responsive overhaul (เดิมดีอยู่แล้ว)
- ❌ ไม่ refactor JavaScript

## 📏 Current State

แต่ละ `public/<tool>.php` มี `<style>` block ~100-200 บรรทัด:
```php
<style>
:root {
    --primary: #...;
    --bg: #...;
    ...
}
body { ... }
.tool-page { ... }
.card { ... }
form { ... }
input { ... }
button { ... }
</style>
```

## 🎯 Target State

```html
<!-- public/<tool>.php -->
<head>
    <link rel="stylesheet" href="/assets/css/design-tokens.css">
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/tool-page.css">
</head>
<body>
    <!-- ไม่มี <style> inline -->
</body>
```

```
public/assets/css/
├── design-tokens.css   (:root vars, theme colors)
├── base.css            (reset, typography, layout)
├── components.css      (buttons, forms, cards, alerts)
└── tool-page.css       (tool-page specific)
```

## ✅ Acceptance Criteria

- [ ] ทุก CSS อยู่ใน `public/assets/css/`
- [ ] ทุก `<style>` inline ถูกลบออก
- [ ] UI ยังเหมือนเดิม 100% (visual regression test)
- [ ] Cache header ทำงาน (`Cache-Control: public, max-age=604800`)
- [ ] ไฟล์ CSS แต่ละไฟล์ < 10KB (ไม่ bloated)
- [ ] ใช้ได้ทั้ง 7 tool pages + landing page + API specs page

## 🔧 Technical Approach

### ขั้นที่ 1: สกัด Design Tokens
Designer ระบุ design system (colors, spacing, typography, radius, shadows):

```css
/* design-tokens.css */
:root {
    /* Colors */
    --color-primary: #3b82f6;
    --color-primary-dark: #1d4ed8;
    --color-success: #10b981;
    --color-error: #ef4444;
    --color-bg: #ffffff;
    --color-text: #1f2937;
    --color-text-muted: #6b7280;
    --color-border: #e5e7eb;

    /* Spacing */
    --space-1: 0.25rem;
    --space-2: 0.5rem;
    --space-3: 1rem;
    --space-4: 1.5rem;
    --space-6: 2.5rem;

    /* Typography */
    --font-base: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --font-mono: ui-monospace, "SF Mono", Menlo, monospace;
    --text-sm: 0.875rem;
    --text-base: 1rem;
    --text-lg: 1.125rem;

    /* Other */
    --radius: 0.5rem;
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
}
```

### ขั้นที่ 2: Base CSS
Reset, font, body background

### ขั้นที่ 3: Components
Buttons, forms, input, cards, alerts, badges

### ขั้นที่ 4: Tool Page Specific
Layout ของหน้า tool

### ขั้นที่ 5: Migrate ไฟล์
ทำทีละไฟล์, test ทุกครั้ง

## 📋 Tasks

### Design (Designer)
- [ ] สร้าง design tokens spec
- [ ] สร้าง component library spec
- [ ] Validate visual กับ originals

### Implementation (Dev)
- [ ] สร้าง `design-tokens.css`
- [ ] สร้าง `base.css`
- [ ] สร้าง `components.css`
- [ ] สร้าง `tool-page.css`
- [ ] Migrate `public/index.php`
- [ ] Migrate `public/health-calculator.php`
- [ ] Migrate `public/password-generator.php`
- [ ] Migrate `public/username-generator.php`
- [ ] Migrate `public/promptpay-qr-generator.php`
- [ ] Migrate `public/qr-code-generator.php`
- [ ] Migrate `public/fortune-teller.php`
- [ ] Migrate `public/randomizer.php`
- [ ] Migrate `public/api-specs/*.php`

### Verify
- [ ] Visual regression test (screenshot diff)
- [ ] ใช้ page speed เร็วขึ้น

### Docs
- [ ] Update `docs/standards/documentation.md` — design tokens section
- [ ] Update `README.md` — assets structure

## 🔗 Dependencies

- ไม่มี

## 📝 Notes

- ควรทำเป็น 1 PR เดียวหรือหลาย PRs? — แนะนำแยกตาม layer (1 PR ต่อ file ใหม่, 1 PR ต่อ migration page)
- ใช้ Git diff ของ HTML ก่อน-หลัง ตรวจสอบ
- Optional: Setup `prettier` (HTML formatter) ใน CI

## 🔖 Labels

`refactor`, `tech-debt`, `design-system`, `css`
