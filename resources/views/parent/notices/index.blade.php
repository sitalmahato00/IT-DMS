@extends('parent.layouts.parentlayout')

@section('title', __('Notices'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Notices & Announcements') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Stay updated with important information') }}</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" placeholder="{{ __('Search notices...') }}" value="{{ request('search') }}" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
            <select name="importance" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
                <option value="">{{ __('All Notices') }}</option>
                <option value="important" {{ request('importance') === 'important' ? 'selected' : '' }}>{{ __('Important Only') }}</option>
                <option value="normal" {{ request('importance') === 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700 text-white rounded-lg font-medium transition">
                <i class="bi bi-search"></i>
                {{ __('Search') }}
            </button>
        </form>
    </div>

    <!-- Notices List -->
    <div class="space-y-4">
        @forelse($notices as $notice)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-l-4 {{ $notice->is_important ? 'border-orange-600 dark:border-orange-400' : 'border-gray-300 dark:border-gray-600' }} overflow-hidden hover:shadow-xl transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                @if($notice->is_important)
                                    <span class="inline-block px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300 rounded text-xs font-semibold">
                                        <i class="bi bi-exclamation-circle"></i> {{ __('Important') }}
                                    </span>
                                @endif
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $notice->published_at?->diffForHumans() ?? 'N/A' }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $notice->localized_title }}</h3>
                            <p class="text-gray-700 dark:text-gray-300 line-clamp-2">{{ $notice->localized_message }}</p>
                        </div>
                        <a href="{{ route('parent.notices.show', $notice->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-800 transition">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-12 text-center">
                <i class="bi bi-bell text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">{{ __('No notices available') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notices->hasPages())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            {{ $notices->links() }}
        </div>
    @endif
</div>
@endsection

