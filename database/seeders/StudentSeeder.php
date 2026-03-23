<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\ParentModel;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $parentUsers = ParentModel::with('user')->get();
        $parentIds = $parentUsers->pluck('id')->toArray();

        for ($i = 1; $i <= 10; $i++) {
            $user = User::where('email', "student{$i}@dit.edu.np")->first();
            if ($user) {
                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'roll_no' => sprintf('S%03d', $i),
                        'registration_number' => 'REG-' . sprintf('%04d', $i),
                        'semester' => ($i % 8) + 1,
                        'parent_id' => $parentIds[($i - 1) % count($parentIds)] ?: null,
                        'academic_year' => '2081/082',
                        'academic_year_bs' => '२०८१/०८२',
                        'is_active' => true,
                        'status' => 'active',
                        'gender' => 'male',
                    ]
                );
            }
        }
    }
}

