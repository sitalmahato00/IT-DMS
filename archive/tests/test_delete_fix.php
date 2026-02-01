<?php
/**
 * Script to test the delete operation after subjects_old table fix
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== Testing Delete Operation ===\n\n";

// First, check if there are any students to delete
echo "Checking for test student (id=28)...\n";
$student = User::find(28);

if (!$student) {
    echo "Student with id=28 not found. Checking for existing students...\n";
    $students = User::where('role', 'student')->limit(5)->get();
    foreach ($students as $s) {
        echo "  - ID: {$s->id}, Name: {$s->name}, Email: {$s->email}\n";
    }
    
    if ($students->isEmpty()) {
        echo "No students found. Creating a test student...\n";
        $testStudent = User::create([
            'name' => 'Test Student ' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
        echo "Created test student with ID: {$testStudent->id}\n";
        
        // Now test delete
        echo "\nTesting delete on new student...\n";
        try {
            $testStudent->delete();
            echo "SUCCESS: Student deleted successfully!\n";
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    } else {
        // Test delete on an existing student (id=28 if it exists, otherwise first student)
        $testId = 28;
        $studentToDelete = User::find($testId);
        
        if (!$studentToDelete) {
            $studentToDelete = $students->first();
            $testId = $studentToDelete->id;
        }
        
        echo "\nTesting delete on student ID: $testId...\n";
        try {
            $studentToDelete->delete();
            echo "SUCCESS: Student deleted successfully!\n";
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "Student found: ID={$student->id}, Name={$student->name}\n";
    
    echo "\nTesting delete...\n";
    try {
        $student->delete();
        echo "SUCCESS: Student deleted successfully!\n";
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Test Complete ===\n";

