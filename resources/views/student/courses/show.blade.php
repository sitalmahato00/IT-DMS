@extends('student.layouts.studentlayout')

@section('title', $subject['name'] . ' - ' . __('Course Details'))

@section('content')
<div class="student-smooth-page space-y-6">
    <!-- Course Header -->
    <div class="student-smooth-hero bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $subject['name'] }}</h1>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-medium rounded-full">
                        {{ $subject['code'] }}
                    </span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium rounded-full">
                        {{ $subject['credits'] }} Cr
                    </span>
                    @if($subject['has_lab'])
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 text-xs font-medium rounded-full">
                            Lab
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-book"></i>
            </div>
        </div>
    </div>

    <!-- Course Info -->
    <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Course Information') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Semester') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['semester'] }}</p>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Course Category') }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['course'] }}</p>
                </div>
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</p>
                    <p class="text-gray-600 dark:text-gray-300 line-clamp-4">{{ $subject['description'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructors -->
    @if($teachers->isNotEmpty())
        <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Instructors') }}</h2>
                
                <div class="space-y-4">
                    @foreach($teachers as $teacher)
                        <div class="student-smooth-list-card flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="bi bi-person text-blue-600 dark:text-blue-400 text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $teacher['name'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($teacher['role'] === 'primary')
                                        {{ __('Primary Instructor') }}
                                    @else
                                        {{ ucfirst($teacher['role']) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Attendance -->
    <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Attendance') }}</h2>
            
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i class="bi bi-calendar-check text-2xl"></i>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Attendance Percentage') }}</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white">{{ $attendancePercentage }}%</p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-blue-600 dark:bg-blue-500 h-2.5 rounded-full" style="width: {{ $attendancePercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $attendancePercentage }}% {{ __('of classes attended') }}</p>
            </div>
        </div>
    </div>

    <!-- Marks -->
    <div class="student-smooth-panel bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Examination Results') }}</h2>
            
            <div class="space-y-6">
                <!-- Assessment Marks -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Assessment Marks') }}</h3>
                    @if($assessmentMarks && $assessmentMarks->full > 0)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Obtained') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->obtained }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Full Marks') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->full }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Passing Marks') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->pass }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Percentage') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assessmentMarks->obtained > 0 && $assessmentMarks->full > 0 ? round(($assessmentMarks->obtained / $assessmentMarks->full) * 100, 2) : 0 }}%</p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status: ') }}
                                <span class="px-2 py-1 rounded 
                                    @if($assessmentMarks->is_pass === true)
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($assessmentMarks->is_pass === false)
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else
                                        bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    @if($assessmentMarks->is_pass === true)
                                        {{ __('Pass') }}
                                    @elseif($assessmentMarks->is_pass === false)
                                        {{ __('Fail') }}
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </span>
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500 dark-gray-400 text-center py-8">{{ __('No assessment marks available') }}</p>
                    @endif
                </div>
                
                <!-- CTEVT Marks -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('CTEVT Marks') }}</h3>
                    @if($ctevtMarks && isset($ctevtMarks->full) && $ctevtMarks->full > 0)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Obtained') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->obtained }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Full Marks') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->full }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Passing Marks') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->pass }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Percentage') }}</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ctevtMarks->obtained > 0 && $ctevtMarks->full > 0 ? round(($ctevtMarks->obtained / $ctevtMarks->full) * 100, 2) : 0 }}%</p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status: ') }}
                                <span class="px-2 py-1 rounded 
                                    @if($ctevtMarks->is_pass === true)
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($ctevtMarks->is_pass === false)
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else
                                        bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    @if($ctevtMarks->is_pass === true)
                                        {{ __('Pass') }}
                                    @elseif($ctevtMarks->is_pass === false)
                                        {{ __('Fail') }}
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </span>
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500 dark-gray-400 text-center py-8">{{ __('No CTEVT marks available') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
