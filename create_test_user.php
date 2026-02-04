<?php
/**
 * Create a test user for password reset testing
 */
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Check if user exists
$email = 'itstudentstudentsital@gmail.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "User found: {$user->name} (ID: {$user->id})\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
} else {
    // Create the user
    $user = User::create([
        'name' => 'IT Student',
        'email' => $email,
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);
    echo "User created successfully!\n";
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
}
