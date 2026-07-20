@extends('student.layouts.studentlayout')

@section('title', __('Attendance'))

@section('content')
<div class="student-smooth-page space-y-6">
    <!-- Welcome Banner -->
    <div class="student-smooth-hero bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] dark:from-red-800 dark:to-red-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ __('Subject Attendance Hub') }}</h1>
                <p class="text-red-100 text-base">{{ __('Open any subject to view attendance charts, marks, syllabus notes, and uploaded files.') }}</p>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Overall Attendance -->
    <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Overall Attendance') }}</h2>
            
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <div class="h-16 w-16 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center text-red-600 dark:text-red-400">
                        <i class="bi bi-calendar-check text-3xl"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Attendance Percentage') }}</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $overallAttendance }}%</p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-red-600 dark:bg-red-500 h-2.5 rounded-full" style="width: {{ $overallAttendance }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $overallAttendance }}% {{ __('of classes attended') }}</p>
            </div>
        </div>
    </div>

    <!-- Attendance Insights -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($attendanceInsights as $insight)
            <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $insight['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $insight['value'] }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300">
                        <i class="bi {{ $insight['icon'] }} text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($subjects->isNotEmpty())
        <div class="grid gap-6 xl:grid-cols-2">
            <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Attendance Breakdown') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Present, absent, and leave status distribution across all class records.') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-[minmax(0,240px)_1fr] items-center">
                        <div class="relative mx-auto h-72 w-full max-w-[260px]">
                            <canvas id="attendanceStatusChart"></canvas>
                        </div>

                        <div class="space-y-3">
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 p-4 dark:border-emerald-900/30 dark:bg-emerald-950/20">
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">{{ __('Present') }}</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceStatusCounts['present'] }}</p>
                            </div>
                            <div class="rounded-2xl border border-rose-100 bg-rose-50/80 p-4 dark:border-rose-900/30 dark:bg-rose-950/20">
                                <p class="text-xs font-semibold uppercase tracking-wider text-rose-700 dark:text-rose-300">{{ __('Absent') }}</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceStatusCounts['absent'] }}</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50/80 p-4 dark:border-amber-900/30 dark:bg-amber-950/20">
                                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">{{ __('Leave') }}</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceStatusCounts['leave'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Subject-wise Attendance') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('A bar chart of your attendance percentage for every enrolled subject.') }}</p>
                        </div>
                    </div>

                    <div class="h-80">
                        <canvas id="subjectAttendanceChart"></canvas>
                    </div>

                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Best Subject') }}</p>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $bestSubject['name'] ?? __('N/A') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ isset($bestSubject['attendance']) ? number_format($bestSubject['attendance'], 1) . '%' : __('No data') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Needs Attention') }}</p>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $lowestSubject['name'] ?? __('N/A') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ isset($lowestSubject['attendance']) ? number_format($lowestSubject['attendance'], 1) . '%' : __('No data') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Subject-wise Attendance') }}</h2>
                <div class="space-y-4">
                    @foreach($subjects as $subject)
                        <div class="student-smooth-list-card flex flex-col gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-l-4 border-red-400 md:flex-row md:items-start md:justify-between">
                            <div class="flex-1">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-book text-red-600 dark:text-red-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $subject['name'] }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <i class="bi bi-tag"></i>
                                            <span>{{ $subject['code'] }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="bi bi-person text-red-600 dark:text-red-400"></i>
                                            {{ $subject['teacher'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center md:min-w-[180px]">
                                <div class="space-y-2">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Attendance') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($subject['attendance'], 1) }}%</p>
                                </div>
                                <a href="{{ route('student.attendance.show', $subject['id']) }}" class="mt-3 inline-flex items-center justify-center gap-2 rounded-full bg-red-600 px-4 py-2 text-xs font-semibold text-white shadow-md shadow-red-900/15 transition hover:bg-red-700">
                                    <i class="bi bi-arrow-right"></i>
                                    <span>{{ __('View subject dashboard') }}</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="student-smooth-empty bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
            <i class="bi bi-book text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('No Subjects Enrolled') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('You are not currently enrolled in any subjects.') }}</p>
        </div>
    @endif

    <!-- Recent Attendance Records -->
    <div class="student-smooth-table-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Recent Attendance Records') }}</h2>
            
            @if($recentAttendance->isEmpty())
                <div class="student-smooth-empty text-center py-8">
                    <i class="bi bi-calendar-x text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No attendance records found') }}</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentAttendance as $record)
                        <div class="student-smooth-list-card flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-l-3 
                            @if($record->status === 'present')
                                border-green-400
                            @elseif($record->status === 'absent')
                                border-red-400
                            @else
                                border-yellow-400
                            @endif
                        ">
                            <div class="flex-1">
                                <div class="flex items-start gap-3">

                                    <div class="flex-shrink-0">
                                        <i class="bi bi-calendar-date text-2xl 
                                            @if($record->status === 'present')
                                                text-green-600 dark:text-green-400
                                            @elseif($record->status === 'absent')
                                                text-red-600 dark:text-red-400
                                            @else
                                                text-yellow-600 dark:text-yellow-400
                                            @endif
                                        "></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white mb-1">{{ $record->subject_name }} ({{ $record->subject_code }})</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="bi bi-clock"></i>
                                            {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="bi bi-clock"></i>
                                            {{ $record->time_in ?? 'N/A' }} - {{ $record->time_out ?? 'N/A' }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                            <div class="text-center">
                                <span class="px-3 py-1 rounded-full 
                                    @if($record->status === 'present')
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($record->status === 'absent')
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @endif">
                                    @if($record->status === 'present')
                                        {{ __('Present') }}
                                    @elseif($record->status === 'absent')
                                        {{ __('Absent') }}
                                    @else
                                        {{ __('Late') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusData = @json($attendanceStatusChart);
    const subjectData = @json($subjectAttendanceChart);
    const isDark = document.documentElement.classList.contains('dark');
    const axisColor = isDark ? '#cbd5e1' : '#475569';
    const gridColor = isDark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.18)';

    const statusCanvas = document.getElementById('attendanceStatusChart');
    if (statusCanvas && window.Chart) {
        new Chart(statusCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: statusData.labels,
                datasets: [{
                    data: statusData.values,
                    backgroundColor: ['#16a34a', '#ef4444', '#f59e0b'],
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
                            color: axisColor,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            padding: 18,
                        },
                    },
                },
            },
        });
    }

    const subjectCanvas = document.getElementById('subjectAttendanceChart');
    if (subjectCanvas && window.Chart) {
        new Chart(subjectCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: subjectData.labels,
                datasets: [{
                    label: @json(__('Attendance %')),
                    data: subjectData.values,
                    backgroundColor: 'rgba(225, 29, 72, 0.85)',
                    borderColor: '#b2002f',
                    borderWidth: 1,
                    borderRadius: 12,
                    barThickness: 18,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const index = context.dataIndex;
                                const code = subjectData.codes?.[index] ?? '';
                                const teacher = subjectData.teachers?.[index] ?? '';
                                return `${code} ${teacher ? '• ' + teacher : ''} • ${context.parsed.x}%`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        suggestedMax: 100,
                        ticks: {
                            color: axisColor,
                            callback: function (value) {
                                return value + '%';
                            },
                        },
                        grid: {
                            color: gridColor,
                        },
                    },
                    y: {
                        ticks: {
                            color: axisColor,
                        },
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }
});
</script>
@endsection

