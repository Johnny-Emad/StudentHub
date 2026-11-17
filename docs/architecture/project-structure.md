# Project Structure

Annotated map of the repository. Paths are relative to the project root.

```
StudentHub/
├── app/                              # Application source (PSR-4: App\)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── TeacherApprovalController.php   # pending teacher list, approve/reject
│   │   │   ├── Auth/                 # 9 Breeze controllers (login, register, password, verification)
│   │   │   ├── AttendanceController.php            # mark / update / history / dashboard
│   │   │   ├── ClassStatisticsController.php       # per-class stats (admin)
│   │   │   ├── DashboardController.php             # landing stats (cached 60s)
│   │   │   ├── ExamController.php                  # exam CRUD + target students (admin)
│   │   │   ├── ExamGradeController.php             # grade entry/edit (admin)
│   │   │   ├── GradeStatisticsController.php       # per-grade stats (admin)
│   │   │   ├── ProfileController.php               # own profile edit/delete
│   │   │   ├── SchoolStatisticsController.php      # school-wide stats + date filter (admin)
│   │   │   ├── StudentController.php               # student CRUD + search/filters
│   │   │   ├── SubjectController.php               # subject CRUD + grade assignment (admin)
│   │   │   ├── SubjectStatisticsController.php     # per-subject stats (admin)
│   │   │   └── Controller.php                      # base controller (empty)
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php                 # role=admin + status=approved gate
│   │   └── Requests/
│   │       ├── Auth/LoginRequest.php               # credentials + rate limit + approval gate
│   │       └── ProfileUpdateRequest.php            # name/email rules
│   ├── Models/                     # User, Student, Attendance, Subject, GradeSubject, Exam, ExamGrade
│   ├── Providers/AppServiceProvider.php            # empty (no bindings)
│   └── View/Components/            # AppLayout, GuestLayout (anonymous components)
│
├── bootstrap/
│   ├── app.php                     # Application::configure — routes, middleware alias, exceptions
│   └── providers.php
│
├── config/                         # Standard Laravel config; database.php defaults to sqlite fallback
│
├── database/
│   ├── factories/                  # Factories for all 7 models
│   ├── migrations/                 # 12 migrations (3 Laravel defaults + 9 app)
│   └── seeders/                    # AdminSeeder + 6 demo-data seeders
│
├── docs/                           # This documentation
│
├── public/                         # index.php, .htaccess, built assets (public/build)
│
├── resources/
│   ├── css/app.css                 # Tailwind directives + custom component classes
│   ├── js/app.js                   # Alpine + axios bootstrap
│   └── views/
│       ├── admin/teachers/         # Teacher approval UI
│       ├── attendance/             # index, history, dashboard
│       ├── auth/                   # Breeze auth views
│       ├── components/             # Breeze Blade components (app-layout, nav-link, …)
│       ├── exams/                  # index, create, edit, students, grades, grade-edit
│       ├── layouts/                # app, guest, navigation
│       ├── profile/                # edit, partials
│       ├── statistics/             # classes, grades, school
│       ├── students/               # index, create, edit, show
│       ├── subjects/               # index, create, edit, grades, statistics
│       ├── dashboard.blade.php
│       └── welcome.blade.php       # landing page
│
├── routes/
│   ├── web.php                     # ALL application routes
│   ├── auth.php                    # Breeze auth routes
│   └── console.php                 # inspire command only
│
├── storage/                        # logs, sessions (database driver), cache, uploads (unused)
├── tests/
│   ├── Feature/Auth/               # 6 Breeze auth test files
│   ├── Feature/ProfileTest.php
│   ├── Feature/ExampleTest.php
│   ├── Unit/ExampleTest.php
│   ├── Pest.php                    # RefreshDatabase bound to Feature suite
│   └── TestCase.php
│
├── artisan
├── composer.json                   # PHP ^8.3, Laravel ^13.17, Pest 4
├── package.json                    # Vite 8, Tailwind 3, Alpine 3
├── phpunit.xml                     # sqlite :memory: for tests
├── vite.config.js
└── tailwind.config.js
```

## Conventions worth knowing

- **Attribute-based model config** — `#[Fillable]` / `#[Hidden]` PHP attributes instead of protected properties.
- **Inline validation** — most controllers call `$request->validate()` directly; Form Requests exist only for login and profile update.
- **`DB::table()` in statistics** — the four statistics controllers bypass Eloquent and use the query builder with `selectRaw`/`join` for aggregate queries.
- **No API, no queues, no events beyond Breeze's `Registered`** — the app is entirely synchronous request/response.
