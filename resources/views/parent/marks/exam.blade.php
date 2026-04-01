@extends('parent.layouts.parentlayout')

@section('title', __('Exam Results - ') . ($child->user?->name ?? 'Unknown'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('parent.marks.child', $child->id) }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 mb-4">
                <i class="bi bi-arrow-left"></i>
                {{ __('Back to Results') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $exam->name ?? 'N/A' }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $child->user?->name ?? 'Unknown' }}</p>
        </div>
    </div>

    <!-- Exam Results -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Subject') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Obtained Marks') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Total Marks') }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('Percentage') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($marks as $mark)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-semibold">{{ $mark->subject?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-lg font-bold text-purple-600 dark:text-purple-400">{{ $mark->obtained_marks ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $mark->total_marks ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-full font-semibold">
                                    {{ round($mark->obtained_marks / ($mark->total_marks ?: 1) * 100, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-600 dark:text-gray-400">
                                {{ __('No results found for this exam') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
