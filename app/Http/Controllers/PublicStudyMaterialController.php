<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Support\SafeCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicStudyMaterialController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $type = (string) $request->get('type', 'all');
        $query = trim((string) $request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));
        $ttl = (int) config('performance.public_data_cache_ttl', 300);
        $typeKey = $type !== '' ? $type : 'all';
        $queryKey = $query !== '' ? md5($query) : 'all';

        $materials = SafeCache::remember("resources:index:{$typeKey}:{$queryKey}:page:{$page}:v1", $ttl, function () use ($type, $query, $page) {
            $materialsQuery = StudyMaterial::published()
                ->with('subject')
                ->whereIn('visibility', ['all', 'students'])
                ->latest();

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

            return $materialsQuery->paginate(9, ['*'], 'page', $page);
        });
        $materials->appends([
            'type' => $type,
            'q' => $query,
        ]);

        $typeOptions = [
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'lecture_notes' => $locale === 'ne' ? 'लेक्चर नोट्स' : 'Lecture Notes',
            'assignment' => $locale === 'ne' ? 'एसाइनमेंट' : 'Assignment',
            'lab_report' => $locale === 'ne' ? 'ल्याब रिपोर्ट' : 'Lab Report',
            'assessment' => $locale === 'ne' ? 'मूल्यांकन' : 'Assessment',
            'study_guide' => $locale === 'ne' ? 'अध्ययन गाइड' : 'Study Guide',
            'syllabus' => $locale === 'ne' ? 'पाठ्यक्रम' : 'Syllabus',
            'project_material' => $locale === 'ne' ? 'प्रोजेक्ट सामग्री' : 'Project Material',
        ];

        return view('resources.index', compact('materials', 'type', 'query', 'typeOptions'));
    }

    public function download($id)
    {
        $material = StudyMaterial::published()
            ->whereIn('visibility', ['all', 'students'])
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

