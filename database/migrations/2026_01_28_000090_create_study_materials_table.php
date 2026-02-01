<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->enum('document_type', ['lecture_notes','assignment','lab_report','assessment','study_guide','syllabus','project_material']);
            $table->string('title', 255);
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->enum('visibility', ['students','teachers','private'])->default('students');
            $table->boolean('is_published')->default(false);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};
