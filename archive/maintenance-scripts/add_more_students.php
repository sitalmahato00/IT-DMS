<?php
/**
 * Add More Students Script
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Adding More Students ===\n\n";

try {
    // Get existing parent IDs
    $parentIds = Capsule::table('users')->where('role', 'parent')->pluck('id')->toArray();
    echo "Found " . count($parentIds) . " parents\n";

    // Get existing student user IDs
    $existingStudentUserIds = Capsule::table('users')
        ->where('role', 'student')
        ->pluck('id')
        ->toArray();
    echo "Found " . count($existingStudentUserIds) . " student users\n";

    // Get existing student IDs in students table
    $existingStudentIds = Capsule::table('students')->pluck('user_id')->toArray();
    echo "Found " . count($existingStudentIds) . " student records\n";

    // Create additional students
    $newStudentNames = [
        'Benjamin Clark', 'Emma Lewis', 'James Walker', 'Sofia Hall',
        'William Allen', 'Ava Young', 'Lucas King', 'Mia Wright',
        'Ethan Scott', 'Charlotte Green', 'Oliver Adams', 'Amelia Nelson'
    ];

    $created = 0;
    foreach ($newStudentNames as $index => $name) {
        // Check if user already exists
        $email = strtolower(str_replace(' ', '.', $name)) . '@student.dms.com';
        $existingUser = Capsule::table('users')->where('email', $email)->first();

        if ($existingUser) {
            echo "User $email already exists, skipping...\n";
            continue;
        }

        $parentId = $parentIds[$index % count($parentIds)] ?? null;
        $userId = Capsule::table('users')->insertGetId([
            'name' => $name,
            'email' => $email,
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'student',
            'parent_id' => $parentId,
            'semester' => rand(1, 6),
            'created_at' => Carbon::now()->subDays(rand(1, 30)),
            'updated_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);

        Capsule::table('students')->insert([
            'user_id' => $userId,
            'roll_no' => 'STU' . str_pad(count($existingStudentUserIds) + $created + 1, 4, '0', STR_PAD_LEFT),
            'semester' => rand(1, 6),
            'parent_id' => $parentId,
            'created_at' => Carbon::now()->subDays(rand(1, 30)),
            'updated_at' => Carbon::now()->subDays(rand(1, 30)),
        ]);

        $created++;
        echo "Created student: $name ($email)\n";
    }

    if ($created == 0) {
        echo "\nAll additional students already exist!\n";
    } else {
        echo "\nCreated $created new students\n";
    }

    // Final counts
    echo "\n=== Final Counts ===\n";
    echo "Users: " . Capsule::table('users')->count() . "\n";
    echo "Students: " . Capsule::table('students')->count() . "\n";
    echo "Teachers: " . Capsule::table('teachers')->count() . "\n";
    echo "Parents: " . Capsule::table('users')->where('role', 'parent')->count() . "\n";
    echo "Attendance: " . Capsule::table('attendance')->count() . "\n";
    echo "Subjects: " . Capsule::table('subjects')->count() . "\n";
    echo "Notices: " . Capsule::table('notices')->count() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

