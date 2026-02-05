@extends('admin.layouts.app')

@section('title', 'Exam Details - ' . $exam->exam_name)

@section('content')
<div class="space-y-4">
    <!-- Back Button & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.assessment') }}" class="text-gray-600 hover:text-gray-900">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <h2 class="text-lg font-semibold text-gray-900">{{ $exam->localized_name }}</h2>
        </div>
        <div class="flex gap-2">
            <button onclick="openEditExamModal()" class="px-3 py-1.5 text-xs bg-yellow-500 text-white rounded hover:bg-yellow-600">
                <i class="bi bi-pencil text-xs mr-1"></i>Edit Exam
            </button>
            <button onclick="openMarkUploadModal()" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="bi bi-upload text-xs mr-1"></i>Upload Marks
            </button>
        </div>
    </div>

    <!-- Exam Details Card -->
    <x-card>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-500">Academic Year</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->academic_year }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Semester</p>
                <p class="text-sm font-semibold text-gray-900">{{ ucwords($exam->semester) }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Course</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->course ? $exam->course->subject_name : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Subject</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->subject ? $exam->subject->subject_name : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Exam Type</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->formatted_type }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Full Marks</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->full_marks }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Passing Marks</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->passing_marks }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Status</p>
                <span class="inline-block px-2 py-0.5 {{ $exam->status_badge_class }} rounded text-xs font-medium">
                    {{ $exam->formatted_status }}
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Exam Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->exam_date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Total Students</p>
                <p class="text-sm font-semibold text-gray-900">{{ $exam->marks->count() }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Average Score</p>
                <p class="text-sm font-semibold text-gray-900">{{ number_format($averageMarks, 2) }}%</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500">Pass Rate</p>
                <p class="text-sm font-semibold text-green-600">{{ $passRate }}%</p>
            </div>
        </div>
        @if($exam->description)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-xs font-medium text-gray-500">Description</p>
            <p class="text-sm text-gray-700">{{ $exam->localized_description }}</p>
        </div>
        @endif
    </x-card>

    <!-- Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stats-card title="Total Students" :value="$totalStudents" icon="bi bi-people-fill" color="blue" />
        <x-stats-card title="Avg Marks" :value="number_format($averageMarks, 2) . '%'" icon="bi bi-percent" color="green" />
        <x-stats-card title="Passed (≥40%)" :value="$exam->marks()->where('percentage', '>=', 40)->count()" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="Failed (<40%)" :value="$exam->marks()->where('percentage', '<', 40)->count()" icon="bi bi-x-circle" color="red" />
    </div>

    <!-- Marks Table -->
    <div class="bg-white rounded shadow-sm border border-gray-200">
        <div class="p-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Student Marks</h3>
            <div class="flex gap-2">
                <button onclick="exportMarks()" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    <i class="bi bi-download mr-1"></i>Export
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <x-table>
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">ID</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Student Name</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Roll No</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Attendance</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Full Marks</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Pass Marks</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Obtained</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Percentage</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Grade</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Status</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exam->marks as $mark)
                    <tr class="border-b border-gray-200 hover:bg-gray-50" id="mark-row-{{ $mark->id }}">
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $mark->student->id ?? '-' }}</td>
                        <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ $mark->student->user->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 text-xs text-gray-700">{{ $mark->student->roll_no ?? '-' }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">Present</span>
                        </td>
                        <td class="px-3 py-2 text-center text-xs text-gray-700">{{ $exam->full_marks }}</td>
                        <td class="px-3 py-2 text-center text-xs text-gray-700">{{ $exam->passing_marks }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="font-semibold {{ $mark->percentage >= 40 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $mark->marks_obtained }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-xs text-gray-700">{{ number_format($mark->percentage, 2) }}%</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $mark->percentage >= 40 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $mark->grade }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($mark->percentage >= 40)
                                <span class="text-green-600 text-xs"><i class="bi bi-check-circle"></i> Passed</span>
                            @else
                                <span class="text-red-600 text-xs"><i class="bi bi-x-circle"></i> Failed</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button onclick="openEditMarkModal({{ $mark->id }})" class="text-blue-600 hover:text-blue-800 text-xs" title="Edit Mark">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-3 py-4 text-center text-gray-500 text-xs">
                            No marks have been uploaded yet. Click "Upload Marks" to add marks.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>
    </div>
</div>

<!-- Edit Exam Modal -->
<div id="editExamModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeEditExamModal()"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto mx-auto mt-20">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Exam</h3>
            <button onclick="closeEditExamModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editExamForm" method="POST" action="{{ route('admin.assessment.update', $exam->id) }}" class="px-5 py-4 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
                    <input name="exam_name" type="text" value="{{ $exam->exam_name }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name (Nepali)</label>
                    <input name="exam_name_ne" type="text" value="{{ $exam->exam_name_ne }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year *</label>
                    <select name="academic_year" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        @foreach($exam->getAcademicYears() as $year)
                            <option value="{{ $year }}" {{ $exam->academic_year == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                    <select name="semester" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        @foreach(['first', 'second', 'third', 'fourth', 'fifth', 'sixth'] as $s)
                            <option value="{{ $s }}" {{ $exam->semester == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                        <option value="">Select Subject</option>
                        @foreach(\App\Models\Subject::all() as $subject)
                            <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Type *</label>
                    <select name="exam_type" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        <option value="internal" {{ $exam->exam_type == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="final" {{ $exam->exam_type == 'final' ? 'selected' : '' }}>Final</option>
                        <option value="midterm" {{ $exam->exam_type == 'midterm' ? 'selected' : '' }}>Midterm</option>
                        <option value="practical" {{ $exam->exam_type == 'practical' ? 'selected' : '' }}>Practical</option>
                        <option value="viva" {{ $exam->exam_type == 'viva' ? 'selected' : '' }}>Viva</option>
                        <option value="assignment" {{ $exam->exam_type == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="assessment" {{ $exam->exam_type == 'assessment' ? 'selected' : '' }}>Assessment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                    <input name="full_marks" type="number" value="{{ $exam->full_marks }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                    <input name="passing_marks" type="number" value="{{ $exam->passing_marks }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (AD) *</label>
                    <input name="exam_date" type="date" value="{{ $exam->exam_date->format('Y-m-d') }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs" required>
                        <option value="draft" {{ $exam->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $exam->status == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ $exam->status == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs h-20">{{ $exam->description }}</textarea>
            </div>
            <div id="editExamErrors" class="text-sm text-red-600"></div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditExamModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Update Exam</button>
            </div>
        </form>
    </div>
</div>

<!-- Mark Upload Modal -->
<div id="markUploadModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeMarkUploadModal()"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto mx-auto mt-20">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Upload Marks - {{ $exam->localized_name }}</h3>
            <button onclick="closeMarkUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="markUploadForm" method="POST" action="{{ route('admin.assessment.upload-marks', $exam->id) }}" class="px-5 py-4 space-y-4">
            @csrf
            <!-- Exam Info -->
            <div class="bg-gray-50 p-3 rounded mb-4">
                <div class="grid grid-cols-4 gap-2 text-xs">
                    <div><span class="text-gray-500">Exam:</span> <span class="font-medium">{{ $exam->localized_name }}</span></div>
                    <div><span class="text-gray-500">Full Marks:</span> <input type="number" name="full_marks" value="{{ $exam->full_marks }}" class="w-16 px-1 border rounded text-xs"></div>
                    <div><span class="text-gray-500">Pass Marks:</span> <input type="number" name="passing_marks" value="{{ $exam->passing_marks }}" class="w-16 px-1 border rounded text-xs"></div>
                    <div><span class="text-gray-500">Subject:</span> <span class="font-medium">{{ $exam->subject ? $exam->subject->subject_name : 'N/A' }}</span></div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Batch</label>
                    <select id="filterBatch" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                        <option value="">All Batches</option>
                        @foreach(range(date('Y')-5, date('Y')) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                    <select id="filterSemester" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                        <option value="">All Semesters</option>
                        @foreach(['first', 'second', 'third', 'fourth', 'fifth', 'sixth'] as $s)
                            <option value="{{ $s }}" {{ $exam->semester == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select id="filterSubject" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                        <option value="">All Subjects</option>
                        @foreach(\App\Models\Subject::all() as $subject)
                            <option value="{{ $subject->id }}" {{ $exam->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Student Marks Table -->
            <div class="overflow-x-auto border border-gray-200 rounded">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700 border-b">ID</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-700 border-b">Name</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Roll No</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Attendance</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Subject Course</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Full Marks</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Pass Marks</th>
                            <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Obtained Marks</th>
                        </tr>
                    </thead>
                    <tbody id="studentsMarksBody">
                        <!-- Dynamic student rows will be loaded here -->
                        <tr>
                            <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                                Select filters and click "Load Students" to fetch students
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-between items-center mt-4">
                <button type="button" onclick="loadStudents()" class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    <i class="bi bi-people mr-1"></i>Load Students
                </button>
                <div class="flex gap-2">
                    <button type="button" onclick="closeMarkUploadModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Save Marks</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Mark Modal -->
<div id="editMarkModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeEditMarkModal()"></div>
    <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto mx-auto mt-20">
        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Edit Mark</h3>
            <button onclick="closeEditMarkModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editMarkForm" class="px-5 py-4 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editMarkId" name="mark_id">
            
            <!-- Student Info (Read-only) -->
            <div class="bg-gray-50 p-3 rounded">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-500">Student:</span>
                        <span id="editStudentName" class="font-medium text-gray-900 ml-1">-</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Roll No:</span>
                        <span id="editRollNo" class="font-medium text-gray-900 ml-1">-</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Full Marks:</span>
                        <span id="editFullMarks" class="font-medium text-gray-900 ml-1">-</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Pass Marks:</span>
                        <span id="editPassMarks" class="font-medium text-gray-900 ml-1">-</span>
                    </div>
                </div>
            </div>

            <!-- Current Marks Info -->
            <div class="grid grid-cols-3 gap-2">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Current</p>
                    <p id="editCurrentMarks" class="text-lg font-bold text-gray-900">-</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Percentage</p>
                    <p id="editCurrentPercentage" class="text-lg font-bold text-gray-900">-</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Grade</p>
                    <p id="editCurrentGrade" class="text-lg font-bold text-gray-900">-</p>
                </div>
            </div>

            <!-- Edit Fields -->
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Obtained Marks *</label>
                <input type="number" id="editMarksObtained" name="marks_obtained" min="0" class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p class="text-xs text-gray-500 mt-1">Maximum: <span id="editMaxMarks">-</span> marks</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                <textarea id="editRemarks" name="remarks" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional remarks..."></textarea>
            </div>

            <div id="editMarkErrors" class="text-sm text-red-600 hidden"></div>
            
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditMarkModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                    <i class="bi bi-check-lg mr-1"></i>Update Mark
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Edit Exam Modal Functions
function openEditExamModal() {
    document.getElementById('editExamModal').classList.remove('hidden');
}

function closeEditExamModal() {
    document.getElementById('editExamModal').classList.add('hidden');
}

// Mark Upload Modal Functions
function openMarkUploadModal() {
    document.getElementById('markUploadModal').classList.remove('hidden');
}

function closeMarkUploadModal() {
    document.getElementById('markUploadModal').classList.add('hidden');
}

// Edit Mark Modal Functions
function openEditMarkModal(markId) {
    // Fetch mark data
    fetch(`/admin/assessment/marks/${markId}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const mark = data.mark;
            
            // Populate form fields
            document.getElementById('editMarkId').value = mark.id;
            document.getElementById('editStudentName').textContent = mark.student_name;
            document.getElementById('editRollNo').textContent = mark.roll_no;
            document.getElementById('editFullMarks').textContent = mark.full_marks;
            document.getElementById('editPassMarks').textContent = mark.passing_marks;
            document.getElementById('editCurrentMarks').textContent = mark.marks_obtained;
            document.getElementById('editCurrentPercentage').textContent = mark.percentage + '%';
            document.getElementById('editCurrentGrade').textContent = mark.grade;
            document.getElementById('editMarksObtained').value = mark.marks_obtained;
            document.getElementById('editMarksObtained').max = mark.full_marks;
            document.getElementById('editMaxMarks').textContent = mark.full_marks;
            document.getElementById('editRemarks').value = mark.remarks || '';
            
            // Clear errors
            document.getElementById('editMarkErrors').classList.add('hidden');
            document.getElementById('editMarkErrors').textContent = '';
            
            // Show modal
            document.getElementById('editMarkModal').classList.remove('hidden');
        } else {
            alert('Failed to load mark data: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error fetching mark data:', error);
        alert('Error loading mark data. Please try again.');
    });
}

function closeEditMarkModal() {
    document.getElementById('editMarkModal').classList.add('hidden');
}

// Load students for mark upload
async function loadStudents() {
    const batch = document.getElementById('filterBatch').value;
    const semester = document.getElementById('filterSemester').value;
    const subject = document.getElementById('filterSubject').value;
    const examId = {{ $exam->id }};
    
    try {
        const url = `/admin/assessment/${examId}/students?batch=${batch}&semester=${semester}&subject_id=${subject}`;
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        
        if (data.success) {
            renderStudentMarks(data.students, data.existing_marks, data.full_marks, data.passing_marks);
        }
    } catch (error) {
        console.error('Error loading students:', error);
    }
}

function renderStudentMarks(students, existingMarks, fullMarks, passingMarks) {
    const tbody = document.getElementById('studentsMarksBody');
    
    if (students.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-3 py-4 text-center text-gray-500">No students found</td></tr>';
        return;
    }
    
    let html = '';
    const examSubjectName = '{{ $exam->subject ? addslashes($exam->subject->subject_name) : "N/A" }}';
    students.forEach(student => {
        const existingMark = existingMarks[student.id] !== undefined ? existingMarks[student.id] : '';
        // Get student's enrolled subjects (comma-separated list)
        let studentSubjects = student.subjects && student.subjects.length > 0 
            ? student.subjects.map(s => s.subject_name).join(', ')
            : '-';
        html += `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-3 py-2 text-gray-700">${student.id}</td>
                <td class="px-3 py-2 font-medium text-gray-900">${student.user ? student.user.name : 'N/A'}</td>
                <td class="px-3 py-2 text-center text-gray-700">${student.roll_no || '-'}</td>
                <td class="px-3 py-2 text-center">
                    <span class="inline-block px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">Present</span>
                </td>
                <td class="px-3 py-2 text-center text-gray-700">${examSubjectName}</td>
                <td class="px-3 py-2 text-center">
                    <input type="hidden" name="marks[${student.id}][student_id]" value="${student.id}">
                    <input type="number" name="marks[${student.id}][full_marks]" value="${fullMarks}" class="w-16 px-2 py-1 border border-gray-300 rounded text-xs text-center" readonly>
                </td>
                <td class="px-3 py-2 text-center">
                    <input type="number" name="marks[${student.id}][passing_marks]" value="${passingMarks}" class="w-16 px-2 py-1 border border-gray-300 rounded text-xs text-center" readonly>
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="marks[${student.id}][marks_obtained]" value="${existingMark}" min="0" max="${fullMarks}" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function exportMarks() {
    alert('Export functionality would be implemented here');
}

// Close modals on outside click
document.getElementById('editExamModal').addEventListener('click', function(e) {
    if (e.target.id === 'editExamModal' || e.target.closest('.fixed.inset-0.bg-black')) {
        closeEditExamModal();
    }
});

document.getElementById('markUploadModal').addEventListener('click', function(e) {
    if (e.target.id === 'markUploadModal' || e.target.closest('.fixed.inset-0.bg-black')) {
        closeMarkUploadModal();
    }
});

// Also allow ESC key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditExamModal();
        closeMarkUploadModal();
        closeEditMarkModal();
    }
});

// Handle Edit Mark Form submission
document.getElementById('editMarkForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const markId = document.getElementById('editMarkId').value;
    const marksObtained = document.getElementById('editMarksObtained').value;
    const remarks = document.getElementById('editRemarks').value;
    const errorsDiv = document.getElementById('editMarkErrors');
    
    const formData = {
        _token: csrfToken,
        _method: 'PUT',
        marks_obtained: marksObtained,
        remarks: remarks
    };
    
    try {
        const res = await fetch(`/admin/assessment/marks/${markId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if (data.success) {
            closeEditMarkModal();
            
            // Update the row in the table
            const row = document.getElementById(`mark-row-${data.mark.id}`);
            if (row) {
                // Update obtained marks with new color based on 40% threshold
                const obtainedCell = row.querySelector('td:nth-child(7) span');
                if (obtainedCell) {
                    obtainedCell.textContent = data.mark.marks_obtained;
                    obtainedCell.className = `font-semibold ${data.mark.is_passed ? 'text-green-600' : 'text-red-600'}`;
                }
                
                // Update percentage
                const percentageCell = row.querySelector('td:nth-child(8)');
                if (percentageCell) {
                    percentageCell.textContent = data.mark.percentage.toFixed(2) + '%';
                }
                
                // Update grade
                const gradeCell = row.querySelector('td:nth-child(9) span');
                if (gradeCell) {
                    gradeCell.textContent = data.mark.grade;
                    gradeCell.className = `inline-block px-2 py-0.5 rounded text-xs font-medium ${data.mark.is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
                }
                
                // Update status
                const statusCell = row.querySelector('td:nth-child(10)');
                if (statusCell) {
                    statusCell.innerHTML = data.mark.status_text;
                }
            }
            
            // Show success message
            alert(data.message);
            
            // Reload page to update statistics
            // setTimeout(() => window.location.reload(), 1000);
        } else {
            errorsDiv.textContent = data.message || 'Error updating mark';
            errorsDiv.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error updating mark:', error);
        errorsDiv.textContent = 'An error occurred. Please try again.';
        errorsDiv.classList.remove('hidden');
    }
});

// Close edit mark modal on outside click
document.getElementById('editMarkModal').addEventListener('click', function(e) {
    if (e.target.id === 'editMarkModal' || e.target.closest('.fixed.inset-0.bg-black')) {
        closeEditMarkModal();
    }
});

// Handle Edit Exam Form submission
document.getElementById('editExamForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const url = this.action;
    const formData = new FormData(this);
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        if (res.ok) {
            closeEditExamModal();
            window.location.reload();
        } else {
            const data = await res.json();
            document.getElementById('editExamErrors').innerHTML = data.message || 'Error updating exam';
        }
    } catch (error) {
        console.error(error);
    }
});
</script>
@endsection

