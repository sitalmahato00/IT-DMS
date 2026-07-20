@extends('parent.layouts.parentlayout')

@section('title', __('Communication'))
@section('subtitle', __('Simple contact and help page for parents'))

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
            <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Teachers') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $teacherContacts->count() }}</p>
        </div>
        <div class="parent-smooth-card rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Linked Children') }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $childrenCount }}</p>
        </div>
        <div class="parent-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
            <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Notices') }}</p>
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('School Contact') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Use these details to contact the department directly.') }}</p>
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Teacher Contacts') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Call or email the teachers linked to your child.') }}</p>
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
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('When To Contact Us') }}</h2>
            <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-300">
                <div class="parent-smooth-list-card rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ __('Attendance Or Marks') }}</p>
                    <p class="mt-2">{{ __('Contact the school if attendance or marks need follow-up.') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/20 p-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ __('Meeting Request') }}</p>
                    <p class="mt-2">{{ __('Use email or the meeting link when you need to talk in detail.') }}</p>
                </div>
                <div class="parent-smooth-list-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ __('General Help') }}</p>
                    <p class="mt-2">{{ __('Use this page if you are not sure whom to contact.') }}</p>
                </div>
            </div>
        </div>

        <div class="xl:col-span-6 space-y-6">
            <div id="help" class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Quick Help') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ __('Check the dashboard before contacting support.') }}</li>
                    <li>{{ __('Use print or export if you need records offline.') }}</li>
                    <li>{{ __('Contact the department if a child record is missing.') }}</li>
                </ul>
            </div>

            <div id="docs" class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Best Place To Start') }}</h2>
                <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ __('Open Attendance to check presence and absence.') }}</li>
                    <li>{{ __('Open Marks to see results and exams.') }}</li>
                    <li>{{ __('Open Profile to keep your contact details updated.') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

