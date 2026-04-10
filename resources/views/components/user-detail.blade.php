@props(['user' => null, 'size' => 'md'])

@php
    $headerPadding = $size === 'sm' ? 'p-4' : 'p-6';
    $contentPadding = $size === 'sm' ? 'p-4' : 'p-6';
    $name = $user->name ?? 'Unknown';
    $subtitle = $user->role ?? '';
    $profileUrl = $user->profile_photo_url ?? null;
    $status = $user->status ?? null;
    $initials = collect(explode(' ', trim($name)))->map(fn($v)=>mb_substr($v,0,1))->take(2)->join('');
    $avatarClass = $size === 'sm' ? '-mt-8 w-16 h-16 rounded-full ring-3 text-lg' : '-mt-10 w-20 h-20 rounded-full ring-4 text-xl';
@endphp

<div class="max-w-2xl mx-auto">
    <div class="relative rounded-lg overflow-hidden shadow">
        <div class="{{ $headerPadding }} bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="{{ $avatarClass }} ring-white overflow-hidden bg-white/10 flex items-center justify-center text-white">
                        @if($profileUrl)
                            <img src="{{ $profileUrl }}" alt="{{ $name }}" class="w-full h-full object-cover">
                        @else
                            <div class="font-semibold">{{ $initials }}</div>
                        @endif
                    </div>

                    <div class="ml-1">
                        <div class="text-2xl font-semibold leading-tight">{{ $name }}</div>
                        @if($subtitle)
                            <div class="text-sm opacity-90">{{ ucfirst($subtitle) }}</div>
                        @endif
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-3">
                    {{ $header ?? '' }}
                </div>
            </div>
        </div>

        <div class="-mt-8 px-4">
            <div class="bg-white rounded-lg shadow-sm {{ $contentPadding }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <!-- Left: labels & values (form-like) -->
                    <div>
                        <div class="text-xs text-gray-500">Profile</div>

                        <div class="mt-3">
                            <div class="text-xs text-gray-500">Roll No</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $user->student->roll_no ?? ($user->id ?? '—') }}</div>
                        </div>

                        <div class="mt-3">
                            <div class="text-xs text-gray-500">Semester</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $user->student->semester ?? '—' }}</div>
                        </div>

                        <div class="mt-3">
                            <div class="text-xs text-gray-500">Department</div>
                            <div class="text-base font-semibold text-gray-900">{{ $user->department ?? ($user->student->department ?? '—') }}</div>
                        </div>

                        <div class="mt-4">
                            <div class="text-xs text-gray-500">Joined</div>
                            <div class="text-sm text-gray-900">{{ optional($user->created_at)->format('M j, Y') ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Right: contact, about, status -->
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs text-gray-500">Contact</div>
                                <div class="mt-2 text-gray-900">{{ $user->email ?? '—' }}</div>
                                <div class="mt-1 text-gray-900">{{ $user->phone ?? '—' }}</div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="mt-2">
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

                        @if(!empty($user->bio))
                            <div class="mt-6">
                                <div class="text-xs text-gray-500">About</div>
                                <div class="mt-2 text-gray-700 text-sm">{{ $user->bio }}</div>
                            </div>
                        @endif

                        @if(isset($footer) && trim($footer) !== '')
                            <div class="mt-6">
                                {{ $footer }}
                            </div>
                        @else
                            <div class="mt-6">
                                <a href="{{ route('admin.students') }}" class="w-full inline-block text-center px-4 py-2 border rounded text-sm bg-white">Close</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

