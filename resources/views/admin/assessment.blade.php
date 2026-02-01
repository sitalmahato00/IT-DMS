@extends('admin.layouts.app')

@section('title', 'Assessment')

@section('content')
<div class="space-y-4">
    <!-- Stats Cards -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stats-card title="Total Assessments" value="14" icon="bi-clipboard-check" color="blue" />
        <x-stats-card title="Published" value="9" icon="bi-check-circle" color="green" />
        <x-stats-card title="Draft" value="5" icon="bi-exclamation-circle" color="yellow" />
        <x-stats-card title="Total Questions" value="127" icon="bi-question-circle" color="purple" />
    </div>

    <!-- Filters & Search -->
    <x-card>
        <h3 class="text-sm font-semibold text-gray-900 mb-2">Search & Filter</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year</label>
                <select class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Years</option>
                    <option value="2023-24">2023-2024</option>
                    <option value="2024-25">2024-2025</option>
                    <option value="2025-26">2025-2026</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                <select class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Semesters</option>
                    <option value="first">First</option>
                    <option value="second">Second</option>
                    <option value="third">Third</option>
                    <option value="fourth">Fourth</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                <select class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Courses</option>
                    <option value="physics">Physics</option>
                    <option value="chemistry">Chemistry</option>
                    <option value="biology">Biology</option>
                    <option value="data">Data Structures</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>
    </x-card>

    <!-- Assessment List Table -->
    <div class="bg-white rounded shadow-sm border border-gray-200">
        <div class="p-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Assessment List</h3>
            <div class="flex gap-1">
                <button class="flex items-center gap-1 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100 rounded transition">
                    <i class="bi bi-download text-xs"></i>
                    <span>Export</span>
                </button>
                <button class="flex items-center gap-1 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100 rounded transition">
                    <i class="bi bi-funnel text-xs"></i>
                    <span>Filter</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <x-table :headers="['Assessment Name','Academic Year','Semester','Course','Type','Total Marks','Status','Actions']">
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">Data Structures Midterm</td>
                        <td class="px-3 py-2 text-gray-700">2024-2025</td>
                        <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                        <td class="px-3 py-2 text-gray-700">Data Structures</td>
                        <td class="px-3 py-2 text-gray-700">Midterm</td>
                        <td class="px-3 py-2 text-gray-700">100</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <button onclick="openViewAssessmentModal('Data Structures Midterm', '2024-2025', 'Fall 2024', 'Data Structures', 'Midterm', '100', '40', 'Published')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="bi bi-eye text-xs"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">Database 1st Internal</td>
                        <td class="px-3 py-2 text-gray-700">2024-2025</td>
                        <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                        <td class="px-3 py-2 text-gray-700">DBMS</td>
                        <td class="px-3 py-2 text-gray-700">1st Internal</td>
                        <td class="px-3 py-2 text-gray-700">50</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">Draft</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <button onclick="openViewAssessmentModal('Database 1st Internal', '2024-2025', 'Fall 2024', 'DBMS', '1st Internal', '50', '20', 'Draft')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="bi bi-eye text-xs"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">Web Dev Practical</td>
                        <td class="px-3 py-2 text-gray-700">2024-2025</td>
                        <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                        <td class="px-3 py-2 text-gray-700">Web Development</td>
                        <td class="px-3 py-2 text-gray-700">Practical</td>
                        <td class="px-3 py-2 text-gray-700">50</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <button onclick="openViewAssessmentModal('Web Dev Practical', '2024-2025', 'Fall 2024', 'Web Development', 'Practical', '50', '25', 'Published')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="bi bi-eye text-xs"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">Machine Learning Final</td>
                        <td class="px-3 py-2 text-gray-700">2023-2024</td>
                        <td class="px-3 py-2 text-gray-700">Spring 2024</td>
                        <td class="px-3 py-2 text-gray-700">Machine Learning</td>
                        <td class="px-3 py-2 text-gray-700">Final</td>
                        <td class="px-3 py-2 text-gray-700">100</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <button onclick="openViewAssessmentModal('Machine Learning Final', '2023-2024', 'Spring 2024', 'Machine Learning', 'Final', '100', '40', 'Published')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="bi bi-eye text-xs"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">Algorithm Analysis Viva</td>
                        <td class="px-3 py-2 text-gray-700">2024-2025</td>
                        <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                        <td class="px-3 py-2 text-gray-700">Data Structures</td>
                        <td class="px-3 py-2 text-gray-700">Viva</td>
                        <td class="px-3 py-2 text-gray-700">20</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">Draft</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <button onclick="openViewAssessmentModal('Algorithm Analysis Viva', '2024-2025', 'Fall 2024', 'Data Structures', 'Viva', '20', '10', 'Draft')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="bi bi-eye text-xs"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
            </x-table>
        </div>

        <div class="px-3 py-2 border-t border-gray-200 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
                <span>Show</span>
                <select class="px-2 py-0.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <span>entries</span>
            </div>
            <span>Showing 1 to 5 of 14 entries</span>
        </div>
    </div>
</div>

<!-- Create Assessment Modal - Compact Size -->
<div id="addAssessmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 flex items-center justify-between z-10">
            <div>
                <h2 class="text-sm font-bold">Create Assessment</h2>
                <p class="text-red-100 text-xs mt-0.5">Define and manage academic assessments for semesters and courses</p>
            </div>
            <button onclick="closeAddAssessmentModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Form Content -->
        <div class="overflow-y-auto flex-1">
            <form class="p-3 space-y-3">
                <!-- Academic Information -->
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Academic Information</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">Select Academic Year</option>
                                <option value="2023-24">2023-2024</option>
                                <option value="2024-25">2024-2025</option>
                                <option value="2025-26">2025-2026</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">Select Semester</option>
                                <option value="first">First</option>
                                <option value="second">Second</option>
                                <option value="third">Third</option>
                                <option value="fourth">Fourth</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Department/Program *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">Select Department</option>
                                <option value="cse">Computer Science</option>
                                <option value="ece">Electronics</option>
                                <option value="civil">Civil Engineering</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Course *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">Select Course</option>
                                <option value="physics">Physics</option>
                                <option value="chemistry">Chemistry</option>
                                <option value="biology">Biology</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Assessment Details -->
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Assessment Details</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Assessment Type *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">Select Assessment Type</option>
                                <option value="midterm">Midterm</option>
                                <option value="final">Final</option>
                                <option value="internal">Internal</option>
                                <option value="practical">Practical</option>
                                <option value="viva">Viva</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Assessment Name *</label>
                            <input type="text" placeholder="Enter assessment name" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                            <input type="number" placeholder="Enter total marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                            <input type="number" placeholder="Enter passing marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Assessment Date (BS) *</label>
                            <input type="text" name="assessment_date_bs" placeholder="YYYY-MM-DD (BS)" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500 bs-date">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Additional Information</h3>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description/Instructions</label>
                        <textarea placeholder="Enter assessment description and instructions..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500 h-16"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 p-3 border-t border-gray-200 flex gap-2">
            <button onclick="closeAddAssessmentModal()" class="flex-1 px-2 py-1.5 text-gray-700 font-medium text-xs border border-gray-300 rounded hover:bg-gray-100">
                Cancel
            </button>
            <button class="flex-1 px-2 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded">
                Create Assessment
            </button>
        </div>
    </div>
</div>

<!-- View Assessment Modal - Attractive Card -->
<div id="viewAssessmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-md w-full">
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-red-600 to-orange-500 p-4 pb-12">
            <button onclick="closeViewAssessmentModal()" class="absolute top-2 right-2 text-white hover:bg-white hover:bg-opacity-20 p-1 rounded">
                <i class="bi bi-x-lg text-sm"></i>
            </button>

            <div class="flex items-end gap-3">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow border-2 border-white">
                    <i class="bi bi-clipboard-check text-2xl text-red-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white" id="viewAssessmentName">Assessment Name</h2>
                    <p class="text-red-100 text-xs" id="viewAssessmentType">Type</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="relative p-4 -mt-6">
            <div class="bg-white rounded shadow p-4 space-y-3">
                <!-- Academic Information -->
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-blue-100 rounded flex items-center justify-center">
                            <i class="bi bi-calendar-event text-blue-600 text-xs"></i>
                        </span>
                        Academic Information
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Academic Year:</span> <span class="font-medium text-gray-900" id="viewAssessmentYear">2024-2025</span></p>
                        <p><span class="text-gray-600">Semester:</span> <span class="font-medium text-gray-900" id="viewAssessmentSemester">Fall 2024</span></p>
                    </div>
                </div>

                <!-- Course Information -->
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-green-100 rounded flex items-center justify-center">
                            <i class="bi bi-book text-green-600 text-xs"></i>
                        </span>
                        Course Information
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Course:</span> <span class="font-medium text-gray-900" id="viewAssessmentCourse">Course Name</span></p>
                    </div>
                </div>

                <!-- Assessment Marks -->
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-purple-100 rounded flex items-center justify-center">
                            <i class="bi bi-percent text-purple-600 text-xs"></i>
                        </span>
                        Assessment Marks
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Total Marks:</span> <span class="font-medium text-gray-900" id="viewAssessmentMarks">100</span></p>
                        <p><span class="text-gray-600">Passing Marks:</span> <span class="font-medium text-gray-900" id="viewAssessmentPassing">40</span></p>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-yellow-100 rounded flex items-center justify-center">
                            <i class="bi bi-circle-fill text-yellow-600 text-xs"></i>
                        </span>
                        Status
                    </h3>
                    <div class="text-xs">
                        <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium" id="viewAssessmentStatus">Published</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 flex gap-2">
            <button onclick="closeViewAssessmentModal()" class="flex-1 px-2 py-1 text-gray-700 font-medium text-xs border border-gray-300 rounded hover:bg-gray-50">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openAddAssessmentModal() {
        document.getElementById('addAssessmentModal').classList.remove('hidden');
    }

    function closeAddAssessmentModal() {
        document.getElementById('addAssessmentModal').classList.add('hidden');
    }

    function openViewAssessmentModal(name, year, semester, course, type, marks, passing, status) {
        document.getElementById('viewAssessmentName').textContent = name;
        document.getElementById('viewAssessmentType').textContent = type;
        document.getElementById('viewAssessmentYear').textContent = year;
        document.getElementById('viewAssessmentSemester').textContent = semester;
        document.getElementById('viewAssessmentCourse').textContent = course;
        document.getElementById('viewAssessmentMarks').textContent = marks;
        document.getElementById('viewAssessmentPassing').textContent = passing;
        document.getElementById('viewAssessmentStatus').textContent = status;
        
        // Update badge color based on status
        const badge = document.getElementById('viewAssessmentStatus');
        if (status === 'Published') {
            badge.className = 'inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium';
        } else if (status === 'Draft') {
            badge.className = 'inline-block px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-medium';
        } else {
            badge.className = 'inline-block px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium';
        }
        
        document.getElementById('viewAssessmentModal').classList.remove('hidden');
    }

    function closeViewAssessmentModal() {
        document.getElementById('viewAssessmentModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('addAssessmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddAssessmentModal();
        }
    });

    document.getElementById('viewAssessmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewAssessmentModal();
        }
    });
</script>
