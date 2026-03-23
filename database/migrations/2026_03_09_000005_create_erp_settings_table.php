<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates a key-value settings table for ERP configuration.
     * Groups: grading, attendance, elective, semester, general, notification
     */
    public function up(): void
    {
        Schema::create('erp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general'); // grading, attendance, elective, semester, general
            $table->string('type', 20)->default('string'); // string, integer, boolean, json, float
            $table->string('label', 200)->nullable(); // Human-readable label
            $table->text('description')->nullable(); // Help text
            $table->timestamps();

            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('erp_settings');
    }
};
