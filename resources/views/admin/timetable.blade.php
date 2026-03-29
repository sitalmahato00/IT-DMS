@extends('admin.layouts.app')

@section('title', __('Timetable & Scheduling'))

@section('styles')
    @include('shared.timetable.partials.routine-styles')
    <style>
        .routine-slot--interactive {
            position: relative;
            cursor: pointer;
            transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
            padding-top: 2rem;
        }

        .routine-slot--interactive:hover,
        .routine-slot--interactive:focus-visible {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            border-color: #94a3b8;
            outline: none;
        }

        .routine-slot--conflict {
            border-color: #f87171 !important;
            box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.18);
        }

        .routine-slot--locked {
            background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.03), rgba(15, 23, 42, 0.01));
        }

        .routine-slot__actions {
            position: absolute;
            top: 0.45rem;
            right: 0.45rem;
            display: flex;
            gap: 0.35rem;
        }

        .routine-slot__actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.6rem;
            height: 1.6rem;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.95);
            color: #334155;
            transition: background 0.16s ease, color 0.16s ease, border-color 0.16s ease;
        }

        .routine-slot__actions button:hover {
            background: #ffffff;
            color: #dc2626;
            border-color: #fca5a5;
        }

        .routine-break-slot {
            border-style: dashed;
            border-color: #cbd5e1;
            background: linear-gradient(135deg, #f8fafc, #eef2ff);
        }

        .routine-empty-slot {
            border-style: dashed;
            border-color: #cbd5e1;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
        }
    </style>
@endsection

@section('content')
{{-- Page Header - Using standardized component --}}
@include('admin.components.admin-page-header', [
    'title' => 'Timetable & Scheduling',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Timetable & Scheduling']
    ]
])

@php
    $printUrl = route('admin.timetable.print', array_filter([
        'semester' => $semester,
        'section' => $section,
    ], fn ($value) => filled($value)));

    $sheetTitle = __('Class Routine');
    $sheetHeading = __('Semester') . ' ' . $semester . (filled($section) ? ' / ' . __('Section') . ' ' . $section : '');
    $institutionName = $college?->name ?? 'IT-DMS';
    $departmentLine = $college?->short_name ?? __('Department');
    $metaItems = [
        ['label' => __('Prepared On'), 'value' => now()->format('Y-m-d')],
        ['label' => __('Academic Year'), 'value' => now()->format('Y')],
    ];
    $summaryItems = [
        ['label' => __('Role'), 'value' => __('Administrator')],
        ['label' => __('Semester'), 'value' => $semester],
        ['label' => __('Section'), 'value' => $section ?: __('All')],
        ['label' => __('Slots'), 'value' => $slots->count()],
    ];
    $footerLeft = $slots->count() . ' ' . __('slots');
    $timetableByDay = $slotsByDay;
    $subjectAssignmentMap = $subjects->mapWithKeys(function ($subject) {
        $primaryAssignment = $subject->teacherAssignments
            ->sortBy(fn ($assignment) => $assignment->role === 'primary' ? 0 : 1)
            ->first();

        return [
            $subject->id => [
                'teacher_id' => $primaryAssignment?->teacher_id,
                'teacher_name' => $primaryAssignment?->teacher?->user?->name,
                'lab_technician_id' => $subject->lab_technician_id,
                'lab_technician_name' => $subject->labTechnician?->user?->name,
                'has_lab' => (bool) $subject->has_lab,
            ],
        ];
    })->all();
@endphp

<div class="routine-page space-y-6">
    <section class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl space-y-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-red-700">
                    <i class="bi bi-calendar2-week"></i>
                    {{ __('Admin Routine') }}
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 md:text-3xl">{{ __('Timetable routine sheet') }}</h1>
                    <p class="mt-2 text-sm text-slate-600 md:text-base">
                        {{ __('The admin timetable now uses the same formal class-routine layout used in the printable routine views.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm text-slate-600">
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-layers"></i>
                        {{ __('Semester') }} {{ $semester }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-diagram-3"></i>
                        {{ __('Section') }} {{ $section ?: __('All') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                        <i class="bi bi-grid-3x3-gap"></i>
                        {{ $slots->count() }} {{ __('visible slots') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="openAddSlotModal()" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700">
                    <i class="bi bi-plus-circle"></i>
                    {{ __('Add Slot') }}
                </button>
                <button
                    type="button"
                    onclick="adminOpenPrintPreview('{{ $printUrl }}', { title: '{{ __('Routine Preview') }}', subtitle: '{{ __('A4 routine sheet') }}' })"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
                >
                    <i class="bi bi-eye"></i>
                    {{ __('Preview') }}
                </button>
                <a
                    href="{{ $printUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700"
                >
                    <i class="bi bi-printer"></i>
                    {{ __('Print / New tab') }}
                </a>
            </div>
        </div>

        <form id="filterForm" method="GET" action="{{ route('admin.timetable') }}" class="mt-6 grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1fr)_auto_auto]">
            <div>
                <label for="semester" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    {{ __('Semester') }}
                </label>
                <select id="semester" name="semester" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem }}" {{ (string) $semester === (string) $sem ? 'selected' : '' }}>
                            {{ __('Semester') }} {{ $sem }}
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
                    @foreach($sections as $sec)
                        <option value="{{ $sec }}" {{ (string) $section === (string) $sec ? 'selected' : '' }}>
                            {{ __('Section') }} {{ $sec }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="day" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    {{ __('Day') }}
                </label>
                <select id="day" name="day" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                    <option value="">{{ __('All Days') }}</option>
                    @foreach($days as $dayName)
                        <option value="{{ $dayName }}" {{ (string) $day === (string) $dayName ? 'selected' : '' }}>
                            {{ __(ucfirst($dayName)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700">
                <i class="bi bi-funnel"></i>
                {{ __('Apply') }}
            </button>

            <a href="{{ route('admin.timetable') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <i class="bi bi-arrow-clockwise"></i>
                {{ __('Reset') }}
            </a>
        </form>
    </section>

    @if($slots->isEmpty())
        <section class="rounded-[28px] border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-100 text-yellow-700">
                    <i class="bi bi-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-yellow-900">{{ __('No timetable slots found') }}</h2>
                    <p class="mt-2 text-sm text-yellow-800">
                        {{ __('There are no active slots for the current filters. Adjust the filters or add a new slot.') }}
                    </p>
                </div>
            </div>
        </section>
    @else
        <section class="rounded-[28px] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-4 px-2">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('Interactive routine sheet') }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Click any slot to view its details, or use the inline buttons in the slot card to view, edit, or delete it.') }}</p>
                </div>
                <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $slots->count() }} {{ __('slots on screen') }}
                </span>
            </div>

            @include('admin.partials.routine-sheet-interactive')
        </section>
    @endif
</div>

<!-- Modal -->
<div id="slotModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 id="slotModalTitle" class="text-base font-semibold text-gray-900">{{ __('Add Timetable Slot') }}</h3>
            <button onclick="closeSlotModal()" class="text-gray-500 hover:text-gray-700"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="slotForm" class="p-6 space-y-4">
            <input type="hidden" id="slotId">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Day') }} *</label>
                    <select id="slotDay" required class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="sunday">{{ __('Sunday') }}</option>
                        <option value="monday">{{ __('Monday') }}</option>
                        <option value="tuesday">{{ __('Tuesday') }}</option>
                        <option value="wednesday">{{ __('Wednesday') }}</option>
                        <option value="thursday">{{ __('Thursday') }}</option>
                        <option value="friday">{{ __('Friday') }}</option>
                        <option value="saturday">{{ __('Saturday') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Type') }} *</label>
                    <select id="slotType" required class="w-full px-3 py-2 border rounded-lg text-sm" onchange="toggleLabGroup()">
                        <option value="theory">{{ __('Theory') }}</option>
                        <option value="practical">{{ __('Practical') }}</option>
                        <option value="tutorial">{{ __('Tutorial') }}</option>
                        <option value="elective">{{ __('Elective') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Start Time') }} *</label>
                    <input type="time" id="slotStartTime" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('End Time') }} *</label>
                    <input type="time" id="slotEndTime" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Subject') }} *</label>
                <select id="slotSubject" required class="w-full px-3 py-2 border rounded-lg text-sm" onchange="handleSubjectSelection(true)">
                    <option value="">-- Select --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Teacher') }}</label>
                    <select id="slotTeacher" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">-- None --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? '—' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Room') }}</label>
                    <input type="text" id="slotRoom" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Room 101">
                </div>
            </div>
            <div id="labGroupSection" class="grid grid-cols-2 gap-4 hidden">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Lab Group') }}</label>
                    <select id="slotLabGroup" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Groups</option>
                        @foreach($labGroups as $g)<option value="{{ $g }}">Group {{ $g }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Section') }}</label>
                    <select id="slotSection" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="">All</option>
                        @foreach($sections as $s)<option value="{{ $s }}">Section {{ $s }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Remarks') }}</label>
                <input type="text" id="slotRemarks" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div id="labTechnicianSection" class="hidden">
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Assigned Lab Technician') }}</label>
                <input type="hidden" id="slotAssignedLabTechId" value="">
                <select id="slotAssignedLabTech" class="w-full px-3 py-2 border rounded-lg text-sm bg-gray-50 text-gray-700" disabled>
                    <option value="">{{ __('-- Not assigned --') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name ?? '—' }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">{{ __('Loaded automatically from the selected subject when practical is chosen.') }}</p>
            </div>
            <div id="conflictWarning" class="hidden bg-red-50 border border-red-200 rounded-lg p-3">
                <p id="conflictMessage" class="text-sm text-red-700"></p>
            </div>
        </form>
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="slotLocked" class="rounded">
                <label for="slotLocked" class="text-sm text-gray-600">{{ __('Lock') }}</label>
            </div>
            <div class="flex gap-3">
                <button id="slotModalCancel" onclick="closeSlotModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">Cancel</button>
                <button id="slotModalSave" onclick="submitSlot()" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const SEMESTER = '{{ $semester }}';
const CURRENT_SECTION = @json($section ?: '');
const BREAK_OVERRIDE_STORE_URL = @json(route('admin.timetable.break-overrides.store'));
const SUBJECT_ASSIGNMENTS = @json($subjectAssignmentMap);
const SLOT_FIELD_IDS = [
    'slotDay',
    'slotType',
    'slotStartTime',
    'slotEndTime',
    'slotSubject',
    'slotTeacher',
    'slotRoom',
    'slotLabGroup',
    'slotSection',
    'slotRemarks',
    'slotLocked',
];

function toggleLabGroup() {
    const type = document.getElementById('slotType').value;
    document.getElementById('labGroupSection').classList.toggle('hidden', type !== 'practical');
    document.getElementById('labTechnicianSection').classList.toggle('hidden', type !== 'practical');
    handleSubjectSelection(false);
}

function handleSubjectSelection(syncTeacher = true) {
    const subjectId = document.getElementById('slotSubject').value;
    const teacherField = document.getElementById('slotTeacher');
    const slotType = document.getElementById('slotType').value;
    const labTechField = document.getElementById('slotAssignedLabTech');
    const labTechIdField = document.getElementById('slotAssignedLabTechId');
    const subjectMeta = subjectId ? SUBJECT_ASSIGNMENTS[String(subjectId)] : null;

    if (syncTeacher) {
        teacherField.value = subjectMeta?.teacher_id ? String(subjectMeta.teacher_id) : '';
    }

    if (labTechField) {
        if (slotType === 'practical' && subjectMeta?.lab_technician_id) {
            labTechField.value = String(subjectMeta.lab_technician_id);
            if (labTechIdField) {
                labTechIdField.value = String(subjectMeta.lab_technician_id);
            }
        } else if (slotType === 'practical') {
            labTechField.value = '';
            if (labTechIdField) {
                labTechIdField.value = '';
            }
        } else {
            labTechField.value = '';
            if (labTechIdField) {
                labTechIdField.value = '';
            }
        }
    }
}

function setSlotModalReadOnly(readOnly) {
    SLOT_FIELD_IDS.forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.disabled = readOnly;
        }
    });

    const saveButton = document.getElementById('slotModalSave');
    const cancelButton = document.getElementById('slotModalCancel');

    saveButton.classList.toggle('hidden', readOnly);
    cancelButton.textContent = readOnly ? 'Close' : 'Cancel';
}

function populateSlotForm(slot) {
    document.getElementById('slotId').value = slot.id || '';
    document.getElementById('slotDay').value = slot.day_of_week || 'monday';
    document.getElementById('slotType').value = slot.slot_type || 'theory';
    document.getElementById('slotSubject').value = slot.subject_id || '';
    document.getElementById('slotTeacher').value = slot.teacher_id || '';
    document.getElementById('slotStartTime').value = slot.start_time?.substring(0, 5) || '';
    document.getElementById('slotEndTime').value = slot.end_time?.substring(0, 5) || '';
    document.getElementById('slotRoom').value = slot.room || '';
    document.getElementById('slotLabGroup').value = slot.lab_group || '';
    document.getElementById('slotSection').value = slot.section || '';
    document.getElementById('slotRemarks').value = slot.remarks || '';
    document.getElementById('slotLocked').checked = Boolean(slot.is_locked);
    document.getElementById('conflictWarning').classList.add('hidden');
    toggleLabGroup();
    handleSubjectSelection(false);
}

async function fetchSlot(id) {
    const res = await fetch(`/admin/timetable/${id}`, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();

    if (!res.ok) {
        throw new Error(data.message || 'Unable to load timetable slot');
    }

    return data.slot;
}

function openAddSlotModal() {
    document.getElementById('slotModalTitle').textContent = 'Add Timetable Slot';
    populateSlotForm({
        id: '',
        day_of_week: 'monday',
        slot_type: 'theory',
        subject_id: '',
        teacher_id: '',
        start_time: '',
        end_time: '',
        room: '',
        lab_group: '',
        section: '',
        remarks: '',
        is_locked: false,
    });
    setSlotModalReadOnly(false);
    document.getElementById('slotModal').classList.remove('hidden');
}

function openAddSlotModalForBreak(day, startTime, endTime, event = null) {
    if (event) {
        event.stopPropagation();
    }

    document.getElementById('slotModalTitle').textContent = 'Add Timetable Slot';
    populateSlotForm({
        id: '',
        day_of_week: day || 'monday',
        slot_type: 'theory',
        subject_id: '',
        teacher_id: '',
        start_time: startTime || '',
        end_time: endTime || '',
        room: '',
        lab_group: '',
        section: '{{ $section }}' || '',
        remarks: 'Created from break slot',
        is_locked: false,
    });
    setSlotModalReadOnly(false);
    document.getElementById('slotModal').classList.remove('hidden');
}

function editBreakSlot(day, startTime, endTime, event = null) {
    if (event) {
        event.stopPropagation();
    }

    document.getElementById('slotModalTitle').textContent = 'Edit Break Slot';
    populateSlotForm({
        id: '',
        day_of_week: day || 'monday',
        slot_type: 'theory',
        subject_id: '',
        teacher_id: '',
        start_time: startTime || '',
        end_time: endTime || '',
        room: '',
        lab_group: '',
        section: '{{ $section }}' || '',
        remarks: 'Edited from break slot',
        is_locked: false,
    });
    setSlotModalReadOnly(false);
    document.getElementById('slotModal').classList.remove('hidden');
}

function createEmptySlotMarkup(targetId, day, startTime, endTime) {
    return `
        <div
            id="${targetId}"
            class="routine-slot routine-slot--interactive routine-empty-slot"
            onclick="openAddSlotModalForBreak('${day}', '${startTime}', '${endTime}', event)"
            role="button"
            tabindex="0"
            title="Click to add a class in this empty slot"
            onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); openAddSlotModalForBreak('${day}', '${startTime}', '${endTime}', event); }"
        >
            <div class="routine-slot__actions" onclick="event.stopPropagation()">
                <button type="button" onclick="openAddSlotModalForBreak('${day}', '${startTime}', '${endTime}', event)" title="Add class">
                    <i class="bi bi-plus-circle"></i>
                </button>
            </div>
            <div class="routine-slot__title">Empty Slot</div>
            <div class="routine-slot__meta">
                <span>${day.charAt(0).toUpperCase() + day.slice(1)}</span>
                <span>${startTime.substring(0, 5)} - ${endTime.substring(0, 5)}</span>
                <span>No class assigned</span>
            </div>
            <div class="routine-slot__note">Add another class in this free period.</div>
        </div>
    `;
}

async function deleteBreakSlot(targetId, day, startTime, endTime, event = null) {
    if (event) {
        event.stopPropagation();
    }

    const res = await fetch(BREAK_OVERRIDE_STORE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            semester: SEMESTER,
            section: CURRENT_SECTION || '',
            day_of_week: day,
            start_time: startTime,
            end_time: endTime,
        }),
    });

    const result = await res.json();

    if (!res.ok || !result.success) {
        alert(result.message || 'Unable to remove break slot');
        return;
    }

    const target = document.getElementById(targetId);
    if (target) {
        target.outerHTML = createEmptySlotMarkup(targetId, day, startTime, endTime);
    }

    if (typeof showToast === 'function') {
        showToast(`Break removed for ${day} ${startTime.substring(0, 5)}-${endTime.substring(0, 5)}. You can add a class in the empty slot.`, 'success');
    }
}

function closeSlotModal() {
    setSlotModalReadOnly(false);
    document.getElementById('slotModal').classList.add('hidden');
}

async function viewSlot(event, id) {
    event.stopPropagation();

    try {
        const slot = await fetchSlot(id);
        document.getElementById('slotModalTitle').textContent = 'View Timetable Slot';
        populateSlotForm(slot);
        setSlotModalReadOnly(true);
        document.getElementById('slotModal').classList.remove('hidden');
    } catch (error) {
        alert(error.message || 'Unable to load slot details');
    }
}

async function editSlot(id, event = null) {
    if (event) {
        event.stopPropagation();
    }

    try {
        const slot = await fetchSlot(id);
    document.getElementById('slotModalTitle').textContent = 'Edit Timetable Slot';
        populateSlotForm(slot);
        setSlotModalReadOnly(false);
        document.getElementById('slotModal').classList.remove('hidden');
    } catch (error) {
        alert(error.message || 'Unable to load slot details');
    }
}

async function deleteSlot(event, id) {
    event.stopPropagation();

    if (!confirm('Delete this timetable slot?')) {
        return;
    }

    const res = await fetch(`/admin/timetable/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });

    const result = await res.json();

    if (result.success) {
        location.reload();
        return;
    }

    alert(result.message || 'Unable to delete slot');
}

async function submitSlot() {
    const id = document.getElementById('slotId').value;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/admin/timetable/${id}` : '/admin/timetable';
    
    const data = {
        subject_id: document.getElementById('slotSubject').value,
        teacher_id: document.getElementById('slotTeacher').value || null,
        semester: SEMESTER,
        day_of_week: document.getElementById('slotDay').value,
        start_time: document.getElementById('slotStartTime').value,
        end_time: document.getElementById('slotEndTime').value,
        slot_type: document.getElementById('slotType').value,
        room: document.getElementById('slotRoom').value,
        lab_group: document.getElementById('slotLabGroup').value || null,
        section: document.getElementById('slotSection').value || null,
        remarks: document.getElementById('slotRemarks').value,
        is_locked: document.getElementById('slotLocked').checked,
    };

    if (!data.subject_id || !data.start_time || !data.end_time) {
        alert('Please fill required fields');
        return;
    }

    const res = await fetch(url, {
        method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(data)
    });
    const result = await res.json();
    
    if (result.success) {
        closeSlotModal();
        location.reload();
    } else if (result.has_conflict) {
        document.getElementById('conflictWarning').classList.remove('hidden');
        document.getElementById('conflictMessage').textContent = result.message;
    } else {
        alert(result.message || 'Error saving');
    }
}

document.getElementById('slotModal').addEventListener('click', e => { if(e.target.id === 'slotModal') closeSlotModal(); });
</script>
@endsection
