<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('teacher_code', 20)->unique();
            $table->string('qualification', 100)->nullable();
            
            // Profile fields
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('department', 100)->nullable();
            $table->text('bio')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'On Leave', 'Retired'])->default('active');
            $table->string('gender', 20)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
