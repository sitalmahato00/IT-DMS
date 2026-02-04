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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name', 200);
            $table->string('subject_name_ne', 200)->nullable();
            $table->string('subject_code', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('description_ne')->nullable();
            $table->string('semester', 20)->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('credits', 10)->default('3');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('category', 50)->nullable();
            $table->text('syllabus')->nullable();
            $table->text('learning_objectives')->nullable();
            $table->integer('theory_percentage')->default(70);
            $table->integer('practical_percentage')->default(30);
            $table->integer('internal_percentage')->default(40);
            $table->integer('external_percentage')->default(60);
            $table->integer('lecture_hours')->default(4);
            $table->integer('practical_hours')->default(2);
            $table->integer('tutorial_hours')->default(1);
            $table->string('prerequisite', 200)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['semester', 'status']);
            $table->index('subject_code');
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

