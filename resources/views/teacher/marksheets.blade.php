@extends('teacher.layouts.teacherlayout')

@section('title', __('Marksheets'))

@section('content')
{{-- Page Header --}}
<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif" id="teacherMarksheetsApp">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Student Marksheets') }}</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('View complete marksheets for all students in your assigned subjects.') }}</p>
    </div>

    <script>
        const baseUrl = '{{ route("teacher.marksheets") }}';
        const availableSemesters = @json($semesters ?? []);
        const availableSubjects = @json($subjects ?? []);
        const availableAssessments = @json($assessments ?? []);

        function buildFilterParams(overrides = {}) {
            const form = document.getElementById('marksheetsFilterForm');
            if (!form) return new URLSearchParams();
            const formData = new FormData(form);
            const params = new URLSearchParams();

            formData.forEach((value, key) => {
                if (value) {
                    params.set(key, value);
                }
            });

            Object.entries(overrides).forEach(([key, value]) => {
                if (value) {
                    params.set(key, value);
                } else {
                    params.delete(key);
                }
            });

            return params;
        }

        function applyFilters() {
            const params = buildFilterParams();
            window.location.href = `${baseUrl}?${params.toString()}`;
        }

        function resetFilters() {
            const form = document.getElementById('marksheetsFilterForm');
            if (form) {
                form.reset();
            }
            window.location.href = baseUrl;
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

        function toggleAssessmentFilter() {
            const categorySelect = document.getElementById('examCategorySelect');
            const assessmentFilter = document.getElementById('assessmentNumberFilter');
            const assessmentSelect = document.querySelector('select[name="assessment_id"]');
            
            if (!categorySelect || !assessmentFilter) return;
            
            const selectedCategory = categorySelect.value;
            if (selectedCategory === 'assessment') {
                assessmentFilter.classList.remove('hidden');
            } else {
                assessmentFilter.classList.add('hidden');
                if (assessmentSelect) {
                    assessmentSelect.value = '';
                }
            }
        }

        // Initialize assessment filter visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleAssessmentFilter();
            const categorySelect = document.getElementById('examCategorySelect');
            if (categorySelect) {
                categorySelect.addEventListener('change', toggleAssessmentFilter);
            }
        });
    </script>

    <div class="space-y-4" id="marksheetsContainer">
        {{-- Filters --}}
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Filters') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Use the filters below to search marksheets from your assigned subjects.') }}</p>
                </div>
            </div>
            <form id="marksheetsFilterForm" class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Exam Category') }}</label>
                        <select 
                            name="exam_category" 
                            id="examCategorySelect"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                        >
                            <option value="">{{ __('All Categories') }}</option>
                            <option value="assessment" {{ ($currentFilters['exam_category'] ?? '') === 'assessment' ? 'selected' : '' }}>{{ __('Assessment') }}</option>
                            <option value="ctevt" {{ ($currentFilters['exam_category'] ?? '') === 'ctevt' ? 'selected' : '' }}>{{ __('CTEVT') }}</option>
                        </select>
                    </div>
                </div>
                <div id="assessmentNumberFilter" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Assessment Number') }}</label>
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
                <p class="text-sm text-amber-800">{{ __('Choose a semester first, then pick a subject to view marksheets below.') }}</p>
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
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Student Marksheets') }} - {{ $subjectLabel }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Complete marksheet information for all students.') }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Roll') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Name') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Full Marks') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Pass Marks') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Obtained') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">%</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Grade') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Result') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php
                                    $rollNo = $student['roll_no'] ?? 'N/A';
                                    $name = $student['name'] ?? 'N/A';
                                    $fullMarks = $student['full_marks'] ?? 0;
                                    $passMarks = $student['pass_marks'] ?? 0;
                                    $obtained = $student['total_marks'] ?? 0;
                                    $percentage = $fullMarks > 0 ? round(($obtained / $fullMarks) * 100, 1) : 0;
                                    $grade = $student['grade'] ?? 'N/A';
                                    $isPassed = $student['is_passed'] ?? false;
                                    $resultLabel = $isPassed ? 'PASS' : 'FAIL';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600">
                                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $rollNo }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $name }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $fullMarks }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $passMarks }}</td>
                                    <td class="px-4 py-3 text-center font-medium {{ !$isPassed ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ $obtained }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $percentage }}%</td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ $grade }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $resultLabel === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                            {{ $resultLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button
                                            type="button"
                                            onclick="openTeacherMarksheetPrint('{{ route('teacher.marksheet.print', ['student_id' => $student['id'], 'subject_id' => $currentFilters['subject_id']]) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition"
                                        >
                                            <i class="bi bi-printer"></i>
                                            <span>{{ __('Print') }}</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="bi bi-search text-4xl mb-2"></i>
                                            <p>{{ __('No student marksheets found. Try adjusting your filters.') }}</p>
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openTeacherMarksheetPrint(url) {
        teacherOpenPrintPreview(url, {
            title: '{{ __('Print Marksheet') }}',
        });
    }
</script>
@endsection

