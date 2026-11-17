# Testing

## Framework & configuration

- **PestPHP 4** (`pestphp/pest` + `pest-plugin-laravel`) on top of PHPUnit.
- `tests/Pest.php` binds `Tests\TestCase` + `RefreshDatabase` to everything in `tests/Feature`.
- `phpunit.xml` forces: `APP_ENV=testing`, `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `CACHE_STORE=array`, `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`, `BCRYPT_ROUNDS=4`.

## Running tests

```powershell
php artisan test          # or: composer test
php artisan test --filter=ProfileTest
```

## Current state — verified failing

> ⚠️ **`php artisan test` currently fails for the entire suite** (verified by running it). Every feature test dies inside `RefreshDatabase` while migrating the fresh in-memory SQLite database:

```
SQLSTATE[HY000]: General error: 1 duplicate column name: notes
(SQL: alter table "students" add column "notes" text)
```

Root cause: `students.notes` is defined in both `2026_11_03_182612_create_students_table` and `2026_11_16_223616_add_notes_to_students_table`. This is the same bug that breaks fresh `php artisan migrate` installs — see [project-status.md](../project-status.md).

Until that migration is fixed, **no test result can be trusted** — the suite cannot reach any test logic.

## Test files present

| File | Tests | Coverage area |
|---|---|---|
| `tests/Feature/ExampleTest.php` | 1 | `GET /` returns 200 |
| `tests/Feature/Auth/AuthenticationTest.php` | 4 | login screen, authenticate, invalid password, logout |
| `tests/Feature/Auth/RegistrationTest.php` | 2 | register screen, registration |
| `tests/Feature/Auth/PasswordResetTest.php` | ~4 | forgot/reset password flow |
| `tests/Feature/Auth/PasswordConfirmationTest.php` | ~2 | confirm-password gate |
| `tests/Feature/Auth/PasswordUpdateTest.php` | ~2 | `PUT /password` |
| `tests/Feature/Auth/EmailVerificationTest.php` | ~4 | verification notice/link/resend |
| `tests/Feature/ProfileTest.php` | 5 | profile display/update/email-unchanged/delete/wrong-password |
| `tests/Unit/ExampleTest.php` | 1 | trivial unit test |

All are stock **Laravel Breeze** tests — none were written for StudentHub's actual features.

## Expected failures once migrations are fixed

Even after the `notes` bug is repaired, several Breeze tests contradict the customized implementation:

- `RegistrationTest::new users can register` asserts `assertAuthenticated()` + redirect to `dashboard` — but `RegisteredUserController` redirects to `login` **without authenticating** (approval workflow). **Will fail.**
- `AuthenticationTest::users can authenticate` uses `User::factory()->create()` which produces `status='pending'` (DB default; `UserFactory` doesn't set role/status) — `LoginRequest` rejects non-approved users. **Will fail.**
- `EmailVerificationTest` assumes `MustVerifyEmail` behavior — `User` doesn't implement the interface. Expect failures.
- `ProfileTest` uses `actingAs()` which bypasses the login approval gate, so those tests should pass once migrations run.

## Factories available

`UserFactory` (states: `unverified`, `teacher`), `StudentFactory` (states: `active`, `inactive`, `inGrade`, `inSection`), `AttendanceFactory`, `ExamFactory`, `ExamGradeFactory`, `GradeSubjectFactory`, `SubjectFactory` — sufficient to build feature tests for every module, but **none exist yet** for students, attendance, subjects, exams, grades, statistics, or teacher approval.

See [test-coverage.md](test-coverage.md) for the coverage map.
