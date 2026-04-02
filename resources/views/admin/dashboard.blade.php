@extends('admin.layouts.app')

@section('title', __('Dashboard'))

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
    $healthyClassCount = (int) ($dashboardOverview['healthy_attendance_classes'] ?? 0);
    $attentionFlags = (int) ($dashboardOverview['low_attendance_classes'] ?? 0)
        + (int) ($dashboardOverview['pending_elective_approvals'] ?? 0)
        + (int) ($dashboardOverview['unread_notifications'] ?? 0);
@endphp

@include('admin.components.admin-page-header', [
    'title' => __('Dashboard'),
    'breadcrumbs' => [
        ['label' => __('Dashboard')]
    ]
])

<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Welcome Section -->
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 md:p-6">
        <div class="grid gap-6 xl:grid-cols-[1.7fr,1fr] xl:items-start">
            <div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span class="rounded-full bg-red-50 px-3 py-1 text-red-600 dark:bg-red-900/30 dark:text-red-300">{{ __('Dashboard Center') }}</span>
                    @if($college && $college->name)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $college->name }}</span>
                    @endif
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ __('Administrator') }}</span>
                </div>
                <h1 class="mt-4 text-2xl font-bold text-slate-900 dark:text-white md:text-3xl">{{ __('Welcome back,') }} {{ $adminName }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('Manage students, teachers, attendance, and all administrative tasks from here.') }}</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <i class="bi bi-calendar2-week"></i>
                        {{ $dashboardOverview['today_class_count'] ?? 0 }} {{ __('classes today') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <i class="bi bi-pie-chart"></i>
                        {{ number_format($passRate, 1) }}% {{ __('pass rate') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                        <i class="bi bi-bell"></i>
                        {{ $dashboardOverview['unread_notifications'] ?? 0 }} {{ __('unread alerts') }}
                    </span>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/30">
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700 dark:text-emerald-300">{{ __('Live Classes') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-emerald-900 dark:text-emerald-100">{{ $dashboardOverview['today_class_count'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-300">{{ $healthyClassCount }} {{ __('on track today') }}</p>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4 dark:border-rose-800 dark:bg-rose-950/30">
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-700 dark:text-rose-300">{{ __('Attention Queue') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-rose-900 dark:text-rose-100">{{ $attentionFlags }}</p>
                    <p class="mt-1 text-xs text-rose-700 dark:text-rose-300">{{ __('attendance, grades, and approvals to review') }}</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/30">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700 dark:text-blue-300">{{ __('Grade Health') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ number_format($distinctionRate, 1) }}%</p>
                    <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">{{ __('students in A and A+') }}</p>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/30">
                    <p class="text-xs font-semibold uppercase tracking-widest text-amber-700 dark:text-amber-300">{{ __('Upcoming Exams') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-amber-900 dark:text-amber-100">{{ $dashboardOverview['upcoming_exam_count'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-amber-700 dark:text-amber-300">{{ __('scheduled assessments ahead') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards with Enhanced Styling -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2">
        <!-- Total Students Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-blue-300 dark:border-blue-700 bg-gradient-to-br from-blue-50 via-white to-blue-50 dark:from-blue-950/40 dark:via-gray-800 dark:to-blue-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-200/20 dark:bg-blue-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-blue-700 dark:text-blue-300 font-bold">{{ __('Total Students') }}</p>
                    <i class="bi bi-people-fill text-lg text-blue-500 dark:text-blue-400"></i>
                </div>
                <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ number_format($totalStudents ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-blue-600 dark:text-blue-400 space-y-0.5">
                    <p>↳ Enrolled this year</p>
                    <p>📈 Growth: +12% YoY</p>
                </div>
            </div>
        </div>

        <!-- Teachers Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-orange-300 dark:border-orange-700 bg-gradient-to-br from-orange-50 via-white to-orange-50 dark:from-orange-950/40 dark:via-gray-800 dark:to-orange-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-orange-200/20 dark:bg-orange-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-orange-700 dark:text-orange-300 font-bold">{{ __('Teachers') }}</p>
                    <i class="bi bi-person-check-fill text-lg text-orange-500 dark:text-orange-400"></i>
                </div>
                <p class="text-lg font-bold text-orange-900 dark:text-orange-100">{{ number_format($teachers ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-orange-600 dark:text-orange-400 space-y-0.5">
                    <p>↳ {{ $teachers > 0 ? round($totalStudents / $teachers, 1) : '0' }} students/teacher</p>
                    <p>✓ All verified</p>
                </div>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-purple-300 dark:border-purple-700 bg-gradient-to-br from-purple-50 via-white to-purple-50 dark:from-purple-950/40 dark:via-gray-800 dark:to-purple-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-purple-200/20 dark:bg-purple-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-purple-700 dark:text-purple-300 font-bold">{{ __('Courses') }}</p>
                    <i class="bi bi-book-fill text-lg text-purple-500 dark:text-purple-400"></i>
                </div>
                <p class="text-lg font-bold text-purple-900 dark:text-purple-100">{{ number_format($courses ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-purple-600 dark:text-purple-400 space-y-0.5">
                    <p>↳ Currently running</p>
                    <p>🎓 All semesters</p>
                </div>
            </div>
        </div>

        <!-- Attendance Rate Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-green-300 dark:border-green-700 bg-gradient-to-br from-green-50 via-white to-green-50 dark:from-green-950/40 dark:via-gray-800 dark:to-green-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-green-200/20 dark:bg-green-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-green-700 dark:text-green-300 font-bold">{{ __('Attendance') }}</p>
                    <i class="bi bi-check-circle-fill text-lg text-green-500 dark:text-green-400"></i>
                </div>
                <p class="text-lg font-bold text-green-900 dark:text-green-100">{{ isset($avgAttendance) ? $avgAttendance . '%' : '—' }}</p>
                <div class="mt-0.5 text-[9px] text-green-600 dark:text-green-400 space-y-0.5">
                    <p>↳ Semester average</p>
                    <p>📊 Target: 85%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Cards with Enhanced Styling -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2">
        <!-- Parents Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-rose-300 dark:border-rose-700 bg-gradient-to-br from-rose-50 via-white to-rose-50 dark:from-rose-950/40 dark:via-gray-800 dark:to-rose-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-rose-200/20 dark:bg-rose-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-rose-700 dark:text-rose-300 font-bold">{{ __('Parents') }}</p>
                    <i class="bi bi-heart-fill text-lg text-rose-500 dark:text-rose-400"></i>
                </div>
                <p class="text-lg font-bold text-rose-900 dark:text-rose-100">{{ number_format($parents ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-rose-600 dark:text-rose-400 space-y-0.5">
                    <p>↳ {{ $totalStudents + $parents + $teachers > 0 ? round(($parents / ($totalStudents + $parents + $teachers)) * 100, 1) : 0 }}% registered</p>
                    <p>💬 Engagement: High</p>
                </div>
            </div>
        </div>

        <!-- Alumni Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-amber-300 dark:border-amber-700 bg-gradient-to-br from-amber-50 via-white to-amber-50 dark:from-amber-950/40 dark:via-gray-800 dark:to-amber-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-amber-200/20 dark:bg-amber-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-amber-700 dark:text-amber-300 font-bold">{{ __('Alumni') }}</p>
                    <i class="bi bi-mortarboard-fill text-lg text-amber-500 dark:text-amber-400"></i>
                </div>
                <p class="text-lg font-bold text-amber-900 dark:text-amber-100">{{ number_format($alumni ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-amber-600 dark:text-amber-400 space-y-0.5">
                    <p>↳ Graduated: {{ $totalStudents + $alumni > 0 ? round(($alumni / ($totalStudents + $alumni)) * 100, 1) : 0 }}%</p>
                    <p>🌟 Success rate</p>
                </div>
            </div>
        </div>

        <!-- Active Semesters Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-teal-300 dark:border-teal-700 bg-gradient-to-br from-teal-50 via-white to-teal-50 dark:from-teal-950/40 dark:via-gray-800 dark:to-teal-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-teal-200/20 dark:bg-teal-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-teal-700 dark:text-teal-300 font-bold">{{ __('Semesters') }}</p>
                    <i class="bi bi-calendar2-check text-lg text-teal-500 dark:text-teal-400"></i>
                </div>
                <p class="text-lg font-bold text-teal-900 dark:text-teal-100">{{ number_format($activeSemesters ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-teal-600 dark:text-teal-400 space-y-0.5">
                    <p>↳ Currently running</p>
                    <p>⏱️ On schedule</p>
                </div>
            </div>
        </div>

        <!-- Electives Card -->
        <div class="relative overflow-hidden rounded-xl border-2 border-indigo-300 dark:border-indigo-700 bg-gradient-to-br from-indigo-50 via-white to-indigo-50 dark:from-indigo-950/40 dark:via-gray-800 dark:to-indigo-900/40 p-2 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-indigo-200/20 dark:bg-indigo-600/20 rounded-full blur-xl group-hover:blur-2xl transition"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] uppercase tracking-widest text-indigo-700 dark:text-indigo-300 font-bold">{{ __('Electives') }}</p>
                    <i class="bi bi-star-fill text-lg text-indigo-500 dark:text-indigo-400"></i>
                </div>
                <p class="text-lg font-bold text-indigo-900 dark:text-indigo-100">{{ number_format($electiveStudents ?? 0) }}</p>
                <div class="mt-0.5 text-[9px] text-indigo-600 dark:text-indigo-400 space-y-0.5">
                    <p>↳ {{ $totalStudents > 0 ? round(($electiveStudents / $totalStudents) * 100, 1) : 0 }}% enrolled</p>
                    <p>★ Trending choice</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section with Enhanced Styling -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Attendance Bar Chart -->
        <div class="xl:col-span-7 rounded-xl border-2 border-emerald-300 dark:border-emerald-700 bg-gradient-to-br from-emerald-50/50 to-white dark:from-emerald-950/20 dark:to-gray-800 p-4 lg:p-5 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-graph-up text-emerald-500 dark:text-emerald-400"></i>
                        {{ __('Attendance Overview') }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ __('Daily attendance trend, class coverage, and risk signals') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <label for="attendancePeriod" class="text-xs font-semibold text-gray-700 dark:text-gray-300 bg-emerald-100/50 dark:bg-emerald-900/40 px-3 py-1 rounded-full">{{ __('Period:') }}</label>
                    <select id="attendancePeriod" class="text-xs rounded-lg border-2 border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent py-1 px-2 font-medium">
                        <option value="week">{{ __('Weekly') }}</option>
                        <option value="month">{{ __('Monthly') }}</option>
                        <option value="semester">{{ __('Semester') }}</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-2 mb-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-white/70 dark:bg-emerald-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-300">{{ __('Semester Avg') }}</p>
                    <p class="mt-1 text-lg font-bold text-emerald-900 dark:text-emerald-100">{{ number_format((float) ($avgAttendance ?? 0), 1) }}%</p>
                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">{{ __('Target') }} {{ $attendanceTarget }}%</p>
                </div>
                <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-white/70 dark:bg-sky-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-700 dark:text-sky-300">{{ __('Present') }}</p>
                    <p class="mt-1 text-lg font-bold text-sky-900 dark:text-sky-100">{{ number_format($attendanceSummary['present'] ?? 0) }}</p>
                    <p class="text-[11px] text-sky-700 dark:text-sky-300">{{ __('Marked on time and present') }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-white/70 dark:bg-amber-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-300">{{ __('Absent') }}</p>
                    <p class="mt-1 text-lg font-bold text-amber-900 dark:text-amber-100">{{ number_format($attendanceSummary['absent'] ?? 0) }}</p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300">{{ __('Records needing follow-up') }}</p>
                </div>
                <div class="rounded-xl border border-violet-200 dark:border-violet-800 bg-white/70 dark:bg-violet-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-700 dark:text-violet-300">{{ __('Late') }}</p>
                    <p class="mt-1 text-lg font-bold text-violet-900 dark:text-violet-100">{{ number_format($attendanceSummary['late'] ?? 0) }}</p>
                    <p class="text-[11px] text-violet-700 dark:text-violet-300">{{ __('Late arrivals in records') }}</p>
                </div>
            </div>
            <div class="h-72 relative">
                <canvas id="attendanceBarChart"></canvas>
                <p id="attendanceBarNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-28">{{ __('No attendance data available.') }}</p>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mt-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/30 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-300">{{ __('Current View Avg') }}</p>
                    <p id="attendanceSelectedAverage" class="mt-1 text-lg font-bold text-emerald-900 dark:text-emerald-100">{{ number_format($initialAttendanceAverage, 1) }}%</p>
                    <p id="attendanceSelectedAverageNote" class="text-[11px] text-emerald-700 dark:text-emerald-300">{{ $activeAttendanceBuckets->count() }} {{ __('active buckets') }}</p>
                </div>
                <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50/70 dark:bg-sky-950/30 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-700 dark:text-sky-300">{{ __('Best Period') }}</p>
                    <p id="attendanceBestLabel" class="mt-1 text-sm font-bold text-sky-900 dark:text-sky-100">{{ $initialAttendanceBest['label'] ?? __('No records') }}</p>
                    <p id="attendanceBestValue" class="text-[11px] text-sky-700 dark:text-sky-300">
                        {{ isset($initialAttendanceBest['percentage']) ? number_format((float) $initialAttendanceBest['percentage'], 1) . '%' . ' ' . __('attendance') : __('Waiting for data') }}
                    </p>
                </div>
                <div class="rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50/70 dark:bg-rose-950/30 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-700 dark:text-rose-300">{{ __('Needs Attention') }}</p>
                    <p id="attendanceLowestLabel" class="mt-1 text-sm font-bold text-rose-900 dark:text-rose-100">{{ $initialAttendanceLowest['label'] ?? __('No records') }}</p>
                    <p id="attendanceLowestValue" class="text-[11px] text-rose-700 dark:text-rose-300">
                        {{ isset($initialAttendanceLowest['percentage']) ? number_format((float) $initialAttendanceLowest['percentage'], 1) . '%' . ' ' . __('attendance') : __('Waiting for data') }}
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/60 px-3 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-600 dark:text-slate-300">{{ __('Coverage') }}</p>
                    <p id="attendanceCoverageValue" class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ number_format($initialAttendancePresent) }} / {{ number_format($initialAttendanceTracked) }}</p>
                    <p id="attendanceCoverageNote" class="text-[11px] text-slate-600 dark:text-slate-300">{{ number_format($initialAttendanceNotPresent) }} {{ __('not present in this range') }}</p>
                </div>
            </div>
        </div>

        <!-- Grade Distribution Pie Chart -->
        <div class="xl:col-span-5 rounded-xl border-2 border-fuchsia-300 dark:border-fuchsia-700 bg-gradient-to-br from-fuchsia-50/50 to-white dark:from-fuchsia-950/20 dark:to-gray-800 p-4 lg:p-5 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-fuchsia-500 dark:text-fuchsia-400"></i>
                        {{ __('Grade Distribution') }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ __('Academic performance mix, pass rate, and support indicators') }}</p>
                </div>
                <span class="text-xs px-3 py-2 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900 dark:to-emerald-900 dark:text-green-200 font-bold border border-green-300 dark:border-green-700">
                    {{ $totalGradedRecords }} {{ __('Graded') }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-white/70 dark:bg-emerald-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-300">{{ __('Pass Rate') }}</p>
                    <p class="mt-1 text-lg font-bold text-emerald-900 dark:text-emerald-100">{{ number_format($passRate, 1) }}%</p>
                    <p class="text-[11px] text-emerald-700 dark:text-emerald-300">{{ $passCount }} {{ __('students cleared') }}</p>
                </div>
                <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-white/70 dark:bg-blue-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-blue-700 dark:text-blue-300">{{ __('A Range') }}</p>
                    <p class="mt-1 text-lg font-bold text-blue-900 dark:text-blue-100">{{ number_format($distinctionRate, 1) }}%</p>
                    <p class="text-[11px] text-blue-700 dark:text-blue-300">{{ $distinctionCount }} {{ __('top grades') }}</p>
                </div>
                <div class="rounded-xl border border-rose-200 dark:border-rose-800 bg-white/70 dark:bg-rose-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-700 dark:text-rose-300">{{ __('Need Support') }}</p>
                    <p class="mt-1 text-lg font-bold text-rose-900 dark:text-rose-100">{{ number_format($needsAttentionRate, 1) }}%</p>
                    <p class="text-[11px] text-rose-700 dark:text-rose-300">{{ $needsAttentionCount }} {{ __('in D or F') }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-white/70 dark:bg-amber-950/30 px-3 py-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-300">{{ __('Top Band') }}</p>
                    <p class="mt-1 text-lg font-bold text-amber-900 dark:text-amber-100">{{ $topGrade }}</p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300">{{ $topGradeCount }} {{ __('students in this band') }}</p>
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
                    <div class="rounded-lg border py-2 px-1 font-bold transition hover:shadow-md" style="border-color: {{ $gradeColors[$grade] ?? '#6b7280' }}; background-color: {{ $gradeColors[$grade] ?? '#6b7280' }}12;">
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
        <div class="xl:col-span-4 rounded-xl border-2 border-yellow-300 dark:border-yellow-700 bg-gradient-to-br from-yellow-50/50 to-white dark:from-yellow-950/20 dark:to-gray-800 p-4 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-megaphone-fill text-yellow-500 dark:text-yellow-400"></i>
                    {{ __('Notices') }}
                </h2>
                <a href="{{ route('admin.notice-board') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900/60 transition">
                    {{ __('All') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if(!empty($recentNotices) && count($recentNotices) > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($recentNotices as $notice)
                        <div class="rounded-lg border-l-4 border-l-yellow-500 bg-yellow-50/50 dark:bg-yellow-900/20 p-2.5 hover:bg-yellow-100/50 dark:hover:bg-yellow-900/30 transition">
                            <h3 class="text-xs font-bold text-yellow-900 dark:text-yellow-100 line-clamp-2">{{ $notice['title'] }}</h3>
                            <p class="text-xs text-yellow-800 dark:text-yellow-200 mt-0.5 line-clamp-1">{{ $notice['message'] }}</p>
                            <p class="text-[10px] text-yellow-700 dark:text-yellow-300 mt-1 font-semibold">⏰ {{ \Carbon\Carbon::parse($notice['created_at'])->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-bell-slash text-4xl text-yellow-200 dark:text-yellow-700"></i>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-3 font-semibold">{{ __('No notices available') }}</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Exams -->
        <div class="xl:col-span-4 rounded-xl border-2 border-red-300 dark:border-red-700 bg-gradient-to-br from-red-50/50 to-white dark:from-red-950/20 dark:to-gray-800 p-4 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-pencil-square text-red-500 dark:text-red-400"></i>
                    {{ __('Exams') }}
                </h2>
                <a href="{{ route('admin.exam') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/60 transition">
                    {{ __('Open') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if($upcomingExams->count() > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($upcomingExams as $exam)
                        <div class="flex items-start justify-between gap-2 rounded-lg border-l-4 border-l-red-500 bg-red-50/50 dark:bg-red-900/20 p-2.5 hover:bg-red-100/50 dark:hover:bg-red-900/30 transition">
                            <div>
                                <h3 class="text-xs font-bold text-red-900 dark:text-red-100">{{ $exam['name'] }}</h3>
                                <p class="text-xs text-red-700 dark:text-red-300 mt-0.5 font-medium">{{ $exam['subject_name'] }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-bold text-red-800 dark:text-red-200 bg-red-100/50 dark:bg-red-900/50 px-1.5 py-0.5 rounded">📅 {{ \Carbon\Carbon::parse($exam['exam_date'])->format('M d') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-calendar-x text-4xl text-red-200 dark:text-red-700"></i>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-3 font-semibold">{{ __('No upcoming exams') }}</p>
                </div>
            @endif
        </div>

        <!-- Today's Classes -->
        <div class="xl:col-span-4 rounded-xl border-2 border-cyan-300 dark:border-cyan-700 bg-gradient-to-br from-cyan-50/50 to-white dark:from-cyan-950/20 dark:to-gray-800 p-6 hover:shadow-xl transition">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="bi bi-calendar-day text-cyan-500 dark:text-cyan-400"></i>
                {{ __("Today's Classes") }}
            </h2>
            @if($todayClasses->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($todayClasses as $class)
                        <div class="rounded-lg border-l-4 border-l-cyan-500 bg-cyan-50/50 dark:bg-cyan-900/20 p-3.5 hover:bg-cyan-100/50 dark:hover:bg-cyan-900/30 transition">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm font-bold text-cyan-900 dark:text-cyan-100">{{ $class['subject_name'] }}</p>
                                    <p class="text-xs text-cyan-700 dark:text-cyan-300 font-medium">Sem: {{ $class['semester'] }}</p>
                                </div>
                                <span class="text-sm font-bold px-2 py-1 rounded-full bg-gradient-to-r from-cyan-400 to-cyan-600 text-white">{{ $class['attendance_rate'] }}%</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-2 text-[10px] font-bold text-center">
                                <span class="rounded-lg bg-green-100 dark:bg-green-900/50 py-1.5 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-700">✓ {{ $class['present_count'] }}</span>
                                <span class="rounded-lg bg-red-100 dark:bg-red-900/50 py-1.5 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-700">✕ {{ $class['absent_count'] }}</span>
                                <span class="rounded-lg bg-blue-100 dark:bg-blue-900/50 py-1.5 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-700">👥 {{ $class['total_students'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-calendar-check text-4xl text-cyan-200 dark:text-cyan-700"></i>
                    <p class="text-sm text-cyan-700 dark:text-cyan-300 mt-3 font-semibold">{{ __('No classes recorded today.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Third Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="rounded-xl border-2 border-violet-300 dark:border-violet-700 bg-gradient-to-br from-violet-50/50 to-white dark:from-violet-950/20 dark:to-gray-800 p-4 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-clock-history text-violet-500 dark:text-violet-400"></i>
                    {{ __('Activities') }}
                </h2>
                <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 hover:bg-violet-200 dark:hover:bg-violet-900/60 transition">
                    {{ __('All') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if(!empty($recentActivities) && count($recentActivities) > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($recentActivities as $act)
                        <div class="rounded-lg border-l-4 border-l-violet-500 bg-violet-50/50 dark:bg-violet-900/20 p-2.5 hover:bg-violet-100/50 dark:hover:bg-violet-900/30 transition">
                            <div class="flex items-start gap-1.5">
                                <div class="relative mt-0.5 flex-shrink-0\">
                                    <div class="w-2 h-2 bg-violet-500 rounded-full animate-pulse\"></div>
                                </div>
                                <div class="min-w-0 flex-1\">
                                    <p class="text-xs font-bold text-violet-900 dark:text-violet-100\">{{ $act['action'] }}</p>
                                    @if(!empty($act['details']))
                                        <p class="text-xs text-violet-700 dark:text-violet-300 truncate\">{{ $act['details'] }}</p>
                                    @else
                                        <p class="text-xs text-violet-700 dark:text-violet-300 truncate\">{{ $act['user_name'] }}</p>
                                    @endif
                                    <p class="text-[10px] text-violet-600 dark:text-violet-400 mt-0.5 font-semibold\">⏲ {{ \Carbon\Carbon::parse($act['timestamp'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-inbox text-3xl text-violet-200 dark:text-violet-700"></i>
                    <p class="text-xs text-violet-700 dark:text-violet-300 mt-2 font-semibold\">{{ __('No activities yet') }}</p>
                </div>
            @endif
        </div>

        <!-- New Students -->
        <div class="rounded-xl border-2 border-sky-300 dark:border-sky-700 bg-gradient-to-br from-sky-50/50 to-white dark:from-sky-950/20 dark:to-gray-800 p-4 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-person-plus-fill text-sky-500 dark:text-sky-400"></i>
                    {{ __('New Students') }}
                </h2>
                <a href="{{ route('admin.students') }}" class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 hover:bg-sky-200 dark:hover:bg-sky-900/60 transition">
                    {{ __('All') }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if(!empty($newStudents) && count($newStudents) > 0)
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @foreach($newStudents as $student)
                        <div class="rounded-lg border-l-4 border-l-sky-500 bg-sky-50/50 dark:bg-sky-900/20 p-2.5 hover:bg-sky-100/50 dark:hover:bg-sky-900/30 transition">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0 shadow-md">
                                    {{ substr($student['name'], 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-sky-900 dark:text-sky-100 truncate">{{ $student['name'] }}</p>
                                    <p class="text-xs text-sky-700 dark:text-sky-300 truncate">{{ $student['email'] }}</p>
                                    <p class="text-[10px] text-sky-600 dark:text-sky-400 mt-0.5 font-semibold">🆕 {{ \Carbon\Carbon::parse($student['created_at'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-inbox text-3xl text-sky-200 dark:text-sky-700"></i>
                    <p class="text-xs text-sky-700 dark:text-sky-300 mt-2 font-semibold">{{ __('No new students') }}</p>
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
