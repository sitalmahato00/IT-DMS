<!-- Section Card Component -->
<!-- Usage: @include('admin.components.section-card', ['title' => 'Students List', 'icon' => 'bi-mortarboard', 'color' => 'red', 'slot' => $content]) -->
@props([
    'title' => '',
    'icon' => 'bi-square',
    'color' => 'red',
    'headerAction' => null
])

@php
    $colorClasses = [
        'red' => 'bg-blue-600',
        'teal' => 'bg-teal-600',
        'orange' => 'bg-orange-600',
        'green' => 'bg-green-600',
        'blue' => 'bg-blue-600',
        'purple' => 'bg-purple-600',
    ];
    $bgColor = $colorClasses[$color] ?? $colorClasses['red'];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
    <!-- Red Header -->
    <div class="{{ $bgColor }} text-white px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="bi {{ $icon }} text-xl"></i>
            <h3 class="text-lg font-bold">{{ $title }}</h3>
        </div>
        @if($headerAction)
            <div>{{ $headerAction }}</div>
        @endif
    </div>
    
    <!-- White Body -->
    <div class="p-6">
        {{ $slot }}
    </div>
</div>


