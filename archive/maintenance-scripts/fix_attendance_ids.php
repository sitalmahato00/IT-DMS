<?php
/**
 * Script to fix attendance student_id to match students.id (which are formatted like 1004)
 */

$dbPath = __DIR__ . '/database/database.sqlite';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Fixing Attendance Student IDs ===\n\n";
    
    // Get all students with their IDs
    $stmt = $pdo->query("SELECT id, user_id FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Students in database:\n";
    foreach ($students as $student) {
        echo "  students.id: {$student['id']}, user_id: {$student['user_id']}\n";
    }
    echo "\n";
    
    // Get attendance records
    $stmt = $pdo->query("SELECT id, student_id, date, status FROM attendance ORDER BY id");
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current attendance records:\n";
    foreach ($attendanceRecords as $record) {
        echo "  ID: {$record['id']}, student_id: {$record['student_id']}, Date: {$record['date']}, Status: {$record['status']}\n";
    }
    echo "\n";
    
    // Map: old user_id -> new students.id
    // Find which student has which user_id
    $userToStudentId = [];
    foreach ($students as $student) {
        $userToStudentId[$student['user_id']] = $student['id'];
    }
    
    echo "Mapping (user_id -> students.id):\n";
    foreach ($userToStudentId as $userId => $studentId) {
        echo "  user_id: $userId -> students.id: $studentId\n";
    }
    echo "\n";
    
    // Update attendance records
    $fixed = 0;
    foreach ($attendanceRecords as $record) {
        $oldStudentId = $record['student_id'];
        
        // Check if this is a user_id that needs to be converted to students.id
        if (isset($userToStudentId[$oldStudentId])) {
            $newStudentId = $userToStudentId[$oldStudentId];
            
            $stmt = $pdo->prepare("UPDATE attendance SET student_id = ? WHERE id = ?");
            $stmt->execute([$newStudentId, $record['id']]);
            
            echo "Fixed: ID {$record['id']} - student_id changed from $oldStudentId to $newStudentId\n";
            $fixed++;
        } else {
            echo "Skipped: ID {$record['id']} - student_id $oldStudentId not found in students table\n";
        }
    }
    
    echo "\n=== Summary ===\n";
    echo "Total records fixed: $fixed\n";
    
    // Show updated attendance records
    $stmt = $pdo->query("SELECT a.id, a.student_id, a.date, a.status, u.name 
                         FROM attendance a 
                         LEFT JOIN students s ON a.student_id = s.id 
                         LEFT JOIN users u ON s.user_id = u.id 
                         ORDER BY a.id");
    $updatedRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nUpdated attendance records:\n";
    foreach ($updatedRecords as $record) {
        echo "  ID: {$record['id']}, Student ID: {$record['student_id']}, Name: {$record['name']}, Date: {$record['date']}, Status: {$record['status']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

