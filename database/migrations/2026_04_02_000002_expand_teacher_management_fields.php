<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'alternate_email')) {
                $table->string('alternate_email', 255)->nullable()->after('phone');
            }

            if (!Schema::hasColumn('teachers', 'national_id_number')) {
                $table->string('national_id_number', 100)->nullable()->after('alternate_email');
            }

            if (!Schema::hasColumn('teachers', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('national_id_number');
            }

            if (!Schema::hasColumn('teachers', 'joining_date')) {
                $table->date('joining_date')->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('teachers', 'years_of_experience')) {
                $table->unsignedSmallInteger('years_of_experience')->nullable()->after('joining_date');
            }

            if (!Schema::hasColumn('teachers', 'specialization')) {
                $table->string('specialization', 255)->nullable()->after('years_of_experience');
            }

            if (!Schema::hasColumn('teachers', 'employment_type')) {
                $table->string('employment_type', 50)->nullable()->after('specialization');
            }

            if (!Schema::hasColumn('teachers', 'previous_institution')) {
                $table->string('previous_institution', 255)->nullable()->after('employment_type');
            }

            if (!Schema::hasColumn('teachers', 'certifications')) {
                $table->text('certifications')->nullable()->after('previous_institution');
            }

            if (!Schema::hasColumn('teachers', 'secondary_phone')) {
                $table->string('secondary_phone', 20)->nullable()->after('phone');
            }

            if (!Schema::hasColumn('teachers', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 255)->nullable()->after('secondary_phone');
            }

            if (!Schema::hasColumn('teachers', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            }

            if (!Schema::hasColumn('teachers', 'emergency_relationship')) {
                $table->string('emergency_relationship', 100)->nullable()->after('emergency_contact_phone');
            }

            if (!Schema::hasColumn('teachers', 'staff_room_location')) {
                $table->string('staff_room_location', 255)->nullable()->after('address');
            }

            if (!Schema::hasColumn('teachers', 'employee_type')) {
                $table->string('employee_type', 50)->nullable()->after('staff_room_location');
            }

            if (!Schema::hasColumn('teachers', 'work_shift')) {
                $table->string('work_shift', 50)->nullable()->after('employee_type');
            }

            if (!Schema::hasColumn('teachers', 'timetable_assignment')) {
                $table->string('timetable_assignment', 255)->nullable()->after('work_shift');
            }

            if (!Schema::hasColumn('teachers', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable()->after('timetable_assignment');
            }

            if (!Schema::hasColumn('teachers', 'bank_name')) {
                $table->string('bank_name', 255)->nullable()->after('salary');
            }

            if (!Schema::hasColumn('teachers', 'bank_account_number')) {
                $table->string('bank_account_number', 100)->nullable()->after('bank_name');
            }

            if (!Schema::hasColumn('teachers', 'tax_identification_number')) {
                $table->string('tax_identification_number', 100)->nullable()->after('bank_account_number');
            }

            if (!Schema::hasColumn('teachers', 'blood_group')) {
                $table->string('blood_group', 10)->nullable()->after('tax_identification_number');
            }

            if (!Schema::hasColumn('teachers', 'medical_conditions')) {
                $table->text('medical_conditions')->nullable()->after('blood_group');
            }

            if (!Schema::hasColumn('teachers', 'emergency_notes')) {
                $table->text('emergency_notes')->nullable()->after('medical_conditions');
            }

            if (!Schema::hasColumn('teachers', 'resume_path')) {
                $table->string('resume_path', 2048)->nullable()->after('profile_photo_path');
            }

            if (!Schema::hasColumn('teachers', 'certificate_paths')) {
                $table->json('certificate_paths')->nullable()->after('resume_path');
            }

            if (!Schema::hasColumn('teachers', 'id_proof_path')) {
                $table->string('id_proof_path', 2048)->nullable()->after('certificate_paths');
            }

            if (!Schema::hasColumn('teachers', 'access_level')) {
                $table->string('access_level', 50)->nullable()->after('id_proof_path');
            }

            if (!Schema::hasColumn('teachers', 'profile_visibility')) {
                $table->string('profile_visibility', 20)->default('public')->after('access_level');
            }

            if (!Schema::hasColumn('teachers', 'social_links')) {
                $table->text('social_links')->nullable()->after('profile_visibility');
            }

            if (!Schema::hasColumn('teachers', 'notes')) {
                $table->text('notes')->nullable()->after('social_links');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            foreach ([
                'alternate_email',
                'national_id_number',
                'date_of_birth',
                'joining_date',
                'years_of_experience',
                'specialization',
                'employment_type',
                'previous_institution',
                'certifications',
                'secondary_phone',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_relationship',
                'staff_room_location',
                'employee_type',
                'work_shift',
                'timetable_assignment',
                'salary',
                'bank_name',
                'bank_account_number',
                'tax_identification_number',
                'blood_group',
                'medical_conditions',
                'emergency_notes',
                'resume_path',
                'certificate_paths',
                'id_proof_path',
                'access_level',
                'profile_visibility',
                'social_links',
                'notes',
            ] as $column) {
                if (Schema::hasColumn('teachers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
