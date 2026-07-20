<?php

namespace Database\Seeders;

use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudyMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();
        $teachers = Teacher::all();

        // Only create 3 core document types per subject to limit records
        $documentTypes = ['lecture_notes', 'assignment', 'study_guide'];

        foreach ($subjects as $subject) {
            $teacher = $teachers->random();

            foreach ($documentTypes as $docType) {
                $slug = Str::slug($subject->subject_name);
                StudyMaterial::firstOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'title' => ucfirst(str_replace('_', ' ', $docType)) . ' - ' . $subject->subject_name,
                        'document_type' => $docType,
                    ],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->user_id,
                        'title' => ucfirst(str_replace('_', ' ', $docType)) . ' - ' . $subject->subject_name,
                        'file_name' => $slug . '_' . $docType . '.pdf',
                        'file_path' => '/storage/study_materials/' . $slug . '/',
                        'file_size' => rand(500, 5000),
                        'document_type' => $docType,
                        'description' => 'Study material for ' . $subject->subject_name,
                        'semester' => 5,
                        'visibility' => 'all',
                        'is_published' => true,
                        'uploaded_at' => now()->subDays(rand(1, 30)),
                    ]
                );
            }
        }
    }
}
