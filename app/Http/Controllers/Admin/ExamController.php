<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        try {
            $query = Exam::with(['subject', 'course']);

            // Apply filters
            if ($request->has('academic_year') && $request->academic_year) {
                $query->forYear($request->academic_year);
            }

            if ($request->has('semester') && $request->semester) {
                $query->forSemester($request->semester);
            }

            if ($request->has('course_id') && $request->course_id) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->has('subject_id') && $request->subject_id) {
                $query->forSubject($request->subject_id);
            }

            if ($request->has('exam_type') && $request->exam_type) {
                $query->byType($request->exam_type);
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('exam_name', 'like', "%{$search}%")
                      ->orWhere('exam_name_ne', 'like', "%{$search}%");
                });
            }

            // Validate per_page - only allow safe values
            $allowedPerPage = [10, 25, 50];
            $perPage = (int) $request->query('per_page', 10);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 10;
            }
            // normalize request param so pagination links use a sanitized value
            $request->merge(['per_page' => $perPage]);

            $exams = $query->ordered()->paginate($perPage)->appends($request->all());

            // Get filter data
            $academicYears = $this->getAcademicYears();
            $semesters = $this->getSemesters();
            $courses = Course::all();
            $subjects = Subject::all();

            $stats = $this->getStatistics();

            if ($request->ajax()) {
                $tableRows = view('admin.partials.exams_table_rows', compact('exams'))->render();
                $tableFooter = view('admin.partials.exams_table_footer', compact('exams'))->render();
                $statsHtml = view('admin.partials.exams_stats', compact('stats'))->render();

                return response()->json([
                    'success' => true,
                    'table_rows' => $tableRows,
                    'table_footer' => $tableFooter,
                    'stats' => $statsHtml,
                ]);
            }

            return view('admin.assessment', compact('exams', 'academicYears', 'semesters', 'courses', 'subjects', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading exams: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load exams: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'exam_name' => 'required|string|max:255',
                'exam_name_ne' => 'nullable|string|max:255',
                'academic_year' => 'required|string',
                'semester' => 'required|string',
                'subject_id' => 'nullable|exists:subjects,id',
                'course_id' => 'nullable|exists:courses,id',
                'exam_type' => 'required|string|in:internal,final,midterm,practical,viva,assignment,assessment',
                'full_marks' => 'required|integer|min:0',
                'passing_marks' => 'required|integer|min:0',
'exam_date' => 'required|date',
                'exam_date_bs' => 'nullable|string',
                'status' => 'required|in:draft,published,archived',
                'description' => 'nullable|string',
                'description_ne' => 'nullable|string',
                'instructions' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $validated['created_by'] = auth()->id();

            $exam = Exam::create($validated);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Exam created successfully!',
                    'exam' => $exam,
                ], 201);
            }

            return redirect()->route('admin.assessment')
                ->with('success', 'Exam created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating exam: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create exam: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        try {
            $validated = $request->validate([
                'exam_name' => 'required|string|max:255',
                'exam_name_ne' => 'nullable|string|max:255',
                'academic_year' => 'required|string',
                'semester' => 'required|string',
                'subject_id' => 'nullable|exists:subjects,id',
                'course_id' => 'nullable|exists:courses,id',
                'exam_type' => 'required|string|in:internal,final,midterm,practical,viva,assignment,assessment',
                'full_marks' => 'required|integer|min:0',
                'passing_marks' => 'required|integer|min:0',
                'exam_date' => 'required|date',
                'exam_date_bs' => 'nullable|string',
                'status' => 'required|in:draft,published,archived',
                'description' => 'nullable|string',
                'description_ne' => 'nullable|string',
                'instructions' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $exam->update($validated);

            DB::commit();

            return redirect()->route('admin.assessment')
                ->with('success', 'Exam updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating exam: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        try {
            DB::beginTransaction();

            // Delete associated marks first
            ExamMark::where('exam_id', $exam->id)->delete();

            $exam->delete();

            DB::commit();

            return redirect()->route('admin.assessment')
                ->with('success', 'Exam deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting exam: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        try {
            $exam->load(['subject', 'course', 'marks.student']);
            
            // Get statistics
            $totalStudents = $exam->marks()->count();
            $averageMarks = $exam->marks()->avg('marks_obtained');
            $passCount = $exam->marks()->where('percentage', '>=', 35)->count();
            $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

            return view('admin.exam-show', compact('exam', 'totalStudents', 'averageMarks', 'passRate'));
        } catch (\Exception $e) {
            Log::error('Error showing exam: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to load exam details: ' . $e->getMessage());
        }
    }

    /**
     * Get exam data for editing via AJAX.
     */
    public function getExamData(Exam $exam)
    {
        try {
            return response()->json([
                'success' => true,
                'exam' => [
                    'id' => $exam->id,
                    'exam_name' => $exam->exam_name,
                    'exam_name_ne' => $exam->exam_name_ne,
                    'academic_year' => $exam->academic_year,
                    'semester' => $exam->semester,
                    'course_id' => $exam->course_id,
                    'subject_id' => $exam->subject_id,
                    'exam_type' => $exam->exam_type,
                    'full_marks' => $exam->full_marks,
                    'passing_marks' => $exam->passing_marks,
                    'exam_date' => $exam->exam_date->format('Y-m-d'),
                    'status' => $exam->status,
                    'description' => $exam->description,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting exam data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exam data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle exam status.
     */
    public function toggleStatus(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,published,archived'
            ]);

            $exam->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Exam status updated successfully!',
                'new_status' => $exam->formatted_status
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling exam status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload marks for an exam.
     */
    public function uploadMarks(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'marks' => 'required|array',
                'marks.*.student_id' => 'required|exists:students,id',
                'marks.*.marks_obtained' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            foreach ($request->marks as $markData) {
                $fullMarks = $exam->full_marks;
                $marksObtained = $markData['marks_obtained'];
                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

                ExamMark::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $markData['student_id']
                    ],
                    [
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.assessment')
                ->with('success', 'Marks uploaded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to upload marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get students for a specific exam.
     */
    public function getStudentsForExam(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'course_id' => 'nullable|exists:courses,id',
                'semester' => 'nullable|string',
                'batch' => 'nullable|string',
                'subject_id' => 'nullable|exists:subjects,id',
            ]);

            $query = Student::with(['user']);

            if ($request->course_id) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->semester) {
                $query->where('semester', $request->semester);
            }

            if ($request->batch) {
                $query->where('batch_year', $request->batch);
            }

            $students = $query->orderBy('roll_no')->get();

            // Get existing marks for this exam
            $existingMarks = ExamMark::where('exam_id', $exam->id)
                ->pluck('marks_obtained', 'student_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'students' => $students,
                'existing_marks' => $existingMarks,
                'full_marks' => $exam->full_marks,
                'passing_marks' => $exam->passing_marks,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting students for exam: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects by semester for exam creation.
     */
    public function getSubjectsBySemester(Request $request)
    {
        try {
            $request->validate([
                'semester' => 'required|string',
                'course_id' => 'nullable|exists:courses,id',
            ]);

            $query = Subject::where('semester', $request->semester);

            if ($request->course_id) {
                $query->where('course_id', $request->course_id);
            }

            $subjects = $query->orderBy('subject_name')->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting subjects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate grade from percentage.
     */
    protected function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    /**
     * Get list of academic years.
     */
    protected function getAcademicYears(): array
    {
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[] = ($i - 1) . '-' . $i;
        }
        return $years;
    }

    /**
     * Get list of semesters.
     */
    protected function getSemesters(): array
    {
        return [
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'third' => 'Third Semester',
            'fourth' => 'Fourth Semester',
            'fifth' => 'Fifth Semester',
            'sixth' => 'Sixth Semester',
        ];
    }

    /**
     * Get exam statistics for dashboard.
     */
    public function getStatistics(): array
    {
        return [
            'total_exams' => Exam::count(),
            'published_exams' => Exam::where('status', 'published')->count(),
            'draft_exams' => Exam::where('status', 'draft')->count(),
            'total_marks_entries' => ExamMark::count(),
        ];
    }
}

