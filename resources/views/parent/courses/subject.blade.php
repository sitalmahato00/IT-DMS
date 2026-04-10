@extends('parent.layouts.parentlayout')

@section('title', __('Subject Details'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('parent.courses.child', $child->id) }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
                <i class="bi bi-arrow-left"></i>
                {{ __('Back to Courses') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $subject->name ?? 'N/A' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $child->user?->name ?? 'Unknown' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subject Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="bi bi-file-earmark"></i>
                    {{ __('Subject Information') }}
                </h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Subject Code') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $subject->code ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Credit Hours') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $subject->credit_hours ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Description') }}</p>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">{{ $subject->description ?? 'No description available' }}</p>
                </div>
            </div>

            <!-- Subject Attendance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="bi bi-calendar-check"></i>
                    {{ __('Attendance in This Subject') }}
                </h2>
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                    <p class="text-sm text-green-600 dark:text-green-300 font-semibold uppercase">{{ __('Overall Attendance') }}</p>
                    <div class="flex items-center gap-4 mt-4">
                        <div class="flex-1 h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 dark:bg-green-600" style="width: {{ $subjectAttendancePercentage }}%"></div>
                        </div>
                        <span class="text-3xl font-bold text-green-900 dark:text-green-100">{{ round($subjectAttendancePercentage, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Instructors -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="bi bi-person-badge"></i>
                    {{ __('Instructors') }}
                </h3>
                @if($subject->teachers->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('No instructors assigned') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($subject->teachers as $teacher)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $teacher->user?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $teacher->user?->email ?? 'N/A' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

