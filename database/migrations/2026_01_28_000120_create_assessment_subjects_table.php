<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('max_marks')->nullable();
            $table->timestamps();
            $table->unique(['assessment_id','subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_subjects');
    }
};
