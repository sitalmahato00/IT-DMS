@extends('parent.layouts.parentlayout')

@section('title', __('Exam Marksheet'))
@section('subtitle', __('Open published exam marksheets for your child'))

@section('content')
@php
    $examCount = $examGroups->count();
    $passedCount = $examGroups->where('status', 'pass')->count();
    $failedCount = $examGroups->where('status', 'fail')->count();
    $pendingCount = $examGroups->where('status', 'pending')->count();
    $averagePercentage = $examGroups->filter(fn ($exam) => !is_null($exam['percentage']))->avg('percentage');
@endphp

<div class="parent-smooth-page space-y-6">
    @include('parent.partials.child-tabs', [
        'children' => $children,
        'selectedChildId' => $selectedChildId,
        'routeName' => 'parent.exams',
    ])

    @if(!$selectedChild)
        <div class="parent-smooth-empty rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No student records are currently linked to this parent account.') }}
        </div>
    @else
        <div class="rounded-2xl border border-red-200 dark:border-red-900 bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 text-white shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-red-100">{{ __('Exam Section') }}</p>
                    <h1 class="mt-2 text-3xl font-bold">{{ __('Published Exam Marksheet') }}</h1>
                    <p class="mt-2 text-red-100">{{ __('Review each published exam marksheet for :name and print the board-style report.', ['name' => $selectedChild['name']]) }}</p>
                </div>
                <a href="{{ route('parent.results', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#B2002F] shadow-md hover:bg-[#fff1f3] transition">
                    <i class="bi bi-clipboard-data"></i>
                    <span>{{ __('Back to Results') }}</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="parent-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Exam Sheets') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $examCount }}</p>
            </div>
            <div class="parent-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Passed') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $passedCount }}</p>
            </div>
            <div class="parent-smooth-card rounded-xl border border-amber-200 dark:border-amber-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">{{ __('Pending') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingCount }}</p>
            </div>
            <div class="parent-smooth-card rounded-xl border border-blue-200 dark:border-blue-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Average') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ is_null($averagePercentage) ? '—' : number_format($averagePercentage, 1) . '%' }}</p>
            </div>
        </div>

        @if($examGroups->isEmpty())
            <div class="parent-smooth-empty rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('No published exam results are available yet.') }}
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
                    <div class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
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
                            @foreach($exam['entries']->take(3) as $row)
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
                            <a href="{{ route('parent.exams.print', ['child' => $selectedChild['id'], 'exam_id' => $exam['exam_id']]) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                                <i class="bi bi-printer"></i>
                                <span>{{ __('Print Marksheet') }}</span>
                            </a>
                            <a href="{{ route('parent.results', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                <i class="bi bi-clipboard-data"></i>
                                <span>{{ __('View Results') }}</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
