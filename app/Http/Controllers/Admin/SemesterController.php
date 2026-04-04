<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Traits\LogsActivity;

class SemesterController extends Controller
{
    use LogsActivity;

    /**
     * Display all semesters.
     */
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('number')->get();

        // FIX #3: Pre-calculate all counts in ONE query (not N queries)
        $counts = DB::table('semesters')
            ->leftJoin('students', function($j) {
                $j->on(DB::raw('CAST(students.semester AS CHAR)'), '=', 'semesters.number')
                  ->where('students.status', 'active')
                  ->where('students.is_alumni', false);
            })
            ->leftJoin('subjects as subjects_all', function($j) {
                $j->on(DB::raw('CAST(subjects_all.semester AS CHAR)'), '=', 'semesters.number')
                  ->where('subjects_all.status', 'active');
            })
            ->leftJoin('subjects as elective_subjects', function($j) {
                $j->on(DB::raw('CAST(elective_subjects.semester AS CHAR)'), '=', 'semesters.number')
                  ->where('elective_subjects.subject_type', 'elective')
                  ->where('elective_subjects.status', 'active');
            })
            ->select(
                'semesters.id',
                'semesters.number',
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw('COUNT(DISTINCT subjects_all.id) as subject_count'),
                DB::raw('COUNT(DISTINCT elective_subjects.id) as elective_count')
            )
            ->groupBy('semesters.id', 'semesters.number')
            ->get()
            ->keyBy('id');

        // Build enriched data as a collection of stdClass objects (avoids model readonly)
        $enrichedSemesters = $semesters->map(function ($semester) use ($counts) {
            $obj = $semester->toArray();
            $semesterCounts = $counts->get($semester->id);
            $obj['student_count'] = $semesterCounts->student_count ?? 0;
            $obj['subject_count'] = $semesterCounts->subject_count ?? 0;
            $obj['elective_count'] = $semesterCounts->elective_count ?? 0;
            return (object) $obj;
        });

        // Overall stats
        $stats = [
            'total'    => $semesters->count(),
            'open'     => $semesters->where('status', 'open')->count(),
            'active'   => $semesters->where('is_active', true)->count(),
            'upcoming' => $semesters->where('status', 'upcoming')->count(),
        ];

        return view('admin.semesters', compact('enrichedSemesters', 'semesters', 'stats'));
    }

    /**
     * Store a new semester.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number'          => 'required|integer|min:1|max:8',
            'name'            => 'required|string|max:100',
            'name_ne'         => 'nullable|string|max:100',
            'academic_year'   => 'nullable|string|max:20',
            'start_date'      => 'nullable|date',
            'start_date_bs'   => 'nullable|string|max:20',
            'end_date'        => 'nullable|date',
            'end_date_bs'     => 'nullable|string|max:20',
            'status'          => 'required|in:open,closed,upcoming',
            'is_active'       => 'sometimes|boolean',
            'max_credits'     => 'nullable|integer|min:1|max:40',
            'total_weeks'     => 'nullable|integer|min:1|max:52',
            'remarks'         => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $semester = Semester::create($validated);
        $this->logActivity('Semester', 'Created semester: ' . $semester->name);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'semester' => $semester]);
        }
        return redirect()->route('admin.semesters')->with('success', 'Semester created successfully.');
    }

    /**
     * Show a single semester for edit (AJAX).
     */
    public function show(int $id)
    {
        $semester = Semester::findOrFail($id);
        return response()->json($semester);
    }

    /**
     * Update a semester.
     */
    public function update(Request $request, int $id)
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'number'          => 'required|integer|min:1|max:8',
            'name'            => 'required|string|max:100',
            'name_ne'         => 'nullable|string|max:100',
            'academic_year'   => 'nullable|string|max:20',
            'start_date'      => 'nullable|date',
            'start_date_bs'   => 'nullable|string|max:20',
            'end_date'        => 'nullable|date',
            'end_date_bs'     => 'nullable|string|max:20',
            'status'          => 'required|in:open,closed,upcoming',
            'is_active'       => 'sometimes|boolean',
            'max_credits'     => 'nullable|integer|min:1|max:40',
            'total_weeks'     => 'nullable|integer|min:1|max:52',
            'remarks'         => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $semester->update($validated);
        $this->logActivity('Semester', 'Updated semester: ' . $semester->name);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'semester' => $semester->fresh()]);
        }
        return redirect()->route('admin.semesters')->with('success', 'Semester updated successfully.');
    }

    /**
     * Toggle open/closed status.
     */
    public function toggle(int $id)
    {
        $semester = Semester::findOrFail($id);
        $newStatus = $semester->status === 'open' ? 'closed' : 'open';
        $semester->update(['status' => $newStatus]);
        $this->logActivity('Semester', "Toggled semester {$semester->name} to {$newStatus}");

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
            'message' => "Semester {$newStatus} successfully.",
        ]);
    }

    /**
     * Toggle active status of a semester.
     */
    public function setActive(int $id)
    {
        $semester = Semester::findOrFail($id);
        $newActive = !$semester->is_active;
        $semester->update(['is_active' => $newActive]);
        $this->logActivity('Semester', ($newActive ? 'Activated' : 'Deactivated') . " semester: {$semester->name}");

        return response()->json([
            'success' => true,
            'message' => "Semester '{$semester->name}' " . ($newActive ? 'activated' : 'deactivated') . ".",
            'is_active' => $newActive,
        ]);
    }

    /**
     * Delete a semester.
     */
    public function destroy(int $id)
    {
        $semester = Semester::findOrFail($id);
        $name = $semester->name;
        $semester->delete();
        $this->logActivity('Semester', "Deleted semester: {$name}");

        return response()->json(['success' => true, 'message' => 'Semester deleted.']);
    }
}
