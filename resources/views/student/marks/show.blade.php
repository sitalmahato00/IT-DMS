@extends('student.layouts.studentlayout')

@section('title', ($subject->subject_name ?? __('Subject')) . ' - ' . __('Marks Details'))
@section('subtitle', __('Subject-wise marks breakdown'))

@section('content')
@php
    $assessmentPercentage = ($assessmentMarks->full ?? 0) > 0
        ? round((($assessmentMarks->obtained ?? 0) / $assessmentMarks->full) * 100, 2)
        : null;

    $ctevtPercentage = ($ctevtMarks && ($ctevtMarks->full ?? 0) > 0)
        ? round((($ctevtMarks->obtained ?? 0) / $ctevtMarks->full) * 100, 2)
        : null;

    $latestExam = $exams->first();

    $statusBadge = function ($status) {
        return match ($status) {
            true => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            false => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        };
    };

    $statusLabel = function ($status) {
        return match ($status) {
            true => __('Pass'),
            false => __('Fail'),
            default => __('Pending'),
        };
    };
@endphp

<div class="student-smooth-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <div class="student-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-white/15 blur-2xl"></div>
        <div class="absolute -left-8 -bottom-14 h-48 w-48 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('student.marks') }}" class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white/90 hover:bg-white/15 transition">
                    <i class="bi bi-arrow-left"></i> {{ __('Back to Results') }}
                </a>

                <h1 class="mt-4 text-2xl md:text-3xl font-bold">{{ $subject->subject_name }}</h1>
                <p class="mt-2 text-[#ffe5ea]">{{ __('Detailed exam performance for this subject.') }}</p>

                <div class="mt-4 flex flex-wrap gap-3 text-sm text-[#ffe5ea]">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-3 py-1">
                        <i class="bi bi-journal-bookmark"></i> {{ $subject->subject_code ?? __('N/A') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-3 py-1">
                        <i class="bi bi-layers"></i> {{ __('Semester') }} {{ $subject->semester ?? __('N/A') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-3 py-1">
                        <i class="bi bi-clipboard-data"></i> {{ $exams->count() }} {{ __('exam entries') }}
                    </span>
                </div>
            </div>

            <div class="hidden lg:flex h-24 w-24 items-center justify-center rounded-3xl border border-white/20 bg-white/10 shadow-lg">
                <i class="bi bi-bar-chart-line text-5xl text-white/90"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="student-smooth-card rounded-xl border border-red-200 bg-white p-5 dark:border-red-900 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">{{ __('Assessment Score') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ is_null($assessmentPercentage) ? '—' : $assessmentPercentage . '%' }}</p>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ ($assessmentMarks->full ?? 0) > 0 ? ($assessmentMarks->obtained . '/' . $assessmentMarks->full) : __('No assessment marks yet') }}
            </p>
        </div>

        <div class="student-smooth-card rounded-xl border border-blue-200 bg-white p-5 dark:border-blue-900 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">{{ __('CTEVT Score') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ is_null($ctevtPercentage) ? '—' : $ctevtPercentage . '%' }}</p>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ ($ctevtMarks && ($ctevtMarks->full ?? 0) > 0) ? ($ctevtMarks->obtained . '/' . $ctevtMarks->full) : __('No CTEVT marks yet') }}
            </p>
        </div>

        <div class="student-smooth-card rounded-xl border border-emerald-200 bg-white p-5 dark:border-emerald-900 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">{{ __('Teachers') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $teachers->count() }}</p>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Assigned to this subject') }}</p>
        </div>

        <div class="student-smooth-card rounded-xl border border-amber-200 bg-white p-5 dark:border-amber-900 dark:bg-gray-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">{{ __('Latest Exam') }}</p>
            <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ $latestExam->exam_name ?? __('Not available') }}</p>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ !empty($latestExam?->exam_date) ? \Carbon\Carbon::parse($latestExam->exam_date)->format('M d, Y') : __('Date not set') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-4 space-y-6">
            <div class="student-smooth-panel rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Subject Information') }}</h2>

                <div class="mt-4 space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Subject Name') }}</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $subject->subject_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Subject Code') }}</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $subject->subject_code ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Semester') }}</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $subject->semester ?? __('N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Course Category') }}</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $subject->category ?? ($subject->subject_name ?? __('N/A')) }}</p>
                    </div>
                </div>
            </div>

            <div class="student-smooth-panel rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Teachers') }}</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $teachers->count() }} {{ __('assigned') }}</span>
                </div>

                @if($teachers->isEmpty())
                    <div class="student-smooth-empty mt-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center dark:border-gray-700 dark:bg-gray-900/40">
                        <i class="bi bi-person-x text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('No teacher assignment found for this subject.') }}</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($teachers as $teacher)
                            <div class="student-smooth-list-card rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $teacher['name'] }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $teacher['role'] === 'primary' ? __('Primary Instructor') : ucfirst($teacher['role']) }}
                                        </p>
                                    </div>
                                    <i class="bi bi-person-badge text-xl text-red-600 dark:text-red-400"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="xl:col-span-8 space-y-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="student-smooth-panel rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Assessment Summary') }}</h2>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $statusBadge($assessmentMarks->is_pass ?? null) }}">
                            {{ $statusLabel($assessmentMarks->is_pass ?? null) }}
                        </span>
                    </div>

                    @if(($assessmentMarks->full ?? 0) > 0)
                        <div class="mt-5 grid grid-cols-2 gap-4">
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Obtained') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->obtained }}</p>
                            </div>
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Full Marks') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->full }}</p>
                            </div>
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Passing Marks') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->pass }}</p>
                            </div>
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Percentage') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentPercentage }}%</p>
                            </div>
                        </div>
                    @else
                        <div class="student-smooth-empty mt-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-900/40">
                            <i class="bi bi-clipboard-x text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('Assessment marks have not been published yet.') }}</p>
                        </div>
                    @endif
                </div>

                <div class="student-smooth-panel rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('CTEVT Summary') }}</h2>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $statusBadge($ctevtMarks->is_pass ?? null) }}">
                            {{ $statusLabel($ctevtMarks->is_pass ?? null) }}
                        </span>
                    </div>

                    @if($ctevtMarks && ($ctevtMarks->full ?? 0) > 0)
                        <div class="mt-5 grid grid-cols-2 gap-4">
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Obtained') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->obtained }}</p>
                            </div>
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Full Marks') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->full }}</p>
                            </div>
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Passing Marks') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->pass }}</p>
                            </div>
                            <div class="student-smooth-mini-card rounded-xl bg-gray-50 p-4 dark:bg-gray-900/40">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Percentage') }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtPercentage }}%</p>
                            </div>
                        </div>
                    @else
                        <div class="student-smooth-empty mt-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-900/40">
                            <i class="bi bi-clipboard-minus text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('CTEVT marks have not been published yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(!empty($componentMarks))
                <div class="student-smooth-panel rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Component Breakdown') }}</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('CTEVT components') }}</span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach($componentMarks as $component => $marks)
                            <div class="student-smooth-list-card rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $component }}</h3>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $statusBadge($marks->is_pass ?? null) }}">
                                        {{ $statusLabel($marks->is_pass ?? null) }}
                                    </span>
                                </div>

                                <div class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Obtained') }}</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $marks->obtained ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Full') }}</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $marks->full ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Pass') }}</span>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $marks->pass ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="student-smooth-table-card rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Exam History') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Recorded exams and marks for this subject.') }}</p>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $exams->count() }} {{ __('rows') }}</span>
                </div>

                @if($exams->isEmpty())
                    <div class="p-10 text-center">
                        <i class="bi bi-journal-x text-4xl text-gray-300 dark:text-gray-600"></i>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('No exam history yet') }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Exam entries for this subject will appear here once marks are available.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-700 dark:bg-slate-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Exam') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Category') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Date') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Score') }}</th>
                                    <th class="px-4 py-3 text-left font-semibold">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-slate-700 dark:bg-gray-800">
                                @foreach($exams as $exam)
                                    @php
                                        $examPercentage = ($exam->full_marks ?? 0) > 0
                                            ? round((($exam->marks_obtained ?? 0) / $exam->full_marks) * 100, 2)
                                            : null;
                                        $examPassed = ($exam->full_marks ?? 0) > 0
                                            ? ($exam->marks_obtained ?? 0) >= ($exam->passing_marks ?? 0)
                                            : null;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition dark:hover:bg-slate-700/40">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $exam->exam_name ?? __('Untitled Exam') }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($exam->exam_type ?? 'general') }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ ucfirst($exam->exam_category ?? 'general') }}</td>
                                        <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                                            {{ !empty($exam->exam_date) ? \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') : __('Not set') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ ($exam->marks_obtained ?? 0) . '/' . ($exam->full_marks ?? 0) }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ __('Pass Mark') }}: {{ $exam->passing_marks ?? 0 }}{{ is_null($examPercentage) ? '' : ' • ' . $examPercentage . '%' }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $statusBadge($examPassed) }}">
                                                {{ $statusLabel($examPassed) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

