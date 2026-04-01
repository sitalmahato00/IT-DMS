@extends('parent.layouts.parentlayout')

@section('title', __('Child Profile'))

@section('content')
<div class="space-y-6">
    <!-- Page Header with Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('parent.children.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
                <i class="bi bi-arrow-left"></i>
                {{ __('Back to Children') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $child->user?->name ?? 'Unknown' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Detailed profile and academic information') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Profile Card -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="bi bi-person"></i>
                    {{ __('Basic Information') }}
                </h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Full Name') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->user?->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Roll Number') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->roll_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Email') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->user?->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Phone') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Date of Birth') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->date_of_birth?->format('Y-m-d') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Gender') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ ucfirst($child->gender ?? 'N/A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <i class="bi bi-book"></i>
                    {{ __('Academic Information') }}
                </h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Semester') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->semester ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Academic Year') }}</p>
                        <p class="text-gray-900 dark:text-white mt-2">{{ $child->academic_year ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Status') }}</p>
                        <div class="mt-2">
                            @if($child->status === 'active')
                                <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">{{ __('Active') }}</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-sm font-semibold">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Attendance') }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 dark:bg-green-600" style="width: {{ $totalAttendance }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ round($totalAttendance, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-r from-amber-50 to-amber-100 dark:from-amber-900 dark:to-amber-800 rounded-xl border border-amber-200 dark:border-amber-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="bi bi-lightning"></i>
                    {{ __('Quick Links') }}
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('parent.attendance.child', $child->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 text-amber-600 dark:text-amber-400 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <i class="bi bi-calendar-check"></i>
                        {{ __('Attendance') }}
                    </a>
                    <a href="{{ route('parent.marks.child', $child->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 text-purple-600 dark:text-purple-400 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <i class="bi bi-graph-up"></i>
                        {{ __('Marks') }}
                    </a>
                    <a href="{{ route('parent.courses.child', $child->id) }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <i class="bi bi-file-earmark"></i>
                        {{ __('Courses') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="space-y-6">
            <!-- Subjects Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="bi bi-file-earmark"></i>
                    {{ __('Enrolled Subjects') }}
                </h3>
                @if($child->subjects->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('No subjects enrolled') }}</p>
                @else
                    <div class="space-y-2">
                        @foreach($child->subjects as $subject)
                            <div class="px-3 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('Code: ') }}{{ $subject->code ?? 'N/A' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Contact Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="bi bi-telephone"></i>
                    {{ __('Contact Information') }}
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Emergency Contact') }}</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $child->emergency_contact ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Address') }}</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $child->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
