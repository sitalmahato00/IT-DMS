@extends('teacher.layouts.teacherlayout')

@section('title', __('My Timetable'))

@section('styles')
    @include('shared.timetable.partials.routine-styles')
    <style>
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .teacher-timetable-hero {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            border-color: rgba(241, 213, 219, 0.96);
            background:
                radial-gradient(circle at top right, rgba(251, 113, 133, 0.2), transparent 30%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 247, 248, 0.98));
            box-shadow: 0 34px 62px -44px rgba(148, 19, 52, 0.28);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .teacher-timetable-kicker {
            box-shadow: 0 18px 30px -24px rgba(225, 29, 72, 0.4);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .teacher-timetable-chip {
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.94);
            background: rgba(255, 255, 255, 0.84);
            box-shadow: 0 16px 28px -24px rgba(15, 23, 42, 0.18);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .teacher-timetable-filter {
            margin-top: 1.75rem;
            border-top: 1px solid rgba(241, 213, 219, 0.82);
            padding-top: 1.5rem;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .teacher-timetable-field label {
            color: #64748b;
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .teacher-timetable-empty {
            border-radius: 28px;
            border-color: rgba(253, 224, 71, 0.82);
            background: linear-gradient(180deg, rgba(255, 251, 235, 0.98), rgba(255, 247, 237, 0.98));
            box-shadow: 0 28px 46px -34px rgba(217, 119, 6, 0.24);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-paper {
            border: 1px solid rgba(226, 232, 240, 0.94);
            border-radius: 32px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(251, 113, 133, 0.1), transparent 24%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 250, 250, 0.98));
            box-shadow: 0 34px 68px -46px rgba(15, 23, 42, 0.26);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-paper__header,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-paper__footer {
            border-color: rgba(226, 232, 240, 0.94);
            background: linear-gradient(180deg, rgba(255, 248, 249, 0.96), rgba(255, 255, 255, 0.95));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-paper__logo {
            border-color: rgba(241, 213, 219, 0.95);
            border-radius: 22px;
            box-shadow: 0 16px 30px -24px rgba(15, 23, 42, 0.18);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-paper__summary {
            border-color: rgba(226, 232, 240, 0.94);
            background: linear-gradient(180deg, rgba(255, 252, 252, 0.98), rgba(255, 246, 247, 0.92));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-paper__summary div {
            border-color: rgba(226, 232, 240, 0.94);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table th,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table td {
            border-color: rgba(226, 232, 240, 0.94);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table thead th {
            background: linear-gradient(180deg, #fff4f6, #fffafb);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table__day,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table__period,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table__break {
            background: linear-gradient(180deg, rgba(255, 247, 248, 0.96), rgba(248, 250, 252, 0.96));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-slot,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-stack-item {
            border-radius: 16px;
            box-shadow: 0 16px 28px -24px rgba(15, 23, 42, 0.16);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-slot__meta span {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table tbody tr:not(.is-break):hover td {
            background: linear-gradient(90deg, rgba(255, 241, 242, 0.72), rgba(255, 255, 255, 0.98));
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table tbody tr:not(.is-break):hover .routine-slot,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-table tbody tr:not(.is-break):hover .routine-stack-item {
            transform: translateY(-1px);
        }

        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-slot,
        html.teacher-ui-enhanced:not(.dark) .teacher-timetable-page .routine-stack-item {
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
    </style>
@endsection

@section('content')
@php
    $printUrl = route('teacher.timetable.print', array_filter([
        'semester' => $selectedSemester,
        'section' => $selectedSection,
    ], fn ($value) => filled($value)));

    $sheetTitle = __('Teacher Routine');
    $sheetHeading = $selectedSemester
        ? __('Semester') . ' ' . $selectedSemester . (filled($selectedSection) ? ' / ' . __('Section') . ' ' . $selectedSection : '')
        : __('All Assigned Semesters');
    $institutionName = $college?->name ?? 'IT-DMS';
    $departmentLine = $college?->short_name ?? __('Department');
    $metaItems = [
        ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
        ['label' => __('Academic Year'), 'value' => now()->format('Y')],
    ];
    $summaryItems = [
        ['label' => __('Teacher'), 'value' => auth()->user()?->name ?? __('Teacher')],
        ['label' => __('Highlighted Subjects'), 'value' => $highlightedSubjectCount ?? 0],
        ['label' => __('Visible Subjects'), 'value' => $totalSubjects],
        ['label' => __('Slots'), 'value' => $totalSlots],
        ['label' => __('Semester Filter'), 'value' => $selectedSemester ?: __('All')],
        ['label' => __('Section Filter'), 'value' => $selectedSection ?: __('All')],
    ];
    $footerLeft = collect($subjects ?? [])->count() . ' ' . __('subjects');
    $showSlotSection = blank($selectedSection);
    $fallbackTeacherName = null;
@endphp

<div class="routine-page teacher-timetable-page space-y-6">
    <section class="teacher-filter-panel teacher-timetable-hero rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl space-y-3">
                <span class="teacher-timetable-kicker inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-700">
                    <i class="bi bi-calendar-week"></i>
                    {{ __('Teacher Schedule') }}
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 md:text-3xl">{{ __('My timetable routine') }}</h1>
                    <p class="mt-2 text-sm text-slate-600 md:text-base">
                        {{ __('The full semester routine is shown below, and the subjects assigned to you are highlighted for quick scanning.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                    <span class="teacher-timetable-chip inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-person-workspace"></i>
                        {{ auth()->user()?->name ?? __('Teacher') }}
                    </span>
                    <span class="teacher-timetable-chip inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-journal-bookmark"></i>
                        {{ $highlightedSubjectCount ?? 0 }} {{ __('my subjects') }}
                    </span>
                    <span class="teacher-timetable-chip inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-calendar3"></i>
                        {{ $activeDays }} {{ __('active days') }}
                    </span>
                    <span class="teacher-timetable-chip inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-rose-700">
                        <i class="bi bi-stars"></i>
                        {{ $highlightedSlotCount ?? 0 }} {{ __('highlighted slots') }}
                    </span>
                    <span class="teacher-timetable-chip inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-diagram-3"></i>
                        {{ __('Section') }} {{ $selectedSection ?: __('All') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    onclick="teacherOpenPrintPreview('{{ $printUrl }}', { title: '{{ __('Routine Preview') }}', subtitle: '{{ __('A4 routine sheet') }}' })"
                    class="teacher-page-secondary-btn inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
                >
                    <i class="bi bi-eye"></i>
                    {{ __('Preview') }}
                </button>
                <a
                    href="{{ $printUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="teacher-page-primary-btn inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                >
                    <i class="bi bi-printer"></i>
                    {{ __('Print / New tab') }}
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('teacher.timetable') }}" class="teacher-timetable-filter mt-6 grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto_auto]">
            <div class="teacher-timetable-field">
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

            <div class="teacher-timetable-field">
                <label for="section" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    {{ __('Section') }}
                </label>
                <select id="section" name="section" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="">{{ __('All Sections') }}</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ (string) $selectedSection === (string) $section ? 'selected' : '' }}>
                            {{ __('Section') }} {{ $section }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="teacher-page-primary-btn inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700">
                <i class="bi bi-funnel"></i>
                {{ __('Apply') }}
            </button>

            <a href="{{ route('teacher.timetable') }}" class="teacher-page-secondary-btn inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <i class="bi bi-arrow-clockwise"></i>
                {{ __('Reset') }}
            </a>
        </form>
    </section>

    @if($totalSlots === 0)
        <section class="teacher-timetable-empty rounded-[28px] border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-100 text-yellow-700">
                    <i class="bi bi-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-yellow-900">{{ __('No timetable assigned') }}</h2>
                    <p class="mt-2 text-sm text-yellow-800">
                        {{ __('No semester routine is available for the current filters yet.') }}
                    </p>
                </div>
            </div>
        </section>
    @else
        @include('shared.timetable.partials.routine-sheet')
    @endif
</div>
@endsection
