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
        $students = Student::all();
        $subjects = Subject::all();

        $examTypes = ['assignment', 'project', 'quiz', 'presentation'];

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                // Get assigned teacher for this subject
                $subjectTeacher = SubjectTeacher::where('subject_id', $subject->id)->first();
                
                if (!$subjectTeacher) {
                    continue;
                }

                // Create multiple marks for each exam type
                foreach ($examTypes as $idx => $examType) {
                    // Vary marks based on type and student
                    $baseMarks = match($examType) {
                        'assignment' => rand(60, 100),  // Assignments tend to have higher marks
                        'project' => rand(50, 95),       // Projects vary more
                        'quiz' => rand(40, 90),          // Quizzes can vary
                        'presentation' => rand(55, 95),  // Presentations vary
                        default => rand(50, 100),
                    };

                    $fullMarks = match($examType) {
                        'assignment' => 10,
                        'project' => 20,
                        'quiz' => 10,
                        'presentation' => 15,
                        default => 10,
                    };

                    // Scale obtained marks to full marks
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
                            'remarks' => ucfirst($examType) . ' marks for ' . $subject->subject_name . ' - Excellent work',
                        ]
                    );
                }
            }
        }
    }
}
