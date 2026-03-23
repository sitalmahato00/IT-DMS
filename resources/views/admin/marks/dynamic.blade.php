@extends('admin.layouts.app')

@section('title', 'Dynamic Marks - IT DMS')

@section('content')
{{-- Page Header --}}
@include('admin.components.admin-page-header', [
    'title' => 'Student Marks',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Dynamic Marks']
    ]
])

<div class="space-y-4" id="dynamicMarksApp">
    {{-- Category Toggle --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Category:</span>
            <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-slate-600">
                <button 
                    type="button"
                    class="category-btn px-6 py-2 text-sm font-medium transition-colors {{ $category === 'assessment' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                    data-category="assessment"
                    onclick="switchCategory('assessment')"
                >
                    Assessment
                </button>
                <button 
                    type="button"
                    class="category-btn px-6 py-2 text-sm font-medium transition-colors {{ $category === 'ctevt' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                    data-category="ctevt"
                    onclick="switchCategory('ctevt')"
                >
                    CTEVT
                </button>
            </div>
        </div>
    </div>

    @php
        if (!isset($currentFilters)) {
            $currentFilters = request()->all();
        }
        $filteredSubjects = collect($subjects);
        if (!empty($currentFilters['semester'])) {
            $filteredSubjects = $filteredSubjects->filter(fn($subject) => (string) $subject->semester === (string) $currentFilters['semester']);
        }
    @endphp
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Filters & Exports</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Use the filters below to target marks by academic session, semester, and subject.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="printMarks()" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold uppercase tracking-wide rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                    <i class="bi bi-printer"></i>
                    <span>Print</span>
                </button>
                <button type="button" onclick="exportMarks('excel')" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold uppercase tracking-wide rounded-lg bg-green-600 hover:bg-green-700 text-white">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span>Excel</span>
                </button>
                <button type="button" onclick="exportMarks('csv')" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold uppercase tracking-wide rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>CSV</span>
                </button>
            </div>
        </div>
        <form id="marksFilterForm" class="p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input 
                        type="text"
                        name="search"
                        value="{{ $currentFilters['search'] }}"
                        placeholder="Name or Roll No..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Year (BS)</label>
                    <select name="academic_year" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">All Years</option>
                        @foreach($filterData->years as $year)
                            <option value="{{ $year }}" {{ $currentFilters['academic_year'] == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester</label>
                    <select 
                        name="semester" 
                        id="filterSemester"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                        {{ empty($currentFilters['academic_year']) ? 'disabled' : '' }}
                    >
                        <option value="">
                            {{ empty($currentFilters['academic_year']) ? 'Select academic year first' : 'Select Semester' }}
                        </option>
                        @foreach($filterData->semesters as $semester)
                            <option value="{{ $semester }}" {{ $currentFilters['semester'] == $semester ? 'selected' : '' }}>
                                {{ $semester }}{{ $semester == 1 ? 'st' : ($semester == 2 ? 'nd' : ($semester == 3 ? 'rd' : 'th')) }} Semester
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                    <select 
                        name="subject_id" 
                        id="filterSubjectId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                        {{ empty($currentFilters['semester']) || empty($currentFilters['academic_year']) ? 'disabled' : '' }}
                    >
                        <option value="">
                            {{ empty($currentFilters['academic_year']) ? 'Select academic year first' : (empty($currentFilters['semester']) ? 'Select semester first' : 'Select Subject') }}
                        </option>
                        @foreach($filteredSubjects as $subject)
                            <option value="{{ $subject->id }}" {{ $currentFilters['subject_id'] == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($category === 'assessment')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assessment Number</label>
                    <select name="assessment_number" id="filterAssessmentNumber" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">All</option>
                        @foreach(($assessmentNumbers ?? collect()) as $number)
                            <option value="{{ $number }}" {{ (string) ($currentFilters['assessment_number'] ?? '') === (string) $number ? 'selected' : '' }}>
                                Assessment {{ $number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                     <select name="status" id="filterStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                         <option value="">All</option>
                         <option value="pass" {{ $currentFilters['status'] == 'pass' ? 'selected' : '' }}>Pass</option>
                         <option value="fail" {{ $currentFilters['status'] == 'fail' ? 'selected' : '' }}>Fail</option>
                         <option value="marks_filled" {{ $currentFilters['status'] == 'marks_filled' ? 'selected' : '' }}>Marks Filled</option>
                         <option value="marks_not_filled" {{ $currentFilters['status'] == 'marks_not_filled' ? 'selected' : '' }}>Marks Not Filled</option>
                     </select>
                 </div>
             @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                    <select name="sort_by" id="filterSort" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="roll_no" {{ ($currentFilters['sort_by'] ?? 'roll_no') === 'roll_no' ? 'selected' : '' }}>Roll Number</option>
                        <option value="name" {{ ($currentFilters['sort_by'] ?? '') === 'name' ? 'selected' : '' }}>Student Name</option>
                        <option value="highest" {{ ($currentFilters['sort_by'] ?? '') === 'highest' ? 'selected' : '' }}>Highest Marks</option>
                        <option value="lowest" {{ ($currentFilters['sort_by'] ?? '') === 'lowest' ? 'selected' : '' }}>Lowest Marks</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-gray-100 dark:border-slate-700 pt-3">
                <button type="button" onclick="applyFilters()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                    <i class="bi bi-funnel"></i>
                    <span>Apply Filters</span>
                </button>
                <button type="button" onclick="resetFilters()" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Reset</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Results Table --}}
    @if(!$selectedSubject)
    <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl shadow-sm p-6 flex items-center gap-3">
        <i class="bi bi-info-circle text-2xl"></i>
        <div>
            <p class="font-semibold">Select Semester & Subject</p>
            <p class="text-sm text-amber-800">Choose a semester first, then pick a subject to view the marks grid below.</p>
        </div>
    </div>
    @else
        @php
            $subjectLabel = $selectedSubject->subject_name ?? 'Selected Subject';
        @endphp
        @if($category === 'assessment')
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Assessment Marks - {{ $subjectLabel }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Filtered by the current selection.</p>
                </div>
                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Subject ID: {{ $selectedSubject->subject_code ?? $selectedSubject->id }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 sticky left-0 bg-gray-50 dark:bg-slate-700 z-10">Roll</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 sticky left-20 bg-gray-50 dark:bg-slate-700 z-10">Student Name</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Full Marks</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Pass Marks</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Obtained</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">%</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Result</th>
                        </tr>
                    </thead>
                    <tbody id="marksTableBody">
                        @forelse($students as $student)
                            @php
                                $selectedAssessmentNumber = $currentFilters['assessment_number'] ?? null;
                                if ($selectedAssessmentNumber === '') {
                                    $selectedAssessmentNumber = null;
                                }
                                $examMark = $student->getExamMarkForSubject($selectedSubject->id, $category, null, $selectedAssessmentNumber);
                                $marksFilled = $examMark ? $examMark->isFilled() : false;
                                $isPassed = $examMark ? $examMark->isPassedAllComponents() : false;
                                $totalObtained = $examMark ? $examMark->calculateTotalMarks() : 0;
                                $totalFull = $examMark ? $examMark->calculateFullMarks() : 0;
                                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
                                $resultLabel = $examMark ? $examMark->getResultAttribute() : 'Pending';
                                $statusFilter = $currentFilters['status'] ?? '';
                                $skipRow = false;
                                if ($statusFilter === 'pass' && !$isPassed) $skipRow = true;
                                if ($statusFilter === 'fail' && (!$examMark || $isPassed)) $skipRow = true;
                                if ($statusFilter === 'marks_filled' && !$marksFilled) $skipRow = true;
                                if ($statusFilter === 'marks_not_filled' && $marksFilled) $skipRow = true;
                            @endphp
                            @if($skipRow)
                                @continue
                            @endif
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600 cursor-pointer" onclick="openStudentModal({{ $student->id }})"
                                data-roll="{{ $student->roll_no }}"
                                data-name="{{ strtolower($student->user->name ?? '') }}"
                                data-total="{{ $totalObtained }}"
                            >
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $student->roll_no }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $student->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $examMark ? ($examMark->full_marks ?? $examMark->exam->full_marks ?? '-') : '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $examMark ? ($examMark->passing_marks ?? $examMark->exam->passing_marks ?? '-') : '-' }}</td>
                                <td class="px-4 py-3 text-center font-medium {{ $examMark && $examMark->isAbsent() ? 'text-purple-600 dark:text-purple-400 font-bold' : ($examMark && !$isPassed ? 'text-red-600 dark:text-red-400' : ($examMark ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300')) }}">
                                    {{ $examMark ? ($examMark->isAbsent() ? 'ABS' : ($examMark->marks_obtained ?? '-')) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $examMark && !$examMark->isAbsent() ? ($percentage . '%') : ($examMark && $examMark->isAbsent() ? 'ABS' : '-') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $examMark && $examMark->isAbsent() ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' : ($resultLabel === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300') }}">
                                        {{ $examMark ? ($examMark->isAbsent() ? 'ABS' : $resultLabel) : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-search text-4xl mb-2"></i>
                                        <p>No student marks found. Try adjusting your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-slate-700">
                {{ $students->links() }}
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">CTEVT Marks - {{ $subjectLabel }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Each component lists Full, Pass, and Obtained marks.</p>
                </div>
                <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Components: TI / TE / PI / PE</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 dark:border-slate-700">
                    <thead>
                        <tr class="bg-red-600 text-white border-b border-white/40">
                            <th class="px-4 py-3 text-left font-semibold">Roll</th>
                            <th class="px-4 py-3 text-left font-semibold">Name</th>
                            <th class="px-4 py-3 text-center font-semibold" colspan="3">Theory Internal</th>
                            <th class="px-4 py-3 text-center font-semibold" colspan="3">Theory External</th>
                            <th class="px-4 py-3 text-center font-semibold" colspan="3">Practical Internal</th>
                            <th class="px-4 py-3 text-center font-semibold" colspan="3">Practical External</th>
                            <th class="px-4 py-3 text-center font-semibold">Total</th>
                            <th class="px-4 py-3 text-center font-semibold">Result</th>
                        </tr>
                        <tr class="bg-red-500 text-white border-b border-red-400">
                            <th class="px-4 py-1"></th>
                            <th class="px-4 py-1"></th>
                            @foreach(['TI', 'TE', 'PI', 'PE'] as $component)
                                <th class="px-2 py-1 text-center font-bold text-xs uppercase tracking-wide">Full</th>
                                <th class="px-2 py-1 text-center font-bold text-xs uppercase tracking-wide">Pass</th>
                                <th class="px-2 py-1 text-center font-bold text-xs uppercase tracking-wide">Obt</th>
                            @endforeach
                            <th class="px-4 py-1 text-center font-semibold text-sm">Total</th>
                            <th class="px-4 py-1 text-center font-semibold text-sm">Result</th>
                        </tr>
                    </thead>
                    <tbody id="marksTableBody">
                        @forelse($students as $student)
                            @php
                                $examMark = $student->getExamMarkForSubject($selectedSubject->id, $category);
                                $marksFilled = $examMark ? $examMark->isFilled() : false;
                                $isPassed = $examMark ? $examMark->isPassedAllComponents() : false;
                                $totalObtained = $examMark ? $examMark->calculateTotalMarks() : 0;
                                $totalFull = $examMark ? $examMark->calculateFullMarks() : 0;
                                $percentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 1) : 0;
                                $resultLabel = $examMark ? $examMark->getResultAttribute() : 'Pending';
                                $statusFilter = $currentFilters['status'] ?? '';
                                $skipRow = false;
                                if ($statusFilter === 'pass' && !$isPassed) $skipRow = true;
                                if ($statusFilter === 'fail' && (!$examMark || $isPassed)) $skipRow = true;
                                if ($statusFilter === 'marks_filled' && !$marksFilled) $skipRow = true;
                                if ($statusFilter === 'marks_not_filled' && $marksFilled) $skipRow = true;
                                $componentValues = [];
                                foreach (['TI', 'TE', 'PI', 'PE'] as $component) {
                                    $componentValues[$component] = (array) $student->getComponentMarks($selectedSubject->id, $component);
                                    $componentValues[$component]['full'] = $componentValues[$component]['full'] ?? 0;
                                    $componentValues[$component]['pass'] = $componentValues[$component]['pass'] ?? 0;
                                    $componentValues[$component]['obtained'] = $componentValues[$component]['obtained'] ?? 0;
                                    $componentValues[$component]['is_pass'] = $componentValues[$component]['is_pass'] ?? null;
                                }
                            @endphp
                            @if($skipRow)
                                @continue
                            @endif
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600 cursor-pointer" onclick="openStudentModal({{ $student->id }})"
                                data-roll="{{ $student->roll_no }}"
                                data-name="{{ strtolower($student->user->name ?? '') }}"
                                data-total="{{ $totalObtained }}"
                            >
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $student->roll_no }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $student->user->name ?? 'N/A' }}</td>
                                @foreach(['TI', 'TE', 'PI', 'PE'] as $component)
                                    <td class="px-2 py-3 text-center text-gray-600 dark:text-gray-300">{{ $componentValues[$component]['full'] }}</td>
                                    <td class="px-2 py-3 text-center text-gray-500 dark:text-gray-400">{{ $componentValues[$component]['pass'] }}</td>
                                    <td class="px-2 py-3 text-center font-medium {{ $componentValues[$component]['is_pass'] === false ? 'text-red-600 dark:text-red-400' : ($componentValues[$component]['is_pass'] === true ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300') }}">
                                        {{ $componentValues[$component]['obtained'] }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-center font-bold text-gray-800 dark:text-gray-100">{{ $examMark ? $totalObtained : '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $resultLabel === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ $examMark ? $resultLabel : 'Pending' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="bi bi-search text-4xl mb-2"></i>
                                        <p>No student marks found. Try adjusting your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-slate-700">
                {{ $students->links() }}
            </div>
        </div>
        @endif
    @endif
    </div>
{{-- Student Marks Modal --}}
<div id="studentMarksModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white dark:bg-slate-800 rounded-2xl shadow-2xl">
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 p-6 flex items-center justify-between rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100" id="modalStudentName">Student Name</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Roll: <span id="modalStudentRoll">-</span> | Semester: <span id="modalStudentSemester">-</span></p>
            </div>
            <button onclick="closeModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        
        <div class="p-6" id="modalContent">
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-500">Loading marks...</p>
            </div>
        </div>
        
        <div class="sticky bottom-0 bg-gray-50 dark:bg-slate-700 border-t border-gray-200 dark:border-slate-600 p-6 rounded-b-2xl">
            <div class="flex items-center justify-between">
                <div class="flex gap-8">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Marks</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100" id="modalTotal">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Percentage</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-100" id="modalPercentage">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Result</p>
                        <p class="text-2xl font-bold" id="modalResult">-</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-slate-600 hover:bg-gray-300 dark:hover:bg-slate-500 rounded-lg font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@php
    $availableSubjects = collect($subjects)->map(function ($subject) {
        return [
            'id' => $subject->id,
            'subject_name' => $subject->subject_name,
            'subject_code' => $subject->subject_code,
            'semester' => (string) $subject->semester,
            'category' => $subject->category,
        ];
    })->toArray();
@endphp
<script>
	    const currentCategory = '{{ $category }}';
	    const baseUrl = '{{ route('admin.marks.dynamic') }}';
	    const printUrl = '{{ route('admin.marks.dynamic.print') }}';
	    const exportTemplate = '{{ route('admin.marks.dynamic.export', ['format' => 'FORMAT_PLACEHOLDER']) }}';
	    const initialSemester = @json($currentFilters['semester'] ?? '');
	    const initialSubjectId = @json($currentFilters['subject_id'] ?? '');
	    const initialSort = @json($currentFilters['sort_by'] ?? 'roll_no');
	    const availableSubjects = @json($availableSubjects);

    function buildFilterParams(overrides = {}) {
        const form = document.getElementById('marksFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();

        formData.forEach((value, key) => {
            if (value) {
                params.set(key, value);
            }
        });

        const finalOverrides = Object.assign({ category: currentCategory }, overrides);
        Object.entries(finalOverrides).forEach(([key, value]) => {
            if (value) {
                params.set(key, value);
            } else {
                params.delete(key);
            }
        });

        return params;
    }

    function switchCategory(category) {
        const params = buildFilterParams({ category });
        const url = new URL(baseUrl, window.location.origin);
        url.search = params.toString();
        window.location.href = url.toString();
    }

    function applyFilters() {
        const params = buildFilterParams();
        window.location.href = `${baseUrl}?${params.toString()}`;
    }

    function resetFilters() {
        window.location.href = `${baseUrl}?category=${currentCategory}`;
    }

    function exportMarks(format) {
        const params = buildFilterParams();
        const exportUrl = exportTemplate.replace('FORMAT_PLACEHOLDER', format);
        window.location.href = `${exportUrl}?${params.toString()}`;
    }

    function printMarks() {
        const subjectSelect = getSubjectSelect();
        const overrides = {};
        if (subjectSelect && subjectSelect.value) {
            overrides.subject_id = subjectSelect.value;
        }
        const params = buildFilterParams(overrides);
        const url = new URL(printUrl, window.location.origin);
        url.search = params.toString();
        window.open(url.toString(), '_blank');
    }

    function getAcademicYearSelect() {
        return document.querySelector('select[name="academic_year"]');
    }

    function getSemesterSelect() {
        return document.getElementById('filterSemester');
    }

    function getSubjectSelect() {
        return document.getElementById('filterSubjectId');
    }

    function refreshDependentFilters() {
        const academicSelect = getAcademicYearSelect();
        const semesterSelect = getSemesterSelect();
        const subjectSelect = getSubjectSelect();
        const hasAcademicYear = academicSelect && academicSelect.value;

        if (semesterSelect) {
            semesterSelect.disabled = !hasAcademicYear;
        }

        if (!subjectSelect) {
            return;
        }

        if (!hasAcademicYear) {
            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Select academic year first</option>';
            return;
        }

        if (!semesterSelect?.value) {
            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Select semester first</option>';
            return;
        }
    }

    function handleSemesterChange(semesterValue) {
        const subjectSelect = getSubjectSelect();

        if (!subjectSelect) {
            return;
        }

        if (!semesterValue) {
            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Select semester first</option>';
            return;
        }

        loadSubjectsForMarksFilter(semesterValue);
    }

    function loadSubjectsForMarksFilter(semester, selectedSubject = '') {
        const subjectSelect = document.getElementById('filterSubjectId');
        if (!subjectSelect) return;

        if (!semester) {
            subjectSelect.innerHTML = '<option value="">Select semester first</option>';
            subjectSelect.disabled = true;
            return;
        }

        const filteredSubjects = availableSubjects.filter(sub => sub.semester === String(semester));
        if (!filteredSubjects.length) {
            subjectSelect.innerHTML = '<option value="">No subjects found for this semester</option>';
            subjectSelect.disabled = true;
            return;
        }

        subjectSelect.disabled = false;
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';

        const grouped = groupSubjectsByCategory(filteredSubjects);
        Object.keys(grouped).forEach(group => {
            const groupItems = grouped[group];
            if (!groupItems.length) return;
            const optgroup = document.createElement('optgroup');
            optgroup.label = group;
            groupItems.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.subject_name + (subject.subject_code ? ` (${subject.subject_code})` : '');
                if (String(option.value) === String(selectedSubject)) option.selected = true;
                optgroup.appendChild(option);
            });
            subjectSelect.appendChild(optgroup);
        });
    }

    function groupSubjectsByCategory(subjects) {
        return subjects.reduce((acc, subject) => {
            const key = subject.category || 'Uncategorized';
            if (!acc[key]) acc[key] = [];
            acc[key].push(subject);
            return acc;
        }, {});
    }

    function sortMarksTable(mode = 'roll_no') {
        const tbody = document.getElementById('marksTableBody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr[data-roll]'));
        if (!rows.length) return;

        const sortFn = {
            roll_no: (a, b) => (parseInt(a.dataset.roll || 0, 10) - parseInt(b.dataset.roll || 0, 10)),
            name: (a, b) => (a.dataset.name || '').localeCompare(b.dataset.name || ''),
            highest: (a, b) => (parseFloat(b.dataset.total || 0) - parseFloat(a.dataset.total || 0)),
            lowest: (a, b) => (parseFloat(a.dataset.total || 0) - parseFloat(b.dataset.total || 0)),
        }[mode] || sortFn.roll_no;

        rows.sort(sortFn);
        rows.forEach(row => tbody.appendChild(row));
    }

    document.addEventListener('DOMContentLoaded', function () {
        const semesterSelect = getSemesterSelect();
        const subjectSelect = getSubjectSelect();
        const academicYearSelect = getAcademicYearSelect();
        const sortSelect = document.getElementById('filterSort');

        refreshDependentFilters();

        academicYearSelect?.addEventListener('change', () => {
            if (semesterSelect) {
                semesterSelect.value = '';
            }
            refreshDependentFilters();
        });

        if (semesterSelect) {
            semesterSelect.addEventListener('change', function () {
                refreshDependentFilters();
                handleSemesterChange(this.value);
            });
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                applyFilters();
            });
        }

        if (initialSemester) {
            loadSubjectsForMarksFilter(initialSemester, initialSubjectId);
        }

        sortMarksTable(initialSort);
    });

    // Open student modal
    function openStudentModal(studentId) {
        const modal = document.getElementById('studentMarksModal');
        modal.classList.remove('hidden');

        const params = new URLSearchParams({
            student_id: studentId,
            category: currentCategory
        });

        if (currentCategory === 'assessment') {
            const assNum = document.getElementById('filterAssessmentNumber')?.value || '';
            if (assNum) {
                params.set('assessment_number', assNum);
            }
        }

        fetch('{{ route('admin.marks.dynamic.student', ':studentId') }}'.replace(':studentId', studentId) + '?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderModalContent(data);
                } else {
                    alert('Failed to load student marks');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load student marks');
            });
    }

    // Render modal content (same as before, no change)
    function renderModalContent(data) {
        const student = data.student;
        const marks = data.marks;
        const summary = data.summary;

        document.getElementById('modalStudentName').textContent = student.name;
        document.getElementById('modalStudentRoll').textContent = student.roll_no;
        document.getElementById('modalStudentSemester').textContent = student.semester;
        document.getElementById('modalTotal').textContent = summary.total_obtained + ' / ' + summary.total_full;
        document.getElementById('modalPercentage').textContent = summary.percentage + '%';

        const resultEl = document.getElementById('modalResult');
        resultEl.textContent = summary.result;
        resultEl.className = 'text-2xl font-bold ' + (summary.result === 'PASS' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400');

        let tableHtml = '';

        if (currentCategory === 'ctevt') {
            tableHtml = `
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Subject</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">TI-F</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">TI-P</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">TI-O</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">TE-F</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">TE-P</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">TE-O</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">PI-F</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">PI-P</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">PI-O</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">PE-F</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">PE-P</th>
                            <th class="px-2 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">PE-O</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            marks.forEach(mark => {
                const components = mark.components;
                tableHtml += `
                    <tr class="border-b border-gray-200 dark:border-slate-600">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">${mark.subject_name}</td>
                        <td class="px-2 py-3 text-center text-gray-600 dark:text-gray-300">${components.TI.full}</td>
                        <td class="px-2 py-3 text-center text-gray-500 dark:text-gray-400">${components.TI.pass}</td>
                        <td class="px-2 py-3 text-center font-medium text-gray-800 dark:text-gray-200">${components.TI.obtained}</td>
                        <td class="px-2 py-3 text-center text-gray-600 dark:text-gray-300">${components.TE.full}</td>
                        <td class="px-2 py-3 text-center text-gray-500 dark:text-gray-400">${components.TE.pass}</td>
                        <td class="px-2 py-3 text-center font-medium text-gray-800 dark:text-gray-200">${components.TE.obtained}</td>
                        <td class="px-2 py-3 text-center text-gray-600 dark:text-gray-300">${components.PI.full}</td>
                        <td class="px-2 py-3 text-center text-gray-500 dark:text-gray-400">${components.PI.pass}</td>
                        <td class="px-2 py-3 text-center font-medium text-gray-800 dark:text-gray-200">${components.PI.obtained}</td>
                        <td class="px-2 py-3 text-center text-gray-600 dark:text-gray-300">${components.PE.full}</td>
                        <td class="px-2 py-3 text-center text-gray-500 dark:text-gray-400">${components.PE.pass}</td>
                        <td class="px-2 py-3 text-center font-medium text-gray-800 dark:text-gray-200">${components.PE.obtained}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800 dark:text-gray-200">${mark.total}</td>
                    </tr>
                `;
            });

            tableHtml += '</tbody></table>';
        } else {
            tableHtml = `
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-700">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Subject</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Full Marks</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Pass Marks</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Obtained</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">%</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            marks.forEach(mark => {
                const isPass = mark.percentage >= 40;
                tableHtml += `
                    <tr class="border-b border-gray-200 dark:border-slate-600">
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">${mark.subject_name}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">${mark.full}</td>
                        <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">${mark.pass}</td>
                        <td class="px-4 py-3 text-center font-medium ${isPass ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">${mark.obtained}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">${mark.percentage}%</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800 dark:text-gray-200">${mark.grade}</td>
                    </tr>
                `;
            });

            tableHtml += '</tbody></table>';
        }

        document.getElementById('modalContent').innerHTML = tableHtml;
    }

    function closeModal() {
        document.getElementById('studentMarksModal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
 </script>
@endsection
