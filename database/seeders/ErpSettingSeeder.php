<?php

namespace Database\Seeders;

use App\Models\ErpSetting;
use Illuminate\Database\Seeder;

class ErpSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'IT Department Management System', 'group' => 'general', 'type' => 'string'],
            ['key' => 'app_abbreviation', 'value' => 'IT-DMS', 'group' => 'general', 'type' => 'string'],
            ['key' => 'academic_year', 'value' => '2080-2081', 'group' => 'general', 'type' => 'string'],
            ['key' => 'academic_year_bs', 'value' => '2080-2081', 'group' => 'general', 'type' => 'string'],
            ['key' => 'semester', 'value' => '5', 'group' => 'general', 'type' => 'string'],
            ['key' => 'app_email', 'value' => 'admin@itdms.local', 'group' => 'general', 'type' => 'string'],
            ['key' => 'bilingual_support', 'value' => '1', 'group' => 'general', 'type' => 'boolean'],
            ['key' => 'attendance_type', 'value' => 'subject', 'group' => 'general', 'type' => 'string'],
            ['key' => 'marks_types', 'value' => 'internal,assessment,final', 'group' => 'general', 'type' => 'string'],
            ['key' => 'security_password_min_length', 'value' => '10', 'group' => 'security', 'type' => 'integer'],
            ['key' => 'security_password_require_uppercase', 'value' => '1', 'group' => 'security', 'type' => 'boolean'],
            ['key' => 'security_password_require_lowercase', 'value' => '1', 'group' => 'security', 'type' => 'boolean'],
            ['key' => 'security_password_require_number', 'value' => '1', 'group' => 'security', 'type' => 'boolean'],
            ['key' => 'security_password_require_symbol', 'value' => '1', 'group' => 'security', 'type' => 'boolean'],
            // 2FA ENABLED FOR ALL USERS - OTP on login
            ['key' => 'security_two_factor_enabled', 'value' => '1', 'group' => 'security', 'type' => 'boolean'],
            ['key' => 'security_two_factor_roles', 'value' => json_encode(['admin', 'teacher', 'student', 'parent']), 'group' => 'security', 'type' => 'json'],
            ['key' => 'security_two_factor_expiry_minutes', 'value' => '10', 'group' => 'security', 'type' => 'integer'],
            ['key' => 'notification_email_enabled', 'value' => '1', 'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notification_email_exam', 'value' => '1', 'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notification_email_attendance', 'value' => '1', 'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notification_email_student', 'value' => '1', 'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notification_email_assignment', 'value' => '1', 'group' => 'notification', 'type' => 'boolean'],
            ['key' => 'notification_email_result', 'value' => '1', 'group' => 'notification', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            ErpSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
