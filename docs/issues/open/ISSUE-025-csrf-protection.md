# ISSUE-025: Add CSRF Protection

> **Type**: feature / security
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: M
> **Status**: Open

## 🎯 Background

MyAPIs เป็น stateless public API — แต่ POST endpoints (form data, JSON body) มีความเสี่ยง CSRF attack ถ้า deploy ใน shared hosting (cPanel) ที่ browser credentials อาจถูกใช้ร่านได้

> **Note**: ISSUE-010 เดิมตั้งใจเป็น CSRF แต่ content ที่เขียนไปคือ Secrets Management — แยกเป็น 2 issues: ISSUE-010 (secrets) + ISSUE-025 (CSRF — issue นี้)

## 👤 Owner

- Dev: เดฟ
- Security review: ออป

## 📦 Scope

### In Scope
- ✅ เพิ่ม `api/_includes/Csrf.php` (หรือ `Validator.php` extension) — issue + verify token
- ✅ Generate CSRF token per session (in-memory acceptable — stateless + no DB)
- ✅ Verify CSRF token on POST endpoints ทุกตัว
- ✅ Optional `?csrf_token=...` query string fallback สำหรับ tools ที่ไม่ใช้ form
- ✅ 403 Forbidden response ถ้า token missing/invalid
- ✅ Document วิธีใช้ใน API specs

### Out of Scope
- ❌ ไม่ใช้ cookie-based session (stateless — in-memory only)
- ❌ ไม่ persist token (no DB)
- ❌ ไม่แก้ GET endpoints (idempotent ไม่ต้องป้องกัน)

## ✅ Acceptance Criteria

- [ ] POST/PUT/PATCH endpoints ทุกตัวต้องการ valid CSRF token
- [ ] 403 Forbidden + structured error envelope เมื่อ token missing/invalid
- [ ] GET/OPTIONS/HEAD bypass CSRF (idempotent)
- [ ] Token expires ทุก 1 ชม. (in-memory TTL)
- [ ] Document ใน `docs/api-specs/*.md` ทุกไฟล์ที่รองรับ POST
- [ ] Lighthouse / security headers ไม่ break

## 🔗 Dependencies

- Blocked by: ISSUE-013 (shared classes — need Csrf class in api/_includes/)
- Related: ISSUE-010 (secrets management), FR-004 (CORS)

## 📝 Notes

- พิจารณาใช้ `random_bytes(32)` + `bin2hex()` สำหรับ token generation
- Token เก็บใน PHP process memory (ไม่ share ระหว่าง requests ใน PHP-FPM — ใช้ `Cache`/`APCu` ถ้ามี)
- ทางเลือก: Stateless double-submit cookie pattern (CSRF token = HMAC of session)

## 🔖 Labels

`feature`, `security`, `enhancement`, `api`
