<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAttendanceController extends Controller
{
    /**
     * Display the student's attendance records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        // Get subjects enrolled by the student


        $subjects = $student->subjects()
            ->with(['teacherAssignments.teacher.user'])
            ->orderBy('semester')
            ->get()
            ->map(function ($subject) use ($student) {
                // Get primary teacher for the subject
                $primaryTeacher = $subject->teacherAssignments()
                    ->where('role', 'primary')
                    ->with('teacher.user')
                    ->first()?->teacher?->user;
                
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                    'course' => $subject->category ?? $subject->subject_name,
                    'teacher' => $primaryTeacher ? $primaryTeacher->name : 'TBA',
                    'attendance' => $student->getAttendancePercentage($subject->id),
                ];
            });

        
        // Get overall attendance percentage
        $overallAttendance = $student->getAttendancePercentage();

        $attendanceStatusCounts = [
            'present' => (int) DB::table('attendance')
                ->where('student_id', $student->id)
                ->where('attendance_type', 'class')
                ->where('status', 'present')
                ->count(),
            'absent' => (int) DB::table('attendance')
                ->where('student_id', $student->id)
                ->where('attendance_type', 'class')
                ->where('status', 'absent')
                ->count(),
            'leave' => (int) DB::table('attendance')
                ->where('student_id', $student->id)
                ->where('attendance_type', 'class')
                ->where('status', 'leave')
                ->count(),
        ];

        $attendanceStatusChart = [
            'labels' => [__('Present'), __('Absent'), __('Leave')],
            'values' => [
                $attendanceStatusCounts['present'],
                $attendanceStatusCounts['absent'],
                $attendanceStatusCounts['leave'],
            ],
        ];

        $sortedSubjects = $subjects->sortByDesc('attendance')->values();
        $bestSubject = $sortedSubjects->first();
        $lowestSubject = $sortedSubjects->last();

        $subjectAttendanceChart = [
            'labels' => $sortedSubjects->pluck('name')->all(),
            'codes' => $sortedSubjects->pluck('code')->all(),
            'teachers' => $sortedSubjects->pluck('teacher')->all(),
            'values' => $sortedSubjects->map(function ($subject) {
                return round((float) $subject['attendance'], 1);
            })->all(),
        ];

        $attendanceInsights = [
            [
                'label' => __('Subjects'),
                'value' => (string) $subjects->count(),
                'icon' => 'bi-journal-bookmark',
            ],
            [
                'label' => __('Present'),
                'value' => (string) $attendanceStatusCounts['present'],
                'icon' => 'bi-check-circle',
            ],
            [
                'label' => __('Absent'),
                'value' => (string) $attendanceStatusCounts['absent'],
                'icon' => 'bi-x-circle',
            ],
            [
                'label' => __('Leave'),
                'value' => (string) $attendanceStatusCounts['leave'],
                'icon' => 'bi-calendar2-x',
            ],
        ];
        
        // Get recent attendance records
        $recentAttendance = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('attendance_type', 'class')
            ->join('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select('attendance.*', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date', 'desc')
            ->limit(10)
            ->get();
        
        return view('student.attendance.index', compact(
            'student',
            'subjects',
            'overallAttendance',
            'attendanceStatusCounts',
            'attendanceStatusChart',
            'subjectAttendanceChart',
            'attendanceInsights',
            'bestSubject',
            'lowestSubject',
            'recentAttendance'
        ));

    }
    
    /**
     * Display attendance details for a specific subject.
     */
    public function show($subjectId)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        // Get the subject and verify student is enrolled
        $subject = $student->subjects()
            ->where('subjects.id', $subjectId)
            ->with(['teacherAssignments.teacher.user'])
            ->first();
        
        if (!$subject) {
            return redirect()->route('student.attendance')->with('error', 'Subject not found or you are not enrolled.');
        }
        
        $primaryTeacher = $subject->teacherAssignments()
            ->where('role', 'primary')
            ->with('teacher.user')
            ->first()?->teacher?->user;

        $attendanceRecords = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->where('attendance_type', 'class')
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->get();

        $attendanceCounts = [
            'present' => (int) $attendanceRecords->where('status', 'present')->count(),
            'absent' => (int) $attendanceRecords->where('status', 'absent')->count(),
            'leave' => (int) $attendanceRecords->where('status', 'leave')->count(),
        ];

        $attendanceTotal = array_sum($attendanceCounts);
        $attendancePercentage = $attendanceTotal > 0 ? $student->getAttendancePercentage($subjectId) : 0;

        $attendanceChart = [
            'labels' => [__('Present'), __('Absent'), __('Leave')],
            'values' => [$attendanceCounts['present'], $attendanceCounts['absent'], $attendanceCounts['leave']],
        ];

        // Get attendance percentage for this subject
        $assessmentMarks = $student->getAssessmentMarks($subjectId, 'assessment', null, true);
        $ctevtMarkRecord = $student->getExamMarkForSubject($subjectId, 'ctevt', null, null, true);

        $ctevtMarks = $ctevtMarkRecord ? (object) [
            'full' => round((float) $ctevtMarkRecord->calculateFullMarks(), 2),
            'pass' => round((float) $ctevtMarkRecord->getEffectivePassingMarksAttribute(), 2),
            'obtained' => round((float) $ctevtMarkRecord->calculateTotalMarks(), 2),
            'percentage' => round((float) $ctevtMarkRecord->calculatePercentage(), 2),
            'is_pass' => $ctevtMarkRecord->isAbsent() ? null : ($ctevtMarkRecord->calculatePercentage() >= 40),
        ] : (object) [
            'full' => 0,
            'pass' => 0,
            'obtained' => 0,
            'percentage' => 0,
            'is_pass' => null,
        ];

        $marksChart = [
            'labels' => [__('Assessment'), __('CTEVT')],
            'obtained' => [
                round((float) $assessmentMarks->obtained, 2),
                round((float) $ctevtMarks->obtained, 2),
            ],
            'full' => [
                round((float) $assessmentMarks->full, 2),
                round((float) $ctevtMarks->full, 2),
            ],
            'pass' => [
                round((float) $assessmentMarks->pass, 2),
                round((float) $ctevtMarks->pass, 2),
            ],
        ];

        $syllabusText = (string) ($subject->syllabus ?? '');
        $syllabusLines = collect(preg_split('/\r\n|\r|\n/', $syllabusText))
            ->map(function ($line) {
                $line = trim((string) $line);
                $line = preg_replace('/^\s*(?:[-*•]|\d+[.)])\s*/u', '', $line);
                return trim((string) $line);
            })
            ->filter()
            ->values();

        $learningObjectives = collect(preg_split('/\r\n|\r|\n/', (string) ($subject->learning_objectives ?? '')))
            ->map(function ($line) {
                $line = trim((string) $line);
                $line = preg_replace('/^\s*(?:[-*•]|\d+[.)])\s*/u', '', $line);
                return trim((string) $line);
            })
            ->filter()
            ->values();

        $syllabusDocumentUrl = null;
        if (!empty($subject->syllabus_document_path) && Storage::disk('public')->exists($subject->syllabus_document_path)) {
            $syllabusDocumentUrl = Storage::url($subject->syllabus_document_path);
        }

        $recentAttendanceRecords = $attendanceRecords->take(12);

        $subjectHighlights = [
            [
                'label' => __('Semester'),
                'value' => $subject->formatted_semester ?: ($subject->semester ?? __('N/A')),
                'icon' => 'bi-calendar3',
            ],
            [
                'label' => __('Credits'),
                'value' => (string) ($subject->credits ?? __('N/A')),
                'icon' => 'bi-award',
            ],
            [
                'label' => __('Type'),
                'value' => ucfirst((string) ($subject->subject_type ?? 'core')),
                'icon' => 'bi-grid-1x2',
            ],
            [
                'label' => __('Lab'),
                'value' => $subject->has_lab ? __('Enabled') : __('Not enabled'),
                'icon' => 'bi-beaker',
            ],
            [
                'label' => __('Hours'),
                'value' => sprintf('%s / %s / %s', $subject->lecture_hours ?? 0, $subject->practical_hours ?? 0, $subject->tutorial_hours ?? 0),
                'icon' => 'bi-clock-history',
            ],
            [
                'label' => __('Prerequisite'),
                'value' => $subject->prerequisite ?: __('None'),
                'icon' => 'bi-link-45deg',
            ],
        ];

        return view('student.attendance.show', compact(
            'subject',
            'primaryTeacher',
            'attendancePercentage',
            'attendanceRecords',
            'recentAttendanceRecords',
            'attendanceCounts',
            'attendanceTotal',
            'attendanceChart',
            'assessmentMarks',
            'ctevtMarks',
            'marksChart',
            'syllabusLines',
            'learningObjectives',
            'syllabusDocumentUrl',
            'subjectHighlights'
        ));
    }
}

