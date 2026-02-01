<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Database Check ===\n\n";

// List all tables
echo "Tables in database:\n";
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
foreach ($tables as $table) {
    echo "  - " . $table->name . "\n";
}

echo "\n=== Attendance Table Structure ===\n";
$columns = DB::select("PRAGMA table_info(attendance)");
echo "Columns:\n";
foreach ($columns as $col) {
    echo "  - {$col->name}: {$col->type} " . ($col->notnull ? 'NOT NULL' : 'NULL') . "\n";
}

echo "\n=== Sample Attendance Records ===\n";
$records = DB::table('attendance')->limit(5)->get();
if ($records->count() > 0) {
    foreach ($records as $record) {
        echo "ID: {$record->id}, Student ID: {$record->student_id}, Date: {$record->date}, Status: {$record->status}\n";
    }
} else {
    echo "No attendance records found.\n";
}

echo "\n=== Users Table Structure ===\n";
$userColumns = DB::select("PRAGMA table_info(users)");
echo "Columns:\n";
foreach ($userColumns as $col) {
    echo "  - {$col->name}: {$col->type}\n";
}

echo "\n=== Students Table Structure ===\n";
$studentColumns = DB::select("PRAGMA table_info(students)");
echo "Columns:\n";
foreach ($studentColumns as $col) {
    echo "  - {$col->name}: {$col->type}\n";
}

echo "\n=== Users Count by Role ===\n";
$roles = DB::table('users')->select('role', DB::raw('count(*) as count'))->groupBy('role')->get();
foreach ($roles as $role) {
    echo "  - {$role->role}: {$role->count}\n";
}

echo "\n=== Students Count ===\n";
$studentCount = DB::table('students')->count();
echo "Total students: {$studentCount}\n";

