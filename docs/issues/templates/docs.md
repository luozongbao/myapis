# Documentation Issue Template

> Copy แล้วใช้สำหรับแก้ documentation

---

```markdown
# ISSUE-<id>: <Doc Title>

> **Type**: docs
> **Priority**: P2 | P3
> **Status**: Open

## 📖 Why

<เหตุผลต้องแก้ doc>

ตัวอย่าง:
- เอกสารไม่ครบ / outdated
- API spec ไม่ match กับ implementation
- Diagram ต้อง update
- Tutorial/example ขาด

## 📄 Scope

### Files to Change
- [ ] `docs/api-specs/<tool>.md`
- [ ] `docs/architecture/<topic>.md`
- [ ] `docs/runbooks/<topic>.md`
- [ ] `README.md`
- [ ] `RELEASE.md`

## ✅ Acceptance Criteria

- [ ] Doc updated และ sync กับ code
- [ ] Links ใช้งานได้
- [ ] Code examples ทดสอบแล้ว
- [ ] Markdown render ถูกต้อง (preview บน GitHub)
- [ ] "Last updated" อัปเดต

## 🔧 Tasks

- [ ] แก้ไฟล์
- [ ] Review (Designer / SA / DevOps แล้วแต่เนื้อหา)
- [ ] PR

## 🔗 Related

- Related: #<id> (code change ที่ตามมา)

## 🔖 Labels

`docs`
```
