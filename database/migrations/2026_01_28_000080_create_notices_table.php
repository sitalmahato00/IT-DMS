<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('message');
            $table->enum('audience', ['all', 'students', 'faculty', 'parents'])->default('all');
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
            $table->string('semester', 20)->nullable();
            $table->boolean('is_important')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('status');
            $table->index('audience');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};

