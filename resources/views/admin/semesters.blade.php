@extends('admin.layouts.app')

@section('title', __('Semester Management'))

@push('styles')
<style>
    .action-btn {
        @apply inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200;
    }
    
    .action-btn-view {
        @apply text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30;
    }
    
    .action-btn-edit {
        @apply text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/30;
    }
    
    .action-btn-delete {
        @apply text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    @include('admin.components.admin-page-header', [
        'title' => 'Semester Management',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Semesters']
        ],
        'addButton' => [
            'label' => 'Add Semester',
            'onclick' => "openCreateModal()",
            'color' => 'green'
        ]
    ])

    <!-- Stats Cards -->
    @include('admin.components.admin-stats-cards', [
        'cards' => [
            ['title' => 'Total', 'value' => $stats['total'] ?? 0, 'icon' => 'bi-archive', 'color' => 'blue'],
            ['title' => 'Open', 'value' => $stats['open'] ?? 0, 'icon' => 'bi-door-open', 'color' => 'green'],
            ['title' => 'Active', 'value' => $stats['active'] ?? 0, 'icon' => 'bi-play-circle', 'color' => 'yellow'],
            ['title' => 'Upcoming', 'value' => $stats['upcoming'] ?? 0, 'icon' => 'bi-clock', 'color' => 'gray']
        ]
    ])

    <!-- Semesters Grid -->
    @if($enrichedSemesters->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-16 text-center">
            <i class="bi bi-calendar3 text-5xl text-gray-300 dark:text-gray-600 block mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('No Semesters Yet') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('Start by creating your first semester for the IT department.') }}</p>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                <i class="bi bi-plus-circle"></i> {{ __('Create First Semester') }}
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($enrichedSemesters as $semester)
                <div class="rounded-xl border-2 bg-white p-5 transition hover:shadow-md {{ $semester->is_active ? 'border-green-400 bg-green-50/30' : 'border-gray-200' }}">
                    <!-- Card Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                                {{ $semester->number }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $semester->name }}</h3>
                                @if($semester->academic_year)
                                    <p class="text-xs text-gray-400">{{ $semester->academic_year }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            @if($semester->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                                    {{ __('Active') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-3">
                        @php
                            $statusClasses = [
                                'open'     => 'bg-green-100 text-green-700 border border-green-200',
                                'closed'   => 'bg-gray-100 text-gray-600 border border-gray-200',
                                'upcoming' => 'bg-blue-100 text-blue-700 border border-blue-200',
                            ];
                            $statusIcons = [
                                'open'     => 'bi-unlock',
                                'closed'   => 'bi-lock',
                                'upcoming' => 'bi-hourglass-split',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$semester->status] ?? 'bg-gray-100 text-gray-600' }}">
                            <i class="bi {{ $statusIcons[$semester->status] ?? 'bi-question' }}"></i>
                            {{ ucfirst($semester->status) }}
                        </span>
                    </div>

                    <!-- Date Range -->
                    @if($semester->start_date || $semester->end_date)
                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                            <i class="bi bi-calendar-range text-gray-400"></i>
                            <span>
                                {{ $semester->start_date ? \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') : '—' }}
                                →
                                {{ $semester->end_date ? \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') : '—' }}
                            </span>
                        </div>
                    @endif

                    <!-- Stats Row -->
                    <div class="grid grid-cols-3 gap-2 py-3 border-t border-gray-100 mb-3">
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $semester->student_count }}</p>
                            <p class="text-[11px] text-gray-400">{{ __('Students') }}</p>
                        </div>
                        <div class="text-center border-x border-gray-100">
                            <p class="text-lg font-bold text-gray-900">{{ $semester->subject_count }}</p>
                            <p class="text-[11px] text-gray-400">{{ __('Subjects') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-purple-600">{{ $semester->elective_count }}</p>
                            <p class="text-[11px] text-gray-400">{{ __('Electives') }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Open/Close Toggle -->
                        <button onclick="toggleSemesterStatus({{ $semester->id }}, '{{ $semester->status }}')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition {{ $semester->status === 'open' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            <i class="bi {{ $semester->status === 'open' ? 'bi-lock' : 'bi-unlock' }}"></i>
                            {{ $semester->status === 'open' ? __('Close') : __('Open') }}
                        </button>

                        @if(!$semester->is_active)
                            <!-- Set Active -->
                            <button onclick="setActiveSemester({{ $semester->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg transition">
                                <i class="bi bi-check-circle"></i>
                                {{ __('Set Active') }}
                            </button>
                        @endif

                        <!-- Edit -->
                        <button onclick="editSemester({{ $semester->id }})"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/30">
                            <i class="bi bi-pencil text-sm"></i>
                        </button>

                        <!-- Delete -->
                        <button onclick="deleteSemester({{ $semester->id }}, '{{ $semester->name }}')"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-red-600 dark:text-red-400 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/30">
                            <i class="bi bi-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Create/Edit Modal -->
<div id="semesterModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 id="modalTitle" class="text-base font-semibold text-gray-900">{{ __('Add Semester') }}</h3>
            <button onclick="closeModal()" class="p-1.5 hover:bg-gray-200 rounded-lg transition">
                <i class="bi bi-x text-lg text-gray-500"></i>
            </button>
        </div>
        <form id="semesterForm" class="p-6 space-y-4" onsubmit="submitSemesterForm(event)">
            @csrf
            <input type="hidden" id="semesterId" name="semester_id" value="">
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Semester Number') }} *</label>
                    <select id="semesterNumber" name="number" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}">{{ __('Semester') }} {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Status') }} *</label>
                    <select id="semesterStatus" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="upcoming">{{ __('Upcoming') }}</option>
                        <option value="open">{{ __('Open') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Semester Name (English)') }} *</label>
                <input type="text" id="semesterName" name="name" required
                    placeholder="{{ __('e.g. First Semester') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Academic Year') }}</label>
                <input type="text" id="semesterAcademicYear" name="academic_year"
                    placeholder="{{ __('e.g. 2082/083') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Start Date') }}</label>
                    <input type="date" id="semesterStartDate" name="start_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('End Date') }}</label>
                    <input type="date" id="semesterEndDate" name="end_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Max Credits') }}</label>
                    <input type="number" id="semesterMaxCredits" name="max_credits" min="1" max="40" value="24"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Total Weeks') }}</label>
                    <input type="number" id="semesterTotalWeeks" name="total_weeks" min="1" max="52" value="16"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="semesterIsActive" name="is_active" value="1" class="sr-only peer">
                    <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
                <span class="text-sm text-gray-700 font-medium">{{ __('Set as Active Semester') }}</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">{{ __('Remarks') }}</label>
                <textarea id="semesterRemarks" name="remarks" rows="2" placeholder="{{ __('Optional notes...') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
        </form>
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-100 text-gray-700 transition">
                {{ __('Cancel') }}
            </button>
            <button onclick="submitSemesterForm(event)" id="saveBtn" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                <i class="bi bi-check2 mr-1"></i> {{ __('Save Semester') }}
            </button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 text-center">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-trash text-2xl text-red-600"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Delete Semester?') }}</h3>
        <p class="text-sm text-gray-500 mb-6" id="deleteConfirmMsg">{{ __('This action cannot be undone.') }}</p>
        <div class="flex justify-center gap-3">
            <button onclick="document.getElementById('deleteModal').classList.add('hidden')"
                class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                {{ __('Cancel') }}
            </button>
            <button id="confirmDeleteBtn"
                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                {{ __('Delete') }}
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function openCreateModal() {
    document.getElementById('modalTitle').textContent = '{{ __("Add Semester") }}';
    document.getElementById('semesterId').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('semesterNumber').value = '1';
    document.getElementById('semesterName').value = '';
    document.getElementById('semesterAcademicYear').value = '';
    document.getElementById('semesterStartDate').value = '';
    document.getElementById('semesterEndDate').value = '';
    document.getElementById('semesterMaxCredits').value = '24';
    document.getElementById('semesterTotalWeeks').value = '16';
    document.getElementById('semesterStatus').value = 'upcoming';
    document.getElementById('semesterIsActive').checked = false;
    document.getElementById('semesterRemarks').value = '';
    document.getElementById('semesterModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('semesterModal').classList.add('hidden');
}

async function editSemester(id) {
    try {
        showLoading('Loading semester...');
        const res = await fetch(`/admin/semesters/${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const semester = await res.json();
        hideLoading();

        document.getElementById('modalTitle').textContent = '{{ __("Edit Semester") }}';
        document.getElementById('semesterId').value = semester.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('semesterNumber').value = semester.number;
        document.getElementById('semesterName').value = semester.name;
        document.getElementById('semesterAcademicYear').value = semester.academic_year || '';
        document.getElementById('semesterStartDate').value = semester.start_date || '';
        document.getElementById('semesterEndDate').value = semester.end_date || '';
        document.getElementById('semesterMaxCredits').value = semester.max_credits || 24;
        document.getElementById('semesterTotalWeeks').value = semester.total_weeks || 16;
        document.getElementById('semesterStatus').value = semester.status || 'upcoming';
        document.getElementById('semesterIsActive').checked = semester.is_active == 1;
        document.getElementById('semesterRemarks').value = semester.remarks || '';
        document.getElementById('semesterModal').classList.remove('hidden');
    } catch (e) {
        hideLoading();
        showToast('Failed to load semester data.', 'error');
    }
}

async function submitSemesterForm(e) {
    if (e) e.preventDefault();
    const id = document.getElementById('semesterId').value;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/admin/semesters/${id}` : '/admin/semesters';

    const formData = new FormData(document.getElementById('semesterForm'));
    const data = {};
    formData.forEach((v, k) => { data[k] = v; });

    // checkbox handling
    data.is_active = document.getElementById('semesterIsActive').checked ? 1 : 0;

    try {
        showLoading('Saving...');
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        const result = await res.json();
        hideLoading();
        closeModal();
        if (result.success) {
            showToast(id ? '{{ __("Semester updated!") }}' : '{{ __("Semester created!") }}', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(result.message || 'Error saving semester.', 'error');
        }
    } catch (e) {
        hideLoading();
        showToast('An error occurred.', 'error');
    }
}

async function toggleSemesterStatus(id, currentStatus) {
    try {
        const res = await fetch(`/admin/semesters/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        const result = await res.json();
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(result.message || 'Error toggling status.', 'error');
        }
    } catch (e) {
        showToast('An error occurred.', 'error');
    }
}

async function setActiveSemester(id) {
    try {
        const res = await fetch(`/admin/semesters/${id}/set-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        const result = await res.json();
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(result.message || 'Error.', 'error');
        }
    } catch (e) {
        showToast('An error occurred.', 'error');
    }
}

let pendingDeleteId = null;

function deleteSemester(id, name) {
    pendingDeleteId = id;
    document.getElementById('deleteConfirmMsg').textContent = `{{ __("Are you sure you want to delete") }} "${name}"? {{ __("This cannot be undone.") }}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!pendingDeleteId) return;
    document.getElementById('deleteModal').classList.add('hidden');
    try {
        showLoading('Deleting...');
        const res = await fetch(`/admin/semesters/${pendingDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
        });
        const result = await res.json();
        hideLoading();
        if (result.success) {
            showToast('{{ __("Semester deleted.") }}', 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(result.message || 'Error deleting.', 'error');
        }
    } catch (e) {
        hideLoading();
        showToast('An error occurred.', 'error');
    }
});

// Close modal on backdrop click
document.getElementById('semesterModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
