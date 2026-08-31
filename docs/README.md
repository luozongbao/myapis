# 📚 MyAPIs Documentation Hub

เอกสารฉบับทางการของโปรเจกต์ **MyAPIs** — คลังเครื่องมือและ REST API สำหรับนักพัฒนา

> เอกสารนี้ออกแบบมาให้ทีมอ่านเพียง **5 นาที** แล้วเข้าใจโครงสร้างทั้งหมด — ทุกอย่างอยู่ใน `docs/` นี้

---

## 🗂️ แผนผังเอกสาร

````text
docs/
├── README.md                       ← (ไฟล์นี้) ศูนย์กลางเอกสาร
│
├── architecture/                   ← สถาปัตยกรรมเชิงระบบ
│   ├── overview.md                 ← ภาพรวม High-level
│   ├── request-flow.md             ← Flow ของ Request ตั้งแต่ Client จนถึง API
│   ├── deployment.md               ← แผนภาพ Deployment & Infrastructure
│   └── directory-structure.md      ← โครงสร้างไฟล์และหน้าที่ของแต่ละโฟลเดอร์
│
├── requirements/                   ← Requirements & Functional Specs
│   ├── product-brief.md            ← Product Brief / Vision
│   ├── functional-requirements.md  ← FRD (Functional Requirements)
│   ├── non-functional-requirements.md ← NFR (Performance, Security, i18n)
│   └── tool-catalog.md             ← รายการเครื่องมือ/Feature ทั้งหมด
│
├── api-specs/                      ← API Specifications (ทางการ)
│   ├── health-calculator.md
│   ├── password-generator.md
│   ├── username-generator.md
│   ├── promptpay-qr-generator.md
│   ├── qr-code-generator.md
│   ├── fortune-teller.md
│   └── randomizer.md
│
├── standards/                      ← มาตรฐาน & กฎเกณฑ์ของทีม
│   ├── coding-standards.md         ← PHP Coding Style, Naming, Error Handling
│   ├── api-design.md               ← REST API Design Guidelines
│   ├── git-workflow.md             ← Git Branching, Commit, PR
│   ├── security.md                 ← Security Checklist
│   └── documentation.md            ← วิธีเขียน Doc/Spec ให้เป็นมาตรฐานเดียวกัน
│
├── runbooks/                       ← คู่มือปฏิบัติการ (สำหรับ DevOps)
│   ├── local-development.md
│   ├── deployment.md
│   ├── monitoring.md
│   └── troubleshooting.md
│
└── issues/                         ← Issue Tracker (เอกสาร ไม่ใช่ GitHub)
    ├── README.md                   ← Issue Workflow & Labels
    ├── templates/
    │   ├── feature.md
    │   ├── bug.md
    │   ├── refactor.md
    │   └── docs.md
    ├── open/                       ← Issue ที่ยังไม่ปิด
    │   ├── ISSUE-001-xxx.md
    │   └── …
    └── done/                       ← Issue ที่ปิดแล้ว (ย้ายมาเก็บ)
````

---

## 🚦 จุดเริ่มต้นแนะนำตามบทบาท

| คุณคือใคร | อ่านเอกสารนี้ก่อน |
|--------|----------------|
| 🧑‍💼 **PM / ผู้จัดการโปรเจกต์** | [`prompts/pm-prompt.md`](../prompts/pm-prompt.md), [`requirements/product-brief.md`](requirements/product-brief.md), [`issues/README.md`](issues/README.md) |
| 🎨 **Designer** | [`prompts/designer-prompt.md`](../prompts/designer-prompt.md), [`requirements/tool-catalog.md`](requirements/tool-catalog.md), [`standards/documentation.md`](standards/documentation.md) |
| 🧑‍🔬 **System Analyst (SA)** | [`prompts/system-analyst-prompt.md`](../prompts/system-analyst-prompt.md), [`api-specs/*`](api-specs/), [`standards/api-design.md`](standards/api-design.md) |
| 👨‍💻 **Developer (Dev)** | [`prompts/dev-prompt.md`](../prompts/dev-prompt.md), [`architecture/overview.md`](architecture/overview.md), [`standards/coding-standards.md`](standards/coding-standards.md) |
| ⚙️ **DevOps / SRE** | [`prompts/devops-prompt.md`](../prompts/devops-prompt.md), [`architecture/deployment.md`](architecture/deployment.md), [`runbooks/*`](runbooks/) |
| 🧪 **QA / Tester** | [`prompts/qa-prompt.md`](../prompts/qa-prompt.md), [`requirements/functional-requirements.md`](requirements/functional-requirements.md), [`standards/api-design.md`](standards/api-design.md) |

---

## 📐 หลักการเขียนเอกสาร (Documentation Principles)

1. **Single Source of Truth** — สเปกของ API อยู่ที่ `docs/api-specs/<tool>.md` เท่านั้น ห้ามก๊อปไปไว้ที่อื่น
2. **Markdown เท่านั้น** — ไม่ใช้ Word/PDF ทุกไฟล์อ่านได้บน GitHub
3. **ตัวอย่างครบ** — ทุก API ต้องมี `Request` + `Response` + `Error` จริง
4. **อัปเดตพร้อมโค้ด** — PR ที่เปลี่ยน API ต้องแนบ Spec ใหม่ใน PR เดียวกัน
5. **ภาษาเดียว** — ใช้ภาษาอังกฤษเป็นหลัก ส่วน Heading/อธิบายสั้น ๆ ใช้ไทยได้เพื่อความเข้าใจของทีม

---

## 🧭 อ่านตามลำดับนี้ ถ้าคุณเพิ่งเข้าร่วมโปรเจกต์

1. [`requirements/product-brief.md`](requirements/product-brief.md) — เข้าใจว่าเราทำอะไร ทำไม
2. [`architecture/overview.md`](architecture/overview.md) — เข้าใจว่าระบบประกอบด้วยอะไร
3. [`architecture/directory-structure.md`](architecture/directory-structure.md) — รู้ว่าไฟล์ไหนอยู่ที่ไหน
4. [`requirements/tool-catalog.md`](requirements/tool-catalog.md) — รู้จัก 7 เครื่องมือ
5. [`standards/coding-standards.md`](standards/coding-standards.md) — รู้ว่าต้องเขียนโค้ดอย่างไร
6. [`issues/README.md`](issues/README.md) — เริ่มหยิบ Issue ทำได้เลย

---

## 🤝 Contributing

โปรดอ่าน [`standards/git-workflow.md`](standards/git-workflow.md) และ [`standards/documentation.md`](standards/documentation.md) ก่อนเปิด PR
