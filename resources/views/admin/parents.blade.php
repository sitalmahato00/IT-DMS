@extends('admin.layouts.app')

@section('title', 'Parents')

@section('content')
<div class="space-y-4">
    <!-- Stats Grid - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stats-card title="Total Parents" :value="isset($parents) ? $parents->total() : 0" icon="bi bi-people" color="blue" />
        <x-stats-card title="Active" :value="isset($parents) ? $parents->where('status','active')->count() : 0" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="Pending" :value="isset($parents) ? $parents->where('status','pending')->count() : 0" icon="bi bi-exclamation-circle" color="yellow" />
        <x-stats-card title="Inactive" :value="isset($parents) ? $parents->where('status','inactive')->count() : 0" icon="bi bi-x-circle" color="red" />
    </div>

    <!-- Filters & Actions - Row 2 -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex gap-2 items-center">
            <input id="parentsSearch" type="text" placeholder="Search name or email..." value="{{ request('q') }}" class="w-48 px-3 py-2 border rounded text-xs" />
            <select id="parentsStatusFilter" class="w-40 px-3 py-2 border rounded text-xs">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select id="parentsRelationshipFilter" class="w-40 px-3 py-2 border rounded text-xs">
                <option value="">All Relationships</option>
                <option value="Mother" {{ request('relationship') === 'Mother' ? 'selected' : '' }}>Mother</option>
                <option value="Father" {{ request('relationship') === 'Father' ? 'selected' : '' }}>Father</option>
                <option value="Guardian" {{ request('relationship') === 'Guardian' ? 'selected' : '' }}>Guardian</option>
            </select>
        </div>

        <button onclick="openAddParentModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
            <i class="bi bi-plus-lg"></i>
            <span>Add Parent</span>
        </button>
    </div>

    <x-card title="Parents List">
        <div id="parentsTableContainer">
            <x-table :headers="['Name','Parent ID','Email','Children','Role','Relationship','Status','Actions']">
                @forelse($parents ?? collect() as $parent)
                <tr>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            @if(!empty($parent->profile_photo_path))
                                <img src="{{ Storage::disk('public')->url($parent->profile_photo_path) }}" alt="avatar" class="w-7 h-7 rounded-full">
                            @else
                                <div class="w-7 h-7 rounded-full bg-gray-300 flex items-center justify-center text-white text-xs font-bold">{{ substr($parent->name ?? '', 0, 1) }}</div>
                            @endif
                            <span class="font-medium text-gray-800">{{ $parent->name }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-gray-600">{{ str_pad($parent->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-3 py-2 text-gray-600">{{ $parent->email }}</td>
                    <td class="px-3 py-2 text-gray-600">{{ $parent->children_count ?? 0 }}</td>
                    <td class="px-3 py-2"><span class="inline-block px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium">{{ ucfirst($parent->role) }}</span></td>
                    <td class="px-3 py-2 text-gray-600">{{ $parent->department ?? 'N/A' }}</td>
                    <td class="px-3 py-2">
                        @if($parent->status === 'active')
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Active</span>
                        @elseif($parent->status === 'pending')
                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Pending</span>
                        @else
                            <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Inactive</span>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex gap-1">
                            <button onclick="openViewParentModal({{ $parent->id }})" class="inline-flex items-center justify-center w-6 h-6 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded">
                                <i class="bi bi-eye text-sm"></i>
                            </button>
                            <button onclick="openEditParentModal({{ $parent->id }})" class="inline-flex items-center justify-center w-6 h-6 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded">
                                <i class="bi bi-pencil text-sm"></i>
                            </button>
                            <button onclick="deleteParent({{ $parent->id }})" class="inline-flex items-center justify-center w-6 h-6 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded">
                                <i class="bi bi-trash text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-3 py-2">No records found.</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                    <td class="px-3 py-2">—</td>
                </tr>
                @endforelse
            </x-table>
            @if(isset($parents) && $parents->hasPages())
                <div class="mt-3">{{ $parents->links() }}</div>
            @endif
        </div>
    </x-card>
</div>

<!-- Add Parent Modal -->
<div id="addParentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeAddParentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <form id="addParentForm" action="{{ route('admin.parents.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
                <div>
                    <h3 class="text-lg font-semibold">Add Parent</h3>
                    <p class="text-sm text-gray-500">Create a new parent account and assign children</p>
                </div>
                <button type="button" onclick="closeAddParentModal()" class="text-gray-500 hover:text-gray-900">✕</button>
            </div>
            <div class="p-6">
                <div class="flex gap-8">
                    <!-- Photo Section -->
                    <div class="flex flex-col items-center">
                        <div id="addParentAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0 mb-3">
                            <img id="addParentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                            <span id="addParentInitial"><i class="bi bi-person text-5xl"></i></span>
                        </div>
                        <label class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <i class="bi bi-download"></i>
                            Choose photo
                            <input type="file" name="profile_photo" accept="image/*" class="hidden" id="addProfilePhotoInput" onchange="previewAddParentPhoto()" />
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Recommended 400x400px. Max 4MB.</p>
                    </div>

                    <!-- Form Section -->
                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                <input type="text" value="Parent" disabled class="w-full px-3 py-2 border rounded-md text-sm bg-gray-100" />
                                <input type="hidden" name="role" value="parent" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Full name <span class="text-red-500 text-base">*</span></label>
                                <input name="name" required placeholder="e.g. John Doe" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500 text-base">*</span></label>
                                <input name="email" type="email" required placeholder="name@example.com" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Phone <span class="text-red-500 text-base">*</span></label>
                                <input name="phone" required placeholder="Phone number" class="w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Relationship <span class="text-red-500 text-base">*</span></label>
                                <select name="relationship" required class="w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="">Select</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Father">Father</option>
                                    <option value="Guardian">Guardian</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500 text-base">*</span></label>
                                <select name="status" required class="w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="">Select</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Bio <span class="text-red-500 text-base">*</span></label>
                            <textarea name="bio" required rows="2" placeholder="Short bio or notes" class="w-full px-3 py-2 border rounded-md text-sm"></textarea>
                        </div>

                        <!-- Children Selection -->
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-2">Select Children</label>
                            <div class="relative">
                                <button type="button" id="addChildrenToggle" class="w-full px-3 py-2 border border-gray-300 rounded-md text-left text-xs bg-white hover:bg-gray-50 flex justify-between items-center">
                                    <span>Choose children...</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </button>
                                <div id="addChildrenDropdown" class="hidden absolute top-full mt-1 w-full border border-gray-300 rounded-md max-h-64 overflow-y-auto bg-white shadow-lg z-50">
                                    <div class="sticky top-0 bg-white border-b px-3 py-2 flex gap-2">
                                        <input type="text" id="addChildrenSearch" placeholder="Search student..." class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs" />
                                        <button type="button" id="addChildrenSearchBtn" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Search</button>
                                    </div>
                                    <div class="px-3 py-2 text-xs text-gray-500">Loading students...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddParentModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Add Parent</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Parent Modal -->
<div id="editParentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeEditParentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <form id="editParentForm" method="POST" enctype="multipart/form-data" class="flex flex-col">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
                <div>
                    <h3 class="text-lg font-semibold">Edit Parent</h3>
                    <p class="text-sm text-gray-500">Update parent information and children assignment</p>
                </div>
                <button type="button" onclick="closeEditParentModal()" class="text-gray-500 hover:text-gray-900">✕</button>
            </div>
            <div class="p-6">
                <input type="hidden" name="_id" id="editParentId" />
                <div class="flex gap-8">
                    <!-- Photo Section -->
                    <div class="flex flex-col items-center">
                        <div id="editParentAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0 mb-3">
                            <img id="editParentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                            <span id="editParentInitial">P</span>
                        </div>
                        <label class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <i class="bi bi-download"></i>
                            Choose photo
                            <input type="file" name="profile_photo" accept="image/*" class="hidden" id="editProfilePhotoInput" onchange="previewEditParentPhoto()" />
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Recommended 400x400px. Max 4MB.</p>
                    </div>

                    <!-- Details Section -->
                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Role</label>
                                <input type="text" value="Parent" disabled class="mt-1 block w-full px-3 py-2 border rounded-md text-sm bg-gray-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Full name</label>
                                <input id="edit_name" name="name" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Email</label>
                                <input id="edit_email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Phone</label>
                                <input id="edit_phone" name="phone" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Relationship</label>
                                <select id="edit_relationship" name="relationship" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="Mother">Mother</option>
                                    <option value="Father">Father</option>
                                    <option value="Guardian">Guardian</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Status</label>
                                <select id="edit_status" name="status" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700">Bio</label>
                            <textarea id="edit_bio" name="bio" rows="2" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm"></textarea>
                        </div>

                        <!-- Children Selection -->
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-2">Select Children</label>
                            <div class="relative">
                                <button type="button" id="editChildrenToggle" class="w-full px-3 py-2 border border-gray-300 rounded-md text-left text-xs bg-white hover:bg-gray-50 flex justify-between items-center">
                                    <span>Choose children...</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </button>
                                <div id="editChildrenDropdown" class="hidden absolute top-full mt-1 w-full border border-gray-300 rounded-md max-h-64 overflow-y-auto bg-white shadow-lg z-50">
                                    <div class="sticky top-0 bg-white border-b px-3 py-2 flex gap-2">
                                        <input type="text" id="editChildrenSearch" placeholder="Search student..." class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs" />
                                        <button type="button" id="editChildrenSearchBtn" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Search</button>
                                    </div>
                                    <div class="px-3 py-2 text-xs text-gray-500">Loading students...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <button type="button" onclick="deleteParent(document.getElementById('editParentId').value)" class="px-3 py-2 text-sm bg-red-50 text-red-700 border border-red-200 rounded hover:bg-red-100">Delete</button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditParentModal()" class="px-3 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Parent Modal -->
<div id="viewParentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" onclick="if(event.target===this) closeViewParentModal()">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-auto">
        <div class="px-6 py-4 border-b flex items-center justify-between sticky top-0 bg-white">
            <div>
                <h3 class="text-lg font-semibold">View Parent</h3>
                <p class="text-sm text-gray-500">Parent information and assigned children</p>
            </div>
            <button type="button" onclick="closeViewParentModal()" class="text-gray-500 hover:text-gray-900">✕</button>
        </div>
        <div class="p-6">
            <div class="flex gap-8">
                <!-- Photo Section -->
                <div class="flex flex-col items-center">
                    <div id="viewParentAvatar" class="w-40 h-40 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-500 overflow-hidden flex-shrink-0">
                        <img id="viewParentAvatarImg" src="" alt="avatar" class="w-full h-full object-cover" style="display:none;">
                        <span id="viewParentInitial"><i class="bi bi-person text-5xl"></i></span>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="flex-1">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Full name</label>
                            <p id="view_name" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                            <p id="view_email" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Phone</label>
                            <p id="view_phone" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Relationship</label>
                            <p id="view_department" class="text-sm text-gray-900">—</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <p id="view_status" class="text-sm text-gray-900">—</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Bio</label>
                        <p id="view_bio" class="text-sm text-gray-900">—</p>
                    </div>

                    <!-- Children List -->
                    <div class="mt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Assigned Children</label>
                        <div id="viewChildrenList" class="space-y-2">
                            <p class="text-xs text-gray-500">Loading children...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t flex justify-end gap-3">
            <button type="button" onclick="closeViewParentModal()" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50">Close</button>
        </div>
    </div>
</div>

<script>
// Fetch all students for children dropdown
async function populateChildrenList(targetId) {
    try {
        const response = await fetch('{{ route("admin.parents.students") }}');
        const students = await response.json();
        const dropdown = document.getElementById(targetId);
        
        if (dropdown) {
            // Keep the search box and only replace the student list
            const searchBox = dropdown.querySelector('div:first-child');
            
            const studentHTML = students.map(s => 
                `<label class="flex items-center px-3 py-2 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition">
                    <input type="checkbox" name="children[]" value="${s.id}" class="children-checkbox mr-2 cursor-pointer" />
                    <span class="text-xs font-medium">${s.id.toString().padStart(3, '0')} - ${s.name}</span>
                    <span class="text-xs text-gray-500 ml-1">(${s.email})</span>
                </label>`
            ).join('');
            
            // Clear all content except the search box
            let existingContent = searchBox ? searchBox.outerHTML : '';
            dropdown.innerHTML = existingContent + studentHTML;
        }
    } catch (error) {
        console.error('Error loading students:', error);
        const dropdown = document.getElementById(targetId);
        if (dropdown) {
            const searchBox = dropdown.querySelector('div:first-child');
            const errorHTML = '<div class="px-3 py-2 text-xs text-red-500">Error loading students</div>';
            if (searchBox) {
                dropdown.innerHTML = searchBox.outerHTML + errorHTML;
            } else {
                dropdown.innerHTML = errorHTML;
            }
        }
    }
}

// Setup search listeners for children dropdowns
function setupSearchListeners(dropdownId, searchInputId, searchBtnId) {
    const searchInput = document.getElementById(searchInputId);
    const searchBtn = document.getElementById(searchBtnId);
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const dropdown = document.getElementById(dropdownId);
        
        if (!dropdown) return;
        
        let foundCount = 0;
        dropdown.querySelectorAll('label').forEach(label => {
            const text = label.textContent.toLowerCase();
            const match = text.includes(searchTerm);
            label.style.display = match ? '' : 'none';
            if (match) foundCount++;
        });
        
        // Show "no results" message if no matches
        let noResultsMsg = dropdown.querySelector('.no-results-msg');
        if (foundCount === 0 && searchTerm) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.className = 'no-results-msg px-3 py-2 text-xs text-gray-500 text-center';
                noResultsMsg.textContent = 'No students found';
                dropdown.appendChild(noResultsMsg);
            }
            noResultsMsg.style.display = '';
        } else if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }
    }
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
    
    if (searchInput) {
        // Trigger search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
        
        // Also filter in real-time as user types
        searchInput.addEventListener('keyup', performSearch);
    }
}

// Toggle Add Children Dropdown
document.getElementById('addChildrenToggle')?.addEventListener('click', function(e) {
    e.preventDefault();
    const dropdown = document.getElementById('addChildrenDropdown');
    dropdown.classList.toggle('hidden');
});

// Toggle Edit Children Dropdown
document.getElementById('editChildrenToggle')?.addEventListener('click', function(e) {
    e.preventDefault();
    const dropdown = document.getElementById('editChildrenDropdown');
    dropdown.classList.toggle('hidden');
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#addChildrenToggle') && !e.target.closest('#addChildrenDropdown')) {
        document.getElementById('addChildrenDropdown')?.classList.add('hidden');
    }
    if (!e.target.closest('#editChildrenToggle') && !e.target.closest('#editChildrenDropdown')) {
        document.getElementById('editChildrenDropdown')?.classList.add('hidden');
    }
});

// Open Add Parent Modal
async function openAddParentModal() {
    document.getElementById('addParentModal').classList.remove('hidden');
    await populateChildrenList('addChildrenDropdown');
    setupSearchListeners('addChildrenDropdown', 'addChildrenSearch', 'addChildrenSearchBtn');
}

// Close Add Parent Modal
function closeAddParentModal() {
    document.getElementById('addParentModal').classList.add('hidden');
    document.getElementById('addParentForm')?.reset?.();
}

// Open Edit Parent Modal
async function openEditParentModal(parentId) {
    try {
        const response = await fetch(`/admin/parents/${parentId}/edit`);
        const data = await response.json();
        
        document.getElementById('editParentId').value = data.id;
        document.getElementById('edit_name').value = data.name;
        document.getElementById('edit_email').value = data.email;
        document.getElementById('edit_phone').value = data.phone || '';
        document.getElementById('edit_relationship').value = data.department || '';
        document.getElementById('edit_status').value = data.status || '';
        document.getElementById('edit_bio').value = data.bio || '';
        
        if (data.profile_photo_path) {
            document.getElementById('editParentAvatarImg').src = `{{ Storage::disk('public')->url('') }}${data.profile_photo_path}`;
            document.getElementById('editParentAvatarImg').style.display = 'block';
            document.getElementById('editParentInitial').style.display = 'none';
        } else {
            document.getElementById('editParentAvatarImg').style.display = 'none';
            document.getElementById('editParentInitial').style.display = 'block';
        }
        
        document.getElementById('editParentForm').action = `/admin/parents/${parentId}`;
        
        document.getElementById('editParentModal').classList.remove('hidden');
        await populateChildrenList('editChildrenDropdown');
        setupSearchListeners('editChildrenDropdown', 'editChildrenSearch', 'editChildrenSearchBtn');
        
        // Check assigned children - with small delay to ensure checkboxes are rendered
        setTimeout(() => {
            const dropdown = document.getElementById('editChildrenDropdown');
            if (dropdown && data.assigned_children) {
                const assignedIds = data.assigned_children.map(child => child.id);
                dropdown.querySelectorAll('input[name="children[]"]').forEach(checkbox => {
                    checkbox.checked = assignedIds.includes(parseInt(checkbox.value));
                });
            }
        }, 100);
    } catch (error) {
        console.error('Error loading parent:', error);
        alert('Error loading parent data');
    }
}

// Close Edit Parent Modal
function closeEditParentModal() {
    document.getElementById('editParentModal').classList.add('hidden');
}

// Open View Parent Modal
async function openViewParentModal(parentId) {
    try {
        const response = await fetch(`/admin/parents/${parentId}/edit`);
        const data = await response.json();
        
        document.getElementById('view_name').textContent = data.name || '—';
        document.getElementById('view_email').textContent = data.email || '—';
        document.getElementById('view_phone').textContent = data.phone || '—';
        document.getElementById('view_department').textContent = data.department || '—';
        document.getElementById('view_status').innerHTML = 
            `<span class="inline-block px-2 py-1 bg-${data.status === 'active' ? 'green' : data.status === 'pending' ? 'yellow' : 'red'}-100 text-${data.status === 'active' ? 'green' : data.status === 'pending' ? 'yellow' : 'red'}-800 text-xs rounded">${data.status || '—'}</span>`;
        document.getElementById('view_bio').textContent = data.bio || '—';
        
        if (data.profile_photo_path) {
            document.getElementById('viewParentAvatarImg').src = `{{ Storage::disk('public')->url('') }}${data.profile_photo_path}`;
            document.getElementById('viewParentAvatarImg').style.display = 'block';
            document.getElementById('viewParentInitial').style.display = 'none';
        } else {
            document.getElementById('viewParentAvatarImg').style.display = 'none';
            document.getElementById('viewParentInitial').innerHTML = '<i class="bi bi-person text-5xl"></i>';
        }
        
        // Load assigned children
        const childrenList = document.getElementById('viewChildrenList');
        if (data.assigned_children && data.assigned_children.length > 0) {
            const childrenHtml = data.assigned_children.map(child => 
                `<div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded text-sm">
                    <i class="bi bi-person-circle text-gray-400"></i>
                    <span>${child.id.toString().padStart(3, '0')} - ${child.name}</span>
                    <span class="text-xs text-gray-500">(${child.email})</span>
                </div>`
            ).join('');
            childrenList.innerHTML = childrenHtml;
        } else {
            childrenList.innerHTML = '<p class="text-xs text-gray-500">No children assigned</p>';
        }
        
        document.getElementById('viewParentModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading parent:', error);
        alert('Error loading parent data');
    }
}

// Close View Parent Modal
function closeViewParentModal() {
    document.getElementById('viewParentModal').classList.add('hidden');
}

// Toast notification system
function showToast(message, type = 'info') {
    const toast = document.getElementById('toastNotification');
    const icon = document.getElementById('toastIcon');
    const msg = document.getElementById('toastMessage');
    
    msg.textContent = message;
    toast.classList.remove('hidden', 'bg-blue-500', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');
    
    switch(type) {
        case 'success':
            toast.classList.add('bg-green-500');
            icon.className = 'bi bi-check-circle';
            break;
        case 'error':
            toast.classList.add('bg-red-500');
            icon.className = 'bi bi-exclamation-circle';
            break;
        case 'warning':
            toast.classList.add('bg-yellow-500');
            icon.className = 'bi bi-exclamation-triangle';
            break;
        default:
            toast.classList.add('bg-blue-500');
            icon.className = 'bi bi-info-circle';
    }
    
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3500);
}

// Delete Parent
async function deleteParent(parentId) {
    if (!confirm('Are you sure you want to delete this parent? Children will not be deleted.')) return;
    
    try {
        const response = await fetch(`/admin/parents/${parentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            showToast('Parent deleted successfully', 'success');
            closeEditParentModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Error deleting parent', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error deleting parent', 'error');
    }
}

// Photo preview - Add
function previewAddParentPhoto() {
    const file = document.getElementById('addProfilePhotoInput').files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('addParentAvatarImg').src = e.target.result;
            document.getElementById('addParentAvatarImg').style.display = 'block';
            document.getElementById('addParentInitial').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Photo preview - Edit
function previewEditParentPhoto() {
    const file = document.getElementById('editProfilePhotoInput').files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('editParentAvatarImg').src = e.target.result;
            document.getElementById('editParentAvatarImg').style.display = 'block';
            document.getElementById('editParentInitial').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

// Handle form submission for Add with AJAX
document.getElementById('addParentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const dropdown = document.getElementById('addChildrenDropdown');
    const checked = dropdown?.querySelectorAll('input[name="children[]"]:checked').length || 0;
    
    if (checked === 0 && !confirm('No children assigned. Continue anyway?')) {
        return;
    }
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Adding...';
    submitBtn.disabled = true;
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw new Error(data.message || 'Failed to add parent'); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Parent added successfully', 'success');
            closeAddParentModal();
            this.reset();
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'Failed to add parent', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Handle form submission for Edit with AJAX
document.getElementById('editParentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const dropdown = document.getElementById('editChildrenDropdown');
    const checked = dropdown?.querySelectorAll('input[name="children[]"]:checked').length || 0;
    
    if (checked === 0 && !confirm('No children assigned. Continue anyway?')) {
        return;
    }
    
    const parentId = document.getElementById('editParentId').value;
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
    fetch(`/admin/parents/${parentId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw new Error(data.message || 'Failed to update parent'); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Parent updated successfully', 'success');
            closeEditParentModal();
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'Failed to update parent', 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Check if we just came back from a form submission
const notification = sessionStorage.getItem('showNotification');
if (notification) {
    sessionStorage.removeItem('showNotification');
    if (notification === 'parent_added') {
        showToast('Parent added successfully', 'success');
    } else if (notification === 'parent_updated') {
        showToast('Parent updated successfully', 'success');
    }
}

// Apply filters
function applyParentsFilters() {
    const search = document.getElementById('parentsSearch')?.value || '';
    const status = document.getElementById('parentsStatusFilter')?.value || '';
    const relationship = document.getElementById('parentsRelationshipFilter')?.value || '';
    
    // Build query string
    const params = new URLSearchParams();
    if (search) params.append('q', search);
    if (status) params.append('status', status);
    if (relationship) params.append('relationship', relationship);
    
    // Reload page with filters
    const queryString = params.toString();
    window.location.href = queryString ? `{{ route('admin.parents') }}?${queryString}` : '{{ route('admin.parents') }}';
}

document.getElementById('parentsSearch')?.addEventListener('keyup', applyParentsFilters);
document.getElementById('parentsStatusFilter')?.addEventListener('change', applyParentsFilters);
document.getElementById('parentsRelationshipFilter')?.addEventListener('change', applyParentsFilters);
</script>
@endsection
