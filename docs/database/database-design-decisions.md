# Database Design Decisions & Inconsistencies

Analysis of why the schema is shaped the way it is, and the inconsistencies found during reverse engineering. Items marked **[issue]** are problems, not intentional design.

## Intentional decisions

### No `classes`/`sections` table

A "class" is represented implicitly by the `(grade, class_section)` pair on `students`. Exams and attendance reference this pair **by value**. This keeps the schema small but means:

- Renaming a section requires updating every exam row.
- Nothing prevents an exam targeting a non-existent class (only write-time validation in `ExamController`).

### `grade_subject` as a string-keyed pivot

`grade_subject.grade` is `varchar` while `students.grade` is `unsignedTinyInteger`. The seeder writes `(string) $grade`. MySQL coerces between them in joins/where clauses, so `where('grade', $exam->grade)` works — but the types will never support a real foreign key, and strict SQL modes or other engines may behave differently.

### `unique` constraints as the business-rule backbone

- `attendances(student_id, date)` — one attendance record per student per day; `updateOrCreate` relies on it.
- `exam_grades(exam_id, student_id)` — one grade per student per exam; `upsert` relies on it.
- `students.student_code`, `students.email`, `subjects.name`, `grade_subject(subject_id, grade)` — uniqueness enforced at DB level *and* re-validated in controllers.

### `marked_by` is nullable with `nullOnDelete`

Attendance history survives user deletion; the marker becomes unknown rather than blocking deletion.

### Performance indexes added late

`2026_11_17_000000_add_performance_indexes` adds indexes on `students(grade)`, `(class_section)`, `(status)`, `(grade, class_section)` and `attendances(date)`, `(status)` — reflecting that the original schema was created before query patterns were known. The students table is expected to hold ~10,000 rows (per `StudentSeeder`).

### Cached statistics instead of materialized views

All statistics are computed on read and cached 60–300 s in the `cache` table. Appropriate for a read-heavy dashboard at this scale; there is no invalidation strategy.

## Inconsistencies & issues

### [issue] Duplicate `notes` column — breaks fresh installs **[verified]**

`students.notes` is created in `2026_11_03_182612_create_students_table` **and** re-added by `2026_11_16_223616_add_notes_to_students_table`. Running `php artisan migrate` on an empty database fails with `duplicate column name: notes` (verified on SQLite; identical failure expected on MySQL). This also breaks `php artisan test`, since every feature test migrates a fresh database.

**Recommended fix:** remove the `notes` line from whichever migration is considered canonical (likely the `add_notes` migration should be deleted or made conditional with `Schema::hasColumn`), then repair affected databases.

### [issue] `exams.class_section` is `varchar(10)` vs `students.class_section` `char(1)`

Validation limits sections to a single uppercase letter, so 10 chars is harmless but inconsistent. More importantly, `exams.grade`/`class_section` are strings while `students.grade` is an integer — comparisons rely on coercion.

### [issue] `users.role`/`status` have no DB-level constraint

Any string can be written directly to the DB. The application only recognizes `admin`/`teacher`/`user` and `pending`/`approved`/`rejected` in code. A typo in a seeder or manual SQL produces a user who can log in (if `status='approved'`) but sees broken behavior.

### [issue] `fix_missing_columns_in_exams_table` is dead weight

The `2026_11_12` migration guards every column with `Schema::hasColumn` — it exists to repair databases created by an earlier, different `exams` migration that is no longer in the repo. On any clean install it does nothing. It can be removed once all environments are confirmed migrated.

### [issue] `User` model has no relationship to academic data

Teachers who mark attendance are recorded only as `marked_by` user IDs. There is no way to answer "which classes does teacher X handle" — by design or omission.

### [issue] `Student::distinct('grade')->count('grade')` in `DashboardController`

Works, but is an unusual way to count distinct grades (`Student::distinct()->count('grade')` or a `countDistinct` would be clearer). Cosmetic.

### [issue] `composer.json` `post-create-project-cmd` creates `database/database.sqlite`

A leftover from the Laravel skeleton. Harmless (the file is gitignored) but misleading — it suggests SQLite is the default database when MySQL is the supported one.
