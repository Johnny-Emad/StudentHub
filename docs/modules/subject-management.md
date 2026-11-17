# Subject Management

Admin-only CRUD for the subject catalogue plus assignment of subjects to grade levels. Controller: `SubjectController`; views: `resources/views/subjects/`; routes under `/admin` (`admin.` name prefix, `auth`+`admin` middleware).

## Routes

| Method | URI | Name |
|---|---|---|
| GET | `/admin/subjects` | `admin.subjects.index` |
| GET | `/admin/subjects/create` | `admin.subjects.create` |
| POST | `/admin/subjects` | `admin.subjects.store` |
| GET | `/admin/subjects/{subject}/edit` | `admin.subjects.edit` |
| PUT/PATCH | `/admin/subjects/{subject}` | `admin.subjects.update` |
| DELETE | `/admin/subjects/{subject}` | `admin.subjects.destroy` |
| GET | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.edit` |
| PUT | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.update` |

`show` is excluded from the resource (`->except(['show'])`).

## Subject CRUD

Validation (store/update):

| Field | Rules |
|---|---|
| `name` | required, string, max 255, **unique** `subjects.name` (ignore self on update) |
| `description` | nullable, string |

- `index()` eager-loads `gradeAssignments`, supports `?search=` (`name LIKE %..%`), orders by name, paginates 10.
- `destroy()` deletes the subject — DB cascades remove its `grade_subject` rows **and all its `exams`** (which cascade to `exam_grades`). Deleting a subject therefore destroys exam history; there is no warning in the controller.

## Grade assignment (`editGrades` / `updateGrades`)

This is the mechanism that decides **which grades a subject is taught in** — and it gates exam creation.

### `editGrades(Subject $subject)`

- `$grades` = distinct `students.grade` values present in the DB (excluding null/empty), ordered.
- `$assignedGrades` = grades already linked via `gradeAssignments`.
- Renders `subjects.grades` checkbox UI.

### `updateGrades(Request $request, Subject $subject)`

1. Builds `$availableGrades` = distinct non-empty `students.grade` values.
2. Validates `grades` as a nullable array; each `grades.*` must be `distinct` and `Rule::in($availableGrades)` — **you can only assign grades that currently have at least one student**.
3. In a `DB::transaction`: deletes **all** existing `gradeAssignments` for the subject, then recreates one `grade_subject` row per selected grade.

```php
$subject->gradeAssignments()->delete();
foreach ($selectedGrades as $grade) {
    $subject->gradeAssignments()->create(['grade' => $grade]);
}
```

**Consequences:**

- The operation is **replace-all**, not additive — unchecking a grade removes the assignment (and thereby blocks new exams for that subject+grade).
- Existing `exams` rows are **not** touched — an exam created while the grade was assigned survives unassignment.
- `grade_subject.grade` stores the grade as a **string**; `students.grade` is an integer — matching relies on PHP/DB coercion.
- If no students exist yet, `$availableGrades` is empty and no grades can be assigned.

## Dependencies

- **Exams** validate `grade` via `Rule::exists('grade_subject','grade')->where('subject_id', ...)` — a subject must be assigned to a grade before an exam can target it.
- **Statistics** count `grade_subject` rows per grade (`subjects_count`).
- Deleting a subject cascades exams → exam_grades, affecting all statistics.
