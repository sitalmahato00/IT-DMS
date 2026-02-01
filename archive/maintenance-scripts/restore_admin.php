<?php
/**
 * Script to restore the accidentally deleted admin user
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Restoring Admin User ===\n\n";

// Check if admin user exists
$admin = DB::table('users')->where('email', 'sitalmahato077@gmail.com')->first();

if ($admin) {
    echo "Admin user already exists!\n";
    echo "ID: {$admin->id}\n";
    echo "Name: {$admin->name}\n";
    echo "Email: {$admin->email}\n";
} else {
    echo "Admin user not found. Restoring...\n";
    
    $id = DB::table('users')->insertGetId([
        'name' => 'sital mahato',
        'email' => 'sitalmahato077@gmail.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'status' => 'active',
        'is_alumni' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Admin user restored with ID: $id\n";
    
    // Also restore password reset token to allow password reset
    DB::table('password_reset_tokens')->insert([
        'email' => 'sitalmahato077@gmail.com',
        'token' => '',
        'created_at' => now(),
    ]);
}

echo "\n=== Done ===\n";

