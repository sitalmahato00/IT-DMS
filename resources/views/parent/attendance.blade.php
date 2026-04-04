@extends('parent.layouts.parentlayout')

@section('title', __('Attendance'))
@section('subtitle', __('Attendance made easy to check'))

@section('content')
<div class="parent-smooth-page space-y-6">
    @include('parent.partials.child-tabs', [
        'children' => $children,
        'selectedChildId' => $selectedChildId,
        'routeName' => 'parent.attendance',
    ])

    @if(!$selectedChild)
        <div class="parent-smooth-empty rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No attendance data is available because no students are linked to this parent account yet.') }}
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="parent-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Overall Attendance') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['attendance_percentage'] }}%</p>
            </div>
            <div class="parent-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Subjects') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['subject_count'] }}</p>
            </div>
            <div class="parent-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Below 75%') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['subjects']->where('attendance_percentage', '<', 75)->count() }}</p>
            </div>
            <div class="parent-smooth-card rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Recent') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['recent_attendance']->count() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="parent-smooth-table-card xl:col-span-7 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Attendance By Subject') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Present, absent, and total attendance for each subject.') }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Subject') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Teacher') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Present') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Absent') }}</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Attendance %') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($selectedChild['subjects'] as $subject)
                                <tr class="bg-white dark:bg-gray-800">
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $subject['teacher_name'] }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $subject['attendance_present'] }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $subject['attendance_absent'] }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $subject['attendance_percentage'] >= 75 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' }}">
                                            {{ $subject['attendance_percentage'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="xl:col-span-5 space-y-6">
                <div class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Needs Attention') }}</h2>
                    <div class="mt-4 space-y-3">
                        @php
                            $lowAttendanceSubjects = $selectedChild['subjects']->where('attendance_percentage', '<', 75);
                        @endphp
                        @forelse($lowAttendanceSubjects as $subject)
                            <div class="parent-smooth-list-card rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300">{{ $subject['name'] }}</p>
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ __('Attendance is :value%. It may need follow-up.', ['value' => $subject['attendance_percentage']]) }}</p>
                            </div>
                        @empty
                            <div class="parent-smooth-list-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                                <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ __('No low-attendance subjects detected.') }}</p>
                                <p class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">{{ __('Attendance is okay in all subjects right now.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('What To Do') }}</h2>
                    <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                        <li>{{ __('Check this page every week.') }}</li>
                        <li>{{ __('Contact the teacher if attendance goes below 75%.') }}</li>
                        <li>{{ __('Compare attendance with marks if a subject needs support.') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Latest Attendance') }}</h2>
                <a href="{{ route('parent.communication') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Need follow-up?') }}</a>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($selectedChild['recent_attendance'] as $record)
                    <div class="parent-smooth-list-card rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record['subject_name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $record['subject_code'] ?: '—' }} • {{ $record['date_label'] }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $record['status'] === 'present' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' }}">
                                {{ ucfirst($record['status']) }}
                            </span>
                        </div>
                        @if($record['remarks'])
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $record['remarks'] }}</p>
                        @endif
                    </div>
                @empty
                    <div class="parent-smooth-empty rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400 md:col-span-2 xl:col-span-3">
                        {{ __('No attendance entries are available yet.') }}
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
