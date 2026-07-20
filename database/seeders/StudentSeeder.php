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
        // Main 4 students - your real users
        $mainStudents = [
            [
                'user_email' => 'itstudentsital@gmail.com',
                'parent_email' => 'sitalmahato00@gmail.com',
                'roll_no' => '001',
                'registration_number' => 'REG2020001',
                'batch_year' => '2020',
                'gender' => 'Male',
                'blood_group' => 'O+',
                'date_of_birth' => '2001-05-15',
            ],
        ];

        // Additional students for better data
        $additionalStudents = [
            [
                'user_email' => 'rajesh.adhikari@student.itdms.local',
                'parent_email' => 'hari.adhikari@parent.itdms.local',
                'roll_no' => '002',
                'registration_number' => 'REG2020002',
                'batch_year' => '2020',
                'gender' => 'Male',
                'blood_group' => 'A+',
                'date_of_birth' => '2002-03-20',
            ],
            [
                'user_email' => 'priya.verma@student.itdms.local',
                'parent_email' => 'sunita.verma@parent.itdms.local',
                'roll_no' => '003',
                'registration_number' => 'REG2020003',
                'batch_year' => '2020',
                'gender' => 'Female',
                'blood_group' => 'B+',
                'date_of_birth' => '2002-07-10',
            ],
            [
                'user_email' => 'arjun.pant@student.itdms.local',
                'parent_email' => 'gyan.pant@parent.itdms.local',
                'roll_no' => '004',
                'registration_number' => 'REG2020004',
                'batch_year' => '2020',
                'gender' => 'Male',
                'blood_group' => 'AB+',
                'date_of_birth' => '2001-11-25',
            ],
            [
                'user_email' => 'anjali.singh@student.itdms.local',
                'parent_email' => 'neha.singh@parent.itdms.local',
                'roll_no' => '005',
                'registration_number' => 'REG2020005',
                'batch_year' => '2020',
                'gender' => 'Female',
                'blood_group' => 'O-',
                'date_of_birth' => '2003-01-30',
            ],
        ];

        $allStudents = array_merge($mainStudents, $additionalStudents);

        foreach ($allStudents as $studentData) {
            $studentUser = User::where('email', $studentData['user_email'])->first();
            $parentUser = User::where('email', $studentData['parent_email'])->first();
            $parent = $parentUser ? StudentParent::where('user_id', $parentUser->id)->first() : null;

            if ($studentUser) {
                $dob = $studentData['date_of_birth'] ?? now()->subYears(20);
                $dobBs = \App\Helpers\NepaliContentHelper::convertAdToBs($dob);
                
                Student::firstOrCreate(
                    ['user_id' => $studentUser->id],
                    [
                        'user_id' => $studentUser->id,
                        'roll_no' => $studentData['roll_no'],
                        'registration_number' => $studentData['registration_number'],
                        'semester' => 5,
                        'batch_year' => $studentData['batch_year'],
                        'academic_year' => '2080-2081',
                        'academic_year_bs' => '2080-2081',
                        'gender' => $studentData['gender'],
                        'blood_group' => $studentData['blood_group'],
                        'phone' => $studentUser->phone,
                        'department' => 'IT',
                        'status' => 'active',
                        'is_active' => true,
                        'address' => 'Kathmandu, Nepal',
                        'parent_id' => $parent?->id,
                        'emergency_contact' => $studentUser->phone,
                        'date_of_birth' => $dob,
                        'date_of_birth_bs' => $dobBs,
                    ]
                );
            }
        }
    }
}

