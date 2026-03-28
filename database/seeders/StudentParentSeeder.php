<?php

namespace Database\Seeders;

use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentEmails = [
            'sitalmahato00@gmail.com',
            'hari.adhikari@parent.itdms.local',
            'sunita.verma@parent.itdms.local',
            'gyan.pant@parent.itdms.local',
            'neha.singh@parent.itdms.local',
        ];

        $parentData = [
            ['parent_code' => 'PARENT001', 'occupation' => 'Engineer', 'gender' => 'Male'],
            ['parent_code' => 'PARENT002', 'occupation' => 'Doctor', 'gender' => 'Male'],
            ['parent_code' => 'PARENT003', 'occupation' => 'Teacher', 'gender' => 'Female'],
            ['parent_code' => 'PARENT004', 'occupation' => 'Business', 'gender' => 'Male'],
            ['parent_code' => 'PARENT005', 'occupation' => 'Nurse', 'gender' => 'Female'],
        ];

        foreach ($parentEmails as $index => $email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                StudentParent::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'parent_code' => $parentData[$index]['parent_code'],
                        'occupation' => $parentData[$index]['occupation'],
                        'phone' => $user->phone,
                        'gender' => $parentData[$index]['gender'],
                        'department' => 'IT',
                        'status' => 'active',
                        'address' => 'Kathmandu, Nepal',
                        'bio' => 'Student Parent/Guardian',
                    ]
                );
            }
        }
    }
}
