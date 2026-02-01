@extends('admin.layouts.app')

@section('title', 'Study Material Management')

@section('content')
<div class="space-y-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-xs">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-xs">
        {{ session('error') }}
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Total Materials</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-file-earmark-text text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Notes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['notes'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-journal-text text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Assignments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['assignments'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-pencil-square text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Papers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['papers'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-file-earmark-ruled text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <h1 class="text-2xl font-bold text-gray-900">Study Materials</h1>
        <a href="#addMaterialModal" onclick="event.preventDefault(); document.getElementById('addMaterialModal').style.display='flex'; document.body.style.overflow='hidden';" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium" style="text-decoration: none;">
            <i class="bi bi-plus-lg"></i>
            <span>Add Material</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.study-material') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search materials..." 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                <select name="semester" class="border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none">
                    <option value="">All Semesters</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>{{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} Semester</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none">
                    <option value="">All Categories</option>
                    <option value="notes" {{ request('category') == 'notes' ? 'selected' : '' }}>Notes</option>
                    <option value="assignment" {{ request('category') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="paper" {{ request('category') == 'paper' ? 'selected' : '' }}>Previous Year Paper</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700 font-medium">
                <i class="bi bi-search mr-1"></i>Filter
            </button>
            <a href="{{ route('admin.study-material') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 font-medium">
                <i class="bi bi-arrow-clockwise mr-1"></i>Reset
            </a>
        </form>
    </div>

    <!-- List Table -->
    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Title</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Course</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Semester</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Category</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Size</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Uploaded By</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Date</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <i class="bi {{ $material->file_icon }} text-lg"></i>
                                <span class="font-medium text-gray-900">{{ $material->title }}</span>
                            </div>
                            @if($material->description)
                            <p class="text-gray-500 text-[10px] mt-1 truncate max-w-[200px]">{{ $material->description }}</p>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-700">
                            {{ $material->course->subject_name ?? 'N/A' }}
                            <span class="text-gray-500">({{ $material->course->subject_code ?? '-' }})</span>
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700">{{ $material->formatted_semester }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $material->category_badge_class }}">
                                {{ $material->category_text }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700">{{ $material->formatted_size }}</td>
                        <td class="px-3 py-2 text-center text-gray-700">{{ $material->uploader->name ?? 'Admin' }}</td>
                        <td class="px-3 py-2 text-center text-gray-700">{{ $material->created_at->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('admin.study-material.download', $material->id) }}" class="text-blue-600 hover:text-blue-800 transition font-medium inline-flex items-center gap-1">
                                <i class="bi bi-download"></i>Download
                            </a>
                            <span class="mx-1 text-gray-300">|</span>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('deleteModal').style.display='flex'; document.body.style.overflow='hidden'; document.getElementById('deleteForm').action='/admin/study-material/{{ $material->id }}';" class="text-red-600 hover:text-red-800 transition font-medium inline-flex items-center gap-1">
                                <i class="bi bi-trash"></i>Delete
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-folder2-open text-4xl text-gray-300 mb-2"></i>
                                <p>No study materials found</p>
                                <p class="text-xs text-gray-400">Upload your first study material to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($materials->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $materials->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Material Modal - Using Inline Styles for Reliability -->
<div id="addMaterialModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; position: relative;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">Add Study Material</h2>
            <button onclick="closeAddMaterialModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #9ca3af;">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.study-material.store') }}" enctype="multipart/form-data" style="padding: 24px;">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Title <span style="color: #dc2626;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Enter material title" required>
                @error('title')<p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Semester <span style="color: #dc2626;">*</span></label>
                    <select name="semester" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" required>
                        <option value="">Select</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>{{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} Semester</option>
                        @endfor
                    </select>
                    @error('semester')<p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Category <span style="color: #dc2626;">*</span></label>
                    <select name="category" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" required>
                        <option value="">Select</option>
                        <option value="notes" {{ old('category') == 'notes' ? 'selected' : '' }}>Notes</option>
                        <option value="assignment" {{ old('category') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        <option value="paper" {{ old('category') == 'paper' ? 'selected' : '' }}>Previous Year Paper</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')<p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Course <span style="color: #dc2626;">*</span></label>
                <select name="course_id" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->subject_name }} ({{ $course->subject_code }}) - {{ $course->semester }}{{ $course->semester == 1 ? 'st' : ($course->semester == 2 ? 'nd' : ($course->semester == 3 ? 'rd' : 'th')) }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">Description</label>
                <textarea name="description" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" placeholder="Short description" rows="2">{{ old('description') }}</textarea>
                @error('description')<p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px;">File <span style="color: #dc2626;">*</span></label>
                <input type="file" name="file" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;" required>
                <p style="color: #6b7280; font-size: 12px; margin-top: 4px;">Max size: 20MB. Supported: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, Images, ZIP, RAR</p>
                @error('file')<p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>@enderror
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="closeAddMaterialModal()" style="padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px; background-color: white; color: #374151; font-size: 14px; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background-color: #dc2626; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer;">
                    <i class="bi bi-upload" style="margin-right: 4px;"></i>Upload Material
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal - Using Inline Styles -->
<div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background-color: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); width: 90%; max-width: 400px; padding: 24px; position: relative;">
        <h3 style="margin: 0 0 12px 0; font-size: 18px; font-weight: 600; color: #111827;">Confirm Delete</h3>
        <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px;">Are you sure you want to delete this study material? This action cannot be undone.</p>
        <form id="deleteForm" method="POST" style="display: flex; justify-content: flex-end; gap: 8px;">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" style="padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px; background-color: white; color: #374151; font-size: 14px; cursor: pointer;">Cancel</button>
            <button type="submit" style="padding: 8px 16px; background-color: #dc2626; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">Delete</button>
        </form>
    </div>
</div>

<script>
function closeAddMaterialModal() {
    document.getElementById('addMaterialModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modals on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddMaterialModal();
        closeDeleteModal();
    }
});

// Close modals on background click
document.getElementById('addMaterialModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeAddMaterialModal();
    }
});

document.getElementById('deleteModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection

