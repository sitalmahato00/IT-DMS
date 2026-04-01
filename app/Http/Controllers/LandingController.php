<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Notice;
use App\Models\Gallery;
use App\Models\StudyMaterial;
use App\Models\Student;
use App\Models\User;
use App\Support\SafeCache;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $ttl = (int) config('performance.public_data_cache_ttl', 300);
        $departmentTtl = (int) config('performance.department_cache_ttl', 600);
        $selectedSemester = (string) $request->query('semester', '');
        $semesterKey = $selectedSemester !== '' ? $selectedSemester : 'all';

        // Department/college info (if existing), else use placeholder service values in view.
        $department = SafeCache::remember('landing:department:v1', $departmentTtl, fn () => Department::first());

        // Semester data for filtering (if present)
        $semesters = SafeCache::remember('landing:semesters:v1', $ttl, fn () => Semester::orderBy('id')->get());

        // Subject/course listing with relations for teacher and method/assigned teachers
        $subjects = SafeCache::remember("landing:subjects:{$semesterKey}:v1", $ttl, function () use ($selectedSemester) {
            $subjectsQuery = Subject::query()
                ->where('status', 'active')
                ->with(['teacher.user', 'teachers.user'])
                ->orderBy('semester')
                ->orderBy('subject_name');

            if ($selectedSemester !== '') {
                $subjectsQuery->where('semester', $selectedSemester);
            }

            return $subjectsQuery->get();
        });

        // Faculty list from teacher model; keep the department connection flexible.
        // TeacherSeeder only sets the department on the related `users` table, so filter both places.
        $departmentKey = $department?->short_name ?: $department?->name;

        $teachers = SafeCache::remember('landing:teachers:' . md5((string) $departmentKey) . ':v1', $ttl, function () use ($departmentKey) {
            $teachersQuery = Teacher::with(['user', 'subjects'])->where('status', 'active');

            if (!empty($departmentKey)) {
                $teachersQuery->where(function ($q) use ($departmentKey) {
                    $q->where('department', $departmentKey)
                        ->orWhereHas('user', fn ($uq) => $uq->where('department', $departmentKey));
                });
            }

            $teachers = $teachersQuery->get();

            // If department filter produced no results (common in seed/demo data), fall back to all active teachers.
            if ($teachers->isEmpty()) {
                return Teacher::with(['user', 'subjects'])
                    ->where('status', 'active')
                    ->get();
            }

            return $teachers;
        });

        // HOD / Admin leadership list (show all on landing page)
        $hods = SafeCache::remember('landing:admins:v1', $ttl, function () {
            return User::where('role', 'admin')
                ->orderBy('name')
                ->get();
        });

        // Backward-compat / convenience: first HOD
        $hod = $hods->first();

        // Lab list from subjects with lab flag and/or lab technician
        $labs = $subjects->filter(fn ($subject) =>
            ($subject->has_lab ?? false) || !empty($subject->lab_technician_id)
        );

        // Notices and announcements (published)
        $notices = SafeCache::remember('landing:notices:v1', $ttl, function () {
            return Notice::published()
                ->with('creator', 'subject')
                ->orderBy('published_at', 'desc')
                ->limit(6)
                ->get();
        });

        // Documents/resources: landing page should show materials intended for general/student access.
        $documents = SafeCache::remember('landing:documents:v1', $ttl, function () {
            return StudyMaterial::published()
                ->with(['subject', 'teacher'])
                ->whereIn('visibility', ['all', 'students'])
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        });

        // Gallery preview (public)
        $galleryItems = SafeCache::remember('landing:gallery:v1', $ttl, function () {
            return Gallery::active()
                ->ordered()
                ->limit(8)
                ->get();
        });

        // Stats for the landing page
        $stats = [
            'students' => SafeCache::remember('landing:stats:students:v1', $ttl, fn () => Student::count()),
            'subjects' => $subjects->count(),
            'labs' => $labs->count(),
            'teachers' => $teachers->count(),
        ];

        return view('landing', compact(
            'department',
            'semesters',
            'selectedSemester',
            'subjects',
            'teachers',
            'hods',
            'hod',
            'labs',
            'notices',
            'documents',
            'galleryItems',
            'stats'
        ));
    }

    public function about(Request $request, $id = null)
    {
        // Get the department
        $department = $id ? Department::findOrFail($id) : Department::first();

        if (!$department) {
            abort(404, 'Department not found');
        }

        return view('department.about', compact('department'));
    }
}
