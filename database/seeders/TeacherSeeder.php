<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherEmails = [
            'ram@dit.edu.np', 'sita@dit.edu.np', 'hari@dit.edu.np',
            'priya@dit.edu.np', 'rajesh@dit.edu.np', 'neha@dit.edu.np',
            'anil@dit.edu.np', 'sunita@dit.edu.np', 'bikram@dit.edu.np',
            'ananya@dit.edu.np', 'deepak@dit.edu.np', 'harshita@dit.edu.np',
            'vikram@dit.edu.np', 'pooja@dit.edu.np', 'suresh@dit.edu.np'
        ];

        $genders = ['male', 'female', 'male', 'female', 'male', 'female', 'male', 'female', 'male', 'female', 'male', 'female', 'male', 'female', 'male'];
        $qualifications = [
            'M.Tech in Computer Engineering',
            'Ph.D in Information Technology',
            'M.Sc in Computer Science',
            'M.Tech in Software Engineering',
            'B.Tech in Computer Engineering',
            'M.Sc in Data Science',
            'M.Tech in Artificial Intelligence',
            'Ph.D in Machine Learning',
            'M.Sc in Cybersecurity',
            'M.Tech in Cloud Computing',
            'M.Sc in Database Systems',
            'M.Tech in Web Technology',
            'B.Tech in Information Technology',
            'M.Sc in Software Development',
            'Ph.D in Computer Networks',
        ];

        foreach ($teacherEmails as $index => $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'teacher_code' => 'T' . sprintf('%03d', $index + 1),
                        'qualification' => $qualifications[$index],
                        'phone' => $user->phone,
                        'status' => 'active',
                        'gender' => $genders[$index],
                    ]
                );
            }
        }
    }
}

