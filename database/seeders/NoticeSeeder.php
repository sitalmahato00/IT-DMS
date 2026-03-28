<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $notices = [
            [
                'title' => 'Welcome to IT-DMS',
                'message' => 'Welcome to the IT Department Management System. This is a comprehensive platform for managing department activities. All students, teachers, and parents can access their relevant information here.',
                'audience' => 'all',
                'status' => 'published',
                'is_important' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Semester 5 Registration',
                'message' => 'All students are required to register for semester 5. Registration deadline is extended to next week. Please login and complete your registration before the deadline.',
                'audience' => 'student',
                'status' => 'published',
                'is_important' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Internal Exam Schedule',
                'message' => 'Internal exams for semester 5 will be conducted starting from next month. The detailed schedule will be published soon. Students are advised to prepare accordingly.',
                'audience' => 'student',
                'status' => 'published',
                'is_important' => false,
                'published_at' => now()->addDays(1),
            ],
            [
                'title' => 'Study Materials Available',
                'message' => 'Teachers have uploaded study materials for all subjects. Students can download them from the Study Materials section.',
                'audience' => 'student',
                'status' => 'published',
                'is_important' => false,
                'published_at' => now()->addDays(2),
            ],
            [
                'title' => 'Attendance Policy',
                'message' => 'Students with attendance below 75% will not be allowed to sit in the final examination. Check your attendance regularly from the Attendance section.',
                'audience' => 'student',
                'status' => 'published',
                'is_important' => true,
                'published_at' => now()->addDays(3),
            ],
            [
                'title' => 'Teacher Training Workshop',
                'message' => 'A workshop on modern teaching methodologies will be conducted for all faculty members. Attendance is mandatory.',
                'audience' => 'teacher',
                'status' => 'published',
                'is_important' => true,
                'published_at' => now()->addDays(4),
            ],
            [
                'title' => 'System Maintenance',
                'message' => 'The system will be under maintenance on Friday from 2 PM to 6 PM. Users will not be able to access the system during this time.',
                'audience' => 'all',
                'status' => 'published',
                'is_important' => true,
                'published_at' => now()->addDays(5),
            ],
            [
                'title' => 'Parent-Teacher Meeting',
                'message' => 'Parent-teacher meetings will be conducted on the last Saturday of this month. Parents can schedule their appointments online.',
                'audience' => 'parent',
                'status' => 'published',
                'is_important' => false,
                'published_at' => now()->addDays(6),
            ],
        ];

        foreach ($notices as $notice) {
            Notice::firstOrCreate(
                ['title' => $notice['title']],
                array_merge($notice, [
                    'created_by' => $admin?->id,
                ])
            );
        }
    }
}
