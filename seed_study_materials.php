<?php
/**
 * Seed study materials using existing table structure
 * Valid document_type values: lecture_notes, assignment, lab_report, assessment, study_guide, syllabus, project_material
 * Valid visibility values: students, teachers, private
 */

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    die("Database file not found!\n");
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check table structure
    echo "Table columns:\n";
    $stmt = $pdo->query("PRAGMA table_info(study_materials)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "  - {$col['name']}: {$col['type']}\n";
    }
    
    // Add missing columns
    $columnNames = array_column($columns, 'name');
    
    $migrations = [];
    if (!in_array('semester', $columnNames)) {
        $migrations[] = "ALTER TABLE study_materials ADD COLUMN semester VARCHAR(10) DEFAULT '1'";
    }
    if (!in_array('category', $columnNames)) {
        $migrations[] = "ALTER TABLE study_materials ADD COLUMN category VARCHAR(50) DEFAULT 'notes'";
    }
    
    foreach ($migrations as $sql) {
        echo "Running: $sql\n";
        $pdo->exec($sql);
    }
    
    // Clear existing data
    $pdo->exec("DELETE FROM study_materials");
    echo "Cleared existing data.\n";
    
    // Insert sample data using EXACT column names from the table
    // Using valid document_type and visibility values from migration
    $materials = [
        [
            'title' => 'Introduction to Programming - Lecture Notes',
            'description' => 'Basic programming concepts including variables, loops, and functions using C language.',
            'semester' => '1',
            'subject_id' => 1,
            'teacher_id' => 1,
            'document_type' => 'lecture_notes',
            'file_name' => 'programming_intro_notes.pdf',
            'file_path' => 'study-materials/programming_intro_notes.pdf',
            'file_size' => 1024000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Data Structures - Assignment',
            'description' => 'Assignment on linked lists and binary trees implementation.',
            'semester' => '2',
            'subject_id' => 2,
            'teacher_id' => 1,
            'document_type' => 'assignment',
            'file_name' => 'data_structures_assignment.pdf',
            'file_path' => 'study-materials/data_structures_assignment.pdf',
            'file_size' => 512000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Database Management Systems - Previous Year Paper 2024',
            'description' => 'End semester examination paper for DBMS course.',
            'semester' => '3',
            'subject_id' => 3,
            'teacher_id' => 1,
            'document_type' => 'assessment',
            'file_name' => 'dbms_paper_2024.pdf',
            'file_path' => 'study-materials/dbms_paper_2024.pdf',
            'file_size' => 2048000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Object Oriented Programming - Lecture Notes',
            'description' => 'Complete notes on OOP concepts including inheritance, polymorphism, and encapsulation.',
            'semester' => '2',
            'subject_id' => 2,
            'teacher_id' => 1,
            'document_type' => 'lecture_notes',
            'file_name' => 'oop_notes.pdf',
            'file_path' => 'study-materials/oop_notes.pdf',
            'file_size' => 1536000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Web Development - Assignment',
            'description' => 'Create a responsive website using HTML, CSS, and JavaScript.',
            'semester' => '4',
            'subject_id' => 4,
            'teacher_id' => 1,
            'document_type' => 'assignment',
            'file_name' => 'web_dev_assignment.pdf',
            'file_path' => 'study-materials/web_dev_assignment.pdf',
            'file_size' => 768000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Computer Networks - Assessment Paper 2023',
            'description' => 'Mid-term examination paper for Computer Networks.',
            'semester' => '5',
            'subject_id' => 5,
            'teacher_id' => 1,
            'document_type' => 'assessment',
            'file_name' => 'networks_paper_2023.pdf',
            'file_path' => 'study-materials/networks_paper_2023.pdf',
            'file_size' => 1280000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Software Engineering - Syllabus',
            'description' => 'Complete syllabus for Software Engineering course.',
            'semester' => '4',
            'subject_id' => 4,
            'teacher_id' => 1,
            'document_type' => 'syllabus',
            'file_name' => 'se_syllabus.pdf',
            'file_path' => 'study-materials/se_syllabus.pdf',
            'file_size' => 256000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Operating Systems - Study Guide',
            'description' => 'Study guide on process management and memory management.',
            'semester' => '3',
            'subject_id' => 3,
            'teacher_id' => 1,
            'document_type' => 'study_guide',
            'file_name' => 'os_study_guide.pdf',
            'file_path' => 'study-materials/os_study_guide.pdf',
            'file_size' => 512000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Computer Networks - Lab Report',
            'description' => 'Lab report on network configuration and troubleshooting.',
            'semester' => '5',
            'subject_id' => 5,
            'teacher_id' => 1,
            'document_type' => 'lab_report',
            'file_name' => 'networks_lab_report.pdf',
            'file_path' => 'study-materials/networks_lab_report.pdf',
            'file_size' => 768000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
        [
            'title' => 'Web Development - Project Material',
            'description' => 'Sample project code and documentation.',
            'semester' => '4',
            'subject_id' => 4,
            'teacher_id' => 1,
            'document_type' => 'project_material',
            'file_name' => 'web_project.zip',
            'file_path' => 'study-materials/web_project.zip',
            'file_size' => 2048000,
            'visibility' => 'students',
            'is_published' => 1,
        ],
    ];
    
    $now = date('Y-m-d H:i:s');
    $uploadedAt = date('Y-m-d H:i:s');
    
    $stmt = $pdo->prepare("
        INSERT INTO study_materials 
        (title, description, semester, subject_id, teacher_id, document_type, file_name, file_path, file_size, visibility, is_published, uploaded_at, created_at, updated_at)
        VALUES 
        (:title, :description, :semester, :subject_id, :teacher_id, :document_type, :file_name, :file_path, :file_size, :visibility, :is_published, :uploaded_at, :created_at, :updated_at)
    ");
    
    foreach ($materials as $material) {
        $material['created_at'] = $now;
        $material['updated_at'] = $now;
        $material['uploaded_at'] = $uploadedAt;
        $stmt->execute($material);
        echo "Added: {$material['title']}\n";
    }
    
    // Get count
    $count = $pdo->query("SELECT COUNT(*) FROM study_materials")->fetchColumn();
    echo "\n✅ Successfully added $count study materials!\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
