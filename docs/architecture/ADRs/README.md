# ADR-001: No Composer Dependency

> **Status**: Accepted
> **Date**: 2026-08-31
> **Deciders**: PM, SA, Dev
> **Context**: Initial architecture decision

---

## Context

MyAPIs ต้องเป็น project ที่ deploy ได้บน:
- Docker
- Shared hosting (Hostinger, cPanel)
- VPS (Nginx + PHP-FPM)
- PHP built-in server (dev)

ทุก deployment topology ต้องทำงานได้เหมือนกัน

## Decision

**ไม่ใช้** Composer + vendor dependencies

## Rationale

| Option | Pros | Cons |
|--------|------|------|
| **No Composer** ✅ | • Portable (ทุก topology ใช้ได้)<br>• No build step<br>• No vendor size<br>• ไม่ต้อง SSH เพื่อ composer install | • ต้องเขียน utilities เอง<br>• ต้อง validate ด้วยตัวเอง<br>• ต้อง dev รู้ PHP stdlib ดี |
| Composer + vendor | • Library พร้อมใช้ | • Shared hosting ไม่ได้ (no CLI)<br>• ต้อง build step<br>• Version conflict<br>• ไม่ portable |

## Consequences

### ✅ Positive
- Project เป็น `clone-and-run` — `git clone && docker compose up` พอ
- Shared hosting deploy ได้ (zip + upload ผ่าน cPanel)
- Image เล็ก (ไม่มี vendor/)
- Cold start เร็ว (ไม่ต้อง load autoloader)

### ❌ Negative
- ต้อง implement utility เอง (JSON parsing, crypto, etc.)
- ต้อง implement validation เอง
- Dev ต้องรู้จัก PHP standard library

### ⚠️ Mitigations
- ใช้ PHP 8.2 features (readonly, enums, named args)
- Implement class-based utilities ใน `api/_includes/`
- Add `composer.json` แค่สำหรับ dev (PHPUnit) ในอนาคต — ไม่กระทบ deployment

---

# ADR-002: Stateless Architecture (No Database)

> **Status**: Accepted
> **Date**: 2026-08-31

## Context

MyAPIs ให้บริการ utility APIs (BMI, password, QR) — แต่ละ call คำนวณทันที ไม่ต้อง persist

## Decision

**ไม่มี database** (Postgres, MySQL, SQLite — none of them)

## Rationale

| Concern | Why not DB |
|---------|-----------|
| User data | ❌ ไม่มี — เป็น public utility |
| Session | ❌ ไม่มี — stateless |
| Analytics | ❌ PHP ไม่เก็บ — external (Umami/GA4) |
| Business data | ❌ คำนวณ on-the-fly |
| Hot data | ✅ อยู่ใน PHP array / JSON file (fortune, theme words) |

## Consequences

### ✅ Positive
- Deploy ง่าย — ไม่ต้อง setup DB
- Backup zero — ไม่มี data
- Scale แนวนอน — แค่เพิ่ม PHP container
- ไม่มี SQL injection (no SQL)

### ❌ Negative
- ไม่ track request log (privacy ✅ + observability ❌)
- ไม่มี rate limit persistence (Issue: [`ISSUE-001`](../issues/open/ISSUE-001-add-rate-limiting.md))

---

# ADR-003: Spec in Markdown, Not OpenAPI

> **Status**: Accepted
> **Date**: 2026-08-31

## Context

ต้องเลือก format สำหรับ API documentation:
- Markdown (custom)
- OpenAPI/Swagger
- Both

## Decision

**Markdown เป็น primary** + `OpenAPI` ในอนาคต (Issue: [`ISSUE-008`](../issues/open/ISSUE-008-openapi-spec.md))

## Rationale

| Format | Pros | Cons |
|--------|------|------|
| **Markdown** ✅ | • Easy to write<br>• Easy to read on GitHub<br>• No tool required<br>• Familiar for dev | • No auto-SDK gen<br>• No try-it-now<br>• No auto-validate |
| OpenAPI/Swagger | • try-it-now UI<br>• SDK gen<br>• Validation | • YAML ยากเขียน<br>• PHP ไม่ได้ framework |

## Consequences

### ✅ Positive
- Spec maintain ง่าย (แก้ MD อย่างเดียว)
- ไม่ต้องเรียนรู้ OpenAPI syntax
- Render ตรง ๆ ใน `public/api-specs/`

### ❌ Negative
- ไม่มี try-it-now (Issue ที่จะเพิ่ม)
- ต้อง manual sync ระหว่าง MD กับ `public/api-specs/*.php`

---

# ADR-004: PHP 8.2 + Alpine

> **Status**: Accepted
> **Date**: 2026-08-31

## Context

ต้องเลือก PHP version + base image

## Decision

**PHP 8.2-FPM (Alpine)** เป็น primary

## Rationale

| Factor | PHP 8.0 (EOL) | PHP 8.1 | **PHP 8.2 ✅** | PHP 8.3 |
|--------|-------|-------|---------|-------|
| EOL | Nov 2023 | Nov 2024 | **Dec 2026** | Nov 2027 |
| New features | - | enums | readonly classes | typed consts |
| Performance | baseline | +5% | +7% | +10% |
| Composer compat | OK | OK | OK | OK |

→ PHP 8.2 + Alpine (ลด image size)

## Consequences

- ✅ ใช้ features ใหม่ (enums, readonly)
- ⚠️ ต้อง verify shared hosting ตั้ง 8.1+ (ส่วนใหญ่ OK ปัจจุบัน)
- ❌ ใช้ PHP 8.3 features ไม่ได้
