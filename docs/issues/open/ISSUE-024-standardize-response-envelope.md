# ISSUE-024: Standardize API Response Envelope

> **Type**: refactor / tech-debt
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: S
> **Status**: Open

## 🎯 Background

จาก ISSUE-013 prep: response envelopes ในแต่ละ tool **ไม่สอดคล้องกัน**:
- `success: true, message: ..., result: {...}` (บาง tools)
- `success: true, data: {...}` (บาง tools)
- `success: false, message: ..., error_code: ...` (บาง tools)
- Field naming: `message` vs `messages` vs `error_message`

**FR-003** (functional-requirements.md) กำหนด envelope ไว้แล้วแต่ยังไม่ได้ apply

## 👤 Owner

- Dev: เดฟ (bundle กับ ISSUE-013)

## 📦 Scope

### In Scope
- ✅ Apply FR-003 envelope ทุก 7 tools:
  - Success: `{success: true, data: {...}, timestamp: "ISO-8601"}`
  - Error: `{success: false, error: "CODE", message: "...", details: {...}, timestamp: "..."}`
- ✅ Field naming: `message` (ไม่ใช่ `messages` / `error_message`)
- ✅ HTTP status: 200/400/405/500 ตาม api-design.md §3
- ✅ `Content-Type: application/json; charset=UTF-8` ทุก response
- ✅ Update API specs ทั้ง 7 files ให้ตรงกับ new envelope

### Out of Scope
- ❌ ไม่เพิ่ม field ใหม่ (backward compat — เพิ่ม field ใหม่ต้องเป็น opt-in)
- ❌ ไม่เปลี่ยน HTTP status codes

## ✅ Acceptance Criteria

- [ ] ทุก 7 tools return envelope เดียวกัน
- [ ] Field naming consistent (`message`, ไม่ใช่ `messages`)
- [ ] `Content-Type` header ตั้งในทุก response (รวม error)
- [ ] API specs ทั้ง 7 files อัปเดตแล้ว
- [ ] `curl` test 7 endpoints — verify envelope structure
- [ ] Backward compat: tools เก่าที่อ่าน `result` → ยังได้ 404 (deprecation warning)

## 🔗 Dependencies

- Blocked by: ISSUE-013 (ApiResponse class)
- Related: FR-003, api-design.md §3, §5

## 🔖 Labels

`refactor`, `tech-debt`, `api`, `envelope`
