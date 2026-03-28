<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates timetable/scheduling slots for the IT department.
     */
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('semester', 20);
            $table->string('section', 50)->nullable(); // Section/batch field
            $table->string('academic_year', 20)->nullable();
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 100)->nullable(); // Classroom / Lab name
            $table->enum('slot_type', ['theory', 'practical', 'tutorial', 'elective'])->default('theory');
            $table->string('lab_group', 10)->nullable(); // Lab group field for practical sessions
            $table->string('group_type', 20)->nullable(); // 'shared', 'group_a', 'group_b', etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('is_locked')->default(false); // Locked status for published timetables
            $table->timestamp('locked_at')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->date('holiday_date')->nullable(); // Used when marking a specific date as holiday
            $table->integer('max_capacity')->nullable(); // Capacity tracking
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['semester', 'section', 'day_of_week'], 'timetable_sem_section_day_idx');
            $table->index(['teacher_id', 'day_of_week', 'start_time', 'end_time'], 'timetable_teacher_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};
