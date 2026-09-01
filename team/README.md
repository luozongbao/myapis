# 🤖 AI Agent Team — MyAPIs

> ทีม AI Agents ของโปรเจกต์ MyAPIs — ทำงานร่วมกับ PM (พีม)

---

## 👥 Team Roster

| Agent ID | Thai Name | Role | Persona |
|----------|-----------|------|---------|
| `pm` | พีม | Project Manager | (main session) |
| `dev` | เดฟ | Developer | [dev.md](agents/dev.md) |
| `sa` | ซ่า | System Analyst | [sa.md](agents/sa.md) |
| `devops` | ออป | DevOps / SRE | [devops.md](agents/devops.md) |
| `designer` | ยู | UX/UI Designer | [designer.md](agents/designer.md) |
| `qa` | เทส | QA / Tester | [qa.md](agents/qa.md) |

## 🎯 Responsibilities

| Agent | Main deliverables |
|-------|------------------|
| พีม (PM) | Planning, coordination, quality gate, routing |
| เดฟ (Dev) | Code (api/, public/), tests, PRs |
| ซ่า (SA) | Specs, FRD/NFR, ADRs, reviews |
| ออป (DevOps) | Docker, CI/CD, monitoring, runbooks |
| ยู (Designer) | Design tokens, components, mockups, a11y |
| เทส (QA) | Test plans, bug reports, regression |

## 🔄 Handoff Patterns

```
ซ่า (spec) ──→ เดฟ (code) ──→ เทส (test) ──→ ออป (deploy)
                  ↑                                        ↓
                  └────── พีม (orchestrate) ──────────────┘
ยู (design) ──→ เดฟ (implement UI)
```

## 📞 Communication (QQ)

### From User (อั้ม)
ใช้ @mention ใน QQ chat:
```
@เดฟ implement rate limiting per ISSUE-001
@ซ่า review the API spec
@เทส test the new endpoint
@ยู design tokens for the new component
@ออป deploy to staging
@พีม status update
```
→ พีม routes ข้อความไป agent ที่เกี่ยวข้อง แล้วส่ง response กลับ

### From PM (พีม)
ใช้ `sessions_send(sessionKey, message)` หรือ `agentId`

### From Agent → Agent
ผ่าน พีม เป็น broker (ไม่ direct)

## 🚀 Current Batch (v2.5 Polish)

Goals: **Restructure File + Restructure Pages + Fix Security** (Features/Functions คงเดิม)

| Issue | Owner | Status |
|-------|-------|--------|
| ISSUE-011: Pre-sprint audit | พีม | Open |
| ISSUE-012: Design tokens + component spec | ยู | Open |
| ISSUE-013: Move shared classes to api/_includes/ | ซ่า + เดฟ | Open |
| ISSUE-001: Rate limiting | เดฟ + ออป | Open |
| ISSUE-010: Secrets mgmt docs | ออป | Open |
| ISSUE-025: CSRF protection | เดฟ | Open |
| ISSUE-002: Extract CSS | เดฟ + ยู | Open |
| ISSUE-009: a11y | ยู + เทส | Open |
| ISSUE-008: OpenAPI spec | ซ่า + เดฟ | Open (optional) |

## 📚 References

- [prompts/](../prompts/) — Detailed role prompts
- [docs/standards/](../docs/standards/) — Standards
- [docs/issues/](../docs/issues/) — Issue tracking
- [docs/architecture/](../docs/architecture/) — Architecture
