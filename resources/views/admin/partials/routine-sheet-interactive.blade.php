@php
    $displayDays = collect($days ?? [])
        ->filter(fn ($dayName) => collect($timetableByDay[$dayName] ?? [])->isNotEmpty())
        ->values();

    if ($displayDays->isEmpty()) {
        $displayDays = collect($days ?? [])->reject(fn ($dayName) => $dayName === 'saturday')->values();
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
    $institutionName = $institutionName ?? strtoupper($college?->name ?? 'Manmohan Memorial Polytechnic');
    $departmentLine = $departmentLine ?? strtoupper($college?->short_name ?? __('Department'));
    $footerLeft = $footerLeft ?? null;
    $footerRight = $footerRight ?? now()->format('Y-m-d H:i');
    $summaryColumns = max($summaryItems->count(), 1);
    $showSlotSection = $showSlotSection ?? false;
    $conflictSlotIds = collect($conflicts ?? [])
        ->flatMap(fn ($conflict) => [$conflict['slot1_id'] ?? null, $conflict['slot2_id'] ?? null])
        ->filter()
        ->unique()
        ->values()
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
                    @foreach($displayDays as $dayName)
                        @foreach($timeRowsCollection as $rowIndex => $timeRow)
                            @php
                                $rowSlots = collect(data_get($slotMatrix ?? [], $dayName . '.' . $timeRow['key'], []));
                                $rowCardId = 'row-slot-' . $dayName . '-' . str_replace(':', '', $timeRow['start']) . '-' . str_replace(':', '', $timeRow['end']);
                                $isGapOverride = (bool) data_get($gapOverrideMatrix ?? [], $dayName . '.' . $timeRow['key'], false);
                                $rowSections = $rowSlots->pluck('section')
                                    ->map(fn ($value) => trim((string) $value))
                                    ->filter()
                                    ->unique()
                                    ->values();
                                $showRowSections = $showSlotSection || $rowSections->count() > 1;
                            @endphp
                            <tr class="{{ $timeRow['is_break'] && !$isGapOverride ? 'is-break' : '' }}">
                                @if($rowIndex === 0)
                                    <td rowspan="{{ $timeRowsCollection->count() }}" class="routine-table__day">
                                        {{ __(ucfirst($dayName)) }}
                                    </td>
                                @endif

                                <td class="routine-table__period">
                                    {{ \Carbon\Carbon::parse($timeRow['start'])->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($timeRow['end'])->format('H:i') }}
                                </td>

                                @if($timeRow['is_break'] && !$isGapOverride)
                                    <td colspan="3" class="routine-table__subject-cell">
                                        <div
                                            id="{{ $rowCardId }}"
                                            class="routine-slot routine-slot--interactive routine-break-slot"
                                            data-break-slot="true"
                                            data-day="{{ $dayName }}"
                                            data-start-time="{{ $timeRow['start'] }}"
                                            data-end-time="{{ $timeRow['end'] }}"
                                            onclick="openAddSlotModalForBreak('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event)"
                                            role="button"
                                            tabindex="0"
                                            title="{{ __('Click to add a slot in this break period') }}"
                                            onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); openAddSlotModalForBreak('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event); }"
                                        >
                                                <div class="routine-slot__actions" onclick="event.stopPropagation()">
                                                    <button type="button" onclick="openAddSlotModalForBreak('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event)" title="{{ __('Add slot') }}">
                                                        <i class="bi bi-plus-circle"></i>
                                                    </button>
                                                    <button type="button" onclick="editBreakSlot('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event)" title="{{ __('Edit break slot') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" onclick="deleteBreakSlot('{{ $rowCardId }}', '{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event)" title="{{ __('Delete break slot') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                            <div class="routine-slot__title">{{ __('Break Slot') }}</div>
                                            <div class="routine-slot__meta">
                                                <span>{{ __(ucfirst($dayName)) }}</span>
                                                <span>{{ \Carbon\Carbon::parse($timeRow['start'])->format('H:i') }} - {{ \Carbon\Carbon::parse($timeRow['end'])->format('H:i') }}</span>
                                                <span>{{ __('Gap in routine') }}</span>
                                            </div>
                                            <div class="routine-slot__note">
                                                {{ __('Use Add or Edit to place a class in this break period. Delete converts the break to an empty slot on this sheet.') }}
                                            </div>
                                        </div>
                                    </td>
                                @elseif(($timeRow['is_break'] && $isGapOverride) || $rowSlots->isEmpty())
                                    <td colspan="3" class="routine-table__subject-cell">
                                        <div
                                            id="{{ $rowCardId }}"
                                            class="routine-slot routine-slot--interactive routine-empty-slot"
                                            onclick="openAddSlotModalForBreak('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event)"
                                            role="button"
                                            tabindex="0"
                                            title="{{ __('Click to add a class in this empty slot') }}"
                                            onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); openAddSlotModalForBreak('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event); }"
                                        >
                                            <div class="routine-slot__actions" onclick="event.stopPropagation()">
                                                <button type="button" onclick="openAddSlotModalForBreak('{{ $dayName }}', '{{ $timeRow['start'] }}', '{{ $timeRow['end'] }}', event)" title="{{ __('Add class') }}">
                                                    <i class="bi bi-plus-circle"></i>
                                                </button>
                                            </div>

                                            <div class="routine-slot__title">{{ __('Empty Slot') }}</div>
                                            <div class="routine-slot__meta">
                                                <span>{{ __(ucfirst($dayName)) }}</span>
                                                <span>{{ \Carbon\Carbon::parse($timeRow['start'])->format('H:i') }} - {{ \Carbon\Carbon::parse($timeRow['end'])->format('H:i') }}</span>
                                                <span>{{ __('No class assigned') }}</span>
                                            </div>
                                            <div class="routine-slot__note">
                                                {{ __('Add another class in this free period.') }}
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td class="routine-table__subject-cell">
                                        @foreach($rowSlots as $slot)
                                            @php
                                                $hasConflict = in_array($slot->id, $conflictSlotIds, true);
                                            @endphp
                                            <div
                                                class="routine-slot routine-slot--{{ strtolower($slot->slot_type ?? 'theory') }} routine-slot--interactive {{ $hasConflict ? 'routine-slot--conflict' : '' }} {{ $slot->is_locked ? 'routine-slot--locked' : '' }}"
                                                onclick="viewSlot(event, {{ $slot->id }})"
                                                role="button"
                                                tabindex="0"
                                                title="{{ __('Click to view slot details') }}"
                                                onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); viewSlot(event, {{ $slot->id }}); }"
                                            >
                                                <div class="routine-slot__actions" onclick="event.stopPropagation()">
                                                    <button type="button" onclick="viewSlot(event, {{ $slot->id }})" title="{{ __('View slot') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" onclick="editSlot({{ $slot->id }}, event)" title="{{ __('Edit slot') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" onclick="deleteSlot(event, {{ $slot->id }})" title="{{ __('Delete slot') }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <div class="routine-slot__title">
                                                    {{ $slot->subject->subject_name ?? __('Class') }}
                                                </div>
                                                <div class="routine-slot__meta">
                                                    @if($slot->subject?->subject_code)
                                                        <span>{{ $slot->subject->subject_code }}</span>
                                                    @endif
                                                    <span>{{ ucfirst($slot->slot_type ?? __('Class')) }}</span>
                                                    @if($showRowSections && filled($slot->section))
                                                        <span>{{ __('Section') }} {{ $slot->section }}</span>
                                                    @endif
                                                    @if($slot->lab_group)
                                                        <span>{{ __('Lab') }} {{ $slot->lab_group }}</span>
                                                    @endif
                                                    @if($slot->is_locked)
                                                        <span>{{ __('Locked') }}</span>
                                                    @endif
                                                    @if($hasConflict)
                                                        <span>{{ __('Conflict') }}</span>
                                                    @endif
                                                </div>
                                                @if($slot->remarks)
                                                    <div class="routine-slot__note">{{ $slot->remarks }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="routine-table__stack-cell">
                                        @foreach($rowSlots as $slot)
                                            <div class="routine-stack-item">
                                                <div class="routine-stack-item__name">
                                                    {{ $slot->teacher->user->name ?? __('TBA') }}
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
                                        @foreach($rowSlots as $slot)
                                            <div class="routine-stack-item">
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

