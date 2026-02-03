<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics and charts.
     * Shows attendance percentage from all users across all semesters.
     */
    public function index(Request $request)
    {
        // Prepare dashboard stats from database where possible
        $totalStudents = Schema::hasTable('users') ? DB::table('users')->where('role', 'student')->count() : 0;
        $teachers = Schema::hasTable('users') ? DB::table('users')->where('role', 'teacher')->count() : 0;
        $parents = Schema::hasTable('users') ? DB::table('users')->where('role', 'parent')->count() : 0;
        
        // Active Courses: Count from subjects table
        $courses = 0;
        if (Schema::hasTable('subjects')) {
            if (Schema::hasColumn('subjects', 'status')) {
                $courses = DB::table('subjects')->where('status', 'active')->count();
            } else {
                $courses = DB::table('subjects')->count();
            }
        }

        // Average attendance: OLD logic - percent of present records from all records
        $avgAttendance = null;
        
        if (Schema::hasTable('attendance')) {
            // Count all attendance records
            $totalRecords = DB::table('attendance')->count();
            
            // Count present records
            $presentRecords = DB::table('attendance')
                ->where('status', 'present')
                ->count();
            
            $avgAttendance = $totalRecords > 0 ? round(($presentRecords / $totalRecords) * 100, 1) : null;
        }

        // Recent Activities: Get real audit logs from admin actions
        $recentActivities = [];
        
        // Helper function to format time as relative string
        $formatTime = function($timestamp) {
            if (!$timestamp) return 'Recently';
            try {
                return \Carbon\Carbon::parse($timestamp)->diffForHumans();
            } catch (\Exception $e) {
                return 'Recently';
            }
        };
        
        // Get real audit logs from the audit_logs table
        if (Schema::hasTable('audit_logs')) {
            $auditLogs = DB::table('audit_logs')
                ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
                ->select(
                    'audit_logs.*',
                    'users.name as user_name',
                    'users.role as user_role'
                )
                ->whereNotNull('audit_logs.timestamp')
                ->orderBy('audit_logs.timestamp', 'desc')
                ->limit(10)
                ->get();
            
            foreach ($auditLogs as $log) {
                $moduleIcon = match($log->module) {
                    'Student' => 'bi-person-plus',
                    'Teacher' => 'bi-briefcase',
                    'Parent' => 'bi-person-vcard',
                    'Attendance' => 'bi-clipboard-check',
                    'Course' => 'bi-book-half',
                    'Notice' => 'bi-megaphone',
                    'Report' => 'bi-file-earmark-pdf',
                    default => 'bi-activity'
                };
                
                $moduleBg = match($log->module) {
                    'Student' => 'bg-blue-100',
                    'Teacher' => 'bg-green-100',
                    'Parent' => 'bg-purple-100',
                    'Attendance' => 'bg-red-100',
                    'Course' => 'bg-blue-100',
                    'Notice' => 'bg-amber-100',
                    'Report' => 'bg-indigo-100',
                    default => 'bg-gray-100'
                };
                
                $iconColor = match($log->module) {
                    'Student' => 'text-blue-600',
                    'Teacher' => 'text-green-600',
                    'Parent' => 'text-purple-600',
                    'Attendance' => 'text-red-600',
                    'Course' => 'text-blue-600',
                    'Notice' => 'text-amber-600',
                    'Report' => 'text-indigo-600',
                    default => 'text-gray-600'
                };
                
                $recentActivities[] = [
                    'id' => $log->id,
                    'action' => $log->action,
                    'activity' => $log->action,
                    'title' => $log->action,
                    'subtitle' => 'By: ' . ($log->user_name ?? 'System') . ' (' . ($log->module ?? 'General') . ')',
                    'user_name' => $log->user_name ?? 'System',
                    'time_raw' => $log->timestamp,
                    'time' => $formatTime($log->timestamp),
                    'icon' => $moduleIcon,
                    'icon_bg' => $moduleBg,
                    'icon_color' => $iconColor,
                    'module' => $log->module
                ];
            }
        }
        
        // Fallback: If no audit logs, show system activities from other tables
        if (empty($recentActivities)) {
            // Activity 1: Recent student enrollments
            if (Schema::hasTable('users')) {
                $recentStudents = DB::table('users')
                    ->where('role', 'student')
                    ->whereNotNull('created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
                foreach ($recentStudents as $u) {
                    $recentActivities[] = [
                        'title' => 'New student enrollment',
                        'subtitle' => $u->name . ' enrolled',
                        'time_raw' => $u->created_at,
                        'time' => $formatTime($u->created_at),
                        'icon' => 'bi-person-plus',
                        'icon_bg' => 'bg-blue-100',
                        'icon_color' => 'text-blue-600',
                        'module' => 'Student'
                    ];
                }
            }
            
            // Activity 2: Add recent notices as activities
            if (Schema::hasTable('notices')) {
                $recentNotices = DB::table('notices')
                    ->whereNotNull('created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(2)
                    ->get();
                foreach ($recentNotices as $notice) {
                    $recentActivities[] = [
                        'title' => 'New notice published',
                        'subtitle' => $notice->title ?? $notice->heading ?? 'Notice',
                        'time_raw' => $notice->created_at,
                        'time' => $formatTime($notice->created_at),
                        'icon' => 'bi-megaphone',
                        'icon_bg' => 'bg-amber-100',
                        'icon_color' => 'text-amber-600',
                        'module' => 'Notice'
                    ];
                }
            }
            
            // Activity 3: Recent attendance records
            if (Schema::hasTable('attendance')) {
                $recentAttendance = DB::table('attendance')
                    ->whereNotNull('created_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(2)
                    ->get();
                foreach ($recentAttendance as $att) {
                    $recentActivities[] = [
                        'title' => 'Attendance recorded',
                        'subtitle' => 'Student ID: ' . $att->student_id . ' - ' . ($att->status ?? 'Present'),
                        'time_raw' => $att->created_at,
                        'time' => $formatTime($att->created_at),
                        'icon' => 'bi-clipboard-check',
                        'icon_bg' => 'bg-red-100',
                        'icon_color' => 'text-red-600',
                        'module' => 'Attendance'
                    ];
                }
            }
        }

        // Sort activities by time (most recent first) using time_raw for proper sorting
        $recentActivities = collect($recentActivities)->sortByDesc('time_raw')->take(5)->values()->toArray();

        // Notices: Get from notices table
        $notices = collect();
        if (Schema::hasTable('notices')) {
            $notices = DB::table('notices')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Prepare chart datasets: grade distribution (pie chart)
        $gradeDistribution = $this->getGradeDistribution();
        $roleLabels = collect(['A Grade', 'B Grade', 'C Grade', 'D Grade', 'F Grade']);
        $roleValues = collect([
            $gradeDistribution['A'] ?? 0,
            $gradeDistribution['B'] ?? 0,
            $gradeDistribution['C'] ?? 0,
            $gradeDistribution['D'] ?? 0,
            $gradeDistribution['F'] ?? 0
        ]);

        // Attendance percentage by month (for single line chart)
        // Shows attendance percentage from all users across all semesters
        $attendancePercentage = $this->getAttendancePercentageByMonth();

        // Recent Notices: Get from notices table for sidebar
        $recentNotices = collect();
        if (Schema::hasTable('notices')) {
            $recentNotices = DB::table('notices')
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        // Recent Attendance: Get from attendance table for bottom section
        $recentAttendance = collect();
        if (Schema::hasTable('attendance')) {
            // Aggregate attendance by subject/teacher for today's date
            // Use date_bs (BS date) since that's what the frontend uses
            $today_bs = date('Y-m-d');

            $recentAttendance = DB::table('attendance')
                ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
                // join teacher referenced on attendance
                ->leftJoin('teachers as attendance_teachers', 'attendance.teacher_id', '=', 'attendance_teachers.id')
                ->leftJoin('users as attendance_users', 'attendance_teachers.user_id', '=', 'attendance_users.id')
                // join teacher referenced on subject (fallback)
                ->leftJoin('teachers as subject_teachers', 'subjects.teacher_id', '=', 'subject_teachers.id')
                ->leftJoin('users as subject_users', 'subject_teachers.user_id', '=', 'subject_users.id')
                ->where('attendance.date_bs', $today_bs)
                ->selectRaw(
                    "attendance.date_bs as date_bs, attendance.date as date, attendance.subject_id as subject_id, subjects.subject_name as course_name, subjects.semester as semester, COALESCE(attendance.teacher_id, subjects.teacher_id) as teacher_id, COALESCE(attendance_users.name, subject_users.name) as teacher_name, COUNT(DISTINCT attendance.student_id) as total_students, SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) as present_count"
                )
                ->groupBy('attendance.date_bs', 'attendance.date', 'attendance.subject_id', DB::raw('COALESCE(attendance.teacher_id, subjects.teacher_id)'), 'subjects.subject_name', 'subjects.semester', DB::raw('COALESCE(attendance_users.name, subject_users.name)'))
                ->orderBy('attendance.date_bs', 'desc')
                ->get();
        }

        // New Students: Get recently added students
        $newStudents = collect();
        if (Schema::hasTable('users')) {
            $newStudents = DB::table('users')
                ->where('role', 'student')
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('admin.dashboard', compact(
            'totalStudents','teachers','parents','courses','avgAttendance',
            'recentActivities','notices','roleLabels','roleValues',
            'attendancePercentage','recentNotices','recentAttendance','newStudents'
        ));
    }

    /**
     * Get grade distribution from marks
     */
    private function getGradeDistribution()
    {
        if (!Schema::hasTable('marks')) {
            return [
                'A' => 28,
                'B' => 35,
                'C' => 22,
                'D' => 10,
                'F' => 5
            ];
        }

        $query = DB::table('marks')
            ->whereNotNull('marks_obtained')
            ->whereNotNull('full_marks');

        $total = $query->clone()->count();

        if ($total === 0) {
            return [
                'A' => 28,
                'B' => 35,
                'C' => 22,
                'D' => 10,
                'F' => 5
            ];
        }

        // Calculate grades based on percentage
        $aGrade = $query->clone()->whereRaw('(marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 90')->count();
        $bGrade = $query->clone()->whereRaw('(marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 80 AND (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 90')->count();
        $cGrade = $query->clone()->whereRaw('(marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 70 AND (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 80')->count();
        $dGrade = $query->clone()->whereRaw('(marks_obtained / CAST(full_marks AS FLOAT) * 100) >= 60 AND (marks_obtained / CAST(full_marks AS FLOAT) * 100) < 70')->count();
        $fGrade = $query->clone()->whereRaw('(marks_obtained / CAST(full_marks AS FLOAT) * 100) < 60')->count();

        return [
            'A' => $total > 0 ? round(($aGrade / $total) * 100) : 28,
            'B' => $total > 0 ? round(($bGrade / $total) * 100) : 35,
            'C' => $total > 0 ? round(($cGrade / $total) * 100) : 22,
            'D' => $total > 0 ? round(($dGrade / $total) * 100) : 10,
            'F' => $total > 0 ? round(($fGrade / $total) * 100) : 5
        ];
    }

    /**
     * Get monthly attendance percentage data for chart.
     * OLD LOGIC: Calculates from all attendance records.
     * Formula: (present_records / total_records) * 100
     * 
     * @param int $year Year to fetch data for (default: current year)
     * @return array
     */
    public function getAttendancePercentageByMonth($year = null)
    {
        $year = $year ?? date('Y');
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $labels = [];
        $data = [];
        $details = [];

        // Always show all 12 months (Jan-Dec) from all users across all semesters
        for ($monthNum = 1; $monthNum <= 12; $monthNum++) {
            $monthName = $monthNames[$monthNum - 1];
            $monthPadded = str_pad($monthNum, 2, '0', STR_PAD_LEFT);
            $period = "$year-$monthPadded";
            
            $labels[] = $monthName;

            // Calculate attendance details for this month
            // OLD LOGIC: Count all records directly
            if (Schema::hasTable('attendance')) {
                // Total records in this month
                $total = DB::table('attendance')
                    ->whereRaw("STRFTIME('%Y-%m', date) = '$period'")
                    ->count();

                // Present records in this month
                $present = DB::table('attendance')
                    ->where('status', 'present')
                    ->whereRaw("STRFTIME('%Y-%m', date) = '$period'")
                    ->count();

                // Absent records in this month
                $absent = DB::table('attendance')
                    ->where('status', 'absent')
                    ->whereRaw("STRFTIME('%Y-%m', date) = '$period'")
                    ->count();

                // Formula: present records / total records * 100
                $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                $data[] = $percentage;
                
                // Store detailed data for tooltip
                $details[] = [
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'percentage' => $percentage
                ];
            } else {
                $data[] = 0;
                $details[] = [
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'percentage' => 0
                ];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'details' => $details
        ];
    }

    /**
     * Get attendance data via AJAX for chart updates.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attendanceData(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $data = $this->getAttendancePercentageByMonth($year);

        return response()->json([
            'success' => true,
            'labels' => $data['labels'],
            'data' => $data['data']
        ]);
    }
}
