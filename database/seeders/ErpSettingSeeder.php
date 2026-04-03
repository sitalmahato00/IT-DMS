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
            ['key' => 'app_name', 'value' => 'IT Department Management System'],
            ['key' => 'app_abbreviation', 'value' => 'IT-DMS'],
            ['key' => 'academic_year', 'value' => '2080-2081'],
            ['key' => 'academic_year_bs', 'value' => '2080-2081'],
            ['key' => 'semester', 'value' => '5'],
            ['key' => 'app_email', 'value' => 'admin@itdms.local'],
            ['key' => 'bilingual_support', 'value' => '1'],
            ['key' => 'attendance_type', 'value' => 'subject'],
            ['key' => 'marks_types', 'value' => 'internal,assessment,final'],
            ['key' => 'security_password_min_length', 'value' => '10'],
            ['key' => 'security_password_require_uppercase', 'value' => '1'],
            ['key' => 'security_password_require_lowercase', 'value' => '1'],
            ['key' => 'security_password_require_number', 'value' => '1'],
            ['key' => 'security_password_require_symbol', 'value' => '1'],
            ['key' => 'security_two_factor_enabled', 'value' => '0'],
            ['key' => 'security_two_factor_roles', 'value' => json_encode(['admin'])],
            ['key' => 'security_two_factor_expiry_minutes', 'value' => '10'],
            ['key' => 'notification_email_enabled', 'value' => '1'],
            ['key' => 'notification_email_exam', 'value' => '1'],
            ['key' => 'notification_email_attendance', 'value' => '1'],
            ['key' => 'notification_email_student', 'value' => '1'],
            ['key' => 'notification_email_assignment', 'value' => '1'],
            ['key' => 'notification_email_result', 'value' => '1'],
        ];

        foreach ($settings as $setting) {
            ErpSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
