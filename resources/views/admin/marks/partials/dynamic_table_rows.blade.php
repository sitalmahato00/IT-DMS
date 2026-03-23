@forelse($students as $student)
    @php
        $subjectIds = collect($columnStructure->subjects)->pluck('id')->toArray();
        $assessmentNumber = $category === 'assessment' ? ($currentFilters['assessment_number'] ?? null) : null;
        $totalMarks = $student->getTotalMarks($subjectIds, $category, $assessmentNumber);
        $totalFull = $student->getTotalFullMarks($subjectIds, $category, $assessmentNumber);
        $percentage = $totalFull > 0 ? round(($totalMarks / $totalFull) * 100, 1) : 0;
        $result = $percentage >= 40 ? 'PASS' : 'FAIL';
        
        // Filter by status if applied
        $showRow = true;
        if (!empty($currentFilters['status'])) {
            if ($currentFilters['status'] === 'pass' && $result !== 'PASS') $showRow = false;
            if ($currentFilters['status'] === 'fail' && $result !== 'FAIL') $showRow = false;
        }
    @endphp
    @if($showRow)
    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 border-b border-gray-200 dark:border-slate-600 cursor-pointer" 
        onclick="openStudentModal({{ $student->id }})">
        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200 sticky left-0 bg-white dark:bg-slate-800 z-10">{{ $student->roll_no }}</td>
        <td class="px-4 py-3 text-gray-700 dark:text-gray-200 sticky left-20 bg-white dark:bg-slate-800 z-10">{{ $student->user->name ?? 'N/A' }}</td>
        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $student->getAttendancePercentage() >= 75 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                {{ $student->getAttendancePercentage() }}%
            </span>
        </td>
        
        {{-- Dynamic Marks Columns --}}
        @foreach($columnStructure->subjects as $subject)
            @php $assessMarks = $student->getAssessmentMarks($subject->id, $category, $assessmentNumber); @endphp
            <td class="px-1 py-3 text-center text-gray-600 dark:text-gray-300 border-l border-gray-200 dark:border-slate-600">{{ $assessMarks->full > 0 ? $assessMarks->full : '-' }}</td>
            <td class="px-1 py-3 text-center text-gray-500 dark:text-gray-400">{{ $assessMarks->pass > 0 ? $assessMarks->pass : '-' }}</td>
            <td class="px-1 py-3 text-center font-medium {{ $assessMarks->is_absent ? 'text-purple-600 dark:text-purple-400 font-bold' : ($assessMarks->obtained > 0 && $assessMarks->is_pass === false ? 'text-red-600 dark:text-red-400' : ($assessMarks->obtained > 0 && $assessMarks->is_pass === true ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300')) }}">
                {{ $assessMarks->is_absent ? 'ABS' : ($assessMarks->obtained > 0 ? $assessMarks->obtained : '-') }}
            </td>
        @endforeach
        
        <td class="px-4 py-3 text-center font-bold text-gray-800 dark:text-gray-200 border-l border-gray-300 dark:border-slate-600">{{ $totalMarks }}</td>
        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $percentage }}%</td>
        <td class="px-4 py-3 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $result === 'PASS' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                {{ $result }}
            </span>
        </td>
    </tr>
    @endif
@empty
    <tr>
        <td colspan="{{ 3 + ($columnStructure->subjects->count() * $columnStructure->colspan) + 3 }}" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
            <div class="flex flex-col items-center justify-center">
                <i class="bi bi-search text-4xl mb-2"></i>
                <p>No student marks found. Try adjusting your filters.</p>
            </div>
        </td>
    </tr>
@endforelse
