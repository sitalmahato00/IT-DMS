@forelse($exam->marks as $mark)
@php
    $rowFullMarks = $mark->full_marks > 0 ? $mark->full_marks : $mark->calculateFullMarks();
    $rowPassingMarks = $mark->passing_marks > 0 ? $mark->passing_marks : $mark->getEffectivePassingMarksAttribute();
    $rowObtained = $mark->isCtevt() ? $mark->calculateTotalMarks() : ($mark->marks_obtained ?? 0);
    $rowPercentage = $rowFullMarks > 0 ? round(($rowObtained / $rowFullMarks) * 100, 2) : 0;
@endphp
<tr class="border-b border-gray-200 hover:bg-gray-50 animate-fade-in" id="mark-row-{{ $mark->id }}">
    <td class="px-3 py-2 text-xs text-gray-700">{{ $mark->student->id ?? '-' }}</td>
    <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ $mark->student->user->name ?? 'N/A' }}</td>
    <td class="px-3 py-2 text-xs text-gray-700">{{ $mark->student->roll_no ?? '-' }}</td>
    <td class="px-3 py-2 text-center">
        <span class="inline-block px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-xs">Present</span>
    </td>
    <td class="px-3 py-2 text-center text-xs text-gray-700">{{ $rowFullMarks }}</td>
    <td class="px-3 py-2 text-center text-xs text-gray-700">{{ $rowPassingMarks }}</td>
    <td class="px-3 py-2 text-center">
        <span class="font-semibold {{ $rowPercentage >= 35 ? 'text-green-600' : 'text-blue-600' }}">
            {{ $rowObtained }}
        </span>
    </td>
    <td class="px-3 py-2 text-center text-xs text-gray-700">{{ number_format($rowPercentage, 2) }}%</td>
    <td class="px-3 py-2 text-center">
        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $mark->percentage >= 35 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-blue-700' }}">
            {{ $mark->grade }}
        </span>
    </td>
    <td class="px-3 py-2 text-center">
        @if($mark->percentage >= 35)
            <span class="text-green-600 text-xs"><i class="bi bi-check-circle"></i> Passed</span>
        @else
            <span class="text-blue-600 text-xs"><i class="bi bi-x-circle"></i> Failed</span>
        @endif
    </td>
</tr>
@empty
<tr id="no-marks-row">
    <td colspan="10" class="px-3 py-4 text-center text-gray-500 text-xs">
        No marks have been uploaded yet. Click "Upload Marks" to add marks.
    </td>
</tr>
@endforelse



