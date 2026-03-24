@php
    $locale = app()->getLocale();

    $semesterSummary = ($subjects ?? collect())
        ->groupBy(fn ($s) => (string) ($s->semester ?? ''))
        ->map(function ($group) {
            return [
                'count' => $group->count(),
                'credits' => (int) $group->sum(fn ($s) => (float) ($s->credits ?? 0)),
            ];
        })
        ->sortKeys();

    $subjectPayload = ($subjects ?? collect())->map(function ($s) {
        $teachers = collect($s->teachers ?? [])
            ->map(fn ($t) => $t?->user?->name)
            ->filter()
            ->values()
            ->all();

        return [
            'id' => $s->id,
            'code' => $s->subject_code,
            'title' => $s->localized_name,
            'description' => $s->localized_description,
            'credits' => $s->credits,
            'semester' => (string) ($s->semester ?? ''),
            'prerequisite' => $s->prerequisite,
            'type' => $s->subject_type,
            'category' => $s->category,
            'teachers' => $teachers,
            'has_lab' => (bool) ($s->has_lab ?? false),
            'is_elective_open' => (bool) ($s->is_elective_open ?? false),
        ];
    })->values();
@endphp

@extends('layouts.public')

@push('head')
    @include('partials.public-page-theme')
@endpush

@section('content')
    <div class="brand-page-bg text-gray-900 dark:text-gray-100">
        <div class="brand-page-orb left-[-4rem] top-28 h-36 w-36 bg-red-300/55 dark:bg-red-500/20"></div>
        <div class="brand-page-orb right-[-3rem] top-64 h-44 w-44 bg-red-200/55 [animation-delay:1.2s] dark:bg-red-400/20"></div>
        <section class="brand-page-shell mx-auto px-4 py-12 sm:px-6 lg:px-8" x-data="subjectCatalog({
            initialSemester: @js($selectedSemester ?: 'all'),
            initialQuery: @js($initialQuery ?? ''),
            subjects: @js($subjectPayload),
            locale: @js($locale),
            indexUrl: @js(route('subjects.index')),
        })">
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
                        {{ $locale === 'ne' ? 'पाठ्यक्रम' : 'Curriculum' }}
                    </p>
                    <h1 class="brand-page-title mt-4 text-3xl font-bold text-gray-900 dark:text-gray-100 sm:text-5xl">
                        {{ $locale === 'ne' ? 'सबै विषय' : 'All Subjects' }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        {{ $locale === 'ne' ? 'विषय खोज्नुहोस् र विवरण/पूर्व-आवश्यकता हेर्नुहोस्।' : 'Search subjects and view details, prerequisites, and credits.' }}
                    </p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label class="sr-only" for="subjectSearch">{{ $locale === 'ne' ? 'विषय खोज' : 'Search subjects' }}</label>
                    <input id="subjectSearch" type="search" x-model.trim="query" placeholder="{{ $locale === 'ne' ? 'विषय/कोड खोज्नुहोस्…' : 'Search by subject or code…' }}"
                        class="brand-page-input w-full px-4 py-3 text-sm text-gray-900 dark:text-gray-100 sm:w-72" />

                    <label class="sr-only" for="semesterFilter">{{ $locale === 'ne' ? 'सेमेस्टर' : 'Semester' }}</label>
                    <select id="semesterFilter" x-model="semester"
                        class="brand-page-select w-full px-4 py-3 text-sm text-gray-900 dark:text-gray-100 sm:w-44">
                        <option value="all">{{ $locale === 'ne' ? 'सबै सेमेस्टर' : 'All semesters' }}</option>
                        @foreach ($semesterSummary as $sem => $meta)
                            @if (!empty($sem))
                                <option value="{{ $sem }}">{{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-10 grid gap-8 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <div class="brand-page-panel rounded-[2rem]">
                        <div class="border-b border-red-100 bg-gradient-to-r from-red-100 to-red-50 px-6 py-4 dark:border-red-900/30 dark:from-red-950/25 dark:to-slate-950/30">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $locale === 'ne' ? 'विषय सूची' : 'Subject catalog' }}
                                </div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="resultText"></div>
                            </div>
                        </div>

                        <div class="divide-y divide-red-100 dark:divide-red-900/20">
                            <template x-for="s in visibleSubjects" :key="s.id">
                                <div class="brand-page-soft px-6 py-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-100 dark:bg-slate-950 dark:text-red-200 dark:ring-red-900/30" x-text="s.code || '—'"></span>
                                                <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300" x-text="semesterLabel(s.semester)"></span>
                                                <template x-if="s.has_lab">
                                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $locale === 'ne' ? 'ल्याब' : 'Lab' }}</span>
                                                </template>
                                                <template x-if="s.is_elective_open">
                                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/40 dark:text-red-300">{{ $locale === 'ne' ? 'इलेक्टिभ' : 'Elective' }}</span>
                                                </template>
                                            </div>
                                            <div class="mt-2 truncate text-base font-semibold text-gray-900 dark:text-gray-100" x-text="s.title"></div>
                                            <div class="mt-1 line-clamp-2 text-sm text-gray-700 dark:text-gray-300" x-text="s.description || fallbackDescription"></div>
                                            <div class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-400" x-text="creditText(s.credits)"></div>
                                        </div>

                                        <div class="shrink-0">
                                            <button type="button" class="brand-page-cta inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-red-700 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-red-900/15 transition hover:-translate-y-0.5 hover:from-red-400 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-red-400"
                                                @click="toggle(s.id)">
                                                <span x-text="openId === s.id ? closeLabel : detailLabel"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="openId === s.id" x-transition class="mt-4 rounded-2xl border border-red-100 bg-gradient-to-br from-red-50 to-white p-4 text-sm text-gray-700 dark:border-red-900/30 dark:from-red-950/20 dark:to-slate-950/40 dark:text-gray-200">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'क्रेडिट' : 'Credits' }}</div>
                                                <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100" x-text="s.credits ?? '—'"></div>
                                            </div>
                                            <div>
                                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'पूर्व-आवश्यकता' : 'Prerequisite' }}</div>
                                                <div class="mt-1 font-semibold text-gray-900 dark:text-gray-100" x-text="s.prerequisite || '—'"></div>
                                            </div>
                                        </div>
                                        <template x-if="(s.teachers || []).length">
                                            <div class="mt-4">
                                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $locale === 'ne' ? 'शिक्षक' : 'Faculty' }}</div>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <template x-for="t in s.teachers" :key="t">
                                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-100 dark:bg-slate-950 dark:text-red-200 dark:ring-red-900/30" x-text="t"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div x-show="filteredSubjects.length === 0" class="px-6 py-10 text-center text-sm text-gray-600 dark:text-gray-300">
                                {{ $locale === 'ne' ? 'कुनै विषय भेटिएन।' : 'No subjects found.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="brand-page-panel rounded-[2rem] p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $locale === 'ne' ? 'सेमेस्टर अनुसार सारांश' : 'Semester-wise overview' }}
                        </h3>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                            @foreach ($semesterSummary as $sem => $meta)
                                @continue(empty($sem))
                                <div class="brand-page-card rounded-2xl p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $locale === 'ne' ? "{$sem} सेमेस्टर" : "Semester {$sem}" }}
                                        </div>
                                        <div class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-100 dark:bg-slate-950 dark:text-red-200 dark:ring-red-900/30">
                                            {{ $meta['count'] }} {{ $locale === 'ne' ? 'विषय' : 'subjects' }}
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $locale === 'ne' ? 'कुल क्रेडिट' : 'Total credits' }}: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $meta['credits'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('subjectCatalog', (config) => ({
                    locale: config.locale || 'en',
                    subjects: Array.isArray(config.subjects) ? config.subjects : [],
                    indexUrl: config.indexUrl || null,
                    maxItems: Number.isFinite(config.maxItems) ? Number(config.maxItems) : null,
                    query: config.initialQuery || '',
                    semester: config.initialSemester || 'all',
                    openId: null,
                    detailLabel: (config.locale === 'ne') ? 'विवरण' : 'Details',
                    closeLabel: (config.locale === 'ne') ? 'बन्द' : 'Close',
                    fallbackDescription: (config.locale === 'ne')
                        ? 'यस विषयको विवरण छिट्टै उपलब्ध हुनेछ।'
                        : 'Details for this subject will be available soon.',
                    get filteredSubjects() {
                        const q = (this.query || '').toLowerCase();
                        return this.subjects.filter((s) => {
                            if (this.semester !== 'all' && String(s.semester || '') !== String(this.semester)) return false;
                            if (!q) return true;
                            const hay = [s.code, s.title, s.description, s.category, s.type, (s.teachers || []).join(' ')]
                                .filter(Boolean).join(' ').toLowerCase();
                            return hay.includes(q);
                        });
                    },
                    get visibleSubjects() {
                        const max = this.maxItems;
                        if (!max || max < 1) return this.filteredSubjects;
                        return this.filteredSubjects.slice(0, max);
                    },
                    get showViewAll() {
                        const max = this.maxItems;
                        if (!this.indexUrl) return false;
                        if (!max || max < 1) return false;
                        return this.filteredSubjects.length > max;
                    },
                    get viewAllUrl() {
                        if (!this.indexUrl) return '#';
                        const params = [];
                        if (this.semester && this.semester !== 'all') {
                            params.push(`semester=${encodeURIComponent(this.semester)}`);
                        }
                        if (this.query) {
                            params.push(`q=${encodeURIComponent(this.query)}`);
                        }
                        return this.indexUrl + (params.length ? `?${params.join('&')}` : '');
                    },
                    get resultText() {
                        const total = this.filteredSubjects.length;
                        const shown = this.visibleSubjects.length;
                        if (this.showViewAll) {
                            return (this.locale === 'ne') ? `${shown} / ${total} (पूर्वावलोकन)` : `${shown} / ${total} (preview)`;
                        }
                        return (this.locale === 'ne') ? `${total} विषय` : `${total} subjects`;
                    },
                    semesterLabel(sem) {
                        const s = String(sem || '').trim();
                        if (!s) return (this.locale === 'ne') ? 'सेमेस्टर —' : 'Semester —';
                        return (this.locale === 'ne') ? `${s} सेमेस्टर` : `Semester ${s}`;
                    },
                    creditText(credits) {
                        const c = (credits ?? '—');
                        return (this.locale === 'ne') ? `क्रेडिट: ${c}` : `Credits: ${c}`;
                    },
                    toggle(id) {
                        this.openId = (this.openId === id) ? null : id;
                    },
                }));
            });
        </script>
    </div>
@endsection
