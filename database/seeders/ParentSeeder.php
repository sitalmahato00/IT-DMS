<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentModel;
use App\Models\User;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parentEmails = [];
        for ($i = 1; $i <= 20; $i++) {
            $parentEmails[] = "parent{$i}@dit.edu.np";
        }

        $occupations = ['Business', 'Service', 'Teacher', 'Doctor', 'Engineer', 'Farmer', 'Housewife', 'Government Officer', 'Private Sector', 'Self-Employed', 'Retired', 'Contractor', 'Businessman', 'Nurse', 'Lawyer', 'Accountant', 'Banker', 'IT Professional', 'Entrepreneur', 'Consultant'];
        $genders = array_merge(array_fill(0, 15, 'male'), array_fill(0, 5, 'female'));

        foreach ($parentEmails as $index => $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                ParentModel::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'parent_code' => 'P' . sprintf('%03d', $index + 1),
                        'occupation' => $occupations[$index % count($occupations)],
                        'phone' => $user->phone,
                        'status' => 'active',
                        'gender' => $genders[$index],
                    ]
                );
            }
        }
    }
}

