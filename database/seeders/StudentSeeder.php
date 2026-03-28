<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentEmails = [
            'itstudentsital@gmail.com',
            'rajesh.adhikari@student.itdms.local',
            'priya.verma@student.itdms.local',
            'arjun.pant@student.itdms.local',
            'anjali.singh@student.itdms.local',
        ];

        $parentEmails = [
            'sitalmahato00@gmail.com',
            'hari.adhikari@parent.itdms.local',
            'sunita.verma@parent.itdms.local',
            'gyan.pant@parent.itdms.local',
            'neha.singh@parent.itdms.local',
        ];

        $studentData = [
            ['roll_no' => '001', 'registration_number' => 'REG2020001', 'batch_year' => '2020', 'gender' => 'Male', 'blood_group' => 'O+'],
            ['roll_no' => '002', 'registration_number' => 'REG2020002', 'batch_year' => '2020', 'gender' => 'Male', 'blood_group' => 'A+'],
            ['roll_no' => '003', 'registration_number' => 'REG2020003', 'batch_year' => '2020', 'gender' => 'Female', 'blood_group' => 'B+'],
            ['roll_no' => '004', 'registration_number' => 'REG2020004', 'batch_year' => '2020', 'gender' => 'Male', 'blood_group' => 'AB+'],
            ['roll_no' => '005', 'registration_number' => 'REG2020005', 'batch_year' => '2020', 'gender' => 'Female', 'blood_group' => 'O-'],
        ];

        foreach ($studentEmails as $index => $email) {
            $studentUser = User::where('email', $email)->first();
            $parentUser = User::where('email', $parentEmails[$index])->first();
            $parent = $parentUser ? StudentParent::where('user_id', $parentUser->id)->first() : null;

            if ($studentUser) {
                Student::firstOrCreate(
                    ['user_id' => $studentUser->id],
                    [
                        'user_id' => $studentUser->id,
                        'roll_no' => $studentData[$index]['roll_no'],
                        'registration_number' => $studentData[$index]['registration_number'],
                        'semester' => 5,
                        'batch_year' => $studentData[$index]['batch_year'],
                        'academic_year' => '2080-2081',
                        'academic_year_bs' => '2080-2081',
                        'gender' => $studentData[$index]['gender'],
                        'blood_group' => $studentData[$index]['blood_group'],
                        'phone' => $studentUser->phone,
                        'department' => 'IT',
                        'status' => 'active',
                        'is_active' => true,
                        'address' => 'Kathmandu, Nepal',
                        'parent_id' => $parent?->id,
                        'emergency_contact' => $studentUser->phone,
                        'date_of_birth' => now()->subYears(20),
                    ]
                );
            }
        }
    }
}

