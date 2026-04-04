@extends('student.layouts.studentlayout')

@section('title', __('Notices'))

@section('content')
@php
    $locale = app()->getLocale();
@endphp

<div class="student-smooth-page space-y-6 @if($locale === 'ne') locale-ne @endif">
    <div class="student-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-10 -top-8 h-36 w-36 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-[#ffe5ea]">{{ __('Announcements') }}</p>
                <h1 class="mt-3 text-3xl font-bold">{{ __('Notices') }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-[#ffe5ea]">{{ __('Stay up to date with published notices, important announcements, and subject-specific updates.') }}</p>
            </div>

            <form method="GET" action="{{ route('student.notices') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="search" name="q" value="{{ $query }}" placeholder="{{ $locale === 'ne' ? 'शीर्षक वा सन्देश खोज्नुहोस्…' : 'Search title or message…' }}" class="w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-white/70 focus:outline-none focus:ring-2 focus:ring-white/30 sm:w-72">
                <select name="audience" style="color:#B2002F;" class="appearance-none rounded-2xl border border-white/20 bg-white px-4 py-3 pr-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/30">
                    @foreach($audienceOptions as $value => $label)
                        <option value="{{ $value }}" @selected($audience === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" style="color:#B2002F;" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold hover:bg-[#fff1f3] transition">{{ __('Filter') }}</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="student-smooth-panel rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Published') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $noticeStats['total'] }}</p>
        </div>
        <div class="student-smooth-panel rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Important') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $noticeStats['important'] }}</p>
        </div>
        <div class="student-smooth-panel rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('For Students') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $noticeStats['students'] }}</p>
        </div>
    </div>

    <div class="grid gap-4">
        @forelse($notices as $notice)
            <div class="student-smooth-panel rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700 dark:bg-red-950/20 dark:text-red-300">{{ $notice->localized_audience_label }}</span>
                            @if($notice->is_important)
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-950/20 dark:text-amber-300">{{ __('Important') }}</span>
                            @endif
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notice->formatted_date }}</span>
                        </div>

                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $notice->localized_title }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-7">{{ \Illuminate\Support\Str::limit($notice->localized_message, 220) }}</p>

                        <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                            @if($notice->subject)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 dark:bg-gray-700">
                                    <i class="bi bi-book"></i> {{ $notice->subject->subject_name ?? __('Subject Notice') }}
                                </span>
                            @endif
                            @if($notice->creator?->name)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 dark:bg-gray-700">
                                    <i class="bi bi-person"></i> {{ $notice->creator->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col items-start gap-3 lg:items-end">
                        @if(!empty($notice->file_path))
                            <a href="{{ route('student.notices.show', $notice->id) }}" class="inline-flex items-center gap-2 rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-red-900/15 transition hover:bg-red-700">
                                <i class="bi bi-arrow-right"></i>
                                {{ __('View details') }}
                            </a>
                        @else
                            <a href="{{ route('student.notices.show', $notice->id) }}" class="inline-flex items-center gap-2 rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-red-900/15 transition hover:bg-red-700">
                                <i class="bi bi-arrow-right"></i>
                                {{ __('Open notice') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="student-smooth-empty rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-600 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                {{ $locale === 'ne' ? 'हाल कुनै नोटिस उपलब्ध छैन।' : 'No notices available yet.' }}
            </div>
        @endforelse
    </div>

    @if($notices->hasPages())
        <div class="student-smooth-panel rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
            {{ $notices->links() }}
        </div>
    @endif
</div>
@endsection
