# Examinations & Exam Grades

Admin-only module for defining exams and recording student marks. Controllers: `ExamController`, `ExamGradeController`; views: `resources/views/exams/`; all routes under `/admin` (`auth`+`admin` middleware).

## Exam CRUD (`ExamController`)

| Method | URI | Name |
|---|---|---|
| GET | `/admin/exams` | `admin.exams.index` |
| GET | `/admin/exams/create` | `admin.exams.create` |
| POST | `/admin/exams` | `admin.exams.store` |
| GET | `/admin/exams/{exam}/edit` | `admin.exams.edit` |
| PUT/PATCH | `/admin/exams/{exam}` | `admin.exams.update` |
| DELETE | `/admin/exams/{exam}` | `admin.exams.destroy` |
| GET | `/admin/exams/{exam}/students` | `admin.exams.students` — target-student preview |

`show` is excluded. `index()` eager-loads `subject`, orders by `exam_date` desc, paginates 15. `create()`/`edit()` load subjects **with their `gradeAssignments`** so the form can constrain grade choices.

### Validation (`validateExam`, shared by store/update)

| Field | Rules |
|---|---|
| `title` | required, string, max 255 |
| `subject_id` | required, integer, `exists:subjects,id` |
| `grade` | required, string, **`Rule::exists('grade_subject','grade')->where('subject_id', input)`** — the grade must be assigned to *that* subject |
| `class_section` | required, string, max 10, **`Rule::exists('students','class_section')->where('grade', input)`** — the section must exist among students *of that grade* |
| `exam_date` | required, date |
| `total_marks` | required, numeric, `gt:0`, `max:999999.99` (DB default 100) |

These cross-field `Rule::exists` checks are the core business rule: **an exam can only target a (subject, grade) pair that exists in `grade_subject`, and a section that actually has students in that grade.**

### `targetStudents(Exam $exam)`

Lists students matching `exam.grade` + `exam.class_section` (ordered by name, paginated 20) plus `$targetCount` — a preview of who the exam applies to. Does **not** filter on `status`.

### `destroy()`

Deletes the exam; `exam_grades` rows cascade-delete. No confirmation of how many grades will be lost.

## Exam grades (`ExamGradeController`)

| Method | URI | Name |
|---|---|---|
| GET | `/admin/exams/{exam}/grades` | `admin.exams.grades.create` — grade-entry sheet |
| POST | `/admin/exams/{exam}/grades` | `admin.exams.grades.store` — bulk save |
| GET | `/admin/exams/{exam}/grades/{examGrade}/edit` | `admin.exams.grades.edit` |
| PUT | `/admin/exams/{exam}/grades/{examGrade}` | `admin.exams.grades.update` |

### `create()` — the grade-entry sheet

- Target students = `students` where `grade`/`class_section` match the exam.
- Counts: `targetCount`, `gradedCount` (exam_grades joined to students in the class), `notGradedCount`.
- `?status=` filter: `all` (default) / `graded` / `not_graded` — implemented via `whereHas`/`whereDoesntHave` on `examGrades` for this exam.
- Paginated 20, eager-loads each student's grade **for this exam only**.
- Computes a per-student **subject average**: joins `exam_grades→exams` filtered to same `subject_id`+`grade`+`class_section`, `SUM(marks_obtained)/SUM(total_marks)*100` — i.e., the student's weighted average across *all exams in that subject for that class*, not just this exam.

### `store()` — bulk upsert

Validation: `grades` required array; `grades.*` nullable, numeric, `min:0`, `max:{exam->total_marks}`.

Logic:

1. Collects submitted student IDs; verifies **each belongs to the exam's grade+section** (`Student::where(...)->whereIn('id', ...)`), else `ValidationException` "One or more selected students do not belong to this exam."
2. Filters out null/empty marks — **blank fields are skipped, not zeroed**.
3. `ExamGrade::upsert($records, ['exam_id','student_id'], ['marks_obtained','updated_at'])` — relies on the `unique(exam_id, student_id)` index; re-submitting updates existing grades.

### `edit()` / `update()` — single grade

`ensureGradeBelongsToExam()` guards both: aborts 404 unless `examGrade.exam_id === exam.id` **and** the student still matches the exam's grade+section. `update()` validates `marks_obtained` numeric 0..`total_marks`.

## Data model

- `exams.subject_id` → `subjects` (cascade), `exam_grades.exam_id`/`student_id` → cascade on both sides.
- `unique(exam_id, student_id)` on `exam_grades` — one mark per student per exam.
- `exams.grade`/`class_section` are strings matched against `students` values by coercion.

## Known gaps

- **No retake/versioning** — one grade per student per exam; corrections overwrite.
- **No grade-letter/GPA mapping** — raw marks and percentages only.
- **No exam scheduling conflicts** — two exams can share subject+date+section.
- **No status filter on students** — inactive students appear in targeting and grade entry.
- **Orphaned exams possible** — if a subject's grade assignment is removed or students move sections after exam creation, the exam keeps its stale target values (validation only runs at write time).
- Deleting an exam or subject silently destroys grade history via cascade.
