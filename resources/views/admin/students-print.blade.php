<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\College;
use Illuminate\Http\Request;

class StudentPrintController extends Controller
{
    /**
     * Display printable student list
     */
    public function printList(Request $request)
    {
        $query = User::where('role', 'student')->with('student');
        
        if ($request->semester) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $students = $query->get();
        $college = College::first();
        
        return view('admin.print.students-list', compact('students', 'college'));
    }
}
</parameter>
</create_file>
