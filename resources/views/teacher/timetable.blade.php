@extends('teacher.layouts.teacherlayout')

@section('title', __('My Timetable'))

@section('styles')
    @include('shared.timetable.partials.routine-styles')
@endsection

@section('content')
@php
    $printUrl = route('teacher.timetable.print', array_filter([
        'semester' => $selectedSemester,
    ], fn ($value) => filled($value)));

    $sheetTitle = __('Teacher Routine');
    $sheetHeading = $selectedSemester
        ? __('Semester') . ' ' . $selectedSemester
        : __('All Assigned Semesters');
    $institutionName = $college?->name ?? 'IT-DMS';
    $departmentLine = $college?->short_name ?? __('Department');
    $metaItems = [
        ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
        ['label' => __('Academic Year'), 'value' => now()->format('Y')],
    ];
    $summaryItems = [
        ['label' => __('Teacher'), 'value' => auth()->user()?->name ?? __('Teacher')],
        ['label' => __('Subjects'), 'value' => $totalSubjects],
        ['label' => __('Slots'), 'value' => $totalSlots],
        ['label' => __('Semester Filter'), 'value' => $selectedSemester ?: __('All')],
    ];
    $footerLeft = collect($subjects ?? [])->count() . ' ' . __('subjects');
@endphp

<div class="routine-page space-y-6">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl space-y-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-700">
                    <i class="bi bi-calendar-week"></i>
                    {{ __('Teacher Schedule') }}
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 md:text-3xl">{{ __('My timetable routine') }}</h1>
                    <p class="mt-2 text-sm text-slate-600 md:text-base">
                        {{ __('Your assigned classes are arranged in the same formal routine sheet used for student schedules.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-person-workspace"></i>
                        {{ auth()->user()?->name ?? __('Teacher') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-journal-bookmark"></i>
                        {{ $totalSubjects }} {{ __('subjects') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-calendar3"></i>
                        {{ $activeDays }} {{ __('active days') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    onclick="teacherOpenPrintPreview('{{ $printUrl }}', { title: '{{ __('Routine Preview') }}', subtitle: '{{ __('A4 routine sheet') }}' })"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
                >
                    <i class="bi bi-eye"></i>
                    {{ __('Preview') }}
                </button>
                <a
                    href="{{ $printUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                >
                    <i class="bi bi-printer"></i>
                    {{ __('Print / New tab') }}
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('teacher.timetable') }}" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto]">
            <div>
                <label for="semester" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    {{ __('Semester') }}
                </label>
                <select id="semester" name="semester" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="">{{ __('All Semesters') }}</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}" {{ (string) $selectedSemester === (string) $semester ? 'selected' : '' }}>
                            {{ __('Semester') }} {{ $semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700">
                <i class="bi bi-funnel"></i>
                {{ __('Apply') }}
            </button>

            <a href="{{ route('teacher.timetable') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <i class="bi bi-arrow-clockwise"></i>
                {{ __('Reset') }}
            </a>
        </form>
    </section>

    @if($totalSlots === 0)
        <section class="rounded-[28px] border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-100 text-yellow-700">
                    <i class="bi bi-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-yellow-900">{{ __('No timetable assigned') }}</h2>
                    <p class="mt-2 text-sm text-yellow-800">
                        {{ __('Your timetable will appear here once classes are assigned to you.') }}
                    </p>
                </div>
            </div>
        </section>
    @else
        @include('shared.timetable.partials.routine-sheet')
    @endif
</div>
@endsection
