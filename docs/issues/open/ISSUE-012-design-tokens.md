# ISSUE-012: Design Tokens + Component Library

> **Type**: feature / design-system
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: M
> **Status**: Open

## 🎯 Background

Foundation สำหรับ ISSUE-002 (Extract CSS) — ต้องมี design system ก่อน refactor CSS

## 👤 Owner

- Designer: ยู
- SA: review spec
- Dev: เดฟ review implementation

## 📦 Scope

### In Scope
- ✅ `public/assets/css/design-tokens.css` (colors, spacing, typography, radius, shadow)
- ✅ `public/assets/css/base.css` (reset, typography, body)
- ✅ `public/assets/css/components.css` (buttons, forms, cards, alerts)
- ✅ `public/assets/css/tool-page.css` (layout ของ tool pages)
- ✅ Component spec document — `docs/designs/components.md`

### Out of Scope
- ❌ ไม่ migrate tool pages (ทำใน ISSUE-002)
- ❌ ไม่เพิ่ม dark mode (Sprint 2+)
- ❌ ไม่ใช้ CSS framework (Bootstrap, Tailwind)

## ✅ Acceptance Criteria

- [ ] design-tokens.css มี CSS variables ครบ (color, spacing, typography, radius, shadow)
- [ ] base.css reset + body styles
- [ ] components.css มี classes: .btn, .form-field, .input, .card, .alert
- [ ] tool-page.css มี layout สำหรับ tool pages
- [ ] docs/designs/components.md มี diagram + usage
- [ ] Lighthouse a11y ≥ 90 (verify กับ landing page)
- [ ] Contrast ratio ≥ 4.5:1 ทุก text/background pair

## 🔗 Dependencies

- Blocked by: ISSUE-011 (audit)
- Blocks: ISSUE-002 (Extract CSS)
- Related: ISSUE-009 (a11y)

## 📝 Notes

- ใช้ semantic CSS variables (--color-primary, --space-3, etc.)
- Mobile-first responsive
- รองรับ TH/EN/ZH fonts

## 🔖 Labels

`feature`, `design-system`, `foundation`, `css`
