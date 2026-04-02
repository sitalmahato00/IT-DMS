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
            $table->string('registration_number', 50)->nullable();
            $table->string('semester', 20)->default('1');
            $table->foreignId('parent_id')->nullable()->index(); // parents FK constraint added later
            $table->date('date_of_birth')->nullable();
            $table->string('date_of_birth_bs', 20)->nullable();
            $table->string('academic_year', 10)->nullable();
            $table->string('academic_year_bs', 10)->nullable();
            $table->string('batch_year', 10)->nullable();
            $table->boolean('is_active')->default(true);

            // Profile fields
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('department', 100)->nullable();
            $table->text('bio')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->boolean('is_alumni')->default(false);
            $table->timestamp('alumni_from')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('emergency_contact', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['semester', 'is_active']);
            $table->index('academic_year');
            $table->index('roll_no');
            // roll_no is non-unique to allow multiple students to have same roll_no
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
