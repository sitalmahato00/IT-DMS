@extends('admin.layouts.app')

@section('title', 'Students')

@section('content')
<div class="space-y-4">
	<div id="toast" class="fixed top-4 right-4 z-50 hidden"></div>
	
	<!-- Stats Grid - Row 1 -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
		<x-stats-card title="Total Students" :value="\App\Models\User::where('role','student')->count()" icon="bi-people" color="blue" />
		<x-stats-card title="Active Students" :value="\App\Models\User::where('role','student')->where('status','active')->count()" icon="bi-check-circle" color="green" />
		<x-stats-card title="Inactive Students" :value="\App\Models\User::where('role','student')->where('status','inactive')->count()" icon="bi-x-circle" color="orange" />
		<x-stats-card title="Alumni" :value="\App\Models\User::where('role','student')->where('is_alumni',1)->count()" icon="bi-mortarboard" color="purple" />
	</div>

	<!-- Header with Actions and Filters - Row 2 -->
	<div class="flex items-center justify-between gap-3 flex-wrap">
		<form id="studentsFilterForm" method="GET" action="{{ route('admin.students') }}" class="flex items-center gap-2">
			<input type="hidden" name="status" id="filter_status" value="{{ request('status') }}">
			<input type="hidden" name="alumni" id="filter_alumni" value="{{ request('alumni') }}">
			<input type="search" id="filter_q" name="q" placeholder="Search name or email" value="{{ request('q') }}" class="w-48 px-3 py-2 border rounded text-xs" />
			<select id="filter_combined" class="w-40 px-3 py-2 border rounded text-xs">
				<option value="all">All</option>
				<option value="active">Active</option>
				<option value="inactive">Inactive</option>
				<option value="pending">Pending</option>
				<option value="alumni">Alumni</option>
			</select>
			<select name="semester" class="w-40 px-3 py-2 border rounded text-xs" id="filter_semester">
				<option value="">Semester</option>
				@for($s=1;$s<=6;$s++)
					<option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Sem {{ $s }}</option>
				@endfor
			</select>

			<select name="batch_year" id="filter_batch_year" class="w-36 px-3 py-2 border rounded text-xs">
				<option value="">Batch Year</option>
				@foreach(($batchYears ?? []) as $by)
					<option value="{{ $by }}" {{ request('batch_year') == $by ? 'selected' : '' }}>{{ $by }}</option>
				@endforeach
			</select>
			<button type="button" id="applyFilters" class="px-3 py-2 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700">Filter</button>
			<a href="{{ route('admin.students') }}" class="px-3 py-2 border rounded text-xs font-medium hover:bg-gray-50">Reset</a>
		</form>

		<button onclick="openAddStudentModal()" class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 font-medium">
			<i class="bi bi-plus-lg"></i>
			<span>Add Student</span>
		</button>
	</div>

	<!-- Users List -->
	<x-card title="Users List" :actions="'<button class=\'px-2 py-1 text-xs border rounded\'>Export</button><button class=\'ml-2 px-2 py-1 text-xs border rounded\'>Filter</button>'">
		<div class="overflow-x-auto">
			<table class="w-full text-sm">
				<thead class="text-left text-xs text-gray-500">
					<tr>
						<th class="px-4 py-3">User</th>
						<th class="px-4 py-3">Id No</th>
						<th class="px-4 py-3">email</th>
						<th class="px-4 py-3">Role</th>
						<th class="px-4 py-3">Semester</th>
						<th class="px-4 py-3">Batch</th>
						<th class="px-4 py-3">Alumni</th>
						<th class="px-4 py-3">Status</th>
						<th class="px-4 py-3 text-center">Actions</th>
					</tr>
				</thead>
				<tbody>
					@forelse($students ?? \App\Models\User::where('role','student')->limit(10)->get() as $student)
					<tr class="border-t">
						<td class="px-4 py-4 flex items-center gap-3">
							<div class="w-9 h-9 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
								@if($student->profile_photo_path)
									<img src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="" class="w-full h-full object-cover">
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
						<td class="px-4 py-4">{{ $student->student->semester ?? '--' }}</td>
						<td class="px-4 py-4">{{ $student->student->batch_year ?? '--' }}</td>
						<td class="px-4 py-4">
							<label class="inline-flex items-center cursor-pointer">
								<input type="checkbox" class="alumni-toggle sr-only" data-id="{{ $student->id }}" {{ ($student->is_alumni || ($student->alumni ?? false)) ? 'checked' : '' }} aria-label="Toggle alumni" />
								<div class="w-8 h-4 rounded-full relative" style="background-color: {{ ($student->is_alumni || ($student->alumni ?? false)) ? '#16a34a' : '#e5e7eb' }};">
									<span class="absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform" style="{{ ($student->is_alumni || ($student->alumni ?? false)) ? 'transform: translateX(16px);' : '' }}"></span>
								</div>
							</label>
						</td>
						<td class="px-4 py-4">
							<label class="inline-flex items-center cursor-pointer">
								<input type="checkbox" class="status-toggle sr-only" data-id="{{ $student->id }}" {{ $student->status==='active' ? 'checked' : '' }} aria-label="Toggle active" />
								<div class="w-8 h-4 rounded-full relative transition-colors" data-checked="{{ $student->status==='active' ? '1' : '0' }}" style="background-color: {{ $student->status==='active' ? '#16a34a' : '#ef4444' }};">
									<span class="dot absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform" style="{{ $student->status==='active' ? 'transform: translateX(16px);' : '' }}"></span>
								</div>
							</label>
						</td>

						<td class="px-4 py-4 text-center">
							<div class="inline-flex items-center gap-2">
								@php
								$studentJson = json_encode([
									'id' => $student->id,
									'name' => $student->name,
									'student_id' => $student->student->roll_no ?? $student->id,
									'email' => $student->email,
									'phone' => $student->phone,
									'department' => $student->student->department ?? '',
									'semester' => $student->student->semester ?? '',
									'date_of_birth' => $student->student->date_of_birth ?? '',
									'batch_year' => $student->student->batch_year ?? '',
									'address' => $student->student->address ?? '',
									'bio' => $student->student->bio ?? ''
								]);
								@endphp
								<a href="{{ route('admin.students.show', $student->id) }}" class="text-blue-600 hover:text-blue-800" title="View"><i class="bi bi-eye"></i></a>
								<button onclick="editStudent({{ $studentJson }})" class="text-yellow-600 hover:text-yellow-800" title="Edit"><i class="bi bi-pencil"></i></button>
								<form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete student?')" style="display:inline">
									@csrf
									@method('DELETE')
									<button type="submit" class="text-red-600 hover:text-red-800" title="Delete"><i class="bi bi-trash"></i></button>
								</form>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="9" class="px-4 py-6 text-center text-gray-500">No students found.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="px-4 py-3 border-t text-xs text-gray-600 flex items-center justify-between">
			<div>
				Show
				<select class="inline-block mx-2 px-2 py-1 border rounded text-sm">
					<option>10</option>
					<option>25</option>
					<option>50</option>
				</select>
				entries
			</div>
			<div>
				@if(isset($students) && method_exists($students,'links'))
					{{ $students->links() }}
				@else
					<span class="text-gray-500">Showing 1 to 4 of 156 results</span>
				@endif
			</div>
		</div>
	</x-card>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="fixed inset-0 z-50 hidden">
	<div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeAddStudentModal()"></div>
	<div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl mx-auto mt-20 overflow-auto max-h-[90vh]">
		<div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white">
			<div>
				<h3 class="text-lg font-semibold">Add Student</h3>
				<p class="text-sm text-gray-500">Create a new student account and profile</p>
			</div>
			<button onclick="closeAddStudentModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
		</div>
		<form id="addStudentForm" action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
			@csrf
			<input type="hidden" name="role" value="student">
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="col-span-1 flex flex-col items-center">
					<div class="w-36 h-36 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
						<img id="profile_preview" src="" alt="Avatar preview" class="w-full h-full object-cover hidden">
						<div id="profile_placeholder" class="text-gray-400"><i class="bi bi-person-fill text-4xl"></i></div>
					</div>
					<label for="profile_photo_input" class="mt-3 inline-flex items-center px-3 py-1.5 bg-white border rounded text-sm cursor-pointer hover:bg-gray-50">
						<i class="bi bi-upload mr-2"></i>Choose photo
					</label>
					<input id="profile_photo_input" type="file" name="profile_photo" accept="image/*" class="sr-only" />
					<p class="mt-3 text-xs text-gray-500 text-center">Recommended 400×400px. Max 4MB.</p>
				</div>
				<div class="col-span-1 lg:col-span-2">
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label class="block text-xs font-medium text-gray-700">Full name <span class="text-red-500 text-base">*</span></label>
							<input name="name" required value="{{ old('name') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" placeholder="e.g. John Doe" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Student ID <span class="text-red-500 text-base">*</span></label>
							<input name="student_id" required value="{{ old('student_id') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" placeholder="Roll or ID" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Email <span class="text-red-500 text-base">*</span></label>
							<input type="email" name="email" required value="{{ old('email') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" placeholder="name@example.com" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Phone <span class="text-red-500 text-base">*</span></label>
							<input name="phone" required value="{{ old('phone') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" placeholder="Phone number" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Department <span class="text-red-500 text-base">*</span></label>
							<input name="department" required value="{{ old('department') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" placeholder="Department or course" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Semester <span class="text-red-500 text-base">*</span></label>
							<select name="semester" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
								<option value="">Select</option>
								@for($s=1;$s<=6;$s++)
									<option value="{{ $s }}" {{ old('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
								@endfor
							</select>
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Date of birth (AD) <span class="text-red-500 text-base">*</span></label>
							<input type="date" name="date_of_birth" required value="{{ old('date_of_birth') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Batch Year <span class="text-red-500 text-base">*</span></label>
							<select name="batch_year" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
								<option value="">Select</option>
								@php $current = date('Y'); @endphp
								@for($y=$current; $y >= $current - 10; $y--)
									<option value="{{ $y }}" {{ old('batch_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
								@endfor
							</select>
						</div>
					</div>
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Address</label>
						<textarea name="address" rows="2" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" placeholder="Street, City, Postal code">{{ old('address') }}</textarea>
					</div>
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Bio <span class="text-red-500 text-base">*</span></label>
						<textarea name="bio" required rows="3" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" placeholder="Short bio or notes">{{ old('bio') }}</textarea>
					</div>
				</div>
			</div>
			<div class="mt-6 flex items-center justify-end gap-3">
				<button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
				<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Add Student</button>
			</div>
		</form>
	</div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="fixed inset-0 z-50 hidden">
	<div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeEditStudentModal()"></div>
	<div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl mx-auto mt-20 overflow-auto max-h-[90vh]">
		<div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white">
			<div>
				<h3 class="text-lg font-semibold">Edit Student</h3>
				<p class="text-sm text-gray-500">Update student information</p>
			</div>
			<button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
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
						<div>
							<label class="block text-xs font-medium text-gray-700">Full name <span class="text-red-500 text-base">*</span></label>
							<input name="name" id="edit_name" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Email <span class="text-red-500 text-base">*</span></label>
							<input type="email" name="email" id="edit_email" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Phone <span class="text-red-500 text-base">*</span></label>
							<input name="phone" id="edit_phone" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Department <span class="text-red-500 text-base">*</span></label>
							<input name="department" id="edit_department" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Semester <span class="text-red-500 text-base">*</span></label>
							<select name="semester" id="edit_semester" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
								<option value="">Select</option>
								@for($s=1;$s<=6;$s++)
									<option value="{{ $s }}">Semester {{ $s }}</option>
								@endfor
							</select>
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Status</label>
							<select name="status" id="edit_status" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
								<option value="active">Active</option>
								<option value="pending">Pending</option>
								<option value="inactive">Inactive</option>
							</select>
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Date of birth (AD) <span class="text-red-500 text-base">*</span></label>
							<input type="date" name="date_of_birth" id="edit_date_of_birth" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
						</div>
						<div>
							<label class="block text-xs font-medium text-gray-700">Batch Year <span class="text-red-500 text-base">*</span></label>
							<select name="batch_year" id="edit_batch_year" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
								<option value="">Select</option>
								@php $current = date('Y'); @endphp
								@for($y=$current; $y >= $current - 10; $y--)
									<option value="{{ $y }}">{{ $y }}</option>
								@endfor
							</select>
						</div>
					</div>
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Address <span class="text-red-500 text-base">*</span></label>
						<textarea name="address" id="edit_address" required rows="2" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm"></textarea>
					</div>
					<div class="mt-4">
						<label class="block text-xs font-medium text-gray-700">Bio</label>
						<textarea name="bio" id="edit_bio" rows="3" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm"></textarea>
					</div>
				</div>
			</div>
			<div class="mt-6 flex items-center justify-end gap-3">
				<button type="button" onclick="closeEditStudentModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
				<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Save changes</button>
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

// Edit student function
function editStudent(student) {
    document.getElementById('edit_name').value = student.name || '';
    document.getElementById('edit_email').value = student.email || '';
    document.getElementById('edit_phone').value = student.phone || '';
    document.getElementById('edit_department').value = student.department || '';
    document.getElementById('edit_semester').value = student.semester || '';
    document.getElementById('edit_date_of_birth').value = student.date_of_birth || '';
    document.getElementById('edit_batch_year').value = student.batch_year || '';
    document.getElementById('edit_address').value = student.address || '';
    document.getElementById('edit_bio').value = student.bio || '';
    document.getElementById('edit_student_id').value = student.id;
    
    document.getElementById('editStudentForm').action = '/admin/students/' + student.id;
    openEditStudentModal();
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddStudentModal();
        closeEditStudentModal();
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

document.getElementById('filter_combined')?.addEventListener('change', function() {
    document.getElementById('applyFilters').click();
});

// Check for notifications
const notification = sessionStorage.getItem('showNotification');
if (notification) {
    sessionStorage.removeItem('showNotification');
    if (notification === 'student_added') {
        showToast('Student added successfully', 'success');
        setTimeout(() => window.location.href = '{{ route("admin.students") }}?page=1', 1000);
    } else if (notification === 'student_deleted') {
        showToast('Student deleted successfully', 'success');
        setTimeout(() => location.reload(), 1000);
    } else if (notification === 'student_updated') {
        showToast('Student updated successfully', 'success');
        setTimeout(() => location.reload(), 1000);
    }
}

// Form submission handlers
document.getElementById('addStudentForm')?.addEventListener('submit', function() {
    sessionStorage.setItem('showNotification', 'student_added');
});

document.getElementById('editStudentForm')?.addEventListener('submit', function() {
    sessionStorage.setItem('showNotification', 'student_updated');
});

// Status toggle handlers
document.querySelectorAll('.status-toggle').forEach(function(el) {
    el.addEventListener('change', function() {
        const studentId = this.dataset.id;
        const self = this;
        
        // Update visual immediately for feedback
        const container = this.nextElementSibling;
        const knob = container?.querySelector('.dot');
        const newChecked = this.checked;
        if (knob) {
            knob.style.transform = newChecked ? 'translateX(16px)' : 'translateX(0)';
            container.style.backgroundColor = newChecked ? '#16a34a' : '#ef4444';
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            showToast('Error: CSRF token missing', 'error');
            self.checked = !newChecked;
            return;
        }
        
        fetch('/admin/students/' + studentId + '/toggle', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        })
        .then(function(response) {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(function(data) {
            console.log('Response data:', data);
            if (data.success) {
                showToast('Status updated successfully', 'success');
            } else {
                showToast(data.message || 'Failed to update status', 'error');
                self.checked = !newChecked;
                if (knob) {
                    knob.style.transform = self.checked ? 'translateX(16px)' : 'translateX(0)';
                    container.style.backgroundColor = self.checked ? '#16a34a' : '#ef4444';
                }
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showToast('Error updating status', 'error');
            self.checked = !newChecked;
            if (knob) {
                knob.style.transform = self.checked ? 'translateX(16px)' : 'translateX(0)';
                container.style.backgroundColor = self.checked ? '#16a34a' : '#ef4444';
            }
        });
    });
});

// Alumni toggle handlers
document.querySelectorAll('.alumni-toggle').forEach(function(el) {
    el.addEventListener('change', function() {
        const studentId = this.dataset.id;
        const self = this;
        
        // Update visual
        const container = this.nextElementSibling;
        const knob = container?.querySelector('span');
        const newChecked = this.checked;
        if (knob) {
            knob.style.transform = newChecked ? 'translateX(16px)' : 'translateX(0)';
            container.style.backgroundColor = newChecked ? '#16a34a' : '#e5e7eb';
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            showToast('Error: CSRF token missing', 'error');
            self.checked = !newChecked;
            return;
        }
        
        fetch('/admin/students/' + studentId + '/toggle-alumni', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        })
        .then(function(response) {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(function(data) {
            console.log('Response data:', data);
            if (data.success) {
                showToast(newChecked ? 'Student marked as alumni' : 'Student removed from alumni', 'success');
            } else {
                showToast(data.message || 'Failed to update alumni status', 'error');
                self.checked = !newChecked;
                if (knob) {
                    knob.style.transform = self.checked ? 'translateX(16px)' : 'translateX(0)';
                    container.style.backgroundColor = self.checked ? '#16a34a' : '#e5e7eb';
                }
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showToast('Error updating alumni status', 'error');
            self.checked = !newChecked;
            if (knob) {
                knob.style.transform = self.checked ? 'translateX(16px)' : 'translateX(0)';
                container.style.backgroundColor = self.checked ? '#16a34a' : '#e5e7eb';
            }
        });
    });
});
</script>
@endsection

