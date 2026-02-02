{{-- Add Material Modal --}}
<div id="addMaterialModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeAddMaterialModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md mx-auto mt-20" style="width: 90%; max-width: 500px;">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Add Study Material</h3>
            <button onclick="closeAddMaterialModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
        </div>
        <form method="POST" action="{{ route('admin.study-material.store') }}" enctype="multipart/form-data" class="p-4">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-600">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Enter material title">
                @error('title')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-600">*</span></label>
                    <select name="semester" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Select</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }}
                            </option>
                        @endfor
                    </select>
                    @error('semester')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-600">*</span></label>
                    <select name="document_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Select</option>
                        <option value="lecture_notes" {{ old('document_type') == 'lecture_notes' ? 'selected' : '' }}>Notes</option>
                        <option value="assignment" {{ old('document_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="lab_report" {{ old('document_type') == 'lab_report' ? 'selected' : '' }}>Lab Report</option>
                        <option value="assessment" {{ old('document_type') == 'assessment' ? 'selected' : '' }}>Paper</option>
                        <option value="study_guide" {{ old('document_type') == 'study_guide' ? 'selected' : '' }}>Study Guide</option>
                        <option value="syllabus" {{ old('document_type') == 'syllabus' ? 'selected' : '' }}>Syllabus</option>
                        <option value="project_material" {{ old('document_type') == 'project_material' ? 'selected' : '' }}>Project</option>
                    </select>
                    @error('document_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Course <span class="text-red-600">*</span></label>
                <select name="course" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Select Course</option>
                    @forelse($courses as $course)
                        <option value="{{ $course->subject_name }}" {{ old('course') == $course->subject_name ? 'selected' : '' }}>
                            {{ $course->subject_name }} ({{ $course->subject_code }})
                        </option>
                    @empty
                        <option value="">No courses available</option>
                    @endforelse
                </select>
                @error('course')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Visibility <span class="text-red-600">*</span></label>
                <select name="visibility" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="all" {{ old('visibility') == 'all' ? 'selected' : '' }}>Everyone (All)</option>
                    <option value="students" {{ old('visibility') == 'students' ? 'selected' : '' }}>Students Only</option>
                    <option value="teachers" {{ old('visibility') == 'teachers' ? 'selected' : '' }}>Teachers Only</option>
                    <option value="admins" {{ old('visibility') == 'admins' ? 'selected' : '' }}>Admins Only</option>
                </select>
                @error('visibility')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Short description (optional)">{{ old('description') }}</textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-600">*</span></label>
                <input type="file" name="file" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <p class="text-gray-500 text-xs mt-1">Max: 20MB (PDF, DOC, Images, ZIP)</p>
                @error('file')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAddMaterialModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                    <i class="bi bi-upload mr-1"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddMaterialModal() {
    document.getElementById('addMaterialModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddMaterialModal() {
    document.getElementById('addMaterialModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close on Escape key press
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAddMaterialModal();
});

// Close on background click
document.getElementById('addMaterialModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddMaterialModal();
});
</script>
