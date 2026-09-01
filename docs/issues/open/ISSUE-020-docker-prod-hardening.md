# ISSUE-020: Docker Compose Production Hardening

> **Type**: chore / devops / security
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: S
> **Status**: Open

## 🎯 Background

จาก audit (§ISSUE-011): docker-compose.yml มี production-grade basics (healthcheck, restart policy) แต่**ขาด hardening**:
- ไม่มี `security_opt: no-new-privileges:true`
- ไม่มี resource limits (`cpus`, `mem`)
- ไม่มี `read_only: true` root filesystem
- Volume mount `./:/var/www/html` เป็น `rw` (ควรเป็น `ro` ใน prod)

## 👤 Owner

- DevOps: ออป

## 📦 Scope

### In Scope
- ✅ เพิ่ม `security_opt: no-new-privileges:true` ทุก service
- ✅ เพิ่ม `mem_limit: 256m` (PHP) / `128m` (Nginx)
- ✅ เพิ่ม `cpus: 0.5` (PHP) / `0.25` (Nginx)
- ✅ เปลี่ยน volume mount เป็น `ro` (อ่านอย่างเดียว) สำหรับ code
- ✅ Document override pattern ใน `example.env` (dev = `rw`, prod = `ro`)

### Out of Scope
- ❌ ไม่เปลี่ยน image base (Alpine stays)
- ❌ ไม่ใช้ rootless mode (Sprint 2+)

## ✅ Acceptance Criteria

- [ ] `docker compose config` valid ทั้ง dev และ prod profile
- [ ] `docker compose up -d` (dev) ยังทำงานปกติ
- [ ] Container ไม่สามารถ escalate privileges (verify with `docker exec`)
- [ ] Memory/CPU limits apply (verify with `docker stats`)
- [ ] Code volume mount เป็น read-only ใน prod (test: `touch /var/www/html/test` fail)

## 🔗 Dependencies

- Blocked by: none
- Related: ISSUE-004 (CI/CD)

## 🔖 Labels

`chore`, `devops`, `security`, `docker`, `production`
