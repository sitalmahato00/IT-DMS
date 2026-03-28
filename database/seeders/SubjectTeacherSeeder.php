<?php

namespace Database\Seeders;

use App\Models\SubjectTeacher;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SubjectTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            'CS201', // Web Technology
            'CS202', // Database Management
            'CS203', // Data Mining
            'CS204', // Advanced Programming
            'CS205', // Software Engineering
            'CS206', // Network Security
        ];

        $teachers = Teacher::all();

        foreach ($subjects as $index => $subjectCode) {
            $subject = Subject::where('subject_code', $subjectCode)->first();
            $teacher = $teachers->get($index % $teachers->count());

            if ($subject && $teacher) {
                SubjectTeacher::firstOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'semester' => 5,
                    ],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'semester' => 5,
                        'role' => 'primary',
                        'assigned_at' => now(),
                        'notes' => 'Primary instructor for ' . $subject->subject_name,
                    ]
                );
            }
        }
    }
}
