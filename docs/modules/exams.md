# Module: Exams

## Overview

Exam management is an **admin-only** module for defining examinations scoped to a subject, grade level, and class section. Exams are the container for grade entry — student marks are recorded per exam in `exam_grades` (see [exam-grades.md](exam-grades.md)).

All routes live under the `/admin` prefix and require both `auth` and `admin` middleware.

---

## Routes

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/exams` | `admin.exams.index` | List exams (paginated, latest exam date first) |
| GET | `/admin/exams/create` | `admin.exams.create` | Create form |
| POST | `/admin/exams` | `admin.exams.store` | Store exam |
| GET | `/admin/exams/{exam}/edit` | `admin.exams.edit` | Edit form |
| PUT/PATCH | `/admin/exams/{exam}` | `admin.exams.update` | Update exam |
| DELETE | `/admin/exams/{exam}` | `admin.exams.destroy` | Delete exam |
| GET | `/admin/exams/{exam}/students` | `admin.exams.students` | Preview target students |

---

## Controller: `ExamController`

`app/Http/Controllers/ExamController.php`

### `index()`

Lists exams with the `subject` relation eager-loaded, ordered by `exam_date` descending, paginated at 15.

### `create()` / `edit(Exam $exam)`

Both load all subjects with their `gradeAssignments` so the form can offer only valid grade choices per subject.

### `store()` / `update()`

Both delegate to the private `validateExam()` method:

| Field | Rules |
|---|---|
| `title` | required, string, max:255 |
| `subject_id` | required, integer, exists:subjects,id |
| `grade` | required, string, `Rule::exists('grade_subject','grade')` constrained to the submitted `subject_id` |
| `class_section` | required, string, max:10, `Rule::exists('students','class_section')` constrained to the submitted `grade` |
| `exam_date` | required, date |
| `total_marks` | required, numeric, gt:0, max:999999.99 |

This is **application-enforced referential integrity**: there is no foreign key between `exams.grade`/`class_section` and `students`, so the `Rule::exists` checks guarantee the exam targets a real class and a subject actually assigned to that grade.

### `destroy(Exam $exam)`

Hard delete; `exam_grades.exam_id` uses `cascadeOnDelete()`, so all grade records for the exam are removed too.

### `targetStudents(Exam $exam)`

Returns the paginated (20/page) list of students matching the exam's `grade` + `class_section`, plus the total target count. Rendered by `exams/students.blade.php`.

---

## Models

- `Exam` (`app/Models/Exam.php`) — fillable: `subject_id`, `title`, `grade`, `class_section`, `exam_date`, `total_marks`. Relations: `subject()` belongsTo `Subject`, `grades()` hasMany `ExamGrade`.

---

## Views

| View | Purpose |
|---|---|
| `exams/index.blade.php` | Exam list |
| `exams/create.blade.php` / `exams/edit.blade.php` | Forms (share `exams/form.blade.php`) |
| `exams/students.blade.php` | Target-student preview |
| `exams/grades.blade.php` | Bulk grade entry (see [exam-grades.md](exam-grades.md)) |
| `exams/grade-edit.blade.php` | Single grade edit |

---

## Database Table: `exams`

- `subject_id` FK → `subjects.id` (cascade).
- `grade` and `class_section` are plain strings — no FK to `students`.
- `total_marks` DECIMAL, default 100.
- Indexes: `(grade, class_section)`, `exam_date`.
- Note: migration `2026_11_12_164107_fix_missing_columns_in_exams_table.php` re-adds the core columns guarded by `Schema::hasColumn()` for databases created before they existed.

See [database/schema.md](../database/schema.md#exams).

---

## Cross-Module Interactions

- **Subjects:** exam creation requires a `grade_subject` assignment; deleting a subject cascades to its exams.
- **Students:** `class_section` must exist among students of the chosen grade at creation time.
- **Exam Grades:** all grade entry flows through this exam's `grade`/`class_section` targeting.
- **Statistics:** all four statistics controllers aggregate exam data.

---

## Current Limitations

- No exam types/categories, no weighting, no scheduling conflicts.
- `class_section` allows up to 10 characters while `students.class_section` is a single letter — the `Rule::exists` check keeps them consistent in practice.
- Editing an exam's grade/section does not migrate or validate already-entered `exam_grades` rows against the new target.