# IT Department Management System (IT-DMS)

A comprehensive Laravel-based academic management system for the IT Department, providing features for departmental activities, student management, attendance tracking, exam management, notices, study materials, and more.

## Features

- **User Management**: Multi-role authentication (Admin, Teacher, Student, Parent)
- **Student Management**: Complete student profiles, academic records, and performance tracking
- **Teacher Management**: Teacher profiles, subject assignments, and teaching schedules
- **Course & Subject Management**: Course creation, subject assignment, and semester-wise organization
- **Attendance Management**: Track student attendance with both AD and BS date support
- **Exam Management**: Create and manage exams with bilingual support (English/Nepali)
- **Mark Management**: Record and calculate exam marks with automatic grade calculation
- **Notice Board**: Publish notices with bilingual content support
- **Gallery**: Manage department photo gallery
- **Study Materials**: Upload and distribute study materials
- **Notifications**: Real-time notifications for results and exam updates

## Technology Stack

- **Framework**: Laravel 10.x
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze/Jetstream
- **Date Support**: Dual calendar (English AD & Nepali BS)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd IT-DMS
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Configure environment:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Configure database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=it_dms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Run migrations and seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```

7. Install NPM dependencies and build frontend:
   ```bash
   npm install
   npm run build
   ```

8. Start development server:
   ```bash
   php artisan serve
   ```

## Dependencies

### PHP Packages
- **laravel/framework** (^12.0) - Core Laravel framework
- **anuzpandey/laravel-nepali-date** - Nepali Date (B.S.) support for Laravel
- **laravel/tinker** (^2.10.1) - REPL for Laravel
- **laravel/breeze** (^2.3) - Authentication scaffolding

### Frontend Dependencies
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Next generation frontend tooling
- **Alpine.js** - Lightweight JavaScript framework

### Key Features Using External Packages
- **Nepali Date Support**: Uses `anuzpandey/laravel-nepali-date` for dual calendar (AD/BS) support
- **Authentication**: Laravel Breeze for authentication
- **Testing**: PHPUnit for testing

## Available Routes

### Public Routes
| Route | Name | Description |
|-------|------|-------------|
| `/` | `home` | Home/landing page |
| `/notices/fetch` | `notices.fetch` | Fetch notices via AJAX |
| `/notices/{id}` | `notices.show` | View single notice |

### Authentication Routes
| Route | Name | Description |
|-------|------|-------------|
| `/login` | `login` | User login |
| `/register` | `register` | User registration |
| `/logout` | `logout` | User logout |

### Dashboard Routes (All require authentication)
| Route | Name | View File | Description |
|-------|------|-----------|-------------|
| `/dashboard` | `dashboard` | `admin.dashboard` | Admin dashboard |
| `/student` | `student.dashboard` | `student.studentdashboard` | Student dashboard |
| `/parent` | `parent.dashboard` | `parent.parentdashboard` | Parent dashboard |
| `/teacher` | `teacher.dashboard` | `teacher.teacherdashboard` | Teacher/Faculty dashboard |

### Profile Routes
| Route | Name | Description |
|-------|------|-------------|
| `/profile` | `profile.edit` | Edit user profile |
| `/profile/show` | `profile.show` | View user profile |
| `/profile` | `profile.update` | Update profile (PATCH) |
| `/profile` | `profile.destroy` | Delete profile (DELETE) |

### Admin Routes (Prefix: `/admin`, Name prefix: `admin.`)
| Route | Name | Description |
|-------|------|-------------|
| `/admin/dashboard` | `admin.dashboard` | Admin dashboard |
| `/admin/students` | `admin.students` | Student management |
| `/admin/teachers` | `admin.teachers` | Teacher management |
| `/admin/parents` | `admin.parents` | Parent management |
| `/admin/attendance` | `admin.attendance` | Attendance management |
| `/admin/exam` | `admin.exam` | Exam management |
| `/admin/courses` | `admin.courses` | Course management |
| `/admin/reports` | `admin.reports` | Reports and analytics |
| `/admin/notifications` | `admin.notifications` | Notifications |
| `/admin/notice-board` | `admin.notice-board` | Notice board |
| `/admin/gallery` | `admin.gallery` | Gallery management |
| `/admin/study-material` | `admin.study-material` | Study materials |
| `/admin/settings` | `admin.settings` | Admin settings |

### Language Routes
| Route | Name | Description |
|-------|------|-------------|
| `/locale` | `language.switch` | Switch application language |

## Sample Login Credentials

**All accounts use the same password: `password123`**

| Role    | Email                           | Password   | Dashboard Route |
|---------|----------------------------------|------------|-----------------|
| Admin   | admin@itdms.local               | password123 | /dashboard      |
| Admin   | sitalmahato077@gmail.com        | password123 | /dashboard      |
| Teacher | (from factory - see below)       | password   | /teacher        |
| Parent  | (from factory - see below)      | password   | /parent         |
| Student | (from factory - see below)      | password   | /student        |

### Default Admin Accounts
| Email                        | Password   | Role  |
|------------------------------|------------|-------|
| admin@itdms.local           | password123| Admin |
| sitalmahato077@gmail.com    | password123| Admin |
| itstudentsital@gmail.com    | password123| Admin |

### How to Find Teacher, Parent, and Student Accounts

Run the following command to see all registered users:
```bash
php artisan tinker
User::all(['name', 'email', 'role'])->each(function($user) { echo $user->name . ' - ' . $user->email . ' (' . $user->role . ')' . PHP_EOL; });
```

Or check the database directly. Users are created by the factory with random emails but with role field set accordingly.

## Project Structure

```
IT-DMS/
├── app/
│   ├── Console/Commands/     # Custom Artisan commands
│   ├── Helpers/            # Helper functions
│   ├── Http/Controllers/   # Controllers (Admin, Auth, etc.)
│   ├── Models/             # Eloquent models
│   ├── Notifications/      # Notification classes
│   ├── Observers/          # Model observers
│   ├── Providers/          # Service providers
│   ├── Services/           # Business logic services
│   └── Traits/             # Reusable traits
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/            # Database seeders
│   └── factories/          # Model factories
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   ├── lang/               # Language files (en, ne)
│   └── views/              # Blade templates
├── routes/                 # Route definitions
└── tests/                  # Test cases
```

## Available Seeders

- `UserSeeder` - Creates users for all roles
- `StudentSeeder` - Creates student profiles
- `TeacherSeeder` - Creates teacher profiles
- `ParentSeeder` - Creates parent/guardian profiles
- `SubjectSeeder` - Creates subjects
- `CourseSeeder` - Creates courses
- `ExamSeeder` - Creates exam records
- `ExamMarkSeeder` - Creates exam marks with grades
- `AttendanceSeeder` - Creates attendance records
- `NoticeSeeder` - Creates sample notices
- `GallerySeeder` - Creates gallery entries
- `StudyMaterialSeeder` - Creates study materials

Run a specific seeder:
```bash
php artisan db:seed --class=ExamMarkSeeder
```

## Notification System

The system implements a comprehensive notification system for account creation and important events. When admins add new students, teachers, or parents, automated emails are sent with temporary login credentials.

### Account Creation Workflow

**For Students:**
1. Admin creates a new student account in the system
2. System generates a temporary random password
3. `StudentAccountNotification` is sent to student's email containing:
   - Login email (assigned email)
   - Temporary password
   - Link to login page
   - Instructions to change password on first login
4. Student receives email and logs in with temporary credentials
5. Student must change password on first login (enforced by system)
6. Access to student dashboard becomes available

**For Teachers:**
1. Admin creates a new teacher account
2. System generates a temporary random password
3. `TeacherAccountNotification` is sent to teacher's email containing:
   - Login credentials
   - Link to login page
   - Information about faculty features available
4. Teacher receives email and logs in
5. Teacher changes password on first login
6. Access to teacher dashboard and features becomes available

**For Parents:**
1. Admin creates a new parent/guardian account
2. System generates a temporary random password
3. `ParentAccountNotification` is sent to parent's email containing:
   - Login credentials
   - Link to login page
   - Information about parent access features
4. Parent receives email and logs in
5. Parent changes password on first login
6. Access to parent dashboard and child's records becomes available

### Email Configuration

The system uses Gmail SMTP for sending notifications. Ensure the following environment variables are set in `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="IT-DMS"
```

**Note:** Use Gmail app password (not your regular Gmail password) for `MAIL_PASSWORD`. Generate one from your Google Account security settings.

### Notification Types

#### StudentAccountNotification
- **Trigger:** When a new student is added by admin
- **Recipients:** New student
- **Channels:** Email, Database
- **Contains:** 
  - Login credentials
  - Security instructions
  - Student portal information

#### TeacherAccountNotification
- **Trigger:** When a new teacher is added by admin
- **Recipients:** New teacher
- **Channels:** Email, Database
- **Contains:**
  - Login credentials
  - Faculty features overview
  - Security instructions

#### ParentAccountNotification
- **Trigger:** When a new parent is added by admin
- **Recipients:** New parent/guardian
- **Channels:** Email, Database
- **Contains:**
  - Login credentials
  - Parent portal features information
  - Security instructions

#### PasswordResetNotification
- **Trigger:** When a user requests forgot password
- **Recipients:** User requesting reset
- **Channels:** Email
- **Contains:** Password reset link with token

#### ExamNotification
- **Trigger:** Before exam dates
- **Recipients:** Students and teachers
- **Channels:** Database
- **Contains:** Exam details and schedule

#### ResultNotification
- **Trigger:** When exam marks are released
- **Recipients:** Students
- **Channels:** Database
- **Contains:** Result information and grades

#### AttendanceNotification
- **Trigger:** Periodic attendance summaries
- **Recipients:** Students and parents
- **Channels:** Database
- **Contains:** Attendance statistics

