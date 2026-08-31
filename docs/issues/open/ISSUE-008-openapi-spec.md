# ISSUE-008: Generate OpenAPI 3.0 Specification

> **Type**: feature / docs / tooling
> **Priority**: P3 - Low
> **MoSCoW**: Could
> **Estimate**: L
> **Status**: Open

## 🎯 Background

ปัจจุบัน API spec อยู่ใน `docs/api-specs/*.md` (custom format) แต่ ecosystem มาตรฐานใช้ **OpenAPI 3.0** ผ่าน Swagger — เครื่องมือ generate client/SDK ต้องใช้ spec นี้

## 👤 User Story

As a developer,
I want OpenAPI spec,
So that generate SDK (PHP, JS, Python) ได้อัตโนมัติ

As a tools user,
I want try-it-now UI,
So that test API โดยไม่ต้องเขียน code

## 📦 Scope

### In Scope
- ✅ สร้าง `openapi.yaml` หรือ `openapi.json` ที่ root
- ✅ ครอบคลุมทั้ง 7 endpoints
- ✅ มี example response, error response
- ✅ Generator script (PHP script ที่ generate OpenAPI จาก class หรือ manual)
- ✅ Serve ผ่าน `/api-specs/` (Swagger UI)

### Out of Scope
- ❌ Auto-generate from code annotations (overkill สำหรับ PHP ไม่ใช้ framework)
- ❌ Postman/Insomnia collection
- ❌ AsyncAPI

## ✅ Acceptance Criteria

- [ ] `openapi.yaml` valid (ผ่าน Swagger CLI validate)
- [ ] ครอบคลุม 7 endpoints
- [ ] มี Swagger UI ที่ `https://example.com/api-specs/`
- [ ] มี try-it-now ทำงาน
- [ ] Tag, description, example ครบ
- [ ] Keep sync กับ `docs/api-specs/*.md` (หรือ replace MD)

## 🔧 Technical Approach

### Option A: Manual YAML (แนะนำ — ง่าย)

```yaml
# openapi.yaml
openapi: 3.0.3
info:
  title: MyAPIs
  description: |
    Public utility APIs for health, security, and creativity.
    All endpoints are stateless and CORS-enabled.
  version: 2.5.0
  contact:
    name: MyAPIs
    url: https://github.com/<org>/myapis
  license:
    name: MIT

servers:
  - url: https://api.example.com
    description: Production
  - url: http://localhost:8080
    description: Local dev

tags:
  - name: health-calculator
    description: BMI, BMR, daily intake, water intake
  - name: password-generator
  - name: ...

paths:
  /api/health-calculator/:
    get:
      tags: [health-calculator]
      summary: Calculate health metrics
      parameters:
        - in: query
          name: type
          required: true
          schema:
            type: string
            enum: [bmi, bmr, daily-intake, water-intake]
        - in: query
          name: weight
          required: true
          schema:
            type: number
            format: float
      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/HealthResult'
              examples:
                bmi-normal:
                  value:
                    success: true
                    type: bmi
                    result:
                      bmi: 22.86
                      category: Normal weight
        '400':
          description: Validation error
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'

components:
  schemas:
    HealthResult:
      type: object
      properties:
        success: { type: boolean }
        type: { type: string }
        result: { type: object }
    ErrorResponse:
      type: object
      properties:
        success: { type: boolean, example: false }
        error: { type: string }
        message: { type: string }
```

### Option B: Generator (overkill)

ใช้ reflection + annotations + script generate `openapi.yaml`
→ เพิ่มความซับซ้อน ไม่คุ้มกับ 7 endpoints

### Serve Swagger UI

```php
<!-- public/api-specs/index.php -->
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
  <script>
    window.onload = () => {
      SwaggerUIBundle({
        url: '/openapi.yaml',
        dom_id: '#swagger-ui'
      });
    };
  </script>
</body>
</html>
```

### Validation

```bash
# Install swagger-cli
npm install -g swagger-cli

# Validate
swagger-cli validate openapi.yaml
```

## 📋 Tasks

### Spec (SA)
- [ ] ร่าง OpenAPI ทั้ง 7 endpoints
- [ ] สร้าง `openapi.yaml`
- [ ] Validate

### UI (Dev + Designer)
- [ ] สร้าง `public/api-specs/index.php` (Swagger UI)
- [ ] (Optional) theme สีของ MyAPIs
- [ ] Link จาก landing page

### CI (DevOps)
- [ ] เพิ่ม step validate `openapi.yaml` ใน ISSUE-004 workflow

### Docs (SA)
- [ ] Cross-link `docs/api-specs/*.md` ↔ `openapi.yaml`
- [ ] Update `README.md`

## 🔗 Dependencies

- ไม่มี

## 📝 Notes

- ควร deprecate `docs/api-specs/*.md` ทีละไฟล์ หรือ keep ทั้งคู่?
- คำแนะนำ: keep ทั้งคู่ — MD ง่ายต่อการอ่าน, OpenAPI ใช้ tooling
- Add link "API Spec (OpenAPI)" → Swagger, "API Spec (Markdown)" → MD

## 🔖 Labels

`feature`, `docs`, `openapi`, `tooling`
