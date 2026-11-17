# Middleware

StudentHub uses Laravel's default web middleware stack plus **one custom middleware**. Registration happens in `bootstrap/app.php` via `Application::configure()`.

## Custom middleware

### `admin` → `App\Http\Middleware\AdminMiddleware`

Registered as an alias in `bootstrap/app.php`:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
]);
```

Applied to the entire `/admin` route group (`prefix('admin')->name('admin.')`).

```php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        abort(403, 'Unauthorized access.');
    }

    $user = Auth::user();

    if ($user->role !== 'admin' || $user->status !== 'approved') {
        abort(403, 'Unauthorized access.');
    }

    return $next($request);
}
```

Behavior:

- Unauthenticated → **403** (not a redirect to login — the `auth` middleware runs first in the group anyway, so this branch is effectively unreachable).
- `role !== 'admin'` **or** `status !== 'approved'` → **403 Forbidden** with plain-text message.
- Both conditions must hold: an approved teacher gets 403; a pending/rejected admin also gets 403.

## Framework middleware in use

| Middleware | Where | Purpose |
|---|---|---|
| `web` group | all `web.php` routes | sessions, CSRF (`VerifyCsrfToken`), cookies, route-model binding |
| `auth` (`Authenticate`) | entire authenticated group + auth.php auth section | redirects guests to `login` |
| `guest` (`RedirectIfAuthenticated`) | register/login/password-reset routes | redirects logged-in users to dashboard |
| `signed` | `verify-email/{id}/{hash}` | signed-URL verification |
| `throttle:6,1` | verification routes | 6 requests/minute |
| `verified` | **not used anywhere** | email verification is scaffolded but unenforced |

## The approval gate is NOT middleware

The `status='approved'` requirement for *login* lives inside `LoginRequest::authenticate()` — it runs once at authentication, not per request. Consequences:

- A user rejected **after** logging in keeps their session until expiry (default 120 min).
- There is no middleware that re-checks `status` on each request.
- `AdminMiddleware` does re-check `status` per request — so a rejected admin loses admin access immediately, while a rejected teacher keeps teacher-level access until re-login.

## Middleware → route mapping

```
routes/web.php
├── GET /                     → view('welcome')          [no middleware]
├── auth group
│   ├── /dashboard, /profile, /students/*, /attendance*  [auth]
│   └── admin group (prefix /admin, name admin.)         [auth + admin]
│       ├── /teachers*           → TeacherApprovalController
│       ├── /subjects*           → SubjectController (+ statistics)
│       ├── /statistics/*        → 3 statistics controllers
│       └── /exams*              → ExamController, ExamGradeController
└── routes/auth.php
    ├── guest group: register, login, forgot/reset password
    └── auth group: verify-email, confirm-password, password update, logout
```
