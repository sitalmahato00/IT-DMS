# IT-DMS Database ER Diagram (Hierarchical)

## Mermaid Diagram Code

```mermaid
graph TD
    A["<b>USERS</b><br/>id PK<br/>name<br/>email UK<br/>role<br/>password"]
    
    B["<b>TEACHERS</b><br/>id PK<br/>user_id FK<br/>teacher_code<br/>qualification"]
    
    C["<b>PARENTS</b><br/>id PK<br/>user_id FK<br/>parent_code<br/>occupation"]
    
    D["<b>STUDENTS</b><br/>id PK<br/>user_id FK<br/>parent_id FK<br/>roll_no<br/>semester"]
    
    E["<b>SUBJECTS</b><br/>id PK<br/>name<br/>code<br/>credits<br/>status"]
    
    F["<b>SEMESTERS</b><br/>id PK<br/>number<br/>academic_year<br/>status"]
    
    G["<b>SUBJECT_TEACHER</b><br/>id PK<br/>subject_id FK<br/>teacher_id FK<br/>role"]
    
    H["<b>SUBJECT_STUDENTS</b><br/>id PK<br/>subject_id FK<br/>student_id FK"]
    
    I["<b>TIMETABLE_SLOTS</b><br/>id PK<br/>subject_id FK<br/>teacher_id FK<br/>day<br/>start_time"]
    
    J["<b>EXAMS</b><br/>id PK<br/>subject_id FK<br/>name<br/>type<br/>full_marks"]
    
    K["<b>ATTENDANCE</b><br/>id PK<br/>student_id FK<br/>subject_id FK<br/>date<br/>status"]
    
    L["<b>MARKS</b><br/>id PK<br/>student_id FK<br/>subject_id FK<br/>marks"]
    
    M["<b>EXAM_MARKS</b><br/>id PK<br/>exam_id FK<br/>student_id FK<br/>marks<br/>grade"]
    
    N["<b>ELECTIVE_ENROLLMENTS</b><br/>id PK<br/>student_id FK<br/>subject_id FK<br/>status"]
    
    O["<b>STUDY_MATERIALS</b><br/>id PK<br/>subject_id FK<br/>teacher_id FK<br/>title"]
    
    P["<b>NOTICES</b><br/>id PK<br/>subject_id FK<br/>title<br/>status"]
    
    Q["<b>AUDIT_LOGS</b><br/>id PK<br/>user_id FK<br/>action"]
    
    A --> B
    A --> C
    A --> D
    A --> Q
    
    C --> D
    
    B --> G
    B --> I
    B --> O
    
    D --> H
    D --> K
    D --> L
    D --> M
    D --> N
    
    E --> G
    E --> H
    E --> J
    E --> K
    E --> L
    E --> N
    E --> O
    E --> P
    
    F --> I
    
    J --> M
    
    style A fill:#003d7a,stroke:#000,stroke-width:2px,color:#fff
    style B fill:#003d7a,stroke:#000,stroke-width:2px,color:#fff
    style C fill:#003d7a,stroke:#000,stroke-width:2px,color:#fff
    style D fill:#003d7a,stroke:#000,stroke-width:2px,color:#fff
    style E fill:#006699,stroke:#000,stroke-width:2px,color:#fff
    style F fill:#006699,stroke:#000,stroke-width:2px,color:#fff
    
    style G fill:#009900,stroke:#000,stroke-width:2px,color:#fff
    style H fill:#009900,stroke:#000,stroke-width:2px,color:#fff
    style I fill:#009900,stroke:#000,stroke-width:2px,color:#fff
    
    style J fill:#cc6600,stroke:#000,stroke-width:2px,color:#fff
    style K fill:#cc6600,stroke:#000,stroke-width:2px,color:#fff
    style L fill:#cc6600,stroke:#000,stroke-width:2px,color:#fff
    style M fill:#cc6600,stroke:#000,stroke-width:2px,color:#fff
    
    style N fill:#993366,stroke:#000,stroke-width:2px,color:#fff
    style O fill:#999900,stroke:#000,stroke-width:2px,color:#fff
    style P fill:#999900,stroke:#000,stroke-width:2px,color:#fff
    
    style Q fill:#666666,stroke:#000,stroke-width:2px,color:#fff
```

## How to View

### Online
- Use [Mermaid Live Editor](https://mermaid.live/)
- Paste the diagram code above

### VS Code
- Install "Markdown Preview Mermaid Support" extension
- Open this file in VS Code preview

### GitHub
- Upload this file to GitHub - Mermaid renders automatically

## Entity Layers

### Layer 1: User Management (Dark Blue)
- **USERS**: Core user entity with role-based access
- **TEACHERS**: Teacher profile
- **PARENTS**: Parent profile
- **STUDENTS**: Student profile
- **AUDIT_LOGS**: System audit trail

### Layer 2: Academic Core (Light Blue)
- **SUBJECTS**: All subjects offered
- **SEMESTERS**: Semester information

### Layer 3: Relationship Tables (Green)
- **SUBJECT_TEACHER**: Maps teachers to subjects
- **SUBJECT_STUDENTS**: Maps students to subjects
- **TIMETABLE_SLOTS**: Class scheduling

### Layer 4: Assessment (Orange)
- **EXAMS**: Exam details
- **EXAM_MARKS**: Exam results
- **MARKS**: General marks
- **ATTENDANCE**: Attendance tracking

### Layer 5: Operations (Purple & Yellow)
- **ELECTIVE_ENROLLMENTS**: Student elective requests
- **STUDY_MATERIALS**: Course materials
- **NOTICES**: System notices

## Relationships Summary

| From | To | Type | Description |
|------|-----|------|-------------|
| USERS | TEACHERS | 1:1 | Each teacher has one user account |
| USERS | PARENTS | 1:1 | Each parent has one user account |
| USERS | STUDENTS | 1:1 | Each student has one user account |
| USERS | AUDIT_LOGS | 1:N | Each user can have multiple audit logs |
| PARENTS | STUDENTS | 1:N | Each parent can have multiple children |
| TEACHERS | SUBJECT_TEACHER | 1:N | Each teacher can teach multiple subjects |
| TEACHERS | TIMETABLE_SLOTS | 1:N | Each teacher has multiple slots |
| TEACHERS | STUDY_MATERIALS | 1:N | Each teacher uploads multiple materials |
| STUDENTS | SUBJECT_STUDENTS | 1:N | Each student enrolls in multiple subjects |
| STUDENTS | ATTENDANCE | 1:N | Each student has multiple attendance records |
| STUDENTS | MARKS | 1:N | Each student has multiple marks |
| STUDENTS | EXAM_MARKS | 1:N | Each student takes multiple exams |
| STUDENTS | ELECTIVE_ENROLLMENTS | 1:N | Each student can enroll in multiple electives |
| SUBJECTS | SUBJECT_TEACHER | 1:N | Each subject is taught by multiple teachers |
| SUBJECTS | SUBJECT_STUDENTS | 1:N | Each subject has multiple students |
| SUBJECTS | EXAMS | 1:N | Each subject can have multiple exams |
| SUBJECTS | ATTENDANCE | 1:N | Each subject tracks multiple attendances |
| SUBJECTS | MARKS | 1:N | Each subject has multiple marks |
| SUBJECTS | TIMETABLE_SLOTS | 1:N | Each subject has multiple time slots |
| SUBJECTS | STUDY_MATERIALS | 1:N | Each subject has multiple study materials |
| SUBJECTS | ELECTIVE_ENROLLMENTS | 1:N | Each subject can be enrolled as elective |
| SUBJECTS | NOTICES | 1:N | Each subject can have multiple notices |
| SEMESTERS | TIMETABLE_SLOTS | 1:N | Each semester has multiple timetable slots |
| EXAMS | EXAM_MARKS | 1:N | Each exam has multiple marks |
