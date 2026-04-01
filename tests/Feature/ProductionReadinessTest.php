<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_routes_are_disabled_by_default(): void
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }

    public function test_public_pages_render_successfully_with_empty_database(): void
    {
        $this->get('/')->assertOk();
        $this->get('/gallery')->assertOk();
        $this->get('/notices')->assertOk();
        $this->get('/resources')->assertOk();
        $this->get('/faculty')->assertOk();
        $this->get('/subjects')->assertOk();
    }

    public function test_admin_student_api_rejects_non_admin_tokens(): void
    {
        $studentUser = $this->makeUser('student', 'student@example.com');

        Sanctum::actingAs($studentUser);

        $this->getJson('/api/admin/students/1')->assertForbidden();
    }

    public function test_admin_student_api_returns_expected_payload_for_admin(): void
    {
        $admin = $this->makeUser('admin', 'admin@example.com');
        $studentUser = $this->makeUser('student', 'learner@example.com');

        Department::create([
            'name' => 'IT Department',
            'short_name' => 'IT',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'roll_no' => 'STU-001',
            'semester' => '1',
            'status' => 'active',
            'phone' => '9800000000',
            'address' => 'Kathmandu',
        ]);

        $subject = Subject::create([
            'subject_name' => 'Programming I',
            'subject_code' => 'IT101',
            'semester' => '1',
            'status' => 'active',
        ]);

        Mark::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $admin->id,
            'exam_type' => 'internal',
            'marks_obtained' => 82,
            'full_marks' => 100,
            'date' => now()->toDateString(),
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'teacher_id' => $admin->id,
            'subject_id' => $subject->id,
            'attendance_type' => 'class',
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        Sanctum::actingAs($admin);

        $this->getJson("/api/admin/students/{$studentUser->id}")
            ->assertOk()
            ->assertJsonPath('student.name', 'student')
            ->assertJsonPath('student.roll_no', 'STU-001')
            ->assertJsonPath('college.name', 'IT Department')
            ->assertJsonPath('attendance.total_days', 1)
            ->assertJsonPath('attendance.present_days', 1)
            ->assertJsonPath('marks.0.subject', 'Programming I')
            ->assertJsonPath('marks.0.obtained_marks', 82);
    }

    private function makeUser(string $role, string $email): User
    {
        $user = User::create([
            'name' => $role,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $user->role = $role;
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }
}
