# ISSUE-004: Implement CI/CD Pipeline with GitHub Actions

> **Type**: feature / devops
> **Priority**: P1 - High
> **MoSCoW**: Must
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบันไม่มี automation ในการ test/lint/deploy:
- ❌ ทุก PR ต้อง manual test
- ❌ ไม่มี consistent lint
- ❌ Deploy ต้อง manual ssh

GitHub Actions ฟรีสำหรับ public repo + มี PHP container image พร้อมใช้

## 👤 User Story

As a developer,
I want automate test + lint + build,
So that มั่นใจทุก PR pass quality bar

As a release manager,
I want one-click deploy,
So that deploy safer และเร็วขึ้น

## 📦 Scope

### In Scope
- ✅ Lint workflow (every PR) — `php -l` on all `.php`
- ✅ Test workflow — PHPUnit/Pest (after ISSUE-003 merged)
- ✅ Docker build workflow — build image เก็บใน GHCR
- ✅ Lint workflow — ตรวจ markdown, docker, yaml
- ✅ (Optional) Auto deploy to staging on merge `main`

### Out of Scope
- ❌ Auto deploy to production (อันตราย — ต้อง manual)
- ❌ E2E tests
- ❌ Performance tests
- ❌ Renovate/Dependabot (ทำปลาย issue)

## ✅ Acceptance Criteria

- [ ] `.github/workflows/` มี:
  - `lint.yml` — php lint + markdown lint
  - `test.yml` — PHPUnit
  - `docker.yml` — build + push to GHCR
  - `docs.yml` — markdown-link-check
- [ ] ทุก workflow run on `push` + `pull_request`
- [ ] PR blocked ถ้า workflow fail
- [ ] Status badge ใน README
- [ ] Docker image push to `ghcr.io/<org>/myapis` on tag

## 🔧 Technical Approach

### Workflow 1: PHP Lint

```yaml
# .github/workflows/lint.yml
name: PHP Lint

on:
  push:
    branches: [main]
  pull_request:
    branches: [main]

jobs:
  lint:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php-version: ['8.2', '8.3']
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}

      - name: Lint api/
        run: |
          find api -name '*.php' -print0 | xargs -0 -n1 php -l

      - name: Lint public/
        run: |
          find public -name '*.php' -print0 | xargs -0 -n1 php -l

      - name: Lint docker/
        run: |
          find docker -name '*.php' -print0 | xargs -0 -n1 php -l
```

### Workflow 2: Tests

```yaml
# .github/workflows/test.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php-version: ['8.2']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          coverage: xdebug

      - run: composer install --prefer-dist --no-progress
      - run: composer test
```

### Workflow 3: Docker Build

```yaml
# .github/workflows/docker.yml
name: Docker Build & Push

on:
  push:
    tags: ['v*']
  workflow_dispatch:

jobs:
  build:
    runs-on: ubuntu-latest
    permissions:
      contents: read
      packages: write
    steps:
      - uses: actions/checkout@v4

      - name: Extract version
        id: version
        run: echo "VERSION=${GITHUB_REF#refs/tags/v}" >> $GITHUB_OUTPUT

      - name: Login to GHCR
        uses: docker/login-action@v3
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Build + push PHP image
        uses: docker/build-push-action@v5
        with:
          context: .
          push: true
          tags: |
            ghcr.io/${{ github.repository_owner }}/myapis-php:${{ steps.version.outputs.VERSION }}
            ghcr.io/${{ github.repository_owner }}/myapis-php:latest
```

### Workflow 4: Markdown Link Check

```yaml
# .github/workflows/docs.yml
name: Docs Check

on: [push, pull_request]

jobs:
  docs:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Markdown Lint
        uses: DavidAnson/markdownlint-cli2-action@v15
        with:
          globs: |
            docs/**/*.md
            *.md

      - name: Markdown Link Check
        uses: lycheeverse/lychee-action@v1
        with:
          args: --offline docs/ README.md RELEASE.md
```

### Status Badges

```markdown
<!-- README.md -->
![Lint](https://github.com/<org>/myapis/workflows/PHP%20Lint/badge.svg)
![Tests](https://github.com/<org>/myapis/workflows/Tests/badge.svg)
![Docker](https://github.com/<org>/myapis/workflows/Docker%20Build%20%26%20Push/badge.svg)
```

### Branch Protection

ไปที่ GitHub repo → Settings → Branches → Branch protection rules → Add rule:
- ✅ Require status checks: lint, test, docker
- ✅ Require pull request reviews: 1+ review

## 📋 Tasks

### Setup (DevOps)
- [ ] สร้าง `.github/workflows/lint.yml`
- [ ] สร้าง `.github/workflows/test.yml` (ถ้า ISSUE-003 merged)
- [ ] สร้าง `.github/workflows/docker.yml`
- [ ] สร้าง `.github/workflows/docs.yml`
- [ ] ตั้ง branch protection rules

### Configure (DevOps)
- [ ] สร้าง GHCR personal access token (auto ผ่าน GITHUB_TOKEN)
- [ ] ตรวจ GHCR packages visibility

### Docs (Dev)
- [ ] เพิ่ม status badges ใน `README.md`
- [ ] Update `docs/runbooks/local-development.md` — CI workflow
- [ ] Update `docs/runbooks/deployment.md` — image pull from GHCR

### Test
- [ ] Push PR ทดสอบ
- [ ] ตรวจ workflows ทำงาน
- [ ] ตรวจ Docker image push

## 🔗 Dependencies

- **Required**: ISSUE-003 (tests) — จะ run ใน CI

## 📝 Notes

- ⚠️ ตั้ง rate limit — GitHub Actions มี concurrent job limit
- ใช้ `concurrency:` เพื่อ cancel previous run

```yaml
concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true
```

## 🔖 Labels

`feature`, `devops`, `ci`, `github-actions`
