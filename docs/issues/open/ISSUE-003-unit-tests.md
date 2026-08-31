# ISSUE-003: Add Unit Tests for Health Calculator

> **Type**: feature / quality
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบัน MyAPIs ไม่มี automated tests ทำให้:
- ❌ ทุก change เสี่ยง break existing feature
- ❌ Onboarding dev ใหม่ช้า (ไม่รู้ว่าทำงานไหม)
- ❌ Confidence ในการ refactor ต่ำ

Health Calculator เป็น tool ที่ logic ชัดเจน (BMI, BMR, Daily Intake, Water) — เหมาะเป็น "test seed" แรก

## 👤 User Story

As a developer,
I want มี unit tests,
So that มั่นใจว่า change ไม่ break ที่ existing

As a user,
I want Health Calculator ให้ผลถูก,
So that ตัดสินใจเรื่องสุขภาพได้แม่นยำ

## 📦 Scope

### In Scope
- ✅ Setup test runner: [PHPUnit 10](https://phpunit.de/) (หรือ [Pest](https://pestphp.com/))
- ✅ Test class `HealthCalculator` ทุก method:
  - `calculateBMI()` — happy path + edge cases (cm vs m, 0, negative)
  - `calculateBMR()` — male/female formulas
  - calculateDailyIntake() — 4 activity levels
  - `calculateWaterIntake()` — kg/lb, activity multiplier
- ✅ Test `RandomGenerator` (CSPRNG verification)
- ✅ Test `PasswordGenerator` (length, charset)
- ✅ Test `Fortune` (file not found, valid file)
- ✅ Test PromptPay (target validation, CRC)
- ✅ CI: run tests on every PR

### Out of Scope
- ❌ Integration tests (slow, fragile)
- ❌ E2E browser tests (Selenium — overkill ปัจจุบัน)
- ❌ Performance tests
- ❌ Coverage 100% (เริ่ม ~60% first PR)

## ✅ Acceptance Criteria

- [ ] Test runner setup (PHPUnit/Pest) — `composer require --dev`
- [ ] Test files: `tests/Unit/HealthCalculatorTest.php` etc.
- [ ] ≥ 30 test cases ใน issue นี้
- [ ] Test pass locally + CI
- [ ] Coverage report (HTML + badge) — optional
- [ ] `composer test` command ทำงาน
- [ ] README updates — how to run tests
- [ ] Tests ไม่ขึ้นกับ external service (QR goQR.me)

## 🔧 Technical Approach

### Setup

```bash
# Add to composer.json (dev-dependency)
composer require --dev phpunit/phpunit
```

### Test File Structure

```
tests/
├── Unit/
│   ├── Calculator/
│   │   ├── HealthCalculatorTest.php
│   │   ├── PasswordGeneratorTest.php
│   │   ├── PromptPayTest.php
│   │   └── RandomGeneratorTest.php
│   ├── Fortune/
│   │   └── FortuneTest.php
│   └── bootstrap.php
└── Integration/    (later)
```

### Example Test

```php
<?php
// tests/Unit/Calculator/HealthCalculatorTest.php
namespace MyAPIs\Tests\Unit\Calculator;

use PHPUnit\Framework\TestCase;
use MyAPIs\Calculator\HealthCalculator;

class HealthCalculatorTest extends TestCase
{
    private HealthCalculator $calc;

    protected function setUp(): void
    {
        $this->calc = new HealthCalculator();
    }

    public function testBMI_normal(): void
    {
        $this->assertEquals(22.86, $this->calc->calculateBMI(70, 175));
    }

    public function testBMI_height_cm_auto_convert(): void
    {
        // 175 cm = 1.75 m, weight 70 kg → 70 / (1.75^2) = 22.86
        $this->assertEquals(22.86, $this->calc->calculateBMI(70, 175));
    }

    public function testBMI_height_already_meter(): void
    {
        // 1.75 m ตรง ๆ
        $this->assertEquals(22.86, $this->calc->calculateBMI(70, 1.75));
    }

    public function testBMI_invalid_negative_weight_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calc->calculateBMI(-70, 175);
    }

    public function testBMR_male_mifflin(): void
    {
        // Male, 30 years, 70 kg, 175 cm
        // 10*70 + 6.25*175 - 5*30 + 5 = 1648.75
        $this->assertEquals(1648.75, $this->calc->calculateBMR('male', 30, 70, 175));
    }

    public function testBMR_female_mifflin(): void
    {
        // Female, 30 years, 60 kg, 165 cm
        // 10*60 + 6.25*165 - 5*30 - 161 = 1285.25
        $this->assertEquals(1285.25, $this->calc->calculateBMR('female', 30, 60, 165));
    }

    public function testDailyIntake_moderate(): void
    {
        // BMR 1648.75, moderate activity (1.55)
        // = 2555.56
        $this->assertEquals(2555.56, $this->calc->calculateDailyIntake(1648.75, 'moderate'));
    }

    public function testWaterIntake_70kg_sedentary(): void
    {
        // 70 kg * 35 ml/kg * 1.0 (sedentary) = 2450 ml
        $this->assertEquals(2450, $this->calc->calculateWaterIntake(70, 'sedentary'));
    }
}
```

### CI Integration

```yaml
# .github/workflows/test.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - run: composer install
      - run: composer test
      - uses: codecov/codecov-action@v3  # optional
```

### Composer Scripts

```json
{
  "scripts": {
    "test": "phpunit",
    "test:coverage": "phpunit --coverage-html coverage/"
  }
}
```

## 📋 Tasks

### Setup (Dev)
- [ ] composer init (ถ้ายังไม่มี)
- [ ] composer require --dev phpunit/phpunit
- [ ] ตั้ง phpunit.xml
- [ ] ตั้ง directory `tests/Unit/`

### Tests (Dev)
- [ ] `HealthCalculatorTest.php` (8 cases minimum)
- [ ] `PasswordGeneratorTest.php` (6 cases)
- [ ] `RandomGeneratorTest.php` (5 cases)
- [ ] `PromptPayTest.php` (5 cases — target validation, CRC)
- [ ] `FortuneTest.php` (4 cases)

### CI (DevOps)
- [ ] สร้าง `.github/workflows/test.yml`
- [ ] (Optional) Codecov integration
- [ ] Add status badge to README

### Docs (SA/Dev)
- [ ] Update `docs/standards/coding-standards.md` — testing section
- [ ] Update `README.md` — how to run tests
- [ ] Update `docs/runbooks/local-development.md`

## 🔗 Dependencies

- ต้องทำ refactor class extraction ก่อน (ปัจจุบัน code ผสม UI + logic ใน `index.php`)

## 📝 Notes

- ⚠️ ห้ามแตะ production code นอกจาก extract class (คนละ issue)
- ใช้ mock `rand` ทดสอบ password entropy ไม่ได้ — ใช้ stat analysis แทน
- ดู doc ใน [`docs/standards/coding-standards.md`](../standards/coding-standards.md)

## 🔖 Labels

`feature`, `quality`, `testing`, `health-calculator`
