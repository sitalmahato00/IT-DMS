<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentMarkController extends Controller
{
    /**
     * Display the student's marks/results.
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
                
                // Get assessment marks
                $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment');
                $ctevtMarks = $student->getExamMarkForSubject($subject->id, 'ctevt');
                $primaryMarks = ($assessmentMarks->full ?? 0) > 0 ? $assessmentMarks : (((isset($ctevtMarks->full) ? $ctevtMarks->full : 0) > 0) ? $ctevtMarks : null);
                $fullMarks = $primaryMarks && ($primaryMarks->full ?? 0) > 0 ? (float) $primaryMarks->full : 0;
                $obtainedMarks = $primaryMarks && ($primaryMarks->obtained ?? 0) > 0 ? (float) $primaryMarks->obtained : 0;
                $percentage = $fullMarks > 0 ? round(($obtainedMarks / $fullMarks) * 100, 2) : null;
                $status = $percentage === null ? 'pending' : (($primaryMarks->is_pass ?? false) ? 'pass' : 'fail');
                 
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject_name,
                    'code' => $subject->subject_code,
                    'semester' => $subject->semester,
                    'course' => $subject->category ?? $subject->subject_name,
                    'teacher' => $primaryTeacher ? $primaryTeacher->name : 'TBA',
                    'assessment_marks' => $assessmentMarks,
                    'ctevt_marks' => $ctevtMarks,
                    'full_marks' => $fullMarks,
                    'obtained_marks' => $obtainedMarks,
                    'percentage' => $percentage,
                    'status' => $status,
                ];
            });
        
        // Calculate overall CGPA (simplified)
        $totalObtained = 0;
        $totalFull = 0;
        $subjectCount = 0;
        
        foreach ($subjects as $subject) {
            if ($subject['assessment_marks'] && $subject['assessment_marks']->full > 0) {
                $totalObtained += $subject['assessment_marks']->obtained;
                $totalFull += $subject['assessment_marks']->full;
                $subjectCount++;
            } elseif ($subject['ctevt_marks'] && isset($subject['ctevt_marks']->full) && $subject['ctevt_marks']->full > 0) {
                $totalObtained += $subject['ctevt_marks']->obtained;
                $totalFull += $subject['ctevt_marks']->full;
                $subjectCount++;
            }
        }
        
        $overallPercentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        $cgpa = $overallPercentage > 0 ? round($overallPercentage / 25, 2) : 0; // Simplified CGPA calculation
        
        return view('student.marks.index', compact('subjects', 'overallPercentage', 'cgpa', 'subjectCount'));
    }
    
    /**
     * Display marks details for a specific subject.
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
            return redirect()->route('student.marks')->with('error', 'Subject not found or you are not enrolled.');
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
        
        // Get assessment marks
        $assessmentMarks = $student->getAssessmentMarks($subject->id, 'assessment');
        
        // Get CTEVT marks
        $ctevtMarks = $student->getExamMarkForSubject($subject->id, 'ctevt');
        
        // Get component marks for CTEVT
        $componentMarks = [];
        if ($ctevtMarks && isset($ctevtMarks->full)) {
            $components = ['TI', 'TE', 'PI', 'PE'];
            foreach ($components as $component) {
                $componentMarks[$component] = $student->getComponentMarks($subject->id, $component);
            }
        }
        
        // Get all exams for this subject
        $exams = DB::table('exam_marks')
            ->where('student_id', $student->id)
            ->where('exam_marks.subject_id', $subject->id)
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->select('exams.*', 'exam_marks.marks_obtained', 'exam_marks.full_marks', 'exam_marks.passing_marks')
            ->orderBy('exams.exam_date', 'desc')
            ->get();
        
        return view('student.marks.show', compact('subject', 'teachers', 'assessmentMarks', 'ctevtMarks', 'componentMarks', 'exams'));
    }
}
