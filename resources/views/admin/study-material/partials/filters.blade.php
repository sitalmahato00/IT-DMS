{{-- Filters --}}
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-gray-200 dark:border-slate-700 p-4">
    <form method="GET" action="{{ route('admin.study-material') }}">
        {{-- Filter Inputs Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Search Material</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search materials..." 
                    class="w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none bg-white dark:bg-slate-700 dark:text-gray-200">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Semester</label>
                <select name="semester" class="w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none bg-white dark:bg-slate-700 dark:text-gray-200">
                    <option value="">All Semesters</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>{{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} Semester</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Document Type</label>
                <select name="category" class="w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-red-500 focus:outline-none bg-white dark:bg-slate-700 dark:text-gray-200">
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
        </div>

        {{-- Action Buttons Row --}}
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex gap-2 items-center">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                    <i class="bi bi-funnel"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.study-material') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 hover:bg-gray-50 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium transition shadow-sm">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Reset</span>
                </a>
            </div>
        </div>
    </form>
</div>


