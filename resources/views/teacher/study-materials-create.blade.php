@extends('teacher.layouts.teacherlayout')

@section('title', __('Upload Study Material'))

@section('content')
<div class="teacher-smooth-page teacher-materials-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <div class="teacher-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="min-w-0">
            <h1 class="teacher-page-header-title text-2xl font-bold text-gray-900 dark:text-white">{{ __('Upload Study Material') }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('Upload files for your assigned subjects only.') }}</p>
        </div>
        <a href="{{ route('teacher.study-materials') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
            <i class="bi bi-arrow-left"></i> {{ __('Back to Materials') }}
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

        <form method="POST" action="{{ route('teacher.study-materials.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Subject') }} <span class="text-red-500">*</span></label>
                    <select id="subject_id" name="subject_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('Select Subject') }}</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject['id'] }}" {{ (string) old('subject_id') === (string) $subject['id'] ? 'selected' : '' }}>
                                {{ $subject['code'] ? $subject['code'].' - ' : '' }}{{ $subject['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Title') }} <span class="text-red-500">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="{{ __('Material title') }}">
                </div>

                <div>
                    <label for="document_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Document Type') }} <span class="text-red-500">*</span></label>
                    <select id="document_type" name="document_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                        @foreach($documentTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('document_type', 'lecture_notes') === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Visibility') }} <span class="text-red-500">*</span></label>
                    <select id="visibility" name="visibility" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="all" {{ old('visibility', 'all') === 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option value="students" {{ old('visibility') === 'students' ? 'selected' : '' }}>{{ __('Students') }}</option>
                        <option value="faculty" {{ old('visibility') === 'faculty' ? 'selected' : '' }}>{{ __('Faculty') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
                <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="{{ __('Optional description about this material...') }}">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('File') }} <span class="text-red-500">*</span></label>
                <input id="file" name="file" type="file" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Allowed types: PDF, Office docs, images, ZIP/RAR (max 20MB)') }}</p>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="teacher-page-primary-btn inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition font-medium">
                    <i class="bi bi-upload"></i> {{ __('Upload Material') }}
                </button>
                <a href="{{ route('teacher.study-materials') }}" class="teacher-page-secondary-btn inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
