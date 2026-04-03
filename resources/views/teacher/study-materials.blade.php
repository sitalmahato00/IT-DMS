@extends('teacher.layouts.teacherlayout')

@section('title', __('Study Materials'))

@section('content')
<div class="teacher-smooth-page teacher-materials-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Page Header -->
    <div class="teacher-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="teacher-page-header-title text-2xl font-bold text-gray-800 dark:text-white">{{ __('Study Materials') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('Upload and manage study materials for your subjects.') }}</p>
        </div>
        <a href="{{ route('teacher.study-materials.create') }}" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition font-medium">
            <i class="bi bi-upload"></i> {{ __('Upload Material') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="teacher-stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Materials') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $studyMaterials->count() ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-file-earmark-pdf text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Subjects') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjects->count() ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Downloads') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalDownloads ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-download text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Size') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSize ?? '0 MB' }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400">
                    <i class="bi bi-hdd text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="teacher-filter-panel bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('teacher.study-materials') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Semester') }}</label>
                    <select name="semester" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Semesters') }}</option>
                        @if(isset($semesters) && is_array($semesters))
                            @foreach($semesters as $sem)
                                <option value="{{ $sem }}" {{ $selectedSemester == $sem ? 'selected' : '' }}>
                                    {{ __('Semester') }} {{ $sem }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                    <select name="subject" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Subjects') }}</option>
                        @if($subjects->isNotEmpty())
                            @foreach($subjects as $subject)
                                <option value="{{ $subject['id'] }}" {{ request('subject') == $subject['id'] ? 'selected' : '' }}>
                                    {{ $subject['code'] }} - {{ $subject['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Material Title') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Sort By') }}</label>
                    <select name="sort" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('Most Downloaded') }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Per Page') }}</label>
                    <select name="per_page" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 justify-between flex-wrap pt-2">
                <div class="flex gap-2 flex-wrap">
                    <button type="submit" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium shadow-sm">
                        <i class="bi bi-funnel"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('teacher.study-materials') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Materials Table -->
    <div class="teacher-smooth-table-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($studyMaterials->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3">{{ __('Title') }}</th>
                            <th class="px-4 py-3">{{ __('Subject') }}</th>
                            <th class="px-4 py-3">{{ __('Type') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Size') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Downloads') }}</th>
                            <th class="px-4 py-3">{{ __('Uploaded') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($studyMaterials as $material)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="teacher-smooth-icon w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $material['title'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $material['subject_code'] ?? '' }} - {{ $material['subject_name'] ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="teacher-smooth-chip inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                    {{ strtoupper($material['file_type'] ?? 'PDF') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                                {{ $material['file_size'] ?? '0 KB' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="teacher-smooth-chip inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                    {{ $material['download_count'] ?? 0 }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $material['created_at'] ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('teacher.study-materials.download', $material['id']) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 rounded transition">
                                    <i class="bi bi-download"></i> {{ __('Download') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $studyMaterials->links() }}
            </div>
        @else
                <div class="teacher-smooth-empty p-8 text-center">
                    <div class="teacher-smooth-icon w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-file-earmark-pdf text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Materials Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No study materials match your filter criteria.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
