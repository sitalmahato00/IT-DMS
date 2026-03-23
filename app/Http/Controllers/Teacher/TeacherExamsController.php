<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
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
            $search = $request->get('q', '');
            $academic_year = $request->get('academic_year', '');
            $exam_category = $request->get('exam_category', '');
            $status = $request->get('status', '');
            
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
                    
                    // Show only exams for the specific semester (not including 'all')
                    $examsQuery = $examsQuery->whereIn('semester', $candidates);
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
                'semesters',
                'subjects',
                'stats',
                'semesterCards',
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
}
