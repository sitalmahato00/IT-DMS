@extends('parent.layouts.parentlayout')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-amber-600 to-amber-700 dark:from-amber-800 dark:to-amber-900 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ __('Welcome back, ') . (auth()->user()->name ?? 'Parent') }}! 👋</h1>
                <p class="text-amber-100 text-base">{{ __('You are logged in as a Parent Guardian. Monitor your child\'s academic progress, attendance, and stay updated with school announcements.') }}</p>
            </div>
            <div class="text-5xl opacity-20">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <!-- Role Description Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Role Card -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border-l-4 border-amber-600 overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Parent/Guardian Role') }}</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('Your role and responsibilities in the system') }}</p>
                    </div>
                    <div class="bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 p-3 rounded-lg">
                        <i class="bi bi-people text-2xl"></i>
                    </div>
                </div>
                
                <div class="space-y-4 mt-6">
                    <div class="border-l-2 border-amber-400 pl-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('General Overview') }}</h3>
                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                            {{ __("As a parent or guardian in the IT-DMS system, you have access to a comprehensive portal where you can monitor your child's academic performance and progress. The system enables seamless communication between parents and the institution, keeping you informed about important updates, notices, and your child's educational journey.") }}
                        </p>
                    </div>

                    <div class="border-l-2 border-amber-400 pl-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('Key Responsibilities') }}</h3>
                        <ul class="text-gray-700 dark:text-gray-300 text-sm space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Regularly monitor your child\'s attendance and academic progress') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Stay informed about examination schedules and results') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Review course information and enrolled subjects') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Keep track of important notices and announcements') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-check-circle-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Communicate with teachers and administrators when needed') }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="border-l-2 border-amber-400 pl-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ __('Permissions & Access') }}</h3>
                        <ul class="text-gray-700 dark:text-gray-300 text-sm space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('View your child\'s enrollment and course information') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Monitor attendance records and progress reports') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Access examination results and academic marks') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Receive and view official notices and announcements') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="bi bi-lock-fill text-amber-600 dark:text-amber-400 mt-0.5"></i>
                                <span>{{ __('Communicate directly with teachers regarding your child') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="space-y-4">
            <!-- Children Card -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900 dark:to-amber-800 rounded-xl border border-amber-200 dark:border-amber-700 p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-amber-600 dark:text-amber-300 font-semibold uppercase tracking-wide">{{ __('Children') }}</p>
                        <p class="text-3xl font-bold text-amber-900 dark:text-amber-100 mt-2">{{ $childrenCount ?? 0 }}</p>
                        <p class="text-xs text-amber-600 dark:text-amber-300 mt-2">{{ __('Enrolled children') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-200 dark:bg-amber-700 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-300">
                        <i class="bi bi-people text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Overall Attendance Card -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl border border-green-200 dark:border-green-700 p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 dark:text-green-300 font-semibold uppercase tracking-wide">{{ __('Overall Attendance') }}</p>
                        <p class="text-3xl font-bold text-green-900 dark:text-green-100 mt-2">{{ round($overallAttendance ?? 0, 1) }}%</p>
                        <p class="text-xs text-green-600 dark:text-green-300 mt-2">{{ __('All children average') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 dark:bg-green-700 rounded-lg flex items-center justify-center text-green-600 dark:text-green-300">
                        <i class="bi bi-calendar-check text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Unread Notices Card -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-xl border border-blue-200 dark:border-blue-700 p-5 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-blue-600 dark:text-blue-300 font-semibold uppercase tracking-wide">{{ __('New Notices') }}</p>
                        <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-2">{{ $unreadNotices ?? 0 }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-300 mt-2">{{ __('Unread notices') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 dark:bg-blue-700 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-300">
                        <i class="bi bi-bell text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Child Profile') }}</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('View your child\'s information') }}</p>
            <a href="{{ route('parent.children.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 text-sm font-medium">
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
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('Check attendance records') }}</p>
            <a href="{{ route('parent.attendance.index') }}" class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm font-medium">
                {{ __('View') }} <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Results') }}</h3>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('View examination results') }}</p>
            <a href="{{ route('parent.marks.index') }}" class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-sm font-medium">
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
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('Stay updated with announcements') }}</p>
            <a href="{{ route('parent.notices.index') }}" class="inline-flex items-center gap-2 text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium">
                {{ __('View') }} <i class="bi bi-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>
@endsection

