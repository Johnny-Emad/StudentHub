# Module: Authentication & Profile Management

## Overview

Authentication in StudentHub is built on Laravel Breeze (session-based). It covers registration, login, logout, password reset, and profile management. A critical extension to the standard Breeze flow is the **approval gate**: every new registration creates a `pending` teacher account, and login is blocked until an admin approves it.

---

## Registration

**Route:** `GET/POST /register`  
**Controller:** `Auth\RegisteredUserController`  
**View:** `auth/register.blade.php`

### Process

1. User submits name, email, and password.
2. Validation rules:
   - `name`: required, string, max:255
   - `email`: required, lowercase, email, max:255, unique in `users`
   - `password`: required, confirmed, satisfies `Password::defaults()` (min 8 chars by default)
3. A new `User` is created with:
   - `role = 'teacher'` (hardcoded in controller)
   - `status = 'pending'` (hardcoded in controller)
4. The `Registered` event is fired (triggers email verification notification if enabled, but `MustVerifyEmail` is commented out in the User model, so no email is sent).
5. The user is **not** logged in. They are redirected to `/login` with a flash message: *"Your account has been created and is waiting for admin approval."*

> There is no path to self-register as an admin. Admin accounts must be created via the `AdminSeeder` or directly in the database.

---

## Login & Approval Gate

**Route:** `GET/POST /login`  
**Controller:** `Auth\AuthenticatedSessionController`  
**Request:** `Auth\LoginRequest`  
**View:** `auth/login.blade.php`

### Process

1. User submits email and password.
2. `LoginRequest::authenticate()` runs:
   - Rate limiting: 5 attempts per email+IP; lockout triggers `Lockout` event.
   - `Auth::attempt()` validates credentials against the database.
   - If credentials fail, rate limiter is incremented and `ValidationException` is thrown.
   - If credentials succeed, the user object is retrieved.
   - **Approval gate:** `if ($user->status !== 'approved')` — the user is immediately logged out and a `ValidationException` is thrown with an appropriate message:
     - `pending`: *"Your account is waiting for admin approval."*
     - Any other non-approved status: *"Your account has not been approved."*
3. If status is `approved`, the session is regenerated and the user is redirected to the intended URL or `/dashboard`.

### Key Security Detail

The approval check runs **after** successful credential verification. A rejected teacher who knows their password will pass the credential check but be blocked by the status check. The status check is the last gate before session creation.

---

## Logout

**Route:** `POST /logout`  
**Controller:** `Auth\AuthenticatedSessionController@destroy`

Logs out the current user, invalidates the session, and regenerates the CSRF token. Redirects to `/`.

---

## Password Reset

**Routes:** `GET/POST /forgot-password`, `GET/POST /reset-password/{token}`  
**Controllers:** `Auth\PasswordResetLinkController`, `Auth\NewPasswordController`  
**Views:** `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`

Standard Laravel Breeze password reset flow using the `password_reset_tokens` table. The mail driver defaults to `log` in the example environment, so reset emails appear in `storage/logs/laravel.log` during development.

---

## Email Verification

**Routes:** `GET /verify-email`, `GET /verify-email/{id}/{hash}`, `POST /email/verification-notification`  
**Controllers:** `Auth\EmailVerificationPromptController`, `Auth\VerifyEmailController`, `Auth\EmailVerificationNotificationController`

The routes and controllers are present (from Breeze scaffold), but the `User` model does **not** implement `MustVerifyEmail` (the interface is commented out). Email verification is therefore **not enforced**.

---

## Profile Management

**Routes:** `GET/PATCH/DELETE /profile`  
**Controller:** `ProfileController`  
**Middleware:** `auth`  
**Views:** `profile/edit.blade.php`, `profile/partials/*`

### Update Profile Information

- Uses `ProfileUpdateRequest` with rules: `name` (required, max:255) and `email` (required, lowercase, unique ignoring own ID).
- If the email changes, `email_verified_at` is reset to `null`.

### Change Password

- Validated with `current_password` rule to confirm the existing password.
- New password must satisfy `Password::defaults()` and be confirmed.

### Delete Account

- Requires the current password via `userDeletion` error bag.
- Logs out the user, deletes the `User` record, invalidates the session.

---

## Current Limitations

- No email notification is sent when a teacher account is approved or rejected. The teacher must attempt to log in to discover their status.
- Email verification infrastructure exists but is not activated.
- The `role` default in the migration is `'user'`, but registration always creates `'teacher'`. A user with `role = 'user'` and `status = 'approved'` can log in but would have no admin access and no different UX from a teacher. This edge case is not expected in normal operation.
