# IT Department Management System (IT-DMS) - Comprehensive Project Documentation

## 1. System Overview

The **IT Department Management System (IT-DMS)** is a comprehensive, Laravel-based academic management system specifically designed to handle the core workflows of an Information Technology department. It serves as a centralized hub bridging communication and administration between administrators, teachers, students, and parents.

### 1.1 Core Objectives
- **Digital Transformation**: Transition traditional paper-based attendance, grading, and notice board systems to a digital platform.
- **Improved Communication**: Provide dedicated portals for all stakeholders (Admin, Teacher, Student, Parent) with role-specific views and notifications.
- **Academic Tracking**: Comprehensive monitoring of student performance, attendance, and exam results.
- **Resource Management**: Centralized repository for study materials, timetables, and department galleries.
- **Bilingual Interface**: Support for both English and Nepali languages and calendars (AD/BS conversion).

---

## 2. Technology Stack

### 2.1 Backend Architecture
- **Framework:** Laravel 11.x
- **Programming Language:** PHP 8.2+
- **Database:** MySQL 8.0+ / MariaDB
- **Authentication:** Laravel Sanctum (Token-based API capabilities)
- **Session Management:** Standard Laravel session handling

### 2.2 Frontend Architecture
- **CSS Framework:** Tailwind CSS 3.1+
- **JavaScript Framework:** Alpine.js 3.4+ (for lightweight DOM manipulation and state management)
- **Build Tool:** Vite (for fast, optimized asset compilation)
- **Templating Engine:** Blade Templates

### 2.3 Additional Libraries & Integrations
- **PDF Generation:** `barryvdh/laravel-dompdf` (used for generating printable marksheets and reports)
- **Data Visualization:** `Chart.js` 4.5+ (used on Admin and Teacher components for analytics)
- **Nepali Localization:** `anuzpandey/laravel-nepali-date` & `nepali-date-picker`
- **HTTP Client:** `GuzzleHTTP`

---

## 3. Role-Based Access Control (RBAC) & Portals

The IT-DMS utilizes a strict, middleware-protected RBAC system. The `users` table holds the authentication credentials, while specific role definitions map users to deeper profile tables (`students`, `teachers`, `parents`).

### 3.1 Administrator (`admin`)
- **Access Level**: Unrestricted CRUD (Create, Read, Update, Delete) access to the entire system.
- **Key Responsibilities**:
  - Manage user accounts (adding new teachers, students, and parents).
  - Configure academic structure (Semesters, Subjects, and Electives).
  - Manage system-wide records (Audit logs, Department settings, Notice boards).
  - Oversee all academic tracking (Final reports, Attendance overrides).

### 3.2 Teacher (`teacher`)
- **Access Level**: Restricted to assigned subjects and students.
- **Key Responsibilities**:
  - Mark and edit daily subject attendance (Class & Lab).
  - Input exam marks and assessments.
  - Upload study materials for their specific assigned subjects.
  - View subject-specific analytical reports.

### 3.3 Student (`student`)
- **Access Level**: Read-only access to their specific academic records.
- **Key Responsibilities**:
  - View personal timetable and attendance records.
  - Download uploaded study materials.
  - Check exam results and download digital marksheets.
  - Read department notices.

### 3.4 Parent (`parent`)
- **Access Level**: Read-only tracking mapped specifically to their linked child(ren).
- **Key Responsibilities**:
  - Track their child’s attendance mapping.
  - Monitor exam results and academic performance.
  - Receive critical alerts and notifications.

---

## 4. Functional Modules

### 4.1 Academic Ecosystem
- **Semester Management**: Organizes the flow of academic progression (1st through 8th Semesters).
- **Subject/Course Management**: Defines core vs. elective subjects, credit hours, and maps them to semesters.
- **Timetable**: Defines class schedules linking subjects, teachers, and time slots.

### 4.2 Attendance Tracking
- **Types**: Tracks both **Class Attendance** and **Lab Attendance**.
- **Statuses**: Present, Absent, Leave.
- **Capabilities**: Real-time AD/BS date handling. Validates data to ensure accurate historical records even if a student’s active status changes to 'alumni'. Both Admin and Teacher can override and print reports.

### 4.3 Examination & Grading
- **Assessments**: Supports complex assessment structures (Internal, Mid-term, Final).
- **Ledger & Marksheet**: Teachers input marks, causing the system to automatically compute GPAs, grades, and remarks based on customizable criteria.
- **Exporting**: Exam data and marksheets can be exported to PDF and CSV.

### 4.4 Communication & Resources
- **Notice Board**: Department-wide or role-specific announcements.
- **Study Materials**: Contextual file uploads restricted by semester and subject context.
- **Notifications**: Internal alerts tracking low attendance, newly published exam results, etc.
- **Gallery**: Department multimedia tracking.

---

## 5. Database Schema & Entity Relationships

Below is the definitive schema for the core operational tables within IT-DMS.

### 5.1 Authentication & User Management

#### `users`
The core authentication table linking credentials to roles.
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `name` | String | Full name of the user |
| `email` | String | Unique email address (login credential) |
| `password` | String | Bcrypt hashed password |
| `role` | Enum | Defines access (`admin`, `teacher`, `student`, `parent`) |
| `status` | Enum | `active`, `inactive` |

#### `students`
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `user_id` | BigInt | Foreign Key (`users.id`) |
| `roll_no` | String | Department roll number / Registration number |
| `semester` | Integer | Currently enrolled semester |
| `parent_id` | BigInt | Foreign Key (`parents.id` - nullable) |
| `is_alumni` | Boolean | True if the student has graduated |

#### `teachers`
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `user_id` | BigInt | Foreign Key (`users.id`) |
| `teacher_code` | String | Unique employee identifier |
| `designation` | String | Job title (e.g., Professor, Lab Assistant) |

#### `parents`
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `user_id` | BigInt | Foreign Key (`users.id`) |
| `phone_number` | String | Primary contact number |

### 5.2 Academic Structure

#### `subjects`
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `subject_code` | String | Unique course code (e.g., IT-301) |
| `subject_name` | String | Name of the course |
| `semester` | Integer | The semester this course belongs to |
| `credit_hours` | Integer | Academic weight of the course |
| `is_elective` | Boolean | Determines if the course is optional |
| `has_lab` | Boolean | Determines if the subject requires lab attendance |

#### `subject_teachers` (Pivot)
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `subject_id` | BigInt | Foreign Key (`subjects.id`) |
| `teacher_id` | BigInt | Foreign Key (`teachers.id` or `users.id`) |
| `semester` | Integer | Contextual semester assignment |

### 5.3 Daily Operations

#### `attendance`
*Recently updated to support strict historical mapping bypassing volatile rosters.*
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `student_id` | BigInt | Foreign Key (`students.id`) |
| `subject_id` | BigInt | Foreign Key (`subjects.id`) |
| `date` | Date | Gregorian Date (AD) |
| `date_bs` | String | Nepali Date (BS) |
| `status` | Enum | `present`, `absent`, `leave` |
| `attendance_type` | Enum | `class`, `lab` |
| `remarks` | Text | Optional note from teacher |

#### `marks` / `exam_marks`
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `student_id` | BigInt | Foreign Key (`students.id`) |
| `subject_id` | BigInt | Foreign Key (`subjects.id`) |
| `exam_type` | String | (e.g., Mid-term, Final, Internal) |
| `marks_obtained` | Decimal| Numeric score |
| `grade` | String | Calculated letter grade |

#### `study_materials`
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `title` | String | Document title |
| `subject_id` | BigInt | Foreign Key (`subjects.id`) |
| `teacher_id` | BigInt | Foreign Key (`teachers.id`) |
| `file_path` | String | Disk location of the uploaded file |

### 5.4 System & Logging

#### `audit_logs`
Tracks major database mutations for accountability.
| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `user_id` | BigInt | User who performed the action |
| `action` | String | (e.g., `created`, `updated`, `deleted`) |
| `model` | String | The model affected (e.g., `Attendance`) |
| `changes` | JSON | Payload of modified columns before/after |

---

## 6. Deployment & Maintenance

- **Backups**: The database should be backed up recursively using standard `mysqldump` processes.
- **Queue Workers**: To ensure email notifications and heavy PDF generation tasks do not bottleneck the web UX, ensure `php artisan queue:work` (or a supervisor equivalent) is continuously running.
- **Storage Links**: Vital for study materials and galleys. Run `php artisan storage:link` on fresh deployments.
