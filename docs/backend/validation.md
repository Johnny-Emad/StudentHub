# Validation

StudentHub validates almost entirely with **inline `$request->validate()`** inside controllers. Only two Form Requests exist: `LoginRequest` (auth) and `ProfileUpdateRequest` (profile). Registration validation is inline in `RegisteredUserController`.

## Consolidated rules by operation

### Auth

| Operation | Rules |
|---|---|
| Register (`RegisteredUserController@store`) | `name` req/string/255 · `email` req/lowercase/email/255/unique:users · `password` req/confirmed/`Password::defaults()` |
| Login (`LoginRequest`) | `email` req/string/email · `password` req/string — plus rate limit 5 attempts per email+IP and the approval gate |
| Profile update (`ProfileUpdateRequest`) | `name` req/string/255 · `email` req/lowercase/email/255/unique:users,ignore-self |
| Account deletion | `password` required + `current_password` (bag: `userDeletion`) |
| Password update (Breeze `PasswordController`) | current password + new password confirmed/defaults |

### Students (`StudentController` store/update — identical except unique-ignore)

| Field | Rules |
|---|---|
| `student_code` | required, string, 255, unique:students |
| `first_name`, `last_name` | required, string, 100 |
| `email` | nullable, email, 255, unique:students |
| `phone` | nullable, **digits:11** |
| `date_of_birth` | nullable, date, before_or_equal:today |
| `gender` | nullable, in:male,female |
| `address` | nullable, string, 1000 |
| `grade` | required, integer, between:1,12 |
| `class_section` | required, string, size:1, regex:`/^[A-Z]$/` |
| `notes` | nullable, string, 5000 |
| `status` | required, in:active,inactive |

### Attendance

| Operation | Rules |
|---|---|
| `store` | `date` req/date · `attendance` req/array/min:1 · `attendance.*` req/in:present,absent — plus manual check that all student IDs exist |
| `update` | `status` req/in:present,absent |
| `history`, `dashboard` filters | `from`/`to` nullable date (`to` after_or_equal:from) · `grade`,`class_section` nullable string · `student` nullable integer exists:students,id |

### Subjects

| Operation | Rules |
|---|---|
| store/update | `name` req/string/255/unique:subjects (ignore-self) · `description` nullable/string |
| `updateGrades` | `grades` nullable/array · `grades.*` required/string/distinct/`Rule::in($availableGrades)` where available = distinct non-empty `students.grade` |

### Exams (`ExamController::validateExam` — store & update)

| Field | Rules |
|---|---|
| `title` | required, string, 255 |
| `subject_id` | required, integer, exists:subjects,id |
| `grade` | required, string, **exists:grade_subject,grade where subject_id=input** |
| `class_section` | required, string, 10, **exists:students,class_section where grade=input** |
| `exam_date` | required, date |
| `total_marks` | required, numeric, gt:0, max:999999.99 |

### Exam grades (`ExamGradeController`)

| Operation | Rules |
|---|---|
| `store` | `grades` req/array · `grades.*` nullable/numeric/min:0/max:{exam.total_marks} — plus manual per-student class-membership check |
| `update` | `marks_obtained` req/numeric/min:0/max:{exam.total_marks} — plus `ensureGradeBelongsToExam` 404 guard |

### Statistics

`SchoolStatisticsController` only: `from`/`to` nullable dates, `to` after_or_equal:from. Other statistics endpoints take no input.

## Notable validation patterns

- **`Rule::exists` with `->where()`** — used for the exam's subject↔grade and grade↔section constraints; the only cross-table validation in the app.
- **Manual existence checks** — `AttendanceController@store` and `ExamGradeController@store` re-verify student IDs in PHP after array validation (needed because array keys can't use `exists` per-element cleanly).
- **`Rule::unique()->ignore($model->id)`** — standard update-time uniqueness for students, subjects.
- **No Form Request authorization** — all requests `authorize()` implicitly (no `$this->authorize()` calls anywhere); access control is 100% middleware.
- **No custom validation rules or messages** — all default Laravel messages.
