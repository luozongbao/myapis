# ISSUE-006: Add Prometheus Metrics Endpoint

> **Type**: feature / observability
> **Priority**: P2 - Medium
> **MoSCoW**: Could
> **Estimate**: M
> **Status**: Open

## 🎯 Background

ปัจจุบันไม่มี observability:
- ❌ ไม่รู้ว่า traffic มาทางไหน
- ❌ ไม่รู้ endpoint ไหนช้า
- ❌ ไม่มี alerting อัตโนมัติ

Prometheus + Grafana เป็น industry standard

## 👤 User Story

As a DevOps,
I want metrics endpoint,
So that สามารถ monitor production ได้จริง

## 📦 Scope

### In Scope
- ✅ `/metrics` endpoint ที่ return Prometheus format
- ✅ Counters:
  - `myapis_requests_total{service, tool, status}` — request count
  - `myapis_errors_total{service, tool, type}` — error count
- ✅ Histograms:
  - `myapis_request_duration_seconds{service, tool}` — response time
- ✅ ✅ Metrics persistent between process (ใช้ APCu)
- ✅ Grafana dashboard template

### Out of Scope
- ❌ Distributed tracing (overkill)
- ❌ Custom business metrics (no business logic)
- ❌ Logs integration

## ✅ Acceptance Criteria

- [ ] `/metrics` returns Prometheus-format text
- [ ] Test ด้วย `promtool` (Prometheus CLI)
- [ ] Counters increment ตามจริง
- [ ] Histograms มี bucket distribution
- [ ] Enable ใน `.env`: `METRICS_ENABLED=true`
- [ ] Grafana dashboard JSON export

## 🔧 Technical Approach

### Endpoints

```
GET /metrics → text/plain (Prometheus format)
```

### Implementation

```php
<?php
// public/metrics.php
require_once __DIR__ . '/../api/_includes/Metrics.php';

header('Content-Type: text/plain; version=0.0.4');

// Counter
foreach (Metrics::getAllCounters() as $counter) {
    echo "myapis_{$counter['name']}_total";
    echo '{'.implode(',', array_map(
        fn($k, $v) => "$k=\"$v\"",
        array_keys($counter['labels']),
        array_values($counter['labels'])
    )).'} ' . $counter['value'] . "\n";
}

// Histogram
foreach (Metrics::getAllHistograms() as $histogram) {
    echo "myapis_{$histogram['name']}_seconds_bucket";
    echo '{...le="..."}' . $histogram['count'] . "\n";
    echo "myapis_{$histogram['name']}_seconds_count " . $histogram['count'] . "\n";
    echo "myapis_{$histogram['name']}_seconds_sum " . $histogram['sum'] . "\n";
}
```

### Auto-instrumentation

ในทุก API:
```php
$start = microtime(true);

// ... main logic ...

$duration = microtime(true) - $start;
Metrics::observe('request_duration', $duration, ['tool' => 'health-calculator']);
Metrics::increment('requests', ['tool' => 'health-calculator', 'status' => 200]);
```

**หรือ** ใช้ shared `api/_includes/bootstrap.php` ที่ include ทุก API

### Storage

ใช้ **APCu** (in-memory):
- Per-worker metrics — ถ้า process restart หาย OK
- Acceptable trade-off (scrape ทุก 15s, restart ไม่บ่อย)

### Grafana Dashboard

ตัวอย่าง panel:
- **Request rate**: `rate(myapis_requests_total[5m])`
- **p95 latency**: `histogram_quantile(0.95, myapis_request_duration_seconds)`
- **Error rate**: `rate(myapis_errors_total[5m]) / rate(myapis_requests_total[5m])`

## 📋 Tasks

### Implement (DevOps + Dev)
- [ ] สร้าง `api/_includes/Metrics.php` (APCu-backed)
- [ ] สร้าง `public/metrics.php`
- [ ] Auto-instrument ทุก API (7 endpoints)
- [ ] เพิ่ม env var `METRICS_ENABLED`

### Config (DevOps)
- [ ] Prometheus config ตัวอย่าง (docs)
- [ ] Grafana dashboard JSON (export)
- [ ] docker-compose optional: Prometheus + Grafana

### Docs (DevOps + SA)
- [ ] เขียน `docs/runbooks/monitoring.md` update
- [ ] Update `docs/architecture/overview.md`
- [ ] Update `example.env`

## 🔗 Dependencies

- APCu enabled (already in Dockerfile?)

## 📝 Notes

- ถ้า APCu ไม่มี (shared hosting) — fallback เป็น file
- ดูเพิ่มที่ [`docs/runbooks/monitoring.md`](../runbooks/monitoring.md)

## 🔖 Labels

`feature`, `devops`, `observability`, `metrics`
