<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\ExamMark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Support\TeacherSubjectRoster;
use App\Traits\LogsActivity;
use Carbon\Carbon;

class TeacherExamsController extends Controller
{
    use LogsActivity;
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
     * Display exams for teacher's subjects
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return view('teacher.exams', [
                    'exams' => collect([]),
                    'subjects' => collect([]),
                    'selectedSubject' => null,
                ]);
            }
            
            $subjectIds = $this->getTeacherSubjectIds();
            
            if (empty($subjectIds)) {
                return view('teacher.exams', [
                    'exams' => collect([]),
                    'subjects' => collect([]),
                    'selectedSubject' => null,
                ]);
            }
            
            // Debug: Log the request parameters
            Log::info('Teacher exams index called with params:', [
                'subject' => $request->get('subject', ''),
                'semester' => $request->get('semester', ''),
                'search' => $request->get('q', '')
            ]);
            
            $subject = $request->get('subject', '');
            $semester = $request->get('semester', '');
            $selectedSemester = $semester;
            $search = $request->get('q', '');
            $academic_year = $request->get('academic_year', '');
            $exam_category = $request->get('exam_category', '');
            $status = $request->get('status', '');
            
            // Get unique semesters from assignments
            $assignedSemesters = SubjectTeacher::where('teacher_id', $teacher->id)
                ->whereNotNull('semester')
                ->distinct()
                ->pluck('semester')
                ->sort()
                ->values()
                ->toArray();
            
            // Prepare semester options, include explicit "all" for all-semester exams and optional "" for no filter
            $semesterOptions = array_merge(['' => 'All Exams (All + Semester-specific)', 'all' => 'All Semesters'], array_combine($assignedSemesters, $assignedSemesters));
            
            // Get subjects for dropdown
            $subjects = SubjectTeacher::where('teacher_id', $teacher->id)
                ->with('subject')
                ->get()
                ->map(function ($st) {
                    return [
                        'id' => $st->subject->id,
                        'name' => $st->subject->subject_name,
                        'code' => $st->subject->subject_code,
                    ];
                });
            
            // Get exams for teacher's subjects
            $examsQuery = Exam::whereIn('subject_id', $subjectIds)
                ->with(['subject'])
                ->select('*', 'assessment_number');
            
            // Apply filters - use filled() to properly handle empty string values
            if ($request->filled('academic_year') && $request->academic_year) {
                $examsQuery = $examsQuery->forYear($request->academic_year);
            }
            
            // Handle semester filter - convert text to number or handle 'all' or empty
            // Empty string '' means show all exams (all semesters + all semester-specific)
            // 'all' means show ONLY exams where semester = 'all' (exams that apply to all semesters)
            if ($request->filled('semester') && $request->semester) {
                if ($request->semester === 'all') {
                    // Show only exams where semester = 'all' (exams that apply to all semesters)
                    $examsQuery = $examsQuery->where('semester', 'all');
                } else {
                    // Support both stored formats: "first" and "1" (legacy)
                    $requested = strtolower((string) $request->semester);
                    $keyToNumber = [
                        'first' => '1',
                        'second' => '2',
                        'third' => '3',
                        'fourth' => '4',
                        'fifth' => '5',
                        'sixth' => '6',
                    ];
                    $numberToKey = array_flip($keyToNumber);

                    $numericSemester = $keyToNumber[$requested] ?? $requested;
                    $textSemester = $numberToKey[$requested] ?? $requested;

                    $candidates = array_values(array_unique(array_filter([
                        $requested,
                        $numericSemester,
                        $textSemester,
                    ])));

                    // Include all-semester exams also when a specific assigned semester is selected
                    $examsQuery = $examsQuery->where(function ($q) use ($candidates) {
                        $q->whereIn('semester', $candidates)
                          ->orWhere('semester', 'all')
                          ->orWhereNull('semester');
                    });
                }
            }
            // If empty string, don't filter - show all exams
            
            if ($request->filled('subject_id') && $request->subject_id) {
                $examsQuery = $examsQuery->where('subject_id', $request->subject_id);
            }
            
            if ($request->filled('exam_category') && $request->exam_category) {
                $examsQuery = $examsQuery->where('exam_category', $request->exam_category);
            }
            
            if ($request->filled('status') && $request->status) {
                $status = $request->status;
                
                // Handle marks_filled and marks_not_filled status filters
                if ($status === 'marks_filled') {
                    // Get exams that have at least one mark entry
                    $examsQuery->whereHas('marks', function ($q) {
                        $q->whereNotNull('marks_obtained');
                    });
                } elseif ($status === 'marks_not_filled') {
                    // Get exams that have no mark entries or all marks are null
                    $examsQuery->whereDoesntHave('marks', function ($q) {
                        $q->whereNotNull('marks_obtained');
                    });
                } elseif ($status === 'upcoming') {
                    // Get exams with future exam dates
                    $examsQuery->where('exam_date', '>', now()->toDateString());
                } elseif ($status === 'completed') {
                    // Get exams with past exam dates
                    $examsQuery->where('exam_date', '<', now()->toDateString());
                } else {
                    // Standard status filter (published, draft, archived, faculty)
                    $examsQuery = $examsQuery->where('status', $status);
                }
            }
            
            if ($request->filled('search') && $request->search) {
                $search = $request->search;
                $examsQuery = $examsQuery->where(function($q) use ($search) {
                    $q->where('exam_name', 'like', "%{$search}%")
                      ->orWhere('exam_name_ne', 'like', "%{$search}%");
                });
            }
            
            // Order by created_at descending (newest first)
            $examsQuery = $examsQuery->orderBy('created_at', 'desc');
            
            // Validate per_page - only allow safe values
            $allowedPerPage = [10, 25, 50];
            $perPage = (int) $request->query('per_page', 10);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 10;
            }
            // normalize request param so pagination links use a sanitized value
            $request->merge(['per_page' => $perPage]);
            
            $exams = $examsQuery->paginate($perPage)->appends($request->all());
            
            // Get filter data
            $academicYears = $this->getAcademicYears();
            $semesters = $this->getSemesters();
            $allSubjects = Subject::whereIn('id', $subjectIds)->get();
            
            $stats = $this->getStatistics($subjectIds);
            
            // Semester cards data (for UI grouping/navigation)
            $semesterCards = $this->buildSemesterCards($request, $subjectIds);
            [$selectedSemesterLabel, $selectedSemesterSubjects] = $this->getSelectedSemesterSubjects($request, $subjectIds);
            
            if ($request->ajax()) {
                $tableRows = view('teacher.partials.exams_table_rows', compact('exams'))->render();
                $tableFooter = view('teacher.partials.exams_table_footer', compact('exams'))->render();
                $statsHtml = view('teacher.partials.exams_stats', compact('stats'))->render();
                
                return response()->json([
                    'success' => true,
                    'table_rows' => $tableRows,
                    'table_footer' => $tableFooter,
                    'stats' => $statsHtml,
                ]);
            }
            
            return view('teacher.exams', compact(
                'exams',
                'academicYears',
                'semesterOptions',
                'semesters',
                'subjects',
                'stats',
                'semesterCards',
                'selectedSemester',
                'selectedSemesterLabel',
                'selectedSemesterSubjects'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading teacher exams: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load exams: ' . $e->getMessage());
        }
    }
    
    /**
     * Get academic years for filter dropdown
     */
    private function getAcademicYears()
    {
        return Exam::select('academic_year')
            ->whereNotNull('academic_year')
            ->distinct()
            ->orderByDesc('academic_year')
            ->pluck('academic_year')
            ->toArray();
    }
    
    /**
     * Get semesters for filter dropdown
     */
    private function getSemesters()
    {
        return [
            'all' => 'All Semesters',
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'third' => 'Third Semester',
            'fourth' => 'Fourth Semester',
            'fifth' => 'Fifth Semester',
            'sixth' => 'Sixth Semester',
        ];
    }
    
    /**
     * Get statistics for exams
     */
    private function getStatistics(array $subjectIds)
    {
        $total = Exam::whereIn('subject_id', $subjectIds)->count();
        
        $published = Exam::whereIn('subject_id', $subjectIds)
            ->where('status', 'published')
            ->count();
            
        $draft = Exam::whereIn('subject_id', $subjectIds)
            ->where('status', 'draft')
            ->count();
            
        $archived = Exam::whereIn('subject_id', $subjectIds)
            ->where('status', 'archived')
            ->count();
            
        $faculty = Exam::whereIn('subject_id', $subjectIds)
            ->where('status', 'faculty')
            ->count();
        
        $upcoming = Exam::whereIn('subject_id', $subjectIds)
            ->where('exam_date', '>', now()->toDateString())
            ->count();
            
        $completed = Exam::whereIn('subject_id', $subjectIds)
            ->where('exam_date', '<', now()->toDateString())
            ->count();
        
        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
            'archived' => $archived,
            'faculty' => $faculty,
            'upcoming' => $upcoming,
            'completed' => $completed,
        ];
    }
    
    /**
     * Build semester cards data for UI grouping/navigation
     */
    private function buildSemesterCards(Request $request, array $subjectIds)
    {
        $semesterCards = [];
        
        // Add "All" semester card
        $allCount = Exam::whereIn('subject_id', $subjectIds)
            ->where(function($query) {
                $query->where('semester', 'all')
                      ->orWhereNull('semester');
            })
            ->count();
            
        $semesterCards['all'] = [
            'label' => 'All Semesters',
            'count' => $allCount,
            'selected' => $request->semester === 'all' || empty($request->semester),
        ];
        
        // Add specific semester cards
        $semesterNames = [
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'third' => 'Third Semester',
            'fourth' => 'Fourth Semester',
            'fifth' => 'Fifth Semester',
            'sixth' => 'Sixth Semester',
        ];
        
        foreach ($semesterNames as $key => $name) {
            $count = Exam::whereIn('subject_id', $subjectIds)
                ->where('semester', $key)
                ->count();
                
            $semesterCards[$key] = [
                'label' => $name,
                'count' => $count,
                'selected' => $request->semester === $key,
            ];
        }
        
        return $semesterCards;
    }
    
    /**
     * Get selected semester label and subjects for display
     */
    private function getSelectedSemesterSubjects(Request $request, array $subjectIds)
    {
        $semester = $request->get('semester', '');
        $selectedSemesterLabel = 'All Semesters';
        $selectedSemesterSubjects = collect([]);
        
        if (!empty($semester) && $semester !== 'all') {
            $selectedSemesterLabel = ucfirst($semester) . ' Semester';
            
            // Get subjects for the selected semester
            $selectedSemesterSubjects = Subject::whereIn('id', $subjectIds)
                ->where('semester', $semester)
                ->orderBy('subject_name')
                ->get(['id', 'subject_name', 'subject_code']);
        } elseif (empty($semester) || $semester === 'all') {
            $selectedSemesterLabel = 'All Semesters';
            
            // Get all subjects (no semester filter)
            $selectedSemesterSubjects = Subject::whereIn('id', $subjectIds)
                ->orderBy('subject_name')
                ->get(['id', 'subject_name', 'subject_code']);
        }
        
        return [$selectedSemesterLabel, $selectedSemesterSubjects];
    }
    
    /**
     * Get students for exam entry
     */
    public function getExamStudents(Request $request, $examId)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        $subjectIds = $this->getTeacherSubjectIds();

        // Get exam and verify it belongs to teacher's subject
        $exam = Exam::find($examId);
        
        if (!$exam) {
            return response()->json(['error' => 'Exam not found'], 404);
        }

        if (!in_array($exam->subject_id, $subjectIds)) {
            return response()->json(['error' => 'Exam not assigned to you'], 403);
        }

        // Get students enrolled in this subject
        $students = DB::table('subject_students')
            ->join('users', 'subject_students.student_id', '=', 'users.id')
            ->leftJoin('students', 'users.id', '=', 'students.user_id')
            ->where('subject_students.subject_id', $exam->subject_id)
            ->select(
                'students.id as student_id',
                'users.id as user_id',
                'users.name',
                'users.email',
                'students.roll_no'
            )
            ->orderBy('users.name')
            ->get();

        // Get existing marks for this exam
        $existingMarks = DB::table('exam_marks')
            ->where('exam_id', $examId)
            ->where('subject_id', $exam->subject_id)
            ->pluck('marks', 'student_id');

        // Merge with existing marks
        $students = $students->map(function ($student) use ($existingMarks) {
            $student->marks = $existingMarks->get($student->student_id);
            return $student;
        });

        return response()->json([
            'students' => $students,
            'exam' => $exam,
        ]);
    }

    /**
     * Show exam details and upload modal for teacher.
     */
    public function show(Exam $exam)
    {
        try {
            $teacher = auth()->user()->teacher;
            if (!$teacher) {
                return redirect()->route('teacher.exams')->with('error', 'Teacher profile not found.');
            }

            $subjectIds = $this->getTeacherSubjectIds();
            if ($exam->subject_id && !in_array($exam->subject_id, $subjectIds, true)) {
                return redirect()->route('teacher.exams')->with('error', 'Exam is not assigned to your subjects.');
            }

            $exam->load(['subject', 'marks.student.user']);

            $totalStudents = $exam->marks()->count();
            $averageMarks = $exam->marks()->avg('marks_obtained') ?? 0;
            $passCount = $exam->marks()->where('percentage', '>=', 35)->count();
            $passRate = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100, 2) : 0;

            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('subject_name')->get();
            $semesters = $this->getSemesters();
            $activeExamCategory = in_array($exam->exam_category, ['assessment', 'ctevt']) ? $exam->exam_category : 'assessment';

            return view('teacher.exam-show', compact(
                'exam',
                'totalStudents',
                'averageMarks',
                'passRate',
                'subjects',
                'semesters',
                'activeExamCategory'
            ));
        } catch (\Exception $e) {
            Log::error('Error showing teacher exam: ' . $e->getMessage());
            return redirect()->route('teacher.exams')->with('error', 'Failed to load exam details.');
        }
    }

    /**
     * Get available academic years and semesters (teacher scope)
     */
    public function getAvailableYearsAndSemesters()
    {
        try {
            $students = Student::where('is_active', true)
                ->select('academic_year_bs', 'batch_year', 'semester')
                ->get();

            $yearSemesterMap = [];
            foreach ($students as $student) {
                $year = $student->academic_year_bs ?? $student->batch_year;
                $semester = $student->semester;

                if ($year && $semester) {
                    if (!isset($yearSemesterMap[$year])) {
                        $yearSemesterMap[$year] = [];
                    }
                    if (!in_array($semester, $yearSemesterMap[$year], true)) {
                        $yearSemesterMap[$year][] = $semester;
                    }
                }
            }

            $semesterLabels = [
                '1' => 'First',
                '2' => 'Second',
                '3' => 'Third',
                '4' => 'Fourth',
                '5' => 'Fifth',
                '6' => 'Sixth',
            ];

            $years = array_keys($yearSemesterMap);
            rsort($years);

            $grouped = [];
            foreach ($years as $year) {
                $semesters = $yearSemesterMap[$year];
                sort($semesters);

                $semesterOptions = array_map(function ($sem) use ($semesterLabels) {
                    return [
                        'value' => (string)$sem,
                        'label' => $semesterLabels[$sem] ?? "Semester {$sem}",
                    ];
                }, $semesters);

                $grouped[] = [
                    'year' => $year,
                    'semesters' => $semesterOptions,
                ];
            }

            return response()->json([
                'success' => true,
                'years' => $grouped,
                'message' => count($grouped) === 0 ? 'No students found with academic year and semester' : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting academic years and semesters (teacher): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get subjects by semester (teacher scope) for modals
     */
    public function getSubjectsBySemester(Request $request)
    {
        $semester = (string)$request->get('semester', '');
        $isAll = $semester === '' || strtolower($semester) === 'all';

        $subjectIds = $this->getTeacherSubjectIds();
        $query = Subject::whereIn('id', $subjectIds);

        if (!$isAll) {
            $query->where('semester', $semester);
        }

        $subjects = $query->select(['id', 'subject_name', 'subject_code', 'semester'])->orderBy('subject_name')->get();

        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    /**
     * Get students with existing marks for the exam and optional subject filter.
     */
    public function getStudentsWithMarks(Request $request, Exam $exam)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 404);
        }

        $subjectIds = $this->getTeacherSubjectIds();

        $subjectId = $request->get('subject_id') ?: $exam->subject_id;
        if ($subjectId) {
            $subjectId = (int) $subjectId;
            if (!in_array($subjectId, $subjectIds, true)) {
                return response()->json(['success' => false, 'message' => 'Subject not assigned to you'], 403);
            }
            $subjectIds = [$subjectId];
        }

        if (empty($subjectIds)) {
            return response()->json(['success' => false, 'message' => 'No subjects assigned'], 400);
        }

        // If specific subject is selected, only that subject; otherwise teacher subjects.
        if ($subjectId) {
            $studentIds = TeacherSubjectRoster::studentIdsForSubject((int)$subjectId);
        } else {
            $studentIds = TeacherSubjectRoster::studentIdsForSubjects($subjectIds);
        }

        if (empty($studentIds)) {
            return response()->json(['success' => true, 'students' => [], 'existing_marks' => [], 'subject_full_marks' => $exam->full_marks, 'subject_passing_marks' => $exam->passing_marks]);
        }

        $studentQuery = Student::with('user')->whereIn('id', $studentIds);

        $semester = (string)$request->get('semester', '');
        if ($semester && strtolower($semester) !== 'all') {
            $studentQuery->where('semester', $semester);
        }

        $students = $studentQuery->get()->map(function ($student) {
            return (object)[
                'id' => $student->id,
                'student_name' => $student->user->name ?? 'Unknown',
                'roll_no' => $student->roll_no ?? '-',
                'attendance_percentage' => $student->attendance_percentage ?? 0,
            ];
        });

        $examMarks = ExamMark::where('exam_id', $exam->id)
            ->when($subjectId, function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->get();

        $existingMarks = [];
        foreach ($examMarks as $mark) {
            $existingMarks[$mark->student_id] = [
                'marks_obtained' => $mark->marks_obtained,
                'full_marks' => $mark->full_marks,
                'passing_marks' => $mark->passing_marks,
                'percentage' => $mark->percentage,
                'grade' => $mark->grade,
                'subject_id' => $mark->subject_id,
            ];
        }

        return response()->json([
            'success' => true,
            'students' => $students,
            'existing_marks' => $existingMarks,
            'subject_full_marks' => $exam->full_marks,
            'subject_passing_marks' => $exam->passing_marks,
        ]);
    }

    /**
     * Upload marks for teacher exam from modal form.
     */
    public function uploadMarks(Request $request, Exam $exam)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return back()->with('error', 'Teacher profile not found.');
        }

        $subjectIds = $this->getTeacherSubjectIds();
        if ($exam->subject_id && !in_array($exam->subject_id, $subjectIds, true)) {
            return back()->with('error', 'Exam is not assigned to your subjects.');
        }

        $validated = $request->validate([
            'marks' => 'required|array|min:1',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0',
            'marks.*.full_marks' => 'nullable|numeric|min:0',
            'marks.*.passing_marks' => 'nullable|numeric|min:0',
            'marks.*.subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $createdCount = 0;
            $updatedCount = 0;

            foreach ($validated['marks'] as $markData) {
                $studentId = $markData['student_id'];
                $marksObtained = $markData['marks_obtained'];
                $subjectId = $markData['subject_id'] ?? $exam->subject_id;

                $fullMarks = $markData['full_marks'] ?? $exam->full_marks;
                $passingMarks = $markData['passing_marks'] ?? $exam->passing_marks;

                if ($subjectId && !in_array($subjectId, $subjectIds, true)) {
                    continue;
                }

                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;
                $grade = $this->calculateGrade($marksObtained, $fullMarks);

                $existing = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $studentId)
                    ->where('subject_id', $subjectId)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'passing_marks' => $passingMarks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $request->input('description'),
                    ]);
                    $updatedCount++;
                } else {
                    ExamMark::create([
                        'exam_id' => $exam->id,
                        'subject_id' => $subjectId,
                        'student_id' => $studentId,
                        'marks_obtained' => $marksObtained,
                        'full_marks' => $fullMarks,
                        'passing_marks' => $passingMarks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'graded_by' => auth()->id(),
                        'graded_at' => now(),
                        'remarks' => $request->input('description'),
                    ]);
                    $createdCount++;
                }
            }

            if ($request->has('description') && $request->description !== null) {
                $exam->update(['description' => $request->description]);
            }

            DB::commit();
            return back()->with('success', "Marks uploaded successfully! Created: {$createdCount}, Updated: {$updatedCount}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks (teacher): ' . $e->getMessage());
            return back()->with('error', 'Failed to upload marks: ' . $e->getMessage());
        }
    }

    private function calculateGrade($marks, $fullMarks)
    {
        if ($fullMarks <= 0) return 'N/A';

        $percentage = ($marks / $fullMarks) * 100;
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 40) return 'C';
        if ($percentage >= 35) return 'D';
        return 'F';
    }

    public function getSubjectMarks(Exam $exam, $subjectId)
    {
        try {
            $subject = Subject::find($subjectId);
            if (!$subject) {
                return response()->json(['success' => false, 'message' => 'Subject not found'], 404);
            }

            $mark = ExamMark::where('exam_id', $exam->id)
                ->where('subject_id', $subject->id)
                ->first();

            $subjectMarks = [
                'full_marks' => $exam->full_marks,
                'passing_marks' => $exam->passing_marks,
                'theory_internal_full_marks' => $exam->theory_internal_max_marks,
                'theory_external_full_marks' => $exam->theory_external_max_marks,
                'practical_internal_full_marks' => $exam->practical_internal_max_marks,
                'practical_external_full_marks' => $exam->practical_external_max_marks,
                'theory_internal_pass_marks' => $exam->theory_internal_pass_marks,
                'theory_external_pass_marks' => $exam->theory_external_pass_marks,
                'practical_internal_pass_marks' => $exam->practical_internal_pass_marks,
                'practical_external_pass_marks' => $exam->practical_external_pass_marks,
            ];

            if ($mark) {
                $subjectMarks = [
                    'full_marks' => $mark->full_marks ?? $subjectMarks['full_marks'],
                    'passing_marks' => $mark->passing_marks ?? $subjectMarks['passing_marks'],
                    'theory_internal_full_marks' => $mark->theory_internal_full_marks ?? $subjectMarks['theory_internal_full_marks'],
                    'theory_external_full_marks' => $mark->theory_external_full_marks ?? $subjectMarks['theory_external_full_marks'],
                    'practical_internal_full_marks' => $mark->practical_internal_full_marks ?? $subjectMarks['practical_internal_full_marks'],
                    'practical_external_full_marks' => $mark->practical_external_full_marks ?? $subjectMarks['practical_external_full_marks'],
                    'theory_internal_pass_marks' => $mark->theory_internal_pass_marks ?? $subjectMarks['theory_internal_pass_marks'],
                    'theory_external_pass_marks' => $mark->theory_external_pass_marks ?? $subjectMarks['theory_external_pass_marks'],
                    'practical_internal_pass_marks' => $mark->practical_internal_pass_marks ?? $subjectMarks['practical_internal_pass_marks'],
                    'practical_external_pass_marks' => $mark->practical_external_pass_marks ?? $subjectMarks['practical_external_pass_marks'],
                ];
            }

            return response()->json([
                'success' => true,
                'marks' => $subjectMarks
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting subject marks (teacher): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load subject marks'], 500);
        }
    }

    public function getMarkData(ExamMark $mark)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 404);
        }

        $subjectIds = $this->getTeacherSubjectIds();
        if (!in_array($mark->subject_id, $subjectIds, true)) {
            return response()->json(['success' => false, 'message' => 'Subject not assigned to you'], 403);
        }

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
                'passing_marks' => $mark->passing_marks ?? $mark->exam->passing_marks,
                'is_passed' => ($mark->percentage ?? 0) >= 35,
            ]
        ]);
    }

    public function updateMark(Request $request, ExamMark $mark)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 404);
        }

        $subjectIds = $this->getTeacherSubjectIds();
        if (!in_array($mark->subject_id, $subjectIds, true)) {
            return response()->json(['success' => false, 'message' => 'Subject not assigned to you'], 403);
        }

        $validated = $request->validate([
            'marks_obtained' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $mark->marks_obtained = $validated['marks_obtained'];
        $mark->remarks = $validated['remarks'] ?? $mark->remarks;

        $fullMarks = $mark->full_marks ?? $mark->exam->full_marks;
        $mark->percentage = $fullMarks > 0 ? round(($mark->marks_obtained / $fullMarks) * 100, 2) : 0;
        $mark->grade = $this->calculateGrade($mark->marks_obtained, $fullMarks);
        $mark->graded_by = auth()->id();
        $mark->graded_at = now();
        $mark->save();

        return response()->json([
            'success' => true,
            'message' => 'Mark updated successfully',
            'mark' => [
                'id' => $mark->id,
                'marks_obtained' => $mark->marks_obtained,
                'percentage' => $mark->percentage,
                'grade' => $mark->grade,
                'remarks' => $mark->remarks,
                'is_passed' => $mark->percentage >= 35,
            ]
        ]);
    }

    public function deleteMark($id)
    {
        try {
            $mark = ExamMark::find($id);
            if (!$mark) {
                return response()->json(['success' => false, 'message' => 'Mark not found'], 404);
            }

            $teacher = auth()->user()->teacher;
            if (!$teacher) {
                return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 403);
            }

            $subjectIds = $this->getTeacherSubjectIds();
            if ($mark->subject_id && !in_array($mark->subject_id, $subjectIds, true)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $mark->delete();
            return response()->json(['success' => true, 'message' => 'Mark deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting mark (teacher): ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete mark'], 500);
        }
    }
}

