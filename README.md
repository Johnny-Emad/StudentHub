# StudentHub

A web-based school management system built with Laravel 13, designed to help teachers and administrators manage students, attendance, subjects, exams, and academic performance analytics from a single platform.

---

## Table of Contents

- [Project Overview](#project-overview)
- [Problem Statement](#problem-statement)
- [Project Objectives](#project-objectives)
- [Main Features](#main-features)
- [User Roles and Permissions](#user-roles-and-permissions)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Database Overview](#database-overview)
- [Main Modules](#main-modules)
- [Screenshots](#screenshots)
- [Installation Requirements](#installation-requirements)
- [Installation Instructions](#installation-instructions)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [How to Run the Project](#how-to-run-the-project)
- [Testing](#testing)
- [Project Structure](#project-structure)
- [Documentation](#documentation)
- [Troubleshooting](#troubleshooting)
- [Known Limitations](#known-limitations)
- [Future Improvements](#future-improvements)
- [License](#license)

---

## Project Overview

StudentHub is a full-stack Laravel application that centralises school operations into one secure, role-based interface. Teachers can record student data and daily attendance, while administrators manage subjects, design exams, record grades, approve teacher accounts, and analyse school-wide academic performance.

The application implements a two-tier role system (teacher / admin) with an approval workflow that prevents unauthorised access. All data operations are protected by Laravel's authentication middleware and a custom `AdminMiddleware`.

---

## Problem Statement

Schools that rely on paper records or disconnected spreadsheets face recurring problems: inconsistent student data, difficulty tracking attendance over time, no easy way to correlate exam results across grades, and no central view of school performance. StudentHub addresses these problems with a structured, validated, and permission-controlled web application.

---

## Project Objectives

- Provide a central registry for student records with search and filtering.
- Enable daily attendance marking and historical review per class or student.
- Allow administrators to define subjects, assign them to grade levels, and create targeted exams.
- Record and display individual student exam grades with per-subject and per-class averages.
- Expose school-wide, grade-level, class-level, and subject-level statistics dashboards.
- Enforce a teacher registration and admin approval workflow before any teacher gains access.

---

## Main Features

| Feature | Description |
|---|---|
| Teacher Registration & Approval | Teachers register and are held in a `pending` state until an admin approves or rejects them |
| Student Management | Full CRUD with search, grade/section/status filters, and paginated results |
| Attendance | Daily marking (present/absent), duplicate prevention, historical log with filters, attendance rate dashboard |
| Subject Management | Create/edit/delete subjects, assign subjects to specific grade levels |
| Exam Management | Create exams tied to a subject and class, validate grade/section combinations against real student data |
| Exam Grading | Bulk-enter marks for all students in an exam, edit individual grades, view per-exam and per-subject averages |
| Statistics Dashboards | Four views: School-wide, Grade-level, Class-level, Subject-level with date-range filtering |
| Profile Management | Update name/email, change password, delete account |

---

## User Roles and Permissions

| Action | Guest | Teacher (approved) | Admin (approved) |
|---|:---:|:---:|:---:|
| Register / Login | ✓ | — | — |
| View Dashboard | — | ✓ | ✓ |
| Manage Students (CRUD) | — | ✓ | ✓ |
| Mark & View Attendance | — | ✓ | ✓ |
| Manage Subjects | — | — | ✓ |
| Manage Exams | — | — | ✓ |
| Record Exam Grades | — | — | ✓ |
| View Statistics | — | — | ✓ |
| Approve/Reject Teachers | — | — | ✓ |
| Edit Own Profile | — | ✓ | ✓ |

> **Note:** Registration always creates a `teacher` role account with `pending` status. There is no self-service admin registration path; admins must be seeded directly into the database.

---

## Technology Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3 |
| Framework | Laravel 13.17 |
| Authentication | Laravel Breeze (session-based) |
| Database | MySQL 8.0+ (officially supported; SQLite is used only by the automated test suite) |
| ORM | Eloquent |
| Frontend | Blade templating, Tailwind CSS v3, Alpine.js v3 |
| Build Tool | Vite 8 with `laravel-vite-plugin` |
| Testing | PestPHP v4 |
| Development Tools | Laravel Pint (code style), Laravel Pail (log viewer), Laravel Tinker |

---

## System Architecture

StudentHub follows the standard Laravel MVC pattern with a custom middleware layer for admin access control.

```
Browser Request
      │
      ▼
  routes/web.php ──► auth middleware ──► admin middleware (admin routes only)
      │
      ▼
  Controller (validates input, queries models, returns view)
      │
      ▼
  Eloquent Model ──► MySQL database
      │
      ▼
  Blade View (rendered HTML returned to browser)
```

See [docs/architecture/system-overview.md](docs/architecture/system-overview.md) and [docs/architecture/application-architecture.md](docs/architecture/application-architecture.md) for the full architecture documentation.

---

## Database Overview

The application defines seven business tables, plus the standard Laravel system tables (`migrations`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`):

| Table | Purpose |
|---|---|
| `users` | Teacher and admin accounts with role and approval status |
| `students` | Student records (personal info, grade, class section, status, notes) |
| `attendances` | Daily attendance records per student |
| `subjects` | Subject catalogue |
| `grade_subject` | Maps which subjects are assigned to which grade levels |
| `exams` | Exam definitions (subject, grade, class section, date, total marks) |
| `exam_grades` | Individual student marks per exam |

See [docs/database/database-overview.md](docs/database/database-overview.md), [docs/database/schema.md](docs/database/schema.md), [docs/database/erd.md](docs/database/erd.md), and [docs/database/relationships.md](docs/database/relationships.md) for complete schema documentation.

---

## Main Modules

| Module | Documentation |
|---|---|
| Authentication | [docs/modules/authentication.md](docs/modules/authentication.md) |
| User Management | [docs/modules/user-management.md](docs/modules/user-management.md) |
| Teacher Approval | [docs/modules/teacher-approval.md](docs/modules/teacher-approval.md) |
| Student Management | [docs/modules/student-management.md](docs/modules/student-management.md) |
| Subject Management | [docs/modules/subject-management.md](docs/modules/subject-management.md) |
| Attendance | [docs/modules/attendance.md](docs/modules/attendance.md) |
| Examinations & Exam Grades | [docs/modules/examinations.md](docs/modules/examinations.md) |
| Statistics Dashboards | [docs/modules/statistics.md](docs/modules/statistics.md) |

---

## Screenshots

> Screenshots are not currently included. The following sections describe what each major view presents.

| View | Description |
|---|---|
| Dashboard | Student count cards (total, active, inactive, grade levels) + quick actions + recent student list |
| Students Index | Paginated table with search, grade/section/status filters |
| Attendance Index | Date-based attendance sheet with present/absent toggles per student |
| Admin — Exams Index | Paginated list of all exams with subject, grade, section, date, and total marks |
| Admin — School Statistics | School-wide summary with optional date range filter covering students, exams, grades, and attendance |

---

## Installation Requirements

- PHP 8.3 or higher with extensions: `pdo`, `pdo_mysql`, `mbstring`, `xml`, `ctype`, `json`, `tokenizer`, `openssl`
- MySQL 8.0+ server (or compatible)
- Composer 2.x
- Node.js 18+ and npm 9+
- Git

---

## Installation Instructions

### Prerequisites

- PHP 8.3+ with `pdo_mysql`, `mbstring`, `xml`, `ctype`, `json`, `tokenizer`, `openssl`
- MySQL 8.0+ server **running** before migrations are executed
- Composer 2.x
- Node.js 18+ and npm
- Git

### Steps (cross-platform)

```bash
git clone https://github.com/Johnny-Emad/StudentHub.git
cd StudentHub

composer install

cp .env.example .env        # Windows PowerShell: Copy-Item .env.example .env

php artisan key:generate
php artisan config:clear

npm install
npm run build
```

### Windows PowerShell

```powershell
git clone https://github.com/Johnny-Emad/StudentHub.git
cd StudentHub

composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan config:clear

npm install
npm run build
```

Then create the database and run migrations (see [Database Setup](#database-setup)) and start the server with `php artisan serve`.

---

## Environment Configuration

Open `.env` and review the following values before running migrations:

```dotenv
APP_NAME=StudentHub          # .env.example ships with APP_NAME=Laravel — change it
APP_URL=http://localhost:8000

# MySQL connection — the officially supported database.
# Update DB_USERNAME / DB_PASSWORD to match your local MySQL setup.
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studenthub
DB_USERNAME=root
DB_PASSWORD=

# Mail is set to 'log' driver by default; emails appear in storage/logs/laravel.log
MAIL_MAILER=log
```

Notes:

- `DB_PASSWORD` is empty in `.env.example` for convenience; set a real password if your MySQL user has one.
- `config/database.php` falls back to the `sqlite` connection only if `DB_CONNECTION` is not set at all. The supported and tested configuration is MySQL via `.env`.
- No external service credentials are required for local development.

---

## Database Setup

MySQL must be running before migrations execute.

```bash
# Create the MySQL database (if it does not already exist)
mysql -u root -p -e "CREATE DATABASE studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run all migrations
php artisan migrate
```

Then choose a seeding option:

```bash
# Option A — admin account only (recommended for development)
php artisan db:seed --class=AdminSeeder

# Option B — full demo dataset (admin + 50 teachers + 10,000 students
# + subjects, exams, grades, and attendance). This is heavy and slow.
php artisan db:seed
```

`DatabaseSeeder` calls `AdminSeeder`, `SubjectSeeder`, `TeacherSeeder`, `StudentSeeder`, `ExamSeeder`, `ExamGradeSeeder`, and `AttendanceSeeder`. `StudentSeeder`, `ExamSeeder`, `ExamGradeSeeder`, and `AttendanceSeeder` skip themselves if their tables already contain data.

See [docs/getting-started/configuration.md](docs/getting-started/configuration.md) for seeder details.

> **Security note:** `AdminSeeder` contains a hardcoded password and a personal email address. Change both before deploying to any shared or production environment, and consider rotating the credentials if this repository has ever been public.

---

## How to Run the Project

```bash
# Start the Laravel development server
php artisan serve

# In a separate terminal, start the Vite dev server (hot-reload)
npm run dev
```

The application will be accessible at `http://localhost:8000`.

If you already ran `npm run build`, `php artisan serve` alone is enough — the built assets are served from `public/build`.

To run server + queue + logs + Vite together using Laravel's `dev` script:

```bash
composer run dev
```

---

## Project Structure

```
StudentHub/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/            # TeacherApprovalController
│   │   │   ├── Auth/             # Breeze authentication controllers
│   │   │   ├── AttendanceController.php
│   │   │   ├── ExamController.php
│   │   │   ├── ExamGradeController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── StudentController.php
│   │   │   ├── SubjectController.php
│   │   │   ├── ClassStatisticsController.php
│   │   │   ├── GradeStatisticsController.php
│   │   │   ├── SchoolStatisticsController.php
│   │   │   └── SubjectStatisticsController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/
│   │       ├── Auth/LoginRequest.php
│   │       └── ProfileUpdateRequest.php
│   ├── Models/
│   │   ├── Attendance.php
│   │   ├── Exam.php
│   │   ├── ExamGrade.php
│   │   ├── GradeSubject.php
│   │   ├── Student.php
│   │   ├── Subject.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/               # 12 migration files
│   ├── factories/                # Model factories for all entities
│   └── seeders/                  # AdminSeeder, DatabaseSeeder + 6 demo-data seeders
├── resources/
│   ├── css/app.css               # Tailwind + custom component classes
│   ├── js/app.js
│   └── views/
│       ├── admin/teachers/
│       ├── attendance/
│       ├── auth/
│       ├── components/           # Breeze Blade components
│       ├── exams/
│       ├── layouts/              # app.blade.php, guest.blade.php, navigation.blade.php
│       ├── profile/
│       ├── statistics/
│       ├── students/
│       ├── subjects/
│       ├── dashboard.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── docs/                         # Full documentation suite (see below)
├── tests/                        # PestPHP feature and unit tests
└── bootstrap/app.php             # Middleware aliases registered here
```

---

## Documentation

Full technical documentation is located in the [`docs/`](docs/) directory:

```
docs/
├── README.md                              — Documentation index
├── getting-started/
│   ├── installation.md                    — Prerequisites through first run
│   ├── configuration.md                   — Environment variables, seeders, cache keys
│   └── troubleshooting.md                 — Common failures and fixes
├── architecture/
│   ├── system-overview.md                 — Purpose, roles, module map
│   ├── application-architecture.md        — MVC layers, middleware, authorization model
│   ├── request-lifecycle.md               — Traced request walkthrough
│   └── project-structure.md               — Annotated repository map
├── database/
│   ├── database-overview.md               — Engine support, table inventory
│   ├── schema.md                          — Column-by-column schema reference
│   ├── erd.md                             — Mermaid ER diagram
│   ├── relationships.md                   — Eloquent relations & delete behavior
│   └── database-design-decisions.md       — Design rationale & inconsistencies
├── modules/
│   ├── authentication.md
│   ├── user-management.md
│   ├── teacher-approval.md
│   ├── student-management.md
│   ├── subject-management.md
│   ├── attendance.md
│   ├── examinations.md
│   └── statistics.md
├── backend/
│   ├── models.md
│   ├── controllers.md
│   ├── middleware.md
│   ├── validation.md
│   └── routes.md                          — Complete route table
├── testing/
│   ├── testing-overview.md
│   └── test-coverage.md
└── project-status.md                      — Implemented vs. broken vs. planned
```

---

## Testing

The test suite uses PestPHP v4 with `RefreshDatabase` on every feature test.

```bash
php artisan test          # or: composer test
```

`phpunit.xml` configures tests to run against an **in-memory SQLite** database (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`), independent of the MySQL database used in development. The suite currently covers the Breeze authentication and profile flows; there are no feature tests for students, attendance, exams, or statistics yet.

---

## Troubleshooting

| Problem | Likely cause | Fix |
|---|---|---|
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL server is not running, or wrong host/port | Start MySQL (XAMPP, MySQL service, Docker). Verify `DB_HOST`/`DB_PORT` in `.env`. |
| `SQLSTATE[HY000] [1045] Access denied` | Wrong `DB_USERNAME`/`DB_PASSWORD` | Check credentials with `mysql -u <user> -p`. Update `.env`, then `php artisan config:clear`. |
| `SQLSTATE[HY000] [1049] Unknown database 'studenthub'` | Database not created | Run the `CREATE DATABASE` command in [Database Setup](#database-setup). |
| `php artisan migrate` fails with duplicate column | `2026_11_16_223616_add_notes_to_students_table` adds `notes`, which already exists in `create_students_table` on a fresh install | Known issue — see [Known Limitations](#known-limitations). On an existing database where `notes` was added later, migrations run normally. |
| Blank/unstyled pages after `composer install` | Frontend assets not built | Run `npm install && npm run build` (or `npm run dev` for hot reload). |
| `Vite manifest not found` | `public/build` missing | Run `npm run build`. |
| `500` errors / missing `storage` permissions | `storage/` or `bootstrap/cache` not writable | Ensure the web server user can write to `storage/` and `bootstrap/cache`. On Windows, check folder permissions. |
| Changes to `.env` have no effect | Config cache | Run `php artisan config:clear` (or `php artisan config:cache` in production). |
| Login says "waiting for admin approval" | New teacher accounts start as `pending` | Approve the account via `/admin/teachers` as an admin, or approve directly in the `users` table. |
| `Class 'Pdo\Mysql' not found` / driver errors | PHP MySQL extensions missing | Enable `pdo_mysql` (and `mysqlnd`) in `php.ini` and restart the web server. |

---

## Known Limitations

- **MySQL 8.0+ is the supported database.** `DB_CONNECTION=mysql` in `.env` is the tested configuration. SQLite is used only for the automated test suite (`phpunit.xml`) and is not a supported development database.
- **No email notifications** — teacher approval/rejection does not send an email; the teacher must attempt to log in again to discover their status.
- **Admin seeder only** — there is no UI to promote a user to admin; it must be done via the `AdminSeeder` or directly in the database.
- **Static dashboard change indicators** — the percentage change badges on the dashboard ("+12%", "+5%", etc.) are hardcoded, not calculated from real historical data.
- **No file uploads** — student profiles have no photo field or document attachment capability.
- **No notifications system** — there is no in-app notification mechanism for any event.
- **Email verification is scaffolded but not enforced** — the `MustVerifyEmail` interface on `User` is commented out.
- **Duplicate `notes` migration** — `students.notes` is defined both in `create_students_table` and re-added by `add_notes_to_students_table`; a fresh `php artisan migrate` on an empty database may fail on the second migration.
- **`composer.json` leftover script** — `post-create-project-cmd` still creates `database/database.sqlite`, a remnant of the Laravel skeleton; it does not affect normal installs but is misleading.
- **Hardcoded admin credentials in `AdminSeeder`** — see the security note in [Database Setup](#database-setup).

---

## Future Improvements

- Email notifications for teacher approval/rejection decisions.
- Dynamic dashboard trend calculations based on historical data.
- Student profile photo uploads.
- Exportable reports (PDF, CSV) for attendance and exam grades.
- Parent/guardian portal with read-only access to their child's records.
- Role management UI so admins can promote teachers without database access.
- Multi-school or multi-tenant support.
- API endpoints for mobile applications.

---

## License

`composer.json` declares the project under the [MIT license](https://opensource.org/licenses/MIT). Note that no `LICENSE` file is currently included in the repository root — adding one is recommended for clarity.
