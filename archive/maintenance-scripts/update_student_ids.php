<?php
/**
 * Script to update students.id to format like 1004, 1002, 1006
 * And update attendance to match
 */

$dbPath = __DIR__ . '/database/database.sqlite';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Updating Students IDs to Formatted IDs ===\n\n";
    
    // Get all students
    $stmt = $pdo->query("SELECT id, user_id, roll_no FROM students ORDER BY id");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current students:\n";
    foreach ($students as $student) {
        echo "  ID: {$student['id']}, user_id: {$student['user_id']}, roll_no: {$student['roll_no']}\n";
    }
    echo "\n";
    
    // Update each student ID to format 10XX
    $pdo->exec("BEGIN TRANSACTION");
    
    $updated = 0;
    foreach ($students as $student) {
        $oldId = $student['id'];
        $newId = 1000 + $oldId; // 2 -> 1002, 4 -> 1004, 6 -> 1006
        
        // Update students table
        $stmt = $pdo->prepare("UPDATE students SET id = ? WHERE id = ?");
        $stmt->execute([$newId, $oldId]);
        
        echo "Updated students: ID $oldId -> $newId\n";
        $updated++;
        
        // Update attendance references
        $stmt = $pdo->prepare("UPDATE attendance SET student_id = ? WHERE student_id = ?");
        $stmt->execute([$newId, $oldId]);
        
        echo "Updated attendance: student_id $oldId -> $newId\n";
    }
    
    $pdo->exec("COMMIT");
    
    echo "\n=== Summary ===\n";
    echo "Total students updated: $updated\n\n";
    
    // Verify updated data
    $stmt = $pdo->query("SELECT s.id, s.user_id, s.roll_no, u.name 
                         FROM students s 
                         LEFT JOIN users u ON s.user_id = u.id 
                         ORDER BY s.id");
    $updatedStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Updated students:\n";
    foreach ($updatedStudents as $student) {
        echo "  ID: {$student['id']}, user_id: {$student['user_id']}, Name: {$student['name']}\n";
    }
    echo "\n";
    
    // Show attendance records
    $stmt = $pdo->query("SELECT a.id, a.student_id, a.date, a.status, u.name 
                         FROM attendance a 
                         LEFT JOIN students s ON a.student_id = s.id 
                         LEFT JOIN users u ON s.user_id = u.id 
                         ORDER BY a.id");
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Attendance records:\n";
    foreach ($attendanceRecords as $record) {
        echo "  ID: {$record['id']}, Student ID: {$record['student_id']}, Name: {$record['name']}, Date: {$record['date']}, Status: {$record['status']}\n";
    }
    
} catch (PDOException $e) {
    $pdo->exec("ROLLBACK");
    echo "Error: " . $e->getMessage() . "\n";
}

