<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentPdfExportController extends Controller
{
    /**
     * Export attendance report as PDF.
     */
    public function attendancePdf($childId)
    {
        $parent = Auth::user();
        $child = Student::findOrFail($childId);

        // Verify child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        $attendance = Attendance::where('student_id', $childId)
            ->with('subject')
            ->orderBy('attendance_date', 'desc')
            ->get();

        $attendancePercentage = $child->getAttendancePercentage() ?? 0;

        $pdf = Pdf::loadView('parent.exports.attendance-pdf', compact('child', 'attendance', 'attendancePercentage'));

        return $pdf->download('attendance-' . $child->id . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export marks report as PDF.
     */
    public function marksPdf($childId)
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
            ->get();

        $subjectPerformance = ExamMark::where('student_id', $childId)
            ->selectRaw('subject_id, AVG(obtained_marks) as avg_marks, COUNT(*) as total_exams')
            ->groupBy('subject_id')
            ->with('subject')
            ->get();

        $pdf = Pdf::loadView('parent.exports.marks-pdf', compact('child', 'marks', 'subjectPerformance'));

        return $pdf->download('marks-' . $child->id . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
