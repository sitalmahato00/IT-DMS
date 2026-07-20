@php
    $locale = app()->getLocale();
@endphp

@extends('layouts.public')

@push('head')
    @include('partials.public-page-theme')
@endpush

@section('content')
    <div class="brand-page-bg text-gray-900 dark:text-gray-100">
        <div class="brand-page-orb left-[-4rem] top-24 h-36 w-36 bg-red-300/55 dark:bg-red-500/20"></div>
        <div class="brand-page-orb right-[-2rem] top-72 h-44 w-44 bg-red-200/55 [animation-delay:1.1s] dark:bg-red-400/20"></div>

        <section class="brand-page-shell mx-auto px-4 py-12 sm:px-6 lg:px-8" x-data="{ open: false, notice: null }">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            {{ $locale === 'ne' ? '← गृहपृष्ठ' : '← Home' }}
                        </a>
                        <span class="text-xs font-medium text-gray-400 dark:text-gray-600">/</span>
                    </div>
                    <p class="brand-page-chip mt-5">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        {{ $locale === 'ne' ? 'सूचना' : 'Updates' }}
                    </p>
                    <h1 class="brand-page-title mt-4 text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-5xl">
                        {{ $locale === 'ne' ? 'सबै सूचना' : 'All Notices' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $locale === 'ne' ? 'विभागका सबै प्रकाशित सूचना र घोषणाहरू हेर्नुहोस्।' : 'Browse all published department notices and announcements.' }}
                    </p>
                </div>

                <form method="GET" action="{{ route('public.notices.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label class="sr-only" for="noticeSearch">{{ $locale === 'ne' ? 'सूचना खोज' : 'Search notices' }}</label>
                    <input id="noticeSearch" type="search" name="q" value="{{ $query }}"
                        placeholder="{{ $locale === 'ne' ? 'शीर्षक वा सामग्री खोज्नुहोस्…' : 'Search by title or content…' }}"
                        class="brand-page-input w-full px-4 py-3 text-sm text-gray-900 dark:text-gray-100 sm:w-80" />

                    <label class="sr-only" for="noticeAudience">{{ $locale === 'ne' ? 'समूह' : 'Audience' }}</label>
                    <select id="noticeAudience" name="audience" class="brand-page-select w-full px-4 py-3 text-sm text-gray-900 dark:text-gray-100 sm:w-48">
                        @foreach ($audienceOptions as $value => $label)
                            <option value="{{ $value }}" @selected($audience === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($notices as $n)
                    <button type="button"
                        class="landing-panel lift-card text-left rounded-[1.75rem] bg-gradient-to-br from-red-50 via-white to-red-100 p-6 focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/25 dark:to-slate-950"
                        @click="notice=@js([
                            'title' => $n->localized_title,
                            'date' => $n->formatted_date,
                            'audience' => $n->localized_audience_label,
                            'priority' => $n->localized_priority_label,
                            'full' => $n->localized_message,
                        ]); open=true;">
                        <div class="flex items-start justify-between gap-4 border-b border-red-100 pb-4 dark:border-red-900/25">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $n->formatted_date }}</div>
                                <div class="mt-2 line-clamp-2 text-base font-bold text-gray-900 dark:text-gray-100">{{ $n->localized_title }}</div>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                    {{ $n->localized_priority_label }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                            {{ \Illuminate\Support\Str::limit(strip_tags($n->localized_message), 160) }}
                        </div>
                        <div class="mt-5 flex items-center justify-between text-xs font-semibold text-gray-600 dark:text-gray-400">
                            <span>{{ $n->localized_audience_label }}</span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                <span>{{ $locale === 'ne' ? 'पढ्नुहोस्' : 'Read' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3">
                                    <path d="M13.207 3.793a1 1 0 0 1 1.414 0l6 6a1 1 0 0 1 0 1.414l-6 6a1 1 0 0 1-1.414-1.414L17.586 11H4a1 1 0 1 1 0-2h13.586l-4.379-4.379a1 1 0 0 1 0-1.414z"/>
                                </svg>
                            </span>
                        </div>
                    </button>
                @empty
                    <div class="brand-page-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-slate-950 dark:to-red-950/10 dark:text-gray-300 md:col-span-2 xl:col-span-3">
                        {{ $locale === 'ne' ? 'हाल कुनै सूचना छैन।' : 'No notices published yet.' }}
                    </div>
                @endforelse
            </div>

            @if ($notices->hasPages())
                <div class="mt-8">
                    {{ $notices->links() }}
                </div>
            @endif

            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true">
                <div class="absolute inset-0 bg-gray-950/70" @click="open=false" aria-hidden="true"></div>
                <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-gradient-to-br from-white via-red-50/80 to-red-100/60 shadow-xl ring-1 ring-red-100 dark:bg-gradient-to-br dark:from-slate-950/95 dark:via-red-950/20 dark:to-slate-950 dark:ring-red-900/30">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-800">
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400" x-text="notice?.date || ''"></div>
                            <div class="mt-1 truncate text-lg font-bold text-gray-900 dark:text-gray-100" x-text="notice?.title || ''"></div>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-900 dark:text-gray-200" x-text="notice?.audience || ''"></span>
                                <span class="rounded-full bg-red-50 px-3 py-1 text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="notice?.priority || ''"></span>
                            </div>
                        </div>
                        <button type="button" class="rounded-xl border border-gray-200 bg-white p-2 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800" @click="open=false" aria-label="{{ $locale === 'ne' ? 'बन्द गर्नुहोस्' : 'Close' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                <path d="M6.22 6.22a.75.75 0 0 1 1.06 0L12 10.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L13.06 12l4.72 4.72a.75.75 0 1 1-1.06 1.06L12 13.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L10.94 12 6.22 7.28a.75.75 0 0 1 0-1.06Z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-6">
                        <div class="prose prose-sm max-w-none text-gray-800 dark:prose-invert dark:text-gray-200" x-html="notice?.full || ''"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

