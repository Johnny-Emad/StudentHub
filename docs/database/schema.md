# Database Schema Reference

All column definitions are sourced directly from the migration files. Data types reflect SQLite equivalents for the declared Eloquent/Blueprint types.

---

## `users`

Migration: `0001_01_01_000000_create_users_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `name` | VARCHAR(255) | No | — | Full display name |
| `email` | VARCHAR(255) | No | — | Unique |
| `email_verified_at` | TIMESTAMP | Yes | NULL | Email verification timestamp |
| `password` | VARCHAR(255) | No | — | Bcrypt hashed |
| `role` | VARCHAR(255) | No | `'user'` | Actual values: `'teacher'`, `'admin'` |
| `status` | VARCHAR(255) | No | `'pending'` | Values: `'pending'`, `'approved'`, `'rejected'` |
| `remember_token` | VARCHAR(100) | Yes | NULL | Laravel remember-me token |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

**Notes:**
- The default `role` value in the migration is `'user'`, but `RegisteredUserController` always creates accounts with `role = 'teacher'`. Admin accounts are seeded with `role = 'admin'`.
- The `email_verified_at` column exists but `MustVerifyEmail` is commented out in the User model, so email verification is not enforced.

---

## `students`

Migration: `2026_11_03_182612_create_students_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `student_code` | VARCHAR(255) | No | — | Unique; manually assigned |
| `first_name` | VARCHAR(255) | No | — | |
| `last_name` | VARCHAR(255) | No | — | |
| `email` | VARCHAR(255) | Yes | NULL | Nullable unique |
| `phone` | VARCHAR(255) | Yes | NULL | Validated as 11 digits |
| `date_of_birth` | DATE | Yes | NULL | Must be ≤ today |
| `gender` | VARCHAR(255) | Yes | NULL | Values: `'male'`, `'female'` |
| `address` | TEXT | Yes | NULL | Max 1000 characters |
| `grade` | VARCHAR(255) | No | — | String grade level (e.g., "5", "Grade 10") |
| `class_section` | VARCHAR(10) | No | — | Validated as single character (e.g., "A", "B") |
| `status` | VARCHAR(255) | No | `'active'` | Values: `'active'`, `'inactive'` |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

---

## `attendances`

Migration: `2026_11_09_132450_create_attendances_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `student_id` | BIGINT UNSIGNED | No | — | FK → `students.id`, cascadeOnDelete |
| `date` | DATE | No | — | Attendance date |
| `status` | ENUM | No | — | Values: `'present'`, `'absent'` |
| `marked_by` | BIGINT UNSIGNED | Yes | NULL | FK → `users.id`, nullOnDelete |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

**Unique constraint:** `(student_id, date)`

---

## `subjects`

Migration: `2026_11_09_193355_create_subjects_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `name` | VARCHAR(255) | No | — | Unique |
| `description` | TEXT | Yes | NULL | |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

---

## `grade_subject`

Migration: `2026_11_09_193416_create_grade_subjects_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `subject_id` | BIGINT UNSIGNED | No | — | FK → `subjects.id`, cascadeOnDelete |
| `grade` | VARCHAR(255) | No | — | Grade string matching `students.grade` values |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

**Unique constraint:** `(subject_id, grade)`

**Note:** This table is not a standard many-to-many pivot. It is an independent model (`GradeSubject`) with its own `id` and timestamps.

---

## `exams`

Migrations: `2026_11_09_195725_create_exams_table.php` + `2026_11_12_164107_fix_missing_columns_in_exams_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `subject_id` | BIGINT UNSIGNED | No | — | FK → `subjects.id`, cascadeOnDelete |
| `title` | VARCHAR(255) | No | — | Exam title |
| `grade` | VARCHAR(255) | No | — | Targets this grade level |
| `class_section` | VARCHAR(10) | No | — | Targets this class section |
| `exam_date` | DATE | No | — | Scheduled exam date |
| `total_marks` | DECIMAL(8,2) | No | 100 | Maximum achievable marks |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

**Indexes:** `(grade, class_section)`, `exam_date`

**Note:** The fix migration (`2026_11_12_164107`) re-adds all columns with `if (!Schema::hasColumn(...))` guards. This was needed because earlier schema state may have been missing these columns in some environments.

---

## `exam_grades`

Migration: `2026_11_09_214149_create_exam_grades_table.php`

| Column | Type | Nullable | Default | Notes |
|---|---|:---:|---|---|
| `id` | BIGINT UNSIGNED | No | auto-increment | Primary key |
| `exam_id` | BIGINT UNSIGNED | No | — | FK → `exams.id`, cascadeOnDelete |
| `student_id` | BIGINT UNSIGNED | No | — | FK → `students.id`, cascadeOnDelete |
| `marks_obtained` | DECIMAL(8,2) | No | — | Must be between 0 and `exam.total_marks` |
| `created_at` | TIMESTAMP | Yes | NULL | |
| `updated_at` | TIMESTAMP | Yes | NULL | |

**Unique constraint:** `(exam_id, student_id)`

---

## Laravel System Tables

### `password_reset_tokens`

| Column | Type | Notes |
|---|---|---|
| `email` | VARCHAR(255) | Primary key |
| `token` | VARCHAR(255) | |
| `created_at` | TIMESTAMP | Nullable |

### `sessions`

| Column | Type | Notes |
|---|---|---|
| `id` | VARCHAR(255) | Primary key |
| `user_id` | BIGINT UNSIGNED | Nullable, indexed |
| `ip_address` | VARCHAR(45) | Nullable |
| `user_agent` | TEXT | Nullable |
| `payload` | LONGTEXT | |
| `last_activity` | INTEGER | Indexed |

### `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`

Standard Laravel cache and queue tables. Not used by application business logic.
