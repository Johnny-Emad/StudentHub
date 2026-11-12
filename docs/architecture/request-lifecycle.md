# Request Lifecycle

This document traces a complete HTTP request through StudentHub using a concrete example: **an admin submitting the form to create a new exam**.

---

## Example: `POST /admin/exams`

### Step 1 — Browser

The admin fills in the exam creation form at `GET /admin/exams/create` and clicks **Create Exam**. The browser sends:

```
POST /admin/exams
Content-Type: application/x-www-form-urlencoded
Cookie: laravel_session=...

_token=...&title=Midterm+Math&subject_id=3&grade=5&class_section=A&exam_date=2026-12-01&total_marks=100
```

---

### Step 2 — Routing

Laravel loads `routes/web.php`. The request URL `/admin/exams` with method `POST` is matched against:

```php
Route::middleware('auth')
    ->group(function () {
        Route::middleware('admin')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::resource('exams', ExamController::class)->except(['show']);
            });
    });
```

The resource route resolves `POST /admin/exams` to `ExamController@store`.

---

### Step 3 — Middleware: `auth`

Laravel's `auth` middleware checks whether a session-authenticated user exists. If not, the request is redirected to `/login`. In this example, the admin is authenticated, so execution continues.

---

### Step 4 — Middleware: `admin`

`AdminMiddleware::handle()` runs:

```php
if (!Auth::check()) {
    abort(403, 'Unauthorized access.');
}

$user = Auth::user();

if ($user->role !== 'admin' || $user->status !== 'approved') {
    abort(403, 'Unauthorized access.');
}

return $next($request);
```

The admin user has `role = 'admin'` and `status = 'approved'`, so the request continues.

---

### Step 5 — Controller: `ExamController@store`

The controller's `store` method calls the private `validateExam($request)` helper:

```php
$validated = $this->validateExam($request);
```

Validation rules applied:

| Field | Rules |
|---|---|
| `title` | required, string, max:255 |
| `subject_id` | required, integer, exists:subjects,id |
| `grade` | required, string, must exist in `grade_subject.grade` where `subject_id` matches |
| `class_section` | required, string, max:10, must exist in `students.class_section` where `grade` matches |
| `exam_date` | required, date |
| `total_marks` | required, numeric, gt:0, max:999999.99 |

If validation fails, Laravel throws a `ValidationException`, which redirects back with errors flashed to the session.

---

### Step 6 — Model Interaction

If validation passes:

```php
Exam::create($validated);
```

Eloquent maps `$validated` to the `exams` table using the `$fillable` attributes defined on the `Exam` model. A SQL `INSERT` is executed:

```sql
INSERT INTO exams (subject_id, title, grade, class_section, exam_date, total_marks, created_at, updated_at)
VALUES (3, 'Midterm Math', '5', 'A', '2026-12-01', 100, NOW(), NOW());
```

---

### Step 7 — Response

After a successful insert:

```php
return redirect()
    ->route('admin.exams.index')
    ->with('success', 'Exam created successfully.');
```

Laravel returns an HTTP 302 redirect to `/admin/exams`. The success message is flashed to the session.

---

### Step 8 — Follow-up GET `/admin/exams`

The browser follows the redirect. The `ExamController@index` method runs, queries `Exam::with('subject')->latest('exam_date')->paginate(15)`, and renders `exams/index.blade.php`. The layout reads the flashed `success` session key and triggers the Alpine.js toast notification via a `<script>` block in the view.

---

## Lifecycle Summary

```
Browser POST /admin/exams
    │
    ├── routes/web.php: matched to ExamController@store
    ├── Middleware: auth → pass
    ├── Middleware: admin → pass (role=admin, status=approved)
    ├── ExamController@store()
    │     ├── validateExam($request) → ValidationException if invalid
    │     └── Exam::create($validated) → SQL INSERT
    └── redirect()->route('admin.exams.index')->with('success', ...)
         └── Browser follows 302 → GET /admin/exams → renders exam list
```
