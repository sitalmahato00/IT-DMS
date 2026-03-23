<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherEmails = ['ram@dit.edu.np', 'sita@dit.edu.np', 'hari@dit.edu.np'];

        foreach ($teacherEmails as $index => $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'teacher_code' => 'T' . sprintf('%03d', $index + 1),
                        'qualification' => 'M.Tech in Computer Engineering',
                        'phone' => $user->phone,
                        'status' => 'active',
                        'gender' => ['male', 'female', 'male'][$index],
                    ]
                );
            }
        }
    }
}

