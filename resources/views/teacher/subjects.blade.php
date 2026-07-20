@extends('teacher.layouts.teacherlayout')

@section('title', __('My Subjects'))

@section('styles')
<style>
    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-filter-count {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: linear-gradient(180deg, rgba(255, 241, 242, 0.94), rgba(255, 250, 251, 0.98));
        border: 1px solid rgba(251, 207, 232, 0.9);
        color: #9f1239;
        font-weight: 600;
        box-shadow: 0 14px 28px -24px rgba(148, 19, 52, 0.32);
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-stat {
        position: relative;
        overflow: hidden;
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-stat::after {
        content: "";
        position: absolute;
        inset: auto -20% -54% auto;
        width: 7rem;
        height: 7rem;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(251, 113, 133, 0.16), rgba(251, 113, 133, 0));
        pointer-events: none;
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-stat--red {
        background: linear-gradient(180deg, rgba(255, 250, 250, 0.98), rgba(255, 242, 244, 0.98));
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-stat--blue {
        background: linear-gradient(180deg, rgba(248, 252, 255, 0.98), rgba(239, 246, 255, 0.98));
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-stat--purple {
        background: linear-gradient(180deg, rgba(251, 248, 255, 0.98), rgba(245, 243, 255, 0.98));
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-stat--amber {
        background: linear-gradient(180deg, rgba(255, 252, 245, 0.98), rgba(255, 247, 237, 0.98));
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-table-shell {
        box-shadow: 0 28px 56px -40px rgba(15, 23, 42, 0.22);
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-table-head {
        background: linear-gradient(180deg, #fff7f8, #fffdfd);
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subject-row {
        transition: background-color 0.18s ease, transform 0.18s ease;
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subject-row:hover {
        background: linear-gradient(90deg, rgba(255, 241, 242, 0.84), rgba(255, 255, 255, 0.98));
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subject-icon {
        box-shadow: 0 18px 30px -24px rgba(220, 38, 38, 0.32);
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subject-action {
        box-shadow: 0 16px 28px -22px rgba(220, 38, 38, 0.34);
    }

    html.teacher-ui-enhanced:not(.dark) .teacher-subjects-page .teacher-subjects-empty {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(255, 249, 250, 0.96));
    }

    html.teacher-ui-enhanced.dark .teacher-subjects-page .teacher-subjects-filter-count {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.72);
        border: 1px solid rgba(248, 113, 113, 0.14);
        color: #fecaca;
    }
</style>
@endsection

@section('content')
@php
    $subjectListQuery = request()->except('page');
    $subjectsExportUrl = route('teacher.subjects.export', $subjectListQuery);
    $subjectsPrintUrl = route('teacher.subjects.print', $subjectListQuery);
@endphp
<div class="teacher-subjects-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed top-4 right-4 z-50"></div>

    <!-- Page Header -->
    <div class="teacher-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="teacher-page-header-title text-2xl font-bold text-gray-800 dark:text-white">{{ __('My Subjects') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('View and manage your assigned subjects.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick='teacherOpenPrintPreview(@json($subjectsPrintUrl), { title: @json(__('Print Subjects')) })' class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                <i class="bi bi-printer"></i> {{ __('Print') }}
            </button>
            <a href="{{ $subjectsExportUrl }}" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition font-medium">
                <i class="bi bi-download"></i> {{ __('Export CSV') }}
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="teacher-filter-panel teacher-subjects-filter bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('teacher.subjects') }}" class="space-y-4">
            <!-- Filter Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <!-- Semester Filter -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Semester') }}</label>
                    <select name="semester" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Semesters') }}</option>
                        @foreach(($availableSemesters ?? []) as $sem)
                            <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>{{ __('Semester ') . $sem }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Subject Name or Code') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Per Page -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Per Page') }}</label>
                    <select name="per_page" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 justify-between flex-wrap pt-2">
                <div class="flex gap-2 flex-wrap">
                    <button type="submit" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium shadow-sm">
                        <i class="bi bi-funnel"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('teacher.subjects') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
                <div class="teacher-subjects-filter-count text-sm text-gray-500 dark:text-gray-400">
                    <i class="bi bi-book-half"></i>
                    {{ $subjectAssignments->count() }} {{ __('subjects found') }}
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="teacher-stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="teacher-stat-card teacher-subjects-stat teacher-subjects-stat--red bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Subjects') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjectAssignments->count() ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card teacher-subjects-stat teacher-subjects-stat--blue bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Students') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjectAssignments->sum('student_count') ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-people text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card teacher-subjects-stat teacher-subjects-stat--purple bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Courses') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $courses->count() ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-collection text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card teacher-subjects-stat teacher-subjects-stat--amber bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Avg. Attendance') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        @php
                            $avgAttendance = $subjectAssignments->count() > 0 
                                ? round($subjectAssignments->avg('attendance_rate'), 1) 
                                : 0;
                        @endphp
                        {{ $avgAttendance }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400">
                    <i class="bi bi-pie-chart text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="teacher-subjects-table-shell bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="teacher-subjects-table-head p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('Assigned Subjects') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Course, semester, student count, and attendance overview for your assigned subjects.') }}</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-white/80 dark:bg-gray-700/80 px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                <i class="bi bi-collection"></i>
                {{ $subjectAssignments->count() }} {{ __('records') }}
            </span>
        </div>
        
        @if($subjectAssignments->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Subject') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Code') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Course') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Semester') }}</th>
                            <th class="px-4 py-3 text-center {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Students') }}</th>
                            <th class="px-4 py-3 text-center {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Attendance') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Role') }}</th>
                            <th class="px-4 py-3 text-center {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($subjectAssignments as $assignment)
                            <tr class="teacher-subject-row hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="teacher-subject-icon w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400 flex-shrink-0">
                                            <i class="bi bi-book"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment['subject_name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment['created_at']->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $assignment['subject_code'] ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $assignment['course_name'] ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ __('Semester ') . ($assignment['semester'] ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $assignment['student_count'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $rate = $assignment['attendance_rate'] ?? 0;
                                        $badgeClass = $rate >= 80 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 
                                                     ($rate >= 60 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' : 
                                                     'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $rate }}%
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300">
                                        {{ ucfirst($assignment['role'] ?? 'Teacher') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('teacher.subjects.show', $assignment['assignment_id']) }}" class="teacher-action-pill teacher-subject-action inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors">
                                        <i class="bi bi-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="teacher-subjects-empty p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-book text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Subjects Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No subjects match your filter criteria.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

