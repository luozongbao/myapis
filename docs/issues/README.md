# MyAPIs — Implementation Worksheets (ใบงาน)

ชุดใบงานสำหรับ implement ตาม [`docs/goal01.md`](../goal01.md) โดยอ้างอิง design ที่อยู่ใน
[`docs/designs/`](../designs/) ใช้ไล่ทำตามลำดับ **01 → 02 → 03**

## 📋 สรุปใบงาน

| ID | ใบงาน | สอดคล้อง goal01 | เนื้อหาหลัก |
| --- | --- | --- | --- |
| 01 | [Extract inline CSS → `style.css`](01-extract-style-css.md) | ข้อ 1 | สร้าง stylesheet ไฟล์เดียว แทน `<style>` inline |
| 02 | [Shared header / footer / analytics](02-shared-header-footer.md) | ข้อ 2 | สร้าง partial กลาง + รวม analytics ไว้ใน header |
| 03 | [Security hardening](03-security-hardening.md) | ข้อ 3 | Nginx / PHP / app-level security |
| 04 | [Restructure & verify file structure](04-restructure-verify.md) | ภาพรวม | ไล่ Before→After ทีละข้อ + verify โครงสร้างตรง `file-structures.md` |

## 🔗 ความสัมพันธ์ระหว่างใบงาน

```text
01 (สร้าง style.css) ──┐
                        ├──> 02 (header/footer ใช้ style.css + analytics)
                        │        │
                        │        └──> 03 (security: CSP ต้องรู้ว่ามี style.css/app.js)
                        │
                        └──────────────> 04 (verify โครงสร้างตรง file-structures.md)
```

- **01** สร้าง stylesheet อย่างเดียว (additive, ไม่เสี่ยง) — ยังไม่แตะหน้าเว็บ
- **02** สร้าง header/footer แล้ว refactor ทุกหน้าให้ใช้ (เป็นจุดที่**ลบ** `<style>` inline
  และ `require analytics.php` ออกจากทุกหน้า + **ย้าย** `public/analytics.php` → `includes/`)
- **03** ต่อยอดได้ทันที เพราะหลัง 02 แล้ว ทุกหน้ามี layout + asset แยกจากกัน (CSP ตั้งได้เข้ม)
- **04** ปิดงาน — ไล่ทุกรายการ Before→After ใน `file-structures.md` เป็น checklist
  และ verify ว่าโครงสร้างจริงตรงกับ design (เป็นใบงาน "ตรวจโครงสร้าง" โดยเฉพาะ)

## 🚦 ก่อนเริ่ม / หลังจบ (Definition of Done ทุกใบงาน)

1. `docker compose config` ผ่าน (ไม่พัง compose)
2. `php -l` ผ่านทุกไฟล์ PHP ที่แก้ (ดูคำสั่งในแต่ละใบงาน)
3. รัน `docker compose up -d --build` แล้ว `curl -I http://localhost:8080` ตอบ `200`
4. ไม่มี regression: homepage / tool page / api-specs เปิดได้ หน้าตาไม่พัง

## ✅ การตรวจสอบความสมบูรณ์ (Completeness Checklist)

นี่คือรายการสุดท้ายที่ใช้ยืนยันว่า "ใบงานพร้อม implement" (goal01 ทั้ง 3 ข้อถูกครอบคลุม):

- [ ] **goal01 #1 (style.css)**
  - [ ] มีใบงาน 01 ระบุไฟล์เป้าหมาย `public/assets/css/style.css`
  - [ ] ระบุ inventory ของ class ที่ต้องครอบคลุม
  - [ ] ระบุว่าหน้าไหนที่ต้องแก้
- [ ] **goal01 #2 (header include เดียว)**
  - [ ] มีใบงาน 02 ระบุ partial ที่ต้องสร้าง (`header.php`, `footer.php`, `helpers.php`)
  - [ ] ระบุการย้าย `public/analytics.php` → `includes/` และทำให้ idempotent
  - [ ] ระบุการลบ `require analytics.php` ออกจากทุกหน้า (เหลือที่ header ที่เดียว)
- [ ] **goal01 #3 (security)**
  - [ ] มีใบงาน 03 ครอบคลุม: output escaping (XSS), input validation (API), nginx headers/CSP/rate-limit/deny, PHP hardening, idempotent analytics
- [ ] **ครบทุกหน้า (ไม่ตกหล่น)**
  - [ ] 7 tool pages + 7 api-specs pages + `index.php` ถูกระบุในใบงาน 01 และ 02
- [ ] **โครงสร้างตรงกับ file-structures.md (ไม่ตกหล่น/ไม่เหลือไฟล์เก่า)**
  - [ ] มีใบงาน 04 ไล่ตาราง Before→After ครบ (รวมการย้าย `public/analytics.php`)
  - [ ] มีขั้นตอน verify: `tree`/`find` เทียบกับ diagram เป้าหมาย
- [ ] **Dependency ชัดเจน** — ใบงานบอกลำดับและ prerequisite กัน
- [ ] **Verifiable** — ทุกใบงานมี acceptance criteria + คำสั่ง verify

> ผลการตรวจสอบจริง ดูในหัวข้อ "ผลการตรวจสอบ" ท้ายไฟล์ `README` นี้

---

## 📁 ตำแหน่งไฟล์อ้างอิง

| สิ่งที่ต้องสร้าง/แก้ | ตำแหน่ง | ใบงาน |
| --- | --- | --- |
| stylesheet ไฟล์เดียว | `public/assets/css/style.css` | 01 |
| JS กลาง | `public/assets/js/app.js` | 02 |
| partial: header | `public/includes/header.php` | 02 |
| partial: footer | `public/includes/footer.php` | 02 |
| partial: helpers | `public/includes/helpers.php` | 02 |
| partial: analytics | `public/includes/analytics.php` | 02 |
| docker analytics (guard) | `docker/php/analytics.php` | 02, 03 |
| nginx security | `docker/nginx/default.conf` | 03 |
| php hardening | `docker/php/php.ini.tpl` | 03 |
| api input validation | `api/*/index.php` | 03 |
| restructure + verify | (ภาพรวมตาม `file-structures.md`) | 04 |

---

## 📌 ผลการตรวจสอบ (ณ วันที่เขียน)

| รายการ | สถานะ | หมายเหตุ |
| --- | --- | --- |
| design-system.md ครอบคลุม CSS tokens + components | ✅ | ดู [`designs/design-system.md`](../designs/design-system.md) |
| page-structure.md กำหนด header/footer/helpers/analytics | ✅ | ดู [`designs/page-structure.md`](../designs/page-structure.md) |
| file-structures.md กำหนดโครงสร้างเป้าหมาย | ✅ | ดู [`designs/file-structures.md`](../designs/file-structures.md) |
| mockups (style.css + 3 html + app.js) | ✅ | ดู [`designs/mockups/`](../designs/mockups/) |
| ใบงาน 01 / 02 / 03 | ✅ | ไฟล์ในโฟลเดอร์นี้ |
| ใบงาน 04 (restructure & verify) | ✅ | [`04-restructure-verify.md`](04-restructure-verify.md) |

**สรุป: ใบงานพร้อม implement แล้ว** — เริ่มที่ [`01-extract-style-css.md`](01-extract-style-css.md)
แล้วปิดงานด้วย [`04-restructure-verify.md`](04-restructure-verify.md) เพื่อยืนยันโครงสร้างตรง design
