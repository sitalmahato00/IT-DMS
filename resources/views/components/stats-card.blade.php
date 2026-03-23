@props(['title','value','icon' => 'bi-people','color' => 'blue'])

<div {{ $attributes->merge(['class' => 'p-3 rounded shadow-sm border border-gray-200 bg-white']) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-600 text-xs">{{ $title }}</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">{{ $value }}</p>
        </div>
        <div class="p-2 rounded" style="background-color:transparent">
            <div class="bg-{{ $color }}-100 p-2 rounded">
                <i class="{{ $icon }} text-lg text-{{ $color }}-600"></i>
            </div>
        </div>
    </div>
</div>
