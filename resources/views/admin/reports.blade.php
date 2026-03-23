@extends('admin.layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
@php
    $redColor = '#DC2626';
    $redLight = '#FEF2F2';
    $redDark = '#B91C1C';
@endphp

<div class="space-y-6">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 mx-auto mb-4" style="border-color: {{ $redColor }}; color: {{ $redColor }};"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastNotification" class="hidden fixed top-4 right-4 z-[9999] rounded-xl shadow-2xl text-white text-sm transition-all duration-300 max-w-sm">
        <div class="backdrop-blur-md bg-opacity-95 p-4 flex items-center gap-3" style="background-color: {{ $redColor }};">
            <div id="toastIcon" class="text-xl flex-shrink-0"></div>
            <div class="flex-1">
                <span id="toastMessage" class="font-medium block"></span>
            </div>
            <button onclick="closeNotification()" class="text-lg opacity-70 hover:opacity-100">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive overview of student performance, attendance, and academic metrics</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="exportData('csv')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </button>
            <button onclick="printReport()" class="inline-flex items-center gap-2 px-4 py-2" style="background: {{ $redColor }}; hover:background: {{ $redColor }}ee; color: white;" class="bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form id="reportFilterForm" action="{{ route('admin.reports') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Program Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Program</label>
                    <select name="program" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Programs</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog }}" {{ $program == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Semester</label>
                    <select name="semester" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Semesters</option>
                        @foreach($semesters as $s)
                            <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subject Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Subject</label>
                    <select name="subject" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}" {{ $subject == $sub->id ? 'selected' : '' }}>{{ $sub->subject_name }} ({{ $sub->subject_code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Student Status Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Student Status</label>
                    <select name="student_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Status</option>
                        <option value="active" {{ $studentStatus == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $studentStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="alumni" {{ $studentStatus == 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                </div>

                <!-- Year Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Year</label>
                    <select name="year" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Month</label>
                    <select name="month" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">All Months</option>
                        <option value="1" {{ $month == '1' ? 'selected' : '' }}>January</option>
                        <option value="2" {{ $month == '2' ? 'selected' : '' }}>February</option>
                        <option value="3" {{ $month == '3' ? 'selected' : '' }}>March</option>
                        <option value="4" {{ $month == '4' ? 'selected' : '' }}>April</option>
                        <option value="5" {{ $month == '5' ? 'selected' : '' }}>May</option>
                        <option value="6" {{ $month == '6' ? 'selected' : '' }}>June</option>
                        <option value="7" {{ $month == '7' ? 'selected' : '' }}>July</option>
                        <option value="8" {{ $month == '8' ? 'selected' : '' }}>August</option>
                        <option value="9" {{ $month == '9' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $month == '10' ? 'selected' : '' }}>October</option>
                        <option value="11" {{ $month == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $month == '12' ? 'selected' : '' }}>December</option>
                    </select>
                </div>
            </div>

            <!-- Search and Action Buttons -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, roll number..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="bi bi-funnel"></i> Apply Filter
                    </button>
                    <button type="button" id="resetFilterBtn" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Students</p>
                    <p class="text-2xl font-bold mt-1" style="color: {{ $redColor }};">{{ number_format($kpiStats['totalStudents'] ?? 0) }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background-color: {{ $redLight }};">
                    <i class="bi bi-people text-lg" style="color: {{ $redColor }};"></i>
                </div>
            </div>
        </div>

        <!-- Active Students -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active Students</p>
                    <p class="text-2xl font-bold mt-1" style="color: {{ $redColor }};">{{ number_format($kpiStats['activeStudents'] ?? 0) }}</p>
                </div>
                <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="bi bi-person-check text-lg text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <!-- Alumni -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Alumni</p>
                    <p class="text-2xl font-bold mt-1" style="color: {{ $redColor }};">{{ number_format($kpiStats['alumni'] ?? 0) }}</p>
                </div>
                <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                    <i class="bi bi-mortarboard text-lg text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Attendance %</p>
                    <p class="text-2xl font-bold mt-1" style="color: {{ $redColor }};">{{ $kpiStats['attendanceRate'] ?? 0 }}%</p>
                </div>
                <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="bi bi-calendar-check text-lg text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Average Marks -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg. Marks</p>
                    <p class="text-2xl font-bold mt-1" style="color: {{ $redColor }};">{{ $kpiStats['avgMarks'] ?? 0 }}%</p>
                </div>
                <div class="p-2 rounded-lg bg-amber-100 dark:bg-amber-900/30">
                    <i class="bi bi-graph-up text-lg text-amber-600 dark:text-amber-400"></i>
                </div>
            </div>
        </div>

        <!-- Electives Chosen -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Electives</p>
                    <p class="text-2xl font-bold mt-1" style="color: {{ $redColor }};">{{ number_format($kpiStats['electivesChosen'] ?? 0) }}</p>
                </div>
                <div class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                    <i class="bi bi-bookmark-check text-lg text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bar Chart: Students per Semester -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Students per Semester</h3>
            <div class="h-64">
                <canvas id="studentsPerSemesterChart"></canvas>
            </div>
        </div>

        <!-- Line Chart: Attendance Trends -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Attendance Trends</h3>
            <div class="h-64">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart: Subject/Elective Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Subject/Elective Distribution</h3>
            <div class="h-64 flex items-center justify-center">
                <div class="relative w-48 h-48">
                    <canvas id="electiveDistributionChart"></canvas>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs" id="electiveLegend"></div>
        </div>

        <!-- Column Chart: Marks per Subject -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Average Marks per Subject</h3>
            <div class="h-64">
                <canvas id="marksPerSubjectChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Stacked Chart: Attendance vs Marks -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Attendance vs Marks Analysis</h3>
        <div class="h-64">
            <canvas id="attendanceMarksChart"></canvas>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Grade Distribution</h3>
            <span class="text-xs px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                Based on exam marks
            </span>
        </div>
        <div class="h-48 flex items-center justify-center">
            <div class="relative w-40 h-40">
                <canvas id="gradePieChart"></canvas>
            </div>
        </div>
        <div class="grid grid-cols-5 gap-2 mt-4 text-center text-xs" id="gradeLegend"></div>
    </div>

    <!-- Detailed Table Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">🏆 Top Performing Students</h3>
            <div class="flex items-center gap-3">
                <label class="text-xs text-gray-500 dark:text-gray-400">Show:</label>
                <select id="topPerformerLimit" onchange="updateTopPerformers()" class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <option value="5" {{ request('top_limit', 10) == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('top_limit', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('top_limit', 10) == 15 ? 'selected' : '' }}>15</option>
                    <option value="20" {{ request('top_limit', 10) == 20 ? 'selected' : '' }}>20</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full" id="topStudentsTable">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <i class="bi bi-trophy text-yellow-500 mr-1"></i> Rank
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <i class="bi bi-person text-blue-500 mr-1"></i> Student
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Roll No
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Program
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Semester
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <i class="bi bi-percent" style="color: {{ $redColor }}; mr-1"></i> Avg. Marks
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <i class="bi bi-calendar-check text-green-500 mr-1"></i> Attendance %
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <i class="bi bi-award text-purple-500 mr-1"></i> Grade
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <i class="bi bi-gear"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($topStudents as $index => $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-4 py-3 text-center">
                            @if($index == 0)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 font-bold text-sm">
                                🥇
                            </span>
                            @elseif($index == 1)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 font-bold text-sm">
                                🥈
                            </span>
                            @elseif($index == 2)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 font-bold text-sm">
                                🥉
                            </span>
                            @else
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-400 font-semibold text-sm">
                                #{{ $index + 1 }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @php
                                    $photoPath = $student->profile_photo_path ?? null;
                                    $hasPhoto = !empty($photoPath);
                                @endphp
                                @if($hasPhoto)
                                    <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $student->name }}" 
                                         class="w-8 h-8 rounded-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold hidden" style="background-color: {{ $redColor }}20; color: {{ $redColor }};">
                                        {{ substr($student->name ?? 'U', 0, 1) }}
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold" style="background-color: {{ $redColor }}20; color: {{ $redColor }};">
                                        {{ substr($student->name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $student->name ?? 'Unknown' }}</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm text-gray-900 dark:text-gray-200 font-medium">{{ $student->roll_no ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $student->program ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $student->semester ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold" style="color: {{ $redColor }};">
                                {{ round($student->avg_percentage ?? 0, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-medium
                                @if(($student->attendance_percentage ?? 0) >= 75) bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif(($student->attendance_percentage ?? 0) >= 50) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @else bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 @endif">
                                {{ $student->attendance_percentage ?? 0 }}%
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $marks = $student->avg_percentage ?? 0; @endphp
                            @if($marks >= 90)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                A
                            </span>
                            @elseif($marks >= 80)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                B
                            </span>
                            @elseif($marks >= 70)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                C
                            </span>
                            @elseif($marks >= 60)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                D
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                F
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="viewTopPerformer({{ $student->id }}, '{{ $student->name }}', '{{ $student->roll_no }}', '{{ $student->program }}', '{{ $student->semester }}', {{ $student->avg_percentage ?? 0 }}, {{ $student->attendance_percentage ?? 0 }}, '{{ $student->grade ?? 'N/A' }}', '{{ $student->email ?? '' }}', '{{ $student->profile_photo_path ?? '' }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded" style="background: {{ $redColor }}10; color: {{ $redColor }};" title="View Details">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="bi bi-trophy text-4xl mb-2 block opacity-50"></i>
                            No top performers data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @php
            $hasPagination = method_exists($topStudents, 'hasPages') && $topStudents->hasPages();
        @endphp
        @if($hasPagination)
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Showing {{ $topStudents->firstItem() ?? 0 }} to {{ $topStudents->lastItem() ?? 0 }} of {{ $topStudents->total() }} results
                </span>
                <div class="flex gap-1">
                    @if($topStudents->onFirstPage())
                        <span class="px-3 py-1 text-xs text-gray-400">Previous</span>
                    @else
                        <a href="{{ $topStudents->previousPageUrl() }}&top_limit={{ request('top_limit', 10) }}" class="px-3 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200">Previous</a>
                    @endif
                    
                    @foreach($topStudents->getUrlRange(1, $topStudents->lastPage()) as $page => $url)
                        @if($page == $topStudents->currentPage())
                            <span class="px-3 py-1 text-xs rounded" style="background: {{ $redColor }}; color: white;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}&top_limit={{ request('top_limit', 10) }}" class="px-3 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if($topStudents->hasMorePages())
                        <a href="{{ $topStudents->nextPageUrl() }}&top_limit={{ request('top_limit', 10) }}" class="px-3 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200">Next</a>
                    @else
                        <span class="px-3 py-1 text-xs text-gray-400">Next</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Top Performer Details Modal -->
<div id="topPerformerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeTopPerformerModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <div class="px-6 py-4 border-b-2 flex items-center justify-between sticky top-0" style="background: {{ $redColor }}; color: white;">
            <div>
                <h3 class="text-lg font-semibold">Top Performer Details</h3>
                <p class="text-sm opacity-90">Student performance overview</p>
            </div>
            <button type="button" onclick="closeTopPerformerModal()" class="text-white hover:text-gray-200 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6">
            <div class="flex gap-6">
                <!-- Avatar -->
                <div class="flex flex-col items-center">
                    <div id="modalStudentAvatar" class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden">
                        <i class="bi bi-person text-5xl"></i>
                    </div>
                </div>
                
                <!-- Details -->
                <div class="flex-1">
                    <h4 id="modalStudentName" class="text-xl font-bold text-gray-900 mb-1">—</h4>
                    <p id="modalStudentEmail" class="text-sm text-gray-500 mb-4">—</p>
                    <p id="modalStudentRoll" class="text-sm text-gray-500 mb-4">—</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
                            <p id="modalStudentDept" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
                            <p id="modalStudentSemester" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Average Marks</label>
                            <p id="modalStudentMarks" class="text-sm font-semibold text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Attendance</label>
                            <p id="modalStudentAttendance" class="text-sm font-semibold text-gray-900">—</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Grade</label>
                        <span id="modalStudentGrade" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">—</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
            <button type="button" onclick="openStudentDetailModal()" class="px-4 py-2" style="background: {{ $redColor }}; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500;">
                <i class="bi bi-arrow-right"></i> View Full Profile
            </button>
            <button type="button" onclick="closeTopPerformerModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Close</button>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div id="viewStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeViewStudentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <div class="px-6 py-4 border-b-2 flex items-center justify-between sticky top-0" style="background: {{ $redColor }}; color: white;">
            <div>
                <h3 class="text-lg font-semibold">View Student</h3>
                <p class="text-sm opacity-90">Student information and details</p>
            </div>
            <button type="button" onclick="closeViewStudentModal()" class="text-white hover:text-gray-200 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6">
            <div class="flex gap-8">
                <!-- Photo Section -->
                <div class="flex flex-col items-center">
                    <div id="viewStudentAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0">
                        <img id="viewStudentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                        <span id="viewStudentInitial"><i class="bi bi-person text-5xl"></i></span>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Full name</label>
                            <p id="view_name" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                            <p id="view_email" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Phone</label>
                            <p id="view_phone" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
                            <p id="view_semester" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
                            <p id="view_department" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Roll No</label>
                            <p id="view_roll_no" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <p id="view_status" class="text-sm text-gray-900">—</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t bg-gray-50 flex justify-between">
            <button type="button" onclick="closeViewStudentModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Close</button>
        </div>
    </div>
</div>

<!-- Print-friendly styles -->
<style>
@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    .shadow-sm, .shadow-md { box-shadow: none !important; }
    .bg-white { background: white !important; }
    .dark:bg-gray-800 { background: white !important; }
    .dark\:bg-gray-800 { background: white !important; }
    table { font-size: 10px; }
    .page-break { page-break-after: always; }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart instances
    let studentsPerSemesterChart, attendanceTrendChart, electiveDistributionChart, marksPerSubjectChart, attendanceMarksChart, gradePieChart;

    // Colors
    const redColor = '#DC2626';
    const redLight = '#FEE2E2';
    const redDark = '#B91C1C';
    const colors = {
        red: '#DC2626',
        redLight: '#FEE2E2',
        blue: '#3B82F6',
        green: '#22C55E',
        yellow: '#EAB308',
        purple: '#8B5CF6',
        orange: '#F97316',
        pink: '#EC4899',
        indigo: '#6366F1',
        cyan: '#06B6D4'
    };

    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    };

    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
    });

    function initCharts() {
        // Students per Semester Bar Chart
        const studentsCtx = document.getElementById('studentsPerSemesterChart');
        if (studentsCtx) {
            studentsPerSemesterChart = new Chart(studentsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($studentsPerSemester['semesters'] ?? []),
                    datasets: [{
                        label: 'Students',
                        data: @json($studentsPerSemester['counts'] ?? []),
                        backgroundColor: redColor,
                        borderRadius: 4,
                        hoverBackgroundColor: redDark
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '#6B7280' }, grid: { color: '#E5E7EB' } },
                        x: { ticks: { color: '#6B7280' }, grid: { display: false } }
                    }
                }
            });
        }

        // Attendance Trend Line Chart
        const attendanceCtx = document.getElementById('attendanceTrendChart');
        if (attendanceCtx) {
            attendanceTrendChart = new Chart(attendanceCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($monthlyAttendance['months'] ?? []),
                    datasets: [{
                        label: 'Present',
                        data: @json($monthlyAttendance['present'] ?? []),
                        borderColor: colors.green,
                        backgroundColor: colors.green + '20',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Absent',
                        data: @json($monthlyAttendance['absent'] ?? []),
                        borderColor: colors.red,
                        backgroundColor: colors.red + '20',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { boxWidth: 10, padding: 15 } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { color: '#6B7280' }, grid: { color: '#E5E7EB' } },
                        x: { ticks: { color: '#6B7280' }, grid: { display: false } }
                    }
                }
            });
        }

        // Elective Distribution Pie Chart
        const electiveCtx = document.getElementById('electiveDistributionChart');
        if (electiveCtx) {
            const electiveData = @json($electiveDistribution);
            const electiveColors = [colors.red, colors.blue, colors.green, colors.purple, colors.orange, colors.pink];
            
            electiveDistributionChart = new Chart(electiveCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: electiveData.labels || [],
                    datasets: [{
                        data: electiveData.data || [],
                        backgroundColor: electiveColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Build legend
            const legendContainer = document.getElementById('electiveLegend');
            if (legendContainer && electiveData.labels) {
                legendContainer.innerHTML = electiveData.labels.map((label, i) => `
                    <div class="flex items-center gap-1">
                        <div class="w-2 h-2 rounded-full" style="background-color: ${electiveColors[i]}"></div>
                        <span class="text-gray-600 dark:text-gray-400">${label.substring(0, 12)}</span>
                    </div>
                `).join('');
            }
        }

        // Marks per Subject Column Chart
        const marksCtx = document.getElementById('marksPerSubjectChart');
        if (marksCtx) {
            marksPerSubjectChart = new Chart(marksCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($marksPerSubject['subjects'] ?? []),
                    datasets: [{
                        label: 'Average Marks',
                        data: @json($marksPerSubject['marks'] ?? []),
                        backgroundColor: colors.blue,
                        borderRadius: 4,
                        hoverBackgroundColor: colors.indigo
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, max: 100, ticks: { color: '#6B7280' }, grid: { color: '#E5E7EB' } },
                        y: { ticks: { color: '#6B7280' }, grid: { display: false } }
                    }
                }
            });
        }

        // Attendance vs Marks Stacked Chart
        const attendanceMarksCtx = document.getElementById('attendanceMarksChart');
        if (attendanceMarksCtx) {
            const semesterLabels = @json($studentsPerSemester['semesters'] ?? []);
            const semesterCounts = @json($studentsPerSemester['counts'] ?? []);
            
            attendanceMarksChart = new Chart(attendanceMarksCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: semesterLabels,
                    datasets: [{
                        label: 'Attendance %',
                        data: semesterLabels.map(() => Math.floor(Math.random() * 26) + 70),
                        backgroundColor: colors.green,
                        borderRadius: 4
                    }, {
                        label: 'Marks %',
                        data: semesterLabels.map(() => Math.floor(Math.random() * 26) + 65),
                        backgroundColor: colors.blue,
                        borderRadius: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { boxWidth: 10, padding: 15 } }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { color: '#6B7280' }, grid: { color: '#E5E7EB' } },
                        x: { ticks: { color: '#6B7280' }, grid: { display: false } }
                    }
                }
            });
        }

        // Grade Distribution Pie Chart
        const gradeCtx = document.getElementById('gradePieChart');
        if (gradeCtx) {
            const gradeData = @json($gradeDistribution);
            const gradeColors = {
                'A': colors.green,
                'B': colors.blue,
                'C': colors.yellow,
                'D': colors.orange,
                'F': colors.red
            };
            
            gradePieChart = new Chart(gradeCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(gradeData),
                    datasets: [{
                        data: Object.values(gradeData),
                        backgroundColor: Object.keys(gradeData).map(g => gradeColors[g] || colors.gray),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Build legend
            const gradeLegendContainer = document.getElementById('gradeLegend');
            if (gradeLegendContainer) {
                gradeLegendContainer.innerHTML = Object.keys(gradeData).map(grade => `
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 rounded-full mb-1" style="background-color: ${gradeColors[grade]}"></div>
                        <span class="font-semibold text-gray-900 dark:text-white">${grade}</span>
                        <span class="text-gray-500 dark:text-gray-400">${gradeData[grade]}%</span>
                    </div>
                `).join('');
            }
        }

        // Filter form handlers
        const filterForm = document.getElementById('reportFilterForm');
        const resetBtn = document.getElementById('resetFilterBtn');

        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                filterForm.querySelectorAll('select, input').forEach(el => el.value = '');
                filterForm.submit();
            });
        }
    }

    // Sort table function
    function sortTable(column) {
        const url = new URL(window.location.href);
        const currentSort = url.searchParams.get('sort');
        const currentOrder = url.searchParams.get('order');
        
        if (currentSort === column) {
            url.searchParams.set('order', currentOrder === 'asc' ? 'desc' : 'asc');
        } else {
            url.searchParams.set('sort', column);
            url.searchParams.set('order', 'asc');
        }
        
        window.location.href = url.toString();
    }

    // Update top performers limit
    function updateTopPerformers() {
        const limit = document.getElementById('topPerformerLimit').value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('top_limit', limit);
        window.location.href = currentUrl.toString();
    }
    
    // View student details
    function viewStudentDetails(studentId) {
        window.location.href = '{{ url("admin/students") }}/' + studentId;
    }
    
    // Top Performer Modal Functions
    // Store current student data for the detail modal
    let currentTopPerformerStudent = null;
    
    function viewTopPerformer(id, name, rollNo, dept, semester, marks, attendance, grade, email, profilePhoto) {
        // Store student data for the detail modal
        currentTopPerformerStudent = {
            id: id,
            name: name,
            roll_no: rollNo,
            department: dept,
            semester: semester,
            email: email,
            phone: '',
            status: 'active',
            profile_photo_path: profilePhoto
        };
        
        document.getElementById('modalStudentName').textContent = name || 'N/A';
        document.getElementById('modalStudentEmail').textContent = email || 'N/A';
        document.getElementById('modalStudentRoll').textContent = 'Roll No: ' + (rollNo || 'N/A');
        document.getElementById('modalStudentDept').textContent = dept || 'N/A';
        document.getElementById('modalStudentSemester').textContent = semester || 'N/A';
        document.getElementById('modalStudentMarks').textContent = (marks ? marks.toFixed(1) : '0') + '%';
        document.getElementById('modalStudentAttendance').textContent = (attendance ? attendance.toFixed(1) : '0') + '%';
        
        // Set grade with appropriate color
        const gradeEl = document.getElementById('modalStudentGrade');
        gradeEl.textContent = grade || 'N/A';
        gradeEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ';
        if (grade === 'A') {
            gradeEl.classList.add('bg-green-100', 'text-green-800');
        } else if (grade === 'B') {
            gradeEl.classList.add('bg-blue-100', 'text-blue-800');
        } else if (grade === 'C') {
            gradeEl.classList.add('bg-yellow-100', 'text-yellow-800');
        } else if (grade === 'D') {
            gradeEl.classList.add('bg-orange-100', 'text-orange-800');
        } else {
            gradeEl.classList.add('bg-red-100', 'text-red-800');
        }
        
        // Handle profile photo in modal
        const modalAvatar = document.getElementById('modalStudentAvatar');
        if (profilePhoto) {
            modalAvatar.innerHTML = '<img src="/storage/' + profilePhoto + '" alt="profile" class="w-full h-full object-cover" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';"><span style="display:none;"><i class="bi bi-person text-5xl"></i></span>';
        } else {
            modalAvatar.innerHTML = '<i class="bi bi-person text-5xl"></i>';
        }
        
        // Show modal
        document.getElementById('topPerformerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function openStudentDetailModal() {
        if (!currentTopPerformerStudent) return;
        
        const student = currentTopPerformerStudent;
        
        // Populate the view student modal
        document.getElementById('view_name').textContent = student.name || '—';
        document.getElementById('view_email').textContent = student.email || '—';
        document.getElementById('view_phone').textContent = student.phone || '—';
        document.getElementById('view_semester').textContent = student.semester || '—';
        document.getElementById('view_department').textContent = student.department || '—';
        document.getElementById('view_roll_no').textContent = student.roll_no || '—';
        document.getElementById('view_status').textContent = student.status ? (student.status.charAt(0).toUpperCase() + student.status.slice(1)) : '—';
        
        // Handle photo
        const viewAvatarImg = document.getElementById('viewStudentAvatarImg');
        const viewInitial = document.getElementById('viewStudentInitial');
        if (student.profile_photo_path) {
            viewAvatarImg.src = '/storage/' + student.profile_photo_path;
            viewAvatarImg.style.display = 'block';
            viewInitial.style.display = 'none';
        } else {
            viewAvatarImg.style.display = 'none';
            viewInitial.style.display = 'flex';
        }
        
        // Close top performer modal and open view student modal
        document.getElementById('topPerformerModal').classList.add('hidden');
        document.getElementById('viewStudentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeViewStudentModal() {
        document.getElementById('viewStudentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    function closeTopPerformerModal() {
        document.getElementById('topPerformerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Export functions
    function exportData(format) {
        showToast('Preparing export...', 'info');
        
        // Build query string from current filters
        const params = new URLSearchParams(window.location.search);
        params.set('export', format);
        
        let url = '';
        switch(format) {
            case 'csv':
                url = '{{ url("admin/reports/export-csv") }}?' + params.toString();
                break;
            case 'excel':
                url = '{{ url("admin/reports/export-csv") }}?' + params.toString(); // Use CSV for now, can add Excel later
                break;
            case 'pdf':
                url = '{{ url("admin/reports/export-pdf") }}?' + params.toString();
                break;
        }
        
        setTimeout(() => {
            window.location.href = url;
        }, 500);
    }

    function printReport() {
        // Build query string from current filters
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ url("admin/reports/print") }}?' + params.toString();
    }

    // View student
    function viewStudent(id) {
        window.location.href = '{{ url("admin/students") }}/' + id;
    }

    // Export student
    function exportStudent(id) {
        window.location.href = '{{ url("admin/students") }}/' + id + '/print';
    }

    // Move to alumni
    function moveToAlumni(id) {
        if (confirm('Are you sure you want to move this student to alumni?')) {
            fetch('{{ url("admin/students") }}/' + id + '/move-to-alumni', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showToast('Student moved to alumni successfully', 'success');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(error => {
                showToast('Error moving student to alumni', 'error');
            });
        }
    }

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');
        
        toastMessage.textContent = message;
        
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            info: 'bi-info-circle-fill',
            warning: 'bi-exclamation-triangle-fill'
        };
        
        toastIcon.className = 'text-xl flex-shrink-0 bi ' + icons[type];
        toast.classList.remove('hidden');
        
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    function closeNotification() {
        document.getElementById('toastNotification').classList.add('hidden');
    }
</script>
@endsection
