# Requirements Document

## Introduction

This document defines the requirements for refactoring and enhancing the existing Laravel-based College ERP system into a fully component-based, modular, high-performance architecture. The refactored system must preserve 100% of existing functionality — including all business logic, validation rules, RBAC, database schema and relationships, APIs, security mechanisms, data integrity, and system behavior — while introducing new capabilities: a Principal (Admin) role, multi-department support, bulk semester promotion, a public website module, alumni/media pages, enhanced reporting, user activity logging, security upgrades, and data backup/recovery. All changes will be made in place within the existing codebase, maintaining backward compatibility throughout the refactoring process.

The existing system is a Laravel 12 / PHP 8.2 application using Eloquent ORM, Blade templating, Laravel Sanctum for API tokens, and a MySQL database. It currently supports four roles: admin, teacher, student, and parent.

---

## Glossary

- **ERP_System**: The refactored College ERP application with component-based architecture
- **Principal**: The new top-level administrative role (mapped to `admin` in the refactored system), responsible for institution-wide management
- **HOD**: Head of Department — the role previously called `admin`, now scoped strictly to a single department
- **Teacher**: A staff member who records attendance, enters marks, and manages course materials
- **Student**: An enrolled learner who accesses academic records and submits assignments
- **Parent**: A guardian who monitors a linked student's academic progress
- **Public_User**: An unauthenticated visitor accessing public-facing pages
- **Department**: An academic unit (e.g., BCA, BBA) with its own HOD, teachers, and students
- **Semester**: An academic period within a department's program
- **RBAC**: Role-Based Access Control — the permission system governing what each role can see and do
- **Service_Layer**: The layer containing business logic, sitting between controllers and repositories
- **Repository_Layer**: The data access layer abstracting Eloquent queries
- **Audit_Log**: A tamper-evident record of user actions stored in `audit_logs`
- **ERP_Setting**: A key-value configuration entry stored in `erp_settings`
- **Component**: A reusable Blade/PHP UI or logic unit within the component-based architecture
- **Module**: A self-contained feature group (e.g., `app/Modules/Admin`, `app/Modules/Hod`) with its own controllers, views, and routes
- **API**: A secured HTTP endpoint delivering JSON data, used by public pages and internal consumers
- **Backup**: A compressed, encrypted snapshot of the database and uploaded files
- **Magic_Link**: A time-limited, single-use authentication URL
- **BS_Date**: Bikram Sambat (Nepali) calendar date, used alongside AD dates throughout the system
- **Alumni**: A student whose `is_alumni` flag is `true` and who has graduated from the institution
- **Elective**: An optional subject a student may enroll in beyond the core curriculum
- **Timetable**: A schedule of class slots assigned to subjects, teachers, and semesters
- **Backward_Compatibility**: The requirement that all existing functionality, APIs, routes, and data structures continue to work during and after refactoring

---

## Requirements

---

### Requirement 1: Component-Based Modular Architecture

**User Story:** As a developer, I want the existing system refactored into a clean layered, component-based architecture, so that the codebase is maintainable, testable, and scalable while preserving all existing functionality.

#### Acceptance Criteria

1. THE ERP_System SHALL refactor the existing Laravel structure to organize code into logical directories: `app/Modules` (containing `Principal`, `Hod`, `Teacher`, `Student`, `Parent`), `app/Services`, `app/Repositories`, `app/Components`, `app/Security`, and maintain existing `app/Http`, `app/Models`, `config`, `resources`, and `routes` directories.
2. THE ERP_System SHALL enforce a four-layer separation: Presentation Layer (Blade components/views), Business Logic Layer (Service classes), Data Access Layer (Repository classes), and Security Layer (middleware and validators).
3. WHEN a feature requires database access, THE ERP_System SHALL gradually migrate queries to the Repository_Layer, routing all new features through repositories and never issuing raw Eloquent queries directly from controllers.
4. THE ERP_System SHALL maintain backward compatibility with all existing routes, controllers, and APIs during the refactoring process.
5. THE ERP_System SHALL preserve the existing database schema, migrations, and Eloquent models, extending them as needed for new features.
6. THE ERP_System SHALL preserve 100% of existing business logic, validation rules, RBAC assignments, API contracts, security mechanisms, and data integrity constraints.

---

### Requirement 2: Principal Role Addition

**User Story:** As an institution administrator (Principal), I want a dedicated top-level role that manages all departments, users, and system-wide settings, so that I have full oversight and control of the entire ERP.

#### Acceptance Criteria

1. THE ERP_System SHALL introduce a `principal` role value in the `users.role` enum alongside the existing `admin`, `teacher`, `student`, and `parent` values.
2. WHEN a user with the `principal` role authenticates, THE ERP_System SHALL redirect the user to the Principal dashboard at `/principal/dashboard`.
3. THE Principal_Module SHALL provide access to: User Management, Department Management, Academic Management, Student Management, System Management, Security & Access Control, Analytics & Reporting, Resource & Content Management, Webpage Control, Data Backup & Recovery, System Settings, Records Management, and Profile Settings.
4. WHILE a user holds the `principal` role, THE ERP_System SHALL grant cross-department read and write access to all data.
5. IF a non-principal user attempts to access a Principal_Module route, THEN THE ERP_System SHALL return an HTTP 403 response and redirect the user to their own role dashboard.
6. THE ERP_System SHALL allow the Principal to create, update, deactivate, and delete user accounts for all roles including HOD, Teacher, Student, and Parent.
7. THE ERP_System SHALL allow the Principal to assign and reassign HOD users to specific departments.

---

### Requirement 3: HOD Role Scoping (Former Admin)

**User Story:** As a Head of Department (HOD), I want access to all academic management features scoped strictly to my own department, so that I cannot view or modify data belonging to other departments.

#### Acceptance Criteria

1. THE ERP_System SHALL map the existing `admin` role to the `hod` role in the new system, preserving all existing permissions within a single-department scope.
2. WHEN a user with the `hod` role queries students, teachers, subjects, attendance, or results, THE ERP_System SHALL automatically apply a department filter matching the HOD's assigned department.
3. IF a HOD user constructs a request that references a resource outside their assigned department, THEN THE ERP_System SHALL return an HTTP 403 response.
4. THE HOD_Module SHALL provide access to: Department Oversight (view-only for cross-department), Teacher Management, Student Monitoring, Academic Coordination, Attendance Monitoring, Result Monitoring, Communication & Notifications, Department Reports & Analytics, and Profile Settings.
5. THE ERP_System SHALL prevent HOD users from accessing system-wide settings, backup/recovery, security configuration, or Principal-only routes.

---

### Requirement 4: Multi-Department System Support

**User Story:** As a Principal, I want the system to support multiple departments each with independent data, so that the institution can manage BCA, BBA, and other programs separately.

#### Acceptance Criteria

1. THE ERP_System SHALL support multiple Department records, each with a unique name, short name, HOD assignment, contact information, and academic configuration.
2. WHEN a student, teacher, or subject record is created, THE ERP_System SHALL require a valid `department_id` foreign key linking the record to an existing Department.
3. THE ERP_System SHALL enforce that a HOD user can only manage records where `department_id` matches the HOD's own department assignment.
4. THE ERP_System SHALL allow the Principal to view aggregated statistics across all departments from a single dashboard.
5. WHEN a department is deactivated by the Principal, THE ERP_System SHALL prevent new enrolments and logins for users belonging to that department while preserving all historical data.

---

### Requirement 5: Role-Based Access Control Enhancement

**User Story:** As a system administrator, I want a robust RBAC system that enforces permissions at every layer, so that no user can access data or actions beyond their assigned role.

#### Acceptance Criteria

1. THE ERP_System SHALL define five roles: `principal`, `hod`, `teacher`, `student`, and `parent`, each with a distinct permission set.
2. THE ERP_System SHALL enforce role checks in dedicated middleware applied to every protected route group.
3. WHEN an authenticated user accesses a route, THE ERP_System SHALL verify both authentication status and role permission before processing the request.
4. THE ERP_System SHALL apply data-level scoping so that teachers see only their assigned subjects, students see only their own records, and parents see only their linked student's records.
5. IF a session token is invalid or expired, THEN THE ERP_System SHALL invalidate the session and redirect the user to the login page.
6. THE ERP_System SHALL log every authorisation failure to the Audit_Log with the user ID, attempted route, timestamp, and IP address.

---

### Requirement 6: System Settings & Configuration

**User Story:** As a Principal, I want a centralised settings panel to configure academic years, grading schemes, notification preferences, and system behaviour, so that the ERP adapts to institutional needs without code changes.

#### Acceptance Criteria

1. THE ERP_System SHALL store all configurable settings as key-value pairs in the `erp_settings` table with `group`, `type`, `label`, and `description` columns.
2. THE ERP_System SHALL provide a settings UI accessible only to the `principal` role, organised by groups: `general`, `grading`, `attendance`, `elective`, `semester`, and `notification`.
3. WHEN a setting value is updated, THE ERP_System SHALL validate the value against the declared `type` (string, integer, boolean, json, float) before persisting.
4. THE ERP_System SHALL cache settings with a configurable TTL and invalidate the cache immediately upon any setting update.
5. IF a required setting key is missing from the database, THEN THE ERP_System SHALL fall back to a defined default value and log a warning.
6. THE ERP_System SHALL allow the Principal to configure the current academic year (AD and BS), active semester, grading scale thresholds, minimum attendance percentage, and email notification toggles.

---

### Requirement 7: Academic Management

**User Story:** As a Principal or HOD, I want to manage academic years, semesters, courses, and subjects, so that the academic calendar is accurately reflected in the system.

#### Acceptance Criteria

1. THE ERP_System SHALL allow the Principal and HOD to create, update, and deactivate Semester records with `name`, `is_active`, `start_date`, and `end_date` fields.
2. THE ERP_System SHALL allow the Principal and HOD to create Course records linked to a Department, and Subject records linked to a Course and Semester.
3. WHEN a Semester is deactivated, THE ERP_System SHALL prevent new attendance and exam mark entries for subjects in that semester while preserving all existing records.
4. THE ERP_System SHALL allow the HOD to assign Teacher users to Subject records via the `subject_teacher` pivot table.
5. WHEN a Teacher is assigned to a Subject, THE ERP_System SHALL send a notification to the Teacher's registered email address.
6. THE ERP_System SHALL support elective subject enrolment with `pending`, `approved`, and `rejected` statuses managed by the HOD.

---

### Requirement 8: Student Bulk Semester Promotion

**User Story:** As a Principal or HOD, I want to promote all eligible students from one semester to the next in a single operation, so that semester transitions are efficient and error-free.

#### Acceptance Criteria

1. THE ERP_System SHALL provide a bulk promotion interface accessible to `principal` and `hod` roles.
2. WHEN a bulk promotion is initiated, THE ERP_System SHALL display a preview list of all eligible students (active, non-alumni, matching the source semester) before committing any changes.
3. WHEN the promotion is confirmed, THE ERP_System SHALL update the `semester` field on each eligible Student record from the source semester value to the target semester value within a single database transaction.
4. IF any individual student update fails during bulk promotion, THEN THE ERP_System SHALL roll back the entire transaction and report the number of failed records.
5. THE ERP_System SHALL record a bulk promotion event in the Audit_Log with the initiating user ID, source semester, target semester, count of promoted students, and timestamp.
6. THE ERP_System SHALL allow the Principal or HOD to exclude specific students from a bulk promotion by deselecting them in the preview list.

---

### Requirement 9: Attendance Management

**User Story:** As a Teacher, I want to record and update student attendance for my assigned subjects, so that accurate attendance data is available for reporting and notifications.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Teachers to record attendance for students enrolled in their assigned subjects, with statuses: `present`, `absent`, and `late`.
2. THE ERP_System SHALL support both AD and BS date fields (`date` and `date_bs`) on every attendance record.
3. WHEN attendance is submitted for a subject on a given date, THE ERP_System SHALL prevent duplicate attendance entries for the same student, subject, and date combination.
4. IF a student's cumulative attendance percentage for a subject falls below the configured minimum threshold, THEN THE ERP_System SHALL trigger an attendance notification to the linked Parent user.
5. THE ERP_System SHALL allow Teachers to view and correct attendance records for dates within the current active semester.
6. THE ERP_System SHALL provide HOD and Principal users with a department-scoped attendance summary showing present, absent, and late counts per subject per day.

---

### Requirement 10: Marks and Examination Management

**User Story:** As a Teacher, I want to enter and update student marks for exams, so that academic performance is accurately recorded and accessible to authorised users.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Teachers to create Exam records linked to a Subject with `exam_name`, `exam_date`, `exam_date_bs`, `total_marks`, and `pass_marks` fields.
2. WHEN an Exam record is created or updated, THE ERP_System SHALL send an exam notification to all enrolled students and their linked parents.
3. THE ERP_System SHALL allow Teachers to enter ExamMark records for each enrolled student, including `marks_obtained`, `grade`, and `remarks`.
4. THE ERP_System SHALL compute and store the `grade` value automatically based on the configured grading scale thresholds when `marks_obtained` is saved.
5. WHEN a student's result is published, THE ERP_System SHALL send a result notification to the student and linked parent.
6. THE ERP_System SHALL allow the HOD and Principal to view a grade distribution report (A+, A, B+, B, C+, C, D, F counts) for any subject or semester within their scope.

---

### Requirement 11: Records Management

**User Story:** As a Principal, I want to manage critical institutional records and documents, so that legal, academic, and administrative documents are securely stored and retrievable.

#### Acceptance Criteria

1. THE ERP_System SHALL allow the Principal to upload, categorise, and delete institutional documents with fields: `title`, `category`, `file_path`, `uploaded_by`, and `uploaded_at`.
2. THE ERP_System SHALL restrict document download access based on role: Level 1 (Highly Sensitive) documents are accessible only to `principal`; Level 2 (Private) documents are accessible to `principal` and `hod`; Level 3 (Public) documents are accessible to all authenticated users.
3. WHEN a document is uploaded, THE ERP_System SHALL validate the file type against an allowlist (pdf, docx, xlsx, jpg, png) and reject files exceeding 20 MB.
4. THE ERP_System SHALL store student ID documents and certificate paths on the Student record and make them accessible to the student and Principal only.
5. THE ERP_System SHALL log every document upload, download, and deletion event in the Audit_Log.

---

### Requirement 12: Study Material Management

**User Story:** As a Teacher, I want to upload study materials for my subjects, so that students can access course content at any time.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Teachers to upload StudyMaterial records linked to a Subject with `title`, `description`, `file_path`, `subject_id`, and `uploaded_by` fields.
2. WHEN a study material is uploaded, THE ERP_System SHALL send a notification to all students enrolled in the linked subject.
3. THE ERP_System SHALL allow Students to view and download study materials for subjects they are enrolled in.
4. THE ERP_System SHALL allow Teachers to update or delete their own study materials.
5. IF a student attempts to access a study material for a subject they are not enrolled in, THEN THE ERP_System SHALL return an HTTP 403 response.

---

### Requirement 13: Assignment Management

**User Story:** As a Teacher, I want to create assignments and receive student submissions, so that coursework is managed digitally within the ERP.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Teachers to create Assignment records with `title`, `description`, `due_date`, `subject_id`, and `max_marks` fields.
2. WHEN an assignment is created, THE ERP_System SHALL send an assignment notification to all enrolled students.
3. THE ERP_System SHALL allow Students to submit assignments by uploading a file before the `due_date`.
4. IF a student attempts to submit an assignment after the `due_date`, THEN THE ERP_System SHALL reject the submission and return a descriptive error message.
5. THE ERP_System SHALL allow Teachers to view all submissions for an assignment and record a mark and feedback for each submission.

---

### Requirement 14: Timetable Management

**User Story:** As a HOD, I want to create and manage class timetables for my department, so that teachers and students have a clear schedule.

#### Acceptance Criteria

1. THE ERP_System SHALL allow HOD users to create TimetableSlot records with `subject_id`, `teacher_id`, `day_of_week`, `start_time`, `end_time`, and `semester` fields.
2. WHEN a TimetableSlot is created, THE ERP_System SHALL validate that the teacher has no conflicting slot on the same day and time.
3. THE ERP_System SHALL allow HOD users to create TimetableGapOverride records to mark exceptions (cancelled classes, special events).
4. THE ERP_System SHALL allow Teachers and Students to view their own timetable filtered by their assigned subjects and semester.
5. THE ERP_System SHALL allow the Principal to view timetables across all departments.

---

### Requirement 15: Notice and Communication Management

**User Story:** As a Principal, HOD, or Teacher, I want to publish notices and communicate with users, so that important information reaches the right audience promptly.

#### Acceptance Criteria

1. THE ERP_System SHALL allow `principal`, `hod`, and `teacher` roles to create Notice records with `title`, `message`, `category`, `target_role`, and `published_at` fields.
2. WHEN a Notice is published, THE ERP_System SHALL send a notification to all users matching the `target_role` value.
3. THE ERP_System SHALL support bilingual notices with separate `title` and `message` fields for English and Nepali (BS) content.
4. THE ERP_System SHALL allow Students and Parents to view notices targeted at their role, ordered by `published_at` descending.
5. THE ERP_System SHALL allow the Principal to delete any notice; HOD and Teacher users may only delete notices they created.

---

### Requirement 16: Gallery Management

**User Story:** As a Principal or HOD, I want to manage a photo gallery, so that institutional events and activities are showcased on the public website.

#### Acceptance Criteria

1. THE ERP_System SHALL allow `principal` and `hod` roles to upload Gallery records with `title`, `description`, `image_path`, `category`, and `is_public` fields.
2. WHEN a gallery image is uploaded, THE ERP_System SHALL validate the file as an image (jpeg, png, gif, webp) with a maximum size of 4 MB.
3. THE ERP_System SHALL allow the Principal to toggle the `is_public` flag on any gallery item to control public visibility.
4. WHEN a gallery item is marked `is_public = true`, THE ERP_System SHALL make it available through the public Gallery API endpoint.
5. THE ERP_System SHALL allow the Principal and HOD to delete gallery items, removing the associated file from storage.

---

### Requirement 17: Webpage Control Module

**User Story:** As a Principal, I want to control the content of the public-facing landing page, alumni page, and media page from within the ERP, so that the website stays current without requiring developer intervention.

#### Acceptance Criteria

1. THE ERP_System SHALL provide a Webpage Control interface accessible only to the `principal` role for managing public page content.
2. THE ERP_System SHALL allow the Principal to update the landing page hero images, programs section, contact information, and department description stored in the `departments` table.
3. THE ERP_System SHALL allow the Principal to manage alumni profiles displayed on the Alumni page, including name, graduation year, program, and photo.
4. THE ERP_System SHALL allow the Principal to manage media items (images, videos) displayed on the Media page with `title`, `media_type`, `url`, and `is_published` fields.
5. WHEN public page content is updated, THE ERP_System SHALL invalidate the relevant API response cache within 60 seconds.

---

### Requirement 18: Public Website with Secure API Data Flow

**User Story:** As a Public_User, I want to access the institution's public website (landing, alumni, media pages) that displays accurate, up-to-date information, so that I can learn about the institution without requiring a login.

#### Acceptance Criteria

1. THE ERP_System SHALL serve all public pages (landing, alumni, media, notices, gallery, contact) without requiring authentication.
2. THE ERP_System SHALL enforce the data flow: Public_Page → Secure_API → Service_Layer → Database; public pages MUST NOT directly query the database or access Eloquent models.
3. THE ERP_System SHALL expose read-only JSON API endpoints under `/api/public/` for: landing page content, alumni list, gallery items, public notices, events, and contact information.
4. WHEN a public API endpoint is called, THE ERP_System SHALL return only Level 3 (Public) data and MUST NOT expose Level 1 or Level 2 data in any response field.
5. THE ERP_System SHALL apply HTTP response caching with a minimum TTL of 300 seconds to all public API endpoints.
6. THE ERP_System SHALL apply rate limiting of 60 requests per minute per IP address to all public API endpoints.
7. THE ERP_System SHALL serve all public pages with a responsive layout compatible with mobile, tablet, and desktop viewports.

---

### Requirement 19: Reporting and Analytics

**User Story:** As a Principal or HOD, I want comprehensive reports on attendance, academic performance, and user activity, so that I can make informed decisions about the institution.

#### Acceptance Criteria

1. THE ERP_System SHALL provide the Principal with system-wide reports: total students by department, attendance summary by department, grade distribution by semester, and user activity summary.
2. THE ERP_System SHALL provide the HOD with department-scoped reports: attendance per subject, grade distribution per subject, teacher workload summary, and student performance trends.
3. WHEN a report is requested, THE ERP_System SHALL generate it within 5 seconds for datasets up to 10,000 records by using optimised queries and database indexes.
4. THE ERP_System SHALL allow the Principal and HOD to export reports as PDF and CSV formats.
5. THE ERP_System SHALL display attendance data as time-series charts with selectable periods: weekly (7 days), monthly (12 months), and semester (6 buckets).
6. THE ERP_System SHALL display grade distribution as a pie chart with segments for grades A+, A, B+, B, C+, C, D, and F.

---

### Requirement 20: User Activity Logging and Monitoring

**User Story:** As a Principal, I want a complete audit trail of all user actions, so that I can investigate incidents and ensure accountability.

#### Acceptance Criteria

1. THE ERP_System SHALL record an Audit_Log entry for every create, update, delete, login, logout, login_failed, and password_reset event across all authenticated users.
2. WHEN an Audit_Log entry is created, THE ERP_System SHALL capture: `user_id`, `action`, `model_type`, `model_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, and `timestamp`.
3. THE ERP_System SHALL provide the Principal with a searchable, paginated audit log viewer filtered by user, action type, date range, and model type.
4. THE ERP_System SHALL prevent any user (including the Principal) from modifying or deleting Audit_Log records through the application interface.
5. WHEN a login failure occurs, THE ERP_System SHALL record the attempted credentials' field names (not values) and the IP address in the Audit_Log.
6. THE ERP_System SHALL retain Audit_Log records for a minimum of 12 months before archival.

---

### Requirement 21: Security and Data Protection

**User Story:** As a Principal, I want the system to enforce strong security controls, so that sensitive student and institutional data is protected from unauthorised access and breaches.

#### Acceptance Criteria

1. THE ERP_System SHALL hash all user passwords using bcrypt with a minimum cost factor of 12 before storage.
2. THE ERP_System SHALL enforce CSRF protection on all state-changing web routes.
3. THE ERP_System SHALL validate and sanitise all user-supplied input before processing or persisting, rejecting inputs that exceed defined length limits or contain disallowed characters.
4. THE ERP_System SHALL apply rate limiting of 5 failed login attempts per 15 minutes per IP address, after which the IP is temporarily blocked for 15 minutes.
5. THE ERP_System SHALL support two-factor authentication (2FA) via a time-limited code sent to the user's registered email.
6. THE ERP_System SHALL encrypt Level 1 (Highly Sensitive) data fields at rest using AES-256 encryption.
7. THE ERP_System SHALL enforce HTTPS for all routes in production by redirecting HTTP requests to HTTPS.
8. WHEN an API token is issued via Laravel Sanctum, THE ERP_System SHALL scope the token to the minimum required abilities for the requesting role.
9. THE ERP_System SHALL implement Content Security Policy (CSP) headers on all responses to mitigate XSS attacks.
10. IF an error occurs during request processing, THEN THE ERP_System SHALL return a generic error message to the client and log the full exception details server-side, without exposing stack traces or database details.

---

### Requirement 22: Data Backup and Recovery

**User Story:** As a Principal, I want automated and on-demand data backups with a tested recovery process, so that institutional data can be restored after any failure.

#### Acceptance Criteria

1. THE ERP_System SHALL perform automated daily database backups at a configurable time, storing compressed and encrypted backup files in the `storage/backups` directory.
2. THE ERP_System SHALL include uploaded files (profile photos, study materials, gallery images, documents) in the backup alongside the database dump.
3. WHEN a backup is created, THE ERP_System SHALL record a backup event in the Audit_Log with the file name, size, and creation timestamp.
4. THE ERP_System SHALL allow the Principal to trigger an on-demand backup from the system settings interface.
5. THE ERP_System SHALL allow the Principal to download any stored backup file through the admin interface.
6. THE ERP_System SHALL allow the Principal to initiate a database restore from a selected backup file, with a confirmation step before execution.
7. IF a backup file is corrupted or fails integrity verification, THEN THE ERP_System SHALL mark the backup as invalid and notify the Principal.
8. THE ERP_System SHALL retain the 30 most recent backup files and automatically delete older backups to manage storage.

---

### Requirement 23: Profile Management

**User Story:** As any authenticated user, I want to view and update my own profile information, so that my contact details and preferences are current.

#### Acceptance Criteria

1. THE ERP_System SHALL allow all authenticated users to view and update their own `name`, `email`, `phone`, `bio`, and `profile_photo_path` fields.
2. WHEN a user updates their email address, THE ERP_System SHALL require email verification before the new address becomes active.
3. THE ERP_System SHALL allow users to change their password by providing the current password and a new password of at least 8 characters.
4. THE ERP_System SHALL allow users to upload a profile photo in jpeg, png, or gif format with a maximum size of 2 MB, replacing any existing photo.
5. THE ERP_System SHALL display the user's role-appropriate dashboard link and navigation after profile updates without requiring re-login.

---

### Requirement 24: Notification System

**User Story:** As any authenticated user, I want to receive in-app and email notifications for relevant events, so that I stay informed without manually checking the system.

#### Acceptance Criteria

1. THE ERP_System SHALL deliver notifications via both the Laravel database notification channel (in-app) and the email channel for the following events: new exam, attendance alert, new result, new assignment, new notice, and account creation.
2. WHEN a notification is created, THE ERP_System SHALL store it in the `notifications` table with `type`, `notifiable_id`, `notifiable_type`, `data`, and `read_at` fields.
3. THE ERP_System SHALL display an unread notification count badge in the navigation bar for all authenticated users, updated without a full page reload.
4. THE ERP_System SHALL allow users to mark individual notifications or all notifications as read.
5. THE ERP_System SHALL allow the Principal to enable or disable each notification type globally via the System Settings panel.
6. WHEN email delivery fails for a notification, THE ERP_System SHALL log the failure and continue processing remaining notifications without throwing an unhandled exception.

---

### Requirement 25: UI/UX Performance and Usability

**User Story:** As any user, I want a fast, clean, and easy-to-navigate interface, so that I can complete tasks efficiently without confusion or performance delays.

#### Acceptance Criteria

1. THE ERP_System SHALL render all authenticated dashboard pages with a Time to First Byte (TTFB) of under 500 ms on a standard server with up to 500 concurrent users.
2. THE ERP_System SHALL use solid background colours, clean typography, and a consistent layout across all pages, avoiding heavy gradients or decorative images that increase page weight.
3. THE ERP_System SHALL implement lazy loading for images and deferred loading for non-critical JavaScript to reduce initial page load size.
4. THE ERP_System SHALL provide a responsive navigation menu that collapses to a mobile-friendly format on viewports narrower than 768 px.
5. THE ERP_System SHALL display role-appropriate navigation items only, hiding menu entries for routes the current user's role cannot access.
6. THE ERP_System SHALL provide inline form validation feedback without requiring a full page reload.
7. THE ERP_System SHALL cache rendered views and database query results where data changes infrequently, using Laravel's cache layer with appropriate TTL values.
8. THE ERP_System SHALL support both English and Nepali (BS) language display, switchable per user session, for all UI labels, dates, and notices.

---

### Requirement 26: Parent Portal

**User Story:** As a Parent, I want to monitor my linked student's attendance, results, and notices, so that I can stay informed about their academic progress.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Parent users to view the attendance records of their linked student, showing present, absent, and late counts per subject.
2. THE ERP_System SHALL allow Parent users to view the exam results of their linked student, including marks obtained, grade, and subject name.
3. THE ERP_System SHALL allow Parent users to view notices targeted at the `parent` or `student` role.
4. THE ERP_System SHALL restrict Parent users from viewing data for any student other than their directly linked student.
5. THE ERP_System SHALL allow Parent users to update their own profile information.

---

### Requirement 27: Student Portal

**User Story:** As a Student, I want to access my academic records, materials, and assignments through a personal portal, so that I can manage my studies effectively.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Student users to view their own attendance records per subject, including percentage and status breakdown.
2. THE ERP_System SHALL allow Student users to view their own exam results, including marks, grade, and subject name.
3. THE ERP_System SHALL allow Student users to view their class timetable for the current semester.
4. THE ERP_System SHALL allow Student users to download study materials for subjects they are enrolled in.
5. THE ERP_System SHALL allow Student users to submit assignments before the due date by uploading a file.
6. THE ERP_System SHALL allow Student users to view notices targeted at the `student` role.
7. THE ERP_System SHALL restrict Student users from viewing other students' records, marks, or attendance data.

---

### Requirement 28: Teacher Portal

**User Story:** As a Teacher, I want a dedicated portal to manage attendance, marks, assignments, and materials for my assigned subjects, so that I can fulfil my teaching responsibilities efficiently.

#### Acceptance Criteria

1. THE ERP_System SHALL allow Teacher users to view only the subjects assigned to them via the `subject_teacher` pivot table.
2. THE ERP_System SHALL allow Teacher users to record and update attendance for students enrolled in their assigned subjects.
3. THE ERP_System SHALL allow Teacher users to create exams and enter marks for students in their assigned subjects.
4. THE ERP_System SHALL allow Teacher users to upload, update, and delete study materials for their assigned subjects.
5. THE ERP_System SHALL allow Teacher users to create assignments and view student submissions for their assigned subjects.
6. THE ERP_System SHALL allow Teacher users to view a class report showing attendance rates and grade distributions for their assigned subjects.
7. THE ERP_System SHALL restrict Teacher users from accessing student records, marks, or attendance data for subjects not assigned to them.
