<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Subject;
use App\Support\SafeCache;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        $selectedSemester = $request->query('semester');
        $initialQuery = (string) $request->query('q', '');
        $ttl = (int) config('performance.public_data_cache_ttl', 300);
        $semesterKey = !empty($selectedSemester) && $selectedSemester !== 'all'
            ? (string) $selectedSemester
            : 'all';

        $semesters = SafeCache::remember('subjects:semesters:v1', $ttl, fn () => Semester::orderBy('id')->get());

        $subjects = SafeCache::remember("subjects:index:{$semesterKey}:v1", $ttl, function () use ($selectedSemester) {
            $subjectsQuery = Subject::query()
                ->where('status', 'active')
                ->with(['teacher.user', 'teachers.user'])
                ->orderBy('semester')
                ->orderBy('subject_name');

            if (!empty($selectedSemester) && $selectedSemester !== 'all') {
                $subjectsQuery->where('semester', $selectedSemester);
            }

            return $subjectsQuery->get();
        });

        return view('subjects.index', compact(
            'subjects',
            'semesters',
            'selectedSemester',
            'initialQuery',
        ));
    }
}

