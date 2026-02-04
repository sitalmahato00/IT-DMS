{{-- Dual Calendar Date Picker Component (BS/AD) --}}
{{-- Supports Bikram Sambat and Gregorian calendars with toggle --}}
{{-- Hidden input stores AD date (YYYY-MM-DD) for Laravel forms --}}

@props([
    'name' => 'date',
    'id' => null,
    'label' => null,
    'value' => null,
    'required' => false,
    'helpText' => null,
    'placeholder' => 'Select date',
    'defaultMode' => 'ad',  // 'bs' or 'ad' - now defaults to AD
])

@php
    $id = $id ?? $name;
    $label = $label ?? ucfirst(str_replace('_', ' ', $name));
    $bsValue = '';
    
    // Convert AD to BS if value is provided
    if (!empty($value)) {
        $bsValue = \App\Helpers\NepaliContentHelper::convertAdToBs($value) ?? '';
    }
    
    // Get current dates
    $currentBsYear = (int) date('Y') + 56; // Approximate
    $currentBsMonth = date('n') > 4 ? date('n') - 8 : date('n') + 4;
    if ($currentBsMonth <= 0) {
        $currentBsMonth += 12;
        $currentBsYear -= 1;
    }
    $currentBsDay = (int) date('j');
@endpress

<div class="dual-date-picker-container" 
     data-calendar-mode="{{ $defaultMode }}"
     data-current-bs-year="{{ $currentBsYear }}"
     data-current-bs-month="{{ $currentBsMonth }}"
     style="position: relative;">
    
    {{-- Hidden AD input (for Laravel form submission) --}}
    <input type="hidden" 
           name="{{ $name }}" 
           id="{{ $id }}_ad" 
           value="{{ old($name, $value) }}"
           class="dual-date-picker-ad">

    {{-- Label --}}
    @if($label)
        <label for="{{ $id }}_display" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    {{-- Calendar Toggle --}}
    <div class="flex items-center gap-2 mb-2">
        <div class="inline-flex items-center bg-gray-100 rounded-lg p-1" role="group" aria-label="Calendar mode">
            <button type="button" 
                    class="calendar-toggle-btn px-3 py-1 text-sm font-medium rounded-md transition-all duration-200"
                    data-mode="bs"
                    data-target="{{ $id }}">
                <span class="devanagari-text">बि.सं.</span> BS
            </button>
            <button type="button" 
                    class="calendar-toggle-btn px-3 py-1 text-sm font-medium rounded-md transition-all duration-200"
                    data-mode="ad"
                    data-target="{{ $id }}">
                <span class="font-medium">AD</span>
            </button>
        </div>
        <span class="text-xs text-gray-500 mode-indicator">
            @if($defaultMode === 'bs')
                <span class="devanagari-text">नेपाली पात्रो</span> (Bikram Sambat)
            @else
                English Calendar (Gregorian)
            @endif
        </span>
    </div>

    {{-- Date Display/Input --}}
    <div class="relative">
        {{-- Display showing selected date in BS format --}}
        <div class="flex">
            <div class="relative flex-1">
                {{-- BS Date Display (Primary) --}}
                <div class="date-display-trigger w-full px-3 py-2 border border-gray-300 rounded-l-lg bg-white cursor-pointer flex items-center gap-2 hover:border-gray-400 transition-colors"
                     id="{{ $id }}_bs_display"
                     onclick="toggleCalendar('{{ $id }}')">
                    <i class="bi bi-calendar3 text-gray-400"></i>
                    <span class="bs-date-text devanagari-text text-gray-900">
                        {{ !empty($bsValue) ? formatBsDisplay($bsValue) : $placeholder }}
                    </span>
                    <span class="text-xs text-gray-400 ad-date-preview ml-auto">
                        @if(!empty($value))
                            ({{ formatAdDisplay($value) }})
                        @endif
                    </span>
                </div>
                
                {{-- Calendar Popup --}}
                <div class="calendar-popup hidden absolute z-50 mt-1 bg-white rounded-lg shadow-xl border border-gray-200"
                     id="{{ $id }}_calendar"
                     style="width: 320px;">
                    
                    {{-- Calendar Header --}}
                    <div class="calendar-header flex items-center justify-between p-3 border-b border-gray-100">
                        <button type="button" 
                                class="calendar-nav-btn p-1 hover:bg-gray-100 rounded"
                                onclick="changeMonth('{{ $id }}', -1)">
                            <i class="bi bi-chevron-left text-gray-600"></i>
                        </button>
                        <div class="calendar-title font-semibold text-gray-900">
                            <span class="bs-month-year devanagari-text"></span>
                            <span class="ad-month-year text-gray-500 text-sm ml-1"></span>
                        </div>
                        <button type="button" 
                                class="calendar-nav-btn p-1 hover:bg-gray-100 rounded"
                                onclick="changeMonth('{{ $id }}', 1)">
                            <i class="bi bi-chevron-right text-gray-600"></i>
                        </button>
                    </div>
                    
                    {{-- Weekday Headers --}}
                    <div class="calendar-weekdays grid grid-cols-7 gap-0 border-b border-gray-100">
                        @foreach(['आइ', 'सोम', 'मंगल', 'बुध', 'बिही', 'शुक्र', 'शनि'] as $day)
                            <div class="py-2 text-center text-xs font-medium text-gray-500 devanagari-text">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Days Grid --}}
                    <div class="calendar-days grid grid-cols-7 gap-0 p-2" id="{{ $id }}_days">
                        {{-- Days will be rendered by JavaScript --}}
                    </div>
                    
                    {{-- Today Button --}}
                    <div class="calendar-footer p-2 border-t border-gray-100">
                        <button type="button" 
                                class="w-full py-1.5 text-sm text-red-600 hover:bg-red-50 rounded transition-colors"
                                onclick="selectToday('{{ $id }}')">
                            <i class="bi bi-calendar-check mr-1"></i>
                            <span class="devanagari-text">आज</span> (Today)
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Clear Button --}}
            <button type="button" 
                    class="px-3 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 hover:bg-gray-100 text-gray-500 transition-colors"
                    onclick="clearDate('{{ $id }}')"
                    title="Clear date">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    {{-- Help Text --}}
    @if($helpText)
        <p class="mt-1 text-xs text-gray-500">{{ $helpText }}</p>
    @endif

    {{-- Validation Error --}}
    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Include the JavaScript --}}
<script src="{{ asset('js/dual-date-picker.js') }}"></script>

{{-- Initialize the date picker --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.dual-date-picker-container[data-current-bs-year]');
        if (container) {
            const id = container.querySelector('.calendar-toggle-btn').dataset.target;
            const initialValue = document.getElementById(id + '_ad').value;
            const defaultMode = container.dataset.calendarMode;
            
            initDualDatePicker(id, initialValue, defaultMode);
        }
    });
</script>

