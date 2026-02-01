<?php
/**
 * Script to check the notices table data in the database
 * Run via: php check_notices.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== NOTICES TABLE DATA CHECK ===\n\n";

// 1. Check if table exists
echo "1. Checking if 'notices' table exists...\n";
if (Schema::hasTable('notices')) {
    echo "   ✓ Table 'notices' exists\n\n";
} else {
    echo "   ✗ Table 'notices' does not exist!\n\n";
    exit;
}

// 2. Get table columns
echo "2. Table structure:\n";
$columns = Schema::getColumnListing('notices');
echo "   Columns: " . implode(', ', $columns) . "\n\n";

// 3. Check column existence
echo "3. Required columns check:\n";
$requiredColumns = ['title', 'message', 'audience', 'status', 'semester', 'is_important', 'published_at', 'created_by', 'subject_id', 'file_path', 'file_name'];
foreach ($requiredColumns as $col) {
    $exists = Schema::hasColumn('notices', $col);
    echo "   " . ($exists ? '✓' : '✗') . " {$col}\n";
}
echo "\n";

// 4. Get all notices
echo "4. Total notices in database: " . DB::table('notices')->count() . "\n\n";

// 5. Notices by status
echo "5. Notices by status:\n";
$statusCounts = DB::table('notices')
    ->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();
foreach ($statusCounts as $row) {
    echo "   - {$row->status}: {$row->count}\n";
}
echo "\n";

// 6. Notices by audience
echo "6. Notices by audience:\n";
$audienceCounts = DB::table('notices')
    ->select('audience', DB::raw('count(*) as count'))
    ->groupBy('audience')
    ->get();
foreach ($audienceCounts as $row) {
    echo "   - {$row->audience}: {$row->count}\n";
}
echo "\n";

// 7. Important notices
echo "7. Important notices: " . DB::table('notices')->where('is_important', true)->count() . "\n\n";

// 8. All notices detail
echo "8. All notices detail:\n";
$notices = DB::table('notices')
    ->leftJoin('users', 'notices.created_by', '=', 'users.id')
    ->leftJoin('subjects', 'notices.subject_id', '=', 'subjects.id')
    ->select(
        'notices.id',
        'notices.title',
        'notices.message',
        'notices.audience',
        'notices.status',
        'notices.semester',
        'notices.is_important',
        'notices.published_at',
        'notices.created_at',
        'notices.updated_at',
        'users.name as creator_name',
        'subjects.subject_name',
        'subjects.subject_code'
    )
    ->orderBy('notices.id')
    ->get();

if ($notices->count() === 0) {
    echo "   No notices found in the database.\n";
} else {
    foreach ($notices as $notice) {
        echo "\n   --- Notice #{$notice->id} ---\n";
        echo "   Title: {$notice->title}\n";
        echo "   Message: " . substr($notice->message, 0, 80) . (strlen($notice->message) > 80 ? '...' : '') . "\n";
        echo "   Audience: {$notice->audience}\n";
        echo "   Status: {$notice->status}\n";
        echo "   Semester: " . ($notice->semester ?: 'All') . "\n";
        echo "   Important: " . ($notice->is_important ? 'Yes' : 'No') . "\n";
        echo "   Published At: " . ($notice->published_at ?: 'Not published') . "\n";
        echo "   Created By: " . ($notice->creator_name ?: 'Unknown') . "\n";
        echo "   Subject: " . ($notice->subject_name ? "{$notice->subject_name} ({$notice->subject_code})" : 'None') . "\n";
        echo "   Created At: {$notice->created_at}\n";
        echo "   Updated At: {$notice->updated_at}\n";
    }
}

echo "\n=== CHECK COMPLETE ===\n";

