# IT Department Management System (IT-DMS)

A comprehensive Laravel-based academic management system for the IT Department with role-based access control, bilingual support (English/Nepali), and complete academic workflow management.

## 🎯 System Overview

**Roles**: Admin, Teacher, Student, Parent  
**Status**: Production Ready & Extensible  
**License**: MIT

### Core Features

#### 📚 Academic Management
- ✅ Student profile management with attendance tracking
- ✅ Teacher profile & subject allocation
- ✅ Subject management with credit hours and semester mapping
- ✅ Flexible timetable slot management
- ✅ Elective course selection and enrollment tracking

#### 📊 Exam & Assessment System
- ✅ Exam creation and scheduling
- ✅ Multiple marks entry (Internal/Assessment/Final)
- ✅ Automatic marksheet generation and GPA calculation
- ✅ Exam marks tracking with result notifications
- ✅ Export exam results to Excel/CSV

#### 📝 Attendance & Performance
- ✅ Subject-wise attendance tracking
- ✅ Real-time attendance reports and statistics
- ✅ Low attendance alerts and notifications
- ✅ Monthly/semester attendance summaries

#### 📢 Communication & Notices
- ✅ Bilingual notice board (English/Nepali)
- ✅ Department announcements and alerts
- ✅ Role-specific notifications
- ✅ Multilingual content management

#### 📚 Learning Resources
- ✅ Study materials upload/download
- ✅ Organized by subject and semester
- ✅ File type restrictions and validation
- ✅ Department gallery and event management

#### 👨‍👩‍👧 Parent Portal
- ✅ Student performance tracking
- ✅ Attendance monitoring
- ✅ Result notifications
- ✅ Communication with teachers

#### 📈 Reports & Analytics
- ✅ Attendance reports
- ✅ Performance analytics
- ✅ Semester results
- ✅ Custom filter and export options (Excel/PDF/CSV)

#### 🔒 Administrative Features
- ✅ Audit logs for all system activities
- ✅ Role-based access control (RBAC)
- ✅ User management and permissions
- ✅ System settings and configuration
- ✅ Backup and restore functions

#### 🔔 Notifications
- ✅ Email notifications
- ✅ In-app notifications
- ✅ Attendance alerts
- ✅ Exam notifications
- ✅ Result announcements

---

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 11.0+
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **API**: RESTful API with Laravel Sanctum (Token-based authentication)

### Frontend
- **CSS Framework**: Tailwind CSS 3.1+
- **JavaScript Framework**: Alpine.js 3.4+
- **Charts**: Chart.js 4.5+ (for analytics & reports)
- **Task Queue**: Laravel Queue system

### Additional Libraries & Tools
- **PDF Generation**: barryvdh/laravel-dompdf (for marksheet printing)
- **Date Handling**: anuzpandey/laravel-nepali-date (Nepali calendar support)
- **Date Picker**: nepali-date-picker 2.0+ (Nepali date selection UI)
- **HTTP Client**: Guzzle HTTP
- **Bilingual Support**: Multi-locale support for EN/NE

### Development Tools
- **Build Tool**: Vite 7.0+
- **Package Management**: Composer, npm
- **Testing**: PHPUnit, Mockery
- **Code Quality**: Laravel Pint
- **Monitoring**: Laravel Pail

---

## 📋 Prerequisites

Before installation, ensure you have:

- **PHP 8.2+** with extensions: `BCMath`, `Ctype`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`
- **Composer** (2.0+) - https://getcomposer.org
- **Node.js** (16.0+) & npm - https://nodejs.org
- **MySQL** (8.0+) or similar database
- **Git** - https://git-scm.com

### PHP Extensions Check
```bash
php -m | findstr /I "bcmath ctype json mbstring openssl pdo tokenizer xml"
```

---

## 🚀 Installation Guide

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/IT-DMS.git
cd IT-DMS
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
```

Edit `.env` and configure:
```env
APP_NAME="IT Department Management System"
APP_ENV=local
APP_DEBUG=true
APP_KEY=
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_dms
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration (Optional for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=admin@itdms.local
MAIL_FROM_NAME="IT-DMS"

# Queue Configuration
QUEUE_CONNECTION=database
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Create Database
```bash
# Create database in MySQL
mysql -u root -p
CREATE DATABASE it_dms;
EXIT;
```

### Step 6: Run Migrations & Seeders
```bash
# Run all migrations
php artisan migrate

# Run seeders (creates initial demo data)
php artisan migrate:fresh --seed
```

### Step 7: Install Frontend Dependencies
```bash
npm install
```

### Step 8: Build Frontend Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### Step 9: Start Development Server
```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

---

## 👤 Test Credentials

After running the seeders, use these credentials to test different roles:

### Admin Login
- **Email**: `sitalmahato077@gmail.com`
- **Password**: `password123`
- **Access**: Full system access, user management, reports

### Teacher Login
- **Email**: `hellogoog94@gmail.com`
- **Password**: `password123`
- **Access**: Student management, attendance, marks entry, study materials

### Student Login
- **Email**: `itstudentsital@gmail.com`
- **Password**: `password123`
- **Access**: View results, attendance, subjects, download materials

### Parent Login
- **Email**: `sitalmahato00@gmail.com`
- **Password**: `password123`
- **Access**: Child performance tracking, attendance, notifications

---

## ⚙️ Post-Installation Setup

### 1. Queue Processing (Optional but Recommended)
```bash
# For background job processing
php artisan queue:listen
```

### 2. File Storage Setup
```bash
# Create storage symlink for public access
php artisan storage:link
```

### 3. Cache Configuration
```bash
# Clear and optimize cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Backup Database
```bash
# Create initial backup
mysqldump -u root -p it_dms > backup_initial.sql
```

---

## 📂 Project Structure

```
IT-DMS/
├── app/
│   ├── Console/          # Console commands
│   ├── Http/
│   │   ├── Controllers/  # Main application controllers
│   │   ├── Middleware/   # Custom middleware (auth, roles, etc.)
│   │   └── Requests/     # Form request validation
│   ├── Models/           # Eloquent models (24 models)
│   ├── Notifications/    # Email notifications
│   ├── Services/         # Business logic services
│   ├── Traits/           # Reusable traits
│   └── Exports/          # Excel export classes
├── database/
│   ├── migrations/       # Database schema migrations
│   ├── seeders/         # Database seeders
│   └── factories/        # Model factories for testing
├── resources/
│   ├── views/           # Blade templates (HTML views)
│   ├── js/              # Alpine.js and custom JavaScript
│   ├── css/             # Tailwind CSS stylesheet
│   └── lang/            # Language files (EN/NE)
├── routes/
│   ├── web.php          # Web application routes
│   ├── api.php          # API routes
│   └── auth.php         # Authentication routes
├── config/              # Configuration files
├── storage/             # File storage (uploads, logs)
├── public/              # Public accessible files
├── vendor/              # Composer dependencies
├── node_modules/        # NPM dependencies
├── artisan              # Laravel CLI tool
├── composer.json        # PHP dependencies
├── package.json         # JS dependencies
├── vite.config.js       # Vite configuration
├── tailwind.config.js   # Tailwind CSS configuration
└── .env                 # Environment configuration
```

---

## � Recent Updates & Improvements (March 2026)

### Database Migrations Standardization
✅ **All 27 migration files** now follow proper Laravel naming convention:
- **Format**: `YYYY_MM_DD_HHMMSS_description.php`
- **Consistency**: All migrations use base date `2024-01-01`
- **Sequential Ordering**: Files numbered `000001` through `000027` based on dependency order
- **Execution Order**: Ensures tables are created in correct dependency sequence

**Migration Execution Order:**
1. Core users (Users, Teachers, Parents)
2. Academic core (Students, Subjects, Semesters, Colleges)
3. Relationships (Subject-Teacher mappings, Elective enrollments)
4. Schedules (Timetable slots)
5. Academic records (Attendance, Exams, Marks)
6. Content (Notices, Study materials, Gallery)
7. System (Audit logs, Laravel defaults)

**Benefits:**
- Prevents foreign key constraint violations
- Ensures clean database initialization
- Simplifies migration management and debugging
- Follows Laravel best practices

### Admin Interface Improvements
✅ **Removed duplicate "Academic Structure" menu item**
- Consolidated redundant pages in admin sidebar
- Single "Subjects" entry for course management
- Removed unused `$isAcademicStructure` conditional logic
- Cleaner navigation for non-technical users
- Improved maintainability of sidebar components

**Changed Routes:**
- ❌ Removed: `/admin/courses?view=structure` (duplicate)
- ✅ Kept: `/admin/courses` (primary subjects management)

---

## �📦 Key Dependencies

### Laravel Packages
| Package | Version | Purpose |
|---------|---------|---------|
| laravel/framework | ^12.0 | Core framework |
| laravel/sanctum | * | API authentication |
| laravel/tinker | ^2.10 | REPL for debugging |
| barryvdh/laravel-dompdf | * | PDF generation for marksheets |
| anuzpandey/laravel-nepali-date | * | Nepali date handling |
| guzzlehttp/guzzle | * | HTTP requests |

### Frontend Libraries
| Package | Version | Purpose |
|---------|---------|---------|
| alpinejs | ^3.4.2 | Lightweight JavaScript framework |
| tailwindcss | ^3.1.0 | Utility-first CSS framework |
| chart.js | ^4.5.1 | Charts and graphs |
| nepali-date-picker | ^2.0.2 | Nepali calendar date picker |
| axios | ^1.11.0 | HTTP client for AJAX |

---

## 🔑 Key Features: Detailed

### 1. **Multi-Role Dashboard**
- Customized dashboards for Admin, Teacher, Student, Parent
- Real-time statistics and quick actions
- Performance metrics and alerts

### 2. **Bilingual Support (English/Nepali)**
- Complete UI in English and Nepali
- Nepali calendar for date selection
- Automatic date conversion (AD to BS)
- Localized notifications and reports

### 3. **Attendance Management**
- Daily attendance marking by teachers
- Multiple attendance types (Present/Absent/Leave/Late)
- Automatic alerts for low attendance (<75%)
- Monthly and semester-wise reports

### 4. **Academic Management**
- Subject allocation to teachers
- Flexible elective course selection
- Semester-based course structure
- Credit hour calculation

### 5. **Examination System**
- Multiple exam types (Midterm, Final, Assessment)
- Flexible marks entry (100, 50, or custom scale)
- Automatic result processing and GPA calculation
- Grade assignment based on scoring criteria

### 6. **Study Materials**
- File sharing between teachers and students
- Organized by subject and semester
- Download tracking and access logs
- Support for PDF, DOC, PPT, etc.

### 7. **Parent Portal**
- Track student attendance and performance
- View exam results and marks
- Receive notifications about student progress
- Communication with teachers

### 8. **Reports & Exports**
- Attendance reports (PDF/Excel/CSV)
- Marksheet generation and PDF export
- Performance analysis charts
- Semester-wise result compilation

### 9. **Notifications**
- Email-based notifications
- In-app notification system
- Role-specific alert rules
- Customizable notification preferences

### 10. **Audit Logging**
- Complete activity tracking
- User action logging
- Data modification history
- Security audit trails

---

## 🔐 Security Features

- **Password Hashing**: Bcrypt encryption
- **CSRF Protection**: Token-based protection
- **Role-Based Access Control (RBAC)**: Fine-grained permissions
- **SQL Injection Protection**: Parameterized queries
- **XSS Prevention**: Output escaping
- **Rate Limiting**: API rate limiting
- **Secure Headers**: Content Security Policy
- **Audit Logs**: Complete activity tracking

---

## 🧪 Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage report
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/ExamTest.php
```

---

## 🐛 Troubleshooting

### Issue: "Class not found" errors
```bash
# Clear autoloader cache
composer dump-autoload

# Or with optimization
composer dump-autoload --optimize
```

### Issue: Migration errors
```bash
# Rollback and re-run
php artisan migrate:reset
php artisan migrate:fresh --seed
```

### Issue: npm build errors
```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Issue: Database connection errors
- Verify MySQL is running
- Check `.env` database credentials
- Ensure database exists: `CREATE DATABASE it_dms;`

### Issue: File upload errors
```bash
# Fix storage permissions
php artisan storage:link
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

## 📱 API Documentation

### Authentication
API uses Laravel Sanctum for token-based authentication:

```bash
# Login
POST /api/login
{
  "email": "user@example.com",
  "password": "password123"
}

# Logout
POST /api/logout
```

### Common Endpoints
```
GET    /api/students          # List all students
GET    /api/students/{id}     # Get student details
POST   /api/attendance        # Record attendance
GET    /api/marks/{student_id}  # Get student marks
GET    /api/notices           # Get notices
POST   /api/study-materials   # Upload study material
```

---

## 📝 Database Schema Overview

### Core Models (24 Total)
- **User**: System users with roles
- **Student**: Student profiles with enrollment details
- **Teacher**: Faculty information
- **StudentParent**: Parent/Guardian contact info
- **Department**: Department structure
- **Semester**: Semester configuration
- **Subject**: Course information
- **Course**: Course structure
- **Exam**: Examination records
- **Mark**: Student marks and grades
- **ExamMark**: Exam-specific marks
- **Attendance**: Attendance records
- **Notice**: System announcements
- **StudyMaterial**: Course materials
- **TimetableSlot**: Class schedule
- **UserDetail**: Extended user information
- **SubjectTeacher**: Teacher-subject mapping
- **ElectiveEnrollment**: Student elective selection
- **Gallery**: Event gallery management
- **AuditLog**: Activity audit trail
- **ErpSetting**: System configuration
- **BilingualNotice**: Multilingual notices
- **Course**: Course structure details
- **ParentModel**: Parent model (base model)

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📞 Support & Contact

- 📧 Email: admin@itdms.local
- 🐛 Report Issues: [GitHub Issues](https://github.com/yourusername/IT-DMS/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/yourusername/IT-DMS/discussions)

---

## 📄 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file for details.

---

## ✨ Version

**Current Version**: 1.1.0  
**Last Updated**: March 28, 2026  
**Status**: Production Ready

### Version History
- **v1.1.0** (March 28, 2026): Database migrations standardized, admin UI improvements, duplicate page removal
- **v1.0.0** (March 2026): Initial release with complete academic management system

*Maintained by IT Department Development Team*
