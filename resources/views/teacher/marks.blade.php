@extends('teacher.layouts.teacherlayout')

@section('title', __('Marks/Results'))

@section('content')
<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif" id="teacherMarksApp">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('Marks/Results') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('Manage exam marks and results for your subjects.') }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Students') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-people text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Filled') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $stats['filled'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400">
                    <i class="bi bi-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Empty') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $stats['empty'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-x-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Passed') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $stats['passed'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400">
                    <i class="bi bi-mortarboard text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Failed') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $stats['failed'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form id="marksFilterForm" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Category -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Category') }}</label>
                    <select name="category" id="categorySelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" onchange="categoryChanged()">
                        <option value="assessment" {{ $selectedCategory === 'assessment' ? 'selected' : '' }}>{{ __('Assessment') }}</option>
                        <option value="ctevt" {{ $selectedCategory === 'ctevt' ? 'selected' : '' }}>{{ __('CTEVT') }}</option>
                    </select>
                </div>

                <!-- Program -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Program') }}</label>
                    <select name="program" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">{{ __('All Programs') }}</option>
                        @if($programs->isNotEmpty())
                            @foreach($programs as $program)
                                <option value="{{ $program['id'] }}" {{ $selectedProgram == $program['id'] ? 'selected' : '' }}>
                                    {{ $program['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Semester -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Semester') }}</label>
                    <select name="semester" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">{{ __('All Semesters') }}</option>
                        @if($semesters->isNotEmpty())
                            @foreach($semesters as $sem)
                                <option value="{{ $sem['id'] }}" {{ $selectedSemester == $sem['id'] ? 'selected' : '' }}>
                                    {{ $sem['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Batch -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Batch') }}</label>
                    <select name="batch" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">{{ __('All Batches') }}</option>
                        @if($batches->isNotEmpty())
                            @foreach($batches as $batch)
                                <option value="{{ $batch['id'] }}" {{ $selectedBatch == $batch['id'] ? 'selected' : '' }}>
                                    {{ $batch['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Subject -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                    <select name="subject" id="subjectSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">{{ __('All Subjects') }}</option>
                        @if($subjects->isNotEmpty())
                            @foreach($subjects as $subject)
                                <option value="{{ $subject['id'] }}" {{ $selectedSubject == $subject['id'] ? 'selected' : '' }}>
                                    {{ $subject['code'] ?? '' }} - {{ $subject['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Exam -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Exam') }}</label>
                    <select name="exam" id="examSelect" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">{{ __('All Exams') }}</option>
                        @if($exams->isNotEmpty())
                            @foreach($exams as $exam)
                                <option value="{{ $exam['id'] }}" {{ $selectedExam == $exam['id'] ? 'selected' : '' }}>
                                    {{ $exam['formatted_assessment'] ?? '' }} - {{ $exam['name'] ?? $exam['exam_name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Status -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">{{ __('All') }}</option>
                        <option value="filled" {{ $selectedStatus === 'filled' ? 'selected' : '' }}>{{ __('Filled') }}</option>
                        <option value="empty" {{ $selectedStatus === 'empty' ? 'selected' : '' }}>{{ __('Empty') }}</option>
                        <option value="pass" {{ $selectedStatus === 'pass' ? 'selected' : '' }}>{{ __('Pass') }}</option>
                        <option value="fail" {{ $selectedStatus === 'fail' ? 'selected' : '' }}>{{ __('Fail') }}</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Student Name, Roll No') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="flex items-center gap-2 justify-between flex-wrap pt-2">
                <div class="flex gap-2 flex-wrap">
                    <button type="button" onclick="applyFilters()" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition-colors font-medium shadow-sm">
                        <i class="bi bi-funnel"></i> {{ __('Apply Filter') }}
                    </button>
                    <a href="{{ route('teacher.marks') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button type="button" onclick="printMarks()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-printer"></i> {{ __('Print') }}
                    </button>
                    <button type="button" onclick="exportMarks('pdf')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium">
                        <i class="bi bi-file-pdf"></i> {{ __('PDF') }}
                    </button>
                    <button type="button" onclick="exportMarks('excel')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition-colors font-medium">
                        <i class="bi bi-file-excel"></i> {{ __('Excel') }}
                    </button>
                    <button type="button" onclick="exportMarks('csv')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors font-medium">
                        <i class="bi bi-file-earmark-text"></i> {{ __('CSV') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Marks Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden" id="marksTableContainer">
        @if(!$selectedExam && !$selectedSubject && !$selectedSemester && !$selectedBatch)
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-funnel text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('Select Filters to View Marks') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Please select an Exam, Subject, or use other filters to display student marks.') }}</p>
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
                            <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
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
                            <th colspan="3" class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $student)
                        @php
                            $mark = $student->exam_mark;
                            $isFilled = $student->is_filled;
                            $result = $student->result;
                            
                            // Get exam info for column headers
                            $exam = null;
                            if($selectedExam) {
                                $exam = App\Models\Exam::find($selectedExam);
                            }
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
                            
                            <!-- Actions -->
                            <td class="px-4 py-4 text-center">
                                <button onclick="editStudentMarks({{ $student->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="bi bi-pencil"></i>
                                </button>
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

<!-- Edit Modal -->
<div id="editMarksModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ __('Edit Marks') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" id="modalStudentInfo"></p>
            </div>
            <button onclick="closeModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        
        <div class="p-6" id="modalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
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

    // Category changed - reload exams
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
        
        applyFilters();
    }

    // Update marks via AJAX
    function updateMarks(input) {
        const studentId = input.dataset.studentId;
        const component = input.dataset.component;
        const examId = input.dataset.examId;
        const value = input.value;
        
        if (!examId) {
            alert('Please select an exam first');
            input.value = '';
            return;
        }

        // Send AJAX request
        const formData = new FormData();
        formData.append('student_id', studentId);
        formData.append('exam_id', examId);
        formData.append('component', component);
        formData.append('value', value);
        
        fetch('{{ route("teacher.marks.update") }}', {
            method: 'POST',
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
        
        window.location.href = '{{ route("teacher.marks.print") }}?' + params.toString();
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

    // Edit student marks
    function editStudentMarks(studentId) {
        const modal = document.getElementById('editMarksModal');
        modal.classList.remove('hidden');
        
        const params = new URLSearchParams({
            student_id: studentId,
            exam_id: document.querySelector('select[name="exam"]')?.value || ''
        });
        
        fetch('{{ route("teacher.marks.edit") }}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalStudentInfo').textContent = data.student_info;
                    document.getElementById('modalContent').innerHTML = data.html;
                } else {
                    alert('Failed to load student marks');
                    closeModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load student marks');
                closeModal();
            });
    }

    // Close modal
    function closeModal() {
        document.getElementById('editMarksModal').classList.add('hidden');
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush
