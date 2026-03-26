@extends('teacher.layouts.teacherlayout')

@section('title', 'Marksheet Search - IT DMS')

@section('content')
@include('teacher.components.teacher-page-header', [
    'title' => 'Marksheet Search',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('teacher.dashboard')],
        ['label' => 'Marksheet Search']
    ]
])

<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="p-6 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Search Student Marksheet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enter student details to search for marksheets from your assigned subjects.</p>
        </div>

        <form method="GET" action="{{ route('teacher.marksheet.search') }}" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Year (BS)</label>
                    <select name="academic_year" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Select Academic Year</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $filters['academic_year'] == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester</label>
                    <select name="semester" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">Select Semester</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem }}" {{ $filters['semester'] == $sem ? 'selected' : '' }}>Semester {{ $sem }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Exam Category</label>
                    <select id="examCategorySelect" name="exam_category" onchange="toggleMarksheetAssessmentFilter()" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        @foreach($examCategories as $cat)
                            <option value="{{ $cat }}" {{ $filters['exam_category'] == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="assessmentNumberFilter" class="{{ $filters['exam_category'] !== 'assessment' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assessment Number</label>
                    <select name="assessment_number" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">All</option>
                        @foreach($assessmentNumbers as $number)
                            <option value="{{ $number }}" {{ (string) $filters['assessment_number'] === (string) $number ? 'selected' : '' }}>{{ __('Assessment') }} {{ $number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Result</label>
                    <select name="result" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                        <option value="">All</option>
                        <option value="pass" {{ $filters['result'] === 'pass' ? 'selected' : '' }}>Passed</option>
                        <option value="fail" {{ $filters['result'] === 'fail' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

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

    @if($student)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Student Details</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Search results for the selected student</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button
                        type="button"
                        onclick="openMarksheetPrintModal('{{ route('teacher.marksheet.print', array_merge($filters, ['student_id' => $student->id])) }}')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <i class="bi bi-printer"></i>
                        Print Marksheet
                    </button>
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
                    <div class="overflow-x-auto">
                        @php
                            $isCtevt = strtolower($filters['exam_category'] ?? '') === 'ctevt';
                            $grandTotal = 0;
                        @endphp
                        <table class="w-full text-sm text-left border border-gray-300 dark:border-slate-700 border-collapse">
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
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600">
                                                {{ $mark->subject->subject_name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $full }}</td>
                                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-slate-600">{{ $pass }}</td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-slate-600 {{ $fail ? 'bg-red-100 text-red-800' : '' }}">{{ $obt }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
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

<div id="marksheetPrintModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeMarksheetPrintModal()"></div>
    <div class="relative mx-auto w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div>
                <h3 class="text-base font-semibold text-white">Print Marksheet</h3>
                <p class="text-blue-100 text-xs">A4 preview (use Print to open dialog)</p>
            </div>
            <button onclick="closeMarksheetPrintModal()" class="text-blue-100 hover:text-white p-2 rounded-full hover:bg-white/10" aria-label="Close print preview">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 bg-gray-100 dark:bg-slate-900 p-4 overflow-auto">
            <iframe id="marksheetPrintFrame" src="" class="w-full h-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white"></iframe>
        </div>

        <div class="px-5 py-3 border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-between items-center gap-3">
            <span class="text-xs text-gray-600 dark:text-gray-400">Tip: Use “New tab” for full-page preview.</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openMarksheetPrintInNewTab()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                    <i class="bi bi-box-arrow-up-right mr-1"></i> New tab
                </button>
                <button type="button" onclick="printMarksheetFrame()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm">
                    <i class="bi bi-printer mr-1"></i> Print
                </button>
                <button type="button" onclick="closeMarksheetPrintModal()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleMarksheetAssessmentFilter() {
        const categorySelect = document.getElementById('examCategorySelect');
        const assessmentBlock = document.getElementById('assessmentNumberFilter');
        const assessmentSelect = document.querySelector('select[name="assessment_number"]');

        if (!categorySelect || !assessmentBlock) {
            return;
        }

        if (categorySelect.value === 'assessment') {
            assessmentBlock.classList.remove('hidden');
            assessmentBlock.style.display = '';
        } else {
            assessmentBlock.classList.add('hidden');
            assessmentBlock.style.display = 'none';
            if (assessmentSelect) {
                assessmentSelect.value = '';
            }
        }
    }

    let currentMarksheetPrintUrl = '';

    document.addEventListener('DOMContentLoaded', function() {
        toggleMarksheetAssessmentFilter();
    });

    function openMarksheetPrintModal(url) {
        const modal = document.getElementById('marksheetPrintModal');
        const frame = document.getElementById('marksheetPrintFrame');
        if (!modal || !frame) return;

        currentMarksheetPrintUrl = url || '';
        frame.src = currentMarksheetPrintUrl;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMarksheetPrintModal() {
        const modal = document.getElementById('marksheetPrintModal');
        const frame = document.getElementById('marksheetPrintFrame');
        if (!modal) return;

        modal.classList.add('hidden');
        if (frame) frame.src = '';
        currentMarksheetPrintUrl = '';
        document.body.style.overflow = '';
    }

    function openMarksheetPrintInNewTab() {
        if (!currentMarksheetPrintUrl) return;
        const url = currentMarksheetPrintUrl + (currentMarksheetPrintUrl.includes('?') ? '&' : '?') + 'newTab=1';
        window.open(url, '_blank');
    }

    function printMarksheetFrame() {
        const frame = document.getElementById('marksheetPrintFrame');
        if (frame && frame.contentWindow) frame.contentWindow.print();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMarksheetPrintModal();
        }
    });
</script>
@endpush
@endsection
