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
        // Main teachers - your real users
        $mainTeachers = [
            [
                'email' => 'hellogoog94@gmail.com',
                'teacher_code' => 'TCH001',
                'qualification' => 'M.Sc. Computer Science',
                'gender' => 'Male',
                'bio' => 'Specialized in Database Management and Web Technologies',
                'is_primary' => true,
            ],
        ];

        // Additional teachers for better data distribution
        $additionalTeachers = [
            [
                'email' => 'anita.sharma@itdms.local',
                'teacher_code' => 'TCH002',
                'qualification' => 'M.Tech. Software Engineering',
                'gender' => 'Female',
                'bio' => 'Expert in Software Engineering and Project Management',
            ],
            [
                'email' => 'pradeep.kumar@itdms.local',
                'teacher_code' => 'TCH003',
                'qualification' => 'Ph.D. Computer Science',
                'gender' => 'Male',
                'bio' => 'Research focused on Data Structures and Algorithms',
            ],
            [
                'email' => 'meera.dhital@itdms.local',
                'teacher_code' => 'TCH004',
                'qualification' => 'M.Sc. Information Technology',
                'gender' => 'Female',
                'bio' => 'Specialized in Web Technologies and Networking',
            ],
            [
                'email' => 'rakesh.singh@itdms.local',
                'teacher_code' => 'TCH005',
                'qualification' => 'B.E. Computer Engineering',
                'gender' => 'Male',
                'bio' => 'Expert in Practical Programming and Development',
            ],
        ];

        $allTeachers = array_merge($mainTeachers, $additionalTeachers);

        foreach ($allTeachers as $teacherData) {
            $user = User::where('email', $teacherData['email'])->first();

            if ($user) {
                Teacher::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'teacher_code' => $teacherData['teacher_code'],
                        'qualification' => $teacherData['qualification'],
                        'phone' => $user->phone,
                        'gender' => $teacherData['gender'],
                        'department' => 'IT',
                        'status' => 'active',
                        'address' => 'Kathmandu, Nepal',
                        'bio' => $teacherData['bio'],
                    ]
                );
            }
        }
    }
}

