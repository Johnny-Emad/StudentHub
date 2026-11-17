# Authentication

Session-based authentication scaffolded with **Laravel Breeze**, customized with a school-specific approval gate. All routes are in `routes/auth.php`; controllers in `app/Http/Controllers/Auth/`.

## Registration

`GET /register` → `auth.register` → `RegisteredUserController@create`
`POST /register` → `RegisteredUserController@store`

Validation (`store`):

| Field | Rules |
|---|---|
| `name` | required, string, max 255 |
| `email` | required, string, lowercase, email, max 255, unique on `users` |
| `password` | required, confirmed, `Rules\Password::defaults()` |

On success the controller **always** creates:

```php
'role'   => 'teacher',
'status' => 'pending',
```

then fires `Registered` event and redirects to `login` with the flash *"Your account has been created and is waiting for admin approval."* — the user is **not** logged in automatically.

> There is no way to register as admin or student. Admins come only from `AdminSeeder`/SQL.

## Login — with approval gate

`GET /login` → `auth.login`
`POST /login` → `AuthenticatedSessionController@store` → `LoginRequest`

`LoginRequest::authenticate()` does three things:

1. **Rate limiting** — `ensureIsNotRateLimited()`: max **5 attempts** per `email|ip` throttle key; throws `Lockout` event + validation error with seconds remaining.
2. **Credential check** — `Auth::attempt(email, password, remember)`; failure increments the rate limiter and throws `auth.failed`.
3. **Approval gate** — after successful auth, if `$user->status !== 'approved'` the user is **logged out immediately** and a validation error is thrown:
   - `pending` → *"Your account is waiting for admin approval."*
   - anything else → *"Your account has not been approved."*

On success: `session()->regenerate()`, redirect to `route('dashboard')` (intended URL honored).

**Important:** the approval check happens **only at login**. A user approved-then-rejected while holding a session stays logged in until the session expires — there is no per-request status middleware.

## Logout

`POST /logout` → `AuthenticatedSessionController@destroy` — `Auth::guard('web')->logout()`, session invalidate, CSRF token regenerate, redirect `/`.

## Password reset (Breeze standard)

| Route | Name | Purpose |
|---|---|---|
| `GET /forgot-password` | `password.request` | Request form |
| `POST /forgot-password` | `password.email` | Send reset link (via `MAIL_MAILER`, default `log`) |
| `GET /reset-password/{token}` | `password.reset` | New password form |
| `POST /reset-password` | `password.store` | Update password |

All under `guest` middleware.

## Email verification — scaffolded, not enforced

Routes exist (`verification.notice`, `verification.verify` with `signed`+`throttle:6,1`, `verification.send`), but:

- `User` does **not** implement `MustVerifyEmail` (the `use` statement is commented out in `app/Models/User.php`).
- No route uses the `verified` middleware.
- `ProfileController@update` still nulls `email_verified_at` when the email changes.

Verification emails are effectively dead code unless `MustVerifyEmail` is restored.

## Confirm password & password update

- `GET/POST /confirm-password` — `password.confirm` (standard Breeze password confirmation for sensitive actions).
- `PUT /password` — `password.update` → `PasswordController` (current-password validated).

## Profile management

See routes `profile.edit` / `profile.update` / `profile.destroy`:

- **Edit** (`GET /profile`) — name + email via `ProfileUpdateRequest` (email unique ignoring self, lowercase). Changing email resets `email_verified_at`.
- **Delete** (`DELETE /profile`) — requires `password` confirmed via `current_password` rule in `userDeletion` error bag; logs out, deletes user, invalidates session.

## Dependencies on other modules

- Approval status is consumed by `AdminMiddleware` (admin area) and `LoginRequest` (all logins).
- `attendances.marked_by` references `users.id` — deleting a user nulls the marker.
- `AdminSeeder` is the only built-in way to create an admin.
