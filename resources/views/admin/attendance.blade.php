@extends('admin.layouts.app')

@section('title', 'Attendance')

@section('content')
    <div class="space-y-4">
        
        <!-- Global Loader Overlay -->
        <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm hidden flex items-center justify-center">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
                <p id="loaderText" class="text-gray-600 dark:text-gray-400 font-medium">Loading...</p>
            </div>
        </div>

        <!-- Page Header -->
        @include('admin.components.admin-page-header', [
            'title' => 'Attendance',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Attendance']
            ],
            'addButton' => [
                'label' => 'Mark Attendance',
                'onclick' => 'openMarkAttendanceModal()',
                'color' => 'green'
            ]
        ])

        <!-- Stats Cards -->
        @include('admin.components.admin-stats-cards', [
            'cards' => [
                ['title' => 'Total Records', 'value' => $stats['total'], 'icon' => 'bi-list-check', 'color' => 'red'],
                ['title' => 'Present', 'value' => $stats['present'], 'icon' => 'bi-check-circle', 'color' => 'green'],
                ['title' => 'Absent', 'value' => $stats['absent'], 'icon' => 'bi-x-circle', 'color' => 'red'],
                ['title' => 'Leave', 'value' => $stats['leave'], 'icon' => 'bi-calendar-event', 'color' => 'purple'],
            ]
        ])

        {{-- Filter Card - Using standardized component --}}
@include('admin.components.admin-filter-card', [
    'formAction' => route('admin.attendance'),
    'filters' => [
        ['name' => 'date', 'type' => 'date', 'value' => $date ?? '', 'label' => 'Date (AD)'],
        ['name' => 'date_bs', 'type' => 'text', 'value' => $date_bs ?? '', 'placeholder' => 'YYYY-MM-DD', 'label' => 'Date (BS)', 'class' => 'bs-date', 'icon' => 'bi-calendar3', 'autocomplete' => 'off'],
        ['name' => 'semester', 'type' => 'select', 'options' => array_merge(['' => 'All Semesters'], array_combine($semesters, $semesters)), 'value' => request('semester'), 'label' => 'Semester'],
        ['name' => 'course', 'type' => 'select', 'options' => array_merge(['' => 'All Courses'], $courses->mapWithKeys(function($c) { return [$c->id => $c->subject_code . ' - ' . $c->subject_name]; })->toArray()), 'value' => $course, 'label' => 'Course']
    ],
    'showReset' => true,
    'resetRoute' => route('admin.attendance')
])

        <!-- Attendance by Subject - Grouped View -->
        @php
            // Handle both Paginator and Collection
            $subjectCount =
                is_object($subjectAttendance) && method_exists($subjectAttendance, 'total')
                    ? $subjectAttendance->total()
                    : (is_countable($subjectAttendance)
                        ? count($subjectAttendance)
                        : 0);
        @endphp
        <div class="bg-white rounded shadow-sm border border-gray-200">
            <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">
                    <i class="bi bi-collection mr-1"></i> Attendance by Subject
                </h3>
                <div class="flex gap-2 mb-4">
                    <a href="{{ route('admin.attendance.export', request()->query()) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                        <i class="bi bi-download mr-1"></i> Export CSV
                    </a>
                    <a href="{{ route('admin.attendance.print-list', request()->query()) }}"
                        onclick="adminOpenPrintPreview('{{ route('admin.attendance.print-list', request()->query()) }}', { title: 'Print Attendance' }); return false;"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        <i class="bi bi-printer mr-1"></i> Print
                    </a>
                </div>

            </div>

            <div class="overflow-x-auto">
                @if ($subjectAttendance->count() > 0)
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 text-sm">Subject</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 text-sm">Date</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 text-sm">Total</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 text-sm">Present</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 text-sm">Absent</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 text-sm">Leave</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200 text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach ($subjectAttendance as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition border-b border-gray-100 dark:border-slate-700">
                                    <td class="px-4 py-4 text-sm dark:text-gray-200">
                                        @if ($item['subject_name'] && $item['subject_name'] !== 'General')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="bi bi-book mr-1"></i>
                                                {{ $item['subject_code'] ?? '' }} {{ $item['subject_name'] }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                                <i class="bi bi-people mr-1"></i>General
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center text-gray-700 text-sm dark:text-gray-300">
                                        <div class="flex flex-col">
                                            <span>{{ $item['date'] ?? '-' }}</span>
                                            <span class="text-gray-500 text-xs mt-0.5">{{ $item['date_bs'] ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-semibold text-gray-900 dark:text-gray-100 text-sm">
                                        {{ $item['total'] }}</td>
                                    <td class="px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $item['present'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $item['absent'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">{{ $item['leave'] }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-sm dark:text-gray-300">
                                        <div class="flex items-center justify-center gap-1">
                                            <button
                                                onclick="viewSubjectAttendance('{{ $item['date'] ?? '' }}', '{{ $item['subject_id'] ?? '' }}', '{{ $item['subject_name'] ?? 'General' }}')"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 bg-blue-100 hover:bg-blue-200 rounded transition"
                                                title="View">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button
                                                onclick="openEditSubjectAttendance('{{ $item['date'] ?? '' }}', '{{ $item['date_bs'] ?? '' }}', '{{ $item['subject_id'] ?? '' }}', '{{ $item['subject_name'] ?? 'General' }}')"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs text-yellow-700 bg-yellow-100 hover:bg-yellow-200 rounded transition"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button
                                                onclick="deleteSubjectAttendance('{{ $item['date'] ?? '' }}', '{{ $item['subject_id'] ?? '' }}', '{{ $item['subject_name'] ?? 'General' }}')"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 bg-red-100 hover:bg-red-200 rounded transition"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-3 py-3 border-t border-gray-200">
                        {{ $subjectAttendance->links() }}
                    </div>
                @else
                    <div class="p-8 text-center">
                        <i class="bi bi-inbox text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">No attendance records found.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Hidden data for JavaScript -->
        <div id="subjectAttendanceData" data-subjects='@json($subjectAttendance)'></div>

        <!-- Edit Student Modal -->
        <div id="editStudentModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-sm w-full">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold">Edit Attendance</h2>
                            <p class="text-blue-100 text-xs" id="editStudentName">Student Name</p>
                        </div>
                        <button onclick="closeEditStudentModal()" class="text-blue-200 hover:text-white">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-4">
                    <input type="hidden" id="editRecordId">
                    <input type="hidden" id="editStudentId">

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Current Status</label>
                        <span id="editCurrentStatus"
                            class="inline-block px-3 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">-</span>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-2">New Status</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="editStatus" value="present" class="peer sr-only">
                                <div
                                    class="px-3 py-2 rounded text-xs font-medium text-center border border-gray-200 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-300 hover:bg-gray-50">
                                    <i class="bi bi-check-circle text-xs mr-1"></i> Present
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="editStatus" value="absent" class="peer sr-only">
                                <div
                                    class="px-3 py-2 rounded text-xs font-medium text-center border border-gray-200 peer-checked:bg-red-100 peer-checked:text-blue-700 peer-checked:border-red-300 hover:bg-gray-50">
                                    <i class="bi bi-x-circle text-xs mr-1"></i> Absent
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="editStatus" value="leave" class="peer sr-only">
                                <div
                                    class="px-3 py-2 rounded text-xs font-medium text-center border border-gray-200 peer-checked:bg-purple-100 peer-checked:text-purple-700 peer-checked:border-purple-300 hover:bg-gray-50">
                                    <i class="bi bi-calendar-event text-xs mr-1"></i> Leave
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea id="editRemarks" placeholder="Add remarks..."
                            class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 h-20 resize-none"></textarea>
                    </div>

                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex gap-2">
                        <button type="button" onclick="closeEditStudentModal()"
                            class="flex-1 px-4 py-2 text-gray-700 border border-gray-300 rounded-md text-sm hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="button" onclick="saveStudentAttendance()"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            <i class="bi bi-check mr-1"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mark Attendance Modal -->
        <div id="markAttendanceModal"
            class="fixed hidden inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col">
                <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 rounded-t-lg shadow-md flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-start gap-2">
                            <div class="bg-white bg-opacity-20 rounded-full p-1.5">
                                <i class="bi bi-calendar-check text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold">Mark Attendance</h2>
                                <p class="text-red-100 text-xs">Pick a date and semester, then load students</p>
                            </div>
                        </div>
                        <button onclick="closeMarkAttendanceModal()" aria-label="Close"
                            class="text-red-200 hover:text-white p-2 rounded-full hover:bg-blue-700/25">
                            <i class="bi bi-x-lg text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-3 space-y-3 overflow-y-auto flex-1">
                    <!-- Hidden inputs to store current filter values from backend -->
                    <input type="hidden" id="current_filter_date" value="{{ $date ?? '' }}">
                    <input type="hidden" id="current_filter_date_bs" value="{{ $date_bs ?? '' }}">
                    <input type="hidden" id="current_filter_semester" value="{{ request('semester') ?? '' }}">
                    <input type="hidden" id="current_filter_course" value="{{ $course ?? '' }}">

                    <!-- Main Input Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date (AD) <span
                                    class="text-blue-500">*</span></label>
                            <input type="date" id="mark_date" value="{{ $date ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-gray-400 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Date (BS)</label>
                            <div class="relative">
                                <input type="text" id="mark_date_bs" placeholder="YYYY-MM-DD" autocomplete="off"
                                    class="bs-date w-full pr-10 px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-gray-400 transition-colors">
                                <button type="button" aria-label="Pick BS date" onclick="event?.preventDefault(); event?.stopPropagation(); window.openBsDatePicker?.('mark_date_bs'); return false;" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Semester <span
                                    class="text-blue-500">*</span></label>
                            <select id="mark_semester"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-gray-400 transition-colors">
                                <option value="">Select Semester</option>
                                @if (count($semesters) > 0)
                                    @foreach ($semesters as $sem)
                                        <option value="{{ $sem }}">
                                            {{ $sem }}{{ $sem == 1 ? 'st' : ($sem == 2 ? 'nd' : ($sem == 3 ? 'rd' : 'th')) }}
                                            Semester</option>
                                    @endforeach
                                @else
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                    <option value="3">3rd Semester</option>
                                    <option value="4">4th Semester</option>
                                    <option value="5">5th Semester</option>
                                    <option value="6">6th Semester</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Course <span
                                    class="text-gray-400 font-normal">(Optional)</span></label>
                            <select id="mark_course"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 hover:border-gray-400 transition-colors">
                                <option value="">General Attendance</option>
                                @if (count($courses) > 0)
                                    @foreach ($courses as $c)
                                        <option value="{{ $c->id }}" data-semester="{{ $c->semester }}">
                                            {{ $c->subject_code }} - {{ $c->subject_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" id="loadStudentsBtn" onclick="loadAttendanceStudents()"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors font-medium shadow-md">
                            <i class="bi bi-search"></i>
                            <span>Load</span>
                        </button>
                        <button type="button" onclick="renderAttendanceTable([]);"
                            class="flex-1 sm:flex-initial px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-gray-50 transition-colors font-medium">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    </div>

                    <div id="attendanceSummary" class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-2 hidden">
                        <div
                            class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-md border border-blue-200 shadow-sm text-center">
                            <div class="text-2xl font-bold text-blue-700" id="summary_total">0</div>
                            <div class="text-xs text-blue-600 font-medium mt-0.5">Total</div>
                        </div>
                        <div
                            class="p-3 bg-gradient-to-br from-green-50 to-green-100 rounded-md border border-green-200 shadow-sm text-center">
                            <div class="text-2xl font-bold text-green-700" id="summary_present">0</div>
                            <div class="text-xs text-green-600 font-medium mt-0.5">Present</div>
                        </div>
                        <div
                            class="p-3 bg-gradient-to-br from-red-50 to-red-100 rounded-md border border-red-200 shadow-sm text-center">
                            <div class="text-2xl font-bold text-blue-700" id="summary_absent">0</div>
                            <div class="text-xs text-blue-600 font-medium mt-0.5">Absent</div>
                        </div>
                    </div>

                    <div id="attendanceTableWrap" class="mt-2 hidden">
                        <div class="overflow-x-auto border border-gray-200 rounded-md shadow-sm">
                            <table class="w-full">
                                <thead class="bg-gray-50 text-sm font-semibold text-gray-700 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Student</th>
                                        <th class="px-4 py-3 text-center">Roll No</th>
                                        <th class="px-4 py-3 text-center">Sem</th>
                                        <th class="px-4 py-3 text-center">Date</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTbody" class="divide-y divide-gray-200"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-2 p-3 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 flex justify-end gap-2 rounded-b-md flex-shrink-0">
                    <button type="button" onclick="closeMarkAttendanceModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm hover:bg-white transition-colors font-medium">
                        <i class="bi bi-x-circle mr-1"></i> Cancel
                    </button>
                    <button type="button" id="saveAllBtn" onclick="saveAllAttendance()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium shadow-md"
                        disabled>
                        <i class="bi bi-check-circle"></i>
                        <span>Save</span>
                    </button>
                </div>
            </div>
        </div>

        <div id="toast" class="hidden fixed top-4 right-4 z-50"></div>

        <!-- Edit Subject Attendance Modal -->
        <div id="editSubjectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeEditSubjectModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-[90vh] overflow-hidden">
                <div
                    class="flex justify-between items-center px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-green-600 to-green-700">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Edit Attendance</h3>
                        <p class="text-green-100 text-xs" id="editSubjectTitle">Subject Name - Date</p>
                    </div>
                    <button onclick="closeEditSubjectModal()" class="text-green-200 hover:text-white">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-3 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center gap-4 text-xs">
                        <span class="text-gray-600 font-medium">Mark all as:</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="bulkStatus" value="present" class="sr-only" checked>
                            <div class="px-3 py-1.5 rounded text-xs font-medium border border-green-300 bg-green-100 text-green-700 hover:bg-green-200"
                                onclick="document.querySelector('input[name=bulkStatus][value=present]').checked=true">
                                <i class="bi bi-check-circle text-xs mr-1"></i> Present
                            </div>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="bulkStatus" value="absent" class="sr-only">
                            <div class="px-3 py-1.5 rounded text-xs font-medium border border-red-300 bg-blue-50 text-blue-700 hover:bg-red-100"
                                onclick="document.querySelector('input[name=bulkStatus][value=absent]').checked=true">
                                <i class="bi bi-x-circle text-xs mr-1"></i> Absent
                            </div>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="bulkStatus" value="leave" class="sr-only">
                            <div class="px-3 py-1.5 rounded text-xs font-medium border border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100"
                                onclick="document.querySelector('input[name=bulkStatus][value=leave]').checked=true">
                                <i class="bi bi-calendar-event text-xs mr-1"></i> Leave
                            </div>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="bulkStatus" value="leave" class="sr-only">
                            <div class="px-3 py-1.5 rounded text-xs font-medium border border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100"
                                onclick="document.querySelector('input[name=bulkStatus][value=leave]').checked=true">
                                <i class="bi bi-calendar-event text-xs mr-1"></i> Leave
                            </div>
                        </label>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[50vh] overflow-y-auto">
                    <table class="min-w-full text-xs">
                        <thead
                            class="bg-gray-50 text-sm font-semibold text-gray-700 sticky top-0 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left">Student Name</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-center">Roll No</th>
                                <th class="px-4 py-3 text-center">Current Status</th>
                                <th class="px-4 py-3 text-center">Select Status</th>
                            </tr>
                        </thead>
                        <tbody id="editSubjectStudentsBody" class="divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                    <div class="text-sm text-gray-500">
                        <span id="editSubjectStudentsCount">0</span> students
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeEditSubjectModal()"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Cancel</button>
                        <button type="button" onclick="saveSubjectAttendance()"
                            class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 transition shadow-sm">
                            <i class="bi bi-check mr-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Subject Attendance Modal -->
        <div id="viewSubjectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeViewSubjectModal()"></div>
            <div class="relative bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-[90vh] overflow-hidden">
                <div
                    class="flex justify-between items-center px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Student Attendance</h3>
                        <p class="text-blue-100 text-xs" id="viewSubjectTitle">Subject Name - Date</p>
                    </div>
                    <button onclick="closeViewSubjectModal()" class="text-blue-200 hover:text-white">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                    <table class="min-w-full text-xs">
                        <thead
                            class="bg-gray-50 text-sm font-semibold text-gray-700 sticky top-0 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left">Student Name</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-center">Roll No</th>
                                <th class="px-4 py-3 text-center">Semester</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-left">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="viewSubjectStudentsBody" class="divide-y divide-gray-200">
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
                    <div class="text-sm text-gray-500">
                        <span id="viewSubjectStudentsCount">0</span> students
                        <button type="button" onclick="printCurrentSubjectAttendance()" class="inline-flex items-center gap-1 px-3 py-1.5 ml-3 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700 transition" title="Print Attendance">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>
                    <button type="button" onclick="closeViewSubjectModal()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Close</button>
                </div>
            </div>
        </div>

        <script>
            // Filter courses by semester when filter button is clicked
            document.addEventListener('DOMContentLoaded', function() {
                var filterSemester = document.getElementById('filter_semester');
                var filterCourse = document.getElementById('filter_course');

                if (filterSemester && filterCourse) {
                    // Get current selected values from URL params
                    var currentSemester = filterSemester.value;
                    var currentCourse = '{{ $course }}';

                    // Function to load courses dynamically based on selected semester
                    function loadCoursesForFilter() {
                        var selectedSemester = filterSemester.value;

                        // If no semester selected, show all courses (or reset to default)
                        if (!selectedSemester || selectedSemester === '') {
                            // Show all options (reset display)
                            var options = filterCourse.getElementsByTagName('option');
                            for (var i = 0; i < options.length; i++) {
                                options[i].style.display = '';
                            }
                            return;
                        }

                        // Show loading state
                        filterCourse.innerHTML = '<option value="">Loading subjects...</option>';

                        // Fetch subjects from database
                        fetch('{{ route('admin.attendance.subjects') }}?semester=' + encodeURIComponent(
                                selectedSemester))
                            .then(res => {
                                if (!res.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return res.json();
                            })
                            .then(data => {
                                console.log('Filter subjects response:', data);

                                // Handle both old format (subjects) and new format (success + subjects)
                                let subjects = [];
                                if (data.success && data.subjects) {
                                    subjects = data.subjects;
                                } else if (Array.isArray(data)) {
                                    subjects = data;
                                } else if (data.subjects && Array.isArray(data.subjects)) {
                                    subjects = data.subjects;
                                }

                                if (subjects && subjects.length > 0) {
                                    let html = '<option value="">All Courses</option>';
                                    subjects.forEach(subject => {
                                        html +=
                                            `<option value="${subject.id}" data-semester="${subject.semester}">${subject.subject_code} - ${subject.subject_name}</option>`;
                                    });
                                    filterCourse.innerHTML = html;
                                    console.log('Loaded ' + subjects.length + ' subjects for filter');
                                } else {
                                    filterCourse.innerHTML = '<option value="">No subjects found</option>';
                                }
                            })
                            .catch(err => {
                                console.error('Error loading subjects for filter:', err);
                                // Fallback: show all options
                                var options = filterCourse.getElementsByTagName('option');
                                for (var i = 0; i < options.length; i++) {
                                    options[i].style.display = '';
                                }
                            });
                    }

                    // Function to filter courses based on selected semester (fallback method)
                    function filterCoursesBySemester() {
                        var selectedSemester = filterSemester.value;
                        var options = filterCourse.getElementsByTagName('option');

                        for (var i = 0; i < options.length; i++) {
                            var option = options[i];
                            var optionSemester = option.getAttribute('data-semester');

                            if (option.value === '') {
                                // Always show the "All Courses" option
                                option.style.display = '';
                            } else if (!selectedSemester || selectedSemester === '') {
                                // When "All Semesters" is selected, show all courses
                                option.style.display = '';
                            } else {
                                // Compare semester values (both are numeric)
                                var semMatch = (optionSemester == selectedSemester);
                                if (semMatch) {
                                    option.style.display = '';
                                } else {
                                    option.style.display = 'none';
                                }
                            }
                        }
                    }

                    // Try to load subjects dynamically if we have a semester selected
                    if (currentSemester) {
                        // First check if there are pre-loaded subjects with semester data
                        var hasSemesterData = false;
                        var options = filterCourse.getElementsByTagName('option');
                        for (var i = 0; i < options.length; i++) {
                            if (options[i].getAttribute('data-semester')) {
                                hasSemesterData = true;
                                break;
                            }
                        }

                        if (hasSemesterData) {
                            // Use local filtering if we have semester data in options
                            setTimeout(filterCoursesBySemester, 100);
                        } else {
                            // Try to load from server
                            setTimeout(loadCoursesForFilter, 100);
                        }
                    }

                    // Also trigger when semester changes - try dynamic loading first, fallback to local filtering
                    filterSemester.addEventListener('change', function() {
                        // Try dynamic loading first
                        loadCoursesForFilter();
                    });
                }

                // Filter courses in mark attendance modal by semester
                var markSemester = document.getElementById('mark_semester');
                var markCourse = document.getElementById('mark_course');

                if (markSemester && markCourse) {
                    // Trigger when semester changes in modal
                    markSemester.addEventListener('change', function() {
                        loadSubjectsForAttendance();
                    });
                }

                // Initialize BS/AD conversion for mark attendance modal
                const markDateInput = document.getElementById('mark_date');
                const markDateBsInput = document.getElementById('mark_date_bs');
                let isSyncingMarkDates = false;

                if (markDateInput && markDateBsInput) {
                    const syncAdToBs = value => {
                        value = String(value || '').trim();
                        if (!value) {
                            markDateBsInput.value = '';
                            return;
                        }
                        isSyncingMarkDates = true;
                        convertAdToBs(value).then(v => {
                            markDateBsInput.value = v || '';
                        }).finally(() => { isSyncingMarkDates = false; });
                    };

                    const syncBsToAd = value => {
                        value = String(value || '').trim();
                        if (!value) {
                            markDateInput.value = '';
                            return;
                        }
                        isSyncingMarkDates = true;
                        convertBsToAd(value).then(v => {
                            markDateInput.value = v || '';
                        }).finally(() => { isSyncingMarkDates = false; });
                    };

                    // Convert AD -> BS when mark date AD changes
                    markDateInput.addEventListener('change', function() {
                        if (isSyncingMarkDates) return;
                        syncAdToBs(this.value);
                    });

                    markDateInput.addEventListener('input', function() {
                        if (isSyncingMarkDates) return;
                        syncAdToBs(this.value);
                    });

                    // Convert BS -> AD when mark date BS changes
                    markDateBsInput.addEventListener('change', function() {
                        if (isSyncingMarkDates) return;
                        syncBsToAd(this.value);
                    });

                    markDateBsInput.addEventListener('input', function() {
                        if (isSyncingMarkDates) return;
                        syncBsToAd(this.value);
                    });

                    // Initialize from existing AD or BS value
                    if (markDateInput.value) {
                        isSyncingMarkDates = true;
                        convertAdToBs(markDateInput.value).then(v => {
                            markDateBsInput.value = v || '';
                        }).finally(() => { isSyncingMarkDates = false; });
                    } else if (markDateBsInput.value) {
                        isSyncingMarkDates = true;
                        convertBsToAd(markDateBsInput.value).then(v => {
                            markDateInput.value = v || '';
                        }).finally(() => { isSyncingMarkDates = false; });
                    }
                }
            });

            // Global function to filter courses by semester in mark attendance modal
            function filterMarkCoursesBySemester() {
                var markSemester = document.getElementById('mark_semester');
                var markCourse = document.getElementById('mark_course');

                if (!markSemester || !markCourse) {
                    console.log('Semester or Course select not found');
                    return;
                }

                var selectedSemester = markSemester.value;
                console.log('Filtering courses for semester:', selectedSemester);

                var options = markCourse.getElementsByTagName('option');
                let visibleCount = 0;

                for (var i = 0; i < options.length; i++) {
                    var option = options[i];
                    var optionSemester = option.getAttribute('data-semester');

                    if (option.value === '') {
                        // Always show the "General Attendance" option
                        option.style.display = '';
                        visibleCount++;
                    } else if (!selectedSemester || selectedSemester === '') {
                        // When no semester is selected, show all courses
                        option.style.display = '';
                        visibleCount++;
                    } else {
                        // Compare semester values (both are numeric strings)
                        if (optionSemester && optionSemester == selectedSemester) {
                            option.style.display = '';
                            visibleCount++;
                        } else {
                            option.style.display = 'none';
                        }
                    }
                }

                console.log('Visible course options:', visibleCount);
            }

            // Load subjects from database based on selected semester
            function loadSubjectsForAttendance() {
                var markSemester = document.getElementById('mark_semester');
                var markCourse = document.getElementById('mark_course');

                if (!markSemester || !markCourse) {
                    console.log('Semester or Course select not found');
                    return;
                }

                var selectedSemester = markSemester.value;
                console.log('Loading subjects for semester:', selectedSemester);

                if (!selectedSemester || selectedSemester === '') {
                    // Reset to show only "General Attendance" when no semester is selected
                    markCourse.innerHTML = '<option value="">General Attendance</option>';
                    return;
                }

                // Show loading state
                markCourse.innerHTML = '<option value="">Loading subjects...</option>';

                // Fetch subjects from database using the correct route
                fetch('{{ route('admin.attendance.subjects') }}?semester=' + encodeURIComponent(selectedSemester))
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Subjects response:', data);

                        // Handle both old format (subjects) and new format (success + subjects)
                        let subjects = [];
                        if (data.success && data.subjects) {
                            subjects = data.subjects;
                        } else if (Array.isArray(data)) {
                            subjects = data;
                        } else if (data.subjects && Array.isArray(data.subjects)) {
                            subjects = data.subjects;
                        }

                        if (subjects && subjects.length > 0) {
                            let html = '<option value="">General Attendance</option>';
                            subjects.forEach(subject => {
                                html +=
                                    `<option value="${subject.id}" data-semester="${subject.semester}">${subject.subject_code} - ${subject.subject_name}</option>`;
                            });
                            markCourse.innerHTML = html;
                            console.log('Loaded ' + subjects.length + ' subjects');
                        } else {
                            // No subjects found for this semester
                            markCourse.innerHTML = '<option value="">No subjects found for this semester</option>';
                            console.log('No subjects found for semester:', selectedSemester);
                        }
                    })
                    .catch(err => {
                        console.error('Error loading subjects:', err);
                        markCourse.innerHTML = '<option value="">Error loading subjects</option>';
                    });
            }

            // Date conversion functions for Bikram Sambat (BS)
            function normalizeNepaliDigits(str) {
                if (!str) return str;
                const map = {
                    '०': '0',
                    '१': '1',
                    '२': '2',
                    '३': '3',
                    '४': '4',
                    '५': '5',
                    '६': '6',
                    '७': '7',
                    '८': '8',
                    '९': '9'
                };
                return String(str).replace(/[०-९]/g, d => map[d] || d);
            }

            async function convertAdToBs(adDate) {
                if (!adDate) return '';
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const res = await fetch('/convert/ad-to-bs', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            date: adDate
                        })
                    });
                    if (!res.ok) return '';
                    const data = await res.json();
                    return data.bs || '';
                } catch (e) {
                    return '';
                }
            }

            async function convertBsToAd(bsDate) {
                bsDate = normalizeNepaliDigits(bsDate);
                if (!bsDate) return '';
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const res = await fetch('/convert/bs-to-ad', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            date: bsDate
                        })
                    });
                    if (!res.ok) return '';
                    const data = await res.json();
                    return data.ad || '';
                } catch (e) {
                    return '';
                }
            }

            // Initialize filter date BS calculation on page load
            document.addEventListener('DOMContentLoaded', function() {
                const filterDateInput = document.getElementById('filterDate');
                const filterDateBsInput = document.getElementById('filterDateBs');
                let isSyncingFilterDates = false;

                if (filterDateInput && filterDateBsInput) {
                    // Convert AD to BS when filter AD date changes
                    filterDateInput.addEventListener('change', function() {
                        if (isSyncingFilterDates) return;
                        if (this.value) {
                            isSyncingFilterDates = true;
                            convertAdToBs(this.value).then(v => {
                                filterDateBsInput.value = v || '';
                            }).finally(() => { isSyncingFilterDates = false; });
                        } else {
                            filterDateBsInput.value = '';
                        }
                    });

                    // Also update on input for real-time calculation (AD to BS)
                    filterDateInput.addEventListener('input', function() {
                        if (isSyncingFilterDates) return;
                        if (this.value && this.value.length === 10) {
                            isSyncingFilterDates = true;
                            convertAdToBs(this.value).then(v => {
                                filterDateBsInput.value = v || '';
                            }).finally(() => { isSyncingFilterDates = false; });
                        }
                    });

                    // Convert BS to AD when filter BS date changes
                    filterDateBsInput.addEventListener('change', function() {
                        if (isSyncingFilterDates) return;
                        if (this.value) {
                            isSyncingFilterDates = true;
                            convertBsToAd(this.value).then(v => {
                                filterDateInput.value = v || '';
                            }).finally(() => { isSyncingFilterDates = false; });
                        } else {
                            filterDateInput.value = '';
                        }
                    });

                    // Also update on input for real-time calculation (BS to AD)
                    filterDateBsInput.addEventListener('input', function() {
                        if (isSyncingFilterDates) return;
                        if (this.value && this.value.length === 10) {
                            isSyncingFilterDates = true;
                            convertBsToAd(this.value).then(v => {
                                filterDateInput.value = v || '';
                            }).finally(() => { isSyncingFilterDates = false; });
                        }
                    });

                    // Initialize BS date from existing AD date value
                    if (filterDateInput.value) {
                        isSyncingFilterDates = true;
                        convertAdToBs(filterDateInput.value).then(v => {
                            filterDateBsInput.value = v || '';
                        }).finally(() => { isSyncingFilterDates = false; });
                    }
                }
            });

            function openEditRecordModal(recordId, studentId, studentName, currentStatus, remarks, date) {
                document.getElementById('editRecordId').value = recordId;
                document.getElementById('editStudentId').value = studentId;
                document.getElementById('editStudentName').textContent = studentName;
                // Do not include date in the edit modal anymore

                const statusBadge = document.getElementById('editCurrentStatus');
                statusBadge.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
                statusBadge.className = `inline-block px-3 py-1 rounded text-xs font-medium whitespace-nowrap
            ${currentStatus === 'present' ? 'bg-green-100 text-green-700' : 
              (currentStatus === 'leave' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-blue-700')}`;

                document.querySelectorAll('input[name="editStatus"]').forEach(radio => {
                    radio.checked = radio.value === currentStatus;
                });

                document.getElementById('editRemarks').value = remarks || '';
                document.getElementById('editStudentModal').classList.remove('hidden');
            }

            function closeEditStudentModal() {
                document.getElementById('editStudentModal').classList.add('hidden');
            }

            async function saveStudentAttendance() {
                const recordId = document.getElementById('editRecordId').value;
                const studentId = document.getElementById('editStudentId').value;
                const newStatus = document.querySelector('input[name="editStatus"]:checked')?.value;
                const remarks = document.getElementById('editRemarks').value.trim();

                if (!newStatus) {
                    alert('Please select a status');
                    return;
                }

                if (!studentId) {
                    showToast('Error: Student ID is missing', 'error');
                    return;
                }

                try {
                    // If editing from subject attendance modal, use bulk endpoint for single student
                    if (editSubjectData.date) {
                        const payload = {
                            attendance: [{
                                student_id: studentId,
                                status: newStatus
                            }],
                            date: editSubjectData.date,
                            date_bs: editSubjectData.date_bs
                        };

                        if (editSubjectData.subject_id) {
                            payload.subject_id = editSubjectData.subject_id;
                        }

                        const res = await fetch('{{ route('admin.attendance.bulk') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();

                        if (data.success || (data.message && data.message.includes('success'))) {
                            showToast('Attendance updated successfully!', 'success');
                            closeEditStudentModal();
                            // Refresh the edit subject modal to show updated data
                            await openEditSubjectAttendance(editSubjectData.date, editSubjectData.date_bs, editSubjectData
                                .subject_id, editSubjectData.subject_name);
                        } else {
                            throw new Error(data.message || 'Failed to save');
                        }
                        return;
                    }

                    // Otherwise use individual store/update endpoints (for standalone modal usage)
                    let url = '{{ route('admin.attendance.store') }}';
                    let method = 'POST';
                    const fallbackDate = document.getElementById('filter_date_bs')?.value || document.getElementById(
                        'mark_date_bs')?.value || '';

                    if (recordId && recordId !== '') {
                        url = '{{ route('admin.attendance.update', ['id' => '__ID__']) }}'.replace('__ID__', recordId);
                        method = 'PUT';
                    }

                    if (method === 'POST' && !fallbackDate) {
                        showToast('Date is required to save attendance', 'error');
                        return;
                    }

                    const payload = {
                        student_id: studentId,
                        status: newStatus,
                        remarks: remarks
                    };

                    if (method === 'POST') payload.date = fallbackDate;

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Attendance updated successfully!', 'success');
                        closeEditStudentModal();
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Failed to save');
                    }
                } catch (error) {
                    console.error('Error saving attendance:', error);
                    showToast('Error saving attendance: ' + error.message, 'error');
                }
            }

            // Toast notification function - uses global showToast from layout
            function showToast(message, type = 'success') {
                // Call the global showToast from admin/layouts/app.blade.php
                if (typeof window.showToast === 'function') {
                    window.showToast(message, type);
                } else {
                    // Fallback if global not available
                    const toast = document.getElementById('toast');
                    if (toast) {
                        const bgColor = type === 'success' ? 'bg-green-500' : (type === 'warning' ? 'bg-yellow-500' :
                            'bg-blue-500');
                        const icon = type === 'success' ? 'bi-check-circle' : (type === 'warning' ? 'bi-exclamation-triangle' :
                            'bi-exclamation-circle');

                        toast.innerHTML = `
                    <div class="flex items-center gap-3 px-4 py-3 rounded shadow-lg text-white ${bgColor}">
                        <i class="bi ${icon} text-lg"></i>
                        <span>${message}</span>
                    </div>
                `;
                        toast.classList.remove('hidden');

                        setTimeout(() => {
                            toast.classList.add('hidden');
                        }, 4000);
                    }
                }
            }

            document.getElementById('editStudentModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditStudentModal();
                }
            });

            document.querySelector('input[name="date_bs"]')?.addEventListener('change', function() {
                this.closest('form').submit();
            });

            // MARK ATTENDANCE - bulk modal and actions
            let attendanceState = {};
            let isSyncingModalDates = false;

            function openMarkAttendanceModal() {
                // Get modal and ensure event listeners are attached
                const markAttendanceModal = document.getElementById('markAttendanceModal');
                const markSemesterSelect = document.getElementById('mark_semester');
                const markCourseSelect = document.getElementById('mark_course');

                // Ensure event listener is attached to semester select
                if (markSemesterSelect && !markSemesterSelect.hasAttribute('data-listener-attached')) {
                    markSemesterSelect.addEventListener('change', function() {
                        loadSubjectsForAttendance(); // Use dynamic loading instead of local filtering
                    });
                    markSemesterSelect.setAttribute('data-listener-attached', 'true');
                }

                const markDateInput = document.getElementById('mark_date');
                const markDateBsInput = document.getElementById('mark_date_bs');

                // Reset all fields to emtpy on modal open
                if (markDateInput) markDateInput.value = '';
                if (markDateBsInput) markDateBsInput.value = '';
                if (markSemesterSelect) markSemesterSelect.value = '';
                if (markCourseSelect) markCourseSelect.innerHTML = '<option value="">General Attendance</option>';

                // Show modal
                markAttendanceModal.classList.remove('hidden');
                renderAttendanceTable([]);

                console.log('Mark Attendance modal opened with clear fields.');
            }

            function closeMarkAttendanceModal() {
                document.getElementById('markAttendanceModal').classList.add('hidden');
                renderAttendanceTable([]);
                document.getElementById('attendanceSummary').classList.add('hidden');
            }

            // Check if attendance already exists for selected date and semester
            async function checkExistingAttendance() {
                const date = document.getElementById('mark_date')?.value || '';
                const semester = document.getElementById('mark_semester')?.value || '';
                const subjectId = document.getElementById('mark_course')?.value || '';

                if (!semester || (!date && !document.getElementById('mark_date_bs')?.value)) return;

                try {
                    let dateBs = document.getElementById('mark_date_bs')?.value || '';
                    if (!dateBs && date) {
                        dateBs = (await convertAdToBs(date)) || '';
                        if (dateBs) {
                            isSyncingModalDates = true;
                            const markDateBsInput = document.getElementById('mark_date_bs');
                            if (markDateBsInput) markDateBsInput.value = dateBs;
                            isSyncingModalDates = false;
                        }
                    }
                    if (!dateBs) return;

                    const url = new URL('{{ route('admin.attendance.students') }}', window.location.origin);
                    url.searchParams.set('date_bs', dateBs);
                    url.searchParams.set('semester', semester);
                    if (subjectId) {
                        url.searchParams.set('subject_id', subjectId);
                    }

                    const res = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    const alreadyMarkedStudents = data.students ? data.students.filter(s => s.alreadyMarked).length : 0;
                    if (alreadyMarkedStudents > 0 && alreadyMarkedStudents === (data.students ? data.students.length : 0)) {
                        showToast('Attendance already marked for all students on this date.', 'warning');
                    } else if (alreadyMarkedStudents > 0) {
                        showToast('Attendance for ' + alreadyMarkedStudents +
                            ' student(s) already marked for this date. They will be updated.', 'warning');
                    }
                } catch (err) {
                    console.error('Error checking existing attendance', err);
                }
            }

            // Add event listener for semester selection in modal
            document.getElementById('mark_semester')?.addEventListener('change', function() {
                const semester = this.value;
                const date = document.getElementById('mark_date').value;
                if (semester && (date || document.getElementById('mark_date_bs')?.value)) checkExistingAttendance();
            });

            async function loadAttendanceStudents() {
                const date = document.getElementById('mark_date').value;
                const semester = document.getElementById('mark_semester').value;
                const subjectId = document.getElementById('mark_course').value;

                if (!date || !semester) {
                    alert('Please select both date and semester');
                    return;
                }

                const btn = document.getElementById('loadStudentsBtn');
                btn.disabled = true;
                btn.textContent = 'Loading...';

                try {
                    let dateBs = document.getElementById('mark_date_bs')?.value || '';
                    if (!dateBs && date) {
                        dateBs = (await convertAdToBs(date)) || '';
                        if (dateBs) {
                            isSyncingModalDates = true;
                            const markDateBsInput = document.getElementById('mark_date_bs');
                            if (markDateBsInput) markDateBsInput.value = dateBs;
                            isSyncingModalDates = false;
                        }
                    }
                    const url = new URL('{{ route('admin.attendance.students') }}', window.location.origin);
                    if (dateBs) url.searchParams.set('date_bs', dateBs);
                    url.searchParams.set('semester', semester);
                    // Pass subject_id to filter attendance by subject
                    if (subjectId) {
                        url.searchParams.set('subject_id', subjectId);
                    }

                    const res = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    // Expecting an array of students: {id, name, email, roll_no, semester}
                    const students = data.students || data || [];

                    // Disable save button ONLY if ALL students already have attendance marked
                    const alreadyMarkedCount = students.filter(s => s.alreadyMarked).length;
                    const saveBtn = document.getElementById('saveAllBtn');
                    if (alreadyMarkedCount > 0 && alreadyMarkedCount === students.length) {
                        saveBtn.disabled = true;
                    } else {
                        saveBtn.disabled = false;
                    }

                    renderAttendanceTable(students, date);
                } catch (err) {
                    console.error('Error loading students', err);
                    showToast('Failed to load students', 'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = '🔍 Load Students';
                }
            }

            function renderAttendanceTable(students = []) {
                attendanceState = {};
                const tbody = document.getElementById('attendanceTbody');
                tbody.innerHTML = '';

                if (!students || students.length === 0) {
                    document.getElementById('attendanceTableWrap').classList.add('hidden');
                    document.getElementById('attendanceSummary').classList.add('hidden');
                    return;
                }

                students.forEach(s => {
                    // Use student_id from the API response (which is students.id)
                    const studentId = s.student_id || s.id;
                    attendanceState[studentId] = {
                        student_id: studentId,
                        student: s,
                        status: s.status || 'present'
                    };

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-blue-50 transition-colors';

                    const nameTd = document.createElement('td');
                    nameTd.className = 'px-3 py-2 text-xs';
                    nameTd.innerHTML =
                        `<div class="flex items-center gap-2"><div class="w-6 h-6 bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center flex-shrink-0"><i class="bi bi-person-fill text-xs text-blue-600"></i></div><div><div class="font-semibold text-gray-900 text-xs">${escapeHtml(s.name)}</div><div class="text-xs text-gray-500 truncate">${escapeHtml(s.email || '')}</div></div></div>`;

                    const rollTd = document.createElement('td');
                    rollTd.className = 'px-3 py-2 text-xs text-center font-medium text-gray-700';
                    rollTd.textContent = s.roll_no || '-';

                    const semTd = document.createElement('td');
                    semTd.className = 'px-3 py-2 text-xs text-center text-gray-700';
                    semTd.innerHTML =
                        `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">${s.semester || '-'}</span>`;

                    const dateTd = document.createElement('td');
                    dateTd.className = 'px-3 py-2 text-xs text-center text-gray-700';
                    dateTd.textContent = document.getElementById('mark_date').value ? document.getElementById(
                        'mark_date').value : '-';

                    const isPresent = (s.status || 'present') === 'present';
                    const presentTd = document.createElement('td');
                    presentTd.className = 'px-3 py-2 text-center';
                    presentTd.innerHTML = `
                <div class="inline-flex items-center gap-2 justify-center">
                    <label class="inline-flex items-center cursor-pointer">
                        <input data-id="${studentId}" type="checkbox" ${isPresent ? 'checked' : ''} class="sr-only attendance-toggle" aria-label="Mark present">
                        <div class="w-10 h-5 ${isPresent ? 'bg-green-500' : 'bg-gray-200'} rounded-full relative transition-colors duration-200">
                            <div class="dot absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200" style="transform: translateX(${isPresent ? '20px' : '0'})"></div>
                        </div>
                    </label>
                    <span class="text-xs font-medium ${isPresent ? 'text-green-700' : 'text-blue-700'} status-badge">${isPresent ? 'Present' : 'Absent'}</span>
                </div>
            `;

                    tr.appendChild(nameTd);
                    tr.appendChild(rollTd);
                    tr.appendChild(semTd);
                    tr.appendChild(dateTd);
                    tr.appendChild(presentTd);

                    tbody.appendChild(tr);
                });

                // wire toggles
                document.querySelectorAll('.attendance-toggle').forEach(cb => {
                    cb.addEventListener('change', function() {
                        const id = this.dataset.id;
                        const isChecked = this.checked;
                        attendanceState[id].status = isChecked ? 'present' : 'absent';
                        updateSummary();
                        markUnsaved();
                        // animate toggle and update badge
                        const wrap = this.nextElementSibling || this.parentElement.querySelector('div');
                        const dot = wrap.querySelector('.dot');
                        const badge = wrap.closest('td').querySelector('.status-badge');

                        if (isChecked) {
                            wrap.classList.remove('bg-gray-200');
                            wrap.classList.add('bg-green-500');
                            dot.style.transform = 'translateX(24px)';
                            if (badge) {
                                badge.textContent = 'Present';
                                badge.classList.remove('text-blue-700');
                                badge.classList.add('text-green-700');
                            }
                        } else {
                            wrap.classList.remove('bg-green-500');
                            wrap.classList.add('bg-gray-200');
                            dot.style.transform = 'translateX(0)';
                            if (badge) {
                                badge.textContent = 'Absent';
                                badge.classList.remove('text-green-700');
                                badge.classList.add('text-blue-700');
                            }
                        }
                    });
                });

                document.getElementById('attendanceTableWrap').classList.remove('hidden');
                document.getElementById('attendanceSummary').classList.remove('hidden');
                updateSummary();
            }

            function updateSummary() {
                const total = Object.keys(attendanceState).length;
                const present = Object.values(attendanceState).filter(x => x.status === 'present').length;
                const absent = total - present;
                document.getElementById('summary_total').textContent = total;
                document.getElementById('summary_present').textContent = present;
                document.getElementById('summary_absent').textContent = absent;
            }

            function markUnsaved() {
                // enable save button when user makes changes
                const saveBtn = document.getElementById('saveAllBtn');
                saveBtn.classList.remove('opacity-50');
                saveBtn.disabled = false;
            }

            function escapeHtml(unsafe) {
                return String(unsafe).replace(/[&<>\"'`]/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '\"': '&quot;',
                "'": '&#39;',
                '`': '&#x60;'
                    } [m];
                });
            }

            async function saveAllAttendance() {
                const attendance = Object.values(attendanceState).map(s => ({
                    student_id: s.student_id,
                    status: s.status
                }));
                const date = document.getElementById('mark_date').value;
                const dateBs = document.getElementById('mark_date_bs').value;
                const semester = document.getElementById('mark_semester').value;
                const courseId = document.getElementById('mark_course').value;

                if (attendance.length === 0) {
                    alert('No students to save');
                    return;
                }

                if (!date) {
                    alert('Please select a date');
                    return;
                }

                if (!semester) {
                    alert('Please select a semester');
                    return;
                }

                try {
                    // Build payload
                    const payload = {
                        attendance,
                        date,
                        date_bs: dateBs
                    };
                    if (courseId) {
                        payload.subject_id = courseId;
                    }

                    console.log('Saving attendance with payload:', payload);

                    const res = await fetch('{{ route('admin.attendance.bulk') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();
                    console.log('Save response:', data);

                    if (data.success || (data.message && data.message.includes('success'))) {
                        showToast('Attendance saved successfully for ' + attendance.length + ' students!', 'success');

                        // Close modal first
                        closeMarkAttendanceModal();

                        const reloadUrl = '{{ route('admin.attendance') }}';
                        console.log('Reloading with URL:', reloadUrl);

                        // Reload after a short delay to show the toast
                        setTimeout(() => {
                            window.location.href = reloadUrl;
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Failed to save attendances');
                    }
                } catch (err) {
                    console.error('Save error:', err);
                    showToast('Error saving attendance: ' + (err.message || ''), 'error');
                }
            }

            async function deleteAttendance(recordId, studentName) {
                if (!confirm('Are you sure you want to delete the attendance record for ' + studentName + '?')) {
                    return;
                }

                try {
                    const response = await fetch('{{ route('admin.attendance.delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id: recordId
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast('Attendance deleted successfully!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Failed to delete');
                    }
                } catch (error) {
                    console.error('Error deleting attendance:', error);
                    showToast('Error deleting attendance: ' + error.message, 'error');
                }
            }

            // View Subject Attendance Modal Functions
            async function viewSubjectAttendance(date, subjectId, subjectName) {
                document.getElementById('viewSubjectTitle').textContent = subjectName + ' - ' + date;
                window.currentSubjectId = subjectId || '';
                window.currentDate = date || '';
                document.getElementById('viewSubjectStudentsBody').innerHTML =
                    '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">Loading...</td></tr>';
                document.getElementById('viewSubjectModal').classList.remove('hidden');

                try {
                    const params = new URLSearchParams();
                    params.append('date', date);
                    if (subjectId && subjectId !== 'null' && subjectId !== '') {
                        params.append('subject_id', subjectId);
                    }

                    const url = '{{ route('admin.attendance.subject-students') }}?' + params.toString();
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    const students = data.students || [];
                    document.getElementById('viewSubjectStudentsCount').textContent = students.length;

                    if (students.length === 0) {
                        document.getElementById('viewSubjectStudentsBody').innerHTML =
                            '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">No students found</td></tr>';
                        return;
                    }

                    let html = '';
                    students.forEach(s => {
                        let statusClass = '';
                        let statusText = '';
                        if (s.status === 'present') {
                            statusClass = 'bg-green-100 text-green-700';
                            statusText = 'Present';
                        } else if (s.status === 'absent') {
                            statusClass = 'bg-red-100 text-blue-700';
                            statusText = 'Absent';
                        } else if (s.status === 'leave') {
                            statusClass = 'bg-purple-100 text-purple-700';
                            statusText = 'Leave';
                        }

                        html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-900 font-medium">${escapeHtml(s.name || 'N/A')}</td>
                        <td class="px-3 py-2 text-gray-600">${escapeHtml(s.email || '-')}</td>
                        <td class="px-3 py-2 text-center text-gray-700">${s.roll_no || '-'}</td>
                        <td class="px-3 py-2 text-center text-gray-700">${s.semester || '-'}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium ${statusClass}">${statusText}</span>
                        </td>
                        <td class="px-3 py-2 text-gray-600 text-xs">${escapeHtml(s.remarks || '-')}</td>
                    </tr>
                `;
                    });

                    document.getElementById('viewSubjectStudentsBody').innerHTML = html;
                } catch (err) {
                    console.error('Error loading subject attendance:', err);
                    document.getElementById('viewSubjectStudentsBody').innerHTML =
                        '<tr><td colspan="6" class="px-3 py-8 text-center text-blue-500">Error loading attendance</td></tr>';
                }
            }

            function closeViewSubjectModal() {
                document.getElementById('viewSubjectModal').classList.add('hidden');
            }

            function printCurrentSubjectAttendance() {
                const sid = window.currentSubjectId || '';
                const d = window.currentDate || '';
                if (!sid || sid === 'null') {
                    showToast('Please select a subject attendance record to print.', 'warning');
                    return;
                }
                const url = '{{ route('admin.attendance.print') }}' + '?subject_id=' + encodeURIComponent(sid) + '&date=' + encodeURIComponent(d);
                adminOpenPrintPreview(url, {
                    title: 'Print Attendance',
                });
            }

            // Close modal on background click
            document.getElementById('viewSubjectModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeViewSubjectModal();
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeViewSubjectModal();
                }
            });

            // Edit Subject Attendance Functions
            let editSubjectData = {
                date: '',
                date_bs: '',
                subject_id: '',
                subject_name: '',
                students: []
            };

            async function openEditSubjectAttendance(date, date_bs, subjectId, subjectName) {
                editSubjectData.date = date;
                editSubjectData.date_bs = date_bs;
                editSubjectData.subject_id = subjectId;
                editSubjectData.subject_name = subjectName;

                document.getElementById('editSubjectTitle').textContent = subjectName + ' - ' + date;
                document.getElementById('editSubjectStudentsBody').innerHTML =
                    '<tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">Loading...</td></tr>';
                document.getElementById('editSubjectModal').classList.remove('hidden');

                try {
                    const params = new URLSearchParams();
                    params.append('date', date);
                    if (subjectId && subjectId !== 'null' && subjectId !== '') {
                        params.append('subject_id', subjectId);
                    }

                    const url = '{{ route('admin.attendance.subject-students') }}?' + params.toString();
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    const students = data.students || [];
                    editSubjectData.students = students;
                    document.getElementById('editSubjectStudentsCount').textContent = students.length;

                    if (students.length === 0) {
                        document.getElementById('editSubjectStudentsBody').innerHTML =
                            '<tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No students found</td></tr>';
                        return;
                    }

                    let html = '';
                    students.forEach(s => {
                        let statusClass = '';
                        if (s.status === 'present') statusClass = 'bg-green-100 text-green-700';
                        else if (s.status === 'absent') statusClass = 'bg-red-100 text-blue-700';
                        else if (s.status === 'leave') statusClass = 'bg-purple-100 text-purple-700';
                        else statusClass = 'bg-gray-100 text-gray-700'; // pending

                        html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-900 font-medium">${escapeHtml(s.name || 'N/A')}</td>
                        <td class="px-3 py-2 text-gray-600">${escapeHtml(s.email || '-')}</td>
                        <td class="px-3 py-2 text-center text-gray-700">${s.roll_no || '-'}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium ${statusClass}">${s.status === 'pending' ? 'Not Marked' : (s.status || '-')}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <select onchange="updateStudentStatus('${s.id}', this.value)" class="text-xs border border-gray-300 rounded px-2 py-1">
                                <option value="present" ${s.status === 'present' ? 'selected' : ''}>Present</option>
                                <option value="absent" ${s.status === 'absent' ? 'selected' : ''}>Absent</option>
                                <option value="leave" ${s.status === 'leave' ? 'selected' : ''}>Leave</option>
                            </select>
                        </td>
                    </tr>
                `;
                    });

                    document.getElementById('editSubjectStudentsBody').innerHTML = html;
                } catch (err) {
                    console.error('Error loading subject attendance for edit:', err);
                    document.getElementById('editSubjectStudentsBody').innerHTML =
                        '<tr><td colspan="5" class="px-3 py-8 text-center text-blue-500">Error loading attendance</td></tr>';
                }
            }

            function updateStudentStatus(studentId, newStatus) {
                const student = editSubjectData.students.find(s => s.id === studentId);
                if (student) {
                    student.new_status = newStatus;
                }
            }

            function closeEditSubjectModal() {
                document.getElementById('editSubjectModal').classList.add('hidden');
                editSubjectData = {
                    date: '',
                    date_bs: '',
                    subject_id: '',
                    subject_name: '',
                    students: []
                };
            }

            async function saveSubjectAttendance() {
                const bulkStatus = document.querySelector('input[name="bulkStatus"]:checked')?.value;

                // Build attendance array only with students that were changed
                const attendance = editSubjectData.students
                    .filter(s => s.new_status) // Only include students whose status was changed
                    .map(s => {
                        let status = s.new_status;
                        if (status === 'pending') {
                            status = 'present';
                        }
                        return {
                            student_id: s.student_id,
                            status: status
                        };
                    });

                // If no students were explicitly changed and bulk status is selected, use bulk for all
                if (attendance.length === 0 && bulkStatus) {
                    editSubjectData.students.forEach(s => {
                        attendance.push({
                            student_id: s.student_id,
                            status: bulkStatus
                        });
                    });
                }

                if (attendance.length === 0) {
                    showToast('No changes to save', 'warning');
                    return;
                }

                try {
                    const payload = {
                        attendance: attendance,
                        date: editSubjectData.date,
                        date_bs: editSubjectData.date_bs
                    };

                    if (editSubjectData.subject_id) {
                        payload.subject_id = editSubjectData.subject_id;
                    }

                    const res = await fetch('{{ route('admin.attendance.bulk') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();

                    if (data.success || (data.message && data.message.includes('success'))) {
                        showToast('Attendance updated successfully!', 'success');
                        closeEditSubjectModal();
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Failed to save');
                    }
                } catch (err) {
                    console.error('Save error:', err);
                    showToast('Error saving attendance: ' + (err.message || ''), 'error');
                }
            }

            async function deleteSubjectAttendance(date, subjectId, subjectName) {
                if (!confirm('Are you sure you want to delete all attendance records for ' + subjectName + ' on ' + date +
                        '? This action cannot be undone.')) {
                    return;
                }

                try {
                    const payload = {
                        date: date
                    };

                    if (subjectId && subjectId !== 'null' && subjectId !== '') {
                        payload.subject_id = subjectId;
                    }

                    const deleteRes = await fetch('{{ route('admin.attendance.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const deleteData = await deleteRes.json();

                    if (deleteData.success) {
                        showToast(deleteData.message || 'Attendance deleted successfully!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        throw new Error(deleteData.message || 'Failed to delete');
                    }
                } catch (error) {
                    console.error('Error deleting attendance:', error);
                    showToast('Error deleting attendance: ' + error.message, 'error');
                }
            }

            // Close edit modal on background click
            document.getElementById('editSubjectModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeEditSubjectModal();
            });

            // Close edit modal on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeEditSubjectModal();
            });
        </script>
    @endsection
