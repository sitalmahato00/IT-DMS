@extends('student.layouts.studentlayout')

@section('title', $subject['name'] . ' - ' . __('Attendance Details'))

@section('content')
<div class="student-smooth-page space-y-6">
    <!-- Attendance Header -->
    <div class="student-smooth-hero bg-gradient-to-r from-green-600 to-green-700 dark:from-green-800 dark:to-green-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $subject['name'] }}</h1>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium rounded-full">
                        {{ $subject['code'] }}
                    </span>
                </div>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
    </div>

    <!-- Subject Info -->
    <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Subject Information') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Semester') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['semester'] }}</p>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Course') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['course'] }}</p>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Instructor') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $primaryTeacher ? $primaryTeacher->name : 'TBA' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Summary -->
    <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Attendance Summary') }}</h2>
            
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <div class="h-16 w-16 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400">
                        <i class="bi bi-calendar-check text-3xl"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Attendance Percentage') }}</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $attendancePercentage }}%</p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-green-600 dark:bg-green-500 h-2.5 rounded-full" style="width: {{ $attendancePercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $attendancePercentage }}% {{ __('of classes attended') }}</p>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="student-smooth-table-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Attendance Records') }}</h2>
            
            @if($attendanceRecords->isEmpty())
                <div class="student-smooth-empty text-center py-8">
                    <i class="bi bi-calendar-x text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('No attendance records found for this subject') }}</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Time In
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Time Out
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($attendanceRecords as $record)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $record->date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $record->time_in }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $record->time_out }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
