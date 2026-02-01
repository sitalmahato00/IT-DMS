<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Test creating a student
try {
    $user = DB::table('users')->insertGetId([
        'name' => 'Test Student ' . time(),
        'email' => 'test' . time() . '@example.com',
        'password' => Hash::make('password123'),
        'phone' => '1234567890',
        'department' => 'Test Dept',
        'bio' => 'Test bio',
        'role' => 'student',
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "User created with ID: $user\n";

    $student = DB::table('students')->insert([
        'user_id' => $user,
        'roll_no' => 'TEST' . time(),
        'semester' => 1,
        'department' => 'Test Dept',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "Student record created\n";
    echo "Total students now: " . DB::table('users')->where('role', 'student')->count() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

