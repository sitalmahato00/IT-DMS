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

		<button id="addStudentBtn" onclick="openAddStudentModal()" class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 font-medium">
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
						<td class="px-4 py-4">{{ $student->email }}</td>					<td class="px-4 py-4"><span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">{{ ucfirst($student->role) }}</span></td>						<td class="px-4 py-4">{{ $student->student->semester ?? '--' }}</td>
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
								<a href="{{ route('admin.students.show', $student->id) }}" class="view-student text-blue-600 hover:text-blue-800" title="View"><i class="bi bi-eye"></i></a>
								<a href="{{ route('admin.students.edit', $student->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit"><i class="bi bi-pencil"></i></a>



								<form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="sessionStorage.setItem('showNotification', 'student_deleted'); return confirm('Delete student?')" style="display:inline">
									@csrf
									@method('DELETE')
									<button type="submit" class="ml-2 text-red-600 hover:text-red-800" title="Delete"><i class="bi bi-trash"></i></button>
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
				{{-- pagination placeholder --}}
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
<div id="addStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4">
	<div class="bg-white rounded-lg shadow-xl w-full max-w-3xl overflow-auto max-h-[90vh]">
		<form id="addStudentForm" action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
			@csrf
				<input type="hidden" name="role" value="student">
			<div class="px-6 py-4 border-b flex items-center justify-between">
				<div>
					<h3 class="text-lg font-semibold">Add Student</h3>
					<p class="text-sm text-gray-500">Create a new student account and profile</p>
				</div>
				<button type="button" onclick="closeAddStudentModal()" class="text-gray-500 hover:text-gray-900">✕</button>
			</div>

			<div class="p-6">
				<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
					<!-- Left: Avatar preview -->
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

					<!-- Right: Form fields -->
					<div class="col-span-1 lg:col-span-2">
						<div>
							<label class="block text-xs font-medium text-gray-700">Role</label>
							<input type="text" value="Student" disabled class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm bg-gray-100" />
							<input type="hidden" name="role" value="student" />
						</div>
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
					</div>

						<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
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
						</div>

						<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
							<div>
						<label class="block text-xs font-medium text-gray-700">Date of birth <span class="text-red-500 text-base">*</span></label>
						<input type="text" name="date_of_birth_bs" required value="{{ old('date_of_birth_bs') }}" placeholder="YYYY-MM-DD (BS)" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm bs-date" />
					</div>
					<div>
						<label class="block text-xs font-medium text-gray-700">Batch Year <span class="text-red-500 text-base">*</span></label>
						<select name="batch_year" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
							<option value="">Select</option>
							@php $current = date('Y'); @endphp
							@for($y=$current; $y >= $current - 10; $y--)
								<option value="{{ $y }}" {{ old('batch_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
							@endfor
							@foreach(($batchYears ?? []) as $by)
								@if(!in_array($by, range(date('Y')-10, date('Y'))))
									<option value="{{ $by }}" {{ old('batch_year') == $by ? 'selected' : '' }}>{{ $by }}</option>
								@endif
							@endforeach
						</select>

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
					<button type="button" onclick="closeAddStudentModal()" class="px-3 py-1 border rounded text-sm">Cancel</button>
					<button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm">Add Student</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection

@section('scripts')
<script>
	function openAddStudentModal(){ document.getElementById('addStudentModal').classList.remove('hidden'); }
	function closeAddStudentModal(){ document.getElementById('addStudentModal').classList.add('hidden'); }
	// close modal on backdrop click
	document.addEventListener('click', function(e){
		const modal = document.getElementById('addStudentModal');
		if(modal && !modal.classList.contains('hidden') && e.target === modal) closeAddStudentModal();
	});

	// Handle form submission for Add Student - force page reload after success
	document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
		sessionStorage.setItem('showNotification', 'student_added');
	});

	// Check if we just came back from a form submission
	document.addEventListener('DOMContentLoaded', function() {
		const notification = sessionStorage.getItem('showNotification');
		if (notification) {
			sessionStorage.removeItem('showNotification');
			if (notification === 'student_added') {
				showToast('Student added successfully', 'success');
				// Reload the page to show the new student (on page 1)
				setTimeout(() => window.location.href = '{{ route("admin.students") }}?page=1', 1000);
			} else if (notification === 'student_deleted') {
				showToast('Student deleted successfully', 'success');
				setTimeout(() => location.reload(), 1000);
			}
		}
	});


	// base URL for AJAX toggles
	const _studentsBaseUrl = '{{ url('admin/students') }}';

	// row status toggles (checkbox) — initialize appearance and attach handlers
	document.querySelectorAll('.status-toggle').forEach(function(el){
		const container = el.nextElementSibling || el.parentElement.querySelector('[data-checked]');
		const knob = container ? container.querySelector('.dot') : null;
		// initialize appearance based on current checked state
		if(container){
			const active = el.checked;
			if(knob) knob.style.transform = active ? 'translateX(20px)' : 'translateX(0)';
			container.style.backgroundColor = active ? '#16a34a' : '#ef4444';
		}

		el.addEventListener('change', async function(){
			const id = this.dataset.id;
			const checked = this.checked;
			// optimistic UI
			if(knob){
				knob.style.transform = checked ? 'translateX(20px)' : 'translateX(0)';
				container.style.backgroundColor = checked ? '#16a34a' : '#ef4444';
			}
			try{
				const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
				const res = await fetch(_studentsBaseUrl + '/' + id + '/toggle', {
					method: 'POST',
					headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
					credentials: 'same-origin'
				});
				if(!res.ok) throw new Error('Network');
				const json = await res.json().catch(()=>null);
				showToast(json && json.message ? json.message : 'Status updated.', 'success');
				setTimeout(()=> location.reload(), 700);
			} catch(err){
				// revert UI
				this.checked = !checked;
				if(knob){
					knob.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';
					container.style.backgroundColor = this.checked ? '#16a34a' : '#ef4444';
				}
				showToast('Unable to update status.', 'error');
			}
		});
	});

	// alumni toggles — initialize and attach handlers
	document.querySelectorAll('.alumni-toggle').forEach(function(el){
		const container = el.nextElementSibling || el.parentElement.querySelector('div');
		const knob = container ? container.querySelector('span') : null;
		if(container){
			const active = el.checked;
			if(knob) knob.style.transform = active ? 'translateX(18px)' : 'translateX(0)';
			container.style.backgroundColor = active ? '#16a34a' : '#e5e7eb';
		}

		el.addEventListener('change', async function(){
			const id = this.dataset.id;
			const checked = this.checked;
			if(knob){
				knob.style.transform = checked ? 'translateX(18px)' : 'translateX(0)';
				container.style.backgroundColor = checked ? '#16a34a' : '#e5e7eb';
			}
			try{
				const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
				const res = await fetch(_studentsBaseUrl + '/' + id + '/toggle-alumni', {
					method: 'POST',
					headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
					credentials: 'same-origin'
				});
				if(!res.ok) throw new Error('Network');
				const json = await res.json().catch(()=>null);
				showToast(json && json.message ? json.message : 'Alumni updated.', 'success');
				setTimeout(()=> location.reload(), 700);
			} catch(err){
				this.checked = !checked;
				if(knob){
					knob.style.transform = this.checked ? 'translateX(18px)' : 'translateX(0)';
					container.style.backgroundColor = this.checked ? '#16a34a' : '#e5e7eb';
				}
				showToast('Unable to update alumni.', 'error');
			}
		});
	});

	// Toast helper
	function showToast(message, type){
		const container = document.getElementById('toast');
		if(!container) return alert(message);
		container.innerHTML = '';
		container.classList.remove('hidden');
		const bg = type === 'success' ? 'bg-green-600' : 'bg-red-600';
		const html = `<div class="max-w-md w-full text-white px-4 py-2 rounded shadow ${bg} border border-transparent">${message}</div>`;
		container.innerHTML = html;
		setTimeout(()=>{ container.classList.add('hidden'); }, 2000);
	}

	// Combined header filter mapping: map combined dropdown to hidden inputs and submit
	(function(){
		const combined = document.getElementById('filter_combined');
		const statusInput = document.getElementById('filter_status');
		const alumniInput = document.getElementById('filter_alumni');
		const applyBtn = document.getElementById('applyFilters');
		// initialize combined select based on current request
		(function initCombined(){
			const status = statusInput ? statusInput.value : '';
			const alumni = alumniInput ? alumniInput.value : '';
			if(alumni && alumni != '0') { combined.value = 'alumni'; }
			else if(status) { combined.value = status; }
			else { combined.value = 'all'; }
		})();

		combined && combined.addEventListener('change', function(){
			const v = this.value;
			if(v === 'alumni'){
				if(statusInput) statusInput.value = '';
				if(alumniInput) alumniInput.value = '1';
			} else if(v === 'all'){
				if(statusInput) statusInput.value = '';
				if(alumniInput) alumniInput.value = '';
			} else {
				if(statusInput) statusInput.value = v;
				if(alumniInput) alumniInput.value = '';
			}
			// auto-submit when combined filter is changed
			if(document.getElementById('studentsFilterForm')){
				document.getElementById('studentsFilterForm').submit();
			}
		});

		applyBtn && applyBtn.addEventListener('click', function(){
			// ensure combined mapping applied before submit
			const ev = new Event('change');
			combined && combined.dispatchEvent(ev);
			document.getElementById('studentsFilterForm').submit();
		});

		// auto-submit when semester select changes
		const semesterSelect = document.getElementById('filter_semester');
		semesterSelect && semesterSelect.addEventListener('change', function(){
			if(document.getElementById('studentsFilterForm')) document.getElementById('studentsFilterForm').submit();
		});

		// auto-submit when batch year changes
		const batchYearSelect = document.getElementById('filter_batch_year');
		batchYearSelect && batchYearSelect.addEventListener('change', function(){
			if(document.getElementById('studentsFilterForm')) document.getElementById('studentsFilterForm').submit();
		});

		// search input: submit on Enter or debounce on input
		function debounce(fn, wait){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); }; }
		const qInput = document.getElementById('filter_q');
		if(qInput){
			qInput.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); document.getElementById('studentsFilterForm').submit(); }});
			qInput.addEventListener('input', debounce(function(){ document.getElementById('studentsFilterForm').submit(); }, 600));
		}
	})();

	// AJAX modal loader for student details — loads show view and displays over current page
	document.addEventListener('click', async function(e){
		const a = e.target.closest && e.target.closest('.view-student');
		if(!a) return;
		e.preventDefault();
		try{
			const url = a.href;
			const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } });
			if(!res.ok) throw new Error('Network');
			const html = await res.text();
			const root = document.getElementById('ajaxModalRoot');
			if(!root) {
				console.warn('No ajax modal root found');
				return;
			}
			// Safely parse and append the returned HTML so script tags don't render as text
			const tpl = document.createElement('template');
			tpl.innerHTML = html.trim();
			root.appendChild(tpl.content);
			// Execute and remove any scripts in the injected content
			root.querySelectorAll('script').forEach(function(s){
				try{
					if(s.src){
						const sc = document.createElement('script'); sc.src = s.src; document.body.appendChild(sc);
					} else {
						(new Function(s.innerText))();
					}
				} catch(err){ console.error('eval script', err); }
				// remove script node so its text doesn't remain in DOM
				s.remove();
			});
			// show modal if it has class to show; otherwise nothing else required since show view outputs modal markup
		} catch(err){
			console.error(err);
		}
	});
</script>
@endsection

@section('ajax-modal')
<div id="ajaxModalRoot"></div>
@endsection




