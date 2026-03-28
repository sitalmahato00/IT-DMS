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
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('document_type', [
                'lecture_notes', 
                'assignment', 
                'lab_report', 
                'assessment', 
                'study_guide', 
                'syllabus', 
                'project_material'
            ])->default('lecture_notes');
            $table->string('title', 255);
            $table->string('title_ne', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ne')->nullable();
            $table->string('semester', 20)->nullable();
            $table->enum('visibility', ['all', 'students', 'faculty'])->default('all');
            $table->boolean('is_published')->default(true);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['subject_id', 'document_type']);
            $table->index(['teacher_id', 'is_published']);
            $table->index(['semester', 'is_published']);
            $table->index(['document_type', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};

