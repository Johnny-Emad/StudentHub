# Module: Exam Grades

## Overview

Exam grade entry is an **admin-only** module. Grades are recorded per exam against the students of that exam's grade + class section. Entry is bulk-oriented: a single sheet lists all target students and their marks are upserted in one request. Individual grades can also be edited afterwards.

All routes live under the `/admin` prefix and require both `auth` and `admin` middleware.

---

## Routes

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/exams/{exam}/grades` | `admin.exams.grades.create` | Bulk grade entry sheet |
| POST | `/admin/exams/{exam}/grades` | `admin.exams.grades.store` | Bulk upsert grades |
| GET | `/admin/exams/{exam}/grades/{examGrade}/edit` | `admin.exams.grades.edit` | Edit single grade |
| PUT | `/admin/exams/{exam}/grades/{examGrade}` | `admin.exams.grades.update` | Update single grade |

---

## Controller: `ExamGradeController`

`app/Http/Controllers/ExamGradeController.php`

### `create(Request $request, Exam $exam)`

Builds the bulk-entry view:

- Selects students matching the exam's `grade` + `class_section`, eager-loading their `examGrades` for this exam, paginated at 20.
- Computes `targetCount`, `gradedCount`, `notGradedCount`.
- Supports a `status` filter: `all`, `graded`, `not_graded`.
- For each displayed student computes `subject_average`: the weighted percentage `SUM(marks_obtained)/SUM(total_marks)*100` across **all exams of the same subject, grade, and section** (raw DB join query).

### `store(Request $request, Exam $exam)`

Validation:

| Field | Rules |
|---|---|
| `grades` | required, array (keyed by `student_id`) |
| `grades.*` | nullable, numeric, min:0, max:{exam.total_marks} |

The controller then verifies every submitted `student_id` belongs to the exam's grade + section (rejecting the whole request otherwise), filters out null/empty marks, and performs a single `ExamGrade::upsert()` on the `(exam_id, student_id)` unique key, updating `marks_obtained` and `updated_at`. Blank fields leave existing grades untouched.

### `edit(Exam $exam, ExamGrade $examGrade)` / `update(...)`

Both call `ensureGradeBelongsToExam()`, which aborts 404 unless the grade record belongs to the given exam **and** the student still matches the exam's grade + section. `update` validates `marks_obtained` as required, numeric, 0..total_marks.

---

## Model: `ExamGrade`

`app/Models/ExamGrade.php`

- `exam()` — belongsTo `Exam`.
- `student()` — belongsTo `Student`.
- Fillable: `exam_id`, `student_id`, `marks_obtained`.

---

## Views

| View | Purpose |
|---|---|
| `exams/grades.blade.php` | Bulk entry sheet with per-student subject averages |
| `exams/grade-edit.blade.php` | Single grade edit form |

---

## Database Table: `exam_grades`

- `marks_obtained` DECIMAL(8,2).
- Unique `(exam_id, student_id)` — enforced both by the upsert key and the DB constraint.
- FKs to `exams` and `students` both `cascadeOnDelete()`.

See [database/schema.md](../database/schema.md#exam_grades).

---

## Cross-Module Interactions

- **Exams:** grade entry is scoped entirely by the exam's subject/grade/section; deleting an exam cascades its grades.
- **Students:** only students in the exam's class can be graded; deleting a student cascades their grades.
- **Statistics:** `ExamGrade` rows feed every statistics controller's averages.

---

## Current Limitations

- No letter grades, GPA, or pass/fail thresholds — raw marks only.
- No grade history/audit trail; updates overwrite `marks_obtained`.
- No partial-save or draft state for the bulk sheet.
- `subject_average` is computed per page of 20 students via a separate aggregate query.