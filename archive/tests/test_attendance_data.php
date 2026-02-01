<?php
/**
 * Test script to check attendance data
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Attendance Data Check ===\n\n";

try {
    // Check tables exist
    $tables = ['attendance', 'students', 'users', 'subjects'];
    foreach ($tables as $table) {
        $exists = Capsule::schema()->hasTable($table);
        echo "$table table exists: " . ($exists ? 'YES' : 'NO') . "\n";
    }
    echo "\n";

    // Check data counts
    $attendanceCount = Capsule::table('attendance')->count();
    echo "Total attendance records: $attendanceCount\n";

    $studentCount = Capsule::table('students')->count();
    echo "Total students: $studentCount\n";

    $userCount = Capsule::table('users')->count();
    echo "Total users: $userCount\n";

    $studentUserCount = Capsule::table('users')->where('role', 'student')->count();
    echo "Student users (role='student'): $studentUserCount\n\n";

    // Check semesters
    $semesters = Capsule::table('students')
        ->distinct()
        ->pluck('semester')
        ->filter()
        ->values()
        ->all();
    echo "Semesters in students table: " . json_encode($semesters) . "\n\n";

    // Show first few attendance records
    echo "First 5 attendance records:\n";
    $records = Capsule::table('attendance')
        ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
        ->leftJoin('users', 'students.user_id', '=', 'users.id')
        ->select('attendance.id', 'attendance.date', 'students.id as student_id', 'users.name', 'attendance.status')
        ->limit(5)
        ->get();
    
    foreach ($records as $record) {
        echo "  ID: {$record->id}, Date: {$record->date}, StudentID: {$record->student_id}, Name: {$record->name}, Status: {$record->status}\n";
    }
    echo "\n";

    // Test the actual query used in the controller
    echo "Testing controller query (with role filter):\n";
    $testRecords = Capsule::table('attendance')
        ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
        ->leftJoin('users', 'students.user_id', '=', 'users.id')
        ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
        ->where('users.role', 'student')
        ->select(
            'attendance.id',
            'attendance.student_id',
            'attendance.status',
            'attendance.date',
            'users.name',
            'users.email',
            'students.roll_no',
            'students.semester'
        )
        ->limit(5)
        ->get();
    
    echo "Records returned: " . count($testRecords) . "\n";
    foreach ($testRecords as $record) {
        echo "  ID: {$record->id}, Name: {$record->name}, Date: {$record->date}, Semester: {$record->semester}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== Check Complete ===\n";
exit(0);
