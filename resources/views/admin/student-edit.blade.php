@extends('admin.layouts.app')

@section('title', 'Edit student')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Edit Student</h2>

        <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            @method('PUT')

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
                            <label class="block text-xs font-medium text-gray-700">Roll No <span class="text-red-500 text-base">*</span></label>
                            <input name="student_id" required value="{{ old('student_id', $student->student->roll_no ?? '') }}" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Semester <span class="text-red-500 text-base">*</span></label>
                            <select name="semester" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">
                                <option value="">Select</option>
                                @for($s=1;$s<=6;$s++)
                                    <option value="{{ $s }}" {{ old('semester', $student->student->semester ?? '') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                    <div class="mt-4">
                        <label class="block text-xs font-medium text-gray-700">Address <span class="text-red-500 text-base">*</span></label>
                        <textarea name="address" required rows="2" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">{{ old('address', $student->student->address ?? '') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-medium text-gray-700">Bio</label>
                        <textarea name="bio" rows="3" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm shadow-sm">{{ old('bio', $student->bio) }}</textarea>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                            <div class="flex items-center gap-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="status" id="statusHidden" value="{{ $student->status ?? 'active' }}">
                                    <input type="checkbox" id="statusToggle" class="sr-only peer" {{ ($student->status ?? '') === 'active' ? 'checked' : '' }} aria-label="Toggle status" />
                                    
                                    <!-- Background -->
                                    <div class="w-8 h-4 bg-gray-300 rounded-full peer-checked:bg-green-500 transition-colors duration-300 shadow-inner"></div>
                                    
                                    <!-- Slider -->
                                    <span class="absolute left-0.5 top-0.5 w-3 h-3 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-4 shadow-sm"></span>
                                </label>
                                <span id="statusLabel" class="text-sm font-medium">{{ $student->status === 'active' ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" onclick="history.back()" class="inline-block px-3 py-1 border rounded text-sm mr-2">Cancel</button>
                            <button type="submit" class="inline-block px-3 py-1 bg-red-600 text-white rounded text-sm">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Edit page: image preview
    (function(){
        const input = document.getElementById('edit_profile_photo_input');
        const img = document.getElementById('edit_profile_preview');
        const placeholder = document.getElementById('edit_profile_placeholder');
        if(!input) return;
        input.addEventListener('change', function(){
            const file = this.files && this.files[0];
            if(!file) return;
            const reader = new FileReader();
            reader.onload = function(e){
                if(img){ img.src = e.target.result; img.classList.remove('hidden'); }
                if(placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
        // allow clicking the placeholder area to open file picker
        const wrapper = img ? img.parentElement : (placeholder ? placeholder.parentElement : null);
        if(wrapper) wrapper.addEventListener('click', function(){ input.click(); });
    })();

    // Status toggle
    const statusToggle = document.getElementById('statusToggle');
    const statusHidden = document.getElementById('statusHidden');
    const statusLabel = document.getElementById('statusLabel');

    if(statusToggle) {
        statusToggle.addEventListener('change', function() {
            const newStatus = this.checked ? 'active' : 'inactive';
            const newLabel = this.checked ? 'Active' : 'Inactive';
            statusHidden.value = newStatus;
            statusLabel.textContent = newLabel;
        });
    }
</script>
@endsection
