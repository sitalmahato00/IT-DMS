<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to use raw SQL since it doesn't support modifying columns directly
        Schema::table('attendance', function (Blueprint $table) {
            // SQLite requires recreating the table to modify columns
            // So we'll just add nullable subject_id handling
        });

        // Raw SQL for SQLite
        DB::statement('PRAGMA foreign_keys=OFF');
        
        // Recreate table with nullable subject_id
        DB::statement('
            CREATE TABLE IF NOT EXISTS attendance_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                subject_id INTEGER,
                teacher_id INTEGER,
                date DATE NOT NULL,
                status VARCHAR DEFAULT \'present\',
                remarks TEXT,
                created_at DATETIME,
                updated_at DATETIME,
                UNIQUE(student_id, subject_id, date),
                FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
                FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
                FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
            )
        ');

        DB::statement('INSERT INTO attendance_temp SELECT * FROM attendance');
        DB::statement('DROP TABLE attendance');
        DB::statement('ALTER TABLE attendance_temp RENAME TO attendance');
        DB::statement('PRAGMA foreign_keys=ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse migration would need similar complex SQL
    }
};
