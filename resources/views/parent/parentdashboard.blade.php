@extends('parent.layouts.parentlayout')

@section('title', __('Dashboard'))
@section('subtitle', __('Simple overview for parents'))

@section('content')
<div class="parent-smooth-page space-y-6">
    <div class="parent-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-600 via-red-700 to-red-800 p-6 md:p-8 text-white shadow-xl border border-red-700">
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/15 blur-2xl"></div>
        <div class="absolute -left-16 -bottom-14 h-48 w-48 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-2xl md:text-3xl font-bold">{{ __('Welcome back, :name', ['name' => $parentUser->name ?? __('Parent')]) }}</h1>
                <p class="mt-2 text-red-100">
                    {{ __('See attendance, marks, notices, and help for your child in one place.') }}
                </p>

                <div class="mt-4 flex flex-wrap gap-3 text-sm text-red-100">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5">
                        <i class="bi bi-people"></i>
                        {{ trans_choice('{0} No children|{1} :count child|[2,*] :count children', $childrenCount, ['count' => $childrenCount]) }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5">
                        <i class="bi bi-calendar2-check"></i>
                        {{ __('Attendance: :value%', ['value' => $overallAttendance]) }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5">
                        <i class="bi bi-bell"></i>
                        {{ __('Unread: :count', ['count' => $unreadNotificationCount]) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <p class="text-red-100">{{ __('Exams soon') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ $upcomingExamCount }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <p class="text-red-100">{{ __('Important notices') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ $importantNoticeCount }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <p class="text-red-100">{{ __('Average marks') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ $overallScore !== null ? $overallScore . '%' : '—' }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                    <p class="text-red-100">{{ __('Subjects') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ $totalSubjects }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($children->isEmpty())
        <div class="rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200">
                <i class="bi bi-person-x text-2xl"></i>
            </div>
            <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __('No linked children found') }}</h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('This parent account is active, but no student records are currently connected. Please contact the administrator to assign child profiles.') }}
            </p>
            <a href="{{ route('parent.communication') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                <i class="bi bi-chat-dots"></i>
                <span>{{ __('Open Communication Support') }}</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="parent-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Linked Children') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $childrenCount }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Children linked to this account') }}</p>
            </div>

            <div class="parent-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Overall Attendance') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $overallAttendance }}%</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Average attendance') }}</p>
            </div>

            <div class="parent-smooth-card rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Published Results') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $overallScore !== null ? $overallScore . '%' : '—' }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Average published marks') }}</p>
            </div>

            <div class="parent-smooth-card rounded-xl border border-violet-200 dark:border-violet-900 bg-white dark:bg-gray-800 p-5">
                <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300 font-semibold">{{ __('Portal Alerts') }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $academicAlerts->count() }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Things that need your attention') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <a href="{{ route('parent.children', ['child' => $selectedChildId]) }}" class="parent-smooth-quicklink rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4 hover:bg-red-100 dark:hover:bg-red-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Children') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Open child details') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('parent.attendance', ['child' => $selectedChildId]) }}" class="parent-smooth-quicklink rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4 hover:bg-emerald-100 dark:hover:bg-emerald-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Attendance') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('See attendance status') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('parent.results', ['child' => $selectedChildId]) }}" class="parent-smooth-quicklink rounded-xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/20 p-4 hover:bg-sky-100 dark:hover:bg-sky-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 text-white">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Marks') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('See latest marks') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('parent.communication') }}" class="parent-smooth-quicklink rounded-xl border border-violet-200 dark:border-violet-900 bg-violet-50 dark:bg-violet-950/20 p-4 hover:bg-violet-100 dark:hover:bg-violet-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-600 text-white">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Help') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Contact school or teacher') }}</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 space-y-6">
                <div class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('What To Check') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Start with these simple checks.') }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-200">{{ __('Start here') }}</span>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-3">
                        <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Attendance') }}</p>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ __('Check if any subject is below the safe attendance range.') }}</p>
                        </div>

                        <div class="rounded-xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/20 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Marks') }}</p>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ __('Review marks and upcoming exams for each child.') }}</p>
                        </div>

                        <div class="rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Need Help') }}</p>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ __('Use the help page when you need to talk to the school or a teacher.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="parent-smooth-panel rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Children Overview') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Attendance and marks for each child.') }}</p>
                        </div>
                        <a href="{{ route('parent.children') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Open detailed profiles') }}</a>
                    </div>

                    <div class="mt-5 grid items-start gap-4 lg:grid-cols-2">
                        @foreach($children as $child)
                            <div class="parent-smooth-list-card self-start rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $child['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('Roll No: :roll | Semester :semester', ['roll' => $child['roll_no'] ?: '—', 'semester' => $child['semester'] ?: '—']) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $child['academic_year'] ?: __('Academic year not set') }}</p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $child['failed_subjects'] > 0 ? 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' }}">
                                        {{ $child['failed_subjects'] > 0 ? __('Needs attention') : __('On track') }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                        <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Attendance') }}</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $child['attendance_percentage'] }}%</p>
                                    </div>
                                    <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                        <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Results') }}</p>
                                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $child['overall_percentage'] !== null ? $child['overall_percentage'] . '%' : '—' }}</p>
                                    </div>
                                </div>

                                @php
                                    $childTodayAttendance = collect($child['today_attendance'] ?? []);
                                @endphp

                                <div class="mt-4 rounded-xl border border-cyan-200 dark:border-cyan-900 bg-cyan-50/70 dark:bg-cyan-950/20 p-4">
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-cyan-700 dark:text-cyan-300 font-semibold">{{ __("Today's Attendance") }}</p>
                                            <p class="text-xs text-cyan-700/80 dark:text-cyan-200/80 mt-1">{{ $child['today_attendance_date_label'] ?? \Carbon\Carbon::now('Asia/Kathmandu')->format('M d, Y') }}</p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-white dark:bg-slate-800 px-2.5 py-1 text-xs font-semibold text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-900">
                                            {{ $childTodayAttendance->count() }}
                                        </span>
                                    </div>

                                    @if($childTodayAttendance->isEmpty())
                                        <p class="text-sm text-cyan-700 dark:text-cyan-200">{{ __('No attendance has been marked for today yet.') }}</p>
                                    @else
                                        <div class="space-y-2">
                                            @foreach($childTodayAttendance->take(3) as $record)
                                                @php
                                                    $statusTone = match ($record['status']) {
                                                        'present' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                        'absent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                                    };
                                                @endphp
                                                <div class="flex items-center justify-between gap-3 rounded-lg bg-white/80 dark:bg-slate-800/80 px-3 py-2 border border-cyan-100 dark:border-cyan-900">
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $record['subject_name'] }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $record['subject_code'] ?: '—' }}</p>
                                                    </div>
                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $statusTone }}">
                                                        {{ ucfirst($record['status']) }}
                                                    </span>
                                                </div>
                                            @endforeach

                                            @if($childTodayAttendance->count() > 3)
                                                <p class="text-xs text-cyan-700 dark:text-cyan-200">{{ __('+ :count more subjects', ['count' => $childTodayAttendance->count() - 3]) }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="rounded-full bg-white dark:bg-slate-800 px-3 py-1 border border-gray-200 dark:border-slate-700">{{ trans_choice('{1} :count subject|[2,*] :count subjects', $child['subject_count'], ['count' => $child['subject_count']]) }}</span>
                                    <span class="rounded-full bg-white dark:bg-slate-800 px-3 py-1 border border-gray-200 dark:border-slate-700">{{ __('Pass: :count', ['count' => $child['passed_subjects']]) }}</span>
                                    <span class="rounded-full bg-white dark:bg-slate-800 px-3 py-1 border border-gray-200 dark:border-slate-700">{{ __('Pending: :count', ['count' => $child['pending_subjects']]) }}</span>
                                </div>

                                <div class="mt-4 flex items-center gap-2">
                                    <a href="{{ route('parent.children', ['child' => $child['id']]) }}" class="inline-flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                        {{ __('Profile') }}
                                    </a>
                                    <a href="{{ route('parent.results', ['child' => $child['id']]) }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                                        {{ __('Results') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Notices') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Important communications for parents and shared audiences.') }}</p>
                        </div>
                        <a href="{{ route('parent.notices') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('View all notices') }}</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($recentNotices->take(5) as $notice)
                            <div class="rounded-xl border {{ $notice->is_important ? 'border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40' }} p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notice->localized_title }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit(strip_tags($notice->localized_message), 140) }}</p>
                                    </div>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $notice->is_important ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200' }}">
                                        {{ $notice->localized_priority_label }}
                                    </span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $notice->formatted_date }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $notice->localized_audience_label }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No parent notices have been published yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recommended Workflow') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('A routine that keeps parents informed and responsive.') }}</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Daily') }}</p>
                            <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                                <li>{{ __('Review the welcome summary and active alerts.') }}</li>
                                <li>{{ __('Check quick stats for children, attendance, and notices.') }}</li>
                                <li>{{ __('Open child profiles when follow-up is needed.') }}</li>
                            </ol>
                        </div>

                        <div class="rounded-xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/20 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Weekly') }}</p>
                            <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                                <li>{{ __('Review attendance patterns for irregularities.') }}</li>
                                <li>{{ __('Track exam schedules, results, and course progress.') }}</li>
                                <li>{{ __('Read all new notices and communicate early when concerns arise.') }}</li>
                            </ol>
                        </div>

                        <div class="rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Periodic') }}</p>
                            <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                                <li>{{ __('Update profile details and preferred contact channels.') }}</li>
                                <li>{{ __('Export records for personal reference each term or quarter.') }}</li>
                                <li>{{ __('Use help resources when new features or processes change.') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-5 space-y-6">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Alerts') }}</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $academicAlerts->count() }} {{ __('items') }}</span>
                    </div>

                    @if($academicAlerts->isEmpty())
                        <div class="mt-4 rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ __('Everything looks fine right now.') }}</p>
                            <p class="mt-1 text-sm text-emerald-600 dark:text-emerald-400">{{ __('No urgent issues were found for your children.') }}</p>
                        </div>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach($academicAlerts as $alert)
                                @php
                                    $alertClasses = match ($alert['tone']) {
                                        'error' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/20 dark:text-red-300',
                                        'warning' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/20 dark:text-red-300',
                                        default => 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900 dark:bg-sky-950/20 dark:text-sky-300',
                                    };
                                @endphp
                                <div class="rounded-xl border p-4 {{ $alertClasses }}">
                                    <p class="text-xs font-semibold uppercase tracking-wide">{{ $alert['title'] }}</p>
                                    <p class="mt-1 text-sm">{{ $alert['message'] }}</p>
                                    <p class="mt-2 text-xs opacity-80">{{ $alert['child_name'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Coming Soon') }}</h2>
                        <a href="{{ route('parent.events') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Open schedule') }}</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse($upcomingExamFeed->take(4) as $event)
                            <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $event['exam_name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $event['subject_name'] }} • {{ $event['child_name'] }}</p>
                                    </div>
                                    <span class="rounded-full bg-white dark:bg-slate-800 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-200 border border-red-200 dark:border-red-900">{{ $event['countdown_label'] }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $event['date_label'] }}</p>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No upcoming exams are scheduled right now.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Help & Downloads') }}</h2>
                        <a href="{{ route('parent.communication') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Get help') }}</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Download Records') }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Download or print child records when needed.') }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('parent.export') }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                                    <i class="bi bi-download"></i>
                                    <span>{{ __('Export CSV') }}</span>
                                </a>
                                <a href="{{ route('parent.print', array_filter(['child' => $selectedChildId])) }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                    <i class="bi bi-printer"></i>
                                    <span>{{ __('Print Summary') }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Need Help?') }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Open help or support pages if you need follow-up.') }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('parent.communication') }}#help" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                    <i class="bi bi-life-preserver"></i>
                                    <span>{{ __('Help Center') }}</span>
                                </a>
                                <a href="{{ route('parent.communication') }}#docs" class="inline-flex items-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                    <i class="bi bi-journal-text"></i>
                                    <span>{{ __('Documentation') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @if(false)
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Notices') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Important communications for parents and shared audiences.') }}</p>
                    </div>
                    <a href="{{ route('parent.notices') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('View all notices') }}</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($recentNotices->take(5) as $notice)
                        <div class="rounded-xl border {{ $notice->is_important ? 'border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40' }} p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notice->localized_title }}</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit(strip_tags($notice->localized_message), 140) }}</p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $notice->is_important ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200' }}">
                                    {{ $notice->localized_priority_label }}
                                </span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $notice->formatted_date }}</span>
                                <span>•</span>
                                <span>{{ $notice->localized_audience_label }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No parent notices have been published yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="xl:col-span-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recommended Workflow') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('A routine that keeps parents informed and responsive.') }}</p>
                    </div>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Daily') }}</p>
                        <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                            <li>{{ __('Review the welcome summary and active alerts.') }}</li>
                            <li>{{ __('Check quick stats for children, attendance, and notices.') }}</li>
                            <li>{{ __('Open child profiles when follow-up is needed.') }}</li>
                        </ol>
                    </div>

                    <div class="rounded-xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-950/20 p-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Weekly') }}</p>
                        <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                            <li>{{ __('Review attendance patterns for irregularities.') }}</li>
                            <li>{{ __('Track exam schedules, results, and course progress.') }}</li>
                            <li>{{ __('Read all new notices and communicate early when concerns arise.') }}</li>
                        </ol>
                    </div>

                    <div class="rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Periodic') }}</p>
                        <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300 list-decimal pl-5">
                            <li>{{ __('Update profile details and preferred contact channels.') }}</li>
                            <li>{{ __('Export records for personal reference each term or quarter.') }}</li>
                            <li>{{ __('Use help resources when new features or processes change.') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
