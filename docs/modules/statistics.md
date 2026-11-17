# Statistics

Four admin-only dashboards computing academic aggregates, plus the attendance dashboard (covered in [attendance.md](attendance.md)). All statistics controllers use `DB::table()` query-builder joins rather than Eloquent relations, and all heavy results are wrapped in `Cache::remember`.

## Routes (all `/admin`, `auth`+`admin`)

| Method | URI | Name | Controller |
|---|---|---|---|
| GET | `/admin/subjects/statistics` | `admin.subjects.statistics` | `SubjectStatisticsController` |
| GET | `/admin/statistics/classes` | `admin.statistics.classes` | `ClassStatisticsController` |
| GET | `/admin/statistics/grades` | `admin.statistics.grades` | `GradeStatisticsController` |
| GET | `/admin/statistics/school` | `admin.statistics.school` | `SchoolStatisticsController` |

## The core metric: weighted percentage

Every average in the system uses the same formula:

```
average = SUM(exam_grades.marks_obtained) / SUM(exams.total_marks) * 100
```

computed per grouping (subject / class / grade / school / student). This is a **weighted** average — an exam out of 100 counts double one out of 50. Per-student averages are computed the same way grouped by `student_id`, then `max()`/`min()` produce highest/lowest.

## Caching

| Controller | Cache key | TTL |
|---|---|---|
| `SubjectStatisticsController` | `stats.subjects` | 300 s |
| `ClassStatisticsController` | `stats.classes` | 300 s |
| `GradeStatisticsController` | `stats.grades` | 300 s |
| `SchoolStatisticsController` | `stats.school.{md5(from\|to)}` | 300 s — one key per date-range combo |
| `DashboardController` | `dashboard.stats`, `dashboard.recent_students` | 60 s |

**There is no cache invalidation anywhere** — new students/exams/grades appear in stats up to 5 minutes late. `php artisan cache:clear` flushes manually.

## Per-controller detail

### `SubjectStatisticsController@index`

For each subject (with `exams_count`):

- `targeted_students` — distinct students joined via `exams.grade = students.grade AND exams.class_section = students.class_section`
- `graded_count` — total `exam_grades` rows across the subject's exams
- `overall_average` — weighted % across all grades
- `highest_average` / `lowest_average` — max/min of per-student weighted averages

### `ClassStatisticsController@index`

Groups `students` by `(grade, class_section)`; per class:

- `students_count`, `subjects_count` (grade_subject rows for that grade), `exams_count`, `grades_count`
- `average`, `highest_average`, `lowest_average` — same weighted formulas restricted to the class's students

### `GradeStatisticsController@index`

Groups `students` by `grade`; per grade:

- `students_count`, `sections_count` (distinct class_section), `subjects_count`, `exams_count`, `grades_count`
- `overall_average`, `highest_average`, `lowest_average`

### `SchoolStatisticsController@index`

Accepts optional `from`/`to` date filters (`to` must be `after_or_equal:from`), applied to `exams.exam_date` and `attendances.date`:

- Counts: students, distinct grades, distinct (grade,section) pairs, subjects, exams in range, grade records in range
- `overallAverage`, `highestAverage`, `lowestAverage` — school-wide weighted stats
- Attendance: `attendanceCount`, `presentCount`, `absentCount`, `attendanceRate` (null when 0 records)

## Accuracy caveats

- **`targeted_students` can double-count intent** — it counts distinct students matching exam grade+section, but if a student's section changed after the exam, they still match by current values.
- **Inactive students are included** in all counts and averages — no `status` filter anywhere in statistics.
- **Students with no grades** don't appear in highest/lowest (their `possible` is 0 → filtered out), but they do count in `students_count`.
- **Exam dates vs. grade dates** — school stats filter by `exam_date`, so "grades recorded" reflects when the exam was scheduled, not when marks were entered.
- The dashboard's percentage-change badges (`+12%`, `+5%`, `-2%`) are **hardcoded in the Blade template**, not computed — see [project-status.md](../project-status.md).
