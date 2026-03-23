<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This creates a pivot table to manage subject-teacher relationships.
     * Instead of storing teacher_id directly on subjects table, we use this pivot table.
     */
    public function up(): void
    {
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('semester', 20)->nullable(); // Store semester info here
            $table->string('role', 50)->default('primary'); // primary, assistant, guest
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // Ensure unique combination (one teacher per subject)
            $table->unique(['subject_id', 'teacher_id'], 'subject_teacher_unique');
            
            // Indexes for efficient querying
            $table->index('teacher_id');
            $table->index('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
    }
};

