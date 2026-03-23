<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\College;

class CollegeSeeder extends Seeder
{
    public function run(): void
    {
        College::create([
            'name' => 'DIT College of Engineering',
            'name_nepali' => 'डिट कलेज अफ इन्जिनियरिङ',
            'short_name' => 'DIT',
            'logo_path' => 'images/default-logo.svg',
            'phone' => '+977-1-437xxxx',
            'email' => 'info@dit.edu.np',
            'website' => 'www.dit.edu.np',
            'address' => 'Lainchaur, Kathmandu, Nepal',
            'address_nepali' => 'काठमाडौं, लैनचौर',
            'city' => 'Kathmandu',
            'district' => 'Kathmandu',
            'province' => 'Bagmati Province',
            'principal_name' => 'Dr. Ram Shrestha',
            'principal_phone' => '+977-98xxxxxxx',
            'principal_email' => 'principal@dit.edu.np',
            'established_year' => 2055,
            'registration_number' => 'EDU-12345',
            'description' => 'Premier IT engineering college offering quality education in Computer Engineering.',
        ]);

        // Update ErpSetting if exists
        \App\Models\ErpSetting::updateOrCreate(['key' => 'college_id'], ['value' => 1]);
    }
}

