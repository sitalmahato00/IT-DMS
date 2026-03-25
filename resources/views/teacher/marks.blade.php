@extends('teacher.layouts.teacherlayout')

@section('title', __('Marks/Results'))

@section('content')
@php
    $currentExam = $selectedExam ? App\Models\Exam::find($selectedExam) : null;
    $filteredSubjects = collect($subjects);
    if ($selectedSemester) {
        $filteredSubjects = $filteredSubjects->where('semester', $selectedSemester);
    }
@endphp
<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif" id="teacherMarksApp">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Student Marks') }}</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Check and update marks for the subjects assigned to your semesters.') }}</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Category') }}:</span>
            <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-slate-600">
                <button
                    type="button"
                    class="px-6 py-2 text-sm font-medium transition-colors {{ $selectedCategory === 'assessment' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                    onclick="switchCategory('assessment')"
                >
                    {{ __('Assessment') }}
                </button>
                <button
                    type="button"
                    class="px-6 py-2 text-sm font-medium transition-colors {{ $selectedCategory === 'ctevt' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                    onclick="switchCategory('ctevt')"
                >
                    {{ __('CTEVT') }}
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-col gap-3 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between dark:border-slate-700">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Filters & Exports') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Use the filters below to target marks from your assigned semester subjects.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="printMarks()" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:bg-gray-50 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-700">
                    <i class="bi bi-printer"></i>
                    <span>{{ __('Print') }}</span>
                </button>
                <button type="button" onclick="exportMarks('excel')" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-green-700">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span>{{ __('Excel') }}</span>
                </button>
                <button type="button" onclick="exportMarks('csv')" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-blue-700">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ __('CSV') }}</span>
                </button>
            </div>
        </div>
        <form id="marksFilterForm" class="space-y-4 p-5">
            <input type="hidden" name="category" id="categorySelect" value="{{ $selectedCategory }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Search') }}</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="{{ __('Name or Roll No...') }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                    >
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Semester') }}</label>
                    <select name="semester" id="semesterSelect" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" onchange="semesterChanged()">
                        <option value="">{{ __('Select Semester') }}</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem['id'] }}" {{ (string) $selectedSemester === (string) $sem['id'] ? 'selected' : '' }}>
                                {{ $sem['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Subject') }}</label>
                    <select name="subject" id="subjectSelect" onchange="categoryChanged()" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" {{ $selectedSemester ? '' : 'disabled' }}>
                        <option value="">{{ $selectedSemester ? __('Select Subject') : __('Select semester first') }}</option>
                        @foreach($filteredSubjects as $subject)
                            <option value="{{ $subject['id'] }}" {{ (string) $selectedSubject === (string) $subject['id'] ? 'selected' : '' }}>
                                {{ $subject['code'] ?? '' }} - {{ $subject['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Exam') }}</label>
                    <select name="exam" id="examSelect" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="">{{ __('Select Exam') }}</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam['id'] }}" {{ (string) $selectedExam === (string) $exam['id'] ? 'selected' : '' }}>
                                {{ $exam['formatted_assessment'] ?? '' }} - {{ $exam['name'] ?? $exam['exam_name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        <option value="filled" {{ $selectedStatus === 'filled' ? 'selected' : '' }}>{{ __('Filled') }}</option>
                        <option value="empty" {{ $selectedStatus === 'empty' ? 'selected' : '' }}>{{ __('Empty') }}</option>
                        <option value="pass" {{ $selectedStatus === 'pass' ? 'selected' : '' }}>{{ __('Pass') }}</option>
                        <option value="fail" {{ $selectedStatus === 'fail' ? 'selected' : '' }}>{{ __('Fail') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sort By') }}</label>
                    <select name="sort_by" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="roll_no" {{ ($selectedSortBy ?? 'roll_no') === 'roll_no' ? 'selected' : '' }}>{{ __('Roll Number') }}</option>
                        <option value="name" {{ ($selectedSortBy ?? '') === 'name' ? 'selected' : '' }}>{{ __('Student Name') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-3 dark:border-slate-700">
                <button type="button" onclick="applyFilters()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <i class="bi bi-funnel"></i>
                    <span>{{ __('Apply Filters') }}</span>
                </button>
                <a href="{{ route('teacher.marks') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-700">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>{{ __('Reset') }}</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Marks Table -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" id="marksTableContainer">
        @if(!$selectedSemester || !$selectedSubject)
            <div class="m-5 rounded-xl border border-amber-200 bg-amber-50 p-6 text-amber-900 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle text-2xl"></i>
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('Select Semester & Subject') }}</h3>
                        <p class="mt-1 text-sm">{{ __('Choose a semester first, then pick one of your assigned subjects to view the marks grid below.') }}</p>
                    </div>
                </div>
            </div>
        @elseif(!$selectedExam)
            <div class="m-5 rounded-xl border border-blue-200 bg-blue-50 p-6 text-blue-900 shadow-sm dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-100">
                <div class="flex items-start gap-3">
                    <i class="bi bi-journal-check text-2xl"></i>
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('Select Exam') }}</h3>
                        <p class="mt-1 text-sm">{{ __('Pick an exam for the selected subject to check and update marks.') }}</p>
                    </div>
                </div>
            </div>
        @elseif($students->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-gray-700" id="marksTable">
                    <thead class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-16">{{ __('Roll') }}</th>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-center w-24">{{ __('Attendance') }}</th>
                            
                            @if($selectedCategory === 'ctevt')
                                <!-- CTEVT: TI, TE, (PI, PE if subject has lab) - Each with Full/Pass column and Obtained column -->
                                <th colspan="2" class="px-2 py-3 text-center border-l border-gray-300 dark:border-gray-600">{{ __('TI Marks') }}</th>
                                <th colspan="2" class="px-2 py-3 text-center border-l border-gray-300 dark:border-gray-600">{{ __('TE Marks') }}</th>
                                @if($selectedSubjectHasLab)
                                    <th colspan="2" class="px-2 py-3 text-center border-l border-gray-300 dark:border-gray-600">{{ __('PI Marks') }}</th>
                                    <th colspan="2" class="px-2 py-3 text-center border-l border-gray-300 dark:border-gray-600">{{ __('PE Marks') }}</th>
                                @endif
                            @else
                                <!-- Assessment: Full Marks, Pass Marks, Obtained -->
                                <th class="px-4 py-3 text-center border-l border-gray-300 dark:border-gray-600">{{ __('Full Marks') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Pass Marks') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('Obtained') }}</th>
                            @endif
                            
                            <th class="px-4 py-3 text-center border-l border-gray-300 dark:border-gray-600">{{ __('Total') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Result') }}</th>
                        </tr>
                        <!-- Sub-header for column details -->
                        <tr class="bg-gray-100 dark:bg-gray-600 text-xs">
                            <th colspan="3" class="px-4 py-2"></th>
                            @if($selectedCategory === 'ctevt')
                                <!-- CTEVT: Full/Pass format "20/8", Obtained -->
                                <th class="px-1 py-2 text-center">{{ __('Full/Pass') }}</th>
                                <th class="px-1 py-2 text-center">{{ __('Obt.') }}</th>
                                <th class="px-1 py-2 text-center">{{ __('Full/Pass') }}</th>
                                <th class="px-1 py-2 text-center">{{ __('Obt.') }}</th>
                                @if($selectedSubjectHasLab)
                                    <th class="px-1 py-2 text-center">{{ __('Full/Pass') }}</th>
                                    <th class="px-1 py-2 text-center">{{ __('Obt.') }}</th>
                                    <th class="px-1 py-2 text-center">{{ __('Full/Pass') }}</th>
                                    <th class="px-1 py-2 text-center">{{ __('Obt.') }}</th>
                                @endif
                            @else
                                <!-- Assessment: Full, Pass, Obtained -->
                                <th class="px-1 py-2 text-center border-l border-gray-300 dark:border-gray-600">{{ __('Full') }}</th>
                                <th class="px-1 py-2 text-center">{{ __('Pass') }}</th>
                                <th class="px-1 py-2 text-center">{{ __('Obt.') }}</th>
                            @endif
                            <th colspan="2" class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $student)
                        @php
                            $mark = $student->exam_mark;
                            $isFilled = $student->is_filled;
                            $result = $student->result;
                            
                            // Get exam info for column headers
                            $exam = $currentExam;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $isFilled ? 'bg-green-50 dark:bg-green-900/10' : 'bg-red-50 dark:bg-red-900/10' }}">
                            <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $student->roll_no ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $student->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $student->attendance_percentage >= 75 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                    {{ $student->attendance_percentage ?? 100 }}%
                                </span>
                            </td>
                            
                            @if($selectedCategory === 'ctevt' && $exam)
                                @php
                                    $tiFull = $mark->theory_internal_full_marks ?? $exam->theory_internal_max_marks ?? 0;
                                    $teFull = $mark->theory_external_full_marks ?? $exam->theory_external_max_marks ?? 0;
                                    $piFull = $selectedSubjectHasLab ? ($mark->practical_internal_full_marks ?? $exam->practical_internal_max_marks ?? 0) : 0;
                                    $peFull = $selectedSubjectHasLab ? ($mark->practical_external_full_marks ?? $exam->practical_external_max_marks ?? 0) : 0;

                                    $tiPassVal = $mark->theory_internal_pass_marks ?? $exam->theory_internal_pass_marks ?? 0;
                                    $tePassVal = $mark->theory_external_pass_marks ?? $exam->theory_external_pass_marks ?? 0;
                                    $piPassVal = $selectedSubjectHasLab ? ($mark->practical_internal_pass_marks ?? $exam->practical_internal_pass_marks ?? 0) : 0;
                                    $pePassVal = $selectedSubjectHasLab ? ($mark->practical_external_pass_marks ?? $exam->practical_external_pass_marks ?? 0) : 0;

                                    $tiFullPass = $tiFull . '/' . $tiPassVal;
                                    $teFullPass = $teFull . '/' . $tePassVal;
                                    $piFullPass = $selectedSubjectHasLab ? ($piFull . '/' . $piPassVal) : 0;
                                    $peFullPass = $selectedSubjectHasLab ? ($peFull . '/' . $pePassVal) : 0;

                                    // Check pass for each component
                                    $tiPass = $mark ? ($mark->theory_internal_marks >= $tiPassVal) : false;
                                    $tePass = $mark ? ($mark->theory_external_marks >= $tePassVal) : false;
                                    $piPass = $selectedSubjectHasLab ? ($mark ? ($mark->practical_internal_marks >= $piPassVal) : false) : true;
                                    $pePass = $selectedSubjectHasLab ? ($mark ? ($mark->practical_external_marks >= $pePassVal) : false) : true;
                                @endphp
                                <!-- TI: Full/Pass and Obtained -->
                                <td class="px-1 py-4 text-center text-gray-600 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">{{ $tiFullPass }}</td>
                                <td class="px-1 py-4 text-center">
                                    <input type="number" 
                                        class="w-14 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 {{ $tiPass === false ? 'border-red-500 bg-red-50 dark:bg-red-900/30' : '' }}" 
                                        data-mark-id="{{ $mark?->id }}"
                                        data-student-id="{{ $student->id }}"
                                        data-component="ti"
                                        data-exam-id="{{ $selectedExam }}"
                                        value="{{ $mark ? $mark->theory_internal_marks : '' }}"
                                        min="0"
                                        max="{{ $exam->theory_internal_max_marks ?? 100 }}"
                                        onchange="updateMarks(this)"
                                    >
                                </td>
                                
                                <!-- TE: Full/Pass and Obtained -->
                                <td class="px-1 py-4 text-center text-gray-600 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">{{ $teFullPass }}</td>
                                <td class="px-1 py-4 text-center">
                                    <input type="number" 
                                        class="w-14 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 {{ $tePass === false ? 'border-red-500 bg-red-50 dark:bg-red-900/30' : '' }}" 
                                        data-mark-id="{{ $mark?->id }}"
                                        data-student-id="{{ $student->id }}"
                                        data-component="te"
                                        data-exam-id="{{ $selectedExam }}"
                                        value="{{ $mark ? $mark->theory_external_marks : '' }}"
                                        min="0"
                                        max="{{ $exam->theory_external_max_marks ?? 100 }}"
                                        onchange="updateMarks(this)"
                                    >
                                </td>
                                
                                @if($selectedSubjectHasLab)
                                    <!-- PI: Full/Pass and Obtained -->
                                    <td class="px-1 py-4 text-center text-gray-600 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">{{ $piFullPass }}</td>
                                    <td class="px-1 py-4 text-center">
                                        <input type="number" 
                                            class="w-14 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 {{ $piPass === false ? 'border-red-500 bg-red-50 dark:bg-red-900/30' : '' }}" 
                                            data-mark-id="{{ $mark?->id }}"
                                            data-student-id="{{ $student->id }}"
                                            data-component="pi"
                                            data-exam-id="{{ $selectedExam }}"
                                            value="{{ $mark ? $mark->practical_internal_marks : '' }}"
                                            min="0"
                                            max="{{ $exam->practical_internal_max_marks ?? 100 }}"
                                            onchange="updateMarks(this)"
                                        >
                                    </td>
                                    
                                    <!-- PE: Full/Pass and Obtained -->
                                    <td class="px-1 py-4 text-center text-gray-600 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">{{ $peFullPass }}</td>
                                    <td class="px-1 py-4 text-center">
                                        <input type="number" 
                                            class="w-14 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 {{ $pePass === false ? 'border-red-500 bg-red-50 dark:bg-red-900/30' : '' }}" 
                                            data-mark-id="{{ $mark?->id }}"
                                            data-student-id="{{ $student->id }}"
                                            data-component="pe"
                                            data-exam-id="{{ $selectedExam }}"
                                            value="{{ $mark ? $mark->practical_external_marks : '' }}"
                                            min="0"
                                            max="{{ $exam->practical_external_max_marks ?? 100 }}"
                                            onchange="updateMarks(this)"
                                        >
                                    </td>
                                @endif
                            @elseif($exam)
                                <!-- Assessment: Full, Pass, Obtained -->
                                <td class="px-1 py-4 text-center text-gray-600 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">{{ $exam->full_marks ?? 100 }}</td>
                                <td class="px-1 py-4 text-center text-gray-500 dark:text-gray-500">{{ $exam->passing_marks ?? 40 }}</td>
                                <td class="px-1 py-4 text-center">
                                    @php
                                        $assessPass = $mark ? ($mark->marks_obtained >= ($exam->passing_marks ?? 40)) : false;
                                    @endphp
                                    <input type="number" 
                                        class="w-20 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500 {{ $assessPass === false && $mark ? 'border-red-500 bg-red-50 dark:bg-red-900/30' : '' }}" 
                                        data-mark-id="{{ $mark?->id }}"
                                        data-student-id="{{ $student->id }}"
                                        data-component="marks"
                                        data-exam-id="{{ $selectedExam }}"
                                        value="{{ $mark ? $mark->marks_obtained : '' }}"
                                        min="0"
                                        max="{{ $exam->full_marks ?? 100 }}"
                                        onchange="updateMarks(this)"
                                    >
                                </td>
                            @else
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400 border-l border-gray-200 dark:border-gray-700">
                                    {{ __('Select an exam') }}
                                </td>
                            @endif
                            
                            <!-- Total -->
                            <td class="px-4 py-4 text-center font-bold text-gray-900 dark:text-white border-l border-gray-300 dark:border-gray-600">
                                @if($mark)
                                    {{ number_format($mark->calculateTotalMarks(), 1) }}
                                @else
                                    0
                                @endif
                            </td>
                            
                            <!-- Result -->
                            <td class="px-4 py-4 text-center">
                                @if($mark)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $result === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ $result }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        -
                                    </span>
                                @endif
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $students->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-search text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Marks Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No marks match your filter criteria.') }}</p>
            </div>
        @endif
    </div>
</div>

<div id="printModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closePrintModal()"></div>
    <div class="relative mx-auto w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-rose-600 to-red-600">
            <div>
                <h3 class="text-base font-semibold text-white">{{ __('Print Marks') }}</h3>
                <p class="text-rose-100 text-xs">{{ __('A4 preview (use Print to open dialog)') }}</p>
            </div>
            <button onclick="closePrintModal()" class="text-rose-100 hover:text-white p-2 rounded-full hover:bg-white/10" aria-label="Close print preview">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="flex-1 bg-gray-100 dark:bg-slate-900 p-4 overflow-auto">
            <iframe id="printFrame" src="" class="w-full h-full rounded-lg border border-gray-200 dark:border-slate-700 bg-white"></iframe>
        </div>

        <div class="px-5 py-3 border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-between items-center gap-3">
            <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Tip: Use "New tab" for full-page preview.') }}</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openPrintInNewTab()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                    <i class="bi bi-box-arrow-up-right mr-1"></i> {{ __('New tab') }}
                </button>
                <button type="button" onclick="printFrame()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition shadow-sm">
                    <i class="bi bi-printer mr-1"></i> {{ __('Print') }}
                </button>
                <button type="button" onclick="closePrintModal()" class="px-3 py-1.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPrintPreviewUrl = '';
    const teacherSubjects = @json(collect($subjects)->values());

    // Apply filters
    function applyFilters() {
        const form = document.getElementById('marksFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        formData.forEach((value, key) => {
            if (value) params.set(key, value);
        });
        
        window.location.href = '{{ route("teacher.marks") }}?' + params.toString();
    }

    function switchCategory(category) {
        const categoryInput = document.getElementById('categorySelect');
        if (!categoryInput) return;

        categoryInput.value = category;
        const subjectSelect = document.getElementById('subjectSelect');
        const examSelect = document.getElementById('examSelect');
        if (subjectSelect) subjectSelect.value = '';
        if (examSelect) examSelect.value = '';
        applyFilters();
    }

    function semesterChanged() {
        const semesterSelect = document.getElementById('semesterSelect');
        const subjectSelect = document.getElementById('subjectSelect');
        const examSelect = document.getElementById('examSelect');
        if (!semesterSelect || !subjectSelect) return;

        const semester = semesterSelect.value;
        const filteredSubjects = teacherSubjects.filter(subject => {
            if (!semester) return false;
            return String(subject.semester) === String(semester);
        });

        subjectSelect.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = semester ? '{{ __("Select Subject") }}' : '{{ __("Select semester first") }}';
        subjectSelect.appendChild(defaultOption);

        filteredSubjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = `${subject.code ?? ''} - ${subject.name}`.replace(/^ - /, '');
            subjectSelect.appendChild(option);
        });

        subjectSelect.disabled = !semester;
        subjectSelect.value = '';
        if (examSelect) {
            examSelect.innerHTML = '<option value="">{{ __("Select Exam") }}</option>';
        }
    }

    function categoryChanged() {
        const category = document.getElementById('categorySelect').value;
        const subjectId = document.getElementById('subjectSelect').value;
        
        // Fetch exams for selected category
        fetch('{{ route("teacher.marks.exams") }}?category=' + category + '&subject_id=' + subjectId)
            .then(response => response.json())
            .then(data => {
                const examSelect = document.getElementById('examSelect');
                examSelect.innerHTML = '<option value="">{{ __("All Exams") }}</option>';
                
                if (data.success && data.exams) {
                    data.exams.forEach(exam => {
                        const option = document.createElement('option');
                        option.value = exam.id;
                        option.textContent = exam.exam_name;
                        examSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Update marks via AJAX
    function updateMarks(input) {
        const markId = input.dataset.markId;
        const studentId = input.dataset.studentId;
        const component = input.dataset.component;
        const examId = input.dataset.examId;
        const value = input.value;
        
        if (!examId) {
            alert('Please select an exam first');
            input.value = '';
            return;
        }

        const formData = new FormData();
        let url = '{{ route("teacher.marks.store") }}';
        let method = 'POST';

        if (markId) {
            url = '{{ route("teacher.marks.update", ["id" => "__MARK_ID__"]) }}'.replace('__MARK_ID__', markId);
            method = 'POST';
            formData.append('_method', 'PUT');

            if (component === 'marks') {
                formData.append('marks_obtained', value);
            } else {
                formData.append(component + '_marks', value);
            }
        } else {
            formData.append('exam_id', examId);
            formData.append('marks[0][student_id]', studentId);

            if (component === 'marks') {
                formData.append('marks[0][marks]', value);
            } else {
                formData.append(`marks[0][${component}_marks]`, value);
            }
        }

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success indicator
                input.classList.add('border-green-500', 'bg-green-50');
                setTimeout(() => {
                    input.classList.remove('border-green-500', 'bg-green-50');
                }, 1000);
                
                // Reload to show updated totals and results
                setTimeout(() => {
                    applyFilters();
                }, 500);
            } else {
                alert('Error: ' + (data.message || 'Failed to update marks'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update marks');
        });
    }

    // Print marks
    function printMarks() {
        const form = document.getElementById('marksFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        formData.forEach((value, key) => {
            if (value) params.set(key, value);
        });

        openPrintModal('{{ route("teacher.marks.print") }}?' + params.toString());
    }

    // Export marks
    function exportMarks(format) {
        const form = document.getElementById('marksFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        formData.forEach((value, key) => {
            if (value) params.set(key, value);
        });
        window.location.href = '{{ route("teacher.marks.export") }}?format=' + format + '&' + params.toString();
    }

    function openPrintModal(url) {
        const modal = document.getElementById('printModal');
        const frame = document.getElementById('printFrame');
        if (!modal || !frame) return;

        currentPrintPreviewUrl = url || '';
        frame.src = currentPrintPreviewUrl;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePrintModal() {
        const modal = document.getElementById('printModal');
        const frame = document.getElementById('printFrame');
        if (!modal) return;

        modal.classList.add('hidden');
        if (frame) frame.src = '';
        currentPrintPreviewUrl = '';
        document.body.style.overflow = '';
    }

    function openPrintInNewTab() {
        if (!currentPrintPreviewUrl) return;
        const url = currentPrintPreviewUrl + (currentPrintPreviewUrl.includes('?') ? '&' : '?') + 'newTab=1';
        window.open(url, '_blank');
    }

    function printFrame() {
        const frame = document.getElementById('printFrame');
        if (frame && frame.contentWindow) frame.contentWindow.print();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePrintModal();
        }
    });

</script>
@endpush
