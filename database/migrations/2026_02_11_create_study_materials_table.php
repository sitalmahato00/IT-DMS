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
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('semester', 10); // 1, 2, 3, etc. or "1st", "2nd", etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('category', 50); // notes, assignment, paper, other
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('subject_id')
                  ->references('id')
                  ->on('subjects')
                  ->onDelete('set null');

            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes for better query performance
            $table->index('category');
            $table->index('semester');
            $table->index(['subject_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};

