@php /** Minimal printable student card (no layout) */ @endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Print student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css'])
    <style>
        body{background:#fff;margin:0;padding:20px}
        .print-root{max-width:900px;margin:0 auto;background:#fff;padding:20px;box-sizing:border-box}
        .no-print{display:none !important}
    </style>
</head>
<body>
    <div class="print-root">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                        @if($student->profile_photo_path)
                            <img src="{{ asset('storage/'.$student->profile_photo_path) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-xl font-semibold text-gray-700">{{ collect(explode(' ', trim($student->name)))->map(fn($v)=>mb_substr($v,0,1))->take(2)->join('') }}</div>
                        @endif
                    </div>

                    <div>
                        <div class="text-2xl font-semibold">{{ $student->name }}</div>
                        <div class="text-sm text-gray-500">{{ ucfirst($student->role ?? 'student') }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="col-span-1">
                    <div class="text-xs text-gray-500">Roll No</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $student->student->roll_no ?? $student->id }}</div>

                    <div class="mt-3">
                        <div class="text-xs text-gray-500">Semester</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $student->student->semester ?? '—' }}</div>
                    </div>

                    <div class="mt-3">
                        <div class="text-xs text-gray-500">Department</div>
                        <div class="text-base font-semibold text-gray-900">{{ $student->department ?? ($student->student->department ?? '—') }}</div>
                    </div>
                </div>

                <div class="col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Contact</div>
                            <div class="mt-2 text-gray-900">{{ $student->email ?? '—' }}</div>
                            <div class="mt-1 text-gray-900">{{ $student->phone ?? '—' }}</div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs text-gray-500">Status</div>
                            @php $status = $student->status ?? null; $isAlumni = $student->is_alumni ?? 0; @endphp
                            <div class="mt-2">
                                <div class="mb-2">
                                    <div class="text-xs text-gray-500">Role</div>
                                    <div class="font-medium text-gray-900">{{ $student->role ?? 'student' }}</div>
                                </div>

                                <div class="mb-2">
                                    <div class="text-xs text-gray-500">Alumni</div>
                                    <div class="font-medium text-gray-900">{{ $isAlumni ? 'Yes' : 'No' }}</div>
                                </div>

                                <div class="mb-2">
                                    @if($status === 'active' || $status === 'present')
                                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded">Active</span>
                                    @elseif($status === 'pending')
                                        <span class="inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded">Pending</span>
                                    @else
                                        <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!empty($student->bio))
                        <div class="mt-6">
                            <div class="text-xs text-gray-500">About</div>
                            <div class="mt-2 text-gray-700 text-sm">{{ $student->bio }}</div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print then close
        window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 300); });
    </script>
</body>
</html>
