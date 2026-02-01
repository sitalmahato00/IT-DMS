<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates the attendance table to:
     * 1. Remove subject_id column
     * 2. Keep student_id referencing students table (role-specific ID)
     * 3. Keep teacher_id referencing teachers table (role-specific ID)
     */
    public function up(): void
    {
        // SQLite doesn't support dropping foreign keys directly
        // So we need to recreate the table
        
        // Disable foreign key constraints
        DB::statement('PRAGMA foreign_keys=OFF');
        
        // Create new table with correct structure
        DB::statement('
            CREATE TABLE IF NOT EXISTS attendance_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                teacher_id INTEGER,
                date DATE NOT NULL,
                status VARCHAR DEFAULT "absent",
                remarks TEXT,
                created_at DATETIME,
                updated_at DATETIME,
                UNIQUE(student_id, date),
                FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
                FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
            )
        ');
        
        // Copy data from old table to new table
        // Map old student_id (users.id) to new student_id (students.id)
        DB::statement('
            INSERT INTO attendance_new (id, student_id, teacher_id, date, status, remarks, created_at, updated_at)
            SELECT 
                a.id,
                COALESCE(s.id, a.student_id) as student_id,
                a.teacher_id,
                a.date,
                a.status,
                a.remarks,
                a.created_at,
                a.updated_at
            FROM attendance a
            LEFT JOIN students s ON a.student_id = s.user_id
        ');
        
        // Drop old table
        DB::statement('DROP TABLE attendance');
        
        // Rename new table
        DB::statement('ALTER TABLE attendance_new RENAME TO attendance');
        
        // Re-enable foreign key constraints
        DB::statement('PRAGMA foreign_keys=ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, hard to reverse
        // In production, you would need proper backup before running
    }
};

