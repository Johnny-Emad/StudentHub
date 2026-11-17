# Database Schema

Column-level reference reconstructed from the migration files. Types are the Laravel schema-builder types; the MySQL equivalents are noted where they differ.

## `users`

Authentication accounts for admins and teachers. Created by `0001_01_01_000000_create_users_table`.

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK, auto-increment) | no | — | primary key |
| `name` | varchar(255) | no | — | |
| `email` | varchar(255) | no | — | **unique** |
| `email_verified_at` | timestamp | yes | null | |
| `password` | varchar(255) | no | — | hashed (cast) |
| `role` | varchar(255) | no | `'user'` | values used: `admin`, `teacher`, `user` |
| `status` | varchar(255) | no | `'pending'` | values used: `pending`, `approved`, `rejected` |
| `remember_token` | varchar(100) | yes | null | |
| `created_at` / `updated_at` | timestamp | yes | null | |

**Notes:**
- `role` and `status` are plain strings — there is no enum or check constraint; validity is enforced only in application code.
- Registration always writes `role='teacher'`, `status='pending'` (`RegisteredUserController`).
- `UserFactory` does **not** set `role`/`status`, so factory users get DB defaults `user`/`pending` — relevant to the stale test suite (see [testing/test-coverage.md](../testing/test-coverage.md)).

## `students`

The student registry. Created by `2026_11_03_182612_create_students_table` (+ duplicate `notes` migration).

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK) | no | — | |
| `student_code` | varchar(255) | no | — | **unique** |
| `first_name` | varchar(255) | no | — | |
| `last_name` | varchar(255) | no | — | |
| `email` | varchar(255) | yes | null | **unique** (MySQL allows multiple NULLs) |
| `phone` | varchar(255) | yes | null | validation: exactly 11 digits |
| `date_of_birth` | date | yes | null | validation: `before_or_equal:today` |
| `gender` | varchar(255) | yes | null | validation: `male`/`female` |
| `address` | text | yes | null | validation: max 1000 chars |
| `grade` | **unsignedTinyInteger** | no | — | validation: integer 1–12 |
| `class_section` | **char(1)** | no | — | validation: single uppercase A–Z |
| `status` | varchar(255) | no | `'active'` | validation: `active`/`inactive` |
| `notes` | text | yes | null | defined in **both** create and add migrations |
| `created_at` / `updated_at` | timestamp | yes | null | |

**Indexes** (from `2026_11_17_000000_add_performance_indexes`): `grade`, `class_section`, `status`, and composite `(grade, class_section)`.

## `attendances`

One record per student per day. Created by `2026_11_09_132450_create_attendances_table`.

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK) | no | — | |
| `student_id` | bigint unsigned (FK) | no | — | → `students.id`, **cascadeOnDelete** |
| `date` | date | no | — | |
| `status` | **enum('present','absent')** | no | — | MySQL enum; no default |
| `marked_by` | bigint unsigned (FK) | yes | null | → `users.id`, **nullOnDelete** |
| `created_at` / `updated_at` | timestamp | yes | null | |

**Constraints:** `unique(student_id, date)` — one attendance record per student per day.
**Indexes:** `date`, `status` (performance migration).

## `subjects`

Subject catalogue. Created by `2026_11_09_193355_create_subjects_table`.

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK) | no | — | |
| `name` | varchar(255) | no | — | **unique** |
| `description` | text | yes | null | |
| `created_at` / `updated_at` | timestamp | yes | null | |

## `grade_subject`

Pivot mapping subjects to grade levels. Table name is singular `grade_subject` (set explicitly via `GradeSubject::$table`). Created by `2026_11_09_193416_create_grade_subjects_table`.

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK) | no | — | |
| `subject_id` | bigint unsigned (FK) | no | — | → `subjects.id`, **cascadeOnDelete** |
| `grade` | **varchar(255)** | no | — | stores grade as string (e.g. `'1'`–`'12'`) |
| `created_at` / `updated_at` | timestamp | yes | null | |

**Constraints:** `unique(subject_id, grade)`.

## `exams`

Exam definitions. Created by `2026_11_09_195725_create_exams_table`; the `2026_11_12` migration adds defensive `hasColumn` guards (no-ops on clean installs).

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK) | no | — | |
| `subject_id` | bigint unsigned (FK) | no* | — | → `subjects.id`, **cascadeOnDelete** (*nullable only in the defensive migration path) |
| `title` | varchar(255) | no* | — | |
| `grade` | varchar(255) | no* | — | stored as **string** |
| `class_section` | varchar(10) | no* | — | wider than `students.class_section` char(1) |
| `exam_date` | date | no* | — | |
| `total_marks` | decimal(8,2) | no | `100` | validation: numeric, >0, ≤999999.99 |
| `created_at` / `updated_at` | timestamp | yes | null | |

**Indexes:** `(grade, class_section)` composite, `exam_date`.

## `exam_grades`

Marks per student per exam. Created by `2026_11_09_214149_create_exam_grades_table`.

| Column | Type | Nullable | Default | Constraints |
|---|---|---|---|---|
| `id` | bigint unsigned (PK) | no | — | |
| `exam_id` | bigint unsigned (FK) | no | — | → `exams.id`, **cascadeOnDelete** |
| `student_id` | bigint unsigned (FK) | no | — | → `students.id`, **cascadeOnDelete** |
| `marks_obtained` | decimal(8,2) | no | — | validation: 0 ≤ marks ≤ `exams.total_marks` |
| `created_at` / `updated_at` | timestamp | yes | null | |

**Constraints:** `unique(exam_id, student_id)` — one grade per student per exam; `ExamGradeController` uses `upsert` on this pair.

## Framework tables

`password_reset_tokens` (email PK, token, created_at), `sessions` (id PK, user_id index, ip, user_agent, payload, last_activity), `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations` — all standard Laravel, created by the `0001_01_01_*` migrations.
