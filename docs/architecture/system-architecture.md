# System Architecture

## Overview

StudentHub is a monolithic Laravel 13 application following the Model-View-Controller (MVC) architectural pattern. There is no separate API layer; all communication happens through server-rendered Blade views over HTTP. Access control is enforced at the routing layer through two middleware: Laravel's built-in `auth` middleware and a custom `AdminMiddleware`.

---

## Architectural Layers

### 1. Routing Layer (`routes/web.php`, `routes/auth.php`)

All HTTP routes are registered in two files. Routes are grouped by middleware, not by resource type.

- **Public routes** (no middleware): welcome page, login, register, password reset.
- **Authenticated routes** (`auth` middleware): dashboard, students, attendance, profile.
- **Admin routes** (`auth` + `admin` middleware, `/admin` prefix): subjects, exams, exam grades, teacher approvals, statistics.

### 2. Middleware Layer

| Middleware | Class | Purpose |
|---|---|---|
| `auth` | Laravel built-in | Redirects unauthenticated users to `/login` |
| `admin` | `App\Http\Middleware\AdminMiddleware` | Aborts with 403 if the user does not have `role = 'admin'` AND `status = 'approved'` |
| `guest` | Laravel built-in | Redirects authenticated users away from login/register |

The `admin` alias is registered in `bootstrap/app.php`:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
]);
```

### 3. Controller Layer

Controllers handle one concern each. Validation is performed inline using `$request->validate()` or via dedicated `FormRequest` classes. Controllers never contain raw SQL except inside statistics controllers, which use the Query Builder for aggregation queries.

| Controller | Responsibility |
|---|---|
| `Auth\RegisteredUserController` | New user registration (always creates `role=teacher`, `status=pending`) |
| `Auth\AuthenticatedSessionController` | Login and logout |
| `ProfileController` | Edit profile, change email, delete account |
| `StudentController` | Full CRUD for student records |
| `AttendanceController` | Daily marking, update, history, attendance dashboard |
| `SubjectController` | Subject CRUD, grade assignment |
| `ExamController` | Exam CRUD, target student preview |
| `ExamGradeController` | Bulk grade entry, individual grade edit |
| `Admin\TeacherApprovalController` | List pending teachers, approve, reject |
| `ClassStatisticsController` | Per-class aggregate statistics |
| `GradeStatisticsController` | Per-grade aggregate statistics |
| `SchoolStatisticsController` | School-wide statistics with optional date range |
| `SubjectStatisticsController` | Per-subject aggregate statistics |

### 4. Model Layer

| Model | Table | Key Relationships |
|---|---|---|
| `User` | `users` | None defined (no Eloquent relations declared) |
| `Student` | `students` | `hasMany(Attendance)`, `hasMany(ExamGrade)` |
| `Attendance` | `attendances` | `belongsTo(Student)`, `belongsTo(User, 'marked_by')` |
| `Subject` | `subjects` | `hasMany(GradeSubject)`, `hasMany(Exam)` |
| `GradeSubject` | `grade_subject` | `belongsTo(Subject)` |
| `Exam` | `exams` | `belongsTo(Subject)`, `hasMany(ExamGrade)` |
| `ExamGrade` | `exam_grades` | `belongsTo(Exam)`, `belongsTo(Student)` |

### 5. View Layer

Blade templates are organised by module. The main application layout is `resources/views/layouts/app.blade.php`, which provides:
- A fixed sidebar with role-aware navigation (admin-only sections are hidden from teachers)
- A sticky top bar for page titles and action buttons
- An Alpine.js-powered toast notification system
- Mobile-responsive sidebar with overlay

The `app.blade.php` layout uses Alpine.js stores for toast state management. All pages extend this layout via `<x-app-layout>`.

Guest pages (login, register, password reset) use the simpler `layouts/guest.blade.php`.

---

## Component Architecture Diagram

```mermaid
graph TD
    Browser["Browser / HTTP Client"]
    Router["routes/web.php\nroutes/auth.php"]
    AuthMW["auth Middleware\n(Laravel built-in)"]
    AdminMW["admin Middleware\n(AdminMiddleware.php)"]
    Controllers["Controllers"]
    Models["Eloquent Models"]
    DB["SQLite Database"]
    Blade["Blade Views\n(resources/views/)"]

    Browser -->|HTTP Request| Router
    Router -->|Protected routes| AuthMW
    AuthMW -->|Admin routes| AdminMW
    AuthMW -->|Standard routes| Controllers
    AdminMW --> Controllers
    Controllers -->|Query/Mutate| Models
    Models -->|SQL| DB
    DB -->|Result set| Models
    Models -->|Data| Controllers
    Controllers -->|compact()| Blade
    Blade -->|Rendered HTML| Browser
```

---

## Authentication Flow Summary

Registration creates a `teacher` account with `status = pending`. The login process (inside `LoginRequest::authenticate()`) explicitly checks `status === 'approved'` after credentials are validated. If the status is `pending` or `rejected`, the user is logged out immediately and an error message is returned. This means the approval gate is enforced at login, not at the route level for teacher accounts.

Admin accounts bypass this problem because they are seeded with `status = approved` directly.

---

## Frontend Architecture

The frontend is built entirely with Blade templates. No JavaScript framework is used for routing or state.

| Technology | Role |
|---|---|
| Tailwind CSS v3 | Utility-first styling |
| Alpine.js v3 | Lightweight interactivity (sidebar toggle, dropdowns, toast notifications) |
| `@tailwindcss/forms` | Form reset plugin |
| Vite 8 | Asset bundling and hot reload |
| `laravel-vite-plugin` | Vite integration for Laravel's asset manifest |

Custom component classes are defined in `resources/css/app.css` using `@layer components`, including: `.btn`, `.btn-primary`, `.btn-danger`, `.card`, `.badge`, `.form-input`, `.nav-item-active`, `.table-header`, `.table-cell`.

---

## Request Lifecycle

See [request-lifecycle.md](request-lifecycle.md) for a step-by-step trace.
