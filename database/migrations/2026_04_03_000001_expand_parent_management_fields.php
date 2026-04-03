<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->string('national_id_number', 100)->nullable()->after('occupation');
            $table->date('date_of_birth')->nullable()->after('national_id_number');
            $table->string('relationship', 60)->nullable()->after('date_of_birth');
            $table->string('secondary_phone', 20)->nullable()->after('phone');
            $table->string('alternate_email', 191)->nullable()->after('secondary_phone');
            $table->string('whatsapp_number', 20)->nullable()->after('alternate_email');
            $table->string('preferred_contact_method', 20)->nullable()->after('whatsapp_number');
            $table->string('city', 120)->nullable()->after('address');
            $table->string('state_province', 120)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state_province');
            $table->string('country', 120)->nullable()->after('postal_code');
            $table->string('employer_name', 150)->nullable()->after('country');
            $table->text('work_address')->nullable()->after('employer_name');
            $table->string('work_phone_number', 20)->nullable()->after('work_address');
            $table->string('income_range', 60)->nullable()->after('work_phone_number');
            $table->string('blood_group', 10)->nullable()->after('gender');
            $table->text('medical_conditions')->nullable()->after('blood_group');
            $table->text('emergency_notes')->nullable()->after('medical_conditions');
            $table->string('id_proof_path', 2048)->nullable()->after('profile_photo_path');
            $table->string('address_proof_path', 2048)->nullable()->after('id_proof_path');
            $table->string('notification_preferences', 50)->nullable()->after('status');
            $table->string('access_level', 20)->default('view_only')->after('notification_preferences');
            $table->boolean('portal_access')->default(true)->after('access_level');
            $table->text('notes')->nullable()->after('portal_access');
            $table->string('preferred_language', 20)->nullable()->after('notes');
            $table->string('profile_visibility', 20)->default('public')->after('preferred_language');
            $table->boolean('emergency_contact_priority')->default(false)->after('profile_visibility');
            $table->foreignId('primary_child_user_id')->nullable()->constrained('users')->nullOnDelete()->after('emergency_contact_priority');
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('primary_child_user_id');
            $table->dropColumn([
                'national_id_number',
                'date_of_birth',
                'relationship',
                'secondary_phone',
                'alternate_email',
                'whatsapp_number',
                'preferred_contact_method',
                'city',
                'state_province',
                'postal_code',
                'country',
                'employer_name',
                'work_address',
                'work_phone_number',
                'income_range',
                'blood_group',
                'medical_conditions',
                'emergency_notes',
                'id_proof_path',
                'address_proof_path',
                'notification_preferences',
                'access_level',
                'portal_access',
                'notes',
                'preferred_language',
                'profile_visibility',
                'emergency_contact_priority',
            ]);
        });
    }
};
