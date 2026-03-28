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
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Support\TeacherSubjectRoster;
use App\Traits\LogsActivity;

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

        $teacherIds = array_values(array_unique(array_filter([
            $teacher->id,
            $user->id,
        ])));

        $subjectIds = SubjectTeacher::whereIn('teacher_id', $teacherIds)
            ->pluck('subject_id')
            ->map(fn ($subjectId) => (int) $subjectId)
            ->toArray();

        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $subjectIds = array_merge(
                $subjectIds,
                Subject::where('teacher_id', $teacher->id)
                    ->pluck('id')
                    ->map(fn ($subjectId) => (int) $subjectId)
                    ->toArray()
            );
        }

        return array_values(array_unique(array_filter($subjectIds)));
    }

    private function normalizeSemesterValue($semester): ?string
    {
        if ($semester === null || $semester === '') {
            return null;
        }

        $normalized = strtolower(trim((string) $semester));
        $numberToKey = [
            '1' => 'first',
            '2' => 'second',
            '3' => 'third',
            '4' => 'fourth',
            '5' => 'fifth',
            '6' => 'sixth',
        ];

        return $numberToKey[$normalized] ?? $normalized;
    }

    private function getSemesterFilterCandidates(string $semester): array
    {
        $normalized = $this->normalizeSemesterValue($semester);
        $keyToNumber = [
            'first' => '1',
            'second' => '2',
            'third' => '3',
            'fourth' => '4',
            'fifth' => '5',
            'sixth' => '6',
        ];

        return array_values(array_unique(array_filter([
            strtolower(trim($semester)),
            $normalized,
            $normalized ? ($keyToNumber[$normalized] ?? null) : null,
        ])));
    }

    private function getSemesterLabel(?string $semester): string
    {
        $labels = [
            'all' => 'All Semesters',
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'third' => 'Third Semester',
            'fourth' => 'Fourth Semester',
            'fifth' => 'Fifth Semester',
            'sixth' => 'Sixth Semester',
        ];

        $normalized = $this->normalizeSemesterValue($semester) ?? 'all';

        return $labels[$normalized] ?? ucfirst($normalized);
    }

    private function getTeacherSubjects(array $subjectIds)
    {
        if (empty($subjectIds)) {
            return collect();
        }

        return Subject::whereIn('id', $subjectIds)
            ->orderBy('subject_name')
            ->get(['id', 'subject_name', 'subject_code', 'semester'])
            ->map(function (Subject $subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $this->normalizeSemesterValue($subject->semester),
                ];
            })
            ->values();
    }

    private function getEmptyIndexViewData(Request $request, $subjects = null): array
    {
        $subjects = $subjects ? collect($subjects)->values() : collect();

        $allowedPerPage = [10, 25, 50];
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $semesterOptions = [
            '' => 'All Exams (All + Semester-specific)',
            'all' => 'All Semesters',
        ];

        foreach ($subjects->pluck('semester')->filter()->unique()->values() as $semester) {
            $semesterOptions[$semester] = $this->getSemesterLabel($semester);
        }

        return [
            'exams' => new LengthAwarePaginator(collect(), 0, $perPage),
            'subjects' => $subjects,
            'semesterOptions' => $semesterOptions,
            'selectedSemester' => (string) $request->query('semester', ''),
            'selectedSubject' => (string) $request->query('subject', $request->query('subject_id', '')),
            'stats' => [
                'total' => 0,
                'published' => 0,
                'draft' => 0,
                'archived' => 0,
                'faculty' => 0,
                'upcoming' => 0,
                'completed' => 0,
            ],
            'upcomingCount' => 0,
            'completedCount' => 0,
        ];
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
                return view('teacher.exams', $this->getEmptyIndexViewData($request));
            }

            $subjectIds = $this->getTeacherSubjectIds();
            $subjects = $this->getTeacherSubjects($subjectIds);

            if (empty($subjectIds)) {
                return view('teacher.exams', $this->getEmptyIndexViewData($request, $subjects));
            }

            $selectedSubject = (string) $request->query('subject', $request->query('subject_id', ''));
            $selectedSemester = (string) $request->query('semester', '');
            $search = trim((string) $request->query('q', $request->query('search', '')));
            $academicYear = trim((string) $request->query('academic_year', ''));
            $status = trim((string) $request->query('status', ''));

            $semesterOptions = [
                '' => 'All Exams (All + Semester-specific)',
                'all' => 'All Semesters',
            ];

            foreach ($subjects->pluck('semester')->filter()->unique()->values() as $semester) {
                $semesterOptions[$semester] = $this->getSemesterLabel($semester);
            }

            // Get exams for teacher's subjects
            $examsQuery = Exam::whereIn('subject_id', $subjectIds)
                ->with('subject');

            if ($academicYear !== '') {
                $examsQuery->forYear($academicYear);
            }

            if ($selectedSemester !== '') {
                if ($selectedSemester === 'all') {
                    $examsQuery = $examsQuery->where('semester', 'all');
                } else {
                    $candidates = $this->getSemesterFilterCandidates($selectedSemester);
                    $examsQuery = $examsQuery->where(function ($q) use ($candidates) {
                        $q->whereIn('semester', $candidates)
                          ->orWhere('semester', 'all')
                          ->orWhereNull('semester');
                    });
                }
            }

            if ($selectedSubject !== '') {
                $examsQuery = $examsQuery->where('subject_id', (int) $selectedSubject);
            }

            if ($status !== '') {
                if ($status === 'marks_filled') {
                    $examsQuery->whereHas('marks', function ($q) {
                        $q->whereNotNull('marks_obtained');
                    });
                } elseif ($status === 'marks_not_filled') {
                    $examsQuery->whereDoesntHave('marks', function ($q) {
                        $q->whereNotNull('marks_obtained');
                    });
                } elseif ($status === 'upcoming') {
                    $examsQuery->where('exam_date', '>', now()->toDateString());
                } elseif ($status === 'completed') {
                    $examsQuery->where('exam_date', '<', now()->toDateString());
                } else {
                    $examsQuery = $examsQuery->where('status', $status);
                }
            }

            if ($search !== '') {
                $examsQuery = $examsQuery->where(function ($q) use ($search) {
                    $q->where('exam_name', 'like', "%{$search}%")
                      ->orWhere('exam_name_ne', 'like', "%{$search}%");
                });
            }

            $examsQuery = $examsQuery->orderBy('created_at', 'desc');

            $allowedPerPage = [10, 25, 50];
            $perPage = (int) $request->query('per_page', 10);
            if (!in_array($perPage, $allowedPerPage, true)) {
                $perPage = 10;
            }

            $request->merge(['per_page' => $perPage]);

            $exams = $examsQuery->paginate($perPage)->appends($request->all());
            $stats = $this->getStatistics($subjectIds);

            return view('teacher.exams', [
                'exams' => $exams,
                'subjects' => $subjects,
                'semesterOptions' => $semesterOptions,
                'selectedSemester' => $selectedSemester,
                'selectedSubject' => $selectedSubject,
                'stats' => $stats,
                'upcomingCount' => $stats['upcoming'] ?? 0,
                'completedCount' => $stats['completed'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading teacher exams: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load exams: ' . $e->getMessage());
        }
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
            ->join('students', 'subject_students.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
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
            ->pluck('marks_obtained', 'student_id');

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

            $exam->load(['subject', 'marks.student.user', 'marks.subject']);

            $totalStudents = $exam->subject_id
                ? TeacherSubjectRoster::studentCountForSubject((int) $exam->subject_id)
                : $exam->marks()->distinct('student_id')->count('student_id');
            $averageMarks = $exam->marks()->avg('percentage') ?? 0;
            $semesters = $this->getSemesters();
            $activeExamCategory = in_array($exam->exam_category, ['assessment', 'ctevt']) ? $exam->exam_category : 'assessment';

            return view('teacher.exam-show', compact(
                'exam',
                'totalStudents',
                'averageMarks',
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
            $candidates = $this->getSemesterFilterCandidates($semester);
            $query->whereIn('semester', $candidates);
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
        if ($exam->subject_id && !in_array((int) $exam->subject_id, $subjectIds, true)) {
            return response()->json(['success' => false, 'message' => 'Exam is not assigned to you'], 403);
        }

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
            $studentQuery->whereIn('semester', $this->getSemesterFilterCandidates($semester));
        }

        $attendanceSubjectId = $subjectId ?: $exam->subject_id;
        $students = $studentQuery->get()->map(function ($student) use ($attendanceSubjectId) {
            return (object)[
                'id' => $student->id,
                'student_name' => $student->user->name ?? 'Unknown',
                'roll_no' => $student->roll_no ?? '-',
                'attendance_percentage' => $student->getAttendancePercentage($attendanceSubjectId),
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
                'theory_internal_marks' => $mark->theory_internal_marks,
                'theory_external_marks' => $mark->theory_external_marks,
                'practical_internal_marks' => $mark->practical_internal_marks,
                'practical_external_marks' => $mark->practical_external_marks,
                'theory_internal_full_marks' => $mark->theory_internal_full_marks,
                'theory_external_full_marks' => $mark->theory_external_full_marks,
                'practical_internal_full_marks' => $mark->practical_internal_full_marks,
                'practical_external_full_marks' => $mark->practical_external_full_marks,
                'theory_internal_pass_marks' => $mark->theory_internal_pass_marks,
                'theory_external_pass_marks' => $mark->theory_external_pass_marks,
                'practical_internal_pass_marks' => $mark->practical_internal_pass_marks,
                'practical_external_pass_marks' => $mark->practical_external_pass_marks,
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
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Teacher profile not found.'], 404);
            }

            return back()->with('error', 'Teacher profile not found.');
        }

        $subjectIds = $this->getTeacherSubjectIds();
        if ($exam->subject_id && !in_array($exam->subject_id, $subjectIds, true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Exam is not assigned to your subjects.'], 403);
            }

            return back()->with('error', 'Exam is not assigned to your subjects.');
        }

        $validated = $request->validate([
            'marks' => 'required|array|min:1',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0',
            'marks.*.full_marks' => 'nullable|numeric|min:0',
            'marks.*.passing_marks' => 'nullable|numeric|min:0',
            'marks.*.subject_id' => 'nullable|exists:subjects,id',
            'marks.*.theory_internal_marks' => 'nullable|numeric|min:0',
            'marks.*.theory_external_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_internal_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_external_marks' => 'nullable|numeric|min:0',
            'marks.*.theory_internal_full_marks' => 'nullable|numeric|min:0',
            'marks.*.theory_external_full_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_internal_full_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_external_full_marks' => 'nullable|numeric|min:0',
            'marks.*.theory_internal_pass_marks' => 'nullable|numeric|min:0',
            'marks.*.theory_external_pass_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_internal_pass_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_external_pass_marks' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $createdCount = 0;
            $updatedCount = 0;
            $isCtevt = $exam->exam_category === 'ctevt';

            foreach ($validated['marks'] as $markData) {
                $studentId = (int) $markData['student_id'];
                $subjectId = isset($markData['subject_id']) ? (int) $markData['subject_id'] : $exam->subject_id;

                if ($subjectId && !in_array($subjectId, $subjectIds, true)) {
                    continue;
                }

                if ($isCtevt) {
                    $componentMarks = [
                        'theory_internal_marks' => (float) ($markData['theory_internal_marks'] ?? 0),
                        'theory_external_marks' => (float) ($markData['theory_external_marks'] ?? 0),
                        'practical_internal_marks' => (float) ($markData['practical_internal_marks'] ?? 0),
                        'practical_external_marks' => (float) ($markData['practical_external_marks'] ?? 0),
                    ];

                    $componentFullMarks = [
                        'theory_internal_full_marks' => (float) ($markData['theory_internal_full_marks'] ?? $exam->theory_internal_max_marks ?? 0),
                        'theory_external_full_marks' => (float) ($markData['theory_external_full_marks'] ?? $exam->theory_external_max_marks ?? 0),
                        'practical_internal_full_marks' => (float) ($markData['practical_internal_full_marks'] ?? $exam->practical_internal_max_marks ?? 0),
                        'practical_external_full_marks' => (float) ($markData['practical_external_full_marks'] ?? $exam->practical_external_max_marks ?? 0),
                    ];

                    $componentPassingMarks = [
                        'theory_internal_pass_marks' => (float) ($markData['theory_internal_pass_marks'] ?? $exam->theory_internal_pass_marks ?? 0),
                        'theory_external_pass_marks' => (float) ($markData['theory_external_pass_marks'] ?? $exam->theory_external_pass_marks ?? 0),
                        'practical_internal_pass_marks' => (float) ($markData['practical_internal_pass_marks'] ?? $exam->practical_internal_pass_marks ?? 0),
                        'practical_external_pass_marks' => (float) ($markData['practical_external_pass_marks'] ?? $exam->practical_external_pass_marks ?? 0),
                    ];

                    foreach ($componentMarks as $field => $value) {
                        $fullField = str_replace('_marks', '_full_marks', $field);
                        $componentFull = (float) ($componentFullMarks[$fullField] ?? 0);

                        if ($componentFull > 0 && $value > $componentFull) {
                            throw ValidationException::withMessages([
                                'marks' => "Component marks cannot exceed {$componentFull} for {$field}.",
                            ]);
                        }
                    }

                    $marksObtained = array_sum($componentMarks);
                    if ($marksObtained <= 0 && isset($markData['marks_obtained'])) {
                        $marksObtained = (float) $markData['marks_obtained'];
                    }

                    $fullMarks = array_sum($componentFullMarks);
                    if ($fullMarks <= 0) {
                        $fullMarks = (float) ($markData['full_marks'] ?? $exam->full_marks ?? 0);
                    }

                    $passingMarks = array_sum($componentPassingMarks);
                    if ($passingMarks <= 0) {
                        $passingMarks = (float) ($markData['passing_marks'] ?? $exam->passing_marks ?? 0);
                    }
                } else {
                    $marksObtained = (float) ($markData['marks_obtained'] ?? 0);
                    $fullMarks = (float) ($markData['full_marks'] ?? $exam->full_marks ?? 0);
                    $passingMarks = (float) ($markData['passing_marks'] ?? $exam->passing_marks ?? 0);

                    if ($fullMarks > 0 && $marksObtained > $fullMarks) {
                        throw ValidationException::withMessages([
                            'marks' => "Obtained marks cannot exceed full marks ({$fullMarks}).",
                        ]);
                    }
                }

                $percentage = $fullMarks > 0 ? round(($marksObtained / $fullMarks) * 100, 2) : 0;
                $grade = $this->calculateGrade($marksObtained, $fullMarks);

                $existing = ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $studentId)
                    ->where('subject_id', $subjectId)
                    ->first();

                $payload = [
                    'marks_obtained' => $marksObtained,
                    'full_marks' => $fullMarks,
                    'passing_marks' => $passingMarks,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'graded_by' => auth()->id(),
                    'graded_at' => now(),
                    'remarks' => $request->input('description'),
                ];

                if ($isCtevt) {
                    $payload = array_merge(
                        $payload,
                        $componentMarks,
                        $componentFullMarks,
                        $componentPassingMarks
                    );
                }

                if ($existing) {
                    $existing->update($payload);
                    $updatedCount++;
                } else {
                    ExamMark::create(array_merge([
                        'exam_id' => $exam->id,
                        'subject_id' => $subjectId,
                        'student_id' => $studentId,
                    ], $payload));
                    $createdCount++;
                }
            }

            if ($request->has('description') && $request->description !== null) {
                $exam->update(['description' => $request->description]);
            }

            DB::commit();
            $message = "Marks uploaded successfully. Created: {$createdCount}, Updated: {$updatedCount}";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);
        } catch (ValidationException $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?? 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading marks (teacher): ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload marks: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to upload marks: ' . $e->getMessage());
        }
    }

    public function uploadMarksAjax(Request $request, Exam $exam)
    {
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $this->uploadMarks($request, $exam);
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
            $subjectIds = $this->getTeacherSubjectIds();
            if (!in_array((int) $subjectId, $subjectIds, true)) {
                return response()->json(['success' => false, 'message' => 'Subject not assigned to you'], 403);
            }

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
