@extends('teacher.layouts.teacherlayout')

@section('title', __('Add Notice'))

@section('content')
<div class="teacher-smooth-page teacher-notices-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <div class="teacher-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="min-w-0">
            <h1 class="teacher-page-header-title text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Notice') }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Publish a new notice for students and faculty audiences.') }}</p>
        </div>
        <a href="{{ route('teacher.notices') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
            <i class="bi bi-arrow-left"></i> {{ __('Back to Notices') }}
        </a>
    </div>

    <div class="teacher-smooth-form-panel bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        @if($errors->any())
            <div class="mb-4 p-4 rounded-lg border border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.notices.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Title') }} <span class="text-red-500">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="{{ __('Notice title') }}">
                </div>

                <div>
                    <label for="audience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Audience') }} <span class="text-red-500">*</span></label>
                    <select id="audience" name="audience" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="all" {{ old('audience', 'all') === 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option value="students" {{ old('audience') === 'students' ? 'selected' : '' }}>{{ __('Students') }}</option>
                        <option value="faculty" {{ old('audience') === 'faculty' ? 'selected' : '' }}>{{ __('Faculty') }}</option>
                        <option value="parents" {{ old('audience') === 'parents' ? 'selected' : '' }}>{{ __('Parents') }}</option>
                    </select>
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Subject') }}</label>
                    <select id="subject_id" name="subject_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('General Notice (All Subjects)') }}</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject['id'] }}" {{ (string) old('subject_id') === (string) $subject['id'] ? 'selected' : '' }}>
                                {{ $subject['code'] ? $subject['code'].' - ' : '' }}{{ $subject['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Semester') }}</label>
                    <input id="semester" name="semester" type="text" value="{{ old('semester') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="{{ __('Optional') }}">
                </div>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Message') }} <span class="text-red-500">*</span></label>
                <textarea id="message" name="message" rows="6" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="{{ __('Write the notice details...') }}">{{ old('message') }}</textarea>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="is_important" value="1" {{ old('is_important') ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                {{ __('Mark as important') }}
            </label>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition font-medium">
                    <i class="bi bi-send"></i> {{ __('Publish Notice') }}
                </button>
                <a href="{{ route('teacher.notices') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

