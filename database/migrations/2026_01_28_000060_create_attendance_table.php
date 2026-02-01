<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->date('date');
            $table->enum('status', ['present','absent'])->default('present');
            $table->timestamps();
            $table->unique(['student_id','subject_id','date'], 'attendance_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
