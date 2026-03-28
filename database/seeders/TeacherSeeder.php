<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherEmails = [
            'hellogoog94@gmail.com',
            'anita.sharma@itdms.local',
            'pradeep.kumar@itdms.local',
            'meera.dhital@itdms.local',
            'rakesh.singh@itdms.local',
        ];

        $teacherData = [
            ['teacher_code' => 'TCH001', 'qualification' => 'M.Sc. Computer Science', 'gender' => 'Male'],
            ['teacher_code' => 'TCH002', 'qualification' => 'M.Tech. Software Engineering', 'gender' => 'Female'],
            ['teacher_code' => 'TCH003', 'qualification' => 'Ph.D. Computer Science', 'gender' => 'Male'],
            ['teacher_code' => 'TCH004', 'qualification' => 'M.Sc. Information Technology', 'gender' => 'Female'],
            ['teacher_code' => 'TCH005', 'qualification' => 'B.E. Computer Engineering', 'gender' => 'Male'],
        ];

        foreach ($teacherEmails as $index => $email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                Teacher::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'teacher_code' => $teacherData[$index]['teacher_code'],
                        'qualification' => $teacherData[$index]['qualification'],
                        'phone' => $user->phone,
                        'gender' => $teacherData[$index]['gender'],
                        'department' => 'IT',
                        'status' => 'active',
                        'address' => 'Kathmandu, Nepal',
                        'bio' => 'Faculty Member - IT Department',
                    ]
                );
            }
        }
    }
}

