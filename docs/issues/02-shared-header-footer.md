# ใบงาน 02 — Shared Header / Footer / Analytics

> **สอดคล้อง goal01 ข้อ 2** — "Header include ไฟล์เดียวกัน, รวม analytics ไว้ใน header ที่เดียว"
> Design อ้างอิง: [`docs/designs/page-structure.md`](../designs/page-structure.md)
> โครงสร้าง: [`docs/designs/file-structures.md`](../designs/file-structures.md)

---

## 🎯 Objective

1. สร้าง partial กลาง `header.php` / `footer.php` / `helpers.php`
2. ย้าย `public/analytics.php` → `public/includes/analytics.php` และทำให้ idempotent
3. Refactor ทุกหน้าให้ใช้ header/footer แทน boilerplate + ลบ `<style>` inline
   และ `require analytics.php` ที่ซ้ำ 14 จุด

---

## 📁 ไฟล์ที่เกี่ยวข้อง

**สร้างใหม่**
- `public/includes/header.php`
- `public/includes/footer.php`
- `public/includes/helpers.php`
- `public/includes/analytics.php` (ย้ายมาจาก `public/analytics.php`)
- `public/assets/js/app.js`

**แก้ไข (refactor ทุกหน้า)**
- `public/index.php`
- `public/{fortune-teller,health-calculator,password-generator,promptpay-qr-generator,qr-code-generator,randomizer,username-generator}.php` (7 ไฟล์)
- `public/api-specs/{...ทั้ง 7 ไฟล์}.php` (7 ไฟล์)

**แก้ไข (path + guard)**
- `docker/php/analytics.php`
- `public/analytics.php` → ลบ (ย้ายแล้ว)

---

## 📋 งาน

### 1. สร้าง partial กลาง

- [ ] `public/includes/helpers.php` — ฟังก์ชัน `e()`, `getBaseUrl()`, `base_url()`
      (เนื้อหาตาม `page-structure.md` §3.3)
- [ ] `public/includes/header.php` — ตาม `page-structure.md` §3.1:
      - รับตัวแปร `$pageTitle`, `$pageDescription`, `$bodyClass`, `$activeNav`, `$breadcrumbs`
      - ใส่ `<link rel="stylesheet" href="/assets/css/style.css">`
      - `require_once helpers.php` + `require_once analytics.php` (จุดเดียวที่ include analytics)
      - render site header/nav + breadcrumb + เปิด `<main>`
- [ ] `public/includes/footer.php` — ตาม §3.2: ปิด `</main>` + site footer + `<script src="/assets/js/app.js" defer>` + ปิด body/html
- [ ] `public/assets/js/app.js` — copy จาก `docs/designs/mockups/app.js` (nav toggle + active link)

### 2. ย้าย + แก้ analytics (idempotent)

- [ ] `git mv public/analytics.php public/includes/analytics.php` (หรือสร้างใหม่ + ลบเก่า)
- [ ] เปลี่ยน guard เป็น return-early:

  ```php
  if (defined('MYAPIS_ANALYTICS_INCLUDED')) {
      return;
  }
  define('MYAPIS_ANALYTICS_INCLUDED', true);
  ```

- [ ] อัปเดต `$configCandidates` ใน `public/includes/analytics.php` ให้ชี้ไปหา `config.php`
      ที่ตำแหน่งใหม่ (เช่น `__DIR__ . '/../config.php'` = `public/config.php`)
- [ ] `docker/php/analytics.php` — เปลี่ยน guard เป็น return-early เหมือนกัน และ
      อัปเดต `$configCandidates` path ถ้าจำเป็น (ยังคงชี้ `public/config.php` ได้)
- [ ] ลบ `public/analytics.php` เดิม

### 3. Refactor หน้า homepage (`public/index.php`)

- [ ] ตั้งตัวแปร header (`$pageTitle`, `$pageDescription`, `$activeNav='home'`)
- [ ] `require __DIR__ . '/includes/header.php';` ที่บนสุด (แทน `<!DOCTYPE html>`…`<body>` เปิด)
- [ ] ลบ `<style>…</style>` และบรรทัด `require analytics.php` ออก
- [ ] เปลี่ยน markup header/hero/cards ให้ใช้ class ใหม่ (หรือคง class เดิมแล้วมี alias ใน css)
- [ ] `require __DIR__ . '/includes/footer.php';` ที่ท้ายสุด

### 4. Refactor tool pages (7 ไฟล์)

- [ ] แต่ละไฟล์: ตั้ง `$pageTitle`, `$pageDescription`, `$activeNav='tools'`, `$breadcrumbs`, `$bodyClass='page-tool'`
- [ ] `require __DIR__ . '/includes/header.php';` — `<main>` เนื้อหา — `require __DIR__ . '/includes/footer.php';`
- [ ] ลบ `<style>` + `require analytics.php`
- [ ] คง `<script>` เฉพาะ tool ไว้ก่อน footer
- [ ] ตรวจ path ลิงก์ (ไฟล์เหล่านี้อยู่ root ของ `public/` → ใช้ `includes/...`)

### 5. Refactor api-specs pages (7 ไฟล์)

- [ ] `require_once __DIR__ . '/../includes/helpers.php';` (เพื่อใช้ `getBaseUrl()`/`e()`)
- [ ] ตั้ง `$pageTitle`, `$activeNav='docs'`, `$breadcrumbs`, `$baseUrl = getBaseUrl('<tool>')`
- [ ] `require __DIR__ . '/../includes/header.php';` (อยู่ลึกลง 1 ชั้น → ใช้ `../includes/`)
- [ ] ลบ `getBaseUrl()` ที่ประกาศซ้ำในแต่ละไฟล์, ลบ `<style>`, ลบ `require analytics.php`
- [ ] เปลี่ยน `<?php echo $baseUrl; ?>` → `<?= e($baseUrl) ?>` (ทั้งใน code-block และ curl ตัวอย่าง)
- [ ] `require __DIR__ . '/../includes/footer.php';`

### 6. ตรวจ cross-file

- [ ] ไม่เหลือ `<style>` ใน `public/**/*.php` (grep ยืนยัน)
- [ ] ไม่เหลือ `require ...analytics.php` นอก `includes/header.php` (grep ยืนยัน)
- [ ] `getBaseUrl(` ประกาศที่ `helpers.php` ที่เดียว (grep ยืนยัน)

---

## ✅ Acceptance Criteria

1. ทุกหน้าใช้ `header.php`/`footer.php` ร่วมกัน (เปิดดูได้เหมือนเดิม)
2. `analytics.php` ถูกรวม **ที่เดียว** ใน `header.php` — ไม่มี `require analytics` กระจายในหน้า
3. analytics idempotent — เมื่อ Docker `auto_prepend_file` โหลด `docker/php/analytics.php`
   แล้ว header include `public/includes/analytics.php` อีกครั้ง **ไม่ emit ซ้ำ**
4. ไม่มี `<style>` inline หลงเหลือ; ทุกหน้าโหลด `/assets/css/style.css`
5. หน้า `api-specs/*` ใช้ `helpers.php` (ไม่ประกาศ `getBaseUrl()` ซ้ำ)

## 🔍 วิธีตรวจสอบ

```bash
# 1) syntax ทุกไฟล์ (docker)
for f in public/includes/*.php public/*.php public/api-specs/*.php docker/php/analytics.php; do
  docker run --rm -v "$PWD":/app -w /app php:8.2-cli php -l "$f" || exit 1
done

# 2) ไม่เหลือ <style> inline
grep -rn '<style>' public --include='*.php' || echo "no inline style ✅"

# 3) require analytics เหลือที่เดียว
grep -rn 'analytics.php' public --include='*.php'
# ควรเจอแค่ใน includes/header.php (และไฟล์ analytics เอง)

# 4) getBaseUrl ประกาศที่เดียว
grep -rn 'function getBaseUrl' public --include='*.php'

# 5) ตรวจ idempotent (อ่าน logic ด้วยตา/รันจำลอง)
#    - ตั้ง ANALYTICS_PROVIDER=umami แล้ว curl homepage 2 ครั้งดูว่า script tag ปรากฏครั้งเดียว
```

## ⚠️ หมายเหตุ / ความเสี่ยง

- **path ต่างกัน**: root pages ใช้ `includes/...`, api-specs ใช้ `../includes/...` — ตรวจให้ดี
- **dynamic class**: อย่าเผลอลบ class ที่ JS อ้างอิง (เช่น `.language-btn`, `.calc-option`,
  `.type-btn`, `.type-button`) ตอน refactor markup
- **ลิงก์เดิมห้ามเปลี่ยนชื่อไฟล์** — คง `public/<tool>.php` และ `public/api-specs/<tool>.php`
- Docker ยังมี `auto_prepend_file` → หลัง refactor ต้องยืนยันว่า analytics ไม่ซ้ำ (ข้อ 3 acceptance)
