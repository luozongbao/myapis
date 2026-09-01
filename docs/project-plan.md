# 🚀 Project Plan & Master Playbook

> **คู่มือสั่งงาน MyAPIs** — ทุก phase มี prompt พร้อม copy ใช้กับ AI agent / ทีมงาน

เอกสารนี้เขียนสำหรับ **Project Manager (PM)** ที่ต้องขับเคลื่อนโปรเจคตั้งแต่เริ่มจน ship โดยแต่ละขั้นจะบอกว่า:
1. ต้องทำอะไร (Objective)
2. ใครรับผิดชอบ (Owner)
3. Prompt ที่จะ copy ไปวางใน chat / task (ใช้ได้ทันที)
4. Deliverable / DoD (Definition of Done)
5. เวลาที่ควรใช้ (Time budget)

---

## 🧭 Workflow ภาพรวม

```
Phase 0  Discovery       (PM)              ─→  Vision & Scope
   ↓
Phase 1  Architecture    (SA + DevOps)     ─→  Stack, ADRs, Folder layout
   ↓
Phase 2  Spec & Design   (SA + Designer)   ─→  API specs, Mockups, Tokens
   ↓
Phase 3  Implementation  (Dev)             ─→  Code per spec
   ↓
Phase 4  Hardening       (Dev + DevOps)    ─→  Tests, Perf, Security
   ↓
Phase 5  QA & UAT        (QA)              ─→  Test reports, Sign-off
   ↓
Phase 6  Deploy          (DevOps)          ─→  Staging → Production
   ↓
Phase 7  Monitor         (DevOps + PM)     ─→  Metrics, Feedback loop
```

> **กฎเหล็ก**: ห้ามข้าม phase — ทุก phase ต้องมี Deliverable + DoD ก่อนไป phase ถัดไป

---

## 📂 อ้างอิงเอกสาร

| ประเภท | Path |
|--------|------|
| Prompts ทั้งหมด | [`../prompts/`](../prompts/) |
| Requirements | [`requirements/`](requirements/) |
| Architecture | [`architecture/`](architecture/) |
| API Specs | [`api-specs/`](api-specs/) |
| Standards | [`standards/`](standards/) |
| Runbooks | [`runbooks/`](runbooks/) |
| Issues | [`issues/`](issues/) |

---

# 🟣 Phase 0 — Discovery (PM)

> **เวลา**: 1–2 วัน
> **เป้าหมาย**: ทุกคนเห็นภาพเดียวกันว่า "ทำอะไร ทำไม ใครใช้"

## 0.1 สิ่งที่ต้องทำ

- [ ] เขียน/ทบทวน Product Brief — [`requirements/product-brief.md`](requirements/product-brief.md)
- [ ] ระบุ Target audience (3–5 personas)
- [ ] ระบุ Business Objectives (3–5 ข้อ)
- [ ] ระบุ Out-of-scope (สิ่งที่จะไม่ทำ)
- [ ] ตั้ง Success Metrics (KPIs)

## 0.2 Prompt สำหรับ PM (คุณ)

````text
คุณคือ PM ของ MyAPIs project

หน้าที่ของคุณ:
1. ช่วยฉันเขียน Product Brief ใน `docs/requirements/product-brief.md`
2. ระบุ 3-5 personas ที่ชัดเจน (เช่น Indie Developer, Student, SMB Owner, Hobbyist Maker)
3. เขียน Business Objectives ที่วัดผลได้ (เช่น "Reach 10K API calls/day ภายใน 6 เดือน")
4. ระบุ Success Metrics (KPIs) ทั้ง leading & lagging
5. กำหนด Out-of-scope ที่ชัดเจน

Context:
- Project: MyAPIs (PHP 8.2, Nginx, Docker)
- Tooling ปัจจุบัน: 7 tools (health-calculator, password-generator, username-generator,
  promptpay-qr-generator, qr-code-generator, fortune-teller, randomizer)
- โปรเจคนี้ deploy ได้ทั้ง Docker / Shared hosting / VPS

ผลลัพธ์ที่ต้องการ: Product Brief Markdown ที่อ่านได้ใน 5 นาที
ทุก section มี actionable content (ไม่ใช่ placeholder)
````

## 0.3 Deliverable

- `docs/requirements/product-brief.md` (filled)
- `docs/requirements/functional-requirements.md` (skeleton)

## 0.4 DoD

- [ ] Product Brief ผ่าน review จาก stakeholders
- [ ] KPIs ตัวเลขชัดเจน (มี target + deadline)
- [ ] Out-of-scope ตกลงร่วมกัน

---

# 🔵 Phase 1 — Architecture (SA + DevOps)

> **เวลา**: 2–4 วัน
> **เป้าหมาย**: รู้ stack, รู้ folder, มี ADRs

## 1.1 สิ่งที่ต้องทำ

- [ ] เขียน Architecture Overview — [`architecture/overview.md`](architecture/overview.md)
- [ ] เขียน Directory Structure — [`architecture/directory-structure.md`](architecture/directory-structure.md)
- [ ] เขียน Deployment topology — [`architecture/deployment.md`](architecture/deployment.md)
- [ ] เขียน ADRs (สำหรับทุก technical decision ใหญ่)
- [ ] ตั้ง Stack (PHP version, framework choice, DB หรือไม่มี)

## 1.2 Prompt สำหรับ SA

````text
คุณคือ System Analyst ของ MyAPIs project

หน้าที่ของคุณ:
1. ตรวจสอบ `docs/architecture/overview.md` และ `docs/architecture/directory-structure.md`
2. ตรวจสอบ ADRs ที่มีอยู่ใน `docs/adr/` (ถ้ายังไม่มี เขียนใหม่)
3. เขียน ADR ใหม่สำหรับ decision ที่ยังขาด:
   - เลือก PHP version + extension list
   - เลือก frontend approach (vanilla JS vs Alpine vs HTMX)
   - เลือก analytics provider
   - เลือก secrets management
4. ตรวจสอบว่าทุก component มีเหตุผลรองรับ (ไม่ใช่ "เพราะชอบ")

Context:
- ทีมเล็ก (1-3 คน), budget จำกัด
- ต้อง deploy ได้ทั้ง Docker + shared hosting + VPS
- ไม่มี database (stateless)
- No Composer (ตาม ADR-001)

ผลลัพธ์:
- ADR แต่ละตัวไม่เกิน 1 หน้า
- Format: Context → Decision → Consequences
- อัปเดต `docs/architecture/overview.md` ให้สะท้อน ADRs ล่าสุด
````

## 1.3 Prompt สำหรับ DevOps

````text
คุณคือ DevOps Engineer ของ MyAPIs project

หน้าที่ของคุณ:
1. ตรวจสอบ `Dockerfile`, `docker-compose.yml`, `example.env`
2. เขียน deployment topology ใน `docs/architecture/deployment.md`
   - Docker Compose (development + self-host)
   - Shared hosting (cPanel/DirectAdmin)
   - VPS (Nginx + PHP-FPM manual)
   - Built-in PHP server (dev only)
3. ตรวจสอบ Nginx config ใน `docker/nginx/default.conf`
4. ตรวจสอบ PHP ini ใน `docker/php/php.ini.tpl`
5. เขียน health check endpoint design (ไม่ต้อง implement)

Constraints:
- Image size < 200MB
- Cold start < 5 seconds
- ไม่มี privileged containers
- RPO = 0 (stateless), RTO < 5 minutes

ผลลัพธ์:
- `docs/architecture/deployment.md` ครบทุก topology
- `docker-compose.yml` รันได้ด้วย `docker compose up -d`
````

## 1.4 Deliverable

- `docs/architecture/overview.md` (complete)
- `docs/architecture/directory-structure.md` (complete)
- `docs/architecture/deployment.md` (complete)
- `docs/adr/ADR-NNN-*.md` (≥ 3 ADRs)

## 1.5 DoD

- [ ] ทุก ADR ผ่าน review
- [ ] `docker compose up` แล้วเข้า `http://localhost:8080` ได้
- [ ] Folder layout ตรงตามที่ SA ระบุ

---

# 🟢 Phase 2 — Spec & Design (SA + Designer)

> **เวลา**: 3–5 วัน
> **เป้าหมาย**: API specs ครบทุก tool, Design tokens, Mockups

## 2.1 สิ่งที่ต้องทำ

- [ ] SA เขียน API spec ทุก tool ใน `docs/api-specs/<tool>.md`
- [ ] Designer สร้าง Design tokens (CSS variables)
- [ ] Designer สร้าง Page mockups ทั้ง 7 tools + landing
- [ ] Designer สร้าง UX flows (empty/loading/error/success)
- [ ] PM review ทั้งหมด

## 2.2 Prompt สำหรับ SA (สร้าง API Specs)

````text
คุณคือ System Analyst ของ MyAPIs project

หน้าที่ของคุณ: เขียน API specification ครบทั้ง 7 tools

Tools (ตาม tool-catalog.md):
1. health-calculator (BMI, BMR, daily-intake, water-intake)
2. password-generator (generate, analyze)
3. username-generator (9 themes)
4. promptpay-qr-generator (phone, tax_id, ewallet)
5. qr-code-generator (text, url, vcard, event, wifi, phone)
6. fortune-teller (52 fortunes × TH/EN/ZH)
7. randomizer (number, dice, coin, card)

สำหรับแต่ละ tool:
1. เขียน `docs/api-specs/<tool>.md` ตาม template
2. กำหนด:
   - Endpoint + Methods
   - Parameters (snake_case)
   - Validation rules (min/max/regex)
   - Response shape (success + error)
   - HTTP status codes
   - Error codes (snake_case UPPER)
   - Algorithm/formula reference (ถ้ามี)
   - Examples (curl + JSON)
3. Cross-check กับ `docs/standards/api-design.md`

Format requirement:
- Markdown only
- ทุก endpoint มี working example
- Error codes ต้องตรงกับ PHP implementation
- ภาษาอังกฤษ (หัวข้อไทยได้แค่สั้นๆ)

ผลลัพธ์: 7 ไฟล์ใน `docs/api-specs/` ที่ dev อ่านแล้วเริ่มเขียนได้ทันที
````

## 2.3 Prompt สำหรับ Designer

````text
คุณคือ UX/UI Designer ของ MyAPIs project

หน้าที่ของคุณ: ออกแบบ UI ครบทั้ง 7 tools

Deliverables:
1. Design tokens ใน `public/assets/css/design-tokens.css`
   - Colors (primary, success, error, warning, bg, text, border)
   - Spacing (8pt grid)
   - Typography (TH + EN support)
   - Radius, Shadow

2. Component library ใน `public/assets/css/components.css`
   - .btn (primary, secondary, danger)
   - .form-field (.input, .label, .help)
   - .card
   - .alert (success, error, warning, info)
   - .tool-page (layout)

3. Page mockups (HTML files):
   - `public/index.php` (landing)
   - `public/health-calculator.php` (mockup example)
   - อีก 6 tools (mockup)

4. UX states ทุก form:
   - Empty state
   - Loading state
   - Error state
   - Success state

Constraints:
- WCAG 2.1 AA (contrast ≥ 4.5:1)
- Mobile-first
- Thai font support (Noto Sans Thai)
- Lighthouse a11y score ≥ 95

Style: เขียน HTML+CSS code โดยตรง (mockup as code)
ไม่ใช่แค่ภาพ Figma

ผลลัพธ์:
- 4 CSS files
- 7 HTML mockup files
- ทุก state มี screenshot example ใน `docs/designs/`
````

## 2.4 Deliverable

- 7 × `docs/api-specs/*.md` (complete)
- `public/assets/css/design-tokens.css`
- `public/assets/css/components.css`
- 7 × page mockups (HTML)
- `docs/designs/` (visual reference)

## 2.5 DoD

- [ ] Dev อ่าน spec แล้วเขียนได้โดยไม่ต้องถาม SA
- [ ] Designer mockup ผ่าน a11y audit
- [ ] PM approve ทั้ง spec + design

---

# 🟡 Phase 3 — Implementation (Dev)

> **เวลา**: 5–10 วัน (ขึ้นกับจำนวน tool)
> **เป้าหมาย**: Code ทุก tool ตาม spec, ผ่าน review

## 3.1 สิ่งที่ต้องทำ

- [ ] Dev เขียน API endpoints ทั้ง 7 tools
- [ ] Dev เขียน Web UI ทั้ง 7 tools
- [ ] Dev เขียน rendered spec pages
- [ ] PR per feature, review ก่อน merge
- [ ] Self-test ทุก endpoint ก่อนส่ง QA

## 3.2 Prompt สำหรับ Dev (เขียน tool ใหม่ทั้งหมด)

````text
คุณคือ Developer ของ MyAPIs project

หน้าที่: Implement ทุก tool ตาม spec

แต่ละ tool ต้องสร้าง 3 ไฟล์:
1. `api/<tool>/index.php`      ← REST endpoint
2. `public/<tool>.php`         ← Web UI (HTML+JS)
3. `public/api-specs/<tool>.php` ← Rendered spec

Workflow:
1. อ่าน `docs/api-specs/<tool>.md`
2. อ่าน `docs/standards/coding-standards.md`
3. สร้าง branch: `git checkout -b feature/<tool>-impl`
4. เขียน endpoint class ใน `api/_includes/<Tool>.php`
5. เขียน `api/<tool>/index.php` (router + response)
6. เขียน `public/<tool>.php` (HTML form + JS fetch)
7. Self-test:
   php -l <file>
   curl "http://localhost:8080/api/<tool>/?..."
   browser test
8. Update spec ถ้า API เปลี่ยน
9. Commit + PR

Code style:
- declare(strict_types=1);
- Type hints ทุก parameter + return
- ใช้ random_int() ไม่ใช่ rand()
- ใช้ password_hash() ไม่ใช่ md5()
- htmlspecialchars() ก่อน echo
- JSON_UNESCAPED_UNICODE

ผลลัพธ์: PR ที่ผ่าน review + ทุก AC ใน Issue
````

## 3.3 Prompt สำหรับ Dev (Refactor / Bug fix)

````text
คุณคือ Developer ของ MyAPIs project

หน้าที่: แก้ Issue (bug / refactor / docs)

Workflow:
1. อ่าน Issue ที่ assigned (เช่น ISSUE-002-extract-css)
2. อ่าน Acceptance Criteria
3. ตรวจสอบขอบเขต (ห้ามเกิน AC)
4. สร้าง branch: `git checkout -b fix/ISSUE-NNN-slug`
5. แก้ code เฉพาะส่วนที่จำเป็น
6. เขียน/อัปเดต test
7. Self-test
8. Update related docs ถ้า API เปลี่ยน
9. PR

Constraints:
- ห้ามแก้ code นอกขอบเขต Issue
- ห้าม break backward compat (ถ้าจำเป็น → issue ใหม่)
- Conventional Commits (`fix:`, `feat:`, `refactor:`)
- PR title ใช้ emoji ตาม conventional commits

ผลลัพธ์: PR + passing self-test + ไม่มี lint error
````

## 3.4 Deliverable

- `api/<tool>/index.php` ทั้ง 7 tools
- `public/<tool>.php` ทั้ง 7 tools
- `public/api-specs/<tool>.php` ทั้ง 7 tools
- PRs merged ≥ 7
- Issues closed ≥ 7

## 3.5 DoD

- [ ] ทุก endpoint ผ่าน self-test (lint + curl + browser)
- [ ] ทุก PR review ≥ 1 approver
- [ ] No `TODO` / `var_dump` / `dd()` ค้างในโค้ด
- [ ] Conventional Commits

---

# 🟠 Phase 4 — Hardening (Dev + DevOps)

> **เวลา**: 3–5 วัน
> **เป้าหมาย**: ปลอดภัย, เร็ว, ทนทาน

## 4.1 สิ่งที่ต้องทำ

- [ ] Unit tests (cover happy + edge cases)
- [ ] Security audit (OWASP top 10)
- [ ] Performance test (p95 < 200ms สำหรับ non-QR)
- [ ] Error handling ครบ (ไม่มี stack trace หลุด)
- [ ] Logging structure
- [ ] Health check endpoint

## 4.2 Prompt สำหรับ Dev (Tests)

````text
คุณคือ Developer ของ MyAPIs project

หน้าที่: เขียน Unit tests

Target:
- ทุก endpoint มี ≥ 5 test cases:
  1. Happy path
  2. Validation error (1 case ต่อ field)
  3. Edge cases (empty, boundary, huge)
  4. Method not allowed (405)
  5. CORS preflight

Stack: PHPUnit 10+ (เพิ่มเป็น dev-only composer dep)

File structure:
- `tests/Unit/HealthCalculatorTest.php`
- `tests/Unit/PasswordGeneratorTest.php`
- ... (ตาม tool)

Test pattern:
```php
<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

final class HealthCalculatorTest extends TestCase
{
    public function testCalculateBMIWithValidInputs(): void
    {
        // arrange, act, assert
    }

    public function testCalculateBMIWithZeroWeightThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
    }
}
```

Run: `composer test` หรือ `./vendor/bin/phpunit`

Coverage target: ≥ 60%
````

## 4.3 Prompt สำหรับ DevOps (Security + Perf)

````text
คุณคือ DevOps Engineer ของ MyAPIs project

หน้าที่: Harden production deployment

Checklist:
1. Security headers:
   - X-Frame-Options: DENY
   - X-Content-Type-Options: nosniff
   - Referrer-Policy: strict-origin-when-cross-origin
   - Content-Security-Policy: ...
   - Permissions-Policy: ...

2. Rate limiting (per IP):
   - 100 req/min default
   - 10 req/min for /api/password-generator/
   - 429 response

3. Health endpoint:
   - GET /health → 200 OK (always)
   - GET /health?deep=true → check DNS, disk, opcache

4. Logging:
   - Access log (Nginx)
   - Error log (PHP-FPM)
   - Application log (JSON lines)

5. Performance:
   - opcache enabled (production)
   - Nginx gzip/brotli
   - HTTP cache headers (Cache-Control: public, max-age=300)
   - goQR response cache (Redis/filesystem ถ้ามี)

Tools: k6 / ab / wrk สำหรับ load test

ผลลัพธ์:
- `docs/runbooks/monitoring.md` ครบ
- `docker/nginx/default.conf` ปลอดภัย
- Security audit report
````

## 4.4 Deliverable

- `tests/Unit/*.php` (coverage ≥ 60%)
- `docs/runbooks/monitoring.md` (complete)
- Security headers ใน Nginx config
- Health check endpoint
- Load test report

## 4.5 DoD

- [ ] Test coverage ≥ 60%
- [ ] Security headers ผ่าน [securityheaders.com](https://securityheaders.com)
- [ ] Load test: 100 RPS ไม่มี 5xx
- [ ] Lighthouse Best Practices ≥ 95

---

# 🔴 Phase 5 — QA & UAT (QA)

> **เวลา**: 3–5 วัน
> **เป้าหมาย**: ทุก bug จับได้ก่อน user เจอ

## 5.1 สิ่งที่ต้องทำ

- [ ] Test plan (สำหรับทุก tool)
- [ ] Manual tests (browser + API)
- [ ] Compatibility test (Chrome, Safari, Firefox, Mobile)
- [ ] i18n test (TH, EN)
- [ ] Accessibility test (NVDA / VoiceOver)
- [ ] Bug reports + retest

## 5.2 Prompt สำหรับ QA (สร้าง Test Plan)

````text
คุณคือ QA Engineer ของ MyAPIs project

หน้าที่: ทดสอบทุก tool ก่อน release

Workflow:
1. อ่าน `docs/api-specs/<tool>.md`
2. อ่าน `docs/requirements/functional-requirements.md`
3. เขียน Test Plan สำหรับแต่ละ tool:
   - Functional tests (จาก AC)
   - Validation tests (จาก Validation Rules)
   - Edge cases (จาก Spec)
   - UX tests (Empty/Loading/Error/Success states)
4. รัน test cases (manual + API)
5. รายงาน bug ใน `docs/issues/open/ISSUE-NNN-*.md`
6. Retest หลัง Dev fix

Test categories:
- Happy path: ใส่ input ถูก → response ถูก
- Validation: ใส่ input ผิด → error code ถูก + HTTP ถูก
- Boundary: min, max, edge values
- Error: external service fail (mock ได้)
- Security: XSS, SQLi (แม้ไม่มี DB), CORS
- UX: empty/loading/error/success states
- a11y: keyboard, screen reader, contrast

Tools:
- Browser DevTools
- Postman / curl
- Lighthouse (a11y, perf, SEO, BP)
- axe DevTools extension
- NVDA / VoiceOver

Bug report template: `docs/issues/templates/bug.md`

Severity:
- P0: ใช้งานไม่ได้, security
- P1: feature หลักพัง
- P2: edge case พัง
- P3: cosmetic

ผลลัพธ์: Test report + bug issues (ถ้ามี)
````

## 5.3 Prompt สำหรับ QA (Regression)

````text
คุณคือ QA Engineer ของ MyAPIs project

หน้าที่: Regression test หลัง Dev fix

Workflow:
1. อ่าน bug report (Issue)
2. ทำตาม reproduce steps
3. ตรวจสอบ:
   - Bug หายแล้ว
   - ไม่ break ส่วนอื่น
   - Test case ใน test plan ผ่านครบ
4. Update Issue:
   - Status: Verified ✓
   - หรือ reopen ถ้ายังพัง
5. Sign-off

ถ้า bug reopen → Dev ต้องแก้ใหม่ → loop จนผ่าน

ผลลัพธ์: Issue status = Done (verified by QA)
````

## 5.4 Deliverable

- Test plan ใน `docs/issues/qa-test-plan.md`
- Bug reports ใน `docs/issues/open/`
- Test report (signed-off)
- Coverage report

## 5.5 DoD

- [ ] ทุก tool ผ่าน functional test
- [ ] ทุก P0/P1 bug ปิด
- [ ] ทุก P2/P3 ถูก prioritize
- [ ] UAT sign-off จาก stakeholders

---

# 🚀 Phase 6 — Deploy (DevOps)

> **เวลา**: 1–2 วัน
> **เป้าหมาย**: ขึ้น production ได้อย่างปลอดภัย

## 6.1 สิ่งที่ต้องทำ

- [ ] Staging deploy
- [ ] Smoke test staging
- [ ] Production deploy (with rollback plan)
- [ ] DNS switch
- [ ] Post-deploy verification

## 6.2 Prompt สำหรับ DevOps

````text
คุณคือ DevOps Engineer ของ MyAPIs project

หน้าที่: Deploy ไป production

Workflow:
1. ตรวจสอบ:
   - ทุก test ผ่าน
   - ทุก PR merged
   - Version tag พร้อม
   - CHANGELOG.md updated

2. Build production image:
   ```bash
   docker build -t myapis:2.7.0 .
   docker tag myapis:2.7.0 registry.example.com/myapis:2.7.0
   docker push registry.example.com/myapis:2.7.0
   ```

3. Deploy to staging:
   ```bash
   ssh staging "cd /app && docker compose pull && docker compose up -d"
   ```

4. Smoke test staging:
   - GET /health
   - GET /api/health-calculator/?type=bmi&weight=70&height=175
   - GET /
   - check logs

5. Deploy to production (rolling):
   ```bash
   ssh prod1 "cd /app && docker compose pull && docker compose up -d"
   ssh prod2 "cd /app && docker compose pull && docker compose up -d"
   ```

6. Verify production:
   - Health check
   - API check
   - Logs check
   - Monitor 15 นาที

7. Rollback plan:
   ```bash
   ssh prod "docker compose down && docker compose up -d"
   # ใช้ image tag เก่า
   ```

Rollback trigger:
- 5xx rate > 1% ภายใน 5 นาที
- Health check fail
- Latency p95 > 1s

ผลลัพธ์: Production live + monitoring OK
````

## 6.3 Deliverable

- Production deployment
- DNS updated
- Monitoring active
- Rollback tested

## 6.4 DoD

- [ ] Production accessible
- [ ] Health check 200
- [ ] ทุก endpoint ทำงาน
- [ ] Logs ส่งเข้า monitoring
- [ ] Rollback procedure verified

---

# 📊 Phase 7 — Monitor & Feedback (DevOps + PM)

> **เวลา**: ต่อเนื่อง
> **เป้าหมาย**: วัดผล ปรับปรุง

## 7.1 สิ่งที่ต้องทำ

- [ ] Monitor metrics (latency, error rate, traffic)
- [ ] รวบ feedback จาก users
- [ ] วางแผน next iteration

## 7.2 Prompt สำหรับ DevOps

````text
คุณคือ DevOps Engineer ของ MyAPIs project

หน้าที่: Monitor production + alert

Metrics:
- Uptime (target: 99.5%)
- p95 latency (target: < 200ms non-QR, < 1s QR)
- 5xx rate (alert: > 0.5%)
- Disk usage (alert: > 80%)
- Memory usage (alert: > 85%)

Tools:
- Prometheus + Grafana (ถ้า deploy VPS)
- Umami Analytics (สำหรับ web traffic)
- หรือ hosted: Better Stack / UptimeRobot

Dashboard:
- Request rate per tool
- Error rate per tool
- p50, p95, p99 latency
- Top countries / IPs
- API version distribution

Alert channels:
- Slack webhook
- Email
- PagerDuty (P0 only)

ผลลัพธ์:
- Grafana dashboard
- Alert rules
- Weekly report
````

## 7.3 Prompt สำหรับ PM (Feedback loop)

````text
คุณคือ PM ของ MyAPIs project

หน้าที่: รวบ feedback + plan next iteration

ทุกสัปดาห์:
1. ดู metrics:
   - API usage per tool
   - User geography
   - Error rate
2. รวบ feedback:
   - GitHub issues
   - User emails
   - Social media
3. Backlog refinement:
   - Issue ใหม่
   - Prioritize (MoSCoW)
   - Estimate effort
4. Sprint planning (ถ้ามี)
5. Roadmap update (roadmap section ใน product-brief.md)

KPIs ที่ต้องติดตาม:
- API calls/day (target: 10K)
- Unique users/month
- NPS (ถ้ามี)
- Issue resolution time

ผลลัพธ์:
- Weekly status report
- Updated backlog
- Updated roadmap
````

## 7.4 Deliverable

- Monitoring dashboard
- Weekly metrics report
- Updated backlog
- Updated roadmap

## 7.5 DoD (continuous)

- [ ] Uptime ≥ 99.5%
- [ ] p95 latency ตาม target
- [ ] Backlog prioritized
- [ ] Roadmap communicated

---

# 🎯 Sprint Cycle (ใช้ซ้ำได้)

ทุก feature ใหม่ใช้ cycle นี้:

```
Phase 2  → Phase 3  → Phase 4  → Phase 5  → Phase 6  → Phase 7
(Spec)    (Code)     (Test)     (QA)       (Deploy)   (Monitor)
   ↓         ↓          ↓          ↓          ↓
  1d       2-3d       1d         1d        0.5d
```

ตัวอย่าง: เพิ่ม feature ใหม่ "color-palette-generator"

1. PM: Issue ใหม่ (`docs/issues/open/ISSUE-011-color-palette.md`) — 1 ชม.
2. SA: อัปเดต `tool-catalog.md` + เขียน `docs/api-specs/color-palette.md` — 1 วัน
3. Designer: Mockup + tokens update (ถ้าจำเป็น) — 0.5 วัน
4. Dev: Implement ตาม spec — 2 วัน
5. QA: Test + bug reports — 1 วัน
6. DevOps: Deploy — 0.5 วัน
7. Monitor 1 สัปดาห์

**Total: ~6 วันทำการ ต่อ feature เล็ก**

---

# 🆘 Emergency Runbook

## ถ้า production ล่ม

1. **Check status**: `curl https://myapis.example.com/health`
2. **ดู logs**: `docker compose logs --tail=100 -f`
3. **Rollback**: ใช้ image tag เก่า (ตาม Phase 6)
4. **Post-mortem**: เขียนใน `docs/runbooks/incidents/`

## ถ้า security incident

1. ปิด endpoint ที่มีปัญหา (Nginx block)
2. รีเซ็ต secret keys (ถ้ามี)
3. Audit logs หา IOC
4. Patch + test
5. Post-mortem

## ถ้าทีมงานติดขัด

1. Daily standup (15 นาที)
2. Issue ที่ stuck > 3 วัน → escalation PM
3. Cross-team sync (SA + Dev + QA) ทุกสัปดาห์
4. Retrospective ทุก 2 สัปดาห์

---

# 📞 Escalation Path

```
Dev / QA ติดขัด
   ↓
SA review (ถ้าเป็นเรื่อง spec)
   ↓
PM (ถ้าเป็นเรื่อง priority / scope)
   ↓
Stakeholder (ถ้าเป็นเรื่อง business)
```

---

# 🎓 สรุป

| Phase | Owner | Time | DoD |
|-------|-------|------|-----|
| 0 Discovery | PM | 1-2d | Product Brief ผ่าน review |
| 1 Architecture | SA + DevOps | 2-4d | ADRs + Docker run ได้ |
| 2 Spec & Design | SA + Designer | 3-5d | API specs + Mockups |
| 3 Implementation | Dev | 5-10d | Code ทุก tool + PR merged |
| 4 Hardening | Dev + DevOps | 3-5d | Tests + Security + Perf |
| 5 QA | QA | 3-5d | Test report + UAT sign-off |
| 6 Deploy | DevOps | 1-2d | Production live |
| 7 Monitor | DevOps + PM | ต่อเนื่อง | KPIs hit target |

> **เคล็ดลับ**: copy prompt ของแต่ละ phase ไปวางใน AI chat (Copilot / Claude / GPT) แล้วเปลี่ยน `<tool-name>` ตามต้องการ — จะได้ code / spec / test ออกมาเป็นภาษาไทย + อังกฤษตามมาตรฐาน MyAPIs

---

## 🔗 เอกสารที่เกี่ยวข้อง

- [`prompts/pm-prompt.md`](../prompts/pm-prompt.md) — บทบาท PM
- [`prompts/sa-prompt.md`](../prompts/system-analyst-prompt.md) — บทบาท SA
- [`prompts/designer-prompt.md`](../prompts/designer-prompt.md) — บทบาท Designer
- [`prompts/dev-prompt.md`](../prompts/dev-prompt.md) — บทบาท Dev
- [`prompts/devops-prompt.md`](../prompts/devops-prompt.md) — บทบาท DevOps
- [`prompts/qa-prompt.md`](../prompts/qa-prompt.md) — บทบาท QA
- [`issues/README.md`](issues/README.md) — Issue workflow
- [`standards/git-workflow.md`](standards/git-workflow.md) — Git & PR
- [`standards/documentation.md`](standards/documentation.md) — Doc style