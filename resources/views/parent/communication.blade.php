@extends('parent.layouts.parentlayout')

@section('title', __('Communication'))
@section('subtitle', __('Department contacts, teacher outreach, meeting requests, and support resources'))

@section('content')
@php
    $departmentEmail = $department?->email;
    $departmentPhone = $department?->phone;
    $meetingSubject = rawurlencode(__('Meeting Request from Parent Portal'));
    $meetingBody = rawurlencode(__('Hello, I would like to request a meeting regarding my child’s academic progress.'));
@endphp

<div class="parent-smooth-page space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="parent-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Teacher Contacts') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $teacherContacts->count() }}</p>
        </div>
        <div class="parent-smooth-card rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Linked Children') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $childrenCount }}</p>
        </div>
        <div class="parent-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Notice Channels') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $recentNotices->count() }}</p>
        </div>
        <div class="parent-smooth-card rounded-xl border border-violet-200 dark:border-violet-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300 font-semibold">{{ __('Unread Notifications') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $unreadNotificationCount }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="parent-smooth-panel xl:col-span-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Department Contact') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Primary institution contact details for support, feedback, and escalation.') }}</p>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm">
                <div class="parent-smooth-list-card rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Department') }}</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $department?->name ?? __('IT Department') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Email') }}</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white break-all">{{ $departmentEmail ?: __('Not configured') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Phone') }}</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $departmentPhone ?: __('Not configured') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Address') }}</p>
                    <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $department?->address ?: __('Not configured') }}</p>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @if($departmentEmail)
                    <a href="mailto:{{ $departmentEmail }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                        <i class="bi bi-envelope"></i>
                        <span>{{ __('Email Department') }}</span>
                    </a>
                    <a href="mailto:{{ $departmentEmail }}?subject={{ $meetingSubject }}&body={{ $meetingBody }}" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                        <i class="bi bi-calendar-event"></i>
                        <span>{{ __('Request Meeting') }}</span>
                    </a>
                @endif
                @if($departmentPhone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $departmentPhone) }}" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                        <i class="bi bi-telephone"></i>
                        <span>{{ __('Call Department') }}</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="parent-smooth-panel xl:col-span-7 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Teacher Outreach') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Quick access to subject teachers connected to your linked students.') }}</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                @forelse($teacherContacts as $teacher)
                    <div class="parent-smooth-list-card rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $teacher['name'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $teacher['subject_name'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs">
                            @if($teacher['email'])
                                <a href="mailto:{{ $teacher['email'] }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-800 px-3 py-1.5 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                    <i class="bi bi-envelope"></i>
                                    <span>{{ __('Email') }}</span>
                                </a>
                            @endif
                            @if($teacher['phone'])
                                <a href="tel:{{ preg_replace('/\s+/', '', $teacher['phone']) }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-800 px-3 py-1.5 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                    <i class="bi bi-telephone"></i>
                                    <span>{{ __('Call') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="parent-smooth-empty rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-6 text-center text-sm text-gray-500 dark:text-gray-400 md:col-span-2">
                        {{ __('Teacher contacts will appear here once subjects and assignments are available.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="parent-smooth-panel xl:col-span-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Communication Strategies') }}</h2>
            <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-300">
                <div class="parent-smooth-list-card rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ __('Institution to Parent') }}</p>
                    <p class="mt-2">{{ __('Use the notice board, email outreach, dashboard alerts, and notification badges to stay informed about official updates.') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/20 p-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ __('Parent to Institution') }}</p>
                    <p class="mt-2">{{ __('Use direct email or phone links, request meetings, and provide feedback whenever attendance, marks, or support needs require follow-up.') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ __('Teacher to Parent') }}</p>
                    <p class="mt-2">{{ __('Teachers can publish parent-targeted notices and maintain outreach based on subject progress, exams, or events.') }}</p>
                </div>
            </div>
        </div>

        <div class="xl:col-span-6 space-y-6">
            <div id="help" class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Help Center') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ __('Check dashboard alerts before escalating an issue.') }}</li>
                    <li>{{ __('Use the print summary or CSV export when offline discussion is needed.') }}</li>
                    <li>{{ __('Reach the department directly if a child profile or contact path is missing.') }}</li>
                </ul>
            </div>

            <div id="docs" class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Documentation') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ __('Use the attendance, results, and courses pages as the primary parent workflow.') }}</li>
                    <li>{{ __('Monitor notices and the schedule page for exams, meetings, and institutional events.') }}</li>
                    <li>{{ __('Keep your profile accurate so notices and account recovery continue to work smoothly.') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
