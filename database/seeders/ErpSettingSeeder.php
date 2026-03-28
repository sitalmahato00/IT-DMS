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
        ];

        foreach ($settings as $setting) {
            ErpSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
