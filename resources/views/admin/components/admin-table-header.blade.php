{{--
    Admin Table Header Component
    Features:
    - Enhanced styling for table headers
    - Dark mode compatible
    
    Usage:
    @include('admin.components.admin-table-header', [
        'columns' => [
            ['label' => 'Name', 'sortable' => true, 'sortBy' => 'name'],
            ['label' => 'Email'],
            ['label' => 'Status'],
            ['label' => 'Actions', 'align' => 'center'],
        ]
    ])
--}}

@php
    $columns = $columns ?? [];
@endphp

<thead class="bg-gray-50 dark:bg-slate-700/50 text-sm font-semibold text-gray-700 dark:text-gray-200 border-b border-gray-200 dark:border-slate-600">
    <tr>
        {{ $slot }}
    </tr>
</thead>
