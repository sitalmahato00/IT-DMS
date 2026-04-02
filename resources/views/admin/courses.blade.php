@extends('admin.layouts.app')

@section('title', 'Courses')

@section('styles')
<script>document.documentElement.classList.add('courses-ui-enhanced');</script>
<style>
    html.courses-ui-enhanced:not(.dark) .courses-hero-panel {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        border: 1px solid #d90033;
        background: linear-gradient(to right, #ff0037, #d90033, #b2002f);
        padding: 1.5rem;
        box-shadow: 0 20px 45px -25px rgba(178, 0, 47, 0.55);
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel::before,
    html.courses-ui-enhanced:not(.dark) .courses-hero-panel::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel::before {
        top: -3rem;
        right: -2rem;
        width: 11rem;
        height: 11rem;
        background: rgba(255, 255, 255, 0.15);
        filter: blur(10px);
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel::after {
        bottom: -4rem;
        left: -1rem;
        width: 13rem;
        height: 13rem;
        background: rgba(255, 255, 255, 0.09);
        filter: blur(16px);
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel > * {
        position: relative;
        z-index: 1;
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel h1 {
        color: #fff;
        font-size: clamp(2rem, 2.4vw, 2.65rem);
        letter-spacing: -0.04em;
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel nav a {
        color: #ffe5ea;
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel nav a:hover {
        color: #fff;
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel nav span {
        color: rgba(255, 255, 255, 0.88);
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel nav span.text-red-600 {
        color: #fff;
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel nav i {
        color: rgba(255, 255, 255, 0.55);
    }

    html.courses-ui-enhanced:not(.dark) .courses-hero-panel button {
        box-shadow: 0 16px 30px -20px rgba(15, 23, 42, 0.45);
    }

    html.courses-ui-enhanced:not(.dark) .courses-stats > .grid > div {
        position: relative;
        overflow: hidden;
        border-width: 2px;
        border-radius: 0.9rem;
        box-shadow: 0 18px 35px -28px rgba(15, 23, 42, 0.22);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    html.courses-ui-enhanced:not(.dark) .courses-stats > .grid > div:hover,
    html.courses-ui-enhanced:not(.dark) .courses-semester-grid > div:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    html.courses-ui-enhanced:not(.dark) .courses-stats > .grid > div:nth-child(1) {
        border-color: #93c5fd;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 56%, #eff6ff 100%);
    }

    html.courses-ui-enhanced:not(.dark) .courses-stats > .grid > div:nth-child(2) {
        border-color: #86efac;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 56%, #f0fdf4 100%);
    }

    html.courses-ui-enhanced:not(.dark) .courses-stats > .grid > div:nth-child(3) {
        border-color: #fcd34d;
        background: linear-gradient(135deg, #fffbeb 0%, #ffffff 56%, #fffbeb 100%);
    }

    html.courses-ui-enhanced:not(.dark) .courses-stats > .grid > div:nth-child(4) {
        border-color: #c4b5fd;
        background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 56%, #f5f3ff 100%);
    }

    html.courses-ui-enhanced:not(.dark) .courses-filter-panel,
    html.courses-ui-enhanced:not(.dark) .courses-semester-section,
    html.courses-ui-enhanced:not(.dark) .courses-table-panel,
    html.courses-ui-enhanced:not(.dark) .course-modal-panel,
    html.courses-ui-enhanced:not(.dark) #subjectDetailModalContent {
        overflow: hidden;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    html.courses-ui-enhanced:not(.dark) .courses-filter-panel label,
    html.courses-ui-enhanced:not(.dark) .course-directory-head th,
    html.courses-ui-enhanced:not(.dark) .course-modal-section h3,
    html.courses-ui-enhanced:not(.dark) #subjectDetailContent > div h3 {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }

    html.courses-ui-enhanced:not(.dark) .courses-filter-panel input,
    html.courses-ui-enhanced:not(.dark) .courses-filter-panel select,
    html.courses-ui-enhanced:not(.dark) #courseForm input:not([type='checkbox']):not([type='file']),
    html.courses-ui-enhanced:not(.dark) #courseForm select,
    html.courses-ui-enhanced:not(.dark) #courseForm textarea {
        min-height: 2.9rem;
        border: 2px solid #cbd5e1;
        border-radius: 0.8rem;
        background: #fff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    html.courses-ui-enhanced:not(.dark) .courses-filter-panel input:focus,
    html.courses-ui-enhanced:not(.dark) .courses-filter-panel select:focus,
    html.courses-ui-enhanced:not(.dark) #courseForm input:not([type='checkbox']):not([type='file']):focus,
    html.courses-ui-enhanced:not(.dark) #courseForm select:focus,
    html.courses-ui-enhanced:not(.dark) #courseForm textarea:focus {
        border-color: #f43f5e;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.1);
        outline: none;
    }

    html.courses-ui-enhanced:not(.dark) .courses-print-btn {
        box-shadow: 0 18px 32px -26px rgba(37, 99, 235, 0.7);
    }

    html.courses-ui-enhanced:not(.dark) .courses-semester-grid > div {
        border-width: 2px;
        border-radius: 1rem;
        box-shadow: 0 18px 32px -28px rgba(15, 23, 42, 0.18);
    }

    html.courses-ui-enhanced:not(.dark) .courses-semester-grid > div .space-y-3 {
        text-align: left;
    }

    html.courses-ui-enhanced:not(.dark) .courses-semester-grid > div .space-y-3 > div {
        justify-content: flex-start;
    }

    html.courses-ui-enhanced:not(.dark) .courses-table-header,
    html.courses-ui-enhanced:not(.dark) .course-modal-header,
    html.courses-ui-enhanced:not(.dark) #subjectDetailModalContent > div:first-child,
    html.courses-ui-enhanced:not(.dark) #subjectDetailModalContent > div:last-child {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    }

    html.courses-ui-enhanced:not(.dark) .courses-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.2rem 1.4rem;
        border-bottom: 1px solid #e2e8f0;
    }

    html.courses-ui-enhanced:not(.dark) .courses-table-header h3,
    html.courses-ui-enhanced:not(.dark) .course-name {
        color: #0f172a;
        font-weight: 700;
    }

    html.courses-ui-enhanced:not(.dark) .courses-table-header p {
        margin-top: 0.2rem;
        font-size: 0.82rem;
        color: #64748b;
    }

    html.courses-ui-enhanced:not(.dark) .course-directory-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    html.courses-ui-enhanced:not(.dark) .course-directory-head th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-bottom: 1px solid #e2e8f0;
    }

    html.courses-ui-enhanced:not(.dark) .course-row td {
        border-bottom-color: #e2e8f0;
        transition: background-color 0.18s ease;
    }

    html.courses-ui-enhanced:not(.dark) .course-row:nth-child(even) td {
        background: #f8fafc;
    }

    html.courses-ui-enhanced:not(.dark) .course-row:hover td {
        background: #fff7f8;
    }

    html.courses-ui-enhanced:not(.dark) .course-title-stack {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    html.courses-ui-enhanced:not(.dark) .course-code {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 0.45rem;
        padding: 0.34rem 0.7rem;
        border-radius: 999px;
        background: #fee2e2;
        color: #be123c;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    html.courses-ui-enhanced:not(.dark) .course-code::before {
        content: '';
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 999px;
        background: #fb7185;
    }

    html.courses-ui-enhanced:not(.dark) .course-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.42rem 0.82rem;
        border-radius: 999px;
        font-weight: 700;
    }

    html.courses-ui-enhanced:not(.dark) .course-actions button {
        border: 1px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 12px 24px -22px rgba(15, 23, 42, 0.45);
    }

    html.courses-ui-enhanced:not(.dark) .course-modal-header {
        position: sticky;
        top: 0;
        z-index: 5;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    html.courses-ui-enhanced:not(.dark) .course-modal-section,
    html.courses-ui-enhanced:not(.dark) #subjectDetailContent > div {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
    }

    html.courses-ui-enhanced:not(.dark) .course-modal-subpanel {
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        background: #f8fafc;
    }

    html.courses-ui-enhanced:not(.dark) #courseForm input[type='file'] {
        border: 1px dashed #94a3b8;
        border-radius: 0.8rem;
        background: #f8fafc;
        padding: 0.72rem 0.85rem;
    }

    @media (max-width: 1024px) {
        html.courses-ui-enhanced:not(.dark) .courses-table-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 640px) {
        html.courses-ui-enhanced:not(.dark) .courses-hero-panel {
            padding: 1.25rem;
        }
    }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="courses-hero-panel">
    @include('admin.components.admin-page-header', [
        'title' => 'Courses',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Courses']
        ],
        'addButton' => [
            'label' => 'Add Course',
            'onclick' => "openAddCourseModal()",
            'color' => 'green'
        ]
    ])
</div>

<div class="courses-page space-y-6">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
            <div class="p-6 text-center">
                <div id="confirmIcon" class="mx-auto mb-4 h-12 w-12 text-gray-400">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900 mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 mb-6">Are you sure you want to proceed?</p>
                <div class="flex justify-center gap-3">
                    <button id="confirmCancel" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">Cancel</button>
                    <button id="confirmOk" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="courses-stats">
        @include('admin.components.admin-stats-cards', [
            'cards' => [
                ['title' => 'Total Courses', 'value' => $stats['total'] ?? 0, 'icon' => 'bi-book', 'color' => 'blue'],
                ['title' => 'Active', 'value' => $stats['active'] ?? 0, 'icon' => 'bi-check-circle', 'color' => 'green'],
                ['title' => 'Archived', 'value' => $stats['archived'] ?? 0, 'icon' => 'bi-archive', 'color' => 'yellow'],
                ['title' => 'Core Subjects', 'value' => $stats['core'] ?? 0, 'icon' => 'bi-bookmark-fill', 'color' => 'blue'],
            ]
        ])
    </div>

    <!-- Filter Card -->
    <div class="courses-filter-panel bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4 mb-4">
        <form id="coursesFilterForm" method="GET" action="{{ route('admin.courses') }}" class="space-y-3">
            <!-- Filter Inputs Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Search</label>
                    <input type="text" name="q" id="coursesSearch" placeholder="Course or code..." value="{{ $search }}" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-red-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subject Type</label>
                    <select name="subject_type" id="coursesSubjectTypeFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Types</option>
                        <option value="core" {{ $subject_type == 'core' ? 'selected' : '' }}>Core</option>
                        <option value="elective" {{ $subject_type == 'elective' ? 'selected' : '' }}>Elective</option>
                        <option value="optional" {{ $subject_type == 'optional' ? 'selected' : '' }}>Optional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" id="coursesStatusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Status</option>
                        <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="archived" {{ $status == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Semester</label>
                    <select name="semester" id="coursesSemesterFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Semesters</option>
                        @if(!empty($semesters) && collect($semesters)->isNotEmpty())
                            @foreach(collect($semesters) as $sem)
                                <option value="{{ $sem->number }}" {{ (isset($semester) && (string)$semester === (string)$sem->number) ? 'selected' : '' }}>
                                    {{ $sem->name ?? \App\Models\Semester::getOrdinalName((int)$sem->number) }}
                                </option>
                            @endforeach
                        @else
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ (isset($semester) && (string)$semester === (string)$i) ? 'selected' : '' }}>
                                    {{ \App\Models\Semester::getOrdinalName($i) }}
                                </option>
                            @endfor
                        @endif
                    </select>
                </div>
            </div>

            <!-- Additional Filters Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Teacher</label>
                    <select name="teacher_id" id="coursesTeacherFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Teachers</option>
                        @foreach($allTeachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $teacher_id && intval($teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex gap-2 items-center">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                        <i class="bi bi-funnel"></i>
                        <span>Filter</span>
                    </button>
                    <button type="button" onclick="resetCoursesFilter()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium transition shadow-sm">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Reset</span>
                    </button>
                </div>

                <button type="button" onclick="adminOpenPrintPreview('{{ route('courses.print-list') }}', { title: 'Print Courses' })" class="courses-print-btn px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium shadow-sm transition-colors inline-flex items-center gap-2">
                    <i class="bi bi-printer"></i>Print
                </button>
            </div>
        </form>
    </div>

    {{-- Semester Cards (click to filter subjects by semester) --}}
    @if(!empty($semesterCards))
        <div class="courses-semester-section bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Semesters</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Click a semester card to view its subjects
                        @if(!empty($selectedSemesterLabel)) ({{ $selectedSemesterLabel }}) @endif
                    </p>
                </div>
            </div>

            <div class="p-5">
                <div class="courses-semester-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($semesterCards as $card)
                        @include('admin.components.semester-card', [
                            'semester' => $card['semester'] ?? null,
                            'subjectCount' => $card['subjectCount'] ?? 0,
                            'metrics' => $card['metrics'] ?? null,
                            'isActive' => $card['isActive'] ?? false,
                            'onClick' => "window.location.href='" . ($card['url'] ?? '#') . "'"
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Table Section -->

    <!-- Table Section -->
    <div class="courses-table-panel bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="courses-table-header">
            <div>
                <h3>Semester Courses</h3>
                <p>Showing {{ $courses->count() }} of {{ method_exists($courses, 'total') ? $courses->total() : $courses->count() }} subjects in the current view.</p>
            </div>
        </div>
        <div id="coursesTableContainer" class="overflow-x-auto">
            <table class="course-directory-table min-w-full text-sm">
                <thead class="course-directory-head bg-gray-50 dark:bg-slate-700 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <tr class="border-b border-gray-200 dark:border-slate-600">
                        <th class="px-6 py-3 text-left">Subject Name</th>
                        <th class="px-6 py-3 text-left">Type</th>
                        <th class="px-6 py-3 text-left">Semester</th>
                        <th class="px-6 py-3 text-left">Students</th>
                        <th class="px-6 py-3 text-left">Credits</th>
                        <th class="px-6 py-3 text-left">Teacher</th>
                        <th class="px-6 py-3 text-left">Lab Tech</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr class="course-row group border-b border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 text-sm">
                            <div class="course-title-stack">
                                <p class="course-name text-gray-900 dark:text-white font-medium text-sm">{{ $course->subject_name }}</p>
                                <p class="course-code text-gray-600 dark:text-gray-400 text-xs">{{ $course->subject_code }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $typeColors = [
                                    'core' => 'bg-blue-100 text-blue-700',
                                    'elective' => 'bg-purple-100 text-purple-700',
                                    'optional' => 'bg-amber-100 text-amber-700',
                                ];
                                $typeColor = $typeColors[$course->subject_type ?? 'core'] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="course-chip inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                                {{ ucfirst($course->subject_type ?? 'Core') }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            @if($course->semester)
                            <span class="course-chip inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $course->semester }}{{ $course->semester == 1 ? 'st' : ($course->semester == 2 ? 'nd' : ($course->semester == 3 ? 'rd' : 'th')) }} Sem
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                            <span class="course-chip inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-100">
                                {{ $course->students_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $course->credits ?? 3 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                            {{ $course->computed_assigned_teachers ?? $course->assigned_teachers ?? $course->teacher_name ?? 'Not Assigned' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ $course->labTechnician?->user?->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="course-chip inline-block px-2 py-0.5 rounded text-xs font-medium
                                @if(($course->status ?? 'active') == 'active') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 @endif">
                                {{ ucfirst($course->status ?? 'Active') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="course-actions flex items-center gap-1 justify-center">
                                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); openSubjectDetailModal({{ $course->id }});" class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-blue-600 dark:text-blue-400 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/30" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); editCourse({{ $course->id }});" class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/30" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" onclick="event.preventDefault(); event.stopPropagation(); deleteCourse({{ $course->id }});" class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-red-600 dark:text-red-400 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/30" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                            <div class="courses-empty-state flex flex-col items-center justify-center">
                                <i class="bi bi-inbox text-4xl text-gray-400 dark:text-gray-500 mb-2"></i>
                                <p class="dark:text-white">No courses found</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Add a new course to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-100 dark:border-slate-700">
            @include('admin.components.admin-pagination', ['paginator' => $courses])
        </div>
    </div>
</div>

<!-- Add/Edit Course Modal -->
<div id="courseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="course-modal-panel bg-white rounded shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="course-modal-header flex items-center justify-between mb-4 px-4 pt-4 sticky top-0 bg-white">
            <h2 id="courseModalTitle" class="text-sm font-bold">Add Course</h2>
            <button onclick="closeCourseModal()" class="text-gray-600 hover:text-gray-900"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="courseForm" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="courseId">
            
            <!-- Basic Information -->
            <div class="course-modal-section mb-4 px-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-3 pb-1 border-b">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Course Name *</label>
                        <input type="text" name="subject_name" id="courseName" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., Data Structures">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Course Code *</label>
                        <input type="text" name="subject_code" id="courseCode" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., CS-301">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Credits</label>
                        <input type="number" name="credits" id="courseCredits" value="3" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" id="courseSemester" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">All Semesters</option>
                            @if(!empty($semesters) && collect($semesters)->isNotEmpty())
                                @foreach(collect($semesters)->sortBy('number') as $sem)
                                    <option value="{{ $sem->number }}">{{ $sem->name ?? \App\Models\Semester::getOrdinalName((int)$sem->number) ?? 'Semester ' . $sem->number }}</option>
                                @endforeach
                            @else
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">{{ \App\Models\Semester::getOrdinalName($i) }}</option>
                                @endfor
                            @endif
                        </select>
                    </div>
                    <div class="xl:col-span-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Assign Teacher</label>
                        <select name="teacher_id" id="courseTeacher" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Unassigned</option>
                            @foreach($allTeachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Subject Type & Elective Settings -->
            <div class="course-modal-section mb-4 px-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-3 pb-1 border-b">Elective Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Subject Type</label>
                        <select name="subject_type" id="courseSubjectType" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="core">Core Subject</option>
                            <option value="elective">Elective Subject</option>
                            <option value="optional">Optional Subject</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-gray-700 mb-1">
                            <input type="checkbox" name="has_lab" id="courseHasLab" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span>Has Lab</span>
                        </label>

                        <div id="labFields" class="course-modal-subpanel hidden bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Lab Technician</label>
                                    <select name="lab_technician_id" id="courseLabTech" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Not Assigned</option>
                                        @if(isset($labTechnicians))
                                            @foreach($labTechnicians as $lt)
                                                <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Lab Document</label>
                                    <input type="file" name="lab_document" id="courseLabDocument" class="w-full text-xs text-gray-700" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                                    <p id="courseLabDocumentExisting" class="text-xs text-gray-500 mt-1"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="electiveFields" class="course-modal-subpanel mt-4 hidden bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Max Students (for Electives)</label>
                            <input type="number" name="max_students" id="courseMaxStudents" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., 30">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Min Students (for Electives)</label>
                            <input type="number" name="min_students" id="courseMinStudents" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., 10">
                        </div>
                        <div class="xl:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Elective Group</label>
                            <select name="elective_group" id="courseElectiveGroup" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Select group</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                                <option value="V">V</option>
                            </select>
                        </div>
                        <div class="flex items-center xl:col-span-2">
                            <input type="checkbox" name="is_elective_open" id="courseElectiveOpen" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="courseElectiveOpen" class="ml-2 text-xs text-gray-700">Elective Enrollment Open</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Details -->
            <div class="course-modal-section mb-4 px-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-3 pb-1 border-b">Additional Details</h3>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                    <div class="xl:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="courseDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Add a short description..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Prerequisite</label>
                        <input type="text" name="prerequisite" id="coursePrerequisite" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., Basic programming">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Syllabus Document</label>
                        <input type="file" name="syllabus_document" id="courseSyllabusDocument" class="w-full text-xs text-gray-700" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip">
                        <p id="courseSyllabusDocumentExisting" class="text-xs text-gray-500 mt-1"></p>
                    </div>
                    <div class="xl:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea name="remarks" id="courseRemarks" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Internal notes (optional)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Teaching Hours -->
            <div class="course-modal-section mb-4 px-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-2 pb-1 border-b">Teaching Hours (per week)</h3>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Lectures (hrs)</label>
                        <input type="number" name="lecture_hours" id="courseLectureHours" value="4" min="0" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Practicals (hrs)</label>
                        <input type="number" name="practical_hours" id="coursePracticalHours" value="2" min="0" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tutorials (hrs)</label>
                        <input type="number" name="tutorial_hours" id="courseTutorialHours" value="1" min="0" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="course-modal-section mb-4 px-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="courseStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            
            <div class="course-modal-actions flex justify-end gap-2 px-4 pb-4">
                <button type="button" onclick="closeCourseModal()" class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 dark:text-gray-300 dark:bg-slate-700 dark:border-slate-600 dark:hover:bg-slate-600 rounded-md text-sm font-medium transition shadow-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium transition shadow-sm">Save Course</button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
<script>
const COURSE_FORM_VERSION = '2.5';

function toggleElectiveFields() {
    const type = document.getElementById('courseSubjectType')?.value;
    const electiveFields = document.getElementById('electiveFields');
    const electiveOpen = document.getElementById('courseElectiveOpen');

    if (!electiveFields || !electiveOpen) return;

    const isElective = type === 'elective';

    electiveFields.classList.toggle('hidden', !isElective);

    // When elective is selected, automatically enable enrollment and prevent manual uncheck
    electiveOpen.checked = isElective;
    electiveOpen.disabled = isElective;
}

function toggleLabFields() {
    const hasLab = document.getElementById('courseHasLab')?.checked;
    const labFields = document.getElementById('labFields');

    if (!labFields) return;
    labFields.classList.toggle('hidden', !hasLab);

    if (!hasLab) {
        // Clear lab-related fields when lab is not enabled
        const labTech = document.getElementById('courseLabTech');
        if (labTech) labTech.value = '';

        const labDocInput = document.getElementById('courseLabDocument');
        if (labDocInput) labDocInput.value = '';

        const labDocExisting = document.getElementById('courseLabDocumentExisting');
        if (labDocExisting) labDocExisting.textContent = '';
    }
}

function initCourseForm() {
    const subjectTypeEl = document.getElementById('courseSubjectType');
    const hasLabEl = document.getElementById('courseHasLab');

    if (subjectTypeEl) {
        subjectTypeEl.addEventListener('change', toggleElectiveFields);
    }

    if (hasLabEl) {
        hasLabEl.addEventListener('change', toggleLabFields);
    }

    // Ensure elective and lab sections visibility is correct on load
    toggleElectiveFields();
    toggleLabFields();
}

document.addEventListener('DOMContentLoaded', initCourseForm);

function openAddCourseModal() {
    document.getElementById('courseModalTitle').textContent = 'Add Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = '';
    document.getElementById('courseForm').action = '{{ route("admin.courses.store") }}';
    // Set default values
    document.getElementById('courseSubjectType').value = 'core';
    document.getElementById('courseHasLab').checked = false;
    document.getElementById('courseLectureHours').value = 4;
    document.getElementById('coursePracticalHours').value = 2;
    document.getElementById('courseTutorialHours').value = 1;
    document.getElementById('courseCredits').value = 3;
    document.getElementById('courseElectiveGroup').value = '';
    document.getElementById('courseMaxStudents').value = '';
    document.getElementById('courseMinStudents').value = '';
    document.getElementById('courseDescription').value = '';
    document.getElementById('coursePrerequisite').value = '';
    document.getElementById('courseRemarks').value = '';
    document.getElementById('courseLabDocument').value = '';
    document.getElementById('courseLabDocumentExisting').textContent = '';
    document.getElementById('courseSyllabusDocument').value = '';
    document.getElementById('courseSyllabusDocumentExisting').textContent = '';

    toggleElectiveFields();
    toggleLabFields();

    const modal = document.getElementById('courseModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeCourseModal() {
    const modal = document.getElementById('courseModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

function editCourse(id) {
    document.getElementById('courseModalTitle').textContent = 'Edit Course';
    
    // Reset form first
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = id;
    document.getElementById('courseForm').action = `/admin/courses/${id}`;
    
    // Show modal with explicit display
    const modal = document.getElementById('courseModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
    
    // Fetch course data with loading state
    fetch(`/admin/courses/${id}/edit`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const course = data.course;
                
                // Populate all form fields with course data
                document.getElementById('courseName').value = course.subject_name || '';
                document.getElementById('courseCode').value = course.subject_code || '';
                document.getElementById('courseCredits').value = course.credits ?? 3;
                document.getElementById('courseSemester').value = course.semester || '';
                document.getElementById('courseSubjectType').value = course.subject_type || 'core';
                document.getElementById('courseLectureHours').value = course.lecture_hours ?? 4;
                document.getElementById('coursePracticalHours').value = course.practical_hours ?? 2;
                document.getElementById('courseTutorialHours').value = course.tutorial_hours ?? 1;
                document.getElementById('courseStatus').value = course.status || 'active';
                document.getElementById('courseDescription').value = course.description || '';
                document.getElementById('coursePrerequisite').value = course.prerequisite || '';
                document.getElementById('courseRemarks').value = course.remarks || '';
                
                // Subject type and elective fields
                document.getElementById('courseSubjectType').value = course.subject_type || 'core';
                document.getElementById('courseHasLab').checked = course.has_lab === true || course.has_lab === 1;
                document.getElementById('courseTeacher').value = course.teacher_id || '';
                document.getElementById('courseLabTech').value = course.lab_technician_id || '';
                document.getElementById('courseLabDocumentExisting').textContent = course.lab_document ? `Existing: ${course.lab_document}` : '';
                document.getElementById('courseSyllabusDocumentExisting').textContent = course.syllabus_document ? `Existing: ${course.syllabus_document}` : '';
                document.getElementById('courseMaxStudents').value = course.max_students || '';
                document.getElementById('courseMinStudents').value = course.min_students || '';
                document.getElementById('courseElectiveGroup').value = course.elective_group || '';
                document.getElementById('courseElectiveOpen').checked = course.is_elective_open == 1 || course.is_elective_open === true;

                toggleElectiveFields();
                toggleLabFields();
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Edit course error:', error);
            alert('Error loading course data: ' + error.message);
            closeCourseModal();
        });
}

document.getElementById('courseModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCourseModal();
    }
});

document.getElementById('courseForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const courseId = document.getElementById('courseId').value;
    const form = e.target;
    const formData = new FormData(form);

    // Ensure checkbox values are always sent
    formData.set('has_lab', document.getElementById('courseHasLab').checked ? 1 : 0);
    formData.set('is_elective_open', document.getElementById('courseElectiveOpen').checked ? 1 : 0);

    let url = '{{ route("admin.courses.store") }}';
    if (courseId) {
        url = `/admin/courses/${courseId}`;
        formData.set('_method', 'PATCH');
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeCourseModal();
            location.reload();
        } else {
            alert(data.message || 'Error saving course');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving course');
    });
});

function deleteCourse(id) {
    if (!id) return;
    
    // Use professional confirmation if available
    const confirmDelete = async () => {
        if (typeof showConfirm === 'function') {
            const confirmed = await showConfirm({
                title: 'Delete Course',
                message: 'Are you sure you want to delete this course?',
                type: 'delete',
                okText: 'Delete',
                cancelText: 'Cancel'
            });
            if (!confirmed) return;
        } else {
            if (!confirm('Are you sure you want to delete this course?')) return;
        }
        
        try {
            showLoading('Deleting course...');
            const response = await fetch(`/admin/courses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message || 'Course deleted successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                hideLoading();
                showToast(data.message || 'Error deleting course', 'error');
            }
        } catch (error) {
            hideLoading();
            console.error('Error:', error);
            showToast('Error deleting course', 'error');
        }
    };
    
    confirmDelete();
}

function resetCoursesFilter() {
    document.getElementById('coursesSearch').value = '';
    document.getElementById('coursesSubjectTypeFilter').value = '';
    document.getElementById('coursesStatusFilter').value = '';
    document.getElementById('coursesSemesterFilter').value = '';
    document.getElementById('coursesTeacherFilter').value = '';
    window.location.href = '{{ route("admin.courses") }}';
}
</script>
@endsection

<!-- Subject Detail Modal Component -->
@include('admin.partials.subject-detail-modal')
