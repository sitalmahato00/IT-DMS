<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Exam;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Notice;
use App\Models\Gallery;
use App\Models\StudyMaterial;
use App\Models\Student;
use App\Models\User;
use App\Support\PublicMarksheetBuilder;
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

        $examResultMeta = SafeCache::remember('landing:exam-result-meta:v1', $ttl, fn () => $this->buildExamResultMeta());
        $examResultSearch = $this->resolveExamResultSearch($request, $examResultMeta);

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
            'stats',
            'examResultMeta',
            'examResultSearch'
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

    public function examResultPrint(Request $request)
    {
        $examResultMeta = SafeCache::remember('landing:exam-result-meta:v1', (int) config('performance.public_data_cache_ttl', 300), fn () => $this->buildExamResultMeta());
        $examResultSearch = $this->resolveExamResultSearch($request, $examResultMeta, true);

        if (!$examResultSearch['searchAttempted'] || !$examResultSearch['student'] || !$examResultSearch['payload']) {
            return redirect()->route('home', array_filter($request->query(), fn ($value) => $value !== null && $value !== ''))
                ->with('error', $examResultSearch['error'] ?: 'Published exam result not found.');
        }

        $department = Department::first() ?: (object) [
            'name' => config('app.name', 'IT DMS'),
            'address' => null,
            'city' => null,
            'district' => null,
            'email' => null,
            'phone' => null,
        ];

        return view('admin.marks.marksheet-print', array_merge($examResultSearch['payload'], [
            'department' => $department,
            'departmentLogoUrl' => method_exists($department, 'getLogoUrl') ? $department->getLogoUrl() : asset('images/default-logo.svg'),
        ]));
    }

    /**
     * Build the public exam result search metadata used by the landing page.
     */
    private function buildExamResultMeta(): array
    {
        $publishedExamQuery = Exam::query()
            ->published()
            ->whereIn('exam_category', ['assessment', 'ctevt']);

        $years = (clone $publishedExamQuery)
            ->whereNotNull('academic_year')
            ->pluck('academic_year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        $semesters = (clone $publishedExamQuery)
            ->whereNotNull('semester')
            ->pluck('semester')
            ->map(fn ($semester) => trim((string) $semester))
            ->filter()
            ->unique()
            ->sort(fn ($left, $right) => (int) $left <=> (int) $right)
            ->values()
            ->all();

        $assessmentMap = (clone $publishedExamQuery)
            ->where('exam_category', 'assessment')
            ->whereNotNull('assessment_number')
            ->get(['academic_year', 'semester', 'assessment_number'])
            ->groupBy(function ($exam) {
                $year = trim((string) ($exam->academic_year ?? 'all'));
                $semester = trim((string) ($exam->semester ?? 'all'));

                return $year . '|' . $semester;
            })
            ->map(function ($group) {
                return $group->pluck('assessment_number')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            })
            ->toArray();

        $assessmentMap['all|all'] = (clone $publishedExamQuery)
            ->where('exam_category', 'assessment')
            ->whereNotNull('assessment_number')
            ->pluck('assessment_number')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [
            'years' => $years,
            'semesters' => $semesters,
            'assessmentMap' => $assessmentMap,
        ];
    }

    /**
     * Resolve the public exam result search data for the landing page.
     */
    private function resolveExamResultSearch(Request $request, array $examResultMeta, bool $searchForced = false): array
    {
        $searchAttempted = $request->boolean('search_exam_result') || $searchForced;
        $dobBs = trim((string) $request->query('dob_bs', ''));
        
        // Normalize BS date format: 2058-2-1 -> 2058-02-01
        if (!empty($dobBs)) {
            $parts = explode('-', $dobBs);
            if (count($parts) === 3) {
                $dobBs = sprintf('%04d-%02d-%02d', (int)$parts[0], (int)$parts[1], (int)$parts[2]);
            }
        }
        
        // Convert BS date to AD for database query
        $dobAd = '';
        if (!empty($dobBs)) {
            try {
                $dobAd = \App\Helpers\NepaliContentHelper::convertBsToAd($dobBs);
                // Normalize date format to YYYY-MM-DD
                if (!empty($dobAd)) {
                    $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $dobAd);
                    $dobAd = $dateObj->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // If conversion fails, try to use as-is
                $dobAd = $dobBs;
            }
        }

        $filters = [
            'academic_year' => trim((string) $request->query('academic_year', $examResultMeta['years'][0] ?? '')),
            'semester' => trim((string) $request->query('semester', $examResultMeta['semesters'][0] ?? '')),
            'exam_category' => trim((string) $request->query('exam_category', 'assessment')) ?: 'assessment',
            'assessment_number' => trim((string) $request->query('assessment_number', '')),
            'student_id' => trim((string) $request->query('student_id', '')),
            'dob' => $dobAd, // AD date for query
            'dob_bs' => $dobBs, // BS date for display (normalized)
        ];

        if ($filters['academic_year'] === '' && !empty($examResultMeta['years'][0])) {
            $filters['academic_year'] = (string) $examResultMeta['years'][0];
        }

        if ($filters['semester'] === '' && !empty($examResultMeta['semesters'][0])) {
            $filters['semester'] = (string) $examResultMeta['semesters'][0];
        }

        $assessmentNumbers = $this->resolveAssessmentNumbers($examResultMeta['assessmentMap'] ?? [], $filters);

        $student = null;
        $payload = null;
        $error = null;

        if ($searchAttempted) {
            if ($filters['student_id'] === '' || $dobBs === '') {
                $error = 'Please enter both Student ID / Roll No and Date of Birth.';
            } else {
                $student = $this->findPublicExamResultStudent($filters);

                if (!$student) {
                    $error = 'No student matched the provided ID / Roll No and DOB.';
                } else {
                    /** @var PublicMarksheetBuilder $builder */
                    $builder = app(PublicMarksheetBuilder::class);
                    $payload = $builder->buildForSearch($student, $filters);

                    if (($payload['marksheetData']['exam_marks'] ?? collect())->isEmpty()) {
                        $studentSemester = trim((string) ($student->semester ?? ''));
                        $studentAcademicYear = trim((string) ($student->academic_year_bs ?? $student->academic_year ?? ''));

                        if ($studentSemester !== '' || $studentAcademicYear !== '') {
                            $fallbackFilters = array_merge($filters, [
                                'semester' => $studentSemester !== '' ? $studentSemester : $filters['semester'],
                                'academic_year' => $studentAcademicYear !== '' ? $studentAcademicYear : $filters['academic_year'],
                            ]);

                            $fallbackAssessmentNumbers = $this->resolveAssessmentNumbers($examResultMeta['assessmentMap'] ?? [], $fallbackFilters);
                            $fallbackPayload = $builder->buildForSearch($student, $fallbackFilters);

                            if (($fallbackPayload['marksheetData']['exam_marks'] ?? collect())->isNotEmpty()) {
                                $filters = $fallbackPayload['filters'];
                                $assessmentNumbers = $fallbackAssessmentNumbers;
                                $payload = $fallbackPayload;
                            }
                        }
                    }

                    if (($payload['marksheetData']['exam_marks'] ?? collect())->isEmpty()) {
                        $error = 'No published exam result was found for the selected filters.';
                    }
                }
            }
        }

        return [
            'searchAttempted' => $searchAttempted,
            'filters' => $filters,
            'assessmentNumbers' => $assessmentNumbers,
            'student' => $student,
            'payload' => $payload,
            'error' => $error,
            'printUrl' => route('public.exam-result.print', $filters),
        ];
    }

    /**
     * Find a student using Student ID / Roll No and DOB.
     */
    private function findPublicExamResultStudent(array $filters): ?Student
    {
        $studentId = trim((string) ($filters['student_id'] ?? ''));
        $dob = trim((string) ($filters['dob'] ?? ''));

        if ($studentId === '' || $dob === '') {
            return null;
        }

        // Normalize date format
        try {
            $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $dob);
            $dob = $dateObj->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, return null
            return null;
        }

        $query = Student::with('user');

        $query->where(function ($builder) use ($studentId) {
            $builder->where('id', $studentId)
                ->orWhere('roll_no', 'like', '%' . $studentId . '%')
                ->orWhereHas('user', function ($userQuery) use ($studentId) {
                    $userQuery->where('id', $studentId);
                });
        });

        $query->whereDate('date_of_birth', $dob);

        return $query->first();
    }

    /**
     * Resolve assessment numbers for the currently selected academic year / semester.
     */
    private function resolveAssessmentNumbers(array $assessmentMap, array $filters): array
    {
        if (($filters['exam_category'] ?? 'assessment') !== 'assessment') {
            return [];
        }

        $year = trim((string) ($filters['academic_year'] ?? ''));
        $semester = trim((string) ($filters['semester'] ?? ''));

        $keysToCheck = [];

        if ($year !== '' || $semester !== '') {
            $keysToCheck[] = ($year !== '' ? $year : 'all') . '|' . ($semester !== '' ? $semester : 'all');
        }

        $keysToCheck[] = 'all|all';

        foreach ($keysToCheck as $key) {
            if (!empty($assessmentMap[$key]) && is_array($assessmentMap[$key])) {
                return $assessmentMap[$key];
            }
        }

        return [];
    }
}
