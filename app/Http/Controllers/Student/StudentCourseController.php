<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentCourseController extends Controller
{
    /**
     * Display the student's enrolled courses.
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
            ->map(function ($subject) {
                // Get primary teacher for the subject
                $primaryTeacher = $subject->teacherAssignments()
                    ->where('role', 'primary')
                    ->first()
                    ->teacher?->user ?? null;
                
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                    'course' => $subject->category ?? $subject->subject_name,
                    'teacher' => $primaryTeacher ? $primaryTeacher->name : 'TBA',
                    'credits' => $subject->credits,
                    'has_lab' => $subject->has_lab,
                    'description' => $subject->description,
                ];
            });
        
        return view('student.courses.index', compact('subjects'));
    }
    
    /**
     * Display a specific course/subject details.
     */
    public function show($id)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        // Get the subject and verify student is enrolled
        $subject = $student->subjects()
            ->where('subjects.id', $id)
            ->with(['teacherAssignments.teacher.user'])
            ->first();
        
        if (!$subject) {
            return redirect()->route('student.courses')->with('error', 'Course not found or you are not enrolled.');
        }
        
        // Get teachers for this subject
        $teachers = $subject->teacherAssignments()
            ->with('teacher.user')
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->teacher->user->id ?? 0,
                    'name' => $assignment->teacher->user->name ?? 'Unknown',
                    'role' => $assignment->role,
                ];
            });

        $primaryTeacher = $subject->teacherAssignments()
            ->where('role', 'primary')
            ->with('teacher.user')
            ->first()?->teacher?->user;

        $attendanceRecords = DB::table('attendance')
            ->where('attendance.student_id', $student->id)
            ->where('attendance.subject_id', $subject->id)
            ->where('attendance.attendance_type', 'class')
            ->join('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select('attendance.*', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date', 'desc')
            ->orderBy('attendance.time_in', 'desc')
            ->get();

        $attendanceCounts = [
            'present' => (int) $attendanceRecords->where('status', 'present')->count(),
            'absent' => (int) $attendanceRecords->where('status', 'absent')->count(),
            'leave' => (int) $attendanceRecords->where('status', 'leave')->count(),
        ];

        $attendanceTotal = array_sum($attendanceCounts);
        $attendancePercentage = $attendanceTotal > 0 ? $student->getAttendancePercentage($subject->id) : 0;

        $attendanceChart = [
            'labels' => [__('Present'), __('Absent'), __('Leave')],
            'values' => [$attendanceCounts['present'], $attendanceCounts['absent'], $attendanceCounts['leave']],
        ];

        $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment', null, true);
        $ctevtMarkRecord = $student->getExamMarkForSubject($subject->id, 'ctevt', null, null, true);

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

        $syllabusLines = collect(preg_split('/\r\n|\r|\n/', (string) ($subject->syllabus ?? '')))
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
            ['label' => __('Semester'), 'value' => $subject->formatted_semester ?: ($subject->semester ?? __('N/A')), 'icon' => 'bi-calendar3'],
            ['label' => __('Credits'), 'value' => (string) ($subject->credits ?? __('N/A')), 'icon' => 'bi-award'],
            ['label' => __('Type'), 'value' => ucfirst((string) ($subject->subject_type ?? 'core')), 'icon' => 'bi-grid-1x2'],
            ['label' => __('Lab'), 'value' => $subject->has_lab ? __('Enabled') : __('Not enabled'), 'icon' => 'bi-beaker'],
            ['label' => __('Hours'), 'value' => sprintf('%s / %s / %s', $subject->lecture_hours ?? 0, $subject->practical_hours ?? 0, $subject->tutorial_hours ?? 0), 'icon' => 'bi-clock-history'],
            ['label' => __('Prerequisite'), 'value' => $subject->prerequisite ?: __('None'), 'icon' => 'bi-link-45deg'],
        ];

        return view('student.courses.show', compact(
            'subject',
            'teachers',
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
