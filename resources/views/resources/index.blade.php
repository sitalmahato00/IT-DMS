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

        <section class="brand-page-shell mx-auto px-4 py-12 sm:px-6 lg:px-8">
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
                        {{ $locale === 'ne' ? 'स्रोत' : 'Resources' }}
                    </p>
                    <h1 class="brand-page-title mt-4 text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-5xl">
                        {{ $locale === 'ne' ? 'सबै स्रोत' : 'All Resources' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $locale === 'ne' ? 'सार्वजनिक अध्ययन सामग्री, सिलेबस, नोट्स र गाइडहरू ब्राउज गर्नुहोस्।' : 'Browse public study materials, syllabi, notes, and guides.' }}
                    </p>
                </div>

                <form method="GET" action="{{ route('public.resources.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label class="sr-only" for="resourceSearch">{{ $locale === 'ne' ? 'स्रोत खोज' : 'Search resources' }}</label>
                    <input id="resourceSearch" type="search" name="q" value="{{ $query }}"
                        placeholder="{{ $locale === 'ne' ? 'शीर्षक वा विषय खोज्नुहोस्…' : 'Search by title or subject…' }}"
                        class="brand-page-input w-full px-4 py-3 text-sm text-gray-900 dark:text-gray-100 sm:w-80" />

                    <label class="sr-only" for="resourceType">{{ $locale === 'ne' ? 'प्रकार' : 'Type' }}</label>
                    <select id="resourceType" name="type" class="brand-page-select w-full px-4 py-3 text-sm text-gray-900 dark:text-gray-100 sm:w-56">
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($materials as $material)
                    <div class="landing-panel lift-card overflow-hidden rounded-[1.75rem] border-l-4 bg-gradient-to-br from-white via-red-50/80 to-red-100/60 transition duration-300 dark:bg-gradient-to-br dark:from-slate-950/90 dark:via-red-950/20 dark:to-slate-950 border-l-red-500 dark:border-l-red-400">
                        <div class="flex h-48 items-center justify-center bg-gradient-to-br from-red-50 to-red-100 dark:from-red-950/30 dark:to-red-900/20">
                            <div class="flex flex-col items-center justify-center gap-3 text-red-500 dark:text-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-20 w-20">
                                    <path d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 .621-.504 1.125-1.125 1.125H1.5V5.25a3.75 3.75 0 0 1 4.125-3.75Z" />
                                    <path fill-rule="evenodd" d="M23.25 7.5a.75.75 0 0 1-.75.75H.75a.75.75 0 0 1-.75-.75v-8a.75.75 0 0 1 .75-.75h22.5a.75.75 0 0 1 .75.75v8Zm-3-5.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M.75 14.25a.75.75 0 0 0 .75.75h22.5a.75.75 0 0 0 .75-.75v-6a.75.75 0 0 0-.75-.75H1.5a.75.75 0 0 0-.75.75v6Zm22.5 6a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 0-.75-.75H1.5a.75.75 0 0 0-.75.75v2a.75.75 0 0 0 .75.75h22.5Z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs font-semibold uppercase tracking-wide">{{ $material->localized_document_type_label }}</span>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                                    {{ $material->localized_document_type_label }}
                                </span>
                                @if ($material->formatted_size)
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $material->formatted_size }}</span>
                                @endif
                            </div>
                            <div class="mt-3 line-clamp-2 text-sm font-bold text-gray-900 dark:text-gray-100">{{ $material->localized_title }}</div>
                            @if ($material->subject?->localized_name)
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="font-medium text-gray-900 dark:text-gray-200">{{ $material->subject->localized_name }}</span>
                                </div>
                            @endif
                            @if ($material->localized_description)
                                <div class="mt-2 line-clamp-3 text-sm text-gray-700 dark:text-gray-300">{{ $material->localized_description }}</div>
                            @endif
                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('materials.download', ['id' => $material->id]) }}"
                                    class="brand-page-cta inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-700 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3.5 w-3.5">
                                        <path fill-rule="evenodd" d="M12 2a.75.75 0 0 1 .75.75v6.69l1.97-1.97a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L7.97 8.53a.75.75 0 0 1 1.06-1.06l1.97 1.97V2.75A.75.75 0 0 1 12 2zM3 14.25a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 14.25v-3.5a.75.75 0 0 0-1.5 0v3.5a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75v-3.5a.75.75 0 0 0-1.5 0v3.5z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="brand-page-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-slate-950 dark:to-red-950/10 dark:text-gray-300 sm:col-span-2 xl:col-span-3">
                        {{ $locale === 'ne' ? 'हाल कुनै स्रोत उपलब्ध छैन।' : 'No public resources available yet.' }}
                    </div>
                @endforelse
            </div>

            @if ($materials->hasPages())
                <div class="mt-8">
                    {{ $materials->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
