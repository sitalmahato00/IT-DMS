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

        // Teachers (15)
        $teachers = [
            ['name' => 'Dr. Ram Shrestha', 'email' => 'ram@dit.edu.np', 'phone' => '+977-9800000101', 'department' => 'Computer Engineering'],
            ['name' => 'Prof. Sita Sharma', 'email' => 'sita@dit.edu.np', 'phone' => '+977-9800000102', 'department' => 'Computer Engineering'],
            ['name' => 'Mr. Hari Karki', 'email' => 'hari@dit.edu.np', 'phone' => '+977-9800000103', 'department' => 'Computer Engineering'],
            ['name' => 'Dr. Priya Poudel', 'email' => 'priya@dit.edu.np', 'phone' => '+977-9800000104', 'department' => 'Computer Engineering'],
            ['name' => 'Mr. Rajesh Singh', 'email' => 'rajesh@dit.edu.np', 'phone' => '+977-9800000105', 'department' => 'Computer Engineering'],
            ['name' => 'Ms. Neha Verma', 'email' => 'neha@dit.edu.np', 'phone' => '+977-9800000106', 'department' => 'Computer Engineering'],
            ['name' => 'Prof. Anil Khadka', 'email' => 'anil@dit.edu.np', 'phone' => '+977-9800000107', 'department' => 'Computer Engineering'],
            ['name' => 'Dr. Sunita Rai', 'email' => 'sunita@dit.edu.np', 'phone' => '+977-9800000108', 'department' => 'Computer Engineering'],
            ['name' => 'Mr. Bikram Thapa', 'email' => 'bikram@dit.edu.np', 'phone' => '+977-9800000109', 'department' => 'Computer Engineering'],
            ['name' => 'Ms. Ananya Das', 'email' => 'ananya@dit.edu.np', 'phone' => '+977-9800000110', 'department' => 'Computer Engineering'],
            ['name' => 'Prof. Deepak Nepal', 'email' => 'deepak@dit.edu.np', 'phone' => '+977-9800000111', 'department' => 'Computer Engineering'],
            ['name' => 'Dr. Harshita Gupta', 'email' => 'harshita@dit.edu.np', 'phone' => '+977-9800000112', 'department' => 'Computer Engineering'],
            ['name' => 'Mr. Vikram Joshi', 'email' => 'vikram@dit.edu.np', 'phone' => '+977-9800000113', 'department' => 'Computer Engineering'],
            ['name' => 'Ms. Pooja Sharma', 'email' => 'pooja@dit.edu.np', 'phone' => '+977-9800000114', 'department' => 'Computer Engineering'],
            ['name' => 'Prof. Suresh Adhikari', 'email' => 'suresh@dit.edu.np', 'phone' => '+977-9800000115', 'department' => 'Computer Engineering'],
        ];

        foreach ($teachers as $t) {
            $user = User::updateOrCreate(
                ['email' => $t['email']],
                array_merge($t, [
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                ])
            );
        }

        // Parents (20)
        $parents = [];
        for ($i = 1; $i <= 20; $i++) {
            $parents[] = [
                'name' => "Parent {$i}",
                'email' => "parent{$i}@dit.edu.np",
                'phone' => "+977-980000020{$i}"
            ];
        }

        foreach ($parents as $p) {
            User::updateOrCreate(
                ['email' => $p['email']],
                array_merge($p, [
                    'password' => Hash::make('password'),
                    'role' => 'parent',
                ])
            );
        }

        // Students (50)
        $student_emails = [];
        for ($i = 1; $i <= 50; $i++) {
            $email = "student{$i}@dit.edu.np";
            $student_emails[] = $email;
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Student {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => "+977-9800000300" . str_pad($i, 2, '0', STR_PAD_LEFT),
                ]
            );
        }
        
        // Store student IDs for later StudentSeeder if needed
        cache(['seeder_student_emails' => $student_emails]);
    }
}

