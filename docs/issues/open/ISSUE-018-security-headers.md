# ISSUE-018: Add HTTPS-only Security Headers (HSTS/CSP/Permissions-Policy)

> **Type**: feature / security
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: S
> **Status**: Open

## 🎯 Background

จาก audit (§ISSUE-011): nginx config มี 4 headers พื้นฐาน (X-Frame, X-Content-Type, Referrer-Policy, X-XSS) แต่**ขาด 3 headers ที่จำเป็นเมื่อใช้ HTTPS**:
- `Strict-Transport-Security` (HSTS)
- `Content-Security-Policy` (CSP)
- `Permissions-Policy`

## 👤 Owner

- DevOps: ออป

## 📦 Scope

### In Scope
- ✅ เพิ่ม `Strict-Transport-Security: max-age=31536000; includeSubDomains` (1 year)
- ✅ เพิ่ม `Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cloud.umami.is; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;`
- ✅ เพิ่ม `Permissions-Policy: geolocation=(), microphone=(), camera=()`
- ✅ ใส่ `always;` flag เพื่อให้ headers persist บน 4xx/5xx
- ✅ Document ใน `docker/nginx/default.conf` comments

### Out of Scope
- ❌ ไม่ configure TLS (defer — edge proxy handles per deployment.md)

## ✅ Acceptance Criteria

- [ ] `curl -I https://myapis.local/` แสดง 3 headers ใหม่
- [ ] Headers persist บน 404/500 responses (test with `curl -I` on invalid URL)
- [ ] CSP ไม่ break Umami analytics (verify script loads)
- [ ] `docker compose restart nginx` apply ได้โดยไม่ crash
- [ ] Lighthouse Best Practices ≥ 95

## 🔗 Dependencies

- Blocked by: none
- Related: ISSUE-001 (rate-limit headers), ISSUE-019 (HTTPS local-dev)

## 🔖 Labels

`feature`, `security`, `enhancement`, `nginx`
