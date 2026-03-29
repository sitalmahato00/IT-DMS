<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('attendance_type', 20)->default('class')->index()->after('subject_id');
        });

        DB::table('attendance')->whereNull('attendance_type')->update(['attendance_type' => 'class']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex(['attendance_type']);
            $table->dropColumn('attendance_type');
        });
    }
};
