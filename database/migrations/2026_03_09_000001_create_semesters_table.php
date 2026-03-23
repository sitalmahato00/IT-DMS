<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates a dedicated semesters table for semester management.
     */
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('number'); // 1-8
            $table->string('name', 100); // e.g. "First Semester"
            $table->string('name_ne', 100)->nullable(); // Nepali name
            $table->string('academic_year', 20)->nullable(); // e.g. "2082/083"
            $table->string('academic_year_bs', 20)->nullable(); // BS format
            $table->date('start_date')->nullable();
            $table->string('start_date_bs', 20)->nullable();
            $table->date('end_date')->nullable();
            $table->string('end_date_bs', 20)->nullable();
            $table->enum('status', ['open', 'closed', 'upcoming'])->default('upcoming');
            $table->boolean('is_active')->default(false);
            $table->integer('max_credits')->default(24);
            $table->integer('total_weeks')->default(16);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['number', 'status']);
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
