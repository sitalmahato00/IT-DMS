@extends('admin.layouts.app')

@section('title', 'Students')

@section('styles')
<script>document.documentElement.classList.add('students-ui-enhanced');</script>
<style>
    html.students-ui-enhanced:not(.dark) .students-stats > .grid,
    html.students-ui-enhanced:not(.dark) .students-filter-panel > div {
        margin-bottom: 0;
    }

    html.students-ui-enhanced:not(.dark) .students-stats > .grid > div {
        position: relative;
        overflow: hidden;
        border-width: 2px;
        border-radius: 1rem;
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    html.students-ui-enhanced:not(.dark) .students-stats > .grid > div:hover,
    html.students-ui-enhanced:not(.dark) .student-photo-panel:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    html.students-ui-enhanced:not(.dark) .students-stats > .grid > div:nth-child(1) { border-color: #86efac; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 56%, #f0fdf4 100%); }
    html.students-ui-enhanced:not(.dark) .students-stats > .grid > div:nth-child(2) { border-color: #fda4af; background: linear-gradient(135deg, #fff1f2 0%, #ffffff 56%, #fff1f2 100%); }
    html.students-ui-enhanced:not(.dark) .students-stats > .grid > div:nth-child(3) { border-color: #fcd34d; background: linear-gradient(135deg, #fffbeb 0%, #ffffff 56%, #fffbeb 100%); }
    html.students-ui-enhanced:not(.dark) .students-stats > .grid > div:nth-child(4) { border-color: #c4b5fd; background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 56%, #f5f3ff 100%); }

    html.students-ui-enhanced:not(.dark) .students-filter-panel > div,
    html.students-ui-enhanced:not(.dark) .students-table-panel,
    html.students-ui-enhanced:not(.dark) .student-modal-panel {
        overflow: hidden;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    html.students-ui-enhanced:not(.dark) .students-filter-panel label,
    html.students-ui-enhanced:not(.dark) .student-directory-head th,
    html.students-ui-enhanced:not(.dark) .student-form label,
    html.students-ui-enhanced:not(.dark) .student-detail-grid label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }

    html.students-ui-enhanced:not(.dark) .students-filter-panel input:not([type='checkbox']):not([type='radio']),
    html.students-ui-enhanced:not(.dark) .students-filter-panel select,
    html.students-ui-enhanced:not(.dark) .students-toolbar select,
    html.students-ui-enhanced:not(.dark) .student-form input:not([type='checkbox']):not([type='radio']):not([type='file']),
    html.students-ui-enhanced:not(.dark) .student-form select,
    html.students-ui-enhanced:not(.dark) .student-form textarea {
        min-height: 2.9rem;
        border: 2px solid #cbd5e1;
        border-radius: 0.85rem;
        background: #ffffff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    html.students-ui-enhanced:not(.dark) .students-filter-panel input:not([type='checkbox']):not([type='radio']):focus,
    html.students-ui-enhanced:not(.dark) .students-filter-panel select:focus,
    html.students-ui-enhanced:not(.dark) .students-toolbar select:focus,
    html.students-ui-enhanced:not(.dark) .student-form input:not([type='checkbox']):not([type='radio']):not([type='file']):focus,
    html.students-ui-enhanced:not(.dark) .student-form select:focus,
    html.students-ui-enhanced:not(.dark) .student-form textarea:focus {
        outline: none;
        border-color: #f43f5e;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.1);
    }

    html.students-ui-enhanced:not(.dark) .students-toolbar,
    html.students-ui-enhanced:not(.dark) .students-pagination,
    html.students-ui-enhanced:not(.dark) .student-modal-footer {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    }

    html.students-ui-enhanced:not(.dark) .students-toolbar { border-bottom: 1px solid #e2e8f0; }
    html.students-ui-enhanced:not(.dark) .students-pagination,
    html.students-ui-enhanced:not(.dark) .student-modal-footer,
    html.students-ui-enhanced:not(.dark) .student-form-actions { border-top: 1px solid #e2e8f0; }

    html.students-ui-enhanced:not(.dark) .students-toolbar-btn,
    html.students-ui-enhanced:not(.dark) .student-secondary-btn,
    html.students-ui-enhanced:not(.dark) .student-primary-btn,
    html.students-ui-enhanced:not(.dark) .action-btn {
        box-shadow: 0 16px 30px -24px rgba(15, 23, 42, 0.45);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    html.students-ui-enhanced:not(.dark) .students-toolbar-btn:hover,
    html.students-ui-enhanced:not(.dark) .student-secondary-btn:hover,
    html.students-ui-enhanced:not(.dark) .student-primary-btn:hover,
    html.students-ui-enhanced:not(.dark) .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.5);
    }

    html.students-ui-enhanced:not(.dark) .student-directory-table { border-collapse: separate; border-spacing: 0; }
    html.students-ui-enhanced:not(.dark) .student-directory-head th { background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-bottom: 1px solid #e2e8f0; color: #64748b; }
    html.students-ui-enhanced:not(.dark) .student-row td { border-bottom: 1px solid #e2e8f0; transition: background-color 0.18s ease; vertical-align: middle; }
    html.students-ui-enhanced:not(.dark) .student-row:nth-child(even) td { background: #f8fafc; }
    html.students-ui-enhanced:not(.dark) .student-row:hover td { background: #fff7f8; }

    html.students-ui-enhanced:not(.dark) .student-avatar,
    html.students-ui-enhanced:not(.dark) .student-photo-frame {
        border: 1px solid #fecdd3;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    html.students-ui-enhanced:not(.dark) .student-roll-chip,
    html.students-ui-enhanced:not(.dark) .student-semester-chip,
    html.students-ui-enhanced:not(.dark) .student-year-chip,
    html.students-ui-enhanced:not(.dark) .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
    }

    html.students-ui-enhanced:not(.dark) .student-name { color: #0f172a; font-weight: 700; }
    html.students-ui-enhanced:not(.dark) .student-meta-text { color: #475569; }
    html.students-ui-enhanced:not(.dark) .student-roll-chip { background: #eff6ff; color: #1d4ed8; }
    html.students-ui-enhanced:not(.dark) .student-semester-chip { background: #ecfeff; color: #0f766e; }
    html.students-ui-enhanced:not(.dark) .student-year-chip { background: #f8fafc; color: #475569; }
    html.students-ui-enhanced:not(.dark) .badge-active { background: #dcfce7; color: #166534; }
    html.students-ui-enhanced:not(.dark) .badge-inactive { background: #fee2e2; color: #b91c1c; }
    html.students-ui-enhanced:not(.dark) .badge-pending,
    html.students-ui-enhanced:not(.dark) .badge-suspended { background: #fef3c7; color: #b45309; }
    html.students-ui-enhanced:not(.dark) .badge-alumni { background: #f3e8ff; color: #7e22ce; }

    html.students-ui-enhanced:not(.dark) .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.8rem;
        background: #ffffff;
    }

    html.students-ui-enhanced:not(.dark) .action-btn-view { color: #2563eb; }
    html.students-ui-enhanced:not(.dark) .action-btn-edit { color: #d97706; }
    html.students-ui-enhanced:not(.dark) .action-btn-delete { color: #dc2626; }
    html.students-ui-enhanced:not(.dark) .student-empty-state { color: #64748b; font-weight: 500; }

    html.students-ui-enhanced:not(.dark) .student-modal-header {
        position: sticky;
        top: 0;
        z-index: 5;
        border-bottom: none;
        background: linear-gradient(135deg, #fb7185 0%, #e11d48 100%);
    }

    html.students-ui-enhanced:not(.dark) .student-modal-header p { color: #ffe4e6; }
    html.students-ui-enhanced:not(.dark) .student-modal-close { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; border-radius: 0.8rem; background: rgba(255, 255, 255, 0.14); }
    html.students-ui-enhanced:not(.dark) .student-photo-panel { border: 1px solid #e2e8f0; border-radius: 1rem; background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%); padding: 1.25rem; transition: transform 0.25s ease, box-shadow 0.25s ease; }
    html.students-ui-enhanced:not(.dark) .student-upload-btn { border: 1px solid #fecdd3; border-radius: 0.85rem; background: #fff1f2; color: #be123c; }
    html.students-ui-enhanced:not(.dark) .student-detail-grid > div { padding: 0.95rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.9rem; background: #ffffff; }
    html.students-ui-enhanced:not(.dark) .student-detail-grid p { color: #0f172a; }
    html.students-ui-enhanced:not(.dark) .student-secondary-btn { border: 1px solid #cbd5e1; background: #ffffff; color: #334155; }
    html.students-ui-enhanced:not(.dark) .student-form-actions { margin-top: 1.5rem; padding-top: 1rem; }

    @media (max-width: 768px) {
        html.students-ui-enhanced:not(.dark) .student-directory-table th,
        html.students-ui-enhanced:not(.dark) .student-directory-table td { padding: 0.75rem 0.5rem; }
    }

    @media (max-width: 640px) {
        .student-directory-table { min-width: 48rem; }
        .student-directory-table th,
        .student-directory-table td { white-space: nowrap; }
        .students-toolbar > div,
        .students-toolbar .flex,
        .students-toolbar .flex.items-center { flex-wrap: wrap; }
        .students-toolbar-select { flex: 1 1 11rem; min-width: 0; }
    }
</style>
@endsection

@section('content')
@php
    $nonAlumniStudentScope = function ($query) {
        $query->where(function ($subQuery) {
            $subQuery->where('is_alumni', 0)->orWhereNull('is_alumni');
        });
    };
@endphp

<!-- Page Header -->
@include('admin.components.admin-page-header', [
    'title' => 'Students',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Students']
    ],
    'addButton' => [
        'label' => 'Add Student',
        'route' => route('admin.students.create'),
        'color' => 'green'
    ]
])

<div class="students-page space-y-6 w-full">
    <!-- Stats Cards -->
    <div class="students-stats">
        @include('admin.components.admin-stats-cards', [
            'cards' => [
                ['title' => 'Active Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', function ($q) use ($nonAlumniStudentScope) { $nonAlumniStudentScope($q); $q->where('status', 'active'); })->count(), 'icon' => 'bi-check-circle', 'color' => 'green', 'subtitle' => 'Excludes alumni records'],
                ['title' => 'Inactive Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', function ($q) use ($nonAlumniStudentScope) { $nonAlumniStudentScope($q); $q->where('status', 'inactive'); })->count(), 'icon' => 'bi-x-circle', 'color' => 'red', 'subtitle' => 'Excludes alumni records'],
                ['title' => 'Suspended Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', function ($q) use ($nonAlumniStudentScope) { $nonAlumniStudentScope($q); $q->where('status', 'suspended'); })->count(), 'icon' => 'bi-pause-circle', 'color' => 'yellow', 'subtitle' => 'Excludes alumni records'],
                ['title' => 'Alumni', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('is_alumni', 1))->count(), 'icon' => 'bi-mortarboard', 'color' => 'purple'],
            ]
        ])
    </div>

    <!-- Filter Card -->
    <div class="students-filter-panel">
        @include('admin.components.admin-filter-card', [
            'formAction' => route('admin.students'),
            'filters' => [
                ['name' => 'q', 'type' => 'text', 'placeholder' => 'Search by name or email...', 'value' => request('q'), 'label' => 'Search'],
                ['name' => 'status', 'type' => 'select', 'options' => ['all' => 'All', 'active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'alumni' => 'Alumni'], 'value' => request('status'), 'label' => 'Status'],
                ['name' => 'semester', 'type' => 'select', 'options' => ['' => 'All Semesters', '1' => 'Sem 1', '2' => 'Sem 2', '3' => 'Sem 3', '4' => 'Sem 4', '5' => 'Sem 5', '6' => 'Sem 6'], 'value' => request('semester'), 'label' => 'Semester'],
                ['name' => 'academic_year', 'type' => 'select', 'options' => array_merge(['' => 'All Years'], array_combine($academicYears ?? [], $academicYears ?? [])), 'value' => request('academic_year'), 'label' => 'Academic Year']
            ],
            'showReset' => true,
            'resetRoute' => route('admin.students')
        ])
    </div>

    <!-- Data Table -->
    <div class="students-table-panel rounded-xl border border-gray-200 bg-white shadow-md dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
        <!-- Toolbar -->
        <div class="students-toolbar px-4 py-4 border-b border-gray-100 dark:border-slate-700 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="select_all" class="form-checkbox rounded text-red-600" />
                    <span class="text-sm text-gray-700 dark:text-gray-300 hidden sm:inline">Select all</span>
                    <select id="bulk_action" class="students-toolbar-select px-3 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 rounded-lg text-sm">
                        <option value="">Bulk actions</option>
                        <option value="set_status_active">Set Active</option>
                        <option value="set_status_inactive">Set Inactive</option>
                        <option value="move_alumni">Move to Alumni</option>
                        <option value="export_csv">Export (CSV)</option>
                    </select>
                    <button id="apply_bulk" class="students-toolbar-btn px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Apply</button>
                </div>
                <div class="flex items-center gap-2">
                    <form id="exportForm" method="GET" action="{{ route('admin.students.export') }}" class="inline-block">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button type="submit" class="students-toolbar-btn px-4 py-1.5 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                        </button>
                    </form>
                    <button onclick="adminOpenPrintPreview('{{ route('students.print-list') }}')" class="students-toolbar-btn px-4 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="student-directory-table w-full text-left text-sm">
                <thead class="student-directory-head">
                    <tr>
                        <th class="px-4 py-3.5"><input type="checkbox" id="th_select_all" class="form-checkbox rounded text-red-600" /></th>
                        <th>User</th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students ?? \App\Models\User::where('role','student')->paginate(15) as $student)
                        <tr class="student-row">
                            <td class="px-4 py-4" data-label="Select"><input type="checkbox" class="student-checkbox rounded text-red-600" data-id="{{ $student->id }}" /></td>
                            <td class="px-4 py-4" data-label="User">
                                <div class="flex items-center gap-2">
                                    @php
                                        $studentPhotoUrl = $student->student->profile_photo_url ?? null;
                                    @endphp
                                    @if($studentPhotoUrl)
                                        <img src="{{ $studentPhotoUrl }}" alt="{{ $student->name }}" class="student-avatar w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="student-avatar w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-semibold text-red-700">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="student-name font-medium">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600" data-label="ID"><span class="student-roll-chip">{{ $student->student->roll_no ?? $student->id }}</span></td>
                            <td class="px-4 py-4 text-gray-600" data-label="Email"><span class="student-meta-text">{{ $student->email }}</span></td>
                            <td class="px-4 py-4" data-label="Semester">
                                @if($student->student->is_alumni)
                                    <span class="badge badge-alumni">Graduate</span>
                                @else
                                    <span class="student-semester-chip">{{ $student->student->semester ?? '--' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-600" data-label="Year"><span class="student-year-chip">{{ $student->student->academic_year ?? '--' }}</span></td>
                            <td class="px-4 py-4" data-label="Status">
                                <span class="badge badge-{{ $student->student->status ?? 'inactive' }}">
                                    {{ ucfirst($student->student->status ?? 'inactive') }}
                                </span>
                            </td>
                             <td class="px-4 py-4 text-center" data-label="Actions">
                                 <div class="student-actions flex gap-2 justify-center">
                                     <a href="{{ route('admin.students.show', $student->id) }}" class="action-btn action-btn-view" title="View">
                                         <i class="bi bi-eye"></i>
                                     </a>
                                     <a href="{{ route('admin.students.edit', $student->id) }}" class="action-btn action-btn-edit" title="Edit">
                                         <i class="bi bi-pencil-square"></i>
                                     </a>
                                     <button type="button" onclick="deleteStudent({{ $student->id }})" class="action-btn action-btn-delete" title="Delete">
                                         <i class="bi bi-trash3"></i>
                                     </button>
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="student-empty-state px-4 py-8 text-center text-gray-500">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="students-pagination px-4 py-4 border-t border-gray-100 dark:border-slate-700 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-800">
            @include('admin.components.admin-pagination', ['paginator' => $students ?? null])
        </div>
    </div>
</div>

<script>

function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        // Create a form and submit it for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/students/${id}`;
        form.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(form);
        form.submit();
    }
}

// Checkbox management
document.getElementById('select_all')?.addEventListener('change', function() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
});

document.getElementById('th_select_all')?.addEventListener('change', function() {
    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = this.checked);
});

// Bulk actions
document.getElementById('apply_bulk')?.addEventListener('click', function() {
    const action = document.getElementById('bulk_action').value;
    const selected = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.dataset.id);
    if (!action || selected.length === 0) {
        alert('Please select action and students');
        return;
    }

    if (action === 'export_csv') {
        document.getElementById('exportForm')?.submit();
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.students.bulk') }}';
    form.style.display = 'none';

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = token;
        form.appendChild(csrf);
    }

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    form.appendChild(actionInput);

    selected.forEach((id) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });

    if (action === 'remove_alumni' || action === 'set_semester') {
        const semester = prompt('Enter semester (1-6):');
        if (!semester) {
            return;
        }

        const semesterInput = document.createElement('input');
        semesterInput.type = 'hidden';
        semesterInput.name = 'semester';
        semesterInput.value = semester;
        form.appendChild(semesterInput);
    }

    document.body.appendChild(form);
    form.submit();
});
</script>

@endsection

