@extends('student.layouts.studentlayout')

@section('title', __('My Courses'))

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-red-600 to-red-700 dark:from-red-800 dark:to-red-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ __('My Courses') }}</h1>
                <p class="text-red-100 text-base">{{ __('View your enrolled courses and course details') }}</p>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-book"></i>
            </div>
        </div>
    </div>

    @if(empty($subjects))
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
            <i class="bi bi-book text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('No Courses Enrolled') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('You are not currently enrolled in any courses. Please contact your academic advisor.') }}</p>
        </div>
    @else
        <!-- Courses Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($subjects as $subject)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $subject['name'] }}</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i class="bi bi-tag"></i>
                                    <span>{{ $subject['code'] }}</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium rounded-full">
                                    {{ $subject['credits'] }} Cr
                                </span>
                                @if($subject['has_lab'])
                                    <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-medium rounded-full">
                                        Lab
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ $subject['description'] }}</p>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Semester') }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['semester'] }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Course') }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['course'] }}</p>
                            </div>
                        </div>

                        @if($subject['teacher'] !== 'TBA')
                            <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                                <i class="bi bi-person text-red-600 dark:text-red-400"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Instructor') }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['teacher'] }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                                <i class="bi bi-person-badge text-yellow-600 dark:text-yellow-400"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Instructor') }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject['teacher'] }}</p>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('student.courses.show', $subject['id']) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 mt-4 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors group-hover:bg-red-700">
                            {{ __('View Course Details') }}
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection