@extends('admin.layouts.app')

@section('title', 'Parents')

@section('styles')
<script>document.documentElement.classList.add('parents-ui-enhanced');</script>
<style>
    .no-print {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }

    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid,
    html.parents-ui-enhanced:not(.dark) .parents-filter-panel > div {
        margin-bottom: 0;
    }

    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid > div {
        position: relative;
        overflow: hidden;
        border-width: 2px;
        border-radius: 1rem;
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid > div:hover,
    html.parents-ui-enhanced:not(.dark) .parent-photo-panel:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid > div:nth-child(1) { border-color: #93c5fd; background: linear-gradient(135deg, #eff6ff 0%, #ffffff 56%, #eff6ff 100%); }
    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid > div:nth-child(2) { border-color: #86efac; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 56%, #f0fdf4 100%); }
    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid > div:nth-child(3) { border-color: #fcd34d; background: linear-gradient(135deg, #fffbeb 0%, #ffffff 56%, #fffbeb 100%); }
    html.parents-ui-enhanced:not(.dark) .parents-stats > .grid > div:nth-child(4) { border-color: #fda4af; background: linear-gradient(135deg, #fff1f2 0%, #ffffff 56%, #fff1f2 100%); }

    html.parents-ui-enhanced:not(.dark) .parents-filter-panel > div,
    html.parents-ui-enhanced:not(.dark) .parents-table-panel,
    html.parents-ui-enhanced:not(.dark) .parent-confirm-panel {
        overflow: hidden;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
    }

    html.parents-ui-enhanced:not(.dark) .parents-filter-panel label,
    html.parents-ui-enhanced:not(.dark) .parent-directory-head th,
    html.parents-ui-enhanced:not(.dark) .parent-form label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
    }

    html.parents-ui-enhanced:not(.dark) .parents-filter-panel input:not([type='checkbox']):not([type='radio']),
    html.parents-ui-enhanced:not(.dark) .parents-filter-panel select,
    html.parents-ui-enhanced:not(.dark) .parents-toolbar select,
    html.parents-ui-enhanced:not(.dark) .parent-form input:not([type='checkbox']):not([type='radio']):not([type='file']),
    html.parents-ui-enhanced:not(.dark) .parent-form select,
    html.parents-ui-enhanced:not(.dark) .parent-form textarea {
        min-height: 2.9rem;
        border: 2px solid #cbd5e1;
        border-radius: 0.85rem;
        background: #ffffff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    html.parents-ui-enhanced:not(.dark) .parents-filter-panel input:not([type='checkbox']):not([type='radio']):focus,
    html.parents-ui-enhanced:not(.dark) .parents-filter-panel select:focus,
    html.parents-ui-enhanced:not(.dark) .parents-toolbar select:focus,
    html.parents-ui-enhanced:not(.dark) .parent-form input:not([type='checkbox']):not([type='radio']):not([type='file']):focus,
    html.parents-ui-enhanced:not(.dark) .parent-form select:focus,
    html.parents-ui-enhanced:not(.dark) .parent-form textarea:focus {
        outline: none;
        border-color: #f43f5e;
        box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.1);
    }

    html.parents-ui-enhanced:not(.dark) .parents-toolbar,
    html.parents-ui-enhanced:not(.dark) .parents-pagination {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    }

    html.parents-ui-enhanced:not(.dark) .parents-toolbar { border-bottom: 1px solid #e2e8f0; }
    html.parents-ui-enhanced:not(.dark) .parents-pagination,
    html.parents-ui-enhanced:not(.dark) .parent-form-actions { border-top: 1px solid #e2e8f0; }

    html.parents-ui-enhanced:not(.dark) .parents-toolbar-btn,
    html.parents-ui-enhanced:not(.dark) .parent-secondary-btn,
    html.parents-ui-enhanced:not(.dark) .parent-primary-btn,
    html.parents-ui-enhanced:not(.dark) .action-btn {
        box-shadow: 0 16px 30px -24px rgba(15, 23, 42, 0.45);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    html.parents-ui-enhanced:not(.dark) .parents-toolbar-btn:hover,
    html.parents-ui-enhanced:not(.dark) .parent-secondary-btn:hover,
    html.parents-ui-enhanced:not(.dark) .parent-primary-btn:hover,
    html.parents-ui-enhanced:not(.dark) .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 34px -24px rgba(15, 23, 42, 0.5);
    }

    html.parents-ui-enhanced:not(.dark) .parent-directory-table { border-collapse: separate; border-spacing: 0; }
    html.parents-ui-enhanced:not(.dark) .parent-directory-head th { background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-bottom: 1px solid #e2e8f0; color: #64748b; }
    html.parents-ui-enhanced:not(.dark) .parent-row td { border-bottom: 1px solid #e2e8f0; transition: background-color 0.18s ease; vertical-align: middle; }
    html.parents-ui-enhanced:not(.dark) .parent-row:nth-child(even) td { background: #f8fafc; }
    html.parents-ui-enhanced:not(.dark) .parent-row:hover td { background: #fff7f8; }

    html.parents-ui-enhanced:not(.dark) .parent-avatar,
    html.parents-ui-enhanced:not(.dark) .parent-photo-frame {
        border: 1px solid #fecdd3;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    html.parents-ui-enhanced:not(.dark) .parent-name { color: #0f172a; font-weight: 700; }
    html.parents-ui-enhanced:not(.dark) .parent-meta-text { color: #475569; }
    html.parents-ui-enhanced:not(.dark) .parent-id-chip,
    html.parents-ui-enhanced:not(.dark) .parent-email-chip,
    html.parents-ui-enhanced:not(.dark) .parent-phone-chip,
    html.parents-ui-enhanced:not(.dark) .parent-children-chip,
    html.parents-ui-enhanced:not(.dark) .parent-role-chip,
    html.parents-ui-enhanced:not(.dark) .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1;
    }

    html.parents-ui-enhanced:not(.dark) .parent-id-chip { background: #eff6ff; color: #1d4ed8; }
    html.parents-ui-enhanced:not(.dark) .parent-email-chip { background: #f8fafc; color: #475569; }
    html.parents-ui-enhanced:not(.dark) .parent-phone-chip { background: #ecfeff; color: #0f766e; }
    html.parents-ui-enhanced:not(.dark) .parent-children-chip { background: #fef3c7; color: #b45309; }
    html.parents-ui-enhanced:not(.dark) .parent-role-chip { background: #f3e8ff; color: #7e22ce; }
    html.parents-ui-enhanced:not(.dark) .badge-active { background: #dcfce7; color: #166534; }
    html.parents-ui-enhanced:not(.dark) .badge-inactive { background: #fee2e2; color: #b91c1c; }
    html.parents-ui-enhanced:not(.dark) .badge-pending { background: #fef3c7; color: #b45309; }

    html.parents-ui-enhanced:not(.dark) .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.8rem;
        background: #ffffff;
    }

    html.parents-ui-enhanced:not(.dark) .action-btn-view { color: #2563eb; }
    html.parents-ui-enhanced:not(.dark) .action-btn-edit { color: #d97706; }
    html.parents-ui-enhanced:not(.dark) .action-btn-delete { color: #dc2626; }
    html.parents-ui-enhanced:not(.dark) .parent-empty-state { color: #64748b; font-weight: 500; }

@media (max-width: 640px) {
    .parent-directory-table { min-width: 52rem; }
    .parent-directory-table th,
    .parent-directory-table td { white-space: nowrap; }
    .parents-toolbar > div,
    .parents-toolbar .flex,
    .parents-toolbar .flex.items-center { flex-wrap: wrap; }
}

</style>
@endsection

@section('content')

{{-- Page Header --}}
@include('admin.components.admin-page-header', [
    'title' => 'Parents',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Parents']
    ],
    'addButton' => [
        'label' => 'Add Parent',
        'route' => route('admin.parents.create')
    ]
])

<div class="parents-page space-y-6">
    <!-- Stats Grid -->
    <div class="parents-stats">
        @include('admin.components.admin-stats-cards', [
            'cards' => [
                [
                    'title' => 'Total Parents',
                    'value' => isset($parents) ? $parents->total() : 0,
                    'icon' => 'bi-people-fill',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Active',
                    'value' => isset($parents) ? \App\Models\User::where('role','parent')->whereHas('parent', function($q) { $q->where('status','active'); })->count() : 0,
                    'icon' => 'bi-check-circle',
                    'color' => 'green'
                ],
                [
                    'title' => 'Pending',
                    'value' => isset($parents) ? \App\Models\User::where('role','parent')->whereHas('parent', function($q) { $q->where('status','pending'); })->count() : 0,
                    'icon' => 'bi-exclamation-circle',
                    'color' => 'yellow'
                ],
                [
                    'title' => 'Inactive',
                    'value' => isset($parents) ? \App\Models\User::where('role','parent')->whereHas('parent', function($q) { $q->where('status','inactive'); })->count() : 0,
                    'icon' => 'bi-x-circle',
                    'color' => 'red'
                ]
            ]
        ])
    </div>

    <!-- Filter Card -->
    <div class="parents-filter-panel">
        @include('admin.components.admin-filter-card', [
            'formAction' => route('admin.parents'),
            'filters' => [
                [
                    'name' => 'q',
                    'type' => 'text',
                    'label' => 'Search',
                    'placeholder' => 'Name or email...',
                    'value' => request('q', '')
                ],
                [
                    'name' => 'status',
                    'type' => 'select',
                    'label' => 'Status',
                    'placeholder' => 'All Status',
                    'options' => [
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'inactive' => 'Inactive'
                    ],
                    'value' => request('status', '')
                ]
            ],
            'showReset' => true,
            'resetRoute' => route('admin.parents')
        ])
    </div>

    <!-- Table Card -->
    <div class="parents-table-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <!-- Table Toolbar -->
        <div class="parents-toolbar px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Left: Entries Selector -->
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Show</label>
                    <select onchange="window.location.href=updatePerPage(this.value)" class="px-2 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <label class="text-sm text-gray-600 dark:text-gray-400">entries</label>
                </div>

                <!-- Right: Search & Export Buttons -->
                <div class="flex items-center gap-2">
                    <form id="exportParentsForm" method="GET" action="{{ route('admin.parents.export') }}" class="inline-block">
                        <input type="hidden" name="q" value="{{ request('q', '') }}">
                        <input type="hidden" name="status" value="{{ request('status', '') }}">
                        <button type="submit" class="parents-toolbar-btn px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 shadow-sm transition-colors inline-flex items-center gap-1">
                            <i class="bi bi-file-earmark-spreadsheet"></i>CSV
                        </button>
                    </form>
                    <button type="button" onclick="adminOpenPrintPreview('{{ route('parents.print-list') }}', { title: 'Print Parents' })" class="parents-toolbar-btn px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-colors inline-flex items-center gap-1 no-print">
                        <i class="bi bi-printer"></i>Print
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="parent-directory-table min-w-full text-left divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="parent-directory-head bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Parent ID</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Children</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($parents ?? collect() as $parent)
                    <tr class="parent-row hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4" data-label="Name">
                            <div class="flex items-center gap-3">
                                @if(!empty(optional($parent->parent)->profile_photo_url))
                                    <img src="{{ optional($parent->parent)->profile_photo_url }}" alt="avatar" class="parent-photo-frame w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="parent-avatar w-10 h-10 bg-gray-100 dark:bg-slate-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 font-medium text-sm">
                                        {{ substr($parent->name ?? '', 0, 1) }}
                                    </div>
                                @endif
                                <span class="parent-name font-medium text-gray-900 dark:text-white">{{ $parent->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300" data-label="Parent ID"><span class="parent-id-chip">{{ optional($parent->parent)->parent_code ?? 'P' . str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300" data-label="Email"><span class="parent-email-chip">{{ $parent->email }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300" data-label="Phone"><span class="parent-phone-chip">{{ optional($parent->parent)->phone ?? '—' }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300" data-label="Children"><span class="parent-children-chip">{{ $parent->children_count ?? 0 }}</span></td>
                        <td class="px-6 py-4 text-sm" data-label="Role">
                            <span class="parent-role-chip inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full text-xs font-medium">
                                {{ ucfirst($parent->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm" data-label="Status">
                            @php 
                                $status = optional($parent->parent)->status ?? 'pending';
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                ];
                                $statusClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
                            @endphp
                            <span class="badge badge-{{ $status }} inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm" data-label="Actions">
                            <div class="parent-actions flex gap-2 justify-center">
                                <a href="{{ route('admin.parents.show', $parent->id) }}" class="action-btn action-btn-view" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.parents.edit', $parent->id) }}" class="action-btn action-btn-edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" onclick="window.deleteParent({{ $parent->id }})" class="action-btn action-btn-delete" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="parent-empty-state px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-inbox text-4xl mb-3 text-gray-300 dark:text-gray-500"></i>
                                <p class="text-gray-600 dark:text-gray-400">No records found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="parents-pagination px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $parents->firstItem() ?? 0 }} to {{ $parents->lastItem() ?? 0 }} of {{ $parents->total() }} parents
            </div>
            {{ $parents->links() }}
        </div>
    </div>
</div>
<script>
    function updatePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    window.deleteParent = function(parentId) {
        if (!confirm('Are you sure you want to delete this parent? This action cannot be undone.')) {
            return;
        }

        fetch(`/admin/parents/${parentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error deleting parent');
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    }
</script>

@endsection
