<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        
        // Get attendance stats for this subject
        $attendancePercentage = $student->getAttendancePercentage($subject->id);
        
        // Get marks for this subject
        $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment');
        $ctevtMarks = $student->getExamMarkForSubject($subject->id, 'ctevt');
        
        return view('student.courses.show', compact('subject', 'teachers', 'attendancePercentage', 'assessmentMarks', 'ctevtMarks'));
    }
}