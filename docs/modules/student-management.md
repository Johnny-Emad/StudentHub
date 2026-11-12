# Module: Student Management

## Overview

Student management is the core data module of StudentHub. It provides full CRUD (Create, Read, Update, Delete) for student records, with server-side search, filtering, and pagination on the index view. All routes are protected by the `auth` middleware; both teachers and admins have equal access.

---

## Routes

| Method | URL | Name | Controller Method |
|---|---|---|---|
| GET | `/students` | `students.index` | `StudentController@index` |
| GET | `/students/create` | `students.create` | `StudentController@create` |
| POST | `/students` | `students.store` | `StudentController@store` |
| GET | `/students/{student}` | `students.show` | `StudentController@show` |
| GET | `/students/{student}/edit` | `students.edit` | `StudentController@edit` |
| PATCH/PUT | `/students/{student}` | `students.update` | `StudentController@update` |
| DELETE | `/students/{student}` | `students.destroy` | `StudentController@destroy` |

All routes are registered via `Route::resource('students', StudentController::class)`.

---

## Controller: `StudentController`

### `index(Request $request)` — Student Listing

Supports live filtering through query string parameters:

| Parameter | Filter behaviour |
|---|---|
| `search` | LIKE match on `student_code`, `first_name`, `last_name`, full name concatenation, `email`, `phone` |
| `grade` | Exact match on `grade` |
| `class_section` | Exact match on `class_section` |
| `status` | Exact match on `status` (`active` or `inactive`) |

Results are paginated at 10 per page with `withQueryString()` to preserve filters across pages.

The view also receives dynamic `$grades` and `$sections` arrays derived from distinct values in the `students` table, used to populate filter dropdowns.

### `store(Request $request)` — Validation Rules

| Field | Rules |
|---|---|
| `student_code` | required, string, max:255, unique:students |
| `first_name` | required, string, max:100 |
| `last_name` | required, string, max:100 |
| `email` | nullable, email, max:255, unique:students |
| `phone` | nullable, digits:11 |
| `date_of_birth` | nullable, date, before_or_equal:today |
| `gender` | nullable, in:male,female |
| `address` | nullable, string, max:1000 |
| `grade` | required, string, max:50 |
| `class_section` | required, string, size:1 |
| `status` | required, in:active,inactive |

### `update(Request $request, Student $student)` — Validation Rules

Same as `store()` with these modifications:
- `student_code`: unique rule ignores the current student's own record.
- `email`: unique rule ignores the current student's own record.

### `destroy(Student $student)` — Delete

Hard delete. Due to cascade constraints in the database, all `attendances` and `exam_grades` rows for this student are also deleted.

---

## Model: `Student`

```php
class Student extends Model
{
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function examGrades(): HasMany
    {
        return $this->hasMany(ExamGrade::class);
    }
}
```

Fillable fields: `student_code`, `first_name`, `last_name`, `email`, `phone`, `date_of_birth`, `gender`, `address`, `grade`, `class_section`, `status`.

---

## Views

| View | Purpose |
|---|---|
| `students/index.blade.php` | Paginated table with search bar, grade/section/status dropdowns |
| `students/create.blade.php` | New student form |
| `students/edit.blade.php` | Edit student form (pre-populated) |
| `students/show.blade.php` | Read-only student detail view |

---

## Database Table: `students`

See [database/schema.md](../database/schema.md#students) for the full column reference.

Key constraints:
- `student_code` is unique — the application does not auto-generate it; users enter it manually.
- `email` is nullable but unique when provided.
- `class_section` is validated as a single character at the application level.

---

## Cross-Module Interactions

- **Attendance:** Attendance records are created with a `student_id` FK. Deleting a student cascades to delete all their attendance records.
- **Exam Grades:** `exam_grades` rows reference `student_id`. Deleting a student cascades to delete all their grades.
- **Exams:** Exam creation validates that the target `grade` and `class_section` combination exists in the `students` table. If the last student in a class is deleted, that class section may become unavailable for new exams.
- **Statistics:** All statistics controllers query the `students` table to build their data sets.

---

## Current Limitations

- No student photo or document upload.
- No bulk import (CSV or spreadsheet).
- Deleting a student is irreversible and silently removes all historical attendance and exam grade data.
- `grade` is a free-text string with no predefined list; inconsistent grade entry (e.g., "5" vs "Grade 5") would create separate filter options.
