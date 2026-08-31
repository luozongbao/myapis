# Feature Issue Template

> Copy แล้วใช้สำหรับ feature ใหม่

---

```markdown
# ISSUE-<id>: <Feature Title>

> **Type**: feature
> **Priority**: P0 | P1 | P2 | P3
> **MoSCoW**: Must | Should | Could | Won't
> **Estimate**: S | M | L | XL
> **Status**: Open

## 🎯 Background

<ปัญหา/โอกาส ที่ทำให้ต้องเพิ่ม feature นี้>

## 👤 User Story

As a <user type>,
I want <goal>,
So that <benefit>.

## 📦 Scope

### In Scope
- <สิ่งที่จะทำ>

### Out of Scope
- <สิ่งที่ไม่ทำ>

## ✅ Acceptance Criteria

- [ ] Criterion 1 — measurable, testable
- [ ] Criterion 2
- [ ] Criterion 3

## 🔧 Technical Approach

<ร่าง technical solution หรือ link spec>

### Files to Change
- `api/<tool>/index.php` — main API
- `public/<tool>.php` — UI
- `docs/api-specs/<tool>.md` — spec
- ...

## 📋 Tasks

### Design (Designer)
- [ ] Mockup/wireframe

### Analysis (SA)
- [ ] Spec/section in docs/api-specs

### Implementation (Dev)
- [ ] Code
- [ ] Self-test

### Review (PM/SA/Dev)
- [ ] Code review
- [ ] PR merge

### QA (QA)
- [ ] Acceptance test
- [ ] Regression test

## 🔗 Dependencies

- Blocked by: #<id> (ถ้ามี)
- Blocks: #<id> (ถ้ามี)
- Related: #<id> (ถ้ามี)

## 📝 Notes & Discussion

<reference, links, debate>

## 🔖 Labels

`feature`, `enhancement`, `<tool-name>`
```
