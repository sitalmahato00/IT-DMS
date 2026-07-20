{{-- Add Material Modal --}}
<div id="addMaterialModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeAddMaterialModal()"></div>
    <div class="document-modal-panel relative mt-10 flex max-h-[calc(100vh-4rem)] w-[90%] max-w-md flex-col overflow-hidden rounded-lg bg-white shadow-xl mx-auto" style="max-width: 500px;">
        <div class="document-modal-header flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold text-white">Add Study Material</h3>
            <button onclick="closeAddMaterialModal()" class="text-white/80 hover:text-white text-2xl">×</button>
        </div>
        <form id="addMaterialForm" method="POST" action="{{ route('admin.study-material.store-ajax') }}" enctype="multipart/form-data" class="document-form overflow-y-auto p-4">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-blue-600">*</span></label>
                <input type="text" name="title" id="addMaterialTitle" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter material title">
                @error('title')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-blue-600">*</span></label>
                    <select name="semester" id="addMaterialSemester" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }}</option>
                        @endfor
                    </select>
                    @error('semester')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-blue-600">*</span></label>
                    <select name="document_type" id="addMaterialType" required
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
                <select name="course" id="addMaterialCourse" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Course</option>
                    @forelse($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->subject_name }} ({{ $course->subject_code }})</option>
                    @empty
                        <option value="">No courses available</option>
                    @endforelse
                </select>
                @error('course')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Visibility <span class="text-blue-600">*</span></label>
                <select name="visibility" id="addMaterialVisibility" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Everyone (All)</option>
                    <option value="students">Students Only</option>
                    <option value="faculty">Faculty Only</option>
                </select>
                @error('visibility')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="addMaterialDescription" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Short description (optional)"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-blue-600">*</span></label>
                <input type="file" name="file" id="addMaterialFile" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <p class="text-gray-500 text-xs mt-1">Max: 20MB (PDF, DOC, Images, ZIP)</p>
                @error('file')<p class="text-blue-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <!-- Upload Progress -->
            <div id="uploadProgress" class="mb-4 hidden">
                <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                    <span>Uploading...</span>
                    <span id="uploadPercentage">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="uploadProgressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
            
            <!-- Error Message -->
            <div id="uploadError" class="mb-4 hidden bg-red-100 border border-red-400 text-blue-700 px-3 py-2 rounded text-xs"></div>
            
            <!-- Success Message -->
            <div id="uploadSuccess" class="mb-4 hidden bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded text-xs"></div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAddMaterialModal()"
                    class="document-secondary-btn px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50"
                    id="addMaterialCancelBtn">
                    Cancel
                </button>
                <button type="submit"
                    class="document-primary-btn px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
                    id="addMaterialSubmitBtn">
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
    resetAddForm();
}

// Reset form to initial state
function resetAddForm() {
    const form = document.getElementById('addMaterialForm');
    form.reset();
    form.classList.remove('hidden');
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('uploadError').classList.add('hidden');
    document.getElementById('uploadSuccess').classList.add('hidden');
    document.getElementById('addMaterialSubmitBtn').disabled = false;
    document.getElementById('addMaterialSubmitBtn').innerHTML = '<i class="bi bi-upload mr-1"></i>Upload';
}

// Close on Escape key press
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAddMaterialModal();
});

// Close on background click
document.getElementById('addMaterialModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddMaterialModal();
});

// AJAX Upload for Add Material Form
document.getElementById('addMaterialForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('addMaterialSubmitBtn');
    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('uploadProgressBar');
    const percentageSpan = document.getElementById('uploadPercentage');
    const errorDiv = document.getElementById('uploadError');
    const successDiv = document.getElementById('uploadSuccess');
    
    // Reset states
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');
    progressDiv.classList.remove('hidden');
    progressBar.style.width = '0%';
    percentageSpan.textContent = '0%';
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split mr-1"></i>Uploading...';
    
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percentComplete + '%';
            percentageSpan.textContent = percentComplete + '%';
        }
    });
    
    xhr.addEventListener('load', function() {
        try {
            const response = JSON.parse(xhr.responseText);
            
            if (xhr.status === 200 && response.success) {
                // Success!
                successDiv.textContent = response.message;
                successDiv.classList.remove('hidden');
                
                // Reset form and close modal after a short delay
                setTimeout(function() {
                    closeAddMaterialModal();
                    
                    // Add the new row to the table
                    addMaterialRowToTable(response.row_html);
                    syncStudyMaterialCache(response.material);
                    
                    // Update statistics
                    updateStatistics(response.stats);
                }, 1000);
                
            } else {
                // Validation or server error
                const errorMessage = response.message || 'Upload failed. Please try again.';
                errorDiv.textContent = errorMessage;
                errorDiv.classList.remove('hidden');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload mr-1"></i>Upload';
            }
        } catch (e) {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-upload mr-1"></i>Upload';
        }
    });
    
    xhr.addEventListener('error', function() {
        errorDiv.textContent = 'Network error. Please check your connection and try again.';
        errorDiv.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-upload mr-1"></i>Upload';
    });
    
    xhr.open('POST', '{{ route("admin.study-material.store-ajax") }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '');
    xhr.send(formData);
});

// Function to add material row to table dynamically
function addMaterialRowToTable(rowHtml) {
    const tableBody = document.querySelector('#materialsTable tbody');
    const emptyRow = tableBody.querySelector('.empty-row');
    
    // Remove empty row if exists
    if (emptyRow) {
        emptyRow.remove();
    }
    
    // Check if there's already a row for this material
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = rowHtml;
    const newRow = tempDiv.firstElementChild;
    const materialId = newRow.id.replace('material-row-', '');
    
    // Remove any existing row with the same ID
    const existingRow = document.getElementById('material-row-' + materialId);
    if (existingRow) {
        existingRow.remove();
    }
    
    // Prepend new row with animation
    newRow.style.opacity = '0';
    newRow.style.transform = 'translateY(-10px)';
    tableBody.insertBefore(newRow, tableBody.firstChild);
    
    // Trigger animation
    setTimeout(function() {
        newRow.style.transition = 'all 0.3s ease';
        newRow.style.opacity = '1';
        newRow.style.transform = 'translateY(0)';
    }, 50);
}

function syncStudyMaterialCache(material) {
    if (!material || !material.id) {
        return;
    }

    if (!Array.isArray(window.studyMaterialsData)) {
        window.studyMaterialsData = [];
    }

    const existingIndex = window.studyMaterialsData.findIndex(item => Number(item.id) === Number(material.id));

    if (existingIndex >= 0) {
        window.studyMaterialsData[existingIndex] = material;
        return;
    }

    window.studyMaterialsData.unshift(material);
}

// Function to update statistics cards
function updateStatistics(stats) {
    // Update total count
    const totalCard = document.querySelector('[data-stat="total"]');
    if (totalCard) {
        totalCard.textContent = stats.total;
    }
    
    // Update type-specific counts
    if (stats.notes !== undefined) {
        const notesCard = document.querySelector('[data-stat="notes"]');
        if (notesCard) notesCard.textContent = stats.notes;
    }
    if (stats.assignments !== undefined) {
        const assignmentCard = document.querySelector('[data-stat="assignments"]');
        if (assignmentCard) assignmentCard.textContent = stats.assignments;
    }
    if (stats.papers !== undefined) {
        const papersCard = document.querySelector('[data-stat="papers"]');
        if (papersCard) papersCard.textContent = stats.papers;
    }
    if (stats.lab_reports !== undefined) {
        const labCard = document.querySelector('[data-stat="lab_reports"]');
        if (labCard) labCard.textContent = stats.lab_reports;
    }
}
</script>

