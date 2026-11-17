# Module: Attendance

## Overview

Attendance is available to **all authenticated users** (teachers and admins). It provides a daily marking sheet, bulk save, single-record updates, a filterable history view, and an attendance statistics dashboard.

---

## Routes

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/attendance` | `attendance.index` | Daily marking sheet |
| POST | `/attendance` | `attendance.store` | Bulk-save attendance for a date |
| PATCH | `/attendance/{attendance}` | `attendance.update` | Update one record |
| GET | `/attendance/history` | `attendance.history` | Historical records |
| GET | `/attendance/dashboard` | `attendance.dashboard` | Attendance statistics |

---

## Controller: `AttendanceController`

`app/Http/Controllers/AttendanceController.php`

### `index(Request $request)`

- `date` query param defaults to today.
- Students are filtered by optional `grade` and `class_section` params, eager-load their `attendances` relation constrained to the selected date, ordered by first name, and paginated at **50 per page**.
- The view receives distinct `$grades` and `$sections` lists for the filter dropdowns.

### `store(Request $request)`

Validation:

| Field | Rules |
|---|---|
| `date` | required, date |
| `attendance` | required, array, min:1 |
| `attendance.*` | required, in:present,absent (keyed by `student_id`) |

The controller verifies every submitted `student_id` exists, then inside a `DB::transaction` calls `Attendance::updateOrCreate()` per student keyed on `(student_id, date)`, setting `status` and `marked_by = auth()->id()`. Re-submitting the same date **overwrites** existing records rather than duplicating them (also guaranteed by the unique constraint).

### `update(Request $request, Attendance $attendance)`

Validates `status` in `present,absent` and updates the record, refreshing `marked_by`.

### `history(Request $request)`

Filterable, paginated (20/page) list of attendance records with `student` and `marker` eager-loaded. Filters: `from`, `to` (to >= from), `grade`, `class_section`, `student` (must exist in `students`).

### `dashboard(Request $request)`

Computes `total`, `present`, `absent`, and `attendanceRate` (percentage, rounded to 2 decimals) over attendance records optionally filtered by date range, grade, and class section (grade/section applied via `whereHas('student')`).

---

## Model: `Attendance`

`app/Models/Attendance.php`

- `student()` — belongsTo `Student`.
- `marker()` — belongsTo `User` via `marked_by`.

---

## Views

| View | Purpose |
|---|---|
| `attendance/index.blade.php` | Daily marking sheet with present/absent controls |
| `attendance/history.blade.php` | Filterable record list |
| `attendance/dashboard.blade.php` | Statistics summary |

---

## Database Table: `attendances`

- `status` is an ENUM(`present`, `absent`).
- Unique `(student_id, date)` prevents duplicate daily records.
- `marked_by` FK → `users.id` with `nullOnDelete`.
- Indexes on `date` and `status` (performance migration).

See [database/schema.md](../database/schema.md#attendances).

---

## Cross-Module Interactions

- **Students:** attendance requires existing students; deleting a student cascades their attendance rows.
- **Users:** `marked_by` records the acting user; deleting a user nulls the column.
- **Statistics:** `SchoolStatisticsController` computes school-wide attendance rates from this table.

---

## Current Limitations

- Only `present`/`absent` statuses — no late, excused, or half-day.
- No per-class timetable or period-level attendance.
- Marking is all-or-nothing per submitted form; there is no draft state.
- No attendance alerts or notifications.