<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    /**
     * Display a listing of study materials.
     */
    public function index(Request $request)
    {
        $query = StudyMaterial::with('subject');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->has('category') && $request->category) {
            $query->where('document_type', $request->category);
        }

        $materials = $query->latest()->paginate(10);
        $courses = Subject::select('id', 'subject_name', 'subject_code', 'semester')->get();

        // Get statistics
        $stats = [
            'total' => StudyMaterial::count(),
            'notes' => StudyMaterial::where('document_type', 'lecture_notes')->count(),
            'assignments' => StudyMaterial::where('document_type', 'assignment')->count(),
            'papers' => StudyMaterial::where('document_type', 'assessment')->count(),
            'lab_reports' => StudyMaterial::where('document_type', 'lab_report')->count(),
        ];

        return view('admin.study-material.index', compact('materials', 'courses', 'stats'));
    }

    /**
     * Store a newly created study material (AJAX).
     */
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'semester' => 'required|string',
            'course' => 'required|string',
            'visibility' => 'required|in:all,students,teachers,admins',
            'document_type' => 'required|in:lecture_notes,assignment,lab_report,assessment,study_guide,syllabus,project_material',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            DB::beginTransaction();

            // Look up subject_id from subject_name (course)
            $subject = Subject::where('subject_name', $validated['course'])->first();
            $subjectId = $subject ? $subject->id : 1;

            $material = new StudyMaterial();
            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            $material->semester = $validated['semester'];
            $material->subject_id = $subjectId;
            $material->visibility = $validated['visibility'];
            $material->document_type = $validated['document_type'];
            $material->teacher_id = Auth::id() ?? 1;
            $material->is_published = true;
            $material->uploaded_at = now();
            
            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('study-materials', $fileName, 'public');
                
                $material->file_name = $file->getClientOriginalName();
                $material->file_path = $filePath;
                $material->file_size = $file->getSize();
            } else {
                $material->file_name = null;
                $material->file_path = null;
                $material->file_size = null;
            }

            $material->save();

            // Load relationships for the row view
            $material->load('subject');

            // Get the row HTML
            $rowHtml = view('admin.study-material.partials.material-row', ['material' => $material])->render();
            
            // Get updated statistics
            $stats = [
                'total' => StudyMaterial::count(),
                'notes' => StudyMaterial::where('document_type', 'lecture_notes')->count(),
                'assignments' => StudyMaterial::where('document_type', 'assignment')->count(),
                'papers' => StudyMaterial::where('document_type', 'assessment')->count(),
                'lab_reports' => StudyMaterial::where('document_type', 'lab_report')->count(),
            ];

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Study material uploaded successfully.',
                'material' => [
                    'id' => $material->id,
                    'title' => $material->title,
                ],
                'row_html' => $rowHtml,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload study material. Please try again. Error: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get HTML for a single material row.
     */
    public function getMaterialRow($id)
    {
        try {
            $material = StudyMaterial::with('subject')->findOrFail($id);
            $rowHtml = view('admin.study-material.partials.material-row', ['material' => $material])->render();
            
            return response()->json([
                'success' => true,
                'row_html' => $rowHtml,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found.',
            ], 404);
        }
    }
    
    /**
     * Store a newly created study material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'semester' => 'required|string',
            'course' => 'required|string',
            'visibility' => 'required|in:all,students,teachers,admins',
            'document_type' => 'required|in:lecture_notes,assignment,lab_report,assessment,study_guide,syllabus,project_material',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            DB::beginTransaction();

            // Look up subject_id from subject_name (course)
            $subject = Subject::where('subject_name', $validated['course'])->first();
            $subjectId = $subject ? $subject->id : 1;

            $material = new StudyMaterial();
            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            $material->semester = $validated['semester'];
            $material->subject_id = $subjectId;
            $material->visibility = $validated['visibility'];
            $material->document_type = $validated['document_type'];
            $material->teacher_id = Auth::id() ?? 1;
            $material->is_published = true;
            $material->uploaded_at = now();
            
            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('study-materials', $fileName, 'public');
                
                $material->file_name = $file->getClientOriginalName();
                $material->file_path = $filePath;
                $material->file_size = $file->getSize();
            } else {
                $material->file_name = null;
                $material->file_path = null;
                $material->file_size = null;
            }

            $material->save();

            DB::commit();

            return redirect()->route('admin.study-material')
                ->with('success', 'Study material uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to upload study material. Please try again. Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update an existing study material.
     */
    public function update(Request $request, $id)
    {
        $material = StudyMaterial::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'semester' => 'required|string',
            'course' => 'required|string',
            'visibility' => 'required|in:all,students,teachers,admins',
            'document_type' => 'required|in:lecture_notes,assignment,lab_report,assessment,study_guide,syllabus,project_material',
            'file' => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            DB::beginTransaction();

            // Look up subject_id from subject_name (course)
            $subject = Subject::where('subject_name', $validated['course'])->first();
            $subjectId = $subject ? $subject->id : 1;

            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            $material->semester = $validated['semester'];
            $material->subject_id = $subjectId;
            $material->visibility = $validated['visibility'];
            $material->document_type = $validated['document_type'];
            
            // Handle file upload (optional update)
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                    Storage::disk('public')->delete($material->file_path);
                }
                
                $file = $request->file('file');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('study-materials', $fileName, 'public');
                
                $material->file_name = $file->getClientOriginalName();
                $material->file_path = $filePath;
                $material->file_size = $file->getSize();
            }

            $material->save();

            DB::commit();

            return redirect()->route('admin.study-material')
                ->with('success', 'Study material updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update study material. Please try again. Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download a study material file.
     */
    public function download($id)
    {
        $material = StudyMaterial::findOrFail($id);

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download(
            $material->file_path,
            $material->file_name
        );
    }

    /**
     * Delete a study material.
     */
    public function destroy($id)
    {
        $material = StudyMaterial::findOrFail($id);

        try {
            // Delete file if exists
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $material->delete();

            return redirect()->route('admin.study-material')
                ->with('success', 'Study material deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete study material. Please try again.');
        }
    }
}

