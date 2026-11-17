# Test Coverage

Coverage map of the PestPHP suite versus the application's actual surface. No coverage percentages are claimed — the suite cannot currently run (see [testing-overview.md](testing-overview.md)).

## Covered (nominal — Breeze defaults)

| Area | Tests | Status |
|---|---|---|
| Guest pages render | `ExampleTest`, `AuthenticationTest` (login screen), `RegistrationTest` (register screen) | should pass once migrations fixed |
| Login/logout mechanics | `AuthenticationTest` | **partially stale** — the "users can authenticate" test uses a `pending` factory user that `LoginRequest` rejects |
| Registration | `RegistrationTest` | **stale** — expects auto-login + dashboard redirect; actual behavior redirects to `login` un-authenticated |
| Password reset / confirmation / update | `Auth/*Test.php` | standard Breeze; likely pass |
| Email verification | `EmailVerificationTest` | **stale** — `MustVerifyEmail` not implemented on `User` |
| Profile CRUD | `ProfileTest` | should pass (`actingAs` bypasses approval gate) |

## Not covered at all

Every StudentHub-specific feature has **zero tests**:

| Module | Untested behaviors |
|---|---|
| Students | CRUD, unique `student_code`/`email`, grade 1–12, section regex, search, filters, pagination, cascade delete |
| Attendance | bulk `updateOrCreate`, `(student_id,date)` uniqueness, invalid-ID rejection, single update, history filters, rate calculation |
| Subjects | CRUD, unique name, grade-assignment replace-all, `Rule::in` grade validation |
| Exams | `Rule::exists` subject↔grade and grade↔section validation, `total_marks` bounds, target-student listing, cascade delete |
| Exam grades | membership validation, `upsert` on `(exam_id,student_id)`, marks 0..total, `ensureGradeBelongsToExam` 404s, subject-average computation |
| Teacher approval | pending-only listing, approve/reject, role guard, status whitelist |
| Statistics | all four dashboards, weighted-average formula, date-range filtering, cache keys |
| Authorization | `admin` middleware 403s for teachers/pending/rejected; approval gate at login; session persistence after rejection |
| Dashboard | cached stats, recent students |

## High-value tests to add (recommended priority)

1. **Migration sanity** — a test that simply runs `migrate:fresh` would have caught the `notes` bug immediately.
2. **Approval gate** — pending/rejected users cannot log in; approved teachers get 403 on `/admin/*`.
3. **Attendance idempotency** — POST same date twice → one row per student, status updated.
4. **Exam validation** — grade not in `grade_subject` rejected; section without students rejected.
5. **Exam grade bounds** — marks > `total_marks` rejected; blank fields skipped not zeroed.
6. **Statistics formula** — weighted `SUM(marks)/SUM(total)*100` on a fixture dataset.

## Factory readiness

All factories exist (`User`, `Student`, `Attendance`, `Subject`, `GradeSubject`, `Exam`, `ExamGrade`) with useful states (`active`/`inactive`/`inGrade`/`inSection`/`teacher`/`unverified`). `UserFactory` lacks `admin`/`pending`/`rejected` states — adding them would simplify authorization tests. `StudentFactory` generates `grade` 1–12 and sections A/B, compatible with exam/attendance fixtures.
