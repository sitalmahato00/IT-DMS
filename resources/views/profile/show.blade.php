@extends($layout ?? 'layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-gray-900 font-semibold text-base flex items-center gap-2">
                <i class="bi bi-person-badge text-gray-500"></i>
                My Profile
            </h3>
        </div>
        <div class="p-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Profile Photo -->
                <div class="flex flex-col items-center lg:items-start">
                    <img src="{{ $user->profile_photo_url }}" alt="Profile photo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=150&background=random'" />
                    <div class="mt-4 text-center lg:text-left">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                        <p class="text-sm text-gray-600">{{ ucfirst($user->role) }}</p>
                        @if($user->status)
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded mt-1">{{ ucfirst($user->status) }}</span>
                        @endif
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <p class="text-sm text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <p class="text-sm text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <p class="text-sm text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <p class="text-sm text-gray-900">{{ $user->department ?? 'Not provided' }}</p>
                    </div>
                    @if($user->role === 'teacher')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teacher ID</label>
                            <p class="text-sm text-gray-900">{{ $user->teacher_code ?? 'Not provided' }}</p>
                        </div>
                    @endif
                    @if($user->role === 'student')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                            <p class="text-sm text-gray-900">{{ $user->student_id ?? 'Not provided' }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <p class="text-sm text-gray-900">{{ $user->gender ? ucfirst($user->gender) : 'Not specified' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <p class="text-sm text-gray-900">{{ $user->bio ?? 'No bio provided' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                @if($user->role === 'teacher')
                    <a href="{{ route('teacher.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="bi bi-pencil mr-1"></i>
                        Edit Profile
                    </a>
                @elseif($user->role === 'student')
                    <a href="{{ route('student.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="bi bi-pencil mr-1"></i>
                        Edit Profile
                    </a>
                @elseif($user->role === 'parent')
                    <a href="{{ route('parent.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="bi bi-pencil mr-1"></i>
                        Edit Profile
                    </a>
                @else
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="bi bi-pencil mr-1"></i>
                        Edit Profile
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Information Cards -->
    @if($user->role === 'teacher')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Courses Taught -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-book text-gray-500"></i>
                        Courses Taught
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($courses) && $courses->count() > 0)
                        <div class="space-y-2">
                            @foreach($courses as $course)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm font-medium text-gray-900">{{ $course->name }}</span>
                                    <span class="text-xs text-gray-600">{{ $course->semester }} Semester</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No courses assigned</p>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-activity text-gray-500"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        <div class="space-y-2">
                            @foreach($recentActivities->take(5) as $activity)
                                <div class="flex items-start gap-2 p-2 bg-gray-50 rounded">
                                    <i class="bi bi-circle-fill text-xs text-blue-500 mt-1"></i>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-900">{{ $activity->action }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($user->role === 'student')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Academic Information -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-mortarboard text-gray-500"></i>
                        Academic Information
                    </h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Semester:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->semester ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->is_alumni ? 'Alumni' : 'Active Student' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-gray-900 font-semibold text-sm flex items-center gap-2">
                        <i class="bi bi-calendar-check text-gray-500"></i>
                        Attendance Summary
                    </h3>
                </div>
                <div class="p-4">
                    @if(isset($attendanceStats))
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Present:</span>
                                <span class="text-sm font-medium text-green-600">{{ $attendanceStats['present'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Absent:</span>
                                <span class="text-sm font-medium text-red-600">{{ $attendanceStats['absent'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Attendance Rate:</span>
                                <span class="text-sm font-medium text-blue-600">{{ $attendanceStats['rate'] ?? 0 }}%</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No attendance data available</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
