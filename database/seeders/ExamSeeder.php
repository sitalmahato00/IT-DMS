<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\User;
use App\Models\Subject;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $subjects = Subject::all();

        foreach ($subjects as $subject) {
            // Assessment exams (Assessment 1..3)
            for ($assessmentNumber = 1; $assessmentNumber <= 3; $assessmentNumber++) {
                Exam::create([
                    'exam_name' => $subject->subject_name . " - Assessment {$assessmentNumber}",
                    'exam_name_ne' => $subject->subject_name . " - मूल्यांकन {$assessmentNumber}",
                    'academic_year' => '2081/082',
                    'academic_year_bs' => '२०८१/०८२',
                    'semester' => (string) $subject->semester,
                    'subject_id' => $subject->id,
                    'exam_type' => 'assessment',
                    'exam_category' => 'assessment',
                    'assessment_number' => $assessmentNumber,
                    'full_marks' => 50,
                    'passing_marks' => 20,
                    'exam_date' => now()->subDays(rand(1, 180)),
                    'exam_date_bs' => '२०८१-१०-१०',
                    'status' => 'published',
                    'created_by' => $admin?->id,
                ]);
            }

            // CTEVT exam (component-based)
            $tiMax = 20; $teMax = 20; $piMax = 5; $peMax = 5;
            $tiPass = 8; $tePass = 8; $piPass = 2; $pePass = 2;
            $ctevtFull = $tiMax + $teMax + $piMax + $peMax;
            $ctevtPass = $tiPass + $tePass + $piPass + $pePass;

            Exam::create([
                'exam_name' => $subject->subject_name . ' - CTEVT',
                'exam_name_ne' => $subject->subject_name . ' - CTEVT',
                'academic_year' => '2081/082',
                'academic_year_bs' => '२०८१/०८२',
                'semester' => (string) $subject->semester,
                'subject_id' => $subject->id,
                'exam_type' => 'internal',
                'exam_category' => 'ctevt',
                'full_marks' => $ctevtFull,
                'passing_marks' => $ctevtPass,
                'theory_internal_max_marks' => $tiMax,
                'theory_external_max_marks' => $teMax,
                'practical_internal_max_marks' => $piMax,
                'practical_external_max_marks' => $peMax,
                'theory_internal_pass_marks' => $tiPass,
                'theory_external_pass_marks' => $tePass,
                'practical_internal_pass_marks' => $piPass,
                'practical_external_pass_marks' => $pePass,
                'exam_date' => now()->subDays(rand(1, 180)),
                'exam_date_bs' => '२०८१-१०-१०',
                'status' => 'published',
                'created_by' => $admin?->id,
            ]);
        }
    }
}
