# Design Document: System Refactoring - Component-Based Architecture

## Overview

This design document outlines the technical architecture for refactoring the existing Laravel-based College ERP system into a fully component-based, modular, high-performance architecture. The refactoring will be performed **in-place** within the existing codebase, maintaining 100% backward compatibility while introducing new capabilities.

### Core Objectives

1. **Preserve All Existing Functionality**: Maintain all business logic, validation rules, RBAC, database schema, APIs, security mechanisms, and system behavior
2. **Introduce Component-Based Architecture**: Organize code into logical modules with clear separation of concerns
3. **Add Principal Role**: Create a new top-level administrative role with institution-wide access
4. **Scope HOD Role**: Convert existing `admin` role to department-scoped `hod` role
5. **Enable Multi-Department Support**: Support multiple independent departments (BCA, BBA, etc.)
6. **Build Public Website Module**: Create public-facing pages with secure API data flow
7. **Enhance Security & Auditing**: Implement comprehensive logging, 2FA, and data protection
8. **Implement Backup & Recovery**: Automated and on-demand backup with tested recovery

### Technology Stack

- **Framework**: Laravel 12 / PHP 8.2
- **Database**: MySQL 8.0+
- **ORM**: Eloquent
- **Authentication**: Laravel Sanctum (API tokens), Session-based (web)
- **Frontend**: Blade templating, Alpine.js, Tailwind CSS
- **Caching**: Redis (production) / File (development)
- **Queue**: Redis (production) / Database (development)

### Refactoring Approach

The refactoring will follow a **gradual migration strategy**:

1. **Phase 1**: Create new directory structure (`app/Modules`, `app/Services`, `app/Repositories`, `app/Components`, `app/Security`)
2. **Phase 2**: Implement Principal role and multi-department support
3. **Phase 3**: Migrate existing controllers to use Service and Repository layers
4. **Phase 4**: Build public website module with secure API endpoints
5. **Phase 5**: Implement security enhancements and audit logging
6. **Phase 6**: Add backup/recovery and final testing

All existing routes, controllers, and APIs will continue to function throughout the refactoring process.

---

## Architecture

### Layered Architecture

The system follows a **four-layer architecture**:

```
┌─────────────────────────────────────────────────────────┐
│                  Presentation Layer                      │
│  (Blade Views, Components, Controllers, API Responses)   │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                  Business Logic Layer                    │
│         (Service Classes, Domain Logic, Validation)      │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                   Data Access Layer                      │
│        (Repository Classes, Eloquent Query Logic)        │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                      Database Layer                      │
│              (MySQL, Eloquent Models, Schema)            │
└─────────────────────────────────────────────────────────┘
```

**Cross-Cutting Concerns** (applied at all layers):
- Security Layer (Middleware, Authorization, Validation)
- Audit Logging (Observer Pattern)
- Caching (Repository & Service layers)
- Error Handling (Global Exception Handler)

### Directory Structure

```
app/
├── Console/
│   └── Commands/
├── Exceptions/
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Existing admin controllers (to be migrated)
│   │   ├── Teacher/        # Existing teacher controllers
│   │   ├── Student/        # Existing student controllers
│   │   ├── Parent/         # Existing parent controllers
│   │   └── Api/
│   │       └── PublicController.php  # New public API endpoints
│   ├── Middleware/
│   │   ├── RoleMiddleware.php
│   │   ├── DepartmentScopeMiddleware.php  # New
│   │   └── RateLimitMiddleware.php        # New
│   └── Requests/
├── Models/                 # Eloquent models (existing + extended)
├── Modules/                # NEW: Role-based feature modules
│   ├── Principal/
│   │   ├── Controllers/
│   │   ├── Views/
│   │   └── Routes/
│   ├── Hod/
│   │   ├── Controllers/
│   │   ├── Views/
│   │   └── Routes/
│   ├── Teacher/
│   ├── Student/
│   └── Parent/
├── Services/               # NEW: Business logic layer
│   ├── UserService.php
│   ├── DepartmentService.php
│   ├── StudentService.php
│   ├── AttendanceService.php
│   ├── ExamService.php
│   ├── NotificationService.php  # Existing
│   ├── BackupService.php        # New
│   └── AuditService.php         # New
├── Repositories/           # NEW: Data access layer
│   ├── UserRepository.php
│   ├── DepartmentRepository.php
│   ├── StudentRepository.php
│   ├── AttendanceRepository.php
│   ├── ExamRepository.php
│   └── AuditLogRepository.php
├── Components/             # NEW: Reusable Blade components
│   ├── Forms/
│   ├── Tables/
│   ├── Charts/
│   └── Layouts/
├── Security/               # NEW: Security utilities
│   ├── Validators/
│   ├── Sanitizers/
│   └── Encryptors/
├── Observers/              # Existing (extended for audit logging)
│   ├── AuditObserver.php
│   └── NotificationObserver.php
├── Notifications/          # Existing notification classes
├── Providers/
└── Support/                # Existing helper classes
```

### Module Structure

Each module in `app/Modules/{Role}` follows this structure:

```
app/Modules/Principal/
├── Controllers/
│   ├── DashboardController.php
│   ├── UserManagementController.php
│   ├── DepartmentController.php
│   ├── SystemSettingsController.php
│   └── BackupController.php
├── Views/
│   ├── dashboard.blade.php
│   ├── users/
│   ├── departments/
│   └── settings/
└── Routes/
    └── principal.php
```

### Data Flow Patterns

#### 1. Web Request Flow (Authenticated Users)

```
HTTP Request
    ↓
Middleware (Auth, CSRF, Role, DepartmentScope)
    ↓
Controller (Presentation Layer)
    ↓
Service (Business Logic Layer)
    ↓
Repository (Data Access Layer)
    ↓
Eloquent Model
    ↓
Database
    ↓
Response (Blade View or JSON)
```

#### 2. Public API Request Flow

```
HTTP Request (Unauthenticated)
    ↓
Middleware (RateLimit, CORS)
    ↓
PublicController (API Layer)
    ↓
Service (Business Logic Layer)
    ↓
Repository (Data Access Layer - Level 3 data only)
    ↓
Cached Response (TTL: 300s)
    ↓
JSON Response
```

#### 3. Audit Logging Flow

```
Model Event (created, updated, deleted)
    ↓
AuditObserver
    ↓
AuditService
    ↓
AuditLogRepository
    ↓
audit_logs table (immutable)
```

---

## Components and Interfaces

### Service Layer Interfaces

All services implement a consistent interface pattern:

```php
interface ServiceInterface
{
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function delete(int $id): bool;
}
```

#### UserService

**Responsibilities**: User account management, role assignment, authentication

**Key Methods**:
- `createUser(array $data, string $role): User`
- `updateUser(int $userId, array $data): User`
- `assignRole(int $userId, string $role): void`
- `assignDepartment(int $userId, int $departmentId): void`
- `deactivateUser(int $userId): void`
- `verifyEmail(int $userId, string $token): bool`
- `changePassword(int $userId, string $currentPassword, string $newPassword): bool`

**Dependencies**: UserRepository, AuditService, NotificationService

#### DepartmentService

**Responsibilities**: Department management, HOD assignment, multi-department logic

**Key Methods**:
- `createDepartment(array $data): Department`
- `updateDepartment(int $deptId, array $data): Department`
- `assignHod(int $deptId, int $userId): void`
- `deactivateDepartment(int $deptId): void`
- `getDepartmentStats(int $deptId): array`
- `getAllDepartmentsStats(): array` (Principal only)

**Dependencies**: DepartmentRepository, UserRepository, AuditService

#### StudentService

**Responsibilities**: Student enrollment, semester management, bulk promotion

**Key Methods**:
- `enrollStudent(array $data): Student`
- `updateStudent(int $studentId, array $data): Student`
- `promoteStudents(int $sourceSemester, int $targetSemester, array $excludeIds = []): array`
- `markAsAlumni(int $studentId): void`
- `getStudentsByDepartment(int $deptId): Collection`
- `getStudentAttendanceSummary(int $studentId): array`

**Dependencies**: StudentRepository, UserRepository, AuditService, NotificationService

#### AttendanceService

**Responsibilities**: Attendance recording, validation, notifications

**Key Methods**:
- `recordAttendance(int $subjectId, string $date, array $studentStatuses): void`
- `updateAttendance(int $attendanceId, string $status): void`
- `getAttendanceBySubject(int $subjectId, string $startDate, string $endDate): Collection`
- `calculateAttendancePercentage(int $studentId, int $subjectId): float`
- `checkAttendanceThreshold(int $studentId, int $subjectId): void` (triggers notification if below threshold)

**Dependencies**: AttendanceRepository, StudentRepository, NotificationService

#### ExamService

**Responsibilities**: Exam creation, mark entry, grade calculation, result publishing

**Key Methods**:
- `createExam(array $data): Exam`
- `enterMarks(int $examId, array $studentMarks): void`
- `calculateGrade(float $marksObtained, float $totalMarks): string`
- `publishResults(int $examId): void`
- `getGradeDistribution(int $subjectId): array`

**Dependencies**: ExamRepository, StudentRepository, NotificationService

#### BackupService

**Responsibilities**: Database backup, file backup, restore operations

**Key Methods**:
- `createBackup(): string` (returns backup filename)
- `scheduleAutomatedBackup(): void`
- `restoreFromBackup(string $filename): bool`
- `listBackups(): Collection`
- `deleteOldBackups(int $keepCount = 30): void`
- `verifyBackupIntegrity(string $filename): bool`

**Dependencies**: AuditService

#### AuditService

**Responsibilities**: Audit log creation, querying, retention

**Key Methods**:
- `log(string $action, string $modelType, int $modelId, array $oldValues, array $newValues): void`
- `logLoginAttempt(int $userId, bool $success, string $ipAddress): void`
- `getAuditLogs(array $filters, int $page, int $perPage): LengthAwarePaginator`
- `archiveOldLogs(int $retentionMonths = 12): void`

**Dependencies**: AuditLogRepository

### Repository Layer Interfaces

All repositories implement a base interface:

```php
interface RepositoryInterface
{
    public function find(int $id): ?Model;
    public function findOrFail(int $id): Model;
    public function all(): Collection;
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function delete(int $id): bool;
}
```

#### UserRepository

**Key Methods**:
- `findByEmail(string $email): ?User`
- `findByRole(string $role): Collection`
- `findByDepartment(int $deptId): Collection`
- `createWithRole(array $data, string $role): User`

#### DepartmentRepository

**Key Methods**:
- `findActive(): Collection`
- `findWithHod(int $deptId): Department`
- `getStudentCount(int $deptId): int`
- `getTeacherCount(int $deptId): int`

#### StudentRepository

**Key Methods**:
- `findBySemester(int $semester, int $deptId = null): Collection`
- `findActive(int $deptId = null): Collection`
- `findAlumni(int $deptId = null): Collection`
- `bulkUpdateSemester(array $studentIds, int $newSemester): int`

#### AttendanceRepository

**Key Methods**:
- `findBySubjectAndDate(int $subjectId, string $date): Collection`
- `findByStudent(int $studentId, int $subjectId = null): Collection`
- `calculatePercentage(int $studentId, int $subjectId): float`

#### ExamRepository

**Key Methods**:
- `findBySubject(int $subjectId): Collection`
- `findWithMarks(int $examId): Exam`
- `getGradeDistribution(int $subjectId): array`

#### AuditLogRepository

**Key Methods**:
- `create(array $data): AuditLog`
- `findByUser(int $userId, array $filters): Collection`
- `findByAction(string $action, array $filters): Collection`
- `findByDateRange(string $startDate, string $endDate): Collection`

### Middleware Components

#### RoleMiddleware

**Purpose**: Enforce role-based access control

**Logic**:
```php
public function handle(Request $request, Closure $next, string ...$roles)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    if (!in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized access');
    }
    
    return $next($request);
}
```

#### DepartmentScopeMiddleware

**Purpose**: Enforce department-level data scoping for HOD role

**Logic**:
```php
public function handle(Request $request, Closure $next)
{
    $user = auth()->user();
    
    if ($user->role === 'hod') {
        // Apply global scope to all queries in this request
        Student::addGlobalScope('department', function ($query) use ($user) {
            $query->where('department', $user->department);
        });
        
        Teacher::addGlobalScope('department', function ($query) use ($user) {
            $query->where('department', $user->department);
        });
        
        // Similar for other models
    }
    
    return $next($request);
}
```

#### RateLimitMiddleware

**Purpose**: Prevent abuse of public API endpoints

**Configuration**:
- Public API: 60 requests per minute per IP
- Login endpoint: 5 attempts per 15 minutes per IP

---

## Data Models

### Database Schema Changes

#### 1. Extend `users` Table

**Migration**: `2024_12_01_000001_add_principal_role_to_users.php`

```php
Schema::table('users', function (Blueprint $table) {
    // Change role enum to include 'principal'
    $table->enum('role', ['principal', 'admin', 'teacher', 'student', 'parent'])
          ->default('student')
          ->change();
    
    // Add department_id foreign key (replacing string department field)
    $table->foreignId('department_id')->nullable()->after('role')
          ->constrained('departments')->onDelete('set null');
    
    // Add 2FA fields
    $table->string('two_factor_code', 6)->nullable()->after('password');
    $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
    
    // Add status field
    $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('role');
});
```

#### 2. Create `departments` Table

**Migration**: `2024_12_01_000002_create_departments_table.php`

```php
Schema::create('departments', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100)->unique();
    $table->string('name_nepali', 100)->nullable();
    $table->string('short_name', 20)->unique();
    $table->foreignId('hod_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('phone', 20)->nullable();
    $table->string('email', 255)->nullable();
    $table->string('website', 255)->nullable();
    $table->text('address')->nullable();
    $table->text('address_nepali')->nullable();
    $table->string('city', 100)->nullable();
    $table->string('district', 100)->nullable();
    $table->string('province', 100)->nullable();
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->text('map_embed_url')->nullable();
    $table->string('map_label', 255)->nullable();
    $table->string('principal_name', 255)->nullable();
    $table->string('principal_phone', 20)->nullable();
    $table->string('principal_email', 255)->nullable();
    $table->year('established_year')->nullable();
    $table->string('registration_number', 100)->nullable();
    $table->text('description')->nullable();
    $table->text('description_nepali')->nullable();
    $table->string('programs_title', 255)->nullable();
    $table->string('programs_title_nepali', 255)->nullable();
    $table->text('programs_content')->nullable();
    $table->text('programs_content_nepali')->nullable();
    $table->string('programs_image_path', 2048)->nullable();
    $table->string('logo_path', 2048)->nullable();
    $table->json('hero_images')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index('is_active');
});
```

#### 3. Add `department_id` to Existing Tables

**Migration**: `2024_12_01_000003_add_department_id_to_tables.php`

```php
// Add to students table
Schema::table('students', function (Blueprint $table) {
    $table->foreignId('department_id')->nullable()->after('user_id')
          ->constrained('departments')->onDelete('restrict');
    $table->index('department_id');
});

// Add to teachers table
Schema::table('teachers', function (Blueprint $table) {
    $table->foreignId('department_id')->nullable()->after('user_id')
          ->constrained('departments')->onDelete('restrict');
    $table->index('department_id');
});

// Add to subjects table
Schema::table('subjects', function (Blueprint $table) {
    $table->foreignId('department_id')->nullable()->after('course_id')
          ->constrained('departments')->onDelete('restrict');
    $table->index('department_id');
});

// Add to courses table
Schema::table('courses', function (Blueprint $table) {
    $table->foreignId('department_id')->nullable()->after('id')
          ->constrained('departments')->onDelete('restrict');
    $table->index('department_id');
});
```

#### 4. Create `audit_logs` Table

**Migration**: `2024_12_01_000004_create_audit_logs_table.php`

```php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('action', 50); // created, updated, deleted, login, logout, login_failed, etc.
    $table->string('model_type', 255)->nullable();
    $table->unsignedBigInteger('model_id')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamp('created_at');
    
    $table->index(['user_id', 'created_at']);
    $table->index(['action', 'created_at']);
    $table->index(['model_type', 'model_id']);
    $table->index('created_at');
});
```

#### 5. Create `erp_settings` Table

**Migration**: `2024_12_01_000005_create_erp_settings_table.php`

```php
Schema::create('erp_settings', function (Blueprint $table) {
    $table->id();
    $table->string('key', 100)->unique();
    $table->text('value')->nullable();
    $table->string('type', 20)->default('string'); // string, integer, boolean, json, float
    $table->string('group', 50)->default('general'); // general, grading, attendance, etc.
    $table->string('label', 255)->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
    
    $table->index('group');
});
```

#### 6. Create `backups` Table

**Migration**: `2024_12_01_000006_create_backups_table.php`

```php
Schema::create('backups', function (Blueprint $table) {
    $table->id();
    $table->string('filename', 255);
    $table->string('path', 2048);
    $table->unsignedBigInteger('size_bytes');
    $table->enum('type', ['automated', 'manual'])->default('manual');
    $table->enum('status', ['completed', 'failed', 'corrupted'])->default('completed');
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('created_at');
    
    $table->index('created_at');
});
```

#### 7. Create `alumni` Table

**Migration**: `2024_12_01_000007_create_alumni_table.php`

```php
Schema::create('alumni', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->string('name', 255);
    $table->year('graduation_year');
    $table->string('program', 150);
    $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
    $table->string('photo_path', 2048)->nullable();
    $table->text('bio')->nullable();
    $table->string('current_position', 255)->nullable();
    $table->string('company', 255)->nullable();
    $table->string('linkedin_url', 255)->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamps();
    
    $table->index(['department_id', 'is_published']);
    $table->index('graduation_year');
});
```

#### 8. Create `media_items` Table

**Migration**: `2024_12_01_000008_create_media_items_table.php`

```php
Schema::create('media_items', function (Blueprint $table) {
    $table->id();
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->enum('media_type', ['image', 'video'])->default('image');
    $table->string('url', 2048); // For images: storage path; for videos: YouTube/Vimeo URL
    $table->string('thumbnail_path', 2048)->nullable();
    $table->boolean('is_published')->default(false);
    $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    
    $table->index('is_published');
});
```

#### 9. Create `institutional_documents` Table

**Migration**: `2024_12_01_000009_create_institutional_documents_table.php`

```php
Schema::create('institutional_documents', function (Blueprint $table) {
    $table->id();
    $table->string('title', 255);
    $table->string('category', 100); // Legal, Academic, Administrative, etc.
    $table->enum('access_level', ['level_1', 'level_2', 'level_3'])->default('level_3');
    // level_1: Principal only, level_2: Principal + HOD, level_3: All authenticated
    $table->string('file_path', 2048);
    $table->unsignedBigInteger('file_size_bytes');
    $table->string('file_type', 50);
    $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
    $table->timestamp('uploaded_at');
    $table->timestamps();
    
    $table->index('category');
    $table->index('access_level');
});
```

### Model Relationships

#### User Model Extensions

```php
class User extends Authenticatable
{
    // Existing relationships
    public function student(): HasOne
    public function teacher(): HasOne
    public function parent(): HasOne
    
    // New relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
    
    public function createdBackups(): HasMany
    {
        return $this->hasMany(Backup::class, 'created_by');
    }
    
    // New methods
    public function isPrincipal(): bool
    {
        return $this->role === 'principal';
    }
    
    public function isHod(): bool
    {
        return $this->role === 'admin' || $this->role === 'hod';
    }
    
    public function canAccessDepartment(int $departmentId): bool
    {
        if ($this->isPrincipal()) {
            return true;
        }
        
        if ($this->isHod()) {
            return $this->department_id === $departmentId;
        }
        
        return false;
    }
}
```

#### Department Model

```php
class Department extends Model
{
    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_id');
    }
    
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
    
    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
    
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
    
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
    
    public function alumni(): HasMany
    {
        return $this->hasMany(Alumni::class);
    }
}
```

#### Student Model Extensions

```php
class Student extends Model
{
    // New relationship
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    
    public function alumniProfile(): HasOne
    {
        return $this->hasOne(Alumni::class);
    }
    
    // Existing relationships preserved
    public function user(): BelongsTo
    public function parent(): BelongsTo
    public function attendance(): HasMany
    public function examMarks(): HasMany
}
```

---: System Refactoring - Component-Based Architecture

## Overview

This design document outlines the technical architecture for refactoring the existing Laravel-based College ERP system into a fully component-based, modular, high-performance architecture. The refactoring will be performed **in-place** within the existing codebase, maintaining 100% backward compatibility throughout the process while introducing new capabilities including a Principal role, multi-department support, bulk semester promotion, public website module, enhanced reporting, security upgrades, and data backup/recovery.

### Design Philosophy

1. **Gradual Migration**: Refactor incrementally without breaking existing functionality
2. **Layered Architecture**: Enforce clear separation between presentation, business logic, data access, and security layers
3. **Backward Compatibility**: All existing routes, APIs, and database schemas remain functional
4. **Module-Based Organization**: Group related functionality into self-contained modules by role
5. **Service-Oriented**: Extract business logic into reusable service classes
6. **Repository Pattern**: Abstract data access through repository classes
7. **Security-First**: Apply security controls at every layer

### Technology Stack

- **Framework**: Laravel 12 / PHP 8.2
- **Database**: MySQL 8.0+
- **ORM**: Eloquent
- **Authentication**: Laravel Sanctum (API tokens) + Session-based auth
- **Templating**: Blade components
- **Frontend**: Alpine.js, Tailwind CSS
- **Caching**: Redis (recommended) or file-based cache
- **Queue**: Database or Redis queue driver

---

## Architecture

### High-Level Architecture Diagram

```mermaid
graph TB
    subgraph "Presentation Layer"
        PC[Public Controllers]
        BC[Blade Components]
        API[API Controllers]
        MW[Middleware]
    end
    
    subgraph "Business Logic Layer"
        PS[Principal Services]
        HS[HOD Services]
        TS[Teacher Services]
        SS[Student Services]
        PAS[Parent Services]
        CS[Common Services]
    end
    
    subgraph "Data Access Layer"
        UR[User Repository]
        SR[Student Repository]
        TR[Teacher Repository]
        DR[Department Repository]
        SBR[Subject Repository]
        AR[Attendance Repository]
        MR[Marks Repository]
    end
    
    subgraph "Security Layer"
        AUTH[Authentication]
        AUTHZ[Authorization]
        RBAC[RBAC Middleware]
        AUDIT[Audit Logger]
    end
    
    subgraph "Data Layer"
        DB[(MySQL Database)]
        CACHE[(Redis Cache)]
    end
    
    PC --> CS
    BC --> CS
    API --> CS
    MW --> RBAC
    
    PS --> UR
    PS --> DR
    HS --> SR
    HS --> TR
    TS --> AR
    TS --> MR
    SS --> SR
    PAS --> SR
    
    UR --> DB
    SR --> DB
    TR --> DB
    DR --> DB
    
    CS --> CACHE
    AUDIT --> DB
    
    RBAC --> AUTHZ
    AUTH --> DB
