@extends('parent.layouts.parentlayout')

@section('title', __('Marks - ') . ($child->user?->name ?? 'Unknown'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('parent.marks.index') }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
                <i class="bi bi-arrow-left"></i>
                {{ __('Back to Results') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $child->user?->name ?? 'Unknown' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Academic Performance') }}</p>
        </div>
        <a href="{{ route('parent.marks.pdf', $child->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-800 dark:hover:bg-red-700 text-white rounded-lg font-medium transition" target="_blank">
            <i class="bi bi-file-pdf"></i>
            {{ __('Download PDF') }}
        </a>
    </div>

    <!-- Subject-wise Performance -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($subjectPerformance as $performance)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $performance->subject?->name ?? 'N/A' }}</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Average Marks') }}</p>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-purple-600" style="width: {{ min(100, ($performance->avg_marks / 100 * 100)) }}%"></div>
                            </div>
                            <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ round($performance->avg_marks, 1) }}</span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Exams: ') }}{{ $performance->total_exams }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-8 text-center">
                <i class="bi bi-graph-up text-4xl text-gray-400 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-600 dark:text-gray-400">{{ __('No examination results available') }}</p>
            </div>
        @endforelse
    </div>

    <!-- All Marks -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('All Examination Marks') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Exam') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Subject') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Obtained') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Total') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Percentage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($marks as $mark)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $mark->exam?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $mark->subject?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $mark->obtained_marks ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $mark->total_marks ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-full text-sm font-semibold">
                                    {{ round($mark->obtained_marks / ($mark->total_marks ?: 1) * 100, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                {{ __('No examination records found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($marks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $marks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
