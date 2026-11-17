# Teacher Approval

Admin workflow for reviewing self-registered teacher accounts. Controller: `App\Http\Controllers\Admin\TeacherApprovalController`; views: `resources/views/admin/teachers/`.

## Routes (all under `auth` + `admin` middleware, prefix `/admin`, name `admin.`)

| Method | URI | Name | Controller |
|---|---|---|---|
| GET | `/admin/teachers` | `admin.teachers.index` | `index` |
| PATCH | `/admin/teachers/{user}/approve` | `admin.teachers.approve` | `approve` |
| PATCH | `/admin/teachers/{user}/reject` | `admin.teachers.reject` | `reject` |
| PATCH | `/admin/teachers/{user}/{status}` | `admin.teachers.update` | `update` |

## Behavior

### `index()`

Lists **only pending teachers**:

```php
User::where('role', 'teacher')->where('status', 'pending')->latest()->get();
```

Approved and rejected teachers disappear from the list — there is no way to view or re-review them in the UI.

### `approve(User $user)` / `reject(User $user)` / `update(User $user, string $status)`

All three guard with `abort_unless($user->role === 'teacher', 404)` — operating on a non-teacher returns 404. `update()` additionally requires `$status ∈ {approved, rejected}`.

Effect: `$user->update(['status' => ...])`, then redirect to `admin.teachers.index` with a success flash.

## Business rules & edge cases

- **Idempotent-ish but unguarded:** approving an already-approved teacher is possible via direct URL (the row just isn't listed). Rejecting an approved teacher via `/admin/teachers/{id}/reject` **works** even though the UI never shows them — a rejected teacher is immediately locked out at their next login attempt (and, per the login-time-only gate, keeps any active session until expiry).
- **No reverse path in UI:** once rejected, a teacher cannot be re-approved through the interface because `index()` only lists `pending`. Recovery requires manual DB update.
- **No audit trail:** no `approved_by`, `approved_at`, or rejection-reason columns exist.
- **No notifications:** the teacher receives no email; they discover the outcome on their next login attempt.
- **Admins cannot be approved/rejected here** — the `role === 'teacher'` guard blocks it (admins are seeded directly anyway).

## Dependencies

- Requires `AdminMiddleware` (`role=admin` + `status=approved`).
- Depends on `users.status` semantics enforced at login by `LoginRequest`.
