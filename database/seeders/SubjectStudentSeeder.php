<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $coreSubjects = Subject::whereIn('subject_type', ['core', 'mandatory'])->get();

        foreach ($students as $student) {
            // Find core subjects for the student's semester
            $semesterSubjects = $coreSubjects->where('semester', $student->semester);
            
            $subjectIds = $semesterSubjects->pluck('id')->toArray();
            
            if (count($subjectIds) > 0) {
                // Attach subjects without detaching existing ones
                $student->subjects()->syncWithoutDetaching($subjectIds);
            }
        }
    }
}
