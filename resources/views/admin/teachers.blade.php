@extends('admin.layouts.app')

@section('title', 'Teachers')

@push('styles')
<style>
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
    
    .badge-alumni {
        @apply bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
@include('admin.components.admin-page-header', [
    'title' => 'Teachers',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Teachers']
    ],
    'addButton' => [
        'label' => 'Add Teacher',
        'onclick' => "openAddTeacherModal()"
    ]
])

<div class="space-y-4">
	<!-- Global Loader Overlay -->
	<div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
		<div class="text-center">
			<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
			<p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
		</div>
	</div>

    <!-- Toast Notification Container -->
    <!-- Toast Notification - Uses global toast system from layout -->

    <!-- Professional Confirmation Modal -->
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

    <!-- Filter Card -->
    @include('admin.components.admin-filter-card', [
        'formAction' => route('admin.teachers'),
        'filters' => [
            [
                'name' => 'search',
                'type' => 'text',
                'label' => 'Search',
                'placeholder' => 'Name or email...',
                'value' => request('search', '')
            ],
            [
                'name' => 'status',
                'type' => 'select',
                'label' => 'Status',
                'placeholder' => 'All Status',
                'options' => [
                    'Active' => 'Active',
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

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <!-- Table Toolbar -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Left: Title & Record Count -->
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Teachers List</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ isset($teachers) ? $teachers->total() : 0 }} records)</span>
                </div>

                <!-- Right: Export & Print Buttons -->
                <div class="flex items-center gap-2">
                    <form id="exportTeachersForm" method="GET" action="{{ route('admin.teachers.export') }}" class="inline-block">
                        <input type="hidden" name="search" value="{{ request('search', '') }}">
                        <input type="hidden" name="status" value="{{ request('status', '') }}">
                        <input type="hidden" name="course" value="{{ request('course', '') }}">
                        <button type="submit" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 shadow-sm transition-colors inline-flex items-center gap-1">
                            <i class="bi bi-file-earmark-spreadsheet"></i>CSV
                        </button>
                    </form>
                    <button onclick="window.open('{{ route('teachers.print-list') }}', '_blank')" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-colors inline-flex items-center gap-1 no-print">
                        <i class="bi bi-printer"></i>Print
                    </button>
                </div>
            </div>
        </div>
        <div id="teachersTableContainer" class="overflow-x-auto">
            <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200">
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
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors border-t border-gray-200 dark:border-slate-700">
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-3">
                            @php
                                $teacherPhoto = $teacher->teacher->profile_photo_path ?? null;
                                $teacherPhotoUrl = $teacherPhoto ? (
                                    \Illuminate\Support\Str::startsWith($teacherPhoto, 'storage/') 
                                    ? asset($teacherPhoto) 
                                    : asset('storage/' . $teacherPhoto)
                                ) : null;
                            @endphp
                            @if($teacherPhotoUrl)
                                <img src="{{ $teacherPhotoUrl }}" alt="avatar" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-medium text-sm">T</div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $teacher->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $teacher->email }}</td>
                    <td class="px-6 py-4 text-sm"><span class="inline-block px-3 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded-full text-xs font-medium">{{ ucfirst($teacher->role) }}</span></td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $teacher->teacher->department ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if(isset($teacher->teaching_load))
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded text-xs font-medium">
                                    {{ $teacher->teaching_load['subjects_count'] ?? 0 }} subjects
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">|</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $teacher->teaching_load['total_hours'] ?? 0 }} hrs/wk</span>
                            </div>
                            @if(count($teacher->teaching_load['subjects'] ?? []) > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ implode(', ', array_slice($teacher->teaching_load['subjects'], 0, 2)) }}{{ count($teacher->teaching_load['subjects']) > 2 ? '...' : '' }}</p>
                            @endif
                        @else
                            <span class="text-gray-400 dark:text-gray-500">—</span>
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
                        <div class="flex gap-2 justify-center">
                            <button type="button" onclick="openViewTeacherModal({{ $teacher->id }})" class="action-btn action-btn-view" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" onclick="openEditTeacherModal({{ $teacher->id }})" class="action-btn action-btn-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" onclick="deleteTeacher({{ $teacher->id }})" class="action-btn action-btn-delete" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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
        @include('admin.components.admin-pagination', [
            'paginator' => $teachers
        ])
    </div>
</div>

<!-- Add Teacher Modal -->
<div id="addTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4" onclick="if(event.target===this) closeAddTeacherModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0 bg-red-600 text-white">
            <div>
                <h3 class="text-lg font-semibold">Add Teacher</h3>
                <p class="text-sm text-red-100">Create a new teacher account</p>
            </div>
            <button type="button" onclick="closeAddTeacherModal()" class="text-red-100 hover:text-white p-1 rounded-lg hover:bg-red-700 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <form id="addTeacherForm" action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <div class="flex gap-8">
                    <!-- Photo Section -->
                    <div class="flex flex-col items-center">
                        <div id="addTeacherAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0 mb-3">
                            <img id="addTeacherAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                            <span id="addTeacherInitial"><i class="bi bi-person text-5xl"></i></span>
                        </div>
                        <label class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <i class="bi bi-download"></i>
                            Choose photo
                            <input type="file" name="profile_photo" accept="image/*" class="hidden" id="addProfilePhotoInput" onchange="previewAddTeacherPhoto()" />
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Recommended 400x400px. Max 4MB.</p>
                    </div>

                    <!-- Form Section -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                                <select name="gender" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                    <option value="">Prefer not to say</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                <input type="text" value="Teacher" disabled class="w-full px-3 py-2 border rounded-md text-sm bg-gray-100" />
                                <input type="hidden" name="role" value="teacher" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Full name <span class="text-red-500 text-lg font-bold">*</span></label>
                                <input name="name" required placeholder="e.g. John Doe" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Teacher ID <span class="text-red-500 text-lg font-bold">*</span></label>
                                <input name="teacher_id" required placeholder="Teacher ID" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500 text-lg font-bold">*</span></label>
                                <input name="email" type="email" required placeholder="name@example.com" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500 text-lg font-bold">*</span></label>
                                <input type="tel" name="phone" required placeholder="Phone number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Department <span class="text-red-500 text-lg font-bold">*</span></label>
                                <input name="department" required placeholder="Department or course" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Qualification</label>
                                <input type="text" name="qualification" placeholder="e.g. M.Sc, B.Ed" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500 text-lg font-bold">*</span></label>
                                <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                    <option value="Retired">Retired</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="3" placeholder="Street, City, Postal code" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors"></textarea>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bio</label>
                            <textarea name="bio" rows="4" placeholder="Brief biography" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddTeacherModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i>Add Teacher
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Teacher Modal -->
<div id="editTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4" onclick="if(event.target===this) closeEditTeacherModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0 bg-red-600 text-white">
            <div>
                <h3 class="text-lg font-semibold">Edit Teacher</h3>
                <p class="text-sm text-red-100">Update teacher information</p>
            </div>
            <button type="button" onclick="closeEditTeacherModal()" class="text-red-100 hover:text-white p-1 rounded-lg hover:bg-red-700 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <form id="editTeacherForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="p-6">
                <input type="hidden" name="_id" id="editTeacherId" />
                <div class="flex gap-8">
                    <!-- Photo Section -->
                    <div class="flex flex-col items-center">
                        <div id="editTeacherAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0 mb-3">
                            <img id="editTeacherAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                            <span id="editTeacherInitial">T</span>
                        </div>
                        <label class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <i class="bi bi-download"></i>
                            Choose photo
                            <input type="file" name="profile_photo" accept="image/*" class="hidden" id="profilePhotoInput" onchange="previewTeacherPhoto()" />
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Recommended 400x400px. Max 4MB.</p>
                    </div>

                    <!-- Details Section -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Role</label>
                                <input type="text" value="Teacher" disabled class="mt-1 block w-full px-3 py-2 border rounded-md text-sm bg-gray-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Gender</label>
                                <select id="edit_gender" name="gender" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
                                    <option value="">Prefer not to say</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Full name</label>
                                <input id="edit_name" name="name" required class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Email</label>
                                <input id="edit_email" name="email" type="email" required class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Phone</label>
                                <input id="edit_phone" type="tel" name="phone" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Department</label>
                                <input id="edit_department" name="department" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Qualification</label>
                                <input type="text" id="edit_qualification" name="qualification" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Teacher ID</label>
                                <input id="edit_teacher_id" name="teacher_id" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Status</label>
                                <select id="edit_status" name="status" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                    <option value="Retired">Retired</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700">Address</label>
                            <textarea id="edit_address" name="address" rows="3" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors"></textarea>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700">Bio</label>
                            <textarea id="edit_bio" name="bio" rows="4" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <button type="button" onclick="deleteTeacher(document.getElementById('editTeacherId').value)" class="px-4 py-2 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-600 rounded-lg text-sm hover:bg-red-100 dark:hover:bg-red-900/50 transition">Delete</button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditTeacherModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Teacher Modal -->
<div id="viewTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4" onclick="if(event.target===this) closeViewTeacherModal()">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0 bg-red-600 text-white">
            <div>
                <h3 class="text-lg font-semibold">Teacher Details</h3>
                <p class="text-sm text-red-100">View teacher information</p>
            </div>
            <button type="button" onclick="event.preventDefault(); closeViewTeacherModal(); return false;" class="text-red-100 hover:text-white p-1 rounded-lg hover:bg-red-700 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="flex gap-8">
                <!-- Photo Section -->
                <div class="flex flex-col items-center">
                    <div id="viewTeacherAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0">
                        <img id="viewTeacherAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                        <span id="viewTeacherInitial">T</span>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="text-xs font-medium text-gray-700">Full name</label>
                            <p id="viewTeacherName" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Email</label>
                            <p id="viewTeacherEmail" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Phone</label>
                            <p id="viewTeacherPhone" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Department</label>
                            <p id="viewTeacherDept" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Teacher ID</label>
                            <p id="viewTeacherCode" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Status</label>
                            <p id="viewTeacherStatus" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Gender</label>
                            <p id="viewTeacherGender" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Qualification</label>
                            <p id="viewTeacherQualification" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-700">Bio</label>
                        <p id="viewTeacherBio" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-700">Address</label>
                        <p id="viewTeacherAddress" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-between gap-3">
                <div class="flex gap-2">
                    <button type="button" onclick="printTeacher()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                        <i class="bi bi-printer"></i>Print
                    </button>
                </div>
                <button type="button" onclick="closeViewTeacherModal()" class="px-3 py-2 border rounded text-sm">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Modal helper functions with body overflow control
function openAddTeacherModal() {
    document.body.style.overflow = 'hidden';
    document.getElementById('addTeacherModal').classList.remove('hidden');
}

function closeAddTeacherModal() {
    document.body.style.overflow = '';
    document.getElementById('addTeacherModal').classList.add('hidden');
}

function openEditTeacherModal(id) {
    // Show modal immediately so user sees feedback
    document.body.style.overflow = 'hidden';
    document.getElementById('editTeacherModal').classList.remove('hidden');
    
    // Then fetch the data
    showLoading('Loading teacher...');
    fetch(`/admin/teachers/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('editTeacherId').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_department').value = data.department || '';
            document.getElementById('edit_teacher_id').value = data.teacher_code || '';
            document.getElementById('edit_gender').value = data.gender || '';
            document.getElementById('edit_status').value = data.status || 'active';
            document.getElementById('edit_bio').value = data.bio || '';
            document.getElementById('edit_qualification').value = data.qualification || '';
            document.getElementById('edit_address').value = data.address || '';
            if (data.profile_photo_path) {
                // Handle both with and without storage/ prefix
                const photoPath = data.profile_photo_path.startsWith('storage/') 
                    ? '/' + data.profile_photo_path 
                    : '/storage/' + data.profile_photo_path;
                document.getElementById('editTeacherAvatarImg').src = photoPath;
                document.getElementById('editTeacherAvatarImg').style.display = 'block';
                document.getElementById('editTeacherInitial').style.display = 'none';
            } else {
                document.getElementById('editTeacherInitial').style.display = 'block';
                document.getElementById('editTeacherAvatarImg').style.display = 'none';
            }
            document.getElementById('editTeacherForm').action = '/admin/teachers/' + data.id;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load teacher details: ' + error.message);
            document.getElementById('editTeacherModal').classList.add('hidden');
        })
        .finally(() => {
            try { hideLoading(); } catch(e) { /* fallback if not available */ }
        });
}

function closeEditTeacherModal() {
    document.body.style.overflow = '';
    document.getElementById('editTeacherModal').classList.add('hidden');
}

function openViewTeacherModal(id) {
    // Store teacher ID for print/download functionality
    currentViewingTeacherId = id;
    
    // Show modal immediately so user sees feedback
    document.body.style.overflow = 'hidden';
    document.getElementById('viewTeacherModal').classList.remove('hidden');
    
    // Then fetch the data
    showLoading('Loading teacher...');
    fetch(`/admin/teachers/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('viewTeacherName').innerText = data.name;
            document.getElementById('viewTeacherEmail').innerText = data.email;
            document.getElementById('viewTeacherPhone').innerText = data.phone || '—';
            document.getElementById('viewTeacherDept').innerText = data.department || '—';
            document.getElementById('viewTeacherCode').innerText = data.teacher_code || '—';
            document.getElementById('viewTeacherStatus').innerText = data.status || '—';
            document.getElementById('viewTeacherBio').innerText = data.bio || '—';
            document.getElementById('viewTeacherQualification').innerText = data.qualification || '—';
            document.getElementById('viewTeacherAddress').innerText = data.address || '—';
            if (data.profile_photo_path) {
                // Handle both with and without storage/ prefix
                const photoPath = data.profile_photo_path.startsWith('storage/') 
                    ? '/' + data.profile_photo_path 
                    : '/storage/' + data.profile_photo_path;
                document.getElementById('viewTeacherAvatarImg').src = photoPath;
                document.getElementById('viewTeacherAvatarImg').style.display = 'block';
                document.getElementById('viewTeacherInitial').style.display = 'none';
            } else {
                document.getElementById('viewTeacherInitial').style.display = 'block';
                document.getElementById('viewTeacherAvatarImg').style.display = 'none';
            }
            // set gender if provided
            if (data.gender !== undefined) {
                const genderEl = document.getElementById('viewTeacherGender');
                if (genderEl) genderEl.textContent = data.gender ? (data.gender.charAt(0).toUpperCase()+data.gender.slice(1)) : '—';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load teacher details: ' + error.message);
            document.getElementById('viewTeacherModal').classList.add('hidden');
        })
        .finally(() => {
            try { hideLoading(); } catch(e) { }
        });
}

function closeViewTeacherModal() {
    document.body.style.overflow = '';
    document.getElementById('viewTeacherModal').classList.add('hidden');
}

function deleteTeacher(id) {
    // Use showConfirm if available, fallback to native confirm
    if (typeof showConfirm === 'function') {
        showConfirm({
            title: 'Delete Teacher',
            message: 'Are you sure you want to delete this teacher? This cannot be undone.',
            type: 'delete',
            okText: 'Delete',
            cancelText: 'Cancel'
        }).then((confirmed) => {
            if (confirmed) performTeacherDelete(id);
        });
    } else {
        if (confirm('Are you sure you want to delete this teacher?')) performTeacherDelete(id);
    }
}

function performTeacherDelete(id) {
    showLoading('Deleting teacher...');
    fetch(`/admin/teachers/${id}`, { 
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Failed to delete');
            return response.json();
        })
        .then(data => {
            showToast('Teacher deleted successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to delete teacher', 'error');
        })
        .finally(() => {
            try { hideLoading(); } catch(e) { }
        });
}

// Toast notification - uses global showToast from admin/layouts/app.blade.php

// Improved error handling for form submissions - shows validation errors as toast
function handleFormError(error, defaultMessage = 'An error occurred') {
    console.error('Error:', error);
    
    let errorMessage = defaultMessage;
    
    // Handle Laravel validation errors (422 response)
    if (error.response && error.response.status === 422) {
        const data = error.response.data;
        if (data && data.errors) {
            // Get first validation error message
            const firstErrorKey = Object.keys(data.errors)[0];
            if (firstErrorKey && data.errors[firstErrorKey]) {
                errorMessage = data.errors[firstErrorKey][0];
            }
        } else if (data && data.message) {
            errorMessage = data.message;
        }
    } else if (error.message) {
        errorMessage = error.message;
    }
    
    showToast(errorMessage, 'error');
    return errorMessage;
}

function setTeacherSubmitState(form, isSubmitting, loadingText) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (!submitBtn) {
        return;
    }

    if (!submitBtn.dataset.originalHtml) {
        submitBtn.dataset.originalHtml = submitBtn.innerHTML;
    }

    submitBtn.disabled = isSubmitting;
    submitBtn.innerHTML = isSubmitting ? loadingText : submitBtn.dataset.originalHtml;
}

async function parseTeacherResponse(response, fallbackMessage) {
    let payload = null;

    try {
        payload = await response.json();
    } catch (error) {
        payload = null;
    }

    if (response.ok) {
        return payload;
    }

    const requestError = new Error(payload?.message || fallbackMessage);
    requestError.response = {
        status: response.status,
        data: payload,
    };

    throw requestError;
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // Add keyboard escape handler for all modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modals = ['addTeacherModal', 'editTeacherModal', 'viewTeacherModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    if (modalId === 'addTeacherModal') closeAddTeacherModal();
                    else if (modalId === 'editTeacherModal') closeEditTeacherModal();
                    else if (modalId === 'viewTeacherModal') closeViewTeacherModal();
                }
            });
        }
    });
    
    // AJAX form submission for Add Teacher
    const addForm = document.getElementById('addTeacherForm');
    if (addForm) {
        addForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (addForm.dataset.submitting === 'true') {
                return;
            }

            addForm.dataset.submitting = 'true';
            setTeacherSubmitState(addForm, true, '<i class="bi bi-arrow-repeat animate-spin me-1"></i>Adding...');
            showLoading('Adding teacher...');

            try {
                const response = await fetch(addForm.action, {
                    method: 'POST',
                    body: new FormData(addForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await parseTeacherResponse(response, 'Failed to add teacher');

                if (data.success) {
                    showToast(data.message || 'Teacher added successfully', 'success');
                    addForm.reset();
                    const addTeacherAvatarImg = document.getElementById('addTeacherAvatarImg');
                    const addTeacherInitial = document.getElementById('addTeacherInitial');
                    if (addTeacherAvatarImg) {
                        addTeacherAvatarImg.src = '';
                        addTeacherAvatarImg.style.display = 'none';
                    }
                    if (addTeacherInitial) {
                        addTeacherInitial.style.display = 'inline-flex';
                    }
                    closeAddTeacherModal();
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                handleFormError(error, 'Failed to add teacher');
            } finally {
                addForm.dataset.submitting = 'false';
                setTeacherSubmitState(addForm, false);
                try { hideLoading(); } catch(e) { }
            }
        });

    // Image preview helpers for teacher forms
    window.previewAddTeacherPhoto = function() {
        const file = document.getElementById('addProfilePhotoInput').files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('addTeacherAvatarImg');
                const init = document.getElementById('addTeacherInitial');
                if (img) { img.src = e.target.result; img.style.display = 'block'; }
                if (init) init.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    };

    window.previewTeacherPhoto = function() {
        const file = document.getElementById('profilePhotoInput').files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('editTeacherAvatarImg');
                const init = document.getElementById('editTeacherInitial');
                if (img) { img.src = e.target.result; img.style.display = 'block'; }
                if (init) init.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    };
    }
    
    // AJAX form submission for Edit Teacher
    const editForm = document.getElementById('editTeacherForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (editForm.dataset.submitting === 'true') {
                return;
            }

            const teacherId = document.getElementById('editTeacherId').value;
            editForm.dataset.submitting = 'true';
            setTeacherSubmitState(editForm, true, '<i class="bi bi-arrow-repeat animate-spin me-1"></i>Saving...');
            showLoading('Saving teacher...');

            try {
                const response = await fetch(`/admin/teachers/${teacherId}`, {
                    method: 'POST',
                    body: new FormData(editForm),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await parseTeacherResponse(response, 'Failed to update teacher');

                if (data.success) {
                    showToast(data.message || 'Teacher updated successfully', 'success');
                    closeEditTeacherModal();
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                handleFormError(error, 'Failed to update teacher');
            } finally {
                editForm.dataset.submitting = 'false';
                setTeacherSubmitState(editForm, false);
                try { hideLoading(); } catch(e) { }
            }
        });
    }
    
    // Check if we just came back from a form submission
    const notification = sessionStorage.getItem('showNotification');
    if (notification) {
        sessionStorage.removeItem('showNotification');
        if (notification === 'teacher_added') {
            showToast('Teacher added successfully', 'success');
        } else if (notification === 'teacher_updated') {
            showToast('Teacher updated successfully', 'success');
        }
    }
});

// Filter functionality - filters only on Apply Filter button click
function filterTeachers() {
    showLoading('Filtering teachers...');
    setTimeout(() => {
        const searchText = document.getElementById('teachersSearch').value.toLowerCase();
        const statusFilter = document.getElementById('teachersStatusFilter').value.toLowerCase();
        const courseFilter = document.getElementById('teachersCourseFilter').value.toLowerCase();
        const rows = document.querySelectorAll('#teachersTableContainer tbody tr');

        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.querySelector('p.font-medium')?.textContent.toLowerCase() || '';
            const email = row.querySelector('p.text-xs')?.textContent.toLowerCase() || '';
            // Get all cells in the row
            const cells = row.querySelectorAll('td');
            const status = cells[5]?.textContent.toLowerCase() || ''; // Status is in 6th column
            const course = cells[4]?.textContent.toLowerCase() || ''; // Course is in 5th column

            const matchesSearch = name.includes(searchText) || email.includes(searchText);
            const matchesStatus = !statusFilter || status.includes(statusFilter);
            const matchesCourse = !courseFilter || course.includes(courseFilter);

            if (matchesSearch && matchesStatus && matchesCourse) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show "no results" if all rows are hidden
        if (visibleCount === 0) {
            const tbody = document.querySelector('#teachersTableContainer tbody');
            if (!document.getElementById('noResultsRow')) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.id = 'noResultsRow';
                noResultsRow.innerHTML = '<td colspan="7" class="px-3 py-4 text-center text-gray-500">No teachers found matching the criteria.</td>';
                tbody.appendChild(noResultsRow);
            }
        } else {
            const noResultsRow = document.getElementById('noResultsRow');
            if (noResultsRow) noResultsRow.remove();
        }
        hideLoading();
    }, 300);
}

// Reset filter function
function resetTeachersFilter() {
    // Clear all filter inputs
    document.getElementById('teachersSearch').value = '';
    document.getElementById('teachersStatusFilter').value = '';
    document.getElementById('teachersCourseFilter').value = '';
    
    // Re-apply filter to show all teachers
    filterTeachers();
}

// =====================
// Print and Download Functions
// =====================
let currentViewingTeacherId = null;

function printTeacher() {
    if (!currentViewingTeacherId) {
        showToast('Teacher ID not found', 'error');
        return;
    }
    // Mark body for modal printing context
    document.body.classList.add('printing-modal');
    window.print();
    // Remove after print dialog closes
    setTimeout(() => {
        document.body.classList.remove('printing-modal');
    }, 500);
}

</script>
@endsection


