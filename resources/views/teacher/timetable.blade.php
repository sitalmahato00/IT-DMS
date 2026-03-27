@extends('teacher.layouts.teacherlayout')

@section('title', __('My Timetable'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('My Timetable') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('View your weekly class schedule for assigned subjects.') }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Subjects') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSubjects }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Slots') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSlots }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-calendar3 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Days') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ collect($timetableByDay)->filter(fn($slots) => $slots->count() > 0)->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <i class="bi bi-calendar-week text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Semesters') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ count($semesters) }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i class="bi bi-layers text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('teacher.timetable') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Filter by Semester') }}</label>
                    <select name="semester" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Semesters') }}</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester }}" {{ $selectedSemester == $semester ? 'selected' : '' }}>
                                {{ __('Semester') }} {{ $semester }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium">
                    <i class="bi bi-funnel"></i> {{ __('Filter') }}
                </button>
                <a href="{{ route('teacher.timetable') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                    <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Timetable by Day -->
    <div class="space-y-4">
        @foreach($days as $day)
            @php
                $daySlots = $timetableByDay[$day] ?? collect();
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        <i class="bi bi-calendar-event mr-2"></i>{{ __(ucfirst($day)) }}
                    </h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $daySlots->count() }} {{ __('classes') }}</span>
                </div>
                
                @if($daySlots->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Time') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Subject') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Room') }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Semester') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($daySlots as $slot)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-900 dark:text-white font-medium">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</span>
                                                <span class="text-gray-400">-</span>
                                                <span class="text-gray-900 dark:text-white font-medium">{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 bg-red-100 dark:bg-red-900/40 rounded-full flex items-center justify-center">
                                                    <i class="bi bi-book text-red-600 dark:text-red-400 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $slot->subject->subject_name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $slot->subject->subject_code ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                            {{ $slot->room ?? 'N/A' }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400">
                                                {{ __('Semester') }} {{ $slot->semester ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <i class="bi bi-calendar-x text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400">No classes scheduled</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if($totalSlots === 0)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <i class="bi bi-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                <div>
                    <p class="font-medium text-yellow-800 dark:text-yellow-400">No Timetable Assigned</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-500">Your timetable will appear here once it's assigned by the administrator.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
