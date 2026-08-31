# Bug Issue Template

> Copy แล้วใช้สำหรับ bug

---

```markdown
# ISSUE-<id>: <Bug Title>

> **Type**: bug
> **Severity**: P0 | P1 | P2 | P3
> **Estimate**: S | M | L
> **Status**: Open

## 🐞 Bug

<อธิบายปัญหา สั้นกระชับ>

## 🔄 Steps to Reproduce

1. <Step 1>
2. <Step 2>
3. <Step 3>

## 🎯 Expected Behavior

<ที่คาดหวัง>

## ❌ Actual Behavior

<ที่เกิดขึ้นจริง>

## 🖼️ Evidence

```
<error log, screenshot, curl output>
```

## 🌍 Environment

- Deployment: Docker | Shared Hosting | VPS
- PHP version: 8.2.x
- Browser (ถ้าเกี่ยว): Firefox 102
- อื่น ๆ

## 🔍 Root Cause Analysis (optional, หลัง investigation)

<อธิบาย root cause หลัง debug>

## ✅ Acceptance Criteria

- [ ] Bug ไม่ reproduce ได้อีก
- [ ] เพิ่ม test (ถ้า possible)
- [ ] ไม่มี regression

## 🔧 Proposed Fix

<ร่าง fix>

## 🔗 Related Issues

- Related: #<id>
- Fixes: <PR link หลัง done>

## 🔖 Labels

`bug`, `<tool-name>`, `<severity>`
```
