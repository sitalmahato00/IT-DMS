<?php

namespace Database\Seeders;

use App\Models\BilingualNotice;
use Illuminate\Database\Seeder;

class BilingualNoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notices = [
            [
                'title_en' => 'System Features',
                'title_ne' => 'प्रणाली सुविधाहरु',
                'content_en' => 'Welcome to our comprehensive academic management system with bilingual support',
                'content_ne' => 'हमारो व्यापक शैक्षणिक प्रबन्धन प्रणालीमा स्वागतम',
                'audience' => 'all',
            ],
            [
                'title_en' => 'Academic Excellence',
                'title_ne' => 'शैक्षणिक उत्कृष्टता',
                'content_en' => 'Pursue academic excellence through quality education and guidance',
                'content_ne' => 'गुणस्तरीय शिक्षा र निर्देशनको माध्यमबाट शैक्षणिक उत्कृष्टता अनुसरण गर्नुहोस्',
                'audience' => 'student',
            ],
            [
                'title_en' => 'Student Success',
                'title_ne' => 'विद्यार्थी सफलता',
                'content_en' => 'Support and resources for student success at every step',
                'content_ne' => 'हरेक चरणमा विद्यार्थी सफलताको लागि समर्थन र स्रोतहरु',
                'audience' => 'parent',
            ],
        ];

        foreach ($notices as $notice) {
            BilingualNotice::firstOrCreate(
                ['audience' => $notice['audience'], 'content_en' => $notice['content_en']],
                array_merge($notice, [
                    'is_published' => true,
                    'is_important' => false,
                    'published_date' => now(),
                ])
            );
        }
    }
}
