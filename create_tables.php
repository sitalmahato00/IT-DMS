<?php
// Check which tables exist and create missing ones
$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'teacher', 'student', 'parent') DEFAULT 'student',
        phone VARCHAR(20) NULL,
        address TEXT NULL,
        photo VARCHAR(255) NULL,
        is_active TINYINT(1) DEFAULT 1,
        is_alumni TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    )",
    
    'students' => "CREATE TABLE IF NOT EXISTS students (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NULL,
        roll_no VARCHAR(50) NOT NULL,
        semester VARCHAR(20) DEFAULT 'first',
        department VARCHAR(100) NULL,
        parent_id BIGINT UNSIGNED NULL,
        date_of_birth DATE NULL,
        date_of_birth_bs VARCHAR(20) NULL,
        address TEXT NULL,
        batch_year VARCHAR(10) NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    )",
    
    'subjects' => "CREATE TABLE IF NOT EXISTS subjects (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(200) NOT NULL,
        subject_code VARCHAR(50) NULL,
        subject_name_ne VARCHAR(200) NULL,
        description TEXT NULL,
        course_id BIGINT UNSIGNED NULL,
        semester VARCHAR(20) NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    )",
    
    'courses' => "CREATE TABLE IF NOT EXISTS courses (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(200) NOT NULL,
        subject_code VARCHAR(50) NULL,
        subject_name_ne VARCHAR(200) NULL,
        description TEXT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    )",
    
    'exams' => "CREATE TABLE IF NOT EXISTS exams (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        exam_name VARCHAR(255) NOT NULL,
        exam_name_ne VARCHAR(255) NULL,
        academic_year VARCHAR(20) NOT NULL,
        semester VARCHAR(20) NOT NULL,
        subject_id BIGINT UNSIGNED NULL,
        course_id BIGINT UNSIGNED NULL,
        exam_type ENUM('internal', 'final', 'midterm', 'practical', 'viva', 'assignment', 'assessment') NOT NULL,
        full_marks INT NOT NULL,
        passing_marks INT NOT NULL,
        exam_date DATE NULL,
        exam_date_bs VARCHAR(20) NULL,
        status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
        description TEXT NULL,
        description_ne TEXT NULL,
        instructions TEXT NULL,
        created_by BIGINT UNSIGNED NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        deleted_at TIMESTAMP NULL,
        INDEX idx_academic_semester (academic_year, semester),
        INDEX idx_exam_type_status (exam_type, status),
        INDEX idx_subject_status (subject_id, status)
    )",
    
    'exam_marks' => "CREATE TABLE IF NOT EXISTS exam_marks (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        exam_id BIGINT UNSIGNED NOT NULL,
        student_id BIGINT UNSIGNED NOT NULL,
        marks_obtained DECIMAL(5,2) NOT NULL,
        full_marks DECIMAL(5,2) NOT NULL,
        percentage DECIMAL(5,2) NULL,
        grade VARCHAR(5) NULL,
        remarks TEXT NULL,
        graded_by BIGINT UNSIGNED NULL,
        graded_at TIMESTAMP NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        UNIQUE exam_student_unique (exam_id, student_id),
        INDEX idx_student_exam (student_id, exam_id)
    )"
];

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=IT-DMS', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    foreach ($tables as $name => $sql) {
        try {
            $pdo->exec($sql);
            echo "Table '$name' created or already exists\n";
        } catch (PDOException $e) {
            echo "Error creating '$name': " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nDone!\n";
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage() . "\n";
}
