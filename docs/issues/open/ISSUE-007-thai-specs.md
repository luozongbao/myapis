# ISSUE-007: Translate API Specs to Thai Language

> **Type**: feature / docs
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: L
> **Status**: Open

## 🎯 Background

ปัจจุบันเอกสาร API specs (`docs/api-specs/*.md`) ทั้งหมดเขียนเป็นภาษาอังกฤษ — แม้ว่า audience หลักของ MyAPIs จะเป็นคนไทย (ดู domain ของ `promptpay-qr-generator`)

## 👤 User Story

As a Thai developer,
I want API specs เป็นภาษาไทย,
So that อ่านง่าย ไม่ต้องแปลเอง

## 📦 Scope

### In Scope
- ✅ แปว `docs/api-specs/*.md` ทั้ง 7 ไฟล์เป็นภาษาไทย
- ✅ แปล `public/api-specs/*.php` ให้ตรงกัน
- ✅ `README.md` summary เป็นไทย
- ✅ i18n-aware (เก็บ EN เดิมไว้)

### Out of Scope
- ❌ ไม่แปล `docs/architecture/`, `docs/runbooks/`
- ❌ ไม่แปล code comments
- ❌ ไม่แปล error messages ฝั่ง API (JSON keys เป็น EN)

## ✅ Acceptance Criteria

- [ ] ไฟล์ `docs/api-specs/*.th.md` ทั้ง 7 มีอยู่
- [ ] มี `public/api-specs/<tool>.th.php` (หรือ i18n mechanism อื่น)
- [ ] README มี link `/specs/<tool>.th` หรือเลือกภาษาได้
- [ ] ภาษาไทยคำชัดถ้อย ไม่แปลก ๆ
- [ ] Technical terms ทับศัพท์ได้ (API, HTTP, JSON, parameter)

## 🔧 Technical Approach

### Option A: ไฟล์แยก (ง่าย)

```
docs/api-specs/
├── health-calculator.md      # EN
├── health-calculator.th.md   # TH (ใหม่)
├── ...
```

แล้ว `public/api-specs/`:
```
public/api-specs/
├── health-calculator.php     # route ไป MD
└── .th/
    └── health-calculator.php # route ไป MD.th
```

### Option B: i18n table ในไฟล์เดียว (cleaner)

```markdown
<!-- docs/api-specs/health-calculator.md -->
# Health Calculator API

## Description

::: en
Calculate BMI, BMR, daily intake, water intake.
:::

::: th
คำนวณ BMI, BMR, พลังงานที่ควรได้รับต่อวัน, น้ำที่ควรดื่มต่อวัน
:::
```

แต่ PHP render ต้อง parse Markdown ที่มี block แบบนี้ → ยาก

**แนะนำ: Option A** (ง่ายกว่า, MVP ได้)

### Markdown Layout

```markdown
# 🏥 Health Calculator API (เครื่องคำนวณสุขภาพ)

> API เวอร์ชัน 1.0 — เผยแพร่ มกราคม 2026

---

## 📖 ภาพรวม

API นี้ให้บริการคำนวณค่าสุขภาพพื้นฐาน...

## 🔌 Endpoint

`GET /api/health-calculator/`

## ⚙️ Parameters (พารามิเตอร์)

| ชื่อ | ประเภท | จำเป็น | คำอธิบาย |
|------|-------|-------|---------|
| type | string | ใช่ | ประเภทการคำนวณ: bmi/bmr/... |
...
```

## 📋 Tasks

### Translation (Designer)
- [ ] `health-calculator.th.md`
- [ ] `password-generator.th.md`
- [ ] `username-generator.th.md`
- [ ] `promptpay-qr-generator.th.md`
- [ ] `qr-code-generator.th.md`
- [ ] `fortune-teller.th.md`
- [ ] `randomizer.th.md`

### Render (Dev)
- [ ] `public/api-specs/<tool>.th.php` (7 ไฟล์)
- [ ] (Optional) Selector ภาษา ที่หัว spec page

### Docs
- [ ] Update `README.md` — เพิ่ม link ภาษาไทย
- [ ] Update `docs/README.md`

## 🔗 Dependencies

- ไม่มี

## 📝 Notes

- ใช้ style guide เดียวกับ `docs/standards/documentation.md`
- Technical terms ใช้ภาษาอังกฤษตรง ๆ (API, parameter, endpoint)
- ภาษาทั่วไปใช้ "แบบแผนทางการ" ไม่ casual

## 🔖 Labels

`feature`, `docs`, `i18n`, `thai`
