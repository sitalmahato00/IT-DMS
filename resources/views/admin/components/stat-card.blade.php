<!-- Stat Card Component - Professional Design -->
<!-- Usage: @include('admin.components.stat-card', ['title' => 'Total Students', 'value' => 1247, 'icon' => 'bi-mortarboard', 'color' => 'red', 'trend' => '+12%', 'trendDirection' => 'up', 'variant' => 'light']) -->
<!-- Variants: 'light' (default) = light bg with colored border, 'solid' = solid colored bg with white text -->
@props([
    'title' => '',
    'value' => 0,
    'icon' => 'bi-square',
    'color' => 'red',
    'trend' => null,
    'trendDirection' => 'up',
    'variant' => 'light'
])

@php
    $colorClasses = [
        'red' => [
            'light' => ['border' => 'border-l-red-600', 'bg' => 'bg-gradient-to-br from-red-50 to-white hover:shadow-md', 'icon_bg' => 'bg-red-100', 'icon_text' => 'text-blue-600'],
            'solid' => ['border' => 'border-0', 'bg' => 'bg-blue-600 hover:bg-blue-700', 'icon_bg' => 'bg-red-700', 'icon_text' => 'text-white', 'text' => 'text-white']
        ],
        'blue' => [
            'light' => ['border' => 'border-l-blue-600', 'bg' => 'bg-gradient-to-br from-blue-50 to-white hover:shadow-md', 'icon_bg' => 'bg-blue-100', 'icon_text' => 'text-blue-600'],
            'solid' => ['border' => 'border-0', 'bg' => 'bg-blue-600 hover:bg-blue-700', 'icon_bg' => 'bg-blue-700', 'icon_text' => 'text-white', 'text' => 'text-white']
        ],
        'green' => [
            'light' => ['border' => 'border-l-green-600', 'bg' => 'bg-gradient-to-br from-green-50 to-white hover:shadow-md', 'icon_bg' => 'bg-green-100', 'icon_text' => 'text-green-600'],
            'solid' => ['border' => 'border-0', 'bg' => 'bg-green-600 hover:bg-green-700', 'icon_bg' => 'bg-green-700', 'icon_text' => 'text-white', 'text' => 'text-white']
        ],
        'orange' => [
            'light' => ['border' => 'border-l-orange-600', 'bg' => 'bg-gradient-to-br from-orange-50 to-white hover:shadow-md', 'icon_bg' => 'bg-orange-100', 'icon_text' => 'text-orange-600'],
            'solid' => ['border' => 'border-0', 'bg' => 'bg-orange-600 hover:bg-orange-700', 'icon_bg' => 'bg-orange-700', 'icon_text' => 'text-white', 'text' => 'text-white']
        ],
        'purple' => [
            'light' => ['border' => 'border-l-purple-600', 'bg' => 'bg-gradient-to-br from-purple-50 to-white hover:shadow-md', 'icon_bg' => 'bg-purple-100', 'icon_text' => 'text-purple-600'],
            'solid' => ['border' => 'border-0', 'bg' => 'bg-purple-600 hover:bg-purple-700', 'icon_bg' => 'bg-purple-700', 'icon_text' => 'text-white', 'text' => 'text-white']
        ],
    ];
    $selectedColors = $colorClasses[$color][$variant] ?? $colorClasses['red'][$variant] ?? $colorClasses['red']['light'];
    $textColor = $selectedColors['text'] ?? 'text-gray-900';
    $labelColor = $variant === 'solid' ? 'text-red-100' : 'text-gray-500';
@endphp

<div class="rounded-lg shadow-sm border {{ $selectedColors['border'] }} p-5 transition-all duration-200 {{ $selectedColors['bg'] }}">
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            <p class="text-xs {{ $labelColor }} font-semibold uppercase tracking-wider">
                {{ $title }}
            </p>
            <p class="text-3xl font-bold {{ $textColor }} mt-2">
                {{ $value }}
            </p>
            @if($trend)
                <div class="mt-3 text-xs {{ $trendDirection === 'up' ? ($variant === 'solid' ? 'text-green-200' : 'text-green-600') : ($variant === 'solid' ? 'text-red-200' : 'text-blue-600') }}">
                    <span class="font-medium">
                        <i class="bi {{ $trendDirection === 'up' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                        {{ $trend }}
                    </span>
                </div>
            @endif
        </div>
        <div class="{{ $selectedColors['icon_bg'] }} p-3 rounded-lg flex-shrink-0">
            <i class="bi {{ $icon }} text-2xl {{ $selectedColors['icon_text'] }}"></i>
        </div>
    </div>
</div>


