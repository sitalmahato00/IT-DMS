<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\Subject;
use App\Models\Department;
use App\Support\TeacherSubjectRoster;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeacherStudentController extends Controller
{
    /**
     * Display students enrolled in teacher's subjects.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);
        
        if (!$teacher) {
            return view('teacher.students', [
                'students' => collect([]),
                'subjects' => collect([]),
                'selectedSubject' => null,
                'availableSemesters' => [],
                'stats' => ['total' => 0, 'male' => 0, 'female' => 0],
            ]);
        }

        $subjectData = $this->getAssignedSubjects($user, $teacher);
        $subjects = $subjectData['subjects'];
        $availableSemesters = $subjectData['availableSemesters'];

        $studentData = $this->buildStudentQuery($request, $subjects, $availableSemesters);
        $query = $studentData['query'];
        $selectedSubject = $studentData['selectedSubject'];

        // Stats for current filters (distinct students)
        $studentGenderRows = (clone $query)
            ->select('u.id', 'st.gender')
            ->distinct()
            ->get();

        $stats = [
            'total' => $studentGenderRows->count(),
            'male' => $studentGenderRows->where('gender', 'male')->count(),
            'female' => $studentGenderRows->where('gender', 'female')->count(),
        ];

        // Paginate
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        // Get paginated students
        $students = $this->applyStudentListSelects($query)
            ->paginate($perPage)
            ->withQueryString();

        return view('teacher.students', compact(
            'students',
            'subjects',
            'selectedSubject',
            'availableSemesters',
            'stats'
        ));
    }

    /**
     * Display a specific student's details.
     */
    public function show($id)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.students')->with('error', 'Teacher profile not found.');
        }

        // Get teacher's subject IDs from pivot table plus legacy/erroneous user IDs
        $subjectIds = SubjectTeacher::where('teacher_id', $teacher->id)
            ->orWhere('teacher_id', auth()->id())
            ->pluck('subject_id')
            ->toArray();

        // Include legacy subjects table assignment format (subjects.teacher_id)
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjectIds = \App\Models\Subject::where('teacher_id', $teacher->id)
                ->pluck('id')
                ->toArray();
            $subjectIds = array_values(array_unique(array_merge($subjectIds, $legacySubjectIds)));
        }

        // Get student and verify they're in teacher's subjects
        $student = User::where('id', $id)
            ->where('role', 'student')
            ->with('student')
            ->first();

        $accessibleStudentIds = TeacherSubjectRoster::studentIdsForSubjects($subjectIds);

        if (
            !$student ||
            !$student->student ||
            !in_array($student->student->id, $accessibleStudentIds, true)
        ) {
            return redirect()->route('teacher.students')->with('error', 'Student not found in your subjects.');
        }

        $studentSubjects = Subject::whereIn('id', $subjectIds)
            ->where('semester', $student->student->semester)
            ->select('id', 'subject_name', 'subject_code', 'semester')
            ->orderBy('subject_name')
            ->get();

        // Get attendance records for this student in teacher's subjects
        $attendanceRecords = DB::table('attendance')
            ->where('student_id', $id)
            ->whereIn('subject_id', $subjectIds)
            ->where('attendance_type', 'class')
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        // Calculate attendance rate per subject
        $attendanceBySubject = [];
        foreach ($subjectIds as $subjectId) {
            $stats = DB::table('attendance')
                ->where('student_id', $id)
                ->where('subject_id', $subjectId)
                ->where('attendance_type', 'class')
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count
                ')
                ->first();

            $attendanceBySubject[$subjectId] = $stats && $stats->total > 0
                ? round(($stats->present_count / $stats->total) * 100, 1)
                : 0;
        }

        // Get exam marks for this student in teacher's subjects
        $examMarks = DB::table('exam_marks')
            ->where('student_id', $id)
            ->whereIn('subject_id', $subjectIds)
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->select('exam_marks.*', 'exams.exam_name', 'exams.exam_date')
            ->orderBy('exams.exam_date', 'desc')
            ->get();

        return view('teacher.student-show', compact('student', 'studentSubjects', 'attendanceRecords', 'attendanceBySubject', 'examMarks'));
    }

    /**
     * Return subjects assigned to the current teacher for a given semester (active subjects only).
     */
    public function subjectsBySemester(Request $request)
    {
        $semester = (int) $request->get('semester');
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);
        if (!$teacher) {
            return response()->json(['subjects' => []]);
        }

        $subjectData = $this->getAssignedSubjects($user, $teacher);
        $subjects = $subjectData['subjects'];

        if ($semester > 0) {
            $subjects = $subjects
                ->filter(fn ($subject) => (int) ($subject['semester'] ?? 0) === $semester)
                ->values();
        }

        $subjects = $subjects
            ->map(fn ($subject) => [
                'id' => $subject['id'],
                'name' => $subject['name'],
                'code' => $subject['code'],
            ])
            ->values();

        return response()->json(['subjects' => $subjects]);
    }

    /**
     * Export students to CSV.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);
        
        if (!$teacher) {
            return redirect()->route('teacher.students')->with('error', 'Teacher profile not found.');
        }

        $subjectData = $this->getAssignedSubjects($user, $teacher);
        $studentData = $this->buildStudentQuery($request, $subjectData['subjects'], $subjectData['availableSemesters']);
        $students = $this->applyStudentListSelects($studentData['query'])->get();

        // Create CSV
        $filename = 'students-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($students) {
            $handle = fopen('php://output', 'w');
            
            // Header row
            fputcsv($handle, ['Name', 'Email', 'Roll No', 'Registration No', 'Phone', 'Gender', 'Semester', 'Academic Year', 'Status']);
            
            // Data rows
            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->name,
                    $student->email,
                    $student->roll_no ?? 'N/A',
                    $student->registration_number ?? 'N/A',
                    $student->phone ?? 'N/A',
                    $student->gender ?? 'N/A',
                    $student->semester ?? 'N/A',
                    $student->academic_year ?? 'N/A',
                    $student->status ?? 'N/A',
                ]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Render the filtered student list for the shared teacher print modal.
     */
    public function print(Request $request)
    {
        $user = auth()->user();
        $teacher = $this->resolveTeacherProfile($user);

        if (!$teacher) {
            return redirect()->route('teacher.students')->with('error', 'Teacher profile not found.');
        }

        $subjectData = $this->getAssignedSubjects($user, $teacher);
        $studentData = $this->buildStudentQuery($request, $subjectData['subjects'], $subjectData['availableSemesters']);
        $students = $this->applyStudentListSelects($studentData['query'])->get();

        $filters = [
            'semester' => $request->input('semester'),
            'subject' => $this->selectedSubjectLabel($subjectData['subjects'], $studentData['selectedSubject']),
            'search' => trim((string) $request->input('q', '')),
        ];

        $college = Department::first();

        return view('teacher.print.students-list', compact('students', 'college', 'filters'));
    }

    private function resolveTeacherProfile($user)
    {
        $teacher = $user->teacher;

        if (!$teacher && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => 'TCH-' . $user->id,
                'status' => 'active',
            ]);
        }

        return $teacher;
    }

    private function getAssignedSubjects($user, $teacher): array
    {
        $pivotAssignments = SubjectTeacher::whereIn('teacher_id', [$teacher->id, $user->id])
            ->with('subject')
            ->get();

        $legacySubjects = collect();
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $teacher->id)->get();
        }

        $activePivotAssignments = $pivotAssignments->filter(function ($assignment) {
            $subject = $assignment->subject;
            return $subject && ($subject->status ?? 'active') === 'active';
        });

        $activeLegacySubjects = $legacySubjects->filter(function ($subject) {
            return ($subject->status ?? 'active') === 'active';
        });

        $subjects = collect();
        foreach ($activePivotAssignments as $assignment) {
            $subject = $assignment->subject;
            $subjects->push([
                'id' => $subject->id,
                'name' => $subject->subject_name,
                'code' => $subject->subject_code,
                'semester' => is_numeric($subject->semester ?? null) ? (int) $subject->semester : null,
            ]);
        }

        foreach ($activeLegacySubjects as $subject) {
            if ($subjects->where('id', $subject->id)->isEmpty()) {
                $subjects->push([
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => is_numeric($subject->semester ?? null) ? (int) $subject->semester : null,
                ]);
            }
        }

        $subjects = $subjects
            ->unique('id')
            ->sortBy([
                fn ($subject) => $subject['semester'] ?? 999,
                fn ($subject) => $subject['name'] ?? '',
            ])
            ->values();

        $availableSemesters = $subjects
            ->pluck('semester')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return [
            'subjects' => $subjects,
            'availableSemesters' => $availableSemesters,
        ];
    }

    private function buildStudentQuery(Request $request, $subjects, array $availableSemesters): array
    {
        $selectedSubject = $request->filled('subject') ? (int) $request->input('subject') : null;
        $selectedSemester = $request->filled('semester') ? (int) $request->input('semester') : null;

        $filteredSubjects = $subjects;
        if ($selectedSemester && in_array($selectedSemester, $availableSemesters, true)) {
            $filteredSubjects = $filteredSubjects
                ->filter(fn ($subject) => (int) ($subject['semester'] ?? 0) === $selectedSemester)
                ->values();
        }

        $filteredSubjectIds = $filteredSubjects
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($selectedSubject && in_array($selectedSubject, $filteredSubjectIds, true)) {
            $studentIds = TeacherSubjectRoster::studentIdsForSubject($selectedSubject);
        } else {
            $selectedSubject = null;
            $studentIds = TeacherSubjectRoster::studentIdsForSubjects($filteredSubjectIds);
        }

        $query = DB::table('students as st')
            ->join('users as u', 'st.user_id', '=', 'u.id')
            ->whereIn('st.id', $studentIds)
            ->where('u.role', 'student')
            ->where(function ($builder) {
                $builder->where('st.status', 'active')
                    ->orWhereNull('st.status');
            })
            ->where(function ($builder) {
                $builder->where('st.is_alumni', 0)
                    ->orWhereNull('st.is_alumni');
            });

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('u.name', 'like', "%{$search}%")
                    ->orWhere('u.email', 'like', "%{$search}%")
                    ->orWhere('st.registration_number', 'like', "%{$search}%")
                    ->orWhere('st.roll_no', 'like', "%{$search}%");
            });
        }

        return [
            'query' => $query,
            'selectedSubject' => $selectedSubject,
        ];
    }

    private function applyStudentListSelects($query)
    {
        return $query
            ->select(
                'u.id',
                'u.name',
                'u.email',
                'u.profile_photo_path',
                'u.role',
                'st.roll_no',
                'st.registration_number',
                'st.phone',
                'st.gender',
                'st.date_of_birth',
                'st.date_of_birth_bs',
                'st.academic_year',
                'st.academic_year_bs',
                'st.address',
                'st.bio',
                'st.status',
                'st.is_alumni',
                'st.blood_group',
                'st.emergency_contact',
                'st.semester'
            )
            ->orderBy('u.name', 'asc')
            ->distinct();
    }

    private function selectedSubjectLabel($subjects, ?int $selectedSubject): ?string
    {
        if (!$selectedSubject) {
            return null;
        }

        $subject = $subjects->first(fn ($item) => (int) ($item['id'] ?? 0) === $selectedSubject);
        if (!$subject) {
            return null;
        }

        $parts = array_values(array_filter([
            $subject['code'] ?? null,
            $subject['name'] ?? null,
        ], fn ($value) => $value !== null && $value !== ''));

        return empty($parts) ? null : implode(' - ', $parts);
    }
}
