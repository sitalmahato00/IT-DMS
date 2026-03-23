<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\SubjectTeacher;
use App\Models\Teacher;

class TeacherNoticesController extends Controller
{
    /**
     * Get teacher's assigned subject IDs and semesters
     */
    private function getTeacherAssignments()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return [
                'subjectIds' => [],
                'semesters' => [],
                'teacherId' => null,
            ];
        }
        
        $assignments = SubjectTeacher::where('teacher_id', $teacher->id)->get();
        
        return [
            'subjectIds' => $assignments->pluck('subject_id')->toArray(),
            'semesters' => $assignments->pluck('semester')->filter()->unique()->values()->toArray(),
            'teacherId' => $teacher->id,
        ];
    }

    /**
     * Display notices for teacher
     */
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        $sort = $request->get('sort', 'latest');
        $subjectFilter = $request->get('subject', '');
        
        $assignments = $this->getTeacherAssignments();
        $subjectIds = $assignments['subjectIds'];
        $semesters = $assignments['semesters'];
        $teacherId = $assignments['teacherId'];
        
        // Get subjects for filter dropdown
        $subjects = SubjectTeacher::where('teacher_id', $teacherId)
            ->with('subject')
            ->get()
            ->map(function ($st) {
                if (!$st->subject) return null;
                return [
                    'id' => $st->subject->id,
                    'name' => $st->subject->subject_name,
                    'code' => $st->subject->subject_code,
                ];
            })
            ->filter()
            ->values();
        
        // Build query - show notices relevant to teacher's assignments
        $noticesQuery = Notice::query();
        
        // Filter by teacher's subjects or general faculty notices
        $noticesQuery->where(function ($q) use ($subjectIds, $semesters, $teacherId) {
            // Notices created by this teacher
            $q->where('created_by', $teacherId)
            // OR notices for teacher's assigned subjects
            ->orWhereIn('subject_id', $subjectIds)
            // OR notices for teacher's assigned semesters
            ->orWhereIn('semester', $semesters)
            // OR general notices for faculty/all
            ->orWhereIn('audience', ['faculty', 'all']);
        });

        if ($sort === 'latest') {
            $noticesQuery->orderBy('created_at', 'desc');
        } else {
            $noticesQuery->orderBy('created_at', 'asc');
        }

        if (!empty($search)) {
            $noticesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by specific subject if selected
        if (!empty($subjectFilter) && in_array($subjectFilter, $subjectIds)) {
            $noticesQuery->where('subject_id', $subjectFilter);
        }

        $notices = $noticesQuery->paginate(20)->withQueryString();

        // Get recent notices count (filtered by teacher's scope)
        $recentCount = Notice::where(function ($q) use ($subjectIds, $semesters, $teacherId) {
            $q->where('created_by', $teacherId)
            ->orWhereIn('subject_id', $subjectIds)
            ->orWhereIn('semester', $semesters)
            ->orWhereIn('audience', ['faculty', 'all']);
        })->where('created_at', '>=', now()->subDays(7))->count();

        // Get unique audiences (categories)
        $categories = Notice::distinct('audience')
            ->whereNotNull('audience')
            ->pluck('audience')
            ->map(function ($audience) {
                $audienceMap = [
                    'all' => 'All',
                    'students' => 'Students',
                    'faculty' => 'Faculty',
                    'parents' => 'Parents',
                ];
                return ['name' => $audienceMap[$audience] ?? $audience];
            });

        return view('teacher.notices', [
            'notices' => $notices,
            'recentCount' => $recentCount,
            'categories' => $categories,
            'subjects' => $subjects,
            'selectedSubject' => $subjectFilter,
        ]);
    }

    /**
     * Show form to create a notice.
     */
    public function create()
    {
        $teacher = auth()->user()?->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.notices')
                ->with('error', 'Teacher profile not found.');
        }

        $subjects = SubjectTeacher::where('teacher_id', $teacher->id)
            ->with('subject')
            ->get()
            ->map(function ($assignment) {
                $subject = $assignment->subject;

                return [
                    'id' => $subject?->id,
                    'name' => $subject?->subject_name ?? 'N/A',
                    'code' => $subject?->subject_code ?? '',
                    'semester' => $subject?->semester ?? $assignment->semester,
                ];
            })
            ->filter(fn ($subject) => !empty($subject['id']))
            ->values();

        return view('teacher.notices-create', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a newly created notice by teacher.
     */
    public function store(Request $request)
    {
        $teacher = auth()->user()?->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.notices')
                ->with('error', 'Teacher profile not found.');
        }

        $subjectIds = SubjectTeacher::where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'message' => 'required|string',
            'audience' => 'required|in:all,students,faculty,parents',
            'subject_id' => 'nullable|integer',
            'semester' => 'nullable|string|max:20',
            'is_important' => 'nullable|boolean',
        ]);

        $subjectId = !empty($validated['subject_id']) ? (int) $validated['subject_id'] : null;
        if ($subjectId && !in_array($subjectId, $subjectIds, true)) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'You can only post notices for your own subjects.']);
        }

        Notice::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'audience' => $validated['audience'],
            'status' => 'published',
            'semester' => $validated['semester'] ?? null,
            'subject_id' => $subjectId,
            'is_important' => $request->boolean('is_important'),
            'published_at' => now(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('teacher.notices')
            ->with('success', 'Notice posted successfully.');
    }
}
