@php
    $locale = app()->getLocale();
@endphp

@extends('student.layouts.studentlayout')

@section('title', __('Study Materials'))

@section('content')
    <div class="student-smooth-page space-y-6">
        <div class="student-smooth-panel rounded-3xl border border-red-200 bg-white dark:border-red-900 dark:bg-slate-800 p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.32em] text-red-600 dark:text-red-400 font-semibold">{{ __('Resources') }}</p>
                    <h1 class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ __('Study Materials') }}</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $locale === 'ne' ? 'पाठ्यपुस्तक, नोट्स, मूल्यांकन र प्रोजेक्ट सामग्रीहरू ब्राउज गर्नुहोस्।' : 'Browse lecture notes, assignments, syllabus, and student resources.' }}</p>
                </div>

                <form method="GET" action="{{ route('student.study-materials') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <label class="sr-only" for="resourceSearch">{{ $locale === 'ne' ? 'स्रोत खोज' : 'Search resources' }}</label>
                    <input id="resourceSearch" type="search" name="q" value="{{ $query }}"
                        placeholder="{{ $locale === 'ne' ? 'शीर्षक वा विषय खोज्नुहोस्…' : 'Search by title or subject…' }}"
                        class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-red-400 dark:focus:ring-red-500/20" />

                    <label class="sr-only" for="resourceType">{{ $locale === 'ne' ? 'प्रकार' : 'Type' }}</label>
                    <select id="resourceType" name="type" class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:focus:border-red-400 dark:focus:ring-red-500/20">
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @php
            $materialStats = $materialStats ?? ['total' => 0, 'lecture_notes' => 0, 'assignment' => 0, 'syllabus' => 0];
        @endphp

        <div class="grid gap-4 md:grid-cols-4">
            <div class="student-smooth-card rounded-3xl border border-red-200 bg-white p-5 shadow-sm dark:border-red-900 dark:bg-slate-800">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Total Files') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $materialStats['total'] }}</p>
            </div>
            <div class="student-smooth-card rounded-3xl border border-blue-200 bg-white p-5 shadow-sm dark:border-blue-900 dark:bg-slate-800">
                <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Lecture Notes') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $materialStats['lecture_notes'] }}</p>
            </div>
            <div class="student-smooth-card rounded-3xl border border-emerald-200 bg-white p-5 shadow-sm dark:border-emerald-900 dark:bg-slate-800">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Assignments') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $materialStats['assignment'] }}</p>
            </div>
            <div class="student-smooth-card rounded-3xl border border-purple-200 bg-white p-5 shadow-sm dark:border-purple-900 dark:bg-slate-800">
                <p class="text-xs uppercase tracking-wide text-purple-700 dark:text-purple-300 font-semibold">{{ __('Syllabus') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $materialStats['syllabus'] }}</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($materials as $material)
                <div class="student-smooth-card rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-red-700 dark:bg-red-950/20 dark:text-red-300">{{ $material->localized_document_type_label }}</span>
                            @if ($material->formatted_size)
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $material->formatted_size }}</span>
                            @endif
                        </div>

                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $material->localized_title }}</h2>

                        @if ($material->subject?->localized_name)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $material->subject->localized_name }}</p>
                        @endif

                        @if ($material->localized_description)
                            <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3">{{ $material->localized_description }}</p>
                        @endif

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <a href="{{ route('student.study-materials.download', ['id' => $material->id]) }}" class="inline-flex items-center gap-2 rounded-2xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                <i class="bi bi-download"></i>
                                {{ $locale === 'ne' ? 'डाउनलोड' : 'Download' }}
                            </a>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $material->created_at?->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="student-smooth-card col-span-full rounded-3xl border border-dashed border-red-200 bg-red-50/50 p-10 text-center text-sm text-gray-600 dark:border-red-900 dark:bg-slate-900/60 dark:text-gray-300">
                    {{ $locale === 'ne' ? 'हाल कुनै अध्ययन सामग्री उपलब्ध छैन।' : 'No study materials available yet.' }}
                </div>
            @endforelse
        </div>

        @if ($materials->hasPages())
            <div class="student-smooth-card rounded-3xl border border-gray-200 bg-white p-5 text-center dark:border-slate-700 dark:bg-slate-800">
                {{ $materials->links() }}
            </div>
        @endif
    </div>
@endsection
