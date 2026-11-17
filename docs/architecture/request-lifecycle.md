# Request Lifecycle

A traced walkthrough of how a request flows through StudentHub, using a real example: **an admin marking attendance** (`POST /attendance`).

## Example: `POST /attendance` (mark daily attendance)

### 1. HTTP request arrives

```
POST /attendance
Cookie: studenthub_session=...
Body: date=2026-11-17&attendance[12]=present&attendance[15]=absent&_token=...
```

### 2. Front controller

`public/index.php` loads `bootstrap/app.php`, which returns a configured `Application`:

- `withRouting` registers `routes/web.php`, `routes/console.php`, and the `/up` health endpoint.
- `withMiddleware` aliases `admin` → `App\Http\Middleware\AdminMiddleware`.
- `withExceptions` renders JSON for `api/*` or `Accept: application/json` requests (no API routes exist, so this only affects AJAX).

### 3. Route matching

`routes/web.php` matches:

```php
Route::post('/attendance', [AttendanceController::class, 'store'])
    ->name('attendance.store');
```

This route sits **inside** `Route::middleware('auth')->group(...)`, so the `auth` middleware runs first.

### 4. Middleware

| Middleware | Effect for this request |
|---|---|
| `web` group (sessions, CSRF, cookies) | Validates `_token` against session; loads session |
| `auth` (`Authenticate`) | If no session user → redirect to `login`. Request has a session, so it passes |
| `admin` | **Not applied** — `/attendance` is outside the `admin` prefix group. Any approved user (teacher or admin) may mark attendance |

> Note: the approval gate is enforced **at login** (`LoginRequest`), not per-request. A user whose status is changed to `rejected` *after* logging in keeps their session until it expires — there is no per-request status re-check.

### 5. Controller — `AttendanceController::store()`

```php
$validated = $request->validate([
    'date'      => ['required', 'date'],
    'attendance'=> ['required', 'array', 'min:1'],
    'attendance.*' => ['required', Rule::in(['present', 'absent'])],
]);
```

- Validation failure → redirect back with errors (standard Laravel behavior for web requests).
- On success, every key of `attendance` is treated as a `student_id`. The controller then verifies **all** IDs exist:

```php
if (Student::whereIn('id', $studentIds)->count() !== count($studentIds)) {
    throw ValidationException::withMessages(['attendance' => 'Invalid student selection.']);
}
```

### 6. Business logic — database transaction

```php
DB::transaction(function () use ($validated, $request) {
    foreach ($validated['attendance'] as $studentId => $status) {
        Attendance::updateOrCreate(
            ['student_id' => $studentId, 'date' => $validated['date']],
            ['status' => $status, 'marked_by' => $request->user()->id]
        );
    }
});
```

- `updateOrCreate` on the `(student_id, date)` pair — backed by the `unique(['student_id','date'])` index — means re-saving the same day **updates** the record instead of duplicating it.
- `marked_by` is overwritten with the current user on every save.
- All rows commit or roll back together.

### 7. Response

```php
return back()->with('success', 'Attendance saved successfully.');
```

Redirect back to `/attendance?date=...&grade=...` with a flash message rendered by the Blade view.

---

## Where each concern lives

| Concern | Location |
|---|---|
| Authentication | `auth` middleware + `LoginRequest::authenticate()` |
| Approval gate | `LoginRequest` (login time only) |
| Admin authorization | `AdminMiddleware` on `/admin/*` routes |
| Input validation | Inline `$request->validate()` in controllers; Form Requests for auth/profile |
| Business rules | Controller methods (transactions, existence checks, cross-field `Rule::exists` validation) |
| Data integrity | Migration constraints: `unique`, `foreignId()->constrained()`, `enum` |
| Presentation | Blade views under `resources/views/` |

## Contrast: an admin-only request

`GET /admin/subjects/statistics` additionally passes through `AdminMiddleware`, which aborts with **403** unless `role === 'admin'` **and** `status === 'approved'`. The controller then wraps its queries in `Cache::remember('stats.subjects', 300, ...)` before returning `subjects.statistics`.
