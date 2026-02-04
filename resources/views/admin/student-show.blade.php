@extends('admin.layouts.app')

@section('title', 'Student details')

@section('content')
<script>
    // Auto-open View modal on page load
    document.addEventListener('DOMContentLoaded', function() {
        openViewStudentModal();
    });
</script>

<!-- View Student Modal -->
<div id="viewStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeViewStudentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
            <div>
                <h3 class="text-lg font-semibold">View Student</h3>
                <p class="text-sm text-gray-500">Student information and details</p>
            </div>
            <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="text-gray-500 hover:text-gray-900">✕</button>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Full name</label>
                            <p id="view_name" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                            <p id="view_email" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Phone</label>
                            <p id="view_phone" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
                            <p id="view_semester" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
                            <p id="view_department" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Roll No</label>
                            <p id="view_roll_no" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date of birth</label>
                            <p id="view_dob" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Batch Year</label>
                            <p id="view_batch_year" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                            <p id="view_address" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <p id="view_status" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                            <p id="view_role" class="text-sm text-gray-900">—</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Bio</label>
                        <p id="view_bio" class="text-sm text-gray-900">—</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t flex justify-end gap-3">
            <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
            <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Close</button>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" onclick="if(event.target===this) closeEditStudentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl overflow-auto max-h-[90vh]">
        <form id="editStudentForm" action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
                <div>
                    <h3 class="text-lg font-semibold">Edit Student</h3>
                    <p class="text-sm text-gray-500">Update student information</p>
                </div>
                <button type="button" onclick="event.preventDefault(); closeEditStudentModal(); return false;" class="text-gray-500 hover:text-gray-900 text-2xl leading-none">✕</button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Avatar -->
                    <div class="col-span-1 flex flex-col items-center">
                        <div class="w-36 h-36 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
                            @if($student->profile_photo_path)
                                <img id="edit_profile_preview" src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover">
                            @else
                                <div id="edit_profile_placeholder" class="text-gray-400"><i class="bi bi-person-fill text-4xl"></i></div>
                            @endif
                        </div>
                        <label for="edit_profile_photo_input" class="mt-3 inline-flex items-center px-3 py-1.5 bg-white border rounded text-sm cursor-pointer hover:bg-gray-50">
                            <i class="bi bi-upload mr-2"></i>Choose photo
                        </label>
                        <input id="edit_profile_photo_input" type="file" name="profile_photo" accept="image/*" class="sr-only" />
                        <p class="mt-3 text-xs text-gray-500 text-center">Recommended 400×400px. Max 4MB.</p>
                    </div>

                    <!-- Fields -->
                    <div class="col-span-1 lg:col-span-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Full name <span class="text-red-500 text-base">*</span></label>
                                <input name="name" required value="{{ old('name', $student->name) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Email <span class="text-red-500 text-base">*</span></label>
                                <input type="email" name="email" required value="{{ old('email', $student->email) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700">Phone <span class="text-red-500 text-base">*</span></label>
                                <input name="phone" required value="{{ old('phone', $student->phone) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Department <span class="text-red-500 text-base">*</span></label>
                                <input name="department" required value="{{ old('department', $student->department ?? ($student->student->department ?? '')) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700">Semester <span class="text-red-500 text-base">*</span></label>
                                <select name="semester" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
                                    @for($s=1;$s<=6;$s++)
                                        <option value="{{ $s }}" {{ old('semester', $student->student->semester ?? '') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
                                    <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ old('status', $student->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700">Bio</label>
                            <textarea name="bio" rows="3" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">{{ old('bio', $student->bio) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700">Address <span class="text-red-500 text-base">*</span></label>
                            <textarea name="address" required rows="2" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">{{ old('address', $student->student->address ?? '') }}</textarea>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Date of birth (AD) <span class="text-red-500 text-base">*</span></label>
                                <input type="date" name="date_of_birth" required value="{{ old('date_of_birth', $student->student->date_of_birth ?? '') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Batch Year <span class="text-red-500 text-base">*</span></label>
                                <select name="batch_year" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
                                    <option value="">Select</option>
                                    @php $current = date('Y'); @endphp
                                    @for($y=$current; $y >= $current - 10; $y--)
                                        <option value="{{ $y }}" {{ old('batch_year', $student->student->batch_year ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="event.preventDefault(); closeEditStudentModal(); return false;" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // View Student Modal Functions
    function openViewStudentModal() {
        const student = {
            name: '{{ $student->name }}',
            email: '{{ $student->email }}',
            phone: '{{ $student->phone ?? '' }}',
            semester: '{{ $student->student->semester ?? '' }}',
            department: '{{ $student->department ?? '' }}',
            roll_no: '{{ $student->student->roll_no ?? $student->id }}',
            date_of_birth_bs: '{{ $student->student->date_of_birth_bs ?? '' }}',
            address: `{!! nl2br(e($student->student->address ?? '')) !!}`,
            batch_year: '{{ $student->student->batch_year ?? '' }}',
            status: '{{ $student->status }}',
            role: '{{ ucfirst($student->role) }}',
            bio: '{{ $student->bio ?? '' }}',
            profile_photo_path: '{{ $student->profile_photo_path ? asset("storage/".$student->profile_photo_path) : "" }}'
        };

        // Populate modal fields
        document.getElementById('view_name').textContent = student.name;
        document.getElementById('view_email').textContent = student.email;
        document.getElementById('view_phone').textContent = student.phone || '—';
        document.getElementById('view_semester').textContent = student.semester || '—';
        document.getElementById('view_department').textContent = student.department || '—';
        document.getElementById('view_roll_no').textContent = student.roll_no;
        document.getElementById('view_dob').textContent = student.date_of_birth_bs || '—';
        // Address may contain line breaks; show plain text (strip <br>)
        document.getElementById('view_address').textContent = (student.address ? student.address.replace(/<br\s*\/?\s*>/gi, "\n") : '—');
        document.getElementById('view_batch_year').textContent = student.batch_year || '—';
        document.getElementById('view_status').textContent = student.status.charAt(0).toUpperCase() + student.status.slice(1);
        document.getElementById('view_role').textContent = student.role;
        document.getElementById('view_bio').textContent = student.bio || '—';
        // Handle photo
        if (student.profile_photo_path) {
            document.getElementById('viewStudentAvatarImg').src = student.profile_photo_path;
            document.getElementById('viewStudentAvatarImg').style.display = 'block';
            document.getElementById('viewStudentInitial').style.display = 'none';
        } else {
            document.getElementById('viewStudentAvatarImg').style.display = 'none';
            document.getElementById('viewStudentInitial').style.display = 'flex';
        }

        document.getElementById('viewStudentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeViewStudentModal() {
        document.getElementById('viewStudentModal').classList.add('hidden');
        document.getElementById('editStudentModal').classList.add('hidden');
        document.getElementById('editStudentForm')?.reset();
        document.body.style.overflow = 'auto';
    }

    function closeBothModals() {
        document.getElementById('viewStudentModal').classList.add('hidden');
        document.getElementById('editStudentModal').classList.add('hidden');
        document.getElementById('editStudentForm')?.reset();
        document.body.style.overflow = 'auto';
    }

    // Close view modal when clicking on the backdrop
    document.getElementById('viewStudentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewStudentModal();
        }
    });

    function openEditStudentModal() {
        document.getElementById('editStudentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
    }

    function closeEditStudentModal() {
        const modal = document.getElementById('editStudentModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Re-enable scrolling
        // Reset the form to prevent any residual data
        document.getElementById('editStudentForm')?.reset();
    }

    // Close modal when clicking on the backdrop
    document.getElementById('editStudentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditStudentModal();
        }
    });

    // Handle photo preview for edit
    document.getElementById('edit_profile_photo_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_profile_preview').src = e.target.result;
                document.getElementById('edit_profile_preview').style.display = 'block';
                const placeholder = document.getElementById('edit_profile_placeholder');
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle form submission
    document.getElementById('editStudentForm')?.addEventListener('submit', function(e) {
        // Ensure modal closes on form submission
        closeEditStudentModal();
        sessionStorage.setItem('showNotification', 'student_updated');
        // Allow form to submit normally
    });

    // Check if we just came back from form submission
    document.addEventListener('DOMContentLoaded', function() {
        const notification = sessionStorage.getItem('showNotification');
        if (notification && notification === 'student_updated') {
            sessionStorage.removeItem('showNotification');
            // Show success message (you can implement a toast here)
        }
        // Ensure modal is hidden on page load
        document.getElementById('editStudentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    });
</script>
<style>
@media print{
    /* hide everything by default */
    body *{ visibility: hidden !important; }
    /* show only printable area */
    .student-printable, .student-printable *{ visibility: visible !important; }
    .student-printable{ position: absolute; left: 0; top: 0; width: 100%; }
    /* remove heavy visuals for print */
    .student-printable .shadow, .student-printable .ring-white, .student-printable [class*="bg-gradient"]{ box-shadow: none !important; background: transparent !important; }
    /* hide action elements explicitly */
    .no-print{ display: none !important; }
}
</style>
@endsection

