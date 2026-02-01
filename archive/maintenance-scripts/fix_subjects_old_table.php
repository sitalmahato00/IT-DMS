<?php
/**
 * Script to fix the "no such table: main.subjects_old" error
 * This creates the missing subjects_old table if it doesn't exist
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Check if subjects_old table exists
    $exists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='subjects_old'");
    
    if (empty($exists)) {
        // Create the subjects_old table with basic structure
        DB::statement('CREATE TABLE IF NOT EXISTS subjects_old (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            code TEXT,
            created_at DATETIME,
            updated_at DATETIME
        )');
        echo "Created subjects_old table successfully.\n";
    } else {
        echo "subjects_old table already exists.\n";
    }
    
    echo "Database fix complete. The delete operation should now work.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
