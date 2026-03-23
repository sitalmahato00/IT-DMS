{{--
    Teacher Page Header Component
    Features:
    - Left: Page title + breadcrumb
    - Right: Primary red Add button + optional Import/Export buttons
    
    Usage:
    @include('teacher.components.teacher-page-header', [
        'title' => 'My Subjects',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('teacher.dashboard')],
            ['label' => 'My Subjects']
        ],
        'addButton' => [
            'label' => 'Add Subject',
            'onclick' => "openModal()"
        ],
        'importRoute' => route('teacher.subjects.import'),
        'exportRoute' => route('teacher.subjects.export')
    ])
--}}

@php
    $breadcrumbs = $breadcrumbs ?? [];
    $addButton = $addButton ?? null;
    $importRoute = $importRoute ?? null;
    $exportRoute = $exportRoute ?? null;
    $rightContent = $rightContent ?? null;
    $buttonColor = $addButton['color'] ?? 'red';
    
    $colorClasses = [
        'red' => 'bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 focus:ring-red-500 dark:focus:ring-red-400',
        'green' => 'bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-600 focus:ring-green-500 dark:focus:ring-green-400',
        'blue' => 'bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400',
    ];
    $buttonClass = $colorClasses[$buttonColor] ?? $colorClasses['red'];
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    {{-- Left: Page Title & Breadcrumb --}}
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
            {{ $title }}
        </h1>
        @if(!empty($breadcrumbs))
        <nav class="flex items-center gap-2 text-sm mt-1">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
                @endif
                @if(isset($crumb['url']) && !$loop->last)
                    <a href="{{ $crumb['url'] }}" class="text-gray-500 dark:text-gray-400 hover:text-[#FF0037] dark:hover:text-red-400 transition-colors">
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                        {{ $crumb['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>
        @endif
    </div>

    {{-- Right: Action Buttons --}}
    <div class="flex flex-wrap items-center gap-2">
        {{-- Custom Right Content --}}
        @if($rightContent)
            {{ $rightContent }}
        @endif

        {{-- Import Button --}}
        @if($importRoute)
            <a href="{{ $importRoute }}" class="inline-flex items-center gap-2 px-4 py-2 border border-blue-300 dark:border-blue-600 bg-white dark:bg-blue-900/30 hover:bg-blue-50 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition-colors shadow-sm">
                <i class="bi bi-upload"></i>
                <span class="hidden sm:inline">{{ __('Import') }}</span>
            </a>
        @endif

        {{-- Export Button --}}
        @if($exportRoute)
            <a href="{{ $exportRoute }}" class="inline-flex items-center gap-2 px-4 py-2 border border-green-300 dark:border-green-600 bg-white dark:bg-green-900/30 hover:bg-green-50 dark:hover:bg-green-900/50 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium transition-colors shadow-sm">
                <i class="bi bi-download"></i>
                <span class="hidden sm:inline">{{ __('Export') }}</span>
            </a>
        @endif

        {{-- Add Button --}}
        @if($addButton)
            <button 
                type="button"
                @if(isset($addButton['onclick']))
                    onclick="{{ $addButton['onclick'] }}"
                @elseif(isset($addButton['route']))
                    onclick="window.location.href='{{ $addButton['route'] }}'"
                @endif
                class="inline-flex items-center gap-2 px-4 py-2 {{ $buttonClass }} text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors shadow-sm"
            >
                <i class="bi bi-plus-lg"></i>
                <span>{{ $addButton['label'] }}</span>
            </button>
        @endif
    </div>
</div>
