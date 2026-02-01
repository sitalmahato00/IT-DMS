<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StudyMaterialController extends Controller
{
    /**
     * Display a listing of study materials.
     */
    public function index(Request $request)
    {
        // Fetch study materials with relationships
        $query = StudyMaterial::with('course', 'uploader');

        // Apply filters
        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $materials = $query->orderBy('created_at', 'desc')->paginate(10);

        // Fetch all courses for the dropdown
        $courses = \App\Models\Subject::orderBy('semester')->orderBy('subject_name')->get();

        // Get statistics
        $stats = [
            'total' => StudyMaterial::count(),
            'notes' => StudyMaterial::where('category', 'notes')->count(),
            'assignments' => StudyMaterial::where('category', 'assignment')->count(),
            'papers' => StudyMaterial::where('category', 'paper')->count(),
        ];

        return view('admin.study-material.index', compact('materials', 'courses', 'stats'));
    }

    /**
     * Store a newly created study material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'semester' => 'required|string',
            'course_id' => 'required|exists:subjects,id',
            'category' => 'required|in:notes,assignment,paper,other',
            'description' => 'nullable|string|max:500',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar',
        ]);

        try {
            DB::beginTransaction();

            $material = new StudyMaterial();
            $material->title = $validated['title'];
            $material->semester = $validated['semester'];
            $material->subject_id = $validated['course_id'];
            $material->category = $validated['category'];
            $material->description = $validated['description'] ?? null;
            $material->uploaded_by = Auth::id();

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('study-materials', $fileName, 'local');
                $material->file_name = $file->getClientOriginalName();
                $material->file_path = $filePath;
                $material->file_size = $file->getSize();
                $material->file_type = $file->getClientMimeType();
            }

            $material->save();

            DB::commit();

            return redirect()->route('study-material')
                ->with('success', 'Study material uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to upload study material. Please try again.')
                ->withInput();
        }
    }

    /**
     * Download a study material file.
     */
    public function download($id)
    {
        $material = StudyMaterial::findOrFail($id);

        if (!$material->file_path || !Storage::exists($material->file_path)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }

        return Storage::download($material->file_path, $material->file_name);
    }

    /**
     * Remove the specified study material.
     */
    public function destroy($id)
    {
        $material = StudyMaterial::findOrFail($id);

        try {
            // Delete file from storage
            if ($material->file_path && Storage::exists($material->file_path)) {
                Storage::delete($material->file_path);
            }

            $material->delete();

            return redirect()->route('study-material')
                ->with('success', 'Study material deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete study material. Please try again.');
        }
    }

    /**
     * Get materials by category for AJAX requests.
     */
    public function byCategory(Request $request)
    {
        $category = $request->get('category');

        $materials = StudyMaterial::with('course', 'uploader')
            ->when($category, function($query) use ($category) {
                return $query->where('category', $category);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'materials' => $materials,
            'counts' => [
                'total' => StudyMaterial::count(),
                'notes' => StudyMaterial::where('category', 'notes')->count(),
                'assignments' => StudyMaterial::where('category', 'assignment')->count(),
                'papers' => StudyMaterial::where('category', 'paper')->count(),
                'other' => StudyMaterial::where('category', 'other')->count(),
            ]
        ]);
    }
}

