@extends('teacher.layouts.teacherlayout')

@section('title', __('Exams'))

@section('content')
<div class="teacher-smooth-page teacher-exams-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Page Header -->
    <div class="teacher-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="teacher-page-header-title text-2xl font-bold text-gray-800 dark:text-white">{{ __('Exams') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('View and manage exams for your subjects.') }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="teacher-stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Exams') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] ?? ($exams->total() ?? $exams->count() ?? 0) }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-file-earmark-text text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Upcoming') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $upcomingCount ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-calendar-event text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Completed') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $completedCount ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Subjects') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjects->count() ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400">
                    <i class="bi bi-book text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="teacher-filter-panel bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('teacher.exams') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Semester') }}</label>
                    <select name="semester" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach($semesterOptions as $value => $label)
                            <option value="{{ $value }}" {{ (string)$selectedSemester === (string)$value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                    <select name="subject" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Subjects') }}</option>
                        @if($subjects->isNotEmpty())
                            @foreach($subjects as $subject)
                                <option value="{{ $subject['id'] }}" {{ (string) ($selectedSubject ?? request('subject', request('subject_id'))) === (string) $subject['id'] ? 'selected' : '' }}>
                                    {{ $subject['code'] }} - {{ $subject['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                    <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="marks_filled" {{ request('status') == 'marks_filled' ? 'selected' : '' }}>{{ __('Marks Filled') }}</option>
                        <option value="marks_not_filled" {{ request('status') == 'marks_not_filled' ? 'selected' : '' }}>{{ __('Marks Not Filled') }}</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>{{ __('Archived') }}</option>
                        <option value="faculty" {{ request('status') == 'faculty' ? 'selected' : '' }}>{{ __('Faculty') }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ request('q', request('search')) }}" placeholder="{{ __('Exam Name') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Per Page') }}</label>
                    <select name="per_page" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 justify-between flex-wrap pt-2">
                <div class="flex gap-2 flex-wrap">
                    <button type="submit" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium shadow-sm">
                        <i class="bi bi-funnel"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('teacher.exams') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Exams Table -->
    <div class="teacher-smooth-table-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($exams->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3">{{ __('Exam Name') }}</th>
                            <th class="px-4 py-3">{{ __('Subject') }}</th>
                            <th class="px-4 py-3">{{ __('Date (AD)') }}</th>
                            <th class="px-4 py-3">{{ __('Date (BS)') }}</th>
                            <th class="px-4 py-3">{{ __('Total Marks') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($exams as $exam)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-4">
                                @php
                                    $assessmentPrefix = $exam->formatted_assessment ? $exam->formatted_assessment . ' - ' : '';
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="teacher-smooth-icon w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $assessmentPrefix }}{{ $exam->exam_name }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $exam->subject?->subject_code ?? '—' }} - {{ $exam->subject?->subject_name ?? __('Unassigned') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $exam->exam_date_bs ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $exam->full_marks ?? 0 }}
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $status = $exam->status ?? 'draft';
                                    $statusClass = match($status) {
                                        'published' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                        'draft' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                        'archived' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
                                        'faculty' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                                        default => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $exam->formatted_status ?? ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('teacher.exams.show', $exam->id) }}" class="teacher-action-pill inline-flex items-center gap-1 px-2 py-1 text-xs text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 rounded transition" title="Upload Marks">
                                    <i class="bi bi-cloud-upload"></i> {{ __('Upload Marks') }}
                                </a>
                                <a href="{{ route('teacher.marks', ['subject_id' => $exam->subject_id ?? '', 'category' => $exam->exam_category ?? 'assessment', 'assessment_id' => $exam->id]) }}" class="teacher-action-pill inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 rounded transition mt-1 block">
                                    <i class="bi bi-eye"></i> {{ __('View Marks') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $exams->links() }}
            </div>
        @else
            <div class="teacher-smooth-empty p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-file-earmark-text text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Exams Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No exams match your filter criteria.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

