<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Notice;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamMark;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $teacher = $user->teacher;
        
        if (!$teacher) {
            if ($user->role === 'teacher') {
                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'teacher_code' => 'TCH-' . $user->id,
                    'status' => 'active',
                ]);
            } else {
                return view('teacher.teacherdashboard', $this->getEmptyData($user));
            }
        }

        $teacher = $teacher->fresh();
        $teacherIds = array_values(array_unique([$teacher->id, $user->id]));

        // Get subjects from BOTH sources: pivot table and legacy teacher_id field
        $pivotAssignments = SubjectTeacher::whereIn('teacher_id', [$teacher->id, $user->id])
            ->with('subject')
            ->get();
        
        // Get from legacy teacher_id field (if column exists)
        $legacySubjects = collect();
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $legacySubjects = Subject::where('teacher_id', $teacher->id)->get();
        }
        
        // Combine both sources into arrays
        $allAssignments = [];
        
        foreach ($pivotAssignments as $assignment) {
            $allAssignments[] = [
                'type' => 'pivot',
                'assignment_id' => $assignment->id,
                'assignment' => $assignment,
                'subject_id' => $assignment->subject_id,
                'subject' => $assignment->subject,
            ];
        }
        
        foreach ($legacySubjects as $subject) {
            // Check if already added from pivot
            $exists = collect($allAssignments)->contains(function($item) use ($subject) {
                return $item['subject_id'] === $subject->id;
            });
            
            if (!$exists) {
                $allAssignments[] = [
                    'type' => 'legacy',
                    'assignment_id' => 'legacy_' . $subject->id,
                    'assignment' => null,
                    'subject_id' => $subject->id,
                    'subject' => $subject,
                ];
            }
        }
        
        $subjectAssignments = collect($allAssignments);
        
        $subjectCount = $subjectAssignments->count();
        
        $subjectIds = $subjectAssignments->pluck('subject_id')->toArray();

        // Filter: role='student', status='active', is_alumni=0
        // (Note: this count is a best-effort distinct count across all assigned subjects)
        $totalStudents = 0;
        if (!empty($subjectIds)) {
            $totalStudents = DB::table('subject_students as ss')
                ->join('students', 'ss.student_id', '=', 'students.id')
                ->whereIn('ss.subject_id', $subjectIds)
                ->where('students.status', 'active')
                ->where('students.is_alumni', 0)
                ->distinct('students.id')
                ->count('students.id');
        }

        $avgAttendance = $this->getTeacherAttendancePercentage($subjectIds);
        $attendanceSummary = $this->getAttendanceSummary($subjectIds);
        $attendanceChartData = $this->getAttendanceChartData($subjectIds);
        $gradeDistribution = $this->getGradeDistribution($subjectIds);
        
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
        
        $noticesCount = Notice::count();
        $upcomingExams = $this->getUpcomingExams($subjectIds);
        $todayClasses = $this->getTodayClasses($subjectIds);
        
        $subjects = $subjectAssignments->map(function ($item) {
            $subject = $item['subject'];
            $assignment = $item['assignment'];
            
            $studentCount = DB::table('subject_students as ss')
                ->join('students', 'ss.student_id', '=', 'students.id')
                ->where('ss.subject_id', $subject->id)
                ->where('students.status', 'active')
                ->where('students.is_alumni', 0)
                ->count();
            
            $attendanceStats = DB::table('attendance')
                ->join('students', 'attendance.student_id', '=', 'students.id')
                ->where('attendance.subject_id', $subject->id)
                ->where('students.status', 'active')
                ->where('students.is_alumni', 0)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN LOWER(attendance.status) = "present" THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN LOWER(attendance.status) = "absent" THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN LOWER(attendance.status) = "late" THEN 1 ELSE 0 END) as late_count
                ')
                ->first();
            
            $attendancePercentage = 0;
            if ($attendanceStats && $attendanceStats->total > 0) {
                $attendancePercentage = round(($attendanceStats->present_count / $attendanceStats->total) * 100, 1);
            }
            
            $marksStats = DB::table('exam_marks')
                ->join('students', 'exam_marks.student_id', '=', 'students.id')
                ->where('exam_marks.subject_id', $subject->id)
                ->where('students.status', 'active')
                ->where('students.is_alumni', 0)
                ->selectRaw('
                    COUNT(*) as total,
                    ROUND(AVG(marks_obtained), 2) as avg_marks,
                    MAX(marks_obtained) as max_marks,
                    MIN(marks_obtained) as min_marks
                ')
                ->first();
            
            return [
                'id' => $subject->id,
                'name' => $subject->subject_name,
                'code' => $subject->subject_code,
                'semester' => $assignment && $assignment->semester ? $assignment->semester : $subject->semester,
                'role' => $assignment && $assignment->role ? $assignment->role : null,
                'student_count' => $studentCount,
                'attendance' => [
                    'total' => (int) ($attendanceStats->total ?? 0),
                    'present' => (int) ($attendanceStats->present_count ?? 0),
                    'absent' => (int) ($attendanceStats->absent_count ?? 0),
                    'late' => (int) ($attendanceStats->late_count ?? 0),
                    'percentage' => $attendancePercentage,
                ],
                'marks' => [
                    'total_records' => (int) ($marksStats->total ?? 0),
                    'average' => (float) ($marksStats->avg_marks ?? 0),
                    'max' => (float) ($marksStats->max_marks ?? 0),
                    'min' => (float) ($marksStats->min_marks ?? 0),
                ],
            ];
        });

        return view('teacher.teacherdashboard', compact(
            'user',
            'teacher',
            'subjectCount',
            'totalStudents',
            'avgAttendance',
            'attendanceSummary',
            'attendanceChartData',
            'gradeDistribution',
            'noticesCount',
            'recentNotices',
            'upcomingExams',
            'todayClasses',
            'subjects'
        ));
    }

    private function getEmptyData($user)
    {
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

        $emptyAttendanceChart = [
            'labels' => [],
            'present' => [],
            'absent' => [],
            'late' => [],
        ];

        return [
            'user' => $user,
            'teacher' => null,
            'subjectCount' => 0,
            'totalStudents' => 0,
            'avgAttendance' => 0,
            'attendanceSummary' => ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0],
            'attendanceChartData' => [
                'weekly' => $emptyAttendanceChart,
                'monthly' => $emptyAttendanceChart,
                'semester' => $emptyAttendanceChart,
            ],
            'gradeDistribution' => ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
            'noticesCount' => Notice::count(),
            'recentNotices' => $recentNotices,
            'upcomingExams' => collect([]),
            'todayClasses' => collect([]),
            'subjects' => collect([]),
        ];
    }

    private function getTeacherAttendancePercentage($subjectIds, $teacherIds = [])
    {
        if (empty($subjectIds)) {
            return 0;
        }

        $attendance = DB::table('attendance')
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->whereIn('attendance.subject_id', $subjectIds)
            ->where('students.status', 'active')
            ->where('students.is_alumni', 0)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(attendance.status) = "present" THEN 1 ELSE 0 END) as present_count
            ')
            ->first();

        if (!$attendance || $attendance->total == 0) {
            return 0;
        }

        return round(($attendance->present_count / $attendance->total) * 100, 1);
    }

    private function getAttendanceSummary($subjectIds, $teacherIds = [])
    {
        if (empty($subjectIds)) {
            return ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
        }

        $summary = DB::table('attendance')
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->whereIn('attendance.subject_id', $subjectIds)
            ->where('students.status', 'active')
            ->where('students.is_alumni', 0)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(attendance.status) = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN LOWER(attendance.status) = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN LOWER(attendance.status) = "late" THEN 1 ELSE 0 END) as late_count
            ')
            ->first();

        return [
            'total' => (int) ($summary->total ?? 0),
            'present' => (int) ($summary->present_count ?? 0),
            'absent' => (int) ($summary->absent_count ?? 0),
            'late' => (int) ($summary->late_count ?? 0),
        ];
    }

    private function getAttendanceChartData($subjectIds, $teacherIds = [])
    {
        if (empty($subjectIds)) {
            $emptyAttendanceChart = [
                'labels' => [],
                'present' => [],
                'absent' => [],
                'late' => [],
            ];

            return [
                'weekly' => $emptyAttendanceChart,
                'monthly' => $emptyAttendanceChart,
                'semester' => $emptyAttendanceChart,
            ];
        }

        // Weekly Data (last 7 days)
        $weeklyData = $this->getAttendanceDataByPeriod($subjectIds, [], 7, 'day');
        
        // Monthly Data (last 6 months)
        $monthlyData = $this->getAttendanceDataByMonth($subjectIds, []);
        
        // Semester Data (last 180 days) - grouped by semester
        $semesterData = $this->getAttendanceDataBySemester($subjectIds, []);

        return [
            'weekly' => $weeklyData,
            'monthly' => $monthlyData,
            'semester' => $semesterData,
        ];
    }

    private function getAttendanceDataByPeriod($subjectIds, $teacherIds, $days, $groupBy = 'day')
    {
        $startDate = Carbon::now()->subDays($days);
        $labels = [];
        $presentPercentages = [];

        if ($groupBy === 'day') {
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::now()->subDays($i)->format('M d');
                
                $attendance = DB::table('attendance')
                    ->whereIn('subject_id', $subjectIds)
                    ->when(!empty($teacherIds), function ($query) use ($teacherIds) {
                        $query->whereIn('teacher_id', $teacherIds);
                    })
                    ->whereDate('date', $date)
                    ->selectRaw('
                        COUNT(*) as total_count,
                        SUM(CASE WHEN LOWER(status) = "present" THEN 1 ELSE 0 END) as present_count,
                        SUM(CASE WHEN LOWER(status) = "absent" THEN 1 ELSE 0 END) as absent_count,
                        SUM(CASE WHEN LOWER(status) = "late" THEN 1 ELSE 0 END) as late_count
                    ')
                    ->first();
                
                $totalCount = (int) ($attendance->total_count ?? 0);
                $presentCount = (int) ($attendance->present_count ?? 0);
                $percentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;
                $presentPercentages[] = $percentage;
            }
        }

        return [
            'labels' => $labels,
            // For charting we expose present percentages on the `present` key to match existing view usage.
            'present' => $presentPercentages,
            'absent' => [],
            'late' => [],
        ];
    }

    private function getAttendanceDataBySemester($subjectIds, $teacherIds)
    {
        // "Semester" period should reflect academic semesters of assigned subjects,
        // not time buckets. Only show semesters that exist in the assigned subjects list.
        $semesterList = Subject::query()
            ->whereIn('id', $subjectIds)
            ->whereNotNull('semester')
            ->selectRaw('DISTINCT CAST(semester AS UNSIGNED) as semester')
            ->orderBy('semester', 'asc')
            ->pluck('semester')
            ->filter()
            ->values()
            ->toArray();

        if (empty($semesterList)) {
            return [
                'labels' => [],
                'present' => [],
                'absent' => [],
                'late' => [],
            ];
        }

        $rows = DB::table('attendance as a')
            ->join('subjects as s', 'a.subject_id', '=', 's.id')
            ->whereIn('a.subject_id', $subjectIds)
            ->when(!empty($teacherIds), function ($query) use ($teacherIds) {
                $query->whereIn('a.teacher_id', $teacherIds);
            })
            ->whereNotNull('s.semester')
            ->selectRaw('CAST(s.semester AS UNSIGNED) as semester, COUNT(*) as total_count, SUM(CASE WHEN LOWER(a.status) = "present" THEN 1 ELSE 0 END) as present_count')
            ->groupByRaw('CAST(s.semester AS UNSIGNED)')
            ->get()
            ->keyBy('semester');

        $labels = [];
        $presentPercentages = [];

        foreach ($semesterList as $semester) {
            $labels[] = 'Semester ' . $semester;

            $row = $rows->get($semester);
            $totalCount = (int) ($row->total_count ?? 0);
            $presentCount = (int) ($row->present_count ?? 0);
            $presentPercentages[] = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;
        }

        return [
            'labels' => $labels,
            'present' => $presentPercentages,
            'absent' => [],
            'late' => [],
        ];
    }

    private function getAttendanceDataByMonth($subjectIds, $teacherIds)
    {
        $from = Carbon::now()->subMonths(5)->startOfMonth()->toDateString();
        $to = Carbon::now()->endOfMonth()->toDateString();

        $rows = DB::table('attendance')
            ->whereIn('subject_id', $subjectIds)
            ->when(!empty($teacherIds), function ($query) use ($teacherIds) {
                $query->whereIn('teacher_id', $teacherIds);
            })
            ->whereBetween('date', [$from, $to])
            ->selectRaw('
                DATE_FORMAT(date, "%Y-%m") as ym,
                COUNT(*) as total_count,
                SUM(CASE WHEN LOWER(status) = "present" THEN 1 ELSE 0 END) as present_count
            ')
            ->groupBy('ym')
            ->havingRaw('COUNT(*) > 0')
            ->orderBy('ym', 'asc')
            ->get();

        $labels = [];
        $presentPercentages = [];
        $labelMeta = [];

        foreach ($rows as $row) {
            $ym = (string) ($row->ym ?? '');
            if ($ym === '') {
                continue;
            }

            $dt = Carbon::createFromFormat('Y-m', $ym);
            $labels[] = $dt->format('M');
            $labelMeta[] = ['month' => $dt->month, 'year' => $dt->year];

            $totalCount = (int) ($row->total_count ?? 0);
            $presentCount = (int) ($row->present_count ?? 0);
            $presentPercentages[] = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : 0;
        }

        // If the chart spans multiple years and would repeat month labels (e.g., Jan twice),
        // append the year only for duplicated months to avoid ambiguity.
        $counts = array_count_values($labels);
        if (!empty($labels)) {
            foreach ($labels as $i => $label) {
                if (($counts[$label] ?? 0) > 1 && isset($labelMeta[$i]['year'])) {
                    $labels[$i] = $label . ' ' . $labelMeta[$i]['year'];
                }
            }
        }

        return [
            'labels' => $labels,
            'present' => $presentPercentages,
            'absent' => [],
            'late' => [],
        ];
    }

    private function getGradeDistribution($subjectIds)
    {
        if (empty($subjectIds)) {
            return ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
        }

        $gradeDistribution = DB::table('exam_marks')
            ->join('students', 'exam_marks.student_id', '=', 'students.id')
            ->whereIn('exam_marks.subject_id', $subjectIds)
            ->where('students.status', 'active')
            ->where('students.is_alumni', 0)
            ->whereNotNull('exam_marks.grade')
            ->where('exam_marks.grade', '!=', '')
            ->select('exam_marks.grade')
            ->get()
            ->groupBy('grade')
            ->map(function($group) { return $group->count(); })
            ->toArray();

        $grades = ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
        
        foreach ($gradeDistribution as $grade => $count) {
            if (isset($grades[$grade])) {
                $grades[$grade] = $count;
            }
        }

        return $grades;
    }

    private function getUpcomingExams($subjectIds)
    {
        if (empty($subjectIds)) {
            return collect([]);
        }

        $today = Carbon::now()->toDateString();
        
        return Exam::whereIn('subject_id', $subjectIds)
            ->where('exam_date', '>=', $today)
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

    private function getTodayClasses($subjectIds)
    {
        if (empty($subjectIds)) {
            return collect([]);
        }

        $today = Carbon::now()->toDateString();
        
        $subjectsMap = Subject::whereIn('id', $subjectIds)->get()->keyBy('id');
        
        $todayAttendance = Attendance::whereIn('subject_id', $subjectIds)
            ->whereDate('date', $today)
            ->whereHas('student', function ($query) {
                $query->where('status', 'active')
                      ->where('is_alumni', 0);
            })
            ->get()
            ->groupBy('subject_id')
            ->map(function ($records, $subjectId) use ($subjectsMap) {
                $subject = $subjectsMap->get($subjectId);
                
                $totalStudents = DB::table('subject_students as ss')
                    ->join('students as s', 'ss.student_id', '=', 's.id')
                    ->where('ss.subject_id', $subjectId)
                    ->where('s.status', 'active')
                    ->where('s.is_alumni', 0)
                    ->count();
                
                $presentCount = $records->filter(function($r) { return strtolower($r->status) === 'present'; })->count();
                $absentCount = $records->filter(function($r) { return strtolower($r->status) === 'absent'; })->count();
                
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
