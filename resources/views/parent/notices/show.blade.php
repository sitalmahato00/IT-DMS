@extends('parent.layouts.parentlayout')

@section('title', __('Notice'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('parent.notices.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
            <i class="bi bi-arrow-left"></i>
            {{ __('Back to Notices') }}
        </a>
    </div>

    <!-- Notice Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 dark:from-amber-800 dark:to-amber-900 p-8 text-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    @if($notice->is_important)
                        <span class="inline-block px-3 py-1 bg-white bg-opacity-30 text-white rounded-full text-sm font-semibold mb-4">
                            <i class="bi bi-exclamation-circle"></i> {{ __('Important') }}
                        </span>
                    @endif
                    <h1 class="text-3xl font-bold mb-2">{{ $notice->localized_title }}</h1>
                    <p class="text-amber-100">{{ __('Published: ') }}{{ $notice->published_at?->format('Y-m-d H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-8 prose prose-lg dark:prose-invert max-w-none">
            {!! nl2br(e($notice->localized_message)) !!}
        </div>

        <!-- File Attachment -->
        @if($notice->file_path)
            <div class="px-8 pb-8">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-file-earmark text-2xl text-amber-600 dark:text-amber-400"></i>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $notice->file_name ?? 'Document' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Attached document') }}</p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $notice->file_path) }}" download class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700 text-white rounded-lg font-medium transition">
                        <i class="bi bi-download"></i>
                        {{ __('Download') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
