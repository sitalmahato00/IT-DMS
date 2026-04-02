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
            $table->string('alternate_email', 255)->nullable();
            $table->string('secondary_phone', 20)->nullable();
            $table->string('national_id_number', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('joining_date')->nullable();
            $table->unsignedSmallInteger('years_of_experience')->nullable();
            $table->string('specialization', 255)->nullable();
            $table->string('employment_type', 50)->nullable();
            $table->string('previous_institution', 255)->nullable();
            $table->text('certifications')->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_relationship', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('staff_room_location', 255)->nullable();
            $table->string('employee_type', 50)->nullable();
            $table->string('work_shift', 50)->nullable();
            $table->string('timetable_assignment', 255)->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_account_number', 100)->nullable();
            $table->string('tax_identification_number', 100)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('emergency_notes')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('resume_path', 2048)->nullable();
            $table->json('certificate_paths')->nullable();
            $table->string('id_proof_path', 2048)->nullable();
            $table->string('access_level', 50)->nullable();
            $table->string('profile_visibility', 20)->default('public');
            $table->text('social_links')->nullable();
            $table->text('notes')->nullable();
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
