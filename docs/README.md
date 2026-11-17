# StudentHub — Documentation

Technical documentation for **StudentHub**, a school management system built with Laravel 13. Every page in this documentation was written from direct analysis of the application source code — migrations, models, controllers, middleware, routes, seeders, and tests. Where the code and older documentation disagreed, the code was treated as the source of truth.

> **Accuracy note:** StudentHub is under active development. Several known issues are documented in [project-status.md](project-status.md), including a migration bug that currently prevents fresh installs and the automated test suite from running.

---

## Getting Started

| File | Description |
|---|---|
| [getting-started/installation.md](getting-started/installation.md) | Prerequisites, clone, dependencies, environment, database, first run — including Windows PowerShell commands |
| [getting-started/configuration.md](getting-started/configuration.md) | Environment variables, seeders, cache keys, and runtime configuration |
| [getting-started/troubleshooting.md](getting-started/troubleshooting.md) | Common installation and runtime failures with diagnoses and fixes |

## Architecture

| File | Description |
|---|---|
| [architecture/system-overview.md](architecture/system-overview.md) | What StudentHub does, the two-role model, and how the layers fit together |
| [architecture/application-architecture.md](architecture/application-architecture.md) | MVC implementation, middleware chain, module map, and interaction diagram |
| [architecture/request-lifecycle.md](architecture/request-lifecycle.md) | A traced request from HTTP to Blade response, including where validation and authorization occur |
| [architecture/project-structure.md](architecture/project-structure.md) | Annotated directory and file map of the repository |

## Database

| File | Description |
|---|---|
| [database/database-overview.md](database/database-overview.md) | Engine support, table inventory, and design summary |
| [database/schema.md](database/schema.md) | Column-level reference for every table, verified against migrations |
| [database/erd.md](database/erd.md) | Mermaid ER diagram of the application schema |
| [database/relationships.md](database/relationships.md) | Eloquent relationships and foreign-key behavior |
| [database/database-design-decisions.md](database/database-design-decisions.md) | Why the schema looks the way it does, plus inconsistencies found |

## Modules

| File | Description |
|---|---|
| [modules/authentication.md](modules/authentication.md) | Registration, login with approval gate, password reset, email verification scaffolding, profile management |
| [modules/user-management.md](modules/user-management.md) | The `users` table, roles, statuses, and how accounts come into existence |
| [modules/teacher-approval.md](modules/teacher-approval.md) | Admin review workflow: pending → approved / rejected |
| [modules/student-management.md](modules/student-management.md) | Student CRUD, search, filters, validation rules |
| [modules/subject-management.md](modules/subject-management.md) | Subject CRUD and grade-level assignment via `grade_subject` |
| [modules/attendance.md](modules/attendance.md) | Daily marking, single-record update, history, and attendance-rate dashboard |
| [modules/examinations.md](modules/examinations.md) | Exam CRUD, target-student resolution, and exam grade entry/editing |
| [modules/statistics.md](modules/statistics.md) | Subject, class, grade, and school statistics dashboards and their caching |

## Backend Reference

| File | Description |
|---|---|
| [backend/models.md](backend/models.md) | All Eloquent models, fillable attributes, casts, and relationships |
| [backend/controllers.md](backend/controllers.md) | Every controller, its responsibilities, and notable logic |
| [backend/middleware.md](backend/middleware.md) | `AdminMiddleware` and the framework middleware in use |
| [backend/validation.md](backend/validation.md) | Consolidated validation rules per operation |
| [backend/routes.md](backend/routes.md) | Complete route table with methods, names, middleware, and controllers |

## Testing

| File | Description |
|---|---|
| [testing/testing-overview.md](testing/testing-overview.md) | Framework, configuration, how to run tests, and current suite state |
| [testing/test-coverage.md](testing/test-coverage.md) | What is covered, what is not, and known stale tests |

## Project Status

| File | Description |
|---|---|
| [project-status.md](project-status.md) | Implemented vs. partial vs. planned features, known limitations, and technical debt |
