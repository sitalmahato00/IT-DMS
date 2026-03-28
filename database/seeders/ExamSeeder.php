<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();
        $admin = User::where('role', 'admin')->first();

        $examTypes = [
            ['type' => 'internal', 'category' => 'assessment', 'full_marks' => 40],
            ['type' => 'midterm', 'category' => 'assessment', 'full_marks' => 50],
            ['type' => 'final', 'category' => 'assessment', 'full_marks' => 100],
        ];

        foreach ($subjects as $subject) {
            foreach ($examTypes as $index => $examType) {
                Exam::firstOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'exam_type' => $examType['type'],
                        'academic_year' => '2080-2081',
                        'semester' => 5,
                    ],
                    [
                        'exam_name' => $subject->subject_name . ' - ' . ucfirst($examType['type']) . ' Exam',
                        'subject_id' => $subject->id,
                        'exam_type' => $examType['type'],
                        'exam_category' => $examType['category'],
                        'academic_year' => '2080-2081',
                        'academic_year_bs' => '2080-2081',
                        'semester' => 5,
                        'full_marks' => $examType['full_marks'],
                        'passing_marks' => $examType['full_marks'] * 0.4,
                        'exam_date' => now()->addDays($index * 7),
                        'exam_date_bs' => now()->addDays($index * 7),
                        'status' => 'published',
                        'created_by' => $admin?->id,
                        'description' => 'Official ' . $examType['type'] . ' examination for ' . $subject->subject_name,
                    ]
                );
            }
        }
    }
}
