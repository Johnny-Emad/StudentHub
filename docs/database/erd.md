# Entity Relationship Diagram

Application schema only (framework tables omitted). Generated from the migrations and Eloquent models.

```mermaid
erDiagram
    USERS ||--o{ ATTENDANCES : "marked_by"
    STUDENTS ||--o{ ATTENDANCES : "student_id"
    SUBJECTS ||--o{ GRADE_SUBJECT : "subject_id"
    SUBJECTS ||--o{ EXAMS : "subject_id"
    EXAMS ||--o{ EXAM_GRADES : "exam_id"
    STUDENTS ||--o{ EXAM_GRADES : "student_id"

    USERS {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role "admin|teacher|user"
        string status "pending|approved|rejected"
        remember_token
        timestamps
    }

    STUDENTS {
        bigint id PK
        string student_code UK
        string first_name
        string last_name
        string email UK
        string phone
        date date_of_birth
        string gender
        text address
        tinyint_unsigned grade "1-12"
        char class_section "A-Z"
        string status "active|inactive"
        text notes
        timestamps
    }

    ATTENDANCES {
        bigint id PK
        bigint student_id FK
        date date
        enum status "present|absent"
        bigint marked_by FK
        timestamps
    }

    SUBJECTS {
        bigint id PK
        string name UK
        text description
        timestamps
    }

    GRADE_SUBJECT {
        bigint id PK
        bigint subject_id FK
        string grade
        timestamps
    }

    EXAMS {
        bigint id PK
        bigint subject_id FK
        string title
        string grade
        string class_section
        date exam_date
        decimal total_marks
        timestamps
    }

    EXAM_GRADES {
        bigint id PK
        bigint exam_id FK
        bigint student_id FK
        decimal marks_obtained
        timestamps
    }
```

## Reading the diagram

- **Solid FK lines** are real database foreign keys (`foreignId()->constrained()`).
- **`(grade, class_section)` is a logical key, not a FK.** Exams, attendance queries, and statistics all match students to exams/sections by comparing `students.grade`/`class_section` values — there is no `classes` table enforcing referential integrity.
- **`grade_subject` is a real pivot** but only links `subject_id → grade` (a string), not to any class entity.
- **`users` connects to the academic schema only through `attendances.marked_by`** — teachers/admins have no direct relationship to students, subjects, or exams. Any approved user can perform any non-admin operation.
