<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'subject_name' => 'Web Technology',
                'subject_code' => 'CS201',
                'credits' => 3,
                'semester' => 5,
                'description' => 'Modern web development techniques and frameworks',
                'category' => 'Core',
                'subject_type' => 'core',
                'has_lab' => true,
                'lecture_hours' => 3,
                'practical_hours' => 2,
                'status' => 'active',
            ],
            [
                'subject_name' => 'Database Management',
                'subject_code' => 'CS202',
                'credits' => 3,
                'semester' => 5,
                'description' => 'Relational database design and SQL',
                'category' => 'Core',
                'subject_type' => 'core',
                'has_lab' => true,
                'lecture_hours' => 3,
                'practical_hours' => 2,
                'status' => 'active',
            ],
            [
                'subject_name' => 'Advanced Programming',
                'subject_code' => 'CS204',
                'credits' => 4,
                'semester' => 5,
                'description' => 'OOP and advanced programming concepts',
                'category' => 'Core',
                'subject_type' => 'core',
                'has_lab' => true,
                'lecture_hours' => 4,
                'practical_hours' => 2,
                'status' => 'active',
            ],
            [
                'subject_name' => 'Software Engineering',
                'subject_code' => 'CS205',
                'credits' => 3,
                'semester' => 5,
                'description' => 'Software development methodologies and practices',
                'category' => 'Core',
                'subject_type' => 'core',
                'has_lab' => false,
                'lecture_hours' => 3,
                'practical_hours' => 0,
                'status' => 'active',
            ],
            [
                'subject_name' => 'Network Security',
                'subject_code' => 'CS206',
                'credits' => 3,
                'semester' => 5,
                'description' => 'Network security principles and encryption',
                'category' => 'Elective',
                'subject_type' => 'elective',
                'has_lab' => true,
                'lecture_hours' => 3,
                'practical_hours' => 2,
                'status' => 'active',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['subject_code' => $subject['subject_code']],
                array_merge($subject, [
                    'status' => 'active',
                    'theory_percentage' => 70,
                    'practical_percentage' => 30,
                    'internal_percentage' => 40,
                    'external_percentage' => 60,
                ])
            );
        }
    }
}

