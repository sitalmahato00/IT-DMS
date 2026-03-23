<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Display reports page with data from database
     */
    public function index(Request $request)
    {
        try {
            Log::info('Reports page accessed - Starting data fetch');
            
            $perPage = intval($request->get('per_page', 25)) ?: 25;
            $topLimit = intval($request->get('top_limit', 10)) ?: 10;
            
            // Get filter parameters
            $program = $request->get('program', '');
            $semester = $request->get('semester', '');
            $subject = $request->get('subject', '');
            $studentStatus = $request->get('student_status', '');
            $year = $request->get('year', date('Y'));
            $month = $request->get('month', '');
            $search = $request->get('search', '');

            // Get available programs (departments)
            $programs = DB::table('users')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->distinct()
                ->pluck('department')
                ->filter()
                ->sort()
                ->values();

            // Get available semesters from students table
            $semesters = DB::table('students')
                ->distinct()
                ->pluck('semester')
                ->filter()
                ->sort()
                ->values();

            // Get available subjects
            $subjects = DB::table('subjects')
                ->select('id', 'subject_name', 'subject_code', 'semester')
                ->orderBy('subject_name')
                ->get();

            // Get available years from attendance
            $availableYears = DB::table('attendance')
                ->selectRaw('YEAR(date) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');
            
            if ($availableYears->isEmpty()) {
                $availableYears = collect([date('Y')]);
            }

            // Get KPI Statistics
            $kpiStats = $this->getKPIStats($program, $semester, $studentStatus);

            // Get attendance statistics
            $attendanceStats = $this->getAttendanceStats($semester);

            // Get marks statistics
            $marksStats = $this->getMarksStats($semester, $subject);

            // Get student progress statistics
            $progressStats = $this->getProgressStats($semester);

            // Get monthly attendance trends
            $monthlyAttendance = $this->getMonthlyAttendanceTrends($year);

            // Get grade distribution
            $gradeDistribution = $this->getGradeDistribution($semester);

            // Get students per semester for bar chart
            $studentsPerSemester = $this->getStudentsPerSemester($program, $studentStatus);

            // Get elective distribution
            $electiveDistribution = $this->getElectiveDistribution($semester);

            // Get marks per subject for column chart
            $marksPerSubject = $this->getMarksPerSubject($semester);

            // Get top performing students (paginated)
            $topStudents = $this->getTopStudents($semester, $topLimit);

            // Get subject performance
            $subjectPerformance = $this->getSubjectPerformance($semester, $perPage);

            // Get detailed student report data
            $studentReport = $this->getStudentReport($program, $semester, $subject, $studentStatus, $year, $month, $search, $perPage);

            return view('admin.reports', [
                'programs' => $programs,
                'semesters' => $semesters,
                'subjects' => $subjects,
                'availableYears' => $availableYears,
                'kpiStats' => $kpiStats,
                'attendanceStats' => $attendanceStats,
                'marksStats' => $marksStats,
                'progressStats' => $progressStats,
                'monthlyAttendance' => $monthlyAttendance,
                'gradeDistribution' => $gradeDistribution,
                'studentsPerSemester' => $studentsPerSemester,
                'electiveDistribution' => $electiveDistribution,
                'marksPerSubject' => $marksPerSubject,
                'topStudents' => $topStudents,
                'subjectPerformance' => $subjectPerformance,
                'studentReport' => $studentReport,
                // Filter values
                'program' => $program,
                'semester' => $semester,
                'subject' => $subject,
                'studentStatus' => $studentStatus,
                'year' => $year,
                'month' => $month,
                'search' => $search
            ]);

        } catch (\Exception $e) {
            Log::error('Reports error: ' . $e->getMessage());
            Log::error('Reports stack trace: ' . $e->getTraceAsString());
            
            // Return view with empty data on error
            return view('admin.reports', [
                'programs' => collect([]),
                'semesters' => collect([]),
                'subjects' => collect([]),
                'availableYears' => collect([date('Y')]),
                'kpiStats' => [
                    'totalStudents' => 0,
                    'activeStudents' => 0,
                    'alumni' => 0,
                    'attendanceRate' => 0,
                    'avgMarks' => 0,
                    'electivesChosen' => 0
                ],
                'attendanceStats' => ['avg' => 0, 'total' => 0, 'present' => 0, 'absent' => 0],
                'marksStats' => ['avg' => 0, 'total' => 0],
                'progressStats' => ['completion' => 0],
                'monthlyAttendance' => ['months' => [], 'present' => [], 'absent' => [], 'leave' => []],
                'gradeDistribution' => [],
                'studentsPerSemester' => ['semesters' => [], 'counts' => []],
                'electiveDistribution' => [],
                'marksPerSubject' => ['subjects' => [], 'marks' => []],
                'topStudents' => collect([]),
                'subjectPerformance' => collect([]),
                'studentReport' => collect([]),
                'program' => '',
                'semester' => '',
                'subject' => '',
                'studentStatus' => '',
                'year' => date('Y'),
                'month' => '',
                'search' => ''
            ]);
        }
    }

    /**
     * Get KPI Statistics
     */
    private function getKPIStats($program, $semester, $studentStatus)
    {
        $studentQuery = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'student');

        if (!empty($program)) {
            $studentQuery->where('users.department', $program);
        }

        if (!empty($semester)) {
            $studentQuery->where('students.semester', $semester);
        }

        if (!empty($studentStatus)) {
            if ($studentStatus === 'alumni') {
                $studentQuery->where('students.is_alumni', true);
            } else {
                $studentQuery->where('students.status', $studentStatus);
            }
        }

        $totalStudents = $studentQuery->clone()->count();
        $activeStudents = $studentQuery->clone()->where('students.status', 'active')->where('students.is_alumni', false)->count();
        $alumni = $studentQuery->clone()->where('students.is_alumni', true)->count();

        // Calculate attendance rate
        $attendanceQuery = DB::table('attendance')
            ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id');

        if (!empty($semester)) {
            $attendanceQuery->where('students.semester', $semester);
        }

        $totalAttendance = $attendanceQuery->clone()->count();
        $presentAttendance = $attendanceQuery->clone()->where('attendance.status', 'present')->count();
        $attendanceRate = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) : 0;

        // Calculate average marks
        $marksQuery = DB::table('exam_marks')
            ->leftJoin('students', 'exam_marks.student_id', '=', 'students.id')
            ->whereNotNull('exam_marks.percentage');

        if (!empty($semester)) {
            $marksQuery->where('students.semester', $semester);
        }

        $avgMarks = $marksQuery->avg(DB::raw('CAST(exam_marks.percentage AS DECIMAL(5,2))'));
        $avgMarks = round($avgMarks ?? 0, 1);

        // Get electives chosen count
        $electiveQuery = DB::table('elective_enrollments')
            ->where('status', 'approved');

        if (!empty($semester)) {
            $electiveQuery->where('semester', $semester);
        }

        $electivesChosen = $electiveQuery->count();

        return [
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'alumni' => $alumni,
            'attendanceRate' => $attendanceRate,
            'avgMarks' => $avgMarks,
            'electivesChosen' => $electivesChosen
        ];
    }

    /**
     * Get students per semester for bar chart
     */
    private function getStudentsPerSemester($program, $studentStatus)
    {
        $query = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'student');

        if (!empty($program)) {
            $query->where('users.department', $program);
        }

        if (!empty($studentStatus)) {
            if ($studentStatus === 'alumni') {
                $query->where('students.is_alumni', true);
            } elseif ($studentStatus === 'active') {
                $query->where('students.status', 'active')->where('students.is_alumni', false);
            }
        }

        $data = $query
            ->select('students.semester', DB::raw('count(*) as count'))
            ->groupBy('students.semester')
            ->orderBy('students.semester')
            ->get();

        if ($data->isEmpty()) {
            // Return sample data if no actual data
            return [
                'semesters' => ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5', 'Sem 6', 'Sem 7', 'Sem 8'],
                'counts' => [45, 52, 48, 55, 42, 38, 35, 30]
            ];
        }

        return [
            'semesters' => $data->pluck('semester')->toArray(),
            'counts' => $data->pluck('count')->toArray()
        ];
    }

    /**
     * Get elective distribution for pie chart
     */
    private function getElectiveDistribution($semester)
    {
        $query = DB::table('elective_enrollments')
            ->leftJoin('subjects', 'elective_enrollments.subject_id', '=', 'subjects.id')
            ->where('elective_enrollments.status', 'approved');

        if (!empty($semester)) {
            $query->where('elective_enrollments.semester', $semester);
        }

        $data = $query
            ->select('subjects.subject_name', DB::raw('count(*) as count'))
            ->groupBy('subjects.subject_name')
            ->orderByDesc('count')
            ->limit(6)
            ->get();

        if ($data->isEmpty()) {
            // Return sample data if no actual data
            return [
                'labels' => ['Web Tech', 'AI/ML', 'Cyber Security', 'Cloud Computing', 'Mobile App', 'Data Science'],
                'data' => [25, 20, 18, 15, 12, 10]
            ];
        }

        return [
            'labels' => $data->pluck('subject_name')->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }

    /**
     * Get marks per subject for column chart
     */
    private function getMarksPerSubject($semester)
    {
        $query = DB::table('exam_marks')
            ->leftJoin('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->leftJoin('students', 'exam_marks.student_id', '=', 'students.id')
            ->whereNotNull('exam_marks.percentage');

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        $data = $query
            ->select('subjects.subject_name', DB::raw('AVG(CAST(exam_marks.percentage AS DECIMAL(5,2))) as avg_marks'))
            ->groupBy('subjects.subject_name')
            ->orderByDesc('avg_marks')
            ->limit(8)
            ->get();

        if ($data->isEmpty()) {
            // Return sample data if no actual data
            return [
                'subjects' => ['Data Structures', 'Algorithms', 'Database', 'Operating Systems', 'Networking', 'Web Tech'],
                'marks' => [85, 78, 82, 75, 80, 88]
            ];
        }

        return [
            'subjects' => $data->pluck('subject_name')->toArray(),
            'marks' => $data->map(fn($item) => round($item->avg_marks, 1))->toArray()
        ];
    }

    /**
     * Get detailed student report
     */
    private function getStudentReport($program, $semester, $subject, $studentStatus, $year, $month, $search, $perPage)
    {
        $query = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', function($join) use ($subject) {
                $join->on('subjects.semester', '=', 'students.semester');
                if (!empty($subject)) {
                    $join->where('subjects.id', $subject);
                }
            })
            ->leftJoin('exam_marks', function($join) use ($subject) {
                $join->on('exam_marks.student_id', '=', 'students.id');
                if (!empty($subject)) {
                    $join->on('exam_marks.subject_id', '=', 'subjects.id');
                }
            })
            ->where('users.role', 'student');

        if (!empty($program)) {
            $query->where('users.department', $program);
        }

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        if (!empty($studentStatus)) {
            if ($studentStatus === 'alumni') {
                $query->where('students.is_alumni', true);
            } else {
                $query->where('students.status', $studentStatus);
            }
        }

        // Note: Year/month filtering is applied to the studentsQuery below
        // as the attendance table join is needed

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('students.roll_no', 'like', "%{$search}%")
                  ->orWhere('students.registration_number', 'like', "%{$search}%");
            });
        }

        // Get attendance percentage per student
        $studentsQuery = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'subjects.semester', '=', 'students.semester')
            ->leftJoin('attendance', 'attendance.student_id', '=', 'students.id')
            ->where('users.role', 'student');

        if (!empty($program)) {
            $studentsQuery->where('users.department', $program);
        }

        if (!empty($semester)) {
            $studentsQuery->where('students.semester', $semester);
        }

        if (!empty($studentStatus)) {
            if ($studentStatus === 'alumni') {
                $studentsQuery->where('students.is_alumni', true);
            } else {
                $studentsQuery->where('students.status', $studentStatus);
            }
        }

        // Apply year and month filters to the attendance join
        if (!empty($year)) {
            $studentsQuery->whereYear('attendance.date', $year);
        }

        if (!empty($month)) {
            $studentsQuery->whereMonth('attendance.date', $month);
        }

        if (!empty($search)) {
            $studentsQuery->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('students.roll_no', 'like', "%{$search}%")
                  ->orWhere('students.registration_number', 'like', "%{$search}%");
            });
        }

        $studentsData = $studentsQuery
            ->select(
                'students.id',
                'students.roll_no',
                'students.registration_number',
                'students.semester',
                'students.status',
                'students.is_alumni',
                'users.name',
                'users.department',
                DB::raw('(SELECT subject_name FROM subjects WHERE subjects.semester = students.semester LIMIT 1) as subject_name'),
                DB::raw('(SELECT ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 1) FROM attendance WHERE student_id = students.id) as attendance_percentage'),
                DB::raw('(SELECT AVG(CAST(percentage AS DECIMAL(5,2))) FROM exam_marks WHERE student_id = students.id) as avg_marks')
            )
            ->groupBy('students.id', 'students.roll_no', 'students.registration_number', 'students.semester', 'students.status', 'students.is_alumni', 'users.name', 'users.department')
            ->orderBy('students.roll_no');

        return $studentsData->paginate($perPage);
    }

    /**
     * Get attendance statistics
     */
    private function getAttendanceStats($semester)
    {
        $query = DB::table('attendance')
            ->leftJoin('students', 'attendance.student_id', '=', 'students.id');

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        $total = $query->count();
        $present = $query->clone()->where('attendance.status', 'present')->count();
        $absent = $query->clone()->where('attendance.status', 'absent')->count();

        $avg = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'avg' => $avg,
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'leave' => 0,
            'change' => 5
        ];
    }

    /**
     * Get marks statistics
     */
    private function getMarksStats($semester, $subject)
    {
        $query = DB::table('exam_marks')
            ->leftJoin('students', 'exam_marks.student_id', '=', 'students.id')
            ->whereNotNull('exam_marks.percentage');

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        if (!empty($subject)) {
            $query->where('exam_marks.subject_id', $subject);
        }

        $total = $query->count();
        $avgMarks = $query->clone()->avg(DB::raw('CAST(exam_marks.percentage AS DECIMAL(5,2))'));
        $avg = round($avgMarks, 1);

        return [
            'avg' => $avg,
            'total' => $total,
            'change' => 3
        ];
    }

    /**
     * Get student progress statistics (course completion rate)
     */
    private function getProgressStats($semester)
    {
        $query = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'student');

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        $totalStudents = $query->clone()->count();
        
        // Calculate completion based on students with exam marks
        $studentsWithMarksQuery = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('exam_marks', 'students.id', '=', 'exam_marks.student_id')
            ->where('users.role', 'student')
            ->whereNotNull('exam_marks.id');
        
        if (!empty($semester)) {
            $studentsWithMarksQuery->where('students.semester', $semester);
        }
        
        $studentsWithMarks = $studentsWithMarksQuery
            ->distinct('students.id')
            ->count();
        
        $completion = $totalStudents > 0 ? round(($studentsWithMarks / $totalStudents) * 100, 1) : 0;

        return [
            'completion' => $completion,
            'total' => $totalStudents,
            'change' => 2
        ];
    }

    /**
     * Get monthly attendance trends
     */
    private function getMonthlyAttendanceTrends($year = null)
    {
        $months = [];
        $present = [];
        $absent = [];

        // Get the current year, but fallback to any year with data
        $latestYear = DB::table('attendance')
            ->orderByRaw('YEAR(date) DESC')
            ->limit(1)
            ->pluck('date')
            ->first();
        
        $selectedYear = $year ?? ($latestYear ? date('Y', strtotime($latestYear)) : date('Y'));

        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;

            // Get attendance data for each month
            $totalPresent = DB::table('attendance')
                ->whereRaw("YEAR(date) = ? AND MONTH(date) = ? AND status = ?", [$selectedYear, $i, 'present'])
                ->count();

            $totalAbsent = DB::table('attendance')
                ->whereRaw("YEAR(date) = ? AND MONTH(date) = ? AND status = ?", [$selectedYear, $i, 'absent'])
                ->count();

            $present[] = $totalPresent;
            $absent[] = $totalAbsent;
        }

        return [
            'months' => $months,
            'present' => $present,
            'absent' => $absent,
            'leave' => array_fill(0, 12, 0)
        ];
    }

    /**
     * Get grade distribution from exam marks
     */
    private function getGradeDistribution($semester)
    {
        $query = DB::table('exam_marks')
            ->whereNotNull('percentage');

        if (!empty($semester)) {
            $query->whereIn('student_id', 
                DB::table('students')
                    ->where('semester', $semester)
                    ->select('id')
            );
        }

        $total = $query->clone()->count();

        if ($total === 0) {
            // Return default distribution if no data
            return [
                'A' => 28,
                'B' => 35,
                'C' => 22,
                'D' => 10,
                'F' => 5
            ];
        }

        // Calculate grades based on percentage (as decimal)
        $aGrade = $query->clone()->whereRaw('CAST(percentage AS DECIMAL(5,2)) >= 90')->count();
        $bGrade = $query->clone()->whereRaw('CAST(percentage AS DECIMAL(5,2)) >= 80 AND CAST(percentage AS DECIMAL(5,2)) < 90')->count();
        $cGrade = $query->clone()->whereRaw('CAST(percentage AS DECIMAL(5,2)) >= 70 AND CAST(percentage AS DECIMAL(5,2)) < 80')->count();
        $dGrade = $query->clone()->whereRaw('CAST(percentage AS DECIMAL(5,2)) >= 60 AND CAST(percentage AS DECIMAL(5,2)) < 70')->count();
        $fGrade = $query->clone()->whereRaw('CAST(percentage AS DECIMAL(5,2)) < 60')->count();

        return [
            'A' => $total > 0 ? round(($aGrade / $total) * 100) : 28,
            'B' => $total > 0 ? round(($bGrade / $total) * 100) : 35,
            'C' => $total > 0 ? round(($cGrade / $total) * 100) : 22,
            'D' => $total > 0 ? round(($dGrade / $total) * 100) : 10,
            'F' => $total > 0 ? round(($fGrade / $total) * 100) : 5
        ];
    }

    /**
     * Get top performing students
     */
    private function getTopStudents($semester, $perPage = 25)
    {
        // Use query builder with proper join
        $query = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'student')
            ->select(
                'students.id as student_id',
                'students.user_id',
                'students.roll_no',
                'students.department as program',
                'students.semester',
                'students.status',
                'users.name',
                'users.email',
                'users.phone',
                'users.profile_photo_path'
            );

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        $students = $query->paginate($perPage);

        // Add avg_percentage and attendance to each student
        $students->getCollection()->transform(function ($student) {
            // Get average marks
            $avgMarks = DB::table('exam_marks')
                ->where('student_id', $student->student_id)
                ->avg('percentage');
            
            // Get attendance
            $total = DB::table('attendance')
                ->where('student_id', $student->student_id)
                ->count();
            $present = DB::table('attendance')
                ->where('student_id', $student->student_id)
                ->where('status', 'present')
                ->count();
            
            $student->id = $student->user_id; // Use user_id for routes
            $student->student_id = $student->student_id; // Keep student record ID for print
            $student->avg_percentage = $avgMarks ?? 0;
            $student->attendance_percentage = $total > 0 ? round(($present / $total) * 100) : 0;
            
            return $student;
        });

        // Sort by avg_percentage descending
        $sorted = $students->getCollection()->sortByDesc('avg_percentage')->values();
        $students->setCollection($sorted);

        return $students;
    }

    /**
     * Get subject performance
     */
    private function getSubjectPerformance($semester, $perPage = 25)
    {
        $query = DB::table('subjects')
            ->leftJoin('exam_marks', 'subjects.id', '=', 'exam_marks.subject_id')
            ->leftJoin('students', 'exam_marks.student_id', '=', 'students.id');

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        $subjectsQuery = $query
            ->select(
                'subjects.id',
                'subjects.subject_name',
                'subjects.subject_code',
                DB::raw('AVG(COALESCE(exam_marks.percentage, 0)) as avg_percentage')
            )
            ->groupBy('subjects.id', 'subjects.subject_name', 'subjects.subject_code')
            ->orderByDesc('avg_percentage')
            ;

        $subjects = $subjectsQuery->paginate($perPage);

        if ($subjects->total() === 0) {
            $mocks = collect([
                (object)['id' => 1, 'subject_name' => 'Data Structures', 'subject_code' => 'CS-301', 'avg_percentage' => 85.2],
                (object)['id' => 2, 'subject_name' => 'Algorithms', 'subject_code' => 'CS-302', 'avg_percentage' => 78.0],
                (object)['id' => 3, 'subject_name' => 'Database Systems', 'subject_code' => 'CS-303', 'avg_percentage' => 72.4],
            ]);

            return new \Illuminate\Pagination\LengthAwarePaginator(
                $mocks->forPage(1, $perPage),
                $mocks->count(),
                $perPage,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return $subjects;
    }

    /**
     * Generate custom report based on filters
     */
    public function generateReport(Request $request)
    {
        $semester = $request->get('semester', '');
        $subject = $request->get('subject', '');
        $reportType = $request->get('report_type', 'all');

        $data = [
            'attendance' => [],
            'marks' => [],
            'students' => []
        ];

        if ($reportType === 'all' || $reportType === 'attendance') {
            $data['attendance'] = $this->getAttendanceData($semester, $subject);
        }

        if ($reportType === 'all' || $reportType === 'marks') {
            $data['marks'] = $this->getMarksData($semester, $subject);
        }

        if ($reportType === 'all' || $reportType === 'progress') {
            $data['students'] = $this->getStudentProgressData($semester);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get detailed attendance data for report
     */
    private function getAttendanceData($semester, $subject)
    {
        $query = DB::table('attendance')
            ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->select(
                'attendance.id',
                'attendance.date',
                'attendance.status',
                'attendance.remarks',
                'users.name as student_name',
                'students.roll_no',
                'students.semester'
            );

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        if (!empty($subject)) {
            $query->where('attendance.subject_id', $subject);
        }

        return $query->orderBy('attendance.date', 'desc')->get();
    }

    /**
     * Get detailed marks data for report
     */
    private function getMarksData($semester, $subject)
    {
        $query = DB::table('exam_marks')
            ->leftJoin('students', 'exam_marks.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->leftJoin('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->select(
                'exam_marks.id',
                'exam_marks.marks_obtained',
                'exam_marks.full_marks',
                'exam_marks.percentage',
                'exam_marks.grade',
                'exam_marks.graded_at as date',
                'users.name as student_name',
                'students.roll_no',
                'students.semester',
                'subjects.subject_name',
                'subjects.subject_code'
            );

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        if (!empty($subject)) {
            $query->where('exam_marks.subject_id', $subject);
        }

        return $query->orderBy('exam_marks.graded_at', 'desc')->get();
    }

    /**
     * Get student progress data for report
     */
    private function getStudentProgressData($semester)
    {
        $query = DB::table('students')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'student');

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        return $query
            ->select(
                'students.id',
                'users.name',
                'students.roll_no',
                'students.semester'
            )
            ->orderBy('users.name')
            ->get();
    }

    /**
     * Get marks grid data (latest 10 marks)
     */
    private function getMarksGrid($semester, $subject)
    {
        $query = DB::table('exam_marks')
            ->leftJoin('students', 'exam_marks.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
            ->select(
                'users.name as student_name',
                'students.roll_no',
                'subjects.subject_name',
                'exam_marks.marks_obtained',
                'exam_marks.full_marks',
                'exam_marks.percentage',
                'exam_marks.grade',
                'exam_marks.created_at as graded_at',
                'exam_marks.id'
            );

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        if (!empty($subject)) {
            $query->where('exam_marks.subject_id', $subject);
        }

        $marks = $query
            ->orderBy('exam_marks.created_at', 'desc')
            ->limit(10)
            ->get();

        // Return sample data if no marks exist
        if ($marks->isEmpty()) {
            return collect([
                (object)[
                    'student_name' => 'Osvaldo Collins',
                    'roll_no' => 'STU0008',
                    'subject_name' => 'Data Structures',
                    'marks_obtained' => 40,
                    'full_marks' => 100,
                    'percentage' => 40,
                    'grade' => 'C'
                ],
                (object)[
                    'student_name' => 'Quentin Kunde',
                    'roll_no' => 'STU0010',
                    'subject_name' => 'Data Structures',
                    'marks_obtained' => 52,
                    'full_marks' => 100,
                    'percentage' => 52,
                    'grade' => 'C+'
                ]
            ]);
        }

        return $marks;
    }

    /**
     * Get attendance grid data (latest 20 attendance records)
     */
    private function getAttendanceGrid($semester)
    {
        $query = DB::table('attendance')
            ->leftJoin('students', 'attendance.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select(
                'users.name as student_name',
                'students.roll_no',
                DB::raw('COALESCE(subjects.subject_name, "General") as subject_name'),
                'attendance.date',
                'attendance.status',
                'attendance.remarks'
            );

        if (!empty($semester)) {
            $query->where('students.semester', $semester);
        }

        $attendance = $query
            ->orderBy('attendance.date', 'desc')
            ->limit(20)
            ->get();

        // Return sample data if no attendance exists
        if ($attendance->isEmpty()) {
            return collect([
                (object)[
                    'student_name' => 'Osvaldo Collins',
                    'roll_no' => 'STU0008',
                    'subject_name' => 'Data Structures',
                    'date' => now()->toDateString(),
                    'status' => 'present',
                    'remarks' => null
                ],
                (object)[
                    'student_name' => 'Quentin Kunde',
                    'roll_no' => 'STU0010',
                    'subject_name' => 'Data Structures',
                    'date' => now()->toDateString(),
                    'status' => 'present',
                    'remarks' => null
                ]
            ]);
        }

        return $attendance;
    }

    /**
     * AJAX: Get chart data based on filters
     * Returns attendance trends and grade distribution for Chart.js
     */
    public function chartData(Request $request)
    {
        try {
            $semester = $request->get('semester', '');
            $subject = $request->get('subject', '');
            $year = $request->get('year', date('Y'));

            // Get monthly attendance trends
            $monthlyAttendance = $this->getMonthlyAttendanceTrendsFiltered($semester, $year);

            // Get grade distribution
            $gradeDistribution = $this->getGradeDistribution($semester);

            // Get attendance stats
            $attendanceStats = $this->getAttendanceStats($semester);

            // Get marks stats
            $marksStats = $this->getMarksStats($semester, $subject);

            // Get progress stats
            $progressStats = $this->getProgressStats($semester);

            // Get KPI stats
            $kpiStats = $this->getKPIStats('', $semester, '');

            // Get students per semester
            $studentsPerSemester = $this->getStudentsPerSemester('', '');

            // Get elective distribution
            $electiveDistribution = $this->getElectiveDistribution($semester);

            // Get marks per subject
            $marksPerSubject = $this->getMarksPerSubject($semester);

            return response()->json([
                'success' => true,
                'data' => [
                    'monthlyAttendance' => $monthlyAttendance,
                    'gradeDistribution' => $gradeDistribution,
                    'attendanceStats' => $attendanceStats,
                    'marksStats' => $marksStats,
                    'progressStats' => $progressStats,
                    'kpiStats' => $kpiStats,
                    'studentsPerSemester' => $studentsPerSemester,
                    'electiveDistribution' => $electiveDistribution,
                    'marksPerSubject' => $marksPerSubject
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filtered monthly attendance trends
     */
    private function getMonthlyAttendanceTrendsFiltered($semester, $year = null)
    {
        $months = [];
        $present = [];
        $absent = [];
        $leave = [];

        $latestYear = DB::table('attendance')
            ->orderByRaw('YEAR(date) DESC')
            ->limit(1)
            ->pluck('date')
            ->first();
        
        $selectedYear = $year ?? ($latestYear ? date('Y', strtotime($latestYear)) : date('Y'));

        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;

            // Base query with optional semester filter
            $presentQuery = DB::table('attendance')
                ->whereRaw("YEAR(date) = ? AND MONTH(date) = ? AND status = ?", [$selectedYear, $i, 'present']);
                
            $absentQuery = DB::table('attendance')
                ->whereRaw("YEAR(date) = ? AND MONTH(date) = ? AND status = ?", [$selectedYear, $i, 'absent']);
                
            $leaveQuery = DB::table('attendance')
                ->whereRaw("YEAR(date) = ? AND MONTH(date) = ? AND status IN (?, ?)", [$selectedYear, $i, 'late', 'excused']);

            // Apply semester filter if provided
            if (!empty($semester)) {
                $presentQuery->whereIn('student_id', 
                    DB::table('students')->where('semester', $semester)->select('id')
                );
                $absentQuery->whereIn('student_id', 
                    DB::table('students')->where('semester', $semester)->select('id')
                );
                $leaveQuery->whereIn('student_id', 
                    DB::table('students')->where('semester', $semester)->select('id')
                );
            }

            $present[] = $presentQuery->count();
            $absent[] = $absentQuery->count();
            $leave[] = $leaveQuery->count();
        }

        return [
            'months' => $months,
            'present' => $present,
            'absent' => $absent,
            'leave' => $leave
        ];
    }

    /**
     * Export report data to PDF
     * Includes all charts and data in a printable format
     */
    public function exportPdf(Request $request)
    {
        try {
            $semester = $request->get('semester', '');
            $subject = $request->get('subject', '');
            $program = $request->get('program', '');
            $studentStatus = $request->get('student_status', '');
            $year = $request->get('year', date('Y'));
            $month = $request->get('month', '');

            // Get all the data for the report
            $semesters = DB::table('students')
                ->distinct()
                ->pluck('semester')
                ->filter()
                ->sort()
                ->values();

            $subjects = DB::table('subjects')
                ->select('id', 'subject_name', 'subject_code', 'semester')
                ->orderBy('subject_name')
                ->get();

            // Get attendance statistics
            $attendanceStats = $this->getAttendanceStats($semester);

            // Get marks statistics
            $marksStats = $this->getMarksStats($semester, $subject);

            // Get student progress statistics
            $progressStats = $this->getProgressStats($semester);

            // Get monthly attendance trends
            $monthlyAttendance = $this->getMonthlyAttendanceTrends($year);

            // Get grade distribution
            $gradeDistribution = $this->getGradeDistribution($semester);

            // Get top performing students (limited to 10)
            $topStudents = $this->getTopStudents($semester, 10);

            // Get subject performance
            $subjectPerformance = $this->getSubjectPerformance($semester, 10);

            // Get marks grid
            $marksGrid = $this->getMarksGrid($semester, $subject);

            // Get attendance grid
            $attendanceGrid = $this->getAttendanceGrid($semester);

            // Prepare data for charts
            $chartData = [
                'attendance' => [
                    'labels' => $monthlyAttendance['months'],
                    'present' => $monthlyAttendance['present'],
                    'absent' => $monthlyAttendance['absent'],
                    'leave' => $monthlyAttendance['leave']
                ],
                'grades' => $gradeDistribution
            ];

            // Calculate totals for display
            $totalStudents = $topStudents->total() ?? $topStudents->count();
            $totalSubjects = $subjectPerformance->total() ?? $subjectPerformance->count();

            // Render the PDF view
            $pdf = \PDF::loadView('admin.reports-pdf', [
                'semesters' => $semesters,
                'subjects' => $subjects,
                'attendanceStats' => $attendanceStats,
                'marksStats' => $marksStats,
                'progressStats' => $progressStats,
                'monthlyAttendance' => $monthlyAttendance,
                'gradeDistribution' => $gradeDistribution,
                'topStudents' => $topStudents,
                'subjectPerformance' => $subjectPerformance,
                'marksGrid' => $marksGrid,
                'attendanceGrid' => $attendanceGrid,
                'semester' => $semester,
                'subject' => $subject,
                'chartData' => $chartData,
                'totalStudents' => $totalStudents,
                'totalSubjects' => $totalSubjects,
                'generatedAt' => now()->format('Y-m-d H:i:s')
            ]);

            return $pdf->download('IT_Reports_' . ($semester ? 'Semester-' . $semester : 'All') . '_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Print report view
     */
    public function printReport(Request $request)
    {
        try {
            $semester = $request->get('semester', '');
            $subject = $request->get('subject', '');
            $program = $request->get('program', '');
            $studentStatus = $request->get('student_status', '');
            $year = $request->get('year', date('Y'));
            $month = $request->get('month', '');
            $search = $request->get('search', '');

            // Get KPI stats
            $kpiStats = $this->getKPIStats($program, $semester, $studentStatus);

            // Get grade distribution
            $gradeDistribution = $this->getGradeDistribution($semester);

            // Get student report
            $studentReport = $this->getStudentReport($program, $semester, $subject, $studentStatus, $year, $month, $search, 100);

            // Get chart data
            $monthlyAttendance = $this->getMonthlyAttendanceTrends($year);
            $studentsPerSemester = $this->getStudentsPerSemester($program, $studentStatus);
            $electiveDistribution = $this->getElectiveDistribution($semester);
            $marksPerSubject = $this->getMarksPerSubject($semester);

            // Get college info for header
            $college = Department::first();

            // Get top performing students
            $topStudents = $this->getTopStudents($semester, 10);

            return view('admin.print.reports', [
                'college' => $college,
                'kpiStats' => $kpiStats,
                'gradeDistribution' => $gradeDistribution,
                'studentReport' => $studentReport,
                'topStudents' => $topStudents,
                'semester' => $semester,
                'program' => $program,
                'studentStatus' => $studentStatus,
                'year' => $year,
                'month' => $month,
                'search' => $search,
                // Chart data
                'monthlyAttendance' => $monthlyAttendance,
                'studentsPerSemester' => $studentsPerSemester,
                'electiveDistribution' => $electiveDistribution,
                'marksPerSubject' => $marksPerSubject
            ]);
        } catch (\Exception $e) {
            Log::error('Print Report error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate print view: ' . $e->getMessage());
        }
    }

    /**
     * Export report data to CSV
     */
    public function exportCsv(Request $request)
    {
        try {
            $semester = $request->get('semester', '');
            $subject = $request->get('subject', '');
            $program = $request->get('program', '');
            $studentStatus = $request->get('student_status', '');
            $year = $request->get('year', date('Y'));
            $month = $request->get('month', '');
            $search = $request->get('search', '');

            // Get data
            $kpiStats = $this->getKPIStats($program, $semester, $studentStatus);
            $topStudents = $this->getTopStudents($semester, 100);
            $subjectPerformance = $this->getSubjectPerformance($semester, 100);
            $attendanceStats = $this->getAttendanceStats($semester);
            $marksStats = $this->getMarksStats($semester, $subject);
            $gradeDistribution = $this->getGradeDistribution($semester);
            $studentReport = $this->getStudentReport($program, $semester, $subject, $studentStatus, $year, $month, $search, 1000);

            // Create CSV content
            $csvData = [];
            
            // Header section
            $csvData[] = ['IT Department Reports - Export'];
            $csvData[] = ['Generated At', now()->format('Y-m-d H:i:s')];
            $csvData[] = ['Program', $program ?: 'All'];
            $csvData[] = ['Semester', $semester ?: 'All'];
            $csvData[] = ['Student Status', $studentStatus ?: 'All'];
            $csvData[] = ['Year', $year];
            $csvData[] = [];
            
            // KPI Stats section
            $csvData[] = ['Key Performance Indicators'];
            $csvData[] = ['Total Students', $kpiStats['totalStudents']];
            $csvData[] = ['Active Students', $kpiStats['activeStudents']];
            $csvData[] = ['Alumni', $kpiStats['alumni']];
            $csvData[] = ['Attendance Rate', $kpiStats['attendanceRate'] . '%'];
            $csvData[] = ['Average Marks', $kpiStats['avgMarks'] . '%'];
            $csvData[] = ['Electives Chosen', $kpiStats['electivesChosen']];
            $csvData[] = [];
            
            // Stats section
            $csvData[] = ['Statistics'];
            $csvData[] = ['Attendance Average', $attendanceStats['avg'] . '%'];
            $csvData[] = ['Marks Average', $marksStats['avg'] . '%'];
            $csvData[] = [];
            
            // Grade Distribution
            $csvData[] = ['Grade Distribution'];
            foreach ($gradeDistribution as $grade => $percentage) {
                $csvData[] = [$grade, $percentage . '%'];
            }
            $csvData[] = [];
            
            // Student Details
            $csvData[] = ['Student Details'];
            $csvData[] = ['Student ID', 'Name', 'Semester', 'Subject', 'Attendance %', 'Marks %', 'Status', 'Is Alumni'];
            foreach ($studentReport as $student) {
                $csvData[] = [
                    $student->roll_no ?? 'N/A',
                    $student->name ?? 'Unknown',
                    $student->semester ?? 'N/A',
                    $student->subject_name ?? 'N/A',
                    $student->attendance_percentage ?? 0,
                    round($student->avg_marks ?? 0, 1),
                    $student->status ?? 'active',
                    $student->is_alumni ? 'Yes' : 'No'
                ];
            }
            $csvData[] = [];
            
            // Top Students
            $csvData[] = ['Top Performing Students'];
            $csvData[] = ['Name', 'Average %', 'Attendance %'];
            foreach ($topStudents as $student) {
                $csvData[] = [
                    $student->name,
                    round($student->avg_percentage, 1) . '%',
                    ($student->attendance_percentage ?? 'N/A') . '%'
                ];
            }
            $csvData[] = [];
            
            // Subject Performance
            $csvData[] = ['Subject Performance'];
            $csvData[] = ['Subject Name', 'Subject Code', 'Average %'];
            foreach ($subjectPerformance as $subj) {
                $csvData[] = [
                    $subj->subject_name,
                    $subj->subject_code,
                    round($subj->avg_percentage, 1) . '%'
                ];
            }

            // Create CSV file
            $filename = 'IT_Reports_' . ($semester ? 'Semester-' . $semester : 'All') . '_' . date('Y-m-d') . '.csv';
            
            $handle = fopen('php://temp', 'r+');
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('CSV Export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate CSV: ' . $e->getMessage());
        }
    }
}
