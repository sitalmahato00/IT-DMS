{{--
    Admin Table Actions Component
    Features:
    - Red icon highlights for important actions
    - View, Edit, Delete, and Special actions
    
    Usage:
    @include('admin.components.admin-table-actions', [
        'actions' => [
            ['type' => 'view', 'route' => route('admin.students.show', $student->id), 'icon' => 'bi-eye'],
            ['type' => 'edit', 'route' => route('admin.students.edit', $student->id), 'icon' => 'bi-pencil'],
            ['type' => 'delete', 'route' => '#', 'onclick' => "deleteStudent({$student->id})", 'icon' => 'bi-trash'],
            ['type' => 'special', 'route' => route('admin.students.alumni', $student->id), 'icon' => 'bi-mortarboard', 'label' => 'Move to Alumni'],
        ]
    ])
--}}

@php
    $actions = $actions ?? [];
    $size = $size ?? 'md'; // sm, md, lg
@endphp

<div class="flex items-center justify-center gap-1">
    @foreach($actions as $action)
        @php
            $type = $action['type'] ?? 'default';
            
            // Styling based on action type - with better dark mode colors
            $classes = match($type) {
                'view' => 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30',
                'edit' => 'text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/30',
                'delete' => 'text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30',
                'special' => 'text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/30',
                default => 'text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900/30',
            };
            
            $iconSize = match($size) {
                'sm' => 'text-xs',
                'lg' => 'text-base',
                default => 'text-sm',
            };
            
            $padding = match($size) {
                'sm' => 'p-1.5',
                'lg' => 'p-2.5',
                default => 'p-2',
            };
        @endphp

        @if(!empty($action['route']))
            <a 
                href="{{ $action['route'] }}"
                @if(!empty($action['target']))
                target="{{ $action['target'] }}"
                @endif
                class="{{ $padding }} rounded-lg {{ $classes }} transition-colors"
                title="{{ $action['label'] ?? ucfirst($type) }}"
            >
                <i class="bi {{ $action['icon'] ?? 'bi-circle' }} {{ $iconSize }}"></i>
            </a>
        @elseif(!empty($action['onclick']))
            <button 
                onclick="{{ $action['onclick'] }}"
                class="{{ $padding }} rounded-lg {{ $classes }} transition-colors"
                title="{{ $action['label'] ?? ucfirst($type) }}"
                type="button"
            >
                <i class="bi {{ $action['icon'] ?? 'bi-circle' }} {{ $iconSize }}"></i>
            </button>
        @elseif(!empty($action['button']))
            <button 
                type="button"
                @if(!empty($action['id']))
                id="{{ $action['id'] }}"
                @endif
                class="{{ $padding }} rounded-lg {{ $classes }} transition-colors"
                title="{{ $action['label'] ?? ucfirst($type) }}"
            >
                <i class="bi {{ $action['icon'] ?? 'bi-circle' }} {{ $iconSize }}"></i>
            </button>
        @endif
    @endforeach
</div>
