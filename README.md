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
- [Project Structure](#project-structure)
- [Documentation](#documentation)
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
| Database | MySQL 8.0+ (any PDO-compatible database is supported) |
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

See [docs/architecture/system-architecture.md](docs/architecture/system-architecture.md) for the full architecture document with Mermaid diagrams.

---

## Database Overview

The application uses seven tables:

| Table | Purpose |
|---|---|
| `users` | Teacher and admin accounts with role and approval status |
| `students` | Student records (personal info, grade, class section, status) |
| `attendances` | Daily attendance records per student |
| `subjects` | Subject catalogue |
| `grade_subject` | Maps which subjects are assigned to which grade levels |
| `exams` | Exam definitions (subject, grade, class section, date, total marks) |
| `exam_grades` | Individual student marks per exam |

See [docs/database/database-overview.md](docs/database/database-overview.md) and [docs/database/erd.md](docs/database/erd.md) for complete schema documentation and ER diagrams.

---

## Main Modules

| Module | Documentation |
|---|---|
| Authentication & Approval | [docs/modules/authentication.md](docs/modules/authentication.md) |
| Teacher Management | [docs/modules/teacher-management.md](docs/modules/teacher-management.md) |
| Student Management | [docs/modules/student-management.md](docs/modules/student-management.md) |
| Subject Management | [docs/modules/subject-management.md](docs/modules/subject-management.md) |
| Attendance | [docs/modules/attendance.md](docs/modules/attendance.md) |
| Exams | [docs/modules/exams.md](docs/modules/exams.md) |
| Exam Grades | [docs/modules/exam-grades.md](docs/modules/exam-grades.md) |

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

```bash
# 1. Clone the repository
git clone <repository-url> StudentHub
cd StudentHub

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Install Node dependencies
npm install

# 6. Build frontend assets
npm run build
```

---

## Environment Configuration

Open `.env` and review the following values before running migrations:

```dotenv
APP_NAME=StudentHub
APP_URL=http://localhost:8000

# MySQL connection — update credentials to match your local setup
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studenthub
DB_USERNAME=root
DB_PASSWORD=

# Mail is set to 'log' driver by default; emails appear in storage/logs/laravel.log
MAIL_MAILER=log
```

No external service credentials are required for local development.

---

## Database Setup

```bash
# Create the MySQL database (if it does not already exist)
mysql -u root -p -e "CREATE DATABASE studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run all migrations
php artisan migrate

# Seed the admin account
php artisan db:seed --class=AdminSeeder
```

After seeding, the admin account is available. See [docs/setup/configuration.md](docs/setup/configuration.md) for seeder details.

> **Security note:** The `AdminSeeder` contains a hardcoded password. Change this before deploying to any shared environment.

---

## How to Run the Project

```bash
# Start the Laravel development server
php artisan serve

# In a separate terminal, start the Vite dev server (hot-reload)
npm run dev
```

The application will be accessible at `http://localhost:8000`.

To run as a single command using the Laravel `dev` script:

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
│   ├── migrations/               # 10 migration files
│   └── seeders/
│       ├── AdminSeeder.php
│       └── DatabaseSeeder.php
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
├── docs/                         # Full documentation suite
└── bootstrap/app.php             # Middleware aliases registered here
```

---

## Documentation

Full technical documentation is located in the [`docs/`](docs/) directory:

```
docs/
├── README.md                          — Documentation index
├── architecture/
│   ├── system-architecture.md         — MVC architecture + Mermaid diagrams
│   └── request-lifecycle.md           — Traced request lifecycle example
├── database/
│   ├── database-overview.md           — All tables, columns, constraints
│   ├── schema.md                      — Column-by-column schema reference
│   └── erd.md                         — Mermaid ER diagram
├── modules/
│   ├── authentication.md
│   ├── teacher-management.md
│   ├── student-management.md
│   ├── subject-management.md
│   ├── attendance.md
│   ├── exams.md
│   └── exam-grades.md
├── workflows/
│   ├── authentication-flow.md
│   ├── teacher-approval-flow.md
│   ├── student-management-flow.md
│   ├── attendance-flow.md
│   └── examination-flow.md
├── routes/
│   └── route-reference.md             — Full route table
└── setup/
    ├── installation.md
    └── configuration.md
```

---

## Known Limitations

- **No email notifications** — teacher approval/rejection does not send an email; the teacher must attempt to log in again to discover their status.
- **Admin seeder only** — there is no UI to promote a user to admin; it must be done via the `AdminSeeder` or directly in the database.
- **MySQL required** — the application is configured for MySQL; SQLite is not supported out of the box.
- **Static dashboard change indicators** — the percentage change badges on the dashboard ("+12%", "+5%", etc.) are hardcoded, not calculated from real historical data.
- **No file uploads** — student profiles have no photo field or document attachment capability.
- **No notifications system** — there is no in-app notification mechanism for any event.
- **Email verification is scaffolded but not enforced** — the `MustVerifyEmail` interface on `User` is commented out.

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

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
