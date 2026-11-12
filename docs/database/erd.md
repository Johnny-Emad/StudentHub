# Entity Relationship Diagram

The following diagram represents all application-defined tables and their relationships. Laravel system tables (`cache`, `jobs`, `sessions`, `password_reset_tokens`) are omitted for clarity.

---

## Mermaid ER Diagram

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role
        string status
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    students {
        bigint id PK
        string student_code UK
        string first_name
        string last_name
        string email
        string phone
        date date_of_birth
        string gender
        text address
        string grade
        string class_section
        string status
        timestamp created_at
        timestamp updated_at
    }

    attendances {
        bigint id PK
        bigint student_id FK
        date date
        enum status
        bigint marked_by FK
        timestamp created_at
        timestamp updated_at
    }

    subjects {
        bigint id PK
        string name UK
        text description
        timestamp created_at
        timestamp updated_at
    }

    grade_subject {
        bigint id PK
        bigint subject_id FK
        string grade
        timestamp created_at
        timestamp updated_at
    }

    exams {
        bigint id PK
        bigint subject_id FK
        string title
        string grade
        string class_section
        date exam_date
        decimal total_marks
        timestamp created_at
        timestamp updated_at
    }

    exam_grades {
        bigint id PK
        bigint exam_id FK
        bigint student_id FK
        decimal marks_obtained
        timestamp created_at
        timestamp updated_at
    }

    students ||--o{ attendances : "has many"
    users ||--o{ attendances : "marks (marked_by)"
    subjects ||--o{ grade_subject : "assigned to grades via"
    subjects ||--o{ exams : "has many"
    exams ||--o{ exam_grades : "has many"
    students ||--o{ exam_grades : "receives"
```

---

## Relationship Descriptions

### `students` → `attendances` (One-to-Many)

Each student can have many attendance records (one per day). The unique constraint `(student_id, date)` ensures only one attendance record exists per student per day.

### `users` → `attendances` (One-to-Many, optional)

The `marked_by` column records which authenticated user marked the attendance. This is nullable — if the user account is deleted, the `marked_by` value is set to `NULL` (nullOnDelete constraint). The marking user does not need to be an admin; any authenticated user (teacher) can mark attendance.

### `subjects` → `grade_subject` (One-to-Many)

A subject can be assigned to multiple grade levels. Each `grade_subject` row represents the assignment of one subject to one grade string. This is maintained as a full model rather than a simple pivot, allowing for future extension.

### `subjects` → `exams` (One-to-Many)

Each exam belongs to exactly one subject. Deleting a subject cascades to delete all its exams (and by extension, all exam grade records for those exams).

### `exams` → `exam_grades` (One-to-Many)

Each exam can have many grade records, one per student. The unique constraint `(exam_id, student_id)` prevents duplicate grading.

### `students` → `exam_grades` (One-to-Many)

Each student can receive many exam grade records across different exams.

---

## Cross-Table Logic (Not Enforced by Foreign Keys)

The following relationships are enforced at the **application layer**, not by database foreign keys:

| Constraint | Where enforced |
|---|---|
| An exam's `grade`+`class_section` must match real students | `ExamController::validateExam()` — `Rule::exists('students', ...)` |
| An exam's `grade` must be assigned to its subject via `grade_subject` | `ExamController::validateExam()` — `Rule::exists('grade_subject', ...)` |
| Exam grades can only be entered for students in the exam's grade+section | `ExamGradeController::store()` — queries students by grade+section |

There is no foreign key between `exams.grade` / `exams.class_section` and the `students` table. The referential integrity here is entirely application-enforced.
