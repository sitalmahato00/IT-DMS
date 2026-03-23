<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\Subject;
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
        $teacher = $user->teacher;
        
        // Auto-create teacher record if it doesn't exist
        if (!$teacher && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => 'TCH-' . $user->id,
                'status' => 'active',
            ]);
        }
        
        if (!$teacher) {
            return view('teacher.students', [
                'students' => collect([]),
                'subjects' => collect([]),
                'selectedSubject' => null,
                'availableSemesters' => [],
                'stats' => ['total' => 0, 'male' => 0, 'female' => 0],
            ]);
        }

        // Get teacher's subject assignments from pivot table
        $pivotAssignments = SubjectTeacher::whereIn('teacher_id', [$teacher->id, $user->id])
            ->with('subject')
            ->get();

        // Legacy subjects linked via subjects.teacher_id (if exists)
        $legacySubjects = collect();
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $teacher->id)->get();
        }

        // Filter for active subjects only
        $activePivotAssignments = $pivotAssignments->filter(function($a) {
            $s = $a->subject;
            if (!$s) return false;
            return ($s->status ?? 'active') === 'active';
        });
        $activeLegacySubjects = $legacySubjects->filter(function($s) {
            return ($s->status ?? 'active') === 'active';
        });

        // Merge subject IDs from both sources (only active ones)
        $pivotIds = $activePivotAssignments->pluck('subject_id')->toArray();
        $legacyIds = $activeLegacySubjects->pluck('id')->toArray();
        $subjectIds = array_values(array_unique(array_merge($pivotIds, $legacyIds)));

        // Build subjects list for filter dropdown from both sources
        $subjects = collect();
        foreach ($activePivotAssignments as $assignment) {
            $subject = $assignment->subject;
            if ($subject) {
                $subjects->push([
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => is_numeric($subject->semester ?? null) ? (int) $subject->semester : null,
                ]);
            }
        }
        foreach ($activeLegacySubjects as $legacySubject) {
            if (!$subjects->where('id', $legacySubject->id)->isNotEmpty()) {
                $subjects->push([
                    'id' => $legacySubject->id,
                    'name' => $legacySubject->subject_name,
                    'code' => $legacySubject->subject_code,
                    'semester' => is_numeric($legacySubject->semester ?? null) ? (int) $legacySubject->semester : null,
                ]);
            }
        }
        $subjects = $subjects
            ->unique('id')
            ->sortBy([
                fn ($s) => $s['semester'] ?? 999,
                fn ($s) => $s['name'] ?? '',
            ])
            ->values();

        // Compute available semesters for the teacher's active subjects (pivot + legacy)
        $availableSemesters = $subjects
            ->pluck('semester')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Build base query for students enrolled in the teacher's subjects
        // Note: subject_students.student_id references students.id in this project.
        $query = DB::table('subject_students as ss')
            ->join('subjects as sub', 'ss.subject_id', '=', 'sub.id')
            ->join('students as st', 'ss.student_id', '=', 'st.id')
            ->join('users as u', 'st.user_id', '=', 'u.id')
            ->whereIn('ss.subject_id', $subjectIds)
            ->where('u.role', 'student')
            ->where(function ($q) {
                $q->where('st.status', 'active')
                    ->orWhereNull('st.status');
            })
            ->where(function ($q) {
                $q->where('st.is_alumni', 0)
                    ->orWhereNull('st.is_alumni');
            });

        // Filter by subject if selected
        $selectedSubject = $request->get('subject');
        if ($selectedSubject && in_array($selectedSubject, $subjectIds)) {
            $query->where('ss.subject_id', $selectedSubject);
        }

        // Filter by semester if selected
        $selectedSemester = $request->get('semester');
        if ($selectedSemester && in_array((int) $selectedSemester, $availableSemesters, true)) {
            $query->where('sub.semester', (int) $selectedSemester);
        }

        // Search filter
        $search = $request->get('q');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('u.name', 'like', "%{$search}%")
                    ->orWhere('u.email', 'like', "%{$search}%")
                    ->orWhere('st.registration_number', 'like', "%{$search}%")
                    ->orWhere('st.roll_no', 'like', "%{$search}%");
            });
        }

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
        $students = $query
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
            ->distinct()
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
            ->whereHas('student', function ($q) use ($subjectIds) {
                $q->whereHas('subjects', function ($sq) use ($subjectIds) {
                    $sq->whereIn('subjects.id', $subjectIds);
                });
            })
            ->with('student')
            ->first();

        if (!$student) {
            return redirect()->route('teacher.students')->with('error', 'Student not found in your subjects.');
        }

        // Get student's subjects (only those taught by this teacher - both sources)
        $studentSubjects = DB::table('subject_students')
            ->where('student_id', $id)
            ->whereIn('subject_id', $subjectIds)
            ->join('subjects', 'subject_students.subject_id', '=', 'subjects.id')
            ->select('subjects.id', 'subjects.subject_name', 'subjects.subject_code', 'subjects.semester')
            ->get();

        // Get attendance records for this student in teacher's subjects
        $attendanceRecords = DB::table('attendance')
            ->where('student_id', $id)
            ->whereIn('subject_id', $subjectIds)
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        // Calculate attendance rate per subject
        $attendanceBySubject = [];
        foreach ($subjectIds as $subjectId) {
            $stats = DB::table('attendance')
                ->where('student_id', $id)
                ->where('subject_id', $subjectId)
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
        $teacher = $user->teacher;
        if (!$teacher) {
            return response()->json(['subjects' => []]);
        }

        // Pivot-based assignments
        $pivotAssignments = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get();

        // Legacy subjects
        $legacySubjects = collect();
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $teacher->id)->get();
        }

        $subs = collect();
        foreach ($pivotAssignments as $a) {
            $s = $a->subject;
            if ($s && (int)($s->semester ?? 0) === $semester && ($s->status ?? 'active') === 'active') {
                $subs->push(['id' => $s->id, 'name' => $s->subject_name, 'code' => $s->subject_code]);
            }
        }
        foreach ($legacySubjects as $s) {
            if (!$subs->where('id', $s->id)->isNotEmpty() && (int)($s->semester ?? 0) === $semester && ($s->status ?? 'active') === 'active') {
                $subs->push(['id' => $s->id, 'name' => $s->subject_name, 'code' => $s->subject_code]);
            }
        }

        return response()->json(['subjects' => $subs->values()]);
    }

    /**
     * Export students to CSV.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.students')->with('error', 'Teacher profile not found.');
        }

        // Get teacher's subject assignments
        $subjectAssignments = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get();

        $subjectIds = $subjectAssignments->pluck('subject_id')->toArray();

        // Build query for students
        $query = DB::table('subject_students')
            ->join('users', 'subject_students.student_id', '=', 'users.id')
            ->leftJoin('students', 'users.id', '=', 'students.user_id')
            ->leftJoin('subjects', 'subject_students.subject_id', '=', 'subjects.id')
            ->whereIn('subject_students.subject_id', $subjectIds)
            ->select(
                'users.name',
                'users.email',
                'students.registration_number',
                'students.phone',
                'students.gender',
                'subjects.subject_name',
                'subjects.subject_code',
                'subjects.semester'
            );

        // Apply filters
        $selectedSubject = $request->get('subject');
        if ($selectedSubject && in_array($selectedSubject, $subjectIds)) {
            $query->where('subject_students.subject_id', $selectedSubject);
        }

        $selectedSemester = $request->get('semester');
        if ($selectedSemester) {
            $query->where('subjects.semester', $selectedSemester);
        }

        $search = $request->get('q');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('students.registration_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('users.name', 'asc')->get()->unique('name');

        // Create CSV
        $filename = 'students-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($students) {
            $handle = fopen('php://output', 'w');
            
            // Header row
            fputcsv($handle, ['Name', 'Email', 'Registration No', 'Phone', 'Gender', 'Subject', 'Subject Code', 'Semester']);
            
            // Data rows
            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->name,
                    $student->email,
                    $student->registration_number ?? 'N/A',
                    $student->phone ?? 'N/A',
                    $student->gender ?? 'N/A',
                    $student->subject_name ?? 'N/A',
                    $student->subject_code ?? 'N/A',
                    $student->semester ?? 'N/A',
                ]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
