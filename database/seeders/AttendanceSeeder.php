<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $statuses = ['present', 'absent', 'late', 'leave', 'excused'];

        $startDate = now()->subDays(30);

        for ($i = 0; $i < 20; $i++) {
            $date = $startDate->copy()->addDays($i);

            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    $teacher = $teachers->random();
                    $status = $statuses[array_rand($statuses)];

                    Attendance::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'date' => $date,
                        ],
                        [
                            'student_id' => $student->id,
                            'teacher_id' => $teacher->user_id,
                            'subject_id' => $subject->id,
                            'date' => $date,
                            'date_bs' => $date,
                            'academic_year' => '2080-2081',
                            'academic_year_bs' => '2080-2081',
                            'status' => $status,
                            'remarks' => ucfirst($status) . ' in ' . $subject->subject_name,
                        ]
                    );
                }
            }
        }
    }
}
