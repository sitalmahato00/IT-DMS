@extends('parent.layouts.parentlayout')

@section('title', __('Event Details'))

@section('content')
<div class="space-y-6">
    <!-- Page Header with Back Button -->
    <div>
        <a href="{{ route('parent.events.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
            <i class="bi bi-arrow-left"></i>
            {{ __('Back to Events') }}
        </a>
    </div>

    <!-- Event Banner -->
    @if($event->image_path)
        <div class="relative h-96 rounded-xl overflow-hidden shadow-lg">
            <img src="{{ \App\Support\Media::publicUrl($event->image_path) }}" alt="{{ $event->title ?? 'Event' }}" class="w-full h-full object-cover">
        </div>
    @endif

    <!-- Event Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $event->title ?? 'N/A' }}</h1>

        <!-- Meta Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
            @if($event->event_date)
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400 flex-shrink-0">
                        <i class="bi bi-calendar"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Date') }}</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $event->event_date->format('F d, Y') }}</p>
                    </div>
                </div>
            @endif

            @if($event->location)
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400 flex-shrink-0">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Location') }}</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $event->location }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Description -->
        <div class="prose prose-lg dark:prose-invert max-w-none">
            {!! nl2br(e($event->description ?? '')) !!}
        </div>
    </div>
</div>
@endsection
