# ISSUE-011: Pre-sprint Audit & Baseline

> **Type**: chore / docs
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: S
> **Status**: Open

## 🎯 Background

ก่อนเริ่ม Sprint 1 implementation ต้องรู้ baseline ของ project ก่อน:
- File layout ปัจจุบันตรงตาม spec หรือไม่?
- Security headers + config ตรงตาม security.md หรือไม่?
- Pages ปัจจุบันมี state/issues อะไรบ้าง?

## 👤 Owner

- PM: พีม

## 📦 Scope

### In Scope
- ✅ File inventory — เทียบ current state กับ `docs/architecture/directory-structure.md`
- ✅ Security baseline — verify security headers, HTTPS config, .env handling
- ✅ Page inventory — list 7 tools × (API + UI + Spec) + landing, identify dead/duplicate files
- ✅ Gap analysis report — สรุป gap ที่ต้องปิดใน Sprint 1

### Out of Scope
- ❌ ไม่แก้ code (audit อย่างเดียว)

## ✅ Acceptance Criteria

- [ ] `docs/audit/file-inventory.md` created
- [ ] `docs/audit/security-baseline.md` created
- [ ] `docs/audit/page-inventory.md` created
- [ ] `docs/audit/gap-analysis.md` created (links to existing issues + flags new ones)
- [ ] PM reviews + approves ก่อนเริ่ม Sprint 1 implementation

## 🔗 Dependencies

- Blocks: ISSUE-001, ISSUE-002, ISSUE-009, ISSUE-010, ISSUE-012, ISSUE-013

## 📝 Notes

- ทำก่อน Sprint 1 implementation เริ่ม
- Output เป็น audit reports ที่ agents อ้างอิงได้

## 🔖 Labels

`chore`, `audit`, `foundation`
