<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ParentModel;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@dit.edu.np'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@dit.edu.np',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+977-9800000001',
                'department' => 'Administration',
            ]
        );

        // Teachers (3)
        $teachers = [
            ['name' => 'Dr. Ram Shrestha', 'email' => 'ram@dit.edu.np', 'phone' => '+977-9800000101', 'department' => 'Computer Engineering'],
            ['name' => 'Prof. Sita Sharma', 'email' => 'sita@dit.edu.np', 'phone' => '+977-9800000102', 'department' => 'Computer Engineering'],
            ['name' => 'Mr. Hari Karki', 'email' => 'hari@dit.edu.np', 'phone' => '+977-9800000103', 'department' => 'Computer Engineering'],
        ];

        foreach ($teachers as $t) {
            $user = User::updateOrCreate(
                ['email' => $t['email']],
                array_merge($t, [
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                ])
            );
            // Will be linked in TeacherSeeder
        }

        // Parents (5)
        $parents = [
            ['name' => 'Parent 1', 'email' => 'parent1@dit.edu.np', 'phone' => '+977-9800000201'],
            ['name' => 'Parent 2', 'email' => 'parent2@dit.edu.np', 'phone' => '+977-9800000202'],
            ['name' => 'Parent 3', 'email' => 'parent3@dit.edu.np', 'phone' => '+977-9800000203'],
            ['name' => 'Parent 4', 'email' => 'parent4@dit.edu.np', 'phone' => '+977-9800000204'],
            ['name' => 'Parent 5', 'email' => 'parent5@dit.edu.np', 'phone' => '+977-9800000205'],
        ];

        foreach ($parents as $p) {
            User::updateOrCreate(
                ['email' => $p['email']],
                array_merge($p, [
                    'password' => Hash::make('password'),
                    'role' => 'parent',
                ])
            );
        }

        // Students (10)
        $student_emails = [];
        for ($i = 1; $i <= 10; $i++) {
            $email = "student{$i}@dit.edu.np";
            $student_emails[] = $email;
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Student {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => "+977-980000030{$i}",
                ]
            );
        }
        
        // Store student IDs for later StudentSeeder if needed
        cache(['seeder_student_emails' => $student_emails]);
    }
}

