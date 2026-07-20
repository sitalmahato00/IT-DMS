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
        $students = Student::take(5)->get(); // Limit to 5 students
        $teachers = Teacher::all();

        foreach ($exams as $exam) {
            foreach ($students as $student) {
                $teacher = $teachers->random();
                $marks = 0;
                $percentage = 0;
                $grade = 'F';
                $markData = [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'subject_id' => $exam->subject_id,
                    'academic_year' => $exam->academic_year,
                    'academic_year_bs' => $exam->academic_year_bs,
                    'full_marks' => $exam->full_marks,
                    'passing_marks' => $exam->passing_marks,
                    'marks_status' => 'filled',
                    'graded_by' => $teacher->user_id,
                    'graded_at' => now(),
                ];

                if ($exam->exam_category === 'ctevt') {
                    $tiMax = max(0, (int) $exam->theory_internal_max_marks);
                    $teMax = max(0, (int) $exam->theory_external_max_marks);
                    $piMax = max(0, (int) $exam->practical_internal_max_marks);
                    $peMax = max(0, (int) $exam->practical_external_max_marks);

                    $tiMarks = $tiMax > 0 ? rand((int) max(0, round($tiMax * 0.4)), $tiMax) : null;
                    $teMarks = $teMax > 0 ? rand((int) max(0, round($teMax * 0.4)), $teMax) : null;
                    $piMarks = $piMax > 0 ? rand((int) max(0, round($piMax * 0.4)), $piMax) : null;
                    $peMarks = $peMax > 0 ? rand((int) max(0, round($peMax * 0.4)), $peMax) : null;

                    $marks = ($tiMarks ?? 0) + ($teMarks ?? 0) + ($piMarks ?? 0) + ($peMarks ?? 0);
                    $fullMarks = $tiMax + $teMax + $piMax + $peMax;
                    $percentage = $fullMarks > 0 ? round(($marks / $fullMarks) * 100, 2) : 0;
                    $grade = $this->getGrade($percentage);

                    $markData = array_merge($markData, [
                        'marks_obtained' => $marks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'theory_internal_marks' => $tiMarks,
                        'theory_external_marks' => $teMarks,
                        'practical_internal_marks' => $piMarks,
                        'practical_external_marks' => $peMarks,
                        'theory_internal_full_marks' => $tiMax,
                        'theory_external_full_marks' => $teMax,
                        'practical_internal_full_marks' => $piMax,
                        'practical_external_full_marks' => $peMax,
                        'theory_internal_pass_marks' => $exam->theory_internal_pass_marks,
                        'theory_external_pass_marks' => $exam->theory_external_pass_marks,
                        'practical_internal_pass_marks' => $exam->practical_internal_pass_marks,
                        'practical_external_pass_marks' => $exam->practical_external_pass_marks,
                    ]);
                } else {
                    $fullMarks = max(1, (int) $exam->full_marks);
                    $marks = $fullMarks <= 40 ? rand(18, $fullMarks) : rand(max(0, (int) round($fullMarks * 0.6)), $fullMarks);
                    $percentage = round(($marks / $fullMarks) * 100, 2);
                    $grade = $this->getGrade($percentage);

                    $markData = array_merge($markData, [
                        'marks_obtained' => $marks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                    ]);
                }

                ExamMark::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'subject_id' => $exam->subject_id,
                    ],
                    $markData
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
