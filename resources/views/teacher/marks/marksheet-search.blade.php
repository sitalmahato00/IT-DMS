@extends('teacher.layouts.app')

@section('title', 'Marksheet Search - IT DMS')

@section('content')
{{-- Page Header --}}
@include('teacher.components.teacher-page-header', [
    'title' => 'Marksheet Search',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('teacher.dashboard')],
        ['label' => 'Marksheet Search']
    ]
])

<div class="space-y-6">
    {{-- Search Form --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Search Student Marksheet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enter student details to search for marksheets</p>
        </div>
        
        <form method="GET" action="{{ route('teacher.marksheet.search') }}" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Year (BS)</label>
                    <select name="academic_year" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Select Academic Year</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $filters['academic_year'] == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Semester --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester</label>
                    <select name="semester" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Select Semester</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem }}" {{ $filters['semester'] == $sem ? 'selected' : '' }}>Semester {{ $sem }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Exam Category --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exam Category</label>
                    <select id="examCategorySelect" name="exam_category" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        @foreach($examCategories as $cat)
                            <option value="{{ $cat }}" {{ $filters['exam_category'] == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Assessment Number --}}
                <div id="assessmentNumberFilter" class="{{ $filters['exam_category'] !== 'assessment' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assessment Number</label>
                    <select name="assessment_number" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">All</option>
                        @foreach($assessmentNumbers as $number)
                            <option value="{{ $number }}" {{ (string)$filters['assessment_number'] === (string)$number ? 'selected' : '' }}>{{ __('Assessment') }} {{ $number }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Result --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Result</label>
                    <select name="result" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">All</option>
                        <option value="pass" {{ $filters['result'] === 'pass' ? 'selected' : '' }}>Passed</option>
                        <option value="fail" {{ $filters['result'] === 'fail' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                {{-- Student ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student ID / Roll No</label>
                    <input 
                        type="text" 
                        name="student_id" 
                        value="{{ $filters['student_id'] }}"
                        placeholder="Enter Student ID or Roll Number"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                    >
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth (AD)</label>
                    <input 
                        type="date" 
                        name="dob" 
                        value="{{ $filters['dob'] }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                    >
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button 
                    type="submit" 
                    name="search_student" 
                    value="1"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                >
                    <i class="bi bi-search mr-2"></i>Search Marksheet
                </button>
                <a 
                    href="{{ route('teacher.marksheet.search') }}" 
                    class="px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors"
                >
                    <i class="bi bi-arrow-counterclockwise mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Search Results --}}
    @if($student)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Student Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Search results for the selected student</p>
                </div>
                <div class="flex gap-2">
                    <a 
                        href="{{ route('teacher.marksheet.print', array_merge($filters, ['student_id' => $student->id])) }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <i class="bi bi-printer"></i>
                        Print Marksheet
                    </a>
                    <a 
                        href="{{ route('teacher.marksheet.export', array_merge($filters, ['student_id' => $student->id])) }}" 
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <i class="bi bi-download"></i>
                        Export CSV
                    </a>
                </div>
            </div>

            <div class="p-6">
                {{-- Student Info --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 dark:bg-slate-700 rounded-lg">
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Name</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $student->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Student ID</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $student->id }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Roll Number</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $student->roll_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Semester</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">Semester {{ $student->semester ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Academic Year (BS)</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $student->academic_year_bs ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Date of Birth</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $student->date_of_birth ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($marksheetData && $marksheetData['exam_marks']->count() > 0)
                    {{-- Marks Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-slate-700 dark:text-gray-300">
                                <tr>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Exam</th>
                                    <th class="px-4 py-3 text-center">Full Marks</th>
                                    <th class="px-4 py-3 text-center">Passing Marks</th>
                                    <th class="px-4 py-3 text-center">Obtained</th>
                                    <th class="px-4 py-3 text-center">Percentage</th>
                                    <th class="px-4 py-3 text-center">Grade</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marksheetData['exam_marks'] as $mark)
                                    <tr class="border-b dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700">
                                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $mark->subject->subject_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                            {{ $mark->exam->exam_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                            {{ $mark->effective_full_marks ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                            {{ $mark->effective_passing_marks ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200">
                                            {{ $mark->effective_obtained_marks ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                                            {{ $mark->percentage ?? 0 }}%
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                @if(in_array($mark->grade, ['A+', 'A'])) bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                                @elseif(in_array($mark->grade, ['B+', 'B'])) bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                                                @elseif(in_array($mark->grade, ['C+', 'C'])) bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                                                @else bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                                @endif">
                                                {{ $mark->grade ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                @if($mark->status == 'passed' || $mark->percentage >= 40) bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                                @else bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                                @endif">
                                                {{ ucfirst($mark->status ?? 'N/A') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="font-medium text-gray-800 dark:text-gray-200">
                                <tr class="bg-gray-100 dark:bg-slate-700">
                                    <td colspan="4" class="px-4 py-3 text-right">Total:</td>
                                    <td class="px-4 py-3 text-center">{{ $marksheetData['total_obtained'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $marksheetData['total_full'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $marksheetData['percentage'] }}%</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($marksheetData['result'] == 'PASS') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                            @else bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                            @endif">
                                            {{ $marksheetData['result'] }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="bi bi-file-earmark-x text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400">No marks found for the selected filters</p>
                    </div>
                @endif
            </div>
        </div>
    @elseif(request()->has('search_student'))
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="text-center py-8">
                <i class="bi bi-person-x text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-500 dark:text-gray-400">No student found. Please check the student ID or DOB.</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('examCategorySelect');
        const assessmentBlock = document.getElementById('assessmentNumberFilter');
        const assessmentSelect = document.querySelector('select[name="assessment_number"]');

        function toggleAssessmentFilter() {
            if (!categorySelect || !assessmentBlock) {
                return;
            }

            if (categorySelect.value === 'assessment') {
                assessmentBlock.classList.remove('hidden');
            } else {
                assessmentBlock.classList.add('hidden');
                if (assessmentSelect) {
                    assessmentSelect.value = '';
                }
            }
        }

        toggleAssessmentFilter();
        categorySelect.addEventListener('change', toggleAssessmentFilter);
    });
</script>
@endpush
@endsection
