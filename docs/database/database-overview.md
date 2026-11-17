# Database Overview

StudentHub uses **MySQL 8.0+** as its database — it is the officially supported and configured engine (`DB_CONNECTION=mysql` in `.env.example`). The schema is managed entirely through Laravel migrations. No stored procedures, triggers, or views are used.

> **Note on SQLite:** SQLite is configured only for the automated test suite (`phpunit.xml` uses an in-memory SQLite database). It is not a supported development or production database for this application.

---

## Tables Summary

### Application tables

| Table | Purpose |
|---|---|
| `users` | Application user accounts (teachers and admins) |
| `students` | Student records (the core entity of the system) |
| `attendances` | Daily presence or absence record per student |
| `subjects` | Subject catalogue (e.g., Mathematics, Science) |
| `grade_subject` | Pivot — which grade levels a subject applies to |
| `exams` | Exam definitions scoped to a subject, grade, and class section |
| `exam_grades` | Individual student mark per exam |

### Laravel system tables

Created by the framework migrations (`0001_01_01_*`, `0001_01_01_000001`, `0001_01_01_000002`) and not used by application business logic:

| Table | Purpose |
|---|---|
| `migrations` | Tracks which migrations have run |
| `password_reset_tokens` | Laravel's built-in password reset token store |
| `sessions` | Database session storage (`SESSION_DRIVER=database`) |
| `cache`, `cache_locks` | Database cache store (`CACHE_STORE=database`) |
| `jobs`, `job_batches`, `failed_jobs` | Database queue tables (`QUEUE_CONNECTION=database`) |

---

## Relationships

### Core Relationships

```
User (1) ─────── (N) Attendance
                       via marked_by

Student (1) ──── (N) Attendance
Student (1) ──── (N) ExamGrade

Subject (1) ──── (N) GradeSubject
Subject (1) ──── (N) Exam

Exam (1) ──────── (N) ExamGrade
```

### Key Design Decisions

**Mixed grade types** — `students.grade` is an `unsignedTinyInteger` (validated as an integer between 1 and 12), while `exams.grade` and `grade_subject.grade` are `string` columns that store the grade number as text (e.g., `"5"`). MySQL coerces the types during comparisons, and the application validates grade values against real student data rather than a foreign key. There is no dedicated grades/year-groups table.

**Class section as a single character** — `students.class_section` is a `VARCHAR(1)` validated as a single uppercase letter (`size:1`, `regex:/^[A-Z]$/`). `exams.class_section` is `VARCHAR(10)`, but exam validation requires the value to exist in `students.class_section` for the chosen grade.

**No teacher–student assignment** — there is no direct relationship between a `User` (teacher) and specific students or classes. Any approved user can mark attendance for any student.

**Soft deletes are not used** — all deletes are hard deletes. The `cascadeOnDelete()` constraints ensure that deleting a student removes their attendance and exam grade records automatically.

---

## Cascade Delete Chains

| Parent deleted | Cascaded children |
|---|---|
| `students` row | All `attendances` rows with that `student_id`; all `exam_grades` rows with that `student_id` |
| `subjects` row | All `grade_subject` rows with that `subject_id`; all `exams` rows with that `subject_id` |
| `exams` row | All `exam_grades` rows with that `exam_id` |
| `users` row | `attendances.marked_by` is set to `NULL` (nullOnDelete) |

---

## Unique Constraints

| Table | Unique columns |
|---|---|
| `users` | `email` |
| `students` | `student_code` only — `email` is **nullable with no database unique constraint**; uniqueness is enforced at the application layer by `StudentController` validation |
| `subjects` | `name` |
| `attendances` | `(student_id, date)` — prevents duplicate attendance records for the same student on the same day |
| `exam_grades` | `(exam_id, student_id)` — prevents recording two marks for the same student in the same exam |
| `grade_subject` | `(subject_id, grade)` — prevents assigning the same subject to the same grade twice |

---

## Indexes

| Table | Index columns | Source migration | Purpose |
|---|---|---|---|
| `exams` | `(grade, class_section)` | `create_exams_table` | Fast lookup of exams by class |
| `exams` | `exam_date` | `create_exams_table` | Fast date-range filtering |
| `students` | `grade` | `add_performance_indexes` | Filtering and statistics |
| `students` | `class_section` | `add_performance_indexes` | Filtering and statistics |
| `students` | `status` | `add_performance_indexes` | Status filtering |
| `students` | `(grade, class_section)` | `add_performance_indexes` | Composite class lookup |
| `attendances` | `date` | `add_performance_indexes` | Date-range filtering |
| `attendances` | `status` | `add_performance_indexes` | Status aggregation |
| `sessions` | `user_id` | `create_users_table` | Laravel session lookup |
| `sessions` | `last_activity` | `create_users_table` | Session garbage collection |
