<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    /**
     * Display the parent dashboard.
     */
    public function index()
    {
        $parent = Auth::user();
        
        // Get all children for this parent
        $children = Student::where('parent_id', $parent->id)->get();
        $childrenCount = $children->count();

        // Calculate overall attendance percentage
       $overallAttendance = 0;
        if ($childrenCount > 0) {
            $totalAttendance = 0;
            foreach ($children as $child) {
                $totalAttendance += $child->getAttendancePercentage() ?? 0;
            }
            $overallAttendance = $totalAttendance / $childrenCount;
        }

        // Get unread notices (recent notices from last 7 days)
        $unreadNotices = Notice::where('audience', 'parent')
            ->where('status', 'published')
            ->where('created_at', '>', now()->subDays(7))
            ->count();

        return view('parent.parentdashboard', compact('childrenCount', 'overallAttendance', 'unreadNotices'));
    }
}
