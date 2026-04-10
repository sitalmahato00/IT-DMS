<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentStudyMaterialController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $locale = app()->getLocale();
        $type = (string) $request->get('type', 'all');
        $query = trim((string) $request->get('q', ''));
        $subjectIds = $student->subjects()->pluck('subjects.id')->all();

        $accessQuery = StudyMaterial::published()
            ->with('subject')
            ->whereIn('visibility', ['all', 'students'])
            ->where(function ($builder) use ($subjectIds) {
                $builder->whereNull('subject_id');
                if (!empty($subjectIds)) {
                    $builder->orWhereIn('subject_id', $subjectIds);
                }
            });

        $materialsQuery = clone $accessQuery;

        if ($type !== '' && $type !== 'all') {
            $materialsQuery->where('document_type', $type);
        }

        if ($query !== '') {
            $materialsQuery->where(function ($builder) use ($query) {
                $builder->where('title', 'like', '%' . $query . '%')
                    ->orWhere('title_ne', 'like', '%' . $query . '%')
                    ->orWhere('description', 'like', '%' . $query . '%')
                    ->orWhere('description_ne', 'like', '%' . $query . '%')
                    ->orWhereHas('subject', function ($subjectQuery) use ($query) {
                        $subjectQuery->where('subject_name', 'like', '%' . $query . '%')
                            ->orWhere('subject_name_nepali', 'like', '%' . $query . '%')
                            ->orWhere('subject_code', 'like', '%' . $query . '%');
                    });
            });
        }

        $materials = $materialsQuery->latest()->paginate(9)->withQueryString();

        $typeOptions = [
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'lecture_notes' => $locale === 'ne' ? 'लेक्चर नोट्स' : 'Lecture Notes',
            'assignment' => $locale === 'ne' ? 'एसाइनमेन्ट' : 'Assignment',
            'lab_report' => $locale === 'ne' ? 'ल्याब रिपोर्ट' : 'Lab Report',
            'assessment' => $locale === 'ne' ? 'मूल्यांकन' : 'Assessment',
            'study_guide' => $locale === 'ne' ? 'अध्ययन गाइड' : 'Study Guide',
            'syllabus' => $locale === 'ne' ? 'पाठ्यक्रम' : 'Syllabus',
            'project_material' => $locale === 'ne' ? 'प्रोजेक्ट सामग्री' : 'Project Material',
        ];

        $materialStats = [
            'total' => (clone $accessQuery)->count(),
            'lecture_notes' => (clone $accessQuery)->where('document_type', 'lecture_notes')->count(),
            'assignment' => (clone $accessQuery)->where('document_type', 'assignment')->count(),
            'syllabus' => (clone $accessQuery)->where('document_type', 'syllabus')->count(),
        ];

        return view('student.study-materials.index', compact(
            'materials',
            'type',
            'query',
            'typeOptions',
            'materialStats',
            'subjectIds'
        ));
    }

    public function download($id)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $subjectIds = $student->subjects()->pluck('subjects.id')->all();

        $material = StudyMaterial::published()
            ->with('subject')
            ->whereIn('visibility', ['all', 'students'])
            ->where(function ($builder) use ($subjectIds) {
                $builder->whereNull('subject_id');
                if (!empty($subjectIds)) {
                    $builder->orWhereIn('subject_id', $subjectIds);
                }
            })
            ->findOrFail($id);

        if (empty($material->file_path) || !Storage::disk('public')->exists($material->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $material->file_path,
            $material->file_name ?: basename($material->file_path)
        );
    }
}

