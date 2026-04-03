<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
                    ->first()
                    ->teacher?->user ?? null;
                
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
        
        // Get recent attendance records
        $recentAttendance = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('attendance_type', 'class')
            ->join('subjects', 'attendance.subject_id', '=', 'subjects.id')
            ->select('attendance.*', 'subjects.subject_name', 'subjects.subject_code')
            ->orderBy('attendance.date', 'desc')
            ->limit(10)
            ->get();
        
        return view('student.attendance.index', compact('student', 'subjects', 'overallAttendance', 'recentAttendance'));

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
        
        // Get attendance percentage for this subject
        $attendancePercentage = $student->getAttendancePercentage($subjectId);
        
        // Get attendance records for this subject
        $attendanceRecords = DB::table('attendance')
            ->where('student_id', $student->id)
            ->where('subject_id', $subjectId)
            ->where('attendance_type', 'class')
            ->orderBy('date', 'desc')
            ->get();
        
        // Get primary teacher for the subject
        $primaryTeacher = $subject->teacherAssignments()
            ->where('role', 'primary')
            ->first()
            ->teacher?->user ?? null;
        
        return view('student.attendance.show', compact('subject', 'attendancePercentage', 'attendanceRecords', 'primaryTeacher'));
    }
}
