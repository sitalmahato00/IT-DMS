<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-6 xl:grid-cols-[1.6fr,1fr] xl:items-start">
            <div>
                <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500">
                    <span class="rounded-full bg-rose-50 px-3 py-1 text-rose-600">{{ __('Dashboard Center') }}</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">{{ now()->format('D, M d, Y') }}</span>
                    @if(isset($college) && $college->name)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">{{ $college->name }}</span>
                    @endif
                </div>
                <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">{{ __('Welcome back,') }} {{ $adminName }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ __('Monitor attendance, grading, classes, notices, and student momentum from one place with a cleaner live dashboard.') }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700">
                        <i class="bi bi-calendar2-week"></i>
                        {{ $dashboardOverview['today_class_count'] ?? 0 }} {{ __('classes today') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700">
                        <i class="bi bi-pie-chart"></i>
                        {{ number_format($passRate, 1) }}% {{ __('pass rate') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-medium text-amber-700">
                        <i class="bi bi-bell"></i>
                        {{ $dashboardOverview['unread_notifications'] ?? 0 }} {{ __('unread alerts') }}
                    </span>
                </div>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">{{ __('Live Classes') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-emerald-900">{{ $dashboardOverview['today_class_count'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-emerald-700">{{ $healthyClassCount }} {{ __('on track today') }}</p>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">{{ __('Attention Queue') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-rose-900">{{ $attentionFlags }}</p>
                    <p class="mt-1 text-xs text-rose-700">{{ __('attendance, grades, and approvals to review') }}</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">{{ __('Grade Health') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-blue-900">{{ number_format($distinctionRate, 1) }}%</p>
                    <p class="mt-1 text-xs text-blue-700">{{ __('students in A and A+') }}</p>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-amber-700">{{ __('Upcoming Exams') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-amber-900">{{ $dashboardOverview['upcoming_exam_count'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-amber-700">{{ __('scheduled assessments ahead') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('Students') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format($totalStudents ?? 0) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $teachers > 0 ? round($totalStudents / $teachers, 1) . ' ' . __('per teacher') : __('No faculty assigned yet') }}</p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"><i class="bi bi-people-fill text-lg"></i></span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('Teachers') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format($teachers ?? 0) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $activeSemesters ?? 0 }} {{ __('active semesters supported') }}</p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-50 text-violet-600"><i class="bi bi-person-badge-fill text-lg"></i></span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('Courses') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format($courses ?? 0) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ number_format($electiveStudents ?? 0) }} {{ __('students in electives') }}</p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-50 text-teal-600"><i class="bi bi-journal-richtext text-lg"></i></span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('Attendance Avg') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format((float) ($avgAttendance ?? 0), 1) }}%</p>
                    <p class="mt-1 text-xs text-slate-500">{{ number_format($attendanceSummary['present'] ?? 0) }} {{ __('present records tracked') }}</p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="bi bi-activity text-lg"></i></span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('Exam Queue') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $dashboardOverview['upcoming_exam_count'] ?? 0 }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('scheduled exams waiting') }}</p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><i class="bi bi-calendar2-event text-lg"></i></span>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ __('Alert Queue') }}</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $attentionFlags }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('items needing follow-up') }}</p>
                </div>
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"><i class="bi bi-exclamation-circle text-lg"></i></span>
            </div>
        </div>
    </section>
    <section class="grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"><i class="bi bi-graph-up-arrow text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Attendance Overview') }}</h2>
                        <p id="attendanceTrendCaption" class="text-sm text-slate-500">{{ __('Rolling classroom attendance for the selected period') }}</p>
                    </div>
                </div>
                <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-50 p-1">
                    <span class="px-3 text-xs font-semibold text-slate-500">{{ __('Period') }}</span>
                    <select id="attendancePeriod" class="rounded-xl border-0 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-emerald-200">
                        <option value="week">{{ __('Weekly') }}</option>
                        <option value="month">{{ __('Monthly') }}</option>
                        <option value="semester">{{ __('Semester') }}</option>
                    </select>
                </div>
            </div>

            <div class="relative mt-5 h-80">
                <canvas id="attendanceTrendChart"></canvas>
                <p id="attendanceTrendNoData" class="hidden pt-28 text-center text-sm text-slate-400">{{ __('No attendance data available for this period.') }}</p>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-4">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">{{ __('Current Avg') }}</p>
                    <p id="attendanceCurrentAverage" class="mt-3 text-2xl font-semibold text-emerald-900">{{ number_format($initialAttendanceAverage, 1) }}%</p>
                    <p id="attendanceCurrentRange" class="mt-1 text-xs text-emerald-700">{{ $trackedBucketCount }} {{ __('tracked periods') }}</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">{{ __('Best Period') }}</p>
                    <p id="attendanceBestLabel" class="mt-3 text-lg font-semibold text-blue-900">{{ $initialAttendanceBest['label'] ?? __('No records') }}</p>
                    <p id="attendanceBestValue" class="mt-1 text-xs text-blue-700">{{ isset($initialAttendanceBest['percentage']) ? number_format((float) $initialAttendanceBest['percentage'], 1) . '%' . ' ' . __('attendance') : __('Waiting for data') }}</p>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">{{ __('Watchlist') }}</p>
                    <p id="attendanceLowestLabel" class="mt-3 text-lg font-semibold text-rose-900">{{ $initialAttendanceLowest['label'] ?? __('No records') }}</p>
                    <p id="attendanceLowestValue" class="mt-1 text-xs text-rose-700">{{ isset($initialAttendanceLowest['percentage']) ? number_format((float) $initialAttendanceLowest['percentage'], 1) . '%' . ' ' . __('attendance') : __('Waiting for data') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">{{ __('Coverage') }}</p>
                    <p id="attendanceCoverageValue" class="mt-3 text-lg font-semibold text-slate-900">{{ number_format($initialAttendancePresent) }} / {{ number_format($initialAttendanceTracked) }}</p>
                    <p id="attendanceCoverageNote" class="mt-1 text-xs text-slate-500">{{ number_format($initialAttendanceNotPresent) }} {{ __('not present in this range') }}</p>
                </div>
            </div>
        </div>

        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-fuchsia-50 text-fuchsia-600"><i class="bi bi-pie-chart-fill text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Grade Distribution') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Performance mix across current graded records') }}</p>
                    </div>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $totalGradedRecords }} {{ __('graded') }}</span>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-3">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-3.5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">{{ __('Pass Rate') }}</p>
                    <p class="mt-2 text-xl font-semibold text-emerald-900">{{ number_format($passRate, 1) }}%</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-3.5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-700">{{ __('A Range') }}</p>
                    <p class="mt-2 text-xl font-semibold text-blue-900">{{ number_format($distinctionRate, 1) }}%</p>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-rose-50 p-3.5">
                    <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">{{ __('Need Support') }}</p>
                    <p class="mt-2 text-xl font-semibold text-rose-900">{{ number_format($needsAttentionRate, 1) }}%</p>
                </div>
            </div>

            <div class="relative mt-5 h-72">
                <canvas id="gradeDonutChart"></canvas>
                <p id="gradeDonutNoData" class="hidden pt-24 text-center text-sm text-slate-400">{{ __('No grades available yet.') }}</p>
            </div>

            <div class="mt-5 space-y-2.5">
                @foreach($gradeStats as $stat)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl text-sm font-semibold text-white" style="background-color: {{ $stat['color'] }}">{{ $stat['grade'] }}</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $stat['count'] }} {{ __('students') }}</p>
                                <p class="text-xs text-slate-500">{{ number_format($stat['percentage'], 1) }}% {{ __('of graded records') }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold" style="color: {{ $stat['color'] }}">{{ $stat['grade'] === $topGrade ? __('Top band') : __('Active') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-yellow-50 text-yellow-600"><i class="bi bi-megaphone-fill text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Notice Board') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Latest announcements and campus updates') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.notice-board') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">{{ __('View all') }}</a>
            </div>

            @forelse($noticeFeed as $notice)
                <div class="mb-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 last:mb-0">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2.5 w-2.5 flex-shrink-0 rounded-full bg-yellow-400"></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ $notice['title'] }}</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">{{ \Illuminate\Support\Str::limit($notice['message'], 110) }}</p>
                            <p class="mt-2 text-[11px] font-medium text-slate-400">{{ \Carbon\Carbon::parse($notice['created_at'])->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex min-h-[240px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No notices available right now.') }}</div>
            @endforelse
        </div>

        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"><i class="bi bi-calendar-event text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Upcoming Events') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Closest exams and scheduled academic events') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.exam') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">{{ __('Open exams') }}</a>
            </div>

            @forelse($upcomingExamsCollection as $exam)
                @php($examDate = \Carbon\Carbon::parse($exam['exam_date']))
                <div class="mb-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 last:mb-0">
                    <div class="flex h-14 w-14 flex-shrink-0 flex-col items-center justify-center rounded-2xl bg-gradient-to-br from-rose-500 to-orange-400 text-white">
                        <span class="text-[10px] font-semibold uppercase tracking-widest">{{ $examDate->format('M') }}</span>
                        <span class="text-xl font-semibold">{{ $examDate->format('d') }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900">{{ $exam['name'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $exam['subject_name'] }}</p>
                        <p class="mt-2 text-[11px] font-medium text-slate-400">{{ $examDate->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="flex min-h-[240px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No upcoming exams scheduled.') }}</div>
            @endforelse
        </div>

        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-600"><i class="bi bi-calendar-day text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __("Today's Classes") }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Class attendance split and strongest live sessions') }}</p>
                    </div>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">{{ number_format($todayAttendanceRate, 1) }}% {{ __('engaged') }}</span>
            </div>

            <div class="grid gap-4 lg:grid-cols-[200px,1fr]">
                <div>
                    <div class="relative mx-auto h-52 w-full max-w-[220px]">
                        <canvas id="classStatusChart"></canvas>
                        <p id="classStatusNoData" class="hidden pt-20 text-center text-sm text-slate-400">{{ __('No class attendance recorded today.') }}</p>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                        @foreach($classStatusData as $status)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-2 py-2">
                                <p class="text-sm font-semibold" style="color: {{ $status['color'] }}">{{ number_format($status['value']) }}</p>
                                <p class="mt-1 text-[11px] text-slate-500">{{ __($status['label']) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($featuredClasses as $class)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $class['subject_name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ __('Semester') }} {{ $class['semester'] }}</p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $class['attendance_rate'] >= 75 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ number_format((float) $class['attendance_rate'], 1) }}%</span>
                            </div>
                            <div class="mt-3 grid grid-cols-4 gap-2 text-center text-[11px]">
                                <div class="rounded-xl bg-white px-2 py-2"><p class="font-semibold text-emerald-600">{{ $class['present_count'] }}</p><p class="mt-1 text-slate-400">{{ __('Present') }}</p></div>
                                <div class="rounded-xl bg-white px-2 py-2"><p class="font-semibold text-rose-600">{{ $class['absent_count'] }}</p><p class="mt-1 text-slate-400">{{ __('Absent') }}</p></div>
                                <div class="rounded-xl bg-white px-2 py-2"><p class="font-semibold text-amber-600">{{ $class['late_count'] ?? 0 }}</p><p class="mt-1 text-slate-400">{{ __('Late') }}</p></div>
                                <div class="rounded-xl bg-white px-2 py-2"><p class="font-semibold text-sky-600">{{ $class['total_students'] }}</p><p class="mt-1 text-slate-400">{{ __('Total') }}</p></div>
                            </div>
                        </div>
                    @empty
                        <div class="flex min-h-[240px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No classes recorded today.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
    <section class="grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600"><i class="bi bi-bar-chart-line-fill text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Activity Overview') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Current operational load across core areas') }}</p>
                    </div>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">{{ $dashboardOverview['recent_activity_count'] ?? 0 }} {{ __('recent updates') }}</span>
            </div>
            <div class="relative h-72"><canvas id="activityOverviewChart"></canvas></div>
        </div>

        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><i class="bi bi-trophy-fill text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Top Performing Students') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Highest average percentages among active students') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.students') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">{{ __('Students') }}</a>
            </div>

            <div class="space-y-3">
                @forelse($topPerformersCollection as $student)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-sm font-semibold text-white">{{ $student['initials'] }}</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $student['name'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ __('Semester') }} {{ $student['semester'] }} . {{ __('Roll') }} {{ $student['roll_no'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">{{ number_format((float) $student['average_percentage'], 1) }}%</span>
                            <p class="mt-2 text-[11px] text-slate-400">{{ $student['grade'] }} . {{ $student['graded_count'] }} {{ __('records') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex min-h-[240px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No graded students available yet.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-sky-50 text-sky-600"><i class="bi bi-journal-check text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Exam Overview') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('What is coming next in the assessment queue') }}</p>
                    </div>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">{{ $upcomingExamsCollection->count() }} {{ __('queued') }}</span>
            </div>

            <div class="space-y-3">
                @forelse($upcomingExamsCollection as $exam)
                    @php($examDate = \Carbon\Carbon::parse($exam['exam_date']))
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $exam['name'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $exam['subject_name'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-white px-3 py-2 text-right">
                                <p class="text-xs font-semibold text-slate-900">{{ $examDate->format('M d') }}</p>
                                <p class="mt-1 text-[11px] text-slate-400">{{ $examDate->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex min-h-[240px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No exam items to show.') }}</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-lime-50 text-lime-600"><i class="bi bi-person-plus-fill text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Recent Student Activity') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Newest student registrations and profile additions') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.students') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">{{ __('Open students') }}</a>
            </div>

            <div class="space-y-3">
                @forelse($studentFeed as $student)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-lime-400 to-emerald-500 text-sm font-semibold text-white">{{ strtoupper(substr($student['name'], 0, 1)) }}</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $student['name'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $student['email'] }}</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1.5 text-xs font-medium text-slate-500">{{ \Carbon\Carbon::parse($student['created_at'])->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="flex min-h-[220px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No recent student activity to show.') }}</div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-50 text-violet-600"><i class="bi bi-clock-history text-lg"></i></span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('Recent Activity Log') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('Latest administrative actions captured in the system') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">{{ __('Audit log') }}</a>
            </div>

            <div class="space-y-3">
                @forelse($activityFeed->take(6) as $activity)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2.5 w-2.5 flex-shrink-0 rounded-full bg-violet-500"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $activity['action'] }}</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-500">{{ \Illuminate\Support\Str::limit($activity['details'] ?: $activity['user_name'], 130) }}</p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1.5 text-[11px] font-medium text-slate-500">{{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex min-h-[220px] items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-400">{{ __('No recent admin activity found.') }}</div>
                @endforelse
            </div>
        </div>
    </section>
</div>

