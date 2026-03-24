{{-- Edit Material Modal --}}
<div id="editMaterialModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeEditMaterialModal()"></div>
    <div class="relative mt-10 flex max-h-[calc(100vh-4rem)] w-[90%] max-w-md flex-col overflow-hidden rounded-lg bg-white shadow-xl mx-auto" style="max-width: 500px;">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Edit Study Material</h3>
            <button onclick="closeEditMaterialModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
        </div>
        <form id="editMaterialForm" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-4">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-blue-600">*</span></label>
                <input type="text" name="title" id="editTitle" value="{{ old('title') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter material title">
                @error('title')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-blue-600">*</span></label>
                    <select name="semester" id="editSemester" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th') ) }}</option>
                        @endfor
                    </select>
                    @error('semester')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-blue-600">*</span></label>
                    <select name="document_type" id="editDocumentType" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select</option>
                        <option value="lecture_notes">Notes</option>
                        <option value="assignment">Assignment</option>
                        <option value="lab_report">Lab Report</option>
                        <option value="assessment">Paper</option>
                        <option value="study_guide">Study Guide</option>
                        <option value="syllabus">Syllabus</option>
                        <option value="project_material">Project</option>
                    </select>
                    @error('document_type')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Course <span class="text-blue-600">*</span></label>
                <select name="course" id="editCourse" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Course</option>
                    @forelse($courses as $course)
                        <option value="{{ $course->subject_name }}">{{ $course->subject_name }} ({{ $course->subject_code }})</option>
                    @empty
                        <option value="">No courses available</option>
                    @endforelse
                </select>
                @error('course')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Visibility <span class="text-blue-600">*</span></label>
                <select name="visibility" id="editVisibility" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Everyone (All)</option>
                    <option value="students">Students Only</option>
                    <option value="teachers">Teachers Only</option>
                    <option value="admins">Admins Only</option>
                </select>
                @error('visibility')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="editDescription" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Short description (optional)">{{ old('description') }}</textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current File</label>
                <div id="currentFileDisplay" class="flex items-center gap-2 p-2 bg-gray-50 rounded border border-gray-200">
                    <i class="bi bi-file-earmark text-lg"></i>
                    <span id="currentFileName" class="text-sm text-gray-700">No file</span>
                </div>
                <p class="text-gray-500 text-xs mt-1">Leave empty to keep current file, or upload a new one</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">New File</label>
                <input type="file" name="file"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <p class="text-gray-500 text-xs mt-1">Max: 20MB (PDF, DOC, Images, ZIP)</p>
                @error('file')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditMaterialModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                    <i class="bi bi-check-lg mr-1"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMaterialModal(materialId) {
    // Find the material data from the global array
    const materials = Array.isArray(window.studyMaterialsData) ? window.studyMaterialsData : [];
    const material = materials.find(m => Number(m.id) === Number(materialId));
    
    if (!material) {
        alert('Material not found');
        return;
    }
    
    // Populate form fields
    document.getElementById('editTitle').value = material.title || '';
    document.getElementById('editSemester').value = material.semester || '';
    document.getElementById('editDocumentType').value = material.document_type || '';
    document.getElementById('editCourse').value = material.course || '';
    document.getElementById('editVisibility').value = material.visibility || 'all';
    document.getElementById('editDescription').value = material.description || '';
    
    // Show current file
    const fileDisplay = document.getElementById('currentFileDisplay');
    const fileNameSpan = document.getElementById('currentFileName');
    if (material.file_name) {
        fileDisplay.innerHTML = '<i class="bi ' + (material.file_icon || 'bi-file-earmark') + ' text-lg"></i><span class="text-sm text-gray-700">' + material.file_name + '</span>';
    } else {
        fileDisplay.innerHTML = '<i class="bi bi-file-earmark text-lg"></i><span class="text-sm text-gray-700">No file attached</span>';
    }
    
    // Update form action
    const updateBaseUrl = window.studyMaterialUpdateBaseUrl || '/admin/study-material';
    document.getElementById('editMaterialForm').action = updateBaseUrl + '/' + materialId;
    
    // Show modal
    document.getElementById('editMaterialModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditMaterialModal() {
    document.getElementById('editMaterialModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close on Escape key press
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditMaterialModal();
});

// Close on background click
document.getElementById('editMaterialModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditMaterialModal();
});
</script>
