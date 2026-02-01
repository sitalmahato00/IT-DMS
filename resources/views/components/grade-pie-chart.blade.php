@props(['gradeDistribution' => []])

@php
    $aGrade = $gradeDistribution['A'] ?? 28;
    $bGrade = $gradeDistribution['B'] ?? 35;
    $cGrade = $gradeDistribution['C'] ?? 22;
    $dGrade = $gradeDistribution['D'] ?? 10;
    $fGrade = $gradeDistribution['F'] ?? 5;
    
    // Calculate stroke-dasharray values (circumference = 251.2 for r=40)
    $circumference = 251.2;
    $aPercent = ($aGrade / 100) * $circumference;
    $bPercent = ($bGrade / 100) * $circumference;
    $cPercent = ($cGrade / 100) * $circumference;
    $dPercent = ($dGrade / 100) * $circumference;
    $fPercent = ($fGrade / 100) * $circumference;
@endphp

<div class="flex flex-col items-center justify-center h-full">
    <div class="relative w-40 h-40">
        <svg viewBox="0 0 100 100" class="w-full h-full">
            <!-- A Grade (Green) -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="8" stroke-dasharray="{{ $aPercent }} {{ $circumference }}" stroke-dashoffset="0" transform="rotate(-90 50 50)"/>
            <!-- B Grade (Blue) -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="8" stroke-dasharray="{{ $bPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent }}" transform="rotate(-90 50 50)"/>
            <!-- C Grade (Orange) -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="8" stroke-dasharray="{{ $cPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent + $bPercent }}" transform="rotate(-90 50 50)"/>
            <!-- D Grade (Red) -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="8" stroke-dasharray="{{ $dPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent + $bPercent + $cPercent }}" transform="rotate(-90 50 50)"/>
            <!-- F Grade (Gray) -->
            <circle cx="50" cy="50" r="40" fill="none" stroke="#9ca3af" stroke-width="8" stroke-dasharray="{{ $fPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $aPercent + $bPercent + $cPercent + $dPercent }}" transform="rotate(-90 50 50)"/>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900">Grade</p>
                <p class="text-xs text-gray-600">Distribution</p>
            </div>
        </div>
    </div>
    
    <!-- Legend -->
    <div class="grid grid-cols-2 gap-2 mt-4 w-full text-xs">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-green-600 rounded-full"></div>
            <span class="text-gray-600">A <span class="font-medium">{{ $aGrade }}%</span></span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
            <span class="text-gray-600">B <span class="font-medium">{{ $bGrade }}%</span></span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-orange-600 rounded-full"></div>
            <span class="text-gray-600">C <span class="font-medium">{{ $cGrade }}%</span></span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-red-600 rounded-full"></div>
            <span class="text-gray-600">D <span class="font-medium">{{ $dGrade }}%</span></span>
        </div>
    </div>
</div>
