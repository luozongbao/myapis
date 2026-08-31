# 📋 Issue Workflow

> วิธีการจัดการ Issue ใน MyAPIs

---

## Issue Lifecycle

```
       ┌────────┐
       │ Backlog │ (ยังไม่ prioritize)
       └───┬────┘
           │ (PM review + MoSCoW)
           ▼
       ┌────────┐
       │  Open  │ (priority set + มีคนรับ)
       └───┬────┘
           │ (assignee start)
           ▼
       ┌────────────┐
       │ In Progress│ (กำลังทำ)
       └───┬────────┘
           │
       ┌───┴────────────┐
       ▼                ▼
   ┌──────────┐    ┌──────────┐
   │  Review  │    │  Blocked │ (รอ dependency)
   └────┬─────┘    └──────────┘
        │
        │ (approved)
        ▼
   ┌──────────┐
   │   Done   │ (merge to main)
   └──────────┘
```

---

## MoSCoW Prioritization

PM ใช้ MoSCoW framework จัด priority:

| Label | Meaning | When |
|-------|---------|------|
| `Must` | ทำใน sprint นี้ (1-2 สัปดาห์) | Critical path |
| `Should` | ทำใน sprint ถัดไป | Important |
| `Could` | ทำเมื่อมีเวลา | Nice to have |
| `Won't` | ไม่ทำในขณะนี้ | Out of scope |

---

## Issue Types (Labels)

| Label | ใช้เมื่อ | Template |
|-------|---------|---------|
| `feature` | เพิ่ม functionality ใหม่ | [`templates/feature.md`](templates/feature.md) |
| `bug` | แก้ bug | [`templates/bug.md`](templates/bug.md) |
| `refactor` | ปรับ code โดยไม่เปลี่ยน behavior | [`templates/refactor.md`](templates/refactor.md) |
| `docs` | เพิ่ม/แก้ documentation | [`templates/docs.md`](templates/docs.md) |
| `security` | ปรับปรุง security | (ใช้ `feature` template + label) |
| `tech-debt` | Clean up code | (ใช้ `refactor` template + label) |

---

## Priority Levels

| Level | Meaning | Timeline |
|-------|---------|----------|
| `P0 - Critical` | Production down / security | ≤ 24 ชม. |
| `P1 - High` | Important user-facing | ≤ 1 สัปดาห์ |
| `P2 - Medium` | Nice-to-have | ≤ 1 เดือน |
| `P3 - Low` | Future | ตามแต่ |

---

## Definition of Done (DoD)

ทุก Issue ต้องผ่านเงื่อนไขเหล่านี้ก่อนเปลี่ยนเป็น `Done`:

### 1. Code
- [ ] Code เขียนเสร็จ + `php -l` ผ่าน
- [ ] Follow style guide ([`coding-standards.md`](../standards/coding-standards.md))
- [ ] Self-tested ด้วย curl/browser
- [ ] ไม่มี debug code (var_dump, dump, ...)

### 2. Documentation
- [ ] ถ้าเปลี่ยน API → update `docs/api-specs/`
- [ ] ถ้าเปลี่ยน env var → update `example.env`
- [ ] ถ้าเป็น breaking change → update `RELEASE.md`

### 3. Process
- [ ] PR linked to Issue (`Closes: #N`)
- [ ] PR review approved ≥ 1 คน
- [ ] CI pass (เมื่อมี)
- [ ] Branch merged + deleted

### 4. Verify
- [ ] Smoke test ผ่าน
- [ ] ไม่มี regression
- [ ] ถ้า deploy → update deployment

---

## Workflow Steps (1 Issue ทำยังไง)

### 1. สร้าง Issue
```bash
cp docs/issues/templates/feature.md docs/issues/open/ISSUE-XXX-slug.md
$EDITOR docs/issues/open/ISSUE-XXX-slug.md
```

### 2. PM Review + Assign
- ใส่ MoSCoW label
- ใส่ Priority
- Assign owner (Designer / SA / Dev / DevOps / QA)

### 3. ทำงาน
```bash
git checkout -b feature/issue-XXX-slug main
# ... code ...
git commit -m "feat(...): ..."
git push origin feature/issue-XXX-slug
```

### 4. Create PR
- Title = commit message
- Body ใช้ PR template ใน [`git-workflow.md`](../standards/git-workflow.md)
- Reference: `Closes: ISSUE-XXX`

### 5. Review + Merge
- Reviewer approve
- Squash/rebase merge
- Branch deleted

### 6. Close Issue
- Issue ถูกย้าย `docs/issues/open/` → `docs/issues/done/`
- Tag ใส่ PR link + commit hash

---

## KPIs ที่ติดตาม

PM จะ track:

| KPI | Target |
|-----|--------|
| Open issues ใน sprint | ≤ 5 |
| Average time-to-merge (P1) | ≤ 7 วัน |
| PR review turnaround | ≤ 2 วัน |
| Defect escape rate | ≤ 5% |
| Issue backlog size | ≤ 20 items |

---

## Folder Structure

```
docs/issues/
├── README.md (file นี้)
├── templates/
│   ├── feature.md
│   ├── bug.md
│   ├── refactor.md
│   └── docs.md
├── open/
│   ├── ISSUE-001-add-rate-limiting.md
│   ├── ISSUE-002-extract-css.md
│   └── ...
└── done/
    ├── ISSUE-000-set-up-docs.md
    └── ...
```

---

## แหล่งอ้างอิง

- [`standards/git-workflow.md`](../standards/git-workflow.md) — Git/PR workflow
- [`standards/documentation.md`](../standards/documentation.md) — วิธีเขียน Issue
- [`prompts/pm-prompt.md`](../../prompts/pm-prompt.md) — PM responsibility
