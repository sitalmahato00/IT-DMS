<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Semester 1
            ['subject_name' => 'Computer Fundamentals', 'subject_code' => 'CF101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Programming Fundamentals', 'subject_code' => 'PF102', 'semester' => '1', 'credits' => '4', 'subject_type' => 'core'],
            ['subject_name' => 'Mathematics I', 'subject_code' => 'M101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Communication English', 'subject_code' => 'CE101', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Digital Logic', 'subject_code' => 'DL103', 'semester' => '1', 'credits' => '3', 'subject_type' => 'core'],
            
            // Semester 2
            ['subject_name' => 'Object Oriented Programming', 'subject_code' => 'OOP201', 'semester' => '2', 'credits' => '4', 'subject_type' => 'core'],
            ['subject_name' => 'Data Structures & Algorithm', 'subject_code' => 'DSA202', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Mathematics II', 'subject_code' => 'M201', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Computer Organization', 'subject_code' => 'CO204', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Web Technology I', 'subject_code' => 'WT205', 'semester' => '2', 'credits' => '3', 'subject_type' => 'core'],
            
            // Add more for 3-8 semesters: 24 total
            ['subject_name' => 'Database Management System', 'subject_code' => 'DBS301', 'semester' => '3', 'credits' => '4', 'subject_type' => 'core'],
            ['subject_name' => 'Software Engineering', 'subject_code' => 'SE302', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Microprocessor', 'subject_code' => 'MP303', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Web Technology II', 'subject_code' => 'WT304', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Statistics', 'subject_code' => 'ST305', 'semester' => '3', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Computer Network', 'subject_code' => 'CN401', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Operating System', 'subject_code' => 'OS402', 'semester' => '4', 'credits' => '3', 'subject_type' => 'core'],
            ['subject_name' => 'Artificial Intelligence', 'subject_code' => 'AI403', 'semester' => '4', 'credits' => '3', 'subject_type' => 'elective'],
            // ... 8 more for higher semesters
            ['subject_name' => 'Machine Learning', 'subject_code' => 'ML601', 'semester' => '6', 'credits' => '3', 'subject_type' => 'elective'],
            ['subject_name' => 'Cloud Computing', 'subject_code' => 'CC701', 'semester' => '7', 'credits' => '3', 'subject_type' => 'elective'],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['subject_code' => $subject['subject_code']],
                array_merge($subject, ['status' => 'active'])
            );
        }
    }
}

