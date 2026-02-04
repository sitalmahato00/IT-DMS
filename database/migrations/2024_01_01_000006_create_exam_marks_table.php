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
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2)->default(0);
            $table->decimal('full_marks', 5, 2)->default(100);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate entries
            $table->unique(['exam_id', 'student_id']);

            // Indexes
            $table->index(['student_id', 'exam_id']);
            $table->index(['exam_id', 'marks_obtained']);
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

