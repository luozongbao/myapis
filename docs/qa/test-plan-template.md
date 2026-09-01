# Test Plan Template (Universal — All Tools)

> **Owner:** QA (เทส)
> **Scope:** ใช้ได้กับทุก tool ใน MyAPIs (8 tools × endpoints + UI)
> **Last updated:** 2026-08-31 (v2.5 Polish sprint)

---

## 🎯 Purpose

Checklist มาตรฐานในการทดสอบ tool หนึ่งๆ ของ MyAPIs — ครอบคลุม 7 test categories:
**Happy / Validation / Boundary / Error / Security / UX / a11y**
(+ **i18n** ถ้า tool มีข้อความภาษาไทย/อังกฤษ)

ทุก Issue ใหม่ต้อง�่าน checklist นี้ก่อน sign-off

---

## 📋 Tool Under Test (TUT)

| Field | Value |
|-------|-------|
| Tool name | `<fortune-teller \| health-calculator \| password-generator \| promptpay-qr-generator \| qr-code-generator \| randomizer \| stats \| username-generator>` |
| API endpoint | `https://web.local/api/<tool>/` |
| UI page | `https://web.local/<tool>.php` |
| Spec page | `https://web.local/api-specs/<tool>.php` |
| Issue ID | ISSUE-XXX |
| PR / branch | `<PR-link>` |
| Tester | เทส (QA) |
| Test date | YYYY-MM-DD |
| Environment | Docker \| Shared Hosting \| VPS \| Dev (PHP built-in) |

---

## 1. 🟢 Happy Path

> ทดสอบว่า tool ทำงานได้ตามปกติกับ input ที่ "ถูกต้อง"

### API

- [ ] **Default request** — เรียก API โดยไม่ส่ง params (ถ้า tool มี default)
  ```bash
  curl -sS "https://web.local/api/<tool>/" | jq .
  ```
  → HTTP 200 + JSON body มี success:true (หรือ structure ที่ถูกต้อง)
- [ ] **Canonical request** — �่ง params ที่ doc แนะนำ
  ```bash
  curl -sS "https://web.local/api/<tool>/?param1=val1&param2=val2" | jq .
  ```
  → HTTP 200 + result ตรงตาม spec
- [ ] **POST body** (ถ้ารองรับ) — ส่ง JSON body
  ```bash
  curl -sS -X POST "https://web.local/api/<tool>/" \
    -H "Content-Type: application/json" \
    -d '{"key":"value"}' | jq .
  ```
  → HTTP 200 + ผลเหมือน GET
- [ ] **All sub-modes covered** (ถ้ามี — เช่น Health: BMI/BMR/Intake/Water)
  ```bash
  curl -sS "https://web.local/api/<tool>/?type=<mode>" | jq .
  ```
  → HTTP 200 ทุก mode

### UI

- [ ] เปิดหน้า `<tool>.php` → render สมบูรณ์ไม่มี console error
- [ ] กรอก form ด้วย default values → submit → result แสดง
- [ ] ทุก sub-mode ใน UI ทำงาน (ถ้ามี)
- [ ] Copy / Download button (ถ้ามี) → copy/download สำเร็จ

### Result verification

- [ ] **Deterministic check** (ถ้า possible) — run 2 �รั้ง, ผลต้อง consistent
  - ยกเว้น: random/dice/fortune/coin → ตรวจว่า *value* เปลี่ยน, *format* เหมือนเดิม
- [ ] **Format check** — `Content-Type`, charset, JSON validity
  ```bash
  curl -sSI "https://web.local/api/<tool>/?..." | grep -i content-type
  ```

---

## 2. 🔴 Validation (per field)

> ทดสอบว่า tool validate input ของแต่ละ field ตาม spec

> **วิธีทำ:** copy-paste URL ด้านล่างแทน `?field=val` ให้ครบทุก field ของ tool นั้น

### 2.1 Required fields

| Field | Test | Expected |
|-------|------|----------|
| `<field1>` | ลบ field นี้ออกจาก URL | 400 + error message ระบุ field ที่หายไป |
| `<field2>` | ลบ field นี้ออกจาก URL | 400 + error message |
| ... | ... | ... |

### 2.2 Type validation

| Field | Test value | Expected |
|-------|-----------|----------|
| `<field1>` (number) | `?f=abc` | 400 + "must be numeric" |
| `<field1>` (number) | `?f=1.5e999` | 400 + out of range |
| `<field1>` (number) | `?f=` (empty) | 400 + required |
| `<field2>` (enum) | `?f=invalid_value` | 400 + list allowed values |
| `<field2>` (enum) | `?f=` (empty) | default �รือ 400 (ขึ้นกับ spec) |
| `<field3>` (string) | `?f=<script>alert(1)</script>` | 200 (escape) หรือ 400 (reject) — verify ไม่ reflect |

### 2.3 Special chars + Unicode

- [ ] Thai text (ถ้ารองรับ): `?f=ทดสอบ`
- [ ] Emoji: `?f=🎲🎯`
- [ ] HTML/JS injection: `?f=<img src=x onerror=alert(1)>`
- [ ] SQL-ish: `?f=' OR 1=1--` (N/A — no DB แต่ทดสอบ defensive)
- [ ] Path traversal: `?f=../../../etc/passwd`
- [ ] Null byte: `?f=foo%00bar`
- [ ] Very long string: `?f=$(python3 -c 'print("a"*10000)')`

---

## 3. 🟡 Boundary (min/max/edge)

> ทดสอบขอบเขตของค่าที่รับได้

### 3.1 Numeric boundaries

| Field | Min | Max | Edge cases |
|-------|-----|-----|-----------|
| `<length>` | `?f=0` | `?f=<max+1>` | 0, negative, overflow, INT_MAX |
| `<count>` | `?f=0` | `?f=<max+1>` | ... |
| `<amount>` | `?f=0` | `?f=999999999` | ... |

Expected:
- ใน range → 200 + result
- นอก range → 400 + message "must be between X and Y"

### 3.2 String boundaries

- [ ] Empty string: `?f=`
- [ ] 1 char: `?f=a`
- [ ] Max length: `?f=<max chars>`
- [ ] Max + 1: `?f=<max+1 chars>`
- [ ] 10 KB: `?f=$(yes a | head -c 10000 | tr -d '\n')`

### 3.3 Array / List boundaries

- [ ] Empty list
- [ ] 1 item
- [ ] Max items
- [ ] Duplicate items
- [ ] Whitespace-only items

### 3.4 Tool-specific edges

| Tool | Edge |
|------|------|
| Health BMI | weight=0, height=0, height=300cm, weight=500kg |
| Password | length=1, length=100, all toggles off → 400 |
| QR text | empty text, text > 1000 chars |
| PromptPay | target=0 (invalid), amount=0, amount=99999999 |
| Random number | min > max → 400, min=max |
| Username | theme=invalid, length=0 |

---

## 4. 🔥 Error (external service / internal)

> ทดสอบเมื่อ external dep fail หรือ internal error

### 4.1 External services (ถ้ามี)

- [ ] **QR (goQR.me)** — kill network หรือ mock timeout → 502/504 + friendly error
- [ ] **PromptPay** — pure logic, ไม่มี external → skip
- [ ] **Rate limiter (ISSUE-001)** — burst 200 req/min → 429 + `Retry-After`
- [ ] **Config missing** — unset `RATE_LIMIT_PER_MINUTE` → default 100 OK

### 4.2 Server error simulation

- [ ] Method ที่ไม่ support — `curl -X DELETE` → 405
- [ ] Wrong content-type — POST with form-urlencoded → 400
- [ ] Very large payload — 10 MB body → 413 (ถ้ามี limit)
- [ ] Concurrent — `xargs -P 20 curl` → no 500s, ทุก response valid

### 4.3 HTTP error codes

| Code | Trigger | Expected message |
|------|---------|------------------|
| 400 | Bad input | "Invalid parameter: ..." |
| 404 | Wrong path | "Not found" (ไม่ expose file path) |
| 405 | Wrong method | "Method not allowed" |
| 429 | Rate limit | "Too many requests" + Retry-After |
| 500 | Server bug | Generic "Internal error" (ไม่ expose stack) |

---

## 5. 🛡️ Security

> ทดสอบ attack surface ของ tool

### 5.1 Headers & CORS

- [ ] **CORS headers** — `curl -I` ต้องมี:
  ```
  Access-Control-Allow-Origin: *
  Access-Control-Allow-Methods: GET, POST, OPTIONS
  Access-Control-Allow-Headers: Content-Type
  ```
- [ ] **Preflight OPTIONS** → 200/204 ไม่ error
- [ ] **No sensitive headers** leak (Server, X-Powered-By)
- [ ] **Content-Type** — JSON ต้องมี `application/json` (ไม่ใช่ text/html)

### 5.2 Injection

- [ ] **XSS reflected** — `<script>alert(1)</script>` ใน input → ไม่ execute ใน UI
- [ ] **XSS stored** — N/A (ไม่มี DB) แต่ verify JSON output escape
- [ ] **HTML injection** — `<h1>x</h1>` → render เป็น text ไม่ใช่ tag
- [ ] **Command injection** — `; ls` → ไม่ทำงาน
- [ ] **Path traversal** — `../../` → ไม่ expose file

### 5.3 Info disclosure

- [ ] Error message ไม่ expose: file path, stack trace, version, query
- [ ] `.env`, `config.php`, `README.md` → 403/404 (ไม่ accessible จาก web)
  ```bash
  curl -sS -o /dev/null -w "%{http_code}" "https://web.local/.env"
  curl -sS -o /dev/null -w "%{http_code}" "https://web.local/config.php"
  curl -sS -o /dev/null -w "%{http_code}" "https://web.local/README.md"
  ```
- [ ] `phpinfo()` ไม่ exposed

### 5.4 CSRF (ISSUE-010)

- [ ] **Origin header check** — request with `Origin: https://evil.com` → ถ้า cookie-based session, ต้อง reject
- [ ] **Token present** — POST form ต้องมี CSRF token (�้า implement)
- [ ] **Token reuse** — same token 2 ครั้ง → second ต้อง fail (ถ้า one-time)
- [ ] **Token missing** — POST without token → 403

### 5.5 Rate limiting (ISSUE-001)

- [ ] **Burst** — `for i in {1..200}; do curl ... ; done` → request ที่ 101+ → 429
- [ ] **Headers** — ทุก response มี:
  ```
  X-RateLimit-Limit: 100
  X-RateLimit-Remaining: <n>
  X-RateLimit-Reset: <unix-ts>
  ```
- [ ] **Retry-After** — response 429 มี `Retry-After: <seconds>`
- [ ] **Reset** — รอ 60s → counter reset → 200 อีกครั้ง
- [ ] **Per-IP** — simulate 2 IP → counter แยกกัน (ถ้า implement)
- [ ] **Skip health check** — `/health` ไม่นับ

### 5.6 Sensitive files

- [ ] `/api/<tool>/index.php~` (backup) → 404
- [ ] `/api/<tool>/index.php.bak` → 404
- [ ] `/api/<tool>/.git/` → 404
- [ ] Directory listing ปิด (ไม่มี `Index of /`)

---

## 6. 🎨 UX

> ทดสอบ user experience ของ UI

### 6.1 Empty states

- [ ] เปิดหน้าเปล่าๆ → ไม่มี form error / broken layout
- [ ] Form ก่อน submit → result section empty / placeholder
- [ ] ไม่มีข้อความ error แสดงโดยไม่มี action

### 6.2 Loading states

- [ ] Submit → แสดง loading indicator (spinner / disabled button)
- [ ] Button disabled ระหว่า� request
- [ ] Slow request → ไม่ block UI

### 6.3 Success states

- [ ] Result แสดงชัดเจน
- [ ] Copy/Download button ใช้งานได้
- [ ] Success message (ถ้ามี) → role="status" + aria-live="polite"
- [ ] Form reset หลัง success (ถ้า appropriate)

### 6.4 Error states

- [ ] Invalid input → inline error (ใกล้ field) **ไม่ใช่** alert กลางจอ
- [ ] Server error → friendly message + retry button
- [ ] Error → role="alert" + aria-live="assertive"
- [ ] Error ไม่ block form (user แก้ไขได้)

### 6.5 Responsive

| Breakpoint | Test |
|------------|------|
| 320 px (mobile small) | layout ไม่ break, ปุ่ม clickable |
| 768 px (tablet) | ... |
| 1024 px (desktop) | ... |
| 1920 px (wide) | ไม่มี overflow horizontal |

### 6.6 i18n (ถ้ามีข้อความ TH/EN/ZH)

- [ ] TH — render ถูกต้อง, ไม่ broken UTF-8
- [ ] EN — render ถูกต้อง
- [ ] ZH — render ถูกต้อง (ถ้ามี)
- [ ] Mixed: `?f=Hello สวัสดี 你好` → render ครบ
- [ ] Font fallback — TH/ZH char render สวย
- [ ] `JSON_UNESCAPED_UNICODE` — API JSON ไม่ escape (`\u0e2a` → `ส`)

---

## 7. ♿ Accessibility (a11y — ISSUE-009)

> ทดสอบ WCAG 2.1 AA compliance

### 7.1 Lighthouse

```bash
npx lighthouse "https://web.local/<tool>.php" \
  --only-categories=accessibility \
  --output=html \
  --output-path=/tmp/a11y-<tool>.html
```

**Pass criteria:** Score ≥ 95

- [ ] Run Lighthouse → score: `____`
- [ ] บันทึก report ที่ `/tmp/a11y-<tool>.html`
- [ ] ไม่มี violation: "Critical", "Serious"

### 7.2 axe DevTools (browser extension)

- [ ] Open page → Run axe → **0 critical issues**
- [ ] ถ้ามี issues → file bug ทันที (P1/P2 ตาม severity)
- [ ] Top issues to check:
  - Missing form labels
  - Color contrast < 4.5:1
  - Missing alt text
  - Empty buttons/links
  - Missing ARIA roles

### 7.3 Keyboard navigation

- [ ] **Tab order** — `Tab` ผ่าน interactive elements ตามลำดับที่คาดหวัง
- [ ] **Tab focus visible** — มี outline/ring ชัดเจน (`:focus-visible`)
- [ ] **Enter/Space** activate button
- [ ] **Esc** close modal/dialog (ถ้ามี)
- [ ] **Skip link** — `Tab` แรกเจอ "Skip to main content"
- [ ] **Form fields** — focus ได้ด้วย Tab, ออกด้วย Shift+Tab
- [ ] **No keyboard trap** — focus ไม่ติดใน element

### 7.4 Screen reader

> ทดสอบด้วย NVDA (Windows) / VoiceOver (macOS/iOS)

- [ ] **Page title** อ่านออกเมื่อ load
- [ ] **Landmarks** — `<header>`, `<main>`, `<footer>` �ูกระบุ
- [ ] **Headings** — `<h1>` มี 1 ตัว, hierarchy ถูก (h1 > h2 > h3)
- [ ] **Form labels** — ทุก input มี label อ่านออก
- [ ] **Buttons** — text/aria-label อ่านออก
- [ ] **Live regions** — result/error อ่านออกเมื่อเปลี่ยน
- [ ] **Alt text** — ทุก `<img>` มี alt (หรือ alt="" ถ้า decorative)
- [ ] **Links** — text มีความหมาย (ไม่ใช่ "click here")

### 7.5 Color contrast (WCAG AA)

- [ ] **Body text / background** ≥ 4.5:1
- [ ] **Large text (≥18pt) / bg** ≥ 3:1
- [ ] **UI components / bg** ≥ 3:1
- [ ] **Disabled state** — exempt (ไม่ต้อง ≥ ratio)
- [ ] **Focus indicator** — ≥ 3:1 vs adjacent

Tools:
- Chrome DevTools → Rendering → "Emulate vision deficiencies"
- [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)

---

## 8. ⚡ Performance (sanity)

> ทดสอบ performance budget

```bash
# Time 100 requests
for i in {1..100}; do
  curl -sS -o /dev/null -w "%{time_total}\n" \
    "https://web.local/api/<tool>/?<canonical_params>"
done | sort -n | awk '
  BEGIN { c=0 }
  { a[c++]=$1; sum+=$1 }
  END {
    print "p50:", a[int(c*0.50)]
    print "p95:", a[int(c*0.95)]
    print "p99:", a[int(c*0.99)]
    print "avg:", sum/c
  }
'
```

**Budget:**
| Tool type | p95 target |
|-----------|-----------|
| Pure compute (random, password, username) | < 50 ms |
| Local logic (health, fortune) | < 100 ms |
| External API (qr-code, promptpay) | < 500 ms |

- [ ] p95 < target
- [ ] ไม่มี request ที่ > 2× target
- [ ] Memory: `ps aux | grep php-fpm` ไม่ leak

---

## 9. 🌍 Compatibility

### Browsers (manual smoke)

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | latest | ☐ |
| Firefox | latest | ☐ |
| Safari (macOS) | latest | ☐ |
| Edge | latest | ☐ |
| Safari iOS | latest | ☐ |
| Chrome Android | latest | ☐ |

### Deployment

| Deployment | Tested |
|------------|--------|
| Docker Compose | ☐ |
| Shared Hosting (Hostinger-style) | ☐ |
| VPS (Nginx + PHP-FPM) | ☐ |
| PHP built-in (dev only) | ☐ |

---

## 10. ✅ Sign-off

| Criterion | Pass |
|-----------|------|
| All Happy path ✅ | ☐ |
| All Validation ✅ | ☐ |
| All Boundary ✅ | ☐ |
| All Error ✅ | ☐ |
| All Security ✅ | ☐ |
| All UX ✅ | ☐ |
| All a11y ✅ (Lighthouse ≥ 95, axe 0 critical) | ☐ |
| Performance ใน budget | ☐ |
| Compatibility ผ่าน | ☐ |
| **No regression** ใน tools อื่น (smoke ทุก tool) | ☐ |
| **DoD** ของ Issue �รบ | ☐ |

### Result

- [ ] ✅ **PASS** — merge approve
- [ ] ❌ **FAIL** — block PR + file bug

### Notes

```
<เขียน observation หรือ caveat>
```

### Tester

QA (เทส) — Date: ____-__-__

---

## 📎 Appendix

### A. Quick reference — error expectations

| Tool | 400 messages |
|------|--------------|
| Health | "Invalid type", "Invalid weight/height/age", "Gender must be male/female" |
| Password | "Length must be between X-Y", "No character types selected" |
| QR | "Unsupported type", "Invalid QR parameter" |
| PromptPay | "Invalid target", "Amount must be positive" |
| Random | "Min cannot be greater than max", "Sides must be 2-100" |
| Username | "Invalid theme", "Length must be ≥3" |
| Fortune | N/A (no input) |

### B. Useful one-liners

```bash
# Pretty JSON
curl -sS "URL" | jq .

# Headers only
curl -sSI "URL"

# POST JSON
curl -sS -X POST "URL" -H "Content-Type: application/json" -d '{"k":"v"}'

# With timing
curl -sS -o /dev/null -w "code:%{http_code} time:%{time_total}s\n" "URL"

# Burst (for rate-limit test)
seq 1 200 | xargs -P 20 -I{} curl -sS -o /dev/null -w "{}:%{http_code}\n" "URL"

# Lighthouse
npx lighthouse URL --only-categories=accessibility --output=html --output-path=/tmp/r.html
```

### C. Related docs

- `docs/issues/README.md` — workflow + DoD
- `docs/issues/templates/bug.md` — bug report template
- `docs/api-specs/` — API contract
- `docs/standards/security.md` — security baseline
- `docs/standards/coding-standards.md` — code style
