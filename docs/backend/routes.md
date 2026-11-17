# Routes

Complete route reference. Web routes only — there is no `routes/api.php`. Source: `routes/web.php` + `routes/auth.php`.

## Public / guest

| Method | URI | Name | Controller / Action | Middleware |
|---|---|---|---|---|
| GET | `/` | — | `view('welcome')` | — |
| GET | `/register` | `register` | `RegisteredUserController@create` | guest |
| POST | `/register` | — | `RegisteredUserController@store` | guest |
| GET | `/login` | `login` | `AuthenticatedSessionController@create` | guest |
| POST | `/login` | — | `AuthenticatedSessionController@store` | guest |
| GET | `/forgot-password` | `password.request` | `PasswordResetLinkController@create` | guest |
| POST | `/forgot-password` | `password.email` | `PasswordResetLinkController@store` | guest |
| GET | `/reset-password/{token}` | `password.reset` | `NewPasswordController@create` | guest |
| POST | `/reset-password` | `password.store` | `NewPasswordController@store` | guest |
| GET | `/up` | — | health check | — |

## Authenticated — shared (`auth` middleware)

| Method | URI | Name | Controller | Notes |
|---|---|---|---|---|
| GET | `/dashboard` | `dashboard` | `DashboardController@index` | cached stats |
| GET | `/profile` | `profile.edit` | `ProfileController@edit` | |
| PATCH | `/profile` | `profile.update` | `ProfileController@update` | |
| DELETE | `/profile` | `profile.destroy` | `ProfileController@destroy` | password-confirmed |
| GET | `/students` | `students.index` | `StudentController@index` | search/filters |
| GET | `/students/create` | `students.create` | `StudentController@create` | |
| POST | `/students` | `students.store` | `StudentController@store` | |
| GET | `/students/{student}` | `students.show` | `StudentController@show` | |
| GET | `/students/{student}/edit` | `students.edit` | `StudentController@edit` | |
| PUT/PATCH | `/students/{student}` | `students.update` | `StudentController@update` | |
| DELETE | `/students/{student}` | `students.destroy` | `StudentController@destroy` | |
| GET | `/attendance` | `attendance.index` | `AttendanceController@index` | marking sheet |
| POST | `/attendance` | `attendance.store` | `AttendanceController@store` | bulk save |
| PATCH | `/attendance/{attendance}` | `attendance.update` | `AttendanceController@update` | single record |
| GET | `/attendance/history` | `attendance.history` | `AttendanceController@history` | |
| GET | `/attendance/dashboard` | `attendance.dashboard` | `AttendanceController@dashboard` | |

## Authenticated — auth scaffolding (`auth` middleware, `routes/auth.php`)

| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/verify-email` | `verification.notice` | verification notice |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | signed + throttle:6,1 |
| POST | `/email/verification-notification` | `verification.send` | throttle:6,1 |
| GET | `/confirm-password` | `password.confirm` | password confirmation |
| POST | `/confirm-password` | — | |
| PUT | `/password` | `password.update` | change own password |
| POST | `/logout` | `logout` | |

## Admin area (`auth` + `admin` middleware; prefix `/admin`, name `admin.`)

### Teacher approval

| Method | URI | Name |
|---|---|---|
| GET | `/admin/teachers` | `admin.teachers.index` |
| PATCH | `/admin/teachers/{user}/approve` | `admin.teachers.approve` |
| PATCH | `/admin/teachers/{user}/reject` | `admin.teachers.reject` |
| PATCH | `/admin/teachers/{user}/{status}` | `admin.teachers.update` |

### Subjects

| Method | URI | Name |
|---|---|---|
| GET | `/admin/subjects` | `admin.subjects.index` |
| GET | `/admin/subjects/create` | `admin.subjects.create` |
| POST | `/admin/subjects` | `admin.subjects.store` |
| GET | `/admin/subjects/{subject}/edit` | `admin.subjects.edit` |
| PUT/PATCH | `/admin/subjects/{subject}` | `admin.subjects.update` |
| DELETE | `/admin/subjects/{subject}` | `admin.subjects.destroy` |
| GET | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.edit` |
| PUT | `/admin/subjects/{subject}/grades` | `admin.subjects.grades.update` |

### Statistics

| Method | URI | Name |
|---|---|---|
| GET | `/admin/subjects/statistics` | `admin.subjects.statistics` |
| GET | `/admin/statistics/classes` | `admin.statistics.classes` |
| GET | `/admin/statistics/grades` | `admin.statistics.grades` |
| GET | `/admin/statistics/school` | `admin.statistics.school` |

### Exams & grades

| Method | URI | Name |
|---|---|---|
| GET | `/admin/exams` | `admin.exams.index` |
| GET | `/admin/exams/create` | `admin.exams.create` |
| POST | `/admin/exams` | `admin.exams.store` |
| GET | `/admin/exams/{exam}/edit` | `admin.exams.edit` |
| PUT/PATCH | `/admin/exams/{exam}` | `admin.exams.update` |
| DELETE | `/admin/exams/{exam}` | `admin.exams.destroy` |
| GET | `/admin/exams/{exam}/students` | `admin.exams.students` |
| GET | `/admin/exams/{exam}/grades` | `admin.exams.grades.create` |
| POST | `/admin/exams/{exam}/grades` | `admin.exams.grades.store` |
| GET | `/admin/exams/{exam}/grades/{examGrade}/edit` | `admin.exams.grades.edit` |
| PUT | `/admin/exams/{exam}/grades/{examGrade}` | `admin.exams.grades.update` |

## Route-model binding notes

- `students`, `attendance`, `subject`, `exam`, `examGrade`, `user` parameters use implicit binding.
- `admin.teachers.update` (`{status}`) is a **catch-all-ish** PATCH — `{status}` accepts any string; the controller 404s unless it's `approved`/`rejected`.
- Resource `show` actions are excluded for `subjects` and `exams` only; `students` has a working `show`.
