@props(['headers' => []])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-xs border-collapse']) }}>
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                @foreach($headers as $index => $header)
                    @php
                        $alignClass = in_array($index, [1, 2, 3, 4]) ? 'text-center' : 'text-left';
                    @endphp
                    <th class="px-4 py-3 font-semibold text-gray-900 align-middle {{ $alignClass }}">{!! $header !!}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
