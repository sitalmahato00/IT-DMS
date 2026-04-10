<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use App\Models\Attendance;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class TeacherReportsController extends Controller
{
    /**
     * Get teacher's assigned subjects with semester info
     */
    private function getTeacherAssignments()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return [
                'subjectIds' => [],
                'semesters' => [],
            ];
        }
        
        $assignments = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get();
        
        $subjectIds = $assignments->pluck('subject_id')->toArray();
        
        // Get unique semesters from assignments
        $semesters = $assignments->pluck('semester')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        
        // Also get semesters from subjects if not in assignments
        $subjectSemesters = Subject::whereIn('id', $subjectIds)
            ->whereNotNull('semester')
            ->distinct()
            ->pluck('semester')
            ->filter()
            ->values()
            ->toArray();
        
        // Merge and unique
        $allSemesters = array_unique(array_merge($semesters, $subjectSemesters));
        sort($allSemesters);
        
        return [
            'subjectIds' => $subjectIds,
            'semesters' => $allSemesters,
            'assignments' => $assignments,
        ];
    }

    /**
     * Display reports dashboard
     */
    public function index(Request $request)
    {
        $assignments = $this->getTeacherAssignments();
        $subjectIds = $assignments['subjectIds'];
        $semesters = $assignments['semesters'];
        
        $selectedSemester = $request->get('semester', '');
        $selectedSubject = $request->get('subject', '');
        
        // Get subjects for dropdown
        $subjectsQuery = SubjectTeacher::whereHas('subject', function($q) use ($subjectIds) {
            $q->whereIn('id', $subjectIds);
        })->with('subject');
        
        if ($selectedSemester) {
            $subjectsQuery->where('semester', $selectedSemester);
        }
        
        $subjects = $subjectsQuery->get()->map(function ($st) {
            return [
                'id' => $st->subject->id,
                'name' => $st->subject->subject_name,
                'code' => $st->subject->subject_code,
                'semester' => $st->semester ?? $st->subject->semester,
            ];
        })->values();
        
        // Filter subject IDs based on selection
        $filteredSubjectIds = !empty($selectedSubject) 
            ? [$selectedSubject] 
            : ($selectedSemester ? $subjects->pluck('id')->toArray() : $subjectIds);
        
        // Get stats for dashboard
        $totalStudents = Student::whereHas('subjects', function($q) use ($filteredSubjectIds) {
            $q->whereIn('subjects.id', $filteredSubjectIds);
        })->count();
        
        $totalAttendanceRecords = Attendance::whereIn('subject_id', $filteredSubjectIds)
            ->where('attendance_type', 'class')
            ->count();
        
        $totalExams = Exam::whereIn('subject_id', $filteredSubjectIds)->count();
        
        $totalMarks = Mark::whereIn('subject_id', $filteredSubjectIds)->count();
        
        // Get recent attendance average
        $attendanceStats = Attendance::whereIn('subject_id', $filteredSubjectIds)
            ->where('attendance_type', 'class')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        $presentCount = $attendanceStats['present'] ?? 0;
        $absentCount = $attendanceStats['absent'] ?? 0;
        $leaveCount = $attendanceStats['leave'] ?? 0;
        $totalRecords = $presentCount + $absentCount + $leaveCount;
        $attendancePercentage = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100) : 0;
        
        // Get exam performance
        $examStats = Mark::whereIn('subject_id', $filteredSubjectIds)
            ->select('marks_obtained', 'full_marks')
            ->get();
        
        $avgMarks = $examStats->count() > 0 
            ? round($examStats->avg('marks_obtained'), 2) 
            : 0;
        
        $passCount = $examStats->filter(function($m) {
            return $m->marks_obtained >= ($m->full_marks * 0.4);
        })->count();
        
        $passPercentage = $examStats->count() > 0 
            ? round(($passCount / $examStats->count()) * 100) 
            : 0;
        
        return view('teacher.reports', [
            'subjects' => $subjects,
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'selectedSubject' => $selectedSubject,
            'totalStudents' => $totalStudents,
            'totalAttendanceRecords' => $totalAttendanceRecords,
            'totalExams' => $totalExams,
            'totalMarks' => $totalMarks,
            'attendancePercentage' => $attendancePercentage,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'leaveCount' => $leaveCount,
            'avgMarks' => $avgMarks,
            'passPercentage' => $passPercentage,
        ]);
    }
}

