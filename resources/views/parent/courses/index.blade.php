@extends('parent.layouts.parentlayout')

@section('title', __('Courses & Subjects'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Courses & Subjects') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('View your children\'s enrolled courses') }}</p>
        </div>
    </div>

    @if($children->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
            <i class="bi bi-file-earmark text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">{{ __('No courses available') }}</p>
        </div>
    @else
        <!-- Courses by Semester -->
        @foreach($subjectsBySemester as $semester => $semesterSubjects)
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('Semester ') }}{{ $semester }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($semesterSubjects as $subject)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 p-4 text-white">
                                <h3 class="text-lg font-semibold">{{ $subject->name ?? 'N/A' }}</h3>
                                <p class="text-blue-100 text-sm">{{ __('Code: ') }}{{ $subject->code ?? 'N/A' }}</p>
                            </div>

                            <!-- Body -->
                            <div class="p-6 space-y-4">
                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Credit Hours') }}</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $subject->credit_hours ?? 'N/A' }}</p>
                                </div>

                                @if($subject->teachers->isNotEmpty())
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Instructor') }}</p>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $subject->teachers->first()?->user?->name ?? 'N/A' }}</p>
                                    </div>
                                @endif

                                <div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold uppercase">{{ __('Description') }}</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ Str::limit($subject->description ?? 'No description available', 100) }}</p>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4">
                                @php
                                    $childWithSubject = $children->filter(fn($c) => $c->subjects->contains($subject->id))->first();
                                @endphp
                                @if($childWithSubject)
                                    <a href="{{ route('parent.courses.subject', [$childWithSubject->id, $subject->id]) }}" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">
                                        {{ __('View Details') }}
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

