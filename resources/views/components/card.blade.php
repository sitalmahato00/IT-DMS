@props(['title' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded shadow-sm border border-gray-200']) }}>
    @if($title || $actions)
    <div class="p-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
        <div class="flex items-center gap-2">{!! $actions !!}</div>
    </div>
    @endif

    <div class="p-3">
        {{ $slot }}
    </div>
</div>
