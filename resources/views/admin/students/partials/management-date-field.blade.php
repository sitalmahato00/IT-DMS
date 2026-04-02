@php
    $isRequired = $required ?? false;
    $fieldSpan = $span ?? '';
    $icon = $icon ?? 'bi-calendar3';
    $hasError = $errors->has($id);
@endphp

<div class="space-y-2 {{ $fieldSpan }}">
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
        {{ $label }}
        @if($isRequired)
            <span class="text-rose-500">*</span>
        @endif
    </label>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
            <i class="bi {{ $icon }}"></i>
        </span>
        <input
            type="date"
            id="{{ $id }}"
            name="{{ $id }}"
            value="{{ old($id, $value ?? '') }}"
            @if($isRequired) required @endif
            class="block w-full rounded-2xl border {{ $hasError ? 'border-rose-300' : 'border-slate-200' }} bg-white py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm outline-none transition hover:border-slate-300 focus:border-rose-300"
        >
    </div>
    <p class="student-field-error text-xs font-medium text-rose-600" data-error-for="{{ $id }}">{{ $errors->first($id) }}</p>
</div>
