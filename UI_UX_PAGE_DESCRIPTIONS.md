# IT-DMS: Complete Page Descriptions for UI/UX Design

## 📋 Document Overview

This document provides comprehensive descriptions of all pages in the IT Department Management System (IT-DMS) for UI/UX designers. Each page description includes purpose, layout components, data structures, and interactions.

**Last Updated**: April 1, 2026  
**Application**: IT Department Management System (IT-DMS)  
**Technology**: Laravel 11 + Blade Templates + Tailwind CSS + Alpine.js

---

## 🎨 Design System Overview

### Color Scheme
- **Primary Red**: #FF0037 (Department branding)
- **Secondary Red**: #D90033, #B2002F (Gradients)
- **Accent Colors**: Blue, Orange, Purple, Green
- **Dark Mode**: Full dark mode support with `dark:` classes

### Typography
- **Headers**: Bold, larger sizes for hierarchy
- **Body**: Clear, readable sans-serif
- **Bilingual**: Full support for English/Nepali text

### Components
- **Cards**: Rounded corners, borders, shadows
- **Tables**: Striped rows, hover effects, sortable columns
- **Modals**: Overlay dialogs for forms
- **Navigation**: Sidebar + Top bar
- **Buttons**: Multiple variants (primary, secondary, danger, etc.)

### Layout Pattern
```
┌─────────── Header ───────────┐
├─── Sidebar ─┬─ Main Content ─┤
│             │                │
│             │   Content Area │
│             │                │
└─────────────┴────────────────┘
```

---

# 🌐 PUBLIC PAGES (No Login Required)

## 1. Landing Page (`landing.blade.php`)

### Purpose
- Homepage showcasing the department
- Entry point for all users
- Information about programs, faculty, and news

### Layout & Components

#### Hero Section
- **Background**: Carousel/slider with department hero images (admin-configurable)
- **Text Overlay**: Department name, tagline, hero title
- **CTAs**: "Learn More", "Login", "Explore Programs"
- **Bilingual**: English/Nepali support
- **Responsive**: Full-width on mobile, desktop with side content

#### About Section
- **Department Info**: Logo, name, description (bilingual)
- **Key Stats**: Quick numbers (total students, teachers, courses)
- **Leadership**: HOD/Principal with photo and title
- **Location**: Department location indicator

#### Programs Section
- **Program Cards**: Grid layout showing available courses
- **Card Details**: Program name, description, semester info
- **Action**: Link to detailed program view

#### Faculty Section
- **Faculty Grid**: Photos and names of teachers
- **Filters**: Browse by subject, department
- **Search**: Find faculty members

#### News/Events Section
- **Recent Events Gallery**: Event photos in carousel
- **Latest Notices**: Department announcements
- **Event Details**: Date, description, category

#### Call-to-Action Section
- **Login Prompt**: Different CTAs for different roles (admin/teacher/student/parent)
- **Sign-up Info**: Registration information (if applicable)

### Data Displayed
- Department name, logo, address, phone, email, website
- Hero images (5-10 configurable images)
- Faculty information (name, designation, subject)
- Programs/courses list
- Recent events and gallery
- Bilingual content (English/Nepali)

### Interactions
- Carousel auto-scroll and manual navigation
- Link to login page
- Link to department about page
- Link to faculty directory

---

## 2. Gallery Page (`gallery.blade.php`)

### Purpose
- Display department events and activities
- Photo showcase organized by category/date
- Public-facing portfolio

### Layout & Components

#### Gallery Filter/Search
- **Category Filter**: Dropdown to filter by event type
- **Date Range**: Date picker or month selector
- **Search Box**: Search by event name or description

#### Gallery Grid
- **Responsive Grid**: 3-4 columns on desktop, 2 on tablet, 1 on mobile
- **Image Cards**: Thumbnail with event name, date, category badge
- **Hover Effect**: Zoom, show download icon, overlay info
- **Image Info**: Event title, date, short description

#### Image Lightbox
- **Full-screen View**: Click to expand image
- **Navigation**: Previous/Next buttons
- **Meta Info**: Event details below image
- **Download Button**: Option to download image
- **Close Button**: X or ESC to close

### Data Displayed
- Gallery title and description
- Event images with metadata
- Event name, date, category, photographer
- Caption or event description

### Interactions
- Filter by category/date range
- Search gallery
- Click to view full image in lightbox
- Navigate between images
- Download images

---

## 3. Faculty Directory (`faculty.index`)

### Purpose
- List all faculty members
- Show teacher information publicly
- Allow filtering and searching

### Layout & Components

#### Faculty Search/Filter
- **Search Bar**: Find faculty by name
- **Filter by Subject**: Dropdown to filter by subject taught
- **Filter by Department**: If multi-department
- **Reset Filters**: Clear all filters button

#### Faculty Grid
- **Card Layout**: 2-3 columns on desktop
- **Card Details**: Photo, name, designation, subject, email, phone
- **Badge**: Specialization or subject count
- **Action Link**: View profile details

#### Faculty Profile Modal/Detail View
- **Photo**: Large photo display
- **Personal Info**: Name, title, qualifications
- **Contact**: Email, phone, office
- **Subjects**: List of subjects taught
- **Bio**: Professional biography
- **Office Hours**: Schedule if available

### Data Displayed
- Faculty name, photo, designation
- Subject(s) taught
- Email, phone, office location
- Academic qualifications
- Teaching experience

### Interactions
- Search by faculty name
- Filter by subject
- Click to view full profile details
- Download CV (if available)

---

## 4. Subjects Directory (`subjects.index`)

### Purpose
- Show available courses/subjects
- Display course information
- Link to enrollment or course details

### Layout & Components

#### Subject Filter
- **Filter by Semester**: Dropdown to select semester
- **Filter by Category**: Theory/Lab/Practical
- **Search**: Find subject by code or name

#### Subject Cards
- **Grid Layout**: Responsive grid
- **Card Info**: Subject name, code, credits, semester, instructor
- **Badge**: Theory/Lab/Practical indicator
- **Action**: View subject details

#### Subject Detail View
- **Header**: Subject name, code, semester, credits
- **Description**: Course outline and objectives
- **Instructor**: Teacher information
- **Schedule**: Class timing and lab timing
- **Resources**: Study materials count, availability
- **Enrollment**: Enroll button (if applicable)
- **Prerequisites**: Required subjects

### Data Displayed
- Subject name, code, credits, semester
- Instructor name and contact
- Description and learning objectives
- Prerequisites and co-requisites
- Class and lab schedule

### Interactions
- Filter and search subjects
- View subject details
- Enroll in subject (if student)
- Download syllabus (if available)

---

## 5. Notices/News Portal (`notices.index`)

### Purpose
- Display public announcements
- News and events information
- Important updates

### Layout & Components

#### Notices Header
- **Title**: "Department Notices" or "Latest News"
- **Filter**: Category, date range, search
- **View Options**: List or card view toggle

#### Notices List/Cards
- **List View**: Title, date, category, preview text
- **Card View**: Title, preview, date, category badge
- **Hover**: Show preview or summary
- **Action**: Click to read full notice

#### Notice Detail View
- **Header**: Title, publication date, category
- **Author**: Posted by teacher/admin
- **Content**: Full text (bilingual support for English/Nepali)
- **Attachments**: Related files or links
- **Metadata**: Views count, last updated
- **Related**: Similar notices or related announcements

### Data Displayed
- Notice title, content (bilingual)
- Publication date
- Category (Academic, Administrative, Event, etc.)
- Author information
- Attachments/links

### Interactions
- Filter by category or date
- Search notices
- Click to read full notice
- Download attachments
- Bilingual toggle

---

# 🔐 AUTHENTICATION PAGES

## 6. Login Page (`auth.login`)

### Purpose
- User authentication and system access
- Role-based login (same form for all roles)

### Layout & Components

#### Login Container
- **Brand Section**: Department logo, name, tagline
- **Form Card**: Centered form with shadow and border
- **Background**: Subtle gradient or pattern

#### Login Form
- **Email Field**: Email input with icon, validation
- **Password Field**: Password input with show/hide toggle
- **Remember Me**: Checkbox option
- **Forgot Password Link**: "Forgot your password?" link
- **Submit Button**: "Login" button (full-width, primary color)

#### Links Section
- **Sign Up Link**: Register new account (if enabled)
- **Help/Support**: FAQ or contact info
- **Language Switcher**: English/Nepali toggle

#### Demo Credentials (Optional - for visitors)
- **Accordion/Collapsible**: Show demo login credentials
- **Separate Sections**: Admin, Teacher, Student, Parent credentials

#### Error Messages
- **Alert Box**: Display validation/authentication errors
- **Field-specific**: Highlight problematic fields

### Data Validation
- Email format validation
- Password required (non-empty)
- CSRF token
- Rate limiting (prevent brute force)

### Interactions
- Email and password input
- Show/hide password toggle
- Submit login form
- Click "Forgot Password" to reset
- Click "Sign Up" to register
- Switch language (English/Nepali)

---

## 7. Register Page (`auth.register`)

### Purpose
- Create new user accounts
- Admin-controlled or self-registration

### Layout & Components

#### Registration Form
- **Name Field**: Full name input
- **Email Field**: Email with uniqueness validation
- **Password Field**: Min 8 characters with strength indicator
- **Confirm Password**: Verify password matches
- **Role Selection**: Dropdown for user role (Student/Teacher/Parent)
- **Student-specific Fields**: Roll number, enrollment year (if student role)
- **Teacher-specific Fields**: Department, subject (if teacher role)
- **Parent-specific Fields**: Guardian of student selector (if parent role)

#### Terms & Conditions
- **Checkbox**: Agree to terms
- **Link**: View full terms document

#### Submit Section
- **Register Button**: Create account
- **Already Member**: Link back to login

### Data Validation
- Email uniqueness check
- Password strength requirements
- Required field validation
- Role-specific field validation

### Interactions
- Fill form fields
- Select role and show role-specific fields
- Accept terms checkbox
- Submit registration
- Link to login page

---

## 8. Password Reset Pages

### 8a. Forgot Password (`auth.forgot-password`)

### Purpose
- Initiate password reset process
- Email verification

### Layout & Components

#### Reset Request Form
- **Email Field**: Enter registered email
- **Description**: Reset instructions text
- **Submit Button**: "Send Password Reset Link"
- **Back to Login**: Link to login page

### Interactions
- Enter email address
- Submit reset request
- Receive password reset email

---

### 8b. Reset Password (`auth.reset-password`)

### Purpose
- Set new password after email verification

### Layout & Components

#### Password Reset Form
- **Email Field**: Pre-filled, non-editable
- **Password Field**: New password with strength indicator
- **Confirm Password**: Verify password
- **Reset Button**: "Reset Password"
- **Validation Messages**: Password requirements

### Interactions
- Enter new password
- Confirm password
- Submit to complete reset

---

## 9. Email Verification Page (`auth.verify-email`)

### Purpose
- Verify email address before accessing system

### Layout & Components

#### Verification Message
- **Title**: "Verify Your Email"
- **Description**: Instructions to check email
- **Resend Option**: "Resend Verification Email" button
- **Email Display**: Show registered email
- **Edit Email Link**: Change email if incorrect

### Interactions
- Check email for verification link
- Click verification link in email
- Resend verification email
- Change email if wrong

---

## 10. Confirm Password Page (`auth.confirm-password`)

### Purpose
- Additional security confirmation for sensitive actions

### Layout & Components

#### Confirmation Form
- **Title**: "Confirm Password"
- **Message**: Action requires password confirmation
- **Password Field**: Re-enter current password
- **Submit Button**: "Confirm"
- **Cancel Link**: Go back

### Interactions
- Enter password to confirm
- Submit confirmation
- Proceed with sensitive action

---

# 👨‍💼 ADMIN DASHBOARD & PAGES

## 11. Admin Dashboard (`admin.dashboard`)

### Purpose
- Administrative overview and quick access
- System health monitoring
- Quick statistics and recent activities

### Layout & Components

#### Header Section
- **Welcome Banner**: Gradient background (red/pink theme)
- **Admin Name**: Personalized greeting
- **Department Info**: Current department badge
- **Role Badge**: "Administrator" indicator
- **Quick Time**: Current date/time display

#### Statistics Cards (4-Column Grid)
- **Total Students**: Number + "Active students enrolled"
- **Total Teachers**: Number + "Faculty members"
- **Total Courses**: Number + "Available subjects"
- **Average Attendance**: Percentage + trend indicator
- **Card Design**: Icon + colored border (blue, orange, purple, green)
- **Hover**: Show more details or mini-chart

#### Charts & Graphs Section
- **Attendance Trend Chart**: Line or area chart showing attendance over time
- **Grade Distribution**: Bar chart showing mark distributions
- **Semester Distribution**: Pie chart showing students per semester
- **Interactive**: Hover for details, zoom capabilities

#### Recent Activities
- **Activity Log**: Timeline of recent actions
- **Items**: User name, action, timestamp, status
- **Icons**: Visual indicators for action types
- **Limit**: Show last 10 activities

#### Quick Actions
- **Button Grid**: 6-8 quick access buttons
- **Actions**: Add Student, Add Teacher, Mark Attendance, Upload Marks, Create Exam, Manage Notice, View Reports, Settings
- **Icons**: Clear icon for each action
- **Hover**: Show tooltip with description

#### Upcoming Events/Deadlines
- **Card List**: Important upcoming dates
- **Items**: Event name, date, type (exam, deadline, event)
- **Color Coding**: Different colors for different types
- **Icon**: Calendar or notification icon

#### Top Section by Semester
- **Semester Cards**: Small cards showing current semesters
- **Toggle Active**: Mark semester as active
- **View Details**: Link to semester details
- **Status Badge**: Active/Inactive indicator

### Data Displayed
- User stats (Total Students, Teachers, Courses)
- Attendance statistics
- Grade distribution
- Recent system activities
- Exam schedule
- Current active semester
- Department information

### Interactions
- Click on stats to filter/drill down
- Interact with charts (hover for details)
- Quick action buttons link to relevant pages
- Refresh data button
- Print dashboard report
- Expand/collapse sections

---

## 12. Students Management Page (`admin.students`)

### Purpose
- Manage all student records
- CRUD operations on student data
- Bulk operations and exports

### Layout & Components

#### Page Header
- **Title**: "Student Management"
- **Breadcrumb**: Admin > Students
- **Quick Stats**: Total Active, Total Alumni, New This Semester
- **View Tabs**: "Active Students", "Alumni", "All"

#### Filters & Search Section
- **Search Box**: Find by name, roll number, email
- **Filter by Semester**: Dropdown selector
- **Filter by Status**: Active/Inactive/Alumni
- **Filter by Department**: If applicable
- **Date Range Filter**: Enrollment date range
- **Advanced Filters**: Show/hide more filter options
- **Filter Chips**: Display active filters with X to remove
- **Reset Filters**: Clear all button

#### Bulk Actions Toolbar
- **Select All Checkbox**: Select all visible students
- **Selected Count**: "X students selected"
- **Bulk Actions Dropdown**: Export, Delete, Change Semester, etc.
- **Export Button**: CSV/Excel export
- **Print Button**: Print list
- **Add New Button**: Create new student

#### Students Table
- **Columns**: Roll Number, Name, Email, Semester, Status (badge), Actions
- **Sortable Headers**: Click to sort ascending/descending
- **Row Hover**: Highlight row, show action buttons
- **Zebra Striping**: Alternate row colors for readability
- **Responsive**: Horizontal scroll on mobile

#### Table Actions (per student)
- **View Icon**: View full student profile
- **Edit Icon**: Edit student information
- **Delete Icon**: Delete student (with confirmation)
- **Download Icon**: Download student details/transcript
- **More Actions**: Dropdown for additional options (Toggle Active, Move to Alumni, etc.)

#### Pagination
- **Page Info**: "Showing 1 to 25 of 150 students"
- **Per Page Selector**: Dropdown (25, 50, 100)
- **Navigation**: Previous, page numbers, Next
- **Go To Page**: Input field to jump to specific page

#### Empty State
- **Message**: "No students found"
- **Icon**: Empty illustration
- **Action**: "Add Student" button

### Data Displayed
- Roll Number, Full Name, Email, Phone
- Semester, Enrollment Date
- Status (Active/Inactive/Alumni)
- Academic performance indicator
- Attendance percentage (optional)

### Interactions
- Search and filter students
- Sort by column headers
- Select students (individual or all)
- Bulk actions on selected students
- Click view to see student details
- Click edit to modify student info
- Click delete with confirmation
- Export data (CSV/Excel)
- Print student list
- Toggle student active/inactive status
- Move student to alumni

---

## 13. Student Detail View (`admin.student-show`)

### Purpose
- Display comprehensive student information
- View academic records
- Modify student profile
- Access student-related functions

### Layout & Components

#### Student Header Card
- **Photo**: Student profile picture (with upload/change option)
- **Name & Roll**: Large, prominent display
- **Status Badge**: Active/Inactive/Alumni
- **Quick Info**: Email, Phone, Semester, Enrollment Date
- **Action Buttons**: Edit, Download, Print Profile, Move to Alumni
- **Edit Button**: Pencil icon to edit details

#### Student Information Tabs

##### Tab 1: Personal Information
- **Basic Details**: Full name, email, phone, address
- **Date of Birth**: Nepali and AD calendar support
- **Gender**: Male/Female/Other
- **Emergency Contact**: Name, relationship, phone
- **Edit Form**: Make fields editable when in edit mode

##### Tab 2: Academic Information
- **Enrollment Details**: Enrollment date, semester, year
- **Current Semester**: Active semester indicator
- **Subjects**: List of enrolled subjects with credit hours
- **Attendance**: Overall attendance percentage
- **Badge**: Low attendance warning if < 75%

##### Tab 3: Attendance History
- **Attendance Chart**: Visual representation (pie/progress bar)
- **Attendance by Subject**: Table showing attendance per subject
- **Monthly Breakdown**: Month-wise attendance statistics
- **Alert Status**: Show if attendance is low

##### Tab 4: Academic Records/Marks
- **Semester-wise Results**: Dropdown to select semester
- **Marks Table**: Exam, Subject, Marks, Grade, GPA
- **GPA Trend**: Chart showing GPA over semesters
- **Performance Indicator**: Alert for low grades

##### Tab 5: Documents & Files
- **Upload Area**: Drag & drop to upload documents
- **Document List**: Name, upload date, file type, download option
- **Allowed Types**: PDF, DOC, JPG, PNG, etc.
- **Delete Option**: Remove document

#### Action Sidebar
- **Print Profile Button**: Print student details
- **Download Transcript Button**: Generate and download academic transcript
- **Download Admit Card Button**: If exam in progress
- **View Time Table Button**: Link to student's class schedule
- **Toggle Active/Inactive Button**: Change student status
- **Move to Alumni Button**: Archive student
- **Edit Profile Button**: Direct edit access
- **Delete Student Button**: With confirmation warning

### Data Displayed
- Personal: Name, Email, Phone, Address, DOB, Gender
- Academic: Roll Number, Semester, GPA, Credits Completed
- Attendance: Percentage by subject, monthly breakdown
- Marks: Subject-wise marks, grades, semester GPA
- Documents: Uploaded files with dates

### Interactions
- Click tabs to navigate sections
- Edit personal information
- Upload new documents
- Download transcripts/reports
- View attendance trends
- View marks and grade progression
- Print or download profile
- Change student status
- Add notes or comments (optional)

---

## 14. Student Create/Edit Page (`admin.student-edit`)

### Purpose
- Create new student record
- Edit existing student information
- Form validation and submission

### Layout & Components

#### Form Header
- **Title**: "Add New Student" or "Edit Student"
- **Breadcrumb**: Admin > Students > Add/Edit
- **Discard Button**: Cancel and go back

#### Form Sections

##### Personal Information Section
- **Full Name**: Text input, required
- **Email**: Email input, must be unique, required
- **Phone**: Phone number input with validation
- **Date of Birth**: Nepali date picker (AD to BS conversion)
- **Gender**: Radio buttons (Male/Female/Other)
- **Address**: Text area for complete address
- **City/District**: Dropdown selector
- **Emergency Contact Name**: Text input
- **Emergency Contact Relationship**: Dropdown (Father, Mother, Guardian, etc.)
- **Emergency Contact Phone**: Phone number

##### Academic Information Section
- **Roll Number**: Auto-generated or manual entry
- **Enrollment Date**: Nepali date picker
- **Semester**: Dropdown selector (Semester 1, 2, 3, etc.)
- **Department**: Dropdown selector
- **Program**: Dropdown (if multiple programs)

##### Additional Information Section
- **Profile Picture**: Image upload (drag & drop or file picker)
- **Nepali Name**: Optional Nepali version of name
- **Guardian Details**: Guardian name, relationship, contact
- **Additional Notes**: Text area for any special notes

#### Form Actions
- **Save Button**: Primary, large button
- **Save & Add Another Button**: Save and open blank form
- **Preview Button**: Preview before submitting
- **Cancel Button**: Discard changes and go back
- **Delete Button**: Only on edit form (with confirmation)

#### Validation Messages
- **Field-level**: Show below each field (red text)
- **Form-level**: Show at top if multiple errors
- **Success Message**: Green alert after successful save

### Data Validation Rules
- Email: Valid format, unique in system
- Phone: Valid phone format
- Name: Non-empty, max 100 characters
- Roll Number: Unique if manually entered
- Required fields: Name, Email, Semester

### Interactions
- Fill in form fields
- Upload profile image
- Date picker for DOB and enrollment date
- Dropdown selections
- Form validation on blur/submit
- Submit form to create/update student
- Cancel to discard changes

---

## 15. Teachers Management Page (`admin.teachers`)

### Purpose
- Manage faculty/teacher records
- Assign subjects to teachers
- CRUD operations

### Layout & Components (Similar to Students Management)

#### Page Header
- **Title**: "Teacher Management"
- **Stats**: Total Active Teachers, New This Year

#### Filters & Search
- **Search**: By name, email, subject
- **Filter by Department**: Department dropdown
- **Filter by Subject**: Subject taught
- **Filter by Status**: Active/Inactive
- **Filter by Specialization**: Subject or area of expertise

#### Teachers Table
- **Columns**: Name, Email, Department, Subject(s), Phone, Status, Actions
- **Row Hover**: Show action buttons
- **Status Badge**: Active/Inactive indicator
- **Subject Badge**: List of subjects with count

#### Mass Actions
- **Bulk Export**: Export teacher list
- **Bulk Print**: Print directory
- **Bulk Upload**: Import teacher data from file
- **Add Teacher Button**: Create new teacher

#### Table Actions
- **View**: See full profile
- **Edit**: Modify teacher info
- **Assign Subjects**: Manage subject-teacher mapping
- **Delete**: Remove teacher
- **Download**: Teacher details/CV

#### Empty State
- **Message**: "No teachers found"
- **Action**: "Add Teacher" button

### Data Displayed
- Name, Email, Phone
- Department, Subject(s)
- Designation/Title
- Qualification(s)
- Office location/Room number
- Status

### Interactions
- Search and filter teachers
- Sort by columns
- View teacher profile
- Edit teacher information
- Assign/manage subjects
- Export teacher list
- Print directory
- Add new teacher
- Delete teacher record

---

## 16. Parents Management Page (`admin.parents`)

### Purpose
- Manage parent/guardian records
- Link parents to students
- CRUD operations

### Layout & Components (Similar to Students/Teachers)

#### Page Header
- **Title**: "Parent Management"
- **Stats**: Total Parent Accounts, Active Accounts

#### Filters & Search
- **Search**: By name, email, phone
- **Filter by Status**: Active/Inactive
- **Filter by Student Count**: Dropdown (1, 2, 3+)
- **Filter by Contact Status**: Primary Contact, Secondary

#### Parents Table
- **Columns**: Name, Email, Phone, Children Count, Status, Actions
- **Children Count**: Show as badge (3 students)
- **Contact Info**: Email, phone with icons

#### Table Actions
- **View Profile**: See parent details and linked children
- **Edit**: Modify parent information
- **View Children**: See list of linked students
- **Manage Links**: Link/unlink students to parent
- **Delete**: Remove parent record
- **Send Message**: Send notification to parent (if feature enabled)

#### Add Parent
- **Add Button**: Create new parent account
- **Link Students**: Option to link students during creation

### Data Displayed
- Name, Email, Phone
- Relationship to student(s)
- Address
- Linked student names and roll numbers
- Contact preference (email/SMS/both)
- Status

### Interactions
- Search and filter parents
- View parent profile
- Edit parent information
- Link/unlink students
- Delete parent account
- Send messages or notifications
- Export parent list

---

## 17. Attendance Management Page (`admin.attendance`)

### Purpose
- Record and manage attendance
- View attendance reports
- Handle attendance corrections

### Layout & Components

#### Page Header
- **Title**: "Attendance Management"
- **Tabs**: "Mark Attendance", "Lab Attendance", "Attendance Reports"

#### Filters & Selection

##### Mark Attendance Tab
- **Date Picker**: Select attendance date (Nepali calendar support)
- **Subject Selector**: Dropdown to select subject
- **Semester Selector**: Dropdown to select semester
- **Section/Group**: If applicable
- **Load Students Button**: Fetch students for selected subject

#### Students Attendance Table
- **Column**: Checkbox, Student Name/Roll Number, Attendance Status
- **Attendance Status Options**: 
  - Present (Green checkmark)
  - Absent (Red X)
  - Late (Yellow clock icon)
  - Leave (Orange exclamation)
- **Quick Actions**: Click cell to toggle between states
- **Bulk Mark**: Select multiple students and mark same status
- **Search**: Filter students by name/roll within loaded list

#### Actions Section
- **Save Attendance Button**: Large button to submit attendance
- **Clear Selection Button**: Reset all changes
- **Import from Last Date Button**: Copy attendance from previous class
- **Print Button**: Print attendance sheet
- **Discard Button**: Go back without saving

#### Lab Attendance Tab (Similar Structure)
- **Lab Subject Selector**: Select practical subject
- **Lab Batch**: If multiple batches
- **Similar Table**: With attendance options for lab

#### Attendance Reports Tab
- **Date Range Filter**: From date to date picker
- **Subject Filter**: Select subject for report
- **Semester Filter**: Select semester
- **View Type Selector**: List view, chart view, summary view
- **Generate Report Button**: Create report based on filters

#### Attendance Report Display
- **Statistics Cards**: Total Classes, Total Present, Total Absent, Attendance %
- **Chart**: Visual representation (pie, bar, or line chart)
- **List View**: Student-wise attendance summary with percentage
- **Export Options**: PDF, CSV, Excel
- **Print Option**: Print report

### Data Displayed
- Date, subject, instructor name
- Class list with attendance status per student
- Attendance count (Present, Absent, Late, Leave)
- Attendance percentage
- Trends and patterns

### Interactions
- Select date and subject
- Load student roster
- Mark attendance (toggle states)
- Bulk mark options
- Save attendance data
- Generate and view reports
- Export or print attendance
- Search within student list
- Undo/edit previous attendance

---

## 18. Examination Management Page (`admin.exam`)

### Purpose
- Create and manage examinations
- Schedule exams
- Upload and manage exam marks

### Layout & Components

#### Page Header
- **Title**: "Examination Management"
- **Add Exam Button**: Create new exam
- **View Options**: List view, Card view, Calendar view

#### Filters & Search
- **Search**: By exam name, subject, date
- **Filter by Exam Type**: Midterm, Final, Assessment, CTEVT
- **Filter by Semester**: Dropdown selector
- **Filter by Status**: Upcoming, Ongoing, Completed, Cancelled
- **Filter by Academic Year**: Year dropdown
- **Date Range**: From date to date

#### Exams Table/Cards
- **Columns (Table View)**:
  - Exam Name, Type, Subject, Date/Time, semester, Status, Actions
- **Card View**:
  - Exam name, type badge, date, subject count, status
  - Mini bar showing marks details

#### Status Badges
- **Upcoming**: Blue badge
- **Ongoing**: Orange badge
- **Completed**: Green badge
- **Cancelled**: Red badge

#### Table Actions (per exam)
- **View Details**: See full exam information
- **Edit**: Modify exam details
- **Upload Marks**: Upload marks sheet/file
- **View Marks**: See entered marks
- **Generate Sheet**: Create marks entry template
- **Print Schedule**: Print exam schedule
- **Delete**: Remove exam (with confirmation)
- **Toggle Status**: Mark exam as completed or cancel

#### Pagination & Bulk Actions
- **Pagination**: Previous, pages, Next
- **Per Page Selector**: 10, 25, 50, 100
- **Select All**: Checkbox to select all exams
- **Bulk Export**: Export all selected exams
- **Bulk Delete**: Delete multiple exams (with warning)

#### Create/Edit Exam Form (Modal or Dedicated Page)
- **Exam Name**: Text input
- **Exam Type**: Dropdown (Midterm, Final, Assessment, CTEVT)
- **Subject**: Search and select subject
- **Semester**: Dropdown selector
- **Exam Date**: Date picker (Nepali calendar support)
- **Exam Time**: Time picker (start and end time)
- **Max Marks**: Number input (100, 50, custom)
- **Duration**: Duration in minutes or hours
- **Description**: Text area for any notes
- **Save Button**: Create/update exam
- **Cancel Button**: Close modal/form

### Data Displayed
- Exam name, type, subject
- Date and time
- Maximum marks
- Semester and academic year
- Exam status
- Number of marks entered
- Subject-wise marks coverage

### Interactions
- Create new exam (form modal)
- Search and filter exams
- View exam details
- Edit exam information
- Upload marks (CSV/Excel file)
- Manually enter marks
- View marks entered
- Generate marks sheet template
- Print exam schedule
- Delete exam
- Update exam status

---

## 19. Marks Management Page (`admin.marks`)

### Purpose
- View and manage all student marks
- Search marks by student/subject/exam
- Dynamic marks entry interface

### Layout & Components

#### Search & Filters Section
- **Student Search**: Autocomplete search by name/roll
- **Subject Filter**: Dropdown to select subject
- **Exam Type Filter**: Midterm, Final, Assessment, CTEVT
- **Semester Filter**: Semester dropdown
- **Date Range**: Exam date range filter
- **Search Button**: Execute search with all filters

#### Search Results Display
- **Results Count**: "Found X records matching criteria"
- **View Toggle**: List view, grid view, chart view

#### Marks Table (List View)
- **Columns**: Student (Roll/Name), Subject, Exam Type, Marks, Total, Grade, Actions
- **Row Coloring**: Highlight based on grade (A=Green, B=Blue, C=Yellow, D/F=Red)
- **Sortable**: Click headers to sort
- **Searchable**: Filter within results
- **Export Option**: Export search results to CSV/Excel

#### Marks Detail View (per record)
- **Student Info**: Photo, name, roll number
- **Subject**: Subject name and code
- **Exam Type**: Type of exam
- **Marks Breakdown**: Internal, Assessment, Final (if applicable)
- **Total Marks**: Calculated total
- **Grade**: Grade letter
- **GPA**: Individual exam GPA contribution
- **Edit Button**: Modify marks

#### Dynamic Marks Entry Tab
- **Exam Selector**: Choose exam from dropdown
- **Semester Selector**: Select semester
- **Subject Selector**: Select subject
- **Load Marks Button**: Fetch current marks for selected criteria
- **Marks Table**: Grid showing students and marks entry fields
- **Mark Input**: Editable cells with validation
- **Validation**: Show error if marks exceed max
- **Save All Button**: Save all changes at once
- **Save Individual Button**: Save single mark

#### Marks Statistics (Dashboard View)
- **Class Average**: Average marks for subject/exam
- **Grade Distribution Chart**: Pie/bar chart
- **Performance Trend**: Line chart over previous exams
- **Grade Count**: Number of students per grade
- **Highest/Lowest Marks**: Top and bottom scorers

#### Actions
- **Print Marks Sheet**: Print marks in readable format
- **Export to PDF**: Export as PDF document
- **Export to Excel**: Export as Excel file
- **Email Marks**: Send marks to students (if feature enabled)
- **Clear Marks**: Delete all marks for an exam (with warning)

### Data Displayed
- Student name, roll number
- Subject name and code
- Exam name and type
- Marks (internal, assessment, final, total)
- Grade/Grade point
- Percentage
- Date marks were entered
- Who entered the marks (admin/teacher)

### Interactions
- Search marks by multiple criteria
- View individual student marks
- Edit marks with validation
- Bulk entry of marks from template
- View marks statistics and trends
- Print or export marks
- Send marks notifications
- Clear/delete marks (with confirmation)
- View grade distribution

---

## 20. Marksheet Generation & Search (`admin.marksheet.search` & `admin.marksheet.print`)

### Purpose
- Generate and print comprehensive marksheets
- Search for specific student marksheets
- Create semester transcripts

### Layout & Components

#### Marksheet Search Interface
- **Student Search**: Autocomplete search by roll/name
- **Semester Selector**: Dropdown to select semester
- **Academic Year**: Year selector dropdown
- **Search Button**: Find marksheet

#### Search Results
- **Matching Students**: List showing matching students
- **Click to Select**: Choose student to generate marksheet

#### Marksheet Preview/Print

##### Marksheet Header
- **Department Logo**: At top left
- **Department Name**: Center, large text
- **Title**: "MARKSHEET" or "Academic Transcript"
- **Academic Year/Semester**: Display current semester

##### Student Information Section
- **Name**: Full name (both English and Nepali if available)
- **Roll Number**: Student ID
- **Semester**: Current semester
- **Admission Date**: When enrolled
- **Cumulative GPA**: Overall GPA to date

##### Marks Table
- **Column Headers**: Subject Code, Subject Name, Credit Hours, Marks, Grade, Quality Points
- **Subject Rows**: One row per subject with all details
- **Subtotal Row**: Shows semester totals and GPA

##### Summary Section
- **Total Credits**: Total credit hours for semester
- **Semester GPA**: GPA for this semester
- **Cumulative GPA**: Overall cumulative GPA
- **Grade Point Scale**: Reference table (A=4.0, B=3.5, etc.)

##### Footer
- **Prepared By**: Admin/Teacher name
- **Date**: Date of generation
- **Authorized By**: Principal/HoD signature line
- **Verification Code**: For document authenticity (optional)

#### Print Layout
- **Full Page View**: Professional print layout
- **Multiple Pages**: If multiple pages needed
- **Print Options**: Print to PDF, print directly
- **Preview**: Before printing option

#### Export Options
- **Export as PDF**: Download PDF copy
- **Export as Image**: Save as PNG/JPG
- **Email to Student**: If feature enabled
- **Save to File**: Save to system

#### Bulk Marksheet Generation
- **Generate All for Semester Button**: Create marksheets for all students in semester
- **Progress**: Show progress bar during generation
- **Batch Download**: Download as ZIP file

### Data Displayed
- Student personal information
- All subject marks and grades
- Semester GPA
- Cumulative GPA
- Credit hours
- Grade distribution

### Interactions
- Search for specific student marksheet
- Select semester for marksheet
- Preview before printing
- Print marksheet directly
- Export as PDF
- Generate bulk marksheets
- Download as ZIP

---

## 21. Timetable Management (`admin.timetable`)

### Purpose
- Create and manage class schedule
- Assign teachers to time slots
- Manage classrooms
- Detect scheduling conflicts

### Layout & Components

#### Page Header
- **Title**: "Timetable Management"
- **Semester Selector**: Current semester dropdown
- **View Mode Toggle**: Week View, Day View, List View, Visual Grid
- **Add Slot Button**: Create new time slot
- **Print Button**: Print timetable
- **Export Button**: Export timetable

#### Filters & Search
- **Department Filter**: Select department
- **Semester Filter**: Select semester
- **Day Filter**: Select specific day(s)
- **Subject Filter**: Find slots for subject
- **Teacher Filter**: Find slots for teacher
- **Classroom Filter**: Find slots for room

#### Visual Timetable Grid (Week View)
- **Layout**: Grid with time slots (rows) and weekdays (columns)
- **Time Slots**: 7:00 AM to 5:00 PM (or configurable)
- **Slot Duration**: 50 minutes, 1 hour, 1.5 hours (configurable)
- **Cell Content**: Subject name, teacher name, classroom
- **Cell Styling**: Color-coded by subject or semester
- **Blank Cells**: Empty time slots available
- **Conflict Indicators**: Red highlight if conflict detected

#### Timetable Interactions (in grid)
- **Click to Add**: Click empty cell to add new slot
- **Click to Edit**: Click filled cell to edit slot
- **Click to Delete**: Right-click context menu to delete
- **Drag to Move/Resize**: Drag slot to different time or extend duration (if enabled)

#### Conflict Detection
- **Alert Box**: Show if conflict detected (teacher double-booked, room double-booked)
- **Highlight Conflicts**: Color code conflicting slots
- **Conflict Report**: Button to view detailed conflict report

#### List View (Tabular)
- **Columns**: Time, Monday, Tuesday, Wednesday, Thursday, Friday, Saturday
- **Rows**: Time slots (7:00-7:50, 7:50-8:40, etc.)
- **Cell Content**: Subject, Teacher, Room
- **Editable**: Click cell to edit

#### Slot Form (Modal or Dedicated Section)

##### Create/Edit Slot Form
- **Day**: Dropdown (Monday-Saturday)
- **Start Time**: Time picker
- **End Time**: Time picker
- **Duration**: Auto-calculated from start/end
- **Subject**: Search and select subject
- **Teacher**: Auto-populated from subject assignment, editable
- **Classroom**: Dropdown to select room/location
- **Semester**: Dropdown selector
- **Lab/Theory**: Toggle if applicable
- **Batch/Section**: If multiple sections
- **Save Button**: Create/update slot
- **Delete Button**: Delete slot (on edit form only)
- **Cancel Button**: Close form

#### Conflict Detection Results
- **Conflict Report Popup**: Show if conflicts exist
- **Details**: List conflicting slots
- **Teacher Name**: Show double-booked teacher
- **Room Name**: Show double-booked room
- **Suggested Solutions**: Offer alternative times

#### Timetable Print/Export

##### Print View
- **Professional Layout**: Suitable for printing
- **Header**: Department name, semester, print date
- **Timetable Grid**: Full week view
- **Colors**: Print-safe colors
- **Page Layout**: Portrait or landscape

##### Export Options
- **Export as PDF**: Download PDF copy
- **Export as Excel**: Download XLS file with formatting
- **Export as Image**: PNG or JPG

#### Bulk Actions
- **Generate Template**: Create empty timetable template
- **Copy Previously Used**: Duplicate previous semester's timetable
- **Lock Timetable**: Prevent further changes
- **Unlock Timetable**: Allow modifications
- **Publish Timetable**: Make visible to students/teachers
- **Archive Old**: Archive previous semester's timetable

#### Break/Gap Overrides
- **Add Break Override**: Create lunch break, prayer time, lab time
- **Specify Duration**: Set break duration
- **Specify Days**: Which days this break applies
- **Specify Time**: What time(s) this break occurs
- **Delete Override**: Remove break rule

### Data Displayed
- Time slots with start and end times
- Subject name and code
- Teacher name(s)
- Classroom/location
- Semester and section
- Theory/Lab indicator
- Batch/Group if applicable

### Interactions
- View timetable by week, day, or list
- Add new time slot
- Edit existing slot
- Delete slot with confirmation
- Detect and resolve conflicts
- Generate timetable template
- Copy previous timetable
- Lock/unlock timetable
- Print and export timetable
- Set break overrides
- Publish to students

---

## 22. Electives Management (`admin.electives`)

### Purpose
- Manage student elective selections
- Approve/reject elective enrollments
- Track elective assignments

### Layout & Components

#### Page Header
- **Title**: "Electives Management"
- **Add Elective Button**: Create new elective course
- **Status Tabs**: All, Pending Approval, Approved, Rejected

#### Filters & Search
- **Semester Filter**: Select semester
- **Subject Filter**: Filter by elective subject
- **Student Filter**: Search by student name/roll
- **Status Filter**: Pending/Approved/Rejected/Withdrawn
- **Date Range**: Filter by request date

#### Electives Table
- **Columns**: Student Name/Roll, Requested Elective, Current Elective, Request Date, Status, Actions
- **Status Badges**: Pending (yellow), Approved (green), Rejected (red), Withdrawn (gray)
- **Row Hover**: Show action buttons

#### Table Actions
- **View Details**: See student and elective info
- **Approve**: Accept elective request
  - Shows confirmation dialog
  - Option to add notes
  - Updates student record
- **Reject**: Decline elective request
  - Rejection reason text field (optional)
  - Notifies student
- **Withdraw**: Cancel approved elective
  - Confirmation warning
  - Allows re-selection
- **Transfer**: Move student from one elective to another
  - Select new elective from dropdown
- **Edit**: Modify elective assignment

#### Elective Selection Interface

##### For Bulk Assignment:
- **Semester Selector**: Choose semester
- **Subject Selector**: Choose subject
- **Student Selector**: Search and select students or auto-select by criteria
- **Selected Count**: "20 students selected"
- **Assign Button**: Bulk assign to selected students
- **Confirmation**: Show summary before confirming

##### For Individual Assignment:
- **Search Student**: Autocomplete to find student
- **Select Elective**: Dropdown of available electives
- **Assign Button**: Create assignment
- **Notify Student**: Checkbox to send notification

#### Elective Subject Management
- **Add Subject Button**: Create elective subject
- **Subject List**: All available electives
- **Per Subject**:
  - Subject name and code
  - Capacity (max students allowed)
  - Current count
  - Instructor/teacher
  - Actions: Edit, View Enrolled, Delete
- **Edit Form**: Name, code, capacity, description, teacher

#### Approval Workflow Display
- **Pending Requests**: Show count and list
- **Auto-approve Option**: Checkbox (approve automatically upon selection)
- **Manual Approval Section**: List of pending with approve/reject buttons
- **Quick Actions**: Bulk approve or reject buttons

#### Reports
- **Enroll ment Report**: Show which students are in each elective
- **Rejection Report**: List rejected requests with reasons
- **Capacity Utilization**: Chart or percentage for each elective

#### Data Validation
- **Capacity Check**: Prevent enrollment if full (unless override allowed)
- **Prerequisites**: Check if student meets elective prerequisites
- **Conflict Check**: Ensure no schedule conflicts

### Data Displayed
- Student name, roll number
- Elective subject name and code
- Current elective (if changing)
- Request date and status
- Capacity and enrollment count
- Instructor information

### Interactions
- View pending elective requests
- Approve or reject requests (reason required)
- Bulk assign electives to students
- View enrolled students per elective
- Transfer students between electives
- Withdraw elective assignments
- Create/edit elective subjects
- View elective capacity and enrollment
- Remove electives
- Auto-approve option

---

## 23. Courses/Subjects Management (`admin.courses`)

### Purpose
- Manage academic subjects/courses
- Define course structure and properties
- Link teachers to subjects

### Layout & Components

#### Page Header
- **Title**: "Courses/Subjects Management"
- **Add Course Button**: Create new course
- **View Toggle**: List view, Card view

#### Filters & Search
- **Search**: By course name, code, subject
- **Filter by Semester**: Dropdown selector
- **Filter by Type**: Theory, Lab, Practical, Project
- **Filter by Credit Hours**: Dropdown (1, 2, 3, 4+)
- **Filter by Department**: Department dropdown (if multi-dept)
- **Filter by Status**: Active, Inactive, Archived

#### Courses Table/Cards

##### Table View Columns:
- Course Code, Name, Semester, Credits, Type, Teacher(s), Status, Actions

##### Card View:
- Course name, code
- Credits badge
- Type badge
- Teacher count
- Status indicator

#### Table Actions
- **View Details**: See full course information
- **Edit**: Modify course details
- **Assign Teachers**: Manage teacher assignments
- **View Enrolled**: See enrolled students
- **View Marks**: See course marks
- **Duplicate**: Create copy of course for another semester
- **Delete**: Remove course (with confirmation)
- **Archive**: Move to archive
- **Make Template**: Create as reusable template

#### Create/Edit Course Form

##### Basic Information
- **Course Name**: Text input (English)
- **Course Name (Nepali)**: Text input for Nepali version
- **Course Code**: Text input (e.g., "CS101")
- **Description**: Text area for course outline
- **Semester**: Dropdown selector
- **Department**: Dropdown (if applicable)

##### Course Details
- **Credit Hours**: Number input  (e.g., 3, 4)
- **Type**: Dropdown (Theory, Lab, Practical, Project, Mixed)
- **Pre-requisites**: Search and select prerequisite courses
- **Co-requisites**: Search and select concurrent courses
- **Max Capacity**: Number of students allowed

##### Academic Details
- **Learning Objectives**: Text area listing objectives
- **Course Outcomes**: Text area with expected outcomes
- **Assessment Methods**: Multi-select (Exam, Assignment, Project, Presentation)
- **Passing Marks**: Number input (minimum passing)

##### Teacher Assignment
- **Assigned Teachers**: List of teaching staff
- **Add Teacher Button**: Search and add teacher
- **Remove Teacher Button**: Unassign teacher
- **Primary Instructor**: Radio button to select primary

##### Status
- **Active/Inactive Toggle**: Checkbox
- **Visible to Students**: Checkbox to control visibility
- **Save Button**: Create/update course
- **Cancel Button**: Discard changes

#### Courses Detail View
- **Course Header**: Name, code, credits, type
- **Tabs**:
  - Overview: Description, learning objectives
  - Enrolled Students: Student list, attendance, marks
  - Teacher Assignments: Teachers assigned
  - Marks: Student marks for course
  - Materials: Study materials uploaded
  - Timetable: Scheduled classes
- **Action Buttons**: Edit, Assign Teachers, View Marks, View Materials

#### Teacher Assignment Management
- **Search Teachers**: Autocomplete search
- **Available Teachers**: List of unassigned teachers
- **Assigned Teachers**: Current assignments with remove button
- **Add Teacher Button**: Add from available list
- **Set as Primary**: Radio or designated button for lead instructor

### Data Displayed
- Course name and code
- Credits and semester
- Course type (Theory/Lab/Practical)
- Description and learning objectives
- Prerequisites and co-requisites
- Assigned teachers
- Max capacity
- Enrollment count
- Status (Active/Inactive)

### Interactions
- Create new course
- Edit course information
- Assign/unassign teachers
- Set primary instructor
- View enrolled students
- View course marks
- Duplicate course to another semester
- Delete course with confirmation
- Archive/restore courses
- Toggle active status
- Set course as template
- View student grades for course

---

## 24. Notice Board Management (`admin.notice-board`)

### Purpose
- Post department announcements
- Manage notices (Create, Edit, Delete)
- Target notices to specific roles

### Layout & Components

#### Page Header
- **Title**: "Notice Board Management"
- **Create Notice Button**: Add new notice
- **View Mode**: Published, Draft, Scheduled, All

#### Filters & Search
- **Search**: By notice title content
- **Filter by Category**: Academic, Administrative, Event, Holiday, Alert, etc.
- **Filter by Status**: Published, Draft, Scheduled, Archived
- **Filter by Target**: All Users, Students, Teachers, Parents, Admins
- **Date Range**: Published date range

#### Notices Table
- **Columns**: Title, Category, Target Audience, Published Date, Status, Views, Actions
- **Title**: Truncated text of notice
- **View Count**: Number of views badge
- **Status Badge**: Published (green), Draft (blue), Scheduled (yellow), Archived (gray)
- **Row Hover**: Show action buttons

#### Table Actions (per notice)
- **View**: See full notice
- **Edit**: Modify notice
- **Publish**: Set as published (if draft)
- **Schedule**: Set publish date/time
- **Duplicate**: Create copy
- **View Analytics**: Views and engagement stats
- **Delete**: Remove notice (with confirmation)
- **Archive**: Move to archive

#### Create/Edit Notice Form

##### Basic Information
- **Title**: Text input (required)
- **Content**: Rich text editor with formatting options
- **Category**: Dropdown (Academic, Administrative, Event, Holiday, Alert, Other)

##### Bilingual Support
- **English Content**: Main content area
- **Nepali Content**: Separate toggle/tab for Nepali translation
- **Auto-Translate Button**: If feature available
- **Display Language Toggle**: Switch between EN/NE in editor

##### Targeting & Audience
- **Target Audience**: Multi-select or radio buttons
  - All Users
  - Students Only
  - Teachers Only
  - Parents Only
  - Admins Only
  - Custom Selection (specific users/roles)
  - By Semester: Multi-select semesters

##### Scheduling
- **Publication Date**: Date picker
- **Publication Time**: Time picker
- **Expire Date**: Optional expiration date picker
- **Auto-Hide After Expiry**: Checkbox
- **Publish Immediately**: Checkbox to publish now vs schedule

##### Additional Options
- **Pin to Top**: Priority checkbox
- **Urgent/Alert**: Flag as urgent
- **Allow Comments**: Checkbox if comments feature enabled
- **Attach Files**: File upload section
  - Drag & drop or file picker
  - Allowed file types display
  - List of attached files with remove option

##### Notification Settings
- **Send Email Notifications**: Checkbox
- **Email Target Groups**: Multi-select who gets email
- **In-App Notifications**: Checkbox
- **Push Notifications**: Checkbox (if mobile app)

##### Form Actions
- **Save as Draft**: Save button
- **Publish**: Publish immediately
- **Schedule Publication**: Save and set schedule
- **Preview**: See how notice looks
- **Cancel**: Discard changes

#### Notice Detail View (Public View)
- **Header**: Title, category badge, publication date
- **Content**: Full notice text (bilingual if available)
- **Metadata**: Posted by, views count, date
- **Attachments**: Download links for files
- **Share Button**: Social share or copy link
- **Print Button**: Print notice
- **Related Notices**: Link to similar notices

#### Notice Analytics (Optional)
- **View Count**: Total views
- **Engagement Chart**: Views over time
- **Demographics**: Views by role (students, teachers, etc.)
- **Read Time**: Average time spent reading

#### Bulk Actions
- **Select All**: Checkbox to select all notices
- **Bulk Publish**: Publish multiple drafts
- **Bulk Delete**: Delete multiple archived notices
- **Bulk Archive**: Move multiple to archive
- **Export**: Export notice list to CSV

#### Empty State
- **Message**: "No notices here yet"
- **Icon**: Notification icon
- **Action**: "Create Your First Notice" button

### Data Displayed
- Notice title and content
- Category and tags
- Publication date and time
- Target audience
- Status (Draft, Published, Scheduled, Archived)
- View count
- Author/Created by
- Attachments
- Expiration date (if any)

### Interactions
- Create new notice
- Edit notice content
- Schedule publication date/time
- Select target audience (role-based)
- Add attachments
- Publish/unpublish
- Delete with confirmation
- Archive/restore
- View engagement analytics
- Duplicate notice for next announcement
- Bulk operations on multiple notices

---

## 25. Gallery Management (`admin.gallery`)

### Purpose
- Manage event photos and images
- Organize gallery by categories
- Upload and manage media

### Layout & Components

#### Page Header
- **Title**: "Gallery Management"
- **Upload Photos Button**: Add new photos
- **Create Category Button**: New event/category
- **View Toggle**: Grid view, List view

#### Categories Section
- **Category List**: Show all categories/events
  - Category name, photo count, last updated
  - Edit, Delete buttons per category
  - View Photos button

#### Gallery Grid (Photos View)
- **Responsive Grid**: 3-4 columns on desktop
- **Photo Cards**: Thumbnail with title overlay
- **Hover Effects**: Show options on hover
- **Click to Expand**: Open full image view

#### Photo Card Actions (per photo)
- **View Full**: Expand in lightbox or dedicated view
- **Edit Info**: Change photo title/description
- **Download**: Download full resolution image
- **Delete**: Remove photo with confirmation
- **Move to Album**: Drag or button to move between categories

#### Upload Interface

##### Drag & Drop Zone
- **Large Drop Area**: "Drag photos here"
- **Or Click to Browse**: File picker link
- **Multiple Files**: Support batch upload
- **Progress**: Show upload progress for each file

##### Upload Form
- **Photo Title**: Text input for each photo
- **Description**: Text area
- **Category**: Dropdown to assign category
- **Tags**: Input field for searchable tags
- **Date Taken**: Date picker for event date
- **Photographer**: Optional text field

##### Upload Management
- **Preview Thumbnails**: Show photos being uploaded
- **Upload Progress**: Bar showing progress
- **Success Message**: Confirmation when uploaded
- **Upload All Button**: Batch upload after selecting
- **Clear Selection**: Remove from queue

#### Photo Detail/Edit View
- **Full Photo**: Large display
- **Metadata Section**:
  - Title (editable)
  - Description (editable text area)
  - Category (editable dropdown)
  - Upload date
  - Photographer (editable)
  - Dimensions and file size
  - Tags (editable)
- **Action Buttons**:
  - Save Changes: Update photo info
  - Download: Download original
  - Delete: Remove photo
  - Share: Copy link or social share
  - Print: Print photo

#### Category Management

##### Category List
- **Categories**: Each category shown with
  - Thumbnail (first photo)
  - Category name
  - Photo count badge
  - Last updated date
  - Edit, Delete, View buttons

##### Create/Edit Category Form
- **Category Name**: Text input (required)
- **Category Name (Nepali)**: Nepali version
- **Description**: Text area
- **Color/Icon**: Optional color or icon selector (for styling)
- **Display Order**: Number input for sort order
- **Visibility**: Public/Private toggle
- **Save Button**: Create/update category
- **Cancel Button**: Discard changes

#### Gallery Search & Filter
- **Search**: By photo title, description, or tag
- **Filter by Category**: Dropdown selector
- **Filter by Date**: Date range filter
- **Filter by Tag**: Multi-select tags
- **Advanced Search**: Show more options

#### Gallery Display Options
- **Sort By**: Dropdown (Newest, Oldest, Name, Upload Date)
- **Grid Size**: Thumbnail size selector (small, medium, large)
- **View Mode**: Grid, List, or Slideshow view

#### Slideshow View
- **Auto-play**: Toggle to start/stop
- **Manual Navigation**: Previous/Next buttons or arrow keys
- **Photo Info**: Title and description display
- **Speed Control**: Adjust slideshow speed
- **Exit Slideshow**: ESC key or close button

#### Bulk Actions
- **Select Multiple**: Checkbox to select photos
- **Bulk Delete**: Remove all selected
- **Bulk Move**: Move all selected to different category
- **Bulk Export**: Download as ZIP
- **Bulk Download**: Download selected photos

#### Empty State (No Photos)
- **Message**: "No photos in this category"
- **Icon**: Camera or photo icon
- **Action**: "Upload Photos" button

### Data Displayed
- Photo thumbnail
- Photo title and description
- Event/Category name
- Upload date
- Photographer name (if available)
- Tags
- File size and dimensions

### Interactions
- Drag & drop photos to upload
- Upload photos with metadata
- Create event categories
- Organize photos by category
- Search and filter photos
- View full-size photos in lightbox
- Edit photo information
- Download photos
- Delete photos with confirmation
- Move photos between categories
- Manage photo categories
- Bulk operations on photos
- Share photo links
- Print photos

---

## 26. Study Materials Management (`admin.study-material`)

### Purpose
- Manage educational resources
- Organize materials by subject/semester
- Track material access

### Layout & Components

#### Page Header
- **Title**: "Study Materials Management"
- **Upload Material Button**: Add new resource
- **View Statistics Button**: Access analytics
- **Bulk Upload Button**: Import multiple files

#### Filters & Search
- **Search**: By material name, description, subject
- **Filter by Subject**: Subject dropdown
- **Filter by Semester**: Semester selector
- **Filter by Type**: Lecture Notes, Book, Slides, Assignment, Solution, Question Paper, etc.
- **Filter by Uploaded By**: Teacher selector
- **Date Range**: Upload date range

#### Materials Table
- **Columns**: File Name, Type, Subject, Semester, Size, Uploaded By, Date, Downloads, Status, Actions
- **File Icon**: Indicates file type (PDF, DOC, etc.)
- **Status Badge**: Active, Inactive, Archived
- **Downloads Count**: Number showing how many times downloaded

#### Table Actions (per material)
- **View/Preview**: Open or preview file
- **Download**: Download file
- **Edit Info**: Modify title, description, category
- **Move/Transfer**: Assign to different subject/semester
- **View Analytics**: See download statistics
- **Delete**: Remove material with confirmation
- **Archive**: Move to archive

#### Upload Material

##### Single Upload Form
- **File Selection**: Drag & drop zone or file picker
- **File Type Display**: Show allowed formats (PDF, DOC, PPT, etc.)
- **Material Title**: Text input (auto-filled from filename)
- **Description**: Text area for content summary
- **Subject**: Search and select subject
- **Semester**: Dropdown selector
- **Material Type**: Dropdown (Lecture Notes, Book, Slides, etc.)
- **Visibility**: Public/Private toggle or role-based access
- **Restrict Access**: By semester, teacher, or specific users
- **Upload Button**: Start upload

##### Bulk Upload
- **Multiple File Selection**: Select multiple files at once
- **Batch Form**:
  - Subject: Common subject for all
  - Semester: Common semester for all
  - Material Type: Common type for all
  - Override Option: Allow individual overrides

#### Material Detail View
- **File Info**:
  - File name and format
  - File size and dimensions (if image)
  - Upload date and uploader
  - Last modified date
- **Content Info**:
  - Title and description
  - Subject and semester
  - Material type
  - Tags/keywords
- **Access Stats**:
  - Total downloads
  - Last downloaded date
  - Download trend chart
  - Views by role (student, teacher, parent)
- **Preview**:
  - For images: Show thumbnail and full view
  - For PDFs: Show first page preview
  - For office docs: Show text preview
- **Actions**:
  - Download: Full file download
  - Edit: Modify metadata
  - Delete: Remove with confirmation
  - Share: Copy link or email link
  - Print: Print file (if applicable)

#### Edit Material Form
- **Title**: Text input (editable)
- **Description**: Text area (editable)
- **Subject**: Dropdown (changeable)
- **Semester**: Dropdown (changeable)
- **Material Type**: Dropdown (changeable)
- **Tags**: Multi-input field
- **Access Control**: Who can see/download the material
- **Status**: Active/Inactive/Archived
- **Replace File**: Option to upload new version
- **Save Button**: Update material

#### Materials by Subject View
- **Subject Filter**: Show all materials for one subject
- **Grouped by Type**: Materials grouped by type
- **Count per Type**: Badge showing quantity
- **Recently Added**: Highlight latest uploads

#### Materials Statistics
- **Total Materials**: Count
- **Materials by Subject**: Pie chart
- **Materials by Type**: Bar chart
- **Most Downloaded**: List of top materials
- **Recent Uploads**: Timeline of recent additions
- **Storage Usage**: Total storage used

#### Access Control
- **Public Materials**: Accessible to all users and guests
- **Restricted Materials**: By role (students, teachers, specific semester)
- **Private Materials**: Only for specific users or teacher
- **Expiration Date**: Option to set material expiry

#### Organization

##### By Subject
- Subject list on left
- Materials for subject on right
- Hierarchical view

##### By Semester
- Semester selector
- All materials for that semester

##### By Type
- Type filters on left
- Group materials by type

#### Bulk Actions
- **Select Multiple**: Checkbox for batch operations
- **Bulk Delete**: Remove multiple materials
- **Bulk Change Subject**: Move to different subject
- **Bulk Change Semester**: Change semester
- **Bulk Archive**: Archive multiple
- **Bulk Download**: Download as ZIP

#### Empty State
- **Message**: "No study materials uploaded yet"
- **Icon**: Document icon
- **Action**: "Upload First Material" button

### Data Displayed
- File name, type, size
- Subject and semester
- Material type/category
- Upload date and uploader
- Description/summary
- Download count
- Access restrictions
- Status (Active/Inactive/Archived)

### Interactions
- Upload single or multiple materials
- Edit material information
- Organize by subject/semester/type
- Search and filter materials
- Preview before downloading
- Download materials
- View download analytics
- Delete with confirmation
- Archive materials
- Change access permissions
- Move between subjects/semesters
- Bulk operations

---

## 27. Audit Logs (`admin.audit-logs`)

### Purpose
- Track system activities
- Monitor user actions for security
- Maintain compliance records

### Layout & Components

#### Page Header
- **Title**: "Audit Logs"
- **Export Logs Button**: Export to CSV/Excel
- **Print Button**: Print log report
- **Clear Old Logs Button**: Archive old entries

#### Filters & Search
- **Date Range**: From date to date picker
- **Search**: Search by user, action, resource
- **Filter by User**: User selector dropdown
- **Filter by Action Type**: Create, Update, Delete, View, Export, etc.
- **Filter by Resource Type**: Students, Marks, Attendance, etc.
- **Filter by Status**: Success, Failed
- **Advanced View**: Checkbox to show/hide detailed view

#### Audit Log Table
- **Columns**: Timestamp, User, Action, Resource Type, Resource, Details, Status, IP Address (optional)
- **Timestamp**: Date and time of action
- **User**: Who performed the action
- **Action**: Type of action (Create/Update/Delete/View)
- **Resource**: What was affected (e.g., "Student Record")
- **Resource Name**: Specific item (e.g., "John Doe - Roll 2024001")
- **Details**: Summary of changes made
- **Status Badge**: Success (green) or Failed (red)
- **Sortable**: Click headers to sort

#### Row Expansion
- **Click Row to Expand**: Show details
- **Full Details Panel**:
  - User: Full user information
  - Timestamp: Exact time
  - IP Address: Where action came from
  - Device/Browser: Agent information
  - Action: Detailed action description
  - Before/After: Show what changed (for updates)
  - Old Values: Previous data (highlighted)
  - New Values: Updated data (highlighted)
  - Status: Success/Failed
  - Error Message: If failed, show reason

#### Pagination
- **Page Info**: "Showing entries 1-50 of 5,000"
- **Per Page**: Dropdown (10, 25, 50, 100)
- **Navigation**: Previous, pages, Next

#### Export Options
- **Export as CSV**: Download CSV file
- **Export as Excel**: Download XLS file
- **Export as PDF**: Download PDF report
- **Date Range**: Export includes date range in filename

#### Statistics Section (Top)
- **Total Actions**: Count of all log entries
- **Today's Actions**: Count from today
- **Failed Actions**: Count of failed operations
- **Active Users**: Count of unique users today
- **Most Active User**: User with most actions (today/this period)

#### Filtering by Action Type
- **Create**: New records added
- **Update**: Existing records modified
- **Delete**: Records removed
- **View**: Records accessed (if logging enabled)
- **Export**: Data exported
- **Login**: User logins
- **Logout**: User logouts
- **Download**: File downloads
- **Upload**: File uploads

#### User Activity Timeline
- **User Selector**: Choose user from dropdown
- **Timeline View**: Vertical timeline of user actions
- **Time-based**: Group by hour/day
- **Action Details**: Show what each user did

#### Report Generation
- **Generate Report**: Button to create audit report
- **Report Options**:
  - Date range
  - User(s) to include
  - Action types to include
  - Resource types
  - Report Format: PDF, Excel, HTML
- **Report Content**:
  - Summary statistics
  - Detailed log table
  - Signature lines
  - Report date and prepared by

#### Delete Confirmation
- **Warning**: "Are you sure?" confirmation
- **Reason**: Text field for deletion reason
- **Backup**: Confirm backup exists before clearing
- **Confirm Button**: Proceed with deletion

### Data Displayed
- Timestamp (date and time)
- User name who performed action
- Action type (Create/Update/Delete/View/Export/Login)
- Resource type (Students/Marks/Attendance/Teachers/Courses)
- Resource identifier (name or ID)
- IP address and browser info
- Status (Success/Failed)
- Error message (if failed)
- Old and new values (for updates)
- Action details/description

### Interactions
- Search and filter audit logs
- Sort by any column
- Expand row to see full details
- Compare before/after values
- Export logs to CSV/Excel/PDF
- Filter by date range
- Filter by user or action type
- Print audit report
- Clear very old logs (archive)
- View user activity timeline
- Generate compliance report

---

## 28. Department Settings (`admin.department`)

### Purpose
- Configure department information
- Upload department media and logos
- Set leadership information

### Layout & Components

#### Tabs Navigation
- **Tab 1: Basic Info**
- **Tab 2: Contact**
- **Tab 3: Location**
- **Tab 4: Leadership**
- **Tab 5: Details**
- **Tab 6: Landing Page**
- **Save Button**: Bottom of form (save to all tabs)
- **Reset Button**: Restore previous values

#### Tab 1: Basic Information
- **Logo Upload**: Drag & drop or file picker
  - Current logo display
  - Remove logo button
  - Accepted formats display
- **Department Name (English)**: Text input
- **Department Name (Nepali)**: Text input
- **Short Name (English)**: Text input (e.g., "IT")
- **Short Name (Nepali)**: Text input
- **Tagline/Slogan**: Text area (for landing page display)

#### Tab 2: Contact Information
- **Email Address**: Email input
- **Phone Number**: Phone input (primary)
- **Alternative Phone**: Additional phone
- **Website URL**: URL input
- **Physical Address**: Text area
- **City/District**: Dropdown
- **Postal Code**: Text input
- **Office Hours**: Time range selector (Opening and closing time)
- **Social Media Links**: Inputs for (Facebook, LinkedIn, Twitter, Instagram)

#### Tab 3: Location & Map
- **Latitude**: Decimal coordinate input
- **Longitude**: Decimal coordinate input
- **Map Embed Code**: Text area for Google Maps embed code or custom map
- **Live Map Preview**: Show map on right side (updates as you type coordinates)
- **Set From Map**: Interactive map to click and set coordinates
- **Get Coordinates Button**: Button to auto-fetch from address

#### Tab 4: Leadership Information
- **HOD/Coordinator Information**:
  - Name: Text input
  - Title/Designation: Text input
  - Photo: Image upload
  - Email: Email input
  - Phone: Phone input
  - Bio: Text area
- **Principal/Director Information** (if applicable):
  - Similar fields as above
- **Other Leadership Roles**: Ability to add multiple leadership positions

#### Tab 5: Department Details
- **Department History**: Rich text editor
  - About department
  - Establishment date
  - Initial information
- **Department Registration**: Registration details
  - Registration number
  - Registration date
  - Certifications
- **Department Accreditation**: Any accreditations text field
- **Vision**: Text area for department vision statement
- **Mission**: Text area for department mission statement
- **Values**: Text area or bullet-point description

#### Tab 6: Landing Page Configuration
- **Hero Images**: Image carousel upload
  - Add Image Button: Upload multiple hero images
  - Current Images: Show thumbnails
  - Reorder: Drag to arrange order
  - Remove: Delete individual images
  - Preview: Show how hero section looks
- **Programs/Courses Display**:
  - Intro Text: Text editor for intro section
  - Program Cards: Configure what info shows (name, description, year)
- **Features to Display**: Checklist
  - ☐ Faculty Section
  - ☐ Gallery Section
  - ☐ News/Notices
  - ☐ About Section
  - ☐ Programs Section

#### Form Actions
- **Save All Changes Button**: Large primary button
- **Save & Preview Button**: Save and view landing page
- **Reset Changes Button**: Revert unsaved changes
- **Cancel Button**: Without saving

#### Validation
- **Required Fields**: Mark required with asterisk
- **Email Validation**: Email must be valid format
- **URL Validation**: Websites must start with http/https
- **Coordinate Validation**: Lat/long must be valid
- **File Size**: Show max file size for uploads

#### Success/Error Messages
- **Success Alert**: "Department settings updated successfully"
- **Validation Errors**: Show below each field or in alert
- **Warning**: Notify if changes will affect landing page

#### Live Preview Section (Right Sidebar)
- **Preview**: Show landing page hero
- **Update in Real-time**: As changes are made
- **Refresh Preview Button**: Manually refresh

### Data Displayed
- Department name and logo
- Contact information (email, phone, address)
- Leadership team members
- Department history and mission
- Social media links
- Location coordinates and map
- Hero images for landing page

### Interactions
- Upload and change department logo
- Edit all department information
- Upload multiple hero images and reorder
- Set location coordinates on map
- Configure landing page display
- Edit leadership team information
- Update social media profiles
- Save changes to all tabs
- Preview changes in real-time
- Reset to previous values

---

# 👨‍🏫 TEACHER DASHBOARD & PAGES

## 29. Teacher Dashboard (`teacher.teacherdashboard`)

### Purpose
- Teacher's main interface
- Quick access to assigned classes and tasks
- Real-time updates and notifications

### Layout & Components

#### Welcome Banner
- **Greeting**: "Welcome back, [Teacher Name]"
- **Current Date/Time**: Display current date and time
- **Department Info**: Department name badge
- **Quick Stats**: Classes today, pending tasks count

#### Statistics Cards
- **My Subjects**: Number of subjects assigned
- **Total Students**: Count of all enrolled students across subjects
- **Classes Today**: Number of classes scheduled today
- **Pending Attendance**: Classes awaiting attendance marking
- **Pending Marks**: Classes with marks not yet submitted
- **Recent Activity**: Last login, last action

#### Today's Schedule Section
- **Today's Classes**: List of classes scheduled for today
  - Time, Subject, Classroom, Number of students
  - Mark Attendance Button
  - View Class Button
- **Next Class**: Highlight the next class with countdown timer
- **No Classes Message**: If no classes today

#### Quick Action Buttons (6-8 buttons)
- **Mark Attendance**: Quick link to attendance marking
- **Enter Marks**: Quick link to marks entry
- **View Students**: View assigned students
- **Upload Materials**: Upload study materials
- **Create Notice**: Create announcement
- **View Reports**: Access performance reports
- **View Notifications**: See notifications
- **Settings**: Account settings

#### Subject Summary Cards
- **Subjects Assigned**: Compact cards for each subject
  - Subject name, semester
  - Student count
  - Marks Status (pending/complete)
  - Attendance Status
  - Click to view subject details

#### Recent Activities
- **Timeline**: Recent actions performed
  - Attendance marked
  - Marks submitted
  - Materials uploaded
  - Notices posted
  - Time and date of each action

#### Upcoming Tasks/Alerts
- **To-Do Items**: Pending tasks (color-coded)
  - Mark attendance for Class X
  - Submit marks for Exam Y
  - Create notice for event Z
  - Complete by date

#### Notifications Panel
- **Recent Notifications**: Last 5-10 notifications
  - New message
  - System updates
  - Deadline reminders
  - Action buttons for each

#### Charts & Analytics (Optional)
- **Class Strength Trend**: Line chart of attendance
- **Student Performance**: Bar chart of average marks
- **Attendance Comparison**: Week/month view

### Interactions
- Navigate to quick action sections
- View today's schedule and class details
- Mark attendance from dashboard
- View subject summaries
- Navigate to subject details
- Check notifications and alerts
- Click to-do items to navigate to action
- View recent activities

---

## 30. Teacher Subjects Page (`teacher.subjects`)

### Purpose
- View all assigned subjects
- Access subject-specific information
- Manage subject materials and assignments

### Layout & Components

#### Page Header
- **Title**: "My Subjects"
- **View Toggle**: List view, Card view
- **Filter by Semester**: Dropdown selector
- **Export Button**: Export subject list
- **Refresh Button**: Refresh data

#### Subjects View

##### Card View (Default)
- **Subject Card Grid**: 2-3 columns
- **Card Content**:
  - Subject name and code
  - Semester and credits
  - Student count badge
  - Last updated date
  - Quick action buttons

##### List View
- **Table Columns**: Subject Code, Name, Semester, Credits, Students, Last Updated, Actions
- **Sortable Headers**: Click to sort
- **Row Hover**: Highlight and show action buttons

#### Subject Card Actions
- **View Details**: Click card to open details
- **View Students**: Link to student list
- **Mark Attendance**: Quick link to attendance
- **Enter Marks**: Quick link to marks entry
- **Upload Materials**: Upload study resources
- **View Reports**: Subject performance report
- **Edit**: Modify subject details
- **More Options**: Dropdown menu

#### Subject Details View (Click Subject)
- **Header**: Subject name, code, semester, credits
- **Tabs**:
  - Overview: Description, objectives
  - Students: Enrolled student list
  - Attendance: Class attendance records
  - Marks: Student marks for subject
  - Materials: Study materials uploaded
  - Timetable: Class schedule for subject
- **Action Buttons**: Mark Attendance, Enter Marks, Upload Materials

#### Pagination & Empty State
- **Pagination**: If many subjects
- **No Subjects**: Message if teacher has no assigned subjects

### Interactions
- View assigned subjects in card or list view
- Filter by semester
- Click subject to view details
- Quick navigation to attendance, marks, materials
- Upload subject materials
- View student roster
- Export subject data

---

## 31. Teacher Students List (`teacher.students`)

### Purpose
- View students enrolled in teacher's subjects
- Filter and search students
- Access student information

### Layout & Components

#### Page Header
- **Title**: "My Students"
- **Total Count**: "125 students total"
- **Export Button**: Export student list
- **Print Button**: Print list

#### Filters & Search
- **Search**: By student name, roll number
- **Filter by Subject**: Subject dropdown (all or specific)
- **Filter by Semester**: Semester selector
- **Filter by Status**: Enrolled, Inactive, Dropped
- **Attendance Range**: Filter by attendance % range
- **Performance**: Filter by grade range (if marks entered)

#### Students Table
- **Columns**: Roll Number, Name, Email, Subject, Attendance %, Performance, Actions
- **Performance Indicator**: Color-coded based on grades
- **Attendance Badge**: Green (>75%), Yellow (50-75%), Red (<50%)
- **Row Hover**: Show action buttons

#### Table Actions (per student)
- **View Profile**: See full student information
- **View Attendance**: Student's attendance record
- **View Marks**: Student's marks/grades
- **Send Message**: Send message to student (if feature enabled)
- **View File**: Student's submissions or documents
- **Add Note**: Add private teacher notes about student
- **Flag**: Flag for follow-up or concern

#### Bulk Actions
- **Select Multiple**: Checkbox for batch operations
- **Send Message**: Send to all selected students
- **Send Email**: Email selected students
- **Export**: Export selected student data

#### Sorting & Export
- **Sort By**: Dropdown (Name, Roll, Attendance, Performance)
- **Export Format**: CSV, Excel, PDF
- **Print**: Print student list

#### Empty State
- **Message**: "No students found matching criteria"
- **Action**: "Clear filters" or "Add students" link

### Interactions
- Search for students
- Filter by subject, attendance, performance
- View individual student profiles
- View student attendance records
- View student marks
- Send messages to students
- Flag students for follow-up
- Export or print student list
- Add notes about students

---

## 32. Teacher Attendance Page (`teacher.attendance`)

### Purpose
- Mark student attendance
- Manage attendance records
- View and export attendance reports

### Layout & Components

#### Page Header
- **Title**: "Attendance Management"
- **Tabs**: "Mark Attendance", "Lab Attendance", "Attendance History"

#### Mark Attendance Tab

##### Selection Section
- **Date Picker**: Select attendance date
- **Subject Selector**: Choose subject to mark attendance
- **Load Students Button**: Fetch roster for selection

##### Students Attendance Table
- **Column**: Checkbox, Student Name/Roll, Attendance Status
- **Status Options**: Present (Green), Absent (Red), Late (Yellow), Leave (Orange)
- **Click to Toggle**: Click cell to change status
- **Quick Mark All**: Button to mark all present/absent quickly

##### Bulk Mark Options
- **Mark All Present**: Select all as present
- **Mark All Absent**: Select all as absent
- **Clear Selections**: Reset all

##### Form Actions
- **Save Attendance**: Submit attendance data
- **Cancel**: Discard changes
- **Print**: Print attendance sheet before saving

#### Lab Attendance Tab
- **Lab Subject Selector**: Select practical/lab subject
- **Lab Batch**: Select batch/group if applicable
- **Similar Attendance Interface**: Mark attendance for lab

#### Attendance History Tab
- **View Attendance Records**: Past attendance entries
- **Filters**: Date range, subject, status
- **Table**: Date, Subject, Students Present/Absent/Late, Total Marked
- **Edit Option**: Click to edit previous attendance
- **Delete Option**: Remove attendance entry (with confirmation)

#### Attendance Reports
- **Report Type Selector**: Dropdown (By Subject, By Date, By Student)
- **Filters**: Subject, date range, semester
- **Generate Button**: Create report
- **Report Display**: Chart and/or table format
- **Export**: PDF, Excel, CSV buttons

#### Daily Attendance Summary
- **Today's Stats**: Last attendance marked time
- **Subjects Marked**: Which subjects marked today
- **Pending**: Which subjects not yet marked

### Interactions
- Select date and subject
- Load student roster
- Mark attendance for each student
- Use bulk mark options
- Save attendance records
- View previous attendance records
- Edit past attendance
- Generate attendance reports
- Export or print reports
- View attendance analytics

---

## 33. Teacher Marks Page (`teacher.marks`)

### Purpose
- Enter and manage student marks
- Create marks sheets
- View marks records

### Layout & Components

#### Page Header
- **Title**: "Marks Management"
- **Tabs**: "Enter Marks", "View Marks", "Marks History"

#### Enter Marks Tab

##### Selection Section
- **Exam Type Selector**: Dropdown (Midterm, Final, Assessment, etc.)
- **Subject Selector**: Select subject
- **Semester Selector**: Select semester
- **Load Marks Button**: Fetch current marks or create new sheet

##### Marks Entry Table
- **Columns**: Student Name/Roll Number, Marks Entry Field
- **Marks Input**: Editable cells with validation
- **Max Marks Display**: Show maximum allowed marks
- **Validation**: Error if marks exceed max
- **Grade Auto-Calculate**: Show grade as marks entered (if applicable)
- **Comments Field**: Optional notes per student (hidden column, expandable)

##### Bulk Import
- **Import from File Button**: Upload CSV/Excel with marks
- **Guide**: Link to template for import format
- **Map Columns**: Dialog to map uploaded file columns

##### Form Actions
- **Save All**: Save all entered marks
- **Save & Close**: Save and return to previous
- **Save as Draft**: Save without submitting
- **Preview**: Preview marks before saving
- **Cancel**: Discard changes

#### View Marks Tab
- **Filter Options**: Subject, exam type, semester, date range
- **Marks Table**: Student, Subject, Marks, Grade, Entry Date
- **View Details**: Click to see full mark details
- **Edit Marks**: Open edit form for marks adjustment
- **Sort & Search**: Sort by columns, search students

#### Marks History Tab
- **All Entries**: All marks ever entered by teacher
- **Timeline View**: Grouped by date/exam
- **Details**: Show what was entered, when, by whom
- **Edit History**: Click to view or edit specific entry
- **Restore**: Option to revert to previous marks (if allowed)

#### Import/Export
- **Download Template**: Get blank marks sheet template
- **Export Current**: Download marks to CSV/Excel
- **Print Marks**: Print marks sheet
- **Email Marks**: Email marks file to students (if feature enabled)

### Interactions
- Select exam, subject, semester
- Load student marks or create new
- Enter marks for students
- Validate marks against max allowed
- Save single or multiple marks
- Import marks from file
- View previous marks entries
- Edit marks
- Export or print marks
- Generate grade reports

---

## 34. Teacher Marksheets Page (`teacher.marksheets`)

### Purpose
- Access comprehensive marksheet generation
- View semester transcripts
- Print marksheets

### Layout & Components

#### Page Header
- **Title**: "Marksheets"
- **Search Button**: Search for student marksheet
- **Semester Selector**: Filter by semester

#### Marksheet Search
- **Student Search**: Autocomplete search by roll/name
- **Semester Filter**: Select semester
- **Search Button**: Find marksheet

#### Marksheet Display (Similar to Admin)
- **Marksheet Header**: Student info, semester
- **Marks Table**: All subjects with marks and grades
- **Semester GPA**: Calculated GPA
- **Grade Distribution**: Summary statistics
- **Print Button**: Print-friendly layout
- **Export Button**: Export as PDF
- **Close Button**: Return to search

### Interactions
- Search for specific student marksheet
- View student marksheet
- Print marksheet
- Export as PDF
- Navigate between student marksheets

---

## 35. Teacher Study Materials (`teacher.study-materials`)

### Purpose
- Upload course materials
- Organize materials by subject
- Manage resource access

### Layout & Components

#### Page Header
- **Title**: "Study Materials"
- **Upload Button**: Add new material
- **Bulk Upload Button**: Upload multiple files

#### Filters
- **Filter by Subject**: Subject selector
- **View My Materials**: Toggle to show only own uploads
- **Search**: By name or description

#### Materials Table
- **Columns**: File Name, Subject, Type, Upload Date, Downloads, Actions
- **Download Count**: Shows how many times downloaded
- **Edit Button**: Modify material info
- **Delete Button**: Remove material
- **View Details**: See full information

#### Upload Form
- **File Selection**: Drag & drop or file picker
- **Title**: Enter material title
- **Description**: Text area for description
- **Subject**: Select subject
- **Material Type**: Dropdown (Lecture Notes, Slides, etc.)
- **Upload Button**: Submit

#### Material Details View
- **File Info**: Name, size, type, upload date
- **Subject & Type**: Categorization
- **Description**: Full description
- **Downloads**: Count of downloads
- **Edit/Delete**: Links to modify or remove
- **Download Button**: File download link

### Interactions
- Upload new materials
- Edit material information
- Delete materials
- View material details
- Track downloads
- Organize by subject

---

## 36. Teacher Notices Page (`teacher.notices`)

### Purpose
- Create class announcements
- Post notices for students
- Archive notices

### Layout & Components

#### Page Header
- **Title**: "Class Notices"
- **Create Notice Button**: Post new notice

#### Notices List
- **Table Columns**: Title, Category, Posted Date, Status, Views, Actions
- **Status Badge**: Published, Draft
- **View Count**: Number of views

#### Create Notice Form
- **Title**: Text input
- **Content**: Rich text editor
- **Category**: Dropdown (Assignment, Announcement, Deadline, etc.)
- **Attach Files**: Optional file attachment
- **Publish Now**: Checkbox for immediate publishing
- **Target Students**: Multi-select students (or all class students)
- **Post Button**: Publish notice

#### Notice Detail View
- **Full Content**: Complete notice text
- **Metadata**: Posted date, views count
- **Edit Button**: Modify notice
- **Delete Button**: Remove notice

### Interactions
- Create and post notices
- View notice details
- Track notice views
- Edit notices
- Delete notices
- Attach files to notices

---

## 37. Teacher Exams Page (`teacher.exams`)

### Purpose
- View assigned exams
- Upload marks for exams
- Track exam status

### Layout & Components

#### Page Header
- **Title**: "Exams"
- **Filter by Status**: Upcoming, Ongoing, Completed

#### Exams List
- **Table Columns**: Exam Name, Subject, Type, Date, Status, Marks Status, Actions
- **Marks Status Badge**: Pending, Submitted, Incomplete
- **View Details**: Click exam name

#### Exam Detail View
- **Exam Info**: Name, type, subject, date, max marks
- **Students**: Number of students enrolled
- **Marks Status**: Pending, Submitted count
- **Actions**:
  - Upload Marks: Upload marks sheet
  - View Marks: See entered marks
  - Edit Marks: Modify individual marks
  - View Students: Student list for exam
  - Print Sheet: Print blank sheet for marks entry

#### Marks Upload Interface
- **File Upload**: Select file (CSV/Excel)
- **Mapping**: Map file columns to marks fields
- **Preview**: Show preview before importing
- **Import Button**: Submit marks

### Interactions
- View assigned exams
- Filter by status
- Upload student marks
- View marks entered
- Edit individual marks
- Download marks template
- View exam details and student list

---

## 38. Teacher Timetable (`teacher.timetable`)

### Purpose
- View personal class schedule
- Print schedule
- Track timing

### Layout & Components

#### Page Header
- **Title**: "My Timetable"
- **Print Button**: Print schedule
- **View Toggle**: Week View, Day View

#### Timetable Display
- **Grid Layout**: Weekdays across columns, times down rows
- **Classes**: Show scheduled classes in slots
- **Class Info**: Subject, classroom, student count
- **Color Coding**: Different colors per subject
- **Navigation**: Previous/Next week buttons (week view)

#### Day View
- **Selected Day**: Show detailed schedule for day
- **Time Slots**: Classes in chronological order
- **Class Details**: Subject, time, classroom, students count

### Interactions
- View weekly or daily schedule
- Navigate between weeks/days
- Print timetable
- Click class for details

---

## 39. Teacher Reports (`teacher.reports`)

### Purpose
- Access performance reports
- View student analytics
- Generate insights

### Layout & Components

#### Report Types
- **Attendance Reports**: Class attendance trends
- **Performance Reports**: Student grades analysis
- **Custom Reports**: Build custom reports

#### Attendance Report
- **Graph**: Attendance trend over time
- **Statistics**: Class average attendance
- **Alert**: Show if attendance below threshold
- **Export**: Download report

#### Performance Report
- **Grade Distribution**: Students by grade
- **Class Average**: Average marks
- **Top Performers**: Best students
- **Low Performers**: Students needing support
- **Trend**: Performance over exams

### Interactions
- Select report type
- Choose filters (subject, semester, date range)
- View report visualizations
- Export report
- Print report

---

## 40. Teacher Profile (`teacher.profile.edit`)

### Purpose
- View and edit teacher information
- Update personal profile
- Change password

### Layout & Components

#### Profile Information
- **Photo**: Current profile picture, upload new
- **Name**: Display and editable
- **Email**: Display and editable
- **Phone**: Display and editable
- **Qualifications**: Display and editable
- **Specializations**: Display and editable
- **Bio**: Display and editable

#### Account Settings
- **Change Password**: Current password, new password, confirm
- **Email Preferences**: Notification settings
- **Privacy**: Profile visibility settings

#### Save Changes
- **Save Button**: Update profile
- **Cancel Button**: Discard changes

### Interactions
- Update profile information
- Upload new photo
- Change password
- Update contact information
- Save changes

---

# 🎓 STUDENT DASHBOARD & PAGES

## 41. Student Dashboard (`student.studentdashboard`)

### Purpose
- Main student interface
- Quick access to courses and grades
- Track academic progress

### Layout & Components

#### Welcome Banner
- **Greeting**: "Welcome, [Student Name]"
- **Semester**: Current semester display
- **GPA**: Current cumulative GPA
- **Attendance**: Overall attendance percentage

#### Statistics Cards
- **Enrolled Subjects**: Number of courses
- **Attendance**: Overall % and badge color
- **Average Grade**: GPA or average marks
- **Pending Assignments**: Count if applicable

#### My Courses Section
- **Course Cards**: List of enrolled courses
  - Course name and code
  - Instructor name
  - Student count
  - Last updated
  - Link to view course details

#### Academic Progress
- **GPA Chart**: Semester-wise GPA trend
- **Grade Distribution**: Pie chart of grades
- **Attendance Chart**: Attendance over time

#### Recent Announcements
- **Latest Notices**: Recent 3-5 notices
- **View All Link**: Link to notices page

#### Quick Links
- **View Timetable**: Link to class schedule
- **View Attendance**: Link to attendance
- **View Marks**: Link to grades/marks
- **Download Materials**: Link to study materials

### Interactions
- Navigate to courses
- View academic progress
- Check announcements
- Access timetable, attendance, marks

---

## 42. Student Courses Page (`student.courses.index`)

### Purpose
- View enrolled courses
- Access course materials
- Track course progress

### Layout & Components

#### Page Header
- **Title**: "My Courses"
- **Semester Selector**: Filter by semester
- **View Toggle**: Grid view, List view

#### Courses Grid/List
- **Course Card**: Course name, code, instructor, credits
- **Click for Details**: Open course detail page

#### Course Detail View
- **Course Header**: Name, code, credits, instructor
- **Course Info**: Description, objectives, schedule
- **Student List**: Class roster (view other students)
- **Materials**: Available study materials
- **Announcements**: Course-specific announcements
- **Marks**: View grades/performance in course
- **Attendance**: Attendance in the course

### Interactions
- View enrolled courses
- Click to view course details
- Access course materials
- View course announcements
- Check course attendance and marks
- Filter by semester

---

## 43. Student Attendance Page (`student.attendance.index`)

### Purpose
- Track personal attendance
- View attendance by subject
- Monitor attendance status

### Layout & Components

#### Overall Attendance Section
- **Total Attendance %**: Large display with color (green if >75%)
- **Status**: Alert if attendance low (<75%)
- **Classes Attended**: Count vs. total classes
- **Progress Bar**: Visual representation

#### Attendance by Subject Table
- **Columns**: Subject Name, Classes Attended, Total Classes, Attendance %, Status
- **Status Badge**: Good (green), Warning (yellow), Alert (red)
- **Sortable**: Click headers to sort
- **Color Coded**: Green for >75%, yellow for 50-75%, red for <50%

#### Attendance Detail View (Click Subject)
- **Subject Info**: Name, code, instructor
- **Attendance Records**: List of all attendance marks
  - Date, status (Present/Absent/Late/Leave)
- **Monthly Summary**: Attendance by month
- **Chart**: Visual attendance trend

#### Print/Export
- **Print Button**: Print attendance report
- **Export Button**: Download as PDF/Excel

### Interactions
- View overall attendance
- View attendance by subject
- Click subject for detailed record
- Print or export attendance report

---

## 44. Student Marks/Results Page (`student.marks.index`)

### Purpose
- View exam marks and grades
- Track academic performance
- Monitor GPA progress

### Layout & Components

#### Semester Selector
- **Dropdown**: Select semester to view marks
- **Academic Year**: Display year

#### Marks Summary
- **Semester GPA**: Large display
- **Grade Count**: Distribution of grades
- **Average Marks**: Class average comparison

#### Marks Table
- **Columns**: Subject Code, Subject Name, Exam Type, Marks, Grade, Comments (if any)
- **Sortable**: Click headers
- **Color Coded**: Based on grade (A=Green, B=Blue, C=Yellow, D/F=Red)

#### Subject Detail View (Click Subject)
- **Subject Info**: Name, code, credits, instructor
- **Marks Breakdown**: Internal, Assessment, Final (if applicable)
- **Total Marks**: Final marks and grade
- **Grade Scale**: Reference showing what grade means
- **Class Statistics**: Average marks, class grade distribution

#### Performance Chart
- **GPA Trend**: Line chart showing GPA across semesters
- **Grade Improvement**: Show trend over time

#### Print/Export
- **Print Marksheet**: Print formatted marksheet
- **Export PDF**: Download as PDF
- **Print Report**: Print grade report

### Interactions
- Select semester to view
- View marks summary and table
- Click subject for detailed marks
- View performance trends
- Print or export marks/marksheet
- Compare with class average

---

## 45. Student Timetable (`student.timetable.index`)

### Purpose
- View class schedule
- Check timing for classes
- Print personal schedule

### Layout & Components

#### Page Header
- **Title**: "My Timetable"
- **Semester**: Current semester display
- **Print Button**: Print schedule
- **Download Button**: Download as PDF/image

#### Timetable Grid
- **Week View**: Default view with all weekdays
- **Time Slots**: 7:00 AM to 5:00 PM (or configured times)
- **Classes**: Show subject name, teacher, classroom
- **Color Coding**: Different colors per subject
- **Navigation**: Previous/Next week buttons

#### Day View Option
- **Select Day**: Dropdown or click day header
- **Day Schedule**: Detailed view of classes for selected day
- **Class Details**: Time, subject, teacher, classroom, notes

#### Today Highlight
- **Today Badge**: Mark today's column
- **Next Class**: Highlight next upcoming class with timer
- **Current Time**: Show current time on schedule

#### Print Layout
- **Professional Format**: Suitable for printing
- **All Week Classes**: Full schedule visible
- **Legend**: Subject names and colors

### Interactions
- View weekly or daily schedule
- Navigate weeks
- View class details
- Print schedule
- Download as PDF/image

---

## 46. Student Profile (`student.profile.edit`)

### Purpose
- View and edit personal information
- Update contact details
- Change password

### Layout & Components

#### Profile Information
- **Photo**: Current profile picture, upload new
- **Name**: Display name
- **Roll Number**: Student ID (non-editable)
- **Email**: Display and editable
- **Phone**: Display and editable
- **Address**: Display and editable
- **Date of Birth**: Display (non-editable typically)

#### Academic Information
- **Current Semester**: Display
- **Enrollment Date**: Display
- **GPA**: Display
- **Credits Completed**: Display

#### Account Settings
- **Change Password**: Current password, new password, confirm
- **Email Preferences**: Notification settings
- **Privacy Settings**: Profile visibility

#### Save Changes
- **Save Button**: Update profile
- **Cancel Button**: Discard changes
- **Delete Account**: Link to account deletion (if allowed)

### Interactions
- Update personal information
- Upload new photo
- Change password
- Update contact details
- Save changes
- Manage notifications

---

# 👨‍👩‍👧 PARENT DASHBOARD & PAGES

## 47. Parent Dashboard/Portal (`parent.parentdashboard`)

### Purpose
- Parent's main interface
- Track child's academic progress
- Quick access to important information

### Layout & Components

#### Welcome Section
- **Greeting**: "Welcome, [Parent Name]"
- **Child/Children**: Display linked children
- **Quick Stats**: Child's attendance, GPA, status

#### Child Selection
- **Dropdown**: Select which child to view details for
- **Multiple Children**: If parent has multiple children
- **Child Cards**: Quick view of each child's status

#### Child Academic Summary (for selected child)
- **Name**: Child's name and class photo
- **Semester**: Current semester
- **GPA**: Current GPA
- **Attendance**: Overall attendance %
- **Status Alert**: If any concerns (low attendance, low marks, etc.)

#### Recent Marks
- **Latest Exam Marks**: Most recent marks entered
- **Subject**: Subject and exam type
- **Marks & Grade**: Display performance
- **Trend**: Arrow showing better/worse than previous

#### Attendance Status
- **Overview**: Current attendance percentage
- **Status Badge**: Good/Warning/Alert
- **Subject Breakdown**: Attendance by subject
- **Attendance Trend**: Chart showing attendance over time

#### Recent Announcements
- **Class Announcements**: Recent notices from child's classes
- **Department Notices**: Important departmental announcements
- **View All Link**: Link to full announcements page

#### Quick Actions
- **View Child's Attendance**: Link to detailed attendance
- **View Child's Marks**: Link to marksheet/results
- **View Child's Courses**: Link to enrolled courses
- **View Schedule**: Link to child's timetable
- **Messages**: Link to teacher communications

#### Notifications/Alerts
- **Important Information**: Alerts from school (deadline, event, etc.)
- **Performance Alert**: If marks/attendance below threshold
- **System Message**: Any system notifications

### Interactions
- Select child to view information
- Navigate to child's attendance, marks, courses
- View recent announcements
- Check for alerts or notifications
- Access communication with teachers

---

## 48. Parent Children Management (`parent.children` & `parent.children.show`)

### Purpose
- Manage linked children
- View individual child's information
- Track multiple children

### Layout & Components

#### Children List
- **Child Cards**: One card per child
  - Child photo/avatar
  - Name and roll number
  - Semester
  - Current GPA
  - Current attendance
  - Status indicator
  - Click to view details

#### Child Detail Page
- **Child Header**: Photo, name, roll number, semester
- **Basic Info**: Date of birth, gender, address
- **Academic Info**: GPA, credits completed, enrollment date
- **Contact Info**: Email, phone

#### Tabs/Sections
- **Performance**: Semester-wise GPA, grade distribution
- **Attendance**: Attendance tracking and trends
- **Subject Enrollment**: List of current subjects
- **Courses**: Detailed course information
- **Documents**: Downloadable documents (admit card, etc.)

#### Action Links
- **View Attendance**: Full attendance detail
- **View Marks**: Marksheet and grades
- **View Timetable**: Class schedule
- **Download Transcript**: Academic transcript
- **View Courses**: Course list

### Interactions
- View list of linked children
- Click child to view details
- Navigate to attendance, marks, courses
- Download documents
- View performance trends

---

## 49. Parent Attendance Tracking (`parent.attendance`)

### Purpose
- Monitor child's attendance
- View attendance by subject
- Receive low attendance alerts

### Layout & Components

#### Child Selector
- **Dropdown**: If multiple children, select which child

#### Attendance Overview
- **Overall Attendance %**: Large display with color indicator
- **Alert**: If below 75% threshold (red warning)
- **Status**: Good standing or at-risk message
- **Last Updated**: Show when attendance data was last updated

#### Attendance by Subject Table
- **Columns**: Subject Name, Classes Attended, Total Classes, Attendance %, Status
- **Sortable**: Click headers to sort
- **Status Badges**: Green if >75%, yellow if 50-75%, red if <50%
- **Trend Indicator**: Arrow showing improving/declining trend

#### Attendance Chart
- **Visual Trend**: Line chart showing attendance over time
- **Month/Week Toggle**: Switch between time periods
- **By Subject View**: Separate line per subject

#### Subject Detail
- **Click Subject Row**: Expand to show detailed attendance
- **Date-wise Attendance**: List of all attendance marks
  - Date, status (Present/Absent/Late/Leave)
- **Monthly Summary**: Breakdown by month

#### Print/Export
- **Print Button**: Print attendance report
- **Export PDF**: Download PDF
- **Share**: Email to parent's other email address

#### Low Attendance Alert
- **Alert Box**: If attendance falls below 75%
- **Action Items**: Suggestions or required follow-up
- **Contact Teacher**: Link to send message to teacher

### Interactions
- View child's overall attendance
- View attendance by subject
- Click subject for detailed record
- View attendance trends over time
- Print or export attendance
- Receive and respond to alerts

---

## 50. Parent Results/Marks Viewing (`parent.results` & `parent.marks.index`)

### Purpose
- View child's exam results
- Track academic performance
- Monitor grade trends

### Layout & Components

#### Child Selector
- **Dropdown**: If multiple children

#### Semester Selector
- **Dropdown**: Select semester to view results
- **Academic Year**: Display year

#### Semester Summary
- **GPA**: Semester GPA display
- **Grade Distribution**: Pie chart or summary
- **Performance**: Compared to class average
- **Status**: Passing/Excellent/At-risk message

#### Marks Table
- **Columns**: Subject Code, Subject Name, Exam Type, Marks, Total, Grade, Percentage
- **Color Coded**: Based on performance
- **Sortable**: Sort by subject or grade
- **Note**: Any teacher comments

#### Subject Detail View
- **Click Subject**: Expand row for details
- **Marks Breakdown**: Theory, practical, internal, final marks
- **Calculation**: Show how grade was calculated
- **Class Average**: Compare to class average
- **Trend**: Performance in this subject over semesters

#### Performance Analytics
- **GPA Trend**: Line chart showing GPA across semesters
- **Grade Improvement/Decline**: Visual trend
- **Subject Performance**: Bar chart of average marks per subject

#### Grade Scale Reference
- **Grade Scale Table**: Show A=4.0, B=3.5, etc.
- **Passing Mark**: Show minimum passing mark

#### Download/Print
- **Download Marksheet**: PDF copy of marksheet
- **Print Report**: Print grade report
- **Email Report**: Email to parent's email address

#### Performance Alerts
- **Alert Box**: If marks are low
- **Improvement Suggestions**: If available
- **Teacher Contact**: Link to reach out to teacher

### Interactions
- Select semester to view
- View marks table and summary
- Click subject for detailed marks
- View performance trends
- Download or print results
- Contact teacher regarding performance
- View grade scale information
- Compare with class average

---

## 51. Parent Courses/Subjects (`parent.courses` & `parent.courses.subject`)

### Purpose
- View child's enrolled subjects
- See subject details
- Track enrollment

### Layout & Components

#### Child Selector
- **Dropdown**: If multiple children

#### Subjects List
- **Grid or Table**: All enrolled subjects
- **Subject Cards**: Name, code, credits, semester, instructor
- **Click to View Details**: Open subject detail page

#### Subject Detail View
- **Header**: Subject name, code, credits, semester
- **Instructor Info**: Teacher name, email, phone, office hours
- **Course Info**: Description, objectives, prerequisites
- **Student Count**: Number in class
- **Schedule**: Class times and classroom
- **Materials**: Study materials (if accessible)
- **Max Marks**: Marks for subject
- **Prerequisites**: If any

#### Back Button
- **Return to List**: Go back to subjects list

### Interactions
- View list of enrolled subjects
- Click subject for details
- View instructor information
- Check course schedule and materials
- Go back to subject list

---

## 52. Parent Events/Gallery (`parent.events`)

### Purpose
- View department events and photos
- Stay informed about activities
- Download event photos

### Layout & Components

#### Events List
- **Event Cards**: Title, date, category, photo
- **Click to View Details**: Open event detail page

#### Event Detail View
- **Event Header**: Title, date, location, category
- **Description**: What the event was about
- **Gallery**: Photos from the event in carousel or grid
- **Photo Details**: Can click photo to view full size
- **Attendees Count**: Number of people at event (if tracked)
- **Download Option**: Download photos as ZIP

#### Gallery Grid
- **Responsive Layout**: Photo thumbnails
- **Click to Expand**: View full image
- **Navigation**: Previous/Next in lightbox
- **Download Original**: Save full resolution

#### Filter Options
- **Filter by Date**: Date range filter
- **Filter by Category**: Event type selector
- **Search**: Search by event name

### Interactions
- View list of events
- Click event to see details and photos
- View photos in gallery
- Download event photos
- Filter and search events
- Share event photos (if enabled)

---

## 53. Parent Notices/Announcements (`parent.notices`)

### Purpose
- Read department announcements
- Stay informed of events and deadlines
- Access important information

### Layout & Components

#### Notices List
- **Recent Notices**: Latest announcements
- **Filter by Category**: Academic, Event, Holiday, etc.
- **Search**: Find specific announcements
- **View All Link**: See all announcements

#### Notice Card
- **Title**: Announcement title
- **Date**: Publication date
- **Category**: Type of notice
- **Summary**: First 2-3 lines of content
- **Click to Read**: Open full notice

#### Notice Detail View
- **Header**: Title, publication date, category
- **Full Content**: Complete announcement text
- **Attachments**: Any files attached
- **Date Valid Until**: Expiration date if applicable
- **Print Button**: Print notice
- **Share Button**: Share notice (email or link)

#### Archive Section
- **Older Notices**: Browse previous announcements
- **Date Range Filter**: Filter by publication date

### Interactions
- View recent announcements
- Filter by category
- Search for announcements
- Read full notice
- Print announcements
- Access archived notices
- Share with others

---

## 54. Parent Communication (`parent.communication`)

### Purpose
- Message with teachers
- Send inquiries about child's progress
- Receive teacher responses

### Layout & Components

#### Message Interface
- **Compose Area**: New message form
- **To Field**: Select teacher/recipient
- **Subject**: Message subject line
- **Message Body**: Rich text editor
- **Send Button**: Submit message

#### Inbox
- **Message List**: All messages received
- **Columns**: From (Teacher name), Subject, Date, Read/Unread
- **Unread Badge**: Show unread count
- **Click to Open**: Read full message

#### Message Thread
- **Conversation View**: All messages in conversation
- **Reply Box**: Respond to message
- **Attachment**: Can attach files

#### Search/Filter
- **Search**: Find messages by teacher or subject
- **Filter by Status**: Unread, Read
- **Sort**: By date, sender

#### Notification Settings
- **Opt-in/Out**: Control message notifications
- **Email Forwarding**: Forward messages to email

### Interactions
- Send new message to teacher
- Read received messages
- Reply to messages
- Search messages
- Manage notification preferences

---

## 55. Parent Print/Export (`parent.print`)

### Purpose
- Print consolidated child reports
- Export academic information

### Layout & Components

#### Report Selection
- **Select Child**: If multiple children
- **Report Type**: Academic report, attendance report, performance report
- **Semester**: Select semester
- **Generate Button**: Create report

#### Report Preview
- **Document Preview**: How report will look when printed
- **Print Button**: Send to printer
- **Save as PDF**: Download PDF copy
- **Email Button**: Email report

#### Report Content
- **Child Info**: Name, roll number, semester
- **Attendance Summary**: Overall and by subject
- **Marks Summary**: Semester marks, GPA
- **Performance Assessment**: Written summary
- **Signature Line**: For official purposes

### Interactions
- Select report type and options
- Preview before printing
- Print or save report
- Email report to email address

---

## 56. Parent Profile (`parent.profile.edit`)

### Purpose
- Update contact information
- Manage account settings
- Change password

### Layout & Components

#### Profile Information
- **Name**: Display and editable
- **Email**: Display and editable
- **Phone**: Display and editable
- **Address**: Display and editable
- **Relationship**: Relationship to child (Father, Mother, Guardian, etc.)

#### Account Settings
- **Change Password**: Current password, new password, confirm
- **Email Preferences**: Notification settings
- **Privacy**: Profile visibility settings
- **Linked Children**: List of linked children

#### Save Changes
- **Save Button**: Update profile
- **Cancel Button**: Discard changes

### Interactions
- Update contact information
- Change password
- Modify notification preferences
- Save changes

---

# 📄 PUBLIC/SHARED PAGES

## 57. Shared Components & Layouts

### Navigation Header (All Pages)
- **Logo**: Department logo clickable link to home
- **Department Name**: Display in header
- **Navigation Menu**: Home, About, Faculty, Subjects, Resources, Notices, Gallery
- **User Menu**: Dropdown with user name and options:
  - View Profile
  - Settings
  - Logout
- **Language Switcher**: English/Nepali toggle
- **Dark Mode Toggle**: Light/Dark theme switch
- **Responsive**: Hamburger menu on mobile

### Sidebar (Admin/Teacher/Student/Parent)
- **Role-specific Navigation**: Different menu items per role
- **Department Info**: Logo and name at top
- **Main Menu Items**: Links to primary sections
- **Sub-menu Items**: Expandable for subsections
- **Collapse Button**: Minimize sidebar for more content space
- **Active Indicator**: Highlight current page
- **Icons**: Visual icons for each menu item

### Footer
- **Links**: Quick links to important pages
- **Contact**: Department contact information
- **Social Media**: Links to social profiles
- **Copyright**: © Info and legal links
- **Responsive**: Stack on mobile

### Modals & Dialogs
- **Confirmation Modal**: Confirm destructive actions
- **Form Modal**: Create/edit forms in overlay
- **Alert Modal**: Display important messages
- **Loading Modal**: Show during processing
- **Close Button**: X to close modal
- **Keyboard Support**: ESC to close

### Notifications/Alerts
- **Success Alert**: Green background, checkmark icon, positive message
- **Error Alert**: Red background, X icon, error message
- **Warning Alert**: Yellow background, warning icon, caution message
- **Info Alert**: Blue background, info icon, informational message
- **Toast Notifications**: Small pop-up in corner
- **Auto-dismiss**: Close after 5-10 seconds

### Form Components
- **Text Input**: Single-line text fields
- **Text Area**: Multi-line text fields
- **Dropdown/Select**: Dropdown options with search
- **Multi-select**: Select multiple options
- **Checkbox**: Boolean options
- **Radio Button**: Single selection from options
- **Date Picker**: Calendar date selection (Nepali calendar support)
- **Time Picker**: Hour and minute selection
- **File Upload**: Drag & drop or file picker
- **Rich Text Editor**: WYSIWYG editing for content
- **Autocomplete**: Search and select from suggestions
- **Validation**: Real-time validation with error messages
- **Required Field**: Mark with asterisk or visual indicator

### Data Tables
- **Sortable Columns**: Click header to sort ascending/descending
- **Filterable**: Filter by values
- **Searchable**: Search within table
- **Pagination**: Navigate pages
- **Per Page**: Select number of items per page
- **Row Selection**: Checkbox for multi-select
- **Responsive**: Horizontal scroll on small screens
- **Striped Rows**: Alternate row colors
- **Row Hover**: Highlight on mouseover
- **Empty State**: Message when no data
- **Loading State**: Skeleton loaders while loading

### Charts & Visualizations
- **Line Charts**: Trends over time
- **Bar Charts**: Compare categories
- **Pie Charts**: Show proportions
- **Area Charts**: Filled line charts
- **Interactive**: Hover for values, zoom, pan
- **Responsive**: Adjust for screen size
- **Legend**: Color coding explanation
- **Export**: Download chart as image

---

## 🎨 Design Guidelines Summary

### Color Usage
- **Red (#FF0037)**: Primary action, important elements
- **Blue**: Secondary elements, information
- **Green**: Success, positive indicators
- **Orange**: Warnings, attention needed
- **Red**: Errors, destructive actions
- **Gray**: Disabled, secondary text
- **Dark Mode**: Gray-800 to Gray-900 backgrounds

### Typography
- **Headings**: Bold, larger font size, hierarchy
- **Body Text**: Regular weight, readable size
- **Bilingual**: Support EN/NE text with ligature support
- **Nepali Font**: Use Unicode-compatible fonts

### Spacing & Layout
- **Padding**: 16px, 24px, 32px standard values
- **Margin**: 8px, 16px, 24px standard values
- **Line Height**: 1.5 for body, 1.2 for headings
- **Max Width**: 1280px for content area

### Responsive Design
- **Mobile First**: Design for mobile then expand
- **Breakpoints**: 640px (tablet), 1024px (desktop), 1280px (large)
- **Touch Targets**: 44px minimum for buttons/interactive
- **Flexible Layout**: Flex/Grid for responsive
- **Hamburger Menu**: Mobile navigation toggle

### Accessibility
- **Contrast**: WCAG AA minimum contrast ratios
- **Focus States**: Clear focus indicators for keyboard navigation
- **ARIA Labels**: Screen reader support
- **Alt Text**: For all images
- **Keyboard Navigation**: All functions accessible via keyboard

### Performance (UI/UX Impact)
- **Lazy Loading**: Load images on demand
- **Skeleton Loaders**: Show while loading
- **Infinite Scroll or Pagination**: Handle large lists
- **Debounced Search**: Optimize search input
- **Optimistic Updates**: Immediate UI feedback

### Print Friendly
- **Print Stylesheet**: Hide sidebars, header buttons
- **Color Adjust**: Print-safe colors
- **Page Break**: Proper page breaks for reports
- **Readable Fonts**: Serif for body in print

---

# 📝 Summary Table of All Pages

| Page Category | Page Name | Purpose | Key Features |
|---|---|---|---|
| **Public** | Landing | Homepage | Hero carousel, faculty, programs, news, CTA |
| | Gallery | Event photos | Category filters, lightbox, download |
| | Faculty | Teacher directory | Search, filter by subject, profile view |
| | Subjects | Course listing | Filter by semester, view details |
| | Notices | Announcements | Category/date filters, search |
| **Auth** | Login | User authentication | Email/password, remember me, demo creds |
| | Register | Account creation | Role selection, role-specific fields |
| | Forgot Password | Password reset | Email verification, reset link |
| | Reset Password | Set new password | Password strength indicator |
| | Verify Email | Email confirmation | Verification link, resend option |
| | Confirm Password | Security confirmation | Re-enter password for sensitive actions |
| **Admin** | Dashboard | Admin overview | Stats, charts, recent activities, quick actions |
| | Students | Student management | CRUD, bulk ops, filters, export/print |
| | Student Detail | Student profile | Tabs for personal, academic, attendance, marks |
| | Student Create/Edit | Add/modify student | Form with validation, role-specific fields |
| | Teachers | Teacher management | CRUD, subject assignment, export/print |
| | Parents | Parent management | CRUD, link to students, bulk ops |
| | Attendance | Mark attendance | Date/subject selection, table entry, reports |
| | Exams | Exam management | Create, schedule, upload marks, track status |
| | Marks | Manage marks | Search, view, dynamic entry, statistics |
| | Marksheet | Generate marksheet | Search student, view/print/export transcript |
| | Timetable | Class schedule | Visual grid, conflict detection, print/export |
| | Electives | Course selection | Approve/reject requests, bulk assign, enroll |
| | Courses | Subject management | CRUD, teacher assignment, course details |
| | Notice Board | Announcements | Create, schedule, target audience, bilingual |
| | Gallery | Event photos | Upload, organize by category, manage access |
| | Study Materials | Educational resources | Upload, organize, track downloads, bulk ops |
| | Audit Logs | Activity tracking | Filter by user/action, export, timeline view |
| | Department | Settings | Logo, contact, location, leadership, landing config |
| **Teacher** | Dashboard | Main interface | Welcome, stats, today's schedule, quick actions |
| | Subjects | My courses | List subjects, view details, quick links |
| | Students | Class roster | Search, filter, view details, send messages |
| | Attendance | Mark attendance | Date/subject selection, table entry, reports |
| | Marks | Enter marks | Selection, marks entry, import/export, history |
| | Marksheets | Generate marksheet | Search, view/print/export student transcript |
| | Study Materials | Upload resources | Upload, organize, manage access, track downloads |
| | Notices | Post announcements | Create bilingual notices, schedule, target students |
| | Exams | Manage exams | View assigned, upload marks, track status |
| | Timetable | View schedule | Grid view, day view, print schedule |
| | Reports | Performance analytics | Attendance, grades, custom reports, export |
| | Profile | Account settings | Edit info, update qualifications, change password |
| **Student** | Dashboard | Main interface | Welcome, stats, courses, progress, announcements |
| | Courses | Enrolled subjects | List courses, view details, access materials |
| | Attendance | Track attendance | Overall %, by subject, trends, status |
| | Marks | View grades | Semester marks, trends, grade scale, export |
| | Timetable | Class schedule | Week/day view, print, download |
| | Profile | Account settings | Edit info, update contact, change password |
| **Parent** | Dashboard | Main interface | Welcome, child selection, stats, alerts |
| | Children | Manage links | View children info, track performance |
| | Attendance | Monitor attendance | Overall %, by subject, trends, alerts |
| | Results | View marks | Semester results, trends, performance, export |
| | Courses | Subjects enrolled | List courses, view course details |
| | Events | Event gallery | Browse events, view photos, download |
| | Notices | Announcements | Read notices, filter, archive, search |
| | Communication | Teacher messaging | Send/receive messages, thread view, search |
| | Print/Export | Reports | Generate reports, print/email/export |
| | Profile | Account settings | Edit contact info, change password |

---

**Total Pages: 56+ distinct pages**

**Design System**:
- Responsive design (Mobile-first approach)
- Dark mode support
- Bilingual (English/Nepali)
- Accessible (WCAG AA compliant)
- Consistent component library
- Print-friendly layouts

---

*This document is intended for UI/UX designers to understand the application structure, user flows, and design requirements for IT-DMS.*
