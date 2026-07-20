@extends('teacher.layouts.teacherlayout')

@section('title', __('Notices'))

@section('content')
<div class="teacher-smooth-page teacher-notices-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Page Header -->
    <div class="teacher-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="teacher-page-header-title text-2xl font-bold text-gray-800 dark:text-white">{{ __('Notices') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('View and manage notices for your students.') }}</p>
        </div>
        <a href="{{ route('teacher.notices.create') }}" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition font-medium">
            <i class="bi bi-plus-lg"></i> {{ __('Add Notice') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="teacher-stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Notices') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $notices->count() ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-bell text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Recent') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $recentCount ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>
            </div>
        </div>

        <div class="teacher-stat-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Categories') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $categories->count() ?? 0 }}</p>
                </div>
                <div class="teacher-smooth-icon w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-tag text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="teacher-filter-panel bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('teacher.notices') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                    <select name="subject" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Subjects') }}</option>
                        @if(isset($subjects) && $subjects->isNotEmpty())
                            @foreach($subjects as $subject)
                                <option value="{{ $subject['id'] }}" {{ $selectedSubject == $subject['id'] ? 'selected' : '' }}>
                                    {{ $subject['code'] ?? '' }} - {{ $subject['name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Notice Title') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Sort By') }}</label>
                    <select name="sort" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('Latest') }}</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
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
                    <a href="{{ route('teacher.notices') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Notices List -->
    <div class="space-y-4">
        @if($notices->isNotEmpty())
            @foreach($notices as $notice)
            <div class="teacher-smooth-list-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $notice['title'] ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-3">{{ $notice['message'] ?? $notice['content'] ?? 'N/A' }}</p>
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <span><i class="bi bi-calendar mr-1"></i>{{ $notice['created_at'] ?? 'N/A' }}</span>
                            @if($notice['category'] ?? null)
                            <span class="teacher-smooth-chip inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                {{ $notice['category'] }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="teacher-smooth-icon w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="bi bi-bell text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="mt-6">
                {{ $notices->links() }}
            </div>
        @else
            <div class="teacher-smooth-empty bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="teacher-smooth-icon w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-bell text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Notices Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No notices match your filter criteria.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

