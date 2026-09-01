# MyAPIs — Target File Structure (Professional)

> เป้าหมาย: กำหนดโครงสร้างโฟลเดอร์/ไฟล์เป้าหมายที่เป็นมาตรฐาน (Goal 01)
> อ่านคู่กับ `design-system.md` และ `page-structure.md`

---

## 1. โครงสร้างเป้าหมาย (Target)

```text
myapis/
├── api/                                # REST API endpoints (คงเดิม)
│   ├── fortune-teller/
│   │   ├── index.php
│   │   └── predictions/*.json
│   ├── health-calculator/index.php
│   ├── password-generator/index.php
│   ├── promptpay-qr-generator/index.php
│   ├── qr-code-generator/index.php
│   ├── randomizer/index.php
│   └── username-generator/index.php
│
├── public/                             # Web document root
│   ├── index.php                       # Homepage (tools directory) — บางลง
│   ├── fortune-teller.php              # Tool pages — บางลง (เหลือเฉพาะเนื้อหา)
│   ├── health-calculator.php
│   ├── password-generator.php
│   ├── promptpay-qr-generator.php
│   ├── qr-code-generator.php
│   ├── randomizer.php
│   ├── username-generator.php
│   ├── api-specs/                      # API documentation pages
│   │   ├── fortune-teller.php
│   │   ├── health-calculator.php
│   │   ├── password-generator.php
│   │   ├── promptpay-qr-generator.php
│   │   ├── qr-code-generator.php
│   │   ├── randomizer.php
│   │   └── username-generator.php
│   ├── includes/                       # ★ ใหม่ — shared partials
│   │   ├── header.php                  #   <head> + style.css + analytics + site header/nav
│   │   ├── footer.php                  #   </main> + site footer + app.js
│   │   ├── helpers.php                 #   e(), getBaseUrl(), base_url()
│   │   └── analytics.php               #   (ย้ายมาจาก public/analytics.php, idempotent)
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css               # ★ ใหม่ — stylesheet ไฟล์เดียว
│   │   ├── js/
│   │   │   └── app.js                  # ★ ใหม่ — JS กลาง (nav toggle, active, copy)
│   │   └── images/                     # (คงไว้สำหรับ asset ในอนาคต)
│   ├── analytics.php                   # (เก่า) → ลบ/ย้ายไป includes/analytics.php
│   └── config.php.example              # (คงไว้) template ค่า analytics บน shared hosting
│
├── docker/
│   ├── entrypoint.sh
│   ├── nginx/default.conf              # เพิ่ม security headers / rate-limit / deny
│   └── php/
│       ├── analytics.php               # ปรับให้ idempotent + update config path
│       ├── php.ini.tpl                 # เพิ่ม security directives
│       └── opcache.ini
│
├── docs/
│   ├── goal01.md
│   ├── prompts.md
│   ├── designs/                        # ★ งานชุดนี้
│   │   ├── design-system.md
│   │   ├── page-structure.md
│   │   ├── file-structures.md
│   │   └── mockups/
│   │       ├── style.css
│   │       ├── homepage.html
│   │       ├── tool-page.html
│   │       └── api-specs.html
│   └── issues/                         # ★ ใบงาน implement
│       ├── README.md
│       ├── 01-extract-style-css.md
│       ├── 02-shared-header-footer.md
│       └── 03-security-hardening.md
│
├── docker-compose.yml
├── Dockerfile
├── example.env
├── .gitignore
├── README.md
└── RELEASE.md
```

---

## 2. การเปลี่ยนแปลงเทียบกับโครงสร้างเดิม

| จาก (Before) | ไป (After) | เหตุผล |
| --- | --- | --- |
| `<style>` inline ใน 14 ไฟล์ | `public/assets/css/style.css` | Goal01 #1 — ดูแลที่เดียว, cache ได้ |
| `require analytics.php` ใน 14 ไฟล์ | `require` ใน `includes/header.php` ที่เดียว | Goal01 #2 — รวมไว้ใน header |
| `public/analytics.php` | `public/includes/analytics.php` | รวม shared partial ไว้ด้วยกัน |
| `getBaseUrl()` ซ้ำในทุก api-specs | `includes/helpers.php` | ลด duplication, escape กลาง |
| (ไม่มี) | `public/includes/header.php`, `footer.php`, `helpers.php` | shared layout |
| (ไม่มี) | `public/assets/js/app.js` | JS กลาง |
| `docker/php/analytics.php` (guard ไม่ return) | idempotent (return early) | กัน snippet ซ้ำ |
| nginx headers พื้นฐาน | + CSP, HSTS, Permissions-Policy, rate-limit, deny config.php | Goal01 #3 — security |

---

## 3. กฎโครงสร้าง (Conventions)

1. **`public/` คือ document root เดียว** — ห้ามมี `.php` ที่ผู้ใช้เรียกตรงนอก `public/` (ยกเว้น `api/` ที่ nginx route เอง)
2. **shared partial อยู่ใน `public/includes/`** — ตั้งชื่อ `*.php` ตัวเล็ก underscore
3. **asset อยู่ใน `public/assets/{css,js,images}/`** — อ้างอิงด้วย path สัมบูรณ์ `/assets/css/style.css`
4. **api endpoint = `api/<tool>/index.php`** — คงเดิม ไม่ยุ่งในงานนี้ (ยกเว้น input validation ใน security)
5. **หน้าเว็บ = `public/<tool>.php`**, **docs = `public/api-specs/<tool>.php`** — คงชื่อไฟล์เดิมเพื่อไม่ให้ URL/ลิงก์เดิมพัง
6. **ห้าม commit `public/config.php` และ `.env`** — มีใน `.gitignore` แล้ว (ตรวจซ้ำใน security issue)

---

## 4. ทำไมต้องเป็นแบบนี้

- **Separation of concerns**: layout (partials) / style (css) / logic (helpers) / content (pages) แยกจากกัน
- **DRY**: boilerplate ถูกเขียนครั้งเดียว ลด 14 จุดเหลือ 1 จุด
- **Deployable**: nginx route `/api/*` ไป `api/`, ที่เหลือเสิร์ฟจาก `public/` — โครงสร้างนี้รองรับ Docker + shared hosting
- **Secure**: secret (`.env`, `config.php`) ไม่อยู่ใน document root ที่เข้าถึงได้; `includes/` ยังอยู่ใน `public/` จึงต้องมี nginx deny ป้องกันการเรียก `includes/*.php` ตรง (ดู security issue)
