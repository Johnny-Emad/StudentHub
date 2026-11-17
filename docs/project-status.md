# Project Status

Honest assessment of what StudentHub does today, verified against source code. Nothing below is aspirational.

## Implemented and working (by inspection)

| Feature | State |
|---|---|
| Registration → pending teacher → admin approval → login | ✅ Complete workflow |
| Login approval gate (all roles) + rate limiting (5 attempts) | ✅ |
| Password reset, confirm-password, password update (Breeze) | ✅ |
| Profile edit/delete | ✅ |
| Student CRUD + search + grade/section/status filters + pagination | ✅ |
| Attendance bulk marking (transactional `updateOrCreate`), single update, history, rate dashboard | ✅ |
| Subject CRUD + grade-level assignment (replace-all, transactional) | ✅ |
| Exam CRUD with subject↔grade↔section validation | ✅ |
| Exam grade bulk upsert + per-student subject averages | ✅ (bulk path; see bug below for single-edit) |
| Four statistics dashboards + school date-range filter | ✅ (cached) |
| Teacher approval list + approve/reject | ✅ |
| Dashboard with student stats + recent students | ✅ |
| Performance indexes on students/attendances | ✅ |

## Partially implemented / scaffolded

| Feature | State |
|---|---|
| Email verification | Routes + views exist; `MustVerifyEmail` commented out on `User`; `verified` middleware unused — **dead code** |
| Role `user` | DB default exists; no code assigns or specially handles it |
| `admin.teachers.update` catch-all route | Works but redundant with approve/reject routes |
| `fix_missing_columns_in_exams_table` migration | Defensive no-op on clean installs; repairs legacy DBs only |
| Queue/jobs/cache/sessions tables | Migrated and configured (`database` drivers) but the app dispatches no jobs; only cache/sessions are actually used |

## Broken / verified issues

| # | Issue | Location | Impact |
|---|---|---|---|
| 1 | **Duplicate `students.notes` migration** — column created in both `create_students_table` and `add_notes_to_students_table` | `database/migrations/2026_11_16_223616_add_notes_to_students_table.php` | **Fresh `php artisan migrate` fails** (verified on SQLite; same on MySQL). **Entire test suite fails** before running any test. Existing databases migrated before the duplication are unaffected |
| 2 | **Hardcoded admin credentials** — real email + password `J123456` in source | `database/seeders/AdminSeeder.php` | Credential exposure if repo is public; **rotate if ever deployed** |
| 3 | **`ensureGradeBelongsToExam` strict type bug** — `$student->grade === $exam->grade` compares int (tinyint) to string (varchar) → always `false` | `app/Http/Controllers/ExamGradeController.php:196-200` | Single exam-grade edit/update (`exams.grades.edit`/`update`) likely **always 404s**. Bulk `store` unaffected (uses string-cast `in_array`) |
| 4 | **Stale Breeze tests** — registration test expects auto-login; auth test uses pending factory user; email-verification tests assume unimplemented interface | `tests/Feature/` | Will fail once migrations are fixed |
| 5 | **Approval gate is login-time only** — rejected users keep active sessions until expiry; no per-request status check for teachers | `app/Http/Requests/Auth/LoginRequest.php` | Rejected teacher retains access up to session lifetime (120 min default) |
| 6 | **Dashboard trend badges hardcoded** — `+12%`, `+5%`, `-2%` are literal strings in Blade | `resources/views/dashboard.blade.php` | Misleading UI; not computed from data |
| 7 | **`composer.json` leftover** — `post-create-project-cmd` creates `database/database.sqlite` | `composer.json` | Suggests SQLite default; harmless but misleading |

## Known limitations (by design or accepted debt)

- **MySQL 8.0+ only** for development; SQLite is test-only.
- **No email notifications** — `MAIL_MAILER=log`; approval decisions are silent.
- **No admin-creation UI** — admins via seeder/SQL only.
- **No soft deletes** anywhere; deletes cascade aggressively (student → attendance+grades; subject → exams → grades).
- **No file uploads, no notifications, no API, no parent portal.**
- **Inactive students** are not excluded from attendance marking, exam targeting, grade entry, or statistics — only seeders filter on `status='active'`.
- **Statistics staleness** — 60–300 s cache TTLs with zero invalidation.
- **Grade/section referential integrity** is application-level only; exams can be orphaned by later student/subject changes.
- **`grade` type mismatch** — `students.grade` tinyint vs `grade_subject.grade`/`exams.grade` varchar; works via coercion, blocks real FKs.
- **No audit trail** — no `created_by`/`approved_by`/`updated_by` columns anywhere.

## Planned / future (not implemented — do not assume existence)

From the project's own README direction and obvious gaps: email notifications for approvals, dynamic dashboard trends, student photos, PDF/CSV report exports, parent portal, role-management UI, multi-tenancy, mobile API. **None of these exist in code.**

## Technical debt summary

1. Fix the duplicate `notes` migration (unblocks installs **and** the whole test suite).
2. Move admin credentials to env/SQL and rotate the exposed password.
3. Fix the `===` grade comparison in `ExamGradeController`.
4. Update or delete stale Breeze tests; add feature tests for the real modules.
5. Decide on per-request status enforcement vs. login-time-only gate.
6. Add cache invalidation (or accept documented staleness).
7. Reconcile `grade` column types (tinyint vs varchar) if FK integrity is ever needed.
