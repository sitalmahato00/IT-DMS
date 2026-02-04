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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('roll_no', 50);
            $table->string('semester', 20)->default('1');
            $table->string('department', 100)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('date_of_birth')->nullable();
            $table->string('date_of_birth_bs', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('batch_year', 10)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_alumni')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['semester', 'is_active']);
            $table->index(['department', 'batch_year']);
            $table->index('roll_no');
            $table->unique('roll_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

