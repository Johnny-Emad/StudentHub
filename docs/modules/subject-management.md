# Module: Subject Management

## Overview

Subject management is an **admin-only** module for maintaining the subject catalogue and controlling which grade levels each subject applies to. Subjects are a prerequisite for creating exams: an exam can only target a grade that has been assigned to its subject via the `grade_subject` table.

All routes live under the `/admin` prefix and require both `auth` and `admin` middleware.

---

## Routes

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/subjects` | `admin.subjects.index` | List all subjects |
| GET | `/admin/subjects/create` | `admin.subjects.create` | Create form |
| POST | `/admin/subjects` | `admin.subjects.store` | Store subject |
| GET | `/admin/subjects/{subject}/edit` | `admin.subjects.edit` | Edit form |
| PUT/PATCH | `/admin/subjects/{subject}` | `admin.subjects.update` | Update subject |
| DELETE | `/admin/subjects/{subject}` | `admin.subjects.destroy` | Delete subject |
| GET | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.edit` | Grade-assignment editor |
| PUT | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.update` | Save grade assignments |

---

## Controller: `SubjectController`

`app/Http/Controllers/SubjectController.php`

### `index()`

Lists all subjects ordered by name.

### `store(Request $request)` / `update(Request $request, Subject $subject)`

Validation rules:

| Field | Rules |
|---|---|
| `name` | required, string, max:255, unique:subjects (ignores current record on update) |
| `description` | nullable, string |

### `destroy(Subject $subject)`

Hard delete. Because `grade_subject.subject_id` and `exams.subject_id` use `cascadeOnDelete()`, deleting a subject also removes all its grade assignments and **all exams (and their grades) for that subject**.

### `editGrades(Subject $subject)`

Displays the grade-assignment editor. The set of selectable grades is derived from **distinct `grade` values that actually exist in the `students` table** — not a hardcoded 1–12 list.

### `updateGrades(Request $request, Subject $subject)`

Validation:

| Field | Rules |
|---|---|
| `grades` | required, array |
| `grades.*` | integer, `Rule::in($availableGrades)` where `$availableGrades` is the distinct set of grades present in `students` |

The method replaces the subject's `grade_subject` rows with the submitted set.

---

## Models

- `Subject` (`app/Models/Subject.php`) — fillable: `name`, `description`. Relations: `gradeAssignments()` hasMany `GradeSubject`, `exams()` hasMany `Exam`.
- `GradeSubject` (`app/Models/GradeSubject.php`) — table explicitly set to `grade_subject`; fillable: `subject_id`, `grade`; `subject()` belongsTo `Subject`.

---

## Views

| View | Purpose |
|---|---|
| `subjects/index.blade.php` | Subject list |
| `subjects/create.blade.php` | Create form |
| `subjects/edit.blade.php` | Edit form |
| `subjects/grades.blade.php` | Grade-assignment checkboxes per subject |
| `subjects/statistics.blade.php` | Per-subject statistics (see [statistics.md](statistics.md)) |

---

## Database Tables

- `subjects` — `name` (unique), `description` (nullable text).
- `grade_subject` — `subject_id` FK (cascade), `grade` string; unique `(subject_id, grade)`.

See [database/schema.md](../database/schema.md#subjects) and [database/schema.md](../database/schema.md#grade_subject).

---

## Cross-Module Interactions

- **Exams:** `ExamController::validateExam()` requires the chosen `grade` to exist in `grade_subject` for the chosen `subject_id`. Removing a grade assignment does not delete existing exams, but those grade+subject combinations can no longer be used for new exams.
- **Statistics:** `SubjectStatisticsController` aggregates exam and grade data per subject.

---

## Current Limitations

- No subject ordering, color coding, or credit-hour fields.
- Grade assignments are limited to grades that currently have at least one student; a grade with zero students cannot be pre-assigned.
- Deleting a subject silently deletes all of its exams and exam grades.