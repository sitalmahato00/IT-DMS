<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Student;
use App\Helpers\NepaliContentHelper;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\College;
use App\Traits\LogsActivity;

class AttendanceController extends Controller
{
    use LogsActivity;
    /**
     * Display attendance records from database (read-only view)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', '');
        $date_bs = $request->get('date_bs', '');
        $search = $request->get('q', '');
        $semester = $request->get('semester', '');
        $course = $request->get('course', '');
        $perPage = intval($request->get('per_page', 25)) ?: 25;

        try {
            // Debug: Log the request parameters
            Log::info('Attendance index called with params:', [
                'date' => $date,
                'date_bs' => $date_bs,
                'search' => $search,
                'semester' => $semester,
                'course' => $course
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
            if ($course !== '' && $course !== null) {
                $attendanceQuery->where('attendance.subject_id', $course);
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
            if (empty($date) && empty($date_bs) && empty($search) && empty($semester) && empty($course)) {
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
            if ($course !== '' && $course !== null) {
                $subjectAttendanceQuery->where('attendance.subject_id', $course);
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
                'leave' => (int)$item->leave_count,
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
                ->orderBy('subject_name')
                ->select('id', 'subject_code', 'subject_name', 'semester')
                ->get();

            // Type hint for static analysis
            /** @var \Illuminate\Support\Collection $attendanceRecords */
            Log::info('Attendance query result count: ' . $attendance->total());

            return view('admin.attendance', compact('attendance', 'date', 'date_bs', 'search', 'semester', 'course', 'stats', 'semesters', 'courses', 'subjectAttendance'));

        }
        catch (\Exception $e) {
            Log::error('Attendance error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

            // Return view with default empty data - use empty Paginator for attendance
            $emptyPaginator = new LengthAwarePaginator(
                collect([]), // items
                0, // total
                25, // perPage
                1, // currentPage
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // options
                );

            return view('admin.attendance', [
                'attendance' => $emptyPaginator,
                'date' => '',
                'date_bs' => '',
                'search' => '',
                'semester' => '',
                'course' => '',
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0],
                'semesters' => [1, 2, 3, 4, 5, 6],
                'courses' => collect([]),
                'subjectAttendance' => collect([]),
            ]);
        }
    }

    /**
     * Store or update attendance
     */
    public function store(Request $request)
    {
        try {
            Log::info('Attendance store request data: ', $request->all());

            $data = $request->validate([
                'student_id' => 'required',
                'date' => 'required|date',
                'status' => 'required|in:present,absent,leave',
                'remarks' => 'nullable|string|max:255',
                'subject_id' => 'nullable'
            ]);

            Log::info('Attendance store validated data: ', $data);

            $remarks = $data['remarks'] ?? ($data['status'] === 'absent' ? 'Absent' : ($data['status'] === 'leave' ? 'Leave' : 'Present'));

            $updateData = [
                'status' => $data['status'],
                'remarks' => $remarks,
                'date' => $data['date'],
                'date_bs' => NepaliContentHelper::convertAdToBs($data['date']),
            ];

            // Add subject_id if provided and not empty
            if (!empty($data['subject_id'])) {
                $updateData['subject_id'] = $data['subject_id'];
            }

            $user = auth()->user();
            if ($user) {
                // Ensure the FK points to the user record as per attendance migration
                $updateData['teacher_id'] = $user->id;
            }

            $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'date' => $data['date'],
            ],
                $updateData
            );

            // Log activity
            $student = Student::find($data['student_id']);
            $studentName = $student ? ($student->user ? $student->user->name : 'Student #' . $data['student_id']) : 'Unknown';
            $this->logActivity('Attendance', 'Recorded Attendance', "Attendance marked for {$studentName} - Status: {$data['status']}");

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'status' => $data['status'],
                'remarks' => $remarks,
                'data' => $attendance
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Attendance store validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        }
        catch (\Exception $e) {
            Log::error('Attendance store error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle attendance status
     */
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date_bs' => 'required|string|max:30',
            'remarks' => 'nullable|string|max:255'
        ]);

        $attendance = Attendance::where('student_id', $data['student_id'])
            ->where('date_bs', $data['date_bs'])
            ->first();

        $newStatus = ($attendance && $attendance->status === 'present') ? 'absent' : 'present';
        $remarks = $data['remarks'] ?? ($newStatus === 'absent' ? 'Absent' : 'Present');

        $updateData = [
            'status' => $newStatus,
            'remarks' => $remarks,
            'date_bs' => $data['date_bs'],
            'date' => NepaliContentHelper::convertBsToAd($data['date_bs']),
        ];

        $user = auth()->user();
        if ($user) {
            $teacher = DB::table('teachers')->where('user_id', $user->id)->first();
            if ($teacher) {
                $updateData['teacher_id'] = $teacher->id;
            }
        }

        $attendance = Attendance::updateOrCreate(
        [
            'student_id' => $data['student_id'],
            'date_bs' => $data['date_bs'],
        ],
            $updateData
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance toggled successfully',
            'status' => $newStatus,
            'remarks' => $remarks
        ]);
    }

    /**
     * Bulk update attendance
     */
    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,leave',
            'date' => 'required|date',
            'subject_id' => 'nullable|exists:subjects,id'
        ]);

        $date = $data['date'];
        $subjectId = $data['subject_id'] ?? null;

        // Get teacher ID from current user
        $teacherId = null;
        $user = auth()->user();
        if ($user) {
            $teacher = DB::table('teachers')->where('user_id', $user->id)->first();
            if ($teacher) {
                $teacherId = $teacher->id;
            }
        }

        // Build records array for upsert (atomic operation)
        $records = [];
        $now = now()->toDateTimeString();
        // Auto-calculate BS date from AD date
        $dateBs = NepaliContentHelper::convertAdToBs($date);

        foreach ($data['attendance'] as $item) {
            $remarks = $item['status'] === 'absent' ? 'Absent' : ($item['status'] === 'leave' ? 'Leave' : 'Present');
            $record = [
                'student_id' => $item['student_id'],
                'date' => $date,
                'date_bs' => $dateBs,
                'teacher_id' => $teacherId,
                'status' => $item['status'],
                'remarks' => $remarks,
                'updated_at' => $now,
                'created_at' => $now
            ];
            if (!empty($subjectId)) {
                $record['subject_id'] = $subjectId;
            }
            $records[] = $record;
        }

        // Use upsert for atomic insert/update (SQLite doesn't support ON CONFLICT, but this works)
        // First delete existing records for this date, then insert all fresh
        // This prevents duplicates while allowing updates
        $studentIds = array_column($records, 'student_id');

        DB::transaction(function () use ($records, $studentIds, $subjectId) {
            // Build query to delete existing attendance records for these students and date
            $query = DB::table('attendance')
                ->whereIn('student_id', $studentIds)
                ->where('date', $records[0]['date']);
            if (!empty($subjectId)) {
                $query->where('subject_id', $subjectId);
            }
            $query->delete();
            DB::table('attendance')->insert($records);
        });

        return response()->json([
            'success' => true,
            'message' => "Attendance saved successfully! " . count($records) . " records updated.",
            'saved' => count($records)
        ]);
    }

    /**
     * Get students for a specific semester to mark attendance
     */
    public function getStudentsForAttendance(Request $request)
    {
        $date_bs = $request->get('date_bs');
        $semester = $request->get('semester');
        $subject_id = $request->get('subject_id');

        $subjectFilterId = null;
        if (!empty($subject_id) && $subject_id !== 'null' && $subject_id !== 'general') {
            $subjectFilterId = $subject_id;
        }

        if (empty($semester)) {
            return response()->json(['error' => 'Semester is required'], 400);
        }

        // Get all students for this semester with father name
        $studentsQuery = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('users as parent_users', 'students.parent_id', '=', 'parent_users.id')
            ->where('users.role', 'student')
            ->where('students.semester', $semester)
            ->when($subjectFilterId, function ($query) use ($subjectFilterId) {
                return $query->join('subject_students', 'subject_students.student_id', '=', 'students.id')
                    ->where('subject_students.subject_id', $subjectFilterId);
            });

        $students = $studentsQuery
            ->select(
                'students.id as student_id',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester',
                'parent_users.name as father_name'
            )
            ->orderBy('users.name')
            ->distinct()
            ->get();

        // Get existing attendance records for this date and subject
        $existingAttendance = collect([]);
        $alreadyMarkedStudents = collect([]);
        if (!empty($date_bs)) {
            $attendanceQuery = DB::table('attendance')
                ->where('date_bs', $date_bs);

            // Filter by subject_id if provided
            if (!empty($subject_id)) {
                $attendanceQuery->where('subject_id', $subject_id);
            }
            else {
                // If no subject selected, get records with null subject_id (general attendance)
                $attendanceQuery->whereNull('subject_id');
            }

            $existingAttendance = $attendanceQuery
                ->pluck('status', 'student_id');

            // Get students who already have attendance for this date and subject
            $alreadyMarkedStudents = $attendanceQuery
                ->pluck('student_id');
        }

        // Merge with existing attendance
        $students = $students->map(function ($student) use ($existingAttendance, $alreadyMarkedStudents) {
            $attendance = $existingAttendance->get($student->student_id);
            $student->status = $attendance ?? 'present'; // Default to present
            $student->alreadyMarked = $alreadyMarkedStudents->contains($student->student_id);
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
     * Get attendance records for a specific subject and date (for viewing in modal)
     */
    public function getSubjectStudentsForAttendance(Request $request)
    {
        $date = $request->get('date');
        $subjectId = $request->get('subject_id');

        if (empty($date)) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Get only students with attendance records
        $studentsQuery = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->join('attendance', function ($join) use ($date, $subjectId) {
            $join->on('attendance.student_id', '=', 'students.id')
                ->where('attendance.date', '=', $date);
            if ($subjectId && $subjectId !== 'null' && $subjectId !== '') {
                $join->where('attendance.subject_id', '=', $subjectId);
            }
            else {
                $join->whereNull('attendance.subject_id');
            }
        })
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select(
            'attendance.id',
            'students.id as student_id',
            'attendance.subject_id',
            'attendance.status',
            'attendance.remarks',
            'attendance.date',
            'attendance.date_bs',
            'users.name',
            'users.email',
            'students.roll_no',
            'students.semester',
            'subjects.subject_name',
            'subjects.subject_code'
        )
            ->where('users.role', 'student')
            ->orderBy('users.name');

        $records = $studentsQuery->get();

        // Transform records
        $students = $records->map(function ($record) {
            return [
            'id' => (string)($record->id ?? $record->student_id),
            'student_id' => (string)$record->student_id,
            'subject_id' => $record->subject_id ? (string)$record->subject_id : null,
            'status' => $record->status ?? 'pending',
            'remarks' => $record->remarks ?? '',
            'date' => $record->date,
            'date_bs' => $record->date_bs,
            'name' => $record->name,
            'email' => $record->email,
            'roll_no' => $record->roll_no,
            'semester' => $record->semester,
            'subject_name' => $record->subject_name,
            'subject_code' => $record->subject_code,
            ];
        });

        return response()->json([
            'students' => $students,
            'total' => $students->count(),
            'present' => $students->where('status', 'present')->count(),
            'absent' => $students->where('status', 'absent')->count(),
            'leave' => $students->where('status', 'leave')->count(),
        ]);
    }

    /**
     * Get attendance report for a student
     */
    public function studentReport($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $attendanceRecords = Attendance::forStudent($studentId)
            ->orderBy('date_bs', 'desc')
            ->get();

        $stats = [
            'total' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'leave' => $attendanceRecords->where('status', 'leave')->count(),
        ];

        $percentage = $stats['total'] > 0
            ? round(($stats['present'] / $stats['total']) * 100, 2)
            : 0;

        return response()->json([
            'student' => $student,
            'attendance' => $attendanceRecords,
            'stats' => $stats,
            'percentage' => $percentage,
        ]);
    }

    /**
     * Export attendance as CSV
     */
    public function export(Request $request)
    {
        $date_bs = $request->get('date_bs', '');
        $search = $request->get('q', '');
        $semester = $request->get('semester', '');
        $course = $request->get('course', '');

        $attendanceQuery = DB::table('attendance')
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->where('users.role', 'student');

        if ($date_bs !== '' && $date_bs !== null) {
            $attendanceQuery->where('attendance.date_bs', '=', $date_bs);
        }

        if (!empty($semester)) {
            $attendanceQuery->where('students.semester', $semester);
        }

        if (!empty($course)) {
            $attendanceQuery->where('attendance.subject_id', $course);
        }

        if (!empty($search)) {
            $attendanceQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        $attendanceRecords = $attendanceQuery
            ->select('attendance.*', 'users.name', 'users.email', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date_bs', 'desc')
            ->get();

        $csv = "Date,Student ID,Name,Email,Course,Course Code,Status,Remarks\n";

        foreach ($attendanceRecords as $record) {
            $courseName = $record->subject_name ?? 'General';
            $courseCode = $record->subject_code ?? '-';
            $csv .= "{$record->date_bs},{$record->student_id},{$record->name},{$record->email},{$courseName},{$courseCode},{$record->status},{$record->remarks}\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, "attendance-export.csv");
    }

    /**
     * Update an attendance record
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'student_id' => 'required|exists:students,id',
                // date field for AD date (optional in update)
                'date' => 'sometimes|nullable|date',
                // date_bs may not always be sent by clients when updating existing rows; only require it when present
                'date_bs' => 'sometimes|required|string|max:30',
                'status' => 'required|in:present,absent,leave',
                'remarks' => 'nullable|string|max:255',
                'subject_id' => 'nullable|exists:subjects,id'
            ]);

            $remarks = $data['remarks'] ?? ($data['status'] === 'absent' ? 'Absent' : ($data['status'] === 'leave' ? 'Leave' : 'Present'));

            $updateData = [
                'student_id' => $data['student_id'],
                'status' => $data['status'],
                'remarks' => $remarks,
            ];

            // Auto-calculate BS date when AD date is provided in update
            if (array_key_exists('date', $data) && !empty($data['date'])) {
                $updateData['date_bs'] = NepaliContentHelper::convertAdToBs($data['date']);
            }

            // Only update date_bs when it is provided by client (prevents validation errors if missing)
            if (array_key_exists('date_bs', $data) && $data['date_bs'] !== null && $data['date_bs'] !== '') {
                $updateData['date_bs'] = $data['date_bs'];
            }

            // Add subject_id if provided
            if (!empty($data['subject_id'])) {
                $updateData['subject_id'] = $data['subject_id'];
            }

            $user = auth()->user();
            if ($user) {
                $teacher = DB::table('teachers')->where('user_id', $user->id)->first();
                if ($teacher) {
                    $updateData['teacher_id'] = $teacher->id;
                }
            }

            // Update by ID
            DB::table('attendance')->where('id', $id)->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'status' => $data['status'],
                'remarks' => $remarks
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Attendance update validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        }
        catch (\Exception $e) {
            Log::error('Attendance update error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an attendance record
     */
    public function delete(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|exists:attendance,id'
            ]);

            DB::table('attendance')->where('id', $data['id'])->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attendance record deleted successfully'
            ]);
        }
        catch (\Exception $e) {
            Log::error('Attendance delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete attendance records by date and subject
     */
    public function bulkDelete(Request $request)
    {
        try {
            $data = $request->validate([
                'date' => 'required|date',
                'subject_id' => 'nullable'
            ]);

            $query = DB::table('attendance')->where('date', $data['date']);

            if (!empty($data['subject_id'])) {
                $query->where('subject_id', $data['subject_id']);
            }
            else {
                $query->whereNull('subject_id');
            }

            $deletedCount = $query->delete();

            return response()->json([
                'success' => true,
                'message' => "Deleted {$deletedCount} attendance records successfully",
                'deleted' => $deletedCount
            ]);
        }
        catch (\Exception $e) {
            Log::error('Bulk attendance delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects by semester for attendance
     */
    public function getSubjectsBySemester(Request $request)
    {
        $semester = $request->get('semester');

        if (empty($semester)) {
            return response()->json([
                'success' => false,
                'message' => 'Semester is required'
            ], 400);
        }

        try {
            // Get subjects for the selected semester from the subjects table
            $subjects = DB::table('subjects')
                ->where('semester', $semester)
                ->orderBy('subject_name')
                ->select('id', 'subject_code', 'subject_name', 'semester')
                ->get();

            return response()->json([
                'success' => true,
                'subjects' => $subjects
            ]);
        }
        catch (\Exception $e) {
            Log::error('Error getting subjects by semester: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show today's attendance page
     */
    public function todayAttendance(Request $request)
    {
        $user = auth()->user();

        // Get today's date
        $todayAd = Carbon::now()->format('Y-m-d');
        $todayBs = NepaliContentHelper::convertAdToBs($todayAd);

        // Get stats for today
        $todayStats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
        ];

        $todayAttendance = DB::table('attendance')
            ->where('date', $todayAd)
            ->get();

        if ($todayAttendance->count() > 0) {
            $todayStats['total'] = $todayAttendance->count();
            $todayStats['present'] = $todayAttendance->where('status', 'present')->count();
            $todayStats['absent'] = $todayAttendance->where('status', 'absent')->count();
            $todayStats['leave'] = $todayAttendance->where('status', 'leave')->count();
        }

        return view('admin.attendance-today', compact('todayAd', 'todayBs', 'todayStats'));
    }

    /**
     * Get today's attendance data via AJAX - grouped by subject
     */
    public function getTodayAttendanceData(Request $request)
    {
        try {
            $todayAd = Carbon::now()->format('Y-m-d');
            $todayBs = NepaliContentHelper::convertAdToBs($todayAd);

            // Get attendance records for today grouped by subject
            $attendanceRecords = DB::table('attendance')
                ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
                ->where('attendance.date', $todayAd)
                ->select(
                'attendance.subject_id',
                'subjects.subject_name',
                'subjects.subject_code',
                DB::raw('COUNT(*) as total_students'),
                DB::raw('SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count'),
                DB::raw('SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count'),
                DB::raw('SUM(CASE WHEN attendance.status = "leave" THEN 1 ELSE 0 END) as leave_count')
            )
                ->groupBy('attendance.subject_id', 'subjects.subject_name', 'subjects.subject_code')
                ->orderBy('subjects.subject_name', 'asc')
                ->get();

            // Transform records
            $subjects = $attendanceRecords->map(function ($record) {
                return [
                'subject_id' => $record->subject_id ? (string)$record->subject_id : null,
                'subject_name' => $record->subject_name ?? 'General',
                'subject_code' => $record->subject_code ?? '',
                'total_students' => (int)$record->total_students,
                'present_count' => (int)$record->present_count,
                'absent_count' => (int)$record->absent_count,
                'leave_count' => (int)$record->leave_count,
                ];
            });

            // Get stats
            $stats = [
                'total' => $subjects->sum('total_students'),
                'present' => $subjects->sum('present_count'),
                'absent' => $subjects->sum('absent_count'),
                'leave' => $subjects->sum('leave_count'),
            ];

            return response()->json([
                'success' => true,
                'subjects' => $subjects,
                'stats' => $stats,
                'date' => $todayAd,
                'date_bs' => $todayBs,
            ]);
        }
        catch (\Exception $e) {
            Log::error('Error getting today attendance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students for a specific subject on today's date
     */
    public function getTodaySubjectStudents(Request $request)
    {
        try {
            $subjectId = $request->get('subject_id');
            $todayAd = Carbon::now()->format('Y-m-d');
            $todayBs = NepaliContentHelper::convertAdToBs($todayAd);

            // Get only students who have attendance marked for today
            $query = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->join('attendance', function ($join) use ($todayAd, $subjectId) {
                $join->on('attendance.student_id', '=', 'students.id')
                    ->where('attendance.date', '=', $todayAd);
                if ($subjectId && $subjectId !== 'general' && $subjectId !== 'null') {
                    $join->where('attendance.subject_id', '=', $subjectId);
                }
                else {
                    $join->whereNull('attendance.subject_id');
                }
            })
                ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
                ->where('users.role', 'student')
                ->select(
                'attendance.id',
                'students.id as student_id',
                'attendance.subject_id',
                'attendance.status',
                'attendance.remarks',
                'attendance.date',
                'attendance.date_bs',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester',
                'subjects.subject_name',
                'subjects.subject_code'
            )
                ->orderBy('users.name');

            $records = $query->get();

            // Transform records
            $students = $records->map(function ($record) {
                return [
                'id' => (string)($record->id ?? $record->student_id),
                'student_id' => (string)$record->student_id,
                'subject_id' => $record->subject_id ? (string)$record->subject_id : null,
                'status' => $record->status ?? 'pending',
                'remarks' => $record->remarks ?? '',
                'date' => $record->date,
                'date_bs' => $record->date_bs,
                'name' => $record->name,
                'email' => $record->email,
                'roll_no' => $record->roll_no,
                'semester' => $record->semester,
                'subject_name' => $record->subject_name,
                'subject_code' => $record->subject_code,
                ];
            });

            // Get subject info
            $subjectName = 'General';
            $subjectCode = '';
            if ($subjectId && $subjectId !== 'general' && $subjectId !== 'null') {
                $subject = DB::table('subjects')->where('id', $subjectId)->first();
                if ($subject) {
                    $subjectName = $subject->subject_name;
                    $subjectCode = $subject->subject_code;
                }
            }

            return response()->json([
                'success' => true,
                'students' => $students,
                'subject_name' => $subjectName,
                'subject_code' => $subjectCode,
            ]);
        }
        catch (\Exception $e) {
            Log::error('Error getting today subject students: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print attendance by subject (for a given date).
     * Used by the admin print preview modal and "new tab" printing.
     */
    public function printAttendance(Request $request)
    {
        $subjectId = (int) $request->query('subject_id') ?: (int) $request->query('subject', 0);
        $date = $request->query('date', now()->toDateString());

        if ($subjectId <= 0) {
            abort(404, 'Subject not found');
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

        $students = $students->map(function ($s) use ($existingAttendance) {
            $s->status = $existingAttendance->get($s->student_id, 'present');
            return $s;
        });

        $subject = DB::table('subjects')->where('id', $subjectId)->first();
        $subjectName = $subject->subject_name ?? 'Subject';
        $subjectCode = $subject->subject_code ?? '';
        $subjectSemester = $subject->semester ?? '';

        // College details for print header with safe fallbacks
        $collegeName = 'College';
        $collegeAddress = '';
        $collegeLogo = asset('images/default-logo.png');
        try {
            if (Schema::hasTable('colleges')) {
                $college = College::orderBy('id', 'desc')->first();
                if ($college) {
                    $collegeName = $college->name ?? $collegeName;
                    $collegeAddress = $college->address ?? $collegeAddress;
                    if (method_exists($college, 'getLogoUrl')) {
                        $logo = $college->getLogoUrl();
                        if ($logo) {
                            $collegeLogo = $logo;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // keep defaults on error
        }

        $user = auth()->user();

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
                'subject_semester' => $subjectSemester,
                'date_bs' => $date ? NepaliContentHelper::convertAdToBs($date) : '',
                'marked_by' => $user->name ?? $user->email ?? 'Unknown',
            ]);
        }

        return view('teacher.print.attendance_by_subject', [
            'subject_id' => $subjectId,
            'subject_name' => $subjectName,
            'subject_code' => $subjectCode,
            'date' => $date,
            'date_bs' => $date ? NepaliContentHelper::convertAdToBs($date) : '',
            'marked_by' => $user->name ?? $user->email ?? 'Unknown',
            'subject_semester' => $subjectSemester,
            'students' => $students,
            'collegeName' => $collegeName,
            'collegeAddress' => $collegeAddress,
            'collegeLogo' => $collegeLogo,
        ]);
    }

    /**
     * Print attendance list
     */
    public function printList(Request $request)
    {
        $date = $request->get('date', '');
        $date_bs = $request->get('date_bs', '');
        $semester = $request->get('semester', '');
        $course = $request->get('course', '');

        // Get college info
        $college = DB::table('colleges')->first();

        // Build grouped query - Attendance by Subject
        $attendanceQuery = DB::table('attendance')
            ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
            ->leftJoin('users', function ($join) {
                $join->on('students.user_id', '=', 'users.id');
            })
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select(
                'attendance.date',
                'attendance.date_bs',
                'attendance.subject_id',
                'subjects.subject_name',
                'subjects.subject_code',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN attendance.status = 'absent' THEN 1 ELSE 0 END) as absent"),
                DB::raw("SUM(CASE WHEN attendance.status = 'leave' THEN 1 ELSE 0 END) as leave")
            )
            ->groupBy('attendance.date', 'attendance.date_bs', 'attendance.subject_id', 'subjects.subject_name', 'subjects.subject_code');

        // Apply filters
        if (!empty($date)) {
            $attendanceQuery->where('attendance.date', '=', $date);
        }

        if (!empty($date_bs)) {
            $attendanceQuery->where('attendance.date_bs', '=', $date_bs);
        }

        if (!empty($semester) && is_numeric($semester)) {
            $attendanceQuery->where('students.semester', $semester);
        }

        if (!empty($course)) {
            $attendanceQuery->where('attendance.subject_id', $course);
        }

        $attendance = $attendanceQuery
            ->orderBy('attendance.date', 'desc')
            ->orderBy('subjects.subject_name')
            ->get();

        // Transform to array to match expected format
        $attendance = $attendance->map(function ($record) {
            return [
                'date' => $record->date,
                'date_bs' => $record->date_bs ?? '',
                'subject_id' => $record->subject_id,
                'subject_name' => $record->subject_name ?? 'General',
                'subject_code' => $record->subject_code ?? '',
                'total' => $record->total,
                'present' => $record->present,
                'absent' => $record->absent,
                'leave' => $record->leave,
            ];
        });

        // Get filter labels
        $subject = null;
        $subjectLabel = 'All';
        if (!empty($course)) {
            $subject = DB::table('subjects')->where('id', $course)->first();
            $subjectLabel = $subject ? $subject->subject_name : 'All';
        }

        $semesterLabel = !empty($semester) ? $semester : 'All';
        $dateLabel = !empty($date) ? $date : (!empty($date_bs) ? $date_bs : 'All Dates');

        return view('admin.print.attendance-list', compact('attendance', 'college', 'date', 'semester', 'subject', 'semesterLabel', 'dateLabel', 'subjectLabel'));
    }
}
