<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ParentChildrenController extends Controller
{
    /**
     * Display a listing of children associated with the parent.
     */
    public function index()
    {
        $parent = Auth::user();
        
        // Get all students where parent_id matches the parent's associated ID
        $children = Student::where('parent_id', $parent->id)
            ->with('user', 'subjects', 'attendanceRecords')
            ->get();

        return view('parent.children.index', compact('children'));
    }

    /**
     * Display the specified child's profile.
     */
    public function show($id)
    {
        $parent = Auth::user();
        $child = Student::with('user', 'subjects', 'attendanceRecords', 'examMarks')
            ->findOrFail($id);

        // Ensure the child belongs to this parent
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        // Calculate attendance percentage
        $totalAttendance = $child->getAttendancePercentage() ?? 0;

        return view('parent.children.show', compact('child', 'totalAttendance'));
    }
}

