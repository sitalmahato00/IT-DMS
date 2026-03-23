{{--
    Semester Card Component
    Features:
    - Displays semester info with exam and subject counts
    - Clickable to filter exams by semester
    - Shows visual indication of active filter
    
    Usage:
    @include('admin.components.semester-card', [
        'semester' => $semester,
        'examCount' => $examCount,
        'subjectCount' => $subjectCount,
        'isActive' => $isActive,
        'onClick' => 'filterBySemester(' . $semester->number . ')'
    ])
--}}

@php
    $semester = $semester ?? null;
    $examCount = $examCount ?? 0;
    $subjectCount = $subjectCount ?? 0;
    $isActive = $isActive ?? false;
    $onClick = $onClick ?? '';
    $metrics = $metrics ?? null;

    $semesterNumber = null;
    $semesterName = null;
    $semesterAcademicYear = null;

    if (is_array($semester)) {
        $semesterNumber = $semester['number'] ?? null;
        $semesterName = $semester['name'] ?? null;
        $semesterAcademicYear = $semester['academic_year'] ?? null;
    } elseif ($semester) {
        $semesterNumber = $semester->number ?? null;
        $semesterName = method_exists($semester, 'getLocalizedNameAttribute')
            ? $semester->getLocalizedNameAttribute()
            : ($semester->name ?? null);
        $semesterAcademicYear = $semester->academic_year ?? null;
    }

    $displayName = $semesterName ?: ($semesterNumber ? ('Semester ' . $semesterNumber) : 'All Semesters');

    // Semester-specific color palette (Tailwind classes)
    $palette = [
        'all' => [
            'bg' => 'bg-slate-100 dark:bg-slate-900/50',
            'border' => 'border-slate-300 dark:border-slate-700/60',
            'hoverBg' => 'hover:bg-slate-200 dark:hover:bg-slate-800/50',
            'text' => 'text-slate-900 dark:text-slate-100',
            'iconBg' => 'bg-slate-200 dark:bg-slate-800/60',
            'iconText' => 'text-slate-700 dark:text-slate-200',
            'activeDot' => 'bg-slate-600 dark:bg-slate-300',
            'ring' => 'ring-slate-300 dark:ring-slate-600',
        ],
        1 => [
            'bg' => 'bg-blue-100 dark:bg-blue-900/30',
            'border' => 'border-blue-300 dark:border-blue-800/50',
            'hoverBg' => 'hover:bg-blue-200 dark:hover:bg-blue-900/45',
            'text' => 'text-blue-900 dark:text-blue-100',
            'iconBg' => 'bg-blue-200 dark:bg-blue-900/55',
            'iconText' => 'text-blue-700 dark:text-blue-200',
            'activeDot' => 'bg-blue-600 dark:bg-blue-300',
            'ring' => 'ring-blue-300 dark:ring-blue-700/60',
        ],
        2 => [
            'bg' => 'bg-emerald-100 dark:bg-emerald-900/25',
            'border' => 'border-emerald-300 dark:border-emerald-800/50',
            'hoverBg' => 'hover:bg-emerald-200 dark:hover:bg-emerald-900/40',
            'text' => 'text-emerald-900 dark:text-emerald-100',
            'iconBg' => 'bg-emerald-200 dark:bg-emerald-900/55',
            'iconText' => 'text-emerald-700 dark:text-emerald-200',
            'activeDot' => 'bg-emerald-600 dark:bg-emerald-300',
            'ring' => 'ring-emerald-300 dark:ring-emerald-700/60',
        ],
        3 => [
            'bg' => 'bg-violet-100 dark:bg-violet-900/25',
            'border' => 'border-violet-300 dark:border-violet-800/50',
            'hoverBg' => 'hover:bg-violet-200 dark:hover:bg-violet-900/40',
            'text' => 'text-violet-900 dark:text-violet-100',
            'iconBg' => 'bg-violet-200 dark:bg-violet-900/55',
            'iconText' => 'text-violet-700 dark:text-violet-200',
            'activeDot' => 'bg-violet-600 dark:bg-violet-300',
            'ring' => 'ring-violet-300 dark:ring-violet-700/60',
        ],
        4 => [
            'bg' => 'bg-amber-100 dark:bg-amber-900/20',
            'border' => 'border-amber-300 dark:border-amber-800/50',
            'hoverBg' => 'hover:bg-amber-200 dark:hover:bg-amber-900/35',
            'text' => 'text-amber-900 dark:text-amber-100',
            'iconBg' => 'bg-amber-200 dark:bg-amber-900/50',
            'iconText' => 'text-amber-700 dark:text-amber-200',
            'activeDot' => 'bg-amber-600 dark:bg-amber-300',
            'ring' => 'ring-amber-300 dark:ring-amber-700/60',
        ],
        5 => [
            'bg' => 'bg-rose-100 dark:bg-rose-900/20',
            'border' => 'border-rose-300 dark:border-rose-800/50',
            'hoverBg' => 'hover:bg-rose-200 dark:hover:bg-rose-900/35',
            'text' => 'text-rose-900 dark:text-rose-100',
            'iconBg' => 'bg-rose-200 dark:bg-rose-900/50',
            'iconText' => 'text-rose-700 dark:text-rose-200',
            'activeDot' => 'bg-rose-600 dark:bg-rose-300',
            'ring' => 'ring-rose-300 dark:ring-rose-700/60',
        ],
        6 => [
            'bg' => 'bg-cyan-100 dark:bg-cyan-900/20',
            'border' => 'border-cyan-300 dark:border-cyan-800/50',
            'hoverBg' => 'hover:bg-cyan-200 dark:hover:bg-cyan-900/35',
            'text' => 'text-cyan-900 dark:text-cyan-100',
            'iconBg' => 'bg-cyan-200 dark:bg-cyan-900/50',
            'iconText' => 'text-cyan-700 dark:text-cyan-200',
            'activeDot' => 'bg-cyan-600 dark:bg-cyan-300',
            'ring' => 'ring-cyan-300 dark:ring-cyan-700/60',
        ],
    ];

    $tone = $semesterNumber ? ($palette[$semesterNumber] ?? $palette['all']) : $palette['all'];

    $bgColor = $tone['bg'];
    $borderColor = $tone['border'];
    $hoverBgColor = $tone['hoverBg'];
    $textColor = $tone['text'];
    $iconBgColor = $tone['iconBg'];
    $iconTextColor = $tone['iconText'];
    $activeDotColor = $tone['activeDot'];
    $activeRing = $tone['ring'];

    $activeClasses = $isActive ? "ring-2 ring-offset-2 ring-offset-white dark:ring-offset-slate-900 {$activeRing}" : '';

    $metricsList = [];
    if (is_array($metrics) && count($metrics) > 0) {
        foreach ($metrics as $m) {
            if (!is_array($m)) continue;
            $metricsList[] = [
                'icon' => $m['icon'] ?? 'bi bi-info-circle',
                'label' => $m['label'] ?? '',
                'value' => $m['value'] ?? 0,
            ];
        }
    }

    if (count($metricsList) === 0) {
        $metricsList = [
            ['icon' => 'bi bi-clipboard-check', 'label' => 'Exams', 'value' => $examCount],
            ['icon' => 'bi bi-book', 'label' => 'Subjects', 'value' => $subjectCount],
        ];
    }
@endphp

<div class="{{ $bgColor }} rounded-xl shadow-sm border {{ $borderColor }} p-6 cursor-pointer transition-all duration-200 {{ $hoverBgColor }} hover:shadow-md {{ $activeClasses }}"
     onclick="{{ $onClick }}"
     title="Click to view exams and subjects for {{ $displayName }}">
    
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $iconBgColor }} {{ $iconTextColor }} flex items-center justify-center font-bold text-lg flex-shrink-0">
                {{ $semesterNumber ?? 'All' }}
            </div>
            <div>
                <h3 class="font-semibold {{ $textColor }}">{{ $displayName }}</h3>
                @if($semesterAcademicYear)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $semesterAcademicYear }}</p>
                @endif
            </div>
        </div>
        
        @if($isActive)
            <div class="w-3 h-3 rounded-full {{ $activeDotColor }}"></div>
        @endif
    </div>
    
    <div class="space-y-3 text-center">
        @foreach($metricsList as $metric)
            <div class="flex justify-center items-center gap-2">
                <i class="{{ $metric['icon'] }} text-xl {{ $iconTextColor }}"></i>
                <span class="font-medium {{ $iconTextColor }}">{{ $metric['value'] }} {{ $metric['label'] }}</span>
            </div>
        @endforeach
    </div>
    
    @if(!$isActive)
        <div class="mt-4 pt-3 border-t {{ $borderColor }} text-sm {{ $iconTextColor }}">
            Click to filter
        </div>
    @endif
</div>
