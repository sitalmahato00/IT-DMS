@extends('admin.layouts.app')

@section('title', 'Parents')

@push('styles')
<style>
    .no-print {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }
    
    /* Enhanced action buttons */
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
    
    /* Badge styles */
    .badge {
        @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
    }
    
    .badge-active {
        @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400;
    }
    
    .badge-inactive {
        @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400;
    }
    
    .badge-pending {
        @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400;
    }
</style>
@endpush

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
        'onclick' => "openAddParentModal()"
    ]
])

<div class="space-y-6">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
            <div class="p-6 text-center">
                <div id="confirmIcon" class="mx-auto mb-4 h-12 w-12 text-gray-400">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900 mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 mb-6">Are you sure you want to proceed?</p>
                <div class="flex justify-center gap-3">
                    <button id="confirmCancel" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">Cancel</button>
                    <button id="confirmOk" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
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

    <!-- Filter Card -->
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

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <!-- Table Toolbar -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
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
                        <button type="submit" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 shadow-sm transition-colors inline-flex items-center gap-1">
                            <i class="bi bi-file-earmark-spreadsheet"></i>CSV
                        </button>
                    </form>
                    <button onclick="window.open('{{ route('parents.print-list') }}', '_blank')" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-colors inline-flex items-center gap-1 no-print">
                        <i class="bi bi-printer"></i>Print
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200">
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
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if(!empty(optional($parent->parent)->profile_photo_path))
                                    <img src="{{ Storage::url(optional($parent->parent)->profile_photo_path) }}" alt="avatar" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-slate-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 font-medium text-sm">
                                        {{ substr($parent->name ?? '', 0, 1) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900 dark:text-white">{{ $parent->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ optional($parent->parent)->parent_code ?? 'P' . str_pad($parent->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $parent->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ optional($parent->parent)->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $parent->children_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full text-xs font-medium">
                                {{ ucfirst($parent->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            @php 
                                $status = optional($parent->parent)->status ?? 'pending';
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                ];
                                $statusClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <div class="flex gap-2 justify-center">
                                <button type="button" onclick="window.openViewParentModal({{ $parent->id }})" class="action-btn action-btn-view" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" onclick="window.openEditParentModal({{ $parent->id }})" class="action-btn action-btn-edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" onclick="window.deleteParent({{ $parent->id }})" class="action-btn action-btn-delete" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $parents->firstItem() ?? 0 }} to {{ $parents->lastItem() ?? 0 }} of {{ $parents->total() }} parents
            </div>
            {{ $parents->links() }}
        </div>
    </div>
</div>

<!-- Add Parent Modal -->
<div id="addParentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) document.getElementById('addParentModal').classList.add('hidden')">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <form id="addParentForm" action="{{ route('admin.parents.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            <div class="px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0 bg-red-600 text-white">
                <div>
                    <h3 class="text-lg font-semibold">Add Parent</h3>
                    <p class="text-sm text-red-100">Create a new parent account and assign children</p>
                </div>
                <button type="button" onclick="document.getElementById('addParentModal').classList.add('hidden'); document.getElementById('addParentForm').reset();" class="text-red-100 hover:text-white">✕</button>
            </div>
            <div class="p-6">
                <div class="flex gap-8">
                    <!-- Photo Section -->
                    <div class="flex flex-col items-center">
                        <div id="addParentAvatar" class="relative w-40 h-40 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0 mb-3">
                            <img id="addParentAvatarImg" src="" alt="avatar" class="absolute inset-0 w-full h-full object-cover" style="display:none;">
                            <span id="addParentInitial" class="relative z-10 text-gray-500 dark:text-gray-400 text-4xl"><i class="bi bi-person text-5xl"></i></span>
                        </div>
                        <label class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer">
                            <i class="bi bi-download"></i>
                            Choose photo
                            <input type="file" name="profile_photo" accept="image/*" class="hidden" id="addProfilePhotoInput" onchange="window.previewAddParentPhoto()" />
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Recommended 400x400px. Max 4MB.</p>
                    </div>

                    <!-- Form Section -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                <input type="text" value="Parent" disabled class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-lg text-sm" />
                                <input type="hidden" name="role" value="parent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Parent ID</label>
                                <input name="parent_code" placeholder="Optional (e.g. P0001)" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Full name <span class="text-red-500 text-base">*</span></label>
                                <input name="name" required placeholder="e.g. John Doe" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500 text-base">*</span></label>
                                <input name="email" type="email" required placeholder="name@example.com" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Phone <span class="text-red-500 text-base">*</span></label>
                                <input type="tel" name="phone" required placeholder="Phone number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                                <select name="gender" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Prefer not to say</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Occupation</label>
                                <input type="text" name="occupation" placeholder="e.g. Engineer, Teacher" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500 text-base">*</span></label>
                                <select name="status" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <textarea name="address" rows="2" placeholder="Street, City, Postal code" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                        </div>

                        <!-- Children Selection -->
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Select Children</label>
                            <div class="relative">
                                <input type="text" id="addChildrenSearch" placeholder="Search by name, ID, or email..." class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" />
                                <div id="addChildrenTags" class="flex flex-wrap gap-1 p-2 mt-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-slate-700 min-h-10"></div>
                                <div id="addChildrenDropdown" class="hidden absolute top-12 left-0 w-full border border-gray-300 dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto bg-white dark:bg-slate-700 shadow-lg z-50">
                                    <div class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">Loading students...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addParentModal').classList.add('hidden'); document.getElementById('addParentForm').reset();" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 font-medium transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition shadow-sm">
                        <i class="bi bi-check-lg mr-1"></i>Save Parent
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Parent Modal -->
<div id="viewParentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between sticky top-0 bg-gray-50 dark:bg-slate-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Parent Details</h3>
            <button type="button" onclick="document.getElementById('viewParentModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="viewParentContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Edit Parent Modal -->
<div id="editParentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <form id="editParentForm" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0 bg-red-600 text-white">
                <div>
                    <h3 class="text-lg font-semibold">Edit Parent</h3>
                    <p class="text-sm text-red-100">Update parent account information</p>
                </div>
                <button type="button" onclick="document.getElementById('editParentModal').classList.add('hidden'); document.getElementById('editParentForm').reset();" class="text-red-100 hover:text-white">✕</button>
            </div>
            <div class="p-6" id="editParentContent">
                <!-- Content loaded via AJAX -->
            </div>
        </form>
    </div>
</div>

<script>
    function updatePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function showLoader(message = 'Loading...') {
        document.getElementById('loaderText').textContent = message;
        document.getElementById('globalLoader').classList.remove('hidden');
    }

    function hideLoader() {
        document.getElementById('globalLoader').classList.add('hidden');
    }

    function showConfirm(title, message, callback) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmModal').classList.remove('hidden');

        const cancelBtn = document.getElementById('confirmCancel');
        const okBtn = document.getElementById('confirmOk');

        const cleanup = () => {
            cancelBtn.removeEventListener('click', onCancel);
            okBtn.removeEventListener('click', onOk);
        };

        const onCancel = () => {
            document.getElementById('confirmModal').classList.add('hidden');
            cleanup();
        };

        const onOk = () => {
            document.getElementById('confirmModal').classList.add('hidden');
            cleanup();
            if (callback) callback();
        };

        cancelBtn.addEventListener('click', onCancel);
        okBtn.addEventListener('click', onOk);
    }

    // Delete Parent
    window.deleteParent = function(parentId) {
        showConfirm('Delete Parent', 'Are you sure you want to delete this parent? This action cannot be undone.', () => {
            showLoader('Deleting...');
            fetch(`/admin/parents/${parentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoader();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error deleting parent');
                }
            })
            .catch(error => {
                hideLoader();
                alert('Error: ' + error);
            });
        });
    }

    // Open Add Parent Modal
    window.openAddParentModal = function() {
        document.getElementById('addParentModal').classList.remove('hidden');
    }

    // Preview Add Parent Photo
    window.previewAddParentPhoto = function() {
        const input = document.getElementById('addProfilePhotoInput');
        const img = document.getElementById('addParentAvatarImg');
        const initial = document.getElementById('addParentInitial');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.style.display = 'block';
                initial.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Open View Parent Modal
    window.openViewParentModal = function(parentId) {
        showLoader('Loading...');
        fetch(`/admin/parents/${parentId}`)
            .then(response => response.json())
            .then(data => {
                hideLoader();
                document.getElementById('viewParentContent').innerHTML = data.html;
                document.getElementById('viewParentModal').classList.remove('hidden');
            })
            .catch(error => {
                hideLoader();
                alert('Error loading parent details');
            });
    }

    // Open Edit Parent Modal
    window.openEditParentModal = function(parentId) {
        showLoader('Loading...');
        fetch(`/admin/parents/${parentId}/edit`)
            .then(response => response.json())
            .then(data => {
                hideLoader();
                document.getElementById('editParentContent').innerHTML = data.html;
                document.getElementById('editParentModal').classList.remove('hidden');
            })
            .catch(error => {
                hideLoader();
                alert('Error loading parent details');
            });
    }
</script>

@endsection
