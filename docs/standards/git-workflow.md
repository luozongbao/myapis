# 🌿 Git Workflow

> มาตรฐานการใช้ Git สำหรับ MyAPIs — ทุกคนในทีมต้องทำตาม

---

## 1. Branching Strategy

ใช้ **GitHub Flow** (lightweight, เหมาะกับโปรเจกต์ขนาดเล็ก)

```
main          ─────────────●──────●──────●─────→ (always deployable)
                              \    / \    /
feature/      ─────────●──────●      \  /
                                \      \/
hotfix/                   ──●──────●──────→
                              \
docs/         ─────●───●─────→
```

### Branch Types

| Prefix | ใช้สำหรับ | Merge ไป | ลบหลัง merge |
|--------|-----------|---------|------------|
| `main` | Production code (always deployable) | - | - |
| `feature/<id>-<slug>` | Feature ใหม่ / Issue ใหม่ | main | ✅ |
| `fix/<id>-<slug>` | Bug fix | main | ✅ |
| `docs/<slug>` | Documentation only | main | ✅ |
| `hotfix/<slug>` | Emergency fix (production) | main | ✅ |
| `release/<version>` | Release prep (changelog, version bump) | main | ✅ |

### ตัวอย่าง
```
feature/issue-042-add-bmi-japanese
fix/issue-058-promptpay-target-validation
docs/update-readme-shared-hosting
hotfix/patch-fortune-file-missing
release/2.5.0
```

---

## 2. Commit Message

### Format (Conventional Commits — lightweight version)

```
<type>(<scope>): <subject>

<body (optional)>

<footer (optional)>
```

### Types

| Type | ใช้เมื่อ |
|------|---------|
| `feat` | Feature ใหม่ |
| `fix` | Bug fix |
| `docs` | Documentation only |
| `style` | Code style (formatting, ไม่เปลี่ยน logic) |
| `refactor` | Code restructure (ไม่เปลี่ยน behavior) |
| `test` | เพิ่ม/แก้ test |
| `chore` | Build, deps, tooling |
| `perf` | Performance improvement |

### Subject
- ไม่เกิน **72 characters**
- Lowercase
- ไม่มี period ท้าย
- ใช้ imperative ("add" ไม่ใช่ "added")

### Body
- อธิบาย **why** ไม่ใช่ what
- Wrap ที่ 100 characters
- แยกจาก subject ด้วย blank line

### Footer
- Reference issue: `Refs: #42` หรือ `Closes: #42`

### ตัวอย่าง

```
feat(health-calculator): add Japanese language support

Add BMI category labels and advice in Japanese (ja).
Reference values follow the Japan Society for the Study of Obesity (JASSO)
guidelines which differ slightly from WHO standards.

Closes: #42
```

```
fix(promptpay): validate target before CRC calculation

Previously, sending an empty target caused a fatal error during
CRC computation. Now returns HTTP 400 with clear error message.

Fixes: #58
```

---

## 3. Pull Request

### Title
- ใช้ commit message style (Conventional Commits)
- ตัวอย่าง: `feat(health-calculator): add Japanese language support`

### Description Template

```markdown
## 🎯 What

<อธิบายสั้น ๆ ว่าทำอะไร>

## 📋 Why

<อธิบายเหตุผล / link issue>

## 🔧 How

<อธิบายการเปลี่ยนแปลงหลัก>

## ✅ Checklist

- [ ] Tests pass (`php -l` on changed files)
- [ ] Self-tested with curl
- [ ] Updated `docs/api-specs/<tool>.md` (ถ้าเปลี่ยน API)
- [ ] Updated `README.md` (ถ้าเพิ่ม feature)
- [ ] Updated `RELEASE.md` (ถ้า breaking change)
- [ ] No new env vars (or updated `example.env`)
- [ ] PR linked to Issue

## 📸 Screenshots (ถ้าเป็น UI)

<paste screenshots>
```

### Reviewers
- ต้องมี **≥ 1 reviewer** approve ก่อน merge
- **PM** ต้อง approve ถ้าเป็น feature / breaking change
- **SA** ต้อง approve ถ้าเป็น API change

### Merge
- ใช้ **Squash and merge** (commit เดียวใน main)
- หรือ **Rebase and merge** (ถ้า branch สะอาด)

---

## 4. Issue Linking

ทุก PR ต้อง link กับ Issue ที่เกี่ยวข้อง:

```
Closes: #42         → auto-close issue เมื่อ merge
Refs: #58           → link แต่ไม่ auto-close
Blocked by: #61     → รอ issue อื่นก่อน
```

---

## 5. Tags & Releases

ใช้ [Semantic Versioning](https://semver.org/):

```
v<MAJOR>.<MINOR>.<PATCH>

MAJOR: Breaking change
MINOR: New feature (backward compatible)
PATCH: Bug fix (backward compatible)
```

ตัวอย่าง: `v2.5.0`, `v2.5.1`, `v3.0.0`

ดู changelog ที่ [`RELEASE.md`](../../RELEASE.md)

---

## 6. กฎการ Merge

| ข้อ | รายละเอียด |
|-----|-----------|
| ✅ Merge ได้ | เมื่อ CI ผ่าน, review ≥ 1, conflict resolved, linked issue |
| ❌ Merge ไม่ได้ | Force-push หลัง review, skip review, unlinked change |
| ⚠️ ต้องระวัง | แก้ไฟล์เยอะ (>500 lines) ควร split PR |

---

## 7. ไฟล์ที่ห้าม Commit

| ไฟล์ | เหตุผล |
|------|--------|
| `.env` | มี credentials/secrets |
| `public/config.php` | มี analytics IDs |
| `*.log` | log files |
| `vendor/` | ไม่มี (ไม่ใช้ Composer) |
| `node_modules/` | ไม่มี (ไม่ใช้ Node) |

ตรวจสอบ `.gitignore` ที่ root

---

## 8. Conflict Resolution

ถ้าเกิด conflict:

1. `git fetch origin`
2. `git rebase origin/main` (preferred over merge)
3. Resolve conflicts locally
4. Force-push เฉพาะ branch ส่วนตัว (ไม่ force-push main)
5. Re-test ก่อน merge
