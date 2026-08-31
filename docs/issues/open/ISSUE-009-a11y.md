# ISSUE-009: Improve Accessibility (a11y) Across All Tools

> **Type**: feature / quality
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบัน web UI มี accessibility ต่ำ:
- ❌ Form ไม่มี `<label>`
- ❌ Color contrast ไม่ผ่าน WCAG AA
- ❌ ปุ่มไม่มี aria-label
- ❌ ไม่รองรับ keyboard navigation เต็มรูปแบบ
- ❌ Loading/error messages ไม่มี role="alert"

NFR-009 ระบุ accessibility ≥ 90 (Lighthouse score)

## 👤 User Story

As a user with disability,
I want UI ที่ screen reader ใช้ได้และ keyboard ใช้ได้,
So that ใช้งานได้ทุกคน

## 📦 Scope

### In Scope
- ✅ เพิ่ม `<label>` ทุก form input
- ✅ เพิ่ม `aria-*` attributes ที่ขาด
- ✅ ปรับ color contrast ≥ 4.5:1
- ✅ รองรับ keyboard navigation
- ✅ เพิ่ม `role="alert"` กับ status messages
- ✅ Skip link (`Skip to main content`)
- ✅ Accessibility audit (Lighthouse / axe)

### Out of Scope
- ❌ ไม่เพิ่ม RTL support
- ❌ ไม่เพิ่ม screen reader testing manual (ทำ e2e ผ่าน Issue อื่น)

## ✅ Acceptance Criteria

- [ ] Lighthouse Accessibility score ≥ 95 ทุกหน้า
- [ ] axe DevTools: 0 critical issues
- [ ] Keyboard navigation ทำงานทั้งหมด
- [ ] Screen reader (NVDA/VoiceOver) อ่านได้
- [ ] Contrast ผ่าน WCAG AA

## 🔧 Technical Approach

### Label + Form

```html
<!-- Before -->
<input type="number" name="weight" placeholder="Weight (kg)">

<!-- After -->
<label for="weight-input">น้ำหนัก (กิโลกรัม)</label>
<input
    type="number"
    id="weight-input"
    name="weight"
    required
    aria-required="true"
    aria-invalid="false"
    aria-describedby="weight-help"
>
<small id="weight-help">ใส่น้ำหนักเป็นตัวเลข เช่น 70</small>
```

### Buttons

```html
<!-- Before -->
<button onclick="calculate()">คำนวณ</button>

<!-- After -->
<button type="submit" aria-label="คำนวณ BMI">คำนวณ</button>
```

### ARIA Live Regions

```html
<div id="result" role="status" aria-live="polite">
    <!-- result -->
</div>

<div id="error" role="alert">
    <!-- error -->
</div>
```

### Skip Link

```html
<a href="#main-content" class="skip-link">ข้ามไปยังเนื้อหา</a>

<main id="main-content" tabindex="-1">
    ...
</main>
```

```css
.skip-link {
    position: absolute;
    left: -9999px;
}
.skip-link:focus {
    position: static;
    /* ... */
}
```

### Color Contrast Check

| Pair | Before | After |
|------|--------|-------|
| Body text / bg | 4.0:1 (fail) | 7.0:1 (AAA) |
| Placeholder / bg | 3.0:1 | 4.5:1 |
| Button text / btn | 4.5:1 | 7.0:1 |

→ ปรับ `--color-text`, `--color-bg`, etc. ใน `design-tokens.css`

### Keyboard Nav

- [ ] `Tab` focus ผ่านทุก interactive element
- [ ] `Enter`/`Space` activate button
- [ ] `Esc` close modal
- [ ] `:focus-visible` มี outline ชัดเจน

## 📋 Tasks

### Audit (Designer)
- [ ] Run Lighthouse + axe ก่อน — บันทึก baseline
- [ ] List ทุก issue

### Implement (Dev + Designer)
- [ ] ปรับ `public/<tool>.php` ทั้ง 7 (labels, aria)
- [ ] ปรับ design tokens (contrast)
- [ ] เพิ่ม skip links
- [ ] ปรับ focus styles

### Verify (QA + Designer)
- [ ] Lighthouse ≥ 95
- [ ] axe: 0 critical
- [ ] Keyboard test ทุกหน้า
- [ ] Screen reader test (NVDA + VoiceOver ถ้า possible)

### Docs
- [ ] Update `docs/standards/coding-standards.md` — a11y ตอน PR
- [ ] Update `docs/standards/security.md` — a11y security

## 🔗 Dependencies

- ทำหลัง ISSUE-002 (CSS refactor) — แก้ tokens ทีเดียว

## 📝 Notes

- ใช้ [axe DevTools](https://www.deque.com/axe/devtools/) browser extension
- ใช้ [Lighthouse CI](https://github.com/GoogleChrome/lighthouse-ci) ใน CI (Issue อื่น)
- ดู [WCAG 2.1 AA](https://www.w3.org/WAI/WCAG21/quickref/)

## 🔖 Labels

`feature`, `quality`, `a11y`, `wcag`
