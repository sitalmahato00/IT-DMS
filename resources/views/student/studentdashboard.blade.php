@extends('student.layouts.studentlayout')

@section('title', __('Dashboard'))
@section('subtitle', __('Student Portal Overview'))

@section('content')
@php
    $studentName = $user->name ?? auth()->user()?->name ?? __('Student');
    $displaySemester = $student->semester ?: ($subjectStats->max('semester') ?: __('N/A'));
    $displaySubjects = $subjectStats->take(6);
    $attentionItems = collect();
    $todayKey = strtolower(now()->format('l'));

    foreach ($subjectStats as $subject) {
        if ($subject['attendance_percentage'] < 75) {
            $attentionItems->push([
                'type' => __('Attendance'),
                'message' => __(':subject is below the 75% attendance target.', ['subject' => $subject['name']]),
                'tone' => 'warning',
            ]);
        }

        if ($subject['status'] === 'fail') {
            $attentionItems->push([
                'type' => __('Result'),
                'message' => __(':subject currently shows a failing result.', ['subject' => $subject['name']]),
                'tone' => 'error',
            ]);
        }

        if ($subject['status'] === 'pending') {
            $attentionItems->push([
                'type' => __('Pending'),
                'message' => __('Marks for :subject have not been published yet.', ['subject' => $subject['name']]),
                'tone' => 'info',
            ]);
        }
    }

    $attentionItems = $attentionItems->take(4);
@endphp

<div class="student-smooth-page student-dashboard-page space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif" id="studentDashboardApp">
    <div class="student-smooth-hero relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#FF0037] via-[#D90033] to-[#B2002F] p-6 md:p-8 text-white shadow-xl border border-[#D90033]">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/20 blur-2xl"></div>
        <div class="absolute -left-10 -bottom-16 w-56 h-56 rounded-full bg-black/10 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">{{ __('Welcome back,') }} {{ $studentName }}</h1>
                <p class="text-[#ffe5ea] mt-2">{{ __('Track your courses, attendance, and marks from a single student workspace.') }}</p>
                <div class="mt-3 flex flex-wrap gap-3 text-sm text-[#ffe5ea]">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-person-badge"></i> {{ __('Roll No:') }} {{ $student->roll_no ?? __('N/A') }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-calendar3"></i> {{ __('Semester') }} {{ $displaySemester }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/15 border border-white/25">
                        <i class="bi bi-book"></i> {{ $subjectCount }} {{ __('subjects') }}
                    </span>
                </div>
            </div>

            <div class="hidden lg:flex items-center justify-center w-24 h-24 rounded-3xl bg-white/10 border border-white/15 shadow-lg">
                <i class="bi bi-mortarboard text-5xl text-white/90"></i>
            </div>
        </div>
    </div>

    <div class="space-y-6" data-student-search-root>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="student-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5" data-student-search-item data-student-search-text="{{ __('Enrolled Subjects') }} {{ __('courses subjects enrolled') }}">
                <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Enrolled Subjects') }}</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjectCount }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Active subjects in your profile') }}</p>
            </div>

            <div class="student-smooth-card rounded-xl border border-blue-200 dark:border-blue-900 bg-white dark:bg-gray-800 p-5" data-student-search-item data-student-search-text="{{ __('Attendance') }} {{ __('attendance percentage') }}">
                <p class="text-xs uppercase tracking-wide text-blue-700 dark:text-blue-300 font-semibold">{{ __('Attendance') }}</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $overallAttendance }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Overall class attendance') }}</p>
            </div>

            <div class="student-smooth-card rounded-xl border border-purple-200 dark:border-purple-900 bg-white dark:bg-gray-800 p-5" data-student-search-item data-student-search-text="{{ __('Overall Score') }} {{ __('marks percentage') }}">
                <p class="text-xs uppercase tracking-wide text-purple-700 dark:text-purple-300 font-semibold">{{ __('Overall Score') }}</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $overallPercentage }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $gradedSubjectCount }} {{ __('graded subjects') }}</p>
            </div>

            <div class="student-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5" data-student-search-item data-student-search-text="{{ __('CGPA') }} {{ __('cgpa academic score') }}">
                <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('CGPA') }}</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($cgpa, 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('Calculated from published marks') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="student-smooth-panel xl:col-span-7 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Academic Status') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('A quick look at pass, fail, and pending subjects.') }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                        {{ $subjectCount }} {{ __('tracked') }}
                    </span>
                </div>

                <div class="h-72">
                    <canvas id="studentStatusChart"></canvas>
                    <p id="studentStatusChartEmpty" class="hidden text-sm text-gray-500 dark:text-gray-400 text-center pt-24">{{ __('No academic status data available yet.') }}</p>
                </div>
            </div>

            <div class="xl:col-span-5 space-y-6">
                <div class="student-smooth-panel rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5" data-student-search-item data-student-search-text="{{ __('Academic alerts') }} {{ __('alerts warnings pending fail attendance') }}">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Academic Alerts') }}</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $attentionItems->count() }} {{ __('items') }}</span>
                    </div>

                    @if($attentionItems->isEmpty())
                        <div class="rounded-lg border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4">
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">{{ __('You are in good standing.') }}</p>
                            <p class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">{{ __('No immediate attendance or result issues were detected.') }}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($attentionItems as $alert)
                                @php
                                    $toneClasses = match ($alert['tone']) {
                                        'error' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-900 dark:bg-red-950/20 dark:text-red-300',
                                        'warning' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950/20 dark:text-amber-300',
                                        default => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-900 dark:bg-blue-950/20 dark:text-blue-300',
                                    };
                                @endphp
                                <div class="rounded-lg border p-3 {{ $toneClasses }}">
                                    <p class="text-xs font-semibold uppercase tracking-wide">{{ $alert['type'] }}</p>
                                    <p class="text-sm mt-1">{{ $alert['message'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="student-smooth-panel rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Quick Actions') }}</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Jump to key pages') }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <a href="{{ route('student.courses') }}" class="student-smooth-quicklink rounded-xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 p-4 hover:bg-red-100 dark:hover:bg-red-950/30 transition" data-student-search-item data-student-search-text="{{ __('Courses') }} {{ __('my courses subjects') }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center">
                <i class="bi bi-journal-bookmark"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ __('Courses') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Open enrolled subjects') }}</p>
            </div>
        </div>
    </a>

    <a href="{{ route('student.attendance') }}" class="student-smooth-quicklink rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-950/20 p-4 hover:bg-blue-100 dark:hover:bg-blue-950/30 transition" data-student-search-item data-student-search-text="{{ __('Attendance') }} {{ __('attendance records') }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ __('Attendance') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Review attendance history') }}</p>
            </div>
        </div>
    </a>

    <a href="{{ route('student.marks') }}" class="student-smooth-quicklink rounded-xl border border-purple-200 dark:border-purple-900 bg-purple-50 dark:bg-purple-950/20 p-4 hover:bg-purple-100 dark:hover:bg-purple-950/30 transition" data-student-search-item data-student-search-text="{{ __('Marks') }} {{ __('results examinations') }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ __('Marks') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('See published results') }}</p>
            </div>
        </div>
    </a>

    <a href="{{ route('student.timetable') }}" class="student-smooth-quicklink rounded-xl border border-indigo-200 dark:border-indigo-900 bg-indigo-50 dark:bg-indigo-950/20 p-4 hover:bg-indigo-100 dark:hover:bg-indigo-950/30 transition" data-student-search-item data-student-search-text="{{ __('Timetable') }} {{ __('class schedule') }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                <i class="bi bi-calendar3"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ __('Timetable') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('View your class schedule') }}</p>
            </div>
        </div>
    </a>

    <a href="{{ route('student.profile.edit') }}" class="student-smooth-quicklink rounded-xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/20 p-4 hover:bg-emerald-100 dark:hover:bg-emerald-950/30 transition" data-student-search-item data-student-search-text="{{ __('Profile') }} {{ __('profile settings account') }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                <i class="bi bi-person-gear"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ __('Profile') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Update account details') }}</p>
            </div>
        </div>
    </a>
</div>
                </div>
            </div>
        </div>

        <div class="student-smooth-panel rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4" data-student-search-item data-student-search-text="{{ __('Timetable routine classes') }}">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Class Routine') }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $timetableTotalSlots }} {{ __('slots this week') }}</p>
                </div>
                <a href="{{ route('student.timetable') }}" class="inline-flex items-center gap-1 text-xs font-medium text-red-700 dark:text-red-400 hover:underline">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>{{ __('Full view') }}</span>
                </a>
            </div>

            @if($timetableTotalSlots === 0)
                <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 px-4 py-5 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No routine assigned yet.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7 gap-3">
                    @foreach($timetableDays as $day)
                        @php
                            $daySlots = $timetableByDay[$day] ?? collect();
                            $isToday = $todayKey === $day;
                        @endphp

                        @if($daySlots->isNotEmpty())
                            <div class="rounded-lg border {{ $isToday ? 'border-red-300 dark:border-red-800 bg-red-50/70 dark:bg-red-950/10' : 'border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-900/40' }} p-3">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($day) }}</p>
                                    @if($isToday)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-600 text-white">{{ __('Today') }}</span>
                                    @endif
                                </div>

                                <div class="space-y-2">
                                    @foreach($daySlots->take(2) as $slot)
                                        <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-2.5 py-2">
                                            <p class="text-[11px] text-red-600 dark:text-red-300 font-semibold">{{ $slot->short_time_range }}</p>
                                            <p class="text-xs text-gray-800 dark:text-gray-200 truncate mt-0.5">
                                                {{ $slot->subject?->subject_code ?? ($slot->subject?->subject_name ?? __('Class')) }}
                                            </p>
                                        </div>
                                    @endforeach

                                    @if($daySlots->count() > 2)
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">+{{ $daySlots->count() - 2 }} {{ __('more') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Subject Snapshot') }}</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $displaySubjects->count() }} {{ __('visible subjects') }}</span>
                </div>

                <div class="p-5">
                    @if($displaySubjects->isEmpty())
                        <div class="text-center py-12">
                            <i class="bi bi-book text-3xl text-gray-300 dark:text-gray-600"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No enrolled subjects found yet.') }}</p>
                        </div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-2">
                            @foreach($displaySubjects as $subject)
                                @php
                                    $statusClasses = match ($subject['status']) {
                                        'pass' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'fail' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                    };
                                @endphp
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-4" data-student-search-item data-student-search-text="{{ $subject['name'] }} {{ $subject['code'] }} {{ $subject['teacher'] }} {{ __('semester') }} {{ $subject['semester'] }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }} • {{ __('Semester') }} {{ $subject['semester'] }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Instructor:') }} {{ $subject['teacher'] }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide {{ $statusClasses }}">
                                            {{ $subject['status'] === 'pending' ? __('Pending') : ucfirst($subject['status']) }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mt-4">
                                        <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                            <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Attendance') }}</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $subject['attendance_percentage'] }}%</p>
                                        </div>
                                        <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                            <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Marks') }}</p>
                                            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ is_null($subject['percentage']) ? '—' : $subject['percentage'] . '%' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 mt-4">
                                        <a href="{{ route('student.courses.show', $subject['id']) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-gray-200 hover:border-red-300 hover:text-red-600 transition">
                                            {{ __('Course') }}
                                        </a>
                                        <a href="{{ route('student.marks.show', $subject['id']) }}" class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition">
                                            {{ __('Results') }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
        </div>

        <div class="xl:col-span-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __("Today's Attendance") }}</h2>
                    <span class="text-xs font-medium text-red-700 dark:text-red-400">
                        {{ \Carbon\Carbon::now('Asia/Kathmandu')->format('M d, Y') }}
                    </span>
                </div>

                @if($todayAttendance->isEmpty())
                    <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No attendance has been marked for today yet.') }}</p>
                    </div>
                @else
                    <div class="space-y-3 mb-6">
                        @foreach($todayAttendance as $record)
                            @php
                                $statusTone = match ($record['status']) {
                                    'present' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-300',
                                    'absent' => 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-300',
                                    default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-300',
                                };
                            @endphp
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record['subject_name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $record['subject_code'] ?: '—' }} • {{ $record['date_label'] }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide {{ $statusTone }}">
                                        {{ ucfirst($record['status']) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Attendance') }}</h2>
                    <a href="{{ route('student.attendance') }}" class="text-xs font-medium text-red-700 dark:text-red-400 hover:underline">{{ __('Open Attendance') }}</a>
                </div>

                @if($recentAttendance->isEmpty())
                    <div class="text-center py-12">
                        <i class="bi bi-calendar-x text-3xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No attendance entries found yet.') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentAttendance as $record)
                            @php
                                $recordDate = \Carbon\Carbon::parse($record->date);
                                $recordTone = match ($record->status) {
                                    'present' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-300',
                                    'absent' => 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-300',
                                    default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-300',
                                };
                            @endphp
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-4" data-student-search-item data-student-search-text="{{ $record->subject_name }} {{ $record->subject_code }} {{ $record->status }} {{ $recordDate->format('M d, Y') }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record->subject_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $record->subject_code }} • {{ $recordDate->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $record->time_in }} - {{ $record->time_out }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wide {{ $recordTone }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div data-student-search-empty class="hidden rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 p-10 text-center">
            <i class="bi bi-search text-3xl text-gray-300 dark:text-gray-600"></i>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('No dashboard items matched your search.') }}</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartCanvas = document.getElementById('studentStatusChart');
        const chartEmpty = document.getElementById('studentStatusChartEmpty');

        if (!chartCanvas || !window.Chart) {
            return;
        }

        const statusValues = [
            {{ $passedSubjects }},
            {{ $failedSubjects }},
            {{ $pendingSubjects }},
        ];

        if (!statusValues.some((value) => value > 0)) {
            chartCanvas.classList.add('hidden');
            chartEmpty?.classList.remove('hidden');
            return;
        }

        const isDark = document.documentElement.classList.contains('dark');

        new window.Chart(chartCanvas, {
            type: 'doughnut',
            data: {
                labels: [
                    @json(__('Passed')),
                    @json(__('Failed')),
                    @json(__('Pending')),
                ],
                datasets: [{
                    data: statusValues,
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderColor: isDark ? '#1f2937' : '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: isDark ? '#d1d5db' : '#4b5563',
                            usePointStyle: true,
                            boxWidth: 10,
                            padding: 18,
                        },
                    },
                },
            },
        });
    });
</script>
@endsection

