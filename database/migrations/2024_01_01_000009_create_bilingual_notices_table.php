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
        Schema::create('bilingual_notices', function (Blueprint $table) {
            $table->id();
            $table->string('title_ne')->nullable();
            $table->string('title_en')->nullable();
            $table->text('content_ne')->nullable();
            $table->text('content_en')->nullable();
            $table->string('audience')->nullable();
            $table->string('audience_label_ne')->nullable();
            $table->string('category')->nullable();
            $table->string('category_label_ne')->nullable();
            $table->string('priority')->nullable();
            $table->string('priority_label_ne')->nullable();
            $table->timestamp('published_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->string('published_date_bs')->nullable();
            $table->string('expiry_date_bs')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_important')->default(false);
            $table->timestamps();
            $table->softDeletes();
            // Indexes
            $table->index(['audience', 'category']);
            $table->index(['is_published', 'is_important']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_categories');
    }
};

