<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 50)->nullable()->unique()->after('email');
            });
        }

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'program')) {
                $table->string('program', 150)->nullable()->after('department');
            }

            if (!Schema::hasColumn('students', 'section')) {
                $table->string('section', 50)->nullable()->after('semester');
            }

            if (!Schema::hasColumn('students', 'enrollment_date')) {
                $table->date('enrollment_date')->nullable()->after('academic_year_bs');
            }

            if (!Schema::hasColumn('students', 'expected_graduation_year')) {
                $table->string('expected_graduation_year', 10)->nullable()->after('enrollment_date');
            }

            if (!Schema::hasColumn('students', 'national_id_number')) {
                $table->string('national_id_number', 100)->nullable()->after('blood_group');
            }

            if (!Schema::hasColumn('students', 'secondary_phone')) {
                $table->string('secondary_phone', 20)->nullable()->after('phone');
            }

            if (!Schema::hasColumn('students', 'emergency_contact_name')) {
                $table->string('emergency_contact_name', 150)->nullable()->after('emergency_contact');
            }

            if (!Schema::hasColumn('students', 'emergency_relationship')) {
                $table->string('emergency_relationship', 100)->nullable()->after('emergency_contact_name');
            }

            if (!Schema::hasColumn('students', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }

            if (!Schema::hasColumn('students', 'state_province')) {
                $table->string('state_province', 100)->nullable()->after('city');
            }

            if (!Schema::hasColumn('students', 'postal_code')) {
                $table->string('postal_code', 30)->nullable()->after('state_province');
            }

            if (!Schema::hasColumn('students', 'country')) {
                $table->string('country', 100)->nullable()->after('postal_code');
            }

            if (!Schema::hasColumn('students', 'medical_conditions')) {
                $table->text('medical_conditions')->nullable()->after('country');
            }

            if (!Schema::hasColumn('students', 'allergies')) {
                $table->text('allergies')->nullable()->after('medical_conditions');
            }

            if (!Schema::hasColumn('students', 'disability_status')) {
                $table->string('disability_status', 120)->nullable()->after('allergies');
            }

            if (!Schema::hasColumn('students', 'notes')) {
                $table->text('notes')->nullable()->after('bio');
            }

            if (!Schema::hasColumn('students', 'id_document_path')) {
                $table->string('id_document_path', 2048)->nullable()->after('profile_photo_path');
            }

            if (!Schema::hasColumn('students', 'certificate_paths')) {
                $table->json('certificate_paths')->nullable()->after('id_document_path');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            });
        }

        Schema::table('students', function (Blueprint $table) {
            $columns = [
                'program',
                'section',
                'enrollment_date',
                'expected_graduation_year',
                'national_id_number',
                'secondary_phone',
                'emergency_contact_name',
                'emergency_relationship',
                'city',
                'state_province',
                'postal_code',
                'country',
                'medical_conditions',
                'allergies',
                'disability_status',
                'notes',
                'id_document_path',
                'certificate_paths',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
