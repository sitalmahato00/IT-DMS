<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            ['number' => 1, 'name' => '1st Semester', 'status' => 'open'],
            ['number' => 2, 'name' => '2nd Semester', 'status' => 'open'],
            ['number' => 3, 'name' => '3rd Semester', 'status' => 'open'],
            ['number' => 4, 'name' => '4th Semester', 'status' => 'open'],
            ['number' => 5, 'name' => '5th Semester', 'status' => 'open'],
            ['number' => 6, 'name' => '6th Semester', 'status' => 'closed'],
            ['number' => 7, 'name' => '7th Semester', 'status' => 'upcoming'],
            ['number' => 8, 'name' => '8th Semester', 'status' => 'upcoming'],
        ];

        foreach ($semesters as $semester) {
            Semester::firstOrCreate(
                ['number' => $semester['number']],
                array_merge($semester, [
                    'academic_year' => '2080-2081',
                    'academic_year_bs' => '2080-2081',
                    'is_active' => $semester['number'] === 5,
                    'total_weeks' => 16,
                    'max_credits' => 24,
                ])
            );
        }
    }
}
