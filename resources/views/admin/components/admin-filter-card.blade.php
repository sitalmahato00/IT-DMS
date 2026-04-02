{{--
    Admin Filter Card Component
    Features:
    - 3-4 column responsive grid
    - Standard filters: Program, Semester, Status, Search
    - Primary red Apply Filter button
    - Neutral Reset Filter button
    
    Usage:
    @include('admin.components.admin-filter-card', [
        'formAction' => route('admin.students'),
        'filters' => [
            ['name' => 'search', 'type' => 'text', 'placeholder' => 'Search...', 'value' => request('q')],
            ['name' => 'semester', 'type' => 'select', 'options' => $semesters, 'placeholder' => 'All Semesters'],
            ['name' => 'status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
        ],
        'showReset' => true,
        'resetRoute' => route('admin.students')
    ])
--}}

@php
    $formAction = $formAction ?? '#';
    $filters = $filters ?? [];
    $showReset = $showReset ?? true;
    $resetRoute = $resetRoute ?? '#';
    $hideFilterButton = $hideFilterButton ?? false;
@endphp

<div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-5">
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
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors {{ isset($filter['disabled']) && $filter['disabled'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ isset($filter['onchange']) ? 'onchange="' . $filter['onchange'] . '"' : '' }}
                                {{ isset($filter['disabled']) && $filter['disabled'] ? 'disabled' : '' }}
                            >
                                <option value="">{{ $filter['placeholder'] ?? 'Select...' }}</option>
                                @if(is_array($filter['options']))
                                    @foreach($filter['options'] as $key => $value)
                                        <option value="{{ $key }}" {{ isset($filter['value']) && $filter['value'] == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                @else
                                    @foreach($filter['options'] as $key => $option)
                                        @if(is_string($option))
                                            <option value="{{ $key }}" {{ isset($filter['value']) && $filter['value'] == $key ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @elseif(is_object($option))
                                            <option value="{{ $option->id }}" {{ isset($filter['value']) && $filter['value'] == $option->id ? 'selected' : '' }}>
                                                {{ $option->name ?? $option->title ?? $option }}
                                            </option>
                                        @else
                                            <option value="{{ $option }}" {{ isset($filter['value']) && $filter['value'] == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
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
                                id="filter{{ str_replace('_', '', ucwords($filter['name'], '_')) }}"
                                name="{{ $filter['name'] }}" 
                                value="{{ $filter['value'] ?? '' }}"
                                class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors {{ $filter['class'] ?? '' }}"
                            >
                        </div>
                        @break

                    @case('text')
                    @default
                        <div class="space-y-1">
                            @if(!empty($filter['label']))
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $filter['label'] }}
                            </label>
                            @endif
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="filter{{ str_replace('_', '', ucwords($filter['name'], '_')) }}"
                                    name="{{ $filter['name'] }}" 
                                    value="{{ $filter['value'] ?? '' }}"
                                    placeholder="{{ $filter['placeholder'] ?? '' }}"
                                    @if(isset($filter['autocomplete'])) autocomplete="{{ $filter['autocomplete'] }}" @endif
                                    class="w-full {{ isset($filter['icon']) ? 'pl-10' : '' }} pr-3 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors {{ $filter['class'] ?? '' }}"
                                >
                                @if(isset($filter['icon']))
                                    <i class="bi {{ $filter['icon'] }} absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                @else
                                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                @endif
                            </div>
                        </div>
                @endswitch
            @endforeach
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col gap-3 border-t border-gray-100 pt-2 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row">
                {{-- Apply Filter Button (Primary Red) --}}
                @if(!$hideFilterButton)
                <button 
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 sm:w-auto"
                >
                    <i class="bi bi-funnel"></i>
                    <span>Apply Filter</span>
                </button>
                @endif

                {{-- Reset Filter Button (Neutral) --}}
                @if($showReset)
                <a 
                    href="{{ $resetRoute }}"
                    class="inline-flex w-full items-center justify-center gap-2 px-5 py-2.5 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition shadow-sm sm:w-auto"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Reset Filter</span>
                </a>
                @endif
            </div>

            {{-- Slot for additional buttons --}}
            @if(!empty($slot))
                {{ $slot }}
            @endif
        </div>
    </form>
</div>
