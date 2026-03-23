<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\Helpers\NepaliContentHelper;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $studentSubjects = \DB::table('subject_students')->select('subject_id', 'student_id')->get()->groupBy('subject_id');
        $subjectTeachers = \App\Models\SubjectTeacher::with('teacher')->get()->groupBy('subject_id');

        $defaultTeacherId = User::where('role', 'teacher')->pluck('id')->all();

        foreach ($studentSubjects as $subjectId => $entries) {
            $subjectTeacher = optional(optional($subjectTeachers->get($subjectId))->first())->teacher;
            $teacherUserId = $subjectTeacher ? $subjectTeacher->user_id : ($defaultTeacherId ? $defaultTeacherId[array_rand($defaultTeacherId)] : null);

            foreach ($entries as $entry) {
                for ($d = 1; $d <= 30; $d++) {
                    $date = Carbon::now()->subDays($d)->toDateString();
                    $status = ['present', 'absent', 'late', 'leave'][array_rand(['present','absent','late','leave'])];

                    Attendance::updateOrCreate(
                        ['student_id' => $entry->student_id, 'subject_id' => $entry->subject_id, 'date' => $date],
                        [
                            'student_id' => $entry->student_id,
                            'subject_id' => $entry->subject_id,
                            'date' => $date,
                            'date_bs' => NepaliContentHelper::convertAdToBs($date) ?? '',
                            'status' => $status,
                            'teacher_id' => $teacherUserId,
                            'remarks' => ucfirst($status),
                        ]
                    );
                }
            }
        }
    }
}

