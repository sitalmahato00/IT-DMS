@extends('parent.layouts.parentlayout')

@section('title', __('Attendance - ') . ($child->user?->name ?? 'Unknown'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('parent.attendance.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
                <i class="bi bi-arrow-left"></i>
                {{ __('Back to Attendance') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $child->user?->name ?? 'Unknown' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Attendance Records') }}</p>
        </div>
        <a href="{{ route('parent.attendance.pdf', $child->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-800 dark:hover:bg-red-700 text-white rounded-lg font-medium transition" target="_blank">
            <i class="bi bi-file-pdf"></i>
            {{ __('Download PDF') }}
        </a>
    </div>

    <!-- Attendance Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-xl border border-green-200 dark:border-green-700 p-6">
            <p class="text-sm text-green-600 dark:text-green-300 font-semibold uppercase">{{ __('Overall Attendance') }}</p>
            <p class="text-4xl font-bold text-green-900 dark:text-green-100 mt-2">{{ round($attendancePercentage, 1) }}%</p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-xl border border-blue-200 dark:border-blue-700 p-6">
            <p class="text-sm text-blue-600 dark:text-blue-300 font-semibold uppercase">{{ __('Total Classes') }}</p>
            <p class="text-4xl font-bold text-blue-900 dark:text-blue-100 mt-2">{{ $attendance->total() ?? 0 }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-xl border border-purple-200 dark:border-purple-700 p-6">
            <p class="text-sm text-purple-600 dark:text-purple-300 font-semibold uppercase">{{ __('Subjects') }}</p>
            <p class="text-4xl font-bold text-purple-900 dark:text-purple-100 mt-2">{{ $subjects->count() }}</p>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Date') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Subject') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($attendance as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $record->attendance_date?->format('Y-m-d') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $record->subject?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($record->is_present)
                                    <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-sm font-semibold"><i class="bi bi-check-circle"></i> {{ __('Present') }}</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full text-sm font-semibold"><i class="bi bi-x-circle"></i> {{ __('Absent') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                {{ __('No attendance records found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendance->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $attendance->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
