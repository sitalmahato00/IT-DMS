@extends('student.layouts.studentlayout')

@section('title', __('Examination Results'))

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 dark:from-purple-800 dark:to-purple-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ __('Examination Results') }}</h1>
                <p class="text-purple-100 text-base">{{ __('View your academic performance across all subjects') }}</p>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-award"></i>
            </div>
        </div>
    </div>

    <!-- Overall Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Overall Performance') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Subjects') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $subjectCount }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('Overall Percentage') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $overallPercentage }}%</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ __('CGPA') }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $cgpa }}</p>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                    <div class="bg-purple-600 dark:bg-purple-500 h-2.5 rounded-full" style="width: {{ min($overallPercentage, 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $overallPercentage }}% {{ __('overall score') }}</p>
            </div>
        </div>
    </div>

    <!-- Subjects Performance -->
    @if($subjects->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
            <i class="bi bi-award text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('No Results Available') }}</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ __('You have no examination results recorded yet.') }}</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Subject-wise Performance') }}</h2>
                
                <div class="space-y-4">
                    @foreach($subjects as $subject)
                        <div class="flex items-start justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-l-4 
                            @if(isset($subject['assessment_marks']->is_pass) && $subject['assessment_marks']->is_pass === true)
                                border-green-400
                            @elseif(isset($subject['assessment_marks']->is_pass) && $subject['assessment_marks']->is_pass === false)
                                border-red-400
                            @elseif(isset($subject['ctevt_marks']->is_pass) && $subject['ctevt_marks']->is_pass === true)
                                border-green-400
                            @elseif(isset($subject['ctevt_marks']->is_pass) && $subject['ctevt_marks']->is_pass === false)
                                border-red-400
                            @else
                                border-gray-400
                            @endif
                        ">
                            <div class="flex-1">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-book text-purple-600 dark:text-purple-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $subject['name'] }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <i class="bi bi-tag"></i>
                                            <span>{{ $subject['code'] }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="bi bi-person text-purple-600 dark:text-purple-400"></i>
                                            {{ $subject['teacher'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if(isset($subject['assessment_marks']->is_pass) && $subject['assessment_marks']->is_pass === true)
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif(isset($subject['assessment_marks']->is_pass) && $subject['assessment_marks']->is_pass === false)
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif(isset($subject['ctevt_marks']->is_pass) && $subject['ctevt_marks']->is_pass === true)
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif(isset($subject['ctevt_marks']->is_pass) && $subject['ctevt_marks']->is_pass === false)
                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else
                                            bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif">
                                        @if(isset($subject['assessment_marks']->is_pass) && $subject['assessment_marks']->is_pass === true)
                                            {{ __('Pass') }}
                                        @elseif(isset($subject['assessment_marks']->is_pass) && $subject['assessment_marks']->is_pass === false)
                                            {{ __('Fail') }}
                                        @elseif(isset($subject['ctevt_marks']->is_pass) && $subject['ctevt_marks']->is_pass === true)
                                            {{ __('Pass') }}
                                        @elseif(isset($subject['ctevt_marks']->is_pass) && $subject['ctevt_marks']->is_pass === false)
                                            {{ __('Fail') }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Assessment: ') }}
                                    @if($subject['assessment_marks'] && $subject['assessment_marks']->full > 0)
                                        {{ $subject['assessment_marks']->obtained }}/{{ $subject['assessment_marks']->full }}
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </p>
                                @if(isset($subject['ctevt_marks']->full) && $subject['ctevt_marks']->full > 0)
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('CTEVT: ') }}
                                        {{ $subject['ctevt_marks']->obtained }}/{{ $subject['ctevt_marks']->full }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection