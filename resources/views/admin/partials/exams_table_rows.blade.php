@forelse($exams as $exam)
<tr class="border-b border-gray-200 hover:bg-gray-50" data-exam-id="{{ $exam->id }}">
    <td class="px-3 py-2 font-medium text-gray-900">{{ $exam->localized_name }}</td>
    <td class="px-3 py-2 text-gray-700">{{ $exam->academic_year }}</td>
    <td class="px-3 py-2 text-gray-700">{{ $exam->semester === 'all' ? 'All Semesters' : ucfirst($exam->semester) }}</td>
    <td class="px-3 py-2 text-gray-700">{{ $exam->subject ? $exam->subject->subject_name : 'All Subjects' }}</td>
    <td class="px-3 py-2 text-gray-700">{{ $exam->formatted_type }}</td>
    <td class="px-3 py-2 text-gray-700">{{ $exam->full_marks }}</td>
    <td class="px-3 py-2 text-gray-700 text-xs">
        <span class="text-gray-900">{{ $exam->formatted_date_ad }}</span>
        <span class="text-gray-400 mx-1">/</span>
        <span class="text-gray-600">{{ $exam->formatted_date_bs }}</span>
    </td>
    <td class="px-3 py-2">
        <span class="inline-block px-2 py-0.5 {{ $exam->status_badge_class }} rounded text-xs font-medium">{{ $exam->formatted_status }}</span>
    </td>
    <td class="px-3 py-2 text-center">
        <div class="flex gap-1 justify-center">
            <a href="{{ route('admin.exam.show', $exam->id) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" title="View">
                <i class="bi bi-eye text-xs"></i>
            </a>
            <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditExamModal({{ $exam->id }})" title="Edit">
                <i class="bi bi-pencil text-xs"></i>
            </button>
            <form method="POST" action="{{ route('admin.exam.destroy', $exam->id) }}" onsubmit="return confirm('Are you sure you want to delete this exam?');" class="inline-block">
                @csrf
                @method('DELETE')
                <button class="text-blue-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-blue-50" title="Delete">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </form>
            <button class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded hover:bg-green-50" onclick="window.location.href='{{ route('admin.exam.show', $exam->id) }}'" title="Upload Marks">
                <i class="bi bi-upload text-xs"></i> Marks
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="px-3 py-3 text-center text-gray-500">No exams found.</td>
</tr>
@endforelse

