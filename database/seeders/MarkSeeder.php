<?php

namespace Database\Seeders;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class MarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        $examTypes = ['internal', 'midterm', 'final', 'practical'];

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                foreach ($examTypes as $examType) {
                    $marksObtained = rand(30, 100);
                    $teacher = $teachers->random();

                    Mark::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'exam_type' => $examType,
                        ],
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->user_id,
                            'exam_type' => $examType,
                            'academic_year' => '2080-2081',
                            'academic_year_bs' => '2080-2081',
                            'marks_obtained' => $marksObtained,
                            'full_marks' => 100,
                            'date' => now(),
                            'remarks' => 'Marks entered for ' . $examType . ' examination',
                        ]
                    );
                }
            }
        }
    }
}
