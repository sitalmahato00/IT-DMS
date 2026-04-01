@extends('parent.layouts.parentlayout')

@section('title', __('Courses'))
@section('subtitle', __('Enrolled subjects, teaching contacts, and course load overview'))

@section('content')
<div class="space-y-6">
    @include('parent.partials.child-tabs', [
        'children' => $children,
        'selectedChildId' => $selectedChildId,
        'routeName' => 'parent.courses',
    ])

    @if(!$selectedChild)
        <div class="rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No course data is available because no students are linked to this parent account yet.') }}
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Total Subjects') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['subject_count'] }}</p>
            </div>
            <div class="rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Lab Subjects') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['subjects']->where('has_lab', true)->count() }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Pass Ready') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['passed_subjects'] }}</p>
            </div>
            <div class="rounded-xl border border-violet-200 dark:border-violet-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300 font-semibold">{{ __('Upcoming Exams') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['upcoming_exams']->count() }}</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($selectedChild['subjects'] as $subject)
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }} • {{ __('Semester :semester', ['semester' => $subject['semester'] ?: '—']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['course'] ?: __('Course category pending') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $subject['has_lab'] ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/30 dark:text-sky-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300' }}">
                            {{ $subject['has_lab'] ? __('Lab') : __('Theory') }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Teacher') }}</p>
                            <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $subject['teacher_name'] }}</p>
                            @if($subject['teacher_email'])
                                <a href="mailto:{{ $subject['teacher_email'] }}" class="mt-2 inline-flex items-center gap-2 text-xs text-red-700 dark:text-red-300 hover:underline">
                                    <i class="bi bi-envelope"></i>
                                    <span>{{ $subject['teacher_email'] }}</span>
                                </a>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Attendance') }}</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $subject['attendance_percentage'] }}%</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Result') }}</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $subject['percentage'] !== null ? $subject['percentage'] . '%' : '—' }}</p>
                            </div>
                        </div>

                        @if($subject['description'])
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                                <p class="mt-1 text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit(strip_tags($subject['description']), 140) }}</p>
                            </div>
                        @endif

                        @if($subject['next_exam'])
                            <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 px-4 py-3 text-sm text-red-700 dark:text-red-200">
                                {{ __('Next exam: :date', ['date' => $subject['next_exam']['date_label']]) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

