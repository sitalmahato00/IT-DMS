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
                if ($exam->exam_category === 'ctevt') {
                    // Create CTEVT component marks
                    $tiObtained = rand(8, 20);
                    $teObtained = rand(8, 20);
                    $piObtained = rand(12, 30);
                    $peObtained = rand(12, 30);
                    $totalObtained = $tiObtained + $teObtained + $piObtained + $peObtained;

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
                            'theory_internal_full_marks' => $exam->theory_internal_max_marks,
                            'theory_internal_pass_marks' => $exam->theory_internal_pass_marks,
                            'theory_internal_marks' => $tiObtained,
                            'theory_external_full_marks' => $exam->theory_external_max_marks,
                            'theory_external_pass_marks' => $exam->theory_external_pass_marks,
                            'theory_external_marks' => $teObtained,
                            'practical_internal_full_marks' => $exam->practical_internal_max_marks,
                            'practical_internal_pass_marks' => $exam->practical_internal_pass_marks,
                            'practical_internal_marks' => $piObtained,
                            'practical_external_full_marks' => $exam->practical_external_max_marks,
                            'practical_external_pass_marks' => $exam->practical_external_pass_marks,
                            'practical_external_marks' => $peObtained,
                            'marks_obtained' => $totalObtained,
                            'full_marks' => $exam->full_marks,
                            'passing_marks' => $exam->passing_marks,
                            'marks_status' => 'filled',
                            'percentage' => ($totalObtained / $exam->full_marks) * 100,
                            'grade' => $this->getGrade(($totalObtained / $exam->full_marks) * 100),
                            'graded_by' => $teacher->user_id,
                            'graded_at' => now(),
                        ]
                    );
                } else {
                    // Create assessment category marks (without components)
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
