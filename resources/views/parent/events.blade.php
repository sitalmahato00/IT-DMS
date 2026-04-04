@extends('parent.layouts.parentlayout')

@section('title', __('Events & Schedule'))
@section('subtitle', __('Formal class routine for parent review'))

@section('styles')
    @include('shared.timetable.partials.routine-styles')
@endsection

@section('content')
<div class="parent-smooth-page routine-page space-y-6">
    @include('parent.partials.child-tabs', [
        'children' => $children,
        'selectedChildId' => $selectedChildId,
        'routeName' => 'parent.events',
        'extraParams' => array_filter([
            'section' => request('section'),
        ], fn ($value) => filled($value)),
    ])

    @if(!$selectedChild)
        <div class="parent-smooth-empty rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No event or schedule data is available because no students are linked to this parent account yet.') }}
        </div>
    @else
        <div class="parent-smooth-panel rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-3xl space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-700">
                        <i class="bi bi-calendar-week"></i>
                        {{ __('Student Routine') }}
                    </span>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 md:text-3xl">{{ __('Class routine') }}</h2>
                        <p class="mt-2 text-sm text-slate-600 md:text-base">
                            {{ __('This student schedule now follows the same formal routine sheet used in the student portal for easier parent review.') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                            <i class="bi bi-person-badge"></i>
                            {{ __('Roll No:') }} {{ $selectedChild['roll_no'] ?: __('N/A') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                            <i class="bi bi-journal-bookmark"></i>
                            {{ $selectedChild['subject_count'] }} {{ __('subjects') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                            <i class="bi bi-clock-history"></i>
                            {{ $selectedChild['timetable_total_slots'] }} {{ __('slots') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                            <i class="bi bi-calendar3"></i>
                            {{ $selectedChild['timetable_active_days'] }} {{ __('active days') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                            <i class="bi bi-diagram-3"></i>
                            {{ __('Section') }} {{ $selectedChild['display_section'] ?: __('All') }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('parent.courses', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                        <i class="bi bi-journal-bookmark"></i>
                        {{ __('Open courses') }}
                    </a>
                    <a href="{{ route('parent.children', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700">
                        <i class="bi bi-person-lines-fill"></i>
                        {{ __('Open profile') }}
                    </a>
                </div>
            </div>

            @if($selectedChild['schedule_sections']->isNotEmpty())
                <form method="GET" action="{{ route('parent.events') }}" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto]">
                    <input type="hidden" name="child" value="{{ $selectedChild['id'] }}">

                    <div>
                        <label for="section" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                            {{ __('Section') }}
                        </label>
                        <select id="section" name="section" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                            <option value="">{{ __('All Sections') }}</option>
                            @foreach($selectedChild['schedule_sections'] as $section)
                                <option value="{{ $section }}" {{ (string) $selectedChild['display_section'] === (string) $section ? 'selected' : '' }}>
                                    {{ __('Section') }} {{ $section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700">
                        <i class="bi bi-funnel"></i>
                        {{ __('Apply') }}
                    </button>

                    <a href="{{ route('parent.events', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="bi bi-arrow-clockwise"></i>
                        {{ __('Reset') }}
                    </a>
                </form>
            @endif
        </div>

        <div class="routine-page space-y-6">
            @if($selectedChild['timetable_total_slots'] === 0)
                <section class="parent-smooth-empty rounded-[28px] border border-amber-200 bg-amber-50 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                            <i class="bi bi-exclamation-circle text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-amber-900">{{ __('No timetable is published for this student') }}</h2>
                            <p class="mt-2 text-sm text-amber-800">
                                {{ __('The parent schedule will appear here in the same formal layout once timetable slots are assigned to this student.') }}
                            </p>
                        </div>
                    </div>
                </section>
            @else
                @php
                    $college = $department ?? null;
                    $days = $selectedChild['timetable_days'];
                    $timetableByDay = $selectedChild['timetable_by_day'];
                    $timeRows = $selectedChild['time_rows'];
                    $slotMatrix = $selectedChild['slot_matrix'];
                    $gapOverrideMatrix = $selectedChild['gap_override_matrix'];
                    $sheetTitle = __('Class Routine');
                    $sheetHeading = __('Semester') . ' ' . ($selectedChild['semester'] ?: __('N/A'))
                        . (filled($selectedChild['display_section']) ? ' / ' . __('Section') . ' ' . $selectedChild['display_section'] : '');
                    $institutionName = $department?->name ?? 'IT-DMS';
                    $departmentLine = $selectedChild['department'] ?: ($department?->short_name ?? __('Department'));
                    $metaItems = [
                        ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
                        ['label' => __('Academic Year'), 'value' => $selectedChild['academic_year'] ?: now()->format('Y')],
                    ];
                    $summaryItems = [
                        ['label' => __('Student'), 'value' => $selectedChild['name']],
                        ['label' => __('Roll No'), 'value' => $selectedChild['roll_no'] ?: __('N/A')],
                        ['label' => __('Semester'), 'value' => $selectedChild['semester'] ?: __('N/A')],
                        ['label' => __('Section'), 'value' => $selectedChild['display_section'] ?: __('All')],
                    ];
                    $footerLeft = $selectedChild['subject_count'] . ' ' . __('subjects');
                    $showSlotSection = blank($selectedChild['display_section']);
                @endphp
                @include('shared.timetable.partials.routine-sheet')
            @endif
        </div>
    @endif
</div>
@endsection
