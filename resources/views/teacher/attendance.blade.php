@extends('teacher.layouts.teacherlayout')

@section('title', __('Attendance'))

@section('content')
<div class="space-y-4">
    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed top-4 right-4 z-50"></div>

    <!-- Page Header -->
    @include('teacher.components.teacher-page-header', [
        'title' => __('Attendance'),
        'breadcrumbs' => [
            ['label' => __('Dashboard'), 'url' => route('teacher.dashboard')],
            ['label' => __('Attendance')]
        ],
        'addButton' => [
            'label' => __('Mark Attendance'),
            'onclick' => 'openMarkAttendanceModal()',
            'color' => 'red'
        ]
    ])

    <!-- Stats Cards -->
    @include('teacher.components.teacher-stats-cards', [
        'cards' => [
            ['title' => __('Total Records'), 'value' => $stats['total'], 'icon' => 'bi-list-check', 'color' => 'blue'],
            ['title' => __('Present'), 'value' => $stats['present'], 'icon' => 'bi-check-circle', 'color' => 'green'],
            ['title' => __('Absent'), 'value' => $stats['absent'], 'icon' => 'bi-x-circle', 'color' => 'red'],
            ['title' => __('Leave'), 'value' => $stats['leave'], 'icon' => 'bi-calendar-event', 'color' => 'purple'],
        ]
    ])

    <!-- Filters Card -->
    @include('teacher.components.teacher-filter-card', [
        'formAction' => route('teacher.attendance'),
        'filters' => [
            ['name' => 'date', 'type' => 'date', 'value' => request('date'), 'label' => __('Date (AD)')],
            ['name' => 'date_bs', 'type' => 'text', 'value' => request('date_bs'), 'placeholder' => 'YYYY-MM-DD', 'label' => __('Date (BS)'), 'class' => 'bs-date', 'icon' => 'bi-calendar3', 'autocomplete' => 'off'],
            ['name' => 'subject', 'type' => 'select', 'options' => $subjects->mapWithKeys(function($s) { return [$s['id'] => $s['code'] . ' - ' . $s['name']]; })->prepend('All Subjects', '')->toArray(), 'value' => request('subject'), 'label' => __('Subject')],
            ['name' => 'q', 'type' => 'text', 'value' => request('q'), 'placeholder' => __('Name, Email, Roll No'), 'label' => __('Search Student')]
        ],
        'showReset' => true,
        'resetRoute' => route('teacher.attendance')
    ])

        <!-- Attendance Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        {{-- Table Header --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        <i class="bi bi-collection mr-2"></i>{{ __('Attendance by Subject') }}
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300">
                        {{ $subjectAttendance->count() }} {{ __('records') }}
                    </span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('teacher.attendance.print') }}?{{ http_build_query(request()->query()) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="bi bi-printer"></i> {{ __('Print') }}
                    </a>
                </div>
            </div>
        </div>
    
        {{-- Table Content --}}
        <div class="overflow-x-auto">
            @if($subjectAttendance->count() > 0)
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Subject') }}</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Date') }}</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Total') }}</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Present') }}</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Absent') }}</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Leave') }}</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @foreach($subjectAttendance as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition border-b border-gray-100 dark:border-slate-700">
                                <td class="px-4 py-4 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40">
                                        <i class="bi bi-book mr-1"></i>
                                        {{ data_get($item, 'subject_code', '') }} {{ data_get($item, 'subject_name', '') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center text-gray-700 dark:text-gray-300 text-sm">
                                    <div class="flex flex-col items-center">
                                        <span class="font-medium">{{ data_get($item, 'date', '-') }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">{{ data_get($item, 'date_bs', '') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-gray-900 dark:text-white text-sm">{{ data_get($item, 'total', 0) }}</td>
                                <td class="px-4 py-4 text-center text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ data_get($item, 'present', 0) }}</span>
                                </td>
                                <td class="px-4 py-4 text-center text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ data_get($item, 'absent', 0) }}</span>
                                </td>
                                <td class="px-4 py-4 text-center text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">{{ data_get($item, 'leave_count', data_get($item, 'leave', 0)) }}</span>
                                </td>
                                <td class="px-4 py-4 text-center text-sm">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="viewSubjectAttendance('{{ data_get($item, 'date', '') }}', '{{ data_get($item, 'subject_id', '') }}', '{{ data_get($item, 'subject_name', 'General') }}')" 
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded transition" title="{{ __('View') }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button onclick="openEditSubjectAttendance('{{ data_get($item, 'date', '') }}', '{{ data_get($item, 'date_bs', '') }}', '{{ data_get($item, 'subject_id', '') }}', '{{ data_get($item, 'subject_name', 'General') }}')" 
                                            class="inline-flex items-center gap-1 px-2 py-1 text-xs text-yellow-700 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30 hover:bg-yellow-200 dark:hover:bg-yellow-900/50 rounded transition" title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center">
                    <i class="bi bi-inbox text-5xl text-gray-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ __('No attendance records found.') }}</p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($subjectAttendance->count() > 0)
            <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
                {{ $subjectAttendance->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Mark Attendance Modal -->
<div id="markAttendanceModal" class="fixed hidden inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 rounded-t-lg shadow-md flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-start gap-3">
                    <div class="bg-white bg-opacity-20 rounded-full p-2">
                        <i class="bi bi-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">{{ __('Mark Attendance') }}</h2>
                        <p class="text-red-100 text-xs">{{ __('Pick a subject and date, then load students') }}</p>
                    </div>
                </div>
                <button onclick="closeMarkAttendanceModal()" aria-label="Close" class="text-red-200 hover:text-white p-2 rounded-full hover:bg-red-700/25">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-5 space-y-4 overflow-y-auto flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Date (AD)') }} <span class="text-red-500">*</span></label>
                    <input type="date" id="mark_date" value="" class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 hover:border-gray-400 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Date (BS)') }}</label>
                    <div class="relative">
                        <input type="text" id="mark_date_bs" placeholder="YYYY-MM-DD" autocomplete="off" class="bs-date w-full pr-10 px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 hover:border-gray-400 transition-colors">
                        <button type="button" aria-label="Pick BS date" onclick="event?.preventDefault(); event?.stopPropagation(); window.openBsDatePicker?.('mark_date_bs'); return false;" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">
                            <i class="bi bi-calendar3"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Subject') }} <span class="text-red-500">*</span></label>
                    <select id="mark_subject" class="w-full px-3 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg text-sm shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 hover:border-gray-400 transition-colors">
                        <option value="">{{ __('Select Subject') }}</option>
                        @if($subjects->count() > 0)
                            @foreach($subjects as $sub)
                                <option value="{{ $sub['id'] }}">{{ $sub['code'] }} - {{ $sub['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            
            <div>
                <button type="button" id="loadStudentsBtn" onclick="loadAttendanceStudents()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition-colors font-medium shadow-md">
                    <i class="bi bi-search"></i>
                    <span>{{ __('Load Students') }}</span>
                </button>
            </div>    

            <div id="attendanceSummary" class="grid grid-cols-3 gap-3 hidden">
                <div class="p-3 bg-gradient-to-br from-blue-50 dark:from-blue-900/20 to-blue-100 dark:to-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-900/40 shadow-sm text-center">
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-400" id="summary_total">0</div>
                    <div class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-0.5">{{ __('Total') }}</div>
                </div>
                <div class="p-3 bg-gradient-to-br from-green-50 dark:from-green-900/20 to-green-100 dark:to-green-900/30 rounded-lg border border-green-200 dark:border-green-900/40 shadow-sm text-center">
                    <div class="text-2xl font-bold text-green-700 dark:text-green-400" id="summary_present">0</div>
                    <div class="text-xs text-green-600 dark:text-green-400 font-medium mt-0.5">{{ __('Present') }}</div>
                </div>
                <div class="p-3 bg-gradient-to-br from-red-50 dark:from-red-900/20 to-red-100 dark:to-red-900/30 rounded-lg border border-red-200 dark:border-red-900/40 shadow-sm text-center">
                    <div class="text-2xl font-bold text-red-700 dark:text-red-400" id="summary_absent">0</div>
                    <div class="text-xs text-red-600 dark:text-red-400 font-medium mt-0.5">{{ __('Absent') }}</div>
                </div>
            </div>

            <div id="attendanceTableWrap" class="hidden border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden shadow-sm">
                <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700 sticky top-0 border-b border-gray-200 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Student') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Roll No') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Sem') }}</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Status') }}</th>
                            </tr>
                        </thead> 
                        <tbody id="attendanceTbody" class="divide-y divide-gray-200 dark:divide-slate-700"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700 flex justify-end gap-2 rounded-b-lg flex-shrink-0">
            <button type="button" onclick="closeMarkAttendanceModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-white dark:hover:bg-slate-600 transition-colors font-medium">
                <i class="bi bi-x-circle mr-1.5"></i> {{ __('Cancel') }}
            </button>
            <button type="button" id="saveAllBtn" onclick="saveAllAttendance()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium shadow-md" disabled>
                <i class="bi bi-check-circle"></i>
                <span>{{ __('Save') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Edit Subject Attendance Modal -->
<div id="editSubjectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeEditSubjectModal()"></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-red-600 dark:from-red-700 to-red-700 dark:to-red-800">
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('Edit Attendance') }}</h3>
                <p class="text-red-100 text-xs" id="editSubjectTitle">{{ __('Subject Name - Date') }}</p>
            </div>
            <button onclick="closeEditSubjectModal()" class="text-red-200 hover:text-white p-2 rounded-full hover:bg-red-700/25">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        
        <div class="p-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
            <div class="flex items-center gap-3 text-xs flex-wrap">
                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ __('Mark all as:') }}</span>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="bulkStatus" value="present" class="sr-only">
                    <div class="px-3 py-1.5 rounded text-xs font-medium border border-green-300 dark:border-green-900/40 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/30">
                        <i class="bi bi-check-circle text-xs mr-1.5"></i> {{ __('Present') }}
                    </div>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="bulkStatus" value="absent" class="sr-only">
                    <div class="px-3 py-1.5 rounded text-xs font-medium border border-red-300 dark:border-red-900/40 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30">
                        <i class="bi bi-x-circle text-xs mr-1.5"></i> {{ __('Absent') }}
                    </div>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="bulkStatus" value="leave" class="sr-only">
                    <div class="px-3 py-1.5 rounded text-xs font-medium border border-purple-300 dark:border-purple-900/40 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/30">
                        <i class="bi bi-calendar-event text-xs mr-1.5"></i> {{ __('Leave') }}
                    </div>
                </label>
            </div>
        </div>
        
        <div class="overflow-x-auto max-h-[50vh] overflow-y-auto flex-1">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700 sticky top-0 border-b border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Student Name') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Roll No') }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Current') }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('New Status') }}</th>
                    </tr>
                </thead>
                <tbody id="editSubjectStudentsBody" class="divide-y divide-gray-200 dark:divide-slate-700">
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-200 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-700/50">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span id="editSubjectStudentsCount">0</span> {{ __('students') }}
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeEditSubjectModal()" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                <button type="button" onclick="saveSubjectAttendance()" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition shadow-sm">
                    <i class="bi bi-check mr-1.5"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Subject Attendance Modal -->
<div id="viewSubjectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black bg-opacity-40" onclick="closeViewSubjectModal()"></div>
    <div class="relative bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-200 dark:border-slate-700 bg-gradient-to-r from-blue-600 dark:from-blue-700 to-blue-700 dark:to-blue-800">
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('Student Attendance') }}</h3>
                <p class="text-blue-100 text-xs" id="viewSubjectTitle">{{ __('Subject Name - Date') }}</p>
            </div>
            <button onclick="closeViewSubjectModal()" class="text-blue-200 hover:text-white p-2 rounded-full hover:bg-blue-700/25">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div class="overflow-x-auto max-h-[60vh] overflow-y-auto flex-1">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700 sticky top-0 border-b border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Student Name') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Roll No') }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Semester') }}</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700 dark:text-gray-200">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('Remarks') }}</th>
                    </tr>
                </thead>
                <tbody id="viewSubjectStudentsBody" class="divide-y divide-gray-200 dark:divide-slate-700">
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t flex justify-between items-center bg-gray-50 dark:bg-slate-700/50">
            <div class="flex items-center gap-2">
                <span id="viewSubjectStudentsCount">0</span> {{ __('students') }}
                <button type="button" onclick="printCurrentSubjectAttendance()" class="inline-flex items-center gap-1 px-3 py-1.5 ml-3 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700 transition" title="Print Attendance">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
            <button type="button" onclick="closeViewSubjectModal()" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-600 transition">{{ __('Close') }}</button>
        </div>
    </div>
</div>

    <script>
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        if (!toast) return;
        
        const bgColor = type === 'success' ? 'bg-green-500' : (type === 'warning' ? 'bg-yellow-500' : 'bg-red-500');
        const icon = type === 'success' ? 'bi-check-circle' : (type === 'warning' ? 'bi-exclamation-triangle' : 'bi-exclamation-circle');
        
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

    // Date conversion functions for Bikram Sambat (BS)
    function normalizeNepaliDigits(str) {
        if (!str) return str;
        const map = { '०':'0','१':'1','२':'2','३':'3','४':'4','५':'5','६':'6','७':'7','८':'8','९':'9' };
        return String(str)
            .replace(/[०-९]/g, d => map[d] || d)
            .replace(/[–—‑−/]/g, '-')
            .trim();
    }

    function toNepaliDigits(str) {
        if (!str) return str;
        const map = { '0':'०','1':'१','2':'२','3':'३','4':'४','5':'५','6':'६','7':'७','8':'८','9':'९' };
        return String(str).replace(/[0-9]/g, d => map[d] || d);
    }

    async function convertAdToBs(adDate) {
        if (!adDate) return '';
        try {
            const res = await fetch('/convert/ad-to-bs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ date: adDate })
            });
            if (!res.ok) return '';
            const data = await res.json();
            return toNepaliDigits(data.bs || '');
        } catch (e) {
            return '';
        }
    }

    async function convertBsToAd(bsDate) {
        bsDate = normalizeNepaliDigits(bsDate);
        if (!bsDate) return '';
        try {
            const res = await fetch('/convert/bs-to-ad', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ date: bsDate })
            });
            if (!res.ok) return '';
            const data = await res.json();
            return data.ad || '';
        } catch (e) {
            return '';
        }
    }

    // Initialize filter date sync (AD <-> BS)
    document.addEventListener('DOMContentLoaded', function() {
        const filterDateInput = document.getElementById('filterDate');
        const filterDateBsInput = document.getElementById('filterDateBs');
        let isSyncingFilterDates = false;

        if (!filterDateInput || !filterDateBsInput) return;

        filterDateInput.addEventListener('change', function() {
            if (isSyncingFilterDates) return;
            if (!this.value) {
                filterDateBsInput.value = '';
                return;
            }
            isSyncingFilterDates = true;
            convertAdToBs(this.value).then(v => { filterDateBsInput.value = v || ''; }).finally(() => { isSyncingFilterDates = false; });
        });

        filterDateInput.addEventListener('input', function() {
            if (isSyncingFilterDates) return;
            if (!this.value || this.value.length !== 10) return;
            isSyncingFilterDates = true;
            convertAdToBs(this.value).then(v => { filterDateBsInput.value = v || ''; }).finally(() => { isSyncingFilterDates = false; });
        });

        filterDateBsInput.addEventListener('change', function() {
            if (isSyncingFilterDates) return;
            if (!this.value) {
                filterDateInput.value = '';
                return;
            }
            isSyncingFilterDates = true;
            convertBsToAd(this.value).then(v => { filterDateInput.value = v || ''; }).finally(() => { isSyncingFilterDates = false; });
        });

        filterDateBsInput.addEventListener('input', function() {
            if (isSyncingFilterDates) return;
            if (!this.value || this.value.length !== 10) return;
            isSyncingFilterDates = true;
            convertBsToAd(this.value).then(v => { filterDateInput.value = v || ''; }).finally(() => { isSyncingFilterDates = false; });
        });

        if (filterDateInput.value) {
            isSyncingFilterDates = true;
            convertAdToBs(filterDateInput.value).then(v => { filterDateBsInput.value = v || ''; }).finally(() => { isSyncingFilterDates = false; });
        }
    });

    // Mark Attendance Modal
    let attendanceState = {};
    let isSyncingModalDates = false;

    function openMarkAttendanceModal() {
        const markAttendanceModal = document.getElementById('markAttendanceModal');
        const markDateInput = document.getElementById('mark_date');
        const markDateBsInput = document.getElementById('mark_date_bs');
        const markSubjectSelect = document.getElementById('mark_subject');

        if (markDateInput) markDateInput.value = '';
        if (markDateBsInput) markDateBsInput.value = '';
        if (markSubjectSelect) markSubjectSelect.value = '';

        markAttendanceModal.classList.remove('hidden');
        renderAttendanceTable([]);
    }

    function closeMarkAttendanceModal() {
        document.getElementById('markAttendanceModal').classList.add('hidden');
        renderAttendanceTable([]);
        document.getElementById('attendanceSummary').classList.add('hidden');
    }

    // Add event listener for date selection in modal
    document.addEventListener('DOMContentLoaded', function() {
        const markAttendanceModal = document.getElementById('markAttendanceModal');
        const markDateInput = document.getElementById('mark_date');
        const markDateBsInput = document.getElementById('mark_date_bs');

        if (markDateInput && markDateBsInput) {
            const syncAdToBs = value => {
                value = String(value || '').trim();
                if (!value) {
                    markDateBsInput.value = '';
                    return;
                }
                isSyncingModalDates = true;
                convertAdToBs(value).then(v => { 
                    markDateBsInput.value = v || ''; 
                }).finally(() => { isSyncingModalDates = false; });
            };

            const syncBsToAd = value => {
                value = String(value || '').trim();
                if (!value) {
                    markDateInput.value = '';
                    return;
                }
                isSyncingModalDates = true;
                convertBsToAd(value).then(v => { 
                    markDateInput.value = v || ''; 
                }).finally(() => { isSyncingModalDates = false; });
            };

            // AD -> BS conversion on change and input
            markDateInput.addEventListener('change', function() {
                if (isSyncingModalDates) return;
                syncAdToBs(this.value);
            });

            markDateInput.addEventListener('input', function() {
                if (isSyncingModalDates) return;
                syncAdToBs(this.value);
            });

            // BS -> AD conversion on change and input
            markDateBsInput.addEventListener('change', function() {
                if (isSyncingModalDates) return;
                syncBsToAd(this.value);
            });

            markDateBsInput.addEventListener('input', function() {
                if (isSyncingModalDates) return;
                syncBsToAd(this.value);
            });

            // Nepali datepicker sometimes updates the input value without firing native events.
            // Poll while the modal is open to keep AD date in sync with BS selection.
            let prev = markDateBsInput.value || '';
            setInterval(() => {
                if (!markAttendanceModal || markAttendanceModal.classList.contains('hidden')) {
                    prev = markDateBsInput.value || '';
                    return;
                }

                const now = markDateBsInput.value || '';
                if (now === prev) return;
                prev = now;

                if (isSyncingModalDates) return;
                syncBsToAd(now);
            }, 200);
        }
    });

    async function loadAttendanceStudents() {
        const date = document.getElementById('mark_date').value;
        const subjectId = document.getElementById('mark_subject').value;

        if (!date || !subjectId) {
            alert('Please select both date and subject');
            return;
        }

        const btn = document.getElementById('loadStudentsBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-spinner animate-spin"></i> Loading...';

        try {
            const res = await fetch('{{ route("teacher.attendance.students") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ subject_id: subjectId, date })
            });
            if (!res.ok) {
                const errText = await res.text();
                throw new Error(errText || 'Failed to load students');
            }
            const data = await res.json();

            const students = data.students || [];
            
            const saveBtn = document.getElementById('saveAllBtn');
            if (students.length > 0) {
                saveBtn.disabled = false;
            } else {
                saveBtn.disabled = true;
            }
            
            renderAttendanceTable(students, date);
        } catch (err) {
            console.error('Error loading students', err);
            showToast('Failed to load students', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-search"></i><span>Load Students</span>';
        }
    }

    // Bulk add attendance for all subjects today
    async function bulkAddAllAttendance() {
        try {
            const res = await fetch('{{ route("teacher.attendance.bulkAddAll") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            });
            const data = await res.json();
            if (data && data.success) {
                const inserted = data.inserted ?? 0;
                showToast('Bulk attendance added for ' + inserted + ' records', 'success');
                // Reload to reflect new data
                window.location.reload();
            } else {
                showToast((data?.message) || 'Bulk add failed', 'error');
            }
        } catch (e) {
            console.error(e);
            showToast('Bulk add failed', 'error');
        }
    }

    function renderAttendanceTable(students = [], date) {
        attendanceState = {};
        const tbody = document.getElementById('attendanceTbody');
        tbody.innerHTML = '';

        if (!students || students.length === 0) {
            document.getElementById('attendanceTableWrap').classList.add('hidden');
            document.getElementById('attendanceSummary').classList.add('hidden');
            return;
        }

        students.forEach(s => {
            const studentId = s.student_id || s.id;
            attendanceState[studentId] = { student_id: studentId, student: s, status: s.status || 'present' };

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-green-50 transition-colors';

            const nameTd = document.createElement('td');
            nameTd.className = 'px-3 py-2 text-xs';
            nameTd.innerHTML = `<div class="flex items-center gap-2"><div class="w-6 h-6 bg-gradient-to-br from-green-100 to-green-200 rounded-full flex items-center justify-center flex-shrink-0"><i class="bi bi-person-fill text-xs text-green-600"></i></div><div><div class="font-semibold text-gray-900 text-xs">${escapeHtml(s.name)}</div><div class="text-xs text-gray-500 truncate">${escapeHtml(s.email || '')}</div></div></div>`;

            const rollTd = document.createElement('td');
            rollTd.className = 'px-3 py-2 text-xs text-center font-medium text-gray-700';
            rollTd.textContent = s.roll_no || '-';

            const semTd = document.createElement('td');
            semTd.className = 'px-3 py-2 text-xs text-center text-gray-700';
            semTd.innerHTML = `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">${s.semester || '-'}</span>`;

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
                    <span class="text-xs font-medium ${isPresent ? 'text-green-700' : 'text-red-700'} status-badge">${isPresent ? 'Present' : 'Absent'}</span>
                </div>
            `;

            tr.appendChild(nameTd);
            tr.appendChild(rollTd);
            tr.appendChild(semTd);
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
                
                const wrap = this.nextElementSibling;
                const dot = wrap.querySelector('.dot');
                const badge = wrap.closest('td').querySelector('.status-badge');

                if (isChecked) {
                    wrap.classList.remove('bg-gray-200');
                    wrap.classList.add('bg-green-500');
                    dot.style.transform = 'translateX(20px)';
                    if (badge) {
                        badge.textContent = 'Present';
                        badge.classList.remove('text-red-700');
                        badge.classList.add('text-green-700');
                    }
                } else {
                    wrap.classList.remove('bg-green-500');
                    wrap.classList.add('bg-gray-200');
                    dot.style.transform = 'translateX(0)';
                    if (badge) {
                        badge.textContent = 'Absent';
                        badge.classList.remove('text-green-700');
                        badge.classList.add('text-red-700');
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

    function escapeHtml(unsafe) {
        return String(unsafe).replace(/[&<>"'`]/g, function (m) {
            return {'&':'&amp;','<':'<','>':'>','"':'"',"'":'&#39;','`':'&#x60;'}[m];
        });
    }

    async function saveAllAttendance() {
        const attendance = Object.values(attendanceState).map(s => ({ student_id: s.student_id, status: s.status }));
        const date = document.getElementById('mark_date').value;
        const dateBs = normalizeNepaliDigits(document.getElementById('mark_date_bs').value);
        const subjectId = document.getElementById('mark_subject').value;

        if (attendance.length === 0) {
            alert('No students to save');
            return;
        }

        if (!date || !subjectId) {
            alert('Please select date and subject');
            return;
        }

        try {
            const payload = { 
                attendance, 
                date,
                date_bs: dateBs,
                subject_id: subjectId
            };

            const res = await fetch('{{ route("teacher.attendance.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            
            if (data.success) {
                showToast('Attendance saved successfully for ' + attendance.length + ' students!', 'success');
                closeMarkAttendanceModal();
                setTimeout(() => { 
                    window.location.href = '{{ route("teacher.attendance") }}'; 
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to save attendances');
            }
        } catch (err) {
            console.error('Save error', err);
            showToast('Error saving attendance: ' + err.message, 'error');
        }
    }

    // View Subject Attendance Modal Functions
async function viewSubjectAttendance(date, subjectId, subjectName) {
        window.currentSubjectId = subjectId;
        window.currentDate = date;
        document.getElementById('viewSubjectTitle').textContent = subjectName + ' - ' + date;
        document.getElementById('viewSubjectStudentsBody').innerHTML = '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">Loading...</td></tr>';
        document.getElementById('viewSubjectModal').classList.remove('hidden');

        try {
            const params = new URLSearchParams();
            params.append('date', date);
            if (subjectId && subjectId !== 'null' && subjectId !== '') {
                params.append('subject_id', subjectId);
            }

            const url = '{{ route("teacher.attendance.students") }}';
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ date: date, subject_id: subjectId, marked_only: true })
            });
            const data = await res.json();

            const students = data.students || [];
            document.getElementById('viewSubjectStudentsCount').textContent = students.length;

            if (students.length === 0) {
                document.getElementById('viewSubjectStudentsBody').innerHTML = '<tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">No students found</td></tr>';
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
                    statusClass = 'bg-red-100 text-red-700';
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
            document.getElementById('viewSubjectStudentsBody').innerHTML = '<tr><td colspan="6" class="px-3 py-8 text-center text-red-500">Error loading attendance</td></tr>';
        }
    }

    function closeViewSubjectModal() {
        document.getElementById('viewSubjectModal').classList.add('hidden');
    }

    function printCurrentSubjectAttendance() {
        const sid = window.currentSubjectId || '';
        const d = window.currentDate || '';
        const url = '{{ route("teacher.attendance.print") }}' + '?subject_id=' + encodeURIComponent(sid) + '&date=' + encodeURIComponent(d);
        teacherOpenPrintPreview(url, {
            title: 'Print Attendance',
        });
    }

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
        editSubjectData.students = [];
        document.querySelectorAll('input[name="bulkStatus"]').forEach(input => {
            input.checked = false;
        });
        
        document.getElementById('editSubjectTitle').textContent = subjectName + ' - ' + date;
        document.getElementById('editSubjectStudentsBody').innerHTML = '<tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">Loading...</td></tr>';
        document.getElementById('editSubjectModal').classList.remove('hidden');

        try {
            const params = new URLSearchParams();
            params.append('date', date);
            if (subjectId && subjectId !== 'null' && subjectId !== '') {
                params.append('subject_id', subjectId);
            }

            const url = '{{ route("teacher.attendance.students") }}';
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ date: date, subject_id: subjectId, marked_only: true })
            });
            const data = await res.json();

            const students = data.students || [];
            editSubjectData.students = students;
            document.getElementById('editSubjectStudentsCount').textContent = students.length;

            if (students.length === 0) {
                document.getElementById('editSubjectStudentsBody').innerHTML = '<tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No students found</td></tr>';
                return;
            }

            let html = '';
            students.forEach(s => {
                let statusClass = '';
                if (s.status === 'present') statusClass = 'bg-green-100 text-green-700';
                else if (s.status === 'absent') statusClass = 'bg-red-100 text-red-700';
                else if (s.status === 'leave') statusClass = 'bg-purple-100 text-purple-700';
                else statusClass = 'bg-gray-100 text-gray-700';

                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-900 font-medium">${escapeHtml(s.name || 'N/A')}</td>
                        <td class="px-3 py-2 text-gray-600">${escapeHtml(s.email || '-')}</td>
                        <td class="px-3 py-2 text-center text-gray-700">${s.roll_no || '-'}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium ${statusClass}">${s.status || '-'}</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <select onchange="updateStudentStatus('${s.student_id}', this.value)" class="text-xs border border-gray-300 rounded px-2 py-1">
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
            document.getElementById('editSubjectStudentsBody').innerHTML = '<tr><td colspan="5" class="px-3 py-8 text-center text-red-500">Error loading attendance</td></tr>';
        }
    }

    function updateStudentStatus(studentId, newStatus) {
        const student = editSubjectData.students.find(s => String(s.student_id) === String(studentId));
        if (student) {
            student.new_status = String(student.status || '') === String(newStatus) ? null : newStatus;
        }
    }

    function closeEditSubjectModal() {
        document.getElementById('editSubjectModal').classList.add('hidden');
        document.querySelectorAll('input[name="bulkStatus"]').forEach(input => {
            input.checked = false;
        });
        editSubjectData = { date: '', date_bs: '', subject_id: '', subject_name: '', students: [] };
    }

    async function saveSubjectAttendance() {
        const bulkStatus = document.querySelector('input[name="bulkStatus"]:checked')?.value;
        const changedStatuses = new Map(
            editSubjectData.students
                .filter(s => s.new_status)
                .map(s => [String(s.student_id), s.new_status])
        );

        let attendance = [];

        if (bulkStatus) {
            attendance = editSubjectData.students.map(s => ({
                student_id: s.student_id,
                status: changedStatuses.get(String(s.student_id)) || bulkStatus
            }));
        } else {
            attendance = editSubjectData.students
                .filter(s => s.new_status)
                .map(s => ({ student_id: s.student_id, status: s.new_status }));
        }

        if (attendance.length === 0) {
            showToast('No changes to save', 'warning');
            return;
        }

        try {
            const payload = {
                attendance: attendance,
                date: editSubjectData.date,
                date_bs: editSubjectData.date_bs,
                subject_id: editSubjectData.subject_id
            };

            const res = await fetch('{{ route("teacher.attendance.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            
            if (data.success) {
                showToast('Attendance updated successfully!', 'success');
                closeEditSubjectModal();
                setTimeout(() => { window.location.reload(); }, 1000);
            } else {
                throw new Error(data.message || 'Failed to save');
            }
        } catch (err) {
            console.error('Save error', err);
            showToast('Error saving attendance: ' + err.message, 'error');
        }
    }

    // Close modals on background click
    document.getElementById('viewSubjectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeViewSubjectModal();
    });

    document.getElementById('editSubjectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditSubjectModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeViewSubjectModal();
            closeEditSubjectModal();
            closeMarkAttendanceModal();
        }
    });
</script>
@endsection
