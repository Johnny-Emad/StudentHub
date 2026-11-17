# Attendance

Daily present/absent marking per student, plus history browsing and an attendance-rate dashboard. Controller: `AttendanceController`; views: `resources/views/attendance/`; routes inside the `auth` group — **available to every approved user**.

## Routes

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/attendance` | `attendance.index` | Daily marking sheet |
| POST | `/attendance` | `attendance.store` | Save marks for a date |
| PATCH | `/attendance/{attendance}` | `attendance.update` | Change a single record's status |
| GET | `/attendance/history` | `attendance.history` | Filterable record log |
| GET | `/attendance/dashboard` | `attendance.dashboard` | Attendance-rate stats |

## `index()` — the marking sheet

- `date` param defaults to today (`now()->toDateString()`).
- Lists students (id, code, name, grade, section) with optional `grade`/`class_section` filters, **paginated 50 per page**, ordered by first name.
- Eager-loads each student's `attendances` **for that date only** (`->where('date', $date)`) so the view can pre-fill existing marks.
- Filter dropdowns = distinct grade/section values present in `students`.
- **All students are listed regardless of `status`** — inactive students can be marked.

## `store()` — bulk save

Validation:

| Field | Rules |
|---|---|
| `date` | required, date |
| `attendance` | required, array, min 1 |
| `attendance.*` | required, `in:present,absent` — keys are student IDs |

Logic:

1. Verifies **every** submitted student ID exists (`Student::whereIn('id', ...)->count() === count(...)`), else `ValidationException` "Invalid student selection."
2. Inside `DB::transaction`, calls `Attendance::updateOrCreate(['student_id','date'], ['status','marked_by' => current user])` per row.

**Duplicate prevention** is enforced at two levels: the `unique(student_id, date)` DB index and `updateOrCreate` semantics — re-submitting the same date **updates** existing rows rather than erroring. This is what makes the sheet re-submittable.

## `update()` — single record

`PATCH /attendance/{attendance}` validates `status ∈ {present,absent}`, updates the row, sets `marked_by` to the current user. Used for corrections from the history view.

## `history()` — record log

Filters (all optional, validated):

| Param | Rule |
|---|---|
| `from` / `to` | date; `to` must be `after_or_equal:from` |
| `grade` / `class_section` | string — applied via `whereHas('student', ...)` |
| `student` | integer, `exists:students,id` |

Eager-loads `student` + `marker` (the user who marked it), orders by `date` desc, paginates 20. Also returns the filtered student list for the dropdown.

## `dashboard()` — attendance rate

Same filter params as history (minus `student`). Computes over the filtered `Attendance` query:

- `total`, `present`, `absent` counts
- `attendanceRate = round(present/total*100, 2)` — `0` when total is 0

**Not cached** — unlike the statistics module, this runs live queries each request.

## Data model interaction

- `attendances.status` is a DB `enum('present','absent')` — no other statuses (no "late", "excused").
- `marked_by` → `users.id`, `nullOnDelete` — history survives user deletion.
- Deleting a student cascade-deletes their attendance rows.

## Known gaps

- No per-class roll-call workflow — marking is per-student checkboxes across a paginated list (50/page), so a class larger than 50 spans pages.
- No "mark all present" shortcut visible in the controller contract (view-level concern).
- No excuse/notes field on attendance records.
- Inactive students are not excluded anywhere in this module.
