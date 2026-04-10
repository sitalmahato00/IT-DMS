@extends('student.layouts.studentlayout')

@section('title', __('Class Routine'))
@section('subtitle', __('Routine sheet for your semester and section'))

@section('styles')
    @include('shared.timetable.partials.routine-styles')
@endsection

@section('content')
@php
    $filterParams = array_filter([
        'semester' => $selectedSemester,
        'section' => $selectedSection,
    ], fn ($value) => filled($value));

    $printUrl = route('student.timetable.print', $filterParams);
@endphp

<div class="student-smooth-page routine-page space-y-6" data-student-search-root>
    <section class="student-smooth-panel rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl space-y-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-700">
                    <i class="bi bi-calendar-week"></i>
                    {{ __('Student Routine') }}
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 md:text-3xl">{{ __('Class routine') }}</h1>
                    <p class="mt-2 text-sm text-slate-600 md:text-base">
                        {{ __('Your weekly routine is now arranged in a formal sheet layout similar to a printed college schedule.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-person-badge"></i>
                        {{ __('Roll No:') }} {{ $student->roll_no ?? __('N/A') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-journal-bookmark"></i>
                        {{ $totalSubjects }} {{ __('subjects') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-clock-history"></i>
                        {{ $totalSlots }} {{ __('slots') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="button"
                    onclick="studentOpenPrintPreview('{{ $printUrl }}', { title: '{{ __('Routine Preview') }}', subtitle: '{{ __('A4 routine sheet') }}' })"
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

        <form method="GET" action="{{ route('student.timetable') }}" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto_auto]">
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

            <div>
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

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700">
                <i class="bi bi-funnel"></i>
                {{ __('Apply') }}
            </button>

            <a href="{{ route('student.timetable') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <i class="bi bi-arrow-clockwise"></i>
                {{ __('Reset') }}
            </a>
        </form>
    </section>

    @if($totalSlots === 0)
        <section class="student-smooth-empty rounded-[28px] border border-amber-200 bg-amber-50 p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                    <i class="bi bi-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-amber-900">{{ __('No timetable is published for your filters') }}</h2>
                    <p class="mt-2 text-sm text-amber-800">
                        {{ __('This page now follows the admin timetable directly. Try another section, or check again after the routine is updated.') }}
                    </p>
                </div>
            </div>
        </section>
    @else
        @php
            $sheetTitle = __('Class Routine');
            $sheetHeading = __('Semester') . ' ' . ($displaySemester ?: ($selectedSemester ?: __('N/A')))
                . (filled($displaySection ?: $selectedSection) ? ' / ' . __('Section') . ' ' . ($displaySection ?: $selectedSection) : '');
            $institutionName = $college?->name ?? 'Manmohan Memorial Polytechnic';
            $departmentLine = $student->department ?: ($college?->short_name ?? __('Department'));
            $metaItems = [
                ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
                ['label' => __('Academic Year'), 'value' => $student->academic_year ?: now()->format('Y')],
            ];
            $summaryItems = [
                ['label' => __('Student'), 'value' => $student->user->name ?? auth()->user()?->name ?? __('Student')],
                ['label' => __('Roll No'), 'value' => $student->roll_no ?? __('N/A')],
                ['label' => __('Semester'), 'value' => $displaySemester ?: ($selectedSemester ?: __('N/A'))],
                ['label' => __('Section'), 'value' => $displaySection ?: ($selectedSection ?: __('All'))],
            ];
            $footerLeft = collect($subjects ?? [])->count() . ' ' . __('subjects');
            $showSlotSection = blank($displaySection ?: $selectedSection);
        @endphp
        @include('shared.timetable.partials.routine-sheet')
    @endif
</div>
@endsection

