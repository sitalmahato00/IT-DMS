@extends('student.layouts.studentlayout')

@section('title', __('Marks / Results'))
@section('subtitle', __('Academic Performance Overview'))

@section('content')
@php
    $gradedSubjects = $subjects->filter(fn ($subject) => !is_null($subject['percentage'] ?? null));
    $passedCount = $subjects->where('status', 'pass')->count();
    $failedCount = $subjects->where('status', 'fail')->count();
    $pendingCount = $subjects->where('status', 'pending')->count();
    $passRate = $gradedSubjects->count() > 0 ? round(($passedCount / $gradedSubjects->count()) * 100, 1) : null;
    $topSubjects = $gradedSubjects->sortByDesc('percentage')->take(4)->values();
@endphp

<div class="student-smooth-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif" id="studentMarksApp">
    <div class="student-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/20 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-16 w-56 h-56 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">{{ __('Marks / Results') }}</h1>
                <p class="text-[#ffe5ea] mt-2">{{ __('Review subject-wise marks, pass status, and overall academic standing.') }}</p>
                <div class="mt-3 flex flex-wrap gap-3 text-sm text-[#ffe5ea]">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-journal-check"></i> {{ $subjectCount }} {{ __('graded subjects') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-graph-up"></i> {{ $overallPercentage }}% {{ __('overall') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-award"></i> {{ number_format($cgpa, 2) }} {{ __('CGPA') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-trophy"></i> {{ $marksheetGrade }} {{ __('grade') }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('student.marksheet') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#B2002F] shadow-md hover:bg-[#fff1f3] transition">
                    <i class="bi bi-journal-text"></i>
                    <span>{{ __('Open Marksheet') }}</span>
                </a>
                <div class="hidden lg:flex items-center justify-center w-24 h-24 rounded-3xl bg-white/10 border border-white/15 shadow-lg">
                    <i class="bi bi-clipboard-data text-5xl text-white/90"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="student-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Recorded Subjects') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjectCount }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Subjects with published marks') }}</p>
        </div>

        <div class="student-smooth-card rounded-xl border border-blue-200 dark:border-blue-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Overall Percentage') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $overallPercentage }}%</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Weighted performance score') }}</p>
        </div>

        <div class="student-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Pass Rate') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ is_null($passRate) ? '—' : $passRate . '%' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Across graded subjects') }}</p>
        </div>

        <div class="student-smooth-card rounded-xl border border-purple-200 dark:border-purple-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-purple-700 dark:text-purple-300 font-semibold">{{ __('CGPA') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($cgpa, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Calculated from current marks') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="student-smooth-panel xl:col-span-7 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Subject Performance') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Published percentages by subject.') }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                    {{ $gradedSubjects->count() }} {{ __('chart points') }}
                </span>
            </div>

            <div class="h-72">
                <canvas id="studentMarksChart"></canvas>
                <p id="studentMarksChartEmpty" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No marks data available yet.') }}</p>
            </div>
        </div>

        <div class="student-smooth-panel xl:col-span-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Status Overview') }}</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Live breakdown') }}</span>
            </div>

            <div class="h-56">
                <canvas id="studentMarksStatusChart"></canvas>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Passed') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $passedCount }}</p>
                </div>
                <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Failed') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $failedCount }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/20 p-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">{{ __('Pending') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $pendingCount }}</p>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
                    <span>{{ __('Overall progress') }}</span>
                    <span>{{ $overallPercentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ min($overallPercentage, 100) }}%"></div>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Top Performing Subjects') }}</h3>
                @if($topSubjects->isEmpty())
                    <div class="text-center py-8">
                        <i class="bi bi-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No ranked subjects yet.') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topSubjects as $subject)
                            <div class="student-smooth-list-card rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }} • {{ $subject['teacher'] }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-red-700 dark:text-red-400">{{ $subject['percentage'] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($subjects->isEmpty())
        <div class="student-smooth-empty rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-10 text-center">
            <i class="bi bi-clipboard-x text-4xl text-gray-300 dark:text-gray-600"></i>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-4">{{ __('No Results Available') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Your marks have not been published yet.') }}</p>
        </div>
    @else
        <div class="student-smooth-table-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Subject-wise Results') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Search this table from the top header to filter subjects instantly.') }}</p>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $subjects->count() }} {{ __('rows') }}</span>
            </div>

            <div class="p-5 space-y-4" data-student-search-root>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-slate-700 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Subject') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Teacher') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Assessment') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('CTEVT') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Score') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-gray-800">
                            @foreach($subjects as $subject)
                                @php
                                    $assessmentText = ($subject['assessment_marks']->full ?? 0) > 0
                                        ? $subject['assessment_marks']->obtained . '/' . $subject['assessment_marks']->full
                                        : __('N/A');

                                    $ctevtText = (isset($subject['ctevt_marks']->full) && $subject['ctevt_marks']->full > 0)
                                        ? $subject['ctevt_marks']->obtained . '/' . $subject['ctevt_marks']->full
                                        : __('N/A');

                                    $statusClasses = match ($subject['status']) {
                                        'pass' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'fail' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40 transition" data-student-search-item data-student-search-text="{{ $subject['name'] }} {{ $subject['code'] }} {{ $subject['teacher'] }} {{ $subject['status'] }}">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }} • {{ __('Semester') }} {{ $subject['semester'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $subject['teacher'] }}</td>
                                    <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $assessmentText }}</td>
                                    <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $ctevtText }}</td>
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ is_null($subject['percentage']) ? '—' : $subject['percentage'] . '%' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $subject['full_marks'] > 0 ? $subject['obtained_marks'] . '/' . $subject['full_marks'] : __('Awaiting publication') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide {{ $statusClasses }}">
                                            {{ $subject['status'] === 'pending' ? __('Pending') : ucfirst($subject['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('student.marks.show', $subject['id']) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition">
                                            {{ __('View') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div data-student-search-empty class="student-smooth-empty hidden rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-8 text-center">
                    <i class="bi bi-search text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No results matched your search.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartCanvas = document.getElementById('studentMarksChart');
        const statusCanvas = document.getElementById('studentMarksStatusChart');
        const chartEmpty = document.getElementById('studentMarksChartEmpty');

        if (!chartCanvas || !window.Chart) {
            return;
        }

        const statusData = @json($marksStatusChart);
        const subjectData = @json(
            $gradedSubjects->map(fn ($subject) => [
                'label' => $subject['code'],
                'value' => $subject['percentage'],
            ])->values()
        );

        if (!Array.isArray(subjectData) || subjectData.length === 0) {
            chartCanvas.classList.add('hidden');
            chartEmpty?.classList.remove('hidden');
            return;
        }

        const isDark = document.documentElement.classList.contains('dark');

        if (statusCanvas) {
            new window.Chart(statusCanvas, {
                type: 'doughnut',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        data: statusData.values,
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                        borderWidth: 3,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: isDark ? '#d1d5db' : '#4b5563',
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 10,
                                padding: 14,
                            },
                        },
                    },
                },
            });
        }

        new window.Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: subjectData.map((item) => item.label),
                datasets: [{
                    label: @json(__('Percentage')),
                    data: subjectData.map((item) => item.value),
                    backgroundColor: ['#FF0037', '#e11d48', '#3b82f6', '#8b5cf6', '#10b981', '#f59e0b'],
                    borderRadius: 10,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: isDark ? '#d1d5db' : '#4b5563',
                        },
                        grid: {
                            display: false,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            color: isDark ? '#d1d5db' : '#4b5563',
                        },
                        grid: {
                            color: isDark ? 'rgba(75, 85, 99, 0.45)' : 'rgba(229, 231, 235, 0.9)',
                        },
                    },
                },
            },
        });
    });
</script>
@endsection
