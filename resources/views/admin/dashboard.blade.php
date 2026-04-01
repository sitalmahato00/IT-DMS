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
        <div class="xl:col-span-7 rounded-xl border-2 border-emerald-300 dark:border-emerald-700 bg-gradient-to-br from-emerald-50/50 to-white dark:from-emerald-950/20 dark:to-gray-800 p-4 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-graph-up text-emerald-500 dark:text-emerald-400"></i>
                        {{ __('Attendance Overview') }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ __('daily patterns') }}</p>
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
            <div class="h-56 relative">
                <canvas id="attendanceBarChart"></canvas>
                <p id="attendanceBarNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No attendance data available.') }}</p>
            </div>
        </div>

        <!-- Grade Distribution Pie Chart -->
        <div class="xl:col-span-5 rounded-xl border-2 border-fuchsia-300 dark:border-fuchsia-700 bg-gradient-to-br from-fuchsia-50/50 to-white dark:from-fuchsia-950/20 dark:to-gray-800 p-4 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-fuchsia-500 dark:text-fuchsia-400"></i>
                        {{ __('Grade Distribution') }}
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">📈 {{ __('Academic performance metrics') }}</p>
                </div>
                <span class="text-xs px-3 py-2 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900 dark:to-emerald-900 dark:text-green-200 font-bold border border-green-300 dark:border-green-700">
                    {{ $totalGradedRecords }} {{ __('Graded') }}
                </span>
            </div>
            <div class="h-56">
                <canvas id="gradePieChart"></canvas>
                <p id="gradePieNoData" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-20">{{ __('No grades available yet.') }}</p>
            </div>
            <div class="grid grid-cols-4 gap-2 mt-6 text-center text-xs">
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
                    <div class="rounded-lg border-2 py-3 px-1 font-bold transition hover:shadow-md" style="border-color: {{ $gradeColors[$grade] ?? '#6b7280' }}; background-color: {{ $gradeColors[$grade] ?? '#6b7280' }}15;">
                        <div class="w-4 h-4 mx-auto rounded-full mb-2" style="background-color: {{ $gradeColors[$grade] ?? '#6b7280' }}"></div>
                        <p class="font-bold" style="color: {{ $gradeColors[$grade] ?? '#6b7280' }}">{{ $count }}</p>
                        <p class="text-gray-700 dark:text-gray-300 text-xs mt-0.5">{{ $grade }}</p>
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

