@extends('parent.layouts.parentlayout')

@section('title', __('Marks / Results'))
@section('subtitle', __('Published exam outcomes, subject performance, and upcoming assessments'))

@section('content')
<div class="space-y-6">
    @include('parent.partials.child-tabs', [
        'children' => $children,
        'selectedChildId' => $selectedChildId,
        'routeName' => 'parent.results',
    ])

    @if(!$selectedChild)
        <div class="rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No result data is available because no students are linked to this parent account yet.') }}
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Overall Score') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['overall_percentage'] !== null ? $selectedChild['overall_percentage'] . '%' : '—' }}</p>
            </div>
            <div class="rounded-xl border border-violet-200 dark:border-violet-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300 font-semibold">{{ __('CGPA') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['cgpa'] !== null ? number_format($selectedChild['cgpa'], 2) : '—' }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Passed Subjects') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['passed_subjects'] }}</p>
            </div>
            <div class="rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Need Attention') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['failed_subjects'] + $selectedChild['pending_subjects'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Subject Result Summary') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Performance status for each subject, with next exam context where available.') }}</p>
                </div>

                <div class="p-5 grid gap-4 md:grid-cols-2">
                    @foreach($selectedChild['subjects'] as $subject)
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }} • {{ $subject['teacher_name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['published_label'] }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $subject['status'] === 'pass' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : ($subject['status'] === 'fail' ? 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200') }}">
                                    {{ $subject['status_label'] }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-3">
                                <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Marks') }}</p>
                                    <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $subject['obtained_marks'] !== null ? $subject['obtained_marks'] : '—' }}</p>
                                </div>
                                <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Full') }}</p>
                                    <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $subject['full_marks'] !== null ? $subject['full_marks'] : '—' }}</p>
                                </div>
                                <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                    <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Score') }}</p>
                                    <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $subject['percentage'] !== null ? $subject['percentage'] . '%' : '—' }}</p>
                                </div>
                            </div>

                            @if($subject['next_exam'])
                                <div class="mt-3 rounded-lg border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 px-3 py-2 text-sm text-red-700 dark:text-red-200">
                                    {{ __('Next exam: :date (:countdown)', ['date' => $subject['next_exam']['date_label'], 'countdown' => $subject['next_exam']['countdown_label']]) }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="xl:col-span-5 space-y-6">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Result Guidance') }}</h2>
                    <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                        <li>{{ __('Check this page whenever new exam notices are published.') }}</li>
                        <li>{{ __('Compare low attendance subjects with weak result areas to decide where support is needed.') }}</li>
                        <li>{{ __('Use communication tools for teacher follow-up whenever a subject remains pending or below benchmark.') }}</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Upcoming Exams') }}</h2>
                        <a href="{{ route('parent.events', ['child' => $selectedChild['id']]) }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Open schedule') }}</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($selectedChild['upcoming_exams']->take(4) as $exam)
                            <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $exam['exam_name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $exam['subject_name'] }}</p>
                                <div class="mt-3 flex items-center justify-between gap-3 text-sm">
                                    <span class="text-gray-600 dark:text-gray-300">{{ $exam['date_label'] }}</span>
                                    <span class="rounded-full bg-white dark:bg-slate-800 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-200 border border-red-200 dark:border-red-900">{{ $exam['countdown_label'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No upcoming exams are scheduled right now.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Result Entries') }}</h2>
                <a href="{{ route('parent.communication') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Need a meeting?') }}</a>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($selectedChild['recent_results'] as $entry)
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $entry['label'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $entry['subject_name'] }} • {{ $entry['date_label'] }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $entry['status'] === 'pass' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : ($entry['status'] === 'fail' ? 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200') }}">
                                {{ $entry['status_label'] }}
                            </span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Marks') }}</p>
                                <p class="mt-1 font-bold text-gray-900 dark:text-white">{{ $entry['obtained_marks'] }} / {{ $entry['full_marks'] }}</p>
                            </div>
                            <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Percentage') }}</p>
                                <p class="mt-1 font-bold text-gray-900 dark:text-white">{{ $entry['percentage'] !== null ? $entry['percentage'] . '%' : '—' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400 md:col-span-2 xl:col-span-3">
                        {{ __('No published result entries are available yet.') }}
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection

