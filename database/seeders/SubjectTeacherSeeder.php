<?php

namespace Database\Seeders;

use App\Models\SubjectTeacher;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the primary teacher (Dr. Ramesh Poudel)
        $primaryTeacher = User::where('email', 'hellogoog94@gmail.com')->first();
        $primaryTeacherObj = $primaryTeacher ? Teacher::where('user_id', $primaryTeacher->id)->first() : null;
        
        // Get other teachers
        $otherTeachers = Teacher::where('user_id', '!=', $primaryTeacher?->id)->get();

        // Subject-Teacher assignments with primary focus on Dr. Ramesh Poudel
        $assignments = [
            ['subject_code' => 'CS202', 'primary' => true], // Database Management - Dr. Ramesh primary
            ['subject_code' => 'CS201', 'primary' => false], // Web Technology
            ['subject_code' => 'CS203', 'primary' => false], // Data Mining
            ['subject_code' => 'CS204', 'primary' => false], // Advanced Programming
            ['subject_code' => 'CS205', 'primary' => false], // Software Engineering
            ['subject_code' => 'CS206', 'primary' => false], // Network Security
        ];

        foreach ($assignments as $index => $assignment) {
            $subject = Subject::where('subject_code', $assignment['subject_code'])->first();
            
            if (!$subject) {
                continue;
            }

            // Assign primary teacher
            if ($assignment['primary'] && $primaryTeacherObj) {
                SubjectTeacher::firstOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $primaryTeacherObj->id,
                        'semester' => 5,
                        'role' => 'primary',
                    ],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $primaryTeacherObj->id,
                        'semester' => 5,
                        'role' => 'primary',
                        'assigned_at' => now(),
                        'notes' => 'Primary instructor for ' . $subject->subject_name,
                    ]
                );
            } else {
                // Assign rotating teachers
                $teacher = $otherTeachers[$index % $otherTeachers->count()] ?? null;
                
                if ($teacher) {
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
}

