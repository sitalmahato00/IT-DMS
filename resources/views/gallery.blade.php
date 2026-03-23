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

@section('content')
    <div class="bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <header class="border-b border-gray-200/70 bg-white/90 backdrop-blur dark:border-gray-800/70 dark:bg-gray-950/75">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
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
                    <select id="localeSelect" name="locale" onchange="this.form.submit()" class="rounded-lg border-gray-300 bg-white text-sm text-gray-800 shadow-sm focus:border-red-500 focus:ring-red-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @foreach (config('locales.supported') as $code => $label)
                            <option value="{{ $code }}" @selected($code === $locale)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">{{ $title }}</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $subtitle }}</p>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ($filters as $key => $label)
                    @php
                        $isActive = ($category ?? 'all') === $key;
                        $count = $counts[$key] ?? null;
                    @endphp
                    <a href="{{ route('gallery.index', ['category' => $key]) }}"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset
                            {{ $isActive
                                ? 'bg-red-600 text-white ring-red-600'
                                : 'bg-white text-gray-800 ring-gray-200 hover:bg-gray-50 dark:bg-gray-950 dark:text-gray-100 dark:ring-gray-800 dark:hover:bg-gray-900' }}">
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
                    <div class="group relative overflow-hidden rounded-3xl border border-gray-200 bg-gray-100 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        @if ($g->image_url)
                            <img src="{{ $g->image_url }}" alt="{{ $g->title }}" class="h-44 w-full object-cover transition duration-300 group-hover:scale-105 sm:h-48">
                        @else
                            <div class="flex h-44 items-center justify-center text-sm text-gray-500 dark:text-gray-400 sm:h-48">
                                {{ $locale === 'ne' ? 'तस्विर छैन' : 'No image' }}
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-gray-950/70 via-gray-950/35 to-transparent p-4">
                            <div class="truncate text-sm font-semibold text-white">{{ $g->title }}</div>
                            <div class="mt-1 text-xs font-medium text-white/80">{{ $g->category_text }}</div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300 sm:col-span-3 lg:col-span-4">
                        {{ $locale === 'ne' ? 'हाल ग्यालरी सामग्री छैन।' : 'No gallery items available yet.' }}
                    </div>
                @endforelse
            </div>
        </main>
    </div>
@endsection

