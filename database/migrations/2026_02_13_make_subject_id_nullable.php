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
        // Recreate the table to make subject_id nullable
        Schema::create('study_materials_new', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->string('document_type');
            $table->string('title');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->text('description')->nullable();
            $table->string('semester', 10)->nullable();
            $table->string('visibility')->default('students');
            $table->boolean('is_published')->default(true);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });

        // Copy data from old table
        \Illuminate\Support\Facades\DB::statement('INSERT INTO study_materials_new (id, subject_id, teacher_id, document_type, title, file_name, file_path, file_size, description, semester, visibility, is_published, uploaded_at, created_at, updated_at) SELECT id, NULL as subject_id, teacher_id, document_type, title, file_name, file_path, file_size, description, semester, visibility, is_published, uploaded_at, created_at, updated_at FROM study_materials');

        // Drop old table
        Schema::dropIfExists('study_materials');

        // Rename new table
        Schema::rename('study_materials_new', 'study_materials');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration, can't easily reverse
    }
};
