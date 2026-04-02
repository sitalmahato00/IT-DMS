@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('styles')
<style>
    html:not(.dark) .dashboard-stats > div {
        position: relative;
        overflow: hidden;
        border-width: 2px;
        border-color: #e2e8f0;
        border-radius: 0.9rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    html:not(.dark) .dashboard-stats > div:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    html:not(.dark) .dashboard-chart-container {
        position: relative;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
        transition: box-shadow 0.25s ease;
    }

    html:not(.dark) .dashboard-chart-container:hover {
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    html:not(.dark) .dashboard-info-card {
        position: relative;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 18px 35px -30px rgba(15, 23, 42, 0.22);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    html:not(.dark) .dashboard-info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 40px -28px rgba(15, 23, 42, 0.28);
    }

    .dark .dashboard-stats > div,
    .dark .dashboard-chart-container,
    .dark .dashboard-info-card {
        border-color: #1e293b;
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        box-shadow: 0 18px 35px -30px rgba(0, 0, 0, 0.5);
    }

    .dark .dashboard-stats > div:hover,
    .dark .dashboard-chart-container:hover,
    .dark .dashboard-info-card:hover {
        box-shadow: 0 24px 40px -28px rgba(0, 0, 0, 0.6);
    }

    .dark .dashboard-stats > div:hover {
        transform: translateY(-4px);
    }

    .dark .dashboard-info-card:hover {
        transform: translateY(-4px);
    }
</style>
@endsection

@section('content')
@php
    $adminName = $user->name ?? 'Administrator';
    $gradeDistribution = $gradeDistribution ?? [];
    $totalGradedRecords = array_sum($gradeDistribution);
    $passCount = $totalGradedRecords - ($gradeDistribution['F'] ?? 0);
    $distinctionCount = ($gradeDistribution['A+'] ?? 0) + ($gradeDistribution['A'] ?? 0);
    $needsAttentionCount = ($gradeDistribution['D'] ?? 0) + ($gradeDistribution['F'] ?? 0);
    $passRate = $totalGradedRecords > 0 ? round(($passCount / $totalGradedRecords) * 100, 1) : 0;
    $distinctionRate = $totalGradedRecords > 0 ? round(($distinctionCount / $totalGradedRecords) * 100, 1) : 0;
    $needsAttentionRate = $totalGradedRecords > 0 ? round(($needsAttentionCount / $totalGradedRecords) * 100, 1) : 0;
    $gradeLeaders = collect($gradeDistribution)->filter(fn ($count) => $count > 0)->sortDesc();
    $topGrade = $gradeLeaders->keys()->first() ?? '—';
    $topGradeCount = $topGrade !== '—' ? ($gradeDistribution[$topGrade] ?? 0) : 0;

    $attendanceTarget = 85;
    $attendanceBuckets = collect($attendanceChartData['labels'] ?? [])->map(function ($label, $index) use ($attendanceChartData) {
        $detail = $attendanceChartData['details'][$index] ?? [];

        return [
            'label' => $label,
            'present' => (int) ($detail['present'] ?? 0),
            'total' => (int) ($detail['total'] ?? 0),
            'percentage' => (float) ($detail['percentage'] ?? 0),
        ];
    });
    $activeAttendanceBuckets = $attendanceBuckets->filter(fn ($bucket) => $bucket['total'] > 0)->values();
    $initialAttendanceAverage = $activeAttendanceBuckets->isNotEmpty()
        ? round((float) $activeAttendanceBuckets->avg('percentage'), 1)
        : 0;
    $initialAttendanceBest = $activeAttendanceBuckets->sortByDesc('percentage')->first();
    $initialAttendanceLowest = $activeAttendanceBuckets->sortBy('percentage')->first();
    $initialAttendancePresent = $activeAttendanceBuckets->sum('present');
    $initialAttendanceTracked = $activeAttendanceBuckets->sum('total');
    $initialAttendanceNotPresent = max($initialAttendanceTracked - $initialAttendancePresent, 0);
@endphp

<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/20 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-16 w-56 h-56 rounded-full bg-[#D90033]/40 blur-3xl"></div>
        <div class="relative flex flex-col gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-[#ffe5ea]">
                    <span>{{ __('Dashboard') }}</span>
                    @if($college && $college->name)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                            <i class="bi bi-building"></i> {{ $college->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-person-badge"></i> {{ __('Administrator') }}
                    </span>
                </div>
                <h1 class="mt-4 text-2xl md:text-3xl font-bold">{{ __('Welcome back,') }} {{ $adminName }}</h1>
                <p class="text-[#ffe5ea] mt-2">{{ __('Manage students, teachers, attendance, and all administrative tasks from here.') }}</p>
                <div class="mt-4 flex flex-wrap gap-3 text-sm text-[#ffe5ea]">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-calendar2-week"></i>
                        {{ $dashboardOverview['today_class_count'] ?? 0 }} {{ __('classes today') }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-pie-chart"></i>
                        {{ number_format($passRate, 1) }}% {{ __('pass rate') }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-bell"></i>
                        {{ $dashboardOverview['unread_notifications'] ?? 0 }} {{ __('unread alerts') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Clean Design -->
    <div class="dashboard-stats grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Total Students Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Total Students') }}</p>
                <i class="bi bi-people-fill text-xl text-blue-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalStudents ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-blue-600 dark:text-blue-400 font-semibold">↳</span> Enrolled this year</p>
                <p><span class="text-green-600 dark:text-green-400 font-semibold">📈</span> Growth: +12% YoY</p>
            </div>
        </div>

        <!-- Teachers Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Teachers') }}</p>
                <i class="bi bi-person-check-fill text-xl text-orange-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($teachers ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-orange-600 dark:text-orange-400 font-semibold">↳</span> {{ $teachers > 0 ? round($totalStudents / $teachers, 1) : '0' }} students/teacher</p>
                <p><span class="text-green-600 dark:text-green-400 font-semibold">✓</span> All verified</p>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Courses') }}</p>
                <i class="bi bi-book-fill text-xl text-purple-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($courses ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-purple-600 dark:text-purple-400 font-semibold">↳</span> Currently running</p>
                <p><span class="text-indigo-600 dark:text-indigo-400 font-semibold">🎓</span> All semesters</p>
            </div>
        </div>

        <!-- Attendance Rate Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Attendance') }}</p>
                <i class="bi bi-check-circle-fill text-xl text-green-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($avgAttendance) ? $avgAttendance . '%' : '—' }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-green-600 dark:text-green-400 font-semibold">↳</span> Semester average</p>
                <p><span class="text-blue-600 dark:text-blue-400 font-semibold">📊</span> Target: 85%</p>
            </div>
        </div>
    </div>

    <!-- Additional Stats Cards - Clean Design -->
    <div class="dashboard-stats grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Parents Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Parents') }}</p>
                <i class="bi bi-heart-fill text-xl text-rose-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($parents ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-rose-600 dark:text-rose-400 font-semibold">↳</span> {{ $totalStudents + $parents + $teachers > 0 ? round(($parents / ($totalStudents + $parents + $teachers)) * 100, 1) : 0 }}% registered</p>
                <p><span class="text-cyan-600 dark:text-cyan-400 font-semibold">💬</span> Engagement: High</p>
            </div>
        </div>

        <!-- Alumni Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Alumni') }}</p>
                <i class="bi bi-mortarboard-fill text-xl text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($alumni ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-amber-600 dark:text-amber-400 font-semibold">↳</span> Graduated: {{ $totalStudents + $alumni > 0 ? round(($alumni / ($totalStudents + $alumni)) * 100, 1) : 0 }}%</p>
                <p><span class="text-yellow-600 dark:text-yellow-400 font-semibold">🌟</span> Success rate</p>
            </div>
        </div>

        <!-- Active Semesters Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Semesters') }}</p>
                <i class="bi bi-calendar2-check text-xl text-teal-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($activeSemesters ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-teal-600 dark:text-teal-400 font-semibold">↳</span> Currently running</p>
                <p><span class="text-cyan-600 dark:text-cyan-400 font-semibold">⏱️</span> On schedule</p>
            </div>
        </div>

        <!-- Electives Card -->
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[11px] uppercase tracking-widest text-gray-600 dark:text-gray-400 font-semibold">{{ __('Electives') }}</p>
                <i class="bi bi-star-fill text-xl text-indigo-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($electiveStudents ?? 0) }}</p>
            <div class="mt-2 text-[11px] text-gray-600 dark:text-gray-400 space-y-1">
                <p><span class="text-indigo-600 dark:text-indigo-400 font-semibold">↳</span> {{ $totalStudents > 0 ? round(($electiveStudents / $totalStudents) * 100, 1) : 0 }}% enrolled</p>
                <p><span class="text-pink-600 dark:text-pink-400 font-semibold">★</span> Trending choice</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Attendance Bar Chart -->
        <div class="xl:col-span-7 dashboard-chart-container p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-graph-up text-emerald-500"></i>
                        {{ __('Attendance Overview') }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Daily attendance trend, class coverage, and risk signals') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <label for="attendancePeriod" class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Period:') }}</label>
                    <select id="attendancePeriod" class="text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 py-1 px-2 font-medium">
                        <option value="week">{{ __('Weekly') }}</option>
                        <option value="month">{{ __('Monthly') }}</option>
                        <option value="semester">{{ __('Semester') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mb-5">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Semester Avg') }}</p>
                    <p class="mt-2 text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format((float) ($avgAttendance ?? 0), 1) }}%</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ __('Target') }} <span class="font-semibold text-cyan-600 dark:text-cyan-400">{{ $attendanceTarget }}%</span></p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Present') }}</p>
                    <p class="mt-2 text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($attendanceSummary['present'] ?? 0) }}</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ __('On time and present') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Absent') }}</p>
                    <p class="mt-2 text-lg font-bold text-orange-600 dark:text-orange-400">{{ number_format($attendanceSummary['absent'] ?? 0) }}</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ __('Needing follow-up') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Late') }}</p>
                    <p class="mt-2 text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($attendanceSummary['late'] ?? 0) }}</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ __('Late arrivals') }}</p>
                </div>
            </div>
            <div class="h-72 relative">
                <canvas id="attendanceBarChart"></canvas>
                <p id="attendanceBarNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-28">{{ __('No attendance data available.') }}</p>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mt-5">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Current View Avg') }}</p>
                    <p id="attendanceSelectedAverage" class="mt-2 text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($initialAttendanceAverage, 1) }}%</p>
                    <p id="attendanceSelectedAverageNote" class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ $activeAttendanceBuckets->count() }} {{ __('active buckets') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Best Period') }}</p>
                    <p id="attendanceBestLabel" class="mt-2 text-sm font-bold text-green-600 dark:text-green-400">{{ $initialAttendanceBest['label'] ?? __('No records') }}</p>
                    <p id="attendanceBestValue" class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">
                        {{ isset($initialAttendanceBest['percentage']) ? number_format((float) $initialAttendanceBest['percentage'], 1) . '%' : __('Waiting') }}
                    </p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Needs Attention') }}</p>
                    <p id="attendanceLowestLabel" class="mt-2 text-sm font-bold text-rose-600 dark:text-rose-400">{{ $initialAttendanceLowest['label'] ?? __('No records') }}</p>
                    <p id="attendanceLowestValue" class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">
                        {{ isset($initialAttendanceLowest['percentage']) ? number_format((float) $initialAttendanceLowest['percentage'], 1) . '%' : __('Waiting') }}
                    </p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Coverage') }}</p>
                    <p id="attendanceCoverageValue" class="mt-2 text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($initialAttendancePresent) }} / {{ number_format($initialAttendanceTracked) }}</p>
                    <p id="attendanceCoverageNote" class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ number_format($initialAttendanceNotPresent) }} {{ __('not present') }}</p>
                </div>
            </div>
        </div>

        <!-- Grade Distribution Pie Chart -->
        <div class="xl:col-span-5 dashboard-chart-container p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-fuchsia-500"></i>
                        {{ __('Grade Distribution') }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Academic performance mix, pass rate, and support indicators') }}</p>
                </div>
                <span class="text-xs px-3 py-2 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 dark:from-green-900 dark:to-emerald-900 dark:text-green-200 font-bold">
                    {{ $totalGradedRecords }} {{ __('Graded') }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Pass Rate') }}</p>
                    <p class="mt-2 text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($passRate, 1) }}%</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ $passCount }} {{ __('students cleared') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('A Range') }}</p>
                    <p class="mt-2 text-lg font-bold text-blue-600 dark:text-blue-400">{{ number_format($distinctionRate, 1) }}%</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ $distinctionCount }} {{ __('top grades') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Need Support') }}</p>
                    <p class="mt-2 text-lg font-bold text-rose-600 dark:text-rose-400">{{ number_format($needsAttentionRate, 1) }}%</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ $needsAttentionCount }} {{ __('in D or F') }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-600 dark:text-gray-400">{{ __('Top Band') }}</p>
                    <p class="mt-2 text-lg font-bold text-amber-600 dark:text-amber-400">{{ $topGrade }}</p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">{{ $topGradeCount }} {{ __('in this band') }}</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="gradePieChart"></canvas>
                <p id="gradePieNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No grades available yet.') }}</p>
            </div>
            <div class="grid grid-cols-4 md:grid-cols-8 gap-2 mt-5 text-center">
                @php
                    $gradeColors = [
                        'A+' => '#22c55e',
                        'A' => '#16a34a',
                        'B+' => '#3b82f6',
                        'B' => '#2563eb',
                        'C+' => '#eab308',
                        'C' => '#ca8a04',
                        'D' => '#f97316',
                        'F' => '#ef4444',
                    ];
                @endphp
                @foreach($gradeDistribution as $grade => $count)
                    @php
                        $gradePercent = $totalGradedRecords > 0 ? round(($count / $totalGradedRecords) * 100, 1) : 0;
                    @endphp
                    <div class="rounded-lg border py-2 px-1 font-bold transition hover:shadow-md bg-gray-50 dark:bg-gray-900" style="border-color: {{ $gradeColors[$grade] ?? '#6b7280' }};">
                        <div class="w-3 h-3 mx-auto rounded-full mb-1.5" style="background-color: {{ $gradeColors[$grade] ?? '#6b7280' }}"></div>
                        <p class="text-sm font-bold leading-none" style="color: {{ $gradeColors[$grade] ?? '#6b7280' }}">{{ $count }}</p>
                        <p class="text-[11px] text-gray-700 dark:text-gray-300 mt-1 leading-none">{{ $grade }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 leading-none">{{ number_format($gradePercent, 1) }}%</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Recent Notices -->
        <div class="xl:col-span-4 dashboard-info-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-megaphone-fill text-yellow-500"></i>
                    {{ __('Notices') }}
                </h2>
                <a href="{{ route('admin.notice-board') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full text-yellow-600 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition">
                    {{ __('All') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if(!empty($recentNotices) && count($recentNotices) > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($recentNotices as $notice)
                        <div class="rounded-lg border-l-4 border-l-yellow-400 bg-gray-50 dark:bg-gray-900 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $notice['title'] }}</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-1">{{ $notice['message'] }}</p>
                            <p class="text-[11px] text-yellow-600 dark:text-yellow-400 mt-1.5 font-semibold">⏰ {{ \Carbon\Carbon::parse($notice['created_at'])->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-bell-slash text-4xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 font-semibold">{{ __('No notices available') }}</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Exams -->
        <div class="xl:col-span-4 dashboard-info-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-pencil-square text-red-500"></i>
                    {{ __('Exams') }}
                </h2>
                <a href="{{ route('admin.exam') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    {{ __('Open') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if($upcomingExams->count() > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($upcomingExams as $exam)
                        <div class="flex items-start justify-between gap-2 rounded-lg border-l-4 border-l-red-400 bg-gray-50 dark:bg-gray-900 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $exam['name'] }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 font-medium">{{ $exam['subject_name'] }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-bold text-red-600 dark:text-red-400">📅 {{ \Carbon\Carbon::parse($exam['exam_date'])->format('M d') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-calendar-x text-4xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 font-semibold">{{ __('No upcoming exams') }}</p>
                </div>
            @endif
        </div>

        <!-- Today's Classes -->
        <div class="xl:col-span-4 dashboard-info-card p-5">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="bi bi-calendar-day text-cyan-500"></i>
                {{ __("Today's Classes") }}
            </h2>
            @if($todayClasses->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($todayClasses as $class)
                        <div class="rounded-lg border-l-4 border-l-cyan-400 bg-gray-50 dark:bg-gray-900 p-3.5 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $class['subject_name'] }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Sem: {{ $class['semester'] }}</p>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400">{{ $class['attendance_rate'] }}%</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-2 text-[10px] font-bold text-center">
                                <span class="rounded-lg bg-green-100 dark:bg-green-900/40 py-1.5 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800">✓ {{ $class['present_count'] }}</span>
                                <span class="rounded-lg bg-red-100 dark:bg-red-900/40 py-1.5 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">✕ {{ $class['absent_count'] }}</span>
                                <span class="rounded-lg bg-blue-100 dark:bg-blue-900/40 py-1.5 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">👥 {{ $class['total_students'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-calendar-check text-4xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 font-semibold">{{ __('No classes recorded today.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Third Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="dashboard-info-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-clock-history text-violet-500"></i>
                    {{ __('Activities') }}
                </h2>
                <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full text-violet-600 dark:text-violet-400 hover:bg-violet-100 dark:hover:bg-violet-900/30 transition">
                    {{ __('All') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if(!empty($recentActivities) && count($recentActivities) > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($recentActivities as $act)
                        <div class="rounded-lg border-l-4 border-l-violet-400 bg-gray-50 dark:bg-gray-900 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div class="flex items-start gap-2">
                                <div class="relative mt-0.5 flex-shrink-0">
                                    <div class="w-2 h-2 bg-violet-500 rounded-full animate-pulse"></div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $act['action'] }}</p>
                                    @if(!empty($act['details']))
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate mt-0.5">{{ $act['details'] }}</p>
                                    @else
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate mt-0.5">{{ $act['user_name'] }}</p>
                                    @endif
                                    <p class="text-[11px] text-violet-600 dark:text-violet-400 mt-0.5 font-semibold">⏲ {{ \Carbon\Carbon::parse($act['timestamp'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 font-semibold">{{ __('No activities yet') }}</p>
                </div>
            @endif
        </div>

        <!-- New Students -->
        <div class="dashboard-info-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-person-plus-fill text-cyan-500"></i>
                    {{ __('New Students') }}
                </h2>
                <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition">
                    {{ __('All') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if(!empty($newStudents) && count($newStudents) > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($newStudents as $student)
                        <div class="rounded-lg border-l-4 border-l-cyan-400 bg-gray-50 dark:bg-gray-900 p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center text-xs font-bold text-cyan-600 dark:text-cyan-400 flex-shrink-0">
                                    {{ substr($student['name'], 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $student['name'] }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $student['email'] }}</p>
                                    <p class="text-[11px] text-cyan-600 dark:text-cyan-400 mt-0.5 font-semibold">🆕 {{ \Carbon\Carbon::parse($student['created_at'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 font-semibold">{{ __('No new students') }}</p>
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
        // Data from server
        const attendanceChartData = @json($attendanceChartData);
        const gradeDistribution = @json($gradeDistribution);
        const attendanceDataUrl = @json(route('admin.dashboard.attendance'));
        const totalGradedRecords = @json($totalGradedRecords);
        const gradePassRate = @json($passRate);
        const attendanceTarget = @json($attendanceTarget);

        // Common variables
        const isDark = document.documentElement.classList.contains('dark');
        const labelColor = isDark ? '#d1d5db' : '#4b5563';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.45)' : 'rgba(229, 231, 235, 0.9)';
        const titleColor = isDark ? '#f9fafb' : '#111827';
        const mutedColor = isDark ? '#9ca3af' : '#6b7280';

        const attendanceInsightNodes = {
            average: document.getElementById('attendanceSelectedAverage'),
            averageNote: document.getElementById('attendanceSelectedAverageNote'),
            bestLabel: document.getElementById('attendanceBestLabel'),
            bestValue: document.getElementById('attendanceBestValue'),
            lowestLabel: document.getElementById('attendanceLowestLabel'),
            lowestValue: document.getElementById('attendanceLowestValue'),
            coverageValue: document.getElementById('attendanceCoverageValue'),
            coverageNote: document.getElementById('attendanceCoverageNote'),
        };

        const formatPercent = (value) => `${Number(value || 0).toFixed(1)}%`;
        const getAttendanceBarColor = (value) => {
            if (value >= attendanceTarget) {
                return '#16a34a';
            }

            if (value >= 75) {
                return '#22c55e';
            }

            if (value >= 60) {
                return '#f59e0b';
            }

            return '#ef4444';
        };

        const getAttendanceHoverColor = (value) => {
            if (value >= attendanceTarget) {
                return '#15803d';
            }

            if (value >= 75) {
                return '#16a34a';
            }

            if (value >= 60) {
                return '#d97706';
            }

            return '#dc2626';
        };

        const updateAttendanceInsights = (chartData) => {
            const labels = chartData.labels || [];
            const details = (chartData.details || []).map((detail, index) => ({
                label: labels[index] || detail.period || '—',
                present: Number(detail.present || 0),
                total: Number(detail.total || 0),
                percentage: Number(detail.percentage || 0),
            }));

            const activeDetails = details.filter((detail) => detail.total > 0);
            const totalTracked = activeDetails.reduce((sum, detail) => sum + detail.total, 0);
            const totalPresent = activeDetails.reduce((sum, detail) => sum + detail.present, 0);
            const average = activeDetails.length
                ? activeDetails.reduce((sum, detail) => sum + detail.percentage, 0) / activeDetails.length
                : 0;

            const best = activeDetails.reduce((winner, detail) => {
                if (!winner || detail.percentage > winner.percentage) {
                    return detail;
                }

                return winner;
            }, null);

            const lowest = activeDetails.reduce((winner, detail) => {
                if (!winner || detail.percentage < winner.percentage) {
                    return detail;
                }

                return winner;
            }, null);

            if (attendanceInsightNodes.average) {
                attendanceInsightNodes.average.textContent = formatPercent(average);
            }

            if (attendanceInsightNodes.averageNote) {
                attendanceInsightNodes.averageNote.textContent = activeDetails.length
                    ? `${activeDetails.length} active bucket${activeDetails.length === 1 ? '' : 's'}`
                    : 'No tracked periods yet';
            }

            if (attendanceInsightNodes.bestLabel) {
                attendanceInsightNodes.bestLabel.textContent = best ? best.label : 'No records';
            }

            if (attendanceInsightNodes.bestValue) {
                attendanceInsightNodes.bestValue.textContent = best
                    ? `${formatPercent(best.percentage)} attendance`
                    : 'Waiting for data';
            }

            if (attendanceInsightNodes.lowestLabel) {
                attendanceInsightNodes.lowestLabel.textContent = lowest ? lowest.label : 'No records';
            }

            if (attendanceInsightNodes.lowestValue) {
                attendanceInsightNodes.lowestValue.textContent = lowest
                    ? `${formatPercent(lowest.percentage)} attendance`
                    : 'Waiting for data';
            }

            if (attendanceInsightNodes.coverageValue) {
                attendanceInsightNodes.coverageValue.textContent = `${totalPresent.toLocaleString()} / ${totalTracked.toLocaleString()}`;
            }

            if (attendanceInsightNodes.coverageNote) {
                const notPresent = Math.max(totalTracked - totalPresent, 0);
                attendanceInsightNodes.coverageNote.textContent = totalTracked
                    ? `${notPresent.toLocaleString()} not present in this range`
                    : 'No attendance records captured';
            }
        };

        const gradeCenterTextPlugin = {
            id: 'gradeCenterText',
            afterDatasetsDraw(chart) {
                if (chart.canvas.id !== 'gradePieChart') {
                    return;
                }

                const dataset = chart.getDatasetMeta(0);
                if (!dataset?.data?.length) {
                    return;
                }

                const { ctx } = chart;
                const { x, y } = dataset.data[0];

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = titleColor;
                ctx.font = '700 22px system-ui';
                ctx.fillText(totalGradedRecords.toLocaleString(), x, y - 12);
                ctx.fillStyle = mutedColor;
                ctx.font = '600 11px system-ui';
                ctx.fillText('graded records', x, y + 8);
                ctx.fillText(`Pass ${formatPercent(gradePassRate)}`, x, y + 24);
                ctx.restore();
            }
        };

        // Grade Pie Chart
        const gradePieCanvas = document.getElementById('gradePieChart');
        const gradePieNoData = document.getElementById('gradePieNoData');
        if (gradePieCanvas) {
            const gradeLabels = Object.keys(gradeDistribution);
            const gradeData = gradeLabels.map(label => Number(gradeDistribution[label] || 0));
            const hasGradeData = gradeData.some(value => value > 0);
            
            const gradeColors = {
                'A+': '#22c55e',
                'A': '#16a34a',
                'B+': '#3b82f6',
                'B': '#2563eb',
                'C+': '#eab308',
                'C': '#ca8a04',
                'D': '#f97316',
                'F': '#ef4444'
            };
            const colors = gradeLabels.map(grade => gradeColors[grade] || '#6b7280');

            if (!hasGradeData && gradePieNoData) {
                gradePieCanvas.classList.add('hidden');
                gradePieNoData.classList.remove('hidden');
            } else if (hasGradeData) {
                new Chart(gradePieCanvas.getContext('2d'), {
                    type: 'doughnut',
                    plugins: [gradeCenterTextPlugin],
                    data: {
                        labels: gradeLabels,
                        datasets: [{
                            data: gradeData,
                            backgroundColor: colors,
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '52%',
                        layout: {
                            padding: {
                                top: 6,
                                bottom: 6
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: labelColor,
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    padding: 14,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label(context) {
                                        const value = Number(context.parsed || 0);
                                        const share = totalGradedRecords > 0 ? (value / totalGradedRecords) * 100 : 0;

                                        return `${context.label}: ${value} students (${share.toFixed(1)}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Attendance Bar Chart
        const barCanvas = document.getElementById('attendanceBarChart');
        const barNoData = document.getElementById('attendanceBarNoData');
        const attendancePeriodSelect = document.getElementById('attendancePeriod');
        let attendanceChartInstance = null;

        const renderAttendanceBarChart = (chartData) => {
            if (!barCanvas) {
                return;
            }

            const barLabels = chartData.labels || [];
            const barValues = (chartData.data || []).map(value => Number(value));
            const hasBarData = barLabels.length > 0 && barValues.some(value => value > 0);

            updateAttendanceInsights(chartData);

            if (attendanceChartInstance) {
                attendanceChartInstance.destroy();
                attendanceChartInstance = null;
            }

            if (!hasBarData && barNoData) {
                barCanvas.classList.add('hidden');
                barNoData.classList.remove('hidden');
                return;
            }

            barCanvas.classList.remove('hidden');
            if (barNoData) {
                barNoData.classList.add('hidden');
            }

            attendanceChartInstance = new Chart(barCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Attendance %',
                        data: barValues,
                        borderRadius: 6,
                        backgroundColor: barValues.map(getAttendanceBarColor),
                        hoverBackgroundColor: barValues.map(getAttendanceHoverColor),
                        maxBarThickness: 48,
                        categoryPercentage: 0.8,
                        barPercentage: 0.9,
                        minBarLength: 4
                    }, {
                        type: 'line',
                        label: 'Target',
                        data: barValues.map(() => attendanceTarget),
                        borderColor: '#0f766e',
                        borderWidth: 2,
                        borderDash: [6, 6],
                        pointRadius: 0,
                        pointHoverRadius: 0,
                        tension: 0,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    if (context.dataset.type === 'line') {
                                        return `Target: ${attendanceTarget}%`;
                                    }

                                    return `Attendance: ${formatPercent(context.parsed.y)}`;
                                },
                                afterLabel(context) {
                                    if (context.dataset.type === 'line') {
                                        return null;
                                    }

                                    const detail = chartData.details?.[context.dataIndex];
                                    if (!detail) {
                                        return null;
                                    }

                                    const present = Number(detail.present || 0);
                                    const total = Number(detail.total || 0);
                                    const notPresent = Math.max(total - present, 0);
                                    if (!total) {
                                        return 'No attendance records';
                                    }

                                    const targetGap = Math.max(attendanceTarget - Number(detail.percentage || 0), 0);

                                    return [
                                        `Present: ${present}`,
                                        `Not present: ${notPresent}`,
                                        `Tracked records: ${total}`,
                                        `Gap to target: ${targetGap.toFixed(1)}%`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: labelColor },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20,
                                callback: (value) => value + '%',
                                color: labelColor
                            },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        };

        renderAttendanceBarChart(attendanceChartData);

        if (attendancePeriodSelect) {
            attendancePeriodSelect.addEventListener('change', async function () {
                const selectedPeriod = this.value;

                try {
                    const response = await fetch(`${attendanceDataUrl}?period=${encodeURIComponent(selectedPeriod)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`Failed to load attendance data (${response.status})`);
                    }

                    const chartData = await response.json();
                    renderAttendanceBarChart(chartData);
                } catch (error) {
                    console.error('Error loading attendance chart data:', error);
                }
            });
        }
    });
</script>
@endsection
