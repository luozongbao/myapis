# 📖 Documentation Standards

> วิธีเขียนเอกสารให้เป็นมาตรฐานเดียวกัน

---

## 1. Markdown Style

ใช้ **GitHub Flavored Markdown** (GFM)

### Headings
- `#` (H1) — ใช้ครั้งเดียวต่อไฟล์ที่ top
- `##` (H2) — section หลัก
- `###` (H3) — subsection
- `####` (H4) — ใช้เมื่อจำเป็น

### Code Block
````markdown
```php
// filepath: api/health-calculator/index.php
public function calculate(): float
{
    return 22.86;
}
```
````

### Tables
- ใช้ `|` syntax ของ GFM
- ต้องมี header row + separator
- ใช้ alignment เมื่อต้องการ

### Lists
- `-` สำหรับ unordered
- `1.` สำหรับ ordered
- ห้าม indent ผิด (ใช้ 2 spaces)

### Links
- ใช้ relative path สำหรับ internal link: `[Spec](../api-specs/health-calculator.md)`
- ใช้ full URL สำหรับ external: [goQR.me API](https://goqr.me/api/doc/create-qr-code/)

---

## 2. โครงสร้างไฟล์ (ทุก Markdown ควรมี)

```markdown
# <Title>

> <One-line summary>
> **<Status>** | **<Author>** | **<Last updated>**

---

## Overview
...

## <Section 1>
...

## <Section 2>
...

## Reference
- [Link 1](url)
- [Link 2](url)
```

---

## 3. API Spec Template

ไฟล์ `docs/api-specs/<tool>.md` ต้องมี section ตามลำดับนี้:

```markdown
# <Tool Name> API

> Version | Base URL | Source

---

## Overview
(1-3 ประโยคอธิบายว่า API ทำอะไร)

## Common
- Methods
- Content-Type
- CORS
- Cache

## Endpoint
(URL + parameter หลัก)

## <Feature 1>
### Request
(table)

### Example
(curl command)

### Response (200)
(json)

### Error Responses
(table)

## Reference
(citations / external docs)
```

---

## 4. Issue Template

ไฟล์ `docs/issues/open/ISSUE-<id>-<slug>.md`:

```markdown
# ISSUE-<id>: <Title>

> **Type**: feature | bug | refactor | docs | security
> **Priority**: High | Medium | Low
> **Estimate**: S | M | L | XL
> **Status**: Open | In Progress | Review | Done

## 🎯 Description

<ปัญหา/โอกาส>

## 👥 Owner

- Designer: @name
- SA: @name
- Dev: @name
- QA: @name

## ✅ Acceptance Criteria

- [ ] Criterion 1
- [ ] Criterion 2

## 🔗 Dependencies

- Blocked by: #<id>
- Blocks: #<id>

## 📝 Notes

<reference, discussion, etc.>
```

ดู template เพิ่มที่ [`docs/issues/templates/`](../issues/templates/)

---

## 5. Tone & Voice

### ใช้
- **Second person** — "You can call this endpoint by..."
- **Active voice** — "The API returns the BMI value"
- **Short sentences** — ≤ 25 words
- **Concrete examples** — curl command + JSON response

### หลีกเลี่ยง
- **First person** — "I designed this API to..."
- **Passive voice** — "The BMI value is returned by the API"
- **Jargon** ที่ไม่จำเป็น
- **Humor / meme** ในเอกสารทางการ

---

## 6. กฎการตั้งชื่อไฟล์

| ประเภท | รูปแบบ | ตัวอย่าง |
|-------|-------|---------|
| Spec | `<tool-name>.md` | `health-calculator.md` |
| Architecture | `<topic>.md` | `overview.md` |
| Standard | `<topic>.md` | `coding-standards.md` |
| Runbook | `<topic>.md` | `local-development.md` |
| Issue | `ISSUE-<id>-<slug>.md` | `ISSUE-001-add-bmi-japanese.md` |
| ADR (Architecture Decision Record) | `ADR-<id>-<slug>.md` | `ADR-002-why-no-composer.md` |

---

## 7. การอัปเดตเอกสาร

### เมื่อไหร่ต้องอัปเดต

| Trigger | Action |
|---------|--------|
| เพิ่ม API endpoint | สร้าง spec ใหม่ + tool catalog |
| เปลี่ยน response shape | แก้ spec + sample response |
| เปลี่ยน parameter | แก้ spec table |
| เพิ่ม env var | แก้ `example.env` + deployment doc |
| เปลี่ยน architecture | สร้าง ADR + แก้ architecture docs |
| Breaking change | แก้ spec + บันทึกใน `RELEASE.md` |

### ใครอัปเดต
- **PM** — product brief, roadmap, issue workflow
- **SA** — API spec, FRD, NFR, tool catalog
- **Designer** — design specs, component catalog
- **Dev** — deployment, runbook, troubleshooting
- **DevOps** — runbook, deployment, monitoring

### PR Checklist (Documentation)
- [ ] ทุก link ใช้งานได้ (ไม่ broken)
- [ ] Code example ทดสอบแล้ว
- [ ] No typo
- [ ] "Last updated" อัปเดต
- [ ] Markdown render ถูกต้อง (preview บน GitHub)
