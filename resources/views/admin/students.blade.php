@extends('admin.layouts.app')

@section('title', 'Students')

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
    
    .badge-alumni {
        @apply bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400;
    }
</style>
@endpush

@section('content')

{{-- Page Header - Using standardized component --}}
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

<div class="space-y-0 max-w-full overflow-x-hidden">
	<!-- Global Loader Overlay -->
	<div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
		<div class="text-center">
			<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
			<p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
		</div>
	</div>

	<!-- Toast Notification Container - Uses global toast system from layout -->

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

	<!-- View Student Modal -->
	<div id="viewStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4" onclick="if(event.target===this) closeViewStudentModal()">
	    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-auto border border-gray-200 dark:border-slate-700">
	        <div class="px-6 py-4 border-b-2 border-red-600 flex items-center justify-between sticky top-0 bg-red-600 text-white">
	            <div>
	                <h3 class="text-lg font-semibold">View Student</h3>
	                <p class="text-sm text-red-100">Student information and details</p>
	            </div>
	            <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="text-red-100 hover:text-white p-1 rounded-lg hover:bg-red-700 transition">
	                <i class="bi bi-x-lg text-lg"></i>
	            </button>
	        </div>
	        <div class="p-6">
	            <div class="flex gap-8">
	                <!-- Photo Section -->
	                <div class="flex flex-col items-center">
	                    <div id="viewStudentAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0">
	                        <img id="viewStudentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
	                        <span id="viewStudentInitial"><i class="bi bi-person text-5xl"></i></span>
	                    </div>
	                </div>

	                <!-- Details Section -->
	                <div class="flex-1">
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Full name</label>
	                            <p id="view_name" class="text-sm text-gray-900">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Email</label>
	                            <p id="view_email" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Phone</label>
	                            <p id="view_phone" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Semester</label>
	                            <p id="view_semester" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Department</label>
	                            <p id="view_department" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Roll No</label>
	                            <p id="view_roll_no" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-lg">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Date of birth (AD)</label>
                            <p id="view_dob" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-lg">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Date of birth (BS)</label>
                            <p id="view_dob_bs" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
						<div class="bg-gray-50 dark:bg-slate-700/50 p-3 rounded-lg">
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Academic Year</label>
	                            <p id="view_batch_year" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Address</label>
	                            <p id="view_address" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Gender</label>
	                            <p id="view_gender" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Blood Group</label>
	                            <p id="view_blood_group" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Status</label>
	                            <p id="view_status" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Role</label>
	                            <p id="view_role" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                        <div>
	                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Emergency Contact</label>
	                            <p id="view_emergency_contact" class="text-sm font-medium text-gray-900 dark:text-white">—</p>
	                        </div>
	                    </div>
	                    <div class="mt-5 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
	                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Bio</label>
	                        <p id="view_bio" class="text-sm text-gray-900 dark:text-white leading-relaxed">—</p>
	                    </div>
	                </div>
	            </div>
	        </div>

        <div class="px-6 py-4 border-t flex justify-between gap-3">
            <div class="flex gap-2">
                <button type="button" onclick="initiateStudentPrint()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition-colors">
                    <i class="bi bi-printer"></i>Print Document
                </button>
            </div>
            <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">Close</button>
        </div>
    </div>
</div>

	<!-- Stats Cards -->
	@include('admin.components.admin-stats-cards', [
		'cards' => [
			['title' => 'Active Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('status','active')->where(function($s) { $s->where('is_alumni', 0)->orWhereNull('is_alumni'); }); })->count(), 'icon' => 'bi-check-circle', 'color' => 'green'],
			['title' => 'Inactive Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('status','inactive')->where(function($s) { $s->where('is_alumni', 0)->orWhereNull('is_alumni'); }); })->count(), 'icon' => 'bi-x-circle', 'color' => 'red'],
			['title' => 'Pending Students', 'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('status','pending')->where(function($s) { $s->where('is_alumni', 0)->orWhereNull('is_alumni'); }); })->count(), 'icon' => 'bi-hourglass-split', 'color' => 'yellow'],
			['title' => 'Total Alumni', 'value' => \App\Models\User::where('role','student')->whereHas('student', function($q) { $q->where('is_alumni', 1); })->count(), 'icon' => 'bi-mortarboard', 'color' => 'purple'],
		]
	])

	{{-- Filter Card - Using standardized component --}}
@include('admin.components.admin-filter-card', [
    'formAction' => route('admin.students'),
    'filters' => [
        ['name' => 'q', 'type' => 'text', 'placeholder' => 'Search by name or email...', 'value' => request('q'), 'label' => 'Search'],
        ['name' => 'status', 'type' => 'select', 'options' => ['all' => 'All', 'active' => 'Active', 'inactive' => 'Inactive', 'pending' => 'Pending', 'alumni' => 'Alumni'], 'value' => request('status'), 'label' => 'Status'],
        ['name' => 'semester', 'type' => 'select', 'options' => ['' => 'All Semesters', '1' => 'Sem 1', '2' => 'Sem 2', '3' => 'Sem 3', '4' => 'Sem 4', '5' => 'Sem 5', '6' => 'Sem 6'], 'value' => request('semester'), 'label' => 'Semester'],
        ['name' => 'academic_year', 'type' => 'select', 'options' => array_merge(['' => 'All Years'], array_combine($academicYears ?? [], $academicYears ?? [])), 'value' => request('academic_year'), 'label' => 'Academic Year'],
        ['name' => 'subject', 'type' => 'select', 'options' => ['' => 'All Subjects'] + $subjects->pluck('name', 'id')->toArray(), 'value' => request('subject'), 'label' => 'Subject']
    ],
    'showReset' => true,
    'resetRoute' => route('admin.students')
])

<!-- Additional hidden inputs for tabs -->
	<input type="hidden" name="tab" id="filter_tab" value="{{ request('tab','active') }}">
	<input type="hidden" name="alumni" id="filter_alumni" value="{{ request('alumni') }}">
	<input type="hidden" name="subject" value="{{ request('subject') }}">

	<!-- Data Table Card -->
<input type="hidden" name="tab" id="filter_tab" value="{{ request('tab','active') }}">
<input type="hidden" name="alumni" id="filter_alumni" value="{{ request('alumni') }}">

<!-- Data Table Card -->
	<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
		<!-- Table Toolbar -->
		<div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
				<!-- Left: Bulk Actions -->
				<div class="flex items-center gap-2">
					<label class="inline-flex items-center gap-2">
						<input type="checkbox" id="select_all" class="form-checkbox rounded text-red-600 focus:ring-red-500" />
						<span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Select all</span>
					</label>
					<select id="bulk_action" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
						<option value="">Bulk actions</option>
						<option value="set_status_active">Set status: Active</option>
						<option value="set_status_inactive">Set status: Inactive</option>
						<option value="set_semester">Set semester (all selected)</option>
						<option value="move_alumni">Move to alumni</option>
						<option value="remove_alumni">Remove from alumni</option>
						<option value="export_csv">Export selected (CSV)</option>
					</select>
					<button id="apply_bulk" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">Apply</button>
				</div>

				<!-- Right: Export Buttons -->
				<div class="flex items-center gap-2">
					<form id="exportForm" method="GET" action="{{ route('admin.students.export') }}" class="inline-block">
						<input type="hidden" name="tab" value="{{ request('tab','active') }}">
						<input type="hidden" name="q" value="{{ request('q') }}">
						<input type="hidden" name="semester" value="{{ request('semester') }}">
						<input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
						<input type="hidden" name="status" value="{{ request('status') }}">
						<input type="hidden" name="alumni" value="{{ request('alumni') }}">
						<input type="hidden" name="subject" value="{{ request('subject') }}">
						<input type="hidden" name="tab" value="{{ request('tab','active') }}">
						<input type="hidden" name="q" value="{{ request('q') }}">
						<input type="hidden" name="semester" value="{{ request('semester') }}">
						<input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
						<input type="hidden" name="status" value="{{ request('status') }}">
						<input type="hidden" name="alumni" value="{{ request('alumni') }}">
						<button type="submit" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 shadow-sm transition-colors inline-flex items-center gap-1">
							<i class="bi bi-file-earmark-spreadsheet"></i>CSV
						</button>
					</form>
					<button type="button" onclick="adminOpenPrintPreview('{{ route('students.print-list') }}', { title: 'Print Students' })" class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-colors inline-flex items-center gap-1 no-print">
						<i class="bi bi-printer"></i>Print
					</button>
				</div>
			</div>
		</div>

			<!-- Print Header (visible only when printing) -->
			<div style="display: none; print-color-adjust: exact; -webkit-print-color-adjust: exact;" class="print-header">
				<div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2d3748; padding-bottom: 15px;">
					<h1 style="margin: 0 0 5px 0; font-size: 18px; font-weight: bold; color: #2d3748;">IT-DMS STUDENTS LIST</h1>
					<p style="margin: 0 0 5px 0; font-size: 12px; color: #666;">Department of Computer Science & Engineering</p>
					<p style="margin: 0; font-size: 11px; color: #999;">Printed on: <span id="print-date">{{ now()->format('d-m-Y H:i') }}</span></p>
				</div>
			</div>

			<div class="overflow-x-hidden">
				<table class="min-w-full text-left divide-y divide-gray-200 dark:divide-slate-700">
					<thead class="bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200">
						<tr>
							<th class="px-4 py-3 text-center"><input type="checkbox" id="th_select_all" class="form-checkbox rounded text-red-600 focus:ring-red-500" /></th>
							<th class="px-4 py-3">User</th>
							<th class="px-4 py-3">Student IT</th>
							<th class="px-4 py-3">Email</th>
							<th class="px-4 py-3">Role</th>
							<th class="px-4 py-3">Semester</th>
							<th class="px-4 py-3">Academic Year</th>
							<th class="px-4 py-3">Alumni</th>
							<th class="px-4 py-3">Status</th>
							<th class="px-4 py-3 text-center">Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-slate-700">
@forelse($students ?? \App\Models\User::where('role','student')->limit(10)->get() as $student)
@php
$profilePhotoPath = $student->student->profile_photo_path ? asset('storage/'.$student->student->profile_photo_path) : '';
$dob = $student->student->date_of_birth ?? null;
$dobFormatted = $dob ? ($dob instanceof \Carbon\Carbon ? $dob->format('Y-m-d') : $dob) : '';
$studentJson = json_encode([
    'id' => $student->id,
    'name' => $student->name,
    'student_id' => $student->student->roll_no ?? $student->id,
    'email' => $student->email,
    'phone' => $student->student->phone ?? '',
    'department' => $student->student->department ?? '',
    'semester' => $student->student->semester ?? '',
    'registration_number' => $student->student->registration_number ?? '',
    'date_of_birth' => $dobFormatted,
    'date_of_birth_bs' => $student->student->date_of_birth_bs ?? '',
    'academic_year' => $student->student->academic_year ?? '',
    'academic_year_bs' => $student->student->academic_year_bs ?? '',
    'address' => $student->student->address ?? '',
    'bio' => $student->student->bio ?? '',
    'gender' => $student->student->gender ?? '',
    'status' => $student->student->status ?? '',
    'is_alumni' => $student->student->is_alumni ?? 0,
    'blood_group' => $student->student->blood_group ?? '',
    'emergency_contact' => $student->student->emergency_contact ?? '',
    'role' => $student->role ?? '',
    'profile_photo_path' => $profilePhotoPath
]);
@endphp
<tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors border-t border-gray-200 dark:border-slate-700">
<td class="px-4 py-4">
<input type="checkbox" class="student-checkbox rounded text-red-600 focus:ring-red-500" data-id="{{ $student->id }}" />
</td>
						<td class="px-4 py-4 flex items-center gap-3">
							<div class="w-9 h-9 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
								@if($student->student && $student->student->profile_photo_path)
									<img src="{{ asset('storage/'.$student->student->profile_photo_path) }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-person-fill text-gray-400\'></i>';">
								@elseif($student->profile_photo_path)
									<img src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-person-fill text-gray-400\'></i>';">
								@else
									<i class="bi bi-person-fill text-gray-400"></i>
								@endif
							</div>
							<div>
								<div class="font-medium text-gray-900">{{ $student->name }}</div>
								<div class="text-xs text-gray-500">{{ $student->email }}</div>
							</div>
						</td>
						<td class="px-4 py-4">{{ $student->student->roll_no ?? $student->id }}</td>
						<td class="px-4 py-4">{{ $student->email }}</td>
						<td class="px-4 py-4"><span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ ucfirst($student->role) }}</span></td>
						<td class="px-4 py-4">
							@if($student->student->is_alumni)
								<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-sm font-medium bg-green-100 text-green-700">
									<i class="bi bi-mortarboard text-sm"></i>
									Graduate
								</span>
							@else
								{{ $student->student->semester ?? '--' }}
							@endif
						</td>
						<td class="px-4 py-4">
							@if($student->student->academic_year || $student->student->academic_year_bs)
								<span class="text-sm">{{ $student->student->academic_year ?? '--' }}AD</span>
								@if($student->student->academic_year_bs)
									<span class="text-xs text-gray-500">/{{ $student->student->academic_year_bs }}BS</span>
								@endif
							@else
								--
							@endif
						</td>
						<td class="px-4 py-4">
							@if(request('tab') === 'alumni' || request('alumni') == '1')
								<button type="button" class="inline-flex items-center gap-2 px-2 py-1 rounded text-sm bg-purple-100 text-purple-700 show-alumni-date" data-alumni_from="{{ $student->student->alumni_from ?? '' }}"> 
									<i class="bi bi-mortarboard"></i>
									<span>Alumni</span>
								</button>
							@else
								<label class="inline-flex items-center cursor-pointer">
									<input type="checkbox" class="alumni-toggle sr-only" data-id="{{ $student->id }}" {{ ($student->student->is_alumni ?? 0) ? 'checked' : '' }} aria-label="Toggle alumni" />
									<div class="w-8 h-4 rounded-full relative" style="background-color: {{ ($student->student->is_alumni ?? 0) ? '#16a34a' : '#e5e7eb' }};">
										<span class="absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform" style="{{ ($student->student->is_alumni ?? 0) ? 'transform: translateX(16px);' : '' }}"></span>
									</div>
								</label>
							@endif
						</td>
						<td class="px-4 py-4">
							<label class="inline-flex items-center cursor-pointer">
								<input type="checkbox" class="status-toggle sr-only" data-id="{{ $student->id }}" {{ ($student->student->status ?? '') === 'active' ? 'checked' : '' }} aria-label="Toggle active" />
								<div class="w-8 h-4 rounded-full relative transition-colors" data-checked="{{ ($student->student->status ?? '') === 'active' ? '1' : '0' }}" style="background-color: {{ ($student->student->status ?? '') === 'active' ? '#16a34a' : '#ef4444' }};">
									<span class="dot absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform" style="{{ ($student->student->status ?? '') === 'active' ? 'transform: translateX(16px);' : '' }}"></span>
								</div>
							</label>
						</td>

<td class="px-4 py-4 text-center">
    <div class="flex gap-1 justify-center">
        <button type="button" onclick="viewStudent({{ $studentJson }})" class="action-btn action-btn-view text-blue-600 dark:text-blue-400" title="View">
            <i class="bi bi-eye"></i>
        </button>
        <button type="button" onclick="editStudent({{ $studentJson }})" class="action-btn action-btn-edit text-yellow-600 dark:text-yellow-400" title="Edit">
            <i class="bi bi-pencil"></i>
        </button>
        <button type="button" onclick="deleteStudent({{ $student->id }})" class="action-btn action-btn-delete text-red-600 dark:text-red-400" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</td>
					</tr>
					@empty
					<tr>
						<td colspan="10" class="px-4 py-6 text-center text-gray-500">No students found.</td>
					</tr>
					@endforelse
				</tbody>
            </table>
		</div>
        
			<!-- Enhanced Pagination -->
			<div class="px-4 py-2 border-t border-gray-100 dark:border-slate-700">
				@include('admin.components.admin-pagination', ['paginator' => $students])
			</div>
	</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="fixed inset-0 z-50 hidden">
	<div class="fixed inset-0 bg-black/50 dark:bg-black/70" onclick="closeAddStudentModal()"></div>
	<div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl mx-auto mt-20 overflow-auto max-h-[90vh] border border-gray-200 dark:border-slate-700">
		<div class="flex items-center justify-between p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white">
			<div>
				<h3 class="text-lg font-semibold">Add Student</h3>
				<p class="text-sm text-red-100">Create a new student account and profile</p>
			</div>
			<button onclick="closeAddStudentModal()" class="text-red-100 hover:text-white p-1 rounded-lg hover:bg-red-700 transition text-xl">
				<i class="bi bi-x-lg"></i>
			</button>
		</div>
		<form id="addStudentForm" action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
			@csrf
			<input type="hidden" name="role" value="student">
			@if ($errors->any())
				<div class="mb-4 p-4 bg-red-100 border border-red-400 text-blue-700 rounded">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="col-span-1 flex flex-col items-center">
					<div class="w-36 h-36 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
						<img id="profile_preview" src="" alt="Avatar preview" class="w-full h-full object-cover hidden">
						<div id="profile_placeholder" class="text-gray-400"><i class="bi bi-person-fill text-4xl"></i></div>
					</div>
					<label for="profile_photo_input" class="mt-3 inline-flex items-center px-3 py-1.5 bg-white border rounded text-sm cursor-pointer hover:bg-gray-50">
						<i class="bi bi-upload mr-2"></i>Choose photo
					</label>
					<input id="profile_photo_input" type="file" name="profile_photo" accept="image/*" class="sr-only" onchange="previewAddStudentPhoto()" />
					<p class="mt-3 text-xs text-gray-500 text-center">Recommended 400×400px. Max 4MB.</p>
				</div>
				<div class="col-span-1 lg:col-span-2">
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<!-- Row 1: Full name | Email -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Full name <span class="text-red-500 text-lg font-bold">*</span></label>
							<input name="name" required value="{{ old('name') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="e.g. John Doe" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500 text-lg font-bold">*</span></label>
							<input type="email" name="email" required value="{{ old('email') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="name@example.com" />
						</div>

						<!-- Row 2: Phone | Department -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500 text-lg font-bold">*</span></label>
							<input type="tel" name="phone" required value="{{ old('phone') }}" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Phone number" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Department <span class="text-red-500 text-lg font-bold">*</span></label>
							<input name="department" required value="{{ old('department') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Department or course" />
						</div>

<!-- Row 3: Semester | Registration Number -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Semester <span class="text-red-500 text-lg font-bold">*</span></label>
							<select name="semester" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
								<option value="">Select</option>
								@for($s=1;$s<=6;$s++)
									<option value="{{ $s }}" {{ old('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
								@endfor
							</select>
						</div>
						<div class="flex flex-col">
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Registration Number</label>
							<input name="registration_number" value="{{ old('registration_number') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Registration Number" />
						</div>

<!-- Row 4: Student IT | Batch Year -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Student IT <span class="text-red-500 text-lg font-bold">*</span></label>
							<input name="student_id" required value="{{ old('student_id') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Roll or ID" />
						</div>
                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
                                <option value="">Prefer not to say</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500 text-lg font-bold">*</span></label>
                            <div class="flex gap-2">
                                <select name="academic_year" id="addAcademicYear" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
                                    <option value="">Select AD</option>
                                    @php $current = date('Y'); @endphp
                                    @for($y=$current; $y >= $current - 10; $y--)
                                        <option value="{{ $y }}" {{ old('academic_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                <input type="text" name="academic_year_bs" id="addAcademicYearBs" placeholder="BS" value="{{ old('academic_year_bs') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
                            </div>
                        </div>

						<!-- Row 5: Date of birth (AD) | Date of birth (BS) -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Date of birth (AD) <span class="text-red-500 text-lg font-bold">*</span></label>
							<input type="date" name="date_of_birth" id="addStudentDateAd" required value="{{ old('date_of_birth') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Date of birth (BS)</label>
							<input type="text" name="date_of_birth_bs" id="addStudentDateBs" placeholder="YYYY-MM-DD" value="{{ old('date_of_birth_bs') }}" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
					</div>
					
					<!-- Address and Bio -->
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Address</label>
						<textarea name="address" rows="3" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Street, City, Postal code">{{ old('address') }}</textarea>
					</div>
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Bio <span class="text-red-500 text-lg font-bold">*</span></label>
						<textarea name="bio" required rows="4" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Short bio or notes">{{ old('bio') }}</textarea>
					</div>
				</div>
			</div>
			<div class="mt-6 flex items-center justify-end gap-3">
				<button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Cancel</button>
				<button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm">Add Student</button>
			</div>
		</form>
	</div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="fixed inset-0 z-50 hidden">
	<div class="fixed inset-0 bg-black/50 dark:bg-black/70" onclick="closeEditStudentModal()"></div>
	<div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl mx-auto mt-20 overflow-auto max-h-[90vh] border border-gray-200 dark:border-slate-700">
		<div class="flex items-center justify-between p-4 border-b-2 border-red-600 sticky top-0 bg-red-600 text-white">
			<div>
				<h3 class="text-lg font-semibold">Edit Student</h3>
				<p class="text-sm text-red-100">Update student information</p>
			</div>
			<button onclick="closeEditStudentModal()" class="text-red-100 hover:text-white p-1 rounded-lg hover:bg-red-700 transition text-xl">
				<i class="bi bi-x-lg"></i>
			</button>
		</div>
		<form id="editStudentForm" action="" method="POST" enctype="multipart/form-data" class="p-6">
			@csrf
			@method('PUT')
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="col-span-1 flex flex-col items-center">
					<div class="w-36 h-36 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
						<img id="edit_profile_preview" src="" alt="Profile" class="w-full h-full object-cover hidden">
						<div id="edit_profile_placeholder" class="text-gray-400"><i class="bi bi-person-fill text-4xl"></i></div>
					</div>
					<label for="edit_profile_photo_input" class="mt-3 inline-flex items-center px-3 py-1.5 bg-white border rounded text-sm cursor-pointer hover:bg-gray-50">
						<i class="bi bi-upload mr-2"></i>Choose photo
					</label>
					<input id="edit_profile_photo_input" type="file" name="profile_photo" accept="image/*" class="sr-only" />
					<p class="mt-3 text-xs text-gray-500 text-center">Recommended 400×400px. Max 4MB.</p>
				</div>
				<div class="col-span-1 lg:col-span-2">
					<input type="hidden" id="edit_student_id" name="student_id">
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<!-- Row 1: Full name | Email -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Full name <span class="text-red-500 text-lg font-bold">*</span></label>
							<input name="name" id="edit_name" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500 text-lg font-bold">*</span></label>
							<input type="email" name="email" id="edit_email" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>

						<!-- Row 2: Phone | Student IT -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500 text-lg font-bold">*</span></label>
							<input type="tel" name="phone" id="edit_phone" required maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Student IT <span class="text-red-500 text-lg font-bold">*</span></label>
							<input name="student_id" id="edit_student_it" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" placeholder="Roll or ID" />
						</div>

						<!-- Row 3: Department | Status -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Department <span class="text-red-500 text-lg font-bold">*</span></label>
							<input name="department" id="edit_department" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
							<select name="status" id="edit_status" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
								<option value="active">Active</option>
								<option value="pending">Pending</option>
								<option value="inactive">Inactive</option>
							</select>
						</div>

						<!-- Row 4: Semester | Gender -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Semester <span class="text-red-500 text-lg font-bold">*</span></label>
							<select name="semester" id="edit_semester" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
								<option value="">Select</option>
								@for($s=1;$s<=6;$s++)
									<option value="{{ $s }}">Semester {{ $s }}</option>
								@endfor
							</select>
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
							<select name="gender" id="edit_gender" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
								<option value="">Prefer not to say</option>
								<option value="male">Male</option>
								<option value="female">Female</option>
								<option value="other">Other</option>
							</select>
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500 text-lg font-bold">*</span></label>
							<div class="flex gap-2">
								<select name="academic_year" id="editAcademicYear" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors">
									<option value="">Select AD</option>
									@php $current = date('Y'); @endphp
									@for($y=$current; $y >= $current - 10; $y--)
										<option value="{{ $y }}">{{ $y }}</option>
									@endfor
								</select>
								<input type="text" name="academic_year_bs" id="editAcademicYearBs" placeholder="BS" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
							</div>
						</div>

						<!-- Row 5: Date of birth (AD) | Date of birth (BS) -->
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Date of birth (AD) <span class="text-red-500 text-lg font-bold">*</span></label>
							<input type="date" name="date_of_birth" id="editStudentDateAd" required class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
						<div class="flex flex-col">
							<label class="block text-xs font-medium text-gray-700 mb-1">Date of birth (BS)</label>
							<input type="text" name="date_of_birth_bs" id="editStudentDateBs" placeholder="YYYY-MM-DD" class="flex-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors" />
						</div>
					</div>
					
					<!-- Address and Bio -->
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Address <span class="text-red-500 text-lg font-bold">*</span></label>
						<textarea name="address" id="edit_address" required rows="3" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors"></textarea>
					</div>
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Bio</label>
						<textarea name="bio" id="edit_bio" rows="4" class="mt-1 block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-slate-700 dark:text-white transition-colors"></textarea>
					</div>
				</div>
			</div>
			<div class="mt-6 flex items-center justify-end gap-3">
				<button type="button" onclick="closeEditStudentModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">Cancel</button>
				<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 transition shadow-sm">Save changes</button>
			</div>
		</form>
	</div>
</div>

<script>
// Modal functions
function openAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Photo preview - Add student
function previewAddStudentPhoto() {
	const file = document.getElementById('profile_photo_input').files[0];
	if (file) {
		const reader = new FileReader();
		reader.onload = function(e) {
			const img = document.getElementById('profile_preview');
			const placeholder = document.getElementById('profile_placeholder');
			if (img) { img.src = e.target.result; img.classList.remove('hidden'); }
			if (placeholder) { placeholder.classList.add('hidden'); }
		};
		reader.readAsDataURL(file);
	}
}

// Photo preview - Edit student
document.getElementById('edit_profile_photo_input')?.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('edit_profile_preview');
            const placeholder = document.getElementById('edit_profile_placeholder');
            if (img) { img.src = e.target.result; img.classList.remove('hidden'); }
            if (placeholder) { placeholder.classList.add('hidden'); }
        };
        reader.readAsDataURL(file);
    }
});

function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openEditStudentModal() {
    document.getElementById('editStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// View student function
function viewStudent(student) {
    currentViewingStudentId = student.id;
    document.getElementById('view_name').textContent = student.name || '—';
    document.getElementById('view_email').textContent = student.email || '—';
    document.getElementById('view_phone').textContent = student.phone || '—';
    
    // Show Graduate if alumni, otherwise show semester
    if (student.is_alumni) {
        document.getElementById('view_semester').innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-sm font-medium bg-green-100 text-green-700"><i class="bi bi-mortarboard text-sm"></i> Graduate</span>';
    } else {
        document.getElementById('view_semester').textContent = student.semester || '—';
    }
    
    document.getElementById('view_department').textContent = student.department || '—';
    document.getElementById('view_roll_no').textContent = student.student_id || '—';
    document.getElementById('view_dob').textContent = student.date_of_birth || '—';
    document.getElementById('view_dob_bs').textContent = student.date_of_birth_bs || '—';
    document.getElementById('view_batch_year').textContent = (student.academic_year ? student.academic_year + 'AD' : '—') + (student.academic_year_bs ? '/' + student.academic_year_bs + 'BS' : '');
    document.getElementById('view_address').textContent = student.address || '—';
    document.getElementById('view_gender').textContent = student.gender ? (student.gender.charAt(0).toUpperCase() + student.gender.slice(1)) : '—';
    document.getElementById('view_blood_group').textContent = student.blood_group || '—';
    document.getElementById('view_status').textContent = student.status ? (student.status.charAt(0).toUpperCase() + student.status.slice(1)) : '—';
    document.getElementById('view_role').textContent = student.role ? (student.role.charAt(0).toUpperCase() + student.role.slice(1)) : '—';
    document.getElementById('view_emergency_contact').textContent = student.emergency_contact || '—';
    document.getElementById('view_bio').textContent = student.bio || '—';
    
    // Handle photo
    const viewAvatarImg = document.getElementById('viewStudentAvatarImg');
    const viewInitial = document.getElementById('viewStudentInitial');
    if (student.profile_photo_path) {
        viewAvatarImg.src = student.profile_photo_path;
        viewAvatarImg.style.display = 'block';
        viewInitial.style.display = 'none';
    } else {
        viewAvatarImg.style.display = 'none';
        viewInitial.style.display = 'flex';
    }
    
    document.getElementById('viewStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeViewStudentModal() {
    document.getElementById('viewStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Edit student function
function editStudent(student) {
    document.getElementById('edit_name').value = student.name || '';
    document.getElementById('edit_email').value = student.email || '';
    document.getElementById('edit_phone').value = student.phone || '';
    document.getElementById('edit_department').value = student.department || '';
    document.getElementById('edit_semester').value = student.semester || '';
    document.getElementById('edit_student_it').value = student.student_id || '';
    document.getElementById('editStudentDateAd').value = student.date_of_birth || '';
    document.getElementById('editStudentDateBs').value = student.date_of_birth_bs || '';
    document.getElementById('editAcademicYear').value = student.academic_year || '';
    document.getElementById('editAcademicYearBs').value = student.academic_year_bs || '';
    document.getElementById('edit_address').value = student.address || '';
    document.getElementById('edit_bio').value = student.bio || '';
    document.getElementById('edit_gender').value = student.gender || '';
    document.getElementById('edit_status').value = student.status || 'active';
    document.getElementById('edit_student_id').value = student.id;
    
    // Handle profile photo preview
    const editProfilePreview = document.getElementById('edit_profile_preview');
    const editProfilePlaceholder = document.getElementById('edit_profile_placeholder');
    if (student.profile_photo_path) {
        editProfilePreview.src = student.profile_photo_path;
        editProfilePreview.classList.remove('hidden');
        if (editProfilePlaceholder) {
            editProfilePlaceholder.classList.add('hidden');
        }
    } else {
        editProfilePreview.classList.add('hidden');
        if (editProfilePlaceholder) {
            editProfilePlaceholder.classList.remove('hidden');
        }
    }
    
    document.getElementById('editStudentForm').action = '/admin/students/' + student.id;
    openEditStudentModal();
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddStudentModal();
        closeEditStudentModal();
        closeViewStudentModal();
    }
});

// Close modals on background click
document.getElementById('addStudentModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAddStudentModal();
});

document.getElementById('editStudentModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditStudentModal();
});

// Filter functionality
document.getElementById('applyFilters')?.addEventListener('click', function() {
    const combined = document.getElementById('filter_combined').value;
    document.getElementById('filter_status').value = combined === 'alumni' ? '' : combined;
    document.getElementById('filter_alumni').value = combined === 'alumni' ? '1' : '0';
    document.getElementById('studentsFilterForm').submit();
});

// Date conversion functions — align with backend global +1 day adjustment
async function convertAdToBs(adDate) {
	if (!adDate) return '';
	try {
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
		const res = await fetch('/admin/convert/ad-to-bs', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken
			},
			body: JSON.stringify({ date: adDate })
		});
		if (!res.ok) return '';
		const json = await res.json();
		return json.bs || '';
	} catch (e) {
		return '';
	}
}

async function convertBsToAd(bsDate) {
	if (!bsDate) return '';
	try {
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
		const res = await fetch('/admin/convert/bs-to-ad', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken
			},
			body: JSON.stringify({ date: bsDate })
		});
		if (!res.ok) return '';
		const json = await res.json();
		return json.ad || '';
	} catch (e) {
		return '';
	}
}

// Academic year AD to BS conversion (just year)
function convertAdYearToBsYear(adYear) {
    if (!adYear) return '';
    // Convert AD year to BS year (approximate: add 56-57 years)
    const bsYear = parseInt(adYear) + 56;
    return bsYear.toString();
}

// Setup academic year auto-calculation
document.addEventListener('DOMContentLoaded', function() {
    // Add Academic Year change listener
    const addAcademicYear = document.getElementById('addAcademicYear');
    const addAcademicYearBs = document.getElementById('addAcademicYearBs');
    if(addAcademicYear && addAcademicYearBs) {
        addAcademicYear.addEventListener('change', function() {
            if(this.value) {
                addAcademicYearBs.value = convertAdYearToBsYear(this.value);
            } else {
                addAcademicYearBs.value = '';
            }
        });
    }
    
    // Edit Academic Year change listener
    const editAcademicYear = document.getElementById('editAcademicYear');
    const editAcademicYearBs = document.getElementById('editAcademicYearBs');
    if(editAcademicYear && editAcademicYearBs) {
        editAcademicYear.addEventListener('change', function() {
            if(this.value) {
                editAcademicYearBs.value = convertAdYearToBsYear(this.value);
            } else {
                editAcademicYearBs.value = '';
            }
        });
    }
    
    // Setup date conversion for Add Student Modal
    const addStudentDateAd = document.getElementById('addStudentDateAd');
    const addStudentDateBs = document.getElementById('addStudentDateBs');
    
    if(addStudentDateAd && addStudentDateBs) {
		// Real-time calculation on input event (as user types)
		addStudentDateAd.addEventListener('input', function() {
			if (this.value && this.value.length === 10) {
				convertAdToBs(this.value).then(v => { addStudentDateBs.value = v || ''; });
			}
		});
		addStudentDateBs.addEventListener('input', function() {
			if (this.value && this.value.length === 10) {
				convertBsToAd(this.value).then(v => { addStudentDateAd.value = v || ''; });
			}
		});

		// Also listen for change event as fallback (for date picker)
		addStudentDateAd.addEventListener('change', function() {
			if (this.value) {
				convertAdToBs(this.value).then(v => { addStudentDateBs.value = v || ''; });
			}
		});
		addStudentDateBs.addEventListener('change', function() {
			if (this.value) {
				convertBsToAd(this.value).then(v => { addStudentDateAd.value = v || ''; });
			}
		});
    }
    
    // Setup date conversion for Edit Student Modal
    const editStudentDateAd = document.getElementById('editStudentDateAd');
    const editStudentDateBs = document.getElementById('editStudentDateBs');
    
    if(editStudentDateAd && editStudentDateBs) {
		// Real-time calculation on input event
		editStudentDateAd.addEventListener('input', function() {
			if (this.value && this.value.length === 10) {
				convertAdToBs(this.value).then(v => { editStudentDateBs.value = v || ''; });
			}
		});
		editStudentDateBs.addEventListener('input', function() {
			if (this.value && this.value.length === 10) {
				convertBsToAd(this.value).then(v => { editStudentDateAd.value = v || ''; });
			}
		});

		// Also listen for change event as fallback (for date picker)
		editStudentDateAd.addEventListener('change', function() {
			if (this.value) {
				convertAdToBs(this.value).then(v => { editStudentDateBs.value = v || ''; });
			}
		});
		editStudentDateBs.addEventListener('change', function() {
			if (this.value) {
				convertBsToAd(this.value).then(v => { editStudentDateAd.value = v || ''; });
			}
		});
    }
});

document.getElementById('filter_combined')?.addEventListener('change', function() {
    document.getElementById('applyFilters').click();
});

// Flash messages are handled globally in admin/layouts/app.blade.php

// Add Student Form submits normally

// Edit Student Form with confirmation and regular submission

// (Removed duplicate immediate-submit status-toggle handler)

// Alumni toggle handlers using form submission - REMOVED, using confirmation dialog below instead


// Tab switching and persistent filters
(function(){
	const tabInput = document.getElementById('filter_tab');
	const currentTab = (tabInput && tabInput.value) ? tabInput.value : (new URLSearchParams(window.location.search).get('tab') || 'active');
	
	function updateBulkActionOptions(tab) {
		const moveAlumniOption = document.querySelector('option[value="move_alumni"]');
		const removeAlumniOption = document.querySelector('option[value="remove_alumni"]');
		const setSemesterOption = document.querySelector('option[value="set_semester"]');
		
		if (tab === 'alumni') {
			// On alumni tab: hide move_alumni and set_semester, show remove_alumni
			if (moveAlumniOption) moveAlumniOption.style.display = 'none';
			if (removeAlumniOption) removeAlumniOption.style.display = 'block';
			if (setSemesterOption) setSemesterOption.style.display = 'none';
		} else {
			// On active tab: show move_alumni and set_semester, hide remove_alumni
			if (moveAlumniOption) moveAlumniOption.style.display = 'block';
			if (removeAlumniOption) removeAlumniOption.style.display = 'none';
			if (setSemesterOption) setSemesterOption.style.display = 'block';
		}
	}
	
	// Apply initial settings based on current tab
	updateBulkActionOptions('active');
	
	// Remove tab switching functionality - alumni now have their own page

})();

// Select all / per-row selection
(function(){
	const checkAll = document.getElementById('th_select_all');
	const checkAllTop = document.getElementById('select_all');
	function setAll(checked){
		document.querySelectorAll('.student-checkbox').forEach(function(cb){ cb.checked = checked; });
	}
	checkAll?.addEventListener('change', function(){ setAll(this.checked); });
	checkAllTop?.addEventListener('change', function(){ setAll(this.checked); });
})();

// Bulk actions handler
// Old handler removed - using async handler below instead

// Show alumni date on click
document.querySelectorAll('.show-alumni-date').forEach(function(btn){
	btn.addEventListener('click', function(){
		const date = this.dataset.alumni_from || 'Unknown';
		if(window.showToast) showToast('Graduation: ' + date, 'info'); else alert('Graduation: ' + date);
	});
});

// =====================
// Global Loader Functions
// =====================
function showLoader(message) {
    const loader = document.getElementById('globalLoader');
    const loaderText = document.getElementById('loaderText');
    if (loader) {
        loaderText.textContent = message || 'Loading...';
        loader.classList.remove('hidden');
    }
}

function hideLoader() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.classList.add('hidden');
    }
}

// =====================
// Toast Notification Functions
// Uses global toast system from admin/layouts/app.blade.php
// =====================

// =====================
// Confirmation Modal Functions
// =====================
function showConfirm(options) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('confirmTitle');
        const message = document.getElementById('confirmMessage');
        const icon = document.getElementById('confirmIcon');
        const okBtn = document.getElementById('confirmOk');
        const cancelBtn = document.getElementById('confirmCancel');
        
        // Set content
        title.textContent = options.title || 'Confirm Action';
        message.textContent = options.message || 'Are you sure you want to proceed?';
        
        // Set icon based on type
        const iconColors = {
            danger: 'text-blue-500',
            warning: 'text-yellow-500',
            info: 'text-blue-500',
            success: 'text-green-500'
        };
        
        if (options.type === 'danger') {
            icon.innerHTML = '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
        } else if (options.type === 'delete') {
            icon.innerHTML = '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
        } else if (options.type === 'success') {
            icon.innerHTML = '<svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        } else {
            icon.innerHTML = '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }
        
        // Style OK button based on type
        okBtn.className = `px-4 py-2 rounded-lg font-medium transition-colors ${
            options.type === 'delete' ? 'bg-blue-600 hover:bg-blue-700 text-white' :
            options.type === 'danger' ? 'bg-orange-500 hover:bg-orange-600 text-white' :
            options.type === 'success' ? 'bg-green-600 hover:bg-green-700 text-white' :
            'bg-blue-600 hover:bg-blue-700 text-white'
        }`;
        
        okBtn.textContent = options.okText || 'Confirm';
        cancelBtn.textContent = options.cancelText || 'Cancel';
        
        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Handle confirm
        const handleConfirm = () => {
            hideConfirm();
            resolve(true);
        };
        
        // Handle cancel
        const handleCancel = () => {
            hideConfirm();
            resolve(false);
        };
        
        okBtn.onclick = handleConfirm;
        cancelBtn.onclick = handleCancel;
        
        // Close on backdrop click
        modal.onclick = (e) => {
            if (e.target === modal) {
                handleCancel();
            }
        };
        
        // Close on Escape key
        const handleEsc = (e) => {
            if (e.key === 'Escape') {
                handleCancel();
                document.removeEventListener('keydown', handleEsc);
            }
        };
        document.addEventListener('keydown', handleEsc);
    });
}

function hideConfirm() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// =====================
// Enhanced Form Handlers with Loaders and Confirmations
// =====================

// Add Student Form with loader
document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
    showLoader('Adding student...');
    sessionStorage.setItem('showNotification', 'student_added');
});

// Edit Student Form - use regular form submission with confirmation
let editFormConfirmed = false;
document.getElementById('editStudentForm')?.addEventListener('submit', function(e) {
    if (!editFormConfirmed) {
        e.preventDefault();
        e.stopPropagation();
        showConfirm({
            title: 'Update Student',
            message: 'Are you sure you want to save the changes?',
            type: 'info',
            okText: 'Save Changes'
        }).then((confirmed) => {
            if (confirmed) {
                editFormConfirmed = true;
                showLoader('Updating student...');
                e.target.submit();
            }
        });
        return false;
    }
    // If already confirmed, proceed with submission
    showLoader('Updating student...');
    return true;
});

// Delete with confirmation
function deleteStudent(studentId) {
	showConfirm({
		title: 'Delete Student',
		message: 'Are you sure you want to delete this student? This action cannot be undone.',
		type: 'delete',
		okText: 'Delete',
		cancelText: 'Cancel'
	}).then(function(confirmed) {
		if (!confirmed) return;
		showLoader('Deleting student...');
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = '/admin/students/' + studentId;
		form.style.display = 'none';
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
		if (csrfToken) {
			form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">';
		}
		// method spoofing for DELETE
		const m = document.createElement('input');
		m.type = 'hidden';
		m.name = '_method';
		m.value = 'DELETE';
		form.appendChild(m);
		document.body.appendChild(form);
		form.submit();
	});
}

// Toggle handlers with loader
document.querySelectorAll('.status-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const studentId = this.dataset.id;
        const newStatus = this.checked ? 'active' : 'inactive';
        
        const confirmed = await showConfirm({
            title: 'Change Status',
            message: `Are you sure you want to change this student's status to ${newStatus}?`,
            type: 'info',
            okText: 'Confirm'
        });
        
        if (!confirmed) {
            // Revert toggle
            this.checked = !this.checked;
            return;
        }
        
        showLoader('Updating status...');
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/students/' + studentId + '/toggle';
        form.style.display = 'none';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
        }
        
        document.body.appendChild(form);
        form.submit();
    });
});

// Initialize alumni toggle visual state on page load
function initializeAlumniToggles() {
    document.querySelectorAll('.alumni-toggle').forEach(el => {
        const container = el.nextElementSibling;
        const knob = container?.querySelector('span');
        if (knob && container) {
            if (el.checked) {
                knob.style.transform = 'translateX(16px)';
                container.style.backgroundColor = '#16a34a';
            } else {
                knob.style.transform = 'translateX(0)';
                container.style.backgroundColor = '#e5e7eb';
            }
        }
    });
}

// Call initialization on page load
document.addEventListener('DOMContentLoaded', initializeAlumniToggles);

// Alumni toggle with confirmation dialog
document.querySelectorAll('.alumni-toggle').forEach(el => {
    el.addEventListener('change', async function() {
        const studentId = this.dataset.id;
        const newStatus = this.checked ? 'alumni' : 'active';
        
        const confirmed = await showConfirm({
            title: this.checked ? 'Move to Alumni' : 'Remove from Alumni',
            message: this.checked 
                ? 'Are you sure you want to move this student to alumni?' 
                : 'Are you sure you want to remove this student from alumni?',
            type: 'warning',
            okText: 'Confirm'
        });
        
        if (!confirmed) {
            this.checked = !this.checked;
            return;
        }
        
        showLoader('Updating alumni status...');
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/students/' + studentId + '/toggle-alumni';
        form.style.display = 'none';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
        }
        
        document.body.appendChild(form);
        form.submit();
    });
});

// Enhanced Bulk Actions with Confirmation
document.getElementById('apply_bulk')?.addEventListener('click', async function(){
    const action = document.getElementById('bulk_action')?.value;
    if(!action) {
        showToast('Please select a bulk action', 'error');
        return;
    }
    
    const ids = Array.from(document.querySelectorAll('.student-checkbox:checked'))
        .map(el => el.dataset.id)
        .filter(Boolean);
    
    if(ids.length === 0) {
        showToast('No students selected', 'error');
        return;
    }
    
    if(action === 'export_csv'){
        showLoader('Preparing export...');
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route("admin.students.export") }}';
        ids.forEach(id => {
            const i = document.createElement('input');
            i.type='hidden'; i.name='ids[]'; i.value=id;
            form.appendChild(i);
        });
        const tabVal = document.getElementById('filter_tab')?.value || 'active';
        const t = document.createElement('input');
        t.type='hidden'; t.name='tab'; t.value = tabVal;
        form.appendChild(t);
        document.body.appendChild(form);
        form.submit();
        return;
    }
    
    let _bulk_semester = null;
    let _skip_alumni = false;
    let confirmMessage = '';
    let confirmTitle = '';
    
    if(action === 'move_alumni'){
        confirmTitle = 'Move to Alumni';
        confirmMessage = `Are you sure you want to move ${ids.length} student(s) to alumni?`;
    } else if(action === 'set_semester'){
        confirmTitle = 'Set Semester';
        confirmMessage = 'Enter the semester number in the next prompt.';
    } else if(action === 'set_status_active'){
        confirmTitle = 'Activate Students';
        confirmMessage = `Are you sure you want to activate ${ids.length} student(s)?`;
    } else if(action === 'set_status_inactive'){
        confirmTitle = 'Deactivate Students';
        confirmMessage = `Are you sure you want to deactivate ${ids.length} student(s)?`;
    } else if(action === 'remove_alumni'){
        confirmTitle = 'Remove from Alumni';
        confirmMessage = `Are you sure you want to remove ${ids.length} student(s) from alumni?`;
    }
    
    const confirmed = await showConfirm({
        title: confirmTitle,
        message: confirmMessage,
        type: action === 'move_alumni' || action === 'set_status_inactive' ? 'warning' : 'info'
    });
    
    if (!confirmed) return;
    
    if(action === 'set_semester'){
        const sem = prompt('Enter semester number (1-6):');
        if(!sem) return;
        if(!['1','2','3','4','5','6'].includes(sem.toString())){
            showToast('Invalid semester', 'error');
            return;
        }
        _bulk_semester = sem.toString();
    } else if(action === 'remove_alumni'){
        // Automatically set semester to 1 when removing from alumni
        _bulk_semester = '1';
    }
    
    showLoader('Processing bulk action...');
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/students/bulk';
    form.style.display = 'none';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if(csrfToken){
        const ci = document.createElement('input');
        ci.type='hidden'; ci.name='_token'; ci.value=csrfToken;
        form.appendChild(ci);
    }
    
    const a = document.createElement('input');
    a.type='hidden'; a.name='action'; a.value = action;
    form.appendChild(a);
    
    ids.forEach(id => {
        const i = document.createElement('input');
        i.type='hidden'; i.name='ids[]'; i.value=id;
        form.appendChild(i);
    });
    
    if(_bulk_semester){
        const s = document.createElement('input');
        s.type='hidden'; s.name='semester'; s.value=_bulk_semester;
        form.appendChild(s);
    }
    
    document.body.appendChild(form);
    form.submit();
});

// =====================
// Print and Download Functions
// =====================

// Store current student ID globally
let currentViewingStudentId = null;

function initiateStudentPrint() {
    if (!currentViewingStudentId) {
        showToast('Student ID not found', 'error');
        return;
    }
    const url = @json(route('students.print-detail', ['id' => '__ID__'])).replace('__ID__', currentViewingStudentId);
    adminOpenPrintPreview(url, {
        title: 'Print Student',
    });
}

// Filter with loader
document.getElementById('applyFilters')?.addEventListener('click', function() {
    showLoader('Applying filters...');
    document.getElementById('studentsFilterForm').submit();
});
</script>
@endsection
