# ISSUE-014: Remove Empty `api/stats/` Directory

> **Type**: chore / cleanup
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: XS
> **Status**: Open

## 🎯 Background

`api/stats/` directory มีอยู่แต่**ว่างเปล่า** — ไม่มี `index.php` implement ไม่มี UI ไม่มี spec ผู้ใช้ request `/api/stats/` ได้ 404

ที่ประชุม PM + user ตัดสินแล้วว่า **stats ไม่ใช่ tool จริง** (ลืมไว้ตอน initial structure) → ลบทิ้ง

## 👤 Owner

- Dev: เดฟ

## 📦 Scope

### In Scope
- ✅ `rmdir api/stats/` (empty dir)
- ✅ `git rm api/stats` (ถ้า tracked)
- ✅ Verify ไม่มี reference ใน code/docs/README

### Out of Scope
- ❌ ไม่ implement stats tool (Sprint 2+ ถ้าต้องการ)
- ❌ ไม่แก้ landing page (ไม่มี card อ้าง stats อยู่แล้ว)

## ✅ Acceptance Criteria

- [ ] `api/stats/` directory หายไป
- [ ] `grep -r "stats" api/ public/ docs/` ไม่เจอ active reference
- [ ] PR commit message: `chore(cleanup): remove empty api/stats/ directory`

## 🔗 Dependencies

- Blocked by: none
- Blocks: none

## 📝 Notes

- Bundle กับ ISSUE-013 (shared classes refactor) PR เดียวกันได้

## 🔖 Labels

`chore`, `cleanup`, `tech-debt`
