@php
    $displayDays = collect($days ?? [])
        ->filter(fn ($day) => collect($timetableByDay[$day] ?? [])->isNotEmpty())
        ->values();

    if ($displayDays->isEmpty()) {
        $displayDays = collect($days ?? [])->reject(fn ($day) => $day === 'saturday')->values();
    }

    $timeRowsCollection = collect($timeRows ?? []);
    $logoUrl = !empty($college) && method_exists($college, 'getLogoUrl')
        ? $college->getLogoUrl()
        : '/images/default-logo.svg';
    $summaryItems = collect($summaryItems ?? [])
        ->filter(fn ($item) => filled(data_get($item, 'label')))
        ->values();
    $metaItems = collect($metaItems ?? [])
        ->filter(fn ($item) => filled(data_get($item, 'label')))
        ->values();
    $sheetTitle = $sheetTitle ?? __('Class Routine');
    $sheetHeading = $sheetHeading ?? __('Routine Schedule');
    $institutionName = $institutionName ?? strtoupper($college?->name ?? 'IT-DMS');
    $departmentLine = $departmentLine ?? strtoupper($college?->short_name ?? __('Department'));
    $footerLeft = $footerLeft ?? null;
    $footerRight = $footerRight ?? now()->format('Y-m-d H:i');
    $summaryColumns = max($summaryItems->count(), 1);
    $showSlotSection = $showSlotSection ?? false;
    $fallbackTeacherName = $fallbackTeacherName ?? null;
    $highlightSubjectIds = collect($highlightSubjectIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->filter()
        ->unique()
        ->values()
        ->all();
    $highlightTeacherId = (int) ($highlightTeacherId ?? 0);
    $teacherFallbackMap = collect($teacherFallbackMap ?? [])
        ->mapWithKeys(fn ($name, $id) => [(int) $id => $name])
        ->all();
@endphp

<section class="routine-paper {{ $paperClass ?? '' }}">
    <header class="routine-paper__header">
        <div class="routine-paper__logo">
            <img src="{{ $logoUrl }}" alt="{{ $institutionName }}">
        </div>
        <div class="routine-paper__titles">
            <p class="routine-paper__institution">{{ strtoupper($institutionName) }}</p>
            <p class="routine-paper__department">{{ strtoupper($departmentLine) }}</p>
            <h2>{{ $sheetTitle }}</h2>
        </div>
        <div class="routine-paper__meta-top">
            @foreach($metaItems as $item)
                <p><span>{{ $item['label'] }}:</span> {{ $item['value'] }}</p>
            @endforeach
        </div>
    </header>

    <div class="routine-paper__summary" style="grid-template-columns: repeat({{ $summaryColumns }}, minmax(0, 1fr));">
        @foreach($summaryItems as $item)
            <div>
                <span>{{ $item['label'] }}</span>
                <strong>{{ $item['value'] }}</strong>
            </div>
        @endforeach
    </div>

    <div class="routine-table-wrap">
        <table class="routine-table">
            <thead>
                <tr>
                    <th rowspan="2" class="routine-table__head-day">{{ __('Working Day') }}</th>
                    <th rowspan="2" class="routine-table__head-period">{{ __('Period') }}</th>
                    <th colspan="3" class="routine-table__head-title">{{ $sheetHeading }}</th>
                </tr>
                <tr>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Teacher') }}</th>
                    <th>{{ __('Room') }}</th>
                </tr>
            </thead>
            <tbody>
                @if($displayDays->isEmpty() || $timeRowsCollection->isEmpty())
                    <tr>
                        <td colspan="5" class="routine-table__empty">{{ __('No routine available for the selected filters.') }}</td>
                    </tr>
                @else
                    @foreach($displayDays as $day)
                        @foreach($timeRowsCollection as $rowIndex => $timeRow)
                            @php
                                $slots = collect(data_get($slotMatrix ?? [], $day . '.' . $timeRow['key'], []));
                                $isGapOverride = (bool) data_get($gapOverrideMatrix ?? [], $day . '.' . $timeRow['key'], false);
                                $rowSections = $slots->pluck('section')
                                    ->map(fn ($value) => trim((string) $value))
                                    ->filter()
                                    ->unique()
                                    ->values();
                                $showRowSections = $showSlotSection || $rowSections->count() > 1;
                            @endphp
                            <tr class="{{ $timeRow['is_break'] && !$isGapOverride ? 'is-break' : '' }}">
                                @if($rowIndex === 0)
                                    <td rowspan="{{ $timeRowsCollection->count() }}" class="routine-table__day">
                                        {{ __(ucfirst($day)) }}
                                    </td>
                                @endif

                                <td class="routine-table__period">
                                    {{ \Carbon\Carbon::parse($timeRow['start'])->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($timeRow['end'])->format('H:i') }}
                                </td>

                                @if($timeRow['is_break'] && !$isGapOverride)
                                    <td class="routine-table__break">{{ __('Break') }}</td>
                                    <td class="routine-table__break">-</td>
                                    <td class="routine-table__break">-</td>
                                @elseif($timeRow['is_break'] && $isGapOverride)
                                    <td class="routine-table__blank">{{ __('Empty Slot') }}</td>
                                    <td class="routine-table__blank">-</td>
                                    <td class="routine-table__blank">-</td>
                                @elseif($slots->isEmpty())
                                    <td class="routine-table__blank">-</td>
                                    <td class="routine-table__blank">-</td>
                                    <td class="routine-table__blank">-</td>
                                @else
                                    <td class="routine-table__subject-cell">
                                        @foreach($slots as $slot)
                                            @php
                                                $subjectId = (int) ($slot->subject_id ?? $slot->subject?->id ?? 0);
                                                $isHighlightedSubject = ((int) ($slot->teacher_id ?? 0) === $highlightTeacherId)
                                                    || (empty($slot->teacher_id) && in_array($subjectId, $highlightSubjectIds, true));
                                            @endphp
                                            <div class="routine-slot routine-slot--{{ strtolower($slot->slot_type ?? 'theory') }} {{ $isHighlightedSubject ? 'routine-slot--teacher-focus' : '' }}">
                                                <div class="routine-slot__title">
                                                    {{ $slot->subject->subject_name ?? __('Class') }}
                                                </div>
                                                <div class="routine-slot__meta">
                                                    @if($slot->subject?->subject_code)
                                                        <span>{{ $slot->subject->subject_code }}</span>
                                                    @endif
                                                    <span>{{ ucfirst($slot->slot_type ?? __('Class')) }}</span>
                                                    @if($isHighlightedSubject)
                                                        <span>{{ __('My Subject') }}</span>
                                                    @endif
                                                    @if($showRowSections && filled($slot->section))
                                                        <span>{{ __('Section') }} {{ $slot->section }}</span>
                                                    @endif
                                                    @if($slot->lab_group)
                                                        <span>{{ __('Lab') }} {{ $slot->lab_group }}</span>
                                                    @endif
                                                </div>
                                                @if($slot->remarks)
                                                    <div class="routine-slot__note">{{ $slot->remarks }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="routine-table__stack-cell">
                                        @foreach($slots as $slot)
                                            @php
                                                $subjectId = (int) ($slot->subject_id ?? $slot->subject?->id ?? 0);
                                                $isHighlightedSubject = ((int) ($slot->teacher_id ?? 0) === $highlightTeacherId)
                                                    || (empty($slot->teacher_id) && in_array($subjectId, $highlightSubjectIds, true));
                                            @endphp
                                            <div class="routine-stack-item {{ $isHighlightedSubject ? 'routine-stack-item--teacher-focus' : '' }}">
                                                <div class="routine-stack-item__name">
                                                    {{ $slot->teacher->user->name ?? ($teacherFallbackMap[$subjectId] ?? $fallbackTeacherName ?? __('TBA')) }}
                                                </div>
                                                @if(($showRowSections && filled($slot->section)) || filled($slot->lab_group))
                                                    <div class="routine-stack-item__meta">
                                                        @if($showRowSections && filled($slot->section))
                                                            <span>{{ __('Section') }} {{ $slot->section }}</span>
                                                        @endif
                                                        @if(filled($slot->lab_group))
                                                            <span>{{ __('Lab') }} {{ $slot->lab_group }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="routine-table__stack-cell">
                                        @foreach($slots as $slot)
                                            @php
                                                $subjectId = (int) ($slot->subject_id ?? $slot->subject?->id ?? 0);
                                                $isHighlightedSubject = ((int) ($slot->teacher_id ?? 0) === $highlightTeacherId)
                                                    || (empty($slot->teacher_id) && in_array($subjectId, $highlightSubjectIds, true));
                                            @endphp
                                            <div class="routine-stack-item {{ $isHighlightedSubject ? 'routine-stack-item--teacher-focus' : '' }}">
                                                <div class="routine-stack-item__name">
                                                    {{ $slot->room ?: __('Not Assigned') }}
                                                </div>
                                                @if(($showRowSections && filled($slot->section)) || filled($slot->lab_group))
                                                    <div class="routine-stack-item__meta">
                                                        @if($showRowSections && filled($slot->section))
                                                            <span>{{ __('Section') }} {{ $slot->section }}</span>
                                                        @endif
                                                        @if(filled($slot->lab_group))
                                                            <span>{{ __('Lab') }} {{ $slot->lab_group }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <footer class="routine-paper__footer">
        <div>
            @if($footerLeft)
                <span>{{ __('Included') }}:</span> {{ $footerLeft }}
            @endif
        </div>
        <div>
            <span>{{ __('Generated At') }}:</span> {{ $footerRight }}
        </div>
    </footer>
</section>
