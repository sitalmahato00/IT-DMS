@extends('admin.layouts.app')

@section('title', 'Teachers')

@section('content')
<div class="space-y-4">
    <!-- Stats Grid - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stats-card title="Total Teachers" :value="isset($teachers) ? $teachers->total() : 0" icon="bi bi-person-workspace" color="blue" />
        <x-stats-card title="Active" :value="isset($teachers) ? $teachers->where('status','active')->count() : 0" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="On Leave" :value="isset($teachers) ? $teachers->where('status','On Leave')->count() : 0" icon="bi bi-exclamation-circle" color="yellow" />
        <x-stats-card title="Retired" :value="isset($teachers) ? $teachers->where('status','Retired')->count() : 0" icon="bi bi-hourglass-end" color="purple" />
    </div>

    <!-- Filters & Actions - Row 2 -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex gap-2 items-center">
            <input id="teachersSearch" type="text" placeholder="Search name or email..." class="w-48 px-3 py-2 border rounded text-xs" />
            <select id="teachersStatusFilter" class="w-40 px-3 py-2 border rounded text-xs">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="On Leave">On Leave</option>
                <option value="Retired">Retired</option>
            </select>
            <select id="teachersCourseFilter" class="w-40 px-3 py-2 border rounded text-xs">
                <option value="">All Courses</option>
                <option value="Physics">Physics</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Chemistry">Chemistry</option>
            </select>
            <button type="button" onclick="resetTeachersFilter()" class="px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-xs font-medium" title="Reset Filters">
                <i class="bi bi-arrow-clockwise mr-1"></i>Reset
            </button>
        </div>

        <button onclick="openAddTeacherModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
            <i class="bi bi-plus-lg"></i>
            <span>Add Teacher</span>
        </button>
    </div>

    <x-card title="Teachers List">
        <div id="teachersTableContainer">
            <x-table :headers="['Name','Teacher ID','Email','Role','Course','Status','Actions']">
                @forelse($teachers ?? collect() as $teacher)
                <tr>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            @if(!empty($teacher->profile_photo_path))
                                <img src="{{ Storage::disk('public')->url($teacher->profile_photo_path) }}" alt="avatar" class="w-7 h-7 rounded-full">
                            @else
                                <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center text-red-600">T</div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $teacher->name }}</p>
                                <p class="text-xs text-gray-600">{{ $teacher->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2">{{ $teacher->teacher_code ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $teacher->email }}</td>
                    <td class="px-3 py-2"><span class="inline-block px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs font-medium">{{ ucfirst($teacher->role) }}</span></td>
                    <td class="px-3 py-2">{{ $teacher->department ?? '—' }}</td>
                    <td class="px-3 py-2"><span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">{{ $teacher->status ?? 'active' }}</span></td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex gap-1 justify-center">
                            <x-icon-button color="blue" onclick="openViewTeacherModal({{ $teacher->id }})">
                                <i class="bi bi-eye text-xs"></i>
                            </x-icon-button>
                            <x-icon-button color="yellow" onclick="openEditTeacherModal({{ $teacher->id }})">
                                <i class="bi bi-pencil text-xs"></i>
                            </x-icon-button>
                            <x-icon-button color="red" onclick="deleteTeacher({{ $teacher->id }})">
                                <i class="bi bi-trash text-xs"></i>
                            </x-icon-button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-3 py-2">No records found.</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                </tr>
                @endforelse
            </x-table>
            @if(isset($teachers) && $teachers->hasPages())
                <div class="mt-3">{{ $teachers->links() }}</div>
            @endif
        </div>
    </x-card>
</div>

<!-- Add Teacher Modal -->
<div id="addTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeAddTeacherModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-auto">
        <form id="addTeacherForm" method="POST" enctype="multipart/form-data" class="flex flex-col" action="{{ route('admin.teachers.store') }}">
            @csrf
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Add Teacher</h3>
                    <p class="text-sm text-gray-500">Create a new teacher account</p>
                </div>
                <button type="button" onclick="closeAddTeacherModal()" class="text-gray-500 hover:text-gray-900">✕</button>
            </div>
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
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                <input type="text" value="Teacher" disabled class="w-full px-3 py-2 border rounded-md text-sm bg-gray-100" />
                                <input type="hidden" name="role" value="teacher" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Full name <span class="text-red-500 text-base">*</span></label>
                                <input name="name" required placeholder="e.g. John Doe" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Teacher ID <span class="text-red-500 text-base">*</span></label>
                                <input name="teacher_id" required placeholder="Teacher ID" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500 text-base">*</span></label>
                                <input name="email" type="email" required placeholder="name@example.com" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500 text-base">*</span></label>
                                <input name="phone" required placeholder="Phone number" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Department <span class="text-red-500 text-base">*</span></label>
                                <input name="department" required placeholder="Department or course" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500 text-base">*</span></label>
                                <select name="status" required class="w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                    <option value="Retired">Retired</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bio <span class="text-red-500 text-base">*</span></label>
                            <textarea name="bio" required rows="3" placeholder="Short bio or notes" class="w-full px-3 py-2 border rounded-md text-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddTeacherModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Add Teacher</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Teacher Modal -->
<div id="editTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeEditTeacherModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-auto">
        <form id="editTeacherForm" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Edit Teacher</h3>
                    <p class="text-sm text-gray-500">Update teacher information</p>
                </div>
                <button type="button" onclick="closeEditTeacherModal()" class="text-gray-500 hover:text-gray-900">✕</button>
            </div>
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
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Role</label>
                                <input type="text" value="Teacher" disabled class="mt-1 block w-full px-3 py-2 border rounded-md text-sm bg-gray-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Full name</label>
                                <input id="edit_name" name="name" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Email</label>
                                <input id="edit_email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Phone</label>
                                <input id="edit_phone" name="phone" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Department</label>
                                <input id="edit_department" name="department" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Teacher ID</label>
                                <input id="edit_teacher_id" name="teacher_id" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Status</label>
                                <select id="edit_status" name="status" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                    <option value="Retired">Retired</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700">Bio</label>
                            <textarea id="edit_bio" name="bio" rows="3" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <button type="button" onclick="deleteTeacher(document.getElementById('editTeacherId').value)" class="px-3 py-2 text-sm bg-red-50 text-red-700 border border-red-200 rounded hover:bg-red-100">Delete</button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditTeacherModal()" class="px-3 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Teacher Modal -->
<div id="viewTeacherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeViewTeacherModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-auto">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Teacher Details</h3>
                <p class="text-sm text-gray-500">View teacher information</p>
            </div>
            <button type="button" onclick="closeViewTeacherModal()" class="text-gray-500 hover:text-gray-900">✕</button>
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
                    <div class="grid grid-cols-2 gap-4 mb-4">
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
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-medium text-gray-700">Bio</label>
                        <p id="viewTeacherBio" class="text-sm font-semibold text-gray-900 mt-1">—</p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeViewTeacherModal()" class="px-3 py-2 border rounded text-sm">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openAddTeacherModal() {
    document.getElementById('addTeacherModal').classList.remove('hidden');
}

function closeAddTeacherModal() {
    document.getElementById('addTeacherModal').classList.add('hidden');
}

function openEditTeacherModal(id) {
    fetch(`/admin/teachers/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
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
            document.getElementById('edit_status').value = data.status || 'active';
            document.getElementById('edit_bio').value = data.bio || '';
            if (data.profile_photo_path) {
                document.getElementById('editTeacherAvatarImg').src = '/storage/' + data.profile_photo_path;
                document.getElementById('editTeacherAvatarImg').style.display = 'block';
                document.getElementById('editTeacherInitial').style.display = 'none';
            } else {
                document.getElementById('editTeacherInitial').style.display = 'block';
                document.getElementById('editTeacherAvatarImg').style.display = 'none';
            }
            document.getElementById('editTeacherForm').action = '/admin/teachers/' + data.id;
            document.getElementById('editTeacherModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load teacher details: ' + error.message);
        });
}

function closeEditTeacherModal() {
    document.getElementById('editTeacherModal').classList.add('hidden');
}

function openViewTeacherModal(id) {
    fetch(`/admin/teachers/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
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
            if (data.profile_photo_path) {
                document.getElementById('viewTeacherAvatarImg').src = '/storage/' + data.profile_photo_path;
                document.getElementById('viewTeacherAvatarImg').style.display = 'block';
                document.getElementById('viewTeacherInitial').style.display = 'none';
            } else {
                document.getElementById('viewTeacherInitial').style.display = 'block';
                document.getElementById('viewTeacherAvatarImg').style.display = 'none';
            }
            document.getElementById('viewTeacherModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load teacher details: ' + error.message);
        });
}

function closeViewTeacherModal() {
    document.getElementById('viewTeacherModal').classList.add('hidden');
}

function deleteTeacher(id) {
    if (confirm('Are you sure you want to delete this teacher?')) {
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
            });
    }
}

// Toast notification system
function showToast(message, type = 'info') {
    const toast = document.getElementById('toastNotification');
    const icon = document.getElementById('toastIcon');
    const msg = document.getElementById('toastMessage');
    
    msg.textContent = message;
    toast.classList.remove('hidden', 'bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');
    
    switch(type) {
        case 'success':
            toast.classList.add('bg-green-500');
            icon.className = 'bi bi-check-circle';
            break;
        case 'error':
            toast.classList.add('bg-red-500');
            icon.className = 'bi bi-exclamation-circle';
            break;
        case 'warning':
            toast.classList.add('bg-yellow-500');
            icon.className = 'bi bi-exclamation-triangle';
            break;
        default:
            toast.classList.add('bg-blue-500');
            icon.className = 'bi bi-info-circle';
    }
    
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3500);
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // AJAX form submission for Add Teacher
    const addForm = document.getElementById('addTeacherForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(addForm);
            const submitBtn = addForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Adding...';
            submitBtn.disabled = true;
            
            fetch(addForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw new Error(data.message || 'Failed to add teacher'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Teacher added successfully', 'success');
                    closeAddTeacherModal();
                    addForm.reset();
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to add teacher', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // AJAX form submission for Edit Teacher
    const editForm = document.getElementById('editTeacherForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(editForm);
            const teacherId = document.getElementById('editTeacherId').value;
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            fetch(`/admin/teachers/${teacherId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => { throw new Error(data.message || 'Failed to update teacher'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Teacher updated successfully', 'success');
                    closeEditTeacherModal();
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to update teacher', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
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

// Filter functionality
document.getElementById('teachersSearch').addEventListener('input', function() {
    filterTeachers();
});

document.getElementById('teachersStatusFilter').addEventListener('change', function() {
    filterTeachers();
});

document.getElementById('teachersCourseFilter').addEventListener('change', function() {
    filterTeachers();
});

function filterTeachers() {
    const searchText = document.getElementById('teachersSearch').value.toLowerCase();
    const statusFilter = document.getElementById('teachersStatusFilter').value.toLowerCase();
    const courseFilter = document.getElementById('teachersCourseFilter').value.toLowerCase();
    const rows = document.querySelectorAll('#teachersTableContainer tbody tr');
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.querySelector('p.font-medium')?.textContent.toLowerCase() || '';
        const email = row.querySelector('p.text-xs')?.textContent.toLowerCase() || '';
        const status = row.querySelectorAll('span')[1]?.textContent.toLowerCase() || '';
        const course = row.querySelectorAll('td')[4]?.textContent.toLowerCase() || '';
        
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
</script>
@endsection


