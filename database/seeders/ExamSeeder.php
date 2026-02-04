<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key constraints for seeding
        DB::statement('PRAGMA foreign_keys = OFF');
        
        $exams = [
            [
                'exam_name' => 'Database 1st Internal',
                'exam_name_ne' => 'डेटाबेस प्रथम आंतरिक',
                'academic_year' => '2024-2025',
                'semester' => 'first',
                'exam_type' => 'internal',
                'full_marks' => 50,
                'passing_marks' => 20,
                'exam_date' => '2024-10-15',
                'exam_date_bs' => '2081-06-29',
                'status' => 'published',
                'description' => 'First internal examination for Database course covering SQL queries and normalization.',
                'description_ne' => 'डेटाबेस कोर्सको लागि SQL क्वेरी र सामान्यीकरण समावेश गर्दै पहिलो आंतरिक परीक्षा।',
                'course_id' => null,
                'subject_id' => null,
                'created_by' => null,
            ],
            [
                'exam_name' => 'Web Development Practical',
                'exam_name_ne' => 'वेब डेवलपमेंट प्रैक्टिकल',
                'academic_year' => '2024-2025',
                'semester' => 'first',
                'exam_type' => 'practical',
                'full_marks' => 50,
                'passing_marks' => 25,
                'exam_date' => '2024-10-20',
                'exam_date_bs' => '2081-07-04',
                'status' => 'published',
                'description' => 'Practical examination for Web Development course.',
                'description_ne' => 'वेब डेवलपमेंट कोर्सको लागि प्रैक्टिकल परीक्षा।',
            ],
            [
                'exam_name' => 'Machine Learning Final',
                'exam_name_ne' => 'मशीन लर्निंग फाइनल',
                'academic_year' => '2023-2024',
                'semester' => 'second',
                'exam_type' => 'final',
                'full_marks' => 100,
                'passing_marks' => 40,
                'exam_date' => '2024-05-10',
                'exam_date_bs' => '2081-01-27',
                'status' => 'published',
                'description' => 'Final examination for Machine Learning course.',
                'description_ne' => 'मशीन लर्निंग कोर्सको अंतिम परीक्षा।',
            ],
            [
                'exam_name' => 'Algorithm Analysis Viva',
                'exam_name_ne' => 'एल्गोरिदम एनालिसिस वाइवा',
                'academic_year' => '2024-2025',
                'semester' => 'first',
                'exam_type' => 'viva',
                'full_marks' => 20,
                'passing_marks' => 10,
                'exam_date' => '2024-11-01',
                'exam_date_bs' => '2081-07-16',
                'status' => 'draft',
                'description' => 'Viva voce examination for Algorithm Analysis.',
                'description_ne' => 'एल्गोरिदम एनालिसिसको लागि मौखिक परीक्षा।',
            ],
            [
                'exam_name' => 'Data Structures Midterm',
                'exam_name_ne' => 'डेटा स्ट्रक्चर्स मिडटर्म',
                'academic_year' => '2024-2025',
                'semester' => 'first',
                'exam_type' => 'midterm',
                'full_marks' => 75,
                'passing_marks' => 30,
                'exam_date' => '2024-09-15',
                'exam_date_bs' => '2081-06-01',
                'status' => 'published',
                'description' => 'Midterm examination for Data Structures.',
                'description_ne' => 'डेटा स्ट्रक्चर्सको मध्यावधि परीक्षा।',
            ],
            [
                'exam_name' => 'Operating Systems Assessment',
                'exam_name_ne' => 'अपरेटिंग सिस्टम्स असेसमेंट',
                'academic_year' => '2024-2025',
                'semester' => 'third',
                'exam_type' => 'assessment',
                'full_marks' => 30,
                'passing_marks' => 12,
                'exam_date' => '2024-10-25',
                'exam_date_bs' => '2081-07-09',
                'status' => 'published',
                'description' => 'Continuous assessment for Operating Systems.',
                'description_ne' => 'अपरेटिंग सिस्टम्सको लागि निरंतर मूल्यांकन।',
            ],
            [
                'exam_name' => 'Computer Networks Assignment',
                'exam_name_ne' => 'कंप्यूटर नेटवर्क्स असाइनमेंट',
                'academic_year' => '2024-2025',
                'semester' => 'second',
                'exam_type' => 'assignment',
                'full_marks' => 20,
                'passing_marks' => 8,
                'exam_date' => '2024-11-10',
                'exam_date_bs' => '2081-07-25',
                'status' => 'draft',
                'description' => 'Assignment for Computer Networks course.',
                'description_ne' => 'कंप्यूटर नेटवर्क्स कोर्सको लागि असाइनमेंट।',
            ],
        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }

        $this->command->info('Exams seeded successfully!');
    }
}

