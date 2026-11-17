# Relationships & Foreign-Key Behavior

Every relationship in the application, verified against the Eloquent models and migrations.

## Eloquent relationships

| Parent | Relation | Child | Type | FK column | On delete |
|---|---|---|---|---|---|
| `Student` | `attendances()` | `Attendance` | HasMany | `attendances.student_id` | cascade (DB-level) |
| `Student` | `examGrades()` | `ExamGrade` | HasMany | `exam_grades.student_id` | cascade (DB-level) |
| `Attendance` | `student()` | `Student` | BelongsTo | `student_id` | — |
| `Attendance` | `marker()` | `User` | BelongsTo | `marked_by` → `users.id` | null on delete |
| `Subject` | `gradeAssignments()` | `GradeSubject` | HasMany | `grade_subject.subject_id` | cascade (DB-level) |
| `Subject` | `exams()` | `Exam` | HasMany | `exams.subject_id` | cascade (DB-level) |
| `GradeSubject` | `subject()` | `Subject` | BelongsTo | `subject_id` | — |
| `Exam` | `subject()` | `Subject` | BelongsTo | `subject_id` | — |
| `Exam` | `grades()` | `ExamGrade` | HasMany | `exam_grades.exam_id` | cascade (DB-level) |
| `ExamGrade` | `exam()` | `Exam` | BelongsTo | `exam_id` | — |
| `ExamGrade` | `student()` | `Student` | BelongsTo | `student_id` | — |
| `User` | — | — | **none defined** | — | — |

Notable absences:

- `User` has **no** `attendances()` HasMany for `marked_by`, no teacher↔subject assignment, no created-by tracking on students/exams/grades.
- `Subject` has no direct `students()` relation — the grade mapping goes through `grade_subject`.
- `GradeSubject` has no relation back to a grade entity (none exists).

## Delete behavior summary

| Action | Effect |
|---|---|
| Delete a `Student` | Their `attendances` and `exam_grades` rows are **cascade-deleted** by the DB. Eloquent `delete()` does not load relations — the DB handles it |
| Delete a `Subject` | Its `grade_subject` rows and `exams` are cascade-deleted; deleting an exam cascades to its `exam_grades` |
| Delete an `Exam` | Its `exam_grades` are cascade-deleted |
| Delete a `User` | `attendances.marked_by` is set to `NULL` (`nullOnDelete`); all other data untouched |
| Delete a `User` who marked attendance | Attendance records survive with `marked_by = NULL` |

## Referential integrity gaps

These are **logical** relationships enforced only in application code, not by the database:

1. **`exams.grade` / `exams.class_section` ↔ `students`** — an exam can reference a grade/section with zero students; nothing prevents it. `ExamController` validates at write time that the section exists among students *of that grade*, but students can later move sections, orphaning the exam's target set.
2. **`grade_subject.grade` ↔ `students.grade`** — type mismatch (`string` vs `tinyint`) means the DB can't FK them; `SubjectController` validates submitted grades against `students.grade` values at write time.
3. **`exam_grades.student_id` ↔ exam's class** — the DB only enforces that the student exists. `ExamGradeController::store()` re-validates grade+section membership in PHP before upserting.
4. **`students.status`** — `inactive` students still appear in attendance marking, exam targeting, and grade entry. Only the seeders filter on `status='active'`; no controller does.
