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
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->unsignedInteger('assessment_number')->nullable()->index();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('academic_year', 10)->nullable();
            $table->string('academic_year_bs', 10)->nullable();
            $table->decimal('marks_obtained', 5, 2)->default(0);
            $table->enum('marks_status', ['filled', 'partial', 'empty', 'absent'])->nullable()->default('empty');
            $table->decimal('full_marks', 5, 2)->default(100);
            $table->decimal('passing_marks', 5, 2)->default(40);
            $table->decimal('theory_internal_marks', 5, 2)->nullable();
            $table->decimal('theory_external_marks', 5, 2)->nullable();
            $table->decimal('practical_internal_marks', 5, 2)->nullable();
            $table->decimal('practical_external_marks', 5, 2)->nullable();
            $table->decimal('theory_internal_full_marks', 5, 2)->nullable();
            $table->decimal('theory_external_full_marks', 5, 2)->nullable();
            $table->decimal('practical_internal_full_marks', 5, 2)->nullable();
            $table->decimal('practical_external_full_marks', 5, 2)->nullable();
            $table->unsignedInteger('theory_internal_pass_marks')->nullable();
            $table->unsignedInteger('theory_external_pass_marks')->nullable();
            $table->unsignedInteger('practical_internal_pass_marks')->nullable();
            $table->unsignedInteger('practical_external_pass_marks')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('entered_by')->nullable()->index();
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'subject_id'], 'ux_exam_student_subject');
            $table->index(['student_id', 'exam_id'], 'idx_student_exam');
            $table->index(['subject_id', 'exam_id'], 'idx_subject_exam');
            $table->index(['exam_id', 'marks_obtained'], 'idx_exam_marks_obtained');
            $table->foreign('subject_id', 'fk_exam_marks_subject_id')->references('id')->on('subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
