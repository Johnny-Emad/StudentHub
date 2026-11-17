# Troubleshooting

Common failures when installing or running StudentHub, with the actual cause and fix. Entries marked **[verified]** were reproduced against the current codebase.

---

## Installation failures

### `php artisan migrate` fails: `duplicate column name: notes` — [verified]

```
SQLSTATE[HY000]: General error: 1 duplicate column name: notes
(SQL: alter table "students" add column "notes" text)
```

**Cause:** `students.notes` is defined twice — once in `2026_11_03_182612_create_students_table` and again in `2026_11_16_223616_add_notes_to_students_table`. Any fresh database hits this.

**Fix (until the migration is repaired upstream):**

- On MySQL, you can skip the offending migration by marking it as run after creating the column manually — or simpler, temporarily comment out the `$table->text('notes')` line inside `2026_11_16_223616_add_notes_to_students_table.php`'s `up()` method, run `php artisan migrate`, then restore the file. Because `down()` drops the column, restoring the file afterwards keeps rollback semantics intact for databases that ran the original version.
- On an **existing** database where `notes` was added by the later migration, nothing needs to be done — the failure only affects fresh installs.

This bug also breaks the entire automated test suite, which migrates a fresh in-memory SQLite database for every test run.

### `SQLSTATE[HY000] [2002] Connection refused`

MySQL is not running, or `DB_HOST`/`DB_PORT` in `.env` don't match. Start the MySQL service (Windows service, XAMPP, or Docker) and verify with `mysql -u root -p`.

### `SQLSTATE[HY000] [1045] Access denied`

Wrong `DB_USERNAME`/`DB_PASSWORD`. Update `.env`, then `php artisan config:clear` — Laravel caches config.

### `SQLSTATE[HY000] [1049] Unknown database 'studenthub'`

The schema doesn't exist. Run the `CREATE DATABASE` statement from [installation.md](installation.md) before migrating.

### `Class 'Pdo\Mysql' not found` / driver errors

PHP's MySQL extensions are missing. Enable `pdo_mysql` and `mysqlnd` in `php.ini` and restart the web server/PHP process.

### Blank or unstyled pages / `Vite manifest not found`

Frontend assets were never built. Run:

```powershell
npm install
npm run build
```

For development with hot reload, use `npm run dev` in a second terminal.

### `.env` changes have no effect

Config is cached. Run `php artisan config:clear` (and `php artisan cache:clear` if statistics look stale — see [configuration.md](configuration.md) for cache keys and TTLs).

---

## Runtime issues

### Login fails with "Your account is waiting for admin approval"

Expected behavior, not a bug. `LoginRequest::authenticate()` rejects **any** account whose `users.status` is not `approved` — including admins. New registrations are always created as `role=teacher, status=pending`. Approve the teacher at `/admin/teachers` (as an admin), or update the row directly:

```sql
UPDATE users SET status = 'approved' WHERE email = 'teacher@example.com';
```

### Login fails with "Your account has not been approved"

The account's `status` is `rejected` (or any non-`approved` value other than `pending`). Rejected teachers cannot log in again unless an admin changes their status directly in the database — the approval UI only lists `pending` teachers, so there is no self-service way to reverse a rejection.

### Admin pages return 403

`AdminMiddleware` requires **both** `role = 'admin'` **and** `status = 'approved'`. A `pending` or `rejected` admin, or any non-admin role, gets `403 Unauthorized access.`

### Statistics appear stale after data entry

Statistics and dashboard queries are cached (60–300 s, see [configuration.md](configuration.md)). There is no automatic invalidation. Run `php artisan cache:clear`.

### `php artisan test` fails for every test

**[verified]** The whole suite currently fails during `RefreshDatabase` migration with the duplicate `notes` column error described above — before any test logic runs. This is the same root cause as the fresh-install migration failure.

### 500 errors / permission problems

Ensure the web server user can write to `storage/` and `bootstrap/cache/`. On Windows, check NTFS permissions on those folders.

---

## Data issues

### Attendance won't save / "Invalid student selection"

`AttendanceController::store()` verifies every submitted student ID exists. This happens if the attendance form was open while students were deleted, or if IDs were tampered with.

### Exam grade entry rejects a student

`ExamGradeController::store()` only accepts students whose `grade` **and** `class_section` exactly match the exam's. Students who transferred sections after the exam was created will be rejected.

### Exam creation rejects a grade or section

`ExamController` validates that the chosen `grade` exists in `grade_subject` **for that subject**, and that `class_section` exists among students of that grade. Assign the subject to the grade first (Subjects → Grades), and ensure at least one student exists in that grade/section.
