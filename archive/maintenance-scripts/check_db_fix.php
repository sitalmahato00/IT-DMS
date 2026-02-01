<?php
/**
 * Script to verify the subjects_old table fix
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Database Tables ===\n";
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
foreach ($tables as $t) {
    echo "  - {$t->name}\n";
}

echo "\n=== Checking subjects_old table ===\n";
$exists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='subjects_old'");
if (empty($exists)) {
    echo "WARNING: subjects_old table does NOT exist!\n";
    echo "This should be created to fix the error.\n";
} else {
    echo "OK: subjects_old table exists.\n";
}

echo "\n=== Checking users table foreign keys ===\n";
$fk = DB::select("PRAGMA foreign_key_list(users)");
if (empty($fk)) {
    echo "No foreign keys on users table.\n";
} else {
    print_r($fk);
}

echo "\n=== Testing delete query (prepared, not executed) ===\n";
try {
    // This would be the problematic query
    $result = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'");
    if ($result) {
        echo "Users table SQL:\n" . $result[0]->sql . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";

