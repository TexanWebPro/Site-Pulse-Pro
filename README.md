# Site Pulse Pro – MVP Design Document

## 1. Purpose & Strategic Role

**Site Pulse Pro** is a free WordPress plugin designed to function as:

- An _admin-only monitoring and risk visibility tool_ for WordPress sites

The plugin intentionally **reveals problems without fully solving them**, creating a natural upgrade path into a managed caretaker relationship.

---

## 2. Target User (ICP)

- Small business owners (1–20 employees)
- Non-technical operators
- WordPress sites that are business-critical but poorly maintained

**Core mindset:**

> “I don’t want to manage this — I just need to know if I’m exposed.”

---

## 3. MVP Feature Scope (Strict)

### Included in MVP

- Site Pulse Score (0–100)
- Risk category breakdown (read-only)
- Admin-only monitoring dashboard
- Report generation
- Basic trend storage (7–30 days)

### Explicitly Excluded

- Auto-fixes
- One-click updates
- Malware cleanup
- Performance optimization
- Public-facing widgets

---

## 4. Core Plugin Screens

### 4.1 Main Dashboard (Overview)

**Purpose:** Immediate risk comprehension

**Components:**

- Large Site Pulse Score (0–100)
- Status label: Healthy / At Risk / Critical
- Last scan timestamp

**Score Weighting (Initial):**

- Updates & Versions – 30%
- Security Hygiene – 25%
- Uptime & Errors – 25%
- Backups – 10%
- Tech Stack Exposure – 10%

---

### 4.2 Risk Category Modules

Each module displays:

- Status indicator (green / yellow / red)
- Short business-language explanation

#### Modules

1. Updates (core, plugins, themes)
2. Uptime & Errors
3. Security Hygiene
4. Backup Status
5. Tech Stack Exposure

---

### 4.3 Admin-Only Monitoring View

**Purpose:** Ongoing visibility without resolution

**Panels:**

- Uptime checks (last 7 days)
- Average response time trend
- HTTP error log (500 / 404 counts)
- Change detection (plugin/theme/core updates)
- Pending update count

**Important:**
No fix actions are available in free version.

---

### 4.4 Report Generation

**Trigger:** “Generate Full Site Health Report” button

**Outputs:**

- On-screen expanded results
- Email-delivered PDF snapshot

---

## 5. Data Collection Logic

### 5.1 Local Checks

- `get_core_updates()`
- `get_plugin_updates()`
- `get_theme_updates()`
- PHP version
- TLS/SSL presence
- Backup plugin detection

### 5.2 Remote Checks

- Scheduled HTTP request to site homepage
- Response time capture
- Status code logging

Cron: every 15 minutes (configurable later)

---

## 6. Site Pulse Score Calculation

Each category emits a normalized score (0–100).

Example:

- Updates: 100 – (10 × outdated plugins)
- Uptime: 100 – (downtime minutes × weight)

Final score = weighted average.

---

## 7. Technical Architecture (MVP)

### WordPress Hooks

- `admin_menu`
- `admin_enqueue_scripts`
- `wp_cron`
- `register_activation_hook`

### Storage Strategy

- Custom DB tables for metrics
- Options API for config
- Transients for caching

---

## 8. Database Schema (MVP)

### 8.1 `wp_sitepulse_metrics`

Stores time-series monitoring data.

| Column       | Type        | Notes                        |
| ------------ | ----------- | ---------------------------- |
| id           | BIGINT PK   | Auto-increment               |
| site_id      | BIGINT      | Multisite-ready              |
| metric_type  | VARCHAR(50) | uptime, response_time, error |
| metric_value | FLOAT       | Numeric value                |
| status_code  | INT         | HTTP status (nullable)       |
| recorded_at  | DATETIME    | UTC                          |

---

### 8.2 `wp_sitepulse_scans`

Stores aggregated scan results.

| Column          | Type      | Notes        |
| --------------- | --------- | ------------ |
| id              | BIGINT PK |              |
| pulse_score     | INT       | 0–100        |
| updates_score   | INT       |              |
| security_score  | INT       |              |
| uptime_score    | INT       |              |
| backup_score    | INT       |              |
| tech_score      | INT       |              |
| critical_issues | INT       | Count        |
| scan_summary    | TEXT      | JSON-encoded |
| scanned_at      | DATETIME  |              |

---

## 9. Security & Privacy

- Admin-only access (`manage_options`)
- Nonce verification on all actions
- No public REST endpoints in MVP
- GDPR-safe minimal data collection

---

## 10. MVP Build Order (Recommended)

1. Plugin shell + admin menu
2. Local data collectors
3. DB tables + cron
4. Score engine
5. Dashboard UI
6. Email
7. Copy

---

## 11. Future Expansion (Post-MVP)

- Paid tier with auto-fixes
- External monitoring service
- Multisite dashboard
- White-label partner version

---

## 12. Project Directory Structure

```
site-pulse-pro/
├── site-pulse-pro.php # Main plugin bootstrap file
├── uninstall.php # Cleanup on uninstall (optional for MVP)
│
├── readme.txt # WP plugin metadata (basic)
│
├── includes/
│ ├── bootstrap.php # Core loader, constants, init
│ │
│ ├── admin/
│ │ ├── menu.php # Admin menu + page registration
│ │ ├── dashboard.php # Dashboard rendering logic
│ │ ├── assets.php # Admin scripts & styles enqueue
│ │
│ ├── collectors/
│ │ ├── updates.php # Core/plugin/theme update checks
│ │ ├── uptime.php # Remote uptime + response checks
│ │ ├── security.php # Headers, versions, hygiene checks
│ │ ├── backups.php # Backup plugin detection (heuristic)
│ │ └── tech.php # PHP, TLS, stack exposure
│ │
│ ├── scoring/
│ │ ├── calculator.php # Site Pulse score engine
│ │ └── weights.php # Category weights & thresholds
│ │
│ ├── cron/
│ │ ├── scheduler.php # Cron registration
│ │ └── runner.php # Periodic scan execution
│ │
│ ├── database/
│ │ ├── schema.php # Table creation / upgrades
│ │ └── queries.php # Read/write helpers
│ │
│ ├── reports/
│ │ ├── generator.php # Report data assembly
│ │ └── email.php # Email delivery
│ │
│ ├── utils/
│ │ ├── permissions.php # Capability checks
│ │ ├── sanitizer.php # Input sanitization helpers
│ │ └── helpers.php # Shared utility functions
│ │
│ └── constants.php # Plugin-wide constants
│
├── assets/
│ ├── css/
│ │ └── admin.css # Dashboard styles
│ │
│ └── js/
│ └── admin.js # Dashboard interactivity
│
├── templates/
│ ├── dashboard.php # Admin dashboard markup
│ ├── modules/
│ │ ├── updates.php
│ │ ├── uptime.php
│ │ ├── security.php
│ │ ├── backups.php
│ │ └── tech.php
│ │
│ └── report.php # Report view template
│
└── languages/
└── site-pulse-pro.pot # Translation file (optional MVP)
```

**Document Owner:** Texan Web Pro
**Status:** MVP Reference Spec
