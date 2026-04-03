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
        $students = Student::with([
            'subjects.teacherAssignments.teacher.user',
        ])->get();

        if ($students->isEmpty()) {
            $this->command?->warn('AttendanceSeeder skipped: no students found.');
            return;
        }

        $fallbackSubjectsBySemester = Subject::query()
            ->whereIn('subject_type', ['core', 'mandatory'])
            ->where('status', 'active')
            ->with(['teacherAssignments.teacher.user'])
            ->get()
            ->groupBy(fn ($subject) => (string) ($subject->semester ?? ''));

        $startDate = now()->subDays(60)->startOfDay();
        $createdCount = 0;

        foreach ($students as $student) {
            $subjects = $student->subjects->isNotEmpty()
                ? $student->subjects
                : collect($fallbackSubjectsBySemester->get((string) ($student->semester ?? ''), []));

            if ($subjects->isEmpty()) {
                continue;
            }

            foreach ($subjects as $subject) {
                $primaryAssignment = $subject->teacherAssignments
                    ->sortBy(fn ($assignment) => $assignment->role === 'primary' ? 0 : 1)
                    ->first();

                $teacherUserId = $primaryAssignment?->teacher?->user_id;

                if (!$teacherUserId && Schema::hasColumn('subjects', 'teacher_id')) {
                    $teacherUserId = $subject->teacher_id ?: null;
                }

                for ($day = 0; $day < 60; $day++) {
                    $date = $startDate->copy()->addDays($day);

                    if (in_array($date->dayOfWeek, [0, 6], true)) {
                        continue;
                    }

                    $rand = random_int(1, 100);
                    if ($rand <= 80) {
                        $status = random_int(1, 10) <= 8 ? 'present' : 'late';
                    } elseif ($rand <= 95) {
                        $status = 'absent';
                    } else {
                        $status = random_int(1, 2) === 1 ? 'leave' : 'excused';
                    }

                    $attendanceType = ($subject->has_lab && random_int(1, 10) <= 3) ? 'lab' : 'class';

                    if ($attendanceType === 'class') {
                        $times = [
                            ['08:00', '09:30'],
                            ['09:45', '11:15'],
                            ['11:30', '13:00'],
                            ['13:45', '15:15'],
                        ];
                        $timeSlot = $times[array_rand($times)];
                    } else {
                        $labStartHour = random_int(8, 14);
                        $timeSlot = [sprintf('%02d:00', $labStartHour), sprintf('%02d:00', $labStartHour + 2)];
                    }

                    $record = Attendance::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'date' => $date->toDateString(),
                            'attendance_type' => $attendanceType,
                        ],
                        [
                            'teacher_id' => $teacherUserId,
                            'date_bs' => NepaliContentHelper::convertAdToBs($date->toDateString()),
                            'time_in' => $timeSlot[0],
                            'time_out' => $timeSlot[1],
                            'academic_year' => $student->academic_year ?: now()->format('Y'),
                            'academic_year_bs' => $student->academic_year_bs ?: '2080-2081',
                            'status' => $status,
                            'remarks' => ucfirst($status) . ' in ' . ($subject->subject_name ?? 'Subject') . ' (' . strtoupper($attendanceType) . ')',
                        ]
                    );

                    if ($record->wasRecentlyCreated) {
                        $createdCount++;
                    }
                }
            }
        }

        $this->command?->info("AttendanceSeeder completed. Newly created records: {$createdCount}");
    }
}
