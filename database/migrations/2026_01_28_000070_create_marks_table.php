<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->enum('exam_type', ['internal','assignment','lab'])->default('internal');
            $table->integer('marks_obtained')->nullable();
            $table->integer('full_marks')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            $table->unique(['student_id','subject_id','exam_type','date'], 'marks_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
