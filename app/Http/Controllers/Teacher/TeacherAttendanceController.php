<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\Attendance;
use App\Models\Student;
use App\Helpers\NepaliContentHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use App\Models\Department;
use App\Traits\LogsActivity;

class TeacherAttendanceController extends Controller
{
    use LogsActivity;
    
    /**
     * Get teacher's assigned subject IDs
     */
    private function getTeacherSubjectIds()
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return [];
        }

        // IDs from pivot table (subject_teacher)
        $pivotIds = SubjectTeacher::where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->toArray();

        // Also support legacy flowers when teacher.assignment might have been saved as user id
        $fallbackByUserId = SubjectTeacher::where('teacher_id', $user->id)
            ->pluck('subject_id')
            ->toArray();

        // Legacy subject field `subjects.teacher_id`
        $legacyIds = [];
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacyIds = \App\Models\Subject::where('teacher_id', $teacher->id)
                ->pluck('id')
                ->toArray();
        }

        $ids = array_values(array_unique(array_merge($pivotIds, $fallbackByUserId, $legacyIds)));

        return $ids;
    }

    /**
     * Load teacher's subject IDs from both pivot (SubjectTeacher) and legacy (subjects.teacher_id).
     * This ensures attendance data is visible even if legacy assignments are used.
     */
    private function loadTeacherSubjectIds()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        if (!$teacher) {
            return [];
        }

        // Pivot table IDs
        $pivotIds = SubjectTeacher::where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->toArray();

        // Fallback for records where teacher_id was stored as user ID
        $fallbackByUserId = SubjectTeacher::where('teacher_id', $user->id)
            ->pluck('subject_id')
            ->toArray();

        // Attempt legacy assignments; ignore if not present to avoid breaking
        $legacyIds = [];
        try {
            $legacyIds = \App\Models\Subject::where('teacher_id', $teacher->id)
                ->pluck('id')
                ->toArray();
        } catch (\Exception $e) {
            $legacyIds = [];
        }

        return array_values(array_unique(array_merge($pivotIds, $fallbackByUserId, $legacyIds)));
    }

    /**
     * Display attendance for teacher's subjects
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => 'TCH-' . $user->id,
                'status' => 'active',
            ]);
        }

        if (!$teacher) {
            return view('teacher.attendance', [
                'attendance' => collect([]),
                'subjectAttendance' => collect([]),
                'subjects' => collect([]),
                'selectedSubject' => null,
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0],
                'semesters' => [1, 2, 3, 4, 5, 6],
                'courses' => collect([]),
                'availableSemesters' => [],
            ]);
        }

        $subjectIds = $this->loadTeacherSubjectIds();

        $pivotAssignments = SubjectTeacher::whereIn('teacher_id', [$teacher->id, $user->id])
            ->with('subject')
            ->get();

        $subjects = $pivotAssignments
            ->map(function ($st) {
                return [
                    'id' => $st->subject->id,
                    'name' => $st->subject->subject_name,
                    'code' => $st->subject->subject_code,
                ];
            });

        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = \App\Models\Subject::where('teacher_id', $teacher->id)->get();
            foreach ($legacySubjects as $sub) {
                if (!$subjects->contains('id', $sub->id)) {
                    $subjects->push([
                        'id' => $sub->id,
                        'name' => $sub->subject_name,
                        'code' => $sub->subject_code,
                    ]);
                }
            }
        }

        // Compute available semesters from active sources (pivot + legacy)
        $availableSemesters = [];
        foreach ($pivotAssignments as $pa) {
            $s = $pa->subject ?? null;
            if ($s && isset($s->semester) && $s->semester != '' && ($s->status ?? 'active') == 'active') {
                $availableSemesters[] = (int)$s->semester;
            }
        }
        if (isset($legacySubjects)) {
            foreach ($legacySubjects as $ls) {
                if (isset($ls->semester) && $ls->semester != '' && ($ls->status ?? 'active') == 'active') {
                    $availableSemesters[] = (int)$ls->semester;
                }
            }
        }
        $availableSemesters = array_values(array_unique($availableSemesters));


        $date = $request->get('date', '');
        $date_bs = $request->get('date_bs', '');
        $search = $request->get('q', '');
        $subject = $request->get('subject', '');
        $semester = $request->get('semester', '');
        $perPage = intval($request->get('per_page', 25)) ?: 25;

        if (empty($subjectIds)) {
            return view('teacher.attendance', [
                'attendance' => collect([]),
                'subjectAttendance' => collect([]),
                'subjects' => $subjects,
                'selectedSubject' => null,
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0],
                'semesters' => [1, 2, 3, 4, 5, 6],
                'courses' => collect([]),
                'availableSemesters' => $availableSemesters,
            ]);
        }

        try {
            // Debug: Log the request parameters
            Log::info('Teacher attendance index called with params:', [
                'date' => $date,
                'date_bs' => $date_bs,
                'search' => $search,
                'semester' => $semester,
                'subject' => $subject
            ]);

            // Check if attendance table has any data
            $attendanceCount = DB::table('attendance')->count();
            Log::info('Total attendance records in DB: ' . $attendanceCount);

            // Check if students exist
            $studentCount = DB::table('students')->count();
            Log::info('Total students in DB: ' . $studentCount);

            // Check if users with student role exist
            $studentUserCount = DB::table('users')->where('role', 'student')->count();
            Log::info('Total student users in DB: ' . $studentUserCount);

            // Get statistics for all records (not just filtered) - for display purposes
            // This must be defined BEFORE we use it in the stats calculation below
            $allStats = DB::table('attendance')
                ->whereIn('subject_id', $subjectIds)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present"),
                    DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent"),
                    DB::raw("SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_count")
                )
                ->first();

            // Build query to get attendance records from database
            // Use leftJoin but with OR condition to allow getting records even if user not found
            $attendanceQuery = DB::table('attendance')
                ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
                ->leftJoin('users', function ($join) {
                    $join->on('students.user_id', '=', 'users.id');
                })
                ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
                ->whereIn('attendance.subject_id', $subjectIds)
                ->select(
                    'attendance.id',
                    'attendance.student_id',
                    'attendance.subject_id',
                    'attendance.status',
                    'attendance.remarks',
                    'attendance.date',
                    'attendance.date_bs',
                    DB::raw('COALESCE(users.name, "Unknown") as name'),
                    DB::raw('COALESCE(users.email, "") as email'),
                    DB::raw('COALESCE(students.roll_no, "") as roll_no'),
                    'students.semester',
                    'subjects.subject_name',
                    'subjects.subject_code',
                    'subjects.category',
                    'users.role'
                );

            // Filter by AD date if provided
            if ($date !== '' && $date !== null) {
                $attendanceQuery->where('attendance.date', '=', $date);
            }

            // Filter by BS date if provided
            if ($date_bs !== '' && $date_bs !== null) {
                $attendanceQuery->where('attendance.date_bs', '=', $date_bs);
            }

            // Filter by semester if selected - only apply if semester value is a valid number
            if ($semester !== '' && $semester !== null && is_numeric($semester)) {
                $attendanceQuery->where('students.semester', $semester);
            }

            // Filter by course/subject if selected
            if ($subject !== '' && $subject !== null) {
                $attendanceQuery->where('attendance.subject_id', $subject);
            }

            // Apply search filter on student name or email
            if (!empty($search)) {
                $attendanceQuery->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('students.roll_no', 'like', "%{$search}%");
                });
            }

            // Get attendance records with student info (paginated)
            $attendance = $attendanceQuery
                ->orderBy('attendance.date', 'desc')
                ->orderBy('users.name')
                ->paginate($perPage)
                ->withQueryString();

            // Calculate stats BEFORE transforming - using the original paginator items
            $stats = [
                'total' => $attendance->total(),
                'present' => 0,
                'absent' => 0,
                'leave' => 0,
            ];

            // If no filters applied, use all stats
            if (empty($date) && empty($date_bs) && empty($search) && empty($semester) && empty($subject)) {
                $stats = [
                    'total' => $allStats->total ?? 0,
                    'present' => $allStats->present ?? 0,
                    'absent' => $allStats->absent ?? 0,
                    'leave' => $allStats->leave_count ?? 0,
                ];
            }
            else {
                // Count stats from the current page items (original stdClass objects before transform)
                foreach ($attendance as $record) {
                    $status = $record->status ?? '';
                    if ($status === 'present') {
                        $stats['present']++;
                    }
                    elseif ($status === 'absent') {
                        $stats['absent']++;
                    }
                    elseif ($status === 'leave') {
                        $stats['leave']++;
                    }
                }
            }

            // Transform each record in the paginator's collection AFTER stats calculation
            $attendance->getCollection()->transform(function ($record) {
                return [
                    'id' => (string)($record->id ?? ''),
                    'student_id' => (string)($record->student_id ?? ''),
                    'subject_id' => (string)($record->subject_id ?? ''),
                    'status' => (string)($record->status ?? ''),
                    'remarks' => (string)($record->remarks ?? ''),
                    'date' => (string)($record->date ?? ''),
                    'date_bs' => (string)($record->date_bs ?? ''),
                    'name' => (string)($record->name ?? ''),
                    'email' => (string)($record->email ?? ''),
                    'roll_no' => (string)($record->roll_no ?? ''),
                    'semester' => (string)($record->semester ?? ''),
                    'subject_name' => (string)($record->subject_name ?? ''),
                    'subject_code' => (string)($record->subject_code ?? ''),
                    'category' => (string)($record->category ?? ''),
                ];
            });

            // Get all attendance grouped by subject for the main table (with pagination)
            $subjectAttendanceQuery = DB::table('attendance')
                ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
                ->whereIn('attendance.subject_id', $subjectIds)
                ->select(
                    'attendance.subject_id',
                    'attendance.date',
                    'attendance.date_bs',
                    'subjects.subject_name',
                    'subjects.subject_code',
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) as present"),
                    DB::raw("SUM(CASE WHEN attendance.status = 'absent' THEN 1 ELSE 0 END) as absent"),
                    DB::raw("SUM(CASE WHEN attendance.status = 'leave' THEN 1 ELSE 0 END) as leave_count")
                );

            // Filter by course/subject if selected
            if ($subject !== '' && $subject !== null) {
                $subjectAttendanceQuery->where('attendance.subject_id', $subject);
            }

            // Filter by date if provided
            if ($date !== '' && $date !== null) {
                $subjectAttendanceQuery->where('attendance.date', '=', $date);
            }

            if ($date_bs !== '' && $date_bs !== null) {
                $subjectAttendanceQuery->where('attendance.date_bs', '=', $date_bs);
            }

            $subjectAttendanceQuery->groupBy('attendance.subject_id', 'attendance.date', 'attendance.date_bs', 'subjects.subject_name', 'subjects.subject_code')
                ->orderBy('attendance.date', 'desc')
                ->orderBy('subjects.subject_name', 'asc');

            // Paginate subject attendance
            $subjectAttendance = $subjectAttendanceQuery->paginate($perPage)->withQueryString();

            // Transform the collection
            $subjectAttendance->getCollection()->transform(function ($item) {
                return [
                    'subject_id' => $item->subject_id,
                    'subject_name' => $item->subject_name ?? 'General',
                    'subject_code' => $item->subject_code ?? '',
                    'date' => $item->date,
                    'date_bs' => $item->date_bs,
                    'days_count' => 1,
                    'total' => (int)$item->total,
                    'present' => (int)$item->present,
                    'absent' => (int)$item->absent,
                    'leave_count' => (int)$item->leave_count,
                ];
            });

            // Get available semesters for the filter dropdown
            $semesters = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->distinct()
                ->pluck('students.semester')
                ->filter(function ($sem) {
                    return !is_null($sem) && $sem !== '';
                })
                ->map(function ($sem) {
                    // Ensure semester is a string/integer
                    return (int)$sem;
                })
                ->unique()
                ->sort()
                ->values()
                ->all();

            // Get all courses/subjects for filter dropdown (including archived)
            $courses = DB::table('subjects')
                ->whereIn('id', $subjectIds)
                ->orderBy('subject_name')
                ->select('id', 'subject_code', 'subject_name', 'semester')
                ->get();

            // Type hint for static analysis
            /** @var \Illuminate\Support\Collection $attendanceRecords */
            Log::info('Teacher attendance query result count: ' . $attendance->total());

            return view('teacher.attendance', [
                'attendance' => $attendance,
                'date' => $date,
                'date_bs' => $date_bs,
                'search' => $search,
                'semester' => $semester,
                'subject' => $subject,
                'stats' => $stats,
                'semesters' => $semesters,
                'courses' => $courses,
                'subjectAttendance' => $subjectAttendance,
                'subjects' => $subjects,
                'availableSemesters' => $availableSemesters,
            ]);

        }
        catch (\Exception $e) {
            Log::error('Teacher attendance error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

            // Return view with default empty data - use empty Paginator for attendance
            $emptyPaginator = new LengthAwarePaginator(
                collect([]), // items
                0, // total
                25, // perPage
                1, // currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath()] // options
            );

            return view('teacher.attendance', [
                'attendance' => $emptyPaginator,
                'date' => '',
                'date_bs' => '',
                'search' => '',
                'semester' => '',
                'subject' => '',
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0],
                'semesters' => [1, 2, 3, 4, 5, 6],
                'courses' => collect([]),
                'subjectAttendance' => collect([]),
            ]);
        }
    }

    /**
     * Get students for a specific subject to mark attendance
     */
    public function getStudentsForAttendance(Request $request)
    {
        $subjectId = (int) $request->input('subject_id');
        $date = $request->input('date', '');

        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        $subjectIds = $this->loadTeacherSubjectIds();
        if (!in_array($subjectId, $subjectIds)) {
            // Fallback: if subject is not in teacher's list, try to fetch enrolled students for that subject anyway
            // but proceed with an empty assigned semesters constraint to avoid blocking UI.
            $subjectIds = $subjectIds; // keep existing behavior, but allow fetch fallback below
        }

        // Only include students from semesters the teacher is responsible for (for this subject)
        $assignedSemesters = SubjectTeacher::where('subject_id', $subjectId)
            ->where('teacher_id', $teacher->id)
            ->whereNotNull('semester')
            ->pluck('semester')
            ->filter()
            ->unique()
            ->toArray();
        // Fetch students enrolled in the subject (or all if legacy data exists)
        // Ensure we only fetch students actually enrolled in the subject
        // subject_students.student_id references students.id, and students.user_id points to users.id
        $students = DB::table('subject_students')
            ->join('students', 'subject_students.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->where('subject_students.subject_id', $subjectId)
            ->where('users.role', 'student')
            ->where('students.is_active', 1)
            ->where('students.is_alumni', 0)
            ->when(!empty($assignedSemesters), function ($query) use ($assignedSemesters) {
                $query->whereIn('students.semester', $assignedSemesters);
            })
            ->select(
                'students.id as student_id',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester'
            )
            ->orderBy('users.name')
            ->get();

        $existingAttendance = collect([]);
        if (!empty($date)) {
            $existingAttendance = DB::table('attendance')
                ->where('date', $date)
                ->where('subject_id', $subjectId)
                ->pluck('status', 'student_id');
        }

        $students = $students->map(function ($student) use ($existingAttendance) {
            $student->status = $existingAttendance->get($student->student_id, 'present');
            return $student;
        });

        return response()->json([
            'students' => $students,
            'total' => $students->count(),
            'present' => $students->where('status', 'present')->count(),
            'absent' => $students->where('status', 'absent')->count(),
        ]);
    }

    /**
     * Bulk add attendance for all subjects assigned to the current teacher for today.
     * Creates present records for all enrolled students in each subject.
     */
    public function bulkAddForAllSubjects(Request $request)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            if (!$teacher) {
                return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 404);
            }

            $subjectIds = $this->loadTeacherSubjectIds();
            if (empty($subjectIds)) {
                return response()->json(['success' => true, 'inserted' => 0]);
            }

            $today = now()->toDateString();
            $todayBs = NepaliContentHelper::convertAdToBs($today);
            $records = [];

            foreach ($subjectIds as $sid) {
                // enrolled students for this subject
                $enrolled = DB::table('subject_students')->where('subject_id', $sid)->pluck('student_id')->toArray();
                foreach ($enrolled as $stid) {
                    // skip if an attendance record already exists for this date and subject
                    $exists = DB::table('attendance')->where('date', $today)->where('subject_id', $sid)->where('student_id', $stid)->exists();
                    if ($exists) continue;
                    // fetch student to determine status later if needed; default Present
                    $records[] = [
                        'student_id' => $stid,
                        'subject_id' => $sid,
                        'teacher_id' => $teacher->id,
                        'date' => $today,
                        'date_bs' => $todayBs,
                        'status' => 'present',
                        'remarks' => 'Present',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (empty($records)) {
                return response()->json(['success' => true, 'inserted' => 0]);
            }

            DB::transaction(function() use ($records) {
                foreach ($records as $r) {
                    DB::table('attendance')->insert($r);
                }
            });

            return response()->json(['success' => true, 'inserted' => count($records)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Bulk add failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Print attendance by subject (for a given date).
     */
    public function printAttendance(Request $request)
    {
        $subjectId = (int) $request->query('subject_id') ?: (int) $request->query('subject', 0);
        $date = $request->query('date', now()->toDateString());

        $user = auth()->user();
        $teacher = $user->teacher;
        if (!$teacher) {
            abort(403, 'Teacher not found');
        }

        // Build subject info (name/code) from pivot/legacy sources
        $subjectIds = $this->loadTeacherSubjectIds();
        if (!in_array($subjectId, $subjectIds)) {
            // fallback: if subject not assigned, try to fetch by id to display anyway
            $sub = \App\Models\Subject::find($subjectId);
            if (!$sub) {
                abort(404, 'Subject not found');
            }
        }
        // Fetch enrolled students for this subject
        $students = DB::table('subject_students')
            ->join('students', 'subject_students.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->where('subject_students.subject_id', $subjectId)
            ->where('users.role', 'student')
            ->where('students.is_active', 1)
            ->where('students.is_alumni', 0)
            ->select(
                'students.id as student_id',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester'
            )
            ->orderBy('users.name')
            ->get();

        // Attach status if attendance exists for given date
        $existingAttendance = collect([]);
        if (!empty($date)) {
            $existingAttendance = DB::table('attendance')
                ->where('date', $date)
                ->where('subject_id', $subjectId)
                ->pluck('status', 'student_id');
        }
        $students = $students->map(function($s) use ($existingAttendance) {
            $s->status = $existingAttendance->get($s->student_id, 'present');
            return $s;
        });

        $subject = \App\Models\Subject::find($subjectId);
        $subjectName = $subject->subject_name ?? 'Subject';
        $subjectCode = $subject->subject_code ?? '';

        // Department details for print header with safe fallbacks
        $collegeName = 'Department';
        $collegeAddress = '';
        $collegeLogo = asset('images/default-logo.png');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('departments')) {
                $college = Department::orderBy('id', 'desc')->first();
                if ($college) {
                    $collegeName = $college->name ?? $collegeName;
                    $collegeAddress = $college->address ?? $collegeAddress;
                    if (method_exists($college, 'getLogoUrl')) {
                        $logo = $college->getLogoUrl();
                        if ($logo) $collegeLogo = $logo;
                    }
                }
            }
        } catch (\Throwable $e) {
            // keep defaults on error
        }

        // If caller asked for the new tab preview, render a dedicated print blade for consistent styling
        if ($request->query('newTab') === '1') {
            return view('teacher.print.attendance_by_subject_print', [
                'subject_id' => $subjectId,
                'subject_name' => $subjectName,
                'subject_code' => $subjectCode,
                'date' => $date,
                'students' => $students,
                'collegeName' => $collegeName,
                'collegeAddress' => $collegeAddress,
                'collegeLogo' => $collegeLogo,
                // branding details used in the header
                'subject_semester' => $subject ? $subject->semester ?? '' : '',
                'date_bs' => isset($date) ? \App\Helpers\NepaliContentHelper::convertAdToBs($date) : '',
                'marked_by' => $user->name ?? $user->email ?? 'Unknown',
            ]);
        }

        // Additional details for header
        $dateBsHeader = $date ? \App\Helpers\NepaliContentHelper::convertAdToBs($date) : '';
        $markedBy = $user->name ?? $user->email ?? 'Unknown';
        $subjectSemester = $subject->semester ?? (isset($subject) ? ($subject->semester ?? '') : '');

        return view('teacher.print.attendance_by_subject', [
            'subject_id' => $subjectId,
            'subject_name' => $subjectName,
            'subject_code' => $subjectCode,
            'date' => $date,
            'date_bs' => $dateBsHeader,
            'marked_by' => $markedBy,
            'subject_semester' => $subjectSemester,
            'students' => $students,
            'collegeName' => $collegeName,
            'collegeAddress' => $collegeAddress,
            'collegeLogo' => $collegeLogo,
        ]);
    }

    /**
     * Mark attendance for students
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'date' => 'required|date',
                'attendance' => 'required|array',
                'attendance.*.student_id' => 'required|exists:students,id',
                'attendance.*.status' => 'required|in:present,absent,leave',
            ]);

            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                return response()->json(['error' => 'Teacher profile not found'], 404);
            }

            $subjectIds = $this->loadTeacherSubjectIds();
            if (!in_array($data['subject_id'], $subjectIds)) {
                return response()->json(['error' => 'Subject not assigned to you'], 403);
            }

            $date = $data['date'];
            $dateBs = NepaliContentHelper::convertAdToBs($date);
            $now = now()->toDateTimeString();

            // Ensure we only save attendance for students actually enrolled in this subject
            $enrolledStudentIds = DB::table('subject_students')
                ->where('subject_id', $data['subject_id'])
                ->pluck('student_id')
                ->toArray();

            $records = [];
            foreach ($data['attendance'] as $item) {
                if (!in_array($item['student_id'], $enrolledStudentIds)) {
                    // Skip any student not enrolled in this subject
                    continue;
                }

                $remarks = $item['status'] === 'absent' ? 'Absent' : ($item['status'] === 'leave' ? 'Leave' : 'Present');
                $records[] = [
                    'student_id' => $item['student_id'],
                    'subject_id' => $data['subject_id'],
                    'teacher_id' => $teacher->id,
                    'date' => $date,
                    'date_bs' => $dateBs,
                    'status' => $item['status'],
                    'remarks' => $remarks,
                    'updated_at' => $now,
                    'created_at' => $now,
                ];
            }

            if (empty($records)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found for the selected subject.',
                ], 422);
            }

            DB::transaction(function () use ($records, $data, $date) {
                DB::table('attendance')
                    ->where('date', $date)
                    ->where('subject_id', $data['subject_id'])
                    ->delete();
                
                DB::table('attendance')->insert($records);
            });

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully!',
                'saved' => count($records),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Teacher attendance store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
