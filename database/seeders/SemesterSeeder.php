<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $semesters = [
            [
                'number' => 1,
                'name' => 'First Semester',
                'name_ne' => 'पहिलो सेमेस्टर',
                'academic_year' => '2081/082',
                'academic_year_bs' => '२०८१/०८२',
                'start_date' => '2024-08-15',
                'start_date_bs' => '२०८१-५-१',
                'end_date' => '2025-01-15',
                'end_date_bs' => '२०८१-१०-३१',
                'status' => 'open',
                'is_active' => true,
            ],
            [
                'number' => 2,
                'name' => 'Second Semester',
                'name_ne' => 'दोस्रो सेमेस्टर',
                'academic_year' => '2081/082',
                'academic_year_bs' => '२०८१/०८२',
                'start_date' => '2025-02-01',
                'start_date_bs' => '२०८१-११-१८',
                'end_date' => '2025-06-30',
                'end_date_bs' => '२०८२-३-१६',
                'status' => 'upcoming',
                'is_active' => false,
            ],
            // Add 3-8 similarly...
            [
                'number' => 3,
                'name' => 'Third Semester',
                'name_ne' => 'तेस्रो सेमेस्टर',
                'academic_year' => '2081/082',
                'academic_year_bs' => '२०८१/०८२',
                'status' => 'upcoming',
                'is_active' => false,
            ],
            [
                'number' => 4,
                'name' => 'Fourth Semester',
                'name_ne' => 'चौथो सेमेस्टर',
                'academic_year' => '2081/082',
                'academic_year_bs' => '२०८१/०८२',
                'status' => 'upcoming',
                'is_active' => false,
            ],
            [
                'number' => 5,
                'name' => 'Fifth Semester',
                'name_ne' => 'पाँचौँ सेमेस्टर',
                'academic_year' => '2082/083',
                'academic_year_bs' => '२०८२/०८३',
                'status' => 'upcoming',
                'is_active' => false,
            ],
            [
                'number' => 6,
                'name' => 'Sixth Semester',
                'name_ne' => 'छैठौँ सेमेस्टर',
                'academic_year' => '2082/083',
                'academic_year_bs' => '२०८२/०८३',
                'status' => 'upcoming',
                'is_active' => false,
            ],
            [
                'number' => 7,
                'name' => 'Seventh Semester',
                'name_ne' => 'सातौँ सेमेस्टर',
                'academic_year' => '2082/083',
                'academic_year_bs' => '२०८२/०८३',
                'status' => 'upcoming',
                'is_active' => false,
            ],
            [
                'number' => 8,
                'name' => 'Eighth Semester',
                'name_ne' => 'आठौँ सेमेस्टर',
                'academic_year' => '2082/083',
                'academic_year_bs' => '२०८२/०८३',
                'status' => 'upcoming',
                'is_active' => false,
            ],
        ];

        foreach ($semesters as $data) {
            Semester::updateOrCreate(
                [
                    'number' => $data['number'],
                    'academic_year' => $data['academic_year'],
                ],
                $data
            );
        }
    }
}
