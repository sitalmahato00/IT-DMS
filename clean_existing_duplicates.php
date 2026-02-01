<?php

/**
 * Script to clean up duplicate attendance records
 * Run this from the command line: php clean_existing_duplicates.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Cleaning up duplicate attendance records...\n\n";

$duplicates = DB::table('attendance')
    ->select('student_id', 'date', DB::raw('COUNT(*) as count'))
    ->groupBy('student_id', 'date')
    ->having('count', '>', 1)
    ->get();

echo "Found " . $duplicates->count() . " student-date combinations with duplicates\n\n";

foreach ($duplicates as $duplicate) {
    echo "Processing student_id: {$duplicate->student_id}, date: {$duplicate->date}, count: {$duplicate->count}\n";
    
    // Get all record IDs for this student/date combination
    $records = DB::table('attendance')
        ->where('student_id', $duplicate->student_id)
        ->where('date', $duplicate->date)
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Keep the first (most recent) record and delete the rest
    $keepId = $records->first()->id;
    
    $deleteCount = $records->slice(1)->count();
    
    DB::table('attendance')
        ->where('student_id', $duplicate->student_id)
        ->where('date', $duplicate->date)
        ->where('id', '!=', $keepId)
        ->delete();
    
    echo "  - Kept ID: {$keepId}, Deleted: {$deleteCount} duplicates\n";
}

echo "\n✅ Cleanup complete!\n";
echo "Total duplicate groups processed: " . $duplicates->count() . "\n";

