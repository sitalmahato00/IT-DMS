<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class ParentCoursesController extends Controller
{
    /**
     * Display courses for all children.
     */
    public function index()
    {
        $parent = Auth::user();
        
        // Get all children for this parent
        $children = Student::where('parent_id', $parent->id)
            ->with('subjects')
            ->get();

        // Get unique subjects across all children
        $subjectIds = $children->map(function ($child) {
            return $child->subjects->pluck('id');
        })->flatten()->unique();

        $subjects = Subject::whereIn('id', $subjectIds)
            ->with('teachers')
            ->get();

        // Group subjects by semester
        $subjectsBySemester = $subjects->groupBy('semester');

        return view('parent.courses.index', compact('children', 'subjects', 'subjectsBySemester'));
    }

    /**
     * Display courses for a specific child.
     */
    public function showChild($childId)
    {
        $parent = Auth::user();
        $child = Student::findOrFail($childId);

        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        $subjects = $child->subjects()->with('teachers')->get();
        $subjectsBySemester = $subjects->groupBy('semester');

        return view('parent.courses.child', compact('child', 'subjects', 'subjectsBySemester'));
    }

    /**
     * Display details for a specific subject/course.
     */
    public function showSubject($childId, $subjectId)
    {
        $parent = Auth::user();
        $child = Student::findOrFail($childId);

        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        // Verify child is enrolled in this subject
        if (!$child->subjects->contains($subjectId)) {
            abort(403, 'Unauthorized action.');
        }

        $subject = Subject::with('teachers')->findOrFail($subjectId);

        // Get marks for this subject
        $marks = $child->examMarks()->where('subject_id', $subjectId)->get();

        // Get attendance for this subject
        $attendance = $child->attendanceRecords()->where('subject_id', $subjectId)->get();
        $subjectAttendancePercentage = $child->getAttendancePercentage($subjectId) ?? 0;

        return view('parent.courses.subject', compact('child', 'subject', 'marks', 'attendance', 'subjectAttendancePercentage'));
    }
}

