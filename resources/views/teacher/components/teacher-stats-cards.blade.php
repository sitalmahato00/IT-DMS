{{--
    Teacher Statistics Cards Component
    Features:
    - 4-column grid showing metrics
    - Red accent colors with icons
    
    Usage:
    @include('teacher.components.teacher-stats-cards', [
        'cards' => [
            ['title' => 'My Subjects', 'value' => $subjectCount, 'icon' => 'bi-book', 'color' => 'red'],
            ['title' => 'Total Students', 'value' => $totalStudents, 'icon' => 'bi-people', 'color' => 'blue'],
            ['title' => 'Attendance Rate', 'value' => $avgAttendance . '%', 'icon' => 'bi-calendar-check', 'color' => 'green'],
            ['title' => 'Notices', 'value' => $noticeCount, 'icon' => 'bi-megaphone', 'color' => 'purple'],
        ]
    ])
--}}

@php
    $cards = $cards ?? [];
@endphp

@if(!empty($cards))
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($cards as $index => $card)
        @php
            $color = $card['color'] ?? 'red';
            $icon = $card['icon'] ?? 'bi-card-text';
            
            $colorClasses = match($color) {
                'red' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                'green' => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400',
                'blue' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
                'orange' => 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400',
                'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400',
                'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400',
                default => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
            };
            
            $iconBgClasses = match($color) {
                'red' => 'bg-red-100 dark:bg-red-900/40',
                'green' => 'bg-green-100 dark:bg-green-900/40',
                'blue' => 'bg-blue-100 dark:bg-blue-900/40',
                'purple' => 'bg-purple-100 dark:bg-purple-900/40',
                'orange' => 'bg-orange-100 dark:bg-orange-900/40',
                'yellow' => 'bg-yellow-100 dark:bg-yellow-900/40',
                'indigo' => 'bg-indigo-100 dark:bg-indigo-900/40',
                default => 'bg-red-100 dark:bg-red-900/40',
            };
        @endphp
        
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $colorClasses }} mb-1">
                        {{ $card['title'] }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                        {{ $card['value'] ?? '—' }}
                    </p>
                    @if(isset($card['subtitle']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $card['subtitle'] }}
                        </p>
                    @endif
                </div>
                <div class="flex-shrink-0 ml-3">
                    <div class="w-10 h-10 rounded-lg {{ $iconBgClasses }} flex items-center justify-center">
                        <i class="bi {{ $icon }} text-lg {{ $colorClasses }}"></i>
                    </div>
                </div>
            </div>
            
            @if(isset($card['trend']))
                <div class="mt-3 flex items-center gap-1">
                    @if($card['trend'] > 0)
                        <i class="bi bi-arrow-up text-green-500 text-xs"></i>
                        <span class="text-xs text-green-600 dark:text-green-400">
                            +{{ $card['trend'] }}%
                        </span>
                    @elseif($card['trend'] < 0)
                        <i class="bi bi-arrow-down text-red-500 text-xs"></i>
                        <span class="text-xs text-red-600 dark:text-red-400">
                            {{ $card['trend'] }}%
                        </span>
                    @endif
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $card['trendLabel'] ?? 'vs last month' }}
                    </span>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endif
