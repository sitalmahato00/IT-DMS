@extends('admin.layouts.app')

@section('title', __('Timetable & Scheduling'))

@section('content')
{{-- Page Header - Using standardized component --}}
@include('admin.components.admin-page-header', [
    'title' => 'Timetable & Scheduling',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Timetable & Scheduling']
    ],
    'rightContent' => '<form method="GET" class="flex items-center gap-2" id="filterForm">
        <select name="semester" class="text-sm px-3 py-2 border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500" onchange="document.getElementById(\'filterForm\').submit()">
            @foreach($semesters as $sem)
                <option value="{{ $sem }}" {{ $semester == $sem ? \'selected\' : \'\' }}>Semester {{ $sem }}</option>
            @endforeach
        </select>
        <select name="section" class="text-sm px-3 py-2 border border-gray-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500" onchange="document.getElementById(\'filterForm\').submit()">
            <option value="">All Sections</option>
            @foreach($sections as $sec)
                <option value="{{ $sec }}" {{ $section == $sec ? \'selected\' : \'\' }}>Section {{ $sec }}</option>
            @endforeach
        </select>
    </form>'
])

{{-- Add Slot Button --}}
<div class="flex justify-end mb-4">
    <button onclick="openAddSlotModal()"
        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
        <i class="bi bi-plus-circle"></i> Add Slot
    </button>
    
    <div class="relative group ml-2">
        <button class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 text-sm rounded-lg transition">
            <i class="bi bi-download"></i> Export
            <i class="bi bi-chevron-down"></i>
        </button>
        <div class="absolute right-0 mt-1 w-40 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg hidden group-hover:block z-20">
            <a href="{{ route('admin.timetable.print', ['semester' => $semester]) }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                <i class="bi bi-printer mr-2"></i>Print
            </a>
            <a href="{{ route('admin.timetable.exportExcel', ['semester' => $semester]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                <i class="bi bi-file-earmark-excel mr-2"></i>Excel
            </a>
        </div>
    </div>
</div>

{{-- Stats Cards - Using standardized component --}}
@include('admin.components.admin-stats-cards', [
    'cards' => [
        ['title' => 'Total Slots', 'value' => $stats['total_slots'], 'icon' => 'bi-grid-3x3', 'color' => 'blue'],
        ['title' => 'Theory', 'value' => $stats['theory_slots'], 'icon' => 'bi-book', 'color' => 'gray'],
        ['title' => 'Practical', 'value' => $stats['practical_slots'], 'icon' => 'bi-laptop', 'color' => 'green'],
        ['title' => 'Elective', 'value' => $stats['elective_slots'], 'icon' => 'bi-people', 'color' => 'purple'],
        ['title' => 'Conflicts', 'value' => $stats['conflicts'], 'icon' => 'bi-exclamation-triangle', 'color' => $stats['conflicts'] > 0 ? 'red' : 'gray'],
    ]
])

<div class="space-y-4">

    <!-- Legend -->
    <div class="flex flex-wrap items-center gap-3 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 px-4 py-3">
        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Legend:</span>
        <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">Theory</span>
        <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Practical</span>
        <span class="px-2 py-1 rounded text-xs font-medium bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300">Tutorial</span>
        <span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300">Elective</span>
        @if($slots->whereNotNull('lab_group')->isNotEmpty())
            @foreach($labGroups as $group)
                @php $colors = ['A'=>'bg-rose','B'=>'bg-orange','C'=>'bg-amber','D'=>'bg-emerald']; @endphp
                <span class="px-2 py-1 rounded text-xs font-medium {{ $colors[$group] ?? 'bg-gray' }}-100 dark:{{ $colors[$group] ?? 'bg-gray' }}-900 text-{{ $colors[$group] ?? 'gray' }}-700 dark:text-{{ $colors[$group] ?? 'gray' }}-300">Lab {{ $group }}</span>
            @endforeach
        @endif
    </div>

    <!-- Timetable Grid: Days as Columns -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                Weekly Schedule — Semester {{ $semester }}
            </h3>
        </div>

        @if($slots->isEmpty())
            <div class="p-16 text-center">
                <i class="bi bi-grid-3x3-gap text-5xl text-gray-200 dark:text-gray-600 block mb-4"></i>
                <h3 class="text-base font-semibold text-gray-600 dark:text-gray-300 mb-2">No Classes Scheduled</h3>
                <button onclick="openAddSlotModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                    <i class="bi bi-plus-circle"></i> Add First Slot
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                        <tr>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 w-20 bg-gray-100 dark:bg-slate-800">Time</th>
                            @foreach($days as $day)
                                <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 min-w-[160px]">{{ ucfirst($day) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @php
                            // Get unique time slots from data
                            $allTimeSlots = [];
                            foreach($slots as $slot) {
                                $key = $slot->start_time;
                                if(!isset($allTimeSlots[$key])) {
                                    $allTimeSlots[$key] = [
                                        'start' => $slot->start_time,
                                        'end' => $slot->end_time,
                                    ];
                                }
                            }
                            ksort($allTimeSlots);
                        @endphp
                        @foreach($allTimeSlots as $timeSlot)
                        <tr>
                            <td class="px-2 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-slate-800 border-r dark:border-slate-700">
                                <div class="flex flex-col items-center">
                                    <span>{{ \Carbon\Carbon::parse($timeSlot['start'])->format('g:i A') }}</span>
                                    <span class="text-[10px] font-normal text-gray-400 dark:text-gray-500">to</span>
                                    <span>{{ \Carbon\Carbon::parse($timeSlot['end'])->format('g:i A') }}</span>
                                </div>
                            </td>
                            @foreach($days as $day)
                                @php
                                    $daySlots = $slotsByDay[$day] ?? collect();
                                    $timeSlots = $daySlots->filter(function($s) use ($timeSlot) {
                                        return $s->start_time == $timeSlot['start'];
                                    });
                                    
                                    // Separate theory/other from practical lab groups
                                    $theorySlots = $timeSlots->filter(function($s) { return is_null($s->lab_group); });
                                    $labSlots = $timeSlots->filter(function($s) { return !is_null($s->lab_group); });
                                @endphp
                                <td class="px-1 py-1 border-r border-gray-100 dark:border-slate-700 min-h-[70px] align-top dark:bg-slate-800/30">
                                    @if($timeSlots->isNotEmpty())
                                        <div class="flex flex-col gap-1">
                                        {{-- Theory slots (stacked) --}}
                                        @foreach($theorySlots as $slot)
                                            @php
                                                $colors = [
                                                    'theory' => 'bg-blue-100 dark:bg-blue-900 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
                                                    'practical' => 'bg-green-100 dark:bg-green-900 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
                                                    'tutorial' => 'bg-amber-100 dark:bg-amber-900 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200',
                                                    'elective' => 'bg-purple-100 dark:bg-purple-900 border-purple-200 dark:border-purple-800 text-purple-800 dark:text-purple-200'
                                                ];
                                                $color = $colors[$slot->slot_type] ?? 'bg-gray-100 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200';
                                                
                                                // Check conflict
                                                $hasConflict = $conflicts->contains(function($c) use ($slot) {
                                                    return $c['slot1_id'] === $slot->id || $c['slot2_id'] === $slot->id;
                                                });
                                            @endphp
                                            <div onclick="editSlot({{ $slot->id }})" 
                                                class="p-1.5 rounded border text-xs cursor-pointer hover:shadow-md transition relative {{ $color }}
                                                {{ $hasConflict ? 'ring-2 ring-red-500' : '' }}
                                                {{ $slot->is_locked ? 'opacity-70' : '' }}">
                                                @if($hasConflict)
                                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-[8px]">!</span>
                                                @endif
                                                @if($slot->is_locked)
                                                    <span class="absolute top-1 right-1 text-gray-400"><i class="bi bi-lock-fill text-[8px]"></i></span>
                                                @endif
                                                <span class="font-semibold block truncate">{{ $slot->subject->subject_name ?? '—' }}</span>
                                                <span class="text-[10px] truncate block">{{ $slot->teacher->user->name ?? '' }}</span>
                                                <span class="text-[9px] truncate block opacity-70">{{ $slot->room }}</span>
                                            </div>
                                        @endforeach
                                        
                                        {{-- Lab slots: side by side in same cell --}}
                                        @if($labSlots->isNotEmpty())
                                            <div class="flex gap-1">
                                                @foreach($labSlots as $slot)
                                                    @php
                                                        $labColors = [
                                                            'A' => 'bg-rose-100 border-rose-200 text-rose-800',
                                                            'B' => 'bg-orange-100 border-orange-200 text-orange-800',
                                                            'C' => 'bg-amber-100 border-amber-200 text-amber-800',
                                                            'D' => 'bg-emerald-100 border-emerald-200 text-emerald-800',
                                                            'E' => 'bg-cyan-100 border-cyan-200 text-cyan-800',
                                                            'F' => 'bg-pink-100 border-pink-200 text-pink-800',
                                                        ];
                                                        $color = $labColors[$slot->lab_group] ?? 'bg-green-100';
                                                        
                                                        $hasConflict = $conflicts->contains(function($c) use ($slot) {
                                                            return $c['slot1_id'] === $slot->id || $c['slot2_id'] === $slot->id;
                                                        });
                                                    @endphp
                                                    <div onclick="editSlot({{ $slot->id }})" 
                                                        class="flex-1 p-1 rounded border text-[10px] cursor-pointer hover:shadow-md transition relative {{ $color }}
                                                        {{ $hasConflict ? 'ring-2 ring-red-500' : '' }}
                                                        {{ $slot->is_locked ? 'opacity-70' : '' }}">
                                                        @if($hasConflict)
                                                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 text-white rounded-full flex items-center justify-center text-[6px]">!</span>
                                                        @endif
                                                        @if($slot->is_locked)
                                                            <span class="absolute top-0.5 right-0.5 text-gray-400"><i class="bi bi-lock-fill text-[6px]"></i></span>
                                                        @endif
                                                        <span class="block text-[9px] font-bold">Lab {{ $slot->lab_group }}</span>
                                                        <span class="font-semibold block truncate">{{ $slot->subject->subject_name ?? '—' }}</span>
                                                        <span class="text-[8px] truncate block">{{ $slot->teacher->user->name ?? '' }}</span>
                                                        <span class="text-[7px] truncate block opacity-70">{{ $slot->room }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
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
                <select id="slotSubject" required class="w-full px-3 py-2 border rounded-lg text-sm">
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
                <button onclick="closeSlotModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">Cancel</button>
                <button onclick="submitSlot()" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const SEMESTER = '{{ $semester }}';

function toggleLabGroup() {
    const type = document.getElementById('slotType').value;
    document.getElementById('labGroupSection').classList.toggle('hidden', type !== 'practical');
}

function openAddSlotModal() {
    document.getElementById('slotModalTitle').textContent = 'Add Timetable Slot';
    document.getElementById('slotId').value = '';
    document.getElementById('slotDay').value = 'monday';
    document.getElementById('slotType').value = 'theory';
    document.getElementById('slotSubject').value = '';
    document.getElementById('slotTeacher').value = '';
    document.getElementById('slotStartTime').value = '';
    document.getElementById('slotEndTime').value = '';
    document.getElementById('slotRoom').value = '';
    document.getElementById('slotLabGroup').value = '';
    document.getElementById('slotSection').value = '';
    document.getElementById('slotRemarks').value = '';
    document.getElementById('slotLocked').checked = false;
    document.getElementById('conflictWarning').classList.add('hidden');
    toggleLabGroup();
    document.getElementById('slotModal').classList.remove('hidden');
}

function closeSlotModal() { document.getElementById('slotModal').classList.add('hidden'); }

async function editSlot(id) {
    const res = await fetch(`/admin/timetable/${id}`, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    const slot = data.slot;
    document.getElementById('slotModalTitle').textContent = 'Edit Timetable Slot';
    document.getElementById('slotId').value = slot.id;
    document.getElementById('slotDay').value = slot.day_of_week;
    document.getElementById('slotType').value = slot.slot_type;
    document.getElementById('slotSubject').value = slot.subject_id;
    document.getElementById('slotTeacher').value = slot.teacher_id || '';
    document.getElementById('slotStartTime').value = slot.start_time?.substring(0,5) || '';
    document.getElementById('slotEndTime').value = slot.end_time?.substring(0,5) || '';
    document.getElementById('slotRoom').value = slot.room || '';
    document.getElementById('slotLabGroup').value = slot.lab_group || '';
    document.getElementById('slotSection').value = slot.section || '';
    document.getElementById('slotRemarks').value = slot.remarks || '';
    document.getElementById('slotLocked').checked = slot.is_locked;
    toggleLabGroup();
    document.getElementById('slotModal').classList.remove('hidden');
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
