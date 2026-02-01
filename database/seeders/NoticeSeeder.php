<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if notices table exists and has the required columns
        if (!Schema::hasTable('notices')) {
            return;
        }

        // Check if audience column exists, if not skip seeding
        if (!Schema::hasColumn('notices', 'audience')) {
            $this->command->warn('Notices table does not have required columns. Skipping seeder.');
            return;
        }

        // Check if there are already notices
        $count = DB::table('notices')->count();
        if ($count > 0) {
            $this->command->info('Notices already exist. Skipping seeder.');
            return;
        }

        $notices = [
            [
                'title' => 'Final Examination Schedule Released',
                'message' => 'All students are requested to check their exam schedules. The final examination schedule for the current semester has been released. Please note the dates and time slots for your respective subjects. In case of any conflicts, please contact the examination cell immediately.',
                'audience' => 'all',
                'status' => 'published',
                'semester' => null,
                'is_important' => true,
                'published_at' => now()->subDays(2),
                'created_by' => 1,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'Annual Sports Day Event',
                'message' => 'Join us for the annual sports day celebration on campus. Various competitions including cricket, volleyball, basketball, and athletics will be held. All students are encouraged to participate and support their teams.',
                'audience' => 'all',
                'status' => 'published',
                'semester' => null,
                'is_important' => false,
                'published_at' => now()->subDays(3),
                'created_by' => 1,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'title' => 'Workshop on AI and Machine Learning',
                'message' => 'Faculty members are invited to attend the workshop on AI and Machine Learning. The workshop will cover recent developments in the field and their applications in education. Certificate of participation will be provided.',
                'audience' => 'faculty',
                'status' => 'published',
                'semester' => null,
                'is_important' => false,
                'published_at' => now()->subDays(5),
                'created_by' => 1,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Library Maintenance Notice',
                'message' => 'The library will be closed for maintenance from December 20-22. During this period, no book issue or return will be available. We apologize for the inconvenience.',
                'audience' => 'all',
                'status' => 'draft',
                'semester' => null,
                'is_important' => false,
                'published_at' => null,
                'created_by' => 1,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(1),
            ],
            [
                'title' => 'Scholarship Application Deadline',
                'message' => 'Last date to apply for merit scholarships is December 31. Students with CGPA above 3.5 are eligible to apply. Submit your applications at the scholarship office.',
                'audience' => 'students',
                'status' => 'scheduled',
                'semester' => null,
                'is_important' => true,
                'published_at' => now()->addDays(2),
                'created_by' => 1,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'title' => 'Parent-Teacher Meeting',
                'message' => 'A parent-teacher meeting is scheduled for this Saturday. Parents are requested to attend and discuss their wards academic progress with respective class teachers.',
                'audience' => 'parents',
                'status' => 'published',
                'semester' => '1',
                'is_important' => false,
                'published_at' => now()->subDays(4),
                'created_by' => 1,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'title' => 'Mid-Term Results Announcement',
                'message' => 'Mid-term results have been announced. Students can check their results through the student portal. Any discrepancies should be reported within 7 days.',
                'audience' => 'all',
                'status' => 'published',
                'semester' => '2',
                'is_important' => true,
                'published_at' => now()->subDays(1),
                'created_by' => 1,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'title' => 'Guest Lecture on Cyber Security',
                'message' => 'A guest lecture on Cyber Security and Best Practices will be conducted by industry experts. All IT students are mandatory to attend.',
                'audience' => 'students',
                'status' => 'published',
                'semester' => '3',
                'is_important' => false,
                'published_at' => now()->subDays(6),
                'created_by' => 1,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
        ];

        DB::table('notices')->insert($notices);

        $this->command->info('8 sample notices seeded successfully!');
    }
}

