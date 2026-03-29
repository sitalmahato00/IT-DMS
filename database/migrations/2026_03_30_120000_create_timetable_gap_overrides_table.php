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
        Schema::create('timetable_gap_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('semester', 20);
            $table->string('section', 50)->default('');
            $table->string('day_of_week', 20);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->unique(
                ['semester', 'section', 'day_of_week', 'start_time', 'end_time'],
                'timetable_gap_overrides_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_gap_overrides');
    }
};
