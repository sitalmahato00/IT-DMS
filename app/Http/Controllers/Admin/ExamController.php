<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Student;
use App\Models\User;
use App\Models\Attendance;
use App\Helpers\NepaliContentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Traits\LogsActivity;
use App\Notifications\ResultNotification;
use App\Notifications\ExamNotification;
use App\Services\NotificationService;

class ExamController extends Controller
{
    use LogsActivity;

    private const SEMESTER_KEY_TO_NUMBER = [
        'first' => '1',
        'second' => '2',
        'third' => '3',
        'fourth' => '4',
        'fifth' => '5',
        'sixth' => '6',
    ];

    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        try {
            $query = Exam::with(['subject'])->select('*', 'assessment_number');

            // Apply filters - use filled() to properly handle empty string values
            if ($request->filled('academic_year') && $request->academic_year) {
                $query = $query->forYear($request->academic_year);
            }

            // Handle semester filter - convert text to number or handle 'all' or empty
            // Empty string '' means show all exams (all semesters + all semester-specific)
            // 'all' means show ONLY exams where semester = 'all' (exams that apply to all semesters)
            if ($request->filled('semester') && $request->semester) {
                if ($request->semester === 'all') {
                    // Show only exams where semester = 'all' (exams that apply to all semesters)
                    $query = $query->where('semester', 'all');
                } else {
                    // Support both stored formats: "first" and "1" (legacy)
                    $requested = strtolower((string) $request->semester);
                    $keyToNumber = self::SEMESTER_KEY_TO_NUMBER;
                    $numberToKey = array_flip($keyToNumber);

                    $numericSemester = $keyToNumber[$requested] ?? $requested;
                    $textSemester = $numberToKey[$requested] ?? $requested;

                    $candidates = array_values(array_unique(array_filter([
                        $requested,
                        $numericSemester,
                        $textSemester,
                    ])));

                    // Show only exams for the specific semester (not including 'all')
                    $query = $query->whereIn('semester', $candidates);
                }
            }
            // If empty string, don't filter - show all exams

            if ($request->filled('subject_id') && $request->subject_id) {
                $query = $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('exam_category') && $request->exam_category) {
                $query = $query->where('exam_category', $request->exam_category);
            }

            if ($request->filled('status') && $request->status) {
                $status = $request->status;
                
                // Handle marks_filled and marks_not_filled status filters
                if ($status === 'marks_filled') {
                    // Get exams that have at least one mark entry
                    $query->whereHas('marks', function ($q) {
                        $q->whereNotNull('marks_obtained');
                    });
                } elseif ($status === 'marks_not_filled') {
                    // Get exams that have no mark entries or all marks are null
                    $query->whereDoesntHave('marks', function ($q) {
                        $q->whereNotNull('marks_obtained');
                    });
                } elseif ($status === 'upcoming') {
                    // Get exams with future exam dates
                    $query->where('exam_date', '>', now()->toDateString());
                } elseif ($status === 'completed') {
                    // Get exams with past exam dates
                    $query->where('exam_date', '<', now()->toDateString());
                } else {
                    // Standard status filter (published, draft, archived, faculty)
                    $query = $query->where('status', $status);
                }
            }

            if ($request->filled('search') && $request->search) {
                $search = $request->search;
                $query = $query->where(function($q) use ($search) {
                    $q->where('exam_name', 'like', "%{$search}%")
                      ->orWhere('exam_name_ne', 'like', "%{$search}%");
                });
            }

            // Order by created_at descending (newest first)
            $query = $query->orderBy('created_at', 'desc');

            // Validate per_page - only allow safe values
            $allowedPerPage = [10, 25, 50];
            $perPage = (int) $request->query('per_page', 10);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 10;
            }
            // normalize request param so pagination links use a sanitized value
            $request->merge(['per_page' => $perPage]);

            $exams = $query->paginate($perPage)->appends($request->all());

            // Get filter data
            $academicYears = $this->getAcademicYears();
            $semesters = $this->getSemesters();
            $subjects = Subject::all();

            $stats = $this->getStatistics();

            // Semester cards data (for UI grouping/navigation)
            $semesterCards = $this->buildSemesterCards($request);
            [$selectedSemesterLabel, $selectedSemesterSubjects] = $this->getSelectedSemesterSubjects($request);

            // Subject filter options should follow selected semester if set; otherwise include all subjects
            $subjectOptions = ($selectedSemesterSubjects && $selectedSemesterSubjects->count())
                ? $selectedSemesterSubjects
                : $subjects;

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

            return view('admin.exam', compact(
                'exams',
                'academicYears',
                'semesters',
                'subjects',
                'subjectOptions',
                'stats',
                'semesterCards',
                'selectedSemesterLabel',
                'selectedSemesterSubjects'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading exams: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load exams: ' . $e->getMessage());
        }
    }

    /**
     * Get subjects by semester for exam views/modals.
     * Returns both a flat list and a "grouped" map (by category) for optgroups.
     */
    public function getSubjectsBySemester(Request $request)
    {
        $semester = (string) $request->get('semester', '');
        $semester = trim($semester);

        $isAll = ($semester === '' || strtolower($semester) === 'all');

        try {
            $candidates = $isAll ? null : $this->getSemesterCandidates($semester);

            $subjectsQuery = Subject::query()
                ->active()
                ->withCount(['students as active_students_count' => function ($q) {
                    $q->where('status', 'active')
                      ->where(function ($q2) {
                          $q2->where('is_alumni', 0)
                             ->orWhereNull('is_alumni');
                      });
                }]);

            if (!$isAll) {
                $subjectsQuery->whereIn('semester', $candidates);
            }

            $subjects = $subjectsQuery
                ->select(['id', 'subject_name', 'subject_code', 'semester', 'category'])
                ->orderBy('category')
                ->orderBy('subject_name')
                ->get();

            $grouped = $subjects
                ->groupBy(function ($s) {
                    $cat = $s->category ?? '';
                    $cat = is_string($cat) ? trim($cat) : '';
                    return $cat !== '' ? $cat : 'Other';
                })
                ->map(function ($items) {
                    return $items->map(function ($s) {
                        return [
                            'id' => (string) $s->id,
                            'subject_name' => $s->subject_name,
                            'subject_code' => $s->subject_code,
                            'semester' => (string) $s->semester,
                            'has_lab' => (bool) $s->has_lab,
                        ];
                    })->values();
                });

            return response()->json([
                'success' => true,
                'subjects' => $subjects->map(function ($s) {
                    return [
                        'id' => (string) $s->id,
                        'subject_name' => $s->subject_name,
                        'subject_code' => $s->subject_code,
                        'semester' => (string) $s->semester,
                        'has_lab' => (bool) $s->has_lab,
                        'active_students' => (int) ($s->active_students_count ?? 0),
                    ];
                })->values(),
                'grouped' => $grouped,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting subjects by semester: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subjects: ' . $e->getMessage(),
            ], 500);
        }
    }

/**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'assessment_number' => 'nullable|integer|min:1',
                'exam_name' => 'required|string|max:255',
                'exam_name_ne' => 'nullable|string|max:255',
                'academic_year' => 'nullable|string',
                'semester' => 'nullable|string|in:all,first,second,third,fourth,fifth,sixth',
                'subject_id' => 'nullable|string',
                'exam_category' => 'required|string|in:assessment,ctevt,general',
                'full_marks' => 'required|integer|min:0',
                'passing_marks' => 'required|integer|min:0',
                'theory_internal_max_marks' => 'nullable|numeric|min:0',
                'theory_external_max_marks' => 'nullable|numeric|min:0',
                'practical_internal_max_marks' => 'nullable|numeric|min:0',
                'practical_external_max_marks' => 'nullable|numeric|min:0',
                'theory_internal_pass_marks' => 'nullable|numeric|min:0',
                'theory_external_pass_marks' => 'nullable|numeric|min:0',
                'practical_internal_pass_marks' => 'nullable|numeric|min:0',
                'practical_external_pass_marks' => 'nullable|numeric|min:0',
                'exam_date' => 'required|date',
                'exam_date_bs' => 'nullable|string',
                'status' => 'required|in:draft,published,archived,faculty',
                'description' => 'nullable|string',
                'description_ne' => 'nullable|string',
                'instructions' => 'nullable|string',
            ]);

            // Ensure semester has a sensible default ('all') if not provided
            if (empty($validated['semester'])) {
                $validated['semester'] = 'all';
            }

            // Handle subject_id: convert 'all' to null, validate real IDs
            $subjectId = $validated['subject_id'] ?? null;
            
            if ($subjectId && $subjectId !== 'all') {
                // Specific subject selected - validate it exists
                if (!Subject::where('id', $subjectId)->exists()) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'The selected subject id is invalid.',
                        ], 422);
                    }
                    return redirect()->back()
                        ->withErrors(['subject_id' => 'The selected subject id is invalid.'])
                        ->withInput();
                }
                $validated['subject_id'] = (int)$subjectId;
            } else {
                // 'all' selected or no subject - store as null
                $validated['subject_id'] = null;
            }

            DB::beginTransaction();

            $category = $validated['exam_category'] ?? ($exam->exam_category ?? 'assessment');
            $typeMap = [
                'assessment' => 'assessment',
                'ctevt' => 'practical',
                'general' => 'assessment',
            ];
            $validated['exam_type'] = $typeMap[$category] ?? 'assessment';
            $validated['created_by'] = auth()->id();

            if ($category !== 'assessment') {
                $validated['assessment_number'] = null;
            } else {
                if (empty($validated['assessment_number']) && !empty($validated['semester'])) {
                    $validated['assessment_number'] = Exam::getNextAssessmentNumber(
                        $validated['subject_id'],
                        $validated['semester'],
                        $validated['academic_year'] ?? null
                    );
                }
            }

            if ($category !== 'ctevt') {
                $validated['theory_internal_max_marks'] = null;
                $validated['theory_external_max_marks'] = null;
                $validated['practical_internal_max_marks'] = null;
                $validated['practical_external_max_marks'] = null;
                $validated['theory_internal_pass_marks'] = null;
                $validated['theory_external_pass_marks'] = null;
                $validated['practical_internal_pass_marks'] = null;
                $validated['practical_external_pass_marks'] = null;
            }

            $exam = Exam::create($validated);

            DB::commit();

            // Log activity
            $this->logActivity('Exam', 'Created Exam', "Exam '{$exam->exam_name}' created for {$exam->semester} semester");

            // Notifications are sent automatically by NotificationObserver

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Exam created successfully!',
                    'exam' => $exam,
                ], 201);
            }

            return redirect()->route('admin.exam')
                ->with('success', 'Exam created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
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
            // Handle JSON requests properly
            $input = $request->all();
            if ($request->expectsJson() || str_contains($request->header('Content-Type', ''), 'application/json')) {
                $input = $request->json()->all();
            }

            // Normalize semester using existing logic
            if (isset($input['semester'])) {
                $candidates = $this->getSemesterCandidates($input['semester']);
                $input['semester'] = $candidates[0] ?? $input['semester']; // Use first valid candidate
            }

            // Pre-process and validate input
            $validator = \Validator::make($input, [
                'assessment_number' => 'sometimes|nullable|integer|min:1',
                'exam_name' => 'sometimes|required|string|max:255',
                'exam_name_ne' => 'sometimes|nullable|string|max:255',
                'academic_year' => 'sometimes|nullable|string',
                'semester' => 'sometimes|required|string|in:all,first,second,third,fourth,fifth,sixth,1,2,3,4,5,6',
                'subject_id' => 'sometimes|nullable|string',
                'exam_category' => 'sometimes|required|string|in:assessment,ctevt,general',
                'full_marks' => 'sometimes|required|integer|min:0',
                'passing_marks' => 'sometimes|required|integer|min:0',
                'theory_internal_max_marks' => 'sometimes|nullable|numeric|min:0',
                'theory_external_max_marks' => 'sometimes|nullable|numeric|min:0',
                'practical_internal_max_marks' => 'sometimes|nullable|numeric|min:0',
                'practical_external_max_marks' => 'sometimes|nullable|numeric|min:0',
                'theory_internal_pass_marks' => 'sometimes|nullable|numeric|min:0',
                'theory_external_pass_marks' => 'sometimes|nullable|numeric|min:0',
                'practical_internal_pass_marks' => 'sometimes|nullable|numeric|min:0',
                'practical_external_pass_marks' => 'sometimes|nullable|numeric|min:0',
                'exam_date' => 'sometimes|required|date',
                'exam_date_bs' => 'sometimes|nullable|string',
                'status' => 'sometimes|required|in:draft,published,archived,faculty',
                'description' => 'sometimes|nullable|string',
                'description_ne' => 'sometimes|nullable|string',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Handle subject_id: convert 'all' to null, validate real IDs
            $subjectId = $validated['subject_id'] ?? null;
            if ($subjectId === 'all') {
                $validated['subject_id'] = null;
            } elseif ($subjectId && is_numeric($subjectId)) {
                if (!Subject::where('id', $subjectId)->exists()) {
                    if ($request->ajax() || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid subject selected.',
                            'errors' => ['subject_id' => ['Subject does not exist.']],
                        ], 422);
                    }
                    return redirect()->back()->withErrors(['subject_id' => 'Subject does not exist.'])->withInput();
                }
                $validated['subject_id'] = (int)$subjectId;
            }

            DB::beginTransaction();

            $category = $validated['exam_category'] ?? 'assessment';
            $typeMap = [
                'assessment' => 'assessment',
                'ctevt' => 'practical',
                'general' => 'assessment',
            ];
            $validated['exam_type'] = $typeMap[$category] ?? 'assessment';

            if ($category !== 'assessment') {
                $validated['assessment_number'] = null;
            }

            if ($category !== 'ctevt') {
                $validated['theory_internal_max_marks'] = null;
                $validated['theory_external_max_marks'] = null;
                $validated['practical_internal_max_marks'] = null;
                $validated['practical_external_max_marks'] = null;
                $validated['theory_internal_pass_marks'] = null;
                $validated['theory_external_pass_marks'] = null;
                $validated['practical_internal_pass_marks'] = null;
                $validated['practical_external_pass_marks'] = null;
            }

            $exam->update($validated);

            DB::commit();

            // Log activity
            $this->logActivity('Exam', 'Updated Exam', "Exam '{$exam->exam_name}' was updated (ID: {$exam->id})");

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Exam updated successfully!',
                    'exam' => $exam->fresh(['subject']),
                ], 200);
            }

            return redirect()->route('admin.exam')
                ->with('success', 'Exam updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating exam: ' . $e->getMessage(), ['exam_id' => $exam->id, 'trace' => $e->getTraceAsString()]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update exam: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update exam: ' . $e->getMessage())->withInput();
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

            $examName = $exam->exam_name;
            $exam->delete();

            DB::commit();

            // Log activity
            $this->logActivity('Exam', 'Deleted Exam', "Exam '{$examName}' was deleted");

            // Notifications are sent automatically by NotificationObserver

            return redirect()->route('admin.exam')
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
$exam->load(['subject', 'marks.student']);
            
            // Get statistics
            $totalStudents = $exam->marks()->count();
            $averageMarks = $exam->marks()->avg('marks_obtained');
            $passCount = $exam->marks()->where('percentage', '>=', 35)->count();
            $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

            // Provide subjects list for upload modal (optional subject override)
            $subjects = Subject::orderBy('subject_name')->get();

            return view('admin.exam-show', compact('exam', 'totalStudents', 'averageMarks', 'passRate', 'subjects'));
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
            // Load subject relationship
            $exam->load(['subject']);
            
            return response()->json([
                'success' => true,
                'exam' => [
                    'id' => $exam->id,
                    'exam_name' => $exam->exam_name,
                    'exam_name_ne' => $exam->exam_name_ne,
                    'academic_year' => $exam->academic_year,
                    'semester' => $exam->semester,
                    'subject_id' => $exam->subject_id,
                    'subject' => $exam->subject ? [
                        'id' => $exam->subject->id,
                        'subject_name' => $exam->subject->subject_name,
                        'subject_code' => $exam->subject->subject_code,
                    ] : null,
                    'full_marks' => $exam->full_marks,
                    'passing_marks' => $exam->passing_marks,
                    'exam_category' => $exam->exam_category,
                    'theory_internal_max_marks' => $exam->theory_internal_max_marks,
                    'theory_external_max_marks' => $exam->theory_external_max_marks,
                    'practical_internal_max_marks' => $exam->practical_internal_max_marks,
                    'practical_external_max_marks' => $exam->practical_external_max_marks,
                    'theory_internal_pass_marks' => $exam->theory_internal_pass_marks,
                    'theory_external_pass_marks' => $exam->theory_external_pass_marks,
                    'assessment_number' => $exam->assessment_number ?? null,
                    'practical_internal_pass_marks' => $exam->practical_internal_pass_marks,
                    'practical_external_pass_marks' => $exam->practical_external_pass_marks,
                    'exam_date' => $exam->exam_date ? $exam->exam_date->format('Y-m-d') : null,
                    'exam_date_bs' => $exam->exam_date_bs,
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
     * Get subject-specific marks for exam
     */
    public function getSubjectMarks(Exam $exam, $subjectId)
    {
        try {
            // Get the average or first mark record to get the subject-specific full marks and passing marks
            $mark = ExamMark::where('exam_id', $exam->id)
                ->where('subject_id', $subjectId)
                ->first();

            if ($mark) {
                return response()->json([
                    'success' => true,
                    'marks' => [
                        'full_marks' => $mark->full_marks,
                        'passing_marks' => $mark->passing_marks ?? $exam->passing_marks,
                    ]
                ]);
            }

            // If no marks exist yet, return exam defaults
            return response()->json([
                'success' => true,
                'marks' => [
                    'full_marks' => $exam->full_marks,
                    'passing_marks' => $exam->passing_marks,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting subject marks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subject marks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload marks for an exam (traditional form submission).
     */
    public function uploadMarks(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'marks' => 'required|array',
                'marks.*.student_id' => 'required|exists:students,id',
                'marks.*.marks_obtained' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            // Update description if provided
            if ($request->has('description') && $request->description !== null) {
                $exam->update(['description' => $request->description]);
            }

            $createdCount = 0;
            $updatedCount = 0;

            foreach ($request->marks as $markData) {
                // Use subject-specific marks if provided, otherwise use exam's default marks
                $fullMarks = $markData['full_marks'] ?? $exam->full_marks;
                $passingMarks = $markData['passing_marks'] ?? $exam->passing_marks;
                
                $marksObtained = $markData['marks_obtained'];
                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

                $subjectId = $markData['subject_id'] ?? ($exam->subject_id ?? null);

                $existingQuery = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $markData['student_id']);
                if ($subjectId) {
                    $existingQuery->where('subject_id', $subjectId);
                } else {
                    $existingQuery->whereNull('subject_id');
                }

                $existing = $existingQuery->first();

                if ($existing) {
                    $existing->update([
                        'subject_id' => $subjectId,
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'passing_marks' => $passingMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                } else {
                    ExamMark::create([
                        'exam_id' => $exam->id,
                        'subject_id' => $subjectId,
                        'student_id' => $markData['student_id'],
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'passing_marks' => $passingMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();

            // Log activity
            $this->logActivity('Marks', 'Uploaded Marks', "Marks uploaded for exam '{$exam->exam_name}': Created {$createdCount}, Updated {$updatedCount}");

            // Send notification to admin about marks upload
            try {
                $adminUser = Auth::user();
                if ($adminUser) {
                    $adminUser->notify(new ResultNotification($exam, $createdCount + $updatedCount));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send notification: ' . $e->getMessage());
            }

            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Marks uploaded successfully! Created: {$createdCount}, Updated: {$updatedCount}"
                ]);
            }

            return redirect()->back()
                ->with('success', "Marks uploaded successfully! Created: {$createdCount}, Updated: {$updatedCount}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks: ' . $e->getMessage());
            
            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload marks: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Failed to upload marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Upload marks for an exam (AJAX).
     */
    public function uploadMarksAjax(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'marks' => 'required|array',
                'marks.*.student_id' => 'required|exists:students,id',
                'marks.*.marks_obtained' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $createdCount = 0;
            $updatedCount = 0;

            foreach ($request->marks as $markData) {
                $fullMarks = $exam->full_marks;
                $marksObtained = $markData['marks_obtained'];
                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

                $existing = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $markData['student_id'])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $updatedCount++;
                } else {
                    ExamMark::create([
                        'exam_id' => $exam->id,
                        'student_id' => $markData['student_id'],
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'percentage' => $percentage,
                        'grade' => $this->calculateGrade($percentage),
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $markData['remarks'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();

            // Reload exam with marks
$exam->load(['subject', 'marks.student.user']);

            // Get updated statistics
            $totalStudents = $exam->marks()->count();
            $averageMarks = $exam->marks()->avg('marks_obtained');
            $passCount = $exam->marks()->where('percentage', '>=', 35)->count();
            $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

            // Get updated marks rows HTML
            $marksRowsHtml = view('admin.exam-show-partials.marks-rows', ['exam' => $exam])->render();

            return response()->json([
                'success' => true,
                'message' => "Marks uploaded successfully! Created: {$createdCount}, Updated: {$updatedCount}",
                'stats' => [
                    'total_students' => $totalStudents,
                    'average_marks' => round($averageMarks, 2),
                    'pass_rate' => $passRate,
                ],
                'marks_html' => $marksRowsHtml,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks (AJAX): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload marks: ' . $e->getMessage(),
            ], 500);
        }
    }

/**
     * Get students for a specific exam.
     */
    public function getStudentsForExam(Request $request, Exam $exam)
    {
        try {
            // Validate - allow empty values for "All" selection
            $request->validate([
                'semester' => 'nullable|string',
                'batch' => 'nullable|string',
                'academic_year' => 'nullable|string',
                'academic_year_bs' => 'nullable|string',
                'subject_id' => 'nullable|string',
            ]);

            // Map semester names to numbers for student filtering
            $semesterMap = [
                'first' => '1',
                'second' => '2',
                'third' => '3',
                'fourth' => '4',
                'fifth' => '5',
                'sixth' => '6',
            ];

            $query = Student::with(['user', 'subjects'])
                ->where('is_active', true)
                ->where('is_alumni', false);

            // Filter by semester if selected (not "All")
            if ($request->semester && $request->semester !== '' && $request->semester !== 'all') {
                $semesterNumber = $semesterMap[$request->semester] ?? $request->semester;
                $query->where('semester', $semesterNumber);
            }

            // Filter by academic year (connect exam's AD year to student's BS year), otherwise fall back to batch_year
            if ($request->has('academic_year') && $request->academic_year !== '') {
                $query->where('academic_year_bs', $request->academic_year);
            } elseif ($request->has('academic_year_bs') && $request->academic_year_bs !== '') {
                $query->where('academic_year_bs', $request->academic_year_bs);
            } elseif ($request->batch && $request->batch !== '') {
                $query->where('batch_year', $request->batch);
            }

            $students = $query->orderBy('roll_no')->get();

            // Get existing marks for this exam filtered by subject if provided
            $existingMarksQuery = ExamMark::where('exam_id', $exam->id);
            if ($request->subject_id && $request->subject_id !== '') {
                $existingMarksQuery->where('subject_id', $request->subject_id);
            } else {
                $existingMarksQuery->whereNull('subject_id');
            }
            $existingMarks = $existingMarksQuery->pluck('marks_obtained', 'student_id')->toArray();

            // Get attendance percentage for each student (for the exam's subject)
            $studentAttendance = [];
            if ($exam->subject_id) {
                foreach ($students as $student) {
                    $totalAttendance = Attendance::where('student_id', $student->id)
                        ->where('subject_id', $exam->subject_id)
                        ->count();
                    
                    $presentAttendance = Attendance::where('student_id', $student->id)
                        ->where('subject_id', $exam->subject_id)
                        ->where('status', 'present')
                        ->count();
                    
                    $attendancePercentage = $totalAttendance > 0 
                        ? round(($presentAttendance / $totalAttendance) * 100, 1) 
                        : 0;
                    
                    $studentAttendance[$student->id] = [
                        'total' => $totalAttendance,
                        'present' => $presentAttendance,
                        'percentage' => $attendancePercentage,
                    ];
                }
            }

            // Convert students to array to ensure relationships are included
            $studentsArray = $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'roll_no' => $student->roll_no,
                    'semester' => $student->semester,
                    'batch_year' => $student->batch_year,
                    'user' => $student->user ? [
                        'id' => $student->user->id,
                        'name' => $student->user->name,
                        'email' => $student->user->email,
                    ] : null,
                    'subjects' => $student->subjects->map(function ($subject) {
                        return [
                            'id' => $subject->id,
                            'subject_name' => $subject->subject_name,
                            'subject_code' => $subject->subject_code,
                        ];
                    })->toArray(),
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'students' => $studentsArray,
                'existing_marks' => $existingMarks,
                'full_marks' => $exam->full_marks,
                'passing_marks' => $exam->passing_marks,
                'attendance' => $studentAttendance,
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
     * Get students with their existing marks for upload modal.
     */
    public function getStudentsWithMarks(Request $request, Exam $exam)
    {
        try {
            // Validate
            $request->validate([
                'semester' => 'nullable|string',
                'academic_year' => 'nullable|string',
                'subject_id' => 'nullable|string',
            ]);

            // Map semester names to numbers
            $semesterMap = [
                'first' => '1',
                'second' => '2',
                'third' => '3',
                'fourth' => '4',
                'fifth' => '5',
                'sixth' => '6',
            ];

            $query = Student::with(['user', 'subjects'])
                ->where('is_active', true)
                ->where('is_alumni', false);

            // Filter by semester if selected
            if ($request->semester && $request->semester !== '' && $request->semester !== 'all') {
                $semesterNumber = $semesterMap[$request->semester] ?? $request->semester;
                $query->where('semester', $semesterNumber);
            }

            // Filter by academic year - check academic_year (AD), academic_year_bs (BS), and batch_year (fallback)
            // Also handle format variations like "2080/081"
            if ($request->has('academic_year') && $request->academic_year !== '') {
                $academicYear = $request->academic_year;
                
                // Extract base year for formats like "2080/081" -> "2080"
                $extractBaseYear = function($year) {
                    if (strpos($year, '/') !== false) {
                        return explode('/', $year)[0];
                    }
                    return $year;
                };
                
                $baseYear = $extractBaseYear($academicYear);
                
                // Try to determine if it's an AD or BS year
                // BS years are typically in 2040-2090 range, AD years in 2000-2100
                $isBSYear = (int)$baseYear >= 2040 && (int)$baseYear <= 2090;
                
                if ($isBSYear) {
                    // Search by BS year with various formats and batch_year as fallback
                    $query->where(function($q) use ($academicYear, $baseYear) {
                        $q->where('academic_year_bs', $academicYear)
                          ->orWhere('academic_year_bs', 'like', $baseYear . '%')
                          ->orWhere('batch_year', $academicYear)
                          ->orWhere('batch_year', $baseYear);
                    });
                } else {
                    // Search by AD year and BS variations
                    $query->where(function($q) use ($academicYear, $baseYear) {
                        $q->where('academic_year', $academicYear)
                          ->orWhere('academic_year_bs', $academicYear)
                          ->orWhere('academic_year_bs', 'like', $baseYear . '%')
                          ->orWhere('batch_year', $academicYear)
                          ->orWhere('batch_year', $baseYear);
                    });
                }
            }

            $students = $query->orderBy('roll_no')->get();

            // Log detailed information about students loaded
            Log::info('Students fetched for exam marks upload:', [
                'exam_id' => $exam->id,
                'exam_academic_year' => $exam->academic_year,
                'exam_academic_year_bs' => $exam->academic_year_bs,
                'filter_academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'semester_mapped_to' => $semesterMap[$request->semester] ?? $request->semester,
                'subject_id' => $request->subject_id,
                'students_count' => $students->count(),
                'students_list' => $students->map(function($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->user->name ?? 'N/A',
                        'semester' => $s->semester,
                        'academic_year_bs' => $s->academic_year_bs,
                        'batch_year' => $s->batch_year,
                    ];
                })->toArray()
            ]);

            // Get existing marks from exam_marks table
            $existingMarksQuery = ExamMark::where('exam_id', $exam->id);
            if ($request->subject_id && $request->subject_id !== '') {
                $existingMarksQuery->where('subject_id', $request->subject_id);
            }
            $existingMarks = $existingMarksQuery->get()->mapWithKeys(function ($mark) {
                $effectiveFullMarks = $mark->full_marks > 0 ? floatval($mark->full_marks) : $mark->calculateFullMarks();
                $effectivePassingMarks = $mark->passing_marks > 0 ? floatval($mark->passing_marks) : $mark->getEffectivePassingMarksAttribute();
                $effectiveObtainedMarks = $mark->isCtevt() ? $mark->calculateTotalMarks() : floatval($mark->marks_obtained ?? 0);

                return [
                    $mark->student_id => [
                        'marks_obtained' => $effectiveObtainedMarks,
                        'full_marks' => $effectiveFullMarks,
                        'passing_marks' => $effectivePassingMarks,
                        'theory_internal_marks' => (float) ($mark->theory_internal_marks ?? 0),
                        'theory_external_marks' => (float) ($mark->theory_external_marks ?? 0),
                        'practical_internal_marks' => (float) ($mark->practical_internal_marks ?? 0),
                        'practical_external_marks' => (float) ($mark->practical_external_marks ?? 0),
                    ],
                ];
            })->toArray();

            // Get attendance percentage for each student (subject-specific if subject_id is provided)
            $studentAttendance = [];
            $subjectId = $request->subject_id && $request->subject_id !== '' ? (int)$request->subject_id : null;
            
            foreach ($students as $student) {
                $attendanceQuery = Attendance::where('student_id', $student->id);
                
                // If a specific subject is selected, filter attendance by that subject
                if ($subjectId) {
                    $attendanceQuery->where('subject_id', $subjectId);
                }
                
                $totalAttendance = $attendanceQuery->count();
                
                $presentAttendance = Attendance::where('student_id', $student->id)
                    ->where('status', 'present');
                
                // If a specific subject is selected, filter by that subject
                if ($subjectId) {
                    $presentAttendance->where('subject_id', $subjectId);
                }
                
                $presentAttendance = $presentAttendance->count();
                
                $attendancePercentage = $totalAttendance > 0 
                    ? round(($presentAttendance / $totalAttendance) * 100, 1) 
                    : 0;
                
                $studentAttendance[$student->id] = $attendancePercentage;
            }

            // Get the subject name if subject_id is provided
            $subjectName = 'N/A';
            if ($subjectId) {
                $subject = Subject::find($subjectId);
                $subjectName = $subject ? $subject->subject_name : 'N/A';
            } elseif ($exam->subject) {
                $subjectName = $exam->subject->subject_name;
            }

            // Format response for JavaScript
            $studentsData = $students->map(function ($student) use ($studentAttendance, $exam, $subjectName) {
                return [
                    'id' => $student->id,
                    'student_name' => $student->user->name ?? 'Unknown',
                    'roll_no' => $student->roll_no,
                    'semester' => $student->semester,
                    'academic_year' => $student->academic_year,
                    'academic_year_bs' => $student->academic_year_bs,
                    'attendance_percentage' => $studentAttendance[$student->id] ?? 0,
                    'subject_name' => $subjectName,
                ];
            })->toArray();

            // Get subject-specific marks for this subject
            $subjectFullMarks = $exam->full_marks;
            $subjectPassingMarks = $exam->passing_marks;
            
            if ($subjectId) {
                // Check if there are any existing marks for this subject
                $firstMarkRecord = ExamMark::where('exam_id', $exam->id)
                    ->where('subject_id', $subjectId)
                    ->first();
                
                if ($firstMarkRecord) {
                    $subjectFullMarks = $firstMarkRecord->full_marks ?? $exam->full_marks;
                    $subjectPassingMarks = $firstMarkRecord->passing_marks ?? $exam->passing_marks;
                }
            }

            return response()->json([
                'success' => true,
                'students' => $studentsData,
                'existing_marks' => $existingMarks,
                'subject_full_marks' => $subjectFullMarks,
                'subject_passing_marks' => $subjectPassingMarks,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting students with marks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load students: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get available assessment numbers for dropdown (1,2,3...New)
     */
    public function getAssessmentNumbers(Request $request)
    {
        $request->validate([
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'semester' => 'required|string',
            'academic_year' => 'nullable|string',
        ]);

        $numbers = Exam::getAvailableAssessmentNumbers(
            $request->integer('subject_id'),
            $request->semester,
            $request->get('academic_year')
        );

        return response()->json([
            'success' => true,
            'numbers' => array_map('strval', $numbers)
        ]);
    }

    /**
     * Get exam data for the edit modal (AJAX).
     */
    public function getEditExamData(Exam $exam)
    {
        try {
            return response()->json([
                'success' => true,
                'exam' => [
                    'id' => $exam->id,
                    'formatted_assessment' => $exam->formatted_assessment ?? null,
                    'assessment_number' => $exam->assessment_number,
                    'exam_name' => $exam->exam_name,
                    'exam_category' => $exam->exam_category,
                    'semester' => $exam->semester,
                    'subject_id' => $exam->subject_id ?? 'all',
                    'subject_name' => $exam->subject?->subject_name ?? null,
                    'full_marks' => $exam->full_marks,
                    'passing_marks' => $exam->passing_marks,
                    'exam_date' => $exam->exam_date?->format('Y-m-d') ?? '',
                    'exam_date_bs' => $exam->exam_date_bs,
                    'status' => $exam->status,
                    'description' => $exam->description,
                    'theory_internal_max_marks' => $exam->theory_internal_max_marks,
                    'theory_external_max_marks' => $exam->theory_external_max_marks,
                    'practical_internal_max_marks' => $exam->practical_internal_max_marks,
                    'practical_external_max_marks' => $exam->practical_external_max_marks,
                    'theory_internal_pass_marks' => $exam->theory_internal_pass_marks,
                    'theory_external_pass_marks' => $exam->theory_external_pass_marks,
                    'practical_internal_pass_marks' => $exam->practical_internal_pass_marks,
                    'practical_external_pass_marks' => $exam->practical_external_pass_marks,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading edit exam data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load exam data',
            ], 500);
        }
    }

    /**
     * Get all subjects (not filtered by semester).
     */
    public function getAllSubjects()
    {
        try {
            $subjects = Subject::active()
                ->orderBy('semester')
                ->orderBy('subject_name')
                ->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting all subjects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available BS academic years from student database.
     */
    public function getAvailableBSYears()
    {
        try {
            // Fetch distinct BS academic years from students table, ordered descending
            $years = Student::where('is_active', true)
                ->distinct()
                ->whereNotNull('academic_year_bs')
                ->orderBy('academic_year_bs', 'desc')
                ->pluck('academic_year_bs')
                ->toArray();

            return response()->json([
                'success' => true,
                'years' => $years,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting BS years: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load academic years: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available academic years and semesters combinations from student database.
     * Returns only data where students actually exist.
     */
    public function getAvailableAcademicYearsAndSemesters()
    {
        try {
            // Get all active students
            $students = Student::where('is_active', true)
                ->select('academic_year_bs', 'batch_year', 'semester')
                ->get();

            Log::info('Total students fetched:', ['count' => $students->count()]);

            // Build a map of unique years with their semesters
            $yearSemesterMap = [];

            foreach ($students as $student) {
                // Determine which year field to use
                $year = $student->academic_year_bs ?? $student->batch_year;
                $semester = $student->semester;

                if ($year && $semester) {
                    if (!isset($yearSemesterMap[$year])) {
                        $yearSemesterMap[$year] = [];
                    }

                    if (!in_array($semester, $yearSemesterMap[$year])) {
                        $yearSemesterMap[$year][] = $semester;
                    }
                }
            }

            Log::info('Year-semester map:', [
                'total_years' => count($yearSemesterMap),
                'map_details' => array_map(function($sems, $year) {
                    return ['year' => $year, 'semesters' => $sems];
                }, $yearSemesterMap, array_keys($yearSemesterMap))
            ]);

            // Build grouped structure
            $grouped = [];
            $semesterLabels = [
                '1' => 'First',
                '2' => 'Second',
                '3' => 'Third',
                '4' => 'Fourth',
                '5' => 'Fifth',
                '6' => 'Sixth',
            ];

            // Sort years in descending order
            $years = array_keys($yearSemesterMap);
            rsort($years);

            foreach ($years as $year) {
                $semesters = $yearSemesterMap[$year];
                sort($semesters); // Sort semesters in ascending order

                $semesterOptions = array_map(function ($sem) use ($semesterLabels) {
                    return [
                        'value' => (string)$sem,
                        'label' => $semesterLabels[$sem] ?? "Semester {$sem}"
                    ];
                }, $semesters);

                $grouped[] = [
                    'year' => $year,
                    'semesters' => $semesterOptions
                ];

                Log::info('Year group added:', [
                    'year' => $year,
                    'semester_count' => count($semesterOptions),
                    'semesters' => $semesterOptions
                ]);
            }

            Log::info('Final grouped data:', ['total_groups' => count($grouped), 'grouped' => $grouped]);

            return response()->json([
                'success' => true,
                'years' => $grouped,
                'message' => count($grouped) === 0 ? 'No students found with academic year and semester' : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting academic years and semesters: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects for a specific exam (for edit mode).
     */
    public function getSubjectsForExam(Exam $exam)
    {
        try {
            // Get subjects for the exam's semester
            $semester = $exam->semester;
            
            // Convert text semester to numeric if needed
            $semesterMap = [
                'first' => '1',
                'second' => '2',
                'third' => '3',
                'fourth' => '4',
                'fifth' => '5',
                'sixth' => '6',
            ];

            $numericSemester = isset($semesterMap[$semester]) ? $semesterMap[$semester] : $semester;

            $subjects = Subject::where('semester', $numericSemester)
                ->active()
                ->orderBy('subject_name')
                ->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects,
                'exam_semester' => $semester,
                'exam_semester_numeric' => $numericSemester
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting subjects for exam: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a specific exam mark (AJAX)
     */
    public function deleteMark(Request $request, ExamMark $mark)
    {
        try {
            // Optional: authorize user
            $mark->delete();

            return response()->json(['success' => true, 'message' => 'Mark deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting mark: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete mark'], 500);
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
     * Get list of academic years from student BS academic years.
     */
    protected function getAcademicYears(): array
    {
        $years = Student::whereNotNull('academic_year_bs')
            ->distinct('academic_year_bs')
            ->orderBy('academic_year_bs', 'desc')
            ->pluck('academic_year_bs')
            ->toArray();
        
        return array_filter($years);
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

    private function getSemesterCandidates(string $semester): array
    {
        $semester = trim($semester);
        $lower = strtolower($semester);

        $keyToNumber = self::SEMESTER_KEY_TO_NUMBER;
        $numberToKey = array_flip($keyToNumber);

        $candidates = [
            $semester,
            $lower,
            ucfirst($lower),
            strtoupper($lower),
        ];

        if (isset($keyToNumber[$lower])) {
            $candidates[] = $keyToNumber[$lower];
        }

        if (isset($numberToKey[$lower])) {
            $candidates[] = $numberToKey[$lower];
        }

        // Also handle common ordinal inputs (1st/2nd/..)
        $ordinalMap = [
            '1st' => '1', '2nd' => '2', '3rd' => '3',
            '4th' => '4', '5th' => '5', '6th' => '6',
        ];
        if (isset($ordinalMap[$lower])) {
            $candidates[] = $ordinalMap[$lower];
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function buildSemesterCards(Request $request): array
    {
        $semesterLabels = $this->getSemesters();
        $keyToNumber = self::SEMESTER_KEY_TO_NUMBER;

        $examCountsRaw = Exam::query()
            ->select('semester', DB::raw('COUNT(*) as cnt'))
            ->groupBy('semester')
            ->pluck('cnt', 'semester');

        $subjectCountsRaw = Subject::query()
            ->active()
            ->select('semester', DB::raw('COUNT(*) as cnt'))
            ->groupBy('semester')
            ->pluck('cnt', 'semester');

        $baseParams = $request->except(['semester', 'subject_id', 'page']);
        $selected = strtolower((string) $request->get('semester', ''));

        $cards = [];

        // "All Semesters" (no semester filter applied)
        $cards[] = [
            'semester' => ['number' => null, 'name' => 'All Semesters', 'academic_year' => null],
            'examCount' => (int) ($examCountsRaw->sum() ?? 0),
            'subjectCount' => (int) ($subjectCountsRaw->sum() ?? 0),
            'isActive' => $selected === '',
            'url' => route('admin.exam', $baseParams),
        ];

        foreach ($semesterLabels as $key => $label) {
            $num = $keyToNumber[$key] ?? null;
            $examCount = (int) (($examCountsRaw[$key] ?? 0) + ($num ? ($examCountsRaw[$num] ?? 0) : 0));
            $subjectCount = (int) (($num ? ($subjectCountsRaw[$num] ?? 0) : 0) + ($subjectCountsRaw[$key] ?? 0));

            $isActive = $selected === $key || ($num && $selected === $num);

            $cards[] = [
                'semester' => ['number' => $num ? (int) $num : null, 'name' => $label, 'academic_year' => null],
                'examCount' => $examCount,
                'subjectCount' => $subjectCount,
                'isActive' => $isActive,
                'url' => route('admin.exam', array_merge($baseParams, ['semester' => $key])),
            ];
        }

        return $cards;
    }

    private function getSelectedSemesterSubjects(Request $request): array
    {
        $semester = trim((string) $request->get('semester', ''));
        if ($semester === '' || strtolower($semester) === 'all') {
            return ['', collect()];
        }

        $lower = strtolower($semester);
        $keyToNumber = self::SEMESTER_KEY_TO_NUMBER;
        $numberToKey = array_flip($keyToNumber);

        $semesterKey = isset($keyToNumber[$lower]) ? $lower : ($numberToKey[$lower] ?? $lower);
        $numeric = $keyToNumber[$lower] ?? $lower;

        $label = $this->getSemesters()[$semesterKey] ?? ("Semester " . $numeric);

        $candidates = $this->getSemesterCandidates($semester);

        $subjects = Subject::query()
            ->active()
            ->whereIn('semester', $candidates)
            ->orderBy('subject_name')
            ->get();

        return [$label, $subjects];
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

    /**
     * Get mark data for editing via AJAX.
     */
    public function getMarkData(ExamMark $mark)
    {
        try {
            $mark->load(['student.user', 'exam']);
            
            return response()->json([
                'success' => true,
                'mark' => [
                    'id' => $mark->id,
                    'student_id' => $mark->student_id,
                    'student_name' => $mark->student->user->name ?? 'N/A',
                    'roll_no' => $mark->student->roll_no ?? '-',
                    'exam_id' => $mark->exam_id,
                    'exam_name' => $mark->exam->localized_name ?? 'N/A',
                    'marks_obtained' => $mark->marks_obtained,
                    'full_marks' => $mark->full_marks,
                    'percentage' => $mark->percentage,
                    'grade' => $mark->grade,
                    'remarks' => $mark->remarks,
                    'passing_marks' => $mark->exam->passing_marks ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting mark data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load mark data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a single mark via AJAX.
     */
    public function updateMark(Request $request, ExamMark $mark)
    {
        try {
            $request->validate([
                'marks_obtained' => 'required|numeric|min:0',
                'remarks' => 'nullable|string|max:500',
            ]);

            $fullMarks = $mark->full_marks;
            $marksObtained = $request->marks_obtained;
            $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;

            DB::beginTransaction();

            $mark->update([
                'marks_obtained' => $marksObtained,
                'percentage' => $percentage,
                'grade' => $this->calculateGrade($percentage),
                'graded_by' => Auth::id(),
                'graded_at' => now(),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

// Reload mark with relationships
            $mark->load(['student.user', 'exam']);

            // Use 40% for pass/fail calculation
            $isPassed = $percentage >= 40;
            $statusBadgeClass = $isPassed 
                ? 'bg-green-100 text-green-700' 
                : 'bg-red-100 text-red-700';
            $statusText = $isPassed 
                ? '<span class="text-green-600 text-xs"><i class="bi bi-check-circle"></i> Passed</span>' 
                : '<span class="text-red-600 text-xs"><i class="bi bi-x-circle"></i> Failed</span>';

            return response()->json([
                'success' => true,
                'message' => 'Mark updated successfully!',
                'mark' => [
                    'id' => $mark->id,
                    'marks_obtained' => $mark->marks_obtained,
                    'percentage' => $mark->percentage,
                    'grade' => $mark->grade,
                    'is_passed' => $isPassed,
                    'status_badge_class' => $statusBadgeClass,
                    'status_text' => $statusText,
                    'student_name' => $mark->student->user->name ?? 'N/A',
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating mark: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update mark: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert date between AD and BS formats via AJAX.
     */
    public function convertDate(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|string',
                'from' => 'required|in:ad,bs',
                'to' => 'required|in:ad,bs',
            ]);

            $date = $request->date;
            $from = $request->from;
            $to = $request->to;

            if ($from === $to) {
                return response()->json([
                    'success' => true,
                    'converted_date' => $date,
                ]);
            }

            if ($from === 'ad' && $to === 'bs') {
                $converted = \App\Helpers\NepaliContentHelper::convertAdToBs($date);
            } elseif ($from === 'bs' && $to === 'ad') {
                $converted = \App\Helpers\NepaliContentHelper::convertBsToAd($date);
            }

            if ($converted === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date conversion failed',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'converted_date' => $converted,
            ]);
        } catch (\Exception $e) {
            Log::error('Error converting date: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Date conversion error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the Marks search/index page.
     */
    public function marksIndex(Request $request)
    {
        try {
            // Redirect to dynamic marks page
            return redirect()->route('admin.marks.dynamic');
        } catch (\Exception $e) {
            Log::error('Error loading marks index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load marks: ' . $e->getMessage());
        }
    }

    /**
     * Get subject IDs assigned to the current teacher (if user is teacher).
     */
    private function getTeacherSubjectIds(): ?array
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'teacher' || !$user->teacher) {
            return null;
        }

        return SubjectTeacher::where('teacher_id', $user->teacher->id)
            ->pluck('subject_id')
            ->toArray();
    }

    /**
     * Display dynamic marks page with Assessment and CTEVT support.
     */
    public function dynamicMarksIndex(Request $request)
    {
        try {
            $category = $request->get('category', 'assessment');
            
            // Validate category - only allow assessment or ctevt
            if (!in_array($category, ['assessment', 'ctevt'])) {
                $category = 'assessment';
            }
            
            // Get filter data
            $filterData = $this->getDynamicMarksFilterData($category);
            
            // Get subject IDs and list (restrict for teacher if needed)
            $teacherSubjectIds = $this->getTeacherSubjectIds();
            $subjectQuery = Subject::orderBy('subject_name');
            if (is_array($teacherSubjectIds)) {
                $subjectQuery->whereIn('id', $teacherSubjectIds);
            }
            $subjects = $subjectQuery->get();
            
            // Current filters
            $currentFilters = [
                'search' => $request->get('search', ''),
                'academic_year' => $request->get('academic_year', ''),
                'semester' => $request->get('semester', ''),
                'batch' => $request->get('batch', ''),
                'subject_id' => $request->get('subject_id', ''),
                'status' => $request->get('status', ''),
                'assessment_number' => $request->get('assessment_number', ''),
                'sort_by' => $request->get('sort_by', 'roll_no'),
            ];

            $assessmentNumbers = $category === 'assessment'
                ? $this->getDynamicMarksAssessmentNumbers($request, $category)
                : collect();
            
            // Column structure for table headers (focus on the currently selected subject)
            $selectedSubject = null;
            if (!empty($currentFilters['subject_id'])) {
                $selectedSubject = $subjects->firstWhere('id', $currentFilters['subject_id']);
            }
            $columnSubjects = $selectedSubject ? collect([$selectedSubject]) : collect();
            $columnStructure = $this->getColumnStructure($category, $columnSubjects);
            
            // Get students with marks
            $students = $this->getDynamicMarksStudents($request, $category, $teacherSubjectIds);
            
            return view('admin.marks.dynamic', [
                'category' => $category,
                'subjects' => $subjects,
                'filterData' => (object) $filterData,
                'currentFilters' => $currentFilters,
                'assessmentNumbers' => $assessmentNumbers,
                'columnStructure' => $columnStructure,
                'selectedSubject' => $selectedSubject,
                'students' => $students,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading dynamic marks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load marks: ' . $e->getMessage());
        }
    }

    /**
     * Get filter data for dynamic marks page.
     */
    private function getDynamicMarksFilterData($category)
    {
        // Get academic years
        $years = Student::whereNotNull('academic_year_bs')
            ->distinct()
            ->orderBy('academic_year_bs', 'desc')
            ->pluck('academic_year_bs')
            ->toArray();
        
        // Get semesters
        $semesters = Student::whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester')
            ->toArray();
        
        // Get batches
        $batches = Student::whereNotNull('batch_year')
            ->distinct()
            ->orderBy('batch_year', 'desc')
            ->pluck('batch_year')
            ->toArray();
        
        return [
            'years' => $years,
            'semesters' => $semesters,
            'batches' => $batches,
        ];
    }

    /**
     * Get available assessment numbers for the Dynamic Marks dropdown.
     * Source of truth is the exams table (assessment_number lives on exams even before marks are entered).
     */
    private function getDynamicMarksAssessmentNumbers(Request $request, string $category)
    {
        $teacherSubjectIds = $this->getTeacherSubjectIds();

        $query = Exam::query()
            ->where('exam_category', 'assessment')
            ->whereNotNull('assessment_number')
            ->when(is_array($teacherSubjectIds), fn ($q) => $q->whereIn('subject_id', $teacherSubjectIds))
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->subject_id))
            ->when($request->filled('semester'), fn ($q) => $q->where('semester', (string) $request->semester))
            // Dynamic marks "academic_year" filter uses BS (from students table), so match exams.academic_year_bs.
            ->when($request->filled('academic_year'), fn ($q) => $q->where('academic_year_bs', (string) $request->academic_year))
            ->distinct()
            ->orderBy('assessment_number');

        return $query->pluck('assessment_number')->filter()->values();
    }

    /**
     * Get column structure for the marks table.
     */
    private function getColumnStructure($category, $subjects = null)
    {
        $components = ['TI', 'TE', 'PI', 'PE'];
        $colspan = $category === 'ctevt' ? 12 : 3; // 4 components x 3 columns (full, pass, obtained)
        
        if ($subjects === null) {
            $subjects = Subject::orderBy('subject_name')->get();
        }

        return (object) [
            'subjects' => collect($subjects),
            'components' => $components,
            'colspan' => $colspan,
        ];
    }

    /**
     * Get students with marks for dynamic marks page.
     */
    private function getDynamicMarksStudents(Request $request, $category, ?array $teacherSubjectIds = null)
    {
        // Teacher should only access assigned subjects
        if (is_array($teacherSubjectIds) && $request->filled('subject_id') && !in_array($request->subject_id, $teacherSubjectIds)) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
        }

        // Require at least one filter: subject_id, semester, batch, academic year, or search
        $hasFilters = $request->filled('subject_id') || 
                      $request->filled('semester') || 
                      $request->filled('batch') ||
                      $request->filled('academic_year') ||
                      $request->filled('search');
        
        if (!$hasFilters) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
        }
        
        $query = Student::with(['user', 'subjects']);
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('roll_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        
        if ($request->filled('batch')) {
            $query->where('batch_year', $request->batch);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('academic_year_bs', $request->academic_year);
        }

         if ($request->filled('status') && $request->filled('subject_id')) {
             $status = $request->status;
             $subjectId = $request->subject_id;
             $query->when(
                 $status === 'marks_not_filled',
                 function ($q) use ($category, $subjectId) {
                     $q->whereDoesntHave('examMarks', function ($markQuery) use ($category, $subjectId) {
                         $markQuery->where('subject_id', $subjectId)
                             ->whereHas('exam', fn ($examQuery) => $examQuery->where('exam_category', $category))
                             ->whereNotNull('marks_obtained');
                     });
                 },
                 function ($q) use ($status, $category, $subjectId) {
                     $q->whereHas('examMarks', function ($markQuery) use ($status, $category, $subjectId) {
                         $markQuery->where('subject_id', $subjectId)
                             ->whereHas('exam', fn ($examQuery) => $examQuery->where('exam_category', $category));

                         if ($status === 'pass') {
                             $markQuery->where('percentage', '>=', 40);
                         } elseif ($status === 'fail') {
                             $markQuery->where('percentage', '<', 40);
                         } elseif ($status === 'marks_filled') {
                             $markQuery->whereNotNull('marks_obtained');
                         }
                     });
                 }
             );
         }
         
         // Filter by assessment number for assessment category
         if ($request->filled('assessment_number') && $category === 'assessment') {
             $assessmentNumber = $request->assessment_number;
             $query->whereHas('examMarks', function ($markQuery) use ($assessmentNumber) {
                 $markQuery->whereHas('exam', function ($examQuery) use ($assessmentNumber) {
                     $examQuery->where('assessment_number', $assessmentNumber);
                 });
             });
         }
        
        // Sort
        $sortBy = $request->get('sort_by', 'roll_no');
        if ($sortBy === 'name') {
            $query->join('users', 'students.user_id', '=', 'users.id')
                 ->orderBy('users.name')
                 ->select('students.*');
        } else {
            $query->orderBy('roll_no');
        }
        
        $students = $query->paginate(25);
        $subjectId = $request->filled('subject_id') ? (int) $request->subject_id : null;

        $students->getCollection()->transform(function ($student) use ($subjectId) {
            $student->attendance_percentage = $this->calculateMarksAttendancePercentage($student->id, $subjectId);
            return $student;
        });

        return $students;
    }

    private function calculateMarksAttendancePercentage(int $studentId, ?int $subjectId = null): float
    {
        $attendanceQuery = Attendance::where('student_id', $studentId);

        if ($subjectId) {
            $attendanceQuery->where('subject_id', $subjectId);
        }

        $totalAttendance = $attendanceQuery->count();
        if ($totalAttendance === 0) {
            return 0;
        }

        $presentAttendance = Attendance::where('student_id', $studentId)
            ->where('status', 'present');

        if ($subjectId) {
            $presentAttendance->where('subject_id', $subjectId);
        }

        return round(($presentAttendance->count() / $totalAttendance) * 100, 1);
    }

    /**
     * Build canonical filter map used across the dynamic marks views.
     */
    private function getCurrentFilters(Request $request): array
    {
        return [
            'search' => $request->get('search', ''),
            'academic_year' => $request->get('academic_year', ''),
            'semester' => $request->get('semester', ''),
            'batch' => $request->get('batch', ''),
            'subject_id' => $request->get('subject_id', ''),
            'status' => $request->get('status', ''),
            'assessment_number' => $request->get('assessment_number', ''),
            'sort_by' => $request->get('sort_by', 'roll_no'),
        ];
    }

    /**
     * Get dynamic marks data (AJAX).
     */
    public function clearDynamicMarks(Request $request)
    {
        try {
            ExamMark::truncate();
            return redirect()->route('marks.dynamic')->with('success', 'All exam marks cleared.');
        } catch (\Exception $e) {
            Log::error('Error clearing exam marks: ' . $e->getMessage());
            return redirect()->route('marks.dynamic')->with('error', 'Failed to clear marks: ' . $e->getMessage());
        }
    }

    public function dynamicMarksData(Request $request)
    {
        try {
            $category = $request->get('category', 'assessment');
            $teacherSubjectIds = $this->getTeacherSubjectIds();
            $students = $this->getDynamicMarksStudents($request, $category, $teacherSubjectIds);
            
            $html = view('admin.marks.partials.dynamic_table_rows', [
                'students' => $students,
                'category' => $category,
            ])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $students->links('vendor.pagination.tailwind')->toHtml(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting dynamic marks data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export dynamic marks.
     */
    public function dynamicMarksExport(Request $request, $format)
    {
        try {
            $category = $request->get('category', 'assessment');
            $currentFilters = $this->getCurrentFilters($request);
            $format = strtolower($format) === 'excel' ? 'excel' : 'csv';
            $teacherSubjectIds = $this->getTeacherSubjectIds();
            $subjectQuery = Subject::orderBy('subject_name');
            if (is_array($teacherSubjectIds)) {
                $subjectQuery->whereIn('id', $teacherSubjectIds);
            }
            $subjects = $subjectQuery->get();
            $selectedSubject = null;
            if (!empty($currentFilters['subject_id'])) {
                $selectedSubject = $subjects->firstWhere('id', $currentFilters['subject_id']);
            }

            if (!$selectedSubject) {
                return back()->with('error', 'Please select a subject to export marks');
            }

            $students = $this->getDynamicMarksStudents($request, $category, $teacherSubjectIds);
            if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $students = $students->getCollection();
            } else {
                $students = collect($students);
            }

            $subjectName = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($selectedSubject->subject_name ?? 'subject'));
            $filename = sprintf('admin_marks_%s_%s', trim($subjectName, '_') ?: 'subject', now()->format('Ymd_His'));
            $delimiter = $format === 'excel' ? "\t" : ',';
            $contentType = $format === 'excel' ? 'application/vnd.ms-excel' : 'text/csv';
            $extension = $format === 'excel' ? 'xls' : 'csv';

            $callback = function () use ($students, $category, $selectedSubject, $currentFilters, $delimiter) {
                $out = fopen('php://output', 'w');
                $statusFilter = $currentFilters['status'] ?? '';

                if ($category === 'assessment') {
                    $selectedAssessmentNumber = $currentFilters['assessment_number'] ?? null;
                    if ($selectedAssessmentNumber === '') {
                        $selectedAssessmentNumber = null;
                    }

                    fputcsv($out, ['Roll No', 'Student Name', 'Attendance %', 'Full Marks', 'Pass Marks', 'Obtained', 'Percentage', 'Result'], $delimiter);

                    foreach ($students as $student) {
                        $examMark = $student->getExamMarkForSubject($selectedSubject->id, $category, null, $selectedAssessmentNumber);
                        $marksFilled = $examMark ? $examMark->isFilled() : false;
                        $isPassed = $examMark ? $examMark->isPassedAllComponents() : false;
                        $totalObtained = $examMark ? $examMark->calculateTotalMarks() : 0;
                        $totalFull = $examMark ? $examMark->calculateFullMarks() : 0;
                        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;

                        if ($statusFilter === 'pass' && !$isPassed) {
                            continue;
                        }
                        if ($statusFilter === 'fail' && (!$examMark || $isPassed)) {
                            continue;
                        }
                        if ($statusFilter === 'marks_filled' && !$marksFilled) {
                            continue;
                        }
                        if ($statusFilter === 'marks_not_filled' && $marksFilled) {
                            continue;
                        }

                        fputcsv($out, [
                            $student->roll_no,
                            $student->user->name ?? 'N/A',
                            ($student->attendance_percentage ?? 0) . '%',
                            $examMark ? ($examMark->full_marks ?? $examMark->exam->full_marks ?? '') : '',
                            $examMark ? ($examMark->passing_marks ?? $examMark->exam->passing_marks ?? '') : '',
                            $examMark ? ($examMark->isAbsent() ? 'ABS' : ($examMark->marks_obtained ?? '')) : '',
                            $examMark ? ($examMark->isAbsent() ? 'ABS' : ($percentage . '%')) : '',
                            $examMark ? ($examMark->isAbsent() ? 'ABS' : $examMark->getResultAttribute()) : 'Pending',
                        ], $delimiter);
                    }
                } else {
                    fputcsv($out, [
                        'Roll No',
                        'Student Name',
                        'TI Full',
                        'TI Pass',
                        'TI Obtained',
                        'TE Full',
                        'TE Pass',
                        'TE Obtained',
                        'PI Full',
                        'PI Pass',
                        'PI Obtained',
                        'PE Full',
                        'PE Pass',
                        'PE Obtained',
                        'Total',
                        'Result',
                    ], $delimiter);

                    foreach ($students as $student) {
                        $examMark = $student->getExamMarkForSubject($selectedSubject->id, $category);
                        $marksFilled = $examMark ? $examMark->isFilled() : false;
                        $isPassed = $examMark ? $examMark->isPassedAllComponents() : false;

                        if ($statusFilter === 'pass' && !$isPassed) {
                            continue;
                        }
                        if ($statusFilter === 'fail' && (!$examMark || $isPassed)) {
                            continue;
                        }
                        if ($statusFilter === 'marks_filled' && !$marksFilled) {
                            continue;
                        }
                        if ($statusFilter === 'marks_not_filled' && $marksFilled) {
                            continue;
                        }

                        $componentValues = [];
                        foreach (['TI', 'TE', 'PI', 'PE'] as $component) {
                            $componentValues[$component] = (array) $student->getComponentMarks($selectedSubject->id, $component);
                        }

                        fputcsv($out, [
                            $student->roll_no,
                            $student->user->name ?? 'N/A',
                            $componentValues['TI']['full'] ?? 0,
                            $componentValues['TI']['pass'] ?? 0,
                            $componentValues['TI']['obtained'] ?? 0,
                            $componentValues['TE']['full'] ?? 0,
                            $componentValues['TE']['pass'] ?? 0,
                            $componentValues['TE']['obtained'] ?? 0,
                            $componentValues['PI']['full'] ?? 0,
                            $componentValues['PI']['pass'] ?? 0,
                            $componentValues['PI']['obtained'] ?? 0,
                            $componentValues['PE']['full'] ?? 0,
                            $componentValues['PE']['pass'] ?? 0,
                            $componentValues['PE']['obtained'] ?? 0,
                            $examMark ? $examMark->calculateTotalMarks() : '',
                            $examMark ? $examMark->getResultAttribute() : 'Pending',
                        ], $delimiter);
                    }
                }

                fclose($out);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => "attachment; filename={$filename}.{$extension}",
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting marks: ' . $e->getMessage());
            return back()->with('error', 'Failed to export: ' . $e->getMessage());
        }
    }

    /**
     * Print marks.
     */
    public function dynamicMarksPrint(Request $request)
    {
        try {
            $category = $request->get('category', 'assessment');
            $currentFilters = $this->getCurrentFilters($request);
            $teacherSubjectIds = $this->getTeacherSubjectIds();
            $subjectQuery = Subject::orderBy('subject_name');
            if (is_array($teacherSubjectIds)) {
                $subjectQuery->whereIn('id', $teacherSubjectIds);
            }
            $subjects = $subjectQuery->get();
            $selectedSubject = null;
            if (!empty($currentFilters['subject_id'])) {
                $selectedSubject = $subjects->firstWhere('id', $currentFilters['subject_id']);
            }

            $columnStructure = $this->getColumnStructure($category, $selectedSubject ? collect([$selectedSubject]) : collect());
            $students = $this->getDynamicMarksStudents($request, $category);

            return view('admin.marks.print.index', [
                'students' => $students,
                'category' => $category,
                'currentFilters' => $currentFilters,
                'subjects' => $subjects,
                'columnStructure' => $columnStructure,
                'selectedSubject' => $selectedSubject,
            ]);
        } catch (\Exception $e) {
            Log::error('Error printing marks: ' . $e->getMessage());
            return back()->with('error', 'Failed to print: ' . $e->getMessage());
        }
    }

    /**
     * Get student detail for modal.
     */
    public function dynamicMarksStudentDetail(Request $request, $studentId = null)
    {
        try {
            // Handle both route parameter and query parameter
            $studentId = $studentId ?? $request->get('student_id');
            
            if (!$studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID is required',
                ], 400);
            }
            
            $student = Student::with('user')->findOrFail($studentId);
            $category = $request->get('category', 'assessment');
            $assessmentNumber = $request->get('assessment_number', '');
            
            // Get marks for this student
            $marksQuery = ExamMark::where('student_id', $studentId)
                ->whereHas('exam', function($q) use ($category) {
                    $q->where('exam_category', $category);
                })
                ->with(['exam', 'subject']);

            if ($category === 'assessment' && !empty($assessmentNumber)) {
                $marksQuery->where('assessment_number', $assessmentNumber);
            }

            $marks = $marksQuery->get();
            
            // Calculate summary
            $totalObtained = $marks->sum('marks_obtained');
            $totalFull = $marks->sum('full_marks');
            $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
            $result = $percentage >= 40 ? 'PASS' : 'FAIL';
            
            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->user->name ?? 'N/A',
                    'roll_no' => $student->roll_no,
                    'semester' => $student->semester,
                ],
                'marks' => $marks->map(function($mark) {
                    return [
                        'id' => $mark->id,
                        'subject' => $mark->subject->subject_name ?? 'N/A',
                        'exam' => $mark->exam->exam_name ?? 'N/A',
                        'full_marks' => $mark->full_marks,
                        'passing_marks' => $mark->passing_marks,
                        'marks_obtained' => $mark->marks_obtained,
                        'percentage' => $mark->percentage,
                        'grade' => $mark->grade,
                        'status' => $mark->marks_status,
                    ];
                }),
                'summary' => [
                    'total_obtained' => $totalObtained,
                    'total_full' => $totalFull,
                    'percentage' => $percentage,
                    'result' => $result,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting student marks detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get filter data for marks search (years, semesters with data).
     */
    public function getMarksFilterData()
    {
        try {
            // Get BS years that have marks data
            $years = ExamMark::join('students', 'exam_marks.student_id', '=', 'students.id')
                ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
                ->whereNotNull('students.academic_year_bs')
                ->distinct()
                ->orderBy('students.academic_year_bs', 'desc')
                ->pluck('students.academic_year_bs')
                ->toArray();

            // Get semesters that have marks data
            $semestersWithMarks = ExamMark::join('students', 'exam_marks.student_id', '=', 'students.id')
                ->whereNotNull('students.semester')
                ->distinct()
                ->orderBy('students.semester')
                ->pluck('students.semester')
                ->toArray();

            // Get subjects that have marks data
            $subjectsWithMarks = ExamMark::whereNotNull('subject_id')
                ->distinct()
                ->pluck('subject_id')
                ->toArray();

            $subjects = Subject::whereIn('id', $subjectsWithMarks)
                ->orderBy('subject_name')
                ->get();

            return response()->json([
                'success' => true,
                'years' => $years,
                'semesters' => $semestersWithMarks,
                'subjects' => $subjects,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting marks filter data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load filter data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search marks with filters and search query.
     */
    public function searchMarks(Request $request)
    {
        try {
            $query = ExamMark::with(['student.user', 'exam', 'subject']);

            // Filter by BS Year
            if ($request->has('academic_year') && $request->academic_year) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('academic_year_bs', $request->academic_year);
                });
            }

            // Filter by Semester
            if ($request->has('semester') && $request->semester) {
                $semesterMap = [
                    'first' => '1', 'second' => '2', 'third' => '3',
                    'fourth' => '4', 'fifth' => '5', 'sixth' => '6',
                ];
                $semesterValue = $semesterMap[$request->semester] ?? $request->semester;
                
                $query->whereHas('student', function ($q) use ($semesterValue) {
                    $q->where('semester', $semesterValue);
                });
            }

            // Filter by Subject
            if ($request->has('subject_id') && $request->subject_id) {
                $query->where('subject_id', $request->subject_id);
            }

            // Search by student details (ID, roll number, name, email)
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('roll_no', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Order by most recent first
            $query->orderBy('graded_at', 'desc');

            // Paginate results
            $perPage = (int) $request->query('per_page', 25);
            $allowedPerPage = [10, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 25;
            }

            $marks = $query->paginate($perPage)->appends($request->all());

            return response()->json([
                'success' => true,
                'marks' => $marks->items(),
                'pagination' => [
                    'current_page' => $marks->currentPage(),
                    'last_page' => $marks->lastPage(),
                    'per_page' => $marks->perPage(),
                    'total' => $marks->total(),
                    'next_page_url' => $marks->nextPageUrl(),
                    'prev_page_url' => $marks->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching marks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search marks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display marksheet search page.
     */
    public function marksheetSearch(Request $request)
    {
        try {
            // Get filter data
            $years = Exam::distinct()
                ->whereNotNull('academic_year')
                ->pluck('academic_year')
                ->filter()
                ->sortDesc()
                ->values()
                ->toArray();
            
            $semesters = ['1', '2', '3', '4', '5', '6'];
            
            // Get exam types
            $examTypes = Exam::distinct()
                ->whereNotNull('exam_type')
                ->pluck('exam_type')
                ->filter()
                ->values()
                ->toArray();
            
            // Get exam categories
            $examCategories = ['assessment', 'ctevt', 'general'];
            
            // Get all subjects for dropdown
            $subjects = Subject::orderBy('subject_name')->get();
            
            // Assessment number list if category is assessment (or for initial display)
            $assessmentNumbers = Exam::query()
                ->where('exam_category', 'assessment')
                ->when($request->filled('academic_year'), function ($q) use ($request) {
                    $q->where('academic_year', $request->academic_year);
                })
                ->when($request->filled('semester'), function ($q) use ($request) {
                    $q->where('semester', $request->semester);
                })
                ->whereNotNull('assessment_number')
                ->pluck('assessment_number')
                ->filter()
                ->unique()
                ->sort()
                ->values();

            $filters = [
                'academic_year' => $request->get('academic_year', ''),
                'semester' => $request->get('semester', ''),
                'exam_category' => $request->get('exam_category', 'assessment'),
                'assessment_number' => $request->get('assessment_number', ''),
                'student_id' => $request->get('student_id', ''),
                'dob' => $request->get('dob', ''),
                'dob_bs' => $request->get('dob_bs', ''),
                'result' => $request->get('result', ''),
            ];
            
            $student = null;
            $marksheetData = null;
            
            // If search parameters are present, search for student
            if ($request->has('search_student')) {
                $student = $this->findStudentByIdOrDob($request);
                
                if ($student) {
                    $marksheetData = $this->getMarksheetData($student, $request);
                }
            }
            
            return view('admin.marks.marksheet-search', [
                'years' => $years,
                'semesters' => $semesters,
                'examTypes' => $examTypes,
                'examCategories' => $examCategories,
                'subjects' => $subjects,
                'assessmentNumbers' => $assessmentNumbers,
                'filters' => $filters,
                'student' => $student,
                'marksheetData' => $marksheetData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading marksheet search: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load search page: ' . $e->getMessage());
        }
    }

    /**
     * Find student by ID or DOB.
     */
    private function findStudentByIdOrDob(Request $request)
    {
        // Trim inputs to avoid mismatches from extra spaces (e.g., from copy/paste)
        $studentId = trim($request->get('student_id', ''));
        $dob = trim($request->get('dob', ''));
        $dobBs = $this->normalizeBsDateOfBirth($request->get('dob_bs', ''));
        
        $query = Student::with('user');
        
        if (!empty($studentId)) {
            // Search by student ID (primary key) or roll_no
            $query->where(function($q) use ($studentId) {
                $q->where('id', $studentId)
                  ->orWhere('roll_no', 'like', "%{$studentId}%");
            });
        }
        
        $normalizedDob = !empty($dob) ? $this->normalizeDateOfBirth($dob) : null;
        $convertedDobBs = !empty($dobBs) ? NepaliContentHelper::convertBsToAd($dobBs) : null;

        if ($normalizedDob || !empty($dob) || !empty($dobBs) || $convertedDobBs) {
            $query->where(function ($q) use ($normalizedDob, $dob, $dobBs, $convertedDobBs) {
                if ($normalizedDob) {
                    $q->whereDate('date_of_birth', $normalizedDob);
                } elseif (!empty($dob)) {
                    $q->where('date_of_birth', $dob);
                }

                if (!empty($dobBs)) {
                    $q->orWhere('date_of_birth_bs', $dobBs);
                }

                if ($convertedDobBs) {
                    $q->orWhereDate('date_of_birth', $convertedDobBs);
                }
            });
        }
        
        $student = $query->first();

        // If no student found and DOB is in YYYY-MM-DD, try swapping day/month (some browsers/locales may flip them)
        if (!$student && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $m)) {
            $year = $m[1];
            $month = $m[2];
            $day = $m[3];

            if (checkdate((int)$day, (int)$month, (int)$year)) {
                $swappedDob = sprintf('%s-%s-%s', $year, $day, $month);
                $student = Student::with('user')
                    ->where(function($q) use ($studentId) {
                        $q->where('id', $studentId)
                          ->orWhere('roll_no', 'like', "%{$studentId}%");
                    })
                    ->whereDate('date_of_birth', $swappedDob)
                    ->first();
            }
        }

        return $student;
    }

    private function normalizeBsDateOfBirth(?string $dobBs): string
    {
        if (empty($dobBs)) {
            return '';
        }

        $normalized = NepaliContentHelper::toEnglishNumber(trim($dobBs));
        $normalized = str_replace(['/', '.'], '-', $normalized);

        return preg_replace('/\s+/', '', $normalized) ?? '';
    }

    /**
     * Normalize a date of birth input into YYYY-MM-DD.
     * Accepts common formats like DD/MM/YYYY, DD-MM-YYYY, YYYY/MM/DD, YYYY-MM-DD.
     */
    private function normalizeDateOfBirth(string $dob): ?string
    {
        $formats = [
            'Y-m-d',
            'Y/m/d',
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'm/d/Y',
            'm-d-Y',
        ];

        foreach ($formats as $format) {
            try {
                $parsed = \Carbon\Carbon::createFromFormat($format, trim($dob));
                if ($parsed) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // ignore parse errors, try next format
            }
        }

        return null;
    }

    /**
     * Get marksheet data for a student.
     */
    private function getMarksheetData(Student $student, Request $request)
    {
        $academicYear = $request->get('academic_year', '');
        $semester = $request->get('semester', '');
        $examCategory = $request->get('exam_category', 'assessment');
        
        $examMarksQuery = ExamMark::where('student_id', $student->id)
            ->with(['exam', 'subject']);
        
        // Filter by academic year
        if (!empty($academicYear)) {
            $examMarksQuery->whereHas('exam', function($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            });
        }
        
        // Filter by semester
        if (!empty($semester)) {
            $examMarksQuery->whereHas('exam', function($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }
        
        // Filter by exam category
        if (!empty($examCategory)) {
            $examMarksQuery->whereHas('exam', function($q) use ($examCategory) {
                $q->where('exam_category', $examCategory);
            });
        }

        // Filter by assessment number (only for assessment category)
        $assessmentNumber = $request->get('assessment_number', '');
        if ($examCategory === 'assessment' && !empty($assessmentNumber)) {
            $examMarksQuery->whereHas('exam', function($q) use ($assessmentNumber) {
                $q->where('assessment_number', $assessmentNumber);
            });
        }
        
        $examMarks = $examMarksQuery->get();

        // Filter by pass/fail result before collapsing repeated subject entries.
        // Otherwise a failing latest assessment can hide an older passing row and produce a false empty state.
        $resultFilter = $request->get('result', '');
        if (in_array($resultFilter, ['pass', 'fail'])) {
            $examMarks = $examMarks->filter(function ($mark) use ($resultFilter) {
                $isPass = strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
                return $resultFilter === 'pass' ? $isPass : !$isPass;
            })->values();
        }

        // If multiple marks exist for the same subject (from different exams/uploads), keep one representative entry.
        // For assessment marksheets, prefer the highest assessment number; otherwise fall back to exam date / id.
        $examMarks = $examMarks->sortByDesc(function ($mark) {
            if (($mark->exam?->exam_category ?? null) === 'assessment' && !empty($mark->assessment_number)) {
                return (int) $mark->assessment_number;
            }

            return $mark->exam?->exam_date ? strtotime($mark->exam->exam_date) : $mark->exam_id;
        })->unique('subject_id')->values();

        // Group marks by subject
        $marksBySubject = $examMarks->groupBy('subject_id');
        
        // Calculate totals
        $totalObtained = $examMarks->sum('marks_obtained');
        $totalFull = $examMarks->sum('full_marks');
        $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        
        // Calculate grade and overall result
        $grade = $this->calculateMarksheetGrade($percentage);
        $result = $examMarks->isNotEmpty() && $examMarks->every(function ($mark) {
            return strtoupper($mark->result ?? ($mark->percentage >= 40 ? 'PASS' : 'FAIL')) === 'PASS';
        }) ? 'PASS' : 'FAIL';
        
        return [
            'exam_marks' => $examMarks,
            'marks_by_subject' => $marksBySubject,
            'total_obtained' => $totalObtained,
            'total_full' => $totalFull,
            'percentage' => $percentage,
            'grade' => $grade,
            'result' => $result,
        ];
    }

    /**
     * Calculate grade based on percentage for marksheet.
     */
    private function calculateMarksheetGrade($percentage)
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
     * Print marksheet for a student.
     */
    public function marksheetPrint(Request $request)
    {
        try {
            $studentId = $request->get('student_id', '');
            $dob = $request->get('dob', '');
            
            if (empty($studentId) && empty($dob)) {
                return redirect()->route('admin.marksheet.search')
                    ->with('error', 'Please provide student ID or Date of Birth');
            }
            
            $student = $this->findStudentByIdOrDob($request);
            
            if (!$student) {
                return redirect()->route('admin.marksheet.search')
                    ->with('error', 'Student not found');
            }
            
            $marksheetData = $this->getMarksheetData($student, $request);
            
            return view('admin.marks.marksheet-print', [
                'student' => $student,
                'marksheetData' => $marksheetData,
                'filters' => $request->all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error printing marksheet: ' . $e->getMessage());
            return redirect()->route('admin.marksheet.search')
                ->with('error', 'Failed to print marksheet: ' . $e->getMessage());
        }
    }

    /**
     * Export marksheet as PDF.
     */
    public function marksheetExport(Request $request)
    {
        try {
            $studentId = $request->get('student_id', '');
            $dob = $request->get('dob', '');

            if (empty($studentId) && empty($dob)) {
                return redirect()->route('admin.marksheet.search')
                    ->with('error', 'Please provide student ID or Date of Birth');
            }

            $student = $this->findStudentByIdOrDob($request);

            if (!$student) {
                return redirect()->route('admin.marksheet.search')
                    ->with('error', 'Student not found');
            }

            $marksheetData = $this->getMarksheetData($student, $request);

            $fileName = sprintf('marksheet_%s_%s.csv', $student->id, now()->format('Ymd_His'));

            $callback = function () use ($marksheetData) {
                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'S.N.',
                    'Subject',
                    'Full Mark (Int)',
                    'Full Mark (Ext)',
                    'Pass Mark (Int)',
                    'Pass Mark (Ext)',
                    'Marks Obtained (Int)',
                    'Marks Obtained (Ext)',
                    'Total',
                ]);

                foreach ($marksheetData['exam_marks'] as $index => $mark) {
                    $tiFull = $mark->exam->theory_internal_max_marks ?? 0;
                    $teFull = $mark->exam->theory_external_max_marks ?? 0;
                    $tiPass = $mark->exam->theory_internal_pass_marks ?? 0;
                    $tePass = $mark->exam->theory_external_pass_marks ?? 0;
                    $piFull = $mark->exam->practical_internal_max_marks ?? 0;
                    $peFull = $mark->exam->practical_external_max_marks ?? 0;
                    $piPass = $mark->exam->practical_internal_pass_marks ?? 0;
                    $pePass = $mark->exam->practical_external_pass_marks ?? 0;
                    $tiObt = $mark->theory_internal_marks ?? 0;
                    $teObt = $mark->theory_external_marks ?? 0;
                    $piObt = $mark->practical_internal_marks ?? 0;
                    $peObt = $mark->practical_external_marks ?? 0;

                    fputcsv($out, [
                        $index + 1,
                        ($mark->subject->subject_name ?? 'N/A') . ' (Th.)',
                        $tiFull,
                        $teFull,
                        $tiPass,
                        $tePass,
                        $tiObt,
                        $teObt,
                        $tiObt + $teObt,
                    ]);

                    fputcsv($out, [
                        '',
                        ($mark->subject->subject_name ?? 'N/A') . ' (Pr.)',
                        $piFull,
                        $peFull,
                        $piPass,
                        $pePass,
                        $piObt,
                        $peObt,
                        $piObt + $peObt,
                    ]);
                }

                fclose($out);
            };

            return response()->streamDownload($callback, $fileName, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting marksheet: ' . $e->getMessage());
            return redirect()->route('admin.marksheet.search')
                ->with('error', 'Failed to export marksheet: ' . $e->getMessage());
        }
    }
}
