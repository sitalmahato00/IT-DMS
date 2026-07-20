@extends('student.layouts.studentlayout')

@section('title', __('Notice Details'))

@section('content')
<div class="student-smooth-page space-y-6">
    <div class="student-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="relative flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <a href="{{ route('student.notices') }}" class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white/90 hover:bg-white/15 transition">
                    <i class="bi bi-arrow-left"></i> {{ __('Back to Notices') }}
                </a>
                <h1 class="mt-4 text-3xl font-bold">{{ $notice->localized_title }}</h1>
                <p class="mt-2 text-sm text-[#ffe5ea]">{{ $notice->localized_audience_label }} • {{ $notice->formatted_date }}</p>
            </div>
        </div>
    </div>

    <div class="student-smooth-panel rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700 dark:bg-red-950/20 dark:text-red-300">{{ $notice->localized_audience_label }}</span>
            @if($notice->is_important)
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-950/20 dark:text-amber-300">{{ __('Important') }}</span>
            @endif
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notice->formatted_date }}</span>
        </div>

        <div class="prose max-w-none text-gray-700 dark:prose-invert dark:text-gray-300">
            {!! nl2br(e($notice->localized_message)) !!}
        </div>

        <div class="mt-6 grid gap-3 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Audience') }}</p>
                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $notice->localized_audience_label }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Created By') }}</p>
                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $notice->creator?->name ?? __('System') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Subject') }}</p>
                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $notice->subject?->subject_name ?? __('General') }}</p>
            </div>
        </div>

        @if(!empty($notice->file_path))
            <div class="mt-6">
                <a href="{{ Storage::disk('public')->url($notice->file_path) }}" class="inline-flex items-center gap-2 rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" target="_blank" rel="noreferrer">
                    <i class="bi bi-download"></i>
                    {{ __('Open attachment') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

