<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Exam;

class ExamMarkSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $exams = Exam::with('subject')->get();

        foreach ($exams as $exam) {
            $subjectId = $exam->subject_id;
            if (!$subjectId) {
                continue;
            }

            foreach ($students as $student) {
                $base = [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'academic_year' => $exam->academic_year,
                    'academic_year_bs' => $exam->academic_year_bs,
                    'full_marks' => $exam->full_marks ?? 0,
                    'passing_marks' => $exam->passing_marks ?? 0,
                    'assessment_number' => $exam->exam_category === 'assessment' ? $exam->assessment_number : null,
                    'marks_status' => 'filled',
                ];

                if ($exam->exam_category === 'ctevt') {
                    $tiMax = $exam->theory_internal_max_marks ?? 0;
                    $teMax = $exam->theory_external_max_marks ?? 0;
                    $piMax = $exam->practical_internal_max_marks ?? 0;
                    $peMax = $exam->practical_external_max_marks ?? 0;

                    $ti = $tiMax > 0 ? rand(0, $tiMax) : 0;
                    $te = $teMax > 0 ? rand(0, $teMax) : 0;
                    $pi = $piMax > 0 ? rand(0, $piMax) : 0;
                    $pe = $peMax > 0 ? rand(0, $peMax) : 0;
                    $total = $ti + $te + $pi + $pe;

                    $full = $tiMax + $teMax + $piMax + $peMax;
                    $pass = ($exam->theory_internal_pass_marks ?? 0) +
                            ($exam->theory_external_pass_marks ?? 0) +
                            ($exam->practical_internal_pass_marks ?? 0) +
                            ($exam->practical_external_pass_marks ?? 0);

                    $data = array_merge($base, [
                        'theory_internal_marks' => $ti,
                        'theory_external_marks' => $te,
                        'practical_internal_marks' => $pi,
                        'practical_external_marks' => $pe,
                        'theory_internal_full_marks' => $tiMax,
                        'theory_external_full_marks' => $teMax,
                        'practical_internal_full_marks' => $piMax,
                        'practical_external_full_marks' => $peMax,
                        'theory_internal_pass_marks' => $exam->theory_internal_pass_marks ?? 0,
                        'theory_external_pass_marks' => $exam->theory_external_pass_marks ?? 0,
                        'practical_internal_pass_marks' => $exam->practical_internal_pass_marks ?? 0,
                        'practical_external_pass_marks' => $exam->practical_external_pass_marks ?? 0,
                        'marks_obtained' => $total,
                        'full_marks' => $full,
                        'passing_marks' => $pass,
                    ]);

                    $data['percentage'] = $full > 0 ? round(($total / $full) * 100, 2) : 0;
                    $data['grade'] = $data['percentage'] >= 80 ? 'A' : ($data['percentage'] >= 60 ? 'B' : ($data['percentage'] >= 40 ? 'C' : 'F'));

                    ExamMark::create($data);
                    continue;
                }

                $full = (float) ($base['full_marks'] ?? 0);
                $obtained = $full > 0 ? rand(0, (int) $full) : 0;
                $percentage = $full > 0 ? round(($obtained / $full) * 100, 2) : 0;

                ExamMark::create(array_merge($base, [
                    'marks_obtained' => $obtained,
                    'percentage' => $percentage,
                    'grade' => $percentage >= 80 ? 'A' : ($percentage >= 60 ? 'B' : ($percentage >= 40 ? 'C' : 'F')),
                ]));
            }
        }
    }
}
