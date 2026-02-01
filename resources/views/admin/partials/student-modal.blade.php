<div id="studentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-6" onclick="if(event.target===this) document.getElementById('studentModal')?.remove()">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-auto sm:mx-4 overflow-auto max-h-[90vh]" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">Student Details</h3>
                <p class="text-xs text-gray-500">Read-only view of student profile</p>
            </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.students.print', $student->id) }}" class="px-3 py-1 border rounded text-sm bg-white/20 no-print" target="_blank" rel="noopener">Print</a>
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="px-3 py-1 border rounded text-sm no-print">Edit</a>
                </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Avatar column -->
                <div class="col-span-1 flex flex-col items-center">
                    <div class="w-36 h-36 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center border">
                        @if($student->profile_photo_path)
                            <img src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="bi bi-person-fill text-4xl text-gray-400"></i>
                        @endif
                    </div>
                    <div class="mt-3 text-sm text-gray-500 text-center">Recommended 400×400px. Max 4MB.</div>
                </div>

                <!-- Fields column -->
                <div class="col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Full name</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->name }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Student ID</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->student->roll_no ?? '' }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700">Email</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->email }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Phone</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->phone ?? '—' }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700">Department</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->department ?? ($student->student->department ?? '—') }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Semester</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->student->semester ?? '—' }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700">Date of birth</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->student->date_of_birth_bs ?? '—' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Batch Year</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50">{{ $student->student->batch_year ?? '—' }}</div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Address</label>
                            <div class="mt-1 w-full px-3 py-2 border rounded-md text-sm bg-gray-50 min-h-[60px]">{{ $student->student->address ?? '—' }}</div>
                        </div>
