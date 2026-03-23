<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudyMaterial;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Str;

class StudyMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::all();
        $subjects = Subject::all();

        foreach ($teachers as $teacher) {
            for ($i = 0; $i < 5; $i++) {
                $subject = $subjects->random();
                $chapter = $i + 1;
                $title = 'Notes ' . $subject->subject_name . ' Chapter ' . $chapter;
                $slug = Str::slug($subject->subject_name);
                $fileName = "{$slug}-notes-chapter-{$chapter}.pdf";
                StudyMaterial::create([
                    'title' => $title,
                    'description' => 'Detailed notes for chapter ' . $chapter,
                    'file_name' => $fileName,
                    'file_path' => 'study-materials/' . $fileName,
                    'subject_id' => $subject->id,
                    // `study_materials.teacher_id` references `users.id` (see migration),
                    // so map the teacher profile back to the owning user.
                    'teacher_id' => $teacher->user_id,
                    'semester' => $subject->semester,
                    'document_type' => 'lecture_notes',
                    'visibility' => 'students',
                    'is_published' => true,
                    'uploaded_at' => now(),
                ]);
            }
        }
    }
}

