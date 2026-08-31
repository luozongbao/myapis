# Directory Structure

> โครงสร้างไฟล์ทั้งหมดของโปรเจกต์ — บอกหน้าที่ของแต่ละโฟลเดอร์และไฟล์

---

## แผนผังระดับบนสุด

```
myapis/
├── api/                ← REST API endpoints (สำหรับ client/server-to-server)
├── public/             ← Web UI + rendered API specs
├── prompts/            ← คำสั่งสำหรับทีมแต่ละบทบาท
├── docs/               ← เอกสารฉบับทางการ (Markdown)
├── docker/             ← Docker config (php.ini, nginx, entrypoint)
├── docker-compose.yml  ← Docker Compose stack
├── Dockerfile          ← PHP-FPM image
├── example.env         ← Template สำหรับ .env
├── README.md           ← Project README
└── RELEASE.md          ← Release notes / changelog
```

---

## `api/` — REST API Endpoints

```
api/
├── health-calculator/
│   └── index.php       ← BMI, BMR, Daily Intake, Water Intake
├── password-generator/
│   └── index.php       ← Generate + Analyze
├── username-generator/
│   └── index.php       ← Multi-theme username
├── promptpay-qr-generator/
│   └── index.php       ← PromptPay EMV QRCPS QR
├── qr-code-generator/
│   └── index.php       ← Universal QR (text/url/vcard/event/wifi/phone)
├── fortune-teller/
│   ├── index.php       ← Random fortune endpoint
│   └── predictions/    ← 52 JSON files
└── randomizer/
    └── index.php       ← number/dice/coin/card
```

**กฎ**:
- ห้ามมี subdirectory นอกจาก `index.php` (defence in depth)
- 1 tool = 1 PHP class (recommended)
- ห้าม `require` ไฟล์นอก `api/`

---

## `public/` — Web UI + Rendered API Specs

```
public/
├── index.php           ← Landing page (tool catalog)
├── .htaccess           ← Apache config (cache, security, CORS)
├── .htaccess.alternative ← Backup alternative config
│
├── {tool}.php          ← Web UI (เช่น health-calculator.php)
│
├── api-specs/
│   └── {tool}.php      ← Rendered HTML spec
│
├── includes/           ← (reserved for shared partials)
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── analytics.php       ← Analytics snippet (ตัวจริง)
├── config.php.example  ← Template สำหรับ shared hosting analytics
│
└── .htaccess
```

**กฎ**:
- ไฟล์ `.php` ทุกไฟล์ต้อง `require __DIR__ . '/analytics.php'` ก่อน `</head>` (ถ้ามี)
- ไฟล์ `.php` ใน `api-specs/` ห้ามมี executable logic — render only

---

## `prompts/` — Team Prompts

```
prompts/
├── pm-prompt.md              ← Project Manager
├── designer-prompt.md        ← Designer (UX/UI)
├── system-analyst-prompt.md  ← System Analyst (SA)
├── dev-prompt.md             ← Developer (Dev)
├── devops-prompt.md          ← DevOps / SRE
└── qa-prompt.md              ← QA / Tester
```

**กฎ**:
- 1 ไฟล์ต่อ 1 บทบาท
- ไฟล์ต้องระบุชัดเจนว่า "ผู้รับผิดชอบ" คือใคร ส่งมอบอะไร
- ไฟล์ต้อง cross-reference กับ `docs/` ที่เกี่ยวข้อง

---

## `docs/` — Documentation (Markdown)

```
docs/
├── README.md                 ← Documentation hub
├── architecture/             ← System architecture
├── requirements/             ← Product brief, FRD, NFR, Tool catalog
├── api-specs/                ← API Specifications (Markdown source of truth)
├── standards/                ← Coding/API/Git/Security/Documentation standards
├── runbooks/                 ← Operational runbooks (DevOps)
└── issues/
    ├── README.md             ← Issue workflow
    ├── templates/            ← Issue templates
    ├── open/                 ← Active issues
    └── done/                 ← Closed issues
```

**กฎ**:
- ห้ามมี Word/PDF — Markdown เท่านั้น
- API Spec เป็น **Markdown** (`docs/api-specs/<tool>.md`)
- เอกสารทุกไฟล์ต้องมี "Last updated" header

---

## `docker/` — Container Config

```
docker/
├── entrypoint.sh             ← PHP-FPM entrypoint (render php.ini from .env)
├── nginx/
│   └── default.conf          ← Nginx vhost
└── php/
    ├── php.ini.tpl           ← PHP ini template
    ├── opcache.ini           ← Opcache config
    └── analytics.php         ← Analytics snippet (ตัวจริง — copy จาก public/)
```

**กฎ**:
- `analytics.php` มี 2 path (`docker/php/` สำหรับ Docker, `public/` สำหรับ shared hosting)
- หากแก้ analytics.php ต้อง sync ทั้ง 2 path

---

## ไฟล์ที่ root

| ไฟล์ | หน้าที่ |
|------|-------|
| `docker-compose.yml` | กำหนด services ทั้งหมด (PHP, Nginx, optional Umami) |
| `Dockerfile` | Build PHP-FPM image |
| `example.env` | Template สำหรับ `.env` (copy แล้วแก้) |
| `README.md` | Entry point สำหรับ contributor + user |
| `RELEASE.md` | Changelog / version history |
| `.htaccess` (root) | Redirect all → `/public/` (สำหรับ Apache) |

---

## กฎการตั้งชื่อ (Naming Convention)

### Folders
- `kebab-case` สำหรับ tools (เช่น `health-calculator`, `promptpay-qr-generator`)
- `lowercase` สำหรับ system folders (เช่น `api`, `public`, `docs`)

### Files
- PHP files: `kebab-case.php` (เช่น `health-calculator.php`)
- Class names: `PascalCase` (เช่น `class HealthCalculator`)
- Markdown: `kebab-case.md` (เช่น `product-brief.md`)

### API Endpoints
- `GET/POST /api/{tool}/` — tool ใช้ `kebab-case` เสมอ
- ตัวอย่าง: `/api/health-calculator/`, `/api/qr-code-generator/`

---

## กฎการเพิ่มไฟล์ใหม่

| ถ้าต้องการเพิ่ม | ต้องสร้าง |
|----------------|---------|
| เครื่องมือใหม่ | `api/<tool>/index.php` + `public/<tool>.php` + `public/api-specs/<tool>.php` + `docs/api-specs/<tool>.md` |
| API spec ใหม่ | `docs/api-specs/<tool>.md` + `public/api-specs/<tool>.php` |
| Component CSS ใหม่ | `public/assets/css/<name>.css` + include ใน `public/<tool>.php` |
| Class ใหม่ | `api/<tool>/includes/<ClassName>.php` (ถ้าต้องแยก) |
| Issue ใหม่ | `docs/issues/open/ISSUE-<id>-<slug>.md` |
| Prompt ใหม่ | `prompts/<role>-prompt.md` |
