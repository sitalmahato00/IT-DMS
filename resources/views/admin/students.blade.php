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
    html.students-ui-enhanced:not(.dark) .badge-pending { background: #fef3c7; color: #b45309; }
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
        html.students-ui-enhanced:not(.dark) .student-directory-table thead th:nth-child(n+4),
        html.students-ui-enhanced:not(.dark) .student-directory-table tbody td:nth-child(n+4) { display: none; }
        html.students-ui-enhanced:not(.dark) .student-directory-table th,
        html.students-ui-enhanced:not(.dark) .student-directory-table td { padding: 0.75rem 0.5rem; }
    }

    @media (max-width: 640px) {
        html.students-ui-enhanced:not(.dark) .student-directory-table thead th:nth-child(n+2),
        html.students-ui-enhanced:not(.dark) .student-directory-table tbody td:nth-child(n+2) { display: none; }
        html.students-ui-enhanced:not(.dark) .student-directory-table th,
        html.students-ui-enhanced:not(.dark) .student-directory-table td { padding: 0.5rem 0.25rem; }
    }
</style>
@endsection

@section('content')

<!-- Page Header -->
@include('admin.components.admin-page-header', [
    'title' => 'Students',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Students']
    ],
    'addButton' => [
        'label' => 'Add Student',
        'onclick' => 'openAddStudentModal()',
        'color' => 'green'
    ]
])

<div class="students-page space-y-6 w-full">
    <!-- Stats Cards -->
    <div class="students-stats">
        @include('admin.components.admin-stats-cards', [
            'cards' => [
                ['title' => 'Active Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('status','active')->whereNull('is_alumni'))->count(), 'icon' => 'bi-check-circle', 'color' => 'green'],
                ['title' => 'Inactive Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('status','inactive')->whereNull('is_alumni'))->count(), 'icon' => 'bi-x-circle', 'color' => 'red'],
                ['title' => 'Pending Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('status','pending')->whereNull('is_alumni'))->count(), 'icon' => 'bi-hourglass-split', 'color' => 'yellow'],
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
                ['name' => 'status', 'type' => 'select', 'options' => ['all' => 'All', 'active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending', 'alumni' => 'Alumni'], 'value' => request('status'), 'label' => 'Status'],
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
                            <td class="px-4 py-4"><input type="checkbox" class="student-checkbox rounded text-red-600" data-id="{{ $student->id }}" /></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="student-avatar w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-semibold text-red-700">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    <span class="student-name font-medium">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600"><span class="student-roll-chip">{{ $student->student->roll_no ?? $student->id }}</span></td>
                            <td class="px-4 py-4 text-gray-600"><span class="student-meta-text">{{ $student->email }}</span></td>
                            <td class="px-4 py-4">
                                @if($student->student->is_alumni)
                                    <span class="badge badge-alumni">Graduate</span>
                                @else
                                    <span class="student-semester-chip">{{ $student->student->semester ?? '--' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-600"><span class="student-year-chip">{{ $student->student->academic_year ?? '--' }}</span></td>
                            <td class="px-4 py-4">
                                <span class="badge badge-{{ $student->student->status ?? 'pending' }}">
                                    {{ ucfirst($student->student->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="student-actions flex gap-2 justify-center">
                                    <button type="button" onclick="viewStudent({{ json_encode($student->only(['id', 'name', 'email']) + ($student->student?->toArray() ?? [])) }})" class="action-btn action-btn-view" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" onclick="editStudent({{ json_encode($student->only(['id', 'name', 'email']) + ($student->student?->toArray() ?? [])) }})" class="action-btn action-btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
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

<!-- View Student Modal -->
<div id="viewStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="event.target === this && closeViewStudentModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">View Student</h3>
                <p class="text-sm text-red-100">Student information and details</p>
            </div>
            <button onclick="closeViewStudentModal()" class="text-red-100 hover:text-white p-1"><i class="bi bi-x-lg text-lg"></i></button>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-6 sm:gap-8">
                <!-- Photo Section -->
                <div class="flex flex-col items-center">
                    <div id="viewStudentAvatar" class="w-32 h-32 sm:w-40 sm:h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0">
                        <img id="viewStudentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                        <span id="viewStudentInitial"><i class="bi bi-person text-5xl"></i></span>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Full Name</label><p id="view_name" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Email</label><p id="view_email" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Phone</label><p id="view_phone" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Student ID</label><p id="view_roll_no" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Semester</label><p id="view_semester" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Department</label><p id="view_department" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Academic Year</label><p id="view_academic_year" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Gender</label><p id="view_gender" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Date of Birth</label><p id="view_dob" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Blood Group</label><p id="view_blood_group" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Status</label><p id="view_status" class="mt-1 text-sm font-medium">—</p></div>
                        <div><label class="text-xs font-semibold text-gray-500 uppercase">Alumni</label><p id="view_is_alumni" class="mt-1 text-sm font-medium">—</p></div>
                        <div class="sm:col-span-2"><label class="text-xs font-semibold text-gray-500 uppercase">Address</label><p id="view_address" class="mt-1 text-sm font-medium">—</p></div>
                        <div class="sm:col-span-2"><label class="text-xs font-semibold text-gray-500 uppercase">Emergency Contact</label><p id="view_emergency_contact" class="mt-1 text-sm font-medium">—</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 border-t flex justify-end gap-2">
            <button onclick="closeViewStudentModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Close</button>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="event.target === this && closeAddStudentModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Add Student</h3>
                <p class="text-sm text-red-100">Create a new student account and profile</p>
            </div>
            <button onclick="closeAddStudentModal()" class="text-red-100 hover:text-white p-1"><i class="bi bi-x-lg text-lg"></i></button>
        </div>
        <form id="addStudentForm" action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="role" value="student">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="text-sm">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Photo Section -->
                <div class="col-span-1 flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
                        <img id="profile_preview" src="" alt="Avatar preview" class="w-full h-full object-cover hidden">
                        <div id="profile_placeholder" class="text-gray-400"><i class="bi bi-person-fill text-4xl"></i></div>
                    </div>
                    <label for="profile_photo_input" class="mt-3 inline-flex items-center px-3 py-1.5 bg-white border rounded text-sm cursor-pointer hover:bg-gray-50">
                        <i class="bi bi-upload mr-2"></i>Choose photo
                    </label>
                    <input id="profile_photo_input" type="file" name="profile_photo" accept="image/*" class="sr-only" onchange="previewAddStudentPhoto()" />
                </div>

                <!-- Form Fields -->
                <div class="col-span-1 sm:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-1">Full Name *</label><input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Phone *</label><input type="tel" name="phone" required maxlength="10" class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Student ID *</label><input type="text" name="student_id" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Department *</label><input type="text" name="department" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Semester *</label>
                            <select name="semester" required class="w-full px-3 py-2 border rounded-lg">
                                @for($i=1;$i<=6;$i++)<option value="{{ $i }}">Semester {{ $i }}</option>@endfor
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium mb-1">Academic Year *</label><input type="text" name="academic_year" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Date of Birth *</label><input type="date" name="date_of_birth" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Gender</label>
                            <select name="gender" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium mb-1">Blood Group</label><input type="text" name="blood_group" class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Emergency Contact</label><input type="text" name="emergency_contact" class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border rounded-lg">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4"><label class="block text-sm font-medium mb-1">Address *</label><textarea name="address" required rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea></div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Add Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="event.target === this && closeEditStudentModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Edit Student</h3>
                <p class="text-sm text-red-100">Update student information</p>
            </div>
            <button onclick="closeEditStudentModal()" class="text-red-100 hover:text-white p-1"><i class="bi bi-x-lg text-lg"></i></button>
        </div>
        <form id="editStudentForm" action="" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Photo Section -->
                <div class="col-span-1 flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
                        <img id="edit_profile_preview" src="" alt="Avatar preview" class="w-full h-full object-cover hidden">
                        <div id="edit_profile_placeholder" class="text-gray-400"><i class="bi bi-person-fill text-4xl"></i></div>
                    </div>
                    <label for="edit_profile_photo_input" class="mt-3 inline-flex items-center px-3 py-1.5 bg-white border rounded text-sm cursor-pointer hover:bg-gray-50">
                        <i class="bi bi-upload mr-2"></i>Change photo
                    </label>
                    <input id="edit_profile_photo_input" type="file" name="profile_photo" accept="image/*" class="sr-only" />
                </div>

                <!-- Form Fields -->
                <div class="col-span-1 sm:col-span-2">
                    <input type="hidden" id="edit_student_id" name="student_id">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-1">Full Name *</label><input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Phone *</label><input type="tel" name="phone" id="edit_phone" required maxlength="10" class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Student ID *</label><input type="text" name="roll_no" id="edit_roll_no" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Department *</label><input type="text" name="department" id="edit_department" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Semester *</label>
                            <select name="semester" id="edit_semester" required class="w-full px-3 py-2 border rounded-lg">
                                @for($i=1;$i<=6;$i++)<option value="{{ $i }}">Semester {{ $i }}</option>@endfor
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium mb-1">Academic Year *</label><input type="text" name="academic_year" id="edit_academic_year" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Date of Birth *</label><input type="date" name="date_of_birth" id="edit_dob" required class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Gender</label>
                            <select name="gender" id="edit_gender" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-medium mb-1">Blood Group</label><input type="text" name="blood_group" id="edit_blood_group" class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Emergency Contact</label><input type="text" name="emergency_contact" id="edit_emergency_contact" class="w-full px-3 py-2 border rounded-lg" /></div>
                        <div><label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" id="edit_status" class="w-full px-3 py-2 border rounded-lg">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4"><label class="block text-sm font-medium mb-1">Address *</label><textarea name="address" id="edit_address" required rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea></div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditStudentModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function previewAddStudentPhoto() {
    const file = document.getElementById('profile_photo_input').files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile_preview').src = e.target.result;
            document.getElementById('profile_preview').classList.remove('hidden');
            document.getElementById('profile_placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function openEditStudentModal() {
    document.getElementById('editStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function viewStudent(student) {
    document.getElementById('view_name').textContent = student.name || '—';
    document.getElementById('view_email').textContent = student.email || '—';
    document.getElementById('view_phone').textContent = student.phone || '—';
    document.getElementById('view_roll_no').textContent = student.roll_no || '—';
    document.getElementById('view_semester').textContent = student.is_alumni ? 'Graduate' : (student.semester || '—');
    document.getElementById('view_department').textContent = student.department || '—';
    document.getElementById('view_academic_year').textContent = student.academic_year || '—';
    document.getElementById('view_gender').textContent = student.gender ? (student.gender.charAt(0).toUpperCase() + student.gender.slice(1)) : '—';
    document.getElementById('view_dob').textContent = student.date_of_birth || '—';
    document.getElementById('view_blood_group').textContent = student.blood_group || '—';
    document.getElementById('view_status').textContent = student.status ? (student.status.charAt(0).toUpperCase() + student.status.slice(1)) : '—';
    document.getElementById('view_is_alumni').textContent = student.is_alumni ? 'Yes' : 'No';
    document.getElementById('view_address').textContent = student.address || '—';
    document.getElementById('view_emergency_contact').textContent = student.emergency_contact || '—';
    
    // Handle photo
    if (student.profile_photo_path) {
        document.getElementById('viewStudentAvatarImg').src = student.profile_photo_path;
        document.getElementById('viewStudentAvatarImg').style.display = 'block';
        document.getElementById('viewStudentInitial').style.display = 'none';
    } else {
        document.getElementById('viewStudentAvatarImg').style.display = 'none';
        document.getElementById('viewStudentInitial').style.display = 'block';
    }
    
    document.getElementById('viewStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeViewStudentModal() {
    document.getElementById('viewStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editStudent(student) {
    openEditStudentModal();
    
    // Populate edit form
    document.getElementById('edit_student_id').value = student.id || '';
    document.getElementById('edit_name').value = student.name || '';
    document.getElementById('edit_email').value = student.email || '';
    document.getElementById('edit_phone').value = student.phone || '';
    document.getElementById('edit_roll_no').value = student.roll_no || '';
    document.getElementById('edit_department').value = student.department || '';
    document.getElementById('edit_semester').value = student.semester || '';
    document.getElementById('edit_academic_year').value = student.academic_year || '';
    document.getElementById('edit_dob').value = student.date_of_birth || '';
    document.getElementById('edit_gender').value = student.gender || '';
    document.getElementById('edit_blood_group').value = student.blood_group || '';
    document.getElementById('edit_emergency_contact').value = student.emergency_contact || '';
    document.getElementById('edit_status').value = student.status || 'active';
    document.getElementById('edit_address').value = student.address || '';
    
    // Set form action to update route
    document.getElementById('editStudentForm').action = `/admin/students/${student.id}`;
    
    // Handle photo
    if (student.profile_photo_path) {
        document.getElementById('edit_profile_preview').src = student.profile_photo_path;
        document.getElementById('edit_profile_preview').classList.remove('hidden');
        document.getElementById('edit_profile_placeholder').classList.add('hidden');
    }
}

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

// Edit photo preview
document.getElementById('edit_profile_photo_input')?.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit_profile_preview').src = e.target.result;
            document.getElementById('edit_profile_preview').classList.remove('hidden');
            document.getElementById('edit_profile_placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

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
    console.log('Action:', action, 'Students:', selected);
});
</script>

@endsection
