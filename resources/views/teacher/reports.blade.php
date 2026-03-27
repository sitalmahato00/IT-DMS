@extends('teacher.layouts.teacherlayout')

@section('title', __('My Reports'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('My Reports') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('View performance reports for your assigned subjects.') }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('teacher.reports') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Semester') }}</label>
                    <select name="semester" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Semesters') }}</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester }}" {{ $selectedSemester == $semester ? 'selected' : '' }}>
                                {{ __('Semester') }} {{ $semester }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                    <select name="subject" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Subjects') }}</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject['id'] }}" {{ $selectedSubject == $subject['id'] ? 'selected' : '' }}>
                                {{ $subject['code'] ?? '' }} - {{ $subject['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium">
                    <i class="bi bi-funnel"></i> {{ __('Filter') }}
                </button>
                <a href="{{ route('teacher.reports') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                    <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Students') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalStudents }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-people text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Attendance Rate') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $attendancePercentage }}%</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Average Marks') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $avgMarks }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-clipboard-data text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Pass Rate') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $passPercentage }}%</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="bi bi-mortarboard text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendance Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="bi bi-pie-chart mr-2"></i>{{ __('Attendance Breakdown') }}
                </h3>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Present') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full" style="width: {{ $totalAttendanceRecords > 0 ? ($presentCount / ($presentCount + $absentCount + $leaveCount) * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white w-12 text-right">{{ $presentCount }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Absent') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-red-500 rounded-full" style="width: {{ $totalAttendanceRecords > 0 ? ($absentCount / ($presentCount + $absentCount + $leaveCount) * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white w-12 text-right">{{ $absentCount }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Leave') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full" style="width: {{ $totalAttendanceRecords > 0 ? ($leaveCount / ($presentCount + $absentCount + $leaveCount) * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white w-12 text-right">{{ $leaveCount }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="bi bi-info-circle mr-1"></i>
                        {{ __('Total attendance records: ') }} {{ $totalAttendanceRecords }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Exam Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="bi bi-file-earmark-text mr-2"></i>{{ __('Exam Summary') }}
                </h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalExams }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Total Exams') }}</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalMarks }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Marks Entered') }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="bi bi-info-circle mr-1"></i>
                        {{ __('Performance based on marks entered for selected subjects') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="bi bi-link-45deg mr-2"></i>{{ __('Quick Links') }}
            </h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('teacher.attendance') }}" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                        <i class="bi bi-calendar-check text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ __('Attendance') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Mark & view</p>
                    </div>
                </a>

                <a href="{{ route('teacher.marks') }}" class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                        <i class="bi bi-clipboard-data text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ __('Marks') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Enter & manage</p>
                    </div>
                </a>

                <a href="{{ route('teacher.exams') }}" class="flex items-center gap-3 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-full flex items-center justify-center">
                        <i class="bi bi-file-earmark-text text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ __('Exams') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View schedule</p>
                    </div>
                </a>

                <a href="{{ route('teacher.students') }}" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                        <i class="bi bi-people text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ __('Students') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View list</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
