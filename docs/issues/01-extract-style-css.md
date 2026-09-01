# ใบงาน 01 — Extract inline CSS → `style.css`

> **สอดคล้อง goal01 ข้อ 1** — "เปลี่ยนมาใช้ style.css"
> Design อ้างอิง: [`docs/designs/design-system.md`](../designs/design-system.md)
> Mockup อ้างอิง: [`docs/designs/mockups/style.css`](../designs/mockups/style.css)

---

## 🎯 Objective

สร้าง stylesheet ไฟล์เดียว `public/assets/css/style.css` ที่ครอบคลุมทุก class ที่ใช้ใน
ทุกหน้า และเป็นฐานให้ใบงาน 02 (header/footer) ใช้ลิงก์เดียว

> หมายเหตุ: ใบงานนี้**สร้างไฟล์เท่านั้น** ยังไม่แก้หน้าเว็บ (การลบ `<style>` inline และ
> ใส่ `<link>` ทำในใบงาน 02 พร้อม header/footer เพื่อไม่ให้แก้ซ้ำสองรอบ)

---

## 📁 ไฟล์ที่เกี่ยวข้อง

**สร้างใหม่**
- `public/assets/css/style.css`

**อ้างอิง (อ่านอย่างเดียว)**
- `docs/designs/mockups/style.css` — ใช้เป็นจุดเริ่มต้น
- `docs/designs/design-system.md` — design tokens + component list
- หน้าเว็บทั้งหมด (14 ไฟล์) เพื่อสำรวจ class จริง

---

## 📋 งาน

### 1. สร้างไฟล์ base

- [ ] สร้าง `public/assets/css/style.css` โดย copy เนื้อหาจาก `docs/designs/mockups/style.css`
- [ ] ตรวจสอบว่า `:root {}` มี design tokens ครบตาม `design-system.md`

### 2. สำรวจ class inventory (สำคัญ — กัน layout พัง)

- [ ] รันคำสั่งเพื่อรวบรวม class ทั้งหมดที่ใช้จริงในทุกหน้า:

  ```bash
  grep -rhoE 'class="[^"]+"' public --include='*.php' \
    | sed -E 's/class="//; s/"$//' \
    | tr ' ' '\n' \
    | grep -vE '^(active|show|is-active|is-open)$' \
    | sort -u
  ```

- [ ] เทียบผลลัพธ์กับ selector ใน `style.css` — class ใดยังไม่มี style ให้เพิ่มเข้าไป
  (คง style เดิมจาก `<style>` inline ของแต่ละหน้า อย่าเปลี่ยนหน้าตา)

### 3. Class หมวดหลักที่ต้องครอบคลุม (checklist)

**Common (ทุกหน้า)**
- [ ] `.container`, `.header`, `.nav`, `.breadcrumb`, `.content`
- [ ] `.section`, `.btn`, `.btn-primary`, `.btn-secondary`
- [ ] `.footer`, `.footer-links`, `.status-badge`

**Homepage / cards**
- [ ] `.tools-grid`, `.tool-card`, `.tool-icon`, `.tool-title`, `.tool-description`
- [ ] `.tool-features`, `.tool-actions`, `.stats`, `.stats-grid`, `.stat-number`, `.stat-label`

**API specs**
- [ ] `.features-grid`, `.feature-card`, `.lang-grid`, `.lang-item`
- [ ] `.categories-grid`, `.category-item`, `.endpoint`, `.method` (`.method.get`, `.method.post`)
- [ ] `.url`, `.code-block`, `.parameter-table`, `.required`, `.optional`
- [ ] `.response-box`, `.error-box`, `.try-it`

**Tool-specific (อย่าลืม — มีในหน้าเดิม)**
- [ ] fortune: `.controls`, `.language-toggle`, `.language-btn`, `.fortune-btn`, `.fortune-display`, `.fortune-card`, `.fortune-text`, `.fortune-id`, `.placeholder`, `.loading`
- [ ] health: `.calculator-selector`, `.calc-option`, `.unit-selector`, `.unit-option`, `.calculator-section`, `.form-group`, `.result-*`
- [ ] password: `.form-section`, `.form-row`, `.checkbox-group`, `.checkbox-item`
- [ ] qr-code: `.type-selector`, `.type-btn`, `.field-group`, `.dyn-list`, `.dyn-row`, `.add-row`, `.remove-row`, `.pill`, `.badge-row`, `.badge`, `.warning`, `.info`
- [ ] randomizer: `.control-section`, `.type-button`, `.type-icon`, `.result-display`
- [ ] username: `.theme-description`, `.checkbox-group`
- [ ] promptpay: `.warning`, `.info`

> ⚠️ รายการด้านบนคือตัวอย่าง — **ให้ยึดผลจากข้อ 2 (grep) เป็นหลัก** เพราะบางหน้ามี
> class เฉพาะมากกว่านี้

### 4. แก้ไขข้อแตกต่างจาก mockup

- [ ] mockup ใช้ class ใหม่แบบ BEM (`.tool-card__*`, `.btn--*`) — ถ้า implementer
      อยากคง class เดิม (`.tool-icon`, `.btn-primary`) ให้เพิ่ม **alias selector** ไว้ด้วยกัน
      เพื่อไม่ต้องแก้ markup ทั้งหมด (ดู group selector ตัวอย่างใน `design-system.md` §3.5)
- [ ] ลบ style เก่าที่ซ้ำ/ไม่ใช้แล้ว (ถ้ามี) หลังยืนยันว่าครอบคลุมครบ

---

## ✅ Acceptance Criteria

1. มีไฟล์ `public/assets/css/style.css` ที่รวม CSS จากทุกหน้าไว้ครบ
2. ทุก class จากคำสั่ง grep ข้อ 2 มี selector อยู่ในไฟล์ (ไม่มี class "กำพร้า")
3. เปิด mockup `docs/designs/mockups/*.html` ดูได้สวย ไม่พัง (ไฟล์นี้ใช้ `style.css` ตัวเดียวกัน)
4. ยังไม่เกิด regression — ณ จุดนี้หน้าจริงยังใช้ inline style เดิม (จะเปลี่ยนในใบงาน 02)

## 🔍 วิธีตรวจสอบ

```bash
# ตรวจว่าไฟล์มีอยู่และไม่ว่าง
test -s public/assets/css/style.css && echo OK

# เทียบ class จริง vs selector ใน css
grep -rhoE 'class="[^"]+"' public --include='*.php' \
  | sed -E 's/class="//; s/"$//' | tr ' ' '\n' | grep -v '^$' | sort -u > /tmp/used.txt
grep -oE '^\.[A-Za-z0-9_-]+' public/assets/css/style.css | tr -d '.' | sort -u > /tmp/defined.txt
comm -23 /tmp/used.txt /tmp/defined.txt   # ควรว่าง (หรือเป็น dynamic class เช่น active/show)
```

---

## ⚠️ หมายเหตุ / ความเสี่ยง

- อย่าเปลี่ยนโทนสี/ระยะห่างโดยไม่จำเป็น — งานนี้คือ **refactor (ย้ายที่)** ไม่ใช่ redesign
- class ที่เป็น dynamic (`active`, `show`, `is-open`, `is-active`) ไม่ต้องมี selector แยก
  แต่ state selector (เช่น `.language-btn.active`) ต้องมี
- หน้า `api-specs/qr-code-generator.php` มี CSS สั้นกว่าไฟล์อื่น (ใช้ class ต่างกัน) — ตรวจเป็นพิเศษ
