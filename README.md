# TaskFlow — PHP Full-Stack Task & Team Management System

A production-grade, full-stack team collaboration and project management web application built from scratch with **Core PHP 8.2+**, **MySQL**, **PDO**, **HTML5/CSS3/Vanilla JavaScript**, and an MVC architecture.

Designed with modern SaaS aesthetics, security-first engineering, and zero external framework dependencies.

---

## 🌟 Key Highlights & Features

- **True Full-Stack Implementation**: Custom MVC architecture separating Models, Views, Controllers, Helpers, and Middlewares.
- **Enterprise Role-Based Access Control (RBAC)**:
  - **Administrator**: Complete control over organization users, teams, projects, global tasks, and audit activity feeds.
  - **Team Leader**: Manages assigned teams, oversees team members, creates projects, schedules deliverables, and assigns tasks.
  - **Team Member**: Tracks assigned tasks, updates task progress, collaborates via task discussion threads, and monitors deadlines.
- **Interactive Kanban Board**: Drag-and-drop workflow (To Do, In Progress, Review, Completed, Blocked) with optimistic UI updates synced via the Fetch API and authenticated CSRF tokens.
- **Real-Time Database Progress Calculation**: Project progress automatically calculates from underlying task completion states.
- **Task Discussions & Comments**: Threaded commentary on tasks with ownership permissions and administrative moderation.
- **In-App Notifications**: Real-time alerts for task assignment, status updates, and discussion threads.
- **Audit Logs & Activity Feed**: Chronological tracking of security and operational events (logins, task movements, team changes).
- **Comprehensive Security Suite**:
  - Prepared statements with PDO (100% SQL injection immune).
  - Strict output escaping with `htmlspecialchars()` preventing Stored & Reflected XSS.
  - Anti-CSRF token verification on all POST/PUT/DELETE requests.
  - Brute-force rate limiting (locks after repeated failed logins within 15 minutes).
  - Secure session handling: `HttpOnly`, `SameSite=Lax`, inactivity timeout, and session ID regeneration.
  - Record-level IDOR protection on sensitive resources.

---

## 📁 Architecture & Directory Structure

```text
tasflow/
├── app/
│   ├── Controllers/         # Auth, Dashboard, Task, Project, Team, User, Profile, Notification, Activity
│   ├── Core/                # Controller, Database, Request, Response, Router, Session, View
│   ├── Helpers/             # Security (CSRF, XSS escaping, input sanitation)
│   ├── Middleware/          # AuthMiddleware, GuestMiddleware, AdminMiddleware, LeaderOrAdminMiddleware
│   ├── Models/              # Model, User, Team, Project, Task, TaskComment, Notification, ActivityLog
│   └── Views/               # Blade-style modular views & partials
│       ├── layouts/         # app.php (master dashboard), auth.php (login/register)
│       ├── auth/            # login, register
│       ├── dashboard/       # role-tailored dashboard with status distribution metrics
│       ├── tasks/           # index, kanban, view, create, edit
│       ├── projects/        # index, view, create, edit
│       ├── teams/           # index, view, create, edit
│       ├── users/           # admin user directory, role/status switching
│       ├── profile/         # profile update & password change
│       ├── notifications/   # in-app notification center
│       ├── activity/        # organization audit log
│       └── errors/          # 404 page
├── config/
│   └── app.php              # Environment and database configuration
├── database/
│   ├── schema.sql           # Normalized MySQL schema with foreign keys and indices
│   └── seed.sql             # Realistic demo users, teams, projects, tasks, comments, and activities
├── public/
│   ├── index.php            # Web entry point & autoloader
│   └── assets/
│       ├── css/app.css      # SaaS design system (CSS variables, responsive layouts, components)
│       └── js/app.js        # Drag-and-drop Kanban, AJAX status updates, alerts & toasts
├── routes/
│   └── web.php              # Route definitions with middleware chains
├── storage/
│   ├── logs/                # Audit & error logs
│   └── uploads/             # Media uploads
├── tests/
│   ├── run_tests.php        # 25-point automated QA and security test suite
│   └── test_http.php        # HTTP probe script
├── .env                     # Local environment settings
├── .env.example             # Template for production and local environments
├── .gitignore               # Excludes secrets, sessions, logs, and vendor files
└── router.php               # Development server router for PHP CLI
```

---

## 🚀 Getting Started & Localhost Setup

### 1. Prerequisites
- **PHP 8.0+** (Tested on PHP 8.2) with `pdo_mysql` and `curl` extensions.
- **MySQL / MariaDB** (Included with XAMPP).

### 2. Database Initialization
Ensure MySQL is running (via XAMPP or terminal):
```powershell
# Create database, tables, and seed records
C:\xampp\mysql\bin\mysql.exe -u root -e "source database/schema.sql"
C:\xampp\mysql\bin\mysql.exe -u root -e "source database/seed.sql"
```

### 3. Environment Configuration
The `.env` file is pre-configured for localhost:
```ini
APP_NAME="TaskFlow"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=taskflow_db
DB_USER=root
DB_PASS=
```

### 4. Running the Web Application

To run using the PHP built-in server:
```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t public router.php
```

Then open your browser and navigate to:
👉 **[http://127.0.0.1:8080](http://127.0.0.1:8080)** (or `http://localhost:8080`)

---

## 👥 Demo Accounts & Credentials

All demo accounts share the password: **`Password123!`**

| Role | Email Address | Access & Capabilities |
| :--- | :--- | :--- |
| **Administrator** | `admin@taskflow.local` | Full control over users, teams, projects, tasks, and system logs |
| **Team Leader** | `leader@taskflow.local` | Manages Core Product Engineering team, creates projects & assigns tasks |
| **Team Member** | `member@taskflow.local` | Views assigned tasks, updates Kanban status, comments on tasks |
| **Team Member** | `liam@taskflow.local` | Active engineering member with assigned security audit tasks |

---

## 🧪 Testing & Verification

A comprehensive automated test suite is included in `tests/run_tests.php`. To execute:

```powershell
C:\xampp\php\php.exe tests/run_tests.php
```

**Results:**
```text
=== STARTING TASKFLOW AUTOMATED TEST SUITE ===
[PASS] Database connection initialized via PDO
[PASS] Admin seed user found in database
[PASS] Password verification succeeds for correct password
[PASS] Password verification rejects wrong password
[PASS] User created successfully with role team_member
[PASS] New user retrieved by email
[PASS] Rate limiting triggers after 6 attempts within 15 mins
[PASS] Rate limiting cleared after reset
[PASS] Team created with ID
[PASS] Team leader correctly associated
[PASS] Member added to team
[PASS] Team roster retrieves active members
[PASS] Project created with team association
[PASS] Project retrieved with initial 0% progress
[PASS] Tasks created in database
[PASS] Project progress correctly calculated from real tasks (50%)
[PASS] Project progress updates to 100% when all tasks completed
[PASS] Comment added to task
[PASS] Task comments retrieved chronologically
[PASS] Notification created for user
[PASS] Unread notification count tracks accurately
[PASS] Notification marked as read
[PASS] Activity log captures events
[PASS] XSS payload escaped with htmlspecialchars
[PASS] Teardown cleaned up test records

============================================
TEST RESULTS: 25 PASSED, 0 FAILED
============================================
```
