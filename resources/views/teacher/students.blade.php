@extends('teacher.layouts.teacherlayout')

@section('title', __('My Students'))

@section('content')
@php
    $studentListQuery = request()->except('page');
    $studentsExportUrl = route('teacher.students.export', $studentListQuery);
    $studentsPrintUrl = route('teacher.students.print', $studentListQuery);
@endphp
<div class="space-y-6 @if(app()->getLocale() === 'ne') locale-ne @endif">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed top-4 right-4 z-50"></div>

    <!-- View Student Modal -->
    <div id="viewStudentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
            <div class="px-6 py-4 border-b-2 border-red-700 flex items-center justify-between sticky top-0 bg-red-600 text-white">
                <div>
                    <h3 class="text-lg font-semibold">{{ __('View Student') }}</h3>
                    <p class="text-sm text-red-100">{{ __('Student information and details') }}</p>
                </div>
                <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="text-red-100 hover:text-white">✕</button>
            </div>
            <div class="p-6">
                <div class="flex gap-8">
                    <!-- Photo Section -->
                    <div class="flex flex-col items-center">
                        <div id="viewStudentAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0">
                            <img id="viewStudentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                            <span id="viewStudentInitial"><i class="bi bi-person text-5xl"></i></span>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Full Name') }}</label>
                                <p id="view_name" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Email') }}</label>
                                <p id="view_email" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Phone') }}</label>
                                <p id="view_phone" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Semester') }}</label>
                                <p id="view_semester" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Course') }}</label>
                                <p id="view_course" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Roll No') }}</label>
                                <p id="view_roll_no" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Date of Birth (AD)') }}</label>
                                <p id="view_dob" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Date of Birth (BS)') }}</label>
                                <p id="view_dob_bs" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Academic Year') }}</label>
                                <p id="view_batch_year" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Address') }}</label>
                                <p id="view_address" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Gender') }}</label>
                                <p id="view_gender" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Blood Group') }}</label>
                                <p id="view_blood_group" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
                                <p id="view_status" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Role') }}</label>
                                <p id="view_role" class="text-sm text-gray-900">—</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Emergency Contact') }}</label>
                                <p id="view_emergency_contact" class="text-sm text-gray-900">—</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Bio') }}</label>
                            <p id="view_bio" class="text-sm text-gray-900">—</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="event.preventDefault(); closeViewStudentModal(); return false;" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('My Students') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('View and manage students enrolled in your subjects.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick='teacherOpenPrintPreview(@json($studentsPrintUrl), { title: @json(__('Print Students')) })' class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                <i class="bi bi-printer"></i> {{ __('Print') }}
            </button>
            <a href="{{ $studentsExportUrl }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition font-medium">
                <i class="bi bi-download"></i> {{ __('Export CSV') }}
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('teacher.students') }}" class="space-y-4">
            <!-- Filter Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Search') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Name, Email, Roll No') }}" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Semester Filter (dynamic) -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Semester') }}</label>
                    <select name="semester" id="semesterFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Semesters') }}</option>
                        @if(!empty($availableSemesters))
                            @foreach($availableSemesters as $sem)
                                <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>{{ __('Semester ') . $sem }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Subject Filter (dynamic) -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Subject') }}</label>
                    <select name="subject" id="subjectFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">{{ __('All Subjects') }}</option>
                        @if($subjects->isNotEmpty())
                            @foreach($subjects as $subject)
                                @php
                                    $selectedSem = request('semester');
                                    $shouldShow = empty($selectedSem) || ((string) ($subject['semester'] ?? '') === (string) $selectedSem);
                                @endphp
                                @if($shouldShow)
                                    <option value="{{ $subject['id'] }}" {{ (string) $selectedSubject === (string) $subject['id'] ? 'selected' : '' }}>
                                        {{ $subject['code'] }} - {{ $subject['name'] }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Per Page -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Per Page') }}</label>
                    <select name="per_page" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 justify-between flex-wrap pt-2">
                <div class="flex gap-2 flex-wrap">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition-colors font-medium shadow-sm">
                        <i class="bi bi-funnel"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('teacher.students') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-md text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $stats['total'] ?? ($students->total() ?? 0) }} {{ __('students found') }}
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total Students') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <i class="bi bi-people text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Subjects') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $subjects->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                    <i class="bi bi-book text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Male') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['male'] }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <i class="bi bi-gender-male text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Female') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['female'] }}</p>
                </div>
                <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center text-pink-600 dark:text-pink-400">
                    <i class="bi bi-gender-female text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        @if($students->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('User') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Student IT') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Email') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Role') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Semester') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Academic Year') }}</th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-center {{ app()->getLocale() === 'ne' ? 'devanagari-text' : '' }}">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $student)
                        @php
                        $profilePhotoPath = $student->profile_photo_url ?? '';
                        $dob = $student->date_of_birth ?? null;
                        $dobFormatted = $dob ? ($dob instanceof \Carbon\Carbon ? $dob->format('Y-m-d') : $dob) : '';
                        $studentJson = json_encode([
                            'id' => $student->id,
                            'name' => $student->name,
                            'student_id' => $student->roll_no ?? $student->id,
                            'email' => $student->email,
                            'phone' => $student->phone ?? '',
                            'department' => $student->course_name ?? '',
                            'semester' => $student->semester ?? '',
                            'registration_number' => $student->registration_number ?? '',
                            'date_of_birth' => $dobFormatted,
                            'date_of_birth_bs' => $student->date_of_birth_bs ?? '',
                            'academic_year' => $student->academic_year ?? '',
                            'academic_year_bs' => $student->academic_year_bs ?? '',
                            'address' => $student->address ?? '',
                            'bio' => $student->bio ?? '',
                            'gender' => $student->gender ?? '',
                            'status' => $student->status ?? '',
                            'is_alumni' => $student->is_alumni ?? 0,
                            'blood_group' => $student->blood_group ?? '',
                            'emergency_contact' => $student->emergency_contact ?? '',
                            'role' => $student->role ?? '',
                            'profile_photo_url' => $profilePhotoPath
                        ]);
                        @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                                            @if($student->profile_photo_url)
                                                <img src="{{ $student->profile_photo_url }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'bi bi-person-fill text-gray-400\'></i>';">
                                            @else
                                                <i class="bi bi-person-fill text-gray-400"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $student->registration_number ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $student->roll_no ?? $student->id }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $student->email }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                        {{ ucfirst($student->role ?? 'student') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    @if($student->is_alumni)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-sm font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                            <i class="bi bi-mortarboard text-sm"></i>
                                            {{ __('Graduate') }}
                                        </span>
                                    @else
                                        {{ $student->semester ?? '--' }}
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    @if($student->academic_year || $student->academic_year_bs)
                                        <span class="text-sm">{{ $student->academic_year ?? '--' }}AD</span>
                                        @if($student->academic_year_bs)
                                            <span class="text-xs text-gray-500">/{{ $student->academic_year_bs }}BS</span>
                                        @endif
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if(($student->status ?? '') === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                            {{ __('Active') }}
                                        </span>
                                    @elseif(($student->status ?? '') === 'inactive')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                            {{ __('Inactive') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                            {{ __('Pending') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button type="button" onclick="viewStudent({{ $studentJson }})" class="inline-flex items-center gap-1 px-2 py-1 text-xs text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 rounded transition" title="{{ __('View') }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $students->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-people text-2xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No Students Found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('No students match your filter criteria.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function viewStudent(student) {
        document.getElementById('view_name').textContent = student.name || '—';
        document.getElementById('view_email').textContent = student.email || '—';
        document.getElementById('view_phone').textContent = student.phone || '—';

        if (student.is_alumni) {
            document.getElementById('view_semester').innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-sm font-medium bg-red-100 text-red-700"><i class="bi bi-mortarboard text-sm"></i> Graduate</span>';
        } else {
            document.getElementById('view_semester').textContent = student.semester || '—';
        }

        document.getElementById('view_course').textContent = student.department || '—';
        document.getElementById('view_roll_no').textContent = student.student_id || '—';
        document.getElementById('view_dob').textContent = student.date_of_birth || '—';
        document.getElementById('view_dob_bs').textContent = student.date_of_birth_bs || '—';
        document.getElementById('view_batch_year').textContent = (student.academic_year ? student.academic_year + 'AD' : '—') + (student.academic_year_bs ? '/' + student.academic_year_bs + 'BS' : '');
        document.getElementById('view_address').textContent = student.address || '—';
        document.getElementById('view_gender').textContent = student.gender ? (student.gender.charAt(0).toUpperCase() + student.gender.slice(1)) : '—';
        document.getElementById('view_blood_group').textContent = student.blood_group || '—';
        document.getElementById('view_status').textContent = student.status ? (student.status.charAt(0).toUpperCase() + student.status.slice(1)) : '—';
        document.getElementById('view_role').textContent = student.role ? (student.role.charAt(0).toUpperCase() + student.role.slice(1)) : '—';
        document.getElementById('view_emergency_contact').textContent = student.emergency_contact || '—';
        document.getElementById('view_bio').textContent = student.bio || '—';

        const viewAvatarImg = document.getElementById('viewStudentAvatarImg');
        const viewInitial = document.getElementById('viewStudentInitial');
        if (student.profile_photo_url) {
            viewAvatarImg.src = student.profile_photo_url;
            viewAvatarImg.style.display = 'block';
            viewInitial.style.display = 'none';
        } else {
            viewAvatarImg.style.display = 'none';
            viewInitial.style.display = 'flex';
        }

        document.getElementById('viewStudentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeViewStudentModal() {
        document.getElementById('viewStudentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const semesterSelect = document.getElementById('semesterFilter');
        const subjectSelect = document.getElementById('subjectFilter');
        if (!semesterSelect || !subjectSelect) {
            return;
        }

        const allSubjects = @json($subjects);

        function rebuildSubjectOptions() {
            const selectedSemester = semesterSelect.value;
            const previousSubjectValue = subjectSelect.value;
            const allowedSubjects = Array.isArray(allSubjects)
                ? allSubjects.filter((s) => {
                    if (!selectedSemester) return true;
                    return String(s.semester ?? '') === String(selectedSemester);
                })
                : [];

            subjectSelect.innerHTML = '';
            const allOpt = document.createElement('option');
            allOpt.value = '';
            allOpt.textContent = @json(__('All Subjects'));
            subjectSelect.appendChild(allOpt);

            allowedSubjects.forEach((s) => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = `${s.code ?? ''} - ${s.name ?? ''}`.trim();
                subjectSelect.appendChild(opt);
            });

            const stillValid = allowedSubjects.some((s) => String(s.id) === String(previousSubjectValue));
            if (stillValid) {
                subjectSelect.value = previousSubjectValue;
            } else {
                subjectSelect.value = '';
            }

            subjectSelect.disabled = allowedSubjects.length === 0;
        }

        rebuildSubjectOptions();
        semesterSelect.addEventListener('change', rebuildSubjectOptions);

        const studentModal = document.getElementById('viewStudentModal');
        if (studentModal) {
            studentModal.addEventListener('click', function (event) {
                if (event.target === this) {
                    closeViewStudentModal();
                }
            });
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeViewStudentModal();
        }
    });
</script>
@endsection
