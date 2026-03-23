<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Notice;
use App\Models\Gallery;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Department/college info (if existing), else use placeholder service values in view.
        $department = College::first();

        // Semester data for filtering (if present)
        $semesters = Semester::orderBy('id')->get();

        $selectedSemester = $request->query('semester');

        // Subject/course listing with relations for teacher and method/assigned teachers
        $subjectsQuery = Subject::query()
            ->where('status', 'active')
            ->with(['teacher.user', 'teachers.user'])
            ->orderBy('semester')
            ->orderBy('subject_name');

        if ($selectedSemester) {
            $subjectsQuery->where('semester', $selectedSemester);
        }

        $subjects = $subjectsQuery->get();

        // Faculty list from teacher model; keep the department connection flexible
        $teachers = Teacher::with(['user', 'subjects'])->where('status', 'active');

        if ($department && !empty($department->name)) {
            $teachers = $teachers->where('department', $department->short_name ?: $department->name);
        }

        $teachers = $teachers->get();

        // Get HOD (Head of Department) - assuming admin role in the department
        $hod = null;
        if ($department && !empty($department->name)) {
            $hod = \App\Models\User::where('role', 'admin')
                ->where('department', $department->short_name ?: $department->name)
                ->first();
        }

        // Lab list from subjects with lab flag and/or lab technician
        $labs = $subjects->filter(fn ($subject) =>
            ($subject->has_lab ?? false) || !empty($subject->lab_technician_id)
        );

        // Notices and announcements (published)
        $notices = Notice::published()
            ->with('creator', 'subject')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Documents/resources: keep the public landing page limited to "all" visibility.
        $documents = StudyMaterial::published()
            ->with(['subject', 'teacher'])
            ->where('visibility', 'all')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // Gallery preview (public)
        $galleryItems = Gallery::active()
            ->ordered()
            ->limit(8)
            ->get();

        // Stats for the landing page
        $stats = [
            'students' => \App\Models\Student::count(),
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
            'hod',
            'labs',
            'notices',
            'documents',
            'galleryItems',
            'stats'
        ));
    }
}
