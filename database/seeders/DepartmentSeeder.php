<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'short_name' => 'IT',
                'description' => 'Department of Information Technology',
                'principal_name' => 'Dr. Department Head',
                'address' => 'Building A, Floor 2, Kathmandu',
            ],
            [
                'name' => 'Computer Science',
                'short_name' => 'CS',
                'description' => 'Department of Computer Science',
                'principal_name' => 'Prof. CS Head',
                'address' => 'Building B, Floor 1, Kathmandu',
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                $dept
            );
        }
    }
}
