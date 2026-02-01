@extends('admin.layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="space-y-4">
    <!-- Stats Cards - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stats-card title="Total Records" value="{{ $stats['total'] }}" icon="bi bi-list-check" color="blue" />
        <x-stats-card title="Present" value="{{ $stats['present'] }}" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="Absent" value="{{ $stats['absent'] }}" icon="bi bi-x-circle" color="red" />
        <x-stats-card title="Leave" value="{{ $stats['leave'] }}" icon="bi bi-calendar-event" color="purple" />
    </div>

    <!-- Filters & Search - Row 2 -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <form method="GET" action="{{ route('admin.attendance') }}" class="flex items-center gap-2">
            <input type="text" name="date_bs" id="filter_date_bs" value="{{ $date_bs ?? '' }}" placeholder="YYYY-MM-DD (BS)" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500 bs-date">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search student..." class="w-48 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
            <select name="semester" id="filter_semester" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Semesters</option>
                @foreach($semesters as $sem)
                <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>{{ $sem }}{{ $sem == 1 ? 'st' : ($sem == 2 ? 'nd' : ($sem == 3 ? 'rd' : 'th')) }} Semester</option>
                @endforeach
            </select>
            <select name="course" id="filter_course" class="w-48 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                <option value="{{ $c->id }}" data-semester="{{ $c->semester }}" {{ $course == $c->id ? 'selected' : '' }}>{{ $c->subject_code }} - {{ $c->subject_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded text-xs hover:bg-red-700 font-medium">Filter</button>
            <a href="{{ route('admin.attendance') }}" class="px-3 py-2 border border-gray-300 rounded text-xs hover:bg-gray-50 font-medium">Reset</a>
        </form>

        <button onclick="openMarkAttendanceModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
            <i class="bi bi-clipboard-check"></i>
            <span>Mark Attendance</span>
        </button>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded shadow-sm border border-gray-200">
        <div class="p-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">
                Attendance Records
                @if(!empty($date_bs))
                for {{ $date_bs }}
                @endif
            </h3>
            <span class="text-xs text-gray-500">{{ count($attendanceRecords) }} records found</span>
        </div>

        <div class="overflow-x-auto">
            @if(count($attendanceRecords) > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Student Name</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Roll No</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Semester</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Course</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($attendanceRecords as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs text-gray-700">
                            {{ $record['date_bs'] ?? ($record['date'] ?? '') }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="bi bi-person-fill text-xs text-red-600"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $record['name'] ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $record['email'] ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700 text-center">{{ $record['roll_no'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-700 text-center">{{ $record['semester'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-left">
                            @if(!empty($record['subject_name']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="bi bi-book text-xs mr-1"></i>
                                {{ $record['subject_code'] ?? '' }} {{ $record['subject_name'] ?? '' }}
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">General</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap
                                {{ ($record['status'] ?? '') === 'present' ? 'bg-green-100 text-green-700' : 
                                   (($record['status'] ?? '') === 'absent' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ ucfirst($record['status'] ?? '') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="openEditRecordModal('{{ $record['id'] ?? '' }}', '{{ $record['student_id'] ?? '' }}', '{{ addslashes($record['name'] ?? '') }}', '{{ $record['status'] ?? '' }}', '{{ $record['remarks'] ?? '' }}')" 
                                class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                <i class="bi bi-pencil text-xs mr-1"></i> Edit
                            </button>
                            <button onclick="deleteAttendance('{{ $record['id'] ?? '' }}', '{{ addslashes($record['name'] ?? '') }}')" 
                                class="text-red-600 hover:text-red-800 text-xs font-medium ml-2">
                                <i class="bi bi-trash text-xs mr-1"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <i class="bi bi-inbox text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500 text-sm">No attendance records found for the selected filters.</p>
            </div>
            @endif
        </div>
        
        @if(count($attendanceRecords) > 0)
        <div class="p-3 border-t border-gray-200 bg-gray-50">
            <p class="text-xs text-gray-600 text-center">Showing {{ count($attendanceRecords) }} record(s)</p>
        </div>
        @endif
    </div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold">Edit Attendance</h2>
                    <p class="text-blue-100 text-xs" id="editStudentName">Student Name</p>
                </div>
                <button onclick="closeEditStudentModal()" class="text-blue-200 hover:text-white">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-4 space-y-4">
            <input type="hidden" id="editRecordId">
            <input type="hidden" id="editStudentId">

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Current Status</label>
                <span id="editCurrentStatus" class="inline-block px-3 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">-</span>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">New Status</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="editStatus" value="present" class="peer sr-only">
                        <div class="px-3 py-2 rounded text-xs font-medium text-center border border-gray-200 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-300 hover:bg-gray-50">
                            <i class="bi bi-check-circle text-xs mr-1"></i> Present
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="editStatus" value="absent" class="peer sr-only">
                        <div class="px-3 py-2 rounded text-xs font-medium text-center border border-gray-200 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-300 hover:bg-gray-50">
                            <i class="bi bi-x-circle text-xs mr-1"></i> Absent
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="editStatus" value="leave" class="peer sr-only">
                        <div class="px-3 py-2 rounded text-xs font-medium text-center border border-gray-200 peer-checked:bg-purple-100 peer-checked:text-purple-700 peer-checked:border-purple-300 hover:bg-gray-50">
                            <i class="bi bi-calendar-event text-xs mr-1"></i> Leave
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                <textarea id="editRemarks" placeholder="Add remarks..." class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 h-20 resize-none"></textarea>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200 flex gap-2">
                <button type="button" onclick="closeEditStudentModal()" class="flex-1 px-3 py-1.5 text-gray-700 border border-gray-300 rounded text-xs hover:bg-gray-100">
                    Cancel
                </button>
                <button type="button" onclick="saveStudentAttendance()" class="flex-1 px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                    <i class="bi bi-check text-xs mr-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mark Attendance Modal -->
<div id="markAttendanceModal" class="fixed hidden inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl">
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 rounded-t-lg shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex items-start gap-3">
                    <div class="bg-white bg-opacity-20 rounded-full p-2">
                        <i class="bi bi-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Mark Attendance</h2>
                        <p class="text-red-100 text-sm">Pick a date and semester, then load students to mark attendance</p>
                    </div>
                </div>
                <button onclick="closeMarkAttendanceModal()" aria-label="Close" class="text-red-200 hover:text-white p-2 rounded-full hover:bg-red-700/25">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date (BS) <span class="text-red-500">*</span></label>
                    <input type="text" id="mark_date_bs" value="{{ $date_bs ?? '' }}" placeholder="YYYY-MM-DD (BS)" class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
                    <select id="mark_semester" class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Select Semester</option>
                        @foreach($semesters as $sem)
                        <option value="{{ $sem }}">{{ $sem }}{{ $sem == 1 ? 'st' : ($sem == 2 ? 'nd' : ($sem == 3 ? 'rd' : 'th')) }} Semester</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course <span class="text-gray-400">(Optional)</span></label>
                    <select id="mark_course" class="w-full px-3 py-2 border border-gray-200 rounded-md text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">General Attendance</option>
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}" data-semester="{{ $c->semester }}">{{ $c->subject_code }} - {{ $c->subject_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="button" id="loadStudentsBtn" onclick="loadAttendanceStudents()" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 shadow"> <i class="bi bi-search"></i> <span>Load Students</span></button>
                    <button type="button" onclick="renderAttendanceTable([]);" class="px-4 py-2 border border-gray-200 rounded-md text-sm hover:bg-gray-50">Clear</button>
                </div>
            </div>    

            <div id="attendanceSummary" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 hidden">
                <div class="p-4 bg-white rounded-lg text-center border border-gray-100 shadow-sm">
                    <div class="text-2xl font-semibold text-gray-800" id="summary_total">0</div>
                    <div class="text-sm text-gray-500">Total Students</div>
                </div>
                <div class="p-4 bg-white rounded-lg text-center border border-green-100 shadow-sm">
                    <div class="text-2xl font-semibold text-green-700" id="summary_present">0</div>
                    <div class="text-sm text-green-600">Present</div>
                </div>
                <div class="p-4 bg-white rounded-lg text-center border border-red-100 shadow-sm">
                    <div class="text-2xl font-semibold text-red-700" id="summary_absent">0</div>
                    <div class="text-sm text-red-600">Absent</div>
                </div>
            </div> 

            <div id="attendanceTableWrap" class="mt-4 hidden">
                <div class="overflow-x-auto border border-gray-200 rounded">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Student</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Roll No</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Sem</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Date</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead> 
                        <tbody id="attendanceTbody" class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 p-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeMarkAttendanceModal()" class="px-3 py-1.5 border border-gray-300 rounded text-sm hover:bg-gray-100">Cancel</button>
                <button type="button" id="saveAllBtn" onclick="saveAllAttendance()" class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="bi bi-save"></i>
                    <span>Save All</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="hidden fixed top-4 right-4 z-50"></div>

<script>
    // Filter courses by semester when filter button is clicked
    document.addEventListener('DOMContentLoaded', function() {
        var filterSemester = document.getElementById('filter_semester');
        var filterCourse = document.getElementById('filter_course');

        if (filterSemester && filterCourse) {
            // Get current selected values from URL params
            var currentSemester = filterSemester.value;
            var currentCourse = '{{ $course }}';

            // Function to filter courses based on selected semester
            function filterCoursesBySemester() {
                var selectedSemester = filterSemester.value;
                var options = filterCourse.getElementsByTagName('option');

                for (var i = 0; i < options.length; i++) {
                    var option = options[i];
                    var optionSemester = option.getAttribute('data-semester');

                    if (option.value === '') {
                        // Always show the "All Courses" option
                        option.style.display = '';
                    } else if (!selectedSemester || selectedSemester === '') {
                        // When "All Semesters" is selected, show all courses
                        option.style.display = '';
                    } else {
                        // Compare semester values (both are numeric)
                        var semMatch = (optionSemester == selectedSemester);
                        if (semMatch) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                }
            }

            // Apply filter on page load if semester is selected
            if (currentSemester) {
                setTimeout(filterCoursesBySemester, 100);
            }

            // Also trigger when semester changes
            filterSemester.addEventListener('change', function() {
                filterCoursesBySemester();
            });
        }

        // Filter courses in mark attendance modal by semester
        var markSemester = document.getElementById('mark_semester');
        var markCourse = document.getElementById('mark_course');

        if (markSemester && markCourse) {
            // Function to filter courses based on selected semester in modal
            function filterMarkCoursesBySemester() {
                var selectedSemester = markSemester.value;
                var options = markCourse.getElementsByTagName('option');

                for (var i = 0; i < options.length; i++) {
                    var option = options[i];
                    var optionSemester = option.getAttribute('data-semester');

                    if (option.value === '') {
                        // Always show the "General Attendance" option
                        option.style.display = '';
                    } else if (!selectedSemester || selectedSemester === '') {
                        // When no semester is selected, show all courses
                        option.style.display = '';
                    } else {
                        // Compare semester values (both are numeric)
                        var semMatch = (optionSemester == selectedSemester);
                        if (semMatch) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                }
            }

            // Trigger when semester changes in modal
            markSemester.addEventListener('change', function() {
                filterMarkCoursesBySemester();
            });
        }
    });

    function openEditRecordModal(recordId, studentId, studentName, currentStatus, remarks, date) {
        document.getElementById('editRecordId').value = recordId;
        document.getElementById('editStudentId').value = studentId;
        document.getElementById('editStudentName').textContent = studentName;
        // Do not include date in the edit modal anymore
        
        const statusBadge = document.getElementById('editCurrentStatus');
        statusBadge.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
        statusBadge.className = `inline-block px-3 py-1 rounded text-xs font-medium whitespace-nowrap
            ${currentStatus === 'present' ? 'bg-green-100 text-green-700' : 
              (currentStatus === 'leave' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-red-700')}`;
        
        document.querySelectorAll('input[name="editStatus"]').forEach(radio => {
            radio.checked = radio.value === currentStatus;
        });
        
        document.getElementById('editRemarks').value = remarks || '';
        document.getElementById('editStudentModal').classList.remove('hidden');
    }

    function closeEditStudentModal() {
        document.getElementById('editStudentModal').classList.add('hidden');
    }

    async function saveStudentAttendance() {
        const recordId = document.getElementById('editRecordId').value;
        const studentId = document.getElementById('editStudentId').value;
        // For creates we use the selected filter or mark date; for edits we don't need to send date
        const fallbackDate = document.getElementById('filter_date_bs')?.value || document.getElementById('mark_date_bs')?.value || '';
        const newStatus = document.querySelector('input[name="editStatus"]:checked')?.value;
        const remarks = document.getElementById('editRemarks').value.trim();

        if (!newStatus) {
            alert('Please select a status');
            return;
        }

        if (!studentId) {
            showToast('Error: Student ID is missing', 'error');
            return;
        }

        try {
            let url = '{{ route("admin.attendance.store") }}';
            let method = 'POST';

            // If we have a record ID, use the PUT endpoint for update
            if (recordId && recordId !== '') {
                url = '{{ route("admin.attendance.update", ["id" => "__ID__"]) }}'.replace('__ID__', recordId);
                method = 'PUT';
            }

            // For POST requests date is required
            if (method === 'POST' && !fallbackDate) {
                showToast('Date (BS) is required to save attendance', 'error');
                return;
            }

            // Build payload conditionally
            const payload = {
                student_id: studentId,
                status: newStatus,
                remarks: remarks
            };

            if (method === 'POST') payload.date_bs = fallbackDate;

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                showToast('Attendance updated successfully!', 'success');
                closeEditStudentModal();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to save');
            }
        } catch (error) {
            console.error('Error saving attendance:', error);
            showToast('Error saving attendance: ' + error.message, 'error');
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const bgColor = type === 'success' ? 'bg-green-500' : (type === 'warning' ? 'bg-yellow-500' : 'bg-red-500');
        const icon = type === 'success' ? 'bi-check-circle' : (type === 'warning' ? 'bi-exclamation-triangle' : 'bi-exclamation-circle');
        
        toast.innerHTML = `
            <div class="flex items-center gap-3 px-4 py-3 rounded shadow-lg text-white ${bgColor}">
                <i class="bi ${icon} text-lg"></i>
                <span>${message}</span>
            </div>
        `;
        toast.classList.remove('hidden');
        
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 4000);
    }

    document.getElementById('editStudentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditStudentModal();
        }
    });

    document.querySelector('input[name="date_bs"]')?.addEventListener('change', function() {
        this.closest('form').submit();
    });

    // MARK ATTENDANCE - bulk modal and actions
    let attendanceState = {};

    function openMarkAttendanceModal() {
        document.getElementById('markAttendanceModal').classList.remove('hidden');
    }

    function closeMarkAttendanceModal() {
        document.getElementById('markAttendanceModal').classList.add('hidden');
        renderAttendanceTable([]);
        document.getElementById('attendanceSummary').classList.add('hidden');
    }

    // Check if attendance already exists for selected date and semester
    async function checkExistingAttendance(date, semester) {
        if (!date || !semester) return;

        try {
            const url = new URL('{{ route("admin.attendance.students") }}', window.location.origin);
            url.searchParams.set('date_bs', date);
            url.searchParams.set('semester', semester);

            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            const alreadyMarkedStudents = data.students ? data.students.filter(s => s.alreadyMarked).length : 0;
            if (alreadyMarkedStudents > 0 && alreadyMarkedStudents === (data.students ? data.students.length : 0)) {
                showToast('Attendance already marked for all students on this date.', 'warning');
            } else if (alreadyMarkedStudents > 0) {
                showToast('Attendance for ' + alreadyMarkedStudents + ' student(s) already marked for this date. They will be updated.', 'warning');
            }
        } catch (err) {
            console.error('Error checking existing attendance', err);
        }
    }

    // Add event listener for date selection in modal
    document.getElementById('mark_date_bs')?.addEventListener('change', function() {
        const date = this.value;
        const semester = document.getElementById('mark_semester').value;
        if (date && semester) {
            checkExistingAttendance(date, semester);
        }
    });

    // Add event listener for semester selection in modal
    document.getElementById('mark_semester')?.addEventListener('change', function() {
        const semester = this.value;
        const date = document.getElementById('mark_date_bs').value;
        if (date && semester) {
            checkExistingAttendance(date, semester);
        }
    });

    async function loadAttendanceStudents() {
        const date = document.getElementById('mark_date_bs').value;
        const semester = document.getElementById('mark_semester').value;

        if (!date || !semester) {
            alert('Please select both date and semester');
            return;
        }

        const btn = document.getElementById('loadStudentsBtn');
        btn.disabled = true;
        btn.textContent = 'Loading...';

        try {
            const url = new URL('{{ route("admin.attendance.students") }}', window.location.origin);
            url.searchParams.set('date_bs', date);
            url.searchParams.set('semester', semester);

            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            // Expecting an array of students: {id, name, email, roll_no, semester}
            const students = data.students || data || [];
            
            // Disable save button ONLY if ALL students already have attendance marked
            const alreadyMarkedCount = students.filter(s => s.alreadyMarked).length;
            const saveBtn = document.getElementById('saveAllBtn');
            if (alreadyMarkedCount > 0 && alreadyMarkedCount === students.length) {
                saveBtn.disabled = true;
            } else {
                saveBtn.disabled = false;
            }
            
            renderAttendanceTable(students, date);
        } catch (err) {
            console.error('Error loading students', err);
            showToast('Failed to load students', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = '🔍 Load Students';
        }
    }

    function renderAttendanceTable(students = []) {
        attendanceState = {};
        const tbody = document.getElementById('attendanceTbody');
        tbody.innerHTML = '';

        if (!students || students.length === 0) {
            document.getElementById('attendanceTableWrap').classList.add('hidden');
            document.getElementById('attendanceSummary').classList.add('hidden');
            return;
        }

        students.forEach(s => {
            // Use student_id from the API response (which is students.id)
            const studentId = s.student_id || s.id;
            attendanceState[studentId] = { student: s, status: s.status || 'present' };

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';

            const nameTd = document.createElement('td');
            nameTd.className = 'px-4 py-3 text-xs';
            nameTd.innerHTML = `<div class="flex items-center gap-2"><div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center"><i class="bi bi-person-fill text-xs text-red-600"></i></div><div><div class="font-medium text-gray-900">${escapeHtml(s.name)}</div><div class="text-xs text-gray-500">${escapeHtml(s.email || '')}</div></div></div>`;

            const rollTd = document.createElement('td');
            rollTd.className = 'px-4 py-3 text-xs text-center text-gray-700';
            rollTd.textContent = s.roll_no || '-';

            const semTd = document.createElement('td');
            semTd.className = 'px-4 py-3 text-xs text-center text-gray-700';
            semTd.textContent = s.semester || '-';

            const dateTd = document.createElement('td');
            dateTd.className = 'px-4 py-3 text-xs text-center text-gray-700';
            dateTd.textContent = document.getElementById('mark_date_bs').value ? document.getElementById('mark_date_bs').value : '-';

            const isPresent = (s.status || 'present') === 'present';
            const presentTd = document.createElement('td');
            presentTd.className = 'px-4 py-3 text-center';
            presentTd.innerHTML = `
                <div class="inline-flex items-center gap-3 justify-center">
                    <label class="inline-flex items-center cursor-pointer">
                        <input data-id="${studentId}" type="checkbox" ${isPresent ? 'checked' : ''} class="sr-only attendance-toggle" aria-label="Mark present">
                        <div class="w-12 h-6 ${isPresent ? 'bg-green-500' : 'bg-gray-200'} rounded-full relative transition-colors duration-200">
                            <div class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200" style="transform: translateX(${isPresent ? '24px' : '0'})"></div>
                        </div>
                    </label>
                    <span class="text-sm font-medium ${isPresent ? 'text-green-700' : 'text-red-700'} status-badge">${isPresent ? 'Present' : 'Absent'}</span>
                </div>
            `;

            tr.appendChild(nameTd);
            tr.appendChild(rollTd);
            tr.appendChild(semTd);
            tr.appendChild(dateTd);
            tr.appendChild(presentTd);

            tbody.appendChild(tr);
        });

        // wire toggles
        document.querySelectorAll('.attendance-toggle').forEach(cb => {
            cb.addEventListener('change', function() {
                const id = this.dataset.id;
                const isChecked = this.checked;
                attendanceState[id].status = isChecked ? 'present' : 'absent';
                updateSummary();
                markUnsaved();
                // animate toggle and update badge
                const wrap = this.nextElementSibling || this.parentElement.querySelector('div');
                const dot = wrap.querySelector('.dot');
                const badge = wrap.closest('td').querySelector('.status-badge');

                if (isChecked) {
                    wrap.classList.remove('bg-gray-200');
                    wrap.classList.add('bg-green-500');
                    dot.style.transform = 'translateX(24px)';
                    if (badge) {
                        badge.textContent = 'Present';
                        badge.classList.remove('text-red-700');
                        badge.classList.add('text-green-700');
                    }
                } else {
                    wrap.classList.remove('bg-green-500');
                    wrap.classList.add('bg-gray-200');
                    dot.style.transform = 'translateX(0)';
                    if (badge) {
                        badge.textContent = 'Absent';
                        badge.classList.remove('text-green-700');
                        badge.classList.add('text-red-700');
                    }
                }
            });
        });

        document.getElementById('attendanceTableWrap').classList.remove('hidden');
        document.getElementById('attendanceSummary').classList.remove('hidden');
        updateSummary();
    }

    function updateSummary() {
        const total = Object.keys(attendanceState).length;
        const present = Object.values(attendanceState).filter(x => x.status === 'present').length;
        const absent = total - present;
        document.getElementById('summary_total').textContent = total;
        document.getElementById('summary_present').textContent = present;
        document.getElementById('summary_absent').textContent = absent;
    }

    function markUnsaved() {
        // enable save button when user makes changes
        const saveBtn = document.getElementById('saveAllBtn');
        saveBtn.classList.remove('opacity-50');
        saveBtn.disabled = false;
    }

    function escapeHtml(unsafe) {
        return String(unsafe).replace(/[&<>\"'`]/g, function (m) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#39;','`':'&#x60;'}[m];
        });
    }

    async function saveAllAttendance() {
        const attendance = Object.values(attendanceState).map(s => ({ student_id: s.student.student_id || s.student.id, status: s.status }));
const date = document.getElementById('mark_date_bs').value;
        const courseId = document.getElementById('mark_course').value;

        if (attendance.length === 0) {
            alert('No students to save');
            return;
        }

        if (!date) {
            alert('Please select a date');
            return;
        }

        try {
            const payload = { attendance, date_bs: date };
            if (courseId) {
                payload.subject_id = courseId;
            }

            const res = await fetch('{{ route("admin.attendance.bulk") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.message && data.message.includes('success') || data.success) {
                showToast('Attendance saved successfully!', 'success');
                setTimeout(() => { location.reload(); }, 800);
            } else {
                throw new Error(data.message || 'Failed to save attendances');
            }
        } catch (err) {
            console.error('Save error', err);
            showToast('Error saving attendance: ' + (err.message || ''), 'error');
        }
    }

    async function deleteAttendance(recordId, studentName) {
        if (!confirm('Are you sure you want to delete the attendance record for ' + studentName + '?')) {
            return;
        }

        try {
            const response = await fetch('{{ route("admin.attendance.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: recordId
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Attendance deleted successfully!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to delete');
            }
        } catch (error) {
            console.error('Error deleting attendance:', error);
            showToast('Error deleting attendance: ' + error.message, 'error');
        }
    }
</script>
@endsection
