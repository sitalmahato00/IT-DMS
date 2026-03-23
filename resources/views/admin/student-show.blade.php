`@extends('admin.layouts.app')

@section('title', 'Student details')

@section('content') class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeViewStudentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <div class="px-6 py-4 border-b-2 border-red-700 flex items-center justify-between sticky top-0 bg-red-600 text-white">
            <div>
                <h3 class="text-lg font-semibold">View Student</h3>
                <p class="text-sm text-red-100">Student information and details</p>
            </div>
            <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="text-red-100 hover:text-white">✕</button>
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
                            <label class="block text-xs font-medium text-gray-500 mb-1">Academic Year</label>
                            <p id="view_batch_year" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                            <p id="view_address" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Gender</label>
                            <p id="view_gender" class="text-sm text-gray-900">—</p>
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

        <!-- Hidden Print Component Container -->
        <div id="printComponentContainer" style="display: none; position: absolute; left: -9999px; top: -9999px; width: 100%; height: 100%;">
        </div>

        <div class="px-6 py-4 border-t flex justify-between gap-3">
            <div>
                <button type="button" onclick="event.preventDefault(); initiateStudentPrint(); return false;" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 inline-flex items-center gap-2">
                    <i class="bi bi-printer"></i> Print Document
                </button>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 p-4" onclick="if(event.target===this) closeEditStudentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl overflow-auto max-h-[90vh]">
        <form id="editStudentForm" action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b-2 border-red-700 flex items-center justify-between sticky top-0 bg-red-600 text-white">
                <div>
                    <h3 class="text-lg font-semibold">Edit Student</h3>
                    <p class="text-sm text-red-100">Update student information</p>
                </div>
                <button type="button" onclick="event.preventDefault(); closeEditStudentModal(); return false;" class="text-red-100 hover:text-white text-2xl leading-none">✕</button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Avatar -->
                    <div class="col-span-1 flex flex-col items-center">
                        <div class="w-36 h-36 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
                            @if($student->student && $student->student->profile_photo_path)
                                <img id="edit_profile_preview" src="{{ asset('storage/'.$student->student->profile_photo_path) }}" alt="Profile" class="w-full h-full object-cover">
                            @elseif($student->profile_photo_path)
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
                                <label class="block text-xs font-medium text-gray-700">Full name <span class="text-blue-500 text-base">*</span></label>
                                <input name="name" required value="{{ old('name', $student->name) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Email <span class="text-blue-500 text-base">*</span></label>
                                <input type="email" name="email" required value="{{ old('email', $student->email) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700">Phone <span class="text-blue-500 text-base">*</span></label>
                                <input type="tel" name="phone" required value="{{ old('phone', $student->phone) }}" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Department <span class="text-blue-500 text-base">*</span></label>
                                <input name="department" required value="{{ old('department', $student->department ?? ($student->student->department ?? '')) }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700">Semester <span class="text-blue-500 text-base">*</span></label>
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
                            <label class="block text-xs font-medium text-gray-700">Address <span class="text-blue-500 text-base">*</span></label>
                            <textarea name="address" required rows="2" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">{{ old('address', $student->student->address ?? '') }}</textarea>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Date of birth (AD) <span class="text-blue-500 text-base">*</span></label>
                                <input type="date" name="date_of_birth" required value="{{ old('date_of_birth', $student->student->date_of_birth ?? '') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm focus:ring-1 focus:ring-red-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Batch Year <span class="text-blue-500 text-base">*</span></label>
                                <div class="flex gap-2">
                                    <select name="batch_year" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
                                        <option value="">AD</option>
                                        @php $current = date('Y'); @endphp
                                        @for($y=$current; $y >= $current - 10; $y--)
                                            <option value="{{ $y }}" {{ old('batch_year', $student->student->batch_year ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <input type="text" name="batch_year_bs" placeholder="BS" value="{{ old('batch_year_bs', $student->student->batch_year_bs ?? '') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                                </div>
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
    function initiateStudentPrint() {
        const studentId = {{ $student->id }};
        const college = @json($student->student->college ?? null);
        const student = @json($student->toArray());
        const marks = @json($student->marks ?? []);
        const attendance = @json([
            'total_days' => $student->student?->attendanceRecords()?->count() ?? 0,
            'present_days' => $student->student?->attendanceRecords()?->where('status', 'present')->count() ?? 0
        ]);

        // Build the component HTML
        let componentHtml = `
            <div class="bg-white" style="font-family: Arial, sans-serif; padding: 40px;">
                <div>
                    <div style="text-align: center; border-bottom: 3px solid #1e3a8a; padding: 30px 20px; position: relative;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 15px;">
                            <div style="height: 80px; width: 80px; background: #1e3a8a; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                LOGO
                            </div>
                            <div style="text-align: left;">
                                <h1 style="margin: 0; font-size: 24px; font-weight: bold; color: #1e3a8a;">
                                    ${college?.name || 'Educational Institution'}
                                </h1>
                                ${college?.address ? `<p style="margin: 5px 0; font-size: 14px; color: #666;">${college.address}</p>` : ''}
                                ${college?.phone ? `<p style="margin: 5px 0; font-size: 13px; color: #666;">Phone: ${college.phone}</p>` : ''}
                            </div>
                        </div>
                        <h2 style="margin: 15px 0 0 0; font-size: 18px; color: #1e3a8a; border-top: 2px solid #1e3a8a; padding-top: 15px;">
                            Student Details & Academic Record
                        </h2>
                    </div>
                    <div style="padding: 25px 20px; background: #f8fafc;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <div style="margin-bottom: 15px;">
                                    <label style="font-weight: bold; color: #1e3a8a; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 3px;">Student Name</label>
                                    <p style="margin: 0; font-size: 16px; font-weight: 600; color: #000;">${student?.name || 'N/A'}</p>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="font-weight: bold; color: #1e3a8a; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 3px;">Roll Number</label>
                                    <p style="margin: 0; font-size: 16px; font-weight: 600; color: #000;">${student?.student?.roll_no || 'N/A'}</p>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="font-weight: bold; color: #1e3a8a; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 3px;">Email</label>
                                    <p style="margin: 0; font-size: 14px; color: #000;">${student?.email || 'N/A'}</p>
                                </div>
                            </div>
                            <div>
                                <div style="margin-bottom: 15px;">
                                    <label style="font-weight: bold; color: #1e3a8a; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 3px;">Date of Birth</label>
                                    <p style="margin: 0; font-size: 16px; font-weight: 600; color: #000;">${student?.student?.date_of_birth || 'N/A'}</p>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="font-weight: bold; color: #1e3a8a; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 3px;">Gender</label>
                                    <p style="margin: 0; font-size: 16px; font-weight: 600; color: #000;">${student?.student?.gender || 'N/A'}</p>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="font-weight: bold; color: #1e3a8a; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 3px;">Status</label>
                                    <p style="margin: 0; font-size: 16px; font-weight: 600; color: #28a745; background: #e8f5e9; padding: 5px 10px; display: inline-block; border-radius: 4px;">${student?.student?.status || 'Active'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="padding: 20px; text-align: center; border-top: 2px solid #1e3a8a; margin-top: 30px; font-size: 12px; color: #666;">
                    <p style="margin: 5px 0;"><strong>Document Generated:</strong> ${new Date().toLocaleString()}</p>
                    <p style="margin: 5px 0; font-size: 11px; color: #999;">This is an official document</p>
                </div>
            </div>
        `;

        // Insert into container
        const container = document.getElementById('printComponentContainer');
        container.innerHTML = componentHtml;

        // Show container temporarily and print
        container.style.display = 'block';
        container.style.position = 'fixed';
        container.style.left = '0';
        container.style.top = '0';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.zIndex = '10000';
        container.style.backgroundColor = 'white';
        container.style.overflow = 'auto';

        // Trigger print after content renders
        setTimeout(() => {
            window.print();
            // Hide after print dialog
            setTimeout(() => {
                container.style.display = 'none';
                container.style.position = 'absolute';
                container.style.left = '-9999px';
                container.style.top = '-9999px';
            }, 500);
        }, 100);
    }

    function printStudentReport() {
        const studentId = {{ $student->id }};
        const printWindow = window.open(`/admin/students/${studentId}/exam-report`, 'StudentReport', 'width=1000,height=800');
        if (printWindow) {
            printWindow.focus();
        }
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
@endsection


