<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('id')->get();

        $selectedSemester = $request->query('semester');
        $initialQuery = (string) $request->query('q', '');

        $subjectsQuery = Subject::query()
            ->where('status', 'active')
            ->with(['teacher.user', 'teachers.user'])
            ->orderBy('semester')
            ->orderBy('subject_name');

        if (!empty($selectedSemester) && $selectedSemester !== 'all') {
            $subjectsQuery->where('semester', $selectedSemester);
        }

        $subjects = $subjectsQuery->get();

        return view('subjects.index', compact(
            'subjects',
            'semesters',
            'selectedSemester',
            'initialQuery',
        ));
    }
}

