<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    /**
     * Display the parent dashboard.
     */
    public function index()
    {
        $parentUser = Auth::user();
        
        // Get all children for this parent
        $children = Student::where('parent_id', $parentUser->id)->get();
        $childrenCount = $children->count();

        // Calculate overall attendance percentage
        $overallAttendance = 0;
        if ($childrenCount > 0) {
            $totalAttendance = 0;
            foreach ($children as $child) {
                $totalAttendance += $child->getAttendancePercentage() ?? 0;
            }
            $overallAttendance = round($totalAttendance / $childrenCount);
        }

        // Get unread notifications (recent notices from last 7 days)
        $unreadNotificationCount = Notice::where('audience', 'parent')
            ->where('status', 'published')
            ->where('created_at', '>', now()->subDays(7))
            ->count();

        // Get important notices
        $importantNoticeCount = Notice::where('audience', 'parent')
            ->where('status', 'published')
            ->count();

        // Get upcoming exams count
        $upcomingExamCount = Exam::where('exam_date', '>=', now())
            ->count();

        // Calculate overall score for all children
        $overallScore = null;
        if ($childrenCount > 0) {
            $totalScore = 0;
            $marks = ExamMark::whereIn('student_id', $children->pluck('id'))->get();
            if ($marks->count() > 0) {
                $totalScore = $marks->avg('marks');
                $overallScore = round($totalScore);
            }
        }

        // Count total subjects for all children
        $totalSubjects = 0;
        if ($childrenCount > 0) {
            $totalSubjects = $children->flatMap(function ($child) {
                return $child->subjects()->pluck('subjects.id');
            })->unique()->count();
        }

        // Get academic alerts (combine low attendance and low marks)
        $academicAlerts = collect();
        if ($childrenCount > 0) {
            foreach ($children as $child) {
                $attendance = $child->getAttendancePercentage() ?? 0;
                if ($attendance < 75) {
                    $academicAlerts->push([
                        'type' => 'attendance',
                        'child' => $child->name,
                        'message' => "Low attendance: {$attendance}%"
                    ]);
                }
            }
        }

        // Select first child by default
        $selectedChildId = $children->first()?->id ?? null;

        return view('parent.parentdashboard', compact(
            'parentUser',
            'children',
            'childrenCount',
            'overallAttendance',
            'unreadNotificationCount',
            'importantNoticeCount',
            'upcomingExamCount',
            'overallScore',
            'totalSubjects',
            'academicAlerts',
            'selectedChildId'
        ));
    }
}
