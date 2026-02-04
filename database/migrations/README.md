# Database Migrations - IT DMS

This document describes the complete database schema for the IT DMS (Institute Technology - Department Management System) with full Nepali language support.

## Overview

The system includes 16 migrations that create all necessary tables for managing students, teachers, attendance, exams, notices, study materials, and more.

## Nepali Language Support

All tables support dual-language content using the following pattern:
- `*_ne` fields: Stores Nepali (Devanagari) content
- `*_bs` fields: Stores Bikram Sambat (BS) dates
- Bilingual content is managed via `NepaliContentHelper`

## Migration Files

| # | Migration File | Table Name | Description |
|---|---------------|------------|-------------|
| 1 | `2024_01_01_000001_create_users_table.php` | `users` | User authentication and profiles |
| 2 | `2024_01_01_000002_create_students_table.php` | `students` | Student profiles linked to users |
| 3 | `2024_01_01_000003_create_subjects_table.php` | `subjects` | Subjects/courses with full details |
| 4 | `2024_01_01_000004_create_attendance_table.php` | `attendance` | Daily attendance records |
| 5 | `2024_01_01_000005_create_exams_table.php` | `exams` | Exam schedules and details |
| 6 | `2024_01_01_000006_create_exam_marks_table.php` | `exam_marks` | Exam results and grades |
| 7 | `2024_01_01_000007_create_marks_table.php` | `marks` | Alternative marks tracking |
| 8 | `2024_01_01_000008_create_notices_table.php` | `notices` | Notice board notices |
| 9 | `2024_01_01_000009_create_bilingual_notices_table.php` | `bilingual_notices` | Advanced bilingual notices (with SoftDeletes) |
| 10 | `2024_01_01_000010_create_notice_categories_table.php` | `notice_categories` | Notice categorization |
| 11 | `2024_01_01_000011_create_study_materials_table.php` | `study_materials` | Study materials and documents |
| 12 | `2024_01_01_000012_create_gallery_table.php` | `gallery` | Gallery images |
| 13 | `2024_01_01_000013_create_audit_logs_table.php` | `audit_logs` | System audit trail |
| 14 | `2024_01_01_000014_create_subject_students_table.php` | `subject_students` | Pivot table for subject enrollments |
| 15 | `2024_01_01_000015_create_password_reset_tokens_table.php` | `password_reset_tokens` | Password reset tokens |
| 16 | `2024_01_01_000016_create_sessions_table.php` | `sessions` | User sessions |

## Table Details

### 1. Users Table (`users`)
Stores all user information with role-based access control.

**Key Fields:**
- `id` - Primary key
- `name`, `email`, `password` - Authentication
- `role` - Enum: admin, teacher, student, parent
- `phone`, `address`, `bio` - Contact info
- `profile_photo_path` - Profile photo storage
- `status` - active, inactive, suspended
- `is_alumni` - Alumni flag

### 2. Students Table (`students`)
Student-specific information linked to users.

**Key Fields:**
- `user_id` - Foreign key to users
- `roll_no` - Unique student identifier
- `semester` - Current semester (1-6)
- `department` - Department name
- `parent_id` - Foreign key to parent user
- `date_of_birth` / `date_of_birth_bs` - DOB in AD/BS
- `gender`, `blood_group`, `emergency_contact`
- `is_active`, `is_alumni`

### 3. Subjects Table (`subjects`)
Complete subject/course information.

**Key Fields:**
- `subject_name`, `subject_name_ne` - Bilingual names
- `subject_code` - Unique subject code
- `description`, `description_ne` - Bilingual descriptions
- `semester` - Associated semester
- `teacher_id` - Assigned teacher
- `credits` - Credit hours
- `category`, `status` - Categorization
- Course details: syllabus, learning_objectives, theory_percentage, practical_percentage, etc.
- Teaching hours: lecture_hours, practical_hours, tutorial_hours

### 4. Attendance Table (`attendance`)
Daily attendance tracking.

**Key Fields:**
- `student_id`, `teacher_id`, `subject_id` - Relationships
- `date`, `date_bs` - Date in AD/BS format
- `status` - present, absent, late, excused
- `remarks` - Optional notes

**Indexes:** Optimized for date/subject and student/date queries

### 5. Exams Table (`exams`)
Exam scheduling and information.

**Key Fields:**
- `exam_name`, `exam_name_ne` - Bilingual names
- `academic_year`, `semester` - Academic context
- `subject_id`, `course_id` - Related subject/course
- `exam_type` - internal, final, midterm, practical, viva, assignment, assessment
- `full_marks`, `passing_marks` - Marks configuration
- `exam_date`, `exam_date_bs` - Exam date in AD/BS
- `status` - draft, published, archived
- `description`, `description_ne`, `instructions` - Bilingual content
- `created_by` - Creator reference

### 6. Exam Marks Table (`exam_marks`)
Results for each exam.

**Key Fields:**
- `exam_id`, `student_id` - Exam and student references
- `marks_obtained`, `full_marks` - Marks
- `percentage` - Calculated percentage
- `grade` - Letter grade (A+, A, B+, B, etc.)
- `remarks`, `graded_by`, `graded_at` - Grading info

**Constraints:** Unique (exam_id, student_id) to prevent duplicates

### 7. Marks Table (`marks`)
Alternative marks tracking for ongoing assessments.

**Key Fields:**
- `student_id`, `subject_id`, `teacher_id` - Relationships
- `exam_type` - Type of assessment
- `marks_obtained`, `full_marks` - Marks
- `date` - Assessment date

### 8. Notices Table (`notices`)
Standard notice board notices.

**Key Fields:**
- `title`, `title_ne` - Bilingual titles
- `message`, `message_ne` - Bilingual content
- `audience`, `audience_ne` - Target audience
- `status` - draft, published, scheduled, archived
- `semester`, `subject_id` - Targeting
- `is_important` - Important flag
- `published_at`, `published_at_bs` - Publication date
- `file_path`, `file_name` - Attachments

### 9. Bilingual Notices Table (`bilingual_notices`)
Advanced notices with full bilingual support and SoftDeletes.

**Key Fields:**
- `title_ne`, `title_en` - Separate language fields
- `content_ne`, `content_en` - Full content
- `audience`, `category`, `priority` - Classification
- `published_date`, `expiry_date` - Date range
- `published_date_bs`, `expiry_date_bs` - BS dates
- `is_published`, `is_important`, `is_featured` - Flags
- `category_id` - Category reference
- **SoftDeletes enabled**

### 10. Notice Categories Table (`notice_categories`)
Categorization for notices.

**Key Fields:**
- `name`, `name_ne` - Bilingual names
- `slug` - URL-friendly identifier
- `description`, `description_ne` - Bilingual descriptions
- `icon`, `color` - Visual styling
- `sort_order`, `is_active` - Ordering

### 11. Study Materials Table (`study_materials`)
Document management for learning materials.

**Key Fields:**
- `subject_id`, `teacher_id` - Relationships
- `document_type` - lecture_notes, assignment, lab_report, assessment, study_guide, syllabus, project_material
- `title`, `title_ne` - Bilingual titles
- `file_name`, `file_path`, `file_size` - File info
- `description`, `description_ne` - Bilingual descriptions
- `semester` - Target semester
- `visibility` - all, students, faculty
- `is_published` - Published status

### 12. Gallery Table (`gallery`)
Image gallery management.

**Key Fields:**
- `title`, `title_ne` - Bilingual titles
- `description`, `description_ne` - Bilingual descriptions
- `image_path`, `image_name` - Image storage
- `category` - Image category
- `order`, `is_active` - Ordering and visibility

### 13. Audit Logs Table (`audit_logs`)
System activity tracking.

**Key Fields:**
- `user_id` - Acting user
- `action` - Action type
- `model_type`, `model_id` - Affected record
- `old_values`, `new_values` - JSON diff
- `ip_address`, `user_agent` - Request info

### 14. Subject Students Pivot Table (`subject_students`)
Many-to-many relationship for subject enrollments.

**Key Fields:**
- `subject_id`, `student_id` - Foreign keys
- `enrolled_at` - Enrollment timestamp

**Constraints:** Unique (subject_id, student_id)

### 15. Password Reset Tokens Table (`password_reset_tokens`)
Laravel password reset functionality.

**Key Fields:**
- `email` - User email
- `token` - Reset token
- `created_at` - Token creation time

### 16. Sessions Table (`sessions`)
Laravel session management.

**Key Fields:**
- `id` - Session ID
- `user_id` - Associated user
- `ip_address`, `user_agent` - Client info
- `payload` - Session data
- `last_activity` - Last activity timestamp

## Running Migrations

```bash
# Run all migrations
php artisan migrate

# Run specific migration
php artisan migrate --path=database/migrations/2024_01_01_000001_create_users_table.php

# Rollback all migrations
php artisan migrate:rollback

# Rollback specific table
php artisan migrate:rollback --path=database/migrations/2024_01_01_000001_create_users_table.php

# Refresh all migrations
php artisan migrate:refresh

# Check migration status
php artisan migrate:status
```

## Key Features

1. **Soft Deletes**: Enabled on users, students, subjects, notices, bilingual_notices, and study_materials tables
2. **Foreign Keys**: All relationships use proper foreign key constraints with appropriate onDelete actions
3. **Indexes**: Optimized indexes for common query patterns
4. **Dual Dates**: All date fields have AD (`date`) and BS (`date_bs`) versions
5. **Bilingual Content**: Nepali content fields (`*_ne`) throughout
6. **Status Enums**: Consistent status fields for easy filtering
7. **Unique Constraints**: Prevents duplicate entries on critical fields

## Nepal Government Standards Compliance

This schema follows Nepal Government Website Standards:
- Stores Nepali content directly in Unicode (utf8mb4)
- Dual date support (AD/BS)
- Proper localization patterns
- Accessible categorization

