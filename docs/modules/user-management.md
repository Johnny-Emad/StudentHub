# User Management

There is **no user-management UI**. "User management" in StudentHub means understanding how `users` rows come into existence and what their `role`/`status` columns mean.

## The `users` table fields that matter

| Column | Values observed in code | Meaning |
|---|---|---|
| `role` | `admin`, `teacher`, `user` | `admin` unlocks `/admin/*` (with `status=approved`). `user` is the DB default but is never assigned by any controller — registration hardcodes `teacher` |
| `status` | `pending`, `approved`, `rejected` | `pending` = awaiting admin review; `approved` = can log in; `rejected` = cannot log in |

## How accounts are created

| Path | Result |
|---|---|
| Self-registration (`POST /register`) | `role=teacher`, `status=pending`, `email_verified_at=null` |
| `AdminSeeder` | One hardcoded admin: `role=admin`, `status=approved`, verified implicitly (no `email_verified_at` set — column stays null) |
| `TeacherSeeder` | 50 teachers `teacher01@studenthub.test`…`teacher50@studenthub.test`, `role=teacher`, `status=approved`, `email_verified_at=now()` (set via `forceFill` after `updateOrCreate`) |
| `UserFactory` (tests) | `role`/`status` **unset** → DB defaults `user`/`pending` |
| Direct SQL / Tinker | Anything goes — no DB constraint on `role`/`status` |

## What each role can actually do

| Capability | admin (approved) | teacher (approved) | user / pending / rejected |
|---|---|---|---|
| Log in | ✓ | ✓ | ✗ (approval gate) |
| Dashboard, profile | ✓ | ✓ | ✗ |
| Students CRUD, attendance | ✓ | ✓ | ✗ |
| `/admin/*` (subjects, exams, grades, stats, teacher approval) | ✓ | ✗ 403 | ✗ |

Teachers and admins have **identical** permissions outside `/admin/*` — there is no finer-grained authorization (no policies/Gates).

## Account lifecycle

```
register ──► pending ──► (admin approves) ──► approved ──► full teacher access
                │
                └──► (admin rejects) ──► rejected ──► locked out permanently
                       (no UI path back; admin must UPDATE the row manually)
```

- Approval/rejection is covered in [teacher-approval.md](teacher-approval.md).
- There is no "disable user", "reset another user's password", or "change role" feature.
- Deleting your own account is possible via `DELETE /profile` (password-confirmed); deleting a user nulls `attendances.marked_by`.

## Known gaps

- No admin UI to create/promote admins — `AdminSeeder` or SQL only.
- No notification (email or in-app) when a teacher is approved/rejected.
- `role='user'` exists as a DB default but no code path produces or handles it meaningfully — a `user`/`approved` account could log in and use teacher-level features.
