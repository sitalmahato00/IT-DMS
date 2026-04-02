@extends('admin.layouts.app')

@section('title', __('Elective Subject Management'))

@section('styles')
<script>
    document.documentElement.classList.add('electives-ui-enhanced');
</script>
<style>
    html.electives-ui-enhanced:not(.dark) .electives-page {
        color: #0f172a;
    }

    html.electives-ui-enhanced:not(.dark) .electives-stats > .grid > div {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border-color: #f2d7de;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 247, 248, 0.96));
        box-shadow: 0 22px 48px -34px rgba(190, 24, 93, 0.45);
    }

    html.electives-ui-enhanced:not(.dark) .electives-stats > .grid > div::after {
        content: "";
        position: absolute;
        inset: auto -22% -55% auto;
        width: 7rem;
        height: 7rem;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(244, 114, 182, 0.18), rgba(244, 114, 182, 0));
        pointer-events: none;
    }

    html.electives-ui-enhanced:not(.dark) .electives-filter-panel,
    html.electives-ui-enhanced:not(.dark) .electives-table-panel,
    html.electives-ui-enhanced:not(.dark) .electives-pending-panel {
        border-radius: 28px;
        border-color: rgba(241, 213, 219, 0.95);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 250, 250, 0.97));
        box-shadow: 0 26px 54px -38px rgba(148, 19, 52, 0.38);
    }

    html.electives-ui-enhanced:not(.dark) .electives-table-head,
    html.electives-ui-enhanced:not(.dark) .electives-pending-head {
        background: linear-gradient(180deg, #fff5f7, #fffafb);
    }

    html.electives-ui-enhanced:not(.dark) .electives-table-row:hover,
    html.electives-ui-enhanced:not(.dark) .electives-pending-row:hover {
        background: linear-gradient(90deg, rgba(255, 241, 242, 0.72), rgba(255, 255, 255, 0.95));
    }

    html.electives-ui-enhanced:not(.dark) .elective-chip {
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    html.electives-ui-enhanced:not(.dark) .elective-count-badge {
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-weight: 700;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.4);
    }

    html.electives-ui-enhanced:not(.dark) .elective-enrollment-btn,
    html.electives-ui-enhanced:not(.dark) .elective-assign-btn,
    html.electives-ui-enhanced:not(.dark) .elective-approve-btn,
    html.electives-ui-enhanced:not(.dark) .elective-reject-btn {
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 16px 26px -18px rgba(15, 23, 42, 0.45);
    }

    html.electives-ui-enhanced:not(.dark) .electives-pending-header {
        background: linear-gradient(135deg, rgba(255, 247, 237, 0.96), rgba(254, 243, 199, 0.9));
    }

    html.electives-ui-enhanced:not(.dark) .elective-modal-panel {
        border-radius: 30px;
        border: 1px solid rgba(253, 230, 138, 0.55);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 250, 245, 0.98));
        box-shadow: 0 34px 70px -38px rgba(15, 23, 42, 0.45);
    }

    html.electives-ui-enhanced:not(.dark) .elective-modal-header {
        background: linear-gradient(135deg, #f97316, #dc2626);
        border-bottom: none;
    }

    html.electives-ui-enhanced:not(.dark) .elective-modal-close {
        color: rgba(255, 255, 255, 0.82);
        background: rgba(255, 255, 255, 0.12);
    }

    html.electives-ui-enhanced:not(.dark) .elective-modal-close:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.2);
    }

    html.electives-ui-enhanced:not(.dark) .elective-info-box {
        border-radius: 20px;
        border-color: rgba(147, 197, 253, 0.55);
        background: linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(224, 242, 254, 0.88));
    }

    html.electives-ui-enhanced:not(.dark) .elective-modal-footer {
        background: linear-gradient(180deg, #fff8f6, #fffdfd);
    }

    html.electives-ui-enhanced:not(.dark) .elective-secondary-btn,
    html.electives-ui-enhanced:not(.dark) .elective-primary-btn {
        border-radius: 999px;
        font-weight: 700;
        box-shadow: 0 16px 28px -20px rgba(15, 23, 42, 0.42);
    }

    html.electives-ui-enhanced:not(.dark) .electives-filter-panel select,
    html.electives-ui-enhanced:not(.dark) #assignStudentSelect {
        border-radius: 16px;
        border-color: #e5d4d9;
        background-color: #fffdfd;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    html.electives-ui-enhanced:not(.dark) .electives-filter-panel select:focus,
    html.electives-ui-enhanced:not(.dark) #assignStudentSelect:focus {
        border-color: #f43f5e;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.12);
    }
</style>
@endsection

@section('content')
<div class="electives-page space-y-6">

    <!-- Page Header -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Elective Subject Management
                </h1>
                <nav class="flex items-center gap-2 text-xs mt-0.5">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                        Dashboard
                    </a>
                    <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                        Elective Subjects
                    </span>
                </nav>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('admin.courses') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition">
                    <i class="bi bi-book-half"></i>
                    <span>Manage Subjects</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="electives-stats">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-red-200 dark:border-red-800 bg-white dark:bg-slate-800 p-4">
            <div class="flex items-center gap-2 mb-1">
                <i class="bi bi-shuffle text-red-500"></i>
                <p class="text-xs uppercase tracking-wide text-red-600 dark:text-red-400 font-semibold">Total Electives</p>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_electives'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Active elective subjects</p>
        </div>
        <div class="rounded-xl border border-green-200 dark:border-green-800 bg-white dark:bg-slate-800 p-4">
            <div class="flex items-center gap-2 mb-1">
                <i class="bi bi-door-open text-green-500"></i>
                <p class="text-xs uppercase tracking-wide text-green-600 dark:text-green-400 font-semibold">Open Enrollment</p>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['open_enrollment'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Accepting students</p>
        </div>
        <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-white dark:bg-slate-800 p-4">
            <div class="flex items-center gap-2 mb-1">
                <i class="bi bi-people text-blue-500"></i>
                <p class="text-xs uppercase tracking-wide text-blue-600 dark:text-blue-400 font-semibold">Enrolled Students</p>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_enrolled'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Approved enrollments</p>
        </div>
        <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-white dark:bg-slate-800 p-4">
            <div class="flex items-center gap-2 mb-1">
                <i class="bi bi-hourglass-split text-amber-500"></i>
                <p class="text-xs uppercase tracking-wide text-amber-600 dark:text-amber-400 font-semibold">Pending</p>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_approvals'] }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Awaiting approval</p>
        </div>
    </div>
    </div>

    <!-- Filter Card -->
    <div class="electives-filter-panel bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4 mb-4">
        <form method="GET" action="{{ route('admin.electives') }}" class="space-y-3">
            <!-- Filter Inputs Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Semester</label>
                    <select name="semester" id="filter_semester" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-md text-sm bg-white dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Semesters</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>Semester {{ $sem }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div class="flex gap-2 items-center">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                        <i class="bi bi-funnel"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.electives') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-md text-sm font-medium transition shadow-sm">
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Elective Subjects Table -->
    <div class="electives-table-panel bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">

        @if($electiveSubjects->isEmpty())
            <div class="p-16 text-center">
                <i class="bi bi-shuffle text-5xl text-gray-200 dark:text-gray-600 block mb-4"></i>
                <h3 class="text-base font-semibold text-gray-600 dark:text-gray-300 mb-2">{{ __('No Elective Subjects Found') }}</h3>
                <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">{{ __('Mark subjects as "Elective" or "Optional" in Subject Management to see them here.') }}</p>
                <a href="{{ route('admin.courses') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    <i class="bi bi-book-half"></i> {{ __('Go to Subjects') }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="electives-table w-full text-sm">
                    <thead class="electives-table-head bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Subject') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Semester') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Teacher') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Capacity') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Enrolled') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Pending') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Enrollment') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700" id="electivesTableBody">
                        @foreach($electiveSubjects as $subject)
                            <tr class="electives-table-row hover:bg-gray-50 dark:hover:bg-slate-700/50 transition elective-row" data-semester="{{ $subject->semester }}">
                                <td class="px-6 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $subject->subject_name }}</p>
                                    @if($subject->subject_code)
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $subject->subject_code }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($subject->subject_type === 'elective')
                                        <span class="elective-chip inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                            <i class="bi bi-shuffle"></i> {{ __('Elective') }}
                                        </span>
                                    @else
                                        <span class="elective-chip inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            <i class="bi bi-list-stars"></i> {{ __('Optional') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ __('Sem') }} {{ $subject->semester }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $primaryTeacher = $subject->teacherAssignments->first();
                                    @endphp
                                    @if($primaryTeacher && $primaryTeacher->teacher && $primaryTeacher->teacher->user)
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $primaryTeacher->teacher->user->name }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ __('Not assigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <span class="text-sm {{ $subject->max_students ? 'text-gray-700 dark:text-gray-300 font-medium' : 'text-gray-400 dark:text-gray-500' }}">
                                        {{ $subject->max_students ? $subject->max_students : '∞' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                        <span class="elective-count-badge px-2 py-0.5 rounded-full text-xs font-semibold {{ $subject->max_students && $subject->approved_count >= $subject->max_students ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                                        {{ $subject->approved_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($subject->pending_count > 0)
                                        <span class="elective-count-badge px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                            {{ $subject->pending_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <button onclick="toggleEnrollment({{ $subject->id }}, {{ $subject->is_elective_open ? 'true' : 'false' }})"
                                        class="elective-enrollment-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $subject->is_elective_open ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200 dark:hover:bg-green-900/50' : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-slate-600' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $subject->is_elective_open ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                        {{ $subject->is_elective_open ? __('Open') : __('Closed') }}
                                    </button>
                                </td>
                                <td class="px-6 py-3">
                                    <button onclick="assignStudent({{ $subject->id }}, '{{ $subject->subject_name }}', {{ $subject->semester }}, {{ $subject->max_students ?? 'null' }}, {{ $subject->approved_count }})"
                                        class="elective-assign-btn inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 rounded-lg text-xs font-medium transition">
                                        <i class="bi bi-person-plus"></i>
                                        {{ __('Assign') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Pending Approvals -->
    @if($pendingEnrollments->count() > 0)
        <div class="electives-pending-panel bg-white dark:bg-slate-800 rounded-xl border border-amber-200 dark:border-amber-800 overflow-hidden">
            <div class="electives-pending-header px-5 py-4 border-b border-amber-100 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 flex items-center gap-2">
                <i class="bi bi-clock text-amber-600 dark:text-amber-400"></i>
                <h3 class="text-sm font-bold text-amber-900 dark:text-amber-300">{{ __('Pending Elective Approvals') }}</h3>
                <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white">
                    {{ $pendingEnrollments->total() }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="electives-pending-head bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Student') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Elective Subject') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Semester') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Requested') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach($pendingEnrollments as $enrollment)
                            <tr class="electives-pending-row hover:bg-gray-50 dark:hover:bg-slate-700/50 transition" id="enrollment-row-{{ $enrollment->id }}">
                                <td class="px-6 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $enrollment->student->user->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Roll') }}: {{ $enrollment->student->roll_no ?? '—' }}</p>
                                </td>
                                <td class="px-6 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $enrollment->subject->subject_name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $enrollment->subject->subject_code ?? '' }}</p>
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ __('Sem') }} {{ $enrollment->semester }}</td>
                                <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $enrollment->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <button onclick="approveEnrollment({{ $enrollment->id }})"
                                            class="elective-approve-btn inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition">
                                            <i class="bi bi-check2"></i> {{ __('Approve') }}
                                        </button>
                                        <button onclick="rejectEnrollment({{ $enrollment->id }})"
                                            class="elective-reject-btn inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-medium transition">
                                            <i class="bi bi-x"></i> {{ __('Reject') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($pendingEnrollments->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $pendingEnrollments->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Assign Student Modal -->
<div id="assignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="elective-modal-panel bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="elective-modal-header px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="text-base font-semibold text-white">{{ __('Assign Student to Elective') }}</h3>
            <button onclick="closeAssignModal()" class="elective-modal-close p-1.5 rounded-lg transition">
                <i class="bi bi-x text-lg"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="elective-info-box p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                <p class="text-sm font-semibold text-blue-800 dark:text-blue-300" id="assignSubjectName"></p>
                <p class="text-xs text-blue-600 mt-0.5" id="assignCapacityInfo"></p>
            </div>
            <input type="hidden" id="assignSubjectId">
            <input type="hidden" id="assignSemester">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Select Student') }}</label>
                <select id="assignStudentSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('-- Select Student --') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" data-semester="{{ $student->semester }}">
                            {{ $student->user->name ?? '—' }} ({{ __('Sem') }} {{ $student->semester }}, Roll: {{ $student->roll_no }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="elective-modal-footer px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
            <button onclick="closeAssignModal()" class="elective-secondary-btn px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-100 text-gray-700 transition">
                {{ __('Cancel') }}
            </button>
            <button onclick="confirmAssign()" class="elective-primary-btn px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                <i class="bi bi-person-plus mr-1"></i> {{ __('Assign') }}
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function filterBySemester(semester) {
    const rows = document.querySelectorAll('.elective-row');
    rows.forEach(row => {
        if (!semester || row.dataset.semester === semester) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

function assignStudent(subjectId, subjectName, semester, maxStudents, approvedCount) {
    document.getElementById('assignSubjectId').value = subjectId;
    document.getElementById('assignSemester').value = semester;
    document.getElementById('assignSubjectName').textContent = subjectName;
    const capacityText = maxStudents 
        ? `{{ __("Capacity:") }} ${approvedCount}/${maxStudents} {{ __("students enrolled") }}`
        : `{{ __("No capacity limit.") }} ${approvedCount} {{ __("students enrolled.") }}`;
    document.getElementById('assignCapacityInfo').textContent = capacityText;
    document.getElementById('assignModal').classList.remove('hidden');
}

async function confirmAssign() {
    const studentId = document.getElementById('assignStudentSelect').value;
    const subjectId = document.getElementById('assignSubjectId').value;
    const semester = document.getElementById('assignSemester').value;
    if (!studentId) {
        showToast('{{ __("Please select a student.") }}', 'warning');
        return;
    }
    try {
        showLoading('Assigning...');
        const res = await fetch('/admin/electives/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ student_id: studentId, subject_id: subjectId, semester: semester }),
        });
        const result = await res.json();
        hideLoading();
        closeAssignModal();
        if (result.success) {
            showToast('{{ __("Student assigned successfully!") }}', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(result.message || 'Error assigning student.', 'error');
        }
    } catch (e) {
        hideLoading();
        showToast('An error occurred.', 'error');
    }
}

async function toggleEnrollment(subjectId, isOpen) {
    try {
        const res = await fetch('/admin/electives/toggle-enrollment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ subject_id: subjectId }),
        });
        const result = await res.json();
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(result.message || 'Error toggling enrollment.', 'error');
        }
    } catch (e) {
        showToast('An error occurred.', 'error');
    }
}

async function approveEnrollment(id) {
    try {
        showLoading('Approving...');
        const res = await fetch(`/admin/electives/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
        });
        const result = await res.json();
        hideLoading();
        if (result.success) {
            showToast('{{ __("Enrollment approved!") }}', 'success');
            document.getElementById(`enrollment-row-${id}`)?.remove();
        } else {
            showToast(result.message || 'Error approving.', 'error');
        }
    } catch (e) {
        hideLoading();
        showToast('An error occurred.', 'error');
    }
}

async function rejectEnrollment(id) {
    if (!confirm('{{ __("Are you sure you want to reject this enrollment?") }}')) return;
    try {
        showLoading('Rejecting...');
        const res = await fetch(`/admin/electives/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
        });
        const result = await res.json();
        hideLoading();
        if (result.success) {
            showToast('{{ __("Enrollment rejected.") }}', 'success');
            document.getElementById(`enrollment-row-${id}`)?.remove();
        } else {
            showToast(result.message || 'Error rejecting.', 'error');
        }
    } catch (e) {
        hideLoading();
        showToast('An error occurred.', 'error');
    }
}

document.getElementById('assignModal').addEventListener('click', function(e) {
    if (e.target === this) closeAssignModal();
});
</script>
@endsection
