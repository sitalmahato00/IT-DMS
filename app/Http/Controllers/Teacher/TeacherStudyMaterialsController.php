<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\StudyMaterial;
use Illuminate\Support\Facades\Storage;

class TeacherStudyMaterialsController extends Controller
{
    private function getTeacherSubjectIds()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return [];
        }
        
        return SubjectTeacher::where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->toArray();
    }

    /**
     * Display study materials for teacher's subjects
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return view('teacher.study-materials', [
                'studyMaterials' => collect([]),
                'subjects' => collect([]),
                'selectedSubject' => null,
            ]);
        }

        $subjectIds = $this->getTeacherSubjectIds();
        
        if (empty($subjectIds)) {
            return view('teacher.study-materials', [
                'studyMaterials' => collect([]),
                'subjects' => collect([]),
                'selectedSubject' => null,
            ]);
        }

        $subject = $request->get('subject', '');
        $semester = $request->get('semester', '');
        $search = $request->get('q', '');

        // Get teacher's first assigned semester for auto-selection
        $firstAssignment = SubjectTeacher::where('teacher_id', $teacher->id)
            ->orderBy('semester', 'asc')
            ->first();
        $defaultSemester = $firstAssignment ? $firstAssignment->semester : null;

        // Auto-select first semester if none selected
        if (empty($semester) && $defaultSemester) {
            $semester = $defaultSemester;
        }

        // Get unique semesters from assignments
        $semesters = SubjectTeacher::where('teacher_id', $teacher->id)
            ->whereNotNull('semester')
            ->distinct()
            ->pluck('semester')
            ->sort()
            ->values()
            ->toArray();

        // Get subjects for dropdown
        $subjects = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get()
            ->map(function ($st) {
                if (!$st->subject) {
                    return null;
                }

                return [
                    'id' => $st->subject->id,
                    'name' => $st->subject->subject_name,
                    'code' => $st->subject->subject_code,
                ];
            })
            ->filter()
            ->values();

        // Get study materials
        $materialsQuery = StudyMaterial::whereIn('subject_id', $subjectIds)
            ->with('subject')
            ->orderBy('created_at', 'desc');

        // Filter by semester if selected
        if (!empty($semester)) {
            $materialsQuery->whereHas('subject', function($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }

        $subjectId = (int) $subject;
        if ($subject !== '' && $subject !== null && in_array($subjectId, array_map('intval', $subjectIds), true)) {
            $materialsQuery->where('subject_id', $subjectId);
        }

        if (!empty($search)) {
            $materialsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $studyMaterials = $materialsQuery->paginate(20)->withQueryString();
        $studyMaterials->getCollection()->transform(function ($material) {
            return [
                'id' => $material->id,
                'title' => $material->title,
                'subject_code' => $material->subject?->subject_code ?? '',
                'subject_name' => $material->subject?->subject_name ?? 'N/A',
                'file_type' => $material->file_extension ?: 'file',
                'file_size' => $material->formatted_size,
                'download_count' => $material->download_count ?? 0,
                'created_at' => optional($material->created_at)->format('M d, Y'),
            ];
        });

        $totalSizeBytes = StudyMaterial::whereIn('subject_id', $subjectIds)->sum('file_size');
        $totalSize = $this->formatBytes((int) $totalSizeBytes);

        return view('teacher.study-materials', [
            'studyMaterials' => $studyMaterials,
            'subjects' => $subjects,
            'selectedSubject' => $subject,
            'selectedSemester' => $semester,
            'semesters' => $semesters,
            'totalDownloads' => 0,
            'totalSize' => $totalSize,
        ]);
    }

    /**
     * Show form to upload study material.
     */
    public function create()
    {
        $teacher = auth()->user()?->teacher;
        if (!$teacher) {
            return redirect()->route('teacher.study-materials')
                ->with('error', 'Teacher profile not found.');
        }

        $subjects = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get()
            ->map(function ($assignment) {
                if (!$assignment->subject) {
                    return null;
                }

                return [
                    'id' => $assignment->subject->id,
                    'name' => $assignment->subject->subject_name,
                    'code' => $assignment->subject->subject_code,
                    'semester' => $assignment->semester ?? $assignment->subject->semester,
                ];
            })
            ->filter()
            ->values();

        return view('teacher.study-materials-create', [
            'subjects' => $subjects,
            'documentTypes' => [
                'lecture_notes' => 'Lecture Notes',
                'assignment' => 'Assignment',
                'lab_report' => 'Lab Report',
                'assessment' => 'Assessment',
                'study_guide' => 'Study Guide',
                'syllabus' => 'Syllabus',
                'project_material' => 'Project Material',
            ],
        ]);
    }

    /**
     * Store new study material uploaded by teacher.
     */
    public function store(Request $request)
    {
        $teacher = auth()->user()?->teacher;
        if (!$teacher) {
            return redirect()->route('teacher.study-materials')
                ->with('error', 'Teacher profile not found.');
        }

        $subjectIds = array_map('intval', $this->getTeacherSubjectIds());

        $validated = $request->validate([
            'subject_id' => 'required|integer',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'document_type' => 'required|in:lecture_notes,assignment,lab_report,assessment,study_guide,syllabus,project_material',
            'visibility' => 'required|in:all,students,faculty',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
        ]);

        $subjectId = (int) $validated['subject_id'];
        if (!in_array($subjectId, $subjectIds, true)) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'You can only upload materials for your own subjects.']);
        }

        $assignment = SubjectTeacher::where('teacher_id', $teacher->id)
            ->where('subject_id', $subjectId)
            ->with('subject')
            ->first();

        $file = $request->file('file');
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('study-materials', $safeName, 'public');

        StudyMaterial::create([
            'subject_id' => $subjectId,
            'teacher_id' => auth()->id(),
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
            'semester' => $assignment?->semester ?? $assignment?->subject?->semester,
            'visibility' => $validated['visibility'],
            'is_published' => true,
            'uploaded_at' => now(),
        ]);

        return redirect()->route('teacher.study-materials')
            ->with('success', 'Study material uploaded successfully.');
    }

    /**
     * Download study material
     */
    public function download($id)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            abort(403, 'Unauthorized');
        }

        $subjectIds = $this->getTeacherSubjectIds();

        $material = StudyMaterial::where('id', $id)
            ->whereIn('subject_id', $subjectIds)
            ->first();

        if (!$material) {
            abort(404, 'Material not found');
        }

        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            return Storage::disk('public')->download(
                $material->file_path,
                $material->file_name ?: basename($material->file_path)
            );
        }

        abort(404, 'File not found');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }
}

