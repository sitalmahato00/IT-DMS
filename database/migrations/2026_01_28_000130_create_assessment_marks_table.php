<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('marks_obtained')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('teachers')->onDelete('set null');
            $table->timestamps();
            $table->unique(['assessment_id','subject_id','student_id'], 'assessment_marks_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_marks');
    }
};
