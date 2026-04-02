@extends('admin.layouts.app')

@section('title', 'Teachers')

@section('styles')
<style>
    .teachers-page .teachers-stats .grid > div,
    .teachers-page .teachers-table-panel,
    .teachers-page .teachers-filter-panel > div {
        border-radius: 1rem;
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    .teachers-page .teachers-stats .grid > div {
        position: relative;
        overflow: hidden;
        border-width: 2px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .teachers-page .teachers-stats .grid > div:hover {
        transform: translateY(-3px);
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    .teachers-page .teacher-directory-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .teachers-page .teacher-directory-head th {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
    }

    .teachers-page .teacher-row td {
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.18s ease;
        vertical-align: middle;
    }

    .teachers-page .teacher-row:nth-child(even) td {
        background: #f8fafc;
    }

    .teachers-page .teacher-row:hover td {
        background: #fff7f8;
    }

    .teachers-page .teacher-avatar,
    .teachers-page .teacher-photo-frame {
        border: 1px solid #fecdd3;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .teachers-page .teacher-avatar-image {
        border: 2px solid #ffffff;
        box-shadow: 0 16px 28px -24px rgba(15, 23, 42, 0.4);
    }

    .teachers-page .teacher-name {
        color: #0f172a;
        font-weight: 700;
    }

    .teachers-page .teacher-meta-text {
        color: #475569;
    }

    .teachers-page .teacher-id-chip,
    .teachers-page .teacher-role-chip,
    .teachers-page .teacher-course-chip,
    .teachers-page .teacher-load-chip,
    .teachers-page .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
    }

    .teachers-page .teacher-id-chip { background: #eff6ff; color: #1d4ed8; }
    .teachers-page .teacher-role-chip { background: #ffedd5; color: #c2410c; }
    .teachers-page .teacher-course-chip { background: #f8fafc; color: #475569; }
    .teachers-page .teacher-load-chip { background: #dbeafe; color: #1d4ed8; }
    .teachers-page .badge-active { background: #dcfce7; color: #166534; }
    .teachers-page .badge-inactive { background: #fee2e2; color: #b91c1c; }
    .teachers-page .badge-pending { background: #fef3c7; color: #b45309; }
    .teachers-page .badge-alumni { background: #f3e8ff; color: #7e22ce; }

    .teachers-page .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.8rem;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 16px 30px -24px rgba(15, 23, 42, 0.45);
    }

    .teachers-page .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.5);
    }

    .teachers-page .action-btn-view { color: #2563eb; }
    .teachers-page .action-btn-edit { color: #d97706; }
    .teachers-page .action-btn-delete { color: #dc2626; }

    @media (max-width: 768px) {
        .teachers-page .teacher-directory-table thead th:nth-child(n+4),
        .teachers-page .teacher-directory-table tbody td:nth-child(n+4) {
            display: none;
        }

        .teachers-page .teacher-directory-table th,
        .teachers-page .teacher-directory-table td {
            padding: 0.75rem 0.5rem;
        }
    }

    @media (max-width: 640px) {
        .teachers-page .teacher-directory-table thead th:nth-child(n+2),
        .teachers-page .teacher-directory-table tbody td:nth-child(n+2) {
            display: none;
        }

        .teachers-page .teacher-directory-table th,
        .teachers-page .teacher-directory-table td {
            padding: 0.5rem 0.25rem;
        }
    }
</style>
@endsection

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Teachers',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Teachers']
    ],
    'addButton' => [
        'label' => 'Add Teacher',
        'route' => route('admin.teachers.create')
    ]
])

<div class="teachers-page space-y-6">
    <div class="teachers-stats">
        @include('admin.components.admin-stats-cards', [
            'cards' => [
                [
                    'title' => 'Total Teachers',
                    'value' => isset($teachers) ? $teachers->total() : 0,
                    'icon' => 'bi bi-person-workspace',
                    'color' => 'red'
                ],
                [
                    'title' => 'Active',
                    'value' => isset($teachers) ? \App\Models\User::where('role','teacher')->whereHas('teacher', function($q) { $q->where('status','active'); })->count() : 0,
                    'icon' => 'bi bi-check-circle',
                    'color' => 'green'
                ],
                [
                    'title' => 'On Leave',
                    'value' => isset($teachers) ? \App\Models\User::where('role','teacher')->whereHas('teacher', function($q) { $q->where('status','On Leave'); })->count() : 0,
                    'icon' => 'bi bi-exclamation-circle',
                    'color' => 'yellow'
                ],
                [
                    'title' => 'Retired',
                    'value' => isset($teachers) ? \App\Models\User::where('role','teacher')->whereHas('teacher', function($q) { $q->where('status','Retired'); })->count() : 0,
                    'icon' => 'bi bi-hourglass-end',
                    'color' => 'purple'
                ]
            ]
        ])
    </div>

    <div class="teachers-filter-panel">
        @include('admin.components.admin-filter-card', [
            'formAction' => route('admin.teachers'),
            'filters' => [
                [
                    'name' => 'search',
                    'type' => 'text',
                    'label' => 'Search',
                    'placeholder' => 'Name or email...',
                    'value' => request('search', request('q', ''))
                ],
                [
                    'name' => 'status',
                    'type' => 'select',
                    'label' => 'Status',
                    'placeholder' => 'All Status',
                    'options' => [
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'On Leave' => 'On Leave',
                        'Retired' => 'Retired'
                    ],
                    'value' => request('status', '')
                ],
                [
                    'name' => 'course',
                    'type' => 'select',
                    'label' => 'Subject',
                    'placeholder' => 'All Subjects',
                    'options' => $subjects ?? [],
                    'value' => request('course', '')
                ]
            ],
            'showReset' => true,
            'resetRoute' => route('admin.teachers')
        ])
    </div>

    <div class="teachers-table-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="teachers-toolbar border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-slate-700 dark:bg-slate-800/50">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Teachers List</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ isset($teachers) ? $teachers->total() : 0 }} records)</span>
                </div>

                <div class="flex items-center gap-2">
                    <form id="exportTeachersForm" method="GET" action="{{ route('admin.teachers.export') }}" class="inline-block">
                        <input type="hidden" name="search" value="{{ request('search', request('q', '')) }}">
                        <input type="hidden" name="status" value="{{ request('status', '') }}">
                        <input type="hidden" name="course" value="{{ request('course', '') }}">
                        <button type="submit" class="teachers-toolbar-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-purple-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-purple-700">
                            <i class="bi bi-file-earmark-spreadsheet"></i>CSV
                        </button>
                    </form>
                    <button type="button" onclick="adminOpenPrintPreview('{{ route('teachers.print-list') }}', { title: 'Print Teachers' })" class="teachers-toolbar-btn inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-blue-700">
                        <i class="bi bi-printer"></i>Print
                    </button>
                </div>
            </div>
        </div>

        <div id="teachersTableContainer" class="overflow-x-auto">
            <table class="teacher-directory-table min-w-full divide-y divide-gray-200 text-left dark:divide-slate-700">
                <thead class="teacher-directory-head bg-gray-50 text-sm font-semibold text-gray-700 dark:bg-slate-700/50 dark:text-gray-200">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Teacher ID</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Course</th>
                        <th class="px-6 py-3">Teaching Load</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($teachers ?? collect() as $teacher)
                        <tr class="teacher-row border-t border-gray-200 transition-colors hover:bg-gray-50 dark:border-slate-700 dark:hover:bg-slate-700/30">
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-3">
                                    @php
                                        $teacherPhoto = $teacher->teacher->profile_photo_path ?? null;
                                        $teacherPhotoUrl = $teacherPhoto ? (\Illuminate\Support\Str::startsWith($teacherPhoto, 'storage/') ? asset($teacherPhoto) : asset('storage/' . $teacherPhoto)) : null;
                                    @endphp
                                    @if($teacherPhotoUrl)
                                        <img src="{{ $teacherPhotoUrl }}" alt="avatar" class="teacher-avatar-image h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="teacher-avatar flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-medium text-gray-600">T</div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="teacher-name truncate font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</p>
                                        <p class="teacher-meta-text truncate text-xs text-gray-500 dark:text-gray-400">{{ $teacher->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <span class="teacher-id-chip">{{ $teacher->teacher->teacher_code ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="teacher-role-chip inline-block bg-orange-100 px-3 py-1 text-xs font-medium text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">{{ ucfirst($teacher->role) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <span class="teacher-course-chip">{{ $teacher->teacher->department ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if(isset($teacher->teaching_load))
                                    <div class="flex items-center gap-2">
                                        <span class="teacher-load-chip inline-flex items-center bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $teacher->teaching_load['subjects_count'] ?? 0 }} subjects
                                        </span>
                                        <span class="teacher-meta-text text-gray-500 dark:text-gray-400">|</span>
                                        <span class="teacher-meta-text text-gray-700 dark:text-gray-300">{{ $teacher->teaching_load['total_hours'] ?? 0 }} hrs/wk</span>
                                    </div>
                                    @if(count($teacher->teaching_load['subjects'] ?? []) > 0)
                                        <p class="teacher-meta-text mt-1 text-xs text-gray-500 dark:text-gray-400">{{ implode(', ', array_slice($teacher->teaching_load['subjects'], 0, 2)) }}{{ count($teacher->teaching_load['subjects']) > 2 ? '...' : '' }}</p>
                                    @endif
                                @else
                                    <span class="teacher-meta-text text-gray-400 dark:text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $status = strtolower($teacher->teacher->status ?? 'active');
                                    $badgeClass = match($status) {
                                        'active' => 'badge-active',
                                        'inactive' => 'badge-inactive',
                                        'on leave' => 'badge-pending',
                                        'retired' => 'badge-alumni',
                                        default => 'badge-active'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($teacher->teacher->status ?? 'active') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="action-btn action-btn-view" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="action-btn action-btn-edit" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" onsubmit="return confirm('Delete this teacher?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-inbox mb-3 text-4xl text-gray-300 dark:text-gray-500"></i>
                                    <p class="text-gray-600 dark:text-gray-400">No records found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="teachers-pagination border-t border-gray-100 bg-white dark:border-slate-700 dark:bg-slate-800">
            @include('admin.components.admin-pagination', [
                'paginator' => $teachers
            ])
        </div>
    </div>
</div>
@endsection
