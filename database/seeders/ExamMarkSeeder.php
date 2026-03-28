<?php

namespace Database\Seeders;

use App\Models\ExamMark;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ExamMarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exams = Exam::all();
        $students = Student::all();
        $teachers = Teacher::all();

        foreach ($exams as $exam) {
            foreach ($students as $student) {
                $marks = rand(20, 39);
                $teacher = $teachers->random();

                ExamMark::firstOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $exam->subject_id,
                    ],
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $exam->subject_id,
                        'academic_year' => $exam->academic_year,
                        'academic_year_bs' => $exam->academic_year_bs,
                        'marks_obtained' => $marks,
                        'full_marks' => $exam->full_marks,
                        'passing_marks' => $exam->passing_marks,
                        'marks_status' => 'filled',
                        'percentage' => ($marks / $exam->full_marks) * 100,
                        'grade' => $this->getGrade(($marks / $exam->full_marks) * 100),
                        'graded_by' => $teacher->user_id,
                        'graded_at' => now(),
                    ]
                );
            }
        }
    }

    private function getGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        return 'F';
    }
}
