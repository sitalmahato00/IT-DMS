@extends('student.layouts.studentlayout')

@section('title', __('Published Exams'))
@section('subtitle', __('Open each published exam marksheet'))

@section('content')
@php
    $examCount = $examGroups->count();
    $passedCount = $examGroups->where('status', 'pass')->count();
    $failedCount = $examGroups->where('status', 'fail')->count();
    $pendingCount = $examGroups->where('status', 'pending')->count();
    $averagePercentage = $examGroups->filter(fn ($exam) => !is_null($exam['percentage']))
        ->avg('percentage');
@endphp

<div class="student-smooth-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <div class="student-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/20 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-16 w-56 h-56 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">{{ __('Published Exams') }}</h1>
                <p class="text-[#ffe5ea] mt-2">{{ __('Review each published exam and open its printable marksheet.') }}</p>
                <div class="mt-3 flex flex-wrap gap-3 text-sm text-[#ffe5ea]">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-journal-check"></i> {{ $examCount }} {{ __('exams') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-graph-up"></i> {{ is_null($averagePercentage) ? '—' : number_format($averagePercentage, 1) . '%' }} {{ __('average') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-check2-circle"></i> {{ $passedCount }} {{ __('passed') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('student.marks') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#B2002F] shadow-md hover:bg-[#fff1f3] transition">
                    <i class="bi bi-clipboard-data"></i>
                    <span>{{ __('Back to Results') }}</span>
                </a>
                <div class="hidden lg:flex items-center justify-center w-24 h-24 rounded-3xl bg-white/10 border border-white/15 shadow-lg">
                    <i class="bi bi-journal-text text-5xl text-white/90"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="student-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Exam Sheets') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $examCount }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Published marksheets available') }}</p>
        </div>
        <div class="student-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Passed') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $passedCount }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Exams with passing results') }}</p>
        </div>
        <div class="student-smooth-card rounded-xl border border-amber-200 dark:border-amber-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">{{ __('Pending') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Awaiting publication or review') }}</p>
        </div>
        <div class="student-smooth-card rounded-xl border border-blue-200 dark:border-blue-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Average') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ is_null($averagePercentage) ? '—' : number_format($averagePercentage, 1) . '%' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Across published exams') }}</p>
        </div>
    </div>

    @if($examGroups->isEmpty())
        <div class="student-smooth-empty rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-10 text-center">
            <i class="bi bi-journal-x text-4xl text-gray-300 dark:text-gray-600"></i>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-4">{{ __('No Published Exams') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Your published exam marksheets will appear here once the results are posted.') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @foreach($examGroups as $exam)
                @php
                    $statusClasses = match ($exam['status']) {
                        'pass' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300',
                        'fail' => 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300',
                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300',
                    };
                @endphp
                <div class="student-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $exam['label'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $exam['category'] }} • {{ $exam['date_label'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $exam['subject_count'] }} {{ __('subjects') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                            {{ $exam['status_label'] }}
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="rounded-lg bg-gray-50 dark:bg-slate-900/40 p-3 border border-gray-200 dark:border-slate-700">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Marks') }}</p>
                            <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ number_format((float) $exam['obtained_marks'], 2) }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-slate-900/40 p-3 border border-gray-200 dark:border-slate-700">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Full') }}</p>
                            <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ number_format((float) $exam['full_marks'], 2) }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 dark:bg-slate-900/40 p-3 border border-gray-200 dark:border-slate-700">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Score') }}</p>
                            <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $exam['percentage'] !== null ? number_format((float) $exam['percentage'], 2) . '%' : '—' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        @foreach($exam['rows']->take(3) as $row)
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-gray-50 dark:bg-slate-900/40 border border-gray-200 dark:border-slate-700 px-3 py-2 text-sm">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $row['subject_name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $row['subject_code'] ?: __('Subject') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $row['obtained_marks'] }} / {{ number_format((float) $row['full_marks'], 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $row['status'] === 'pass' ? __('Pass') : ($row['status'] === 'fail' ? __('Fail') : __('Pending')) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="{{ route('student.marksheet', ['exam_id' => $exam['exam_id']]) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                            <i class="bi bi-printer"></i>
                            <span>{{ __('Open Marksheet') }}</span>
                        </a>
                        <a href="{{ route('student.marks') }}" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                            <i class="bi bi-clipboard-data"></i>
                            <span>{{ __('View Results') }}</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
