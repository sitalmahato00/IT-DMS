<?php

namespace Database\Seeders;

use App\Models\ElectiveEnrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ElectiveEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $electiveSubjects = Subject::where('subject_type', 'elective')->get();
        $admin = User::where('role', 'admin')->first();

        foreach ($students as $student) {
            // Each student enrolls in 2 electives
            $enrollingSubjects = $electiveSubjects->random(min(2, count($electiveSubjects)));

            foreach ($enrollingSubjects as $subject) {
                ElectiveEnrollment::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'semester' => 5,
                    ],
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'semester' => 5,
                        'academic_year' => '2080-2081',
                        'status' => 'approved',
                        'approved_by' => $admin?->id,
                        'approved_at' => now(),
                        'remarks' => 'Approved elective enrollment',
                    ]
                );
            }
        }
    }
}
