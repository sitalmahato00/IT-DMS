@extends('admin.layouts.app')

@section('title', __('Notice Board'))

@section('content')
<div class="space-y-4">
    <!-- Global Loader Overlay -->
    <div id="globalLoader" class="fixed inset-0 z-[9999] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm hidden flex items-center justify-center">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto mb-4"></div>
            <p id="loaderText" class="text-gray-600 dark:text-gray-400 font-medium">Loading...</p>
        </div>
    </div>

    <!-- Professional Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all duration-300 animate-scale-up">
            <div id="confirmHeader" class="relative h-20 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/20 flex items-center justify-center">
                <div id="confirmIconContainer" class="absolute h-24 w-24 rounded-full flex items-center justify-center" style="transform: translateY(50%);">
                    <i id="confirmIcon" class="text-4xl"></i>
                </div>
            </div>
            <div class="pt-16 px-6 pb-6 text-center">
                <h3 id="confirmTitle" class="text-xl font-bold text-gray-900 dark:text-white mb-2">Confirm Action</h3>
                <p id="confirmMessage" class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-8">Are you sure you want to proceed?</p>
                <div class="flex justify-center gap-3">
                    <button id="confirmCancel" class="flex-1 px-4 py-2.5 border-2 border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-gray-400 transition-all duration-150 active:scale-95">
                        <i class="bi bi-x-circle mr-1"></i>Cancel
                    </button>
                    <button id="confirmOk" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all duration-150 active:scale-95 shadow-lg hover:shadow-xl">
                        <i id="confirmOkIcon" class="bi bi-check-circle mr-1"></i><span id="confirmOkText">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Header - Using standardized component --}}
    @include('admin.components.admin-page-header', [
        'title' => 'Notice Board',
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Notice Board']
        ],
        'addButton' => [
            'label' => 'Create Notice',
            'onclick' => 'openCreateNoticeModal()'
        ]
    ])

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-600 dark:text-green-300 px-4 py-2 rounded text-xs">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-600 dark:text-red-300 px-4 py-2 rounded text-xs">
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Cards - Using standardized component --}}
    @include('admin.components.admin-stats-cards', [
        'cards' => [
            ['title' => 'Total Notices', 'value' => $stats['total'] ?? 0, 'icon' => 'bi-bell', 'color' => 'blue'],
            ['title' => 'Published', 'value' => $stats['published'] ?? 0, 'icon' => 'bi-check-circle', 'color' => 'green'],
            ['title' => 'Draft', 'value' => $stats['draft'] ?? 0, 'icon' => 'bi-pencil-square', 'color' => 'orange'],
            ['title' => 'Scheduled', 'value' => $stats['scheduled'] ?? 0, 'icon' => 'bi-calendar-event', 'color' => 'purple'],
        ]
    ])

    {{-- Filter Card - Using standardized component --}}
    @include('admin.components.admin-filter-card', [
        'formAction' => route('admin.notice-board'),
        'filters' => [
            ['name' => 'search', 'type' => 'text', 'placeholder' => 'Search notices...', 'value' => request('search'), 'label' => 'Search'],
            ['name' => 'semester', 'type' => 'select', 'options' => ['' => 'All Semesters', '1' => '1st Semester', '2' => '2nd Semester', '3' => '3rd Semester', '4' => '4th Semester', '5' => '5th Semester', '6' => '6th Semester'], 'value' => request('semester'), 'label' => 'Semester'],
            ['name' => 'audience', 'type' => 'select', 'options' => ['' => 'All Audience', 'all' => 'All', 'students' => 'Students', 'teachers' => 'Teachers', 'parents' => 'Parents'], 'value' => request('audience'), 'label' => 'Audience'],
            ['name' => 'status', 'type' => 'select', 'options' => ['' => 'All Status', 'published' => 'Published', 'draft' => 'Draft', 'scheduled' => 'Scheduled'], 'value' => request('status'), 'label' => 'Status'],
        ],
        'showReset' => true,
        'resetRoute' => route('admin.notice-board')
    ])

    {{-- Table Card --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Notices List</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notices->total() }} total notices</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                        <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-gray-100">Title</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-900 dark:text-gray-100">Category</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Semester</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Audience</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Status</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Date</th>
                        <th class="px-3 py-2 text-center font-semibold text-gray-900 dark:text-gray-100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                    <tr class="border-b border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50">
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-bell text-lg text-blue-600 dark:text-blue-400"></i>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $notice->title }}</span>
                            </div>
                            @if($notice->description)
                            <p class="text-gray-500 dark:text-gray-400 text-[10px] mt-1 truncate max-w-[200px]">{{ $notice->description }}</p>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                                {{ $notice->category->name ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">
                            {{ $notice->semester ? 'Semester ' . $notice->semester : 'All' }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                                {{ ucfirst($notice->audience) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            @php
                                $statusClass = match($notice->status) {
                                    'published' => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300',
                                    'draft' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300',
                                    'scheduled' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
                                    default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($notice->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-gray-700 dark:text-gray-300">
                            {{ $notice->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button onclick='openViewNoticeModal({{ $notice->id }})' class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition font-medium inline-flex items-center gap-1">
                                <i class="bi bi-eye"></i>View
                            </button>
                            <span class="mx-1 text-gray-300">|</span>
                            <button onclick='openEditNoticeModal({{ $notice->id }})' class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition font-medium inline-flex items-center gap-1">
                                <i class="bi bi-pencil"></i>Edit
                            </button>
                            <span class="mx-1 text-gray-300">|</span>
                            <button onclick="deleteNotice({{ $notice->id }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition font-medium inline-flex items-center gap-1">
                                <i class="bi bi-trash"></i>Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-bell-slash text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                <p>No notices found</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Create your first notice to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notices->hasPages())
        <div class="px-3 py-3 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
            @include('admin.components.admin-pagination', [
                'paginator' => $notices,
                'route' => route('admin.notice-board')
            ])
        </div>
        @endif
    </div>
</div>

<!-- Create Notice Modal -->
<div id="createNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-red-600 to-orange-500 px-4 py-3 flex items-center justify-between sticky top-0">
            <h2 class="text-white font-semibold text-sm">Create New Notice</h2>
            <button onclick="closeCreateNoticeModal()" class="text-white hover:text-gray-200 transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.notice-board.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Notice Title *</label>
                <input type="text" name="title" placeholder="Enter notice title" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Notice Content *</label>
                <textarea name="message" rows="4" placeholder="Enter detailed notice content..." required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Audience *</label>
                    <select name="audience" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All</option>
                        <option value="students">Students</option>
                        <option value="faculty">Faculty</option>
                        <option value="parents">Parents</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Semester</label>
                    <select name="semester" id="createSemester" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Course (Subject)</label>
                <select name="subject_id" id="createSubject" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All / Select Course</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Attachment (Optional)</label>
                <input type="file" name="file" id="noticeFile" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                <p class="text-xs text-gray-500 mt-1">Max size: 10MB. Allowed: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Publish Date (AD)</label>
                    <input type="date" id="createPublishedAtAd" name="published_at" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1 mt-2">Publish Date (BS)</label>
                    <input type="text" id="createPublishedAtBs" name="published_at_bs" placeholder="YYYY-MM-DD" maxlength="10" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Status *</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_important" id="importantNotice" value="1" class="w-4 h-4 border-gray-300 rounded text-blue-600 focus:ring-blue-500">
                <label for="importantNotice" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Mark as Important Notice</label>
            </div>
            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-700 border-t border-gray-200 dark:border-slate-600 flex justify-end gap-2 sticky bottom-0">
                <button type="button" onclick="closeCreateNoticeModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-500 rounded-md text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                    Create Notice
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Notice Modal -->
<div id="viewNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto flex flex-col">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between sticky top-0 flex-shrink-0 border-b-4 border-red-800">
            <div class="flex items-center gap-3">
                <i class="bi bi-bell-fill text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">Notice Details</h2>
            </div>
            <button onclick="closeViewNoticeModal()" class="text-white hover:text-gray-100 transition hover:scale-110">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 space-y-5" id="viewNoticeContent">
            <div class="text-center py-12">
                <i class="bi bi-hourglass-split text-5xl text-gray-400 animate-spin mb-3"></i>
                <p class="text-gray-500 text-sm">Loading notice details...</p>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 px-6 py-4 flex justify-between items-center gap-3 flex-shrink-0">
            <div class="text-xs text-gray-600 dark:text-gray-400" id="noticeMetaFooter"></div>
            <div class="flex gap-2">
                <button type="button" onclick="closeViewNoticeModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-500 rounded text-xs font-medium text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-slate-600 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Notice Modal -->
<div id="editNoticeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto flex flex-col">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 flex items-center justify-between sticky top-0 flex-shrink-0 border-b-4 border-red-800">
            <div class="flex items-center gap-3">
                <i class="bi bi-pencil-square text-white text-xl"></i>
                <h2 class="text-white font-bold text-lg">Edit Notice</h2>
            </div>
            <button onclick="closeEditNoticeModal()" class="text-white hover:text-gray-100 transition hover:scale-110">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <form id="editNoticeForm" action="" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-5">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Notice Title *</label>
                <input type="text" name="title" id="editTitle" placeholder="Enter notice title" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Content *</label>
                <textarea name="message" id="editMessage" rows="5" placeholder="Enter detailed notice content..." required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Course / Subject *</label>
                <select name="subject_id" id="editSubject" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Select Course / Subject</option>
                </select>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Audience *</label>
                    <select name="audience" id="editAudience" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="all">All</option>
                        <option value="students">Students</option>
                        <option value="faculty">Faculty</option>
                        <option value="parents">Parents</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Semester</label>
                    <select name="semester" id="editSemester" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Publish Date</label>
                    <input type="date" name="published_at" id="editPublishedAt" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">Status *</label>
                    <select name="status" id="editStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-100 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 flex items-center gap-3">
                <input type="checkbox" name="is_important" id="editImportantNotice" value="1" class="w-5 h-5 border-red-300 rounded text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <label for="editImportantNotice" class="text-sm font-medium text-red-900 dark:text-red-300 cursor-pointer flex-1">
                    <i class="bi bi-exclamation-circle-fill mr-1.5"></i>Mark as Important Notice
                </label>
            </div>
            <div id="editExistingFileSection" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4"></div>
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-100 uppercase tracking-wide">
                    <i class="bi bi-file-earmark-arrow-up mr-1"></i>Attachment (Optional)
                </label>
                <div class="relative border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg p-4 hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition cursor-pointer group" onclick="document.getElementById('editNoticeFile').click()">
                    <input type="file" name="file" id="editNoticeFile" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                    <div class="text-center">
                        <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 group-hover:text-blue-500 transition mb-2"></i>
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-100">Click to upload or drag & drop</p>
                        <p class="text-xs text-gray-500 mt-1">Max 10MB • PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR</p>
                    </div>
                </div>
                <p class="text-xs text-amber-600 mt-2 hidden" id="editFileReplaceInfo">
                    <i class="bi bi-info-circle mr-1"></i>Uploading a new file will replace the existing attachment
                </p>
            </div>
        </form>
        <div class="border-t border-gray-200 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 px-6 py-4 flex justify-between items-center gap-3 flex-shrink-0">
            <p class="text-xs text-gray-600 dark:text-gray-400">* Required fields</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeEditNoticeModal()" class="px-4 py-2 border border-gray-300 dark:border-slate-500 rounded-md text-sm font-medium text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-slate-600 transition">
                    Cancel
                </button>
                <button type="button" onclick="document.getElementById('editNoticeForm').submit()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                    <i class="bi bi-check-lg"></i>Update Notice
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Bidirectional create-notice AD<->BS sync
    (function(){
        const adInput = document.getElementById('createPublishedAtAd');
        const bsInput = document.getElementById('createPublishedAtBs');
        if (!adInput || !bsInput) return;

        let syncing = false;

        async function convertAdToBs(adDate) {
            if (!adDate) return '';
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/admin/convert/ad-to-bs', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ date: adDate })
                });
                if (!res.ok) return '';
                const data = await res.json();
                return data.bs || '';
            } catch (e) { return ''; }
        }

        async function convertBsToAd(bsDate) {
            if (!bsDate) return '';
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/admin/convert/bs-to-ad', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ date: bsDate })
                });
                if (!res.ok) return '';
                const data = await res.json();
                return data.ad || '';
            } catch (e) { return ''; }
        }

        function debounce(fn, wait){
            let t;
            return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); };
        }

        const onAdChanged = debounce(async function(){
            if (syncing) return;
            const val = adInput.value;
            if (!val) { syncing = true; bsInput.value = ''; syncing = false; return; }
            syncing = true;
            const bs = await convertAdToBs(val);
            if (bs) bsInput.value = bs; else bsInput.value = '';
            syncing = false;
        }, 250);

        const onBsChanged = debounce(async function(){
            if (syncing) return;
            const val = bsInput.value;
            if (!val) { syncing = true; adInput.value = ''; syncing = false; return; }
            syncing = true;
            const ad = await convertBsToAd(val);
            if (ad && /^\d{4}-\d{2}-\d{2}$/.test(ad)) adInput.value = ad;
            syncing = false;
        }, 300);

        adInput.addEventListener('change', onAdChanged);
        adInput.addEventListener('input', onAdChanged);
        bsInput.addEventListener('input', onBsChanged);
        bsInput.addEventListener('change', onBsChanged);
    })();

    // Modal Functions
    function openCreateNoticeModal() {
        document.getElementById('createNoticeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        loadSubjectsForCreate();
    }

    function closeCreateNoticeModal() {
        document.getElementById('createNoticeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openViewNoticeModal(id) {
        document.getElementById('viewNoticeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        fetch(`/admin/notice-board/${id}`)
            .then(response => response.json())
            .then(data => {
                const notice = data.notice;
                let semesterText = notice.semester ? `${notice.semester}<sup>th</sup> Semester` : 'All Semesters';
                let subjectText = notice.subject ? notice.subject.subject_name : 'General Notice';
                let subjectCode = notice.subject ? notice.subject.subject_code : '';
                let subjectDisplay = subjectCode ? `<strong>${subjectText}</strong> <span class="text-gray-600">(${subjectCode})</span>` : `<strong>${subjectText}</strong>`;
                let importanceDisplay = notice.is_important ? 
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 font-semibold"><i class="bi bi-exclamation-circle-fill"></i> Important Notice</span>' :
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium"><i class="bi bi-info-circle"></i> Standard Notice</span>';
                
                let fileDisplay = '';
                if (notice.file_name && notice.file_path) {
                    const downloadUrl = `/storage/${notice.file_path}`;
                    fileDisplay = `
                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="bi bi-file-earmark-text text-blue-600 text-lg"></i>
                                <p class="text-xs font-semibold text-blue-900 dark:text-blue-300 uppercase tracking-wide">Attachment</p>
                            </div>
                            <div class="flex items-center justify-between gap-3 p-3 bg-white dark:bg-slate-700 rounded border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <i class="bi bi-file text-blue-600"></i>
                                    <a href="${downloadUrl}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 underline font-medium truncate">
                                        ${notice.file_name}
                                    </a>
                                </div>
                                <a href="${downloadUrl}" download class="text-blue-600 hover:text-blue-800 text-lg hover:scale-110 transition flex-shrink-0">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }

                const createdDate = new Date(notice.created_at);
                const updatedDate = new Date(notice.updated_at);
                const createdDateStr = createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const createdTimeStr = createdDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const updatedDateStr = updatedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const updatedTimeStr = updatedDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                
                document.getElementById('noticeMetaFooter').innerHTML = `
                    <div class="space-y-1">
                        <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Created:</span> ${createdDateStr} at ${createdTimeStr}</p>
                        <p class="text-gray-600 dark:text-gray-400"><span class="font-medium">Updated:</span> ${updatedDateStr} at ${updatedTimeStr}</p>
                    </div>
                `;
                
                document.getElementById('viewNoticeContent').innerHTML = `
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-block px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300">
                            <i class="bi bi-dot mr-1"></i>${notice.status.charAt(0).toUpperCase() + notice.status.slice(1)}
                        </span>
                        ${importanceDisplay}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Title</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white leading-snug">${notice.title}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700 p-4 rounded-lg border border-gray-200 dark:border-slate-600 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Course</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100">${subjectDisplay}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Semester</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100">${semesterText}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-700 p-4 rounded-lg border border-gray-200 dark:border-slate-600 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Audience</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">${data.audience_text}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Created By</p>
                            <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">${notice.creator ? notice.creator.name : 'System Admin'}</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg p-4">
                        <p class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Content</p>
                        <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed whitespace-pre-wrap">${notice.message}</p>
                    </div>
                    ${fileDisplay}
                `;
            })
            .catch(error => {
                console.error('Error loading notice:', error);
                document.getElementById('viewNoticeContent').innerHTML = `
                    <div class="text-center py-12">
                        <i class="bi bi-exclamation-triangle text-5xl text-red-400 mb-3"></i>
                        <p class="text-red-600 font-semibold mb-1">Failed to Load Notice</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">There was an error loading the notice details. Please try again.</p>
                    </div>
                `;
            });
    }

    function closeViewNoticeModal() {
        document.getElementById('viewNoticeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditNoticeModal(id) {
        fetch(`/admin/notice-board/${id}`)
            .then(response => response.json())
            .then(data => {
                const notice = data.notice;
                document.getElementById('editTitle').value = notice.title;
                document.getElementById('editMessage').value = notice.message;
                document.getElementById('editAudience').value = notice.audience;
                document.getElementById('editSemester').value = notice.semester || '';
                document.getElementById('editStatus').value = notice.status;
                document.getElementById('editImportantNotice').checked = notice.is_important;

                if (notice.published_at) {
                    const dateObj = new Date(notice.published_at);
                    const year = dateObj.getFullYear();
                    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const day = String(dateObj.getDate()).padStart(2, '0');
                    document.getElementById('editPublishedAt').value = `${year}-${month}-${day}`;
                }

                const existingFileSection = document.getElementById('editExistingFileSection');
                const fileReplaceInfo = document.getElementById('editFileReplaceInfo');
                
                if (notice.file_name && notice.file_path) {
                    const downloadUrl = `/storage/${notice.file_path}`;
                    existingFileSection.innerHTML = `
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mb-2">CURRENT ATTACHMENT</p>
                        <div class="flex items-center justify-between px-2 py-1.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <i class="bi bi-file-earmark text-green-600 flex-shrink-0"></i>
                                <a href="${downloadUrl}" target="_blank" class="text-xs text-green-600 hover:text-green-800 underline truncate">
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

                document.getElementById('editNoticeFile').value = '';
                document.getElementById('editNoticeForm').action = `/admin/notice-board/${id}`;
                loadSubjectsForEdit(notice.subject_id);

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
        if (event.target === this) closeCreateNoticeModal();
    });
    document.getElementById('viewNoticeModal')?.addEventListener('click', function(event) {
        if (event.target === this) closeViewNoticeModal();
    });
    document.getElementById('editNoticeModal')?.addEventListener('click', function(event) {
        if (event.target === this) closeEditNoticeModal();
    });

    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateNoticeModal();
            closeViewNoticeModal();
            closeEditNoticeModal();
        }
    });

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
                    select.appendChild(opt);
                });
            })
            .catch(err => {
                select.innerHTML = '<option value="">Failed to load subjects</option>';
            });
    }

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
                    if (selectedId && String(s.id) === String(selectedId)) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
            })
            .catch(err => {
                select.innerHTML = '<option value="">Failed to load subjects</option>';
            });
    }

    document.getElementById('createSemester')?.addEventListener('change', function() {
        loadSubjectsForCreate();
    });

    document.getElementById('editSemester')?.addEventListener('change', function() {
        loadSubjectsForEdit();
    });

    function deleteNotice(id) {
        if (!confirm('Are you sure you want to delete this notice?')) return;
        
        showLoading('Deleting notice...');
        
        fetch(`/admin/notice-board/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Notice deleted successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Failed to delete notice', 'error');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('An error occurred while deleting notice', 'error');
        });
    }

    // Loading helper functions
    function showLoading(message = 'Loading...') {
        const loader = document.getElementById('globalLoader');
        const loaderText = document.getElementById('loaderText');
        if (loader) {
            loader.classList.remove('hidden');
            if (loaderText) loaderText.textContent = message;
        }
    }

    function hideLoading() {
        const loader = document.getElementById('globalLoader');
        if (loader) loader.classList.add('hidden');
    }

    function showToast(message, type = 'success') {
        // Simple toast implementation - using Laravel session flash message
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[9999] px-4 py-3 rounded-lg shadow-lg text-sm font-medium transform transition-all duration-300 translate-y-2 opacity-0 ${type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        }, 10);
        
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endsection
