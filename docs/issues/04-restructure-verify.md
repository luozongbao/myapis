# ใบงาน 04 — Restructure & Verify File Structure

> **สอดคล้อง goal01 (ภาพรวม)** — ทำให้โครงสร้างสุดท้ายตรงกับ
> [`docs/designs/file-structures.md`](../designs/file-structures.md) ทุกข้อ
> ใบงานนี้เป็น **ตัวปิดงาน** ไล่ทุกรายการในตาราง Before→After เป็น checklist
> และจบด้วยการ verify ว่าโครงสร้างตรงกับ design

> ⚠️ ทำ**หลัง**ใบงาน 01–03 เสร็จ (หรืออย่างน้อยหลัง 01+02) เพราะใช้ผลลัพธ์ของงานก่อนหน้า
> ใบงานนี้ไม่สร้าง/แก้ logic ใหม่ — มีหน้าที่ **ตรวจความครบของโครงสร้าง** เท่านั้น

---

## 🎯 Objective

1. ไล่ทุกรายการ "Before → After" ใน `file-structures.md` เป็น checklist
2. ยืนยันว่าไฟล์ที่ต้องย้าย / สร้างใหม่ / ลบ / แก้ อยู่ในตำแหน่งเป้าหมายครบ
3. ตรวจว่าไม่มีไฟล์ตกค้างในตำแหน่งเก่า (เช่น `public/analytics.php`)
4. ตรวจว่าโครงสร้างจริง `tree` ตรงกับ diagram เป้าหมาย

---

## 📁 ตาราง Before → After (Mapping Checklist)

> ที่มา: `docs/designs/file-structures.md` §2 "การเปลี่ยนแปลงเทียบกับโครงสร้างเดิม"

| # | Before | After | ประเภท | ตรวจ |
| --- | --- | --- | --- | --- |
| 1 | `<style>` inline ใน 14 ไฟล์ | `public/assets/css/style.css` | แก้ + สร้าง | [ ] |
| 2 | `require analytics.php` ใน 14 ไฟล์ | รวมใน `includes/header.php` ที่เดียว | แก้ | [ ] |
| 3 | `public/analytics.php` | `public/includes/analytics.php` | **ย้าย (move)** | [ ] |
| 4 | `getBaseUrl()` ซ้ำใน 7 api-specs | `includes/helpers.php` | แก้ + สร้าง | [ ] |
| 5 | (ไม่มี) | `public/includes/header.php` | สร้าง | [ ] |
| 6 | (ไม่มี) | `public/includes/footer.php` | สร้าง | [ ] |
| 7 | (ไม่มี) | `public/includes/helpers.php` | สร้าง | [ ] |
| 8 | (ไม่มี) | `public/assets/js/app.js` | สร้าง | [ ] |
| 9 | `docker/php/analytics.php` (guard ไม่ return) | idempotent (return early) | แก้ | [ ] |
| 10 | nginx headers พื้นฐาน | + CSP/HSTS/Permissions-Policy/rate-limit/deny | แก้ | [ ] |
| 11 | (ไม่มี) | `.gitignore` เพิ่ม `public/config.php` | แก้ | [ ] |

---

## 📋 งาน (ไล่ทีละข้อ)

### ข้อ 3 — ย้ายไฟล์ (รายการเดียวที่ต้อง move จริง)

- [ ] ย้าย `public/analytics.php` → `public/includes/analytics.php`

  ```bash
  mkdir -p public/includes
  git mv public/analytics.php public/includes/analytics.php
  # หรือ (ถ้าไม่ใช้ git mv)
  # mv public/analytics.php public/includes/analytics.php
  ```

- [ ] ยืนยันว่า `public/analytics.php` **ไม่มีอยู่แล้ว** (ไฟล์เก่าหายไป):

  ```bash
  test ! -e public/analytics.php && echo "moved ✅" || echo "STILL EXISTS ❌"
  ```

### ข้อ 1, 2, 4 — ยืนยัน refactor หน้าเว็บ

- [ ] ไม่มี `<style>` inline หลงเหลือใน `public/**/*.php`:

  ```bash
  grep -rn '<style>' public --include='*.php' || echo "no inline style ✅"
  ```

- [ ] `require analytics` เหลือใน `includes/header.php` ที่เดียว:

  ```bash
  grep -rn 'analytics.php' public --include='*.php'
  # คาดหวัง: เจอเฉพาะใน includes/header.php (require_once) + includes/analytics.php (ไฟล์ตัวเอง)
  ```

- [ ] `function getBaseUrl` ประกาศที่ `includes/helpers.php` ที่เดียว:

  ```bash
  grep -rn 'function getBaseUrl' public --include='*.php'
  # คาดหวัง: includes/helpers.php เท่านั้น
  ```

### ข้อ 5–8 — ยืนยันไฟล์ใหม่ครบ

- [ ] ตรวจว่า 5 ไฟล์ใหม่มีอยู่จริงและไม่ว่าง:

  ```bash
  for f in public/includes/header.php public/includes/footer.php \
           public/includes/helpers.php public/includes/analytics.php \
           public/assets/css/style.css public/assets/js/app.js; do
    test -s "$f" && echo "OK   $f" || echo "MISS $f ❌"
  done
  ```

### ข้อ 9 — ยืนยัน docker analytics idempotent

- [ ] `docker/php/analytics.php` ใช้ return-early guard:

  ```bash
  grep -n 'MYAPIS_ANALYTICS_INCLUDED' docker/php/analytics.php public/includes/analytics.php
  # คาดหวัง: แต่ละไฟล์มี `if (defined(...)) { return; }` + `define(...)`
  ```

### ข้อ 10 — ยืนยัน nginx security (ผลลัพธ์จากใบงาน 03)

- [ ] header ใหม่มีครบ:

  ```bash
  grep -nE 'Content-Security-Policy|Strict-Transport-Security|Permissions-Policy' docker/nginx/default.conf
  ```

- [ ] deny rule สำหรับ `includes/` และ `config.php` มีอยู่:

  ```bash
  grep -nE 'includes/|config\.php' docker/nginx/default.conf
  ```

### ข้อ 11 — ยืนยัน .gitignore

- [ ] `public/config.php` ถูก ignore:

  ```bash
  grep -n 'config.php' .gitignore || echo "add public/config.php to .gitignore ❌"
  ```

---

## ✅ Acceptance Criteria

1. ตาราง Before→After ครบ 11 ข้อถูกติ๊กหมด
2. `tree` ของโครงสร้างจริง ตรงกับ diagram ใน `file-structures.md`
   (ไม่มีไฟล์เก่าตกค้าง, มีไฟล์ใหม่ครบ)
3. ผ่านทุกคำสั่งตรวจใน "งาน" ข้างบน (ทุก `✅`, ไม่มี `❌`)

## 🔍 วิธีตรวจสอบ (final gate)

```bash
# 1) แสดงโครงสร้างจริงเทียบกับเป้าหมาย
find . -path ./node_modules -prune -o -type f -print \
  | grep -vE '\.git/' | sort | sed 's|^\./||' > /tmp/actual.txt
# ไล่ตาดูว่า public/includes/*, public/assets/css/style.css, public/assets/js/app.js อยู่ครบ

# 2) ไฟล์เก่าที่ควรหาย
for gone in public/analytics.php; do
  test ! -e "$gone" && echo "removed ✅ $gone" || echo "STILL EXISTS ❌ $gone"
done

# 3) syntax ทุกไฟล์ (docker)
for f in public/includes/*.php public/*.php public/api-specs/*.php docker/php/analytics.php; do
  docker run --rm -v "$PWD":/app -w /app php:8.2-cli php -l "$f" || exit 1
done

# 4) compose + runtime
docker compose config -q && docker compose up -d --build
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080   # 200
```

---

## ⚠️ หมายเหตุ / ความเสี่ยง

- ข้อเดียวที่ต้อง **move** จริงคือ `public/analytics.php` — อย่าเผลอย้ายไฟล์อื่น
  (page/specs/endpoint ตั้งใจคงชื่อเดิมเพื่อไม่ให้ URL พัง ตาม convention ข้อ 4–5)
- หลังย้าย `analytics.php` ต้อง confirm ว่า nginx ยังอนุญาตให้ header `require` มันได้
  (header.php ใช้ `require_once` จาก PHP ฝั่ง server — ไม่เกี่ยวกับ nginx route)
- ถ้าบางข้อทำซ้ำกับใบงาน 01–03 แล้ว ให้ติ๊กว่า "ทำแล้วในใบงานก่อนหน้า" ไม่ต้องทำซ้ำ
