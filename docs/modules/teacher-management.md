# Module: Teacher Management (Admin Approval)

## Overview

StudentHub does not use a separate "teachers" table. Teacher accounts are `users` rows with `role = 'teacher'`. The teacher management module is entirely about the approval workflow: an admin can review pending teacher registrations and approve or reject them.

---

## Purpose

Prevent unapproved users from accessing the application. Any teacher who registers is held in `status = 'pending'` until an admin explicitly approves them.

---

## Routes

| Method | URL | Name | Middleware |
|---|---|---|---|
| GET | `/admin/teachers` | `admin.teachers.index` | `auth`, `admin` |
| PATCH | `/admin/teachers/{user}/approve` | `admin.teachers.approve` | `auth`, `admin` |
| PATCH | `/admin/teachers/{user}/reject` | `admin.teachers.reject` | `auth`, `admin` |
| PATCH | `/admin/teachers/{user}/{status}` | `admin.teachers.update` | `auth`, `admin` |

The `admin.teachers.update` route is a combined route used by the view, accepting `{status}` as either `'approved'` or `'rejected'`. The dedicated `approve` and `reject` routes exist alongside it for clarity.

---

## Controller: `Admin\TeacherApprovalController`

### `index()`

```php
$teachers = User::where('role', 'teacher')
    ->where('status', 'pending')
    ->latest()
    ->get();
```

Fetches all users with `role = 'teacher'` and `status = 'pending'`, ordered by most recently registered. This means **approved and rejected teachers are not shown** in this view — once actioned, they disappear from the list.

**Returns:** `admin/teachers/index` view.

### `approve(User $user)`

- Guards: `abort_unless($user->role === 'teacher', 404)` — prevents approving non-teachers.
- Sets `status = 'approved'`.
- Redirects back to `admin.teachers.index` with a success message.

### `reject(User $user)`

- Same guard as `approve()`.
- Sets `status = 'rejected'`.
- Redirects back with success message.

### `update(User $user, string $status)`

- Guards: `abort_unless($user->role === 'teacher' && in_array($status, ['approved', 'rejected']), 404)`.
- Sets `status` to the provided value.
- Used by the view when it calls the combined route with the status as a URL parameter.

---

## Model: `User`

Relevant columns:

| Column | Relevant Values |
|---|---|
| `role` | `'teacher'` |
| `status` | `'pending'`, `'approved'`, `'rejected'` |

No Eloquent relationships are defined on `User` relating to teacher-specific data.

---

## Authorization

The `admin` middleware (`AdminMiddleware`) requires:
- `Auth::check()` — user must be authenticated.
- `$user->role === 'admin'` — must be an admin.
- `$user->status === 'approved'` — admin's own account must be approved.

If either condition fails, a 403 response is returned.

---

## View: `admin/teachers/index.blade.php`

Displays a table of pending teachers with their name, email, and registration date. Each row has approve and reject buttons that submit `PATCH` requests to `admin.teachers.update` using a hidden `_method` field.

---

## Current Limitations

- Once approved or rejected, teachers no longer appear in the pending list. There is no view to see all teachers (approved + rejected) or to change a previously rejected teacher to approved.
- No email is sent to the teacher when their account status changes.
- There is no audit log for approval actions.
