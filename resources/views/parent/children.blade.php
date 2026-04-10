@extends('parent.layouts.parentlayout')

@section('title', __('My Children'))
@section('subtitle', __('Simple child details for parents'))

@section('content')
<div class="parent-smooth-page space-y-6">
    @include('parent.partials.child-tabs', [
        'children' => $children,
        'selectedChildId' => $selectedChildId,
        'routeName' => 'parent.children',
    ])

    @if(!$selectedChild)
        <div class="parent-smooth-empty rounded-2xl border border-dashed border-red-300 dark:border-red-800 bg-white dark:bg-gray-800 p-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ __('No student records are currently linked to this parent account.') }}
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="parent-smooth-panel xl:col-span-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ $selectedChild['profile_photo_url'] }}" alt="{{ $selectedChild['name'] }}" class="h-24 w-24 rounded-full object-cover border border-red-200 dark:border-red-800 shadow-sm">
                    <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">{{ $selectedChild['name'] }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Roll No: :roll', ['roll' => $selectedChild['roll_no'] ?: '—']) }}</p>
                    <span class="mt-3 inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-200">
                        {{ __('Semester :semester', ['semester' => $selectedChild['semester'] ?: '—']) }}
                    </span>
                </div>

                <div class="mt-6 space-y-3 text-sm">
                    <div class="parent-smooth-list-card flex items-start justify-between gap-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Academic Year') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white text-right">{{ $selectedChild['academic_year'] ?: '—' }}</span>
                    </div>
                    <div class="parent-smooth-list-card flex items-start justify-between gap-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Department') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white text-right">{{ $selectedChild['department'] ?: '—' }}</span>
                    </div>
                    <div class="parent-smooth-list-card flex items-start justify-between gap-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white text-right break-all">{{ $selectedChild['email'] ?: '—' }}</span>
                    </div>
                    <div class="parent-smooth-list-card flex items-start justify-between gap-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Phone') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white text-right">{{ $selectedChild['phone'] ?: '—' }}</span>
                    </div>
                    <div class="parent-smooth-list-card flex items-start justify-between gap-3 rounded-xl bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('Address') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white text-right">{{ $selectedChild['address'] ?: '—' }}</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="{{ route('parent.attendance', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition">
                        <i class="bi bi-calendar-check"></i>
                        <span>{{ __('Attendance') }}</span>
                    </a>
                    <a href="{{ route('parent.results', ['child' => $selectedChild['id']]) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                        <i class="bi bi-clipboard-data"></i>
                        <span>{{ __('Results') }}</span>
                    </a>
                </div>
            </div>

            <div class="xl:col-span-8 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="parent-smooth-card rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-gray-800 p-5">
                        <p class="text-xs uppercase tracking-wide text-red-700 dark:text-red-300 font-semibold">{{ __('Attendance') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['attendance_percentage'] }}%</p>
                    </div>
                    <div class="parent-smooth-card rounded-xl border border-sky-200 dark:border-sky-900 bg-white dark:bg-gray-800 p-5">
                        <p class="text-xs uppercase tracking-wide text-sky-700 dark:text-sky-300 font-semibold">{{ __('Overall Score') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['overall_percentage'] !== null ? $selectedChild['overall_percentage'] . '%' : '—' }}</p>
                    </div>
                    <div class="parent-smooth-card rounded-xl border border-emerald-200 dark:border-emerald-900 bg-white dark:bg-gray-800 p-5">
                        <p class="text-xs uppercase tracking-wide text-emerald-700 dark:text-emerald-300 font-semibold">{{ __('Subjects') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['subject_count'] }}</p>
                    </div>
                    <div class="parent-smooth-card rounded-xl border border-violet-200 dark:border-violet-900 bg-white dark:bg-gray-800 p-5">
                        <p class="text-xs uppercase tracking-wide text-violet-700 dark:text-violet-300 font-semibold">{{ __('CGPA') }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $selectedChild['cgpa'] !== null ? number_format($selectedChild['cgpa'], 2) : '—' }}</p>
                    </div>
                </div>

                <div class="parent-smooth-table-card rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Subjects') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Attendance, teacher, and marks for each subject.') }}</p>
                        </div>
                        <a href="{{ route('parent.courses', ['child' => $selectedChild['id']]) }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Open courses') }}</a>
                    </div>

                    <div class="p-5 grid gap-4 md:grid-cols-2">
                        @foreach($selectedChild['subjects'] as $subject)
                            <div class="parent-smooth-list-card rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $subject['name'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $subject['code'] }} • {{ __('Semester :semester', ['semester' => $subject['semester'] ?: '—']) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Teacher: :name', ['name' => $subject['teacher_name']]) }}</p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $subject['status'] === 'pass' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : ($subject['status'] === 'fail' ? 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200') }}">
                                        {{ $subject['status_label'] }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                        <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Attendance') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $subject['attendance_percentage'] }}%</p>
                                    </div>
                                    <div class="rounded-lg bg-white dark:bg-slate-800 p-3 border border-gray-200 dark:border-slate-700">
                                        <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Result') }}</p>
                                        <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $subject['percentage'] !== null ? $subject['percentage'] . '%' : '—' }}</p>
                                    </div>
                                </div>

                                @if($subject['next_exam'])
                                    <div class="mt-3 rounded-lg border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20 px-3 py-2 text-sm text-red-700 dark:text-red-200">
                                        {{ __('Next exam: :date', ['date' => $subject['next_exam']['date_label']]) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="parent-smooth-panel xl:col-span-7 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Latest Attendance') }}</h2>
                    <a href="{{ route('parent.attendance', ['child' => $selectedChild['id']]) }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Open attendance') }}</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($selectedChild['recent_attendance'] as $record)
                        <div class="parent-smooth-list-card rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $record['subject_name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $record['subject_code'] ?: '—' }} • {{ $record['date_label'] }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $record['status'] === 'present' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : 'bg-red-100 text-red-700 dark:bg-red-950/30 dark:text-red-300' }}">
                                {{ ucfirst($record['status']) }}
                            </span>
                        </div>
                    @empty
                        <div class="parent-smooth-empty rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('No attendance entries are available yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="parent-smooth-panel xl:col-span-5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Teachers') }}</h2>
                    <a href="{{ route('parent.communication') }}" class="text-sm font-medium text-red-700 dark:text-red-300 hover:underline">{{ __('Communication hub') }}</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($selectedChild['teachers'] as $teacher)
                        <div class="parent-smooth-list-card rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $teacher['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $teacher['subject_name'] }}</p>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                @if($teacher['email'])
                                    <a href="mailto:{{ $teacher['email'] }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-800 px-3 py-1.5 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                        <i class="bi bi-envelope"></i>
                                        <span>{{ $teacher['email'] }}</span>
                                    </a>
                                @endif
                                @if($teacher['phone'])
                                    <a href="tel:{{ preg_replace('/\s+/', '', $teacher['phone']) }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-800 px-3 py-1.5 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-slate-700 hover:border-red-300 hover:text-red-700 transition">
                                        <i class="bi bi-telephone"></i>
                                        <span>{{ $teacher['phone'] }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="parent-smooth-empty rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Teacher contacts will appear here once subjects and assignments are available.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

