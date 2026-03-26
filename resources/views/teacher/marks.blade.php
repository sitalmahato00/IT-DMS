@extends('teacher.layouts.teacherlayout')

@section('title', __('Marks/Results'))

@section('content')
{{-- Page Header --}}
<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif" id="teacherMarksApp">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Student Marks') }}</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Check and view marks for the subjects assigned to your semesters.') }}</p>
    </div>

    <script>
        const currentCategory = '{{ $selectedCategory }}';
        const baseUrl = '{{ route("teacher.marks") }}';
        const availableSemesters = @json($semesters ?? []);
        const availableSubjects = @json($subjects ?? []);

        function buildFilterParams(overrides = {}) {
            const form = document.getElementById('marksFilterForm');
            if (!form) return new URLSearchParams();
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
            const form = document.getElementById('marksFilterForm');
            if (form) {
                form.reset();
            }
            window.location.href = `${baseUrl}?category=${currentCategory}`;
        }

        function updateSubjects() {
            const semesterSelect = document.getElementById('filterSemester');
            const subjectSelect = document.getElementById('filterSubjectId');
            
            if (!semesterSelect || !subjectSelect) return;
            
            const selectedSemester = semesterSelect.value;
            const filteredSubjects = selectedSemester 
                ? availableSubjects.filter(s => String(s.semester) === String(selectedSemester))
                : availableSubjects;
            
            subjectSelect.innerHTML = '<option value="">' + (selectedSemester ? '{{ __("Select Subject") }}' : '{{ __("Select semester first") }}') + '</option>';
            
            filteredSubjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                subjectSelect.appendChild(option);
            });
            
            subjectSelect.disabled = !selectedSemester;
        }
    </script>

    <div class="space-y-4" id="marksContainer">
        {{-- Category Toggle --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Category') }}:</span>
                <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-slate-600">
                    <button 
                        type="button"
                        class="category-btn px-6 py-2 text-sm font-medium transition-colors {{ $selectedCategory === 'assessment' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                        data-category="assessment"
                        onclick="switchCategory('assessment')"
                    >
                        {{ __('Assessment') }}
                    </button>
                    <button 
                        type="button"
                        class="category-btn px-6 py-2 text-sm font-medium transition-colors {{ $selectedCategory === 'ctevt' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                        data-category="ctevt"
                        onclick="switchCategory('ctevt')"
                    >
                        {{ __('CTEVT') }}
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
                $filteredSubjects = $filteredSubjects->filter(fn($subject) => (string) $subject['semester'] === (string) $currentFilters['semester']);
            }
        @endphp
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="p-4 border-b border-gray-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Filters & Exports') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Use the filters below to target marks from your assigned semester subjects.') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="printMarks()" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold uppercase tracking-wide rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition">
                        <i class="bi bi-printer"></i>
                        <span>{{ __('Print') }}</span>
                    </button>
                    <button type="button" onclick="exportMarks('csv')" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold uppercase tracking-wide rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>{{ __('CSV') }}</span>
                    </button>
                </div>
            </div>
            <form id="marksFilterForm" class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Search') }}</label>
                        <input 
                            type="text"
                            name="search"
                            value="{{ $currentFilters['search'] ?? '' }}"
                            placeholder="{{ __('Name or Roll No...') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Academic Year') }}</label>
                        <select 
                            name="academic_year" 
                            id="filterAcademicYear"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                        >
                            <option value="">{{ __('All Years') }}</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ ($currentFilters['academic_year'] ?? '') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Semester') }}</label>
                        <select 
                            name="semester" 
                            id="filterSemester"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                            onchange="updateSubjects()"
                        >
                            <option value="">{{ __('Select Semester') }}</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester }}" {{ ($currentFilters['semester'] ?? '') == $semester ? 'selected' : '' }}>
                                    {{ $semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Subject') }}</label>
                        <select 
                            name="subject_id" 
                            id="filterSubjectId"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                            {{ empty($currentFilters['semester'] ?? '') ? 'disabled' : '' }}
                        >
                            <option value="">
                                {{ empty($currentFilters['semester'] ?? '') ? __('Select semester first') : __('Select Subject') }}
                            </option>
                            @foreach($filteredSubjects as $subject)
                                <option value="{{ $subject['id'] }}" {{ ($currentFilters['subject_id'] ?? '') == $subject['id'] ? 'selected' : '' }}>
                                    {{ $subject['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Assessment') }}</label>
                        <select 
                            name="assessment_id" 
                            id="filterAssessmentId"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                        >
                            <option value="">{{ __('All Assessments') }}</option>
                            @foreach($assessments as $id => $name)
                                <option value="{{ $id }}" {{ ($currentFilters['assessment_id'] ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 border-t border-gray-100 dark:border-slate-700 pt-3">
                    <button type="button" onclick="applyFilters()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                        <i class="bi bi-funnel"></i>
                        <span>{{ __('Apply Filters') }}</span>
                    </button>
                    <button type="button" onclick="resetFilters()" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>{{ __('Reset') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Results Table --}}
        @if(!($currentFilters['subject_id'] ?? null))
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl shadow-sm p-6 flex items-center gap-3">
            <i class="bi bi-info-circle text-2xl"></i>
            <div>
                <p class="font-semibold">{{ __('Select Semester & Subject') }}</p>
                <p class="text-sm text-amber-800">{{ __('Choose a semester first, then pick a subject to view the marks grid below.') }}</p>
            </div>
        </div>
        @else
            @php
                $subjectLabel = '';
                foreach ($subjects as $subject) {
                    if ($subject['id'] == ($currentFilters['subject_id'] ?? null)) {
                        $subjectLabel = $subject['name'];
                        break;
                    }
                }
            @endphp
            @if($selectedCategory === 'assessment')
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Assessment Marks') }} - {{ $subjectLabel }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Filtered by the current selection.') }}</p>
                </div>
                @php
                    $selectedAssessmentId = $currentFilters['assessment_id'] ?? '';
                    if (!empty($selectedAssessmentId)) {
                        $selectedAssessmentId = (int) $selectedAssessmentId;
                    }
                @endphp
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Roll') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Student Name') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Attendance %') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Full Marks') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Pass Marks') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Obtained') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">%</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Result') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $selectedAss = null;
                                    if (!empty($selectedAssessmentId) && isset($student['assessments']) && is_array($student['assessments'])) {
                                        $selectedAss = collect($student['assessments'])->firstWhere('exam_id', $selectedAssessmentId);
                                    }

                                    $totalObtained = $student['total_marks'] ?? 0;
                                    $totalFull = $student['full_marks'] ?? 0;
                                    $totalPass = $student['pass_marks'] ?? 0;

                                    $displayFull = $selectedAss['full'] ?? $totalFull;
                                    $displayPass = $selectedAss['pass'] ?? $totalPass;
                                    $displayObtained = $selectedAss['obtained'] ?? $totalObtained;
                                    $displayPercentage = $selectedAss['percentage'] ?? ($displayFull > 0 ? round(($displayObtained / $displayFull) * 100, 1) : 0);
                                    $isPassed = isset($selectedAss['is_passed']) ? $selectedAss['is_passed'] : ($student['is_passed'] ?? false);
                                    $resultLabel = $isPassed ? 'PASS' : 'FAIL';
                                    $attendancePercentage = $student['attendance_percentage'] ?? 0;
                                    $attendanceClass = $attendancePercentage >= 75
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                                        : ($attendancePercentage >= 50
                                            ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'
                                            : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300');
                                    $obtainedClass = $displayFull > 0
                                        ? ($isPassed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400')
                                        : 'text-gray-400';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $student['roll_no'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $student['name'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attendanceClass }}">
                                            {{ $attendancePercentage }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $displayFull > 0 ? $displayFull : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $displayPass > 0 ? $displayPass : '-' }}</td>
                                    <td class="px-4 py-3 text-center font-medium {{ $obtainedClass }}">{{ $displayFull > 0 ? $displayObtained : '-' }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $displayFull > 0 ? ($displayPercentage . '%') : '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($displayFull > 0)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $resultLabel === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                                {{ $resultLabel }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="bi bi-search text-4xl mb-2"></i>
                                            <p>{{ __('No student marks found. Try adjusting your filters.') }}</p>
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
                <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('CTEVT Marks') }} - {{ $subjectLabel }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Each component lists Full, Pass, and Obtained marks.') }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-200 dark:border-slate-700">
                        <thead>
                            <tr class="bg-red-600 text-white border-b border-white/40">
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Roll') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Name') }}</th>
                                <th class="px-4 py-3 text-center font-semibold" colspan="3">{{ __('Theory Internal') }}</th>
                                <th class="px-4 py-3 text-center font-semibold" colspan="3">{{ __('Theory External') }}</th>
                                <th class="px-4 py-3 text-center font-semibold" colspan="3">{{ __('Practical Internal') }}</th>
                                <th class="px-4 py-3 text-center font-semibold" colspan="3">{{ __('Practical External') }}</th>
                                <th class="px-4 py-3 text-center font-semibold">{{ __('Total') }}</th>
                                <th class="px-4 py-3 text-center font-semibold">{{ __('Result') }}</th>
                            </tr>
                            <tr class="bg-red-500 text-white border-b border-red-400">
                                <th class="px-4 py-1"></th>
                                <th class="px-4 py-1"></th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('F') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('P') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('O') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('F') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('P') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('O') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('F') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('P') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('O') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('F') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('P') }}</th>
                                <th class="px-2 py-1 text-center font-bold text-xs">{{ __('O') }}</th>
                                <th class="px-4 py-1"></th>
                                <th class="px-4 py-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $totalObtained = $student['total_marks'] ?? 0;
                                    $totalFull = $student['full_marks'] ?? 0;
                                    $isPassed = $student['is_passed'] ?? false;
                                    $resultLabel = $isPassed ? 'PASS' : 'FAIL';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $student['roll_no'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $student['name'] ?? 'N/A' }}</td>
                                    @foreach(['ti', 'te', 'pi', 'pe'] as $component)
                                        <td class="px-2 py-3 text-center text-gray-600 dark:text-gray-300">{{ $student[$component . '_full'] ?? 0 }}</td>
                                        <td class="px-2 py-3 text-center text-gray-500 dark:text-gray-400">{{ $student[$component . '_pass'] ?? 0 }}</td>
                                        <td class="px-2 py-3 text-center font-medium {{ ($student[$component . '_is_pass'] ?? null) === false ? 'text-red-600 dark:text-red-400' : ($student[$component . '_is_pass'] === true ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300') }}">
                                            {{ $student[$component . '_obtained'] ?? 0 }}
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-center font-bold text-gray-800 dark:text-gray-100">{{ $totalObtained }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $resultLabel === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                            {{ $resultLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="bi bi-search text-4xl mb-2"></i>
                                            <p>{{ __('No student marks found. Try adjusting your filters.') }}</p>
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
</div>

@endsection

@section('scripts')
<script>
    const printUrl = '{{ route("teacher.marks.print") }}';
    const exportUrl = '{{ route("teacher.marks.export") }}';

    function printMarks() {
        const params = buildFilterParams();
        teacherOpenPrintPreview(`${printUrl}?${params.toString()}`, {
            title: '{{ __('Print Marks') }}',
        });
    }

    function exportMarks(format) {
        const params = buildFilterParams({ format });
        window.location.href = `${exportUrl}?${params.toString()}`;
    }
</script>
@endsection
