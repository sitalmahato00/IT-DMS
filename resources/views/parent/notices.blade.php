@extends('parent.layouts.parentlayout')

@section('title', __('Notices'))
@section('subtitle', __('Parent-targeted announcements, important updates, and institutional notices'))

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Total Notices') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $recentNotices->count() }}</p>
        </div>
        <div class="rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Important') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $importantNoticeCount }}</p>
        </div>
        <div class="rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Recent (14 days)') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $recentNoticeCount }}</p>
        </div>
        <div class="rounded-xl border border-violet-200 dark:border-violet-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300 font-semibold">{{ __('Unread Alerts') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $unreadNotificationCount }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Notice Board') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Read all published parent and shared audience notices from the institution.') }}</p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse($recentNotices as $notice)
                    <div class="rounded-xl border {{ $notice->is_important ? 'border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40' }} p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $notice->localized_title }}</p>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $notice->is_important ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200' }}">
                                        {{ $notice->localized_priority_label }}
                                    </span>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $notice->formatted_date }}</span>
                                    <span>•</span>
                                    <span>{{ $notice->localized_audience_label }}</span>
                                    @if($notice->semester)
                                        <span>•</span>
                                        <span>{{ __('Semester :semester', ['semester' => $notice->semester]) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-sm text-gray-600 dark:text-gray-300 leading-6">
                            {{ $notice->localized_message }}
                        </div>

                        @if($notice->has_attachment)
                            <div class="mt-4">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($notice->file_path) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                    <i class="bi bi-paperclip"></i>
                                    <span>{{ $notice->file_name ?: __('Open attachment') }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No notices are available right now.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-4 space-y-6">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('How Notices Support Parents') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ __('Teachers and administrators can target notices directly to parents.') }}</li>
                    <li>{{ __('Important updates also surface in the header notification area with unread counts.') }}</li>
                    <li>{{ __('Use notices together with the events page to stay ahead of exams and meetings.') }}</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recommended Reading Routine') }}</h2>
                <ol class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                    <li>{{ __('Check new notices during each portal visit.') }}</li>
                    <li>{{ __('Prioritize items marked Important or tied to a child semester.') }}</li>
                    <li>{{ __('Follow up with teachers when a notice needs clarification or action.') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

