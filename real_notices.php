<?php
/**
 * Script to query the real notice data from SQLite database directly
 * Run via: php real_notices.php
 */

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    echo "Database file not found: $dbPath\n";
    exit;
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== REAL NOTICE DATA FROM DATABASE ===\n\n";

    // Check if notices table exists
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='notices'")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        echo "Notices table does not exist!\n";
        exit;
    }
    echo "✓ Notices table exists\n\n";

    // Get all columns
    $columns = $pdo->query("PRAGMA table_info(notices)")->fetchAll(PDO::FETCH_ASSOC);
    echo "Table Structure:\n";
    foreach ($columns as $col) {
        echo "  - {$col['name']} ({$col['type']})" . ($col['pk'] ? ' [PRIMARY KEY]' : '') . "\n";
    }
    echo "\n";

    // Get all notices
    $stmt = $pdo->query("SELECT * FROM notices ORDER BY id");
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Total notices: " . count($notices) . "\n\n";

    if (count($notices) === 0) {
        echo "No notices found in the database.\n";
        exit;
    }

    // Display each notice
    foreach ($notices as $notice) {
        echo "--- Notice #{$notice['id']} ---\n";
        echo "Title: {$notice['title']}\n";
        echo "Message: " . substr($notice['message'], 0, 100) . (strlen($notice['message']) > 100 ? '...' : '') . "\n";
        echo "Audience: {$notice['audience']}\n";
        echo "Status: {$notice['status']}\n";
        echo "Semester: " . ($notice['semester'] ?: 'NULL') . "\n";
        echo "Is Important: " . ($notice['is_important'] ? 'Yes' : 'No') . "\n";
        echo "Published At: " . ($notice['published_at'] ?: 'NULL') . "\n";
        echo "Created By: {$notice['created_by']}\n";
        echo "Subject ID: " . ($notice['subject_id'] ?: 'NULL') . "\n";
        echo "File Path: " . ($notice['file_path'] ?: 'NULL') . "\n";
        echo "File Name: " . ($notice['file_name'] ?: 'NULL') . "\n";
        echo "Created At: {$notice['created_at']}\n";
        echo "Updated At: {$notice['updated_at']}\n";
        echo "\n";
    }

    // Summary statistics
    echo "=== SUMMARY ===\n";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM notices GROUP BY status");
    echo "By Status:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['status']}: {$row['count']}\n";
    }

    $stmt = $pdo->query("SELECT audience, COUNT(*) as count FROM notices GROUP BY audience");
    echo "By Audience:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['audience']}: {$row['count']}\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM notices WHERE is_important = 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Important Notices: {$row['count']}\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

