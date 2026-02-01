<?php
/**
 * Script to test the registration functionality
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "=== Testing User Registration ===\n\n";

// Test email
$testEmail = 'testuser' . time() . '@example.com';

echo "Creating test user with email: $testEmail\n";

// Check if email already exists
if (User::where('email', $testEmail)->exists()) {
    echo "Email already exists, using a different one.\n";
    $testEmail = 'testuser' . time() . '2@example.com';
}

try {
    $user = User::create([
        'name' => 'Test User',
        'email' => $testEmail,
        'password' => Hash::make('password123'),
        'role' => 'student',
        'status' => 'active',
    ]);

    echo "SUCCESS: User created!\n";
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
    echo "  Status: {$user->status}\n";

    // Clean up - delete test user
    $user->delete();
    echo "\nTest user deleted successfully.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";

