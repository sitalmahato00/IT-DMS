@extends('teacher.layouts.teacherlayout')

@section('title', __('Teacher Dashboard'))

@section('content')
@php
    $teacherName = $user->name ?? 'Teacher';
    $attendanceSummary = $attendanceSummary ?? ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
    $presentCount = (int) ($attendanceSummary['present'] ?? 0);
    $absentCount = (int) ($attendanceSummary['absent'] ?? 0);
    $lateCount = (int) ($attendanceSummary['late'] ?? 0);
    $attendanceTotal = (int) ($attendanceSummary['total'] ?? 0);
    $totalGradedRecords = array_sum($gradeDistribution ?? []);
@endphp

<div class="teacher-dashboard-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
<div class="teacher-dashboard-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-700 via-red-600 to-rose-600 p-6 md:p-8 text-white shadow-xl">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-16 w-56 h-56 rounded-full bg-black/10 blur-3xl"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">{{ __('Welcome back,') }} {{ $teacherName }}</h1>
                <p class="text-white/80 mt-2">{{ __('Manage attendance, marks, exams, and class communications in one place.') }}</p>
                <p class="text-white/80 mt-1 text-sm">
                    {{ __('Sightboard:') }}
                    {{ $subjectCount ?? 0 }} {{ __('subjects') }},
                    {{ $totalStudents ?? 0 }} {{ __('students') }},
                    {{ $attendanceTotal ?? 0 }} {{ __('attendance entries') }}.
                </p>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-white/80">
                    <div class="rounded-lg bg-white/10 p-2">
                        <strong>{{ __('Average Attendance') }}:</strong> {{ $avgAttendance ?? 0 }}%
                    </div>
                    <div class="rounded-lg bg-white/10 p-2">
                        <strong>{{ __('Graded Records') }}:</strong> {{ $totalGradedRecords }}
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap gap-3 text-sm text-white/90">
                    @if($teacher && $teacher->teacher_code)
                        <span class="teacher-dashboard-hero-pill inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-white">
                            <i class="bi bi-person-badge"></i> {{ __('Code:') }} {{ $teacher->teacher_code }}
                        </span>
                    @endif
                    @if($teacher && $teacher->gender)
                        <span class="teacher-dashboard-hero-pill inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25 text-white">
                            <i class="bi bi-person"></i> {{ ucfirst($teacher->gender) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
<div class="teacher-dashboard-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Teaching Subjects') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjectCount }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Assigned to your profile') }}</p>
        </div>

        <div class="teacher-dashboard-card rounded-xl border border-blue-200 dark:border-blue-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Total Students') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalStudents }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Active students only') }}</p>
        </div>

        <div class="teacher-dashboard-card rounded-xl border border-purple-200 dark:border-purple-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-purple-700 dark:text-purple-300 font-semibold">{{ __('Average Attendance') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $avgAttendance }}%</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Across your subjects') }}</p>
        </div>

        <div class="teacher-dashboard-card rounded-xl border border-amber-200 dark:border-amber-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">{{ __('Graded Records') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalGradedRecords }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('From uploaded exam marks') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="teacher-dashboard-card xl:col-span-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Attendance') }}</h2>
                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                    {{ $attendanceTotal }} {{ __('records') }}
                </span>
            </div>

            <!-- Period Selector -->
            <div class="flex gap-2 mb-4">
                <button onclick="changeAttendancePeriod('semester')" id="periodBtnSemester" class="teacher-dashboard-period-btn flex-1 px-3 py-2 rounded-lg text-xs font-medium bg-red-600 text-white hover:bg-red-700 transition">
                    {{ __('Semester') }}
                </button>
                <button onclick="changeAttendancePeriod('monthly')" id="periodBtnMonthly" class="teacher-dashboard-period-btn flex-1 px-3 py-2 rounded-lg text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    {{ __('Monthly') }}
                </button>
                <button onclick="changeAttendancePeriod('weekly')" id="periodBtnWeekly" class="teacher-dashboard-period-btn flex-1 px-3 py-2 rounded-lg text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    {{ __('Weekly') }}
                </button>
            </div>

            <div class="h-56">
                <canvas id="attendanceBarChart"></canvas>
                <p id="attendanceBarNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-20">{{ __('No attendance records yet.') }}</p>
            </div>
        </div>

        <div class="teacher-dashboard-card xl:col-span-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Marks Pie') }}</h2>
<a href="{{ route('teacher.marks') }}" class="text-xs font-medium text-red-700 dark:text-red-400 hover:underline">{{ __('Manage Marks') }}</a>
            </div>
            <div class="h-72">
                <canvas id="marksDistributionChart"></canvas>
                <p id="marksChartNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No graded marks found yet.') }}</p>
            </div>
        </div>

        <div class="teacher-dashboard-card xl:col-span-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Notices') }}</h2>
<a href="{{ route('teacher.notices') }}" class="text-xs font-medium text-red-700 dark:text-red-400 hover:underline">{{ __('View All') }}</a>
            </div>
            @if($recentNotices->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($recentNotices as $notice)
                        <div class="teacher-dashboard-notice-item rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $notice['title'] }}</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $notice['message'] }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-500 mt-2">{{ \Carbon\Carbon::parse($notice['created_at'])->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-bell-slash text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No notices available') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="teacher-dashboard-card xl:col-span-7 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('7-Day Attendance Trend') }}</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Percentage (%)') }}</span>
            </div>
            <div class="h-72">
                <canvas id="attendanceTrendChart"></canvas>
                <p id="attendanceTrendNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No trend data available.') }}</p>
            </div>
        </div>

        <div class="xl:col-span-5 space-y-6">
            <div class="teacher-dashboard-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Upcoming Exams') }}</h2>
<a href="{{ route('teacher.exams') }}" class="text-xs font-medium text-red-700 dark:text-red-400 hover:underline">{{ __('Open Exams') }}</a>
                </div>
                @if($upcomingExams->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingExams as $exam)
                            <div class="teacher-dashboard-list-item flex items-start justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $exam['name'] }}</h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $exam['subject_name'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($exam['exam_date'])->format('M d, Y') }}</p>
                                    @if($exam['exam_date_bs'])
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $exam['exam_date_bs'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No upcoming exams') }}</p>
                @endif
            </div>

            <div class="teacher-dashboard-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __("Today's Classes") }}</h2>
                @if($todayClasses->count() > 0)
                    <div class="space-y-3">
                        @foreach($todayClasses as $class)
                            <div class="teacher-dashboard-class-card rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $class['subject_name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Semester') }}: {{ $class['semester'] }}</p>
                                    </div>
<span class="text-sm font-bold text-red-700 dark:text-red-400">{{ $class['attendance_rate'] }}%</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mt-3 text-[11px] text-center">
<span class="rounded bg-red-50 dark:bg-red-900/30 py-1 text-red-700 dark:text-red-300">{{ $class['present_count'] }} {{ __('Present') }}</span>
                                    <span class="rounded bg-red-50 dark:bg-red-900/30 py-1 text-red-700 dark:text-red-300">{{ $class['absent_count'] }} {{ __('Absent') }}</span>
                                    <span class="rounded bg-gray-100 dark:bg-gray-700 py-1 text-gray-700 dark:text-gray-300">{{ $class['total_students'] }} {{ __('Total') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No classes recorded today.') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="teacher-dashboard-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('My Subjects - Detailed View') }}</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ count($subjects) }} {{ __('assigned') }}</span>
        </div>
        <div class="p-5">
            @if(count($subjects) > 0)
                <div class="space-y-5">
                    @foreach($subjects as $subject)
                        <div class="teacher-dashboard-subject-card rounded-lg border border-gray-300 dark:border-gray-600 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-600/50 p-5 hover:shadow-md transition">
                            <!-- Subject Header -->
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $subject['name'] }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        <span class="font-semibold">{{ $subject['code'] }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ __('Semester') }} {{ $subject['semester'] }}</span>
                                        @if($subject['role'])
                                            <span class="mx-2">•</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">{{ ucfirst($subject['role']) }}</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('teacher.subjects.show', $subject['id']) }}" class="teacher-dashboard-action-link inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 transition">
                                    <i class="bi bi-eye mr-1"></i> {{ __('View') }}
                                </a>
                            </div>

                            <!-- Statistics Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4">
                                <!-- Students Count -->
                                <div class="teacher-dashboard-mini-stat rounded-lg bg-white dark:bg-gray-700/40 p-3 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 font-semibold">{{ __('Students') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $subject['student_count'] }}</p>
                                </div>

                                <!-- Attendance -->
                                <div class="teacher-dashboard-mini-stat rounded-lg bg-white dark:bg-gray-700/40 p-3 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 font-semibold">{{ __('Attendance') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $subject['attendance']['percentage'] }}%</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['attendance']['present'] }}/{{ $subject['attendance']['total'] }}</p>
                                </div>

                                <!-- Average Marks -->
                                <div class="teacher-dashboard-mini-stat rounded-lg bg-white dark:bg-gray-700/40 p-3 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 font-semibold">{{ __('Avg Marks') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $subject['marks']['average'] > 0 ? number_format($subject['marks']['average'], 1) : '--' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['marks']['total_records'] }} {{ __('records') }}</p>
                                </div>

                                <!-- Max Marks -->
                                <div class="teacher-dashboard-mini-stat rounded-lg bg-white dark:bg-gray-700/40 p-3 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 font-semibold">{{ __('Highest') }}</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $subject['marks']['max'] > 0 ? number_format($subject['marks']['max'], 1) : '--' }}</p>
                                </div>

                                <!-- Min Marks -->
                                <div class="teacher-dashboard-mini-stat rounded-lg bg-white dark:bg-gray-700/40 p-3 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 font-semibold">{{ __('Lowest') }}</p>
                                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $subject['marks']['min'] > 0 ? number_format($subject['marks']['min'], 1) : '--' }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                                 <a href="{{ route('teacher.attendance') }}?subject={{ $subject['id'] }}" class="teacher-dashboard-action-link flex-1 text-center px-3 py-2 rounded-lg text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                    <i class="bi bi-calendar-check"></i> {{ __('Class Attendance') }}
                                </a>
                                 <a href="{{ route('teacher.marks') }}?subject={{ $subject['id'] }}" class="teacher-dashboard-action-link flex-1 text-center px-3 py-2 rounded-lg text-sm font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                                    <i class="bi bi-clipboard-data"></i> {{ __('Marks') }}
                                </a>
                                 <a href="{{ route('teacher.students') }}?subject={{ $subject['id'] }}" class="teacher-dashboard-action-link flex-1 text-center px-3 py-2 rounded-lg text-sm font-medium text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                                    <i class="bi bi-people"></i> {{ __('Students') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-book text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No subjects assigned yet.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let attendanceBarChart = null;
    let marksDistributionChart = null;
    let attendanceTrendChart = null;
    let currentAttendancePeriod = 'semester';
    let attendanceChartDataByPeriod = {};
    let gradeDistributionData = {};
    let barCanvas = null;
    let barNoData = null;
    let labelColor = '#4b5563';
    let gridColor = 'rgba(229, 231, 235, 0.9)';

    const attendanceTranslations = {
        presentPercent: @json(__('Present %')),
    };

    function syncTeacherDashboardPalette() {
        const isDark = document.documentElement.classList.contains('dark');
        labelColor = isDark ? '#cbd5e1' : '#4b5563';
        gridColor = isDark ? 'rgba(71, 85, 105, 0.46)' : 'rgba(229, 231, 235, 0.9)';
    }

    function changeAttendancePeriod(period) {
        currentAttendancePeriod = period;

        const btnSemester = document.getElementById('periodBtnSemester');
        const btnMonthly = document.getElementById('periodBtnMonthly');
        const btnWeekly = document.getElementById('periodBtnWeekly');

        if (btnSemester) {
            btnSemester.classList.toggle('bg-red-600', period === 'semester');
            btnSemester.classList.toggle('text-white', period === 'semester');
            btnSemester.classList.toggle('bg-gray-200', period !== 'semester');
            btnSemester.classList.toggle('dark:bg-gray-700', period !== 'semester');
            btnSemester.classList.toggle('text-gray-700', period !== 'semester');
            btnSemester.classList.toggle('dark:text-gray-300', period !== 'semester');
        }

        if (btnMonthly) {
            btnMonthly.classList.toggle('bg-red-600', period === 'monthly');
            btnMonthly.classList.toggle('text-white', period === 'monthly');
            btnMonthly.classList.toggle('bg-gray-200', period !== 'monthly');
            btnMonthly.classList.toggle('dark:bg-gray-700', period !== 'monthly');
            btnMonthly.classList.toggle('text-gray-700', period !== 'monthly');
            btnMonthly.classList.toggle('dark:text-gray-300', period !== 'monthly');
        }

        if (btnWeekly) {
            btnWeekly.classList.toggle('bg-red-600', period === 'weekly');
            btnWeekly.classList.toggle('text-white', period === 'weekly');
            btnWeekly.classList.toggle('bg-gray-200', period !== 'weekly');
            btnWeekly.classList.toggle('dark:bg-gray-700', period !== 'weekly');
            btnWeekly.classList.toggle('text-gray-700', period !== 'weekly');
            btnWeekly.classList.toggle('dark:text-gray-300', period !== 'weekly');
        }

        if (barCanvas && attendanceChartDataByPeriod[period]) {
            renderAttendanceBarChart(period);
        }
    }

    function hasAttendanceDataForPeriod(period, data) {
        if (!data || !Array.isArray(data.labels) || data.labels.length === 0) {
            return false;
        }

        const presentValues = Array.isArray(data.present) ? data.present : [];
        return presentValues.some((value) => Number(value) > 0);
    }

    function buildAttendanceDatasets(period, data) {
        if (!data) {
            return [];
        }

        return [{
            label: attendanceTranslations.presentPercent,
            data: data.present || [],
            backgroundColor: '#16a34a',
            borderRadius: 6,
            borderWidth: 0
        }];
    }

    function renderAttendanceBarChart(period) {
        if (!barCanvas) {
            return;
        }

        const data = attendanceChartDataByPeriod[period] ?? { labels: [], present: [], absent: [], late: [] };
        const hasData = hasAttendanceDataForPeriod(period, data);

        if (!hasData) {
            barCanvas.classList.add('hidden');
            if (barNoData) {
                barNoData.classList.remove('hidden');
            }

            if (attendanceBarChart) {
                attendanceBarChart.destroy();
                attendanceBarChart = null;
            }
            return;
        }

        barCanvas.classList.remove('hidden');
        if (barNoData) {
            barNoData.classList.add('hidden');
        }

        const datasets = buildAttendanceDatasets(period, data);
        const stacked = false;
        const yAxisConfig = {
            max: 100,
            ticks: {
                stepSize: 20,
                callback: (value) => `${value}%`,
                color: labelColor
            }
        };

        if (attendanceBarChart) {
            attendanceBarChart.destroy();
        }

        attendanceBarChart = new Chart(barCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: labelColor,
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 11 }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked,
                        ticks: { color: labelColor },
                        grid: { display: false }
                    },
                    y: {
                        stacked,
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: yAxisConfig.ticks,
                        ...(yAxisConfig.max !== undefined ? { max: yAxisConfig.max } : {})
                    }
                }
            }
        });

    }

    document.addEventListener('DOMContentLoaded', function () {
        const attendanceChartDataRaw = @json($attendanceChartData);
        gradeDistributionData = @json($gradeDistribution);

        attendanceChartDataByPeriod = {
            'semester': attendanceChartDataRaw.semester || { labels: [], present: [], absent: [], late: [] },
            'monthly': attendanceChartDataRaw.monthly || { labels: [], present: [], absent: [], late: [] },
            'weekly': attendanceChartDataRaw.weekly || { labels: [], present: [], absent: [], late: [] }
        };

        syncTeacherDashboardPalette();

        barCanvas = document.getElementById('attendanceBarChart');
        barNoData = document.getElementById('attendanceBarNoData');

        changeAttendancePeriod('semester');

        renderMarksDistributionChart();
        renderAttendanceTrendChart();
        observeTeacherDashboardTheme();
    });

    function renderMarksDistributionChart() {
        const marksCanvas = document.getElementById('marksDistributionChart');
        const marksNoData = document.getElementById('marksChartNoData');

        if (!marksCanvas) {
            return;
        }

        const gradeLabels = Object.keys(gradeDistributionData || {});
        const gradeData = gradeLabels.map((label) => Number(gradeDistributionData[label] || 0));
        const hasGradeData = gradeData.some((value) => value > 0);

        if (marksDistributionChart) {
            marksDistributionChart.destroy();
            marksDistributionChart = null;
        }

        if (!hasGradeData) {
            marksCanvas.classList.add('hidden');
            if (marksNoData) {
                marksNoData.classList.remove('hidden');
            }
            return;
        }

        marksCanvas.classList.remove('hidden');
        if (marksNoData) {
            marksNoData.classList.add('hidden');
        }

        const colors = ['#1f2937', '#dc2626', '#ea580c', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#0ea5e9', '#6366f1', '#a855f7'];
        marksDistributionChart = new Chart(marksCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: gradeLabels,
                datasets: [{
                    data: gradeData,
                    backgroundColor: colors.slice(0, gradeLabels.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: labelColor,
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }

    function renderAttendanceTrendChart() {
        const trendCanvas = document.getElementById('attendanceTrendChart');
        const trendNoData = document.getElementById('attendanceTrendNoData');

        if (!trendCanvas) {
            return;
        }

        const weeklyData = attendanceChartDataByPeriod.weekly || { labels: [], present: [] };
        const trendLabels = weeklyData.labels || [];
        const trendValues = Array.isArray(weeklyData.present)
            ? weeklyData.present.map((value) => Number(value || 0))
            : [];
        const hasTrendData = trendLabels.length > 0 && trendValues.some((value) => value > 0);

        if (attendanceTrendChart) {
            attendanceTrendChart.destroy();
            attendanceTrendChart = null;
        }

        if (!hasTrendData) {
            trendCanvas.classList.add('hidden');
            if (trendNoData) {
                trendNoData.classList.remove('hidden');
            }
            return;
        }

        trendCanvas.classList.remove('hidden');
        if (trendNoData) {
            trendNoData.classList.add('hidden');
        }

        attendanceTrendChart = new Chart(trendCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Attendance %',
                    data: trendValues,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(34, 197, 94, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: labelColor },
                        grid: { display: false }
                    },
                    y: {
                        min: 0,
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
    }

    function observeTeacherDashboardTheme() {
        const root = document.documentElement;
        const themeObserver = new MutationObserver((mutations) => {
            const classMutation = mutations.some((mutation) => mutation.attributeName === 'class');
            if (!classMutation) {
                return;
            }

            syncTeacherDashboardPalette();
            renderAttendanceBarChart(currentAttendancePeriod);
            renderMarksDistributionChart();
            renderAttendanceTrendChart();
        });

        themeObserver.observe(root, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
</script>
@endsection
