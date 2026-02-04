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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_name', 255);
            $table->string('exam_name_ne', 255)->nullable();
            $table->string('academic_year', 20)->nullable();
            $table->string('semester', 20)->nullable();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('course_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->enum('exam_type', ['internal', 'final', 'midterm', 'practical', 'viva', 'assignment', 'assessment'])->default('internal');
            $table->integer('full_marks')->default(100);
            $table->integer('passing_marks')->default(35);
            $table->date('exam_date')->nullable();
            $table->string('exam_date_bs', 20)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('description')->nullable();
            $table->text('description_ne')->nullable();
            $table->text('instructions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['academic_year', 'semester']);
            $table->index(['exam_type', 'status']);
            $table->index(['subject_id', 'status']);
            $table->index('exam_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

