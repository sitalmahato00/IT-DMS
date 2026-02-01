<?php
/**
 * Fix script to update the status column to accept 'archived' value
 * SQLite doesn't support ALTER TABLE for CHECK constraints, so we recreate the table
 */

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    die("Database file not found at: $dbPath\n");
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n\n";
    
    // Get current table structure
    $stmt = $pdo->query("PRAGMA table_info(subjects)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current columns in subjects table:\n";
    foreach ($columns as $col) {
        echo "  - {$col['name']} ({$col['type']})\n";
    }
    echo "\n";
    
    // Get all existing data
    $stmt = $pdo->query("SELECT * FROM subjects");
    $existingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($existingData) . " records.\n\n";
    
    // Get the CREATE TABLE SQL to understand the full structure
    $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='subjects'");
    $createSql = $stmt->fetchColumn();
    echo "Original CREATE TABLE SQL:\n$createSql\n\n";
    
    // Check if 'archived' is already allowed
    if (strpos($createSql, 'archived') !== false) {
        echo "Status column already accepts 'archived'. No fix needed.\n";
        exit(0);
    }
    
    // Get all column names
    $colNames = array_column($columns, 'name');
    $colPlaceholders = array_map(fn($c) => ":$c", $colNames);
    
    // Begin transaction
    $pdo->beginTransaction();
    
    echo "Rebuilding table with updated status constraint...\n";
    
    // Rename old table
    $pdo->exec("ALTER TABLE subjects RENAME TO subjects_old");
    
    // Create new table with updated CHECK constraint
    $pdo->exec("
        CREATE TABLE subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject_name VARCHAR(100) NOT NULL,
            subject_code VARCHAR(20) NOT NULL,
            semester VARCHAR(20),
            teacher_id INTEGER,
            credits INTEGER DEFAULT 3,
            status VARCHAR(20) DEFAULT 'active' CHECK(status IN ('active', 'inactive', 'archived')),
            category VARCHAR(100),
            description TEXT,
            syllabus TEXT,
            learning_objectives TEXT,
            theory_percentage INTEGER DEFAULT 70,
            practical_percentage INTEGER DEFAULT 30,
            internal_percentage INTEGER DEFAULT 40,
            external_percentage INTEGER DEFAULT 60,
            lecture_hours INTEGER DEFAULT 4,
            practical_hours INTEGER DEFAULT 2,
            tutorial_hours INTEGER DEFAULT 1,
            prerequisite VARCHAR(255),
            start_date DATE,
            end_date DATE,
            remarks TEXT,
            created_at DATETIME,
            updated_at DATETIME,
            FOREIGN KEY (teacher_id) REFERENCES teachers(id)
        )
    ");
    
    // Copy data, ensuring status values are valid
    foreach ($existingData as $row) {
        $status = $row['status'] ?? 'active';
        // Ensure status is one of the allowed values
        if (!in_array($status, ['active', 'inactive', 'archived'])) {
            $status = 'active';
        }
        
        $cols = array_keys($row);
        $placeholders = array_map(fn($c) => ":$c", $cols);
        
        $sql = "INSERT INTO subjects (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);
        
        // Replace any invalid status values before binding
        $boundRow = $row;
        if (isset($boundRow['status']) && !in_array($boundRow['status'], ['active', 'inactive', 'archived'])) {
            $boundRow['status'] = 'active';
        }
        
        $stmt->execute($boundRow);
    }
    
    // Drop old table
    $pdo->exec("DROP TABLE subjects_old");
    
    // Commit transaction
    $pdo->commit();
    
    echo "\nSuccessfully updated!\n";
    
    // Verify the change
    $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='subjects'");
    $newCreateSql = $stmt->fetchColumn();
    echo "\nNew CREATE TABLE SQL:\n$newCreateSql\n\n";
    
    // Test inserting with 'archived' status
    $pdo->exec("INSERT INTO subjects (subject_name, subject_code, status) VALUES ('Test', 'TEST001', 'archived')");
    $pdo->exec("DELETE FROM subjects WHERE subject_code = 'TEST001'");
    echo "Verified: 'archived' status can now be inserted.\n";
    
    echo "\nDone! The status column now accepts: active, inactive, archived\n";
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database error: " . $e->getMessage() . "\n");
}

