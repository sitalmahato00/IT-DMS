@extends('admin.layouts.app')

@section('title', isset($course) ? $course->subject_name : 'Course Details')

@if(!isset($course))
    <div class="container mx-auto p-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <i class="bi bi-exclamation-triangle text-4xl text-red-500 mb-4"></i>
            <h1 class="text-2xl font-bold text-red-800 mb-2">Course Not Found</h1>
            <p class="text-red-600 mb-6">The requested course could not be loaded.</p>
            <a href="{{ route('admin.courses') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="bi bi-arrow-left mr-2"></i>
                Back to Courses
            </a>
        </div>
    </div>
@else
@section('content')
<div class="space-y-4">
@endif
    <!-- Back Button and Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses') }}" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="bi bi-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $course->subject_name }}</h1>
                <p class="text-gray-600 text-xs mt-1">{{ $course->subject_code }} • Semester {{ ucfirst($course->semester ?? 'N/A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($course->status == 'active') bg-green-100 text-green-800
                @else bg-yellow-100 text-yellow-800 @endif">
                {{ ucfirst($course->status) }}
            </span>
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs flex items-center gap-1.5 transition">
                <i class="bi bi-pencil text-sm"></i>
                <span>Edit Course</span>
            </a>
        </div>
    </div>

    <!-- Main Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <!-- Credits -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Credits</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $course->credits ?? 4 }}</p>
                </div>
                <div class="bg-blue-100 p-2.5 rounded-lg">
                    <i class="bi bi-star text-lg text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Assigned Teacher -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Assigned Teacher</p>
                    <p class="text-lg font-bold text-gray-900 mt-1 truncate">{{ $course->teacher_name ?? $course->assigned_teachers ?? $course->pivot_teacher_name ?? 'Not Assigned' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Lab Tech: {{ $course->labTechnician?->user?->name ?? 'None' }}</p>
                </div>
                <div class="bg-green-100 p-2.5 rounded-lg">
                    <i class="bi bi-person-badge text-lg text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Category -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Category</p>
                    <p class="text-lg font-bold text-gray-900 mt-1 truncate">{{ $course->category ?? 'General' }}</p>
                </div>
                <div class="bg-purple-100 p-2.5 rounded-lg">
                    <i class="bi bi-tag text-lg text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Duration -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Course Duration</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">
                        @if($course->start_date && $course->end_date)
                            {{ date('M d, Y', strtotime($course->start_date)) }} - {{ date('M d, Y', strtotime($course->end_date)) }}
                        @else
                            Not Set
                        @endif
                    </p>
                </div>
                <div class="bg-orange-100 p-2.5 rounded-lg">
                    <i class="bi bi-calendar-range text-lg text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Description -->
    @if($course->description)
    <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-2">Course Description</h3>
        <p class="text-gray-700 text-sm">{{ $course->description }}</p>
    </div>
    @endif

    <!-- Course Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Assessment Pattern -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                <i class="bi bi-clipboard-check mr-1"></i>Assessment Pattern
            </h3>
            <div class="space-y-3">
                <!-- Theory/Practical Split -->
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Theory</span>
                        <span class="font-medium">{{ $course->theory_percentage ?? 70 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $course->theory_percentage ?? 70 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Practical</span>
                        <span class="font-medium">{{ $course->practical_percentage ?? 30 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $course->practical_percentage ?? 30 }}%"></div>
                    </div>
                </div>
                <hr class="border-gray-200">
                <!-- Internal/External Split -->
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Internal Assessment</span>
                        <span class="font-medium">{{ $course->internal_percentage ?? 40 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $course->internal_percentage ?? 40 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">External Exam</span>
                        <span class="font-medium">{{ $course->external_percentage ?? 60 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $course->external_percentage ?? 60 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Teaching Hours -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                <i class="bi bi-clock mr-1"></i>Weekly Teaching Hours
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $course->lecture_hours ?? 4 }}</p>
                    <p class="text-xs text-gray-600 mt-1">Lectures</p>
                    <p class="text-xs text-gray-500">hrs/week</p>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ $course->practical_hours ?? 2 }}</p>
                    <p class="text-xs text-gray-600 mt-1">Practicals</p>
                    <p class="text-xs text-gray-500">hrs/week</p>
                </div>
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">{{ $course->tutorial_hours ?? 1 }}</p>
                    <p class="text-xs text-gray-600 mt-1">Tutorials</p>
                    <p class="text-xs text-gray-500">hrs/week</p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Teaching Hours/Week</span>
                    <span class="text-lg font-bold text-gray-900">
                        {{ ($course->lecture_hours ?? 4) + ($course->practical_hours ?? 2) + ($course->tutorial_hours ?? 1) }} hrs
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Prerequisites -->
    @if($course->prerequisite)
    <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-2">
            <i class="bi bi-link-45deg mr-1"></i>Prerequisite Course
        </h3>
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 bg-gray-100 rounded text-sm font-medium">{{ $course->prerequisite }}</span>
        </div>
    </div>
    @endif

    <!-- Syllabus and Learning Objectives -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Syllabus -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                <i class="bi bi-list-check mr-1"></i>Course Syllabus
            </h3>
            @if($course->syllabus)
                <ul class="space-y-2 text-sm">
                    @foreach(array_filter(array_map('trim', explode("\n", $course->syllabus))) as $item)
                        @if($item)
                        <li class="flex items-start gap-2">
                            <i class="bi bi-chevron-right text-blue-500 mt-0.5 text-xs"></i>
                            <span class="text-gray-700">{{ $item }}</span>
                        </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-sm italic">No syllabus details added</p>
            @endif
        </div>

        <!-- Learning Objectives -->
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">
                <i class="bi bi-bullseye mr-1"></i>Learning Objectives
            </h3>
            @if($course->learning_objectives)
                <ul class="space-y-2 text-sm">
                    @foreach(array_filter(array_map('trim', explode("\n", $course->learning_objectives))) as $item)
                        @if($item)
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle text-green-500 mt-0.5 text-xs"></i>
                            <span class="text-gray-700">{{ $item }}</span>
                        </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-sm italic">No learning objectives added</p>
            @endif
        </div>
    </div>

    <!-- Remarks -->
    @if($course->remarks)
    <div class="bg-yellow-50 p-4 rounded border border-yellow-200">
        <h3 class="text-sm font-semibold text-yellow-800 mb-2">
            <i class="bi bi-info-circle mr-1"></i>Additional Remarks
        </h3>
        <p class="text-sm text-yellow-700">{{ $course->remarks }}</p>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-blue-700 rounded text-xs flex items-center gap-1.5 transition">
                <i class="bi bi-pencil"></i> Edit Course
            </a>
            <button onclick="deleteCourse({{ $course->id }})" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs flex items-center gap-1.5 transition">
                <i class="bi bi-trash"></i> Delete Course
            </button>
            <a href="{{ route('admin.attendance') }}?subject={{ $course->id }}" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded text-xs flex items-center gap-1.5 transition">
                <i class="bi bi-calendar-check"></i> View Attendance
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function deleteCourse(id) {
        if (confirm('Are you sure you want to delete this course?')) {
            fetch(`/admin/courses/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = '{{ route("admin.courses") }}';
                } else {
                    alert(data.message || 'Error deleting course');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting course');
            });
        }
    }
</script>
@endsection



