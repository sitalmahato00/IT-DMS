<?php
/**
 * Script to directly insert notices into the database
 * Run via: php seed_notices.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SEEDING NOTICES DATA ===\n\n";

// Check if admin user exists (created_by = 1)
$adminExists = DB::table('users')->where('id', 1)->exists();
if (!$adminExists) {
    // Create admin user if not exists
    echo "Creating admin user...\n";
    DB::table('users')->insert([
        'id' => 1,
        'name' => 'Admin',
        'email' => 'admin@test.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'role' => 'admin',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    echo "Admin user created.\n\n";
}

// Check if notices already exist
$noticeCount = DB::table('notices')->count();
if ($noticeCount > 0) {
    echo "Notices already exist ({$noticeCount} records). Skipping seed.\n";
    echo "To re-seed, first delete all notices.\n\n";
    exit;
}

$notices = [
    [
        'title' => 'Final Examination Schedule Released',
        'message' => 'All students are requested to check their exam schedules. The final examination schedule for the current semester has been released. Please note the dates and time slots for your respective subjects. In case of any conflicts, please contact the examination cell immediately.',
        'audience' => 'all',
        'status' => 'published',
        'semester' => null,
        'is_important' => true,
        'published_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
    ],
    [
        'title' => 'Annual Sports Day Event',
        'message' => 'Join us for the annual sports day celebration on campus. Various competitions including cricket, volleyball, basketball, and athletics will be held. All students are encouraged to participate and support their teams.',
        'audience' => 'all',
        'status' => 'published',
        'semester' => null,
        'is_important' => false,
        'published_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
    ],
    [
        'title' => 'Workshop on AI and Machine Learning',
        'message' => 'Faculty members are invited to attend the workshop on AI and Machine Learning. The workshop will cover recent developments in the field and their applications in education. Certificate of participation will be provided.',
        'audience' => 'faculty',
        'status' => 'published',
        'semester' => null,
        'is_important' => false,
        'published_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
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
        'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ],
    [
        'title' => 'Scholarship Application Deadline',
        'message' => 'Last date to apply for merit scholarships is December 31. Students with CGPA above 3.5 are eligible to apply. Submit your applications at the scholarship office.',
        'audience' => 'students',
        'status' => 'scheduled',
        'semester' => null,
        'is_important' => true,
        'published_at' => date('Y-m-d H:i:s', strtotime('+2 days')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ],
    [
        'title' => 'Parent-Teacher Meeting',
        'message' => 'A parent-teacher meeting is scheduled for this Saturday. Parents are requested to attend and discuss their wards academic progress with respective class teachers.',
        'audience' => 'parents',
        'status' => 'published',
        'semester' => '1',
        'is_important' => false,
        'published_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
    ],
    [
        'title' => 'Mid-Term Results Announcement',
        'message' => 'Mid-term results have been announced. Students can check their results through the student portal. Any discrepancies should be reported within 7 days.',
        'audience' => 'all',
        'status' => 'published',
        'semester' => '2',
        'is_important' => true,
        'published_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ],
    [
        'title' => 'Guest Lecture on Cyber Security',
        'message' => 'A guest lecture on Cyber Security and Best Practices will be conducted by industry experts. All IT students are mandatory to attend.',
        'audience' => 'students',
        'status' => 'published',
        'semester' => '3',
        'is_important' => false,
        'published_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
    ],
];

try {
    DB::table('notices')->insert($notices);
    echo "✓ Successfully seeded " . count($notices) . " notices!\n\n";
} catch (Exception $e) {
    echo "✗ Error seeding notices: " . $e->getMessage() . "\n\n";
}

// Verify
echo "Verification:\n";
echo "- Total notices: " . DB::table('notices')->count() . "\n";
echo "- Published: " . DB::table('notices')->where('status', 'published')->count() . "\n";
echo "- Draft: " . DB::table('notices')->where('status', 'draft')->count() . "\n";
echo "- Scheduled: " . DB::table('notices')->where('status', 'scheduled')->count() . "\n";
echo "- Important: " . DB::table('notices')->where('is_important', true)->count() . "\n\n";

echo "=== SEEDING COMPLETE ===\n";

