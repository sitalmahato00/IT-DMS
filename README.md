# IT Department Management System (IT-DMS)

A comprehensive Laravel-based management system designed for IT departments to manage students, teachers, courses, attendance, examinations, and more. Built with modern technologies to provide a scalable solution for educational institutions.

## Table of Contents

- [Features](#features)
- [Technologies](#technologies)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Configuration](#configuration)

## Features

- **User Management**: Multi-role system (Admin, Teacher, Student, Parent)
- **Course Management**: Manage subjects with elective/core classification
- **Student Management**: Complete student profiles with academic history
- **Teacher Management**: Comprehensive teacher profiles with qualifications and specializations
- **Attendance Tracking**: Automated attendance marking with multiple attendance types
- **Examination System**: Manage exams, exam schedules, and marks
- **Marks Management**: Internal and external marks tracking
- **Timetable Management**: Create and manage class schedules with gap overrides
- **Bilingual Date Support**: Gregorian (AD) ↔ Nepali (BS) date conversion for forms and data entry
- **Notice System**: Multi-audience notice publishing (draft, published, scheduled, archived)
- **Gallery Management**: Image gallery for department/college events
- **Study Materials**: Upload and manage course materials
- **Audit Logging**: Track all system changes for compliance
- **Session Management**: Semester-based academic sessions
- **Parent Portal**: Parent access to student information

## Technologies

- **Backend**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Vue.js with Vite
- **Database**: MySQL/MariaDB
- **Styling**: Tailwind CSS
- **Additional Libraries**:
  - Laravel Breeze (Authentication)
  - Laravel Sanctum (API Authentication)
  - Laravel DOMPDF (PDF Generation)
  - Nepali Date Support
  - K6 (Load Testing)

## Database Schema

### Complete Table Structure

The system uses **30 database tables** organized by functionality:

### All Migration Tables

1. `users` - User accounts
2. `teachers` - Teacher profiles
3. `parents` - Parent/Guardian profiles
4. `students` - Student profiles
5. `subjects` - Courses/Subjects
6. `semesters` - Academic semesters
7. `departments` - Departments
8. `subject_teacher` - Teacher assignments to subjects
9. `subject_students` - Student enrollments in subjects
10. `elective_enrollments` - Elective course enrollments
11. `attendance` - Student attendance records
12. `timetable_slots` - Class schedule slots
13. `timetable_gap_overrides` - Timetable overrides
14. `exams` - Exam definitions
15. `exam_marks` - Student exam marks
16. `marks` - Subject marks records
17. `notices` - News and announcements
18. `bilingual_notices` - Bilingual notice content
19. `notice_categories` - Notice categories
20. `study_materials` - Course materials
21. `galleries` - Image galleries
22. `audit_logs` - System audit trail
23. `erp_settings` - System configuration
24. `sessions` - Laravel session storage
25. `cache` - Cache storage
26. `cache_locks` - Cache locks
27. `password_reset_tokens` - Password reset tokens
28. `notifications` - System notifications
29. `personal_access_tokens` - API access tokens

### Detailed Table Reference with Attributes

#### **1. Users Table**
| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | BIGINT | ✗ | - | Primary Key |
| name | VARCHAR | ✗ | - | User full name |
| email | VARCHAR | ✗ | - | Email address (UNIQUE) |
| username | VARCHAR(50) | ✓ | - | Username (UNIQUE) |
| email_verified_at | TIMESTAMP | ✓ | - | Email verification timestamp |
| password | VARCHAR | ✗ | - | Encrypted password |
| role | ENUM | ✗ | 'student' | admin, teacher, student, parent |
| phone | VARCHAR | ✓ | - | Phone number |
| department | VARCHAR | ✓ | - | Department |
| bio | TEXT | ✓ | - | User biography |
| profile_photo_path | VARCHAR | ✓ | - | Profile photo path |
| remember_token | VARCHAR | ✓ | - | Remember me token |
| created_at | TIMESTAMP | ✗ | - | Creation timestamp |
| updated_at | TIMESTAMP | ✗ | - | Last update timestamp |
| deleted_at | TIMESTAMP | ✓ | - | Soft delete timestamp |

#### **2. Teachers Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| user_id | BIGINT | - | Foreign Key → users |
| teacher_code | VARCHAR(20) | - | Unique teacher code |
| qualification | VARCHAR(100) | - | Educational qualification |
| phone, secondary_phone | VARCHAR | - | Contact numbers |
| date_of_birth | DATE | - | Date of birth |
| joining_date | DATE | - | Joining date |
| years_of_experience | SMALLINT | - | Years of experience |
| specialization | VARCHAR(255) | - | Subject specialization |
| employment_type | VARCHAR(50) | - | Full-time, contract, etc. |
| salary | DECIMAL(12,2) | - | Salary amount |
| bank_account_number | VARCHAR | - | Bank account for salary |
| status | ENUM | 'active' | active, inactive, suspended, On Leave, Retired |
| gender | VARCHAR(20) | - | Gender |
| profile_photo_path | VARCHAR(2048) | - | Profile photo |
| resume_path | VARCHAR(2048) | - | Resume document |
| timestamps | - | - | created_at, updated_at |

#### **3. Students Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| user_id | BIGINT | - | Foreign Key → users |
| roll_no | VARCHAR(50) | - | Roll number |
| registration_number | VARCHAR(50) | - | Registration/ID number |
| semester | VARCHAR(20) | '1' | Current semester |
| section | VARCHAR(50) | - | Class section |
| parent_id | BIGINT | - | Foreign Key → parents |
| date_of_birth | DATE | - | Date of birth |
| academic_year | VARCHAR(10) | - | Current academic year |
| enrollment_date | DATE | - | Date of enrollment |
| batch_year | VARCHAR(10) | - | Batch year |
| is_active | BOOLEAN | true | Active status |
| is_alumni | BOOLEAN | false | Alumni status |
| status | ENUM | 'active' | active, inactive, suspended |
| blood_group | VARCHAR(10) | - | Blood group |
| national_id_number | VARCHAR(100) | - | National ID |
| medical_conditions | TEXT | - | Health conditions |
| profile_photo_path | VARCHAR(2048) | - | Profile photo |
| id_document_path | VARCHAR(2048) | - | ID document scan |
| timestamps | - | - | created_at, updated_at, deleted_at |

#### **4. Parents Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| user_id | BIGINT | - | Foreign Key → users |
| parent_code | VARCHAR(20) | - | Unique parent code |
| occupation | VARCHAR(100) | - | Parent's occupation |
| phone | VARCHAR(20) | - | Contact phone |
| address | TEXT | - | Physical address |
| status | ENUM | 'active' | active, inactive, suspended |
| gender | VARCHAR(20) | - | Gender |
| profile_photo_path | VARCHAR(2048) | - | Profile photo |
| timestamps | - | - | created_at, updated_at |

#### **5. Subjects Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| subject_name | VARCHAR(200) | - | Subject name (English) |
| subject_name_ne | VARCHAR(200) | - | Subject name (Nepali) |
| subject_code | VARCHAR(50) | - | Unique code |
| description | TEXT | - | Description |
| semester | VARCHAR(20) | - | Semester level |
| credits | VARCHAR(10) | '3' | Credit hours |
| status | ENUM | 'active' | active, inactive |
| subject_type | ENUM | 'core' | core, elective, optional |
| theory_percentage | INT | 70 | Theory marking % |
| practical_percentage | INT | 30 | Practical marking % |
| has_lab | BOOLEAN | false | Has lab component |
| lecture_hours | INT | 4 | Weekly lecture hours |
| practical_hours | INT | 2 | Weekly practical hours |
| max_students, min_students | INT | - | Capacity for electives |
| prerequisite | VARCHAR(200) | - | Prerequisites |
| timestamps | - | - | created_at, updated_at, deleted_at |

#### **6. Semesters Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| semester_number | INT | Semester number (1-8) |
| semester_name | VARCHAR | Semester name/label |
| academic_year | VARCHAR(10) | Academic year |
| start_date | DATE | Semester start date |
| end_date | DATE | Semester end date |
| status | ENUM | active, inactive |
| timestamps | - | created_at, updated_at |

#### **7. Departments Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| department_name | VARCHAR | Department name |
| department_code | VARCHAR(50) | Unique code |
| head_id | BIGINT | Foreign Key → users (Department head) |
| description | TEXT | Department description |
| contact_email | VARCHAR | Department email |
| phone | VARCHAR | Department phone |
| office_location | VARCHAR | Office location |
| timestamps | - | created_at, updated_at |

#### **8. Colleges Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| college_name | VARCHAR | College name |
| college_code | VARCHAR(50) | Unique code |
| address | TEXT | College address |
| phone | VARCHAR | Contact number |
| email | VARCHAR | Contact email |
| principal_name | VARCHAR | Principal/Head name |
| website | VARCHAR | College website |
| timestamps | - | created_at, updated_at |

#### **9. Subject-Teacher Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| subject_id | BIGINT | Foreign Key → subjects |
| teacher_id | BIGINT | Foreign Key → users (teacher) |
| semester | VARCHAR(20) | Assigned semester |
| status | VARCHAR | Active/inactive status |
| assigned_date | DATE | Assignment date |
| created_at | TIMESTAMP | Creation timestamp |

#### **10. Subject-Students Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| subject_id | BIGINT | Foreign Key → subjects |
| student_id | BIGINT | Foreign Key → students |
| enrollment_date | DATE | Enrollment date |
| status | VARCHAR | active, dropped, completed |
| timestamps | - | created_at, updated_at |

#### **11. Elective-Enrollments Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| student_id | BIGINT | Foreign Key → students |
| subject_id | BIGINT | Foreign Key → subjects |
| enrollment_date | DATE | Enrollment date |
| status | VARCHAR | pending, approved, rejected |
| semester | VARCHAR(20) | Semester enrolled |
| created_at | TIMESTAMP | Creation timestamp |

#### **12. Attendance Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| student_id | BIGINT | - | Foreign Key → students |
| teacher_id | BIGINT | - | Foreign Key → users (teacher marking) |
| subject_id | BIGINT | - | Foreign Key → subjects |
| attendance_type | VARCHAR(20) | 'class' | class, lab, exam |
| date | DATE | - | Attendance date |
| date_bs | VARCHAR(20) | - | Nepali date |
| time_in | TIME | - | Check-in time |
| time_out | TIME | - | Check-out time |
| status | ENUM | 'present' | present, absent, late, excused, leave |
| remarks | TEXT | - | Additional notes |
| timestamps | - | - | created_at, updated_at |

#### **13. Exams Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| exam_name | VARCHAR(255) | - | Exam name (English) |
| exam_name_ne | VARCHAR(255) | - | Exam name (Nepali) |
| academic_year | VARCHAR(20) | - | Academic year |
| semester | VARCHAR(20) | - | Semester |
| subject_id | BIGINT | - | Foreign Key → subjects |
| exam_type | ENUM | 'internal' | internal, final, midterm, practical, viva |
| exam_category | ENUM | 'general' | assessment, ctevt, general |
| full_marks | DECIMAL(5,2) | 100 | Total marks |
| passing_marks | DECIMAL(5,2) | 40 | Passing marks |
| exam_date | DATE | - | Exam date |
| status | ENUM | 'draft' | draft, published, archived |
| created_by | BIGINT | - | Foreign Key → users |
| timestamps | - | - | created_at, updated_at, deleted_at |

#### **14. Exam-Marks Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| student_id | BIGINT | Foreign Key → students |
| exam_id | BIGINT | Foreign Key → exams |
| marks_obtained | DECIMAL(5,2) | Marks obtained |
| status | VARCHAR | Graded, pending |
| created_at | TIMESTAMP | Creation timestamp |

#### **15. Marks Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| student_id | BIGINT | Foreign Key → students |
| subject_id | BIGINT | Foreign Key → subjects |
| internal_marks | DECIMAL(5,2) | Internal assessment marks |
| external_marks | DECIMAL(5,2) | External exam marks |
| practical_marks | DECIMAL(5,2) | Practical exam marks |
| total_marks | DECIMAL(5,2) | Total marks |
| grade | VARCHAR(10) | Letter grade (A+, A, B+, etc.) |
| semester | VARCHAR(20) | Semester |
| status | VARCHAR | completed, pending |
| timestamps | - | created_at, updated_at |

#### **16. Notices Table**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| id | BIGINT | - | Primary Key |
| title | VARCHAR(255) | - | Notice title (English) |
| title_ne | VARCHAR(255) | - | Notice title (Nepali) |
| message | TEXT | - | Notice message |
| audience | VARCHAR(50) | 'all' | Target audience: all, students, teachers, parents |
| status | ENUM | 'draft' | draft, published, scheduled, archived |
| semester | VARCHAR(20) | - | Target semester |
| subject_id | BIGINT | - | Related subject (if any) |
| is_important | BOOLEAN | false | Mark as important |
| published_at | TIMESTAMP | - | Publication time |
| created_by | BIGINT | - | Foreign Key → users |
| file_path | VARCHAR(500) | - | Attached file path |
| timestamps | - | - | created_at, updated_at, deleted_at |

#### **17. Bilingual-Notices Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| notice_id | BIGINT | Foreign Key → notices |
| language | VARCHAR(10) | Language code (en, ne) |
| title | VARCHAR(255) | Title in language |
| message | TEXT | Message in language |
| timestamps | - | created_at, updated_at |

#### **18. Notice-Categories Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| category_name | VARCHAR(100) | Category name |
| description | TEXT | Category description |
| created_at | TIMESTAMP | Creation timestamp |

#### **19. Study-Materials Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| subject_id | BIGINT | Foreign Key → subjects |
| teacher_id | BIGINT | Foreign Key → users (uploader) |
| title | VARCHAR(255) | Material title |
| description | TEXT | Material description |
| file_path | VARCHAR(2048) | File location |
| file_type | VARCHAR(50) | PDF, DOC, VIDEO, etc. |
| semester | VARCHAR(20) | Applicable semester |
| status | VARCHAR | published, draft |
| uploaded_by | BIGINT | Foreign Key → users |
| timestamps | - | created_at, updated_at, deleted_at |

#### **20. Galleries Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| title | VARCHAR(255) | Gallery title |
| description | TEXT | Description |
| image_path | VARCHAR(2048) | Image file path |
| event_date | DATE | Event date |
| uploaded_by | BIGINT | Foreign Key → users |
| album | VARCHAR(100) | Album/category |
| timestamps | - | created_at, updated_at |

#### **21. Audit-Logs Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| user_id | BIGINT | Foreign Key → users (who made change) |
| model_type | VARCHAR(255) | Model class name (e.g., Student) |
| model_id | BIGINT | ID of changed resource |
| action | VARCHAR(50) | created, updated, deleted |
| old_values | JSON | Previous values |
| new_values | JSON | New values |
| description | TEXT | Change description |
| timestamps | - | created_at |

#### **22. Timetable-Slots Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| day_of_week | VARCHAR(10) | MON, TUE, WED, etc. |
| start_time | TIME | Slot start time |
| end_time | TIME | Slot end time |
| subject_id | BIGINT | Foreign Key → subjects |
| teacher_id | BIGINT | Foreign Key → users (teacher) |
| room_number | VARCHAR(50) | Classroom/room number |
| semester | VARCHAR(20) | Applicable semester |
| semester_section | VARCHAR(50) | Section identifier |
| academic_year | VARCHAR(10) | Academic year |
| status | VARCHAR | active, inactive |
| created_at | TIMESTAMP | Creation timestamp |

#### **23. Timetable-Gap-Overrides Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| date | DATE | Override date |
| teacher_id | BIGINT | Foreign Key → users |
| reason | TEXT | Reason for override |
| timetable_slot_id | BIGINT | Foreign Key → timetable_slots |
| created_at | TIMESTAMP | Creation timestamp |

#### **24. ERP-Settings Table**
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| key | VARCHAR(50) | Setting key (UNIQUE) |
| value | TEXT | Setting value |
| description | TEXT | Setting description |
| data_type | VARCHAR(20) | string, int, boolean, json |
| timestamps | - | created_at, updated_at |

#### **25. Sessions Table** (Laravel)
| Column | Type | Description |
|--------|------|-------------|
| id | VARCHAR(255) | Session ID (PRIMARY) |
| user_id | BIGINT | Foreign Key → users |
| ip_address | VARCHAR(45) | Client IP address |
| user_agent | TEXT | Browser user agent |
| payload | LONGTEXT | Session payload (JSON) |
| last_activity | INT | Last activity timestamp |

#### **26. Cache Table** (Laravel)
| Column | Type | Description |
|--------|------|-------------|
| key | VARCHAR(255) | Cache key (PRIMARY) |
| value | LONGTEXT | Cached value |
| expiration | INT | Expiration timestamp |

#### **27. Cache-Locks Table** (Laravel)
| Column | Type | Description |
|--------|------|-------------|
| key | VARCHAR(255) | Lock key (PRIMARY) |
| owner | VARCHAR(255) | Lock owner |
| expiration | INT | Expiration time |

#### **28. Password-Reset-Tokens Table** (Laravel)
| Column | Type | Description |
|--------|------|-------------|
| email | VARCHAR(255) | User email |
| token | VARCHAR(255) | Reset token |
| created_at | TIMESTAMP | Token creation time |

#### **29. Notifications Table** (Laravel)
| Column | Type | Description |
|--------|------|-------------|
| id | UUID/VARCHAR | Notification ID |
| type | VARCHAR(255) | Notification class name |
| notifiable_type | VARCHAR(255) | Model type (User, Student, etc.) |
| notifiable_id | BIGINT | Model ID |
| data | JSON | Notification data |
| read_at | TIMESTAMP | When read |
| created_at | TIMESTAMP | Creation time |

#### **30. Personal-Access-Tokens Table** (Sanctum)
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary Key |
| tokenable_type | VARCHAR(255) | Model type (User) |
| tokenable_id | BIGINT | Model ID |
| name | VARCHAR(255) | Token name |
| token | VARCHAR(64) | Hashed token |
| abilities | JSON | Token permissions |
| last_used_at | TIMESTAMP | Last usage time |
| created_at | TIMESTAMP | Creation time |

### **1. User Management Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `users` | Core user accounts | id, name, email, username, role, password, phone, department, profile_photo_path |
| `user_details` | Extended user information | user_id, bio, social_links, emergency_contact |
| `personal_access_tokens` | API authentication tokens | id, tokenable_id, name, token |

### **2. Role-Specific Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `teachers` | Teacher profiles | id, user_id, teacher_code, qualification, joining_date, specialization, salary, experience_years |
| `students` | Student profiles | id, user_id, roll_no, registration_number, semester, section, batch_year, enrollment_date |
| `parents` | Parent/Guardian information | id, user_id, occupation, phone, emergency_contact |
| `student_parents` | Student-Parent relationship mapping | id, student_id, parent_id |

### **3. Academic Structure Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `subjects` | Course/Subject information | id, subject_name, subject_code, credits, semester, subject_type (core/elective) |
| `subject_teacher` | Teacher assignment to subjects | id, subject_id, teacher_id, semester |
| `subject_students` | Student enrollment in subjects | id, subject_id, student_id |
| `semesters` | Semester definitions | id, semester_name, start_date, end_date, academic_year |
| `departments` | Department information | id, department_name, department_code, head_id |
| `colleges` | College information | id, college_name, college_code |

### **4. Curriculum & Enrollment Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `elective_enrollments` | Student elective course enrollments | id, student_id, subject_id, enrollment_date, status |
| `courses` | Course definitions | id, course_name, course_code, department_id |

### **5. Timetable Management Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `timetable_slots` | Daily class schedule slots | id, day_of_week, start_time, end_time, subject_id, teacher_id, room_number |
| `timetable_gap_overrides` | Override default timetable gaps | id, date, teacher_id, reason |

### **6. Attendance & Record Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `attendance` | Student attendance records | id, student_id, teacher_id, subject_id, date, status (present/absent/late), remarks |

### **7. Examination & Assessment Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `exams` | Exam definitions | id, exam_name, exam_date, total_marks, passing_marks, subject_id |
| `exam_marks` | Student exam marks | id, student_id, exam_id, marks_obtained |
| `marks` | Subject marks (internal/external) | id, student_id, subject_id, internal_marks, external_marks, practical_marks |

### **8. Communication & Notices Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `notices` | News and announcements | id, title, message, audience, status (draft/published), published_at |
| `bilingual_notices` | Bilingual notice content | id, notice_id, language, title, message |
| `notice_categories` | Notice categorization | id, category_name, description |

### **9. Learning Materials Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `study_materials` | Course materials and resources | id, subject_id, teacher_id, title, file_path, uploaded_at |
| `galleries` | Image galleries for events | id, title, description, image_path, uploaded_by |

### **10. System & Configuration Tables**

| Table | Purpose | Key Fields |
|-------|---------|-----------|
| `audit_logs` | System audit trail | id, user_id, model_type, model_id, action, old_values, new_values |
| `erp_settings` | System configuration settings | id, key, value, description |
| `sessions` | Laravel session storage | id, user_id, ip_address, user_agent, payload, last_activity |
| `cache` | Cache table for storing cached data | key, value, expiration |
| `cache_locks` | Cache locking mechanism | key, owner, expiration |
| `password_reset_tokens` | Password reset token storage | email, token, created_at |
| `notifications` | System notifications | id, type, notifiable_type, notifiable_id, data, read_at |

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL/MariaDB 5.7+
- Git

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd IT-DMS
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up database**
   ```bash
   # Update .env with database credentials
   php artisan migrate --force
   php artisan db:seed
   ```

5. **Install Node dependencies**
   ```bash
   npm install
   npm run build
   ```

6. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

### Using Laragon (Windows)

```powershell
.\setup-laragon.ps1
```

## Project Structure

```
IT-DMS/
├── app/
│   ├── Console/          # Artisan commands
│   ├── Exceptions/       # Custom exceptions
│   ├── Exports/          # Data export classes
│   ├── Helpers/          # Helper functions
│   ├── Http/
│   │   ├── Controllers/  # Route controllers
│   │   ├── Middleware/   # HTTP middleware
│   │   └── Requests/     # Form request validation
│   ├── Mail/             # Mailable classes
│   ├── Models/           # Eloquent models
│   ├── Notifications/    # Notification classes
│   ├── Observers/        # Model observers for auditing
│   └── Services/         # Business logic services
├── bootstrap/            # Bootstrap files
├── config/               # Configuration files
├── database/
│   ├── factories/        # Model factories for testing
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Web root
├── resources/
│   ├── css/              # Tailwind CSS
│   ├── js/               # Vue.js components
│   ├── lang/             # Language files (EN, NE)
│   └── views/            # Blade templates
├── routes/
│   ├── api.php           # API routes
│   ├── auth.php          # Authentication routes
│   ├── web.php           # Web routes
│   └── console.php       # Console commands
├── storage/              # File storage
├── tests/                # Unit & feature tests
└── vendor/               # Composer dependencies
```

## Configuration

### Environment Variables (`.env`)

```env
APP_NAME="IT Department Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_dms
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### Key Features Configuration

- **Bilingual Support**: Configure language in `config/locales.php`
- **Database Settings**: Edit `config/database.php`
- **Mail Configuration**: Set up mail driver in `config/mail.php`
- **Session Settings**: Configure in `config/session.php`

## Key Models & Relationships

### User Model
- One User can have many roles (Admin, Teacher, Student, Parent)
- Relationships: Teacher, Student, Parent, notifications, audit logs

### Student Model
- Belongs to User
- Has many Subject enrollments
- Has many Attendance records
- Has marks through Subject relationship

### Teacher Model
- Belongs to User
- Has many Subjects taught
- Has many Attendance records (as recorder)
- Creates Study Materials and Notices

### Subject Model
- Has many Students (through subject_students)
- Has many Teachers (through subject_teacher)
- Has many Attendance records
- Has many Marks records

## Testing

Run unit and feature tests:

```bash
php artisan test
```

Load testing with K6:

```bash
k6 run k6-baseline-test.js
k6 run k6-progressive-load-test.js
k6 run k6-stress-test.js
```

## API Documentation

API endpoints follow RESTful conventions:

- **Authentication**: `/api/auth/*`
- **Students**: `/api/students/*`
- **Teachers**: `/api/teachers/*`
- **Subjects**: `/api/subjects/*`
- **Attendance**: `/api/attendance/*`
- **Marks**: `/api/marks/*`
- **Exams**: `/api/exams/*`
- **Notices**: `/api/notices/*`

## Performance Optimization

The system includes:
- Query optimization with eager loading
- Indexed database columns for fast queries
- Caching strategies via Redis/file drivers
- Pagination for large datasets
- Optimized Blade templates
- Vite bundler for fast asset delivery

## License

MIT License - See LICENSE file for details

## Support

For issues and feature requests, please contact the development team at the IT Department.

---

**Last Updated**: April 2026  
**Version**: 1.0.0  
**Status**: Production Ready
