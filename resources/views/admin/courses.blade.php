@extends('admin.layouts.app')

@section('title', 'Courses')

@section('content')
<div class="space-y-4">
    <!-- Stats Cards - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <x-stats-card title="Total Courses" value="{{ $stats['total'] }}" icon="bi bi-book" color="blue" />
        <x-stats-card title="Active" value="{{ $stats['active'] }}" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="Archived" value="{{ $stats['archived'] }}" icon="bi bi-archive" color="yellow" />
    </div>

    <!-- Filters & Actions - Row 2 -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <form id="coursesFilterForm" action="{{ route('admin.courses') }}" method="GET" class="flex gap-2 items-center">
            <input type="text" name="q" id="coursesSearch" placeholder="Search course or code..." value="{{ $search }}" class="w-48 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500" />
            <select name="status" id="coursesStatusFilter" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Status</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="archived" {{ $status == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <select name="semester" id="coursesSemesterFilter" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Semesters</option>
                <option value="1" {{ (isset($semester) && $semester == '1') ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ (isset($semester) && $semester == '2') ? 'selected' : '' }}>2nd Semester</option>
                <option value="3" {{ (isset($semester) && $semester == '3') ? 'selected' : '' }}>3rd Semester</option>
                <option value="4" {{ (isset($semester) && $semester == '4') ? 'selected' : '' }}>4th Semester</option>
                <option value="5" {{ (isset($semester) && $semester == '5') ? 'selected' : '' }}>5th Semester</option>
                <option value="6" {{ (isset($semester) && $semester == '6') ? 'selected' : '' }}>6th Semester</option>
            </select>
            <select name="category" id="coursesCategoryFilter" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Categories</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ $category == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium">
                <i class="bi bi-search mr-1"></i>Search
            </button>
            <button type="button" onclick="resetCoursesFilter()" class="px-3 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-xs font-medium" title="Reset Filters">
                <i class="bi bi-arrow-clockwise mr-1"></i>Reset
            </button>
        </form>

        <button onclick="openAddCourseModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
            <i class="bi bi-plus-lg"></i>
            <span>Add Course</span>
        </button>
    </div>

    <x-card>
        <div id="coursesTableContainer">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Course Name</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Code</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Semester</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Category</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Credits</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Teacher</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Status</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2">
                            <div>
                                <p class="text-gray-900 font-medium">{{ $course->subject_name }}</p>
                                <p class="text-gray-600 text-xs">{{ $course->subject_code }}</p>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-gray-700">{{ $course->subject_code }}</td>
                        <td class="px-3 py-2 text-gray-700">
                            @if($course->semester)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $course->semester }}{{ $course->semester == 1 ? 'st' : ($course->semester == 2 ? 'nd' : ($course->semester == 3 ? 'rd' : 'th')) }} Sem
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-700">{{ $course->category ?? 'General' }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ $course->credits ?? 3 }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ $course->teacher_name ?? 'Not Assigned' }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                @if(($course->status ?? 'active') == 'active') bg-green-100 text-green-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ ucfirst($course->status ?? 'Active') }}
                            </span>
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-1">
                                <button onclick="editCourse({{ $course->id }})" class="p-1 text-blue-600 hover:bg-blue-100 rounded" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button onclick="deleteCourse({{ $course->id }})" class="p-1 text-red-600 hover:bg-red-100 rounded" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-gray-600">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="bi bi-inbox text-4xl text-gray-400 mb-2"></i>
                                <p>No courses found</p>
                                <p class="text-xs text-gray-500">Add a new course to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>

<!-- Add/Edit Course Modal -->
<div id="courseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-2xl w-full p-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-3">
            <h2 id="courseModalTitle" class="text-sm font-bold">Add Course</h2>
            <button onclick="closeCourseModal()" class="text-gray-600 hover:text-gray-900"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="courseForm" action="{{ route('admin.courses.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="courseId">
            
            <!-- Basic Information -->
            <div class="mb-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-2 pb-1 border-b">Basic Information</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Course Name *</label>
                        <input type="text" name="subject_name" id="courseName" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="e.g., Data Structures">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Course Code *</label>
                        <input type="text" name="subject_code" id="courseCode" required class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500" placeholder="e.g., CS-301">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Credits</label>
                        <input type="number" name="credits" id="courseCredits" value="3" min="1" max="10" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" id="courseSemester" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">All Semesters</option>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                            <option value="3">3rd Semester</option>
                            <option value="4">4th Semester</option>
                            <option value="5">5th Semester</option>
                            <option value="6">6th Semester</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" id="courseCategory" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">Select Category</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Teacher</label>
                        <select name="teacher_id" id="courseTeacher" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                            <option value="">Not Assigned</option>
                            @foreach($allTeachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
            </div>

            <!-- Assessment Pattern -->
            <div class="mb-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-2 pb-1 border-b">Assessment Pattern</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Theory (%)</label>
                        <input type="number" name="theory_percentage" id="courseTheory" value="70" min="0" max="100" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Practical (%)</label>
                        <input type="number" name="practical_percentage" id="coursePractical" value="30" min="0" max="100" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Internal (%)</label>
                        <input type="number" name="internal_percentage" id="courseInternal" value="40" min="0" max="100" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">External (%)</label>
                        <input type="number" name="external_percentage" id="courseExternal" value="60" min="0" max="100" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
            </div>

            <!-- Teaching Hours -->
            <div class="mb-4">
                <h3 class="text-xs font-semibold text-gray-900 mb-2 pb-1 border-b">Teaching Hours (per week)</h3>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Lectures (hrs)</label>
                        <input type="number" name="lecture_hours" id="courseLectureHours" value="4" min="0" max="10" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Practicals (hrs)</label>
                        <input type="number" name="practical_hours" id="coursePracticalHours" value="2" min="0" max="10" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tutorials (hrs)</label>
                        <input type="number" name="tutorial_hours" id="courseTutorialHours" value="1" min="0" max="10" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="courseStatus" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeCourseModal()" class="px-3 py-1.5 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded text-xs">Cancel</button>
                <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs">Save Course</button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
<script>
    const COURSE_FORM_VERSION = '2.4';
    
    function openAddCourseModal() {
        document.getElementById('courseModalTitle').textContent = 'Add Course';
        document.getElementById('courseForm').reset();
        document.getElementById('courseId').value = '';
        document.getElementById('courseForm').action = '{{ route("admin.courses.store") }}';
        // Set default values
        document.getElementById('courseTheory').value = 70;
        document.getElementById('coursePractical').value = 30;
        document.getElementById('courseInternal').value = 40;
        document.getElementById('courseExternal').value = 60;
        document.getElementById('courseLectureHours').value = 4;
        document.getElementById('coursePracticalHours').value = 2;
        document.getElementById('courseTutorialHours').value = 1;
        document.getElementById('courseCredits').value = 3;
        document.getElementById('courseTeacher').value = '';
        document.getElementById('courseModal').classList.remove('hidden');
    }

    function closeCourseModal() {
        document.getElementById('courseModal').classList.add('hidden');
    }

    function editCourse(id) {
        document.getElementById('courseModalTitle').textContent = 'Edit Course';
        
        // Show modal immediately
        document.getElementById('courseModal').classList.remove('hidden');
        
        // Store the course ID for form submission
        document.getElementById('courseId').value = id;
        document.getElementById('courseForm').action = `/admin/courses/${id}`;
        
        // Reset form first
        document.getElementById('courseForm').reset();
        
        // Fetch course data
        fetch(`/admin/courses/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const course = data.course;
                    
                    // Populate all form fields with course data
                    document.getElementById('courseName').value = course.subject_name || '';
                    document.getElementById('courseCode').value = course.subject_code || '';
                    document.getElementById('courseCredits').value = (course.credits !== null && course.credits !== undefined) ? course.credits : 3;
                    document.getElementById('courseSemester').value = course.semester || '';
                    document.getElementById('courseCategory').value = course.category || '';
                    document.getElementById('courseTeacher').value = course.teacher_id || '';
                    document.getElementById('courseTheory').value = (course.theory_percentage !== null && course.theory_percentage !== undefined) ? course.theory_percentage : 70;
                    document.getElementById('coursePractical').value = (course.practical_percentage !== null && course.practical_percentage !== undefined) ? course.practical_percentage : 30;
                    document.getElementById('courseInternal').value = (course.internal_percentage !== null && course.internal_percentage !== undefined) ? course.internal_percentage : 40;
                    document.getElementById('courseExternal').value = (course.external_percentage !== null && course.external_percentage !== undefined) ? course.external_percentage : 60;
                    document.getElementById('courseLectureHours').value = (course.lecture_hours !== null && course.lecture_hours !== undefined) ? course.lecture_hours : 4;
                    document.getElementById('coursePracticalHours').value = (course.practical_hours !== null && course.practical_hours !== undefined) ? course.practical_hours : 2;
                    document.getElementById('courseTutorialHours').value = (course.tutorial_hours !== null && course.tutorial_hours !== undefined) ? course.tutorial_hours : 1;
                    document.getElementById('courseStatus').value = course.status || 'active';
                } else {
                    alert('Error loading course data');
                    closeCourseModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading course data');
                closeCourseModal();
            });
    }

    // Close modal on outside click
    document.getElementById('courseModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCourseModal();
        }
    });

    // Form submission via AJAX
    document.getElementById('courseForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const courseId = document.getElementById('courseId').value;
        
        // Build URL-encoded data from form (always use POST with method spoofing)
        const formData = new URLSearchParams();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('subject_name', document.getElementById('courseName').value);
        formData.append('subject_code', document.getElementById('courseCode').value);
        formData.append('credits', document.getElementById('courseCredits').value);
        formData.append('semester', document.getElementById('courseSemester').value);
        formData.append('category', document.getElementById('courseCategory').value);
        formData.append('theory_percentage', document.getElementById('courseTheory').value);
        formData.append('practical_percentage', document.getElementById('coursePractical').value);
        formData.append('internal_percentage', document.getElementById('courseInternal').value);
        formData.append('external_percentage', document.getElementById('courseExternal').value);
        formData.append('lecture_hours', document.getElementById('courseLectureHours').value);
        formData.append('practical_hours', document.getElementById('coursePracticalHours').value);
        formData.append('tutorial_hours', document.getElementById('courseTutorialHours').value);
        formData.append('teacher_id', document.getElementById('courseTeacher').value);
        formData.append('status', document.getElementById('courseStatus').value);
        
        // Determine URL and add _method spoofing for updates
        let url = '{{ route("admin.courses.store") }}';
        
        if (courseId) {
            url = `/admin/courses/${courseId}`;
            // Use method spoofing - POST with _method=PATCH
            formData.append('_method', 'PATCH');
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeCourseModal();
                location.reload();
            } else {
                alert(data.message || 'Error saving course');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving course');
        });
    });

    // Delete course handler (also used on the listing page)
    function deleteCourse(id) {
        if (!id) return;
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
                    // Refresh listing
                    location.reload();
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
    
    // Reset filter function
    function resetCoursesFilter() {
        // Clear all filter inputs
        document.getElementById('coursesSearch').value = '';
        document.getElementById('coursesStatusFilter').value = '';
        document.getElementById('coursesSemesterFilter').value = '';
        document.getElementById('coursesCategoryFilter').value = '';
        
        // Submit the form to refresh with cleared filters
        document.getElementById('coursesFilterForm').submit();
    }
</script>
@endsection
