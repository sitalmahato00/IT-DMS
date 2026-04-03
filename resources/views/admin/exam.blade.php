@extends('admin.layouts.app')

@section('title', 'Exam')

@section('styles')
<script>
    document.documentElement.classList.add('exam-ui-enhanced');
</script>
<style>
    html.exam-ui-enhanced:not(.dark) .exam-page {
        color: #0f172a;
    }

    html.exam-ui-enhanced:not(.dark) #exams-stats > * > div {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border-color: #f2d7de;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 247, 248, 0.96));
        box-shadow: 0 24px 48px -34px rgba(190, 24, 93, 0.4);
    }

    html.exam-ui-enhanced:not(.dark) .exam-filter-panel > *,
    html.exam-ui-enhanced:not(.dark) .exam-semesters-panel,
    html.exam-ui-enhanced:not(.dark) .exam-subjects-panel,
    html.exam-ui-enhanced:not(.dark) .exam-table-panel {
        border-radius: 28px;
        border-color: rgba(241, 213, 219, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 250, 250, 0.97));
        box-shadow: 0 28px 56px -40px rgba(148, 19, 52, 0.34);
    }

    html.exam-ui-enhanced:not(.dark) .exam-panel-header,
    html.exam-ui-enhanced:not(.dark) .exam-table-head {
        background: linear-gradient(180deg, #fff5f7, #fffafb);
    }

    html.exam-ui-enhanced:not(.dark) .exam-table-row:hover {
        background: linear-gradient(90deg, rgba(255, 241, 242, 0.74), rgba(255, 255, 255, 0.97));
    }

    html.exam-ui-enhanced:not(.dark) .exam-chip,
    html.exam-ui-enhanced:not(.dark) .exam-action-btn,
    html.exam-ui-enhanced:not(.dark) .exam-toolbar-btn {
        border-radius: 999px;
        font-weight: 700;
    }

    html.exam-ui-enhanced:not(.dark) .exam-action-btn,
    html.exam-ui-enhanced:not(.dark) .exam-toolbar-btn {
        box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.4);
    }

    html.exam-ui-enhanced:not(.dark) #confirmModal > div,
    html.exam-ui-enhanced:not(.dark) #addAssessmentModal > div,
    html.exam-ui-enhanced:not(.dark) #viewAssessmentModal > div,
    html.exam-ui-enhanced:not(.dark) #editExamModal .relative.z-10 > div {
        border-radius: 30px;
        border: 1px solid rgba(241, 213, 219, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 250, 250, 0.98));
        box-shadow: 0 34px 70px -38px rgba(15, 23, 42, 0.42);
        overflow: hidden;
    }

    html.exam-ui-enhanced:not(.dark) #confirmHeader,
    html.exam-ui-enhanced:not(.dark) #addAssessmentModal .bg-gradient-to-r,
    html.exam-ui-enhanced:not(.dark) #viewAssessmentModal .bg-gradient-to-r,
    html.exam-ui-enhanced:not(.dark) #editExamModal .bg-gradient-to-r {
        border-bottom: none;
    }

    html.exam-ui-enhanced:not(.dark) #addAssessmentModal input,
    html.exam-ui-enhanced:not(.dark) #addAssessmentModal select,
    html.exam-ui-enhanced:not(.dark) #addAssessmentModal textarea,
    html.exam-ui-enhanced:not(.dark) #editExamModal input,
    html.exam-ui-enhanced:not(.dark) #editExamModal select,
    html.exam-ui-enhanced:not(.dark) #editExamModal textarea {
        border-radius: 16px;
        border-color: #e5d4d9;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    html.exam-ui-enhanced:not(.dark) #addAssessmentModal input:focus,
    html.exam-ui-enhanced:not(.dark) #addAssessmentModal select:focus,
    html.exam-ui-enhanced:not(.dark) #addAssessmentModal textarea:focus,
    html.exam-ui-enhanced:not(.dark) #editExamModal input:focus,
    html.exam-ui-enhanced:not(.dark) #editExamModal select:focus,
    html.exam-ui-enhanced:not(.dark) #editExamModal textarea:focus {
        border-color: #f43f5e;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.12);
    }
</style>
@endsection

@section('content')
{{-- Page Header - Using standardized component --}}
@include('admin.components.admin-page-header', [
    'title' => 'Exams',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Exams']
    ],
    'addButton' => [
        'label' => 'Add Exam',
        'route' => route('admin.exam.create'),
        'color' => 'green'
    ]
])

@php
    $createExamComponentDefinitions = [
        'assessment' => [
            ['key' => 'theory', 'label' => 'Theory'],
            ['key' => 'practical', 'label' => 'Practical'],
            ['key' => 'viva', 'label' => 'Viva'],
        ],
        'ctevt' => [
            ['key' => 'theory_internal', 'label' => 'Theory Internal'],
            ['key' => 'theory_external', 'label' => 'Theory External'],
            ['key' => 'practical_internal', 'label' => 'Practical Internal'],
            ['key' => 'practical_external', 'label' => 'Practical External'],
        ],
    ];

    $createExamComponentFields = [
        'ctevt' => [
            'theory_internal' => ['max' => 'theory_internal_max_marks', 'pass' => 'theory_internal_pass_marks'],
            'theory_external' => ['max' => 'theory_external_max_marks', 'pass' => 'theory_external_pass_marks'],
            'practical_internal' => ['max' => 'practical_internal_max_marks', 'pass' => 'practical_internal_pass_marks'],
            'practical_external' => ['max' => 'practical_external_max_marks', 'pass' => 'practical_external_pass_marks'],
        ],
    ];
@endphp

<div class="exam-page space-y-6">
    

    <!-- Toast Notification - Uses global toast system from layout -->

    <!-- Professional Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 animate-fade-in">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all duration-300 animate-scale-up">
            <!-- Header with icon background -->
            <div id="confirmHeader" class="relative h-20 bg-gradient-to-r from-blue-50 to-blue-100 flex items-center justify-center">
                <div id="confirmIconContainer" class="absolute h-24 w-24 rounded-full flex items-center justify-center" style="transform: translateY(50%);">
                    <i id="confirmIcon" class="text-4xl"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="pt-16 px-6 pb-6 text-center">
                <h3 id="confirmTitle" class="text-xl font-bold text-gray-900 mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 text-sm leading-relaxed mb-8">Are you sure you want to proceed?</p>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-3">
                    <button id="confirmCancel" class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-md text-gray-700 text-sm font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-150 active:scale-95">
                        <i class="bi bi-x-circle mr-1"></i>Cancel
                    </button>
                    <button id="confirmOk" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 transition-all duration-150 active:scale-95 shadow-lg hover:shadow-xl">
                        <i id="confirmOkIcon" class="bi bi-check-circle mr-1"></i><span id="confirmOkText">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards - Using standardized component --}}
<div id="exams-stats">
@include('admin.components.admin-stats-cards', [
    'cards' => [
        ['title' => 'Total Exams', 'value' => $stats['total_exams'] ?? 0, 'icon' => 'bi-clipboard-check', 'color' => 'blue'],
        ['title' => 'Published', 'value' => $stats['published_exams'] ?? 0, 'icon' => 'bi-check-circle', 'color' => 'green'],
        ['title' => 'Draft', 'value' => $stats['draft_exams'] ?? 0, 'icon' => 'bi-exclamation-circle', 'color' => 'yellow'],
        ['title' => 'Total Marks Entries', 'value' => $stats['total_marks_entries'] ?? 0, 'icon' => 'bi-question-circle', 'color' => 'purple'],
    ]
])
</div>

{{-- Filter Card - Using standardized component --}}
<div class="exam-filter-panel">
@include('admin.components.admin-filter-card', [
    'formAction' => route('admin.exam'),
    'filters' => [
        ['name' => 'search', 'type' => 'text', 'placeholder' => 'Search exam name...', 'value' => request('search'), 'label' => 'Search', 'onchange' => 'document.getElementById(\'filterForm\').submit()'],
        ['name' => 'semester', 'type' => 'select', 'options' => ['all' => 'All Semesters'] + ($activeSemesters ?? $semesters), 'value' => request('semester'), 'label' => 'Semester', 'onchange' => 'onSemesterFilterChange()'],
        ['name' => 'subject_id', 'type' => 'select', 'placeholder' => 'All Subjects', 'options' => ($subjectOptions ?? $subjects)->pluck('subject_name', 'id')->toArray(), 'value' => request('subject_id'), 'label' => 'Subject', 'onchange' => 'document.getElementById(\'filterForm\').submit()'],
        ['name' => 'exam_category', 'type' => 'select', 'placeholder' => 'All Categories', 'options' => ['assessment' => 'Assessment', 'ctevt' => 'CTEVT'], 'value' => request('exam_category'), 'label' => 'Category', 'onchange' => 'document.getElementById(\'filterForm\').submit()'],
        ['name' => 'status', 'type' => 'select', 'placeholder' => 'All Status', 'options' => ['published' => 'Published', 'draft' => 'Draft', 'archived' => 'Archived', 'faculty' => 'Faculty'], 'value' => request('status'), 'label' => 'Status', 'onchange' => 'document.getElementById(\'filterForm\').submit()'],
    ],
    'showReset' => true,
    'resetRoute' => route('admin.exam')
])
</div>

    {{-- Semester Cards (click to view exams + subjects) --}}
    @if(!empty($semesterCards))
        <div class="exam-semesters-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="exam-panel-header px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Semesters</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Click a semester card to view its exams and subjects</p>
                </div>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($semesterCards as $card)
                        @include('admin.components.semester-card', [
                            'semester' => $card['semester'] ?? null,
                            'examCount' => $card['examCount'] ?? 0,
                            'subjectCount' => $card['subjectCount'] ?? 0,
                            'isActive' => $card['isActive'] ?? false,
                            'onClick' => "window.location.href='" . ($card['url'] ?? '#') . "'"
                        ])
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Selected semester subjects list --}}
        <div class="exam-subjects-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="exam-panel-header px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Subjects @if(!empty($selectedSemesterLabel)) — {{ $selectedSemesterLabel }} @endif</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @if(!empty($selectedSemesterLabel))
                            Showing active subjects for the selected semester
                        @else
                            Select a semester card to see its subjects
                        @endif
                    </p>
                </div>
            </div>

            <div class="p-5">
                @if(!empty($selectedSemesterLabel) && isset($selectedSemesterSubjects) && $selectedSemesterSubjects->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($selectedSemesterSubjects as $subject)
                            <div class="exam-chip p-3 rounded-lg border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $subject->subject_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @if(!empty($subject->subject_code))
                                        <span class="font-mono">{{ $subject->subject_code }}</span>
                                        <span class="text-gray-300 dark:text-gray-600 mx-1">•</span>
                                    @endif
                                    {{ $subject->category ?? 'Other' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif(!empty($selectedSemesterLabel))
                    <div class="text-sm text-gray-500 dark:text-gray-400">No active subjects found for this semester.</div>
                @else
                    <div class="text-sm text-gray-500 dark:text-gray-400">Click a semester card above to view its subjects and filter the exam list.</div>
                @endif
            </div>
        </div>
    @endif

    <!-- Exam List Table Card with Print and Export -->
    <div class="exam-table-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <!-- Table Header with Title and Actions -->
        <div class="exam-panel-header px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Exam List</h3>
            <div class="flex items-center gap-2">
                <button onclick="exportTable('csv')" class="exam-toolbar-btn inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition shadow-sm" title="Export CSV">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span class="hidden sm:inline">CSV</span>
                </button>
                <button onclick="exportTable('excel')" class="exam-toolbar-btn inline-flex items-center gap-1.5 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm" title="Export Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                    <span class="hidden sm:inline">Excel</span>
                </button>
            </div>
        </div>

    <div class="overflow-x-auto">
        <table class="exam-table min-w-full text-left divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="exam-table-head bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left">Exam Name</th>
                    <th class="px-4 py-3 text-left">Semester</th>
                    <th class="px-4 py-3 text-left">Subject</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Total Marks</th>
                    <th class="px-4 py-3 text-left">Date (AD/BS)</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="exams-table-body" class="divide-y divide-gray-200 dark:divide-slate-700">
                @forelse($exams as $exam)
                <tr class="exam-table-row hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-4 font-medium text-gray-900 dark:text-gray-100 text-sm">
{{ $exam->formatted_assessment }} - {{ $exam->exam_name }}
</td>
                    <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-sm">
                        @php
                            $semesterLabels = [
                                '1' => 'First', '2' => 'Second', '3' => 'Third', '4' => 'Fourth', '5' => 'Fifth', '6' => 'Sixth',
                                'first' => 'First', 'second' => 'Second', 'third' => 'Third', 'fourth' => 'Fourth', 'fifth' => 'Fifth', 'sixth' => 'Sixth',
                            ];
                            $sem = (string) ($exam->semester ?? '');
                        @endphp
                        @if($sem === '')
                            -
                        @else
                            {{ $sem === 'all' ? 'All Semesters' : (($semesterLabels[$sem] ?? ucfirst($sem)) . ' Semester') }}
                        @endif
                    </td>
                    <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-sm">{{ $exam->subject_id ? ($exam->subject->subject_name ?? '-') : 'All Subjects' }}</td>
                    <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-sm">{{ $exam->formatted_category }}</td>
                    <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-sm">{{ $exam->full_marks }}</td>
                    <td class="px-4 py-4 text-gray-700 dark:text-gray-300 text-sm">
                        <span class="text-gray-900 dark:text-gray-100">{{ $exam->formatted_date_ad }}</span>
                        <span class="text-gray-400 mx-1">/</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $exam->formatted_date_bs }}</span>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <span class="exam-chip inline-block px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($exam->status=='published') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                            @elseif($exam->status=='draft') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                            @endif">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center text-sm">
                        <div class="flex gap-1 justify-center">
                            <a href="{{ route('admin.exam.show', $exam->id) }}" class="exam-action-btn inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded transition" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.exam.edit', $exam->id) }}" class="exam-action-btn inline-flex items-center gap-1 px-2 py-1 text-xs text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-900/50 rounded transition" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="toggleExamStatusConfirm({{ $exam->id }}, '{{ $exam->status }}')" class="exam-action-btn inline-flex items-center gap-1 px-2 py-1 text-xs text-purple-700 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50 rounded transition" title="Toggle Status">
                                <i class="bi bi-toggle-{{ $exam->status === 'published' ? 'on' : 'off' }}"></i>
                            </button>
                            <a href="{{ route('admin.exam.show', $exam) }}" onclick="try{ if(typeof showLoading==='function') showLoading(); }catch(e){}" class="exam-action-btn inline-flex items-center gap-1 px-2 py-1 text-xs text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 rounded transition" title="Upload Marks">
                                <i class="bi bi-upload"></i>
                            </a>
                            <button onclick="deleteExam({{ $exam->id }})" class="exam-action-btn inline-flex items-center gap-1 px-2 py-1 text-xs text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 rounded transition" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No exams found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
<div id="exams-pagination">
@include('admin.components.admin-pagination', ['paginator' => $exams])
</div>
    </div>
</div>

<!-- Create Exam Modal -->
<div id="addAssessmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 flex items-center justify-between z-10">
            <div>
                <h2 class="text-sm font-bold">Create Exam</h2>
                <p class="text-red-100 text-xs mt-0.5">Define and manage exams for semesters and subjects</p>
            </div>
            <button onclick="closeAddAssessmentModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 pl-8 pr-8">
                <form id="createExamForm" class="p-3 space-y-3" method="POST" action="{{ route('admin.exam.store') }}">
                @csrf
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Academic Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                                <select name="semester" id="modalSemester" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="loadSubjectsForModal()">
                                <option value="">Select Semester</option>
                                <option value="all">All Semesters</option>
                                @foreach(($activeSemesters ?? $semesters) as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Subject *</label>
                        <select name="subject_id" id="modalSubject" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">Select Subject</option>
                            <option value="all">All Subjects</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Exam Details</h3>
	                    <div class="grid grid-cols-1 gap-2">
	                        <div>
	                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Category *</label>
	                            <select name="exam_category" id="createExamCategory" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
	                                <option value="assessment">Assessment</option>
	                                <option value="ctevt">CTEVT</option>
	                            </select>
	                        </div>
	                    </div>
	                    <div id="createAssessmentNumberField" class="grid grid-cols-1 gap-2">
	                        <div>
	                            <label class="block text-xs font-medium text-gray-700 mb-1">Assessment Number</label>
	                            <select name="assessment_number" id="createAssessmentNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
	                                <option value="">Auto (Next)</option>
	                            </select>
	                            <p class="text-[11px] text-gray-500 mt-1">Shows as “Assessment 1”, “Assessment 2”… in lists and marks.</p>
	                        </div>
	                    </div>
	                    <div>
	                        <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
	                        <input type="text" name="exam_name" placeholder="Enter exam name" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
	                    </div>
                    <input type="hidden" name="full_marks" id="createFullMarksInput">
                    <input type="hidden" name="passing_marks" id="createPassingMarksInput">
                    <div id="assessmentMarkFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                            <input type="number" id="createAssessmentFullMarks" placeholder="Enter total marks" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                            <input type="number" id="createAssessmentPassingMarks" placeholder="Enter passing marks" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                    </div>
                    <div id="ctevtComponentFields" class="space-y-3 hidden">
                        <p class="text-sm text-gray-600 italic">CTEVT exams use component-level Full/Pass fields below.</p>
                        <div id="createExamComponentSection" class="space-y-3">
                            @foreach($createExamComponentDefinitions['ctevt'] as $component)
                            @php
                                $fields = $createExamComponentFields['ctevt'][$component['key']] ?? ['max' => '', 'pass' => ''];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white border border-dashed border-gray-200 rounded p-3" data-component="{{ $component['key'] }}">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ $component['label'] }} Full Marks</label>
                                    <input type="number" name="{{ $fields['max'] }}" min="0" step="0.5" data-component="{{ $component['key'] }}" data-value-type="max" data-component-category="ctevt" class="create-component-input w-full px-3 py-2 border border-gray-200 rounded-md text-sm" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ $component['label'] }} Pass Marks</label>
                                    <input type="number" name="{{ $fields['pass'] }}" min="0" step="0.5" data-component="{{ $component['key'] }}" data-value-type="pass" data-component-category="ctevt" class="create-component-input w-full px-3 py-2 border border-gray-200 rounded-md text-sm" placeholder="0">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (AD) *</label>
                            <input type="date" name="exam_date" id="exam_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="convertAdToBsForAdd()">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (BS)</label>
                            <input type="text" name="exam_date_bs" id="exam_date_bs" placeholder="YYYY-MM-DD" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="convertBsToAdForAdd()">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                            <option value="faculty">Faculty</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Additional Information</h3>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description/Instructions</label>
                        <textarea name="description" placeholder="Enter assessment description and instructions..." class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm h-16"></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 border-t border-gray-200 flex gap-2">
                    <button type="button" onclick="closeAddAssessmentModal()" class="flex-1 px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium transition-colors">Cancel</button>
                    <button type="submit" id="createExamSubmitBtn" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium shadow-sm transition-colors">Create Exam</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Assessment Modal -->
<div id="viewAssessmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-md w-full">
        <div class="relative bg-gradient-to-r from-red-600 to-orange-500 p-4 pb-12">
            <button onclick="closeViewAssessmentModal()" class="absolute top-2 right-2 text-red-200 hover:text-white p-1 rounded">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
            <div class="flex items-end gap-3">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow border-2 border-white">
                    <i class="bi bi-clipboard-check text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white" id="viewAssessmentName">Assessment Name</h2>
                    <p class="text-red-100 text-xs" id="viewAssessmentCategory">Category</p>
                </div>
            </div>
        </div>
        <div class="relative p-4 -mt-6">
            <div class="bg-white rounded shadow p-4 space-y-3">
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-blue-100 rounded flex items-center justify-center">
                            <i class="bi bi-calendar-event text-blue-600 text-xs"></i>
                        </span>
                        Academic Information
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Semester:</span> <span class="font-medium text-gray-900" id="viewAssessmentSemester">-</span></p>
                        <p><span class="text-gray-600">Assessment #:</span> <span class="font-medium text-gray-900" id="viewAssessmentNumber">-</span></p>
                    </div>
                </div>
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-green-100 rounded flex items-center justify-center">
                            <i class="bi bi-book text-green-600 text-xs"></i>
                        </span>
                        Subject Information
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Subject:</span> <span class="font-medium text-gray-900" id="viewAssessmentCourse">Subject Name</span></p>
                    </div>
                </div>
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-purple-100 rounded flex items-center justify-center">
                            <i class="bi bi-percent text-purple-600 text-xs"></i>
                        </span>
                        Assessment Marks
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Total Marks:</span> <span class="font-medium text-gray-900" id="viewAssessmentMarks">100</span></p>
                        <p><span class="text-gray-600">Passing Marks:</span> <span class="font-medium text-gray-900" id="viewAssessmentPassing">40</span></p>
                    </div>
                </div>

                <div id="viewAssessmentCtevtComponents" class="border-b pb-3 hidden">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-slate-100 rounded flex items-center justify-center">
                            <i class="bi bi-diagram-3 text-slate-600 text-xs"></i>
                        </span>
                        CTEVT Components (Full / Pass)
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Theory Internal:</span> <span class="font-medium text-gray-900" id="viewTiMarks">-</span></p>
                        <p><span class="text-gray-600">Theory External:</span> <span class="font-medium text-gray-900" id="viewTeMarks">-</span></p>
                        <p><span class="text-gray-600">Practical Internal:</span> <span class="font-medium text-gray-900" id="viewPiMarks">-</span></p>
                        <p><span class="text-gray-600">Practical External:</span> <span class="font-medium text-gray-900" id="viewPeMarks">-</span></p>
                    </div>
                </div>
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-yellow-100 rounded flex items-center justify-center">
                            <i class="bi bi-calendar-event text-yellow-600 text-xs"></i>
                        </span>
                        Exam Dates
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">AD Date:</span> <span class="font-medium text-gray-900" id="viewAssessmentDateAd">-</span></p>
                        <p><span class="text-gray-600">BS Date:</span> <span class="font-medium text-gray-900" id="viewAssessmentDateBs">-</span></p>
                    </div>
                </div>

                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-cyan-100 rounded flex items-center justify-center">
                            <i class="bi bi-card-text text-cyan-700 text-xs"></i>
                        </span>
                        Description / Instructions
                    </h3>
                    <div class="text-xs text-gray-800 whitespace-pre-line" id="viewAssessmentDescription">-</div>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-orange-100 rounded flex items-center justify-center">
                            <i class="bi bi-circle-fill text-orange-600 text-xs"></i>
                        </span>
                        Status
                    </h3>
                    <div class="text-xs">
                        <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium" id="viewAssessmentStatus">Published</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-gray-200 flex gap-2">
            <button onclick="closeViewAssessmentModal()" class="flex-1 px-2 py-1 text-white font-medium text-xs bg-blue-600 hover:bg-blue-700 rounded">Close</button>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
    function el(id) { return document.getElementById(id); }
    
    // Copy ALL edit exam modal JS functions here
    function normalizeSemesterValue(raw) {
        const v = String(raw ?? '').trim().toLowerCase();
        const map = {
            '1': 'first',
            '2': 'second',
            '3': 'third',
            '4': 'fourth',
            '5': 'fifth',
            '6': 'sixth',
        };
        return map[v] || v;
    }
    
    const EDIT_EXAM_COMPONENT_DEFINITIONS = @json($editExamComponentDefinitions ?? []);
    const EDIT_EXAM_DEFAULT_CATEGORY = '{{ $editExamDefaultCategory ?? "assessment" }}';
    const EDIT_EXAM_DATA_URL_TEMPLATE = '{{ route("admin.exam.edit-data", ["exam" => "__EXAM__"]) }}';
    const EDIT_EXAM_UPDATE_URL_TEMPLATE = '{{ route("admin.exam.update", ["exam" => "__EXAM__"]) }}';
    const EDIT_EXAM_SUBJECTS_URL = '{{ route("admin.exam.subjects") }}';

    function showEditLoading(){ const sp = el('editLoadingSpinner'); if(sp) sp.classList.remove('hidden'); }
    function hideEditLoading(){ const sp = el('editLoadingSpinner'); if(sp) sp.classList.add('hidden'); }
    
    function handleEditExamCategoryChange() {
        const select = el('editExamCategory');
        const category = select?.value || EDIT_EXAM_DEFAULT_CATEGORY;
        const isAssessment = category === 'assessment';

        el('assessmentEditFields').classList.toggle('hidden', !isAssessment);
        el('ctevtEditComponentFields').classList.toggle('hidden', isAssessment);
        el('editAssessmentNumberField')?.classList.toggle('hidden', !isAssessment);

        if (!isAssessment) {
            // Update hidden full/pass marks from component totals
            updateEditExamComponentTotals();

            // Clear the assessment number so it doesn't get saved by mistake
            const numInput = el('editAssessmentNumber');
            if (numInput) numInput.value = '';
        }
    }
    
    function updateEditExamComponentTotals(force = false) {
        const category = el('editExamCategory')?.value || EDIT_EXAM_DEFAULT_CATEGORY;
        if (category !== 'ctevt') return;
        const fullMarksInput = el('editFullMarks');
        const passMarksInput = el('editPassingMarks');
        if (!fullMarksInput && !passMarksInput) return;

        const maxInputs = document.querySelectorAll(`#editExamComponentSection [data-component-category="ctevt"][data-value-type="max"]`);
        const passInputs = document.querySelectorAll(`#editExamComponentSection [data-component-category="ctevt"][data-value-type="pass"]`);

        const totalMax = Array.from(maxInputs).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        const totalPass = Array.from(passInputs).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

        const hasMaxValue = Array.from(maxInputs).some(input => input.value.trim() !== '');
        const hasPassValue = Array.from(passInputs).some(input => input.value.trim() !== '');

        if (fullMarksInput && (force || hasMaxValue)) {
            fullMarksInput.value = totalMax || 0;
        }
        if (passMarksInput && (force || hasPassValue)) {
            passMarksInput.value = totalPass || 0;
        }
    }
    
    function populateEditComponentInputs(exam) {
        document.querySelectorAll('#editExamComponentSection .subject-component-input').forEach(input => {
            const fieldName = input.dataset.fieldName;
            if (!fieldName) return;
            input.value = exam[fieldName] ?? '';
        });
        updateEditExamComponentTotals(true);
    }
    
    function registerEditExamComponentListeners() {
        const select = el('editExamCategory');
        const assessmentFull = el('editAssessmentFullMarks');
        const assessmentPass = el('editAssessmentPassingMarks');
        if (select) {
            select.addEventListener('change', handleEditExamCategoryChange);
        }
        if (assessmentFull) {
            assessmentFull.addEventListener('input', function() {
                el('editFullMarks').value = this.value;
            });
        }
        if (assessmentPass) {
            assessmentPass.addEventListener('input', function() {
                el('editPassingMarks').value = this.value;
            });
        }
        document.addEventListener('input', function(e) {
            if (e.target.matches('#editExamComponentSection .subject-component-input')) {
                updateEditExamComponentTotals();
            }
        });
        handleEditExamCategoryChange();
    }

    function openEditExamModal(examId) {
        showEditLoading();
        
        const url = EDIT_EXAM_DATA_URL_TEMPLATE.replace('__EXAM__', encodeURIComponent(examId));
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            cache: 'no-store',
        })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                throw new Error(`HTTP ${response.status}: ${text.slice(0, 300)}`);
            }
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Expected JSON but got "${contentType}": ${text.slice(0, 300)}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.exam) {
                const exam = data.exam;
                
                el('editExamForm').action = EDIT_EXAM_UPDATE_URL_TEMPLATE.replace('__EXAM__', encodeURIComponent(examId));
                
                // Populate form fields
                el('editExamName').value = exam.exam_name || '';
                el('editAssessmentNumber').value = exam.assessment_number || '';
                el('editExamCategory').value = exam.exam_category || EDIT_EXAM_DEFAULT_CATEGORY;
                el('editFullMarks').value = exam.full_marks ?? '';
                el('editPassingMarks').value = exam.passing_marks ?? '';
                el('editAssessmentFullMarks').value = exam.full_marks ?? '';
                el('editAssessmentPassingMarks').value = exam.passing_marks ?? '';
                el('editExamDate').value = exam.exam_date || '';
                el('editExamDateBs').value = exam.exam_date_bs || '';
                el('editStatus').value = exam.status || 'draft';
                el('editDescription').value = exam.description || '';

                populateEditComponentInputs(exam);
                handleEditExamCategoryChange();
                
                // Set semester
                const semester = normalizeSemesterValue(exam.semester || 'all');
                el('editSemester').value = semester;
                
                // Load subjects
                loadSubjectsForEditExam(exam.subject_id || 'all');
                
                el('editExamModal').classList.remove('hidden');
            } else {
                const msg = data?.message || 'Failed to load exam data';
                console.error('Failed to load exam data:', data);
                el('editExamErrors').textContent = msg;
                el('editExamErrors').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const msg = 'Error loading exam data. ' + (error?.message || '');
            el('editExamErrors').textContent = msg;
            el('editExamErrors').classList.remove('hidden');
        })
        .finally(() => {
            hideEditLoading();
        });
    }

    function closeEditExamModal() {
        el('editExamModal').classList.add('hidden');
        hideEditLoading();
    }
    
    function loadSubjectsForEditExam(selectedSubjectId) {
        const semester = el('editSemester').value;
        const subjectSelect = el('editSubject');
        
        if (semester === 'all') {
            subjectSelect.innerHTML = '<option value="all">All Subjects</option>';
            subjectSelect.value = 'all';
            subjectSelect.disabled = true;
            return;
        }
        
        if (!semester || semester === '') {
            subjectSelect.innerHTML = '<option value="">Select semester first</option>';
            subjectSelect.disabled = true;
            return;
        }
        
        subjectSelect.innerHTML = '<option value="">Loading...</option>';
        showEditLoading();
        
        const url = `${EDIT_EXAM_SUBJECTS_URL}?semester=${encodeURIComponent(semester)}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="">Select Subject</option><option value="all">All Subjects</option>';
                
                let hasSubjects = false;
                
                if (data.grouped) {
                    Object.keys(data.grouped).forEach(group => {
                        const sublist = data.grouped[group];
                        if (!sublist || sublist.length === 0) return;
                        hasSubjects = true;
                        const optgrp = document.createElement('optgroup');
                        optgrp.label = group;
                        sublist.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                            optgrp.appendChild(option);
                        });
                        subjectSelect.appendChild(optgrp);
                    });
                } else if (data.subjects && data.subjects.length > 0) {
                    hasSubjects = true;
                    data.subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                        subjectSelect.appendChild(option);
                    });
                }
                
                if (!hasSubjects) {
                    subjectSelect.innerHTML = '<option value="">No subjects found</option><option value="all">All Subjects</option>';
                }
                
                subjectSelect.disabled = false;
                
                if (selectedSubjectId && selectedSubjectId !== 'all') {
                    const option = subjectSelect.querySelector(`option[value="${selectedSubjectId}"]`);
                    if (option) option.selected = true;
                } else if (selectedSubjectId === 'all') {
                    const option = subjectSelect.querySelector('option[value="all"]');
                    if (option) option.selected = true;
                }
            })
            .catch(error => {
                console.error('Error loading subjects:', error);
                subjectSelect.innerHTML = '<option value="">No subjects found</option><option value="all">All Subjects</option>';
            })
            .finally(() => {
                hideEditLoading();
            });
    }

    el('editExamForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const url = form.action;
        
        let semesterRaw = el('editSemester')?.value || '';
        let semester = normalizeSemesterValue(semesterRaw);
        
        const data = {
            exam_name: (el('editExamName')?.value || ''),
            full_marks: (el('editFullMarks')?.value || ''),
            passing_marks: (el('editPassingMarks')?.value || ''),
            exam_date: (el('editExamDate')?.value || ''),
            exam_date_bs: el('editExamDateBs')?.value || '',
            status: el('editStatus')?.value || '',
            semester: semester,
            subject_id: el('editSubject')?.value || '',
            exam_category: el('editExamCategory')?.value || 'assessment',
            assessment_number: el('editAssessmentNumber')?.value || '',
            description: el('editDescription')?.value || '',
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };
        
        // Add CTEVT component fields if category is ctevt
        if (data.exam_category === 'ctevt') {
            // Collect component values from the form inputs
            document.querySelectorAll('#editExamComponentSection .subject-component-input').forEach(input => {
                const fieldName = input.dataset.fieldName;
                if (fieldName) {
                    data[fieldName] = input.value || '';
                }
            });
        }
        
        console.log('Normalized data sent:', {semesterRaw, semester, data});
        
        const submitBtn = el('editSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-arrow-repeat text-xs mr-1 animate-spin"></i>Updating...';
        showEditLoading();
        
        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok && response.status === 422) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    showToast(data.message || 'Exam updated successfully!', 'success');
                } else {
                    alert(data.message || 'Exam updated successfully!');
                }
                closeEditExamModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                const errorsDiv = el('editExamErrors');
                let errorMsg = data.message || 'Error updating exam';
                if (data.errors && typeof data.errors === 'object') {
                    errorMsg = Object.entries(data.errors).map(([field, msgs]) => 
                        `${field}: ${Array.isArray(msgs) ? msgs[0] : msgs}`
                    ).join('\\n');
                }
                errorsDiv.innerHTML = errorMsg.replace(/\\n/g, '<br>');
                errorsDiv.classList.remove('hidden');
                errorsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.showToast) {
                showToast('An error occurred. Please try again.', 'error');
            } else {
                alert('An error occurred. Please try again.');
            }
            const errorsDiv = el('editExamErrors');
            errorsDiv.textContent = 'An error occurred. Please try again.';
            errorsDiv.classList.remove('hidden');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check text-xs mr-1"></i>Update';
            hideEditLoading();
        });
    });

    document.addEventListener('click', function(e) {
        const modal = el('editExamModal');
        if (modal && !modal.classList.contains('hidden') && e.target === modal.querySelector('.fixed.inset-0.bg-black')) {
            closeEditExamModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditExamModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        registerEditExamComponentListeners();
        
        // Add event listener for edit modal semester change
        const editSemester = el('editSemester');
        if (editSemester) {
            editSemester.addEventListener('change', function() {
                loadSubjectsForEditExam();
            });
        }
    });

    function formatDisplayNumber(value) {
        const numeric = parseFloat(value);
        if (Number.isNaN(numeric)) return '0';
        return Number.isInteger(numeric) ? numeric.toString() : numeric.toFixed(2).replace(/\.00$/, '');
    }

    const CREATE_EXAM_COMPONENT_CONFIG = @json($createExamComponentDefinitions);
    const CREATE_EXAM_DEFAULT_CATEGORY = 'assessment';
    // Modal controls
    function openAddAssessmentModal() {
        document.getElementById('addAssessmentModal').classList.remove('hidden');
        
        // Reset subject dropdown to disabled state initially
        const subjectSelect = document.getElementById('modalSubject');
        subjectSelect.innerHTML = '<option value="">Select semester first</option>';
        subjectSelect.disabled = true;
        
        // Load subjects for the default selected semester
        // Auto-select first non-empty semester if none is selected
        const semesterSelect = document.getElementById('modalSemester');
        if (!semesterSelect.value || semesterSelect.value === '') {
            // Select the first actual semester (skip empty and 'all' options)
            for (let i = 0; i < semesterSelect.options.length; i++) {
                const option = semesterSelect.options[i];
                if (option.value && option.value !== 'all' && option.value !== '') {
                    semesterSelect.selectedIndex = i;
                    break;
                }
            }
        }
        loadSubjectsForModal();
        handleCreateExamCategoryChange();
    }
    function closeAddAssessmentModal() {
        document.getElementById('addAssessmentModal').classList.add('hidden');
        try { if (typeof hideLoading === 'function') hideLoading(); } catch(e) {}
    }

    const VIEW_EXAM_DATA_URL_TEMPLATE = @json(route('admin.exam.edit-data', ['exam' => '__EXAM__']));

    function formatSemesterLabel(raw) {
        const v = String(raw ?? '').trim().toLowerCase();
        const map = {
            'all': 'All Semesters',
            'first': 'First Semester',
            'second': 'Second Semester',
            'third': 'Third Semester',
            'fourth': 'Fourth Semester',
            'fifth': 'Fifth Semester',
            'sixth': 'Sixth Semester',
            '1': 'First Semester',
            '2': 'Second Semester',
            '3': 'Third Semester',
            '4': 'Fourth Semester',
            '5': 'Fifth Semester',
            '6': 'Sixth Semester',
        };
        return map[v] || (v ? (v.charAt(0).toUpperCase() + v.slice(1)) : '-');
    }

    function formatCategoryLabel(raw) {
        const v = String(raw ?? '').trim().toLowerCase();
        const map = { 'assessment': 'Assessment', 'ctevt': 'CTEVT', 'general': 'General' };
        return map[v] || (v ? (v.charAt(0).toUpperCase() + v.slice(1)) : '-');
    }

    function formatStatusLabel(raw) {
        const v = String(raw ?? '').trim().toLowerCase();
        return v ? (v.charAt(0).toUpperCase() + v.slice(1)) : '-';
    }

    function setViewAssessmentStatusBadge(statusLabel) {
        const badge = document.getElementById('viewAssessmentStatus');
        if (!badge) return;
        badge.textContent = statusLabel || '-';
        const normalized = String(statusLabel ?? '').trim().toLowerCase();
        if (normalized === 'published') {
            badge.className = 'inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium';
        } else if (normalized === 'draft') {
            badge.className = 'inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium';
        } else {
            badge.className = 'inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium';
        }
    }

    async function openViewAssessmentModal(examId) {
        try{ if(typeof showLoading==='function') showLoading(); }catch(e){}

        const url = VIEW_EXAM_DATA_URL_TEMPLATE.replace('__EXAM__', encodeURIComponent(examId));
        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!response.ok) {
                const text = await response.text();
                throw new Error(`HTTP ${response.status} ${response.statusText}: ${text.slice(0, 200)}`);
            }
            const data = await response.json();
            if (!data?.success || !data?.exam) {
                throw new Error(data?.message || 'Failed to load exam data');
            }

            const exam = data.exam;
            const categoryLabel = formatCategoryLabel(exam.exam_category);
            const statusLabel = formatStatusLabel(exam.status);

            const formattedName = [exam.formatted_assessment, exam.exam_name].filter(Boolean).join(' - ') || exam.exam_name || 'Exam';
            document.getElementById('viewAssessmentName').textContent = formattedName;
            document.getElementById('viewAssessmentCategory').textContent = categoryLabel;
            document.getElementById('viewAssessmentSemester').textContent = formatSemesterLabel(exam.semester);
            document.getElementById('viewAssessmentNumber').textContent = exam.assessment_number ? String(exam.assessment_number) : '-';
            const subjectId = exam.subject_id;
            const isAllSubjects = subjectId === 'all' || subjectId === null || subjectId === undefined || subjectId === '';
            document.getElementById('viewAssessmentCourse').textContent = exam.subject_name || (isAllSubjects ? 'All Subjects' : '-');
            document.getElementById('viewAssessmentMarks').textContent = exam.full_marks ?? '0';
            document.getElementById('viewAssessmentPassing').textContent = exam.passing_marks ?? '0';
            document.getElementById('viewAssessmentDateAd').textContent = exam.exam_date || '-';
            document.getElementById('viewAssessmentDateBs').textContent = exam.exam_date_bs || '-';
            document.getElementById('viewAssessmentDescription').textContent = exam.description || '-';
            setViewAssessmentStatusBadge(statusLabel);

            const ctevtSection = document.getElementById('viewAssessmentCtevtComponents');
            const isCtevt = String(exam.exam_category ?? '').toLowerCase() === 'ctevt';
            ctevtSection?.classList.toggle('hidden', !isCtevt);
            if (isCtevt) {
                const ti = `${exam.theory_internal_max_marks ?? 0} / ${exam.theory_internal_pass_marks ?? 0}`;
                const te = `${exam.theory_external_max_marks ?? 0} / ${exam.theory_external_pass_marks ?? 0}`;
                const pi = `${exam.practical_internal_max_marks ?? 0} / ${exam.practical_internal_pass_marks ?? 0}`;
                const pe = `${exam.practical_external_max_marks ?? 0} / ${exam.practical_external_pass_marks ?? 0}`;
                const tiEl = document.getElementById('viewTiMarks'); if (tiEl) tiEl.textContent = ti;
                const teEl = document.getElementById('viewTeMarks'); if (teEl) teEl.textContent = te;
                const piEl = document.getElementById('viewPiMarks'); if (piEl) piEl.textContent = pi;
                const peEl = document.getElementById('viewPeMarks'); if (peEl) peEl.textContent = pe;
            }

            document.getElementById('viewAssessmentModal').classList.remove('hidden');
        } catch (err) {
            console.error('Failed to open view modal:', err);
            if (window.showToast) showToast('Failed to load exam details. Please try again.', 'error');
        } finally {
            try{ if(typeof hideLoading==='function') hideLoading(); }catch(e){}
        }
    }

    function closeViewAssessmentModal() {
        document.getElementById('viewAssessmentModal').classList.add('hidden');
    }


    function changePerPage(select) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', select.value);
        window.location.href = url.toString();
    }

    // Load subjects based on selected semester in modal
	    function loadSubjectsForModal() {
	        const semester = document.getElementById('modalSemester').value;
	        const subjectSelect = document.getElementById('modalSubject');
	        const assessmentNumberSelect = document.getElementById('createAssessmentNumber');
	
// If 'all' selected, load all subjects, not disable permanently
        if (semester === 'all') {
            subjectSelect.innerHTML = '<option value="">Loading subjects...</option>';
            subjectSelect.disabled = true;
	        }
	
	        // If no semester selected show placeholder and disable
	        if (!semester || semester === '') {
	            subjectSelect.innerHTML = '<option value="">Select semester first</option>';
	            subjectSelect.disabled = true;
	            if (assessmentNumberSelect) {
	                assessmentNumberSelect.innerHTML = '<option value="">Select semester first</option>';
	                assessmentNumberSelect.value = '';
	                assessmentNumberSelect.disabled = true;
	            }
	            return;
	        }

        // Show global loading (if defined)
        try { if (typeof showLoading === 'function') showLoading(); } catch(e) {}
        subjectSelect.innerHTML = '<option value="">Loading...</option>';
        subjectSelect.disabled = false;

        // Fetch subjects from the server
        let url = `/admin/exam/subjects/by-semester?semester=${encodeURIComponent(semester)}`;

        fetch(url)
            .then(response => response.json())
	            .then(data => {
	                // Reset and build grouped options (optgroups)
	        subjectSelect.innerHTML = '<option value="">Select Subject</option><option value="all">All Subjects</option>';
                
                let hasSubjects = false;
                
                if (data.grouped) {
                    Object.keys(data.grouped).forEach(group => {
                        const sublist = data.grouped[group];
                        if (!sublist || sublist.length === 0) return;
                        hasSubjects = true;
                        const optgrp = document.createElement('optgroup');
                        optgrp.label = group;
                        sublist.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                            option.dataset.hasLab = subject.has_lab ? '1' : '0';
                            optgrp.appendChild(option);
                        });
                        subjectSelect.appendChild(optgrp);
                    });
                } else if (data.subjects && data.subjects.length > 0) {
                    hasSubjects = true;
                    data.subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                        option.dataset.hasLab = subject.has_lab ? '1' : '0';
                        subjectSelect.appendChild(option);
                    });
                }
                
                // If no subjects found, show message
                if (!hasSubjects) {
                    subjectSelect.innerHTML = '<option value="">No subjects found</option><option value="all">All Subjects</option>';
                }
                
                subjectSelect.disabled = false;
                subjectSelect.removeAttribute('disabled');

                // Update practical component visibility based on selected subject
                updateCreateExamPracticalFieldsVisibility();

                // Refresh assessment-number list after subjects are loaded
                try { refreshCreateAssessmentNumbers(); } catch (e) {}
            })
            .then(()=>{ try{ if (typeof hideLoading === 'function') hideLoading(); }catch(e){} })
            .catch(error => {
                console.error('Error loading subjects:', error);
                subjectSelect.innerHTML = '<option value="">No subjects found</option><option value="all">All Subjects</option>';
                subjectSelect.disabled = false;
                try{ if (typeof hideLoading === 'function') hideLoading(); }catch(e){}
            });
    }

function updateCreateExamPracticalFieldsVisibility() {
        const category = document.getElementById('createExamCategory')?.value || CREATE_EXAM_DEFAULT_CATEGORY;
        const subjectSelect = document.getElementById('modalSubject');
        const selectedOption = subjectSelect?.selectedOptions?.[0];
        const hasLab = selectedOption ? selectedOption.dataset.hasLab === '1' : false;
        const showPractical = category === 'ctevt' && hasLab;

        document.querySelectorAll('[data-component="pi"], [data-component="pe"]').forEach((element) => {
            const wrapper = element.closest('[data-component]') || element;
            if (wrapper) {
                wrapper.style.display = showPractical ? '' : 'none';
            }
        });
    }

    function handleCreateExamCategoryChange() {
        const select = document.getElementById('createExamCategory');
        const category = select?.value || CREATE_EXAM_DEFAULT_CATEGORY;
        const isAssessment = category === 'assessment';
        document.getElementById('assessmentMarkFields').classList.toggle('hidden', !isAssessment);
        document.getElementById('ctevtComponentFields').classList.toggle('hidden', isAssessment);
        document.getElementById('createAssessmentNumberField')?.classList.toggle('hidden', !isAssessment);
        if (!isAssessment) {
            const numberSelect = document.getElementById('createAssessmentNumber');
            if (numberSelect) numberSelect.value = '';
            updateCreateExamComponentTotals();
        } else {
            refreshCreateAssessmentNumbers();
        }

        // Update practical component visibility (subject-specific)
        updateCreateExamPracticalFieldsVisibility();
    }

    async function refreshCreateAssessmentNumbers() {
        const category = document.getElementById('createExamCategory')?.value || CREATE_EXAM_DEFAULT_CATEGORY;
        if (category !== 'assessment') return;

        const semester = document.getElementById('modalSemester')?.value || '';
        const subjectRaw = document.getElementById('modalSubject')?.value || '';
        const numberSelect = document.getElementById('createAssessmentNumber');
        if (!numberSelect) return;

        if (!semester) {
            numberSelect.innerHTML = '<option value="">Select semester first</option>';
            numberSelect.disabled = true;
            return;
        }

        const prevValue = numberSelect.value || '';
        const params = new URLSearchParams();
        params.set('semester', semester);
        if (subjectRaw && /^\d+$/.test(subjectRaw)) {
            params.set('subject_id', subjectRaw);
        }

        numberSelect.disabled = true;
        numberSelect.innerHTML = '<option value="">Loading...</option>';

        try {
            const res = await fetch(`{{ route('admin.exam.assessment-numbers') }}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Failed to load assessment numbers');
            const data = await res.json();
            if (!data?.success || !Array.isArray(data.numbers)) throw new Error('Invalid response');

            const parsed = data.numbers
                .map(n => parseInt(String(n).trim(), 10))
                .filter(n => Number.isFinite(n) && n > 0);

            const maxReturned = parsed.length ? Math.max(...parsed) : 0;
            const maxToShow = Math.max(maxReturned, 5);
            numberSelect.innerHTML = '<option value="">Auto (Next)</option>';
            for (let n = 1; n <= maxToShow; n++) {
                const option = document.createElement('option');
                option.value = String(n);
                option.textContent = `Assessment ${n}`;
                numberSelect.appendChild(option);
            }

            // Preserve previous selection if possible, else keep Auto (Next)
            if (prevValue && Array.from(numberSelect.options).some(o => o.value === prevValue)) {
                numberSelect.value = prevValue;
            } else {
                numberSelect.value = '';
            }
            numberSelect.disabled = false;
        } catch (e) {
            console.error(e);
            numberSelect.innerHTML = '<option value="">Auto (Next)</option>';
            numberSelect.disabled = false;
        }
    }

    function updateCreateExamComponentTotals() {
        const select = document.getElementById('createExamCategory');
        const category = select?.value || CREATE_EXAM_DEFAULT_CATEGORY;
        if (category !== 'ctevt') {
            return;
        }
        const inputs = document.querySelectorAll(`#createExamComponentSection [data-component-category="${category}"]`);
        let fullSum = 0;
        let passSum = 0;
        inputs.forEach(input => {
            const type = input.dataset.valueType;
            const value = parseFloat(input.value);
            if (!isNaN(value)) {
                if (type === 'max') fullSum += value;
                if (type === 'pass') passSum += value;
            }
        });
        const fullMarksInput = document.querySelector('input[name="full_marks"]');
        if (fullMarksInput) {
            fullMarksInput.value = fullSum || 0;
        }
        const passingMarksInput = document.querySelector('input[name="passing_marks"]');
        if (passingMarksInput) {
            passingMarksInput.value = passSum || 0;
        }
    }

	    function registerCreateExamComponentListeners() {
	        const categorySelect = document.getElementById('createExamCategory');
	        const assessmentFull = document.getElementById('createAssessmentFullMarks');
	        const assessmentPass = document.getElementById('createAssessmentPassingMarks');
	        const semesterSelect = document.getElementById('modalSemester');
	        const subjectSelect = document.getElementById('modalSubject');

	        if (categorySelect) {
	            categorySelect.addEventListener('change', () => handleCreateExamCategoryChange());
	        }
	        semesterSelect?.addEventListener('change', () => {
	            refreshCreateAssessmentNumbers();
	            updateCreateExamPracticalFieldsVisibility();
	        });
	        subjectSelect?.addEventListener('change', () => {
	            refreshCreateAssessmentNumbers();
	            updateCreateExamPracticalFieldsVisibility();
	        });
	        if (assessmentFull) {
	            assessmentFull.addEventListener('input', function() {
	                document.getElementById('createFullMarksInput').value = this.value;
	            });
            assessmentPass?.addEventListener('input', function() {
                document.getElementById('createPassingMarksInput').value = this.value;
            });
        }
        document.addEventListener('input', function(e) {
            if (e.target.matches('.create-component-input')) {
                updateCreateExamComponentTotals();
            }
        });
        handleCreateExamCategoryChange();
    }

    // Show global loading when creating an exam
    try {
        const createForm = document.getElementById('createExamForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e){
                try{ if (typeof showLoading === 'function') showLoading(); }catch(err){}
                const btn = this.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.dataset.origText = btn.textContent; btn.textContent = 'Creating...'; }
            });
        }
    } catch(e) {}

    // Date conversion functions (use server-side authoritative endpoints)
    async function convertAdToBs(adDate) {
        if (!adDate) return '';
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/admin/convert/ad-to-bs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ date: adDate })
            });
            if (!res.ok) return '';
            const data = await res.json();
            return data.bs || '';
        } catch (e) {
            return '';
        }
    }

    async function convertBsToAd(bsDate) {
        if (!bsDate) return '';
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/admin/convert/bs-to-ad', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ date: bsDate })
            });
            if (!res.ok) return '';
            const data = await res.json();
            return data.ad || '';
        } catch (e) {
            return '';
        }
    }

    // Add modal date conversion
    function convertAdToBsForAdd() {
        const adDate = document.getElementById('exam_date').value;
        const bsDateField = document.getElementById('exam_date_bs');
        if (bsDateField && adDate) {
            convertAdToBs(adDate).then(v => { bsDateField.value = v || ''; });
        }
    }

    function convertBsToAdForAdd() {
        const bsDate = document.getElementById('exam_date_bs').value;
        const adDateField = document.getElementById('exam_date');
        if (adDateField && bsDate) {
            convertBsToAd(bsDate).then(v => { adDateField.value = v || ''; });
        }
    }

    // Edit modal date conversion
    function convertEditExamDateToBs() {
        const adDate = document.getElementById('editExamDate').value;
        const bsDateField = document.getElementById('editExamDateBs');
        if (bsDateField && adDate) {
            convertAdToBs(adDate).then(v => { bsDateField.value = v || ''; });
        }
    }

    function convertEditExamDateToAd() {
        const bsDate = document.getElementById('editExamDateBs').value;
        const adDateField = document.getElementById('editExamDate');
        if (adDateField && bsDate) {
            convertBsToAd(bsDate).then(v => { adDateField.value = v || ''; });
        }
    }
    

    // Subject filter has been removed from exam filter UI, no dynamic subject load needed for this view

</script>

<script>
    // Handle exam creation form submission - allow normal form submission
    if (document.getElementById('createExamForm')) {
        document.getElementById('createExamForm').addEventListener('submit', function(e) {
            // Show loading state on button
            const submitBtn = document.getElementById('createExamSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating...';
            }
            // Form will submit normally - no e.preventDefault()
        });
    }

    // Toggle inline edit form visibility
    function toggleEditForm(examId) {
        const editFormRow = document.getElementById(`editFormRow-${examId}`);
        if (editFormRow) {
            editFormRow.classList.toggle('hidden');
        }
    }

    // Load subjects for inline edit based on selected semester
    function loadSubjectsForInlineEdit(examId) {
        const semesterSelect = document.getElementById(`editSemester-${examId}`);
        const subjectSelect = document.getElementById(`editSubject-${examId}`);

        if (!semesterSelect || !subjectSelect) return;

        const semester = semesterSelect.value;

        if (!semester || semester === '') {
            subjectSelect.innerHTML = '<option value="">Select Semester First</option>';
            return;
        }

        if (semester === 'all') {
            subjectSelect.innerHTML = '<option value="all" selected>All Subjects</option>';
            subjectSelect.disabled = false;
            return;
        }

        subjectSelect.innerHTML = '<option value="">Loading...</option>';

        const url = `/admin/exam/subjects/by-semester?semester=${encodeURIComponent(semester)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="">Select Subject</option><option value="all">All Subjects</option>';
                if (data.grouped) {
                    Object.keys(data.grouped).forEach(group => {
                        const optgrp = document.createElement('optgroup');
                        optgrp.label = group;
                        data.grouped[group].forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                            optgrp.appendChild(option);
                        });
                        subjectSelect.appendChild(optgrp);
                    });
                } else if (data.subjects && data.subjects.length > 0) {
                    data.subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.subject_name + (subject.subject_code ? ` - ${subject.subject_code}` : '');
                        subjectSelect.appendChild(option);
                    });
                }
                subjectSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading subjects:', error);
                subjectSelect.innerHTML = '<option value="">Select Subject</option><option value="all">All Subjects</option>';
                subjectSelect.disabled = false;
            });
    }

    // Handle inline edit form submissions
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all inline edit forms
        document.querySelectorAll('[id^="inlineEditForm-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const examId = form.id.replace('inlineEditForm-', '');
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;

                try { if (typeof showLoading === 'function') showLoading(); } catch(e) {}
                submitBtn.disabled = true;
                submitBtn.textContent = 'Updating...';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Failed to update exam');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    try { if (typeof hideLoading === 'function') hideLoading(); } catch(e) {}
                    try { if (typeof showToast === 'function') showToast('Exam updated successfully', 'success'); } catch(e) { alert('Exam updated successfully'); }
                    toggleEditForm(examId); // Hide the form
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    try { if (typeof hideLoading === 'function') hideLoading(); } catch(e) {}
                    console.error('Error updating exam:', error);
                    try { if (typeof showToast === 'function') showToast(error.message || 'Failed to update exam', 'error'); } catch(e) { alert(error.message || 'Failed to update exam'); }
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            });
        });
    });
</script>

<script>
    // Exam filter is initialized in the first script block

    function toggleExamStatus(id, currentStatus) {
        const newStatus = currentStatus === 'published' ? 'draft' : 'published';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        const url = `/admin/exam/${id}/toggle-status`;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Failed to update exam status');
                });
            }
            return response.json();
        })
        .then(data => {
            try { if (typeof hideLoading === 'function') hideLoading(); } catch(e) {}
            if (data.success) {
                try { if (typeof showToast === 'function') showToast(data.message || 'Exam status updated successfully', 'success'); } catch(e) { alert('Exam status updated successfully'); }
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || 'Failed to update exam status');
            }
        })
        .catch(error => {
            try { if (typeof hideLoading === 'function') hideLoading(); } catch(e) {}
            console.error('Error toggling exam status:', error);
            try { if (typeof showToast === 'function') showToast(error.message || 'Failed to update exam status', 'error'); } catch(e) { alert(error.message || 'Failed to update exam status'); }
        });
    }

    function toggleExamStatusConfirm(id, currentStatus) {
        const confirmToggleStatus = async () => {
            const isPublished = currentStatus === 'published';
            if (typeof showConfirm === 'function') {
                const confirmed = await showConfirm({
                    title: isPublished ? 'Unpublish Exam' : 'Publish Exam',
                    message: `Are you sure you want to ${isPublished ? 'unpublish' : 'publish'} this exam?`,
                    type: isPublished ? 'warning' : 'success',
                    okText: isPublished ? 'Unpublish' : 'Publish',
                    cancelText: 'Cancel'
                });
                if (!confirmed) return;
            } else {
                if (!confirm(`Are you sure you want to ${isPublished ? 'unpublish' : 'publish'} this exam?`)) return;
            }
            
            showLoading(`${isPublished ? 'Unpublishing' : 'Publishing'} exam...`);
            toggleExamStatus(id, currentStatus);
        };
        
        confirmToggleStatus();
    }

    function deleteExam(id) {
        const confirmDelete = async () => {
            if (typeof showConfirm === 'function') {
                const confirmed = await showConfirm({
                    title: 'Delete Exam',
                    message: 'Are you sure you want to delete this exam? This action cannot be undone.',
                    type: 'delete',
                    okText: 'Delete',
                    cancelText: 'Cancel'
                });
                if (!confirmed) return;
            } else {
                if (!confirm('Delete this exam?')) return;
            }
            
            showLoading('Deleting exam...');
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const response = await fetch(`/admin/exam/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success !== false) {
                    showToast(data.message || 'Exam deleted successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    hideLoading();
                    showToast(data.message || 'Failed to delete exam', 'error');
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                showToast('An error occurred while deleting exam', 'error');
            }
        };
        
        confirmDelete();
    }

    // Date conversion functions for Bikram Sambat (BS)
    async function convertAdToBs(adDate) {
        if (!adDate) return '';
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/admin/convert/ad-to-bs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ date: adDate })
            });
            if (!res.ok) return '';
            const data = await res.json();
            return data.bs || '';
        } catch (e) {
            return '';
        }
    }

    async function convertBsToAd(bsDate) {
        if (!bsDate) return '';
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/admin/convert/bs-to-ad', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ date: bsDate })
            });
            if (!res.ok) return '';
            const data = await res.json();
            return data.ad || '';
        } catch (e) {
            return '';
        }
    }

    // Initialize filter date BS calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        const filterDateInput = document.getElementById('filter_exam_date');
        const filterDateBsInput = document.getElementById('filter_exam_date_bs');
        
        if (filterDateInput && filterDateBsInput) {
            // Convert AD to BS when filter AD date changes
            filterDateInput.addEventListener('change', function() {
                if (this.value) {
                    convertAdToBs(this.value).then(v => { filterDateBsInput.value = v || ''; });
                } else {
                    filterDateBsInput.value = '';
                }
            });
            
            // Also update on input for real-time calculation (AD to BS)
            filterDateInput.addEventListener('input', function() {
                if (this.value && this.value.length === 10) {
                    convertAdToBs(this.value).then(v => { filterDateBsInput.value = v || ''; });
                }
            });
            
            // Convert BS to AD when filter BS date changes
            filterDateBsInput.addEventListener('change', function() {
                if (this.value) {
                    convertBsToAd(this.value).then(v => { filterDateInput.value = v || ''; });
                } else {
                    filterDateInput.value = '';
                }
            });
            
            // Also update on input for real-time calculation (BS to AD)
            filterDateBsInput.addEventListener('input', function() {
                if (this.value && this.value.length === 10) {
                    convertBsToAd(this.value).then(v => { filterDateInput.value = v || ''; });
                }
            });
            
            // Initialize BS date from existing AD date value
            if (filterDateInput.value) {
                convertAdToBs(filterDateInput.value).then(v => { filterDateBsInput.value = v || ''; });
            }
        }
    });

    const EXAM_SUBJECTS_BY_SEMESTER_URL = '{{ route("admin.exam.subjects") }}';

    function rebuildSubjectFilterOptions(subjects) {
        const select = document.getElementById('filterSubjectId');
        if (!select) return;

        const selectedValue = select.value;
        select.innerHTML = '';

        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = 'All Subjects';
        select.appendChild(emptyOpt);

        if (subjects.grouped) {
            Object.entries(subjects.grouped).forEach(([groupName, groupItems]) => {
                const optgroup = document.createElement('optgroup');
                optgroup.label = groupName;
                groupItems.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = `${subject.subject_name}${subject.subject_code ? ' - ' + subject.subject_code : ''}`;
                    optgroup.appendChild(option);
                });
                select.appendChild(optgroup);
            });
        } else if (subjects.subjects && subjects.subjects.length) {
            subjects.subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = `${subject.subject_name}${subject.subject_code ? ' - ' + subject.subject_code : ''}`;
                select.appendChild(option);
            });
        }

        // restore selection or fallback to empty
        if (selectedValue && Array.from(select.options).some(opt => opt.value === selectedValue)) {
            select.value = selectedValue;
        } else {
            select.value = '';
        }
    }

    async function onSemesterFilterChange() {
        const semester = document.getElementById('filterSemester')?.value || '';
        const select = document.getElementById('filterSubjectId');
        if (!select) return;

        select.disabled = true;
        select.innerHTML = '<option>Loading subjects...</option>';

        try {
            const url = `${EXAM_SUBJECTS_BY_SEMESTER_URL}?semester=${encodeURIComponent(semester)}`;
            const response = await fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Unable to load subjects');
            const data = await response.json();
            rebuildSubjectFilterOptions(data);
        } catch (err) {
            console.error('Subject load error', err);
            select.innerHTML = '<option value="">All Subjects</option>';
        } finally {
            select.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sem = document.getElementById('filterSemester');
        if (sem) {
            sem.addEventListener('change', onSemesterFilterChange);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        registerCreateExamComponentListeners();
    });
</script>

@endsection
