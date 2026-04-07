<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Passwords per role; allow overrides via env for deployment
        $rolePasswords = [
            'admin' => env('SEED_PASSWORD_ADMIN', 'admin123'),
            'teacher' => env('SEED_PASSWORD_TEACHER', 'teacher123'),
            'student' => env('SEED_PASSWORD_STUDENT', 'student123'),
            'parent' => env('SEED_PASSWORD_PARENT', 'parent123'),
        ];

        $defaultPassword = env('SEED_DEFAULT_PASSWORD', 'role123');

        $getPassword = function (string $role) use ($rolePasswords, $defaultPassword) {
            return $rolePasswords[$role] ?? $defaultPassword;
        };

        // Admin User
        User::updateOrCreate(
            ['email' => 'sitalmahato077@gmail.com'],
            [
                'name' => 'Admin User',
                'email' => 'sitalmahato077@gmail.com',
            'password' => Hash::make($getPassword('admin')),
                'role' => 'admin',
                'phone' => '9841234567',
                'department' => 'IT',
                'bio' => 'System Administrator',
                'email_verified_at' => now(),
            ]
        );

        // Teacher Users
        User::updateOrCreate(
            ['email' => 'hellogoog94@gmail.com'],
            [
                'name' => 'Dr. Ramesh Poudel',
                'email' => 'hellogoog94@gmail.com',
            'password' => Hash::make($getPassword('teacher')),
                'role' => 'teacher',
                'phone' => '9847654321',
                'department' => 'IT',
                'bio' => 'Assistant Professor - Web & Database Technologies',
                'email_verified_at' => now(),
            ]
        );

        // Additional Teachers
        $teachers = [
            ['name' => 'Prof. Anita Sharma', 'email' => 'anita.sharma@itdms.local', 'phone' => '9841112233'],
            ['name' => 'Dr. Pradeep Kumar', 'email' => 'pradeep.kumar@itdms.local', 'phone' => '9842223344'],
            ['name' => 'Ms. Meera Dhital', 'email' => 'meera.dhital@itdms.local', 'phone' => '9843334455'],
            ['name' => 'Mr. Rakesh Singh', 'email' => 'rakesh.singh@itdms.local', 'phone' => '9844445566'],
        ];

        foreach ($teachers as $teacher) {
            User::updateOrCreate(
                ['email' => $teacher['email']],
                [
                    'name' => $teacher['name'],
                    'email' => $teacher['email'],
                    'password' => Hash::make($getPassword('teacher')),
                    'role' => 'teacher',
                    'phone' => $teacher['phone'],
                    'department' => 'IT',
                    'bio' => 'Faculty Member - IT Department',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Student Users
        User::updateOrCreate(
            ['email' => 'itstudentsital@gmail.com'],
            [
                'name' => 'Sital Mahato',
                'email' => 'itstudentsital@gmail.com',
            'password' => Hash::make($getPassword('student')),
                'role' => 'student',
                'phone' => '9845556789',
                'department' => 'IT',
                'bio' => 'IT Student',
                'email_verified_at' => now(),
            ]
        );

        // Additional Students
        $students = [
            ['name' => 'Rajesh Adhikari', 'email' => 'rajesh.adhikari@student.itdms.local', 'phone' => '9841234567'],
            ['name' => 'Priya Verma', 'email' => 'priya.verma@student.itdms.local', 'phone' => '9842345678'],
            ['name' => 'Arjun Pant', 'email' => 'arjun.pant@student.itdms.local', 'phone' => '9843456789'],
            ['name' => 'Anjali Singh', 'email' => 'anjali.singh@student.itdms.local', 'phone' => '9844567890'],
        ];

        foreach ($students as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'email' => $student['email'],
                    'password' => Hash::make($getPassword('student')),
                    'role' => 'student',
                    'phone' => $student['phone'],
                    'department' => 'IT',
                    'bio' => 'IT Student',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Parent User
        User::updateOrCreate(
            ['email' => 'sitalmahato00@gmail.com'],
            [
                'name' => 'Parent User',
                'email' => 'sitalmahato00@gmail.com',
            'password' => Hash::make($getPassword('parent')),
                'role' => 'parent',
                'phone' => '9843334444',
                'department' => 'IT',
                'bio' => 'Student Parent/Guardian',
                'email_verified_at' => now(),
            ]
        );

        // Additional Parents
        $parents = [
            ['name' => 'Hari Adhikari', 'email' => 'hari.adhikari@parent.itdms.local', 'phone' => '9841111111'],
            ['name' => 'Sunita Verma', 'email' => 'sunita.verma@parent.itdms.local', 'phone' => '9842222222'],
            ['name' => 'Gyan Pant', 'email' => 'gyan.pant@parent.itdms.local', 'phone' => '9843333333'],
            ['name' => 'Neha Singh', 'email' => 'neha.singh@parent.itdms.local', 'phone' => '9844444444'],
        ];

        foreach ($parents as $parent) {
            User::updateOrCreate(
                ['email' => $parent['email']],
                [
                    'name' => $parent['name'],
                    'email' => $parent['email'],
                    'password' => Hash::make($getPassword('parent')),
                    'role' => 'parent',
                    'phone' => $parent['phone'],
                    'department' => 'IT',
                    'bio' => 'Student Parent/Guardian',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

