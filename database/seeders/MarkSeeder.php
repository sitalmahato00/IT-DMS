<?php

namespace Database\Seeders;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use Illuminate\Database\Seeder;

class MarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::take(5)->get(); // Limit to 5 students
        $subjects = Subject::all();

        // Only create 2 exam types per student-subject
        $examTypes = ['assignment', 'quiz'];

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                // Get assigned teacher for this subject
                $subjectTeacher = SubjectTeacher::where('subject_id', $subject->id)->first();
                
                if (!$subjectTeacher) {
                    continue;
                }

                // Create marks for each exam type
                foreach ($examTypes as $idx => $examType) {
                    $baseMarks = match($examType) {
                        'assignment' => rand(60, 100),
                        'quiz' => rand(40, 90),
                        default => rand(50, 100),
                    };

                    $fullMarks = match($examType) {
                        'assignment' => 10,
                        'quiz' => 10,
                        default => 10,
                    };

                    $marksObtained = round(($baseMarks / 100) * $fullMarks, 2);

                    Mark::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'exam_type' => $examType,
                        ],
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $subjectTeacher->teacher->user_id,
                            'exam_type' => $examType,
                            'academic_year' => '2080-2081',
                            'academic_year_bs' => '2080-2081',
                            'marks_obtained' => $marksObtained,
                            'full_marks' => $fullMarks,
                            'date' => now()->subDays(rand(1, 30)),
                            'remarks' => ucfirst($examType) . ' marks for ' . $subject->subject_name . ' - Good performance',
                        ]
                    );
                }
            }
        }
    }
}
