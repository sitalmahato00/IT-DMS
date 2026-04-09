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
use App\Support\Media;
use App\Support\PublicMarksheetBuilder;
use App\Support\SafeCache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        return view('landing', [
            'landingApiUrl' => route('api.landing', ['locale' => app()->getLocale()]),
        ]);
    }

    public function data(Request $request)
    {
        $locale = $this->resolveLandingLocale($request);
        app()->setLocale($locale);

        $ttl = (int) config('performance.public_data_cache_ttl', 300);

        $payload = SafeCache::remember("landing:api:payload:{$locale}:v1", $ttl, function () use ($locale) {
            return $this->buildLandingPayload($locale);
        });

        return response()->json($payload);
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

    private function buildLandingPayload(string $locale): array
    {
        $department = Department::first();
        $subjects = Subject::query()
            ->active()
            ->with(['teachers.user'])
            ->ordered()
            ->get();

        $semesters = Semester::query()
            ->orderBy('number')
            ->get();

        $teachers = Teacher::query()
            ->with('user')
            ->where('status', 'active')
            ->orderByDesc('years_of_experience')
            ->orderBy('teacher_code')
            ->get();

        $admins = User::query()
            ->where('role', 'admin')
            ->orderBy('name')
            ->get();

        $notices = Notice::published()
            ->with('creator', 'subject')
            ->orderByDesc('is_important')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $documents = StudyMaterial::published()
            ->with(['subject', 'teacher'])
            ->whereIn('visibility', ['all', 'students'])
            ->latest()
            ->limit(6)
            ->get();

        $newsItems = Gallery::active()
            ->ordered()
            ->limit(8)
            ->get()
            ->map(fn (Gallery $item) => $this->transformGalleryItem($item, $locale))
            ->filter(fn (array $item) => filled($item['image_url']))
            ->values();

        $heroImages = collect($department?->hero_images ?? [])
            ->map(fn ($path) => Media::publicUrl(is_string($path) ? $path : null))
            ->filter()
            ->values();

        if ($heroImages->isEmpty()) {
            $heroImages = $newsItems->pluck('image_url')
                ->filter()
                ->unique()
                ->values();
        }

        if ($heroImages->isEmpty()) {
            $heroImages = collect([asset('images/hero-image.jpg')]);
        }

        $leadership = $this->buildLeadershipPayload($department, $admins, $teachers, $locale);
        $aboutText = $this->cleanSnippet(
            $this->localizedField($department, 'description', 'description_nepali', $locale)
                ?: ($locale === 'ne'
                    ? 'विभागले व्यवहारिक सिकाइ, प्रयोगशाला अभ्यास, सूचना प्रणाली, र डिजिटल स्रोतहरू मार्फत विद्यार्थीलाई भविष्यका लागि तयार बनाउँछ।'
                    : 'The department blends practical learning, laboratory experience, student support, and digital services to prepare learners for modern academic and professional work.'),
            420
        );

        $subjectGroups = $subjects
            ->groupBy(fn (Subject $subject) => (string) ($subject->semester ?? 'other'))
            ->map(function ($group, string $semesterKey) {
                $first = $group->first();

                return [
                    'semester_key' => $semesterKey,
                    'semester_label' => $first?->formatted_semester ?: ('Semester ' . $semesterKey),
                    'subject_count' => $group->count(),
                    'credit_total' => (int) round($group->sum(fn (Subject $subject) => (float) ($subject->credits ?? 0))),
                    'items' => $group->take(5)->map(function (Subject $subject) {
                        return [
                            'id' => $subject->id,
                            'code' => $subject->subject_code,
                            'title' => $subject->localized_name,
                            'credits' => (int) ($subject->credits ?? 0),
                        ];
                    })->values()->all(),
                ];
            })
            ->sortBy(fn (array $group) => is_numeric($group['semester_key']) ? (int) $group['semester_key'] : 999)
            ->values()
            ->all();

        return [
            'generated_at' => now()->toIso8601String(),
            'department' => [
                'name' => $this->localizedField($department, 'name', 'name_nepali', $locale) ?: config('app.name', 'IT-DMS'),
                'short_name' => $department?->short_name ?: 'IT-DMS',
                'tagline' => $locale === 'ne'
                    ? 'प्राविधिक शिक्षा, सूचना, र डिजिटल प्रशासनका लागि एकीकृत प्रणाली'
                    : 'A unified public portal for academics, notices, resources, and campus updates.',
                'description' => $aboutText,
                'welcome_title' => $locale === 'ne'
                    ? 'हाम्रो विभागमा स्वागत छ'
                    : 'Welcome To Our Department',
                'logo_url' => $department?->getLogoUrl() ?? asset('images/default-logo.svg'),
                'hero_image' => $heroImages->first(),
                'hero_images' => $heroImages->all(),
                'address' => $this->localizedField($department, 'address', 'address_nepali', $locale),
                'email' => $department?->email,
                'phone' => $department?->phone,
                'website' => $department?->website,
                'map_url' => $this->buildDepartmentMapUrl($department),
                'message_author' => $leadership[0]['name'] ?? ($department?->principal_name ?: ($locale === 'ne' ? 'विभाग' : 'Department Office')),
                'message_author_title' => $leadership[0]['title'] ?? ($locale === 'ne' ? 'विभागीय नेतृत्व' : 'Department Leadership'),
            ],
            'stats' => [
                'students' => Student::count(),
                'teachers' => $teachers->count(),
                'subjects' => $subjects->count(),
                'semesters' => $semesters->count() ?: collect($subjectGroups)->count(),
            ],
            'leadership' => $leadership,
            'academic_highlights' => [
                [
                    'title' => $locale === 'ne' ? 'सक्रिय विषय' : 'Active Subjects',
                    'value' => (string) $subjects->count(),
                    'description' => $locale === 'ne'
                        ? 'चालु सेमेस्टरहरूमा सार्वजनिक पाठ्यक्रम र पाठ्यवस्तु हेर्नुहोस्।'
                        : 'Browse active subjects and the public-facing curriculum structure.',
                    'url' => route('subjects.index'),
                ],
                [
                    'title' => $locale === 'ne' ? 'प्रायोगिक र ल्याब' : 'Labs & Practicals',
                    'value' => (string) $subjects->where('has_lab', true)->count(),
                    'description' => $locale === 'ne'
                        ? 'प्रयोगशाला वा व्यवहारिक कम्पोनेन्ट भएका विषयहरूको झलक।'
                        : 'A quick count of subjects with lab and practical components.',
                    'url' => route('subjects.index'),
                ],
                [
                    'title' => $locale === 'ne' ? 'डाउनलोड स्रोत' : 'Download Resources',
                    'value' => (string) $documents->count(),
                    'description' => $locale === 'ne'
                        ? 'विद्यार्थी र सार्वजनिक पहुँचका लागि प्रकाशित सामग्रीहरू।'
                        : 'Published materials available for students and public visitors.',
                    'url' => route('public.resources.index'),
                ],
            ],
            'notices' => $notices->map(function (Notice $notice) {
                return [
                    'id' => $notice->id,
                    'title' => $notice->localized_title,
                    'excerpt' => $this->cleanSnippet($notice->localized_message, 140),
                    'date' => $notice->formatted_date,
                    'audience' => $notice->localized_audience_label,
                    'important' => (bool) $notice->is_important,
                    'url' => route('public.notices.index'),
                ];
            })->values()->all(),
            'news_events' => $newsItems->all(),
            'documents' => $documents->map(function (StudyMaterial $document) {
                return [
                    'id' => $document->id,
                    'title' => $document->localized_title,
                    'type_label' => $document->localized_document_type_label,
                    'subject' => $document->subject?->localized_name,
                    'size' => $document->formatted_size,
                    'uploaded_at' => optional($document->uploaded_at ?? $document->created_at)?->format('d M Y'),
                    'download_url' => !empty($document->file_path)
                        ? route('materials.download', ['id' => $document->id])
                        : route('public.resources.index'),
                ];
            })->values()->all(),
            'subject_groups' => $subjectGroups,
            'links' => [
                'about' => route('department.about'),
                'notices' => route('public.notices.index'),
                'subjects' => route('subjects.index'),
                'faculty' => route('faculty.index'),
                'resources' => route('public.resources.index'),
                'gallery' => route('gallery.index'),
                'login' => route('login'),
            ],
        ];
    }

    private function resolveLandingLocale(Request $request): string
    {
        $supported = array_keys(config('locales.supported', ['en' => 'English']));
        $locale = trim((string) $request->query('locale', app()->getLocale()));

        if (!in_array($locale, $supported, true)) {
            $locale = app()->getLocale();
        }

        if (!in_array($locale, $supported, true)) {
            $locale = config('app.locale', 'en');
        }

        return $locale;
    }

    private function buildLeadershipPayload(?Department $department, $admins, $teachers, string $locale): array
    {
        $people = collect();

        if (filled($department?->principal_name)) {
            $people->push([
                'name' => (string) $department->principal_name,
                'title' => $locale === 'ne' ? 'प्रमुख / संयोजक' : 'Principal / Coordinator',
                'subtitle' => $department->principal_email ?: $department->principal_phone,
                'bio' => $this->cleanSnippet($this->localizedField($department, 'description', 'description_nepali', $locale), 120),
                'photo_url' => null,
                'initials' => Str::upper(Str::substr(Str::slug((string) $department->principal_name, ''), 0, 2)) ?: 'DP',
            ]);
        }

        foreach ($admins as $index => $admin) {
            $people->push([
                'name' => $admin->name,
                'title' => $index === 0
                    ? ($locale === 'ne' ? 'प्रणाली प्रशासक' : 'System Administrator')
                    : ($locale === 'ne' ? 'प्रशासन' : 'Administration'),
                'subtitle' => $admin->email ?: $admin->phone,
                'bio' => $this->cleanSnippet($admin->bio, 120),
                'photo_url' => Media::publicUrl($admin->profile_photo_path),
                'initials' => Str::upper(Str::substr(Str::slug((string) $admin->name, ''), 0, 2)) ?: 'AD',
            ]);
        }

        foreach ($teachers as $teacher) {
            $people->push([
                'name' => $teacher->user?->name ?: $teacher->teacher_code,
                'title' => $teacher->specialization ?: ($locale === 'ne' ? 'संकाय सदस्य' : 'Faculty Member'),
                'subtitle' => $teacher->qualification ?: $teacher->teacher_code,
                'bio' => $this->cleanSnippet($teacher->bio ?: $teacher->user?->bio, 120),
                'photo_url' => Media::publicUrl($teacher->profile_photo_path) ?: Media::publicUrl($teacher->user?->profile_photo_path),
                'initials' => Str::upper(Str::substr(Str::slug((string) ($teacher->user?->name ?: $teacher->teacher_code), ''), 0, 2)) ?: 'FM',
            ]);
        }

        return $people
            ->filter(fn (array $person) => filled($person['name']))
            ->unique('name')
            ->take(3)
            ->values()
            ->all();
    }

    private function transformGalleryItem(Gallery $item, string $locale): array
    {
        $category = Str::lower(trim((string) $item->category));
        $title = $this->localizedField($item, 'title', 'title_ne', $locale) ?: ($locale === 'ne' ? 'क्याम्पस अपडेट' : 'Campus Update');
        $description = $this->localizedField($item, 'description', 'description_ne', $locale);

        return [
            'id' => $item->id,
            'title' => $title,
            'excerpt' => $this->cleanSnippet($description, 180),
            'category' => $category ?: 'events',
            'category_label' => $this->galleryCategoryLabel($category, $locale),
            'date' => optional($item->created_at)->format('d M Y'),
            'image_url' => Media::publicUrl($item->image_path),
            'url' => route('gallery.index'),
        ];
    }

    private function galleryCategoryLabel(string $category, string $locale): string
    {
        $labels = [
            'events' => ['ne' => 'कार्यक्रम', 'en' => 'Events'],
            'activities' => ['ne' => 'गतिविधि', 'en' => 'Activities'],
            'campus' => ['ne' => 'क्याम्पस', 'en' => 'Campus'],
            'students' => ['ne' => 'विद्यार्थी', 'en' => 'Students'],
            'faculty' => ['ne' => 'संकाय', 'en' => 'Faculty'],
            'facilities' => ['ne' => 'सुविधा', 'en' => 'Facilities'],
        ];

        return $labels[$category][$locale] ?? ($locale === 'ne' ? 'अपडेट' : 'Updates');
    }

    private function localizedField($model, string $field, string $localeField, string $locale): string
    {
        if (!$model) {
            return '';
        }

        $localized = trim((string) data_get($model, $locale === 'ne' ? $localeField : $field, ''));
        $fallback = trim((string) data_get($model, $field, ''));

        return $localized !== '' ? $localized : $fallback;
    }

    private function cleanSnippet(?string $value, int $limit = 180): string
    {
        $clean = preg_replace('/\s+/u', ' ', trim(strip_tags((string) $value)));

        if (!$clean) {
            return '';
        }

        return Str::limit($clean, $limit);
    }

    private function buildDepartmentMapUrl(?Department $department): ?string
    {
        if (!$department) {
            return null;
        }

        if (!empty($department->latitude) && !empty($department->longitude)) {
            return 'https://www.google.com/maps?q=' . rawurlencode((string) $department->latitude . ',' . (string) $department->longitude);
        }

        $query = trim(implode(' ', array_filter([
            $department->address,
            $department->city,
            $department->district,
            $department->province,
        ])));

        return $query !== '' ? 'https://www.google.com/maps?q=' . rawurlencode($query) : null;
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
