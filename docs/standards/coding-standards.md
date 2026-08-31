# 📝 PHP Coding Standards

> มาตรฐานการเขียน PHP สำหรับ MyAPIs — ทีมทุกคนต้องยึดถือเพื่อให้โค้ดอ่านง่าย ตรวจง่าย ขยายง่าย

---

## 1. Style Guide

ใช้ [PSR-12](https://www.php-fig.org/psr/psr-12/) เป็นพื้นฐาน + ข้อกำหนดเพิ่มเติมของ MyAPIs

### Indentation
- **4 spaces** (ห้ามใช้ tab)

### Line Length
- **120 characters** max (soft limit)
- ขึ้นบรรทัดใหม่ถ้ายาวเกิน

### Naming

| Element | Convention | Example |
|---------|-----------|---------|
| Class | `PascalCase` | `class HealthCalculator` |
| Method | `camelCase` | `public function calculateBMI()` |
| Private method | `camelCase` | `private function validateInput()` |
| Variable | `snake_case` | `$user_weight`, `$bmi_result` |
| Constant | `UPPER_SNAKE_CASE` | `const MAX_LENGTH = 128` |
| File | `kebab-case.php` | `health-calculator.php` |
| Folder | `kebab-case` | `promptpay-qr-generator/` |

---

## 2. Class Structure

```php
<?php
/**
 * <ClassName>
 * ---------------------------------------------------------
 * <Short description>
 *
 * @author <Team Member>
 * @since <Version>
 */
class HealthCalculator
{
    // 1. Constants (public → private)
    public const DEFAULT_AGE = 30;
    private const MAX_LENGTH = 128;

    // 2. Public properties (rarely used)
    public string $version = '1.0';

    // 3. Private/protected properties
    private array $config = [];

    // 4. Constructor
    public function __construct(array $config = []) { ... }

    // 5. Public methods (grouped by responsibility)
    public function calculate(): array { ... }

    // 6. Private methods (helpers)
    private function validate(): void { ... }
}
```

---

## 3. File Header

ทุกไฟล์ PHP ต้องมี header block:

```php
<?php
/**
 * =============================================================
 * MyAPIs - <File Purpose>
 * -------------------------------------------------------------
 * <Longer description if needed>
 *
 * @category <Tool Name>
 * @package  MyAPIs
 * @author   <Team Member> <email>
 * @since    <Version>
 * =============================================================
 */

declare(strict_types=1);   // ← บังคับ strict type ทุกไฟล์ใหม่
```

---

## 4. Type Declarations

ใช้ **strict types** + **type hints** ทุกที่:

```php
declare(strict_types=1);

public function calculateBMI(float $weight, float $height): float
{
    return round($weight / ($height * $height), 2);
}

public function getFortune(int $id): ?array  // nullable return
{
    return $fortune;
}
```

### Scalar Type Hints
- ใช้ `int`, `float`, `string`, `bool` — ห้ามใช้ `mixed`
- `array` ใช้ได้ — แต่ถ้า schema ชัดเจน ควรใช้ DTO class

---

## 5. Function / Method

### Naming
- Verb-based: `calculate()`, `validate()`, `generate()`, `parse()`
- Avoid: `doStuff()`, `process1()`, `helper()`

### Parameters
- ไม่เกิน **5 parameters** — ถ้าเกินให้ใช้ DTO/array
- เรียงลำดับ: required → optional, scalar → complex

### Return
- ระบุ return type เสมอ
- ใช้ `?type` สำหรับ nullable
- ใช้ `void` ถ้าไม่ return
- ใช้ `never` ถ้า throw / exit เสมอ

---

## 6. Error Handling

### ใช้ Exception เท่านั้น — ห้าม return false/null แทน error

```php
// ✅ Good
public function getFortune(int $id): array
{
    $file = __DIR__ . "/predictions/{$id}.json";
    if (!file_exists($file)) {
        throw new InvalidArgumentException("Fortune {$id} not found");
    }
    return json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
}

// ❌ Bad
public function getFortune(int $id)
{
    if (!file_exists(...)) {
        return null;  // caller จะรู้ได้ยังไงว่า error?
    }
    ...
}
```

### Exception Types

| Type | เมื่อไหร่ |
|------|---------|
| `InvalidArgumentException` | Input ผิดพลาด (400) |
| `RuntimeException` | External dependency fail (500) |
| `OutOfBoundsException` | Resource ไม่พบ (404) |
| `JsonException` | JSON parse fail (400) |

### Catch Block

```php
try {
    $result = $api->call();
} catch (InvalidArgumentException $e) {
    // user error → 400
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_INPUT',
        'message' => $e->getMessage(),
    ]);
} catch (\Throwable $e) {
    // unknown → 500
    http_response_code(500);
    error_log('[myapis] ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'INTERNAL_ERROR',
        'message' => APP_ENV === 'production' ? 'Internal error' : $e->getMessage(),
    ]);
}
```

---

## 7. Comments

### กฎ
- ✅ Comment **Why** ไม่ใช่ What
- ✅ ทุก public method มี DocBlock
- ✅ Reference algorithm/formula ต้องระบุ source (URL/citation)
- ❌ ห้าม comment ที่อธิบาย trivial
- ❌ ห้าม TODO ใน production code (ใช้ Issue แทน)

### ตัวอย่าง

```php
/**
 * Calculate BMI using the metric formula.
 *
 * @param float $weight Weight in kilograms (must be > 0)
 * @param float $height Height in centimeters OR meters (auto-detect)
 * @return float BMI rounded to 2 decimal places
 * @throws InvalidArgumentException when weight or height ≤ 0
 */
public function calculateBMI(float $weight, float $height): float
{
    if ($weight <= 0 || $height <= 0) {
        throw new InvalidArgumentException('Weight and height must be positive');
    }

    // Auto-convert: height > 3 is treated as cm, otherwise meters
    if ($height > 3) {
        $height = $height / 100;
    }

    return round($weight / ($height * $height), 2);
}
```

---

## 8. Arrays & Data

### ใช้ short array syntax เสมอ

```php
✅ $items = ['a', 'b', 'c'];
❌ $items = array('a', 'b', 'c');
```

### Associative Array Keys
- ใช้ `snake_case` (เพื่อ consistency กับ JSON response)
- ❌ ห้าม `camelCase` ใน response (ยกเว้น third-party spec)

### Default Values
- ทุก optional input ต้องมี default ผ่าน `??` หรือ `array_merge` กับ defaults

```php
$min = (int)($_GET['min'] ?? 1);
$options = array_merge($defaults, $input);
```

---

## 9. JSON Response

### Content-Type ต้องตั้งก่อน echo

```php
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
```

### JSON_UNESCAPED_UNICODE
- **ต้องใช้** เพื่อให้ response อ่านได้ (TH/ZH/ja)

### JSON_THROW_ON_ERROR
- ใช้เพื่อให้ error throw exception แทน silent fail

### Timestamp Format
- ใช้ `date('c')` (ISO 8601) เสมอ
- ตัวอย่าง: `2026-08-31T10:00:00+07:00`

---

## 10. Security

### Input
- ✅ Cast type ก่อนใช้เสมอ
- ✅ `htmlspecialchars()` ก่อน echo HTML
- ✅ `strip_tags()` สำหรับ plain text
- ❌ ห้าม eval, exec, system, shell_exec, passthru
- ❌ ห้าม `$_REQUEST` (ใช้ `$_GET`/`$_POST` แยก)

### Output
- ✅ `htmlspecialchars($s, ENT_QUOTES, 'UTF-8')`
- ❌ ห้าม echo user input ตรง ๆ

### Cryptography
- ✅ `random_int()` สำหรับ security-sensitive
- ✅ `password_hash()` / `password_verify()` สำหรับ password (ถ้ามี)
- ✅ `hash_hmac('sha256', $data, $secret)` สำหรับ signing
- ❌ ห้าม `md5()`, `sha1()` สำหรับ password
- ❌ ห้ามเขียน crypto algorithm เอง

---

## 11. Testing Checklist (Manual)

ทุก `index.php` ใหม่ต้องทดสอบ:

- [ ] `php -l <file>` → ไม่มี syntax error
- [ ] `OPTIONS /api/<tool>/` → 200
- [ ] `GET /api/<tool>/` (no params) → 400 with clear error
- [ ] `GET /api/<tool/?...valid params)` → 200 with expected JSON
- [ ] `POST /api/<tool/>` with JSON body → 200
- [ ] Invalid input (ลองทุก validation case) → 400
- [ ] CORS header ปรากฏใน response
- [ ] JSON parse ได้ด้วย `jq .`
- [ ] Lint passed in CI (Issue ในอนาคต)

---

## 12. Forbidden Patterns

| Pattern | เหตุผล | ใช้แทนด้วย |
|---------|--------|----------|
| `eval()` | Security | expression parser |
| `$$var` (variable variables) | Maintainability | array / map |
| `goto` | Readability | control structure |
| `extract()` | Hidden side effect | explicit assignment |
| `mysql_*` | Deprecated | ไม่มี DB อยู่แล้ว |
| `@` (suppression) | Hides bugs | handle error properly |
| `var_dump()` ใน production | Information leak | error_log() |

---

## 13. PR Checklist

PR ที่เปลี่ยน PHP code ต้องมี:

- [ ] `php -l` ผ่าน
- [ ] ไม่มี new syntax error
- [ ] ถ้าเพิ่ม public method → update Doc
- [ ] ถ้าเปลี่ยน response shape → update `docs/api-specs/`
- [ ] ถ้าเพิ่ม env var → update `example.env`
- [ ] ผ่าน PR review ≥ 1 คน
- [ ] Self-tested ด้วย curl ทุก endpoint
