# ISSUE-005: Auto-calculate total_fortunes from glob

> **Type**: refactor / bug
> **Priority**: P2 - Medium
> **MoSCoW**: Should
> **Estimate**: S
> **Status**: Open

## 🎯 Background

ใน `api/fortune-teller/index.php` มี hard-coded `total_fortunes = 52` ซึ่งผูกกับจำนวนไฟล์ใน `predictions/` — ถ้าเพิ่มไฟล์ใหม่แล้วลืม update จะทำให้ random ไม่ครอบคลุม

## 👤 User Story

As a developer,
I want ค่า total_fortunes คำนวณจากไฟล์อัตโนมัติ,
So that ไม่ต้อง maintain count manually

## 📦 Scope

### In Scope
- ✅ เปลี่ยน `total_fortunes` ให้คำนวณจาก `glob(__DIR__ . '/predictions/*.json')`
- ✅ Cache ผล (avoid re-glob ทุก request)
- ✅ Validate count >= 1 (error ถ้าว่าง)

### Out of Scope
- ❌ ไม่ auto-add fortune files (ยังต้อง add manual)

## 📏 Current Code

```php
// api/fortune-teller/index.php (assumed)
$total_fortunes = 52;  // hard-coded!
$id = random_int(1, $total_fortunes);
$file = __DIR__ . "/predictions/{$id}.json";
```

## 🎯 Target Code

```php
function getTotalFortunes(): int
{
    static $total = null;
    if ($total === null) {
        $files = glob(__DIR__ . '/predictions/*.json');
        $total = count($files);
        if ($total < 1) {
            throw new RuntimeException('No fortune files found');
        }
    }
    return $total;
}

$total = getTotalFortunes();
$id = random_int(1, $total);
```

### Alternative: Use APCu cache

```php
$total = apcu_fetch('total_fortunes', $success);
if (!$success) {
    $files = glob(__DIR__ . '/predictions/*.json');
    $total = count($files);
    apcu_store('total_fortunes', $total, 3600);  // 1 hour
}
```

## ✅ Acceptance Criteria

- [ ] ลบ `$total_fortunes = 52` hard-coded
- [ ] ใช้ glob แทน
- [ ] Test ทั้ง edge case (0 files, 1 file, 100 files)
- [ ] Cache ผล — ไม่ glob ทุก request
- [ ] ถ้าไฟล์ว่าง → 500 error ที่เข้าใจง่าย

## 🔧 Technical Approach

**APCu หรือ static variable** ใน function — static ก็พอ เพราะ PHP-FPM process นึง cache ได้

⚠️ But: PHP-FPM มีหลาย worker → static ของ process นึงไม่ share กับอีก process — แต่ performance drop ไม่มากเพราะ glob เร็ว (< 1ms)

ถ้าต้องการ global cache ใช้:
- APCu (in-memory, fast)
- File (simple, slower)

## 📋 Tasks

- [ ] Implement
- [ ] Test edge cases (0, 1, 100)
- [ ] PR

## 🔖 Labels

`refactor`, `bug`, `fortune-teller`, `small`
