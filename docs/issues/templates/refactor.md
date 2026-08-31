# Refactor Issue Template

> Copy แล้วใช้สำหรับ refactor / tech debt

---

```markdown
# ISSUE-<id>: <Refactor Title>

> **Type**: refactor
> **Priority**: P2 | P3
> **MoSCoW**: Should | Could
> **Estimate**: S | M | L
> **Status**: Open

## 🎯 Why Refactor

<อธิบาย why — ไม่ใช่ what>

ตัวอย่าง:
- Code smell: <เช่น duplication, long method>
- Maintainability: <เช่น hard to test>
- Performance: <เช่น inefficient>
- Tech debt: <เช่น จะ block feature นี้>

## 📏 Current State

<อธิบาย code ปัจจุบัน — มี snippet>

## 🎯 Target State

<อธิบาย code หลัง refactor — มี snippet>

## 🚫 Out of Scope

- ❌ ไม่เปลี่ยน behavior
- ❌ ไม่เพิ่ม feature
- ❌ ไม่ break API

## ✅ Acceptance Criteria

- [ ] Code ตาม target state
- [ ] Tests ยังผ่าน (ถ้ามี)
- [ ] API response เหมือนเดิม (byte-level ถ้า possible)
- [ ] ไม่มี regression
- [ ] `php -l` ผ่าน

## 🔧 Tasks

- [ ] เขียน refactor
- [ ] Compare response (before/after)
- [ ] PR + review

## 🔗 Related Issues

- Refactor for: #<id> (issue ที่ block)
- Required by: <new feature>

## 🔖 Labels

`refactor`, `tech-debt`
```
