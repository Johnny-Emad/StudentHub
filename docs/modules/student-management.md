# Student Management

Full CRUD for the student registry plus search and filtering. Controller: `StudentController`; views: `resources/views/students/`; routes: `Route::resource('students', ...)` inside the `auth` group — **available to every approved user** (teachers and admins alike).

## Routes

| Method | URI | Name |
|---|---|---|
| GET | `/students` | `students.index` |
| GET | `/students/create` | `students.create` |
| POST | `/students` | `students.store` |
| GET | `/students/{student}` | `students.show` |
| GET | `/students/{student}/edit` | `students.edit` |
| PUT/PATCH | `/students/{student}` | `students.update` |
| DELETE | `/students/{student}` | `students.destroy` |

## Index — search & filters

`index()` builds a query with optional request params, paginates **10 per page** (`latest()` first), and preserves query string in pagination links.

| Param | Behavior |
|---|---|
| `search` | `LIKE %term%` across `student_code`, `first_name`, `last_name`, `CONCAT(first_name,' ',last_name)`, `email`, `phone` |
| `grade` | exact match on `students.grade` |
| `class_section` | exact match |
| `status` | exact match (`active`/`inactive`) |

Filter dropdowns are populated from **distinct values actually present in the table** (`Student::distinct()->pluck()`), not a fixed grade 1–12 list — so grades with no students don't appear.

> `CONCAT(first_name, ' ', last_name)` uses `whereRaw` — portable SQL, works on MySQL and SQLite.

## Validation rules (store & update — identical except unique-ignore)

| Field | Rules |
|---|---|
| `student_code` | required, string, max 255, **unique** `students.student_code` (ignored self on update) |
| `first_name` / `last_name` | required, string, max 100 |
| `email` | nullable, email, max 255, **unique** `students.email` (ignored self on update) |
| `phone` | nullable, **digits:11** |
| `date_of_birth` | nullable, date, `before_or_equal:today` |
| `gender` | nullable, `in:male,female` |
| `address` | nullable, string, max 1000 |
| `grade` | required, integer, `between:1,12` |
| `class_section` | required, string, `size:1`, `regex:/^[A-Z]$/` |
| `notes` | nullable, string, max 5000 |
| `status` | required, `in:active,inactive` |

## Business logic

- **Create/update** — plain `Student::create($validated)` / `$student->update($validated)`; no transactions needed (single row).
- **Delete** — `$student->delete()`. DB cascades remove the student's `attendances` and `exam_grades` rows. **There is no confirmation of academic impact and no soft deletes** — deletion is permanent.
- **Show** — `students.show` displays the record; the model eager-loads nothing (attendances/examGrades relations exist but aren't loaded in `show()`).

## Interactions with other modules

- `grade` + `class_section` define the class a student belongs to — used by attendance marking, exam targeting (`ExamController@targetStudents`), grade entry, and all statistics.
- Deleting a student cascades attendance and exam grades — statistics totals shrink retroactively.
- `status='inactive'` students remain fully manageable and still appear in attendance marking, exam targeting, and grade entry — **no controller filters on status** (only seeders do).
- `student_code` uniqueness is the only required identifier; there is no auto-generation — staff must invent codes.

## Known gaps

- No bulk import/export (CSV/Excel).
- No photo or document uploads.
- `phone` requires exactly 11 digits — rigid for international formats.
- `grade`/`class_section` have no referential check against `grade_subject` — a student can be enrolled in a grade that has no subjects assigned.
