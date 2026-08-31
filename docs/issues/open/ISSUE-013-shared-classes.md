# ISSUE-013: Move Shared Classes to api/_includes/

> **Type**: refactor / tech-debt
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบัน `api/<tool>/index.php` แต่ละไฟล์อาจมี code ซ้ำ (CORS headers, error handler, validation helpers, analytics) — ควร extract ไปไว้ใน `api/_includes/` ตามที่ระบุใน ADR-001 mitigation

## 👤 Owner

- SA: ซ่า (audit + spec)
- Dev: เดฟ (implementation)

## 📦 Scope

### In Scope
- ✅ สร้าง `api/_includes/` folder
- ✅ Extract CORS handler → `api/_includes/Cors.php`
- ✅ Extract error handler → `api/_includes/ErrorHandler.php`
- ✅ Extract validation helpers → `api/_includes/Validator.php`
- ✅ Extract analytics pre-pend logic → `api/_includes/Analytics.php`
- ✅ Update แต่ละ `api/<tool>/index.php` ให้ใช้ shared classes
- ✅ Update `docs/architecture/directory-structure.md`

### Out of Scope
- ❌ ไม่แก้ business logic (เฉพาะ shared utilities)
- ❌ ไม่เปลี่ยน public API contract
- ❌ ไม่ทำ autoloader (ใช้ require เหมือนเดิม — ไม่มี Composer)

## ✅ Acceptance Criteria

- [ ] `api/_includes/Cors.php` — handle OPTIONS, CORS headers (single source)
- [ ] `api/_includes/ErrorHandler.php` — exception → JSON response
- [ ] `api/_includes/Validator.php` — input validation helpers (type, range, sanitize)
- [ ] `api/_includes/Analytics.php` — analytics pre-pend logic (Docker path)
- [ ] `api/<tool>/index.php` ทุกไฟล์ refactor ใช้ shared classes
- [ ] ไม่มี code duplication (DRY)
- [ ] ทุก endpoint ทำงานเหมือนเดิม (regression test)
- [ ] ไม่ break backward compat
- [ ] `php -l` ผ่านทุกไฟล์

## 🔗 Dependencies

- Blocked by: ISSUE-011 (audit — ต้องรู้ว่ามี code ซ้ำตรงไหน)
- Blocks: ISSUE-001 (rate-limit จะใช้ shared classes)

## 📝 Notes

- ใช้ `require_once __DIR__ . "/_includes/Cors.php"` pattern
- ไม่ใช้ namespace + autoloader (keep simple — ไม่มี Composer)
- 1 PR หรือแยกหลาย PR — เดฟ + ซ่า ตกลงกัน

## 🔖 Labels

`refactor`, `tech-debt`, `foundation`, `architecture`
