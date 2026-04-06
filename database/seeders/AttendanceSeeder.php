<?php

namespace Database\Seeders;

use App\Helpers\NepaliContentHelper;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limit to first 3 students with 20 days of attendance records only
        $students = Student::with([
            'subjects.teacherAssignments.teacher.user',
        ])->take(3)->get();

        if ($students->isEmpty()) {
            $this->command?->warn('AttendanceSeeder skipped: no students found.');
            return;
        }

        $startDate = now()->subDays(20)->startOfDay();
        
        foreach ($students as $student) {
            $subjects = $student->subjects->isNotEmpty()
                ? $student->subjects->take(2)  // Max 2 subjects per student
                : collect([]);

            if ($subjects->isEmpty()) {
                continue;
            }

            foreach ($subjects as $subject) {
                $primaryAssignment = $subject->teacherAssignments
                    ->sortBy(fn ($assignment) => $assignment->role === 'primary' ? 0 : 1)
                    ->first();

                $teacherUserId = $primaryAssignment?->teacher?->user_id;

                // Only create 20 attendance records per student-subject
                for ($day = 0; $day < 20; $day++) {
                    $date = $startDate->copy()->addDays($day);

                    if (in_array($date->dayOfWeek, [0, 6], true)) {
                        continue;
                    }

                    $rand = random_int(1, 100);
                    if ($rand <= 80) {
                        $status = random_int(1, 10) <= 8 ? 'present' : 'late';
                    } else {
                        $status = 'absent';
                    }

                    $attendanceType = $subject->has_lab ? 'class' : 'class';

                    Attendance::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'attendance_date' => $date->format('Y-m-d'),
                            'attendance_type' => $attendanceType,
                        ],
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'semester' => $student->semester,
                            'academic_year' => '2080-2081',
                            'academic_year_bs' => '2080-2081',
                            'attendance_date' => $date->format('Y-m-d'),
                            'attendance_date_bs' => $date->format('Y-m-d'),
                            'attendance_type' => $attendanceType,
                            'status' => $status,
                            'entered_by' => $teacherUserId,
                            'recorded_at' => now(),
                            'remarks' => null,
                        ]
                    );
                }
            }
        }
    }
}
