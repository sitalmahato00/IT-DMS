@extends('admin.layouts.app')

@section('title', 'Alumni Students')

@push('styles')
<style>
    .no-print {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
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
    
    /* Action buttons */
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

{{-- Page Header --}}
@include('admin.components.admin-page-header', [
    'title' => 'Alumni Students',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Alumni Students']
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
				'title' => 'Total Alumni',
				'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni',1); })->count(),
				'icon' => 'bi-mortarboard-fill',
				'color' => 'purple'
			],
			[
				'title' => 'Active Alumni',
				'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni',1)->where('status','active'); })->count(),
				'icon' => 'bi-check-circle',
				'color' => 'green'
			],
			[
				'title' => 'Inactive Alumni',
				'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni',1)->where('status','inactive'); })->count(),
				'icon' => 'bi-x-circle',
				'color' => 'orange'
			]
		]
	])

	<!-- Filters & Actions -->
	@include('admin.components.admin-filter-card', [
		'formAction' => route('admin.alumni-students'),
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
					'inactive' => 'Inactive',
					'pending' => 'Pending'
				],
				'value' => request('status', '')
			],
			[
				'name' => 'semester',
				'type' => 'select',
				'label' => 'Semester',
				'placeholder' => 'All Semesters',
				'options' => [
					'1' => 'Sem 1',
					'2' => 'Sem 2',
					'3' => 'Sem 3',
					'4' => 'Sem 4',
					'5' => 'Sem 5',
					'6' => 'Sem 6'
				],
				'value' => request('semester', '')
			],
			[
				'name' => 'academic_year',
				'type' => 'select',
				'label' => 'Academic Year',
				'placeholder' => 'All Years',
				'options' => $academicYears ?? [],
				'value' => request('academic_year', '')
			]
		],
		'showReset' => true,
		'resetRoute' => route('admin.alumni-students')
	])

	<!-- Alumni List -->
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

				<!-- Right: Export & Print Buttons -->
				<div class="flex items-center gap-2">
					<form id="exportAlumniForm" method="GET" action="{{ route('admin.alumni-students.export') }}" class="inline-block">
						<input type="hidden" name="q" value="{{ request('q') }}">
						<input type="hidden" name="status" value="{{ request('status') }}">
						<input type="hidden" name="semester" value="{{ request('semester') }}">
						<input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
						<button type="submit" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 shadow-sm transition-colors inline-flex items-center gap-1">
							<i class="bi bi-file-earmark-spreadsheet"></i>CSV
						</button>
					</form>
					<button onclick="window.open('{{ route('alumni-students.print-list') }}', '_blank')" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-colors inline-flex items-center gap-1 no-print">
						<i class="bi bi-printer"></i>Print
					</button>
				</div>
			</div>
		</div>
		
		<!-- Bulk actions toolbar -->
		<div class="flex items-center justify-between px-6 py-3 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
			<div class="flex items-center gap-3">
				<label class="inline-flex items-center gap-2 cursor-pointer">
					<input type="checkbox" id="select_all" class="form-checkbox rounded text-red-600 focus:ring-red-500" />
					<span class="text-sm text-gray-700 dark:text-gray-300">Select All</span>
				</label>
			</div>
			<form id="bulkAlumniForm" method="POST" action="{{ route('admin.alumni-students.bulk') }}" class="inline-flex items-center gap-2">
				@csrf
				<select id="bulk_action_select" class="px-3 py-1.5 border border-gray-300 rounded text-sm bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
					<option value="">-- Bulk Actions --</option>
					<option value="set_status_active">Mark Active</option>
					<option value="set_status_inactive">Mark Inactive</option>
					<option value="remove_alumni">Remove from Alumni</option>
				</select>
				<input type="hidden" id="bulk_semester" name="semester" value="" />
				<button type="button" id="applyBulkAction" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700 font-medium transition">Apply</button>
			</form>
		</div>

		<div class="overflow-x-auto">
			<table class="min-w-full text-left divide-y divide-gray-200 dark:divide-slate-700">
				<thead class="bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200">
					<tr>
						<th class="px-6 py-3 text-center"><input type="checkbox" class="select-checkbox rounded text-red-600 focus:ring-red-500" /></th>
						<th class="px-6 py-3">Name</th>
						<th class="px-6 py-3">Email</th>
						<th class="px-6 py-3">Roll No</th>
						<th class="px-6 py-3">Semester</th>
						<th class="px-6 py-3">Department</th>
						<th class="px-6 py-3 text-center">Status</th>
						<th class="px-6 py-3 text-center">Actions</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 dark:divide-slate-700">
					@forelse($students ?? [] as $student)
					<tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors">
						<td class="px-6 py-4">
							<input type="checkbox" class="select-checkbox rounded text-red-600 focus:ring-red-500" value="{{ $student->id }}" />
						</td>
						<td class="px-6 py-4">
							<div class="flex items-center gap-3">
								@if(!empty($student->profile_photo_path))
									<img src="{{ asset('storage/' . $student->profile_photo_path) }}" alt="avatar" class="w-10 h-10 rounded-full object-cover">
								@else
									<div class="w-10 h-10 bg-gray-100 dark:bg-slate-600 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 font-medium text-sm">
										{{ substr($student->name ?? '', 0, 1) }}
									</div>
								@endif
								<span class="font-medium text-gray-900">{{ $student->name }}</span>
							</div>
						</td>
						<td class="px-6 py-4 text-sm text-gray-600">{{ $student->email }}</td>
						<td class="px-6 py-4 text-sm text-gray-600">{{ $student->student->roll_no ?? '-' }}</td>
						<td class="px-6 py-4 text-sm text-gray-600">{{ $student->student->semester ?? '-' }}</td>
						<td class="px-6 py-4 text-sm text-gray-600">{{ $student->student->department ?? '-' }}</td>
						<td class="px-6 py-4 text-center text-sm">
							@php 
								$status = $student->student->status ?? 'pending';
								$statusColors = [
									'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
									'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
									'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
									'suspended' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400'
								];
								$statusClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
							@endphp
							<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span>
						</td>
						<td class="px-6 py-4 text-center text-sm">
							<div class="flex gap-2 justify-center">
								<a href="{{ route('admin.students.show', $student->id) }}" class="action-btn action-btn-view" title="View">
									<i class="bi bi-eye"></i>
								</a>
								<a href="{{ route('admin.students.edit', $student->id) }}" class="action-btn action-btn-edit" title="Edit">
									<i class="bi bi-pencil"></i>
								</a>
								<a href="javascript:void(0);" onclick="removeFromAlumni('{{ $student->id }}')" class="text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200" title="Remove from Alumni">
									<i class="bi bi-x-circle"></i>
								</a>
								<a href="javascript:void(0);" onclick="deleteStudent('{{ $student->id }}')" class="action-btn action-btn-delete" title="Delete">
									<i class="bi bi-trash"></i>
								</a>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="8" class="px-6 py-12 text-center text-gray-500">
							<div class="flex flex-col items-center justify-center">
								<i class="bi bi-inbox text-4xl mb-3 text-gray-300"></i>
								<p class="text-gray-600">No alumni students found</p>
							</div>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<!-- Pagination -->
		<div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
			<div class="text-sm text-gray-600">
				Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} alumni
			</div>
			{{ $students->links() }}
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

	// Remove from Alumni
	function removeFromAlumni(studentId) {
		showConfirm('Remove from Alumni', 'Are you sure you want to remove this student from alumni status?', () => {
			showLoader('Updating...');
			fetch(`/admin/students/${studentId}/toggle-alumni`, {
				method: 'POST',
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
				}
			})
			.catch(error => {
				hideLoader();
				alert('Error: ' + error);
			});
		});
	}

	// Delete Student
	function deleteStudent(studentId) {
		showConfirm('Delete Student', 'Are you sure you want to delete this student? This action cannot be undone.', () => {
			showLoader('Deleting...');
			fetch(`/admin/students/${studentId}`, {
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
				}
			})
			.catch(error => {
				hideLoader();
				alert('Error: ' + error);
			});
		});
	}

	// Select All Checkbox
	document.getElementById('select_all')?.addEventListener('change', function() {
		const checkboxes = document.querySelectorAll('.select-checkbox');
		checkboxes.forEach(cb => cb.checked = this.checked);
	});

	// Apply Bulk Action
	document.getElementById('applyBulkAction')?.addEventListener('click', function() {
		const action = document.getElementById('bulk_action_select').value;
		if (!action) {
			alert('Please select an action');
			return;
		}

		const selectedIds = Array.from(document.querySelectorAll('.select-checkbox:checked')).map(cb => cb.value);
		if (selectedIds.length === 0) {
			alert('Please select at least one student');
			return;
		}

		// If removing from alumni, ask for semester
		if (action === 'remove_alumni') {
			const semester = prompt('Enter semester (1-6) to assign to removed students:');
			if (!semester || !['1','2','3','4','5','6'].includes(semester)) {
				alert('Invalid semester');
				return;
			}
			document.getElementById('bulk_semester').value = semester;
		}

		showConfirm('Confirm Bulk Action', `Apply "${action}" to ${selectedIds.length} student(s)?`, () => {
			const form = document.getElementById('bulkAlumniForm');
			const input = document.createElement('input');
			input.type = 'hidden';
			input.name = 'action';
			input.value = action;
			form.appendChild(input);

			const idsInput = document.createElement('input');
			idsInput.type = 'hidden';
			idsInput.name = 'ids[]';
			idsInput.value = selectedIds.join(',');
			form.appendChild(idsInput);

			showLoader('Applying bulk action...');
			form.submit();
		});
	});
</script>

@endsection
