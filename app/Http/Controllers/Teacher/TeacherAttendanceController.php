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
use App\Support\TeacherSubjectRoster;
use App\Traits\LogsActivity;

class TeacherAttendanceController extends Controller
{
    use LogsActivity;

    private const ATTENDANCE_TYPE_CLASS = 'class';
    private const ATTENDANCE_TYPE_LAB = 'lab';
    
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
     * Resolve the semester(s) this teacher is assigned for a subject.
     */
    private function getAssignedSemestersForSubject(int $subjectId, $teacher, $user): array
    {
        $teacherIds = array_values(array_filter([
            $teacher?->id,
            $user?->id,
        ], fn ($id) => !is_null($id) && $id !== ''));

        $assignedSemesters = SubjectTeacher::where('subject_id', $subjectId)
            ->when(!empty($teacherIds), function ($query) use ($teacherIds) {
                $query->whereIn('teacher_id', $teacherIds);
            })
            ->whereNotNull('semester')
            ->pluck('semester')
            ->filter(fn ($semester) => $semester !== null && $semester !== '')
            ->map(fn ($semester) => (string) $semester)
            ->unique()
            ->values()
            ->all();

        if (!empty($assignedSemesters)) {
            return $assignedSemesters;
        }

        $subjectSemester = DB::table('subjects')
            ->where('id', $subjectId)
            ->value('semester');

        return $subjectSemester !== null && $subjectSemester !== ''
            ? [(string) $subjectSemester]
            : [];
    }

    /**
     * Build the subject roster limited to the teacher's assigned semester for that subject.
     */
    private function getRosterStudentsForSubject(int $subjectId, array $assignedSemesters = [])
    {
        $students = TeacherSubjectRoster::studentRowsForSubject($subjectId);

        if (!empty($assignedSemesters)) {
            $allowedSemesters = array_map('strval', $assignedSemesters);
            $students = $students
                ->filter(function ($student) use ($allowedSemesters) {
                    return in_array((string) ($student->semester ?? ''), $allowedSemesters, true);
                })
                ->values();
        }

        return $students->map(function ($student) {
            return (object) [
                'student_id' => (int) $student->student_id,
                'name' => (string) ($student->name ?? ''),
                'email' => (string) ($student->email ?? ''),
                'roll_no' => (string) ($student->roll_no ?? ''),
                'semester' => (string) ($student->semester ?? ''),
            ];
        })->values();
    }

    /**
     * Display attendance for teacher's subjects
     */
    public function index(Request $request)
    {
        return $this->indexByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    /**
     * Display lab attendance for teacher's lab-enabled subjects
     */
    public function labIndex(Request $request)
    {
        return $this->indexByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    private function indexByType(Request $request, string $attendanceType)
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
                'attendanceType' => $attendanceType,
                'attendanceLabel' => $this->getAttendanceLabel($attendanceType),
                'attendanceRoutes' => $this->getAttendanceRoutes($attendanceType),
            ]);
        }

        $subjectsData = $this->getSubjectsForAttendanceType($attendanceType, $teacher, $user);
        $subjectIds = $subjectsData['ids'];
        $subjects = $subjectsData['subjects'];
        $availableSemesters = $subjectsData['availableSemesters'];


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
                'attendanceType' => $attendanceType,
                'attendanceLabel' => $this->getAttendanceLabel($attendanceType),
                'attendanceRoutes' => $this->getAttendanceRoutes($attendanceType),
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
            $attendanceCount = DB::table('attendance')
                ->where('attendance_type', $attendanceType)
                ->count();
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
                ->where('attendance_type', $attendanceType)
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
                ->where('attendance.attendance_type', $attendanceType)
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
                ->where('attendance.attendance_type', $attendanceType)
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
                'attendanceType' => $attendanceType,
                'attendanceLabel' => $this->getAttendanceLabel($attendanceType),
                'attendanceRoutes' => $this->getAttendanceRoutes($attendanceType),
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
                'attendanceType' => $attendanceType,
                'attendanceLabel' => $this->getAttendanceLabel($attendanceType),
                'attendanceRoutes' => $this->getAttendanceRoutes($attendanceType),
            ]);
        }
    }

    /**
     * Get students for a specific subject to mark attendance
     */
    public function getStudentsForAttendance(Request $request)
    {
        return $this->getStudentsForAttendanceByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    public function getStudentsForLabAttendance(Request $request)
    {
        return $this->getStudentsForAttendanceByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    /**
     * Get EXISTING attendance records for view/edit modals.
     * Unlike the mark-attendance endpoint, this queries attendance directly
     * (not the roster) so it always returns saved records.
     */
    public function getSubjectAttendanceForView(Request $request)
    {
        return $this->getSubjectAttendanceForViewByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    public function getSubjectLabAttendanceForView(Request $request)
    {
        return $this->getSubjectAttendanceForViewByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    private function getSubjectAttendanceForViewByType(Request $request, string $attendanceType)
    {
        $date     = $request->input('date', '');
        $subjectId = $request->input('subject_id');

        if (empty($date)) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Query directly from attendance — no roster filter, no subject-assignment check.
        // This ensures view/edit modals always show the actual saved records.
        $query = DB::table('attendance')
            ->where('attendance.date', $date)
            ->where('attendance.attendance_type', $attendanceType);

        if ($subjectId && $subjectId !== 'null') {
            $query->where('attendance.subject_id', (int) $subjectId);
        } else {
            $query->whereNull('attendance.subject_id');
        }

        $records = $query
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->where('users.role', 'student')
            ->select(
                'students.id as student_id',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester',
                'attendance.status',
                'attendance.remarks',
                'attendance.date',
                'attendance.date_bs',
                'subjects.subject_name',
                'subjects.subject_code'
            )
            ->orderBy('users.name')
            ->get();

        $students = $records->map(function ($r) {
            return [
                'student_id'   => (string) $r->student_id,
                'name'         => $r->name,
                'email'        => $r->email,
                'roll_no'      => $r->roll_no,
                'semester'     => $r->semester,
                'status'       => $r->status ?? 'present',
                'remarks'      => $r->remarks ?? '',
                'date'         => $r->date,
                'date_bs'      => $r->date_bs,
                'subject_name' => $r->subject_name,
                'subject_code' => $r->subject_code,
            ];
        });

        return response()->json([
            'students' => $students,
            'total'    => $students->count(),
            'present'  => $students->where('status', 'present')->count(),
            'absent'   => $students->where('status', 'absent')->count(),
            'leave'    => $students->where('status', 'leave')->count(),
        ]);
    }

    private function getStudentsForAttendanceByType(Request $request, string $attendanceType)
    {
        $subjectId = (int) $request->input('subject_id');
        $date = $request->input('date', '');
        $markedOnly = $request->boolean('marked_only');

        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        $subjectIds = array_map('intval', $this->getSubjectIdsForAttendanceType($attendanceType, $teacher, $user));
        if ($subjectId <= 0) {
            return response()->json(['error' => 'Invalid subject selected'], 422);
        }

        if (!in_array($subjectId, $subjectIds, true)) {
            return response()->json(['error' => 'Subject not assigned to you'], 403);
        }

        $assignedSemesters = $this->getAssignedSemestersForSubject($subjectId, $teacher, $user);
        $students = $this->getRosterStudentsForSubject($subjectId, $assignedSemesters);

        $existingAttendance = collect([]);
        if (!empty($date)) {
            $existingAttendance = DB::table('attendance')
                ->where('date', $date)
                ->where('subject_id', $subjectId)
                ->where('attendance_type', $attendanceType)
                ->select('student_id', 'status', 'remarks')
                ->get()
                ->keyBy('student_id');
        }

        if ($markedOnly) {
            $students = $students
                ->filter(function ($student) use ($existingAttendance) {
                    return $existingAttendance->has($student->student_id);
                })
                ->values();
        }

        $students = $students->map(function ($student) use ($existingAttendance) {
            $attendance = $existingAttendance->get($student->student_id);
            $student->status = $attendance ? $attendance->status : 'present';
            $student->remarks = $attendance ? $attendance->remarks : null;
            return $student;
        })->values();

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
        return $this->bulkAddForAllSubjectsByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    public function bulkAddForAllLabSubjects(Request $request)
    {
        return $this->bulkAddForAllSubjectsByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    private function bulkAddForAllSubjectsByType(Request $request, string $attendanceType)
    {
        try {
            $user = auth()->user();
            $teacher = $user->teacher;
            if (!$teacher) {
                return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 404);
            }

            $subjectIds = $this->getSubjectIdsForAttendanceType($attendanceType, $teacher, $user);
            if (empty($subjectIds)) {
                return response()->json(['success' => true, 'inserted' => 0]);
            }

            $today = now()->toDateString();
            $todayBs = NepaliContentHelper::convertAdToBs($today);
            $records = [];

            foreach ($subjectIds as $sid) {
                $assignedSemesters = $this->getAssignedSemestersForSubject((int) $sid, $teacher, $user);
                $enrolled = $this->getRosterStudentsForSubject((int) $sid, $assignedSemesters)
                    ->pluck('student_id')
                    ->map(fn ($studentId) => (int) $studentId)
                    ->all();
                foreach ($enrolled as $stid) {
                    // skip if an attendance record already exists for this date and subject
                    $exists = DB::table('attendance')
                        ->where('date', $today)
                        ->where('subject_id', $sid)
                        ->where('student_id', $stid)
                        ->where('attendance_type', $attendanceType)
                        ->exists();
                    if ($exists) continue;
                    // fetch student to determine status later if needed; default Present
                    $records[] = [
                        'student_id' => $stid,
                        'subject_id' => $sid,
                        'teacher_id' => $teacher->id,
                        'attendance_type' => $attendanceType,
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
        return $this->printAttendanceByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    public function printLabAttendance(Request $request)
    {
        return $this->printAttendanceByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    private function printAttendanceByType(Request $request, string $attendanceType)
    {
        $subjectId = (int) $request->query('subject_id') ?: (int) $request->query('subject', 0);
        $date = $request->query('date', now()->toDateString());

        $user = auth()->user();
        $teacher = $user->teacher;
        if (!$teacher) {
            abort(403, 'Teacher not found');
        }

        // Build subject info (name/code) from pivot/legacy sources
        $subjectIds = $this->getSubjectIdsForAttendanceType($attendanceType, $teacher, $user);
        if (!in_array($subjectId, $subjectIds)) {
            // fallback: if subject not assigned, try to fetch by id to display anyway
            $sub = \App\Models\Subject::find($subjectId);
            if (!$sub) {
                abort(404, 'Subject not found');
            }
        }

        // Fetch students directly from the attendance table
        $query = DB::table('attendance')
            ->where('attendance.date', $date)
            ->where('attendance.attendance_type', $attendanceType);
            
        if ($subjectId > 0) {
            $query->where('attendance.subject_id', $subjectId);
        } else {
            $query->whereNull('attendance.subject_id');
        }

        $records = $query
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->where('users.role', 'student')
            ->select(
                'students.id as student_id',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester',
                'attendance.status',
                'attendance.remarks'
            )
            ->orderBy('users.name')
            ->get();

        $students = $records->map(function ($s) {
            return (object) [
                'student_id' => $s->student_id,
                'name' => $s->name,
                'email' => $s->email,
                'roll_no' => $s->roll_no,
                'semester' => $s->semester,
                'status' => $s->status ?? 'present',
                'remarks' => $s->remarks
            ];
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
            'attendanceType' => $attendanceType,
        ]);
    }

    /**
     * Export detailed attendance records as CSV
     */
    public function exportAttendance(Request $request)
    {
        return $this->exportAttendanceByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    public function exportLabAttendance(Request $request)
    {
        return $this->exportAttendanceByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    private function exportAttendanceByType(Request $request, string $attendanceType)
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            abort(403, 'Teacher not found');
        }

        $subjectId = $request->query('subject');
        $dateBs = $request->query('date_bs');
        $search = $request->query('q');
        
        // Get all subjects assigned to this teacher for this attendance type
        $assignedSubjectIds = $this->getSubjectIdsForAttendanceType($attendanceType, $teacher, $user);

        if (empty($assignedSubjectIds)) {
            // Nothing to export
            return response()->streamDownload(function () {
                echo "Date,Student ID,Name,Email,Course,Course Code,Status,Remarks\n";
            }, "teacher-attendance-export.csv");
        }

        $query = DB::table('attendance')
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->where('attendance.attendance_type', $attendanceType)
            ->where('users.role', 'student')
            ->whereIn('attendance.subject_id', $assignedSubjectIds);

        if (!empty($dateBs)) {
            $query->where('attendance.date_bs', $dateBs);
        }

        if (!empty($subjectId)) {
            $query->where('attendance.subject_id', $subjectId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('students.roll_no', 'like', "%{$search}%");
            });
        }

        $records = $query
            ->select('attendance.*', 'users.name', 'users.email', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date_bs', 'desc')
            ->get();

        $csv = "Date,Student ID,Name,Email,Course,Course Code,Status,Remarks\n";

        foreach ($records as $record) {
            $courseName = $record->subject_name ?? 'General';
            $courseCode = $record->subject_code ?? '-';
            // Output row (escaping names just in case they contain commas)
            $safeName = str_replace(',', ' ', $record->name);
            $csv .= "{$record->date_bs},{$record->student_id},{$safeName},{$record->email},{$courseName},{$courseCode},{$record->status},{$record->remarks}\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, "teacher-attendance-export.csv");
    }

    /**
     * Mark attendance for students
     */
    public function store(Request $request)
    {
        return $this->storeByType($request, self::ATTENDANCE_TYPE_CLASS);
    }

    public function storeLab(Request $request)
    {
        return $this->storeByType($request, self::ATTENDANCE_TYPE_LAB);
    }

    private function storeByType(Request $request, string $attendanceType)
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

            $subjectIds = array_map('intval', $this->getSubjectIdsForAttendanceType($attendanceType, $teacher, $user));
            if (!in_array((int) $data['subject_id'], $subjectIds, true)) {
                return response()->json(['error' => 'Subject not assigned to you'], 403);
            }

            $date = $data['date'];
            $dateBs = NepaliContentHelper::convertAdToBs($date);
            $now = now()->toDateTimeString();

            // Ensure we only save attendance for students actually enrolled in this subject
            $assignedSemesters = $this->getAssignedSemestersForSubject((int) $data['subject_id'], $teacher, $user);
            $enrolledStudentIds = $this->getRosterStudentsForSubject((int) $data['subject_id'], $assignedSemesters)
                ->pluck('student_id')
                ->map(fn ($studentId) => (int) $studentId)
                ->all();

            $records = [];
            foreach ($data['attendance'] as $item) {
                $studentId = (int) $item['student_id'];

                if (!in_array($studentId, $enrolledStudentIds, true)) {
                    // Skip any student not enrolled in this subject
                    continue;
                }

                $remarks = $item['status'] === 'absent' ? 'Absent' : ($item['status'] === 'leave' ? 'Leave' : 'Present');
                $records[] = [
                    'student_id' => $studentId,
                    'subject_id' => $data['subject_id'],
                    'teacher_id' => $teacher->id,
                    'attendance_type' => $attendanceType,
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
                    ->where('attendance_type', $records[0]['attendance_type'] ?? self::ATTENDANCE_TYPE_CLASS)
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

    private function getAttendanceLabel(string $attendanceType): string
    {
        return $attendanceType === self::ATTENDANCE_TYPE_LAB ? 'Lab Attendance' : 'Class Attendance';
    }

    private function getAttendanceRoutes(string $attendanceType): array
    {
        if ($attendanceType === self::ATTENDANCE_TYPE_LAB) {
            return [
                'index' => route('teacher.attendance.lab'),
                'students' => route('teacher.attendance.lab.students'),
                'store' => route('teacher.attendance.lab.store'),
                'print' => route('teacher.attendance.lab.print'),
                'export' => route('teacher.attendance.lab.export'),
                'bulkAddAll' => route('teacher.attendance.lab.bulkAddAll'),
            ];
        }

        return [
            'index' => route('teacher.attendance'),
            'students' => route('teacher.attendance.students'),
            'store' => route('teacher.attendance.store'),
            'print' => route('teacher.attendance.print'),
            'export' => route('teacher.attendance.export'),
            'bulkAddAll' => route('teacher.attendance.bulkAddAll'),
        ];
    }

    private function getSubjectIdsForAttendanceType(string $attendanceType, $teacher, $user): array
    {
        return $this->getSubjectsForAttendanceType($attendanceType, $teacher, $user)['ids'];
    }

    private function getSubjectsForAttendanceType(string $attendanceType, $teacher, $user): array
    {
        if (!$teacher) {
            return [
                'ids' => [],
                'subjects' => collect([]),
                'availableSemesters' => [],
            ];
        }

        if ($attendanceType === self::ATTENDANCE_TYPE_LAB) {
            $assignedIds = $this->loadTeacherSubjectIds();

            $labSubjects = DB::table('subjects')
                ->where(function ($q) {
                    $q->where('has_lab', 1)->orWhereNotNull('lab_technician_id');
                })
                ->where(function ($q) use ($assignedIds, $teacher) {
                    $q->whereIn('id', $assignedIds)
                        ->orWhere('lab_technician_id', $teacher->id);
                })
                ->select('id', 'subject_name', 'subject_code', 'semester', 'status')
                ->orderBy('subject_name')
                ->get();

            $subjects = $labSubjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                ];
            });

            $availableSemesters = $labSubjects
                ->filter(fn ($subject) => !empty($subject->semester) && ($subject->status ?? 'active') === 'active')
                ->map(fn ($subject) => (int) $subject->semester)
                ->unique()
                ->values()
                ->all();

            return [
                'ids' => $labSubjects->pluck('id')->map(fn ($id) => (int) $id)->all(),
                'subjects' => $subjects,
                'availableSemesters' => $availableSemesters,
            ];
        }

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

        $legacySubjects = collect();
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

        $availableSemesters = [];
        foreach ($pivotAssignments as $pa) {
            $s = $pa->subject ?? null;
            if ($s && isset($s->semester) && $s->semester != '' && ($s->status ?? 'active') == 'active') {
                $availableSemesters[] = (int)$s->semester;
            }
        }
        if ($legacySubjects->isNotEmpty()) {
            foreach ($legacySubjects as $ls) {
                if (isset($ls->semester) && $ls->semester != '' && ($ls->status ?? 'active') == 'active') {
                    $availableSemesters[] = (int)$ls->semester;
                }
            }
        }

        return [
            'ids' => $subjects->pluck('id')->map(fn ($id) => (int) $id)->all(),
            'subjects' => $subjects,
            'availableSemesters' => array_values(array_unique($availableSemesters)),
        ];
    }
}

