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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('title_ne', 255)->nullable();
            $table->text('message')->nullable();
            $table->text('message_ne')->nullable();
            $table->string('audience', 50)->default('all');
            $table->string('audience_ne', 100)->nullable();
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->string('semester', 20)->nullable();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->boolean('is_important')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('published_at_bs', 20)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('file_path', 500)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'published_at']);
            $table->index(['audience', 'status']);
            $table->index(['is_important', 'status']);
            $table->index(['semester', 'status']);
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};

