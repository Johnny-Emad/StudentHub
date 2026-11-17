# Route Reference

All routes are defined in `routes/web.php` and `routes/auth.php`. There is no API (`routes/api.php` does not exist).

Middleware legend:

- **guest** — only unauthenticated users
- **auth** — any authenticated user (teacher or admin)
- **admin** — authenticated user with `role = 'admin'` **and** `status = 'approved'` (enforced by `AdminMiddleware`)

---

## Public / Guest Routes

| Method | URI | Name | Controller@Action | Middleware | Purpose |
|---|---|---|---|---|---|
| GET | `/` | — | closure → `welcome` view | — | Landing page |
| GET | `/register` | `register` | `RegisteredUserController@create` | guest | Registration form |
| POST | `/register` | — | `RegisteredUserController@store` | guest | Create teacher account (`role=teacher`, `status=pending`) |
| GET | `/login` | `login` | `AuthenticatedSessionController@create` | guest | Login form |
| POST | `/login` | — | `AuthenticatedSessionController@store` | guest | Authenticate (approval gate in `LoginRequest`) |
| GET | `/forgot-password` | `password.request` | `PasswordResetLinkController@create` | guest | Reset request form |
| POST | `/forgot-password` | `password.email` | `PasswordResetLinkController@store` | guest | Send reset link |
| GET | `/reset-password/{token}` | `password.reset` | `NewPasswordController@create` | guest | Reset form |
| POST | `/reset-password` | `password.store` | `NewPasswordController@store` | guest | Update password via token |

---

## Authenticated Routes (`auth`)

| Method | URI | Name | Controller@Action | Purpose |
|---|---|---|---|---|
| GET | `/dashboard` | `dashboard` | `DashboardController@index` | Main dashboard statistics |
| GET | `/profile` | `profile.edit` | `ProfileController@edit` | Edit profile form |
| PATCH | `/profile` | `profile.update` | `ProfileController@update` | Update name/email |
| DELETE | `/profile` | `profile.destroy` | `ProfileController@destroy` | Delete account |
| GET | `/verify-email` | `verification.notice` | `EmailVerificationPromptController` | Verification notice (not enforced — `MustVerifyEmail` is commented out) |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | `VerifyEmailController` | Signed email verification link |
| POST | `/email/verification-notification` | `verification.send` | `EmailVerificationNotificationController@store` | Resend verification email |
| GET | `/confirm-password` | `password.confirm` | `ConfirmablePasswordController@show` | Password confirmation |
| POST | `/confirm-password` | — | `ConfirmablePasswordController@store` | Confirm password |
| PUT | `/password` | `password.update` | `PasswordController@update` | Change password |
| POST | `/logout` | `logout` | `AuthenticatedSessionController@destroy` | Log out |

### Students (`Route::resource('students')` — full resource including `show`)

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/students` | `students.index` | List with search/filters, paginated |
| GET | `/students/create` | `students.create` | Create form |
| POST | `/students` | `students.store` | Store student |
| GET | `/students/{student}` | `students.show` | Student detail |
| GET | `/students/{student}/edit` | `students.edit` | Edit form |
| PUT/PATCH | `/students/{student}` | `students.update` | Update student |
| DELETE | `/students/{student}` | `students.destroy` | Delete student (cascades attendance + exam grades) |

### Attendance

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/attendance` | `attendance.index` | Daily marking sheet (date, grade, section filters) |
| POST | `/attendance` | `attendance.store` | Bulk-save attendance for a date |
| PATCH | `/attendance/{attendance}` | `attendance.update` | Update a single record |
| GET | `/attendance/history` | `attendance.history` | Historical records with filters |
| GET | `/attendance/dashboard` | `attendance.dashboard` | Attendance statistics |

---

## Admin Routes (`auth` + `admin`, prefix `/admin`, name prefix `admin.`)

### Teacher Approval

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/teachers` | `admin.teachers.index` | List pending teacher accounts |
| PATCH | `/admin/teachers/{user}/approve` | `admin.teachers.approve` | Approve teacher |
| PATCH | `/admin/teachers/{user}/reject` | `admin.teachers.reject` | Reject teacher |
| PATCH | `/admin/teachers/{user}/{status}` | `admin.teachers.update` | Generic status update (used by the view) |

### Statistics

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/subjects/statistics` | `admin.subjects.statistics` | Per-subject statistics |
| GET | `/admin/statistics/classes` | `admin.statistics.classes` | Per-class statistics |
| GET | `/admin/statistics/grades` | `admin.statistics.grades` | Per-grade statistics |
| GET | `/admin/statistics/school` | `admin.statistics.school` | School-wide statistics (optional date range) |

### Subjects (`Route::resource('subjects')->except(['show'])`)

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/subjects` | `admin.subjects.index` | List subjects |
| GET | `/admin/subjects/create` | `admin.subjects.create` | Create form |
| POST | `/admin/subjects` | `admin.subjects.store` | Store subject |
| GET | `/admin/subjects/{subject}/edit` | `admin.subjects.edit` | Edit form |
| PUT/PATCH | `/admin/subjects/{subject}` | `admin.subjects.update` | Update subject |
| DELETE | `/admin/subjects/{subject}` | `admin.subjects.destroy` | Delete subject (cascades grade assignments + exams) |
| GET | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.edit` | Edit grade assignments |
| PUT | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.update` | Save grade assignments |

### Exams (`Route::resource('exams')->except(['show'])`)

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/exams` | `admin.exams.index` | List exams (paginated, by exam date) |
| GET | `/admin/exams/create` | `admin.exams.create` | Create form |
| POST | `/admin/exams` | `admin.exams.store` | Store exam (cross-validated) |
| GET | `/admin/exams/{exam}/edit` | `admin.exams.edit` | Edit form |
| PUT/PATCH | `/admin/exams/{exam}` | `admin.exams.update` | Update exam |
| DELETE | `/admin/exams/{exam}` | `admin.exams.destroy` | Delete exam (cascades exam grades) |
| GET | `/admin/exams/{exam}/students` | `admin.exams.students` | Preview target students for the exam's class |

### Exam Grades

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/admin/exams/{exam}/grades` | `admin.exams.grades.create` | Bulk grade entry sheet (graded/not-graded filter) |
| POST | `/admin/exams/{exam}/grades` | `admin.exams.grades.store` | Bulk upsert grades |
| GET | `/admin/exams/{exam}/grades/{examGrade}/edit` | `admin.exams.grades.edit` | Edit single grade |
| PUT | `/admin/exams/{exam}/grades/{examGrade}` | `admin.exams.grades.update` | Update single grade |

---

## Route Ordering Notes

- `/admin/subjects/statistics`, `/admin/exams/{exam}/students`, and the `/admin/exams/{exam}/grades*` routes are registered **before** the `subjects`/`exams` resource routes so the literal segments are not captured by `{subject}`/`{exam}` parameters.
- `students` is the only resource that includes a `show` route; `subjects` and `exams` exclude it.
- `PATCH /admin/teachers/{user}/{status}` overlaps with the approve/reject routes; the view uses the generic `update` route.