@extends('parent.layouts.parentlayout')

@section('title', __('Events'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('College Events') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Stay informed about upcoming events') }}</p>
        </div>
    </div>

    <!-- Events List -->
    <div class="space-y-6">
        @forelse($events as $event)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-0">
                    <!-- Image -->
                    @if($event->image_path)
                        <div class="md:col-span-1 h-48 md:h-auto bg-gray-200 dark:bg-gray-700 overflow-hidden">
                            <img src="{{ \App\Support\Media::publicUrl($event->image_path) }}" alt="{{ $event->title ?? 'Event' }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="md:col-span-3 p-6">
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $event->title ?? 'N/A' }}</h3>
                                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                    @if($event->event_date)
                                        <span><i class="bi bi-calendar"></i> {{ $event->event_date->format('M d, Y') }}</span>
                                    @endif
                                    @if($event->location)
                                        <span><i class="bi bi-geo-alt"></i> {{ $event->location }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <p class="text-gray-700 dark:text-gray-300 line-clamp-2 mb-4">{{ Str::limit($event->description ?? '', 150) }}</p>

                        <a href="{{ route('parent.events.show', $event->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700 text-white rounded-lg font-medium transition">
                            {{ __('View Details') }}
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-12 text-center">
                <i class="bi bi-calendar text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg">{{ __('No events available') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
