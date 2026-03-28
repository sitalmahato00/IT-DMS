@extends('teacher.layouts.teacherlayout')

@section('title', 'Exam Details - ' . $exam->exam_name)

@section('content')
@php
    $examComponentDefinitions = [
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

    $activeExamCategory = in_array($exam->exam_category ?? '', ['assessment', 'ctevt']) ? $exam->exam_category : 'assessment';
@endphp

<div class="space-y-4">
    <!-- Back Button & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.exams') }}" class="text-gray-600 hover:text-gray-900">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <h2 class="text-lg font-semibold text-gray-900">{{ $exam->localized_name }}</h2>
        </div>
        <!-- View moved into filter row for alignment with other actions -->
    </div>

    <!-- Exam details moved to View Details modal -->

<!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @php
        // Calculate statistics using effective mark values
        $passedCount = 0;
        $failedCount = 0;

        foreach($exam->marks as $mark) {
            $fullMarks = $mark->full_marks > 0 ? $mark->full_marks : $mark->calculateFullMarks();
            $passingMarks = $mark->passing_marks > 0 ? $mark->passing_marks : $mark->getEffectivePassingMarksAttribute();
            $obtainedMarks = $mark->isCtevt() ? $mark->calculateTotalMarks() : ($mark->marks_obtained ?? 0);
            $percentage = $fullMarks > 0 ? round(($obtainedMarks / $fullMarks) * 100, 2) : 0;
            $isPassed = $percentage >= ($fullMarks > 0 ? ($passingMarks / $fullMarks * 100) : 0);

            if ($isPassed) {
                $passedCount++;
            } else {
                $failedCount++;
            }
        }
        @endphp
        <x-stats-card title="Total Students" value="{{ $totalStudents }}" icon="bi bi-people-fill" color="blue" />
        <x-stats-card title="Avg Marks" value="{{ number_format($averageMarks, 2) }}%" icon="bi bi-percent" color="green" />
        <x-stats-card title="Passed" value="{{ $passedCount }}" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="Failed" value="{{ $failedCount }}" icon="bi bi-x-circle" color="red" />
    </div>

    <!-- Marks Table with Filters -->
    <div class="bg-white rounded shadow-sm border border-gray-200">
        <div class="p-3 border-b border-gray-200 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-gray-900">Student Marks</h3>
        <!-- Filter Tabs -->
                <div class="flex items-center gap-1 bg-gray-100 p-0.5 rounded ml-4">
                    <input type="hidden" name="mark_filter" id="markFilter" value="all">
                    <button type="button" onclick="filterMarks('all')" class="px-3 py-1 text-xs rounded transition bg-white text-blue-600 shadow-sm font-medium" data-filter="all">All</button>
                </div>
            </div>
            
        </div>
        
        <!-- Advanced Filters + Actions: filters on the left, actions aligned on the right -->
        <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between gap-4">
                <form id="marksFilterForm" class="flex items-center gap-2 flex-nowrap overflow-x-auto">
                    <div class="flex items-center gap-1">
                        <input type="text" id="searchStudent" placeholder="Search student..." class="w-40 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <!-- Academic year filter removed per request; column remains in table -->
                    <div class="flex items-center gap-1">
                        <div class="relative">
                            <label class="sr-only">Semester</label>
                            @php $semesters = ['first'=>'First','second'=>'Second','third'=>'Third','fourth'=>'Fourth','fifth'=>'Fifth','sixth'=>'Sixth']; @endphp
                            @if($exam->semester && $exam->semester !== 'all')
                                <div class="w-36 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 text-gray-700 font-medium">
                                    {{ $semesters[$exam->semester] ?? ucfirst($exam->semester) }}
                                </div>
                                <input type="hidden" id="filterSemester" name="semester" value="{{ $exam->semester }}">
                            @else
                                <select id="filterSemester" name="semester" class="js-filter-semester w-36 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="loadSubjectsForMarkUploadAndTable()">
                                    <option value="">Semester</option>
                                    @foreach($semesters as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="relative">
                            <label class="sr-only">Subject</label>
                            @if($exam->subject_id && $exam->subject_id !== 'all')
                                <div class="w-48 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-100 text-gray-700 font-medium">
                                    {{ $exam->subject ? $exam->subject->subject_name : 'Not assigned' }}
                                </div>
                                <input type="hidden" id="filterSubject" name="subject_id" value="{{ $exam->subject_id }}">
                            @else
                                <select id="filterSubject" name="subject_id" class="js-filter-subject w-48 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="applyMarksFilters()">
                                    <option value="all">Subject</option>
                                </select>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick="applyMarksFilters()" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 font-medium transition shadow-sm">
                        <i class="bi bi-funnel mr-1"></i>Filter
                    </button>
                    <button type="button" onclick="resetMarksFilters()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-50 font-medium transition">
                        <i class="bi bi-arrow-clockwise mr-1"></i>Reset
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <button onclick="openMarkUploadModal()" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium shadow-sm transition-colors">
                        <i class="bi bi-cloud-upload mr-1"></i>Upload Marks
                    </button>
                    
                    <button onclick="openViewMarksModal()" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-200 font-medium transition-colors">
                        <i class="bi bi-eye mr-1"></i>View Details
                    </button>

                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <x-table :paginate="true" :perPage="25">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-left">Student Name</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-left">Roll No</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-left">Semester</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-left">Academic Year</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Attendance</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Full Marks</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Pass Marks</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Obtained</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Percentage</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Subject</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    // Use 40% for pass/fail calculation
                    $passingPercentage = 40;
                    @endphp
                    @forelse($exam->marks as $mark)
                    @php
                    // Use subject-specific full marks if available, otherwise use exam's default
                    $fullMarks = $mark->full_marks ?? $exam->full_marks;
                    $passingMarks = $mark->passing_marks ?? $exam->passing_marks;
                    
                    // Recalculate percentage using subject-specific full marks
                    $calculatedPercentage = $fullMarks > 0 ? round(($mark->marks_obtained / $fullMarks) * 100, 2) : 0;
                    
                    // Calculate grade based on percentage
                    if ($calculatedPercentage >= 90) $calculatedGrade = 'A+';
                    elseif ($calculatedPercentage >= 80) $calculatedGrade = 'A';
                    elseif ($calculatedPercentage >= 70) $calculatedGrade = 'B+';
                    elseif ($calculatedPercentage >= 60) $calculatedGrade = 'B';
                    elseif ($calculatedPercentage >= 50) $calculatedGrade = 'C+';
                    elseif ($calculatedPercentage >= 40) $calculatedGrade = 'C';
                    elseif ($calculatedPercentage >= 35) $calculatedGrade = 'D';
                    else $calculatedGrade = 'F';
                    
                    // Determine pass/fail status using subject-specific passing marks
                    $isPassed = $calculatedPercentage >= ($fullMarks > 0 ? ($passingMarks / $fullMarks * 100) : 0);
                    
                    // Calculate attendance for this student
                    $attendanceSubjectId = $mark->subject_id ?? $exam->subject_id;
                    $attendance = App\Models\Attendance::where('student_id', $mark->student_id)
                        ->where('subject_id', $attendanceSubjectId)
                        ->count();
                    $present = App\Models\Attendance::where('student_id', $mark->student_id)
                        ->where('subject_id', $attendanceSubjectId)
                        ->where('status', 'present')
                        ->count();
                    $attendancePercentage = $attendance > 0 ? round(($present / $attendance) * 100, 1) : 0;
                    $attendanceClass = $attendancePercentage >= 75 ? 'bg-green-100 text-green-700' : ($attendancePercentage >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                    
                    // Check if this mark uses subject-specific marks (different from exam defaults)
                    $isSubjectSpecific = $mark->full_marks != $exam->full_marks || $mark->passing_marks != $exam->passing_marks;
                    @endphp
                    <tr class="border-b border-gray-200 hover:bg-gray-50 mark-row" 
                        data-percentage="{{ $calculatedPercentage }}" 
                        data-passed="{{ $isPassed ? 'true' : 'false' }}" 
                        data-subject-id="{{ $mark->subject_id ?? $exam->subject_id ?? '' }}"
                        data-academic-year-bs="{{ $mark->student->academic_year_bs ?? '' }}"
                        data-student-semester="{{ $mark->student->semester ?? '' }}"
                        id="mark-row-{{ $mark->id }}">
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $mark->student->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $mark->student->roll_no ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @php
                                $semesterLabels = ['first'=>'First','second'=>'Second','third'=>'Third','fourth'=>'Fourth','fifth'=>'Fifth','sixth'=>'Sixth'];
                            @endphp
                            {{ $mark->student->semester ? ($semesterLabels[$mark->student->semester] ?? ucfirst($mark->student->semester)) : '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">BS {{ $mark->student->academic_year_bs ?? '-' }}</td>
                        <td class="px-4 py-4 text-center text-sm">
                            <span class="inline-block px-2.5 py-0.5 {{ $attendanceClass }} rounded-full text-xs font-medium">{{ $attendancePercentage }}%</span>
                        </td>
                        <td class="px-4 py-4 text-center text-sm {{ $isSubjectSpecific ? 'font-semibold text-blue-600' : 'text-gray-700' }}">
                            {{ $fullMarks }}
                            @if($isSubjectSpecific)
                            <span class="text-xs text-blue-500" title="Subject-specific marks">(S)</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center text-sm {{ $isSubjectSpecific ? 'font-semibold text-blue-600' : 'text-gray-700' }}">
                            {{ $passingMarks }}
                        </td>
                        <td class="px-4 py-4 text-center text-sm">
                            <span class="font-semibold {{ $mark->isAbsent() ? 'text-purple-600' : ($isPassed ? 'text-green-600' : 'text-blue-600') }}">
                                {{ $mark->isAbsent() ? 'ABS' : $mark->marks_obtained }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-gray-700">{{ $mark->isAbsent() ? 'ABS' : number_format($calculatedPercentage, 2) . '%' }}</td>
                        <td class="px-4 py-4 text-center text-sm text-gray-700">
                            {{ $mark->subject->subject_name ?? ($exam->subject->subject_name ?? '-') }}
                        </td>
                        <td class="px-4 py-4 text-center text-sm">
                            @if($mark->isAbsent())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700"><i class="bi bi-person-x mr-1"></i> Absent</span>
                            @elseif($isPassed)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"><i class="bi bi-check-circle mr-1"></i> Passed</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><i class="bi bi-x-circle mr-1"></i> Failed</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center text-sm">
                            <div class="flex items-center justify-center gap-1">
                                <button data-mark-id="{{ $mark->id }}" class="btn-edit-mark inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 bg-blue-100 hover:bg-blue-200 rounded transition" title="Edit Mark">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button onclick="deleteMark({{ $mark->id }})" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-red-700 bg-red-100 hover:bg-red-200 rounded transition" title="Delete Mark">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-3 py-4 text-center text-gray-500 text-xs">
                            No marks have been uploaded yet. Click "Upload Marks" to add marks.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </div>
</div>


<!-- Mark Upload Modal -->
<div id="markUploadModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeMarkUploadModal()"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto mx-auto mt-20">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-red-600 to-red-700">
            <h3 class="text-lg font-semibold text-white">Upload Marks - {{ $exam->localized_name }}</h3>
            <button onclick="closeMarkUploadModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="markUploadForm" method="POST" action="{{ route('teacher.exams.upload-marks', $exam->id) }}" class="px-5 py-4 space-y-4 pb-24">
            @csrf

            <!-- Exam Details Section (Read-only) -->
            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
                <h4 class="font-semibold text-blue-900 mb-3 text-sm">Exam Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-gray-600 font-medium">Exam Name:</span>
                        <p class="text-gray-900 font-semibold">{{ $exam->exam_name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Exam Category:</span>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($exam->exam_category ?? 'general') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Full Marks:</span>
                        <p class="text-gray-900 font-semibold">{{ $exam->full_marks }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Passing Marks:</span>
                        <p class="text-gray-900 font-semibold">{{ $exam->passing_marks }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Exam Date:</span>
                        <p class="text-gray-900 font-semibold">{{ $exam->exam_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 font-medium">Status:</span>
                        <p class="text-gray-900 font-semibold">
                            <span class="inline-block px-2 py-1 rounded text-white text-xs {{ $exam->status=='published' ? 'bg-green-600' : ($exam->status=='draft' ? 'bg-yellow-600' : 'bg-gray-600') }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <!-- Description (Editable) -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <label class="block text-sm font-semibold text-blue-900 mb-2">Description/Notes</label>
                    <textarea id="examDescription" name="description" class="w-full px-3 py-2 border border-blue-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" rows="3" placeholder="Add any notes or guidelines for this exam...">{{ $exam->description ?? '' }}</textarea>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-900 mb-3 text-sm">Filter by Student Details</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <select id="modalSemesterFilter" name="semester" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Semester</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select id="modalSubjectFilter" name="subject_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateSubjectSpecificMarks()">
                        <option value="">Select Subject</option>
                    </select>
                </div>
            </div>

            <!-- Subject-Specific Exam Marks (Editable) -->
            <div id="subjectMarksSection" class="hidden bg-amber-50 border border-amber-200 rounded p-3 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-amber-900 text-sm">📝 Edit Exam Marks for Selected Subject</h4>
                    <span class="text-xs text-amber-700">Component values update subject totals</span>
                </div>
                @if($activeExamCategory === 'ctevt')
                    <input type="hidden" id="subjectFullMarks" value="0">
                    <input type="hidden" id="subjectPassingMarks" value="0">
                    <div id="componentMarksSection" class="space-y-3 mt-4">
                        <p class="text-[11px] text-slate-600 italic">Component-level totals update the subject-specific marks above.</p>
                        <div data-component-panel="ctevt" class="space-y-3">
                            @foreach($examComponentDefinitions['ctevt'] as $component)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white border border-dashed border-gray-200 rounded p-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ $component['label'] }} Full Marks</label>
                                        <input type="number" min="0" step="0.5" data-component="{{ $component['key'] }}" data-component-category="ctevt" data-value-type="max" class="subject-component-input w-full px-3 py-2 border border-gray-200 rounded-md text-sm" placeholder="0">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ $component['label'] }} Pass Marks</label>
                                        <input type="number" min="0" step="0.5" data-component="{{ $component['key'] }}" data-component-category="ctevt" data-value-type="pass" class="subject-component-input w-full px-3 py-2 border border-gray-200 rounded-md text-sm" placeholder="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-amber-900 mb-1">Full Marks for this Subject</label>
                            <input type="number" id="subjectFullMarks" class="w-full px-3 py-2 border border-amber-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" min="0" step="0.5" onchange="updateStudentMarksDisplay()">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-amber-900 mb-1">Passing Marks for this Subject</label>
                            <input type="number" id="subjectPassingMarks" class="w-full px-3 py-2 border border-amber-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" min="0" step="0.5" onchange="updateStudentMarksDisplay()">
                        </div>
                    </div>
                @endif
                <p class="text-xs text-amber-700 mt-2 italic">💡 These marks will apply to all students for this subject. If not set, the exam's default marks will be used. Other subjects will remain unchanged.</p>
            </div>

            <!-- Student Marks Table -->
            <div class="overflow-x-auto border border-gray-200 rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                @if($activeExamCategory === 'ctevt')
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Student ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Roll No</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Attendance %</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Theory Internal</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Theory External</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Practical Internal</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Practical External</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Total Internal</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Total External</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Average %</th>
                </tr>
                @else
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Student ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Roll No</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Attendance %</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700" data-full-marks-col>Full Marks</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700" data-pass-marks-col>Pass Marks</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Marks Obtained</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Average %</th>
                </tr>
                @endif
            </thead>
                    <tbody id="studentsMarksBody" class="divide-y divide-gray-200">
                        <!-- Dynamic student rows will be loaded here -->
                        <tr>
                            <td colspan="11" class="px-4 py-6 text-center text-gray-500 text-sm">
                                Select Semester, then optionally select Subject to filter attendance by subject, then click "Load Students"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Actions (sticky footer) -->
            <div class="sticky bottom-0 left-0 right-0 bg-white px-5 py-3 border-t flex justify-between items-center">
                <button id="loadStudentsBtn" type="button" onclick="loadStudents()" class="px-3 py-1.5 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200" disabled>
                    <i class="bi bi-people mr-1"></i>Load Students
                </button>
                <div class="flex gap-2">
                    <button type="button" onclick="closeMarkUploadModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                    <button id="saveMarksBtn" type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700" disabled>Save Marks</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Marks Modal (read-only details) -->
<div id="viewMarksModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeViewMarksModal()"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-auto mt-20">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-red-600 to-red-700">
            <h3 class="text-lg font-semibold text-white">Exam Details - {{ $exam->localized_name }}</h3>
            <button onclick="closeViewMarksModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="px-5 py-4 space-y-4">
            <!-- Main Exam Details -->
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-900 bg-gray-50 px-3 py-2 rounded">Academic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Exam Name</p>
                        <p class="font-semibold text-gray-900">{{ $exam->exam_name }}</p>
                        @if($exam->exam_name_ne)
                        <p class="text-gray-700 text-xs mt-1">({{ $exam->exam_name_ne }})</p>
                        @endif
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Academic Year</p>
                        <p class="font-semibold text-gray-900">{{ $exam->academic_year }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Semester</p>
                        <p class="font-semibold text-gray-900">{{ $exam->semester === 'all' ? 'All Semesters' : ($semesters[$exam->semester] ?? ucwords($exam->semester)) }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Subject</p>
                        <p class="font-semibold text-gray-900">{{ $exam->subject_id ? ($exam->subject->subject_name ?? 'N/A') : 'All Subjects' }}</p>
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-900 bg-gray-50 px-3 py-2 rounded">Exam Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Exam Category</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($exam->exam_category ?? 'general') }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Status</p>
                        <span class="inline-block px-2 py-1 rounded text-white font-semibold {{ $exam->status=='published' ? 'bg-green-600' : ($exam->status=='draft' ? 'bg-yellow-600' : 'bg-gray-600') }}">{{ ucfirst($exam->status) }}</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Full Marks</p>
                        <p class="font-semibold text-gray-900">{{ $exam->full_marks }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Passing Marks</p>
                        <p class="font-semibold text-gray-900">{{ $exam->passing_marks }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Exam Date (AD)</p>
                        <p class="font-semibold text-gray-900">{{ $exam->exam_date->format('Y-m-d') }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Exam Date (BS)</p>
                        <p class="font-semibold text-gray-900">{{ $exam->exam_date_bs ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Description & Instructions -->
            @if($exam->description || $exam->description_ne || $exam->instructions)
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-900 bg-gray-50 px-3 py-2 rounded">Additional Information</h4>
                @if($exam->description || $exam->description_ne)
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-gray-600 font-medium text-xs">Description</p>
                    <p class="text-gray-700 text-xs mt-1">{{ $exam->localized_description }}</p>
                </div>
                @endif
                @if($exam->instructions)
                <div class="bg-gray-50 p-3 rounded">
                    <p class="text-gray-600 font-medium text-xs">Instructions</p>
                    <p class="text-gray-700 text-xs mt-1">{{ $exam->instructions }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Metadata -->
            <div class="space-y-3 pt-2 border-t border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900 bg-gray-50 px-3 py-2 rounded">Metadata</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Created By</p>
                        <p class="font-semibold text-gray-900">{{ $exam->creator->name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Created At</p>
                        <p class="font-semibold text-gray-900">{{ $exam->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($exam->updated_at && $exam->updated_at != $exam->created_at)
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="text-gray-600 font-medium">Last Updated</p>
                        <p class="font-semibold text-gray-900">{{ $exam->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeViewMarksModal()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mark Modal -->
<div id="editMarkModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeEditMarkModal()"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto mx-auto mt-20">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-red-600 to-red-700">
            <h3 class="text-lg font-semibold text-white">Edit Mark</h3>
            <button onclick="closeEditMarkModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editMarkForm" class="px-5 py-4 space-y-4" onsubmit="submitEditMarkForm(event)">
            <input type="hidden" id="editMarkId" name="mark_id" value="">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                    <div id="editMarkStudent" class="font-semibold text-gray-900 text-sm p-2 bg-gray-50 rounded-md">&nbsp;</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Roll No</label>
                    <div id="editMarkRoll" class="text-gray-700 text-sm p-2 bg-gray-50 rounded-md">&nbsp;</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marks Obtained</label>
                    <input type="number" step="0.5" min="0" id="editMarksObtained" name="marks_obtained" class="w-full sm:w-1/2 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                    <textarea id="editMarkRemarks" name="remarks" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeEditMarkModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
// ============= Route URLs (from Blade) =============
const ROUTES = {
    availableYearsSemesters: "{{ route('teacher.exams.available-years-semesters') }}",
    subjectsBySemester: "{{ route('teacher.exams.subjects') }}",
    studentsWithMarks: "{{ route('teacher.exams.students-with-marks', $exam->id) }}",
};
const EXAM_CATEGORY = '{{ $activeExamCategory }}';
const EXAM_COMPONENT_DEFINITIONS = @json($examComponentDefinitions);
const EXAM_DATA = @json($exam->toArray());
const DEFAULT_EXAM_FULL_MARKS = {{ $exam->full_marks ?? 0 }};
const DEFAULT_EXAM_PASSING_MARKS = {{ $exam->passing_marks ?? 0 }};
const STUDENT_TABLE_COLSPAN = EXAM_CATEGORY === 'ctevt' ? 11 : 8;



function formatComponentValue(value) {
    const numeric = parseFloat(value);
    if (Number.isNaN(numeric)) {
        return '0';
    }
    if (Number.isInteger(numeric)) {
        return numeric.toString();
    }
    return numeric.toFixed(2).replace(/\.00$/, '');
}

function buildAssessmentRow(student, existingMark, subjectId, fullMarks, passingMarks) {
    const studentId = student.id;
    const obtained = parseFloat(existingMark?.marks_obtained) || 0;
    const attendance = student.attendance_percentage ?? '0';
    const percentage = fullMarks > 0 ? (obtained / fullMarks) * 100 : 0;
    const subjectHidden = subjectId ? `<input type="hidden" name="marks[${studentId}][subject_id]" value="${subjectId}">` : '';
    return `
        <tr class="border-b border-gray-200 hover:bg-gray-50" data-student-id="${studentId}" data-full-marks="${fullMarks}">
            <td class="px-4 py-3 text-left text-xs font-medium text-gray-500">${studentId}</td>
            <td class="px-4 py-3 text-left text-xs font-medium text-gray-900">${student.student_name || 'Unknown'}</td>
            <td class="px-4 py-3 text-center text-xs text-gray-700">${student.roll_no || '-'}</td>
            <td class="px-4 py-3 text-center text-xs text-gray-700"><span class="inline-block px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-[11px]">${attendance}%</span></td>
            <td class="px-4 py-3 text-center text-xs text-gray-700 font-semibold" data-full-marks-cell>${fullMarks}</td>
            <td class="px-4 py-3 text-center text-xs text-gray-700 font-semibold" data-pass-marks-cell>${passingMarks}</td>
            <td class="px-4 py-3 text-center text-xs">
                <input type="number" min="0" step="0.5" max="${fullMarks}" name="marks[${studentId}][marks_obtained]" value="${formatComponentValue(obtained)}" class="marks-obtained-input w-20 px-2 py-1 text-center border border-gray-300 rounded text-xs">
                <input type="hidden" name="marks[${studentId}][student_id]" value="${studentId}">
                ${subjectHidden}
            </td>
            <td class="px-4 py-3 text-center text-xs percentage-cell">${formatComponentValue(percentage)}%</td>
            <input type="hidden" name="marks[${studentId}][full_marks]" value="${fullMarks}">
            <input type="hidden" name="marks[${studentId}][passing_marks]" value="${passingMarks}">
        </tr>
    `;
}

function buildCtevtComponentCell(studentId, component, existingMark) {
    const key = component.key;
    const storedValue = existingMark?.[`${key}_marks`];
    const value = storedValue !== undefined && storedValue !== null ? formatComponentValue(storedValue) : '';
    const componentType = key.includes('_internal') ? 'internal' : 'external';
    return `
        <td class="px-4 py-3 text-center text-xs">
            <input type="number" min="0" step="0.5" name="marks[${studentId}][${key}_marks]" value="${value}" class="student-component-input w-20 px-2 py-1 text-center border border-gray-300 rounded text-xs" data-component-key="${key}" data-component-type="${componentType}">
        </td>
    `;
}

function buildCtevtRow(student, existingMark, subjectId, fullMarks, passingMarks) {
    const studentId = student.id;
    const attendance = student.attendance_percentage ?? '0';
    const internalTotal = (parseFloat(existingMark?.theory_internal_marks) || 0) + (parseFloat(existingMark?.practical_internal_marks) || 0);
    const externalTotal = (parseFloat(existingMark?.theory_external_marks) || 0) + (parseFloat(existingMark?.practical_external_marks) || 0);
    const componentSum = internalTotal + externalTotal;
    const defaultObtained = parseFloat(existingMark?.marks_obtained) || 0;
    const guaranteedTotal = componentSum > 0 ? componentSum : defaultObtained;
    const boundedTotal = fullMarks > 0 ? Math.min(guaranteedTotal, fullMarks) : guaranteedTotal;
    const percentage = fullMarks > 0 ? (boundedTotal / fullMarks) * 100 : 0;
    const subjectHidden = subjectId ? `<input type="hidden" name="marks[${studentId}][subject_id]" value="${subjectId}">` : '';
    const componentCells = (EXAM_COMPONENT_DEFINITIONS.ctevt || []).map(component => buildCtevtComponentCell(studentId, component, existingMark)).join('');
    return `
        <tr class="border-b border-gray-200 hover:bg-gray-50" data-student-id="${studentId}" data-full-marks="${fullMarks}">
            <td class="px-4 py-3 text-left text-xs font-medium text-gray-500">${studentId}</td>
            <td class="px-4 py-3 text-left text-xs font-medium text-gray-900">${student.student_name || 'Unknown'}</td>
            <td class="px-4 py-3 text-center text-xs text-gray-700">${student.roll_no || '-'}</td>
            <td class="px-4 py-3 text-center text-xs text-gray-700"><span class="inline-block px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-[11px]">${attendance}%</span></td>
            ${componentCells}
            <td class="px-4 py-3 text-center text-xs font-semibold row-total-internal">${formatComponentValue(internalTotal)}</td>
            <td class="px-4 py-3 text-center text-xs font-semibold row-total-external">${formatComponentValue(externalTotal)}</td>
            <td class="px-4 py-3 text-center text-xs percentage-cell">${formatComponentValue(percentage)}%</td>
            <input type="hidden" name="marks[${studentId}][marks_obtained]" class="marks-obtained-input" value="${formatComponentValue(boundedTotal)}">
            <input type="hidden" name="marks[${studentId}][student_id]" value="${studentId}">
            <input type="hidden" name="marks[${studentId}][full_marks]" value="${fullMarks}">
            <input type="hidden" name="marks[${studentId}][passing_marks]" value="${passingMarks}">
            ${subjectHidden}
        </tr>
    `;
}

function updateStudentRowStats(row) {
    if (!row) return;
    const marksInput = row.querySelector('.marks-obtained-input');
    let obtained = marksInput ? parseFloat(marksInput.value) || 0 : 0;
    if (EXAM_CATEGORY === 'ctevt') {
        obtained = updateCtevtRowTotals(row);
        if (marksInput) {
            marksInput.value = formatComponentValue(obtained);
        }
    }
    const fullMarks = parseFloat(row.dataset.fullMarks) || window.currentSubjectFullMarks || DEFAULT_EXAM_FULL_MARKS;
    const passingMarks = window.currentSubjectPassingMarks || DEFAULT_EXAM_PASSING_MARKS;
    const studentId = row.dataset.studentId;
    const fullMarksHidden = row.querySelector(`input[name="marks[${studentId}][full_marks]"]`);
    const passMarksHidden = row.querySelector(`input[name="marks[${studentId}][passing_marks]"]`);
    if (fullMarksHidden) {
        fullMarksHidden.value = fullMarks;
    }
    if (passMarksHidden) {
        passMarksHidden.value = passingMarks;
    }
    const percentageCell = row.querySelector('.percentage-cell');
    if (percentageCell) {
        const percentage = fullMarks > 0 ? (obtained / fullMarks) * 100 : 0;
        percentageCell.textContent = `${formatComponentValue(percentage)}%`;
    }
}

function updateCtevtRowTotals(row) {
    const internalInputs = row.querySelectorAll('.student-component-input[data-component-type="internal"]');
    const externalInputs = row.querySelectorAll('.student-component-input[data-component-type="external"]');
    let internalTotal = 0;
    let externalTotal = 0;
    internalInputs.forEach(input => internalTotal += parseFloat(input.value) || 0);
    externalInputs.forEach(input => externalTotal += parseFloat(input.value) || 0);
    const internalCell = row.querySelector('.row-total-internal');
    if (internalCell) {
        internalCell.textContent = formatComponentValue(internalTotal);
    }
    const externalCell = row.querySelector('.row-total-external');
    if (externalCell) {
        externalCell.textContent = formatComponentValue(externalTotal);
    }

    let total = internalTotal + externalTotal;
    const fullMarks = parseFloat(row.dataset.fullMarks) || window.currentSubjectFullMarks || DEFAULT_EXAM_FULL_MARKS;
    if (fullMarks > 0 && total > fullMarks) {
        total = fullMarks;
    }

    const marksInput = row.querySelector('.marks-obtained-input');
    if (marksInput) {
        marksInput.value = formatComponentValue(total);
    }

    return total;
}

function updateAllStudentRowStats() {
    const rows = document.querySelectorAll('#studentsMarksBody tr');
    rows.forEach(row => updateStudentRowStats(row));
}
function refreshSubjectComponentPanels() {
    document.querySelectorAll('#componentMarksSection [data-component-panel]').forEach(panel => {
        panel.classList.toggle('hidden', panel.dataset.componentPanel !== EXAM_CATEGORY);
    });
}

function updateSubjectAggregateFields() {
    const panel = document.querySelector(`#componentMarksSection [data-component-panel="${EXAM_CATEGORY}"]`);
    if (!panel) return;
    const maxInputs = panel.querySelectorAll('[data-value-type="max"]');
    const passInputs = panel.querySelectorAll('[data-value-type="pass"]');
    let sumMax = 0;
    let sumPass = 0;
    maxInputs.forEach(input => {
        const value = parseFloat(input.value);
        if (!Number.isNaN(value)) sumMax += value;
    });
    passInputs.forEach(input => {
        const value = parseFloat(input.value);
        if (!Number.isNaN(value)) sumPass += value;
    });
    const fullMarksInput = document.getElementById('subjectFullMarks');
    const passingMarksInput = document.getElementById('subjectPassingMarks');
    if (fullMarksInput) {
        fullMarksInput.value = sumMax || DEFAULT_EXAM_FULL_MARKS;
    }
    if (passingMarksInput) {
        passingMarksInput.value = sumPass || DEFAULT_EXAM_PASSING_MARKS;
    }
    window.currentSubjectFullMarks = sumMax || DEFAULT_EXAM_FULL_MARKS;
    window.currentSubjectPassingMarks = sumPass || DEFAULT_EXAM_PASSING_MARKS;
    updateStudentMarksDisplay();
}

// ============= Modal Control Functions =============

// Mark Upload Modal
function openMarkUploadModal() {
    document.getElementById('markUploadModal').classList.remove('hidden');
    // Load semesters directly since academic year filter is removed
    loadAvailableSemesters();

    // Prefill subject-level marks with exam defaults from creation
    const fullMarksInput = document.getElementById('subjectFullMarks');
    const passingMarksInput = document.getElementById('subjectPassingMarks');
    if (fullMarksInput) fullMarksInput.value = EXAM_DATA.full_marks ?? '';
    if (passingMarksInput) passingMarksInput.value = EXAM_DATA.passing_marks ?? '';

    if (EXAM_CATEGORY === 'ctevt') {
        document.querySelectorAll('#componentMarksSection [data-component-category="ctevt"]').forEach(input => {
            const component = input.dataset.component;
            const valueType = input.dataset.valueType;
            if (!component || !valueType) return;

            const examValueKey = `${component}_${valueType === 'max' ? 'max' : 'pass'}_marks`;
            if (EXAM_DATA[examValueKey] !== undefined && EXAM_DATA[examValueKey] !== null) {
                input.value = EXAM_DATA[examValueKey];
            }
        });
    }

    // Ensure buttons reflect defaults
    setTimeout(() => { if (typeof updateModalActionsState === 'function') updateModalActionsState(); }, 200);
}

function closeMarkUploadModal() {
    document.getElementById('markUploadModal').classList.add('hidden');
    resetUploadForm();
}

// View Marks Modal
function openViewMarksModal() {
    document.getElementById('viewMarksModal').classList.remove('hidden');
}

function closeViewMarksModal() {
    document.getElementById('viewMarksModal').classList.add('hidden');
}

// ============= Load Semesters from Database =============

/**
 * Load available semesters from database (without academic year filter)
 */
let _cachedSemesters = null;

// Defaults for modal - prefill but editable
window.defaultModalSemester = @json(match(strtolower((string) ($exam->semester ?? ''))) {
    'first' => '1',
    'second' => '2',
    'third' => '3',
    'fourth' => '4',
    'fifth' => '5',
    'sixth' => '6',
    'all' => '',
    default => (string) ($exam->semester ?? ''),
});
window.defaultModalSubject = '{{ $exam->subject_id ?? '' }}';

function loadAvailableSemesters() {
    const select = document.getElementById('modalSemesterFilter');
    if (!select) return;
    
    // If we have cached data, use it
    if (_cachedSemesters) {
        populateSemesters(select, _cachedSemesters);
        return;
    }
    
    fetch(ROUTES.availableYearsSemesters)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (data.success && data.years && Array.isArray(data.years)) {
                // Collect all unique semesters from all years
                const allSemesters = [];
                const seenSemesters = new Set();
                
                data.years.forEach(yearData => {
                    if (yearData.semesters) {
                        yearData.semesters.forEach(sem => {
                            const key = sem.value + '-' + sem.label;
                            if (!seenSemesters.has(key)) {
                                seenSemesters.add(key);
                                allSemesters.push(sem);
                            }
                        });
                    }
                });
                
                _cachedSemesters = allSemesters;
                populateSemesters(select, allSemesters);
            } else {
                select.innerHTML = '<option value="">No semesters available</option>';
            }
        })
        .catch(err => {
            console.error('Error loading semesters:', err);
            select.innerHTML = '<option value="">Error loading semesters</option>';
        });
}

function populateSemesters(select, semesters) {
    let html = '<option value="">Select Semester</option>';
    if (!semesters || semesters.length === 0) {
        html = '<option value="">No semesters available</option>';
    } else {
        semesters.forEach(sem => {
            html += `<option value="${sem.value}">${sem.label}</option>`;
        });
    }
    select.innerHTML = html;

    // If a default semester is provided (from exam), pre-select it but keep select editable
    try {
        const def = window.defaultModalSemester || '';
        if (def) {
            const opt = Array.from(select.options).find(o => String(o.value) === String(def));
            if (opt) {
                select.value = def;
                // Trigger change to load subjects for that semester
                const ev = new Event('change', { bubbles: true });
                select.dispatchEvent(ev);
            }
        }
    } catch (err) {
        console.error('Error setting default semester:', err);
    }
}

/**
 * Handle modal semester change - load subjects for that semester
 */
function onModalSemesterChange() {
    const semester = document.getElementById('modalSemesterFilter')?.value || '';
    const subjectSelect = document.getElementById('modalSubjectFilter');
    
    if (!semester) {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        return;
    }
    
    subjectSelect.innerHTML = '<option value="">Loading...</option>';
    
    fetch(`${ROUTES.subjectsBySemester}?semester=${encodeURIComponent(semester)}`)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">Select Subject</option>';
            if (data.success && data.subjects && Array.isArray(data.subjects)) {
                data.subjects.forEach(subject => {
                    html += `<option value="${subject.id}">${subject.subject_name}${subject.subject_code ? ' - ' + subject.subject_code : ''}</option>`;
                });
            }
            subjectSelect.innerHTML = html;
                // If default subject provided by exam, select it (editable)
                try {
                    const defSub = window.defaultModalSubject || '';
                    if (defSub) {
                        const opt = Array.from(subjectSelect.options).find(o => String(o.value) === String(defSub));
                        if (opt) subjectSelect.value = defSub;
                    }
                } catch (err) {
                    console.error('Error setting default subject:', err);
                }
                // Update modal action buttons state
                if (typeof updateModalActionsState === 'function') updateModalActionsState();
        })
        .catch(err => {
            console.error('Error loading subjects:', err);
            subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
        });
}

/**
 * Update subject-specific marks display based on selected subject
 */
function updateSubjectSpecificMarks() {
    const subjectId = document.getElementById('modalSubjectFilter')?.value || '';
    const section = document.getElementById('subjectMarksSection');
    const fullMarksInput = document.getElementById('subjectFullMarks');
    const passingMarksInput = document.getElementById('subjectPassingMarks');
    
    if (!subjectId) {
        section.classList.add('hidden');
        if (fullMarksInput) fullMarksInput.value = EXAM_DATA.full_marks ?? '';
        if (passingMarksInput) passingMarksInput.value = EXAM_DATA.passing_marks ?? '';
        document.querySelectorAll('#componentMarksSection [data-component-category="ctevt"]').forEach(input => input.value = '');
        return;
    }
    
    // Show the section
    section.classList.remove('hidden');
    refreshSubjectComponentPanels();
    
    // Try to load existing marks for this subject from the backend
    const examId = {{ $exam->id }};
    fetch(`/teacher/exams/${examId}/subject-marks/${subjectId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.marks) {
                const marksData = data.marks || {};
                const fullMarks = marksData.full_marks ?? EXAM_DATA.full_marks ?? '';
                const passingMarks = marksData.passing_marks ?? EXAM_DATA.passing_marks ?? '';

                if (fullMarksInput) fullMarksInput.value = fullMarks;
                if (passingMarksInput) passingMarksInput.value = passingMarks;

                if (EXAM_CATEGORY === 'ctevt') {
                    const componentValues = {
                        theory_internal: {
                            max: marksData.theory_internal_full_marks ?? EXAM_DATA.theory_internal_max_marks ?? 0,
                            pass: marksData.theory_internal_pass_marks ?? EXAM_DATA.theory_internal_pass_marks ?? 0,
                        },
                        theory_external: {
                            max: marksData.theory_external_full_marks ?? EXAM_DATA.theory_external_max_marks ?? 0,
                            pass: marksData.theory_external_pass_marks ?? EXAM_DATA.theory_external_pass_marks ?? 0,
                        },
                        practical_internal: {
                            max: marksData.practical_internal_full_marks ?? EXAM_DATA.practical_internal_max_marks ?? 0,
                            pass: marksData.practical_internal_pass_marks ?? EXAM_DATA.practical_internal_pass_marks ?? 0,
                        },
                        practical_external: {
                            max: marksData.practical_external_full_marks ?? EXAM_DATA.practical_external_max_marks ?? 0,
                            pass: marksData.practical_external_pass_marks ?? EXAM_DATA.practical_external_pass_marks ?? 0,
                        },
                    };

                    document.querySelectorAll('#componentMarksSection [data-component-category="ctevt"]').forEach(input => {
                        const component = input.dataset.component;
                        const valueType = input.dataset.valueType;
                        if (!component || !valueType || !componentValues[component]) return;
                        input.value = componentValues[component][valueType] ?? 0;
                    });
                }
            } else {
                if (fullMarksInput) fullMarksInput.value = EXAM_DATA.full_marks ?? '';
                if (passingMarksInput) passingMarksInput.value = EXAM_DATA.passing_marks ?? '';
                if (EXAM_CATEGORY === 'ctevt') {
                    const ctevtDefaults = {
                        theory_internal: {
                            max: EXAM_DATA.theory_internal_max_marks ?? 0,
                            pass: EXAM_DATA.theory_internal_pass_marks ?? 0,
                        },
                        theory_external: {
                            max: EXAM_DATA.theory_external_max_marks ?? 0,
                            pass: EXAM_DATA.theory_external_pass_marks ?? 0,
                        },
                        practical_internal: {
                            max: EXAM_DATA.practical_internal_max_marks ?? 0,
                            pass: EXAM_DATA.practical_internal_pass_marks ?? 0,
                        },
                        practical_external: {
                            max: EXAM_DATA.practical_external_max_marks ?? 0,
                            pass: EXAM_DATA.practical_external_pass_marks ?? 0,
                        },
                    };
                    document.querySelectorAll('#componentMarksSection [data-component-category="ctevt"]').forEach(input => {
                        const component = input.dataset.component;
                        const valueType = input.dataset.valueType;
                        if (!component || !valueType || !ctevtDefaults[component]) return;
                        input.value = ctevtDefaults[component][valueType] ?? 0;
                    });
                }
            }

            // Update the student marks display with the loaded values
            window.currentSubjectFullMarks = parseFloat(fullMarksInput.value) || DEFAULT_EXAM_FULL_MARKS;
            window.currentSubjectPassingMarks = parseFloat(passingMarksInput.value) || DEFAULT_EXAM_PASSING_MARKS;
            updateSubjectAggregateFields();
            updateStudentMarksDisplay();
        })
        .catch(err => {
            console.error('Error loading subject marks:', err);
            if (fullMarksInput) fullMarksInput.value = EXAM_DATA.full_marks ?? '';
            if (passingMarksInput) passingMarksInput.value = EXAM_DATA.passing_marks ?? '';
        });
}

/**
 * Update the student marks table display when full marks or passing marks are changed
 * This ensures only the edited subject's marks are affected
 */
function updateStudentMarksDisplay() {
    const fullMarksInput = document.getElementById('subjectFullMarks');
    const passingMarksInput = document.getElementById('subjectPassingMarks');
    const subjectId = document.getElementById('modalSubjectFilter')?.value;
    
    const fullMarks = fullMarksInput ? parseFloat(fullMarksInput.value) || {{ $exam->full_marks }} : {{ $exam->full_marks }};
    const passingMarks = passingMarksInput ? parseFloat(passingMarksInput.value) || {{ $exam->passing_marks }} : {{ $exam->passing_marks }};
    
    // Store for use during form submit
    window.currentSubjectFullMarks = fullMarks;
    window.currentSubjectPassingMarks = passingMarks;
    
    // Update the table rows if they exist
    const tbody = document.getElementById('studentsMarksBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('tr');
    rows.forEach(row => {
        const fullMarksCell = row.querySelector('[data-full-marks-cell]');
        if (fullMarksCell) {
            fullMarksCell.textContent = fullMarks;
            fullMarksCell.classList.add('font-semibold', 'text-blue-600');
        }
        const passMarksCell = row.querySelector('[data-pass-marks-cell]');
        if (passMarksCell) {
            passMarksCell.textContent = passingMarks;
            passMarksCell.classList.add('font-semibold', 'text-blue-600');
        }
        if (EXAM_CATEGORY !== 'ctevt') {
            const marksInput = row.querySelector('.marks-obtained-input');
            if (marksInput) {
                marksInput.max = fullMarks;
                if (parseFloat(marksInput.value) > fullMarks) {
                    marksInput.value = fullMarks;
                }
            }
        }
        updateStudentRowStats(row);
    });
    updateAllStudentRowStats();
}

// Handle dropdown changes for the modal
document.addEventListener('DOMContentLoaded', function() {
    const modalSemester = document.getElementById('modalSemesterFilter');
    const modalSubject = document.getElementById('modalSubjectFilter');
    const loadBtn = document.getElementById('loadStudentsBtn');
    const saveBtn = document.getElementById('saveMarksBtn');

    // Helper: enable/disable load & save buttons; require both semester and subject selected
    window.updateModalActionsState = function() {
        try {
            const sem = document.getElementById('modalSemesterFilter')?.value || '';
            const sub = document.getElementById('modalSubjectFilter')?.value || '';
            const enabled = (sem && sub);
            if (loadBtn) loadBtn.disabled = !enabled;
            if (saveBtn) saveBtn.disabled = !enabled;
        } catch (err) {
            console.error('updateModalActionsState error', err);
        }
    }

    if (modalSemester) {
        modalSemester.addEventListener('change', function() {
            onModalSemesterChange();
            updateModalActionsState();
        });
    }

    if (modalSubject) {
        modalSubject.addEventListener('change', function() {
            // Update subject-specific marks but DO NOT auto-load students
            updateSubjectSpecificMarks();
            updateModalActionsState();
            if (!this.value) {
                const fullMarksInput = document.getElementById('subjectFullMarks');
                const passingMarksInput = document.getElementById('subjectPassingMarks');
                if (fullMarksInput) fullMarksInput.value = '';
                if (passingMarksInput) passingMarksInput.value = '';
            }
        });
    }

    // Initialize button states (in case defaults are set)
    updateModalActionsState();

    // Attach click handlers to edit buttons (use delegation fallback)
    try {
        // Use event delegation so dynamically replaced rows still work
        document.addEventListener('click', function delegatedEditHandler(e) {
            try {
                const btn = e.target.closest && e.target.closest('.btn-edit-mark');
                if (!btn) return;
                e.preventDefault();
                const id = btn.getAttribute('data-mark-id');
                if (id) openEditMarkModal(id);
            } catch (err) {
                console.error('Delegated edit handler error', err);
            }
        });
    } catch (err) {
        console.error('Error attaching edit button handlers', err);
    }
});

// ============= Mark Upload Functions =============

function loadStudents() {
    const semester = document.getElementById('modalSemesterFilter')?.value || '';
    const subjectId = document.getElementById('modalSubjectFilter')?.value || '';
    const examId = {{ $exam->id }};
    
    // Require semester to be selected
    if (!semester) {
        const tbody = document.getElementById('studentsMarksBody');
        tbody.innerHTML = `<tr><td colspan="${STUDENT_TABLE_COLSPAN}" class="px-3 py-4 text-center text-orange-500">Please select Semester to load students</td></tr>`;
        return;
    }
    
    const tbody = document.getElementById('studentsMarksBody');
    tbody.innerHTML = `<tr><td colspan="${STUDENT_TABLE_COLSPAN}" class="px-3 py-4 text-center text-gray-500">Loading...</td></tr>`;
    
    const params = new URLSearchParams({
        semester: semester,
        subject_id: subjectId
    });
    
    const url = `${ROUTES.studentsWithMarks}?${params}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Prefill subject marks fields at load-time, especially for CTEVT
                const fullMarksInput = document.getElementById('subjectFullMarks');
                const passingMarksInput = document.getElementById('subjectPassingMarks');
                if (fullMarksInput) {
                    fullMarksInput.value = data.subject_full_marks ?? {{ $exam->full_marks }};
                }
                if (passingMarksInput) {
                    passingMarksInput.value = data.subject_passing_marks ?? {{ $exam->passing_marks }};
                }

                renderStudentMarksTable(
                    data.students || [], 
                    data.existing_marks || {}, 
                    data.subject_full_marks ?? {{ $exam->full_marks }},
                    data.subject_passing_marks ?? {{ $exam->passing_marks }}
                );

                // If subject is selected, pull component-level defaults from subject mark endpoint immediately
                const subjectId = document.getElementById('modalSubjectFilter')?.value || '';
                if (subjectId) {
                    updateSubjectSpecificMarks();
                }
            } else {
                console.error('Backend returned error:', data.message);
                tbody.innerHTML = `<tr><td colspan="${STUDENT_TABLE_COLSPAN}" class="px-3 py-4 text-center text-red-500">Error: ${data.message || 'Failed to load students'}</td></tr>`;
            }
        })
        .catch(err => {
            console.error('Error loading students:', err);
            tbody.innerHTML = `<tr><td colspan="${STUDENT_TABLE_COLSPAN}" class="px-3 py-4 text-center text-red-500">Error loading students. Check console for details.</td></tr>`;
        });
}

function renderStudentMarksTable(students, existingMarks = {}, subjectFullMarks = null, subjectPassingMarks = null) {
    const tbody = document.getElementById('studentsMarksBody');

    if (!students || students.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${STUDENT_TABLE_COLSPAN}" class="px-3 py-4 text-center text-gray-500">No students found</td></tr>`;
        return;
    }

    const fullMarks = subjectFullMarks || DEFAULT_EXAM_FULL_MARKS;
    const passingMarks = subjectPassingMarks || DEFAULT_EXAM_PASSING_MARKS;
    const subjectId = document.getElementById('modalSubjectFilter')?.value || '';

    window.currentSubjectFullMarks = fullMarks;
    window.currentSubjectPassingMarks = passingMarks;

    const fullMarksInput = document.getElementById('subjectFullMarks');
    const passingMarksInput = document.getElementById('subjectPassingMarks');
    if (fullMarksInput) {
        fullMarksInput.value = fullMarks;
    }
    if (passingMarksInput) {
        passingMarksInput.value = passingMarks;
    }

    const html = students.map(student => {
        const studentId = student.id;
        const existingMark = existingMarks[studentId] || {};
        const rowFullMarks = (existingMark.full_marks > 0 ? existingMark.full_marks : fullMarks);
        const rowPassingMarks = (existingMark.passing_marks > 0 ? existingMark.passing_marks : passingMarks);
        if (EXAM_CATEGORY === 'ctevt') {
            return buildCtevtRow(student, existingMark, subjectId, rowFullMarks, rowPassingMarks);
        }
        return buildAssessmentRow(student, existingMark, subjectId, rowFullMarks, rowPassingMarks);
    }).join('');

    tbody.innerHTML = html;
    updateAllStudentRowStats();
}

function loadSubjectsForMarkUpload() {
    // Delegate to onModalSemesterChange which handles modal subject loading
    onModalSemesterChange();
}

function resetUploadForm() {
    const semSelect = document.getElementById('modalSemesterFilter');
    const subSelect = document.getElementById('modalSubjectFilter');
    if (semSelect) semSelect.innerHTML = '<option value="">Select Semester</option>';
    if (subSelect) subSelect.innerHTML = '<option value="">Select Subject</option>';
    document.getElementById('studentsMarksBody').innerHTML = `<tr><td colspan="${STUDENT_TABLE_COLSPAN}" class="px-3 py-4 text-center text-gray-500">Select Semester, then optionally select Subject to filter attendance by subject, then click "Load Students"</td></tr>`;
    document.querySelectorAll('#componentMarksSection .subject-component-input').forEach(input => input.value = '');
    const fullMarksInput = document.getElementById('subjectFullMarks');
    const passingMarksInput = document.getElementById('subjectPassingMarks');
    if (fullMarksInput) fullMarksInput.value = DEFAULT_EXAM_FULL_MARKS;
    if (passingMarksInput) passingMarksInput.value = DEFAULT_EXAM_PASSING_MARKS;
    refreshSubjectComponentPanels();
    window.currentSubjectFullMarks = DEFAULT_EXAM_FULL_MARKS;
    window.currentSubjectPassingMarks = DEFAULT_EXAM_PASSING_MARKS;
}

// ============= Mark Operations =============

function deleteMark(id) {
    if (!confirm('Are you sure you want to delete this mark?')) return;
    
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    fetch("{{ route('teacher.exams.mark.delete', ':id') }}".replace(':id', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`mark-row-${id}`);
            if (row) {
                row.remove();
            }
            showSuccessMessage('Mark deleted successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showErrorMessage(data.message || 'Error deleting mark');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showErrorMessage('Error deleting mark');
    });
}

/**
 * Edit Mark Modal functions
 */
function openEditMarkModal(id) {
    // Use teacher route for editing marks
    const url = `/teacher/exams/marks/${id}/edit`;
    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
            const ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) {
                throw new Error('Expected JSON response, got ' + ct);
            }
            return res.json();
        })
        .then(data => {
            if (data.success && data.mark) {
                document.getElementById('editMarkId').value = data.mark.id;
                document.getElementById('editMarkStudent').textContent = data.mark.student_name || 'N/A';
                document.getElementById('editMarkRoll').textContent = data.mark.roll_no || '-';
                document.getElementById('editMarksObtained').value = data.mark.marks_obtained ?? '';
                document.getElementById('editMarkRemarks').value = data.mark.remarks ?? '';
                // Show modal robustly (remove hidden, ensure display, focus input)
                const modal = document.getElementById('editMarkModal');
                if (!modal) {
                    console.error('editMarkModal element not found');
                } else {
                    modal.classList.remove('hidden');
                    modal.removeAttribute('hidden');
                    try {
                        // Move modal to document.body to avoid parent CSS collapsing it
                        if (modal.parentNode !== document.body) {
                            document.body.appendChild(modal);
                        }

                        // Force fullscreen fixed positioning so it can't collapse
                        modal.style.position = 'fixed';
                        modal.style.top = '0';
                        modal.style.left = '0';
                        modal.style.width = '100%';
                        modal.style.height = '100%';
                        modal.style.display = 'flex';
                        modal.style.justifyContent = 'center';
                        modal.style.alignItems = 'flex-start';
                        modal.style.zIndex = '99999';

                        // Ensure inner dialog is visible and scrolled into view
                        setTimeout(() => {
                            try {
                                const input = document.getElementById('editMarksObtained');
                                if (input) {
                                    input.focus();
                                    input.scrollIntoView({ block: 'center', inline: 'nearest' });
                                }
                            } catch (err) {
                                console.error('Error during modal post-show actions:', err);
                            }
                        }, 60);
                    } catch (err) {
                        console.error('Error forcing modal display:', err);
                    }
                }
            } else {
                showErrorMessage(data.message || 'Failed to load mark data');
            }
        })
        .catch(err => {
            console.error('Error loading mark data:', err);
            showErrorMessage('Failed to load mark data');
        });
}

function closeEditMarkModal() {
    const modal = document.getElementById('editMarkModal');
    if (!modal) return;
    try {
        // Add hidden class and clear any inline styles we set when opening
        modal.classList.add('hidden');
        modal.setAttribute('hidden', 'true');
        // Ensure inline display/positioning do not keep it visible
        modal.style.display = 'none';
        modal.style.position = '';
        modal.style.top = '';
        modal.style.left = '';
        modal.style.width = '';
        modal.style.height = '';
        modal.style.justifyContent = '';
        modal.style.alignItems = '';
        modal.style.zIndex = '';
    } catch (err) {
        console.error('Error closing edit modal:', err);
    }
}

async function submitEditMarkForm(e) {
    e.preventDefault();
    const id = document.getElementById('editMarkId').value;
    if (!id) return showErrorMessage('Invalid mark id');

    // Update endpoint for mark
    const url = `/teacher/exams/marks/${id}`;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const marksObtained = parseFloat(document.getElementById('editMarksObtained').value) || 0;
    const remarks = document.getElementById('editMarkRemarks').value || '';

    try {
        const res = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ marks_obtained: marksObtained, remarks })
        });
        if (!res.ok) {
            const text = await res.text();
            throw new Error('Server error: ' + res.status + ' - ' + text);
        }
        const ct = res.headers.get('content-type') || '';
        if (ct.indexOf('application/json') === -1) {
            const text = await res.text();
            throw new Error('Expected JSON response, got: ' + text);
        }
        const data = await res.json();
        if (data.success && data.mark) {
            showSuccessMessage(data.message || 'Mark updated');
            // Update row in table if present
            const row = document.getElementById(`mark-row-${id}`);
            if (row) {
                // Obtained (8th column)
                const obtainedCell = row.querySelector('td:nth-child(8)');
                if (obtainedCell) {
                    obtainedCell.innerHTML = `<span class="font-semibold ${data.mark.is_passed ? 'text-green-600' : 'text-blue-600'}">${data.mark.marks_obtained}</span>`;
                }
                // Percentage (9th column)
                const percCell = row.querySelector('td:nth-child(9)');
                if (percCell) {
                    percCell.textContent = (data.mark.percentage !== undefined ? Number(data.mark.percentage).toFixed(2) : data.mark.percentage) + '%';
                }
                // Status (11th column)
                const statusCell = row.querySelector('td:nth-child(11)');
                if (statusCell) {
                    statusCell.innerHTML = data.mark.is_passed ? '<span class="text-green-600 text-xs"><i class="bi bi-check-circle"></i> Passed</span>' : '<span class="text-red-600 text-xs"><i class="bi bi-x-circle"></i> Failed</span>';
                }
            }
            closeEditMarkModal();
        } else {
            showErrorMessage(data.message || 'Failed to update mark');
        }
    } catch (err) {
        console.error('Error updating mark:', err);
        showErrorMessage('Error updating mark');
    }
}

// ============= Filter Functions =============

function applyMarksFilters() {
    try {
    const searchTerm = document.getElementById('searchStudent')?.value?.toLowerCase() || '';
    const year = document.getElementById('filterYear')?.value || '';
    const semester = document.getElementById('filterSemester')?.value || '';
    const subject = document.getElementById('filterSubject')?.value || '';
    
    const rows = document.querySelectorAll('.mark-row');
    
    rows.forEach(row => {
        const studentName = row.querySelector('td:nth-child(1)')?.textContent?.toLowerCase() || '';
        const rollNo = row.querySelector('td:nth-child(2)')?.textContent?.toLowerCase() || '';
        const rowYear = row.querySelector('td:nth-child(4)')?.textContent?.trim() || '';
        const rowSemester = row.querySelector('td:nth-child(3)')?.textContent?.trim() || '';
        
        let matches = true;
        
        // Search filter
        if (searchTerm && !studentName.includes(searchTerm) && !rollNo.includes(searchTerm)) {
            matches = false;
        }
        
        // Year filter (compare against BS year stored in data-academic-year-bs or the displayed row text)
        const rowYearData = row.dataset.academicYearBs || rowYear;
        if (year && !String(rowYearData).includes(String(year))) {
            matches = false;
        }

        // Semester filter - convert textual semester values like 'first' to numeric where needed
        if (semester && semester !== '') {
            // map textual to numeric
            const semMap = { first: '1', second: '2', third: '3', fourth: '4', fifth: '5', sixth: '6' };
            const wantedSem = semMap[semester] || semester;
            const rowSem = row.dataset.studentSemester || rowSemester;
            if (wantedSem && String(rowSem) !== String(wantedSem)) {
                matches = false;
            }
        }

        // Subject filter - match by subject id when possible, otherwise match by name
        if (subject && subject !== '' && subject !== 'all') {
            const rowSubjectId = row.dataset.subjectId || '';
            if (rowSubjectId) {
                if (String(rowSubjectId) !== String(subject)) matches = false;
            } else {
                // fallback: compare by displayed subject name
                const rowSubjectName = row.querySelector('td:nth-child(10)')?.textContent?.trim().toLowerCase() || '';
                // not able to determine subject id, so skip if filter value is not 'all'
                // (unless the filter value is numeric id which won't match name)
                if (!rowSubjectName.includes(String(subject).toLowerCase())) {
                    // Allow pass-through if subject filter isn't a name
                }
            }
        }
        
        row.style.display = matches ? '' : 'none';
    });
    } catch (err) {
        console.error('Error applying filters:', err);
    }
}

function resetMarksFilters() {
    // Clear search box
    const searchEl = document.getElementById('searchStudent');
    if (searchEl) searchEl.value = '';

    // Reset page-level semester filter if present
    const semEl = document.getElementById('filterSemester');
    if (semEl) {
        // If the exam fixed the semester, there may be a hidden input instead of a select
        if (semEl.tagName.toLowerCase() === 'select') {
            semEl.value = '';
        }
    }

    // Reset page-level subject filter if present
    const subjEl = document.getElementById('filterSubject');
    if (subjEl) {
        if (subjEl.tagName.toLowerCase() === 'select') subjEl.value = 'all';
    }

    // Reset mark filter tab and show all rows
    const markFilterEl = document.getElementById('markFilter');
    if (markFilterEl) markFilterEl.value = 'all';

    const rows = document.querySelectorAll('.mark-row');
    rows.forEach(row => row.style.display = '');

    // Re-populate subjects if needed
    if (typeof loadSubjectsForMarkUploadAndTable === 'function') loadSubjectsForMarkUploadAndTable();
}

function filterMarks(filter) {
    const rows = document.querySelectorAll('.mark-row');
    document.getElementById('markFilter').value = filter;
    
    rows.forEach(row => {
        const isPassed = row.dataset.passed === 'true';
        let show = false;
        
        if (filter === 'all') {
            show = true;
        } else if (filter === 'passed') {
            show = isPassed;
        } else if (filter === 'failed') {
            show = !isPassed;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

// ============= UI Helper Functions =============

function showSuccessMessage(msg) {
    const el = document.createElement('div');
    el.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

function showErrorMessage(msg) {
    const el = document.createElement('div');
    el.className = 'fixed bottom-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50';
    el.textContent = 'Error: ' + msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

function loadSubjectsForMarkUploadAndTable() {
    // Load subjects for the page-level filter (not the modal)
    const semester = document.getElementById('filterSemester')?.value || '';
    const subjectSelect = document.getElementById('filterSubject');
    if (!subjectSelect || !semester) {
        if (subjectSelect) subjectSelect.innerHTML = '<option value="all">Subject</option>';
        return;
    }
    subjectSelect.innerHTML = '<option value="">Loading...</option>';
    fetch(`${ROUTES.subjectsBySemester}?semester=${encodeURIComponent(semester)}`)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="all">Subject</option>';
            if (data.success && data.subjects && Array.isArray(data.subjects)) {
                data.subjects.forEach(subject => {
                    html += `<option value="${subject.id}">${subject.subject_name}${subject.subject_code ? ' - ' + subject.subject_code : ''}</option>`;
                });
            }
            subjectSelect.innerHTML = html;
        })
        .catch(() => {
            subjectSelect.innerHTML = '<option value="all">Subject</option>';
        });
}

// ============= Event Listeners =============

document.addEventListener('DOMContentLoaded', () => {
    // Search on Enter key
    document.getElementById('searchStudent')?.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') applyMarksFilters();
    });
    
    // Close modals on background click
    document.getElementById('markUploadModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'markUploadModal') closeMarkUploadModal();
    });
    
    document.getElementById('viewMarksModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'viewMarksModal') closeViewMarksModal();
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMarkUploadModal();
            closeViewMarksModal();
        }
    });
    
    // Handle mark upload form
    document.getElementById('markUploadForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
    try {
        const formData = new FormData(form);
        const marksArray = [];
        const numericFields = new Set([
            'marks_obtained',
            'full_marks',
            'passing_marks',
            'theory_internal_marks',
            'theory_external_marks',
            'practical_internal_marks',
            'practical_external_marks',
            'theory_internal_full_marks',
            'theory_external_full_marks',
            'practical_internal_full_marks',
            'practical_external_full_marks',
            'theory_internal_pass_marks',
            'theory_external_pass_marks',
            'practical_internal_pass_marks',
            'practical_external_pass_marks'
        ]);

        for (const [key, value] of formData.entries()) {
            if (!key.startsWith('marks[') || !key.includes('][')) {
                continue;
            }

            const match = key.match(/marks\[(\d+)\]\[(\w+)\]/);
            if (!match) {
                continue;
            }

            const studentId = parseInt(match[1], 10);
            const fieldName = match[2];
            let existingEntry = marksArray.find(mark => mark.student_id === studentId);

            if (!existingEntry) {
                existingEntry = { student_id: studentId };
                marksArray.push(existingEntry);
            }

            if (fieldName === 'student_id' || fieldName === 'subject_id') {
                if (value !== '') {
                    existingEntry[fieldName] = parseInt(value, 10);
                }
                continue;
            }

            if (numericFields.has(fieldName)) {
                if (value !== '') {
                    existingEntry[fieldName] = parseFloat(value);
                }
                continue;
            }

            if (value !== '') {
                existingEntry[fieldName] = value;
            }
        }

        const subjectId = document.getElementById('modalSubjectFilter')?.value;
        const subjectFullMarks = document.getElementById('subjectFullMarks')?.value;
        const subjectPassingMarks = document.getElementById('subjectPassingMarks')?.value;
        const ctevtComponentDefaults = {};

        if (EXAM_CATEGORY === 'ctevt') {
            document.querySelectorAll('#componentMarksSection [data-component-category="ctevt"]').forEach((input) => {
                const component = input.dataset.component;
                const valueType = input.dataset.valueType;
                const parsedValue = parseFloat(input.value || '0');

                if (!component || !valueType || Number.isNaN(parsedValue)) {
                    return;
                }

                const suffix = valueType === 'max' ? 'full_marks' : 'pass_marks';
                ctevtComponentDefaults[`${component}_${suffix}`] = parsedValue;
            });
        }

        const validMarks = marksArray.filter((mark) => {
            if (EXAM_CATEGORY === 'ctevt') {
                return [
                    'theory_internal_marks',
                    'theory_external_marks',
                    'practical_internal_marks',
                    'practical_external_marks',
                    'marks_obtained',
                ].some((field) => mark[field] !== undefined && !Number.isNaN(mark[field]));
            }

            return mark.marks_obtained !== undefined && !Number.isNaN(mark.marks_obtained);
        });

        if (validMarks.length === 0) {
            showErrorMessage('Please enter marks for at least one student');
            return;
        }

        validMarks.forEach(mark => {
            if (subjectId) {
                mark.subject_id = parseInt(subjectId);
            }
                if (subjectFullMarks) {
                    mark.full_marks = parseFloat(subjectFullMarks);
                }
            if (subjectPassingMarks) {
                mark.passing_marks = parseFloat(subjectPassingMarks);
            }

            if (EXAM_CATEGORY === 'ctevt') {
                Object.assign(mark, ctevtComponentDefaults);
            }
        });

        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    marks: validMarks
                })
            });
            
            const data = await res.json();
            if (data.success) {
                showSuccessMessage('Marks uploaded successfully');
                closeMarkUploadModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrorMessage(data.message || 'Error uploading marks');
            }
        } catch (err) {
            showErrorMessage('Error uploading marks: ' + err.message);
        }
    });
});
document.addEventListener('input', function(e) {
    if (e.target.matches('.student-component-input') || e.target.matches('.marks-obtained-input')) {
        const row = e.target.closest('tr');
        if (row) updateStudentRowStats(row);
    }
    if (e.target.matches('.subject-component-input')) {
        updateSubjectAggregateFields();
    }
});
</script>
@endsection
