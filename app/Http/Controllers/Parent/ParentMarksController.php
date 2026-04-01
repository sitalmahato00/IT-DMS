<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ParentMarksController extends Controller
{
    /**
     * Display marks/results for all children.
     */
    public function index(Request $request)
    {
        $parent = Auth::user();
        
        // Get all children for this parent
        $children = Student::where('parent_id', $parent->id)->get();
        
        if ($children->isEmpty()) {
            return view('parent.marks.index', ['children' => collect(), 'marks' => collect()]);
        }

        $childrenIds = $children->pluck('id');
        
        // Get exam marks for all children
        $query = ExamMark::whereIn('student_id', $childrenIds);

        // Filter by exam if provided
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filter by subject if provided
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $marks = $query->with('student', 'exam', 'subject')
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        // Get available exams and subjects for filters
        $exams = Exam::whereIn('id', function ($q) use ($childrenIds) {
            $q->select('exam_id')
                ->from('exam_marks')
                ->whereIn('student_id', $childrenIds)
                ->distinct();
        })->get();

        $subjects = Subject::whereIn('id', function ($q) use ($childrenIds) {
            $q->select('subject_id')
                ->from('exam_marks')
                ->whereIn('student_id', $childrenIds)
                ->distinct();
        })->get();

        return view('parent.marks.index', compact('children', 'marks', 'exams', 'subjects'));
    }

    /**
     * Display marks for a specific child.
     */
    public function showChild($childId)
    {
        $parent = Auth::user();
        $child = Student::findOrFail($childId);

        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        $marks = ExamMark::where('student_id', $childId)
            ->with('exam', 'subject')
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        // Calculate subject-wise performance
        $subjectPerformance = ExamMark::where('student_id', $childId)
            ->selectRaw('subject_id, AVG(obtained_marks) as avg_marks, COUNT(*) as total_exams')
            ->groupBy('subject_id')
            ->with('subject')
            ->get();

        return view('parent.marks.child', compact('child', 'marks', 'subjectPerformance'));
    }

    /**
     * Display detailed result for a specific exam.
     */
    public function showExam($childId, $examId)
    {
        $parent = Auth::user();
        $child = Student::findOrFail($childId);

        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        $exam = Exam::findOrFail($examId);
        
        $marks = ExamMark::where('student_id', $childId)
            ->where('exam_id', $examId)
            ->with('subject')
            ->get();

        return view('parent.marks.exam', compact('child', 'exam', 'marks'));
    }
}
