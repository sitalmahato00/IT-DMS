@extends('admin.layouts.app')

@section('title', 'Students')

@push('styles')
<style>
    /* Essential styles only */
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }

    .badge-active { @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400; }
    .badge-inactive { @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400; }
    .badge-pending { @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400; }
    .badge-alumni { @apply bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400; }

    /* Action buttons */
    .action-btn {
        @apply inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 hover:scale-110;
    }

    .action-btn-view { @apply text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30; }
    .action-btn-edit { @apply text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30; }
    .action-btn-delete { @apply text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30; }

    /* Table styling */
    table thead { @apply bg-gradient-to-r from-red-50 to-pink-50 dark:from-slate-700/50 dark:to-slate-700/30 border-b-2 border-red-500/20; }
    table thead th { @apply text-gray-700 dark:text-gray-200 font-semibold text-sm px-4 py-3 text-left; }
    table tbody tr { @apply border-b border-gray-100 dark:border-slate-700/50 transition-all duration-150 hover:bg-red-50/40 dark:hover:bg-red-900/10; }
    table tbody td { @apply px-4 py-3.5 text-sm; }

    /* Form inputs */
    input:not([type="checkbox"]):not([type="radio"]),
    select,
    textarea {
        @apply transition-all duration-200 focus:ring-2 focus:ring-red-500 focus:ring-offset-0;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        table thead th:nth-child(n+4),
        table tbody td:nth-child(n+4) { display: none; }
        
        table th, table td { padding: 0.75rem 0.5rem; }
    }

    @media (max-width: 640px) {
        table thead th:nth-child(n+2),
        table tbody td:nth-child(n+2) { display: none; }
        
        table th, table td { padding: 0.5rem 0.25rem; }
    }
</style>
@endpush

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

<div class="space-y-4 w-full">
    <!-- Stats Cards -->
    @include('admin.components.admin-stats-cards', [
        'cards' => [
            ['title' => 'Active Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('status','active')->whereNull('is_alumni'))->count(), 'icon' => 'bi-check-circle', 'color' => 'green'],
            ['title' => 'Inactive Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('status','inactive')->whereNull('is_alumni'))->count(), 'icon' => 'bi-x-circle', 'color' => 'red'],
            ['title' => 'Pending Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('status','pending')->whereNull('is_alumni'))->count(), 'icon' => 'bi-hourglass-split', 'color' => 'yellow'],
            ['title' => 'Alumni', 'value' => \App\Models\User::where('role','student')->whereHas('student', fn($q) => $q->where('is_alumni', 1))->count(), 'icon' => 'bi-mortarboard', 'color' => 'purple'],
        ]
    ])

    <!-- Filter Card -->
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

    <!-- Data Table -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-md dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
        <!-- Toolbar -->
        <div class="px-4 py-4 border-b border-gray-100 dark:border-slate-700 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="select_all" class="form-checkbox rounded text-red-600" />
                    <span class="text-sm text-gray-700 dark:text-gray-300 hidden sm:inline">Select all</span>
                    <select id="bulk_action" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 rounded-lg text-sm">
                        <option value="">Bulk actions</option>
                        <option value="set_status_active">Set Active</option>
                        <option value="set_status_inactive">Set Inactive</option>
                        <option value="export_csv">Export (CSV)</option>
                    </select>
                    <button id="apply_bulk" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Apply</button>
                </div>
                <div class="flex items-center gap-2">
                    <form id="exportForm" method="GET" action="{{ route('admin.students.export') }}" class="inline-block">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <button type="submit" class="px-4 py-1.5 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                        </button>
                    </form>
                    <button onclick="adminOpenPrintPreview('{{ route('students.print-list') }}')" class="px-4 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
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
                        <tr>
                            <td class="px-4 py-4"><input type="checkbox" class="student-checkbox rounded text-red-600" data-id="{{ $student->id }}" /></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-semibold text-red-700">
                                        {{ substr($student->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-600">{{ $student->student->roll_no ?? $student->id }}</td>
                            <td class="px-4 py-4 text-gray-600">{{ $student->email }}</td>
                            <td class="px-4 py-4">
                                @if($student->student->is_alumni)
                                    <span class="badge badge-alumni">Graduate</span>
                                @else
                                    {{ $student->student->semester ?? '--' }}
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-600">{{ $student->student->academic_year ?? '--' }}</td>
                            <td class="px-4 py-4">
                                <span class="badge badge-{{ $student->student->status ?? 'pending' }}">
                                    {{ ucfirst($student->student->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex gap-2 justify-center">
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
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t border-gray-100 dark:border-slate-700 bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/50 dark:to-slate-800">
            @include('admin.components.admin-pagination', ['paginator' => $students ?? null])
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div id="viewStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="event.target === this && closeViewStudentModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white flex items-center justify-between">
            <h3 class="text-lg font-semibold">Student Details</h3>
            <button onclick="closeViewStudentModal()" class="text-red-100 hover:text-white p-1"><i class="bi bi-x-lg text-lg"></i></button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><label class="text-xs font-semibold text-gray-500 uppercase">Name</label><p id="view_name" class="mt-1">—</p></div>
                <div><label class="text-xs font-semibold text-gray-500 uppercase">Email</label><p id="view_email" class="mt-1">—</p></div>
                <div><label class="text-xs font-semibold text-gray-500 uppercase">Phone</label><p id="view_phone" class="mt-1">—</p></div>
                <div><label class="text-xs font-semibold text-gray-500 uppercase">Semester</label><p id="view_semester" class="mt-1">—</p></div>
                <div><label class="text-xs font-semibold text-gray-500 uppercase">Department</label><p id="view_department" class="mt-1">—</p></div>
                <div><label class="text-xs font-semibold text-gray-500 uppercase">Status</label><p id="view_status" class="mt-1">—</p></div>
            </div>
        </div>
        <div class="p-4 border-t flex justify-end gap-2">
            <button onclick="closeViewStudentModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Close</button>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" onclick="event.target === this && closeAddStudentModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white flex items-center justify-between">
            <h3 class="text-lg font-semibold">Add Student</h3>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Full Name *</label><input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg" /></div>
                <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg" /></div>
                <div><label class="block text-sm font-medium mb-1">Phone *</label><input type="tel" name="phone" required maxlength="10" class="w-full px-3 py-2 border rounded-lg" /></div>
                <div><label class="block text-sm font-medium mb-1">Department *</label><input type="text" name="department" required class="w-full px-3 py-2 border rounded-lg" /></div>
                <div><label class="block text-sm font-medium mb-1">Student ID *</label><input type="text" name="student_id" required class="w-full px-3 py-2 border rounded-lg" /></div>
                <div><label class="block text-sm font-medium mb-1">Semester *</label>
                    <select name="semester" required class="w-full px-3 py-2 border rounded-lg">
                        @for($i=1;$i<=6;$i++)<option value="{{ $i }}">Semester {{ $i }}</option>@endfor
                    </select>
                </div>
                <div><label class="block text-sm font-medium mb-1">Academic Year *</label><input type="text" name="academic_year" required class="w-full px-3 py-2 border rounded-lg" /></div>
                <div><label class="block text-sm font-medium mb-1">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium mb-1">Date of Birth *</label><input type="date" name="date_of_birth" required class="w-full px-3 py-2 border rounded-lg" /></div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium mb-1">Address *</label><textarea name="address" required rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea></div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Add Student</button>
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

function viewStudent(student) {
    document.getElementById('view_name').textContent = student.name || '—';
    document.getElementById('view_email').textContent = student.email || '—';
    document.getElementById('view_phone').textContent = student.phone || '—';
    document.getElementById('view_semester').textContent = student.is_alumni ? 'Graduate' : (student.semester || '—');
    document.getElementById('view_department').textContent = student.department || '—';
    document.getElementById('view_status').textContent = student.status || '—';
    document.getElementById('viewStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeViewStudentModal() {
    document.getElementById('viewStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function editStudent(student) {
    alert('Edit functionality coming soon');
}

function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        alert('Delete functionality coming soon');
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
    console.log('Action:', action, 'Students:', selected);
});
</script>

@endsection
