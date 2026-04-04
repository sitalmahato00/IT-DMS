@extends('student.layouts.studentlayout')

@section('title', $subject->localized_name . ' - ' . __('Course Details'))

@section('content')
@php
    $attendancePercent = (float) $attendancePercentage;
    $assessmentPercent = (($assessmentMarks->full ?? 0) > 0)
        ? round(((float) $assessmentMarks->obtained / (float) $assessmentMarks->full) * 100, 1)
        : 0;
    $ctevtPercent = (float) ($ctevtMarks->percentage ?? 0);

    $assessmentStatus = match (true) {
        ($assessmentMarks->full ?? 0) <= 0 => __('N/A'),
        $assessmentMarks->is_pass === true => __('Pass'),
        $assessmentMarks->is_pass === false => __('Fail'),
        default => __('N/A'),
    };

    $ctevtStatus = match (true) {
        ($ctevtMarks->full ?? 0) <= 0 => __('N/A'),
        $ctevtMarks->is_pass === true => __('Pass'),
        $ctevtMarks->is_pass === false => __('Fail'),
        default => __('N/A'),
    };

    $statusStyles = [
        'present' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300',
        'absent' => 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-300',
        'leave' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-300',
        'default' => 'bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-300',
    ];

    $statusBorder = [
        'present' => 'border-emerald-400',
        'absent' => 'border-rose-400',
        'leave' => 'border-amber-400',
        'default' => 'border-slate-300',
    ];
@endphp

<div class="student-smooth-page space-y-6">
    <div class="student-smooth-hero relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-12 -top-12 h-44 w-44 rounded-full bg-white/20 blur-3xl"></div>
        <div class="absolute -left-10 -bottom-16 h-56 w-56 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-wide text-white/90">
                    <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1">{{ $subject->subject_code }}</span>
                    <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1">{{ $subject->formatted_semester ?: ($subject->semester ?? __('N/A')) }}</span>
                    <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1">{{ ucfirst($subject->subject_type ?? 'core') }}</span>
                    @if($subject->has_lab)
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1">{{ __('Lab Enabled') }}</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-bold leading-tight md:text-4xl">{{ $subject->localized_name }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/85 md:text-base">
                    {{ $subject->localized_description ?: __('A subject dashboard with attendance, marks, syllabus notes, and uploaded course documents.') }}
                </p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('student.courses') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2.5 text-sm font-semibold shadow-lg shadow-black/10 transition hover:-translate-y-0.5" style="color:#B2002F;">
                        <i class="bi bi-arrow-left" style="color:#B2002F;"></i>
                        <span>{{ __('Back to Courses') }}</span>
                    </a>
                    <a href="{{ route('student.attendance.show', $subject->id) }}" class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/15">
                        <i class="bi bi-calendar-check"></i>
                        <span>{{ __('Attendance View') }}</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <div class="rounded-3xl border border-white/15 bg-white/10 p-4 shadow-lg backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">{{ __('Attendance') }}</p>
                    <p class="mt-2 text-3xl font-bold">{{ number_format($attendancePercent, 1) }}%</p>
                    <p class="mt-1 text-xs text-white/70">{{ __('Overall subject attendance') }}</p>
                </div>
                <div class="rounded-3xl border border-white/15 bg-white/10 p-4 shadow-lg backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">{{ __('Syllabus') }}</p>
                    <p class="mt-2 text-3xl font-bold">{{ $syllabusLines->count() }}</p>
                    <p class="mt-1 text-xs text-white/70">{{ __('Topics listed') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 space-y-6">
            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Course Information') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Core details pulled from the subject record') }}</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/30 dark:text-red-300">
                        <i class="bi bi-journal-bookmark"></i>
                        {{ __('Subject overview') }}
                    </span>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Semester') }}</p>
                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->formatted_semester ?: ($subject->semester ?? __('N/A')) }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Course Category') }}</p>
                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->course ?: __('N/A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Credits') }}</p>
                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->credits ?? __('N/A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Lab') }}</p>
                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->has_lab ? __('Enabled') : __('Not enabled') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40 sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                        <p class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-200">
                            {{ $subject->localized_description ?: __('No description available.') }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Performance Snapshot') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Attendance and marks visualized in a clean subject summary.') }}</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950/30 dark:text-red-300">
                        <i class="bi bi-graph-up"></i>
                        {{ __('Live analytics') }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Attendance Mix') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Present, absent, and leave status') }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                                {{ $attendanceTotal }} {{ __('records') }}
                            </span>
                        </div>
                        <div class="h-72">
                            <canvas id="attendanceStatusChart"></canvas>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Marks Comparison') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Obtained marks against full marks') }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gray-600 shadow-sm dark:bg-gray-800 dark:text-gray-300">
                                {{ __('Assessment') }} + {{ __('CTEVT') }}
                            </span>
                        </div>
                        <div class="h-72">
                            <canvas id="marksComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Syllabus and Learning Plan') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('The syllabus comes directly from the subject table.') }}</p>
                    </div>
                    @if($syllabusDocumentUrl)
                        <a href="{{ $syllabusDocumentUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-red-900/15 transition hover:bg-red-700">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                            <span>{{ __('Download Syllabus File') }}</span>
                        </a>
                    @endif
                </div>

                @if($syllabusLines->isNotEmpty())
                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        @foreach($syllabusLines as $item)
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300">
                                        <i class="bi bi-check2"></i>
                                    </span>
                                    <span class="leading-6">{{ $item }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($syllabusDocumentUrl)
                    <div class="mt-5 rounded-2xl border border-dashed border-red-200 bg-red-50/60 p-5 text-sm text-red-800 dark:border-red-900/40 dark:bg-red-950/20 dark:text-red-200">
                        {{ __('The syllabus is available as a file. Open the download button above to review the full document.') }}
                    </div>
                @else
                    <div class="mt-5 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-400">
                        {{ __('No syllabus text or uploaded syllabus file has been added yet.') }}
                    </div>
                @endif

                @if($learningObjectives->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Learning Objectives') }}</h3>
                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            @foreach($learningObjectives as $objective)
                                <div class="rounded-2xl border border-red-100 bg-red-50/70 p-4 text-sm text-gray-700 shadow-sm dark:border-red-900/30 dark:bg-red-950/20 dark:text-gray-200">
                                    <div class="flex items-start gap-3">
                                        <i class="bi bi-bullseye mt-0.5 text-red-600 dark:text-red-300"></i>
                                        <span class="leading-6">{{ $objective }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <section class="student-smooth-table-card rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Recent Attendance Records') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Latest class entries for this subject') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300">{{ __('Present') }}: {{ $attendanceCounts['present'] }}</span>
                            <span class="rounded-full bg-rose-50 px-3 py-1 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300">{{ __('Absent') }}: {{ $attendanceCounts['absent'] }}</span>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300">{{ __('Leave') }}: {{ $attendanceCounts['leave'] }}</span>
                        </div>
                    </div>
                </div>

                @if($recentAttendanceRecords->isEmpty())
                    <div class="p-8 text-center">
                        <i class="bi bi-calendar-x text-4xl text-gray-400"></i>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No attendance records found for this subject yet.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Date') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Time In') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Time Out') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                @foreach($recentAttendanceRecords as $record)
                                    @php
                                        $status = strtolower((string) $record->status);
                                        $statusClass = $statusStyles[$status] ?? $statusStyles['default'];
                                        $borderClass = $statusBorder[$status] ?? $statusBorder['default'];
                                        $displayStatus = match ($status) {
                                            'present' => __('Present'),
                                            'absent' => __('Absent'),
                                            'leave' => __('Leave'),
                                            default => ucfirst($status ?: __('Unknown')),
                                        };
                                        $displayDate = !empty($record->date_bs)
                                            ? $record->date_bs
                                            : \Carbon\Carbon::parse($record->date)->format('M d, Y');
                                    @endphp
                                    <tr class="border-l-4 {{ $borderClass }} hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            <div class="font-medium">{{ $displayDate }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $record->subject_name }} ({{ $record->subject_code }})</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $record->time_in ?? '—' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $record->time_out ?? '—' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ $displayStatus }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="xl:col-span-4 space-y-6">
            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Instructors') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('People teaching this subject') }}</p>
                    </div>
                    <i class="bi bi-people text-2xl text-red-600 dark:text-red-300"></i>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($teachers as $teacher)
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $teacher['name'] }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $teacher['role'] === 'primary' ? __('Primary Instructor') : ucfirst($teacher['role']) }}
                            </p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-400">
                            {{ __('No instructors assigned yet.') }}
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Marks Summary') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Latest published marks for the subject') }}</p>
                    </div>
                    <i class="bi bi-clipboard-data text-2xl text-red-600 dark:text-red-300"></i>
                </div>

                <div class="mt-5 space-y-4">
                    <div class="rounded-2xl border border-red-100 bg-red-50/70 p-4 dark:border-red-900/30 dark:bg-red-950/20">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">{{ __('Assessment') }}</p>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $assessmentStatus }}</p>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($assessmentPercent, 1) }}%</p>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-white/80 dark:bg-gray-800/70">
                            <div class="h-2 rounded-full bg-red-600" style="width: {{ min(100, max(0, $assessmentPercent)) }}%"></div>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ $assessmentMarks->obtained ?? 0 }} / {{ $assessmentMarks->full ?? 0 }} {{ __('marks') }}</p>
                    </div>

                    <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 dark:border-blue-900/30 dark:bg-blue-950/20">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 dark:text-blue-300">{{ __('CTEVT') }}</p>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $ctevtStatus }}</p>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($ctevtPercent, 1) }}%</p>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-white/80 dark:bg-gray-800/70">
                            <div class="h-2 rounded-full bg-blue-600" style="width: {{ min(100, max(0, $ctevtPercent)) }}%"></div>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ $ctevtMarks->obtained ?? 0 }} / {{ $ctevtMarks->full ?? 0 }} {{ __('marks') }}</p>
                    </div>
                </div>
            </section>

            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Quick Facts') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Important subject details at a glance') }}</p>
                    </div>
                    <i class="bi bi-info-circle text-2xl text-red-600 dark:text-red-300"></i>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    @foreach($subjectHighlights as $highlight)
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <div class="flex items-start gap-3">
                                <div class="rounded-2xl bg-white p-2 text-red-600 shadow-sm dark:bg-gray-800 dark:text-red-300">
                                    <i class="bi {{ $highlight['icon'] }}"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $highlight['label'] }}</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $highlight['value'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($subject->remarks)
                    <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900/30 dark:bg-amber-950/20 dark:text-amber-100">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-chat-square-text mt-0.5"></i>
                            <span class="leading-6">{{ $subject->remarks }}</span>
                        </div>
                    </div>
                @endif
            </section>

            <section class="student-smooth-panel rounded-[1.75rem] border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Attendance Summary') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('How the subject is trending') }}</p>
                    </div>
                    <i class="bi bi-calendar-check text-2xl text-red-600 dark:text-red-300"></i>
                </div>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 text-center dark:border-emerald-900/30 dark:bg-emerald-950/20">
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $attendanceCounts['present'] }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Present') }}</p>
                    </div>
                    <div class="rounded-2xl border border-rose-100 bg-rose-50/70 p-4 text-center dark:border-rose-900/30 dark:bg-rose-950/20">
                        <p class="text-2xl font-bold text-rose-700 dark:text-rose-300">{{ $attendanceCounts['absent'] }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Absent') }}</p>
                    </div>
                    <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4 text-center dark:border-amber-900/30 dark:bg-amber-950/20">
                        <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $attendanceCounts['leave'] }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Leave') }}</p>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($attendancePercent, 1) }}%</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Attendance percentage calculated from the subject class records.') }}</p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const attendanceData = @json($attendanceChart);
    const marksData = @json($marksChart);
    const isDark = document.documentElement.classList.contains('dark');
    const axisColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.18)';
    const legendColor = isDark ? '#e2e8f0' : '#334155';

    const attendanceCanvas = document.getElementById('attendanceStatusChart');
    if (attendanceCanvas && window.Chart) {
        new Chart(attendanceCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: attendanceData.labels,
                datasets: [{
                    data: attendanceData.values,
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: legendColor,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            padding: 18,
                        },
                    },
                }
            }
        });
    }

    const marksCanvas = document.getElementById('marksComparisonChart');
    if (marksCanvas && window.Chart) {
        new Chart(marksCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: marksData.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: @json(__('Obtained')),
                        data: marksData.obtained,
                        backgroundColor: '#e11d48',
                        borderRadius: 12,
                    },
                    {
                        type: 'bar',
                        label: @json(__('Full Marks')),
                        data: marksData.full,
                        backgroundColor: '#cbd5e1',
                        borderRadius: 12,
                    },
                    {
                        type: 'line',
                        label: @json(__('Passing Marks')),
                        data: marksData.pass,
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b',
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: axisColor },
                        grid: { color: gridColor },
                    },
                    x: {
                        ticks: { color: axisColor },
                        grid: { display: false },
                    },
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: legendColor,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            padding: 18,
                        },
                    },
                }
            }
        });
    }
});
</script>
@endsection
