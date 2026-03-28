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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name', 200);
            $table->string('subject_name_ne', 200)->nullable();
            $table->string('subject_code', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('description_ne')->nullable();
            $table->string('semester', 20)->nullable();
            $table->string('credits', 10)->default('3');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('category', 50)->nullable();
            $table->text('syllabus')->nullable();
            $table->string('syllabus_document_path', 500)->nullable();
            $table->text('learning_objectives')->nullable();
            $table->integer('practical_full_marks')->nullable();
            $table->integer('practical_pass_marks')->nullable();
            $table->integer('practical_obtained_marks')->nullable();
            $table->integer('theory_percentage')->default(70);
            $table->integer('practical_percentage')->default(30);
            $table->integer('internal_percentage')->default(40);
            $table->integer('external_percentage')->default(60);
            $table->integer('lecture_hours')->default(4);
            $table->integer('practical_hours')->default(2);
            $table->integer('tutorial_hours')->default(1);
            // Elective/optional subject support
            $table->enum('subject_type', ['core', 'elective', 'optional'])->default('core');
            $table->boolean('has_lab')->default(false);
            $table->foreignId('lab_technician_id')->nullable()->index();
            $table->string('lab_document')->nullable();
            $table->integer('max_students')->nullable(); // For elective subjects
            $table->integer('min_students')->nullable();
            $table->boolean('is_elective_open')->default(false);
            $table->string('elective_group', 100)->nullable();
            $table->string('prerequisite', 200)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['semester', 'status']);
            $table->index('subject_code');
            $table->index('subject_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

