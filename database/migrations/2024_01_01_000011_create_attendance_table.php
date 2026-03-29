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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->string('attendance_type', 20)->default('class')->index();
            $table->date('date');
            $table->string('date_bs', 20)->nullable();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('academic_year', 10)->nullable();
            $table->string('academic_year_bs', 10)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'leave'])->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['date', 'subject_id']);
            $table->index(['student_id', 'date']);
            $table->index(['student_id', 'status']);
            $table->index(['subject_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};

