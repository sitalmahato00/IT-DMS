@props(['headers' => [], 'paginate' => false, 'perPage' => 10])

@php
    $tableId = 'tbl_' . uniqid();
    $perPage = intval($perPage) ?: 10;
@endphp

<div class="overflow-x-auto" id="{{ $tableId }}_wrapper">
    <table id="{{ $tableId }}" {{ $attributes->merge(['class' => 'w-full text-xs border-collapse']) }}>
        @if(count($headers))
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    @foreach($headers as $index => $header)
                        @php
                            $centerIndexes = array_values(array_diff(range(0, count($headers) - 1), [0]));
                            $alignClass = in_array($index, $centerIndexes) ? 'text-center' : 'text-left';
                        @endphp
                        <th class="px-4 py-3 font-semibold text-gray-900 align-middle {{ $alignClass }}">{!! $header !!}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        @else
            {{-- Allow caller to provide their own thead/tbody markup inside the slot --}}
            {{ $slot }}
        @endif
    </table>
</div>

@if($paginate)
    <div class="mt-2 flex items-center justify-between text-xs" id="{{ $tableId }}_pager">
        <div class="flex items-center gap-2">
            <button type="button" data-action="prev" class="px-2 py-1 border rounded text-gray-700">Prev</button>
            <div class="pages flex items-center gap-1"></div>
            <button type="button" data-action="next" class="px-2 py-1 border rounded text-gray-700">Next</button>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-gray-600">Per page</label>
            <select id="{{ $tableId }}_perpage" class="px-2 py-1 border rounded">
                <option value="5">5</option>
                <option value="10" {{ $perPage==10 ? 'selected' : '' }}>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <script>
        (function(){
            const table = document.getElementById('{{ $tableId }}');
            if (!table) return;
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const pager = document.getElementById('{{ $tableId }}_pager');
            const pagesContainer = pager.querySelector('.pages');
            const perPageSelect = document.getElementById('{{ $tableId }}_perpage');

            let perPage = parseInt(perPageSelect.value, 10) || {{ $perPage }};
            let currentPage = 1;

            function renderPage(page){
                const total = rows.length;
                const totalPages = Math.max(1, Math.ceil(total / perPage));
                currentPage = Math.min(Math.max(1, page), totalPages);
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                rows.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
                renderControls(totalPages);
            }

            function renderControls(totalPages){
                pagesContainer.innerHTML = '';
                for(let i=1;i<=totalPages;i++){
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = i;
                    btn.className = 'px-2 py-1 border rounded ' + (i===currentPage ? 'bg-gray-200' : '');
                    btn.addEventListener('click', () => renderPage(i));
                    pagesContainer.appendChild(btn);
                }
            }

            pager.querySelector('[data-action="prev"]').addEventListener('click', ()=> renderPage(currentPage-1));
            pager.querySelector('[data-action="next"]').addEventListener('click', ()=> renderPage(currentPage+1));
            perPageSelect.addEventListener('change', ()=>{ perPage = parseInt(perPageSelect.value,10); renderPage(1); });

            // Initialize
            renderPage(1);
        })();
    </script>
@endif
