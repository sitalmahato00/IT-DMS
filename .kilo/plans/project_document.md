# IT Department Management System (IT-DMS)
## Minor Project Documentation

---

## Chapter 1: Introduction

### 1.1 Project Overview
The IT Department Management System (IT-DMS) is a comprehensive web-based academic management system designed specifically for educational institutions, particularly for managing IT department operations. The system provides a complete solution for managing students, teachers, academic records, attendance, examinations, and communication between all stakeholders including parents.

Built using Laravel (PHP framework), IT-DMS offers a modern, secure, and scalable platform with role-based access control ensuring that each user type has appropriate access to the system features.

### 1.2 Project Type
- **Application Type**: Web-based Academic Management System
- **Framework**: Laravel (PHP)
- **Database**: MySQL
- **Architecture**: MVC (Model-View-Controller)
- **Deployment**: Ready for production deployment

### 1.3 Objectives
1. **Centralized Data Management**: Provide a centralized platform for managing all academic data including student information, teacher profiles, subject allocations, and academic records.
2. **Efficient Attendance Tracking**: Enable real-time subject-wise attendance tracking with automated reports and low attendance alerts.
3. **Streamlined Examination Process**: Simplify exam creation, marks entry, and result generation with automatic GPA calculation.
4. **Improved Communication**: Facilitate effective communication between administrators, teachers, students, and parents through bilingual notice boards.
5. **Parent Engagement**: Enable parents to monitor their children's academic progress, attendance, and results through a dedicated parent portal.
6. **Resource Management**: Provide a platform for managing study materials and organizing department events through a gallery system.

### 1.4 Scope
#### Inclusions
- User management with role-based access control (Admin, Teacher, Student, Parent)
- Student profile management with comprehensive academic tracking
- Teacher profile and subject allocation management
- Subject management with credit hours and semester mapping
- Flexible timetable slot management
- Elective course selection and enrollment tracking
- Exam creation and scheduling
- Multiple marks entry (Internal/Assessment/Final)
- Automatic marksheet generation and GPA calculation
- Subject-wise attendance tracking
- Real-time attendance reports and statistics
- Bilingual notice board (English/Nepali)
- Study materials upload and download
- Department gallery and event management
- Parent portal for student monitoring

#### Exclusions
- Online examination/quiz system
- Live virtual classroom integration
- Fee management and payment processing
- Library management
- Transport management
- Hostel management
- SMS/Email notifications

---

## Chapter 2: Literature Survey

### 2.1 Background
Traditional academic management in educational institutions involves Manual processes for maintaining records, tracking attendance, managing examinations, and communicating with stakeholders. These manual systems are prone to errors, time-consuming, and often result in inefficient workflow.

### 2.2 Existing Systems Analysis
| System | Features | Limitations |
|--------|----------|-------------|
| ERP Systems | Comprehensive | Expensive, complex, not department-specific |
| Moodle | Course management | Learning-focused, not comprehensive |
| Custom Excel Sheets | Flexible | Error-prone, no security, manual backup |
| Proprietary College Systems | Department-specific | Vendor lock-in, limited customization |

### 2.3 Proposed Solution
IT-DMS addresses these limitations by providing:
- Department-specific functionality
- Open-source flexibility
- Bilingual support (English/Nepali)
- Role-based access control
- Real-time data access
- Automated reports and analytics

### 2.4 Technology Stack
#### Frontend
- **Template Engine**: Blade (Laravel)
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Vanilla JS with Alpine.js
- **Icons**: Heroicons

#### Backend
- **Framework**: Laravel 10.x
- **PHP Version**: 8.1+
- **Database**: MySQL 8.0+

#### Development Tools
- **Version Control**: Git
- **Local Server**: Laravel Sail (Docker)
- **Authentication**: Laravel Breeze/Jetstream

---

## Chapter 3: System Analysis

### 3.1 User Roles and Actors

#### 3.1.1 Administrator (Admin)
- Complete system access
- Manage all users (teachers, students, parents)
- Manage subjects, semesters, timetable
- Create and manage exams
- View all reports and analytics
- Manage department settings

#### 3.1.2 Teacher
- Manage assigned subjects
- Take attendance for assigned classes
- Enter marks for assigned students
- Create and upload study materials
- Post notices
- View reports for assigned subjects

#### 3.1.3 Student
- View personal profile and academic records
- View timetable and schedule
- View attendance records
- View exam results and marks
- Download study materials
- View notices and announcements
- Select elective courses

#### 3.1.4 Parent
- Monitor child's academic progress
- View attendance records
- View exam results
- Receive notifications
- Communicate with teachers

### 3.2 Functional Requirements
| ID | Requirement | Priority |
|----|-------------|----------|
| FR-01 | User Management | High |
| FR-02 | Student Management | High |
| FR-03 | Teacher Management | High |
| FR-04 | Subject Management | High |
| FR-05 | Timetable Management | High |
| FR-06 | Attendance Management | High |
| FR-07 | Examination Management | High |
| FR-08 | Notice Management | Medium |
| FR-09 | Study Materials | Medium |
| FR-10 | Elective Course Management | Medium |

### 3.3 Use Case Diagram
```
┌────────────────────────────────────┐
│           ADMIN                   │
└──────────────┬─────────────────────┘
               │
┌──────────────┼─────────────────────┐
│              │                     │
▼              ▼                     ▼
┌────────┐ ┌────────┐ ┌────────┐
│Manage  │ │Manage  │ │Generate│
│Users   │ │Academic│ │Reports │
│        │ │Data    │ │        │
└────────┘ └────────┘ └────────┘
        └──────────┬───────────┘
                   │
        ┌─────────┼─────────┐
        ▼         ▼         ▼
    ┌───────┐ ┌───────┐ ┌──────┐
    │TEACHER│ │STUDENT│ │PARENT│
    └───────┘ └───────┘ └──────┘
```

### 3.4 DFD Diagrams

#### Level 0 DFD (Context Diagram)
```
[Admin] ──┐         ┌──────────────────┐
          │         │                  │
[Teacher]─┼────────►│    IT-DMS        │◄────[Database]
          │         │    SYSTEM        │
[Student]─┤         │                  │
          │         └──────────────────┘
[Parent]──┘              │
                  ┌─────┴─────┐
                  │ Payment   │
                  │ Gateway  │
                  └──────────┘
```

#### Level 1 DFD (Decomposed)
Processes: P1-User, P2-Academic, P3-Attendance, P4-Examination, P5-Communication
Data Stores: D1-Users, D2-Students, D3-Teachers, D4-Subjects, D5-Semesters, D6-Exams, D7-Marks, D8-Attendance, D9-Notices

---

## Chapter 4: System Design

### 4.1 Database Schema (Main Tables)

#### 4.1.1 Users Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | User ID |
| name | varchar(255) | NOT NULL | Full name |
| email | varchar(255) | UNIQUE, NOT NULL | Email |
| password | varchar(255) | NOT NULL | Hashed password |
| role | enum | NOT NULL | admin/teacher/student/parent |
| created_at | timestamp | DEFAULT | Creation date |

#### 4.1.2 Students Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Student ID |
| user_id | bigint | FK | User reference |
| student_id | varchar(50) | UNIQUE | College ID |
| batch | varchar(10) | NOT NULL | Batch year |
| semester_id | bigint | FK | Current semester |
| parent_id | bigint | FK | Linked parent |

#### 4.1.3 Teachers Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Teacher ID |
| user_id | bigint | FK | User reference |
| employee_id | varchar(50) | UNIQUE | Employee ID |
| designation | varchar(100) | NULL | Job title |
| specialization | varchar(255) | NULL | Subject expertise |

#### 4.1.4 Subjects Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Subject ID |
| name | varchar(255) | NOT NULL | Subject name |
| code | varchar(20) | UNIQUE | Subject code |
| credit_hours | int | NOT NULL | Credit hours |
| semester_id | bigint | FK | Semester |
| is_elective | boolean | DEFAULT FALSE | Elective status |

#### 4.1.5 Semesters Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Semester ID |
| name | varchar(100) | NOT NULL | Semester name |
| semester_number | int | NOT NULL | Order number |
| academic_year | varchar(10) | NOT NULL | Academic year |
| is_active | boolean | DEFAULT TRUE | Active status |

#### 4.1.6 Attendance Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Attendance ID |
| student_id | bigint | FK | Student |
| subject_id | bigint | FK | Subject |
| teacher_id | bigint | FK | Teacher |
| date | date | NOT NULL | Attendance date |
| status | enum | NOT NULL | present/absent |

#### 4.1.7 Exams Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Exam ID |
| name | varchar(255) | NOT NULL | Exam name |
| semester_id | bigint | FK | Semester |
| exam_type | enum | NOT NULL | internal/assessment/final |
| start_date | date | NOT NULL | Exam start |
| end_date | date | NOT NULL | Exam end |

#### 4.1.8 Marks Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Mark ID |
| student_id | bigint | FK | Student |
| subject_id | bigint | FK | Subject |
| exam_id | bigint | FK | Exam |
| marks_obtained | decimal(5,2) | NOT NULL | Marks scored |
| total_marks | decimal(5,2) | NOT NULL | Maximum marks |
| grade | varchar(2) | NULL | Letter grade |
| gpa_points | decimal(3,2) | NULL | GPA points |

#### 4.1.9 Notices Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Notice ID |
| title | varchar(255) | NOT NULL | Title |
| content | text | NOT NULL | Content |
| title_np | varchar(255) | NULL | Nepali title |
| content_np | text | NULL | Nepali content |
| posted_by | bigint | FK | Posted by |
| target_role | enum | NOT NULL | Target audience |

#### 4.1.10 Study Materials Table
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | bigint | PK | Material ID |
| subject_id | bigint | FK | Subject |
| title | varchar(255) | NOT NULL | Title |
| file_path | varchar(255) | NOT NULL | File location |
| file_type | varchar(50) | NOT NULL | File type |
| uploaded_by | bigint | FK | Uploader |

### 4.2 Entity Relationship Diagram
```
Users (1:1)──(1:1) Students/Teachers/Parents
         │
         └──────────┬─────────────┐
                    │
                    ▼
          Semesters (1:M)──(M:1) Subjects
                              │
              ┌───────────────┼───────────────┐
              ▼               ▼               ▼
         Timetable       Marks          Attendance
           Slots         (M:1)            (M:1)
                              └───────┬────────┘
                                     ▼
                                Notices
```

### 4.3 System Architecture (MVC)
```
Client Request → Routes → Controller → Model → Database
                  ↑                          ↓
              Views ← (Response) ← Model ← Controller

---

## Chapter 5: Implementation

### 5.1 Key Modules
- **Authentication**: Laravel Breeze, role-based middleware
- **Dashboard**: Role-specific dashboards with quick statistics
- **Academic**: Student/Teacher CRUD, Subject management
- **Attendance**: Date-wise marking, bulk attendance, reports
- **Examination**: Exam scheduling, marks entry, GPA calculation
- **Notices**: Bilingual support, category management
- **Resources**: File upload with validation, gallery management

### 5.2 Security Features
- CSRF Protection
- XSS Prevention
- SQL Injection Prevention
- Secure Password Storage (bcrypt)
- Role-based Access Control

### 5.3 GPA Calculation Formula
GPA = Σ(GPA Points × Credit Hours) / Σ(Credit Hours)

---

## Chapter 6: Testing

### 6.1 Test Cases
| Test ID | Feature | Test Case | Expected | Status |
|---------|---------|-----------|----------|----------|--------|
| TC-01 | Login | Valid credentials | Success | PASS |
| TC-02 | Login | Invalid credentials | Error | PASS |
| TC-03 | Student Add | Valid data | Created | PASS |
| TC-04 | Attendance | Mark present | Saved | PASS |
| TC-05 | Marks | Enter valid marks | Saved | PASS |
| TC-06 | Notice | Create notice | Published | PASS |
| TC-07 | Export | Export to CSV | Downloaded | PASS |

---

## Chapter 7: Conclusion

### 7.1 Summary
IT-DMS provides a comprehensive solution for managing IT department academic operations with role-based access, bilingual support, and complete workflow management.

### 7.2 Key Achievements
1. Centralized academic data management
2. Real-time attendance tracking
3. Automated examination and marks management
4. Effective communication channel
5. Parent engagement through dedicated portal

### 7.3 Future Scope
- Mobile application development
- Online examination system
- Live classroom integration
- Fee management module
- SMS/Email notifications
- Library management integration

### 7.4 Limitations
- No real-time classroom feature
- Basic notification system
- No mobile application
- No integrated payment system

---

## References

1. Laravel Documentation. https://laravel.com/docs
2. Tailwind CSS Documentation. https://tailwindcss.com/docs
3. MySQL Documentation. https://dev.mysql.com/doc
4. PHP Documentation. https://www.php.net/docs.php

---

*Project Documentation for IT-DMS*
*Minor Project Submission*
*Academic Year: 2025-2026*
```