# IT-DMS (Department Management System) - Comprehensive Use Cases

## Table of Contents
1. [System Overview](#system-overview)
2. [Actors](#actors)
3. [Admin Use Cases](#admin-use-cases)
4. [Teacher Use Cases](#teacher-use-cases)
5. [Student Use Cases](#student-use-cases)
6. [Parent Use Cases](#parent-use-cases)
7. [System Use Cases](#system-use-cases)

---

## System Overview

The IT Department Management System (IT-DMS) is a comprehensive educational management platform that streamlines academic operations across multiple stakeholders. It provides role-based access control, and integrates all aspects of academic management from enrollment to assessment.

**Core Objectives:**
- Centralize academic data and processes
- Automate attendance tracking and mark management
- Facilitate teacher-student-parent communication
- Maintain audit trails for compliance
- Provide real-time academic insights

---

## Actors

### 1. **Admin (System Administrator)**
- High-level system control
- User and department management
- System configuration
- Audit monitoring
- Report generation

### 2. **Teacher (Faculty Member)**
- Course management
- Attendance recording
- Mark submission and management
- Study material distribution
- Notice publishing
- Student communication

### 3. **Student (Learner)**
- Course enrollment (core & elective)
- View personal grades
- Check attendance
- Access study materials
- View notifications and notices
- Profile management

### 4. **Parent/Guardian**
- Monitor child's performance
- Access attendance records
- View academic progress
- Communicate with teachers/admin
- Generate reports

---

## Admin Use Cases

### UC-AD-001: Dashboard Access
**Primary Actor:** Admin
**Description:** Admin accesses the system dashboard to view overall statistics
**Preconditions:** Admin is logged in
**Main Flow:**
1. Admin logs in with credentials
2. Dashboard displays key metrics:
   - Total students, teachers, departments
   - Active courses and semesters
   - Recent system activities
   - Enrollment statistics
**Postconditions:** Admin can navigate to management modules

---

### UC-AD-002: Manage Departments
**Primary Actor:** Admin
**Description:** Create, view, update, and delete departments
**Main Flow:**
1. Admin navigates to Department Management
2. Admin can:
   - **Create:** Add new department with name, code, and description
   - **View:** List all departments with details
   - **Update:** Edit department information
   - **Delete:** Remove inactive departments
3. System validates department code uniqueness
4. Audit log records all changes

**Related Data:** Department name, code, head of department, contact info

---

### UC-AD-003: Manage Users
**Primary Actor:** Admin
**Description:** Complete user lifecycle management
**Sub-Use Cases:**
#### UC-AD-003a: Create User
1. Admin selects user role (Admin, Teacher, Student, Parent)
2. Enters user details:
   - Name, Email, Username, Phone
   - Department assignment
   - Initial password
3. System creates user account
4. Activation email sent (if email verified)
5. User can reset password on first login

#### UC-AD-003b: View Users
1. Admin displays user list with filters:
   - Filter by role (Admin/Teacher/Student/Parent)
   - Filter by department
   - Search by name/email
   - Sort by status (active/inactive/suspended)
2. View detailed user profile

#### UC-AD-003c: Update User
1. Admin selects user to modify
2. Edits allowed fields:
   - Contact information
   - Department assignment
   - Status (active/inactive/suspended)
3. System updates record
4. Audit log entry created

#### UC-AD-003d: Deactivate/Suspend User
1. Admin marks user as inactive or suspended
2. User cannot access system
3. Related records remain intact

**Exception:** Cannot delete users with active relationships (students with marks, etc.)

---

### UC-AD-004: Manage Semesters
**Primary Actor:** Admin
**Description:** Define academic periods for the institution
**Main Flow:**
1. Admin navigates to Semester Management
2. Creates/Updates semester:
   - Semester number (1-8)
   - Academic year
   - Start and end dates
   - Status (active/inactive)
3. System validates date ranges
4. Only one semester can be active at a time
5. Semester appears in student/teacher course lists

**Impact:** Affects student enrollment, attendance records, grade tracking

---

### UC-AD-005: Manage Subjects/Courses
**Primary Actor:** Admin (may delegate to Teachers)
**Description:** Define all courses offered in the department
**Main Flow:**
1. Admin accesses Course Management
2. **Create Subject:**
   - Subject name (English)
   - Subject code (unique)
   - Semester level
   - Credits
   - Subject type (Core/Elective/Optional)
   - Theory percentage (e.g., 70%)
   - Practical percentage (e.g., 30%)
   - Has lab component (Yes/No)
   - Weekly lecture/practical hours
   - Capacity limits (for electives)
3. **Assign Teachers:**
   - Map one or more teachers to subject
   - Define teacher role (primary/co-teacher)
4. **Enroll Students:**
   - For core subjects: Auto-enroll eligible students
   - For electives: Students request enrollment
5. **Update/Delete:**
   - Edit subject details
   - Archive inactive subjects

**Validation Rules:**
- Subject code must be unique
- Theory + Practical = 100%
- Credits must be > 0

---

### UC-AD-006: Assign Teachers to Subjects
**Primary Actor:** Admin
**Description:** Map teachers to courses they will teach
**Main Flow:**
1. Admin selects semester and subject
2. Displays available teachers with qualifications
3. Selects teacher(s) for assignment
4. System creates subject_teacher relationship
5. Notification sent to teacher
6. Teacher appears in student list for that subject

**Constraints:**
- Cannot assign teacher to subject outside their qualification
- Teacher cap can be set per subject

---

### UC-AD-007: Manage System Settings
**Primary Actor:** Admin
**Description:** Configure system-wide parameters
**Settings Include:**
- **Academic Settings:**
  - Grading scale (A, B, C, D, F)
  - Pass marks threshold
  - Attendance percentage requirement
  - GPA calculation method
- **System Settings:**
  - File upload limits
  - Session timeout duration
  - System maintenance mode
- **Email Settings:**
  - SMTP configuration
  - Email templates
  - Notification schedules

---

### UC-AD-008: View Audit Logs
**Primary Actor:** Admin
**Description:** Monitor all system changes for compliance
**Main Flow:**
1. Admin accesses Audit Log module
2. Filter by:
   - User who made the change
   - Action type (Create/Update/Delete)
   - Date range
   - Resource type
3. View detailed log entries showing:
   - Who changed it
   - What changed (field names and old/new values)
   - When it changed
   - From which IP address
4. Export audit log to CSV/PDF

**Immutable Records:** Audit logs cannot be deleted or modified

---

### UC-AD-009: Generate Reports
**Primary Actor:** Admin
**Description:** Create system-wide reports
**Report Types:**
1. **Student Reports:**
   - Total enrollment by semester/department
   - Student demographics
   - Dropout analysis
2. **Academic Reports:**
   - Course completion rates
   - Grade distribution
   - Teacher performance metrics
3. **Operational Reports:**
   - User activity logs
   - System performance metrics
   - File upload statistics
4. **Export:** PDF, Excel, CSV formats

---

### UC-AD-010: Manage Parent Accounts
**Primary Actor:** Admin
**Description:** Link parents to students
**Main Flow:**
1. Admin creates parent account
2. Associates parent with one or more students
3. Parent receives login credentials
4. Parent can access linked children's information
5. Can update parent-student relationships

---

## Teacher Use Cases

### UC-T-001: Teacher Dashboard
**Primary Actor:** Teacher
**Description:** Access teacher-specific overview
**Dashboard Shows:**
- Assigned subjects and student counts
- Today's schedule/timetable
- Pending tasks (marks to upload, attendance to mark)
- Recent student submissions
- Notifications
- Quick access buttons to common tasks

---

### UC-T-002: View Assigned Subjects
**Primary Actor:** Teacher
**Description:** View all courses assigned to the teacher
**Main Flow:**
1. Teacher accesses Subject List
2. Display shows:
   - Subject name and code
   - Semester
   - Number of enrolled students
   - Subject type (Core/Elective)
3. Click on subject to view:
   - Enrolled students
   - Timetable slots
   - Materials uploaded
   - Marks uploaded
   - Attendance records

---

### UC-T-003: Record Attendance
**Primary Actor:** Teacher
**Description:** Mark student attendance for class sessions
**Main Flow:**
1. Teacher selects subject and date
2. System displays enrolled students
3. For each student, teacher marks:
   - **Present (P)**
   - **Absent (A)**
   - **With Permission (WP)**
   - **Leave (L)**
4. Optional: Add remarks/notes
5. Submit attendance record
6. System validates and saves

**Constraints:**
- Attendance can only be marked for today or past dates
- Cannot modify locked attendance (after 7 days)
- Must mark all students before submission

**Outcomes:**
- Attendance record created
- System calculates percentage automatically
- Notification sent to affected students/parents

---

### UC-T-004: Upload Marks
**Primary Actor:** Teacher
**Description:** Submit student assessment scores
**Mark Types:**
1. **Continuous Assessment Marks:**
   - Internal assignments
   - Quizzes
   - Class participation
   - Practical assignments

2. **Exam Marks:**
   - Mid-term exam
   - Final/End-term exam
   - Practical exam

**Main Flow:**
1. Teacher selects subject and mark type
2. System displays:
   - Enrolled students in subject
   - Current marks (if any)
3. Teacher enters marks for each student:
   - Theory marks
   - Practical marks
4. System auto-calculates total marks
5. Review and submit marks
6. System validates:
   - Marks within range (0-100)
   - All students have entries
   - Weightage totals correctly
7. Marks locked and visible to students

**Features:**
- **Bulk Upload:** Import marks from Excel file
- **Template Generation:** Download template specific to subject
- **Mark Review:** See historical mark entries
- **Correction:** Can request to modify submitted marks

---

### UC-T-005: Create and Publish Notices
**Primary Actor:** Teacher
**Description:** Communicate updates and announcements to students
**Notice Statuses:**
- **Draft:** Not yet published
- **Published:** Visible to target audience
- **Scheduled:** Publish at specific date/time
- **Archived:** Hidden but retained

**Main Flow:**
1. Teacher clicks "Create Notice"
2. Enters notice details:
   - Title
   - Content (English)
   - Audience (specific subject, all students, etc.)
   - Priority (Normal/Important/Urgent)
3. Attach files (if needed):
   - PDF documents
   - Images
   - Other resources
4. Choose action:
   - **Save as Draft:** Editable later
   - **Schedule:** Set publish date/time
   - **Publish Immediately:** Visible to students now
5. Review before publishing
6. Publish

**Outcomes:**
- Notice visible in student portal
- Notification sent to target students
- Notice log created for audit

**Visibility Control:**
- By subject: Show to enrolled students
- By semester: Show to all students in semester
- Public: Show to all authenticated users

---

### UC-T-006: Upload Study Materials
**Primary Actor:** Teacher
**Description:** Share educational resources with students
**Material Types:**
- Lecture notes (PDF, DOC, PPT)
- Video lectures
- Reference books
- Code samples
- Practice problems
- Solutions

**Main Flow:**
1. Teacher selects subject
2. Clicks "Upload Material"
3. Provides material details:
   - Title
   - Description
   - Category (Lecture Notes, Reference, Problems, etc.)
   - Week/Unit number
   - Availability date (can schedule for future)
4. Uploads file(s)
5. Sets access permissions:
   - Available to subject's enrolled students
   - Optionally password-protected
6. Publishes material

**Outcomes:**
- Material appears in student's study materials section
- Notification sent to students
- Download tracked for analytics

**Constraints:**
- Max file size: 100MB per file
- Total storage per teacher: Configurable (e.g., 10GB)

---

### UC-T-007: Manage Timetable
**Primary Actor:** Teacher
**Description:** View and manage class schedule
**Main Flow:**
1. Teacher views timetable for assigned subjects
2. Displays:
   - Subject name, code, room number
   - Day and time slots
   - Duration (e.g., 60 min, 90 min)
   - Student count
3. Teacher can:
   - **View Timetable:** See weekly schedule
   - **Request Adjustment:** Request schedule change with justification
   - **Apply Override:** For one-off changes (temporary reschedule due to exam)
   - **View Gaps:** See free time slots

---

### UC-T-008: View and Manage Exams
**Primary Actor:** Teacher
**Description:** Manage examination scheduling and grading
**Main Flow:**
1. Teacher accesses Exam Management
2. Views exams for assigned subjects:
   - Exam name, type, date/time
   - Room allocation
   - Invigilator assignment
3. For exam management:
   - **Upload exam marks:** Enter marks after exam
   - **Generate admit cards:** For students
   - **View exam result analysis:** Pass rate, grade distribution
   - **Handle revaluation:** Accept/reject recheck requests

---

### UC-T-009: Export and Generate Reports
**Primary Actor:** Teacher
**Description:** Create academic reports for analysis
**Report Types:**
1. **Class Performance Report:**
   - Average marks by assignment
   - Grade distribution
   - Top/bottom performers
2. **Attendance Report:**
   - Student-wise attendance percentage
   - Identified absent students
   - Attendance trends
3. **Individual Student Report:**
   - Complete assessment record
   - Attendance status
   - Progress tracking
4. **Subject Analytics:**
   - Pass rate
   - Subject difficulty index
   - Comparative performance

**Export Formats:** PDF, Excel

---

### UC-T-010: View Student Information
**Primary Actor:** Teacher
**Description:** Access enrolled student details
**Information Available:**
- Personal details (name, roll number, contact)
- Semester and section
- Photo
- Course enrollments
- Academic history
- Attendance percentage
- Current marks
- Emergency contact

---

### UC-T-011: Receive and Manage Notifications
**Primary Actor:** Teacher
**Description:** Get system alerts and messages
**Notification Types:**
- Attendance/marks deadline reminders
- New student enrollments
- System maintenance alerts
- Important admin messages
- Parent/student inquiries
- Exception alerts (e.g., low marks)

---

### UC-T-012: Manage Teacher Profile
**Primary Actor:** Teacher
**Description:** Maintain personal and professional information
**Profile Information:**
- Personal details:
  - Full name, email, phone
  - Date of birth
  - Gender, blood group
  - Address
- Professional details:
  - Teacher code
  - Qualifications
  - Specialization
  - Years of experience
  - Employment type
  - Department
  - Salary (if permitted to view)
- Profile photo and resume upload

**Actions:**
- Update profile
- Change password
- Manage notifications preferences
- Set availability/leave status

---

## Student Use Cases

### UC-S-001: Student Dashboard
**Primary Actor:** Student
**Description:** Access personalized student overview
**Dashboard Displays:**
- Current semester and GPA
- Enrolled courses (Core and Elective)
- Recent marks and grades
- Attendance overview
- Upcoming classes and exams
- New notices and announcements
- Quick links to common tasks

---

### UC-S-002: View Enrolled Courses
**Primary Actor:** Student
**Description:** Access list of courses student is enrolled in
**Main Flow:**
1. Student navigates to "My Courses"
2. System shows courses organized by:
   - Core courses (mandatory for semester)
   - Elective courses (chosen by student)
3. For each course, display:
   - Course code and name
   - Credits
   - Assigned teacher
   - Class schedule
   - Course status (ongoing/completed/upcoming)
4. Click on course to view:
   - Course syllabus
   - Course materials
   - Marks
   - Attendance

---

### UC-S-003: Enroll in Elective Courses
**Primary Actor:** Student
**Description:** Select optional courses for current semester
**Preconditions:**
- Elective enrollment window is open
- Student meets prerequisites
- Course capacity not exceeded

**Main Flow:**
1. Student navigates to "Elective Enrollment"
2. Views available electives:
   - Course name, code, credits
   - Course description
   - Instructor
   - Capacity and current enrollment
   - Required prerequisites
3. Views current enrollments:
   - Total credits enrolled (core + elective)
4. Student selects electives:
   - Can add multiple courses
   - Validates total credit load (min-max constraints)
5. Submits enrollment request
6. System validates:
   - Credit limits
   - Prerequisites
   - Course capacity
7. Enrollment confirmed
8. Notification sent to student and teacher

**Exception Handling:**
- If electives exceed credit limit: Show error with recommendation
- If course full: Show waitlist option
- Prerequisites not met: Show requirement message

**Constraints:**
- Cannot exceed maximum credits per semester (typically 15-18)
- Cannot go below minimum credits (typically 12)
- Cannot enroll after deadline

---

### UC-S-004: View Attendance
**Primary Actor:** Student
**Description:** Check personal attendance records
**Main Flow:**
1. Student navigates to "My Attendance"
2. View options:
   - **By Course:** Attendance for each enrolled subject
   - **Summary:** Overall attendance percentage
   - **Semester View:** Attendance trends across semester
3. For each subject, view:
   - Total classes held
   - Classes attended
   - Attendance percentage
   - Status (meets requirement or deficient)
   - Leaves/excused absences (if any)
4. Can download attendance report (PDF)

**Attendance Status Indicator:**
- ✅ Green: 75% or above (good standing)
- 🟡 Yellow: 60-75% (borderline)
- ❌ Red: Below 60% (deficient - may affect exam eligibility)

**Alert:** If attendance drops below required threshold, system sends notification

---

### UC-S-005: View Marks and Grades
**Primary Actor:** Student
**Description:** Access assessment scores and grades
**Main Flow:**
1. Student navigates to "My Marks"
2. View options:
   - **By Course:** Marks for each subject
   - **Overall Performance:** GPA and grade summary
3. For each subject, view:
   - Theory marks
   - Practical marks
   - Total marks obtained vs. total marks
   - Grade (A, B, C, D, F)
   - Percentage
4. View mark breakdown:
   - Continuous assessment scores
   - Quiz marks
   - Assignment marks
   - Exam marks
5. Historical marks:
   - View marks from previous semesters
   - Track improvement/decline
6. Download transcript (Official marks sheet)
   - Can request official transcript
   - System generates PDF with stamp/seal

**Mark Visibility:**
- Marks only visible after teacher submits and locks them
- Cannot modify marks once submitted
- Amendment requests can be filed if student believes error

---

### UC-S-006: Download Study Materials
**Primary Actor:** Student
**Description:** Access course study resources uploaded by teachers
**Main Flow:**
1. Student selects a course
2. Navigates to "Study Materials"
3. Browse materials organized by:
   - Category (Lecture Notes, Reference, Problems, etc.)
   - Week/Unit number
   - Upload date
4. View material details:
   - Title, description
   - Uploaded date
   - File size
   - Download count
5. Download material
6. Can add to favorites/bookmarks
7. View download history

**Features:**
- Search materials by keyword
- Filter by type and date
- Share material link with classmates (if allowed)
- View material preview (if available)

---

### UC-S-007: View Notices and Announcements
**Primary Actor:** Student
**Description:** Receive and view notifications and notices
**Main Flow:**
1. Student navigates to "Notices"
2. View notices organized by:
   - Priority (Important/Urgent marked with badges)
   - Date (newest first)
   - Category (Academic/Administrative/Event)
   - Relevance (by course/semester)
3. For each notice, view:
   - Title and content
   - Date published
   - Attachments
   - Importance level
4. Features:
   - Mark as read/unread
   - Archive notices
   - Search notices
   - Filter by course
5. Notification badge:
   - Shows count of unread notices
   - Alert sound/email for urgent notices

---

### UC-S-008: Update Profile
**Primary Actor:** Student
**Description:** Maintain personal information
**Editable Fields:**
- Email
- Phone number
- Address
- Date of birth (might be restricted)
- Profile photo
- Emergency contact
- Blood group (medical info)

**Non-Editable Fields:**
- Roll number
- Registration number
- Name (requires admin approval)
- Semester

**Actions:**
1. Click "Edit Profile"
2. Update allowed fields
3. Upload new profile photo (if needed)
4. Save changes
5. System notifies of successful update

**Exceptions:**
- Name changes require admin approval
- Changes logged in audit trail

---

### UC-S-009: Download Transcript/Certificate
**Primary Actor:** Student
**Description:** Generate official academic documents
**Document Types:**
1. **Academic Transcript:**
   - All marks and grades
   - GPA
   - Official stamp/seal
   - Request date

2. **Character Certificate:**
   - Conduct record
   - Issued by admin
   - Official seal

3. **Completion Certificate:**
   - Upon graduation
   - Lists all completed courses
   - Final GPA and grades

**Main Flow:**
1. Student navigates to "Documents"
2. Selects document type
3. Request is submitted
4. Admin/authorized person approves
5. Document generated in PDF format
6. Student can download or request hardcopy courier
7. System tracks document requests

---

### UC-S-010: Manage Notifications
**Primary Actor:** Student
**Description:** Control notification preferences
**Notification Types:**
- Mark submission alerts
- Attendance reminders
- Notice announcements
- Exam schedule updates
- System alerts

**Preferences:**
- Choose notification channels:
  - In-app only
  - Email
  - SMS (if available)
- Set quiet hours (do not disturb times)
- Select which notifications to receive
- Frequency preferences (immediate/daily digest)

---

### UC-S-011: View Class Timetable
**Primary Actor:** Student
**Description:** Access personal course schedule
**Main Flow:**
1. Student navigates to "My Timetable"
2. View weekly schedule:
   - Day, time, subject, room number
   - Teacher name
   - Type (Lecture/Practical/Lab)
   - Duration
3. View options:
   - Weekly view
   - Monthly calendar view
   - Subject-wise view
4. Features:
   - Can add personal notes
   - Set reminders
   - Download timetable (PDF)
   - Get calendar sync (iCal format)

---

## Parent Use Cases

### UC-P-001: Parent Dashboard
**Primary Actor:** Parent
**Description:** Access overview of child's academic progress
**Dashboard Shows:**
- Child's basic info (name, roll number, semester)
- Current GPA and grades summary
- Attendance percentage
- Recent marks updates
- Upcoming exams
- Important notices
- Teacher contact information

---

### UC-P-002: Link Child Accounts
**Primary Actor:** Parent
**Description:** Associate multiple children to parent account
**Main Flow:**
1. Parent requests linking of student account
2. Enters child's roll number or email
3. System verifies:
   - Parent is listed as guardian
   - Student exists in system
4. Link approved
5. Parent can now access child's information
6. Can link multiple children
7. Manage which children are linked/unlinked

---

### UC-P-003: View Child's Attendance
**Primary Actor:** Parent
**Description:** Monitor child's class attendance
**Main Flow:**
1. Parent navigates to "Child's Attendance"
2. Select child (if multiple)
3. View:
   - Overall attendance percentage
   - Attendance by course
   - Recent attendance entries
   - Course-wise breakdown
4. Receive alerts if:
   - Attendance drops below threshold (e.g., 75%)
   - Child is marked absent
   - Attendance improves
5. Download attendance report (PDF)

---

### UC-P-004: View Child's Marks
**Primary Actor:** Parent
**Description:** Access child's assessment scores
**Main Flow:**
1. Parent navigates to "Child's Marks"
2. View marks for all courses:
   - Course name and code
   - Theory marks
   - Practical marks
   - Total marks and percentage
   - Grade
3. View performance trends:
   - Marks across semesters
   - Subject-wise performance
   - GPA trends
4. Comparison analytics (optional):
   - Class average vs. child's marks
   - Subject strength/weakness
5. Download report (PDF)
6. Request detailed feedback from teacher

---

### UC-P-005: View Timetable and Calendar
**Primary Actor:** Parent
**Description:** Check child's class schedule
**Main Flow:**
1. Parent views child's timetable
2. Displays weekly schedule with:
   - Subject name and code
   - Time and room
   - Teacher name
   - Type (theory/practical)
3. Get exam schedule
4. Get event calendar
5. Sync with personal calendar (iCal export)

---

### UC-P-006: View Notices and Communications
**Primary Actor:** Parent
**Description:** Receive important announcements
**Main Flow:**
1. Parent navigates to "Notices"
2. Filters:
   - Child-specific notices
   - General announcements
   - Important alerts
3. View notice details
4. Download attachments
5. Mark as read/archive
6. Receive email notifications for important notices

---

### UC-P-007: Download Academic Documents
**Primary Actor:** Parent
**Description:** Access child's official certificates and transcripts
**Documents Available:**
- Academic transcript
- Report card
- Character certificate
- Progress reports
- Attendance certificate

**Main Flow:**
1. Parent requests document
2. Approval from admin (if required)
3. Generate PDF
4. Download or request courier/physical copy
5. Track document requests

---

### UC-P-008: Communicate with Teachers
**Primary Actor:** Parent
**Description:** Contact child's teachers for feedback
**Main Flow:**
1. Parent navigates to "Teachers"
2. View all child's teachers with:
   - Name, subject, contact info
   - Office hours
3. Send message to teacher:
   - Type message
   - Subject line
   - Attach files if needed
   - Send
4. Receive responses
5. View message history
6. Optional: Schedule meeting/consultation

**Constraints:**
- Message history maintained for audit
- Response time SLA (e.g., 48 hours)

---

### UC-P-009: View Events and Holidays
**Primary Actor:** Parent
**Description:** Access academic calendar and events
**Events Include:**
- Semester start/end dates
- Exam dates
- Holiday schedules
- College events
- Important deadlines
- Parent-teacher meetings

---

### UC-P-010: Generate Progress Reports
**Primary Actor:** Parent
**Description:** Create child's academic progress summary
**Report Includes:**
- Overall performance summary
- Subject-wise performance
- Attendance record
- Improvement areas
- Strengths
- Comparison with class average
- Recommendations

**Main Flow:**
1. Parent requests "Progress Report"
2. Select date range
3. System generates PDF report
4. Report includes:
   - Child's photo and basic info
   - GPA and grade summary
   - Graphical representations
   - Teacher comments (if available)
5. Download or print report

---

### UC-P-011: Manage Profile and Preferences
**Primary Actor:** Parent
**Description:** Update parent information and settings
**Profile Fields:**
- Name, email, phone
- Address
- Occupation
- Emergency contact
- Linked children
- Profile photo

**Preferences:**
- Notification settings
- Communication preferences
- Document preferences

---

### UC-P-012: Export Child's Academic Data
**Primary Actor:** Parent
**Description:** Export comprehensive academic records
**Export Formats:** PDF, Excel, CSV

**Includes:**
- Complete academic history
- Marks and grades
- Attendance records
- Course list
- Certificates (if available)

---

## System Use Cases

### UC-SYS-001: User Authentication and Authorization
**Primary Actor:** System
**Description:** Secure login and role-based access control (RBAC)

**Main Flow:**
1. User enters email and password
2. System validates credentials against database
3. If valid:
   - Create session token
   - Load user role (Admin/Teacher/Student/Parent)
   - Assign permissions based on role
   - Redirect to role-specific dashboard
4. If invalid:
   - Show error message
   - Log failed attempt
   - Lock account after 5 failed attempts (for security)
5. User can remain logged in using "Remember Me" option

**Features:**
- Password strength validation
- Two-factor authentication (optional)
- Session timeout (30 minutes inactivity)
- Logout functionality
- "Forgot Password" recovery

---

### UC-SYS-002: Password Reset and Recovery
**Primary Actor:** System, User
**Description:** Allow users to recover and reset passwords
**Main Flow:**
1. User clicks "Forgot Password"
2. Enters email address
3. System generates reset token
4. Email sent with reset link
5. User clicks link and enters new password
6. System validates password strength
7. Password updated
8. Email confirmation sent
9. User redirected to login

**Constraints:**
- Reset link valid for 24 hours only
- New password different from last 3 passwords
- Password minimum 8 characters with mixed case and numbers

---

### UC-SYS-003: Date Conversion Support
**Primary Actor:** System
**Description:** Support Gregorian (AD) and Nepali (BS) date conversion for forms and data entry
**Implementation:**
- Calendar date picker with dual AD/BS input
- Automatic conversion between AD and BS dates
- Date conversion endpoints for student forms
- Date fields preserve both formats in database

---

### UC-SYS-004: Audit Logging
**Primary Actor:** System
**Description:** Track all system changes for compliance and security

**Logged Interactions:**
- User login/logout
- User creation/modification
- Mark submission
- Attendance recording
- Notice publishing
- File uploads
- User access to sensitive data
- System configuration changes

**Audit Log Fields:**
- Timestamp
- User ID
- Action type
- Resource affected
- Old value (if update)
- New value (if update)
- IP address
- Browser/device info

---

### UC-SYS-005: Email Notifications
**Primary Actor:** System
**Description:** Send automated notifications via email
**Triggers:**
- New user account creation (send welcome email)
- Password reset request
- Mark submission (notify student)
- Notice published (notify students)
- Attendance marked (notify student/parent)
- Important alerts (to admin)
- Report generation (send link)
- Teacher assignment (notify teacher)

**Email Templates:**
- Customizable templates for each notification
- Support for dynamic content
- HTML and plain text formats

---

### UC-SYS-006: File Management
**Primary Actor:** System
**Description:** Handle file uploads and storage
**Supported File Types:**
- Documents: PDF, DOC, DOCX, PPT
- Images: JPG, PNG, GIF
- Archives: ZIP (optional)

**Features:**
- Virus scanning on upload
- File size validation
- Storage quota management
- Access control (who can download)
- Automatic compression for images
- Backup and recovery

---

### UC-SYS-007: Report Generation
**Primary Actor:** System
**Description:** Generate various reports in multiple formats
**Report Formats:** PDF, Excel, CSV

**Scheduling:**
- Manual generation
- Scheduled automated reports
- Email delivery of reports
- Archive reports for historical reference

---

### UC-SYS-008: Data Backup and Recovery
**Primary Actor:** System Administrator
**Description:** Regular data backup and recovery procedures
**Frequency:** Daily automated backups
**Retention:** 30 days of backups
**Recovery:** Point-in-time recovery capability

---

### UC-SYS-009: System Performance Monitoring
**Primary Actor:** System
**Description:** Monitor and optimize system performance
**Monitored Metrics:**
- Server resource usage (CPU, memory, disk)
- Response time of critical operations
- Database query performance
- User session count
- API response times

---

### UC-SYS-010: Load Testing
**Primary Actor:** DevOps/Performance Team
**Description:** Validate system capacity
**Testing Scenarios:**
- Baseline load test
- Progressive load test
- Stress test
- Spike test

**Tools:** K6 for load testing

---

## Additional Cross-Functional Capabilities

### Date Conversion Support (Gregorian ↔ Nepali)
- Dual calendar support for date entry
- Automatic AD to BS and BS to AD conversion
- Date conversion APIs for external integration
- Date fields in forms support both formats

### Export and Import Capabilities
- **Export:** Student lists, marks, attendance → Excel/CSV/PDF
- **Import:** Bulk student/teacher creation from file
- **Integration:** APIs for third-party integration

### Mobile Responsiveness
- All features accessible on mobile devices
- Responsive design for phones and tablets
- Mobile-optimized interfaces

### Accessibility
- WCAG compliance (for differently-abled users)
- Screen reader support
- Keyboard navigation
- High contrast mode

### Security Features
- SSL/HTTPS encryption
- Password hashing (bcrypt)
- SQL injection prevention
- CSRF token protection
- Rate limiting on APIs
- Role-based access control (RBAC)
- Data encryption at rest

---

## Use Case Relationship Map

```
ADMIN (UC-AD-001 to UC-AD-010)
├─ Manages system foundation
├─ Creates TEACHERS (UC-T-001 to UC-T-012)
├─ Creates STUDENTS (UC-S-001 to UC-S-011)
├─ Creates PARENTS (UC-P-001 to UC-P-012)
└─ Monitors via AUDIT LOGS (UC-SYS-004)

TEACHER (UC-T-001 to UC-T-012)
├─ Takes ATTENDANCE (relates to UC-S-004)
├─ Uploads MARKS (relates to UC-S-005, UC-P-004)
├─ Publishes NOTICES (relates to UC-S-007, UC-P-006)
├─ Uploads MATERIALS (relates to UC-S-006)
└─ Notified via EMAILS (UC-SYS-005)

STUDENT (UC-S-001 to UC-S-011)
├─ Views profile data
├─ Enrolls in COURSES (UC-S-002, UC-S-003)
├─ Sees MARKS from TEACHER (UC-S-005)
└─ Notified via EMAILS (UC-SYS-005)

PARENT (UC-P-001 to UC-P-012)
├─ Views CHILD's ATTENDANCE (UC-S-004)
├─ Views CHILD's MARKS (UC-S-005)
└─ Communicates with TEACHERS
```

---

## System Architecture - Use Case Support

**Technology Stack:**
- **Backend:** Laravel 12.0 (PHP 8.2+)
- **Frontend:** Vue.js with Vite
- **Database:** MySQL/MariaDB (30 tables)
- **Authentication:** Laravel Breeze + Sanctum
- **PDF Generation:** Laravel DOMPDF
- **Testing:** PHPUnit
- **Load Testing:** K6

---

## Summary Statistics

| Category | Count | Details |
|----------|-------|---------|
| **Admin Use Cases** | 10 | Dashboard, Users, Subjects, Departments, Semesters, etc. |
| **Teacher Use Cases** | 12 | Attendance, Marks, Materials, Notices, Exams, Reports, etc. |
| **Student Use Cases** | 11 | Dashboard, Courses, Marks, Attendance, Materials, etc. |
| **Parent Use Cases** | 12 | Child monitoring, Marks, Attendance, Communications, etc. |
| **System Use Cases** | 10 | Authentication, Emails, Audit, Backups, Performance, etc. |
| **Total Use Cases** | **55** | Complete coverage of system functionality |
| **Database Tables** | 30 | Supporting all use cases with data persistence |
| **Actors** | 4 | Admin, Teacher, Student, Parent |

---

## Conclusion

The IT-DMS provides a comprehensive solution for department academic management with:
- ✅ Clear separation of concerns per user role
- ✅ Complete academic lifecycle coverage
- ✅ Audit trail and compliance support
- ✅ Multi-language support (English & Nepali)
- ✅ Real-time data and notifications
- ✅ Flexible reporting and exports
- ✅ Scalable architecture for institutional growth

This use case documentation serves as the blueprint for all system functionality and can be referenced during development, testing, and maintenance phases.
