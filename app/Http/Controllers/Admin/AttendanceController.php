<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display attendance records from database (read-only view)
     */
public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', '');
        $search = $request->get('q', '');
        $semester = $request->get('semester', '');
        $course = $request->get('course', '');
        
        try {
            // Build query to get attendance records from database
            $attendanceQuery = DB::table('attendance')
                ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
                ->leftJoin('users', 'students.user_id', '=', 'users.id')
                ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
                ->where('users.role', 'student')
                ->select(
                    'attendance.id',
                    'attendance.student_id',
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
                    'subjects.subject_code',
                    'subjects.category'
                );
            
            // Filter by AD date if provided
            if ($date !== '' && $date !== null) {
                $attendanceQuery->where('attendance.date', '=', $date);
            }
            
            // Filter by semester if selected
            if ($semester !== '' && $semester !== null) {
                $attendanceQuery->where('students.semester', $semester);
            }

            // Filter by course/subject if selected
            if ($course !== '' && $course !== null) {
                $attendanceQuery->where('attendance.subject_id', $course);
            }
            
            // Apply search filter on student name or email
            if (!empty($search)) {
                $attendanceQuery->where(function($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%")
                      ->orWhere('students.roll_no', 'like', "%{$search}%");
                });
            }
            
            // Get attendance records with student info
            $attendanceRecords = $attendanceQuery
                ->orderBy('attendance.date', 'desc')
                ->orderBy('users.name')
                ->get()
                ->map(function ($record) {
                    // Convert each record to an array with properly typed values
                    return [
                        'id' => (string) ($record->id ?? ''),
                        'student_id' => (string) ($record->student_id ?? ''),
                        'subject_id' => (string) ($record->subject_id ?? ''),
                        'status' => (string) ($record->status ?? ''),
                        'remarks' => (string) ($record->remarks ?? ''),
                        'date' => (string) ($record->date ?? ''),
                        'date_bs' => (string) ($record->date_bs ?? ''),
                        'name' => (string) ($record->name ?? ''),
                        'email' => (string) ($record->email ?? ''),
                        'roll_no' => (string) ($record->roll_no ?? ''),
                        'semester' => (string) ($record->semester ?? ''),
                        'subject_name' => (string) ($record->subject_name ?? ''),
                        'subject_code' => (string) ($record->subject_code ?? ''),
                        'category' => (string) ($record->category ?? ''),
                    ];
                });
            
            // Ensure it's a Collection for blade helpers
            $attendanceRecords = collect($attendanceRecords);
            
            // Get statistics for the filtered records
            $stats = [
                'total' => $attendanceRecords->count(),
                'present' => $attendanceRecords->where('status', 'present')->count(),
                'absent' => $attendanceRecords->where('status', 'absent')->count(),
                'leave' => $attendanceRecords->where('status', 'leave')->count(),
            ];
            
            // Get available semesters for the filter dropdown
            $semesters = DB::table('students')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->distinct()
                ->pluck('students.semester')
                ->filter(function($sem) {
                    return !is_null($sem) && $sem !== '';
                })
                ->map(function ($sem) {
                    // Ensure semester is a string/integer
                    return (int) $sem;
                })
                ->sort()
                ->values()
                ->unique();

            // Get all courses/subjects for filter dropdown (including archived)
            $courses = DB::table('subjects')
                ->orderBy('subject_name')
                ->select('id', 'subject_code', 'subject_name', 'semester')
                ->get();
            
            // Type hint for static analysis
            /** @var \Illuminate\Support\Collection $attendanceRecords */
            return view('admin.attendance', compact('attendanceRecords', 'date', 'search', 'semester', 'course', 'stats', 'semesters', 'courses'));
            
        } catch (\Exception $e) {
            Log::error('Attendance error: ' . $e->getMessage());
            
            // Return view with empty data on error
            return view('admin.attendance', [
                'attendanceRecords' => collect([]),
                'date' => $date,
                'search' => $search,
                'semester' => $semester,
                'course' => $course,
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'leave' => 0],
                'semesters' => collect([]),
                'courses' => collect([])
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
            ];

            // Add subject_id if provided and not empty
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

            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'date' => $data['date'],
                ],
                $updateData
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'status' => $data['status'],
                'remarks' => $remarks,
                'data' => $attendance
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Attendance store validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
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
        // Use AD date directly
        foreach ($data['attendance'] as $item) {
            $remarks = $item['status'] === 'absent' ? 'Absent' : ($item['status'] === 'leave' ? 'Leave' : 'Present');
            $record = [
                'student_id' => $item['student_id'],
                'date' => $date,
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

        if (empty($semester)) {
            return response()->json(['error' => 'Semester is required'], 400);
        }

        // Get all students for this semester with father name
        $students = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('users as parent_users', 'students.parent_id', '=', 'parent_users.id')
            ->where('users.role', 'student')
            ->where('students.semester', $semester)
            ->select(
                'students.id as student_id',
                'users.name',
                'users.email',
                'students.roll_no',
                'students.semester',
                'parent_users.name as father_name'
            )
            ->orderBy('users.name')
            ->get();

        // Get existing attendance records for this date
        $existingAttendance = collect([]);
        $alreadyMarkedStudents = collect([]);
        if (!empty($date_bs)) {
            $existingAttendance = DB::table('attendance')
                ->where('date_bs', $date_bs)
                ->pluck('status', 'student_id');
            
            // Get students who already have attendance for this date
            $alreadyMarkedStudents = DB::table('attendance')
                ->where('date_bs', $date_bs)
                ->pluck('student_id');
        }

        // Merge with existing attendance
        $students = $students->map(function($student) use ($existingAttendance, $alreadyMarkedStudents) {
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
            $attendanceQuery->where(function($q) use ($search) {
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

        return response()->streamDownload(function() use ($csv) {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Attendance update validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            Log::error('Attendance delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
