# ISSUE-010: Add Secrets Management Documentation

> **Type**: docs
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: S
> **Status**: Open

## 🎯 Background

ปัจจุบันมี secret อยู่ 2 ที่:
- `.env` (Docker)
- `public/config.php` (shared hosting)

แต่ไม่มีเอกสารกลางที่อธิบาย:
- ❌ มี secret อะไรบ้าง
- ❌ เก็บที่ไหน
- ❌ rotate ยังไง
- ❌ ถ้ารั่วทำอย่างไร

Issue นี้เขียน `docs/runbooks/secrets-management.md` และ cross-link กับที่อื่น

## 👤 User Story

As a new developer,
I want เอกสาร secrets management,
So that รู้ว่าจัดการ config/secrets อย่างไร

## 📦 Scope

### In Scope
- ✅ `docs/runbooks/secrets-management.md`
- ✅ ระบุ secret ทั้งหมด + ที่เก็บ + rotate plan
- ✅ Emergency rotation runbook
- ✅ Update `.gitignore` (ถ้าขาด)

### Out of Scope
- ❌ ไม่ implement secret manager (HashiCorp Vault)

## ✅ Acceptance Criteria

- [ ] Doc ครอบคลุม secret ทั้งหมด
- [ ] Step-by-step rotation
- [ ] Pre-commit hook verify no secrets (optional)
- [ ] Linked จาก README และ deployment docs

## 📋 Tasks

### Doc (DevOps)
- [ ] สร้าง `docs/runbooks/secrets-management.md`
- [ ] Update `docs/runbooks/deployment.md` (link)
- [ ] Update `docs/standards/security.md` (link)
- [ ] Update `README.md` (link)

### Tooling (Dev)
- [ ] ตรวจ `.gitignore` cover `.env`, `public/config.php`
- [ ] (Optional) `git-secrets` hook

## 🔖 Labels

`docs`, `security`, `devops`
