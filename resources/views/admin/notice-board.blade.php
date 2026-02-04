@extends('admin.layouts.app')

@section('title', __('Notice Board'))

@section('content')
<div class="space-y-4">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded text-xs">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-xs">
        {{ session('error') }}
    </div>
    @endif

    <!-- Statistics Cards - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Total Notices -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">{{ __('Total Notices') }}</p>
                    <p class="text-xl font-bold text-gray-900 mt-0.5">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-lg">
                    <i class="bi bi-bell text-lg text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Published Notices -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">{{ __('Published') }}</p>
                    <p class="text-xl font-bold text-green-600 mt-0.5">{{ $stats['published'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-lg">
                    <i class="bi bi-check-circle text-lg text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Draft Notices -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">{{ __('Draft') }}</p>
                    <p class="text-xl font-bold text-orange-600 mt-0.5">{{ $stats['draft'] ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 p-2 rounded-lg">
                    <i class="bi bi-pencil-square text-lg text-orange-600"></i>
                </div>
            </div>
        </div>

        <!-- Scheduled Notices -->
        <div class="bg-white p-3 rounded shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-xs font-medium">{{ __('Scheduled') }}</p>
                    <p class="text-xl font-bold text-red-600 mt-0.5">{{ $stats['scheduled'] ?? 0 }}</p>
                </div>
                <div class="bg-red-100 p-2 rounded-lg">
                    <i class="bi bi-calendar-event text-lg text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions - Row 2 -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <form id="noticeFiltersForm" action="{{ route('admin.notice-board') }}" method="GET" class="flex items-center gap-2">
            <div class="flex-1 relative min-w-48">
                <i class="bi bi-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="noticeSearch" name="search" placeholder="Search notices..." value="{{ request('search') }}" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
            </div>
            <select name="semester" id="filterSemester" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Semesters</option>
                <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>2nd Semester</option>
                <option value="3" {{ request('semester') == '3' ? 'selected' : '' }}>3rd Semester</option>
                <option value="4" {{ request('semester') == '4' ? 'selected' : '' }}>4th Semester</option>
                <option value="5" {{ request('semester') == '5' ? 'selected' : '' }}>5th Semester</option>
                <option value="6" {{ request('semester') == '6' ? 'selected' : '' }}>6th Semester</option>
            </select>
            <select name="audience" id="filterAudience" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Audience</option>
                <option value="all" {{ request('audience') == 'all' ? 'selected' : '' }}>All</option>
                <option value="students" {{ request('audience') == 'students' ? 'selected' : '' }}>Students</option>
                <option value="faculty" {{ request('audience') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                <option value="parents" {{ request('audience') == 'parents' ? 'selected' : '' }}>Parents</option>
            </select>
            <select name="status" id="filterStatus" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                <option value="">All Status</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            </select>
<input type="date" name="date" id="filterDate" value="{{ request('date') }}" class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500" onchange="this.form.submit()">
            <button type="button" id="applyFiltersBtn" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium">Filter</button>
@if(request('search') || request('semester') || request('status') || request('audience') || request('date'))
            <a href="{{ route('admin.notice-board') }}" class="px-3 py-2 border border-gray-300 rounded text-xs hover:bg-gray-50 font-medium">Clear</a>
            @endif
        </form>

        <button onclick="openCreateNoticeModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
            <i class="bi bi-plus-lg"></i>
            <span>Add Notice</span>
        </button>
    </div>

    <!-- Notice Table -->
    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-left font-semibold text-gray-900">Notice Title</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Course</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Audience</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Semester</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Date Published</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Status</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices ?? [] as $notice)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2">
                            <div class="flex items-start gap-2">
                                <div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0 {{ $notice->indicator_color }}"></div>
                                <div>
                                    <p class="text-gray-900 font-medium">{{ $notice->title }}</p>
                                    <p class="text-gray-600 truncate max-w-xs">{{ Str::limit($notice->message, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700">
                            {{ $notice->subject ? ($notice->subject->subject_name . (isset($notice->subject->subject_code) && $notice->subject->subject_code ? ' - ' . $notice->subject->subject_code : '')) : '-' }}
                        </td>

                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex items-center gap-1 text-gray-700">
                                <i class="bi bi-people text-sm"></i>
                                {{ $notice->audience_text }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700">{{ $notice->semester ? ( $notice->semester == '1' ? '1st' : ($notice->semester == '2' ? '2nd' : ($notice->semester == '3' ? '3rd' : $notice->semester . 'th'))) : '-' }}</td>
                        <td class="px-3 py-2 text-center text-gray-700">{{ $notice->formatted_date }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $notice->status_badge_class }}">
                                {{ ucfirst($notice->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openViewNoticeModal({{ $notice->id }})" class="text-blue-600 hover:text-blue-800 transition" title="View">
                                    <i class="bi bi-eye text-sm"></i>
                                </button>
                                <button onclick="openEditNoticeModal({{ $notice->id }})" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                    <i class="bi bi-pencil text-sm"></i>
                                </button>
                                <form action="{{ route('admin.notice-board.destroy', $notice->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this notice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="bi bi-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-gray-500">
                            <i class="bi bi-inbox text-4xl mb-2 block"></i>
                            No notices found. Create your first notice!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($notices) && $notices->hasPages())
        <div class="px-3 py-2 border-t border-gray-200 flex items-center justify-between">
            <p class="text-xs text-gray-600">Showing {{ $notices->firstItem() ?? 0 }}-{{ $notices->lastItem() ?? 0 }} of {{ $notices->total() }} notices</p>
            <div class="flex gap-1">
                {{ $notices->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create Notice Modal -->
<div id="createNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-orange-500 px-4 py-3 flex items-center justify-between sticky top-0">
            <h2 class="text-white font-semibold text-sm">Create New Notice</h2>
            <button onclick="closeCreateNoticeModal()" class="text-white hover:text-gray-200 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <form action="{{ route('admin.notice-board.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            @csrf
            <!-- Notice Title -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Notice Title *</label>
                <input type="text" name="title" placeholder="Enter notice title" required class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notice Content -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Notice Content *</label>
                <textarea name="message" rows="4" placeholder="Enter detailed notice content..." required class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500"></textarea>
                @error('message')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Audience and Semester Row -->
            <div class="grid grid-cols-2 gap-3">
                <!-- Audience -->
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Audience *</label>
                    <select name="audience" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="all">All</option>
                        <option value="students">Students</option>
                        <option value="faculty">Faculty</option>
                        <option value="parents">Parents</option>
                    </select>
                </div>

                <!-- Semester -->
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Semester</label>
                    <select name="semester" id="createSemester" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All Semesters</option>
                        <option value="1">1st Semester</option>
                        <option value="2">2nd Semester</option>
                        <option value="3">3rd Semester</option>
                        <option value="4">4th Semester</option>
                        <option value="5">5th Semester</option>
                        <option value="6">6th Semester</option>
                    </select>
                </div>
            </div>

                <!-- Course / Subject -->
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Course (Subject)</label>
                    <select name="subject_id" id="createSubject" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="">All / Select Course</option>
                    </select>
                </div>

            <!-- File Upload -->
            <div>
                <label class="block text-xs font-medium text-gray-900 mb-1">Attachment (Optional)</label>
                <div class="relative">
                    <input type="file" name="file" id="noticeFile" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                </div>
                <p class="text-xs text-gray-500 mt-1">Max size: 10MB. Allowed: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR</p>
            </div>

            <!-- Publish Date and Status Row -->
            <div class="grid grid-cols-2 gap-3">
<!-- Publish Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Publish Date</label>
                    <input type="date" name="published_at" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-medium text-gray-900 mb-1">Status *</label>
                    <select name="status" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
            </div>

            <!-- Important Notice Checkbox -->
            <div class="flex items-center">
                <input type="checkbox" name="is_important" id="importantNotice" value="1" class="w-4 h-4 border-gray-300 rounded text-red-600 focus:ring-red-500">
                <label for="importantNotice" class="ml-2 text-xs text-gray-700">Mark as Important Notice</label>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2 sticky bottom-0">
                <button type="button" onclick="closeCreateNoticeModal()" class="px-3 py-1.5 border border-gray-300 rounded text-xs font-medium text-gray-900 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition">
                    Create Notice
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Notice Modal -->
<div id="viewNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto flex flex-col">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between sticky top-0 flex-shrink-0 border-b-4 border-red-800">
            <div class="flex items-center gap-3">
                <i class="bi bi-bell-fill text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">Notice Details</h2>
            </div>
            <button onclick="closeViewNoticeModal()" class="text-white hover:text-gray-100 transition hover:scale-110">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5" id="viewNoticeContent">
            <!-- Content will be loaded via AJAX -->
            <div class="text-center py-12">
                <i class="bi bi-hourglass-split text-5xl text-gray-400 animate-spin mb-3"></i>
                <p class="text-gray-500 text-sm">Loading notice details...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center gap-3 flex-shrink-0">
            <div class="text-xs text-gray-600" id="noticeMetaFooter">
                <!-- Meta info will be populated -->
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="closeViewNoticeModal()" class="px-4 py-2 border border-gray-300 rounded text-xs font-medium text-gray-700 hover:bg-gray-100 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Notice Modal -->
<div id="editNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto flex flex-col">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between sticky top-0 flex-shrink-0 border-b-4 border-red-800">
            <div class="flex items-center gap-3">
                <i class="bi bi-pencil-square text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">Edit Notice</h2>
            </div>
            <button onclick="closeEditNoticeModal()" class="text-white hover:text-gray-100 transition hover:scale-110">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <form id="editNoticeForm" action="" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-5">
            @csrf
            @method('PUT')
            
            <!-- Notice Title Section -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Notice Title *</label>
                <input type="text" name="title" id="editTitle" placeholder="Enter notice title" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
            </div>

            <!-- Notice Content Section -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Content *</label>
                <textarea name="message" id="editMessage" rows="5" placeholder="Enter detailed notice content..." required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition resize-none"></textarea>
            </div>

            <!-- Course/Subject Section -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Course / Subject *</label>
                <select name="subject_id" id="editSubject" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                    <option value="">Select Course / Subject</option>
                </select>
            </div>

            <!-- Audience & Semester Row -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Audience *</label>
                    <select name="audience" id="editAudience" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                        <option value="all">All</option>
                        <option value="students">Students</option>
                        <option value="faculty">Faculty</option>
                        <option value="parents">Parents</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Semester</label>
                    <select name="semester" id="editSemester" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                        <option value="">All Semesters</option>
                        <option value="1">1st Semester</option>
                        <option value="2">2nd Semester</option>
                        <option value="3">3rd Semester</option>
                        <option value="4">4th Semester</option>
                        <option value="5">5th Semester</option>
                        <option value="6">6th Semester</option>
                    </select>
                </div>
            </div>

            <!-- Publish Date & Status Row -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Publish Date</label>
<input type="date" name="published_at" id="editPublishedAt" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">Status *</label>
                    <select name="status" id="editStatus" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
            </div>

            <!-- Important Notice Toggle -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center gap-3">
                <input type="checkbox" name="is_important" id="editImportantNotice" value="1" class="w-5 h-5 border-red-300 rounded text-red-600 focus:ring-2 focus:ring-red-500 cursor-pointer">
                <label for="editImportantNotice" class="text-sm font-medium text-red-900 cursor-pointer flex-1">
                    <i class="bi bi-exclamation-circle-fill mr-1.5"></i>Mark as Important Notice
                </label>
            </div>

            <!-- Existing File Attachment Section -->
            <div id="editExistingFileSection" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <!-- Existing file info will be loaded here -->
            </div>

            <!-- File Upload Section -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide">
                    <i class="bi bi-file-earmark-arrow-up mr-1"></i>Attachment (Optional)
                </label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-red-500 hover:bg-red-50 transition cursor-pointer group" onclick="document.getElementById('editNoticeFile').click()">
                    <input type="file" name="file" id="editNoticeFile" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                    <div class="text-center">
                        <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 group-hover:text-red-500 transition mb-2"></i>
                        <p class="text-xs font-medium text-gray-700">Click to upload or drag & drop</p>
                        <p class="text-xs text-gray-500 mt-1">Max 10MB • PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR</p>
                    </div>
                </div>
                <p class="text-xs text-amber-600 mt-2 hidden" id="editFileReplaceInfo">
                    <i class="bi bi-info-circle mr-1"></i>Uploading a new file will replace the existing attachment
                </p>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 flex justify-between items-center gap-3 flex-shrink-0">
            <p class="text-xs text-gray-600">* Required fields</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeEditNoticeModal()" class="px-5 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="button" onclick="document.getElementById('editNoticeForm').submit()" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition flex items-center gap-2">
                    <i class="bi bi-check-lg"></i>Update Notice
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Create Notice Modal Functions
    function openCreateNoticeModal() {
        document.getElementById('createNoticeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // load subjects for the currently selected semester
        loadSubjectsForCreate();
    }

    // Debounced search and Apply button for filters
    (function() {
        const searchInput = document.getElementById('noticeSearch');
        const applyBtn = document.getElementById('applyFiltersBtn');
        const filterForm = document.getElementById('noticeFiltersForm');
        let debounceTimer = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filterForm.submit();
                }, 600);
            });
        }

        if (applyBtn) {
            applyBtn.addEventListener('click', function() {
                filterForm.submit();
            });
        }

        // Submit when BS date filter changes
        document.getElementById('filterDateBs')?.addEventListener('change', function() {
            this.closest('form').submit();
        });
    })();

    function closeCreateNoticeModal() {
        document.getElementById('createNoticeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // View Notice Modal Functions
    function openViewNoticeModal(id) {
        document.getElementById('viewNoticeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Load notice details via AJAX
        fetch(`/admin/notice-board/${id}`)
            .then(response => response.json())
            .then(data => {
                const notice = data.notice;
                
                // Format semester text
                let semesterText = notice.semester ? `${notice.semester}<sup>th</sup> Semester` : 'All Semesters';
                
                // Get subject info
                let subjectText = notice.subject ? notice.subject.subject_name : 'General Notice';
                let subjectCode = notice.subject ? notice.subject.subject_code : '';
                let subjectDisplay = subjectCode ? `<strong>${subjectText}</strong> <span class="text-gray-600">(${subjectCode})</span>` : `<strong>${subjectText}</strong>`;
                
                // Format importance flag
                let importanceDisplay = notice.is_important ? 
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-red-100 text-red-700 font-semibold"><i class="bi bi-exclamation-circle-fill"></i> Important Notice</span>' :
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700 font-medium"><i class="bi bi-info-circle"></i> Standard Notice</span>';
                
                // Format file attachment
                let fileDisplay = '';
                if (notice.file_name && notice.file_path) {
                    const downloadUrl = `/storage/${notice.file_path}`;
                    fileDisplay = `
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-file-earmark-text text-blue-600 text-lg"></i>
                                <p class="text-xs font-semibold text-blue-900 uppercase tracking-wide">Attachment</p>
                            </div>
                            <div class="flex items-center justify-between gap-3 p-3 bg-white rounded border border-blue-200">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <i class="bi bi-file text-blue-600 text-sm"></i>
                                    <a href="${downloadUrl}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-600 hover:text-blue-800 underline font-medium truncate">
                                        ${notice.file_name}
                                    </a>
                                </div>
                                <a href="${downloadUrl}" download class="text-blue-600 hover:text-blue-800 text-lg hover:scale-110 transition flex-shrink-0" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }

                // Get formatted dates
                // Prefer BS dates for display when available
                const createdDate = new Date(notice.created_at);
                const updatedDate = new Date(notice.updated_at);
                const createdDateBsLatin = notice.created_at_bs_latin || notice.created_at_bs_pretty || notice.created_at_bs || null;
                const updatedDateBsLatin = notice.updated_at_bs_latin || notice.updated_at_bs_pretty || notice.updated_at_bs || null;
                const createdDateStr = createdDateBsLatin ? createdDateBsLatin : createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const createdTimeStr = createdDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const updatedDateStr = updatedDateBsLatin ? updatedDateBsLatin : updatedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const updatedTimeStr = updatedDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                
                // Update footer meta
                document.getElementById('noticeMetaFooter').innerHTML = `
                    <div class="space-y-1">
                        <p class="text-gray-600"><span class="font-medium">Created:</span> ${createdDateStr} at ${createdTimeStr}</p>
                        <p class="text-gray-600"><span class="font-medium">Updated:</span> ${updatedDateStr} at ${updatedTimeStr}</p>
                    </div>
                `;
                
                document.getElementById('viewNoticeContent').innerHTML = `
                    <!-- Status & Importance Badge Row -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-block px-3 py-1.5 rounded-full text-xs font-semibold bg-${data.status_badge_class.split('bg-')[1]?.split(' ')[0]}-100 text-${data.status_badge_class.split('text-')[1]?.split(' ')[0]}-700">
                            <i class="bi bi-dot mr-1"></i>${notice.status.charAt(0).toUpperCase() + notice.status.slice(1)}
                        </span>
                        ${importanceDisplay}
                    </div>

                    <!-- Notice Title Section -->
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Title</p>
                        <p class="text-xl font-bold text-gray-900 leading-snug">${notice.title}</p>
                    </div>

                    <!-- Course & Semester Info -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Course</p>
                            <p class="text-sm text-gray-900">${subjectDisplay}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Semester</p>
                            <p class="text-sm text-gray-900">${semesterText}</p>
                        </div>
                    </div>

                    <!-- Metadata Row -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Audience</p>
                            <p class="text-sm text-gray-900 font-medium">${data.audience_text}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-1">Created By</p>
                            <p class="text-sm text-gray-900 font-medium">${notice.creator ? notice.creator.name : 'System Admin'}</p>
                        </div>
                    </div>

                    <!-- Notice Content Section -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-3">Content</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${notice.message}</p>
                    </div>

                    <!-- File Attachment Section -->
                    ${fileDisplay}

                    <!-- Published Date Info (if applicable) -->
                    ${(notice.published_at_bs_latin || notice.published_at_bs_pretty || notice.published_at_bs) ? `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-xs text-green-700 font-medium">
                                <i class="bi bi-check-circle-fill mr-1"></i>
                                Published on ${notice.published_at_bs_latin || notice.published_at_bs_pretty || notice.published_at_bs}
                            </p>
                        </div>
                    ` : ''}
                `;
            })
            .catch(error => {
                console.error('Error loading notice:', error);
                document.getElementById('viewNoticeContent').innerHTML = `
                    <div class="text-center py-12">
                        <i class="bi bi-exclamation-triangle text-5xl text-red-400 mb-3"></i>
                        <p class="text-red-600 font-semibold mb-1">Failed to Load Notice</p>
                        <p class="text-gray-600 text-sm">There was an error loading the notice details. Please try again.</p>
                    </div>
                `;
            });
    }

    function closeViewNoticeModal() {
        document.getElementById('viewNoticeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Edit Notice Modal Functions
    function openEditNoticeModal(id) {
        // Load notice details first
        fetch(`/admin/notice-board/${id}`)
            .then(response => response.json())
            .then(data => {
                const notice = data.notice;
                
// Populate form fields
                document.getElementById('editTitle').value = notice.title;
                document.getElementById('editMessage').value = notice.message;
                document.getElementById('editAudience').value = notice.audience;
                document.getElementById('editSemester').value = notice.semester || '';
                document.getElementById('editStatus').value = notice.status;
                document.getElementById('editImportantNotice').checked = notice.is_important;

                // Populate publish date (AD format)
                if (notice.published_at) {
                    const dateObj = new Date(notice.published_at);
                    const year = dateObj.getFullYear();
                    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const day = String(dateObj.getDate()).padStart(2, '0');
                    document.getElementById('editPublishedAt').value = `${year}-${month}-${day}`;
                }

                // Display existing file attachment if present
                const existingFileSection = document.getElementById('editExistingFileSection');
                const fileReplaceInfo = document.getElementById('editFileReplaceInfo');
                
                if (notice.file_name && notice.file_path) {
                    const downloadUrl = `/storage/${notice.file_path}`;
                    existingFileSection.innerHTML = `
                        <p class="text-xs text-gray-600 font-medium mb-2">CURRENT ATTACHMENT</p>
                        <div class="flex items-center justify-between px-2 py-1.5 bg-green-50 border border-green-200 rounded">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <i class="bi bi-file-earmark text-green-600 flex-shrink-0"></i>
                                <a href="${downloadUrl}" target="_blank" rel="noopener noreferrer" class="text-xs text-green-600 hover:text-green-800 underline truncate">
                                    ${notice.file_name}
                                </a>
                                <a href="${downloadUrl}" download class="text-green-600 hover:text-green-800 text-xs flex-shrink-0">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    `;
                    fileReplaceInfo.style.display = 'block';
                } else {
                    existingFileSection.innerHTML = '';
                    fileReplaceInfo.style.display = 'none';
                }

                // Clear the file input
                document.getElementById('editNoticeFile').value = '';

                // Update form action
                document.getElementById('editNoticeForm').action = `/admin/notice-board/${id}`;

                // load subjects for the edit modal and select the notice's subject
                loadSubjectsForEdit(notice.subject_id);

                // Show modal
                document.getElementById('editNoticeModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error loading notice:', error);
                alert('Failed to load notice details for editing');
            });
    }

    function closeEditNoticeModal() {
        document.getElementById('editNoticeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.getElementById('createNoticeModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeCreateNoticeModal();
        }
    });

    document.getElementById('viewNoticeModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeViewNoticeModal();
        }
    });

    document.getElementById('editNoticeModal')?.addEventListener('click', function(event) {
        if (event.target === this) {
            closeEditNoticeModal();
        }
    });

    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateNoticeModal();
            closeViewNoticeModal();
            closeEditNoticeModal();
        }
    });

    // Load subjects for the create modal based on selected semester
    function loadSubjectsForCreate() {
        const sem = document.getElementById('createSemester')?.value || '';
        const select = document.getElementById('createSubject');
        if (!select) return;
        select.innerHTML = '<option value="">Loading...</option>';

        fetch(`/admin/notice-board/subjects/by-semester?semester=${encodeURIComponent(sem)}`)
            .then(res => res.json())
            .then(data => {
                select.innerHTML = '<option value="">All / Select Course</option>';
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.subject_name + (s.subject_code ? (' - ' + s.subject_code) : '');
                    opt.setAttribute('data-semester', s.semester || '');
                    select.appendChild(opt);
                });
            })
            .catch(err => {
                console.error('Failed to load subjects:', err);
                select.innerHTML = '<option value="">Failed to load subjects</option>';
            });
    }

    // Load subjects for the edit modal and optionally select a subject id
    function loadSubjectsForEdit(selectedId) {
        const sem = document.getElementById('editSemester')?.value || '';
        const select = document.getElementById('editSubject');
        if (!select) return;
        select.innerHTML = '<option value="">Loading...</option>';

        fetch(`/admin/notice-board/subjects/by-semester?semester=${encodeURIComponent(sem)}`)
            .then(res => res.json())
            .then(data => {
                select.innerHTML = '<option value="">All / Select Course</option>';
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.subject_name + (s.subject_code ? (' - ' + s.subject_code) : '');
                    opt.setAttribute('data-semester', s.semester || '');
                    if (selectedId && String(s.id) === String(selectedId)) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
            })
            .catch(err => {
                console.error('Failed to load subjects for edit:', err);
                select.innerHTML = '<option value="">Failed to load subjects</option>';
            });
    }

    // Re-load subjects when semester selection changes
    document.getElementById('createSemester')?.addEventListener('change', function() {
        loadSubjectsForCreate();
    });

    document.getElementById('editSemester')?.addEventListener('change', function() {
        // reload and clear selection
        loadSubjectsForEdit();
    });
</script>
@endsection

