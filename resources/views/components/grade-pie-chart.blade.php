@props(['gradeDistribution' => []])

@php
    // Get actual counts for all 8 grades
    $aplusGrade = $gradeDistribution['A+'] ?? 0;
    $aGrade = $gradeDistribution['A'] ?? 0;
    $bplusGrade = $gradeDistribution['B+'] ?? 0;
    $bGrade = $gradeDistribution['B'] ?? 0;
    $cplusGrade = $gradeDistribution['C+'] ?? 0;
    $cGrade = $gradeDistribution['C'] ?? 0;
    $dGrade = $gradeDistribution['D'] ?? 0;
    $fGrade = $gradeDistribution['F'] ?? 0;
    
    // Calculate total for percentage calculations
    $total = $aplusGrade + $aGrade + $bplusGrade + $bGrade + $cplusGrade + $cGrade + $dGrade + $fGrade;
    
    // Calculate actual percentages for display
    $aplusPct = $total > 0 ? round(($aplusGrade / $total) * 100, 1) : 0;
    $aPct = $total > 0 ? round(($aGrade / $total) * 100, 1) : 0;
    $bplusPct = $total > 0 ? round(($bplusGrade / $total) * 100, 1) : 0;
    $bPct = $total > 0 ? round(($bGrade / $total) * 100, 1) : 0;
    $cplusPct = $total > 0 ? round(($cplusGrade / $total) * 100, 1) : 0;
    $cPct = $total > 0 ? round(($cGrade / $total) * 100, 1) : 0;
    $dPct = $total > 0 ? round(($dGrade / $total) * 100, 1) : 0;
    $fPct = $total > 0 ? round(($fGrade / $total) * 100, 1) : 0;
    
    // Calculate stroke-dasharray values (circumference = 251.2 for r=40)
    $circumference = 251.2;
    
    // Calculate percentages for the pie chart segments
    $aplusPercent = $total > 0 ? ($aplusGrade / $total) * $circumference : 0;
    $aPercent = $total > 0 ? ($aGrade / $total) * $circumference : 0;
    $bplusPercent = $total > 0 ? ($bplusGrade / $total) * $circumference : 0;
    $bPercent = $total > 0 ? ($bGrade / $total) * $circumference : 0;
    $cplusPercent = $total > 0 ? ($cplusGrade / $total) * $circumference : 0;
    $cPercent = $total > 0 ? ($cGrade / $total) * $circumference : 0;
    $dPercent = $total > 0 ? ($dGrade / $total) * $circumference : 0;
    $fPercent = $total > 0 ? ($fGrade / $total) * $circumference : 0;
    
    // Calculate cumulative offsets
    $offset1 = 0;
    $offset2 = $aplusPercent;
    $offset3 = $aplusPercent + $aPercent;
    $offset4 = $aplusPercent + $aPercent + $bplusPercent;
    $offset5 = $aplusPercent + $aPercent + $bplusPercent + $bPercent;
    $offset6 = $aplusPercent + $aPercent + $bplusPercent + $bPercent + $cplusPercent;
    $offset7 = $aplusPercent + $aPercent + $bplusPercent + $bPercent + $cplusPercent + $cPercent;
    $offset8 = $aplusPercent + $aPercent + $bplusPercent + $bPercent + $cplusPercent + $cPercent + $dPercent;
@endphp

<div class="flex flex-col items-center justify-center h-full">
    <div class="relative w-40 h-40">
        <svg viewBox="0 0 100 100" class="w-full h-full">
            <!-- A+ Grade (Dark Green) -->
            @if($aplusPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#15803d" stroke-width="8" stroke-dasharray="{{ $aplusPercent }} {{ $circumference }}" stroke-dashoffset="0" transform="rotate(-90 50 50)"/>
            @endif
            <!-- A Grade (Green) -->
            @if($aPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#22c55e" stroke-width="8" stroke-dasharray="{{ $aPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset2 }}" transform="rotate(-90 50 50)"/>
            @endif
            <!-- B+ Grade (Blue) -->
            @if($bplusPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#2563eb" stroke-width="8" stroke-dasharray="{{ $bplusPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset3 }}" transform="rotate(-90 50 50)"/>
            @endif
            <!-- B Grade (Light Blue) -->
            @if($bPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="8" stroke-dasharray="{{ $bPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset4 }}" transform="rotate(-90 50 50)"/>
            @endif
            <!-- C+ Grade (Yellow) -->
            @if($cplusPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#eab308" stroke-width="8" stroke-dasharray="{{ $cplusPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset5 }}" transform="rotate(-90 50 50)"/>
            @endif
            <!-- C Grade (Orange) -->
            @if($cPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="8" stroke-dasharray="{{ $cPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset6 }}" transform="rotate(-90 50 50)"/>
            @endif
            <!-- D Grade (Red) -->
            @if($dPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="8" stroke-dasharray="{{ $dPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset7 }}" transform="rotate(-90 50 50)"/>
            @endif
            <!-- F Grade (Gray) -->
            @if($fPercent > 0)
            <circle cx="50" cy="50" r="40" fill="none" stroke="#9ca3af" stroke-width="8" stroke-dasharray="{{ $fPercent }} {{ $circumference }}" stroke-dashoffset="-{{ $offset8 }}" transform="rotate(-90 50 50)"/>
            @endif
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900">{{ $total }}</p>
                <p class="text-xs text-gray-600">Students</p>
            </div>
        </div>
    </div>
    
    <!-- Grade Legend - Horizontal Row with Percentages -->
    <div class="flex flex-wrap justify-center gap-3 mt-4 text-xs">
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-green-700"></div>
            <span class="text-gray-700 font-medium">A+</span>
            <span class="text-gray-500">{{ $aplusPct }}%</span>
            <span class="text-gray-400">({{ $aplusGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="text-gray-700 font-medium">A</span>
            <span class="text-gray-500">{{ $aPct }}%</span>
            <span class="text-gray-400">({{ $aGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-blue-700"></div>
            <span class="text-gray-700 font-medium">B+</span>
            <span class="text-gray-500">{{ $bplusPct }}%</span>
            <span class="text-gray-400">({{ $bplusGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
            <span class="text-gray-700 font-medium">B</span>
            <span class="text-gray-500">{{ $bPct }}%</span>
            <span class="text-gray-400">({{ $bGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
            <span class="text-gray-700 font-medium">C+</span>
            <span class="text-gray-500">{{ $cplusPct }}%</span>
            <span class="text-gray-400">({{ $cplusGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-orange-500"></div>
            <span class="text-gray-700 font-medium">C</span>
            <span class="text-gray-500">{{ $cPct }}%</span>
            <span class="text-gray-400">({{ $cGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <span class="text-gray-700 font-medium">D</span>
            <span class="text-gray-500">{{ $dPct }}%</span>
            <span class="text-gray-400">({{ $dGrade }})</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full bg-gray-400"></div>
            <span class="text-gray-700 font-medium">F</span>
            <span class="text-gray-500">{{ $fPct }}%</span>
            <span class="text-gray-400">({{ $fGrade }})</span>
        </div>
    </div>
</div>
