<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamMark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // FIX: Batch load all attendance data ONCE instead of N queries in loops
        $attendanceStats = collect();
        if ($childrenCount > 0) {
            $childrenIds = $children->pluck('id')->toArray();
            
            // Single query for all attendance data
            $stats = DB::table('attendance')
                ->whereIn('student_id', $childrenIds)
                ->select(
                    'student_id',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count')
                )
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');
            
            // Calculate percentages in memory
            foreach ($children as $child) {
                $childStats = $stats->get($child->id);
                $percentage = 0;
                
                if ($childStats && $childStats->total > 0) {
                    $percentage = ($childStats->present_count / $childStats->total) * 100;
                }
                
                $attendanceStats[$child->id] = round($percentage, 1);
            }
        }

        // Calculate overall attendance using cached data
        $overallAttendance = 0;
        if ($childrenCount > 0) {
            $totalAttendance = $attendanceStats->sum();
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

        // Create academic alerts using cached attendance data
        $academicAlerts = collect();
        if ($childrenCount > 0) {
            foreach ($children as $child) {
                $attendance = $attendanceStats->get($child->id, 0);
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

