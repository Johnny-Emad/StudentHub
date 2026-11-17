# Controllers

All controllers in `app/Http/Controllers/`. Business logic lives here — there are no services, actions, or jobs.

## Shared / authenticated area

### `DashboardController@index`

`GET /dashboard` — any approved user.

- `Cache::remember('dashboard.stats', 60)` → total/active/inactive student counts + distinct grade count.
- `Cache::remember('dashboard.recent_students', 60)` → latest 5 students.
- Returns `dashboard` view. The Blade template hardcodes trend badges (`+12%` etc.) — cosmetic only.

### `ProfileController`

Standard Breeze: `edit` (name/email form), `update` (via `ProfileUpdateRequest`; nulls `email_verified_at` on email change), `destroy` (requires `current_password`, deletes account, invalidates session).

### `StudentController`

Full CRUD + search/filters — see [modules/student-management.md](../modules/student-management.md). Notable: `index()` uses `whereRaw("CONCAT(first_name,' ',last_name) LIKE ?")` for full-name search; pagination 10; filter options derived from distinct DB values.

### `AttendanceController`

Mark/update/history/dashboard — see [modules/attendance.md](../modules/attendance.md). Notable: `store()` wraps `updateOrCreate` loop in `DB::transaction`; `dashboard()` computes live (uncached) attendance rate.

## Admin area (`/admin`, `auth`+`admin` middleware)

### `Admin\TeacherApprovalController`

Pending-teacher list + approve/reject/update — see [modules/teacher-approval.md](../modules/teacher-approval.md). Guards: `abort_unless(role==='teacher')`, status whitelist `{approved,rejected}`.

### `SubjectController`

Subject CRUD + grade assignment — see [modules/subject-management.md](../modules/subject-management.md). `updateGrades()` replaces all assignments inside `DB::transaction`; grades validated against distinct `students.grade` values.

### `ExamController`

Exam CRUD + target preview — see [modules/examinations.md](../modules/examinations.md). `validateExam()` shared by store/update enforces subject↔grade (`grade_subject`) and grade↔section (`students`) existence via `Rule::exists`.

### `ExamGradeController`

Grade entry sheet, bulk upsert, single edit/update — see [modules/examinations.md](../modules/examinations.md). `store()` verifies class membership per student then `upsert`s on `(exam_id, student_id)`; `ensureGradeBelongsToExam()` guards edit/update with 404s.

> ⚠️ `ensureGradeBelongsToExam()` compares `$student->grade === $exam->grade` — **int vs string strict comparison that always fails** on MySQL (tinyint → int, varchar → string). Single-grade edit/update likely always 404s. See [project-status.md](../project-status.md).

### Statistics controllers

`SubjectStatisticsController`, `ClassStatisticsController`, `GradeStatisticsController`, `SchoolStatisticsController` — see [modules/statistics.md](../modules/statistics.md). All use `DB::table()` joins + `selectRaw` aggregates, cached 300 s.

## Auth controllers (`app/Http/Controllers/Auth/`)

Standard Breeze set: `AuthenticatedSessionController` (login/logout), `RegisteredUserController` (customized: forces `teacher`/`pending`, no auto-login), `PasswordResetLinkController`, `NewPasswordController`, `ConfirmablePasswordController`, `EmailVerificationPromptController`, `VerifyEmailController`, `EmailVerificationNotificationController`, `PasswordController`.

## Cross-cutting patterns

- **Validation:** inline `$request->validate()` everywhere except `LoginRequest` and `ProfileUpdateRequest`.
- **Redirects:** `redirect()->route(...)` or `back()` with `->with('success', ...)` flash messages.
- **Pagination:** `paginate(10|15|20|50)` + `withQueryString()`.
- **Transactions:** only `AttendanceController@store` and `SubjectController@updateGrades`.
- **Upsert:** only `ExamGradeController@store`.
- **Caching:** `DashboardController` (60 s) and the four statistics controllers (300 s); no invalidation.
- **No authorization inside controllers** beyond `TeacherApprovalController`'s role guards and `ExamGradeController`'s ownership checks — everything else relies on route middleware.
