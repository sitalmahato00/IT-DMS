@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
@php
    $adminName = $user->name ?? 'Administrator';
    $totalGradedRecords = array_sum($gradeDistribution ?? []);
@endphp

<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/20 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-16 w-56 h-56 rounded-full bg-[#D90033]/40 blur-3xl"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">{{ __('Welcome back,') }} {{ $adminName }}</h1>
                <p class="text-[#ffe5ea] mt-2">{{ __('Manage students, teachers, attendance, and all administrative tasks from here.') }}</p>
                <div class="mt-3 flex flex-wrap gap-3 text-sm text-[#ffe5ea]">
                    @if($college && $college->name)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                            <i class="bi bi-building"></i> {{ $college->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-person-badge"></i> {{ __('Administrator') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-blue-200 dark:border-blue-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Total Students') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalStudents ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Active students enrolled') }}</p>
        </div>

        <div class="rounded-xl border border-orange-200 dark:border-orange-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-orange-700 dark:text-orange-300 font-semibold">{{ __('Teachers') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($teachers ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Faculty members') }}</p>
        </div>

        <div class="rounded-xl border border-purple-200 dark:border-purple-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-purple-700 dark:text-purple-300 font-semibold">{{ __('Courses') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($courses ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Total courses') }}</p>
        </div>

        <div class="rounded-xl border border-green-200 dark:border-green-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-green-700 dark:text-green-300 font-semibold">{{ __('Attendance Rate') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ isset($avgAttendance) ? $avgAttendance . '%' : '—' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Average this semester') }}</p>
        </div>
    </div>

    <!-- Additional Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-purple-200 dark:border-purple-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-purple-700 dark:text-purple-300 font-semibold">{{ __('Parents') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($parents ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Registered parents') }}</p>
        </div>

        <div class="rounded-xl border border-amber-200 dark:border-amber-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-amber-700 dark:text-amber-300 font-semibold">{{ __('Alumni') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($alumni ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Graduated students') }}</p>
        </div>

        <div class="rounded-xl border border-teal-200 dark:border-teal-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-teal-700 dark:text-teal-300 font-semibold">{{ __('Active Semesters') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($activeSemesters ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Currently running') }}</p>
        </div>

        <div class="rounded-xl border border-indigo-200 dark:border-indigo-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-indigo-700 dark:text-indigo-300 font-semibold">{{ __('Elective Students') }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($electiveStudents ?? 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Approved electives') }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Attendance Bar Chart -->
        <div class="xl:col-span-7 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Attendance Overview') }}</h2>
                <div class="flex items-center gap-2">
                    <label for="attendancePeriod" class="text-xs text-gray-500 dark:text-gray-400">{{ __('Period') }}</label>
                    <select id="attendancePeriod" class="text-xs rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 py-1 px-2">
                        <option value="week">{{ __('Weekly') }}</option>
                        <option value="month">{{ __('Monthly') }}</option>
                        <option value="semester">{{ __('Semester') }}</option>
                    </select>
                </div>
            </div>
            <div class="h-72">
                <canvas id="attendanceBarChart"></canvas>
                <p id="attendanceBarNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No attendance data available.') }}</p>
            </div>
        </div>

        <!-- Grade Distribution Pie Chart -->
        <div class="xl:col-span-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Grade Distribution') }}</h2>
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                    {{ $totalGradedRecords }} {{ __('graded') }}
                </span>
            </div>
            <div class="h-56">
                <canvas id="gradePieChart"></canvas>
                <p id="gradePieNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-20">{{ __('No grades available yet.') }}</p>
            </div>
            <div class="grid grid-cols-4 gap-2 mt-4 text-center text-xs">
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
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 py-2">
                        <div class="w-3 h-3 mx-auto rounded-full mb-1" style="background-color: {{ $gradeColors[$grade] ?? '#6b7280' }}"></div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $count }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $grade }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Recent Notices -->
        <div class="xl:col-span-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Notices') }}</h2>
                <a href="{{ route('admin.notice-board') }}" class="text-xs font-medium text-blue-700 dark:text-red-400 hover:underline">{{ __('View All') }}</a>
            </div>
            @if(!empty($recentNotices) && count($recentNotices) > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($recentNotices as $notice)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-3">
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

        <!-- Upcoming Exams -->
        <div class="xl:col-span-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Upcoming Exams') }}</h2>
                <a href="{{ route('admin.exam') }}" class="text-xs font-medium text-blue-700 dark:text-red-400 hover:underline">{{ __('Open Exams') }}</a>
            </div>
            @if($upcomingExams->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($upcomingExams as $exam)
                        <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
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
                <div class="text-center py-12">
                    <i class="bi bi-calendar-x text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No upcoming exams') }}</p>
                </div>
            @endif
        </div>

        <!-- Today's Classes -->
        <div class="xl:col-span-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __("Today's Classes") }}</h2>
            @if($todayClasses->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($todayClasses as $class)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $class['subject_name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Semester') }}: {{ $class['semester'] }}</p>
                                </div>
                                <span class="text-sm font-bold text-blue-700 dark:text-red-400">{{ $class['attendance_rate'] }}%</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-3 text-[11px] text-center">
                                <span class="rounded bg-green-50 dark:bg-green-900/30 py-1 text-green-700 dark:text-green-300">{{ $class['present_count'] }} {{ __('Present') }}</span>
                                <span class="rounded bg-blue-50 dark:bg-red-900/30 py-1 text-blue-700 dark:text-red-300">{{ $class['absent_count'] }} {{ __('Absent') }}</span>
                                <span class="rounded bg-gray-100 dark:bg-gray-700 py-1 text-gray-700 dark:text-gray-300">{{ $class['total_students'] }} {{ __('Total') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-calendar-check text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No classes recorded today.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Third Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Activities') }}</h2>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-medium text-blue-700 dark:text-red-400 hover:underline">{{ __('View All') }}</a>
            </div>
            @if(!empty($recentActivities) && count($recentActivities) > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($recentActivities as $act)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-3">
                            <div class="flex items-start gap-2">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">{{ $act['action'] }}</p>
                                    @if(!empty($act['details']))
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $act['details'] }}</p>
                                    @else
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $act['user_name'] }}</p>
                                    @endif
                                    <p class="text-[11px] text-gray-500 dark:text-gray-500 mt-1">{{ \Carbon\Carbon::parse($act['timestamp'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No activities yet') }}</p>
                </div>
            @endif
        </div>

        <!-- New Students -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('New Students') }}</h2>
                <a href="{{ route('admin.students') }}" class="text-xs font-medium text-blue-700 dark:text-red-400 hover:underline">{{ __('View All') }}</a>
            </div>
            @if(!empty($newStudents) && count($newStudents) > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @foreach($newStudents as $student)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-xs font-semibold text-blue-600 dark:text-blue-300 flex-shrink-0">
                                    {{ substr($student['name'], 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $student['name'] }}</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $student['email'] }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-500 mt-1">{{ \Carbon\Carbon::parse($student['created_at'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="bi bi-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No new students') }}</p>
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

        // Common variables
        const isDark = document.documentElement.classList.contains('dark');
        const labelColor = isDark ? '#d1d5db' : '#4b5563';
        const gridColor = isDark ? 'rgba(75, 85, 99, 0.45)' : 'rgba(229, 231, 235, 0.9)';

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
                    data: {
                        labels: gradeLabels,
                        datasets: [{
                            data: gradeData,
                            backgroundColor: colors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
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
                        backgroundColor: '#22c55e',
                        hoverBackgroundColor: '#16a34a'
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

