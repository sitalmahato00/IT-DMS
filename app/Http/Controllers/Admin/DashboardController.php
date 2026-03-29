<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Notice;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Semester;
use App\Models\ElectiveEnrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Helpers\NepaliContentHelper;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with various statistics.
     */
    public function index(Request $request)
    {
        // Total students count (active, non-alumni)
        $totalStudents = User::where('role', 'student')
            ->whereHas('student', function($q) {
                $q->where('status', 'active')
                  ->where('is_alumni', 0);
            })
            ->count();

        // Teachers count
        $teachers = User::where('role', 'teacher')->count();

        // Parents count
        $parents = User::where('role', 'parent')->count();
        
        // Alumni count (students marked as alumni)
        $alumni = User::where('role', 'student')
            ->whereHas('student', function($q) {
                $q->where('is_alumni', 1);
            })
            ->count();
        
        // Active semesters count
        $activeSemesters = Semester::where('is_active', 1)->count();
        
        // Elective students count (students with approved elective enrollments)
        $electiveStudents = ElectiveEnrollment::where('status', 'approved')->distinct('student_id')->count('student_id');
        
        // Courses count
        $courses = Course::count();

        // Calculate average attendance this semester
        $avgAttendance = $this->getAverageAttendance();
        
        // Get attendance summary (present, absent, late)
        $attendanceSummary = $this->getAttendanceSummary();

        // Attendance percentage data for chart
        $attendanceChartData = $this->getAttendanceChartData();

        // Grade distribution for pie chart
        $gradeDistribution = $this->getGradeDistribution();

        // Recent notices (latest 5)
        $recentNotices = Notice::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($notice) {
                return [
                    'title' => $notice->title,
                    'message' => $notice->message ?? $notice->content ?? '',
                    'created_at' => $notice->created_at,
                ];
            });

        // Upcoming exams
        $upcomingExams = $this->getUpcomingExams();
        
        // Today's classes
        $todayClasses = $this->getTodayClasses();

        // Recent activities from audit logs (latest 10) - with user join to get user names
        $recentActivities = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name')
            ->orderBy('audit_logs.created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($log) {
                $action = $log->action ?? $log->activity ?? 'action';
                $old = is_array($log->old_values) ? $log->old_values : (json_decode($log->old_values ?? '{}', true) ?? []);
                $new = is_array($log->new_values) ? $log->new_values : (json_decode($log->new_values ?? '{}', true) ?? []);

                // Attempt to find a human-friendly name/title in payload
                $nameKeys = ['name', 'title', 'subject_name', 'course_name', 'exam_name', 'file_name', 'gallery_title'];
                $displayName = null;
                foreach ($nameKeys as $k) {
                    if (isset($new[$k]) && $new[$k]) {
                        $displayName = $new[$k];
                        break;
                    }
                    if (isset($old[$k]) && $old[$k]) {
                        $displayName = $old[$k];
                        break;
                    }
                }

                $details = '';
                $entity = $log->model_type ? class_basename($log->model_type) : ($log->model_type ?? 'Record');

                if ($action === 'create') {
                    if ($displayName) {
                        $details = "Created {$entity}: {$displayName}";
                    } elseif (!empty($new)) {
                        $details = 'Created ' . $entity . ' (' . implode(', ', array_map(function($k, $v){ return "{$k}: {$v}"; }, array_keys($new), $new)) . ')';
                    } else {
                        $details = "Created {$entity}";
                    }
                } elseif ($action === 'update') {
                    $changes = [];
                    foreach ($new as $k => $v) {
                        $oldVal = $old[$k] ?? null;
                        if ($oldVal !== $v) {
                            $changes[] = "{$k}: " . (is_scalar($oldVal) ? $oldVal : json_encode($oldVal)) . " → " . (is_scalar($v) ? $v : json_encode($v));
                        }
                    }
                    if ($displayName && !empty($changes)) {
                        $details = "Updated {$entity} {$displayName} — " . implode(', ', $changes);
                    } elseif (!empty($changes)) {
                        $details = "Updated {$entity}: " . implode(', ', $changes);
                    } else {
                        $details = "Updated {$entity}";
                    }
                } elseif ($action === 'delete') {
                    if ($displayName) {
                        $details = "Deleted {$entity}: {$displayName}";
                    } elseif (!empty($old)) {
                        $details = 'Deleted ' . $entity . ' (' . implode(', ', array_map(function($k, $v){ return "{$k}: {$v}"; }, array_keys($old), $old)) . ')';
                    } else {
                        $details = "Deleted {$entity}";
                    }
                } else {
                    // Generic fallback; include model info if available
                    $details = $displayName ? "{$entity}: {$displayName}" : ($log->model_type ? $entity : ($log->action ?? 'Activity'));
                }

                return [
                    'id' => $log->id,
                    'action' => $log->formatted_action ?? ucfirst($action),
                    'user_name' => $log->user_name ?? 'System',
                    'timestamp' => $log->created_at,
                    'details' => $details,
                    'model_type' => $log->model_type,
                    'model_id' => $log->model_id,
                ];
            });

        // New students (recently registered, last 5)
        $newStudents = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($student) {
                return [
                    'name' => $student->name,
                    'email' => $student->email,
                    'created_at' => $student->created_at,
                ];
            });

        // Recent attendance records (today's classes)
        $perPage = intval($request->get('per_page', 5)) ?: 5;
        $recentAttendance = $this->getRecentAttendance($perPage);

        // Get the authenticated user
        $user = auth()->user();

        return view('admin.dashboard', compact(
            'user',
            'totalStudents',
            'teachers',
            'parents',
            'alumni',
            'activeSemesters',
            'electiveStudents',
            'courses',
            'avgAttendance',
            'attendanceSummary',
            'attendanceChartData',
            'gradeDistribution',
            'recentNotices',
            'recentActivities',
            'newStudents',
            'recentAttendance',
            'upcomingExams',
            'todayClasses'
        ));
    }

    /**
     * Get attendance data for AJAX chart requests.
     */
    public function attendanceData(Request $request)
    {
        $period = $request->get('period', 'week'); // week, month, semester
        if (!in_array($period, ['week', 'month', 'semester'], true)) {
            $period = 'week';
        }

        $data = $this->getAttendanceChartData($period);

        return response()->json($data);
    }

    /**
     * Get average attendance percentage for current semester.
     */
    private function getAverageAttendance()
    {
        // Only include active, non-alumni students
        $attendance = $this->getActiveNonAlumniAttendanceQuery()->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN LOWER(attendance.status) = "present" THEN 1 ELSE 0 END) as present_count
        ')->first();

        if (!$attendance || $attendance->total == 0) {
            return 0;
        }

        return round(($attendance->present_count / $attendance->total) * 100, 1);
    }

    /**
     * Get attendance chart data for the specified period.
     */
    private function getAttendanceChartData($period = 'week')
    {
        if (!in_array($period, ['week', 'month', 'semester'], true)) {
            $period = 'week';
        }

        $labels = [];
        $data = [];
        $details = [];
        $today = Carbon::today();

        if ($period === 'week') {
            // Weekly: exactly 7 bars (today and previous 6 days)
            $startDate = $today->copy()->subDays(6);
            for ($i = 0; $i < 7; $i++) {
                $bucketDate = $startDate->copy()->addDays($i);
                $bucketSummary = $this->getAttendanceBucketSummary($bucketDate, $bucketDate);

                $labels[] = $bucketDate->format('D, M j');
                $data[] = $bucketSummary['percentage'];
                $details[] = [
                    'period' => $bucketDate->toDateString(),
                    'present' => $bucketSummary['present'],
                    'total' => $bucketSummary['total'],
                    'percentage' => $bucketSummary['percentage'],
                ];
            }
        } elseif ($period === 'semester') {
            // Semester: 6 buckets of 30 days (Semester labels)
            // keep same style as teacher dashboard in this app
            $semesterStart = $today->copy()->subDays(180);
            for ($i = 0; $i < 6; $i++) {
                $bucketStart = $semesterStart->copy()->addDays($i * 30)->startOfDay();
                $bucketEnd = $bucketStart->copy()->addDays(29)->endOfDay();
                $bucketSummary = $this->getAttendanceBucketSummary($bucketStart, $bucketEnd);

                $labels[] = 'Semester ' . ($i + 1);
                $data[] = $bucketSummary['percentage'];
                $details[] = [
                    'period' => sprintf('%s_%s', $bucketStart->format('Y-m-d'), $bucketEnd->format('Y-m-d')),
                    'present' => $bucketSummary['present'],
                    'total' => $bucketSummary['total'],
                    'percentage' => $bucketSummary['percentage'],
                ];
            }
        } else {
            // Monthly: exactly 12 bars for the current year (Jan-Dec)
            $yearStart = $today->copy()->startOfYear();
            for ($i = 0; $i < 12; $i++) {
                $bucketStart = $yearStart->copy()->addMonths($i)->startOfMonth();
                $bucketEnd = $bucketStart->copy()->endOfMonth();
                $bucketSummary = $this->getAttendanceBucketSummary($bucketStart, $bucketEnd);

                $labels[] = $bucketStart->format('M');
                $data[] = $bucketSummary['percentage'];
                $details[] = [
                    'period' => $bucketStart->format('Y-m'),
                    'present' => $bucketSummary['present'],
                    'total' => $bucketSummary['total'],
                    'percentage' => $bucketSummary['percentage'],
                ];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'details' => $details,
        ];
    }

    /**
     * Base attendance query for active, non-alumni students only.
     */
    private function getActiveNonAlumniAttendanceQuery()
    {
        $query = Attendance::query()
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->where('students.status', 'active')
            ->where('students.is_alumni', 0)
            ->whereNull('students.deleted_at');

        if (Schema::hasColumn('attendance', 'attendance_type')) {
            $query->where(function ($q) {
                $q->where('attendance.attendance_type', 'class')
                  ->orWhereNull('attendance.attendance_type');
            });
        }

        return $query;
    }

    /**
     * Get attendance summary for a date range.
     */
    private function getAttendanceBucketSummary(Carbon $startDate, Carbon $endDate): array
    {
        $summary = $this->getActiveNonAlumniAttendanceQuery()
            ->whereBetween('attendance.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(attendance.status) = "present" THEN 1 ELSE 0 END) as present_count
            ')
            ->first();

        $total = (int) ($summary->total ?? 0);
        $present = (int) ($summary->present_count ?? 0);
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get recent attendance records with class details.
     */
    private function getRecentAttendance($perPage = 5)
    {
        // Only show attendance for the exact current date (both AD and BS)
        $todayAd = Carbon::now()->format('Y-m-d');
        $todayBs = NepaliContentHelper::convertAdToBs($todayAd);

        // Get distinct attendance records by subject for today - one record per subject
        $attendanceQuery = Attendance::with(['user', 'subject.teacherAssignments.teacher.user'])
            ->where('date', $todayAd)
            ->where('date_bs', $todayBs)
            ->select('subject_id', 'date', 'date_bs')
            ->selectRaw('COUNT(CASE WHEN status = "present" THEN 1 END) as present_count')
            ->selectRaw('COUNT(*) as total_count')
            ->groupBy('subject_id', 'date', 'date_bs')
            ->orderBy('created_at', 'desc');
        if (Schema::hasColumn('attendance', 'attendance_type')) {
            $attendanceQuery->where(function ($q) {
                $q->where('attendance_type', 'class')
                  ->orWhereNull('attendance_type');
            });
        }

        $attendanceRecords = $attendanceQuery->paginate($perPage)->withQueryString();

        $attendanceRecords->getCollection()->transform(function ($att) use ($todayBs) {
            // Get teacher name from loaded relationships
            $teacherName = 'Not Assigned';
            if ($att->subject && $att->subject->teacherAssignments->isNotEmpty()) {
                $firstAssignment = $att->subject->teacherAssignments->first();
                if ($firstAssignment->teacher && $firstAssignment->teacher->user) {
                    $teacherName = $firstAssignment->teacher->user->name;
                }
            }
            
            return [
                'date_ad' => $att->date?->format('Y-m-d') ?? Carbon::now()->format('Y-m-d'),
                'date_bs' => $att->date_bs ?? $todayBs,
                'course_name' => $att->subject?->subject_name ?? $att->subject?->name ?? 'General',
                'teacher_name' => $teacherName,
                'semester' => $att->subject?->semester ?? 'N/A',
                'present_count' => (int)$att->present_count ?? 0,
                'total_students' => (int)$att->total_count ?? 0,
            ];
        });

        return $attendanceRecords;
    }

    /**
     * Display notifications page.
     */
    public function notifications()
    {
        $notifications = auth()->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Mark a notification as read (AJAX).
     */
    public function markAsRead(Request $request)
    {
        $id = $request->input('id');

        if ($id === 'all') {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        }

        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        if (method_exists($notification, 'markAsRead')) {
            $notification->markAsRead();
        } else {
            $notification->read_at = now();
            $notification->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Return unread notifications count for the current user.
     */
    public function unreadCount()
    {
        $user = auth()->user();
        $count = $user ? $user->unreadNotifications()->count() : 0;

        // Breakdown by notification type (database notifications)
        $examCount = $user ? $user->unreadNotifications()->where('type', '\\App\\Notifications\\ExamNotification')->count() : 0;
        $attendanceNotifCount = $user ? $user->unreadNotifications()->where('type', '\\App\\Notifications\\AttendanceNotification')->count() : 0;

        // Additionally provide counts for new resources (created today)
        $today = Carbon::now()->toDateString();
        $noticeCount = \App\Models\Notice::whereDate('created_at', $today)->count();
        $noticeUpdatedCount = \App\Models\Notice::whereDate('updated_at', $today)->count();

        $documentCount = \App\Models\StudyMaterial::whereDate('created_at', $today)->count();
        $documentUpdatedCount = \App\Models\StudyMaterial::whereDate('updated_at', $today)->count();

        $subjectCount = \App\Models\Subject::whereDate('created_at', $today)->count();
        $subjectUpdatedCount = \App\Models\Subject::whereDate('updated_at', $today)->count();

        $galleryCount = \App\Models\Gallery::whereDate('created_at', $today)->count();
        $galleryUpdatedCount = \App\Models\Gallery::whereDate('updated_at', $today)->count();

        // Today's attendance: count distinct subjects with attendance marked today
        $todayAttendanceQuery = \App\Models\Attendance::whereDate('date', $today);
        if (Schema::hasColumn('attendance', 'attendance_type')) {
            $todayAttendanceQuery->where(function ($q) {
                $q->where('attendance_type', 'class')
                  ->orWhereNull('attendance_type');
            });
        }
        $todayAttendanceCount = $todayAttendanceQuery->distinct('subject_id')->count('subject_id');

        // Also provide counts of unread notifications for 'updated' actions where available
        $examUpdatedCount = $user ? $user->unreadNotifications()->where('type', '\\App\\Notifications\\ExamNotification')->where('data->action', 'updated')->count() : 0;
        $attendanceUpdatedCount = $user ? $user->unreadNotifications()->where('type', '\\App\\Notifications\\AttendanceNotification')->where('data->action', 'updated')->count() : 0;

        return response()->json([
            'unread' => $count,
            'breakdown' => [
                'exam_notifications' => $examCount,
                'exam_notifications_updated' => $examUpdatedCount,
                'attendance_notifications' => $attendanceNotifCount,
                'attendance_notifications_updated' => $attendanceUpdatedCount,
                'new_notices_today' => $noticeCount,
                'updated_notices_today' => $noticeUpdatedCount,
                'new_documents_today' => $documentCount,
                'updated_documents_today' => $documentUpdatedCount,
                'new_subjects_today' => $subjectCount,
                'updated_subjects_today' => $subjectUpdatedCount,
                'new_gallery_items_today' => $galleryCount,
                'updated_gallery_items_today' => $galleryUpdatedCount,
                'today_attendance_subjects' => $todayAttendanceCount,
            ],
        ]);
    }

    /**
     * Get grade distribution from all exam marks
     * Returns count of students for each grade
     */
    private function getGradeDistribution()
    {
        // Include marks from active, non-alumni students only
        $gradeDistribution = ExamMark::query()
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->where('students.status', 'active')
            ->where('students.is_alumni', 0)
            ->whereNull('students.deleted_at')
            ->whereNotNull('exam_marks.grade')
            ->where('exam_marks.grade', '!=', '')
            ->selectRaw('exam_marks.grade as grade, COUNT(*) as count')
            ->groupBy('exam_marks.grade')
            ->pluck('count', 'grade')
            ->toArray();

        // Ensure we have all 8 grade letters with default 0
        $grades = [
            'A+' => 0, 
            'A' => 0, 
            'B+' => 0, 
            'B' => 0, 
            'C+' => 0, 
            'C' => 0, 
            'D' => 0, 
            'F' => 0
        ];
        
        foreach ($gradeDistribution as $grade => $count) {
            if (isset($grades[$grade])) {
                $grades[$grade] = $count;
            }
        }

        return $grades;
    }

    /**
     * Get attendance summary (present, absent, late counts)
     */
    private function getAttendanceSummary()
    {
        $summary = $this->getActiveNonAlumniAttendanceQuery()->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN LOWER(attendance.status) = "present" THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN LOWER(attendance.status) = "absent" THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN LOWER(attendance.status) = "late" THEN 1 ELSE 0 END) as late_count
        ')->first();

        return [
            'total' => (int) ($summary->total ?? 0),
            'present' => (int) ($summary->present_count ?? 0),
            'absent' => (int) ($summary->absent_count ?? 0),
            'late' => (int) ($summary->late_count ?? 0),
        ];
    }

    /**
     * Get upcoming exams for admin dashboard
     */
    private function getUpcomingExams()
    {
        $today = Carbon::now()->toDateString();
        
        return Exam::where('exam_date', '>=', $today)
            ->orderBy('exam_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->exam_name,
                    'subject_name' => $exam->subject->subject_name ?? 'N/A',
                    'exam_date' => $exam->exam_date,
                    'exam_date_bs' => $exam->exam_date_bs,
                ];
            });
    }

    /**
     * Get today's classes for admin dashboard
     */
    private function getTodayClasses()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        // Get all subjects that have attendance marked today
        $todayAttendanceQuery = Attendance::whereDate('date', $today);
        if (Schema::hasColumn('attendance', 'attendance_type')) {
            $todayAttendanceQuery->where(function ($q) {
                $q->where('attendance_type', 'class')
                  ->orWhereNull('attendance_type');
            });
        }
        $todayAttendance = $todayAttendanceQuery->get()
            ->groupBy('subject_id')
            ->map(function ($records, $subjectId) {
                $subject = Subject::find($subjectId);
                
                $totalStudents = DB::table('subject_students')
                    ->where('subject_id', $subjectId)
                    ->whereExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('users')
                            ->whereColumn('users.id', 'subject_students.student_id')
                            ->where('users.role', 'student')
                            ->whereExists(function ($q) {
                                $q->select(DB::raw(1))
                                    ->from('students')
                                    ->whereColumn('students.user_id', 'users.id')
                                    ->where('students.status', 'active')
                                    ->where('students.is_alumni', 0);
                            });
                    })
                    ->count('student_id');
                
                $presentCount = $records->where('status', 'present')->count();
                $absentCount = $records->where('status', 'absent')->count();
                
                return [
                    'subject_id' => $subjectId,
                    'subject_name' => optional($subject)->subject_name ?? 'N/A',
                    'semester' => optional($subject)->semester ?? 'N/A',
                    'total_students' => $totalStudents,
                    'present_count' => $presentCount,
                    'absent_count' => $absentCount,
                    'attendance_rate' => $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 1) : 0,
                ];
            })
            ->values();

        return $todayAttendance;
    }
}
