# Installation

Step-by-step guide to get StudentHub running locally. Commands are given for **Windows PowerShell**; bash equivalents are noted where they differ.

---

## 1. Prerequisites

| Requirement | Version | Notes |
|---|---|---|
| PHP | **8.3+** | `composer.json` requires `^8.3`. Extensions needed: `pdo`, `pdo_mysql`, `mbstring`, `xml`, `ctype`, `json`, `tokenizer`, `openssl` |
| Composer | 2.x | Dependency manager |
| MySQL | **8.0+** | Officially supported database engine. Must be running before migrations |
| Node.js + npm | 18+ / 9+ | For Vite asset compilation |
| Git | any | To clone the repository |

Verify your toolchain:

```powershell
php -v
composer -V
node -v
npm -v
mysql --version
```

---

## 2. Clone and install PHP dependencies

```powershell
git clone https://github.com/Johnny-Emad/StudentHub.git
cd StudentHub
composer install
```

> Alternatively, `composer setup` runs install + `.env` copy + key generation + migrations + npm build in one shot — but only after you have configured `.env` and created the database (see below), because `composer setup` runs `php artisan migrate --force`.

---

## 3. Environment file

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Then edit `.env`. The values that matter for a local install:

```dotenv
APP_NAME=StudentHub
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studenthub
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

See [configuration.md](configuration.md) for the full variable reference.

---

## 4. Create the database

MySQL must be running. Create the schema:

```powershell
mysql -u root -p -e "CREATE DATABASE studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

## 5. Run migrations

```powershell
php artisan migrate
```

> **Known blocker:** as of this writing, a fresh `php artisan migrate` **fails** on the `2026_11_16_223616_add_notes_to_students_table` migration because the `students.notes` column is already created by `2026_11_03_182612_create_students_table`. See [project-status.md](../project-status.md) and [troubleshooting.md](troubleshooting.md) for details and workarounds. If your database was migrated before `notes` was added to the create migration, your existing database is unaffected.

---

## 6. Seed the database

Two options:

```powershell
# Option A — admin account only (recommended for development)
php artisan db:seed --class=AdminSeeder

# Option B — full demo dataset: admin + 50 teachers + 10,000 students
# + 3 subjects, ~87 exams, exam grades, and attendance records.
# This is heavy and slow; the large seeders skip themselves if their
# tables already contain data.
php artisan db:seed
```

`DatabaseSeeder` calls, in order: `AdminSeeder` → `SubjectSeeder` → `TeacherSeeder` → `StudentSeeder` → `ExamSeeder` → `ExamGradeSeeder` → `AttendanceSeeder`.

> **Security warning:** `AdminSeeder` hardcodes an admin email and password in the repository. Do not deploy seeded credentials to any shared or production environment — see [project-status.md](../project-status.md).

---

## 7. Frontend assets

```powershell
npm install
npm run build
```

For local development with hot reload, run `npm run dev` in a second terminal instead of (or alongside) the build.

---

## 8. Start the application

```powershell
php artisan serve
```

The app is available at `http://localhost:8000`. If assets were built, no Vite dev server is needed. To run server + queue + logs + Vite together:

```powershell
composer run dev
```

Log in with the seeded admin account (created by `AdminSeeder`) to reach the admin area at `/admin/*`.
