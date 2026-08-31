# 📘 Product Brief — MyAPIs

> **Status**: Living document
> **Owner**: Project Manager
> **Last updated**: 2026-08-31

---

## 1. วิสัยทัศน์ (Vision)

**MyAPIs** เป็นคลัง **เครื่องมือและ REST API ขนาดเล็กที่พร้อมใช้งานทันที** สำหรับนักพัฒนาและผู้ใช้ทั่วไป
โดยเน้น 3 คุณค่าหลัก:

1. **Plug-and-play** — เรียก `https://<host>/api/<tool>/` ได้เลย ไม่ต้องสมัคร ไม่ต้อง API Key
2. **Open & Self-hostable** — โค้ดเปิดเผย ใครก็โคลนไปรันบนเครื่องตัวเองได้
3. **Multi-purpose** — ทั้งคำนวณสุขภาพ สร้างรหัสผ่าน สร้าง QR Code สุ่มเลข ดูดวง โดยใช้ Interface เดียวกัน

---

## 2. กลุ่มเป้าหมาย (Target Audience)

| กลุ่ม | ใช้ทำอะไร | ตัวอย่าง |
|------|----------|---------|
| 👨‍💻 **นักพัฒนา Full-stack** | เรียก API สร้าง QR / สุ่มรหัสผ่าน / คำนวณ BMI ไปใช้ในแอป | ใช้ `password-generator` API ในเว็บสมัครสมาชิก |
| 🧑‍🎓 **นักเรียน/นักศึกษา** | ศึกษาการออกแบบ REST API ที่ดี เห็นโค้ดจริง | อ่าน source ของ `username-generator` เพื่อเรียนรู้ OOP |
| 🏢 **ธุรกิจขนาดเล็ก (SME)** | สร้าง PromptPay QR / สร้าง Username ให้ลูกค้า | ร้านก๋วยเตี๋ยวสร้าง QR รับเงินผ่าน `promptpay-qr-generator` |
| 🌏 **ผู้ใช้ทั่วไป (ภาษาไทย/อังกฤษ/จีน)** | คำนวณ BMI, ดูดวง, สุ่มเลขผ่าน UI สวยงาม | ใช้ `health-calculator` ผ่านมือถือ |

---

## 3. ขอบเขตของผลิตภัณฑ์ (Scope)

### ✅ In-Scope (ปัจจุบัน)

- 7 เครื่องมือที่แต่ละตัวมี **Web UI + REST API + Specs Document** ครบชุด
- รองรับภาษา **TH / EN / ZH** ตามความเหมาะสมของแต่ละเครื่องมือ
- CORS เปิด (ทุก API เรียกจาก client อื่นได้)
- Deploy ได้ 4 รูปแบบ: Docker, Nginx + PHP-FPM, Apache (.htaccess), PHP built-in server

### 🚫 Out-of-Scope (ไม่ทำ)

- ระบบ Login / API Key / Rate Limit (เวอร์ชันปัจจุบัน)
- ฐานข้อมูล (ทุกอย่างเป็น stateless / static JSON)
- Mobile Native App (ใช้ Responsive Web แทน)
- Payment Gateway integration จริง (PromptPay แค่สร้าง QR)

---

## 4. วัตถุประสงค์ทางธุรกิจ (Business Objectives)

| ID | Objective | Metric | Target |
|----|-----------|--------|--------|
| BO-1 | ลดเวลาที่นักพัฒนาใช้เขียน utility ซ้ำ ๆ | เวลา dev เฉลี่ยต่อ feature | ≤ 2 วัน |
| BO-2 | ทำให้โปรเจกต์ Professional พร้อม Production | Lighthouse / API Adoption | Lighthouse ≥ 90, มีผู้ใช้จริง ≥ 100 req/วัน |
| BO-3 | ลดเวลา onboarding ของ Contributor ใหม่ | เวลาจาก clone → first PR | ≤ 1 วัน |
| BO-4 | ทำให้เอกสารครบถ้วน ไม่มี "guessing" | API ที่มี Spec + Test | 100% |

---

## 5. ตัวชี้วัดความสำเร็จ (Success Metrics)

- **API Coverage** — ทุก API ใน `/api/` มีไฟล์ใน `/docs/api-specs/` และ `/public/api-specs/`
- **Issue Closure Rate** — ≥ 80% ของ Issue ที่เปิดใน Sprint ต้องปิดใน Sprint เดียวกัน
- **Build Success Rate** — `docker compose up -d --build` ต้องสำเร็จ 100% บน PHP 8.2
- **Lighthouse Score** — Performance ≥ 90, Accessibility ≥ 90, Best Practices ≥ 90, SEO ≥ 90

---

## 6. ข้อจำกัดและสมมติฐาน (Constraints & Assumptions)

### Constraints

- **Stack**: PHP ≥ 7.4 (ทดสอบบน 8.2 เป็นหลัก) + Nginx/Apache
- **ไม่พึ่ง Composer** — โปรเจกต์ต้อง `git clone` แล้วรันได้ทันที
- **ไม่มี Build step** — แก้ PHP แล้วเห็นผลทันที
- **QR Code** สร้างผ่าน `api.qrserver.com` (goQR.me) — ต้องมีอินเทอร์เน็ต

### Assumptions

- ผู้ใช้ส่วนใหญ่รันบน Docker / Shared Hosting
- นักพัฒนาที่ contribute คุ้นเคย PHP และ REST API concept
- โปรเจกต์ไม่เก็บข้อมูลผู้ใช้ (privacy by design)

---

## 7. Roadmap ระดับสูง (High-Level Roadmap)

| Phase | ระยะเวลา | เป้าหมาย |
|------|--------|---------|
| **v2.5 — Polish** | Sprint ถัดไป | ปรับโครงสร้างเอกสาร + Coding Standards + Tests |
| **v2.6 — Hardening** | +1 เดือน | เพิ่ม Rate Limit, Cache, Health Check API |
| **v2.7 — Scale** | +2 เดือน | เพิ่มเครื่องมือใหม่ 2–3 ตัว, i18n ครบทุกหน้า |
| **v3.0 — Platform** | +3 เดือน | เปิด API Key / Dashboard / Webhook |

รายละเอียดดูที่ [`issues/README.md`](../issues/README.md) และ Issue ใน `issues/open/`

---

## 8. คำศัพท์ (Glossary)

| คำ | ความหมาย |
|----|---------|
| **Tool** | เครื่องมือ 1 ตัว เช่น `health-calculator` |
| **API Endpoint** | URL ที่ให้บริการ เช่น `/api/health-calculator/` |
| **Web UI** | หน้า HTML ใน `public/<tool>.php` |
| **Spec** | เอกสารสเปกของ API ใน `docs/api-specs/<tool>.md` |
| **Public Specs** | หน้าเว็บสเปกใน `public/api-specs/<tool>.php` (rendered) |
| **Prompt** | ไฟล์คำสั่งสำหรับทีมแต่ละบทบาท ใน `prompts/<role>-prompt.md` |
| **Issue** | ใบงานใน `docs/issues/` |

---

## 9. Stakeholders

| Role | Responsibility |
|------|---------------|
| **Project Manager (PM)** | Planning, Coordination, Quality Gate |
| **Designer** | UX/UI, Component Spec, Visual Consistency |
| **System Analyst (SA)** | Requirements, API Spec, Data Model |
| **Developer (Dev)** | Implementation, Testing, Deployment |
| **DevOps / SRE** | CI/CD, Infrastructure, Monitoring |
| **QA / Tester** | Manual Test, Acceptance Test, Regression |
