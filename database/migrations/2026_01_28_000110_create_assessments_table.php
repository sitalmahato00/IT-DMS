<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year', 20)->nullable();
            $table->string('semester', 50)->nullable();
            $table->string('department', 50)->nullable();
            $table->string('course', 100)->nullable();
            $table->string('assessment_name', 150);
            $table->enum('assessment_type', ['Internal','Midterm','Final','Practical','Viva','Project']);
            $table->integer('total_marks')->nullable();
            $table->integer('passing_marks')->nullable();
            $table->date('assessment_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft','published','locked'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
