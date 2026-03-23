@extends('student.layouts.studentlayout')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ __('Welcome back, ') . (auth()->user()->name ?? 'Student') }}! 👋</h1>
                <p class="text-blue-100 text-base">{{ __('You are logged in as a Student. Check your courses, attendance, marks, and study materials.') }}</p>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-mortarboard"></i>
            </div>
        </div>
    </div>

    <!-- Role Description Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Role Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border-l-4 border-blue-600 overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Student Role') }}</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Your role and responsibilities in the system') }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 p-3 rounded-lg">
                        <i class="bi bi-mortarboard text-2xl"></i>
                    </div>
                </div>
                
                <div class="space-y-4 mt-6">
                    <div class="border-l-2 border-blue-400 pl-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('General Overview') }}</h3>
                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                            {{ __("As a student in the IT-DMS system, you have access to your academic portal where you can view and manage your educational journey. The system provides a comprehensive platform for tracking your progress, viewing course materials, monitoring attendance, and reviewing your examination results.") }}
                        </p>
                    </div>

                    <div class="border-l-2 border-blue-400 pl-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('Key Responsibilities') }}</h3>
                        <ul class="text-gray-700 dark:text-gray-300 text-sm space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Regularly check enrolled courses and access course materials') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Maintain good attendance as per institute requirements') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Complete assignments and submit coursework on time') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Review examination schedules and prepare accordingly') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Stay updated with institute notices and announcements') }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-blue-400 pl-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('Permissions & Access') }}</h3>
                        <ul class="text-gray-700 dark:text-gray-300 text-sm space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('View your enrolled courses and course details') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Access study materials and resources shared by teachers') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('View your own attendance records') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('View your examination results and marks') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-blue-600 dark:text-blue-400 mt-0.5"></i>
                                <span>{{ __('Access notices and announcements from the institution') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="space-y-4">
            <!-- Enrolled Courses Card -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-xl border border-blue-200 dark:border-blue-700 p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-600 dark:text-blue-300 font-semibold uppercase tracking-wide">{{ __('Enrolled Courses') }}</p>
                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-2">0</p>
                        <p class="text-xs text-blue-600 dark:text-blue-300 mt-2">{{ __('Active courses') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 dark:bg-blue-700 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-300">
                        <i class="bi bi-book text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Attendance Card -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl border border-green-200 dark:border-green-700 p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 dark:text-green-300 font-semibold uppercase tracking-wide">{{ __('Attendance') }}</p>
                        <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-2">0%</p>
                        <p class="text-xs text-green-600 dark:text-green-300 mt-2">{{ __('This semester') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 dark:bg-green-700 rounded-lg flex items-center justify-center text-green-600 dark:text-green-300">
                        <i class="bi bi-calendar-check text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- CGPA Card -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-xl border border-purple-200 dark:border-purple-700 p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-purple-600 dark:text-purple-300 font-semibold uppercase tracking-wide">{{ __('CGPA') }}</p>
                        <p class="text-3xl font-bold text-purple-900 dark:text-purple-100 mt-2">0.00</p>
                        <p class="text-xs text-purple-600 dark:text-purple-300 mt-2">{{ __('Cumulative') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-200 dark:bg-purple-700 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-300">
                        <i class="bi bi-graph-up text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-file-earmark"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Courses') }}</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('View your enrolled courses and materials') }}</p>
            <a href="{{ route('student.courses') }}" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                {{ __('View') }} <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center text-green-600 dark:text-green-400">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Attendance') }}</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('Check your attendance records') }}</p>
            <a href="{{ route('student.attendance') }}" class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm font-medium">
                {{ __('View') }} <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-award"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Results') }}</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('View your examination results') }}</p>
            <a href="{{ route('student.marks') }}" class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-sm font-medium">
                {{ __('View') }} <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center text-orange-600 dark:text-orange-400">
                    <i class="bi bi-bell"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Notices') }}</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('Check institute announcements') }}</p>
            <a href="{{ route('student.notices') }}" class="inline-flex items-center gap-2 text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium">
                {{ __('View') }} <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection

