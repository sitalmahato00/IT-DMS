@extends('admin.layouts.app')

@section('title', 'Document Management')

@section('content')
{{-- Page Header - Using standardized component --}}
@include('admin.components.admin-page-header', [
    'title' => 'Documents Management',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Documents Management']
    ],
    'addButton' => [
        'label' => 'Add Material',
        'onclick' => "openAddMaterialModal()"
    ]
])

{{-- Flash Messages --}}
@if(session('success'))
<div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-2 rounded text-xs mb-4">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-2 rounded text-xs mb-4">
    {{ session('error') }}
</div>
@endif

{{-- Stats Cards - Using standardized component --}}
@include('admin.components.admin-stats-cards', [
    'cards' => [
        ['title' => 'Total Materials', 'value' => $stats['total'] ?? 0, 'icon' => 'bi-file-earmark-text', 'color' => 'blue'],
        ['title' => 'Lecture Notes', 'value' => $stats['notes'] ?? 0, 'icon' => 'bi-journal-text', 'color' => 'green'],
        ['title' => 'Assignments', 'value' => $stats['assignments'] ?? 0, 'icon' => 'bi-pencil-square', 'color' => 'purple'],
        ['title' => 'Assessments', 'value' => $stats['papers'] ?? 0, 'icon' => 'bi-file-earmark-ruled', 'color' => 'orange'],
    ]
])

{{-- Filter Card - Using standardized component --}}
@include('admin.components.admin-filter-card', [
    'formAction' => route('admin.study-material'),
    'filters' => [
        ['name' => 'search', 'type' => 'text', 'placeholder' => 'Search materials...', 'value' => request('search'), 'label' => 'Search'],
        ['name' => 'semester', 'type' => 'select', 'options' => array_merge(['' => 'All Semesters'], array_combine(range(1,6), array_map(function($i){return $i.($i==1?'st':($i==2?'nd':($i==3?'rd':'th'))).' Semester';},range(1,6)))), 'value' => request('semester'), 'label' => 'Semester'],
        ['name' => 'category', 'type' => 'select', 'options' => ['' => 'All Types', 'lecture_notes' => 'Lecture Notes', 'assignment' => 'Assignment', 'lab_report' => 'Lab Report', 'assessment' => 'Assessment/Paper', 'study_guide' => 'Study Guide', 'syllabus' => 'Syllabus', 'project_material' => 'Project Material'], 'value' => request('category'), 'label' => 'Document Type'],
    ],
    'showReset' => true,
    'resetRoute' => route('admin.study-material')
])

{{-- Table Card with dark mode --}}
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Materials List</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs" id="materialsTable">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                    <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-gray-100">Title</th>
                    <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-gray-100">Subject</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Semester</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Type</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Size</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Visibility</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Uploaded</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $material)
                <tr class="border-b border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50">
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <i class="bi {{ $material->file_icon }} text-lg"></i>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $material->title }}</span>
                        </div>
                        @if($material->description)
                        <p class="text-gray-500 dark:text-gray-400 text-[10px] mt-1 truncate max-w-[200px]">{{ $material->description }}</p>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-gray-700 dark:text-gray-300">
                        {{ $material->subject->subject_name ?? 'N/A' }}
                        <span class="text-gray-500 dark:text-gray-400">({{ $material->subject->subject_code ?? '-' }})</span>
                    </td>
                    <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">{{ $material->formatted_semester }}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $material->document_type_badge_class }}">
                            {{ $material->document_type_text }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">{{ $material->formatted_size }}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $material->visibility == 'students' ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300' : ($material->visibility == 'teachers' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300') }}">
                            {{ ucfirst($material->visibility) }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">{{ $material->uploaded_at ? $material->uploaded_at->format('Y-m-d') : $material->created_at->format('Y-m-d') }}</td>
                    <td class="px-3 py-2 text-center">
                        @if($material->file_path)
                        <a href="{{ route('admin.study-material.download', $material->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition font-medium inline-flex items-center gap-1">
                            <i class="bi bi-download"></i>Download
                        </a>
                        <span class="mx-1 text-gray-300">|</span>
                        @endif
                        <button onclick='openEditMaterialModal({{ $material->id }})' class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition font-medium inline-flex items-center gap-1">
                            <i class="bi bi-pencil"></i>Edit
                        </button>
                        <span class="mx-1 text-gray-300">|</span>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('deleteModal').style.display='flex'; document.body.style.overflow='hidden'; document.getElementById('deleteForm').action='{{ route('admin.study-material.destroy', $material->id) }}';" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition font-medium inline-flex items-center gap-1">
                            <i class="bi bi-trash"></i>Delete
                        </a>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="bi bi-folder2-open text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                            <p>No study materials found</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Upload your first study material to get started</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($materials->hasPages())
    <div class="px-3 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
        @include('admin.components.admin-pagination', [
            'paginator' => $materials,
            'route' => route('admin.study-material')
        ])
    </div>
    @endif
</div>

{{-- Modals --}}
@include('admin.study-material.partials.add-modal')
@include('admin.study-material.partials.edit-modal')
@include('admin.study-material.partials.delete-modal')
@endsection
