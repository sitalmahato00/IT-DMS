<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
use App\Models\Subject;

class SubjectTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $subjects = Subject::all();

        foreach ($teachers as $teacher) {
            // Assign 4-8 subjects per teacher
            $assignedSubjects = $subjects->random(min(8, $subjects->count()))->pluck('id');
            
            foreach ($assignedSubjects as $subjectId) {
                SubjectTeacher::updateOrCreate(
                    [
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacher->id,
                    ],
                    [
                        'semester' => '1',
                        'role' => 'primary',
                    ]
                );
            }
        }
    }
}

