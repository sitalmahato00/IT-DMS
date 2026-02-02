<?php
// Quick check for teachers table
$pdo = new PDO('sqlite:database/database.sqlite');
$count = $pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
echo "Teachers count: $count\n";

// Check if teachers table has data
if ($count == 0) {
    echo "No teachers found. Adding sample teacher...\n";
    
    // First check if users table has admin user
    $adminUser = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1")->fetchColumn();
    
    if ($adminUser) {
        $pdo->exec("INSERT INTO teachers (user_id, teacher_code, qualification, created_at, updated_at) 
                    VALUES ($adminUser, 'T001', 'M.Sc. Computer Science', datetime('now'), datetime('now'))");
        echo "Added teacher with user_id: $adminUser\n";
    } else {
        // Create a user first
        $pdo->exec("INSERT INTO users (name, email, password, role, created_at, updated_at) 
                    VALUES ('Admin User', 'admin@test.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', datetime('now'), datetime('now'))");
        $userId = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO teachers (user_id, teacher_code, qualification, created_at, updated_at) 
                    VALUES ($userId, 'T001', 'M.Sc. Computer Science', datetime('now'), datetime('now'))");
        echo "Created user and teacher\n";
    }
} else {
    echo "Teachers exist in database\n";
}

