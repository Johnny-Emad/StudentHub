# Models

All seven Eloquent models live in `app/Models/`. They use PHP 8 **attribute-based** configuration — `#[Fillable([...])]` and `#[Hidden([...])]` — rather than `$fillable`/`$hidden` properties. None define `$table` except `GradeSubject` (`grade_subject`), and none define `$casts()` except `User`.

## `User` (`users`)

Authenticatable. `HasFactory`, `Notifiable`.

- **Fillable:** `name`, `email`, `password`, `role`, `status`
- **Hidden:** `password`, `remember_token`
- **Casts:** `email_verified_at → datetime`, `password → hashed`
- **Does NOT implement `MustVerifyEmail`** (commented out)
- **No relationships defined** — the only link to academic data is the inverse `Attendance::marker()`

## `Student` (`students`)

- **Fillable:** `student_code`, `first_name`, `last_name`, `email`, `phone`, `date_of_birth`, `gender`, `address`, `grade`, `class_section`, `status`, `notes`
- **No casts** — `date_of_birth` is *not* cast to a date; `grade` stays integer from DB
- `attendances(): HasMany` → `Attendance`
- `examGrades(): HasMany` → `ExamGrade`
- Factory states: `active()`, `inactive()`, `inGrade(int)`, `inSection(string)`

## `Attendance` (`attendances`)

- **Fillable:** `student_id`, `date`, `status`, `marked_by`
- `student(): BelongsTo` → `Student`
- `marker(): BelongsTo` → `User` via `marked_by`

## `Subject` (`subjects`)

- **Fillable:** `name`, `description`
- `gradeAssignments(): HasMany` → `GradeSubject`
- `exams(): HasMany` → `Exam`

## `GradeSubject` (`grade_subject`)

- **Fillable:** `subject_id`, `grade`
- `protected $table = 'grade_subject'` — required because Laravel would guess `grade_subjects`
- `subject(): BelongsTo` → `Subject`
- No relation to any grade entity (none exists)

## `Exam` (`exams`)

- **Fillable:** `subject_id`, `title`, `grade`, `class_section`, `exam_date`, `total_marks`
- `subject(): BelongsTo` → `Subject`
- `grades(): HasMany` → `ExamGrade`

## `ExamGrade` (`exam_grades`)

- **Fillable:** `exam_id`, `student_id`, `marks_obtained`
- `exam(): BelongsTo` → `Exam`
- `student(): BelongsTo` → `Student`

## Cross-cutting observations

- **No model events, observers, scopes, or accessors** anywhere — all logic is in controllers.
- **No soft deletes** — `delete()` is permanent everywhere.
- **Statistics bypass models** — the four statistics controllers use `DB::table()` joins, so they don't benefit from relationships or casts.
- **`grade` type inconsistency** — `Student.grade` is `unsignedTinyInteger`, `GradeSubject.grade`/`Exam.grade` are strings. Eloquent returns them as int/string respectively; comparisons in PHP (`===`) between them would fail, which is why `ExamGradeController::ensureGradeBelongsToExam` uses loose `==`-style comparison via string casting on IDs but direct `===` on grade/section strings (both sides are strings there — `exam.grade` string vs `student.grade` int... actually `$student->grade === $exam->grade` compares int to string and would be `false`!). See [project-status.md](../project-status.md) — this is a latent bug.
