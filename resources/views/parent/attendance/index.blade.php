@extends('parent.layouts.parentlayout')

@section('title', __('Attendance'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Attendance Records') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Monitor your children\'s attendance') }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('Subject') }}</label>
                <select name="subject_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
                    <option value="">{{ __('All Subjects') }}</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('From Date') }}</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('To Date') }}</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-amber-600">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-amber-800 dark:hover:bg-amber-700 text-white rounded-lg font-medium transition">
                    <i class="bi bi-search"></i>
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-{{ $children->count() }} gap-4">
        @foreach($children as $child)
            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl border border-green-200 dark:border-green-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold text-green-600 dark:text-green-300 uppercase">{{ $child->user?->name ?? 'N/A' }}</p>
                    <i class="bi bi-calendar-check text-green-600 dark:text-green-400"></i>
                </div>
                <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ round($attendancePercentages[$child->id] ?? 0, 1) }}%</p>
                <p class="text-xs text-green-700 dark:text-green-300 mt-2">{{ __('Overall Attendance') }}</p>
            </div>
        @endforeach
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Child') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Subject') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Date') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($attendance as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 text-gray-900 dark:text-white">
                                <a href="{{ route('parent.attendance.child', $record->student_id) }}" class="text-amber-600 dark:text-amber-400 hover:underline">
                                    {{ $record->student?->user?->name ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $record->subject?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $record->attendance_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($record->is_present)
                                    <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold">{{ __('Present') }}</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full text-sm font-semibold">{{ __('Absent') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                {{ __('No attendance records found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendance->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $attendance->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
