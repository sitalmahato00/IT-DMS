{{-- Filters --}}
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
            <label class="block text-xs font-medium text-gray-700 mb-1">Document Type</label>
            <select name="category" class="border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none">
                <option value="">All Types</option>
                <option value="lecture_notes" {{ request('category') == 'lecture_notes' ? 'selected' : '' }}>Lecture Notes</option>
                <option value="assignment" {{ request('category') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                <option value="lab_report" {{ request('category') == 'lab_report' ? 'selected' : '' }}>Lab Report</option>
                <option value="assessment" {{ request('category') == 'assessment' ? 'selected' : '' }}>Assessment/Paper</option>
                <option value="study_guide" {{ request('category') == 'study_guide' ? 'selected' : '' }}>Study Guide</option>
                <option value="syllabus" {{ request('category') == 'syllabus' ? 'selected' : '' }}>Syllabus</option>
                <option value="project_material" {{ request('category') == 'project_material' ? 'selected' : '' }}>Project Material</option>
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
