# Database Overview

StudentHub uses SQLite as its default database driver. The schema is managed entirely through Laravel migrations. No stored procedures, triggers, or views are used.

---

## Tables Summary

| Table | Rows / Purpose |
|---|---|
| `users` | Application user accounts (teachers and admins) |
| `students` | Student records (the core entity of the system) |
| `attendances` | Daily presence or absence record per student |
| `subjects` | Subject catalogue (e.g., Mathematics, Science) |
| `grade_subject` | Pivot — which grade levels a subject applies to |
| `exams` | Exam definitions scoped to a subject, grade, and class section |
| `exam_grades` | Individual student mark per exam |
| `password_reset_tokens` | Laravel's built-in password reset token store |
| `sessions` | Laravel database session storage |
| `cache` | Laravel cache store |
| `jobs` | Laravel job queue |

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

**Grade as a string** — `grade` is stored as a plain `string` column in `students`, `exams`, `grade_subject`, and `attendances` (via student). This avoids a separate grades/year-groups table but means grade values are never enforced by a foreign key; they are instead validated against existing student data at the application layer.

**Class section as a single character** — `class_section` is validated as `size:1` in `StudentController` (single uppercase letter such as "A", "B"). The `exams` table uses `max:10` allowing slightly longer values, but the cross-validation ensures that `class_section` values used in exams actually exist in the students table for that grade.

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
| `students` | `student_code`, `email` (nullable unique) |
| `subjects` | `name` |
| `attendances` | `(student_id, date)` — prevents duplicate attendance records for the same student on the same day |
| `exam_grades` | `(exam_id, student_id)` — prevents recording two marks for the same student in the same exam |
| `grade_subject` | `(subject_id, grade)` — prevents assigning the same subject to the same grade twice |

---

## Indexes

| Table | Index columns | Purpose |
|---|---|---|
| `exams` | `(grade, class_section)` | Fast lookup of exams by class |
| `exams` | `exam_date` | Fast date-range filtering |
| `sessions` | `user_id` | Laravel session lookup |
| `sessions` | `last_activity` | Session garbage collection |
