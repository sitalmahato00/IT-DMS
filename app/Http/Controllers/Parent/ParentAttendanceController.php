<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ParentAttendanceController extends Controller
{
    /**
     * Display attendance for all children of the parent.
     */
    public function index(Request $request)
    {
        $parent = Auth::user();
        
        // Get all children for this parent
        $children = Student::where('parent_id', $parent->id)->get();
        
        if ($children->isEmpty()) {
            return view('parent.attendance.index', ['children' => collect(), 'attendance' => collect()]);
        }

        $childrenIds = $children->pluck('id');
        
        // Get attendance records for all children
        $query = Attendance::whereIn('student_id', $childrenIds);

        // Filter by subject if provided
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by date range if provided
        if ($request->filled('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        $attendance = $query->with('student', 'subject')
            ->orderBy('attendance_date', 'desc')
            ->paginate(50);

        // Get subjects for filter dropdown
        $subjects = Subject::whereIn('id', function ($q) use ($childrenIds) {
            $q->select('subject_id')
                ->from('subject_students')
                ->whereIn('student_id', $childrenIds)
                ->distinct();
        })->get();

        // FIX: Batch load all attendance percentages with a single query
        $attendancePercentages = [];
        if (!$children->isEmpty()) {
            $attendanceIds = $children->pluck('id')->toArray();
            
            // Single batch query for all attendance percentages
            $stats = DB::table('attendance')
                ->whereIn('student_id', $attendanceIds)
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
                if ($childStats && $childStats->total > 0) {
                    $attendancePercentages[$child->id] = round(
                        ($childStats->present_count / $childStats->total) * 100,
                        1
                    );
                } else {
                    $attendancePercentages[$child->id] = 0;
                }
            }
        }

        return view('parent.attendance.index', compact('children', 'attendance', 'subjects', 'attendancePercentages'));
    }

    /**
     * Display attendance details for a specific child.
     */
    public function showChild($childId)
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
            ->paginate(50);

        $attendancePercentage = $child->getAttendancePercentage() ?? 0;
        $subjects = $child->subjects;

        return view('parent.attendance.child', compact('child', 'attendance', 'attendancePercentage', 'subjects'));
    }
}

