{{--
    Teacher Filter Card Component
    Features:
    - 3-4 column responsive grid
    - Standard filters: Semester, Subject, Status, Search
    - Primary red Apply Filter button
    - Neutral Reset Filter button
    
    Usage:
    @include('teacher.components.teacher-filter-card', [
        'formAction' => route('teacher.students'),
        'filters' => [
            ['name' => 'search', 'type' => 'text', 'placeholder' => 'Search...', 'value' => request('q')],
            ['name' => 'semester', 'type' => 'select', 'options' => $semesters, 'placeholder' => 'All Semesters'],
            ['name' => 'subject_id', 'type' => 'select', 'options' => $subjects, 'placeholder' => 'All Subjects'],
            ['name' => 'status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'showReset' => true,
        'resetRoute' => route('teacher.students')
    ])
--}}

@php
    $formAction = $formAction ?? '#';
    $filters = $filters ?? [];
    $showReset = $showReset ?? true;
    $resetRoute = $resetRoute ?? '#';
    $hideFilterButton = $hideFilterButton ?? false;
@endphp

<div class="teacher-filter-panel bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 mb-6">
    <form action="{{ $formAction }}" method="GET" id="filterForm" class="space-y-4">
        {{-- Filter Fields Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($filters as $filter)
                @switch($filter['type'])
                    @case('select')
                        <div class="space-y-1">
                            @if(!empty($filter['label']))
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $filter['label'] }}
                            </label>
                            @endif
                            <select 
                                id="filter{{ str_replace('_', '', ucwords($filter['name'], '_')) }}"
                                name="{{ $filter['name'] }}" 
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors {{ isset($filter['disabled']) && $filter['disabled'] ? 'opacity-50 cursor-not-allowed' : '' }} {{ $filter['class'] ?? '' }}"
                                {{ isset($filter['onchange']) ? 'onchange="' . $filter['onchange'] . '"' : '' }}
                                {{ isset($filter['disabled']) && $filter['disabled'] ? 'disabled' : '' }}
                            >
                                <option value="">{{ $filter['placeholder'] ?? 'Select...' }}</option>
                                @if(is_array($filter['options']))
                                    @foreach($filter['options'] as $key => $value)
                                        <option value="{{ $key }}" {{ ($filter['value'] ?? request($filter['name'])) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                @elseif($filter['options'] instanceof \Illuminate\Support\Collection)
                                    @foreach($filter['options'] as $option)
                                        <option value="{{ $option->id }}" {{ ($filter['value'] ?? request($filter['name'])) == $option->id ? 'selected' : '' }}>
                                            {{ $option->name ?? $option->title ?? $option->value ?? $option->id }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @break
                    
                    @case('text')
                        <div class="space-y-1">
                            @if(!empty($filter['label']))
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $filter['label'] }}
                            </label>
                            @endif
                            <div class="relative">
                                @if(isset($filter['icon']))
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi {{ $filter['icon'] }} text-gray-400"></i>
                                </div>
                                @endif
                                <input 
                                    type="text"
                                    name="{{ $filter['name'] }}"
                                    value="{{ $filter['value'] ?? request($filter['name']) }}"
                                    placeholder="{{ $filter['placeholder'] ?? '' }}"
                                    @if(isset($filter['autocomplete'])) autocomplete="{{ $filter['autocomplete'] }}" @endif
                                    class="w-full {{ isset($filter['icon']) ? 'pl-10' : '' }} px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors {{ $filter['class'] ?? '' }}"
                                >
                            </div>
                        </div>
                        @break
                    
                    @case('date')
                        <div class="space-y-1">
                            @if(!empty($filter['label']))
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $filter['label'] }}
                            </label>
                            @endif
                            <input 
                                type="date"
                                name="{{ $filter['name'] }}"
                                value="{{ $filter['value'] ?? request($filter['name']) }}"
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors {{ $filter['class'] ?? '' }}"
                            >
                        </div>
                        @break
                    
                    @case('dateRange')
                        <div class="space-y-1">
                            @if(!empty($filter['label']))
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $filter['label'] }}
                            </label>
                            @endif
                            <div class="flex gap-2">
                                <input 
                                    type="date"
                                    name="{{ $filter['name'] }}_from"
                                    value="{{ $filter['value_from'] ?? request($filter['name'] . '_from') }}"
                                    placeholder="From"
                                    class="flex-1 px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                                >
                                <input 
                                    type="date"
                                    name="{{ $filter['name'] }}_to"
                                    value="{{ $filter['value_to'] ?? request($filter['name'] . '_to') }}"
                                    placeholder="To"
                                    class="flex-1 px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors"
                                >
                            </div>
                        </div>
                        @break
                @endswitch
            @endforeach
        </div>

        {{-- Action Buttons --}}
        @if(!$hideFilterButton)
        <div class="flex flex-wrap items-center gap-2 pt-2">
            <button 
                type="submit" 
                class="teacher-page-primary-btn inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition-colors shadow-sm">
                <i class="bi bi-funnel"></i>
                {{ __('Apply Filters') }}
            </button>
            
            @if($showReset)
                @if($resetRoute !== '#')
                    <a 
                        href="{{ $resetRoute }}" 
                        class="teacher-page-secondary-btn inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-500 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        {{ __('Reset') }}
                    </a>
                @else
                    <button 
                        type="button" 
                        onclick="document.getElementById('filterForm').reset(); document.getElementById('filterForm').submit();"
                        class="teacher-page-secondary-btn inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-700/50 border border-slate-300 dark:border-slate-500 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                        {{ __('Reset') }}
                    </button>
                @endif
            @endif
        </div>
        @endif
    </form>
</div>
