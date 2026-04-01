# 🧪 IT-DMS PRODUCTION TEST STRATEGY

## Overview
Minimum 70+ tests required for production readiness. Current: ~5 tests (95% gap)

---

## Test Structure

```
tests/
├── Feature/                 # Integration tests (70% of total)
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   ├── LogoutTest.php
│   │   └── PasswordResetTest.php
│   ├── Academic/
│   │   ├── CourseManagementTest.php
│   │   ├── StudentEnrollmentTest.php
│   │   └── TimetableTest.php
│   ├── Marks/
│   │   ├── MarksEntryTest.php
│   │   ├── GradeCalculationTest.php
│   │   └── ResultsExportTest.php
│   ├── Attendance/
│   │   ├── AttendanceMarkingTest.php
│   │   ├── AttendanceReportTest.php
│   │   └── AttendanceAlertTest.php
│   ├── Reports/
│   │   ├── PerformanceReportTest.php
│   │   ├── ExcelExportTest.php
│   │   └── PDFGenerationTest.php
│   └── API/
│       ├── CourseAPITest.php
│       ├── RateLimitingTest.php
│       └── AuthTokenTest.php
│
├── Unit/                    # Unit tests (25% of total)
│   ├── Models/
│   │   ├── StudentTest.php
│   │   ├── CourseTest.php
│   │   ├── AttendanceTest.php
│   │   └── ExamMarkTest.php
│   ├── Services/
│   │   ├── GradeCalculationServiceTest.php
│   │   └── ReportGenerationServiceTest.php
│   └── Helpers/
│       └── NepaliContentHelperTest.php
│
└── Browser/                 # E2E tests (optional, 5%)
    └── LoginFlowTest.php
```

---

## Test Count by Feature

### Authentication & Authorization (12 tests)
```php
// tests/Feature/Auth/LoginTest.php

class LoginTest extends TestCase
{
    // ✅ TEST 1: User can login with valid credentials
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create(['password' => 'password123']);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    // ✅ TEST 2: User cannot login with invalid password
    public function test_user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create();
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors();
    }

    // ✅ TEST 3: Admin can access admin panel
    public function test_admin_can_access_admin_panel()
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    // ✅ TEST 4: Student cannot access admin panel
    public function test_student_cannot_access_admin_panel()
    {
        $student = User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    // ✅ TEST 5: Session expires after 8 hours
    // ✅ TEST 6: User can logout
    // ✅ TEST 7: Password reset email sent
    // ✅ TEST 8: Password reset with valid token works
    // ✅ TEST 9: Password reset with invalid token fails
    // ✅ TEST 10: Two-factor auth (if implemented)
    // ✅ TEST 11: Account lockout after 5 attempts
    // ✅ TEST 12: CSRF token validation
}
```

### Course Management (10 tests)
```php
class CourseManagementTest extends TestCase
{
    // ✅ TEST 1: Admin can create course
    // ✅ TEST 2: Course validation works
    // ✅ TEST 3: Admin can update course
    // ✅ TEST 4: Admin can delete course
    // ✅ TEST 5: Cannot create course without teacher
    // ✅ TEST 6: Course appears in student's course list
    // ✅ TEST 7: Query count is < 5 for course listing
    // ✅ TEST 8: Search filter works correctly
    // ✅ TEST 9: Pagination works on course list
    // ✅ TEST 10: Teacher can see assigned courses
}
```

### Student Enrollment (8 tests)
```php
class StudentEnrollmentTest extends TestCase
{
    // ✅ TEST 1: Student can enroll in elective
    // ✅ TEST 2: Student cannot enroll twice
    // ✅ TEST 3: Enrollment validates course capacity
    // ✅ TEST 4: Student sees enrolled courses in dashboard
    // ✅ TEST 5: Parent can see child's enrollments
    // ✅ TEST 6: Teacher sees enrolled students
    // ✅ TEST 7: Enrollment records are created in DB
    // ✅ TEST 8: Cannot enroll after deadline
}
```

### Marks Management (15 tests)
```php
class MarksEntryTest extends TestCase
{
    // ✅ TEST 1: Teacher can enter internal marks
    // ✅ TEST 2: Teacher can enter assessment marks
    // ✅ TEST 3: Teacher can enter final marks
    // ✅ TEST 4: Marks validation works (0-100)
    // ✅ TEST 5: Cannot enter marks with decimal places > 2
    // ✅ TEST 6: Marks cannot be edited after submission
    // ✅ TEST 7: Student can view own marks
    // ✅ TEST 8: Parent can see child's marks
    // ✅ TEST 9: Admin can download marks as Excel
    // ✅ TEST 10: GPA calculated correctly
    // ✅ TEST 11: Letter grade assigned correctly (A, B, C, etc.)
    // ✅ TEST 12: Bulk marks import works
    // ✅ TEST 13: Marks backup created
    // ✅ TEST 14: Result notification sent to parents
    // ✅ TEST 15: Query N+1 prevention verified
}
```

### Attendance Tracking (10 tests)
```php
class AttendanceMarkingTest extends TestCase
{
    // ✅ TEST 1: Teacher can mark attendance
    // ✅ TEST 2: Attendance validation works
    // ✅ TEST 3: Cannot mark attendance twice for same date
    // ✅ TEST 4: Query optimization verified
    // ✅ TEST 5: Attendance report generates correctly
    // ✅ TEST 6: Student can view own attendance
    // ✅ TEST 7: Parent sees child's attendance
    // ✅ TEST 8: Attendance alerts sent for <75% attendance
    // ✅ TEST 9: Bulk attendance import works
    // ✅ TEST 10: Monthly attendance summary generated
}
```

### Reports & Exports (12 tests)
```php
class ReportGenerationTest extends TestCase
{
    // ✅ TEST 1: Excel export works
    // ✅ TEST 2: PDF export works
    // ✅ TEST 3: CSV export works
    // ✅ TEST 4: Performance report generated
    // ✅ TEST 5: Attendance report generated
    // ✅ TEST 6: Report filtering works
    // ✅ TEST 7: Report permissions enforced
    // ✅ TEST 8: Large dataset export handles pagination
    // ✅ TEST 9: Export filename includes date
    // ✅ TEST 10: Exported file has correct headers
    // ✅ TEST 11: Report caching works
    // ✅ TEST 12: Concurrent exports don't conflict
}
```

### API Tests (15 tests)
```php
class CourseAPITest extends TestCase
{
    // ✅ TEST 1: GET /api/courses returns list
    // ✅ TEST 2: GET /api/courses filtered by semester
    // ✅ TEST 3: Authentication required for API
    // ✅ TEST 4: Invalid token rejected
    // ✅ TEST 5: Rate limiting enforced (60 req/min)
    // ✅ TEST 6: POST /api/courses creates course
    // ✅ TEST 7: API validation works
    // ✅ TEST 8: 404 for non-existent resource
    // ✅ TEST 9: CORS headers correct
    // ✅ TEST 10: API response format consistent
    // ✅ TEST 11: Pagination works in API
    // ✅ TEST 12: API accepts JSON and form data
    // ✅ TEST 13: Sensitive data not exposed in API
    // ✅ TEST 14: Rate limit headers present
    // ✅ TEST 15: API versioning works (v1/v2)
}
```

### Security Tests (10 tests)
```php
class SecurityTest extends TestCase
{
    // ✅ TEST 1: SQL injection prevented in search
    // ✅ TEST 2: XSS prevented in user input
    // ✅ TEST 3: CSRF token validation works
    // ✅ TEST 4: File upload validation prevents executables
    // ✅ TEST 5: File upload prevents path traversal
    // ✅ TEST 6: Session regeneration on login
    // ✅ TEST 7: Brute force protection works
    // ✅ TEST 8: Sensitive headers present (X-Frame-Options, etc.)
    // ✅ TEST 9: Password reset tokens expire
    // ✅ TEST 10: Authorization bypass prevented
}
```

### Database Tests (7 tests)
```php
class DatabaseTest extends TestCase
{
    // ✅ TEST 1: Migration rolls back cleanly
    // ✅ TEST 2: All relationships load correctly
    // ✅ TEST 3: Foreign key constraints enforced
    // ✅ TEST 4: Indexes created correctly
    // ✅ TEST 5: Database transactions rollback on error
    // ✅ TEST 6: Soft delete models work
    // ✅ TEST 7: Timestamps auto-update
}
```

### Email & Notification Tests (5 tests)
```php
class NotificationTest extends TestCase
{
    // ✅ TEST 1: Password reset email sent
    // ✅ TEST 2: Result notification sent to parent
    // ✅ TEST 3: Attendance alert sent
    // ✅ TEST 4: Email contains correct data
    // ✅ TEST 5: Notification queue works
}
```

---

## Total Test Count

| Category | Count | Priority |
|----------|-------|----------|
| Authentication | 12 | CRITICAL |
| Courses | 10 | CRITICAL |
| Enrollment | 8 | CRITICAL |
| Marks | 15 | CRITICAL |
| Attendance | 10 | HIGH |
| Reports | 12 | HIGH |
| API | 15 | HIGH |
| Security | 10 | CRITICAL |
| Database | 7 | MEDIUM |
| Email/Notifications | 5 | MEDIUM |
| **TOTAL** | **104** | - |

**Minimum for Production**: 70 tests
**Current**: ~5 tests
**Gap**: 65 tests needed

---

## Running Tests

### Run all tests
```bash
php artisan test
```

### Run with coverage report
```bash
php artisan test --coverage

# Generate HTML coverage report
php artisan test --coverage-html=coverage
```

### Run specific test file
```bash
php artisan test tests/Feature/Auth/LoginTest.php
```

### Run tests matching pattern
```bash
php artisan test --filter=LoginTest
```

### Run in parallel (faster)
```bash
php artisan test --parallel --processes=4
```

---

## Coverage Requirements

```
Minimum Required: 60% Code Coverage
Target: 80% Code Coverage

- Controllers: 80%+
- Models: 90%+
- Services: 85%+
- Helpers: 70%+
- Middleware: 75%+
```

---

## Continuous Integration Setup

### GitHub Actions Example (.github/workflows/tests.yml)

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: test_db
          MYSQL_PASSWORD: password
          MYSQL_ROOT_PASSWORD: password
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 3306:3306

      redis:
        image: redis:7-alpine
        options: >-
          --health-cmd="redis-cli ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 6379:6379

    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mysql, redis
          coverage: xdebug
      
      - name: Install dependencies
        run: composer install --no-interaction
      
      - name: Create .env
        run: cp .env.example .env && php artisan key:generate
      
      - name: Run tests
        run: php artisan test --coverage --min=60
      
      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage.xml
```

---

## Testing Timeline

### Phase 1: Critical Tests (Day 1)
- Authentication (12 tests) - 2 hours
- Security (10 tests) - 2 hours
- **Total**: 22 tests in 4 hours

### Phase 2: Core Features (Day 2)
- Courses (10 tests) - 1.5 hours
- Enrollment (8 tests) - 1 hour
- Marks (15 tests) - 2.5 hours
- **Total**: 33 tests in 5 hours

### Phase 3: Supporting Features (Day 3)
- Attendance (10 tests) - 1.5 hours
- Reports (12 tests) - 2 hours
- API (15 tests) - 2 hours
- Database (7 tests) - 1 hour
- Email (5 tests) - 1 hour
- **Total**: 49 tests in 7.5 hours

### Phase 4: Integration & Coverage (Day 4)
- Run full test suite
- Achieve 60%+ coverage
- Fix failing tests
- **Total**: 4 hours

---

## Testing Best Practices

### 1. Use Factories for Test Data
```php
// ✅ GOOD
$user = User::factory()->create();
$course = Course::factory()->create(['teacher_id' => $user->id]);

// ❌ BAD
$user = new User();
$user->name = 'Test User';
$user->email = 'test@example.com';
$user->save();
```

### 2. Run Tests Before Each Commit
```bash
# Pre-commit hook
#!/bin/bash
php artisan test || exit 1
```

### 3. Test Edge Cases
```php
// Test boundary conditions
public function test_attendance_percentage_calculation()
{
    // 0% attendance
    // 50% attendance
    // 75% threshold
    // 100% attendance
}
```

### 4. Mock External Services
```php
Mail::fake();
$this->post('/password-reset', [...])->assertDontSee('error');
Mail::assertSent(ResetPasswordMail::class);
```

---

## Expected Test Problems & Solutions

| Problem | Cause | Solution |
|---------|-------|----------|
| Tests timeout | DB queries slow | Add indexes, use factories |
| Intermittent failures | Race conditions | Use database transactions |
| Random failures | Time-dependent tests | Mock time with Carbon::setTestNow() |
| Permission errors | File permissions | Use chmod in setUp() |
| Data conflicts | Tests not isolated | Use DatabaseTransactions trait |

---

**Test Strategy Document Created**: April 2026
**Status**: Ready for implementation
**Estimated Effort**: 20-25 hours for complete test suite
