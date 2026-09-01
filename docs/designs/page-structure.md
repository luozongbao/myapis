# MyAPIs — Page Structure: Shared Partials

> เป้าหมาย: ออกแบบไฟล์กลาง (`header.php`, `footer.php`, `helpers.php`,
> `analytics.php`) ที่ทุกหน้า (Website + API Tools + API Specs) ใช้ร่วมกัน
> (Goal 01 ข้อ 2 — รวม `include analytics.php` มาไว้ใน header ที่เดียว)

---

## 1. แนวคิด

แทนที่ทุกหน้า (`public/*.php`, `public/api-specs/*.php`) จะเขียน
`<!DOCTYPE html>`, `<head>`, `<style>`, และ `require analytics.php` ซ้ำกันเอง
เราจะดึงส่วนที่ซ้ำกันออกมาเป็น partial กลาง แล้วแต่ละหน้าเหลือแค่ **เนื้อหาเฉพาะหน้า**

```text
  header.php  ──>  <!DOCTYPE html> + <head> + style.css + analytics + site header/nav
  [ หน้าเฉพาะ ] ──>  เฉพาะ <main> เนื้อหาของหน้านั้น
  footer.php  ──>  </main> + site footer + app.js + </body></html>
```

### ข้อดี
1. เปลี่ยน title / nav / meta / analytics ที่เดียว
2. ลด `require analytics.php` จาก 14 จุด เหลือ 1 จุด (ใน header)
3. หน้าใหม่สร้างเร็ว ไม่ต้อง copy boilerplate

---

## 2. ไฟล์ที่ต้องสร้าง

| ไฟล์ | หน้าที่ |
| --- | --- |
| `public/includes/header.php` | เปิด HTML + `<head>` + link `style.css` + include analytics + site header/nav + breadcrumb |
| `public/includes/footer.php` | ปิด `<main>` + site footer + `app.js` + ปิด `</body></html>` |
| `public/includes/helpers.php` | ฟังก์ชันกลาง: `e()`, `getBaseUrl()`, `base_url()` |
| `public/includes/analytics.php` | (ย้ายมาจาก `public/analytics.php`) ทำเป็น idempotent |
| `public/assets/js/app.js` | JS กลาง (mobile nav toggle, active nav, copy code) |

---

## 3. Contract ของ partial

### 3.1 `header.php`

**ตัวแปร input (ตั้งก่อน `require`)**

| ตัวแปร | type | บังคับ | คำอธิบาย |
| --- | --- | --- | --- |
| `$pageTitle` | string | ✅ | `<title>` และ heading |
| `$pageDescription` | string | ❌ | meta description + subtitle |
| `$bodyClass` | string | ❌ | class เพิ่มของ `<body>` |
| `$activeNav` | string | ❌ | `home` / `tools` / `docs` สำหรับเน้นเมนู |
| `$breadcrumbs` | array | ❌ | `[ ['label'=>'Home','url'=>'index.php'], ... ]` |

**pseudo-code**

```php
<?php
// header.php — ไม่มี output ก่อนหน้านี้ (หรือใช้ ob_start ถ้าจำเป็น)
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/analytics.php';   // <-- รวม analytics ที่นี่ที่เดียว

$pageTitle       = $pageTitle       ?? 'MyAPIs';
$pageDescription = $pageDescription ?? 'Developer tools and APIs collection';
$bodyClass       = $bodyClass       ?? '';
$activeNav       = $activeNav       ?? '';
$breadcrumbs     = $breadcrumbs     ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — MyAPIs</title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">
    <header class="site-header">
        <div class="container site-header__inner">
            <a class="site-brand" href="/index.php">
                <span class="site-brand__logo">🚀</span> MyAPIs
            </a>
            <button class="site-nav__toggle" aria-label="Toggle menu" aria-expanded="false">☰</button>
            <nav class="site-nav">
                <div class="site-nav__links">
                    <a class="site-nav__link <?= $activeNav==='home' ? 'is-active' : '' ?>" href="/index.php">Home</a>
                    <a class="site-nav__link <?= $activeNav==='tools' ? 'is-active' : '' ?>" href="/index.php#tools">Tools</a>
                    <a class="site-nav__link <?= $activeNav==='docs' ? 'is-active' : '' ?>" href="/api-specs/health-calculator.php">Docs</a>
                </div>
            </nav>
        </div>
    </header>

    <?php if ($breadcrumbs): ?>
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <div class="container">
            <?php foreach ($breadcrumbs as $i => $crumb): ?>
                <?php if ($i > 0): ?><span class="breadcrumb__sep">/</span><?php endif; ?>
                <?php if (!empty($crumb['url'])): ?>
                    <a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a>
                <?php else: ?>
                    <span aria-current="page"><?= e($crumb['label']) ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php endif; ?>

    <main class="main">
```

### 3.2 `footer.php`

```php
<?php // footer.php ?>
    </main>

    <footer class="site-footer">
        <div class="container site-footer__inner">
            <p class="site-footer__tagline">🚀 MyAPIs — a collection of developer tools &amp; APIs</p>
            <div class="site-footer__links">
                <a href="/index.php">Home</a>
                <a href="/api-specs/health-calculator.php">Documentation</a>
                <a href="/index.php#tools">All Tools</a>
            </div>
            <p class="site-footer__copyright">© <?= date('Y') ?> MyAPIs. All rights reserved.</p>
        </div>
    </footer>

    <script src="/assets/js/app.js" defer></script>
</body>
</html>
```

### 3.3 `helpers.php`

```php
<?php
// helpers.php — ฟังก์ชันกลาง (idempotent via require_once)

if (!function_exists('e')) {
    /**
     * Escape HTML output (ป้องกัน XSS). ใช้กับทุกค่าที่มาจากผู้ใช้/เซิร์ฟเวอร์
     */
    function e($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('getBaseUrl')) {
    /**
     * สร้าง base URL ของ tool หนึ่ง (server-agnostic).
     * คืนค่าที่ escape แล้วเพื่อความปลอดภัย
     */
    function getBaseUrl(string $toolName): string {
        $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $proto  = $https ? 'https' : 'http';
        return $proto . '://' . $host . '/api/' . $toolName . '/';
    }
}

if (!function_exists('base_url')) {
    /** ใช้ทำลิงก์ relative ที่ทนต่อ sub-directory deployment */
    function base_url(string $path = ''): string {
        return '/' . ltrim($path, '/');
    }
}
```

> หมายเหตุ: `getBaseUrl()` ใช้ `$_SERVER['HTTP_HOST']` (มาจาก header `Host`)
> — ต้อง escape ตอน echo เสมอ (`<?= e($baseUrl) ?>`) หรือตั้งค่า
> host allow-list ที่ nginx (ดู security) เพื่อกัน Host-header injection

### 3.4 `analytics.php` (idempotent — สำคัญ!)

**ปัญหาเดิม**: `public/analytics.php` กับ `docker/php/analytics.php` ใช้ guard
`MYAPIS_ANALYTICS_INCLUDED` แค่กัน `define()` ซ้ำ แต่**ไม่ return** → ถ้าโหลดทั้งคู่
(กรณี Docker มี `auto_prepend_file` + header include) snippet จะซ้ำ 2 ครั้ง

**แก้**: เปลี่ยน guard เป็น return-early

```php
<?php
if (defined('MYAPIS_ANALYTICS_INCLUDED')) {
    return;                       // <-- idempotent: โหลดครั้งเดียวเท่านั้น
}
define('MYAPIS_ANALYTICS_INCLUDED', true);

if (PHP_SAPI === 'cli') {
    return;
}

// ... (logic เดิม: อ่าน env -> config.php fallback -> skip /api & JSON -> emit) ...
```

Logic ที่เหลือคงเดิม (อ่าน `ANALYTICS_PROVIDER`, fallback `config.php`, ข้าม `/api/*`
และ `Accept: application/json`, `htmlspecialchars` ก่อน echo)

---

## 4. วิธีใช้ในแต่ละประเภทหน้า

### 4.1 Website page (homepage `public/index.php`)

```php
<?php
$pageTitle       = 'Developer Tools Collection';
$pageDescription = 'A comprehensive collection of developer tools and APIs.';
$activeNav       = 'home';
require __DIR__ . '/includes/header.php';
?>
    <section class="hero">
        <h1>🚀 MyAPIs</h1>
        <p>A comprehensive collection of developer tools and APIs…</p>
        <span class="badge badge--success">✅ All Systems Operational</span>
    </section>

    <!-- tools grid: 7 tool cards -->
<?php require __DIR__ . '/includes/footer.php'; ?>
```

### 4.2 Tool page (`public/fortune-teller.php`)

```php
<?php
$pageTitle       = 'ดูดวงออนไลน์ — Fortune Teller';
$pageDescription = 'คำพยากรณ์ 52 บท ครอบคลุมทุกเรื่องในชีวิต';
$activeNav       = 'tools';
$bodyClass       = 'page-tool';
$breadcrumbs     = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Fortune Teller'],
];
require __DIR__ . '/includes/header.php';
?>
    <div class="container container--narrow">
        <div class="tool-head">…</div>
        <div class="controls">…</div>
        <div class="fortune-display">…</div>
    </div>
    <script>/* JS เฉพาะ tool (inline เฉพาะหน้าได้) */</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
```

### 4.3 API Specs page (`public/api-specs/fortune-teller.php`)

```php
<?php
require_once __DIR__ . '/../includes/helpers.php';
$baseUrl        = getBaseUrl('fortune-teller');
$pageTitle       = 'Fortune Teller API';
$pageDescription = 'Multilingual fortune predictions covering all aspects of life.';
$activeNav       = 'docs';
$breadcrumbs     = [
    ['label' => 'Home', 'url' => '../index.php'],
    ['label' => 'Fortune Teller', 'url' => '../fortune-teller.php'],
    ['label' => 'API Documentation'],
];
require __DIR__ . '/../includes/header.php';
?>
    <div class="container">
        <div class="section">…overview…</div>
        <div class="section">…endpoints + tables + code blocks…</div>
    </div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
```

> ข้อควรระวัง: หน้าใน `api-specs/` อยู่ลึกลงหนึ่งชั้น → ใช้ path `../includes/...`

---

## 5. กติกาการใช้ร่วมกัน

1. **ห้าม** มี `<style>` หรือ `require analytics.php` ในหน้าเฉพาะอีกต่อไป
2. `<script>` เฉพาะหน้า (เช่น logic ของ tool) วาง**ก่อน** `require footer.php` ได้
   (footer จะใส่ `app.js` ท้ายสุดให้อัตโนมัติ)
3. ตัวแปร input ของ header ต้องตั้งค่า**ก่อน** `require header.php`
4. ทุก dynamic value ต้องผ่าน `e()` เสมอ
5. `helpers.php` / `analytics.php` ใช้ `require_once` เพื่อกัน double-load
