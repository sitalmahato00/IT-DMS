<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeacher;
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
        $statuses = ['present', 'absent', 'late', 'leave', 'excused'];
        $attendanceTypes = ['class', 'lab'];

        $startDate = now()->subDays(60);

        // Create detailed attendance records for each day of the past 60 days
        for ($day = 0; $day < 60; $day++) {
            $date = $startDate->copy()->addDays($day);
            
            // Skip Saturdays (day 6) and Sundays (day 0)
            if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
                continue;
            }

            // For each student
            foreach ($students as $student) {
                // Each subject gets attendance markings
                foreach ($subjects as $subject) {
                    // Get the assigned teacher for this subject
                    $subjectTeacher = SubjectTeacher::where('subject_id', $subject->id)->first();
                    
                    if (!$subjectTeacher) {
                        continue;
                    }

                    // Determine attendance for this day
                    // 80% present/late, 15% absent, 5% leave/excused
                    $rand = rand(1, 100);
                    if ($rand <= 80) {
                        $status = rand(1, 10) <= 8 ? 'present' : 'late';
                    } elseif ($rand <= 95) {
                        $status = 'absent';
                    } else {
                        $status = rand(1, 2) === 1 ? 'leave' : 'excused';
                    }

                    // 70% class, 30% lab attendance
                    $attendanceType = rand(1, 10) <= 7 ? 'class' : 'lab';

                    Attendance::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'date' => $date,
                            'attendance_type' => $attendanceType,
                        ],
                        [
                            'student_id' => $student->id,
                            'teacher_id' => $subjectTeacher->teacher->user_id,
                            'subject_id' => $subject->id,
                            'date' => $date,
                            'date_bs' => $date,
                            'attendance_type' => $attendanceType,
                            'academic_year' => '2080-2081',
                            'academic_year_bs' => '2080-2081',
                            'status' => $status,
                            'remarks' => ucfirst($status) . ' in ' . $subject->subject_name . ' (' . strtoupper($attendanceType) . ')',
                        ]
                    );
                }
            }
        }
    }
}

