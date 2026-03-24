@php
    $locale = app()->getLocale();

    $title = $locale === 'ne' ? 'ग्यालरी' : 'Gallery';
    $subtitle = $locale === 'ne' ? 'विभागका गतिविधि र सुविधाका झलकहरू।' : 'Highlights from department events and facilities.';

    $filters = [
        'all' => $locale === 'ne' ? 'सबै' : 'All',
        'campus' => $locale === 'ne' ? 'क्याम्पस' : 'Campus',
        'events' => $locale === 'ne' ? 'कार्यक्रम' : 'Events',
        'activities' => $locale === 'ne' ? 'गतिविधि' : 'Activities',
        'students' => $locale === 'ne' ? 'विद्यार्थी' : 'Students',
        'faculty' => $locale === 'ne' ? 'शिक्षक' : 'Faculty',
        'facilities' => $locale === 'ne' ? 'सुविधा' : 'Facilities',
    ];
@endphp

@extends('layouts.public')

@push('head')
    @include('partials.public-page-theme')
@endpush

@section('content')
    <div class="brand-page-bg text-gray-900 dark:text-gray-100">
        <div class="brand-page-orb left-[-4rem] top-24 h-36 w-36 bg-red-300/55 dark:bg-red-500/20"></div>
        <div class="brand-page-orb right-[-2rem] top-64 h-44 w-44 bg-red-200/55 [animation-delay:1.1s] dark:bg-red-400/20"></div>
        <header class="border-b border-red-100/70 bg-white/80 backdrop-blur dark:border-red-900/20 dark:bg-slate-950/75">
            <div class="brand-page-shell mx-auto flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <span aria-hidden="true">←</span>
                        {{ $locale === 'ne' ? 'मुखपृष्ठ' : 'Home' }}
                    </a>
                    <span class="text-sm text-gray-300 dark:text-gray-700">|</span>
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</div>
                </div>

                <form method="POST" action="{{ route('language.switch') }}" class="hidden sm:block">
                    @csrf
                    <label class="sr-only" for="localeSelect">{{ $locale === 'ne' ? 'भाषा' : 'Language' }}</label>
                    <select id="localeSelect" name="locale" onchange="this.form.submit()" class="brand-page-select px-4 py-2.5 text-sm text-gray-800 dark:text-gray-100">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </header>

        <main class="brand-page-shell mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div>
                <p class="brand-page-chip">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                    {{ $locale === 'ne' ? 'ग्यालरी' : 'Gallery' }}
                </p>
                <h1 class="brand-page-title mt-4 text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-5xl">{{ $title }}</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $subtitle }}</p>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ($filters as $key => $label)
                    @php
                        $isActive = ($category ?? 'all') === $key;
                        $count = $counts[$key] ?? null;
                    @endphp
                    <a href="{{ route('gallery.index', ['category' => $key]) }}"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset shadow-sm transition hover:-translate-y-0.5
                            {{ $isActive
                                ? 'bg-gradient-to-r from-red-500 to-red-700 text-white ring-red-600'
                                : 'bg-white/90 text-gray-800 ring-red-100 hover:bg-white dark:bg-slate-950/80 dark:text-gray-100 dark:ring-red-900/30 dark:hover:bg-slate-900' }}">
                        <span>{{ $label }}</span>
                        @if (!is_null($count))
                            <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-bold {{ $isActive ? 'text-white' : 'text-gray-600 dark:text-gray-300' }}">
                                {{ $count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @forelse (($galleryItems ?? collect()) as $g)
                    <div class="brand-page-card group relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-red-50 via-white to-red-100 dark:bg-gradient-to-br dark:from-slate-950/90 dark:via-red-950/20 dark:to-slate-950">
                        @if ($g->image_url)
                            <img src="{{ $g->image_url }}" alt="{{ $g->title }}" class="h-44 w-full object-cover transition duration-300 group-hover:scale-105 sm:h-48">
                        @else
                            <div class="flex h-44 items-center justify-center text-sm text-gray-500 dark:text-gray-400 sm:h-48">
                                {{ $locale === 'ne' ? 'तस्विर छैन' : 'No image' }}
                            </div>
                        @endif
                        @if ($g->image_url)
                            <div class="pointer-events-none absolute inset-0 opacity-0 transition group-hover:opacity-100">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-950/65 via-gray-950/10 to-transparent"></div>
                                <div class="pointer-events-auto absolute right-3 top-3 flex gap-2">
                                    <a href="{{ $g->image_url }}" target="_blank" rel="noopener"
                                       class="brand-page-panel inline-flex items-center justify-center rounded-xl bg-white/90 px-3 py-2 text-xs font-semibold text-gray-900">
                                        {{ $locale === 'ne' ? 'हेर्नुहोस्' : 'View' }}
                                    </a>
                                    <a href="{{ route('gallery.download', ['id' => $g->id]) }}"
                                       class="brand-page-cta inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-700 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                                    </a>
                                </div>
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-950/70 via-gray-950/35 to-transparent p-4">
                            <div class="truncate text-sm font-semibold text-white">{{ $g->title }}</div>
                            <div class="mt-1 text-xs font-medium text-white/80">{{ $g->category_text }}</div>
                        </div>
                    </div>
                @empty
                    <div class="brand-page-panel rounded-[1.75rem] border-dashed border-red-200 bg-gradient-to-br from-white to-red-50 p-10 text-center text-sm text-gray-600 dark:border-red-900/40 dark:bg-gradient-to-br dark:from-slate-950 dark:to-red-950/10 dark:text-gray-300 sm:col-span-3 lg:col-span-4">
                        {{ $locale === 'ne' ? 'हाल ग्यालरी सामग्री छैन।' : 'No gallery items available yet.' }}
                    </div>
                @endforelse
            </div>
        </main>
    </div>
@endsection
