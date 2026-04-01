@extends('parent.layouts.parentlayout')

@section('title', __('Courses - ') . ($child->user?->name ?? 'Unknown'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('parent.courses.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
                <i class="bi bi-arrow-left"></i>
                {{ __('Back to Courses') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $child->user?->name ?? 'Unknown' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Enrolled Courses') }}</p>
        </div>
    </div>

    <!-- Courses by Semester -->
    @if($subjectsBySemester->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <i class="bi bi-file-earmark text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">{{ __('No courses enrolled') }}</p>
        </div>
    @else
        @foreach($subjectsBySemester as $semester => $semesterSubjects)
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Semester ') }}{{ $semester }}</h2>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($semesterSubjects as $subject)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject->name ?? 'N/A' }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('Code: ') }}{{ $subject->code ?? 'N/A' }}</p>
                                    </div>
                                    <a href="{{ route('parent.courses.subject', [$child->id, $subject->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-800 dark:hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                        {{ __('View Details') }}
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Credit Hours') }}</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $subject->credit_hours ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Instructor') }}</p>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $subject->teachers->first()?->user?->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Capacity') }}</p>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $subject->capacity ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
