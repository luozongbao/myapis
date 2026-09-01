# Regression Checklist — v2.5 Polish (8 Tools + Landing)

> **Owner:** QA (เทส)
> **Sprint:** v2.5 Polish — Restructure File / Pages + Security fixes
> **Use:** Run หลัง Dev merge เพื่อ confirm ไม่มี regression
> **Last updated:** 2026-08-31

---

## 🎯 Purpose

หลังจาก Dev push fix สำหรับ ISSUE-001 (rate-limit), ISSUE-002 (CSS extract),
ISSUE-009 (a11y), ISSUE-010 (CSRF), และ file restructure — รัน checklist นี้เพื่อ
ยืนยันว่า **ทุก tool** ยังทำงานปกติ + ไม่มี regression

---

## � Sprint 1 Status (อัปเดตเมื่อ Dev merge)

| ISSUE | Title | Status | Test owner |
|-------|-------|--------|-----------|
| ISSUE-001 | Rate Limiting | ☐ pending | เทส |
| ISSUE-002 | Extract CSS (design tokens) | ☐ pending | เทส |
| ISSUE-009 | Improve a11y | ☐ pending | เทส + Designer |
| ISSUE-010 | CSRF / Secrets mgmt (verify) | ☐ pending | เทส |
| Restructure | File tree + Page restructure | ☐ pending | เทส |

> **หมายเหตุ:** `api/stats/` directory มีอยู่แต่ **ว่าง** (ไม่มี `index.php`) — ตอนนี้ถือว่า
> ยังไม่ใช่ tool ที่ test ได้ (รอ Dev เขียนก่อน)

---

## 🚦 Master Gate — ทำทุกข้อก่อน merge approve

- [ ] **G0.1** Smoke test ผ่าน 8/8 endpoints (ดู §A)
- [ ] **G0.2** 8 UI pages render สำเร็จ (ไม่มี 5xx)
- [ ] **G0.3** 7 spec pages load สำเร็จ
- [ ] **G0.4** ไม่มี console error ใน browser (Chrome DevTools → Console)
- [ ] **G0.5** ไม่มี PHP error/warning (`docker logs` หรือ error.log)

---

## 🧰 A. Smoke Test (run ก่อนทุกอย่าง)

```bash
# Set base URL
BASE="https://web.local"

echo "=== Landing ==="
curl -sS -o /dev/null -w "/                  → %{http_code}\n" "$BASE/"

echo "=== API endpoints ==="
curl -sS -o /dev/null -w "/api/health-calculator/      → %{http_code}\n" \
  "$BASE/api/health-calculator/?type=bmi&weight=70&height=175"
curl -sS -o /dev/null -w "/api/password-generator/      → %{http_code}\n" \
  "$BASE/api/password-generator/?length=16"
curl -sS -o /dev/null -w "/api/username-generator/      → %{http_code}\n" \
  "$BASE/api/username-generator/"
curl -sS -o /dev/null -w "/api/randomizer/              → %{http_code}\n" \
  "$BASE/api/randomizer/?type=number&min=1&max=100"
curl -sS -o /dev/null -w "/api/fortune-teller/          → %{http_code}\n" \
  "$BASE/api/fortune-teller/"
curl -sS -o /dev/null -w "/api/qr-code-generator/       → %{http_code}\n" \
  "$BASE/api/qr-code-generator/?type=text&text=Hello"
curl -sS -o /dev/null -w "/api/promptpay-qr-generator/  → %{http_code}\n" \
  "$BASE/api/promptpay-qr-generator/?target=0812345678"

echo "=== Tool UI pages ==="
for t in fortune-teller health-calculator password-generator promptpay-qr-generator \
         qr-code-generator randomizer stats username-generator; do
  curl -sS -o /dev/null -w "/$t.php            → %{http_code}\n" "$BASE/$t.php"
done

echo "=== Spec pages ==="
for t in fortune-teller health-calculator password-generator promptpay-qr-generator \
         qr-code-generator randomizer stats username-generator; do
  curl -sS -o /dev/null -w "/api-specs/$t.php → %{http_code}\n" "$BASE/api-specs/$t.php"
done
```

**Pass criteria:** ทุก endpoint = 200 หรือ 400 (validation error = ปกติ ไม่ใช่ regression)
**Fail signal:** 5xx = P0 bug; 404 unexpected = P1 bug; 401/403 unexpected = P1 bug

---

## 🟢 B. Tool-by-Tool Regression

### B1. `health-calculator`

| # | Test | Pass |
|---|------|------|
| 1 | API: `?type=bmi&weight=70&height=175` → BMI ≈ 22.86 | ☐ |
| 2 | API: BMI height=300cm → auto convert → BMI valid | ☐ |
| 3 | API: BMI weight=0 → 400 | ☐ |
| 4 | API: BMI missing weight → 400 | ☐ |
| 5 | API: `?type=bmr&weight=70&height=175&age=30&gender=male` → ~1650 | ☐ |
| 6 | API: BMR gender=female → ~1340 | ☐ |
| 7 | API: BMR invalid gender → 400 | ☐ |
| 8 | API: `?type=water&weight=70&age=30&gender=male&activity=moderate&climate=temperate&healthCondition=normal` → ~2700ml | ☐ |
| 9 | API: `?type=intake&...&goal=lose` → calories - 500 | ☐ |
| 10 | UI: open `/health-calculator.php` → tabs BMI/BMR/Intake/Water render | ☐ |
| 11 | UI: BMI form submit → result shows | ☐ |
| 12 | UI: switch tab → form fields update | ☐ |
| 13 | Spec: `/api-specs/health-calculator.php` loads + correct examples | ☐ |

---

### B2. `password-generator`

| # | Test | Pass |
|---|------|------|
| 1 | API: `?length=16` → array 5 passwords, each length 16 | ☐ |
| 2 | API: `?length=8` → length 8 | ☐ |
| 3 | API: `?length=100` → length 100 | ☐ |
| 4 | API: `?length=1` → length 1 (or min?) | ☐ |
| 5 | API: `?length=0` → 400 | ☐ |
| 6 | API: all toggles off → 400 "No character types" | ☐ |
| 7 | API: `?exclude_ambiguous=true` → no `0O1lI` `` ` `` | ☐ |
| 8 | API: `?no_repeated_chars=true` → no duplicate chars | ☐ |
| 9 | API: `?count=10` → array 10 passwords | ☐ |
| 10 | UI: form submit → 5 passwords show | ☐ |
| 11 | UI: toggle "Exclude ambiguous" → regenerate correctly | ☐ |
| 12 | UI: copy button works | ☐ |

---

### B3. `randomizer`

| # | Test | Pass |
|---|------|------|
| 1 | API: `?type=number&min=1&max=100` → number in [1,100] | ☐ |
| 2 | API: `?type=number&min=50&max=10` → 400 | ☐ |
| 3 | API: `?type=dice&sides=6&count=1` → number 1-6 | ☐ |
| 4 | API: `?type=dice&sides=2&count=10` → 10 dice 1-2 | ☐ |
| 5 | API: `?type=coin&count=1` → Heads/Tails | ☐ |
| 6 | API: `?type=coin&count=10` → 10 results | ☐ |
| 7 | API: invalid type → 400 (or default?) | ☐ |
| 8 | API: `?type=number` (no params) → default 1-100 | ☐ |
| 9 | UI: tabs Number/Dice/Coin render | ☐ |
| 10 | UI: each tab submit → result shows | ☐ |

---

### B4. `username-generator`

| # | Test | Pass |
|---|------|------|
| 1 | API: default → username returned | ☐ |
| 2 | API: `?theme=Fantasy` → fantasy word | ☐ |
| 3 | API: `?theme=Science and Space` → space word | ☐ |
| 4 | API: `?theme=invalid` → 400 or fallback | ☐ |
| 5 | API: `?count=5` → 5 usernames | ☐ |
| 6 | UI: theme selector works | ☐ |
| 7 | UI: copy username works | ☐ |
| 8 | Spec page renders examples | ☐ |

---

### B5. `fortune-teller`

| # | Test | Pass |
|---|------|------|
| 1 | API: `GET /api/fortune-teller/` → JSON with `fortune`, `total_fortunes: 52` | ☐ |
| 2 | API: fortune JSON has text in Thai (UTF-8) | ☐ |
| 3 | API: 5 calls → ได้ fortune ต่างกัน (random) | ☐ |
| 4 | API: response has `success: true` | ☐ |
| 5 | UI: open `/fortune-teller.php` → button + empty result | ☐ |
| 6 | UI: click "ดูดวง" → fortune shows | ☐ |
| 7 | UI: click again → new fortune | ☐ |
| 8 | **Data check** — มี 52 JSON files ใน `api/fortune-teller/predictions/` (ls ดู) | ☐ |

---

### B6. `qr-code-generator`

| # | Test | Pass |
|---|------|------|
| 1 | API: `?type=text&text=Hello` → 200 + PNG/SVG binary | ☐ |
| 2 | API: `?type=url&url=https://example.com` → 200 binary | ☐ |
| 3 | API: `?type=vcard&firstname=A&lastname=B&phone=...` → 200 binary | ☐ |
| 4 | API: `?type=event&summary=...&start=...&end=...` → 200 binary | ☐ |
| 5 | API: `?type=wifi&ssid=X&password=Y` → 200 binary | ☐ |
| 6 | API: `?type=phone&phone=0812345678` → 200 binary | ☐ |
| 7 | API: `?type=invalid` → 400 | ☐ |
| 8 | API: `?type=text&text=` → 400 | ☐ |
| 9 | API: `?format=svg` → SVG output (verify Content-Type) | ☐ |
| 10 | API: **goQR.me timeout** → 502/504 (mock) | ☐ |
| 11 | UI: type selector switches form | ☐ |
| 12 | UI: form submit → QR image renders | ☐ |
| 13 | UI: download button works | ☐ |
| 14 | Spec page renders | ☐ |

---

### B7. `promptpay-qr-generator`

| # | Test | Pass |
|---|------|------|
| 1 | API: `?target=0812345678` → PNG QR (PromptPay static) | ☐ |
| 2 | API: `?target=0812345678&amount=100` → PNG QR (PromptPay dynamic) | ☐ |
| 3 | API: `?target=1234567890123` (13 digits = tax ID) → valid | ☐ |
| 4 | API: `?target=1234567890123456` (16 digits = e-wallet) → valid | ☐ |
| 5 | API: `?target=abc` (non-numeric) → sanitized → 400 if invalid | ☐ |
| 6 | API: `?target=` (empty) → 400 | ☐ |
| 7 | API: `?amount=-100` → 400 | ☐ |
| 8 | API: `?amount=999999999` → large valid | ☐ |
| 9 | UI: form submit → QR shows | ☐ |
| 10 | UI: target validation (numeric only) | � |
| 11 | Spec page renders examples | ☐ |

---

### B8. `stats` *(status: NOT IMPLEMENTED YET)*

| # | Test | Pass |
|---|------|------|
| 1 | `api/stats/` exists but **EMPTY** (no `index.php`) | ☐ |
| 2 | `api/stats/index.php` 404 | ☐ |
| 3 | `stats.php` UI page 404 | ☐ |
| 4 | `api-specs/stats.php` 404 | ☐ |
| 5 | **Bug filed:** stats tool missing — needs separate ISSUE | ☐ |

> ⚠️ **Action required:** PM + SA ต้องตัดสินใจว่า stats จะ:
> (a) Implement ก่อน merge v2.5
> (b) Defer ไป v2.6 (mark ใน RELEASE.md)
> (c) Remove �อกจาก README + docs

---

## 🔐 C. Cross-Cutting: Security (ISSUE-001, 010)

### C1. Rate Limiting (ISSUE-001)

```bash
# Burst 120 requests, count HTTP codes
seq 1 120 | xargs -P 10 -I{} curl -sS -o /dev/null -w "%{http_code}\n" \
  "https://web.local/api/password-generator/?length=16" \
  | sort | uniq -c
```

| # | Test | Expected | Pass |
|---|------|----------|------|
| 1 | Burst 100 → ~100 × 200, ~20 × 429 | ☐ |
| 2 | Response header `X-RateLimit-Limit: 100` | ☐ |
| 3 | Response header `X-RateLimit-Remaining` (decreasing) | ☐ |
| 4 | Response header `X-RateLimit-Reset` (unix ts) | ☐ |
| 5 | 429 response has `Retry-After` | ☐ |
| 6 | Wait 60s → counter reset → 200 | ☐ |
| 7 | Health endpoint not rate-limited (no 429) | ☐ |
| 8 | Configurable via env (default 100 OK) | ☐ |
| 9 | Performance: rate-limit overhead < 5ms (p95) | ☐ |

### C2. CSRF / Origin Validation (ISSUE-010)

| # | Test | Pass |
|---|------|------|
| 1 | POST with `Origin: https://evil.com` → 403 (if cookie-based) | ☐ |
| 2 | POST without `Origin` → still works (CORS) | ☐ |
| 3 | Token (if implemented) present in form | ☐ |
| 4 | Token reuse → fail | ☐ |
| 5 | Token missing → 403 | ☐ |
| 6 | Secrets docs exist at `docs/runbooks/secrets-management.md` | ☐ |

### C3. Sensitive files (general)

```bash
for f in .env config.php README.md .git/config phpinfo.php; do
  echo -n "/$f: "
  curl -sS -o /dev/null -w "%{http_code}\n" "https://web.local/$f"
done
```

| # | Path | Expected | Actual |
|---|------|----------|--------|
| 1 | `/.env` | 404 | ☐ |
| 2 | `/config.php` | 404 | ☐ |
| 3 | `/README.md` | 404 | ☐ |
| 4 | `/.git/config` | 404 | ☐ |
| 5 | `/phpinfo.php` | 404 | ☐ |
| 6 | `/api/<tool>/index.php~` | 404 | ☐ |

---

## ♿ D. Cross-Cutting: a11y (ISSUE-009)

### D1. Lighthouse (per page)

```bash
mkdir -p /tmp/a11y-regression
for t in fortune-teller health-calculator password-generator promptpay-qr-generator \
         qr-code-generator randomizer stats username-generator index; do
  npx lighthouse "https://web.local/$t.php" \
    --only-categories=accessibility \
    --output=json \
    --output-path="/tmp/a11y-regression/$t.json" \
    --chrome-flags="--headless" \
    --quiet
done
```

| Page | Score (≥95) | Pass |
|------|------------|------|
| `index.php` | ___ | ☐ |
| `fortune-teller.php` | ___ | ☐ |
| `health-calculator.php` | ___ | ☐ |
| `password-generator.php` | ___ | ☐ |
| `promptpay-qr-generator.php` | ___ | ☐ |
| `qr-code-generator.php` | ___ | ☐ |
| `randomizer.php` | ___ | ☐ |
| `stats.php` | N/A (404) | ☐ |
| `username-generator.php` | ___ | ☐ |

### D2. axe DevTools (browser — manual)

| Page | Critical = 0 | Pass |
|------|--------------|------|
| `index.php` | ☐ | ☐ |
| Each tool page (8) | ☐ | ☐ |

### D3. Keyboard navigation (per page)

| Action | Pass |
|--------|------|
| Tab เข้าหน้า → เจอ "Skip to main content" เป็น element แรก | ☐ |
| Tab ผ่าน form fields ตาม logical order | ☐ |
| Visible focus ring ทุก interactive element | ☐ |
| Enter/Space activate button | � |
| Esc close modal (ถ้ามี) | ☐ |

### D4. Screen reader spot check (NVDA / VoiceOver)

| Page | Title reads | Labels read | Live regions announce | Pass |
|------|-------------|-------------|----------------------|------|
| `health-calculator.php` | ☐ | ☐ | ☐ | ☐ |
| `password-generator.php` | ☐ | ☐ | ☐ | ☐ |
| `qr-code-generator.php` | ☐ | ☐ | ☐ | ☐ |

---

## 🎨 E. Cross-Cutting: CSS Migration (ISSUE-002)

### E1. Visual regression (before vs after)

> **Setup:** ก่อน Dev merge → screenshot ทุกหน้า (Chrome DevTools → device toolbar)
> หลัง Dev merge → screenshot อีกรอบ → diff

```bash
# Before
mkdir -p /tmp/css-before
for t in fortune-teller health-calculator password-generator promptpay-qr-generator \
         qr-code-generator randomizer username-generator; do
  # Use puppeteer / playwright headless screenshot
  npx playwright screenshot --browser=chromium "https://web.local/$t.php" \
    "/tmp/css-before/$t.png" 2>/dev/null || echo "$t: skipped"
done
```

| Page | Before path | After path | Diff | Pass |
|------|-------------|-----------|------|------|
| `index.php` | ☐ | ☐ | ☐ | ☐ |
| 8 tool pages | ☐ | ☐ | ☐ | ☐ |

### E2. CSS loading

| Check | Expected | Pass |
|-------|----------|------|
| `public/assets/css/design-tokens.css` exists | ✅ | ☐ |
| `public/assets/css/main.css` (or tool-specific) exists | ✅ | ☐ |
| Inline `<style>` ใน `<tool>.php` ลดลง (target ≤ 20 lines) | ✅ | ☐ |
| CSS loaded ทุก page (Network tab: 200) | ✅ | ☐ |
| ไม่มี 404 ใน Network (CSS file หาย) | ✅ | ☐ |

### E3. Design tokens applied

| Token | Before | After | Pass |
|-------|--------|-------|------|
| Body text contrast | ___ | ≥ 4.5:1 | ☐ |
| Button color | ___ | matches token | ☐ |
| Heading font | ___ | matches token | ☐ |
| Spacing | ___ | consistent | ☐ |

---

## 🌍 F. i18n Spot Check

| Tool | TH text correct | EN text correct | UTF-8 in JSON | Pass |
|------|----------------|-----------------|---------------|------|
| fortune (TH) | ☐ | N/A | ☐ | ☐ |
| health-calculator (EN/TH mix) | ☐ | ☐ | ☐ | � |
| promptpay (TH) | ☐ | N/A | ☐ | ☐ |
| username | N/A | ☐ | ☐ | ☐ |
| Others | N/A | ☐ | ☐ | ☐ |

Sample:
```bash
curl -sS "https://web.local/api/fortune-teller/" | jq -r '.fortune.text' | head -1
# Expected: Thai text, no \u escapes
```

---

## � G. Responsive Spot Check

| Viewport | index | each tool | Pass |
|----------|-------|-----------|------|
| 375 × 667 (iPhone) | ☐ | ☐ | ☐ |
| 768 × 1024 (iPad) | ☐ | ☐ | ☐ |
| 1440 × 900 (desktop) | ☐ | ☐ | ☐ |

Manual check: ไม่มี horizontal scroll, ปุ่ม clickable, layout ไม่ break

---

## 📦 H. Restructure Verification

### H1. File tree (after restructure)

```bash
ssh web.local 'tree -L 3 ~/projects/myapis/ --noreport'
```

| Check | Expected | Pass |
|-------|----------|------|
| `api/_includes/` มี shared classes | ✅ | ☐ |
| `api/<tool>/index.php` สั้นลง (logic แยก) | ✅ | ☐ |
| `public/assets/css/` populated | ✅ | ☐ |
| `public/includes/` มี shared partials (header, footer) | ✅ | ☐ |
| ไม่มีไฟล์ .bak / .old / .tmp | ✅ | ☐ |
| `git log` clean (no merge commits นอกสาย) | ✅ | ☐ |

### H2. Page restructure

| Check | Pass |
|-------|------|
| ทุก `<tool>.php` ใช้ shared `header.php` / `footer.php` | � |
| ไม่มี duplicated `<style>` block (CSS extract ไป main.css) | ☐ |
| ไม่มี duplicated `<script>` block (JS extract ไป main.js) | ☐ |
| Inline event handlers (`onclick=""`) → ย้ายไป `addEventListener` | ☐ |

---

## ⚡ I. Performance (sanity)

```bash
# 100 reqs, p95 budget
for t in health-calculator password-generator randomizer fortune-teller username-generator; do
  echo -n "$t: "
  for i in {1..100}; do
    curl -sS -o /dev/null -w "%{time_total}\n" \
      "https://web.local/api/$t/?$(canonical-params $t)"
  done | sort -n | awk '{a[NR]=$1} END{print "p95:", a[int(NR*0.95)]"s"}'
done
```

| Tool | p95 budget | Actual | Pass |
|------|-----------|--------|------|
| health-calculator | 100 ms | ___ | ☐ |
| password-generator | 50 ms | ___ | ☐ |
| randomizer | 50 ms | ___ | ☐ |
| fortune-teller | 50 ms | ___ | ☐ |
| username-generator | 100 ms | ___ | ☐ |
| qr-code-generator | 500 ms (external) | ___ | ☐ |
| promptpay-qr-generator | 100 ms | ___ | ☐ |

---

## ✅ Sign-off

| Section | Pass | Tester initials |
|---------|------|-----------------|
| A. Smoke | � | ___ |
| B1-B8. Tool-by-tool | ☐ | ___ |
| C1-C3. Security | ☐ | ___ |
| D1-D4. a11y | � | ___ |
| E1-E3. CSS | ☐ | ___ |
| F. i18n | ☐ | ___ |
| G. Responsive | ☐ | ___ |
| H1-H2. Restructure | ☐ | ___ |
| I. Performance | ☐ | ___ |

### Final verdict

- [ ] ✅ **NO REGRESSION** — merge approve
- [ ] ⚠️ **MINOR REGRESSION** — file P3 bug + approve
- [ ] ❌ **REGRESSION FOUND** — block merge + file P0/P1/P2

### Blockers filed

```
ISSUE-XXX: <title>
ISSUE-XXX: <title>
```

### Tester

QA (เทส) — Date: ____-__-__

---

## 📎 Appendix — Quick Commands

### Run smoke (1-liner)

```bash
BASE="https://web.local"; \
  curl -sS -o /dev/null -w "/                  %{http_code}\n" "$BASE/" && \
  curl -sS -o /dev/null -w "/api/health-calc/ %{http_code}\n" "$BASE/api/health-calculator/?type=bmi&weight=70&height=175" && \
  curl -sS -o /dev/null -w "/api/pwgen/       %{http_code}\n" "$BASE/api/password-generator/?length=16" && \
  curl -sS -o /dev/null -w "/api/randomizer/  %{http_code}\n" "$BASE/api/randomizer/?type=number&min=1&max=100" && \
  curl -sS -o /dev/null -w "/api/fortune/     %{http_code}\n" "$BASE/api/fortune-teller/" && \
  curl -sS -o /dev/null -w "/api/username/    %{http_code}\n" "$BASE/api/username-generator/"
```

### Run rate-limit burst

```bash
seq 1 150 | xargs -P 10 -I{} curl -sS -o /dev/null -w "{}=%{http_code}\n" \
  "https://web.local/api/password-generator/?length=16" | sort | uniq -c | sort -rn
```

### Run a11y (all pages)

```bash
mkdir -p /tmp/a11y
for t in index fortune-teller health-calculator password-generator promptpay-qr-generator \
         qr-code-generator randomizer username-generator; do
  npx lighthouse "https://web.local/$t.php" \
    --only-categories=accessibility --quiet \
    --output=json --output-path="/tmp/a11y/$t.json" \
    --chrome-flags="--headless" 2>/dev/null
  echo "$t: $(jq -r '.categories.accessibility.score * 100' /tmp/a11y/$t.json 2>/dev/null)/100"
done
```

---

**Related:** `docs/qa/test-plan-template.md`
