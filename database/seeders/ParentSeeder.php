<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentModel;
use App\Models\User;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parentEmails = ['parent1@dit.edu.np', 'parent2@dit.edu.np', 'parent3@dit.edu.np', 'parent4@dit.edu.np', 'parent5@dit.edu.np'];

        foreach ($parentEmails as $index => $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                ParentModel::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'parent_code' => 'P' . sprintf('%03d', $index + 1),
                        'occupation' => 'Business',
                        'phone' => $user->phone,
                        'status' => 'active',
                        'gender' => 'male',
                    ]
                );
            }
        }
    }
}

