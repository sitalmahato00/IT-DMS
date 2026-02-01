@props(['color' => 'gray', 'size' => 'xs'])
@php
    $classes = "flex items-center gap-1 px-2 py-" . ($size === 'xs' ? '1' : '2') . " text-{$size} text-{$color}-700 hover:bg-{$color}-100 rounded transition";
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
