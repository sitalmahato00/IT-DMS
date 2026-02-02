                <!-- Add Mark Modal (Professional, like Add Exam) -->
                <div id="addMarkModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
                        <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Add New Mark</h3>
                            <button onclick="closeAddMarkModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <form class="px-5 py-4 space-y-4">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year *</label>
                                    <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Select Year</option>
                                        <option value="2023-24">2023-2024</option>
                                        <option value="2024-25">2024-2025</option>
                                        <option value="2025-26">2025-2026</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester *</label>
                                    <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Select Semester</option>
                                        <option value="first">First</option>
                                        <option value="second">Second</option>
                                        <option value="third">Third</option>
                                        <option value="fourth">Fourth</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Course *</label>
                                    <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Select Course</option>
                                        <option value="physics">Physics</option>
                                        <option value="chemistry">Chemistry</option>
                                        <option value="biology">Biology</option>
                                        <option value="cs">Computer Science</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject *</label>
                                    <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Select Subject</option>
                                        <option value="dbms">DBMS</option>
                                        <option value="webdev">Web Development</option>
                                        <option value="ml">Machine Learning</option>
                                        <option value="algorithms">Algorithms</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Type *</label>
                                    <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">Select Exam Type</option>
                                        <option value="midterm">Midterm</option>
                                        <option value="final">Final</option>
                                        <option value="internal">Internal</option>
                                        <option value="practical">Practical</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
                                    <input type="text" placeholder="Enter exam name" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks *</label>
                                    <input type="number" placeholder="Enter total marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Passing Marks *</label>
                                    <input type="number" placeholder="Enter passing marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (BS) *</label>
                                    <input type="text" name="exam_date_bs" placeholder="YYYY-MM-DD (BS)" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bs-date">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Status *</label>
                                    <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Additional Information</h3>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Description/Instructions</label>
                                    <textarea placeholder="Enter mark entry description and instructions..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 h-16"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" onclick="closeAddMarkModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Add Mark</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                function openAddMarkModal() {
                    // Only show modal if Mark Management tab is active
                    var marksTab = document.getElementById('sectionMarks');
                    if (marksTab && !marksTab.classList.contains('hidden')) {
                        document.getElementById('addMarkModal').classList.remove('hidden');
                    }
                }
                function closeAddMarkModal() {
                    document.getElementById('addMarkModal').classList.add('hidden');
                }
                </script>
        <!-- Mark Upload Modal (Prefilled Exam Info, Student Filter, Mark Entry Table) -->
        <div id="markUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
                <div class="flex justify-between items-center px-5 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Upload Marks</h3>
                    <button onclick="closeMarkUploadModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form class="px-5 py-4 space-y-4">
                    <div class="space-y-2">
                        <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Exam Information</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name</label>
                                <input type="text" name="exam_name" id="markExamName" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year</label>
                                <input type="text" name="year" id="markYear" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                                <input type="text" name="semester" id="markSemester" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                                <input type="text" name="course" id="markCourse" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                                <input type="text" name="category" id="markCategory" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Full Mark</label>
                                <input type="number" name="full_mark" id="markFullMark" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pass Mark</label>
                                <input type="number" name="pass_mark" id="markPassMark" class="w-full px-3 py-2 border border-gray-300 rounded text-xs bg-gray-100" readonly>
                            </div>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded mt-4">Filter Students</h3>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Batch</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <option value="Fall 2024">Fall 2024</option>
                                    <option value="Spring 2025">Spring 2025</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <option value="DBMS">DBMS</option>
                                    <option value="Web Development">Web Development</option>
                                </select>
                            </div>
                        </div>
                        <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded mt-4">Enter Marks</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs border border-gray-200 rounded">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-1 border-b">Student ID</th>
                                        <th class="px-2 py-1 border-b">Name</th>
                                        <th class="px-2 py-1 border-b">Subject</th>
                                        <th class="px-2 py-1 border-b">Obtained Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-2 py-1 border-b">CS001</td>
                                        <td class="px-2 py-1 border-b">John Smith</td>
                                        <td class="px-2 py-1 border-b">DBMS</td>
                                        <td class="px-2 py-1 border-b">
                                            <input type="number" name="marks[CS001]" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-1 border-b">CS002</td>
                                        <td class="px-2 py-1 border-b">Emily Johnson</td>
                                        <td class="px-2 py-1 border-b">DBMS</td>
                                        <td class="px-2 py-1 border-b">
                                            <input type="number" name="marks[CS002]" class="w-20 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="0">
                                        </td>
                                    </tr>
                                    <!-- More students dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeMarkUploadModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Upload</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        function openMarkUploadModal() {
            document.getElementById('markUploadModal').classList.remove('hidden');
        }
        function closeMarkUploadModal() {
            document.getElementById('markUploadModal').classList.add('hidden');
        }
        function openMarkUploadModalWithData(exam, year, semester, course, category, fullMark, passMark) {
            document.getElementById('markExamName').value = exam;
            document.getElementById('markYear').value = year;
            document.getElementById('markSemester').value = semester;
            document.getElementById('markCourse').value = course;
            document.getElementById('markCategory').value = category;
            document.getElementById('markFullMark').value = fullMark;
            document.getElementById('markPassMark').value = passMark;
            openMarkUploadModal();
        }
        // Tab switching for marks
        function switchMarksTab(tab) {
            ['theory','practical'].forEach(function(t) {
                document.getElementById(t+'-tab').classList.add('hidden');
            });
            document.getElementById(tab+'-tab').classList.remove('hidden');
            document.querySelectorAll('.marks-tab-button').forEach(function(btn) {
                btn.classList.remove('active','text-gray-900','border-red-600','hover:text-red-600');
                btn.classList.add('text-gray-600','border-transparent','hover:text-gray-900');
            });
            var idx = {'theory':0,'practical':1}[tab];
            document.querySelectorAll('.marks-tab-button')[idx].classList.add('active','text-gray-900','border-red-600','hover:text-red-600');
            document.querySelectorAll('.marks-tab-button')[idx].classList.remove('text-gray-600','border-transparent','hover:text-gray-900');
        }
        </script>
@extends('admin.layouts.app')

@section('title', 'Exam')

@section('content')
<div class="space-y-4">
    <!-- Tabs for Exam and Mark Management -->
    <div class="flex gap-2 mb-4">
        <button id="tabExam" class="tab-button px-4 py-2 text-xs font-medium text-gray-900 border-b-2 border-red-600 hover:text-red-600 transition">Exam</button>
        <button id="tabMarks" class="tab-button px-4 py-2 text-xs font-medium text-gray-900 border-b-2 border-transparent hover:text-red-600 transition">Mark Management</button>
    </div>
    
    <div id="sectionExam">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <x-stats-card title="Total Exams" value="14" icon="bi-clipboard-check" color="blue" />
            <x-stats-card title="Published" value="9" icon="bi-check-circle" color="green" />
            <x-stats-card title="Draft" value="5" icon="bi-exclamation-circle" color="yellow" />
            <x-stats-card title="Total Questions" value="127" icon="bi-question-circle" color="purple" />
        </div>

        <!-- Filters & Search -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Search & Filter Exams</h3>
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
            <div class="flex gap-2 mt-2">
                <button class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300" onclick="resetAssessmentFilters()">Reset</button>
            </div>
        </x-card>

        <!-- Assessment List Table -->
        <div class="bg-white rounded shadow-sm border border-gray-200">
            <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Exam List</h3>
                <div class="flex gap-2">
                    <button class="flex items-center gap-1 px-2 py-1 text-xs text-gray-700 hover:bg-gray-100 rounded transition">
                        <i class="bi bi-download text-xs"></i>
                        <span>Export</span>
                    </button>
                    <button id="btnAddNewExam" class="flex items-center gap-1 px-2 py-1 text-xs text-white bg-red-600 hover:bg-red-700 rounded transition">
                        <i class="bi bi-plus-circle text-xs"></i>
                        <span>Add New Exam</span>
                    </button>
                    <button class="flex items-center gap-1 px-2 py-1 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded transition" onclick="openMarkUploadModal()">
                        <i class="bi bi-upload text-xs"></i>
                        <span>Upload Marks</span>
                    </button>
                </div>
            </div>
            
            <x-table>
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Exam Name</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Year</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Semester</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Course</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Type</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Total Marks</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Status</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">Database 1st Internal</td>
                        <td class="px-3 py-2 text-gray-700">2024-2025</td>
                        <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                        <td class="px-3 py-2 text-gray-700">DBMS</td>
                        <td class="px-3 py-2 text-gray-700">1st Internal</td>
                        <td class="px-3 py-2 text-gray-700">50</td>
                        <td class="px-3 py-2">
                            <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <button onclick="openViewAssessmentModal('Database 1st Internal', '2024-2025', 'Fall 2024', 'DBMS', '1st Internal', '50', '20', 'Published')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                    <i class="bi bi-eye text-xs"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                    <i class="bi bi-pencil text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openMarkUploadModalWithData('Database 1st Internal','2024-2025','Fall 2024','DBMS','1st Internal',50,20)">
                                    <i class="bi bi-upload text-xs"></i> Upload
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
                                <button class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openMarkUploadModalWithData('Web Dev Practical','2024-2025','Fall 2024','Web Development','Practical',50,25)">
                                    <i class="bi bi-upload text-xs"></i> Upload
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
                                <button class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openMarkUploadModalWithData('Machine Learning Final','2023-2024','Spring 2024','Machine Learning','Final',100,40)">
                                    <i class="bi bi-upload text-xs"></i> Upload
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
                                <button class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openMarkUploadModalWithData('Algorithm Analysis Viva','2024-2025','Fall 2024','Data Structures','Viva',20,10)">
                                    <i class="bi bi-upload text-xs"></i> Upload
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </x-table>
            
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
                <span>Showing 1 to 4 of 14 entries</span>
            </div>
        </div>
    </div>

    <div id="sectionMarks" class="hidden">
        <!-- Stats Cards - Mark Management -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <x-stats-card title="Total Students" value="156" icon="bi bi-people-fill" color="blue" />
            <x-stats-card title="Marks Submitted" value="142" icon="bi bi-check-circle" color="green" />
            <x-stats-card title="Pending" value="14" icon="bi bi-exclamation-circle" color="yellow" />
            <x-stats-card title="Average Score" value="75.5%" icon="bi bi-percent" color="purple" />
        </div>

            <!-- Add Mark Button -->
            <div class="flex justify-end my-3">
                <button class="flex items-center gap-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium" onclick="openAddMarkModal()">
                    <i class="bi bi-plus-circle text-xs"></i>
                    <span>Add New Mark Upload</span>
                </button>
            </div>

        <!-- Filters & Search for Marks -->
        <x-card>
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Filter Marks</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Academic Year</label>
                    <select id="marksYearFilter" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Years</option>
                        <option value="2023-24">2023-2024</option>
                        <option value="2024-25">2024-2025</option>
                        <option value="2025-26">2025-2026</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                    <select id="marksSemesterFilter" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Semesters</option>
                        <option value="first">First</option>
                        <option value="second">Second</option>
                        <option value="third">Third</option>
                        <option value="fourth">Fourth</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                    <select id="marksCourseFilter" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Courses</option>
                        <option value="physics">Physics</option>
                        <option value="chemistry">Chemistry</option>
                        <option value="biology">Biology</option>
                        <option value="cs">Computer Science</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                    <select id="marksSubjectFilter" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Subjects</option>
                        <option value="dbms">DBMS</option>
                        <option value="webdev">Web Development</option>
                        <option value="ml">Machine Learning</option>
                        <option value="algorithms">Algorithms</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select id="marksStatusFilter" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Status</option>
                        <option value="submitted">Submitted</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <input type="text" id="marksSearchInput" placeholder="Search by Roll No or Student Name..." class="flex-1 px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <button onclick="applyMarksFilters()" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium flex items-center gap-1.5 transition">
                    <i class="bi bi-search text-xs"></i>
                    <span>Search</span>
                </button>
                <button onclick="resetMarksFilters()" class="px-3 py-1.5 text-gray-700 font-medium text-xs border border-gray-300 rounded hover:bg-gray-100">
                    Reset
                </button>
            </div>
        </x-card>

        <!-- Mark Management Tabs -->
        <div class="bg-white rounded shadow-sm border border-gray-200">
            <div class="flex border-b border-gray-200">
                <button onclick="switchMarksTab('theory')" class="marks-tab-button active px-4 py-2 text-xs font-medium text-gray-900 border-b-2 border-red-600 hover:text-red-600 transition">
                    Theory
                </button>
                <button onclick="switchMarksTab('practical')" class="marks-tab-button px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition">
                    Practical
                </button>
            </div>
            <!-- Theory Tab Content -->
            <div id="theory-tab" class="marks-tab-content">
                <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Theory Marks</h3>
                        <p class="text-gray-600 text-xs mt-1">View and manage theory marks uploads</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <x-table>
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Exam Name</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Year</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Semester</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Course</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Type</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Total Marks</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Status</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example row, update with dynamic data as needed -->
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">Database 1st Internal</td>
                                <td class="px-3 py-2 text-gray-700">2024-2025</td>
                                <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                                <td class="px-3 py-2 text-gray-700">DBMS</td>
                                <td class="px-3 py-2 text-gray-700">Theory</td>
                                <td class="px-3 py-2 text-gray-700">50</td>
                                <td class="px-3 py-2">
                                    <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Published</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex gap-1 justify-center">
                                        <button onclick="openViewAssessmentModal('Database 1st Internal', '2024-2025', 'Fall 2024', 'DBMS', 'Theory', '50', '20', 'Published')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                            <i class="bi bi-eye text-xs"></i>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                            <i class="bi bi-pencil text-xs"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded hover:bg-red-50">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                        <button class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openMarkUploadModalWithData('Database 1st Internal','2024-2025','Fall 2024','DBMS','Theory',50,20)">
                                            <i class="bi bi-upload text-xs"></i> Upload
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </x-table>
                </div>
            </div>
            <!-- Practical Tab Content -->
            <div id="practical-tab" class="marks-tab-content hidden">
                <div class="p-3 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Practical Marks</h3>
                        <p class="text-gray-600 text-xs mt-1">View and manage practical marks uploads</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <x-table>
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Exam Name</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Year</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Semester</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Course</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Type</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Total Marks</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-left">Status</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-700 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example row, update with dynamic data as needed -->
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">Web Dev Practical</td>
                                <td class="px-3 py-2 text-gray-700">2024-2025</td>
                                <td class="px-3 py-2 text-gray-700">Fall 2024</td>
                                <td class="px-3 py-2 text-gray-700">Web Development</td>
                                <td class="px-3 py-2 text-gray-700">Practical</td>
                                <td class="px-3 py-2 text-gray-700">50</td>
                                <td class="px-3 py-2">
                                    <span class="inline-block px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Uploaded</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="flex gap-1 justify-center">
                                        <button onclick="openViewMarksModal('Web Dev Practical', '2024-2025', 'Fall 2024', 'Web Development', '50', 'Uploaded', 'Practical')" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                            <i class="bi bi-eye text-xs"></i>
                                        </button>
                                        <button onclick="openMarkUploadModalWithData('Web Dev Practical','2024-2025','Fall 2024','Web Development','Practical',50,25)" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50">
                                            <i class="bi bi-upload text-xs"></i> Upload
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </x-table>
                </div>
            </div>

    <!-- Edit Marks Modal -->
    <div id="editMarksModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded shadow-2xl max-w-lg w-full">
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 flex items-center justify-between z-10">
                <div>
                    <h2 class="text-sm font-bold">Edit Marks</h2>
                    <p class="text-red-100 text-xs mt-0.5" id="editMarksStudentDisplay">Student Name</p>
                </div>
                <button onclick="closeEditMarksModal()" class="text-red-200 hover:text-white">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="overflow-y-auto max-h-[70vh]">
                <form class="p-3 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Roll Number *</label>
                        <input type="text" id="editMarksRollNo" readonly class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs bg-gray-50 focus:outline-none">
                    </div>
                    <div class="border-t pt-3">
                        <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded mb-2">Subject Marks</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Subject 1 *</label>
                                <input type="number" placeholder="Enter marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Subject 2 *</label>
                                <input type="number" placeholder="Enter marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Subject 3 *</label>
                                <input type="number" placeholder="Enter marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Subject 4 *</label>
                                <input type="number" placeholder="Enter marks" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Remarks/Comments</label>
                        <textarea placeholder="Add any remarks..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 h-12"></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 p-3 border-t border-gray-200 flex gap-2">
                <button onclick="closeEditMarksModal()" class="flex-1 px-2 py-1.5 text-gray-700 font-medium text-xs border border-gray-300 rounded hover:bg-gray-100">
                    Cancel
                </button>
                <button class="flex-1 px-2 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded">
                    Update Marks
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Create Exam Modal -->
<div id="addAssessmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 flex items-center justify-between z-10">
            <div>
                <h2 class="text-sm font-bold">Create Exam</h2>
                <p class="text-red-100 text-xs mt-0.5">Define and manage exams for semesters and courses</p>
            </div>
            <button onclick="closeAddAssessmentModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1">
            <form class="p-3 space-y-3">
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
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Exam Details</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Type *</label>
                            <select class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                                <option value="">Select Exam Type</option>
                                <option value="midterm">Midterm</option>
                                <option value="final">Final</option>
                                <option value="internal">Internal</option>
                                <option value="practical">Practical</option>
                                <option value="viva">Viva</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Name *</label>
                            <input type="text" placeholder="Enter exam name" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
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
                            <label class="block text-xs font-medium text-gray-700 mb-1">Exam Date (BS) *</label>
                            <input type="text" name="exam_date_bs" placeholder="YYYY-MM-DD (BS)" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500 bs-date" onclick="showBsCalendar(this)">
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
                <div class="space-y-2">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded">Additional Information</h3>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description/Instructions</label>
                        <textarea placeholder="Enter assessment description and instructions..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500 h-16"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="bg-gray-50 p-3 border-t border-gray-200 flex gap-2">
            <button onclick="closeAddAssessmentModal()" class="flex-1 px-2 py-1.5 text-gray-700 font-medium text-xs border border-gray-300 rounded hover:bg-gray-100">
                Cancel
            </button>
            <button class="flex-1 px-2 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium text-xs rounded">
                Create Exam
            </button>
        </div>
    </div>
</div>

<!-- View Assessment Modal -->
<div id="viewAssessmentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-md w-full">
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
        <div class="relative p-4 -mt-6">
            <div class="bg-white rounded shadow p-4 space-y-3">
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
    // Add event listener for Add New Exam button when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        var addExamBtn = document.getElementById('btnAddNewExam');
        if (addExamBtn) {
            addExamBtn.addEventListener('click', function() {
                document.getElementById('addAssessmentModal').classList.remove('hidden');
            });
        }
        
        var addExamModal = document.getElementById('addAssessmentModal');
        if (addExamModal) {
            addExamModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddAssessmentModal();
                }
            });
        }
        
        var viewAssessmentModal = document.getElementById('viewAssessmentModal');
        if (viewAssessmentModal) {
            viewAssessmentModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeViewAssessmentModal();
                }
            });
        }
    });
    
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

    function openMarkUploadModal() {
        // TODO: Implement mark upload modal
    }

    function openMarkUploadModalWithData(examName, year, semester, course, type, totalMarks, passingMarks) {
        // TODO: Implement mark upload with pre-filled data
    }

    function resetAssessmentFilters() {
        // TODO: Implement filter reset
    }

    // Tab switching functionality
    document.getElementById('tabExam').addEventListener('click', function() {
        document.getElementById('sectionExam').classList.remove('hidden');
        document.getElementById('sectionMarks').classList.add('hidden');
        this.classList.add('border-red-600');
        this.classList.remove('border-transparent');
        document.getElementById('tabMarks').classList.remove('border-red-600');
        document.getElementById('tabMarks').classList.add('border-transparent');
    });

    document.getElementById('tabMarks').addEventListener('click', function() {
        document.getElementById('sectionExam').classList.add('hidden');
        document.getElementById('sectionMarks').classList.remove('hidden');
        this.classList.add('border-red-600');
        this.classList.remove('border-transparent');
        document.getElementById('tabExam').classList.remove('border-red-600');
        document.getElementById('tabExam').classList.add('border-transparent');
    });

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

    // Marks tab switching
    function switchMarksTab(tabName) {
        const tabs = document.querySelectorAll('.marks-tab-content');
        tabs.forEach(tab => tab.classList.add('hidden'));
        const buttons = document.querySelectorAll('.marks-tab-button');
        buttons.forEach(btn => {
            btn.classList.remove('border-b-2', 'border-red-600', 'text-gray-900');
            btn.classList.add('border-transparent', 'text-gray-600');
        });
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        event.target.classList.add('border-b-2', 'border-red-600', 'text-gray-900');
        event.target.classList.remove('border-transparent', 'text-gray-600');
    }

    function openViewMarksModal(rollNo, studentName, s1, s2, s3, s4, obtained, max, type) {
        document.getElementById('viewMarksRollNo').textContent = rollNo;
        document.getElementById('viewMarksStudentName').textContent = studentName;
        document.getElementById('viewMarksSubject1').textContent = s1;
        document.getElementById('viewMarksSubject2').textContent = s2;
        document.getElementById('viewMarksSubject3').textContent = s3;
        document.getElementById('viewMarksSubject4').textContent = s4;
        document.getElementById('viewMarksTotalObtained').textContent = obtained;
        document.getElementById('viewMarksTotalMax').textContent = max;
        document.getElementById('viewMarksType').textContent = type;
        const percentage = (obtained / max) * 100;
        document.getElementById('viewMarksProgressBar').style.width = percentage + '%';
        document.getElementById('viewMarksPercentage').textContent = percentage.toFixed(2) + '%';
        document.getElementById('viewMarksModal').classList.remove('hidden');
    }

    function closeViewMarksModal() {
        document.getElementById('viewMarksModal').classList.add('hidden');
    }

    function openEditMarksModal(rollNo, studentName) {
        document.getElementById('editMarksRollNo').value = rollNo;
        document.getElementById('editMarksStudentDisplay').textContent = studentName;
        document.getElementById('editMarksModal').classList.remove('hidden');
    }

    function closeEditMarksModal() {
        document.getElementById('editMarksModal').classList.add('hidden');
    }

    // Marks filter functions
    function applyMarksFilters() {
        const year = document.getElementById('marksYearFilter').value;
        const semester = document.getElementById('marksSemesterFilter').value;
        const course = document.getElementById('marksCourseFilter').value;
        const subject = document.getElementById('marksSubjectFilter').value;
        const status = document.getElementById('marksStatusFilter').value;
        const search = document.getElementById('marksSearchInput').value;
        
        // Log filter values - in production, this would filter the table data
        console.log('Applying marks filters:', { year, semester, course, subject, status, search });
        
        // Show feedback that filters were applied
        alert('Filters applied! Year: ' + (year || 'All') + ', Semester: ' + (semester || 'All') + ', Course: ' + (course || 'All') + ', Subject: ' + (subject || 'All') + ', Status: ' + (status || 'All') + ', Search: ' + (search || 'None'));
    }

    function resetMarksFilters() {
        document.getElementById('marksYearFilter').value = '';
        document.getElementById('marksSemesterFilter').value = '';
        document.getElementById('marksCourseFilter').value = '';
        document.getElementById('marksSubjectFilter').value = '';
        document.getElementById('marksStatusFilter').value = '';
        document.getElementById('marksSearchInput').value = '';
        
        // Show feedback that filters were reset
        alert('Filters reset!');
    }
</script>
@endsection

