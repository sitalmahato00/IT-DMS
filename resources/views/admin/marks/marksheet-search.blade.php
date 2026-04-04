@extends('admin.layouts.app')

@section('title', 'Marksheet Search - IT DMS')

@section('styles')
<script>
    document.documentElement.classList.add('marksheet-ui-enhanced');
</script>
<style>
    html.marksheet-ui-enhanced:not(.dark) .marksheet-page {
        color: #0f172a;
    }

    html.marksheet-ui-enhanced:not(.dark) .marksheet-panel {
        border-radius: 28px;
        border-color: rgba(215, 227, 243, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(248, 251, 255, 0.97));
        box-shadow: 0 28px 56px -40px rgba(37, 99, 235, 0.28);
    }

    html.marksheet-ui-enhanced:not(.dark) .marksheet-panel-header,
    html.marksheet-ui-enhanced:not(.dark) .marksheet-table thead {
        background: linear-gradient(180deg, #f6faff, #fbfdff);
    }

    html.marksheet-ui-enhanced:not(.dark) .marksheet-info-box {
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(255, 255, 255, 0.98));
    }

    html.marksheet-ui-enhanced:not(.dark) .marksheet-chip,
    html.marksheet-ui-enhanced:not(.dark) .marksheet-toolbar-btn {
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.34);
    }

    html.marksheet-ui-enhanced:not(.dark) .marksheet-page input,
    html.marksheet-ui-enhanced:not(.dark) .marksheet-page select {
        border-radius: 16px;
        border-color: #d8e4f5;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    html.marksheet-ui-enhanced:not(.dark) .marksheet-page input:focus,
    html.marksheet-ui-enhanced:not(.dark) .marksheet-page select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }
</style>
@endsection

@section('content')
{{-- Page Header --}}
@include('admin.components.admin-page-header', [
    'title' => 'Marksheet Search',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Marksheet Search']
    ]
])

<div class="marksheet-page space-y-6">
    {{-- Search Form --}}
    <div class="marksheet-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="marksheet-panel-header p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Search Student Marksheet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enter student details to search for marksheets</p>
        </div>
        
        <form method="GET" action="{{ route('admin.marksheet.search') }}" class="p-6 space-y-4">
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

            </div>

            <div class="flex items-center gap-4 pt-4">
                <button 
                    type="submit" 
                    name="search_student" 
                    value="1"
                    class="marksheet-toolbar-btn px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                >
                    <i class="bi bi-search mr-2"></i>Search Marksheet
                </button>
                <a 
                    href="{{ route('admin.marksheet.search') }}" 
                    class="marksheet-toolbar-btn px-6 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors"
                >
                    <i class="bi bi-arrow-counterclockwise mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Search Results --}}
    @if($student)
        <div class="marksheet-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="marksheet-panel-header p-6 border-b border-gray-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Student Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Search results for the selected student</p>
                </div>
                <div class="flex gap-2">
                    <a 
                        href="{{ route('admin.marksheet.print', array_merge($filters, ['student_id' => $student->id])) }}" 
                        onclick="adminOpenPrintPreview('{{ route('admin.marksheet.print', array_merge($filters, ['student_id' => $student->id])) }}', { title: 'Print Marksheet' }); return false;"
                        class="marksheet-toolbar-btn inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <i class="bi bi-printer"></i>
                        Print Marksheet
                    </a>
                    <a 
                        href="{{ route('admin.marksheet.export', array_merge($filters, ['student_id' => $student->id])) }}" 
                        target="_blank"
                        class="marksheet-toolbar-btn inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <i class="bi bi-download"></i>
                        Export CSV
                    </a>
                </div>
            </div>

            <div class="p-6">
                {{-- Student Info --}}
                <div class="marksheet-info-box grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 dark:bg-slate-700 rounded-lg">
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
                        <span class="text-xs text-gray-500 dark:text-gray-400">Result</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ strtoupper((string) ($marksheetData['result'] ?? 'N/A')) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Published Exams</span>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $marksheetData['exam_marks']->count() ?? 0 }}</p>
                    </div>
                </div>

                @if($marksheetData && $marksheetData['exam_marks']->count() > 0)
                    {{-- Marks Table --}}
                    <div class="overflow-x-auto">
                        @php
                            $isCtevt = strtolower($filters['exam_category'] ?? '') === 'ctevt';
                            $grandTotal = 0;
                        @endphp
                        <table class="marksheet-table w-full text-sm text-left border border-gray-300 dark:border-slate-700 border-collapse">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-slate-700 dark:text-gray-300">
                                <tr>
                                    <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">S.N.</th>
                                    <th class="px-4 py-3 border border-gray-300 dark:border-slate-600">Subject</th>

                                    @if($isCtevt)
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Full Mark (Int)</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Full Mark (Ext)</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Pass Mark (Int)</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Pass Mark (Ext)</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Marks Obtained (Int)</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Marks Obtained (Ext)</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Total</th>
                                    @else
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Full Marks</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Pass Marks</th>
                                        <th class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">Marks Obtained</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($marksheetData['exam_marks'] as $index => $mark)
                                    @if($isCtevt)
                                        @php
                                            $tiFull = $mark->theory_internal_full_marks ?? $mark->exam->theory_internal_max_marks ?? 0;
                                            $teFull = $mark->theory_external_full_marks ?? $mark->exam->theory_external_max_marks ?? 0;
                                            $tiPass = $mark->theory_internal_pass_marks ?? $mark->exam->theory_internal_pass_marks ?? 0;
                                            $tePass = $mark->theory_external_pass_marks ?? $mark->exam->theory_external_pass_marks ?? 0;
                                            $piFull = $mark->practical_internal_full_marks ?? $mark->exam->practical_internal_max_marks ?? 0;
                                            $peFull = $mark->practical_external_full_marks ?? $mark->exam->practical_external_max_marks ?? 0;
                                            $piPass = $mark->practical_internal_pass_marks ?? $mark->exam->practical_internal_pass_marks ?? 0;
                                            $pePass = $mark->practical_external_pass_marks ?? $mark->exam->practical_external_pass_marks ?? 0;
                                            $tiObt = $mark->theory_internal_marks ?? 0;
                                            $teObt = $mark->theory_external_marks ?? 0;
                                            $piObt = $mark->practical_internal_marks ?? 0;
                                            $peObt = $mark->practical_external_marks ?? 0;

                                            $tiFail = $tiObt < $tiPass;
                                            $teFail = $teObt < $tePass;
                                            $piFail = $piObt < $piPass;
                                            $peFail = $peObt < $pePass;

                                            $componentTotal = $tiObt + $teObt + $piObt + $peObt;
                                            $total = ($componentTotal > 0 ? $componentTotal : ($mark->marks_obtained ?? 0));
                                            $grandTotal += $total;
                                        @endphp

                                        <tr class="border-b dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600" rowspan="2">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">
                                                {{ $mark->subject->subject_name ?? 'N/A' }} (Th.)
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $tiFull }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $teFull }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $tiPass }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $tePass }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600 {{ $tiFail ? 'bg-red-100 text-red-800' : '' }}">{{ $tiObt }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600 {{ $teFail ? 'bg-red-100 text-red-800' : '' }}">{{ $teObt }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">{{ $tiObt + $teObt }}</td>
                                        </tr>

                                        <tr class="border-b dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">
                                                {{ $mark->subject->subject_name ?? 'N/A' }} (Pr.)
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $piFull }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $peFull }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $piPass }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $pePass }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600 {{ $piFail ? 'bg-red-100 text-red-800' : '' }}">{{ $piObt }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600 {{ $peFail ? 'bg-red-100 text-red-800' : '' }}">{{ $peObt }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">{{ $piObt + $peObt }}</td>
                                        </tr>
                                    @else
                                        @php
                                            $full = $mark->full_marks > 0 ? $mark->full_marks : ($mark->exam->full_marks ?? 0);
                                            $pass = $mark->passing_marks > 0 ? $mark->passing_marks : ($mark->exam->passing_marks ?? 0);
                                            $obt = $mark->isAbsent() ? 'ABS' : ($mark->isCtevt() ? $mark->calculateTotalMarks() : ($mark->marks_obtained ?? 0));
                                            $isAbsent = $mark->isAbsent();
                                            $fail = !$isAbsent && $pass > 0 && is_numeric($obt) && $obt < $pass;
                                            if (!$isAbsent) { $grandTotal += is_numeric($obt) ? $obt : 0; }
                                        @endphp

                                        <tr class="border-b dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">{{ $mark->subject->subject_name ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $full }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $pass }}</td>
                                            <td class="px-4 py-3 text-center font-medium border border-gray-300 dark:border-slate-600 {{ $isAbsent ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 font-bold' : ($fail ? 'bg-red-100 text-red-800' : '') }}">{{ $obt }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="font-medium text-gray-800 dark:text-gray-200">
                                <tr class="bg-gray-100 dark:bg-slate-700">
                                    <td colspan="{{ $isCtevt ? 8 : 4 }}" class="px-4 py-3 text-right border border-gray-300 dark:border-slate-600">Grand Total:</td>
                                    <td class="px-4 py-3 text-center border border-gray-300 dark:border-slate-600">{{ $grandTotal }}</td>
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
                <p class="text-gray-500 dark:text-gray-400">No student found. Please check the student ID or roll number.</p>
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
        categorySelect?.addEventListener('change', toggleAssessmentFilter);
    });
</script>
@endpush
@endsection
