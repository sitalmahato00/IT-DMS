{{-- @extends('admin.layouts.app')

@section('title', 'Marks Management')

@section('content')
<div class="space-y-4">
    <!-- Stats Cards - Row 1 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stats-card title="Total Students" value="156" icon="bi bi-people-fill" color="blue" />
        <x-stats-card title="Marks Submitted" value="142" icon="bi bi-check-circle" color="green" />
        <x-stats-card title="Pending" value="14" icon="bi bi-exclamation-circle" color="yellow" />
        <x-stats-card title="Average Score" value="75.5%" icon="bi bi-percent" color="purple" />
    </div>

	<!-- Filters & Search - Row 2 -->
	<div class="flex items-center justify-between gap-3 flex-wrap">
		<div class="flex gap-2 items-center flex-wrap">
			<select class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
				<option value="">Select Year</option>
				<option value="2025">2025</option>
				<option value="2024">2024</option>
				<option value="2023">2023</option>
			</select>
			<select class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
				<option value="">Select Semester</option>
				<option value="first">Semester 1</option>
				<option value="second">Semester 2</option>
				<option value="third">Semester 3</option>
				<option value="fourth">Semester 4</option>
			</select>
			<select class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
				<option value="">Select Subject</option>
				<option value="physics">Physics</option>
				<option value="chemistry">Chemistry</option>
				<option value="biology">Biology</option>
				<option value="english">English</option>
			</select>
			<select class="w-40 px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
				<option value="">All Types</option>
				<option value="internal">Internal Assessment</option>
				<option value="practical">Practical Marks</option>
				<option value="theory">Theory</option>
			</select>
			<button class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium flex items-center gap-1.5 transition">
				<i class="bi bi-funnel text-xs"></i>
				<span>Filter</span>
			</button>
		</div>

		<button class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 font-medium">
			<i class="bi bi-plus-lg"></i>
			<span>Add Marks</span>
		</button>

    <!-- Tabs -->
    <div class="bg-white rounded shadow-sm border border-gray-200">
        <div class="flex border-b border-gray-200">
            <button onclick="switchTab('internal')" class="tab-button active px-4 py-2 text-xs font-medium text-gray-900 border-b-2 border-red-600 hover:text-red-600 transition">
                Internal Assessment
            </button>
            <button onclick="switchTab('practical')" class="tab-button px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition">
                Practical Marks
            </button>
            <button onclick="switchTab('theory')" class="tab-button px-4 py-2 text-xs font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900 transition">
                Theory Marks
            </button>
        </div>

        <!-- Internal Assessment Tab Content -->
        <div id="internal-tab" class="tab-content">
            <div class="p-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Internal Assessment Marks</h3>
                <p class="text-gray-600 text-xs mt-1">View and manage internal assessment scores</p>
            </div>

            <div class="overflow-x-auto">
                <x-table :headers="['Roll No','Student Name','Subject 1','Subject 2','Subject 3','Subject 4','Total','Actions']">
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS001</td>
                        <td class="px-3 py-2 text-gray-700">John Smith</td>
                        <td class="px-3 py-2 text-center text-gray-700">85</td>
                        <td class="px-3 py-2 text-center text-gray-700">92</td>
                        <td class="px-3 py-2 text-center text-gray-700">78</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">343/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS001', 'John Smith', '85', '92', '78', '88', '343', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS001', 'John Smith')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS002</td>
                        <td class="px-3 py-2 text-gray-700">Emily Johnson</td>
                        <td class="px-3 py-2 text-center text-gray-700">90</td>
                        <td class="px-3 py-2 text-center text-gray-700">87</td>
                        <td class="px-3 py-2 text-center text-gray-700">95</td>
                        <td class="px-3 py-2 text-center text-gray-700">82</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">354/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS002', 'Emily Johnson', '90', '87', '95', '82', '354', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS002', 'Emily Johnson')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS003</td>
                        <td class="px-3 py-2 text-gray-700">Michael Brown</td>
                        <td class="px-3 py-2 text-center text-gray-700">76</td>
                        <td class="px-3 py-2 text-center text-gray-700">84</td>
                        <td class="px-3 py-2 text-center text-gray-700">70</td>
                        <td class="px-3 py-2 text-center text-gray-700">79</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">309/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS003', 'Michael Brown', '76', '84', '70', '79', '309', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS003', 'Michael Brown')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS004</td>
                        <td class="px-3 py-2 text-gray-700">Sarah Davis</td>
                        <td class="px-3 py-2 text-center text-gray-700">93</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center text-gray-700">91</td>
                        <td class="px-3 py-2 text-center text-gray-700">89</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">361/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS004', 'Sarah Davis', '93', '88', '91', '89', '361', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS004', 'Sarah Davis')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS005</td>
                        <td class="px-3 py-2 text-gray-700">John Smith</td>
                        <td class="px-3 py-2 text-center text-gray-700">85</td>
                        <td class="px-3 py-2 text-center text-gray-700">92</td>
                        <td class="px-3 py-2 text-center text-gray-700">78</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">343/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS005', 'John Smith', '85', '92', '78', '88', '343', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS005', 'John Smith')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS006</td>
                        <td class="px-3 py-2 text-gray-700">Emily Johnson</td>
                        <td class="px-3 py-2 text-center text-gray-700">90</td>
                        <td class="px-3 py-2 text-center text-gray-700">87</td>
                        <td class="px-3 py-2 text-center text-gray-700">95</td>
                        <td class="px-3 py-2 text-center text-gray-700">82</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">354/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS006', 'Emily Johnson', '90', '87', '95', '82', '354', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS006', 'Emily Johnson')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS007</td>
                        <td class="px-3 py-2 text-gray-700">Michael Brown</td>
                        <td class="px-3 py-2 text-center text-gray-700">76</td>
                        <td class="px-3 py-2 text-center text-gray-700">84</td>
                        <td class="px-3 py-2 text-center text-gray-700">70</td>
                        <td class="px-3 py-2 text-center text-gray-700">79</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">309/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS007', 'Michael Brown', '76', '84', '70', '79', '309', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS007', 'Michael Brown')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS008</td>
                        <td class="px-3 py-2 text-gray-700">Sarah Davis</td>
                        <td class="px-3 py-2 text-center text-gray-700">93</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center text-gray-700">91</td>
                        <td class="px-3 py-2 text-center text-gray-700">89</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">361/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS008', 'Sarah Davis', '93', '88', '91', '89', '361', '400', 'Internal')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS008', 'Sarah Davis')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                </x-table>
            </div>

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
                <span>Showing 1 to 8 of 142 entries</span>
            </div>
        </div>

        <!-- Practical Marks Tab Content -->
        <div id="practical-tab" class="tab-content hidden">
            <div class="p-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Practical Marks</h3>
                <p class="text-gray-600 text-xs mt-1">View and manage practical assessment scores</p>
            </div>

            <div class="overflow-x-auto">
                <x-table :headers="['Roll No','Student Name','Attendance','Assessments','Discipline','Assignments','Total','Actions']">
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS001</td>
                        <td class="px-3 py-2 text-gray-700">John Smith</td>
                        <td class="px-3 py-2 text-center text-gray-700">85</td>
                        <td class="px-3 py-2 text-center text-gray-700">92</td>
                        <td class="px-3 py-2 text-center text-gray-700">78</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">343/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS001', 'John Smith', '85', '92', '78', '88', '343', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS001', 'John Smith')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS002</td>
                        <td class="px-3 py-2 text-gray-700">Emily Johnson</td>
                        <td class="px-3 py-2 text-center text-gray-700">90</td>
                        <td class="px-3 py-2 text-center text-gray-700">87</td>
                        <td class="px-3 py-2 text-center text-gray-700">95</td>
                        <td class="px-3 py-2 text-center text-gray-700">82</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">354/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS002', 'Emily Johnson', '90', '87', '95', '82', '354', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS002', 'Emily Johnson')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS003</td>
                        <td class="px-3 py-2 text-gray-700">Michael Brown</td>
                        <td class="px-3 py-2 text-center text-gray-700">76</td>
                        <td class="px-3 py-2 text-center text-gray-700">84</td>
                        <td class="px-3 py-2 text-center text-gray-700">70</td>
                        <td class="px-3 py-2 text-center text-gray-700">79</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">309/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS003', 'Michael Brown', '76', '84', '70', '79', '309', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS003', 'Michael Brown')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS004</td>
                        <td class="px-3 py-2 text-gray-700">Sarah Davis</td>
                        <td class="px-3 py-2 text-center text-gray-700">93</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center text-gray-700">91</td>
                        <td class="px-3 py-2 text-center text-gray-700">89</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">361/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS004', 'Sarah Davis', '93', '88', '91', '89', '361', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS004', 'Sarah Davis')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS005</td>
                        <td class="px-3 py-2 text-gray-700">John Smith</td>
                        <td class="px-3 py-2 text-center text-gray-700">85</td>
                        <td class="px-3 py-2 text-center text-gray-700">92</td>
                        <td class="px-3 py-2 text-center text-gray-700">78</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">343/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS005', 'John Smith', '85', '92', '78', '88', '343', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS005', 'John Smith')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS006</td>
                        <td class="px-3 py-2 text-gray-700">Emily Johnson</td>
                        <td class="px-3 py-2 text-center text-gray-700">90</td>
                        <td class="px-3 py-2 text-center text-gray-700">87</td>
                        <td class="px-3 py-2 text-center text-gray-700">95</td>
                        <td class="px-3 py-2 text-center text-gray-700">82</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">354/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS006', 'Emily Johnson', '90', '87', '95', '82', '354', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS006', 'Emily Johnson')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS007</td>
                        <td class="px-3 py-2 text-gray-700">Michael Brown</td>
                        <td class="px-3 py-2 text-center text-gray-700">76</td>
                        <td class="px-3 py-2 text-center text-gray-700">84</td>
                        <td class="px-3 py-2 text-center text-gray-700">70</td>
                        <td class="px-3 py-2 text-center text-gray-700">79</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">309/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS007', 'Michael Brown', '76', '84', '70', '79', '309', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS007', 'Michael Brown')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS008</td>
                        <td class="px-3 py-2 text-gray-700">Sarah Davis</td>
                        <td class="px-3 py-2 text-center text-gray-700">93</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center text-gray-700">91</td>
                        <td class="px-3 py-2 text-center text-gray-700">89</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">361/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS008', 'Sarah Davis', '93', '88', '91', '89', '361', '400', 'Practical')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS008', 'Sarah Davis')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                </x-table>
            </div>

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
                <span>Showing 1 to 8 of 142 entries</span>
            </div>
        </div>

        <!-- Theory Marks Tab Content -->
        <div id="theory-tab" class="tab-content hidden">
            <div class="p-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900">Theory Marks</h3>
                <p class="text-gray-600 text-xs mt-1">View and manage theory assessment scores</p>
            </div>

            <div class="overflow-x-auto">
                <x-table :headers="['Roll No','Student Name','Attendance','Assessments','Discipline','Assignments','Total','Actions']">
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS001</td>
                        <td class="px-3 py-2 text-gray-700">John Smith</td>
                        <td class="px-3 py-2 text-center text-gray-700">85</td>
                        <td class="px-3 py-2 text-center text-gray-700">92</td>
                        <td class="px-3 py-2 text-center text-gray-700">78</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">343/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS001', 'John Smith', '85', '92', '78', '88', '343', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS001', 'John Smith')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS002</td>
                        <td class="px-3 py-2 text-gray-700">Emily Johnson</td>
                        <td class="px-3 py-2 text-center text-gray-700">90</td>
                        <td class="px-3 py-2 text-center text-gray-700">87</td>
                        <td class="px-3 py-2 text-center text-gray-700">95</td>
                        <td class="px-3 py-2 text-center text-gray-700">82</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">354/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS002', 'Emily Johnson', '90', '87', '95', '82', '354', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS002', 'Emily Johnson')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS003</td>
                        <td class="px-3 py-2 text-gray-700">Michael Brown</td>
                        <td class="px-3 py-2 text-center text-gray-700">76</td>
                        <td class="px-3 py-2 text-center text-gray-700">84</td>
                        <td class="px-3 py-2 text-center text-gray-700">70</td>
                        <td class="px-3 py-2 text-center text-gray-700">79</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">309/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS003', 'Michael Brown', '76', '84', '70', '79', '309', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS003', 'Michael Brown')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS004</td>
                        <td class="px-3 py-2 text-gray-700">Sarah Davis</td>
                        <td class="px-3 py-2 text-center text-gray-700">93</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center text-gray-700">91</td>
                        <td class="px-3 py-2 text-center text-gray-700">89</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">361/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS004', 'Sarah Davis', '93', '88', '91', '89', '361', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS004', 'Sarah Davis')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS005</td>
                        <td class="px-3 py-2 text-gray-700">John Smith</td>
                        <td class="px-3 py-2 text-center text-gray-700">85</td>
                        <td class="px-3 py-2 text-center text-gray-700">92</td>
                        <td class="px-3 py-2 text-center text-gray-700">78</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">343/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS005', 'John Smith', '85', '92', '78', '88', '343', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS005', 'John Smith')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS006</td>
                        <td class="px-3 py-2 text-gray-700">Emily Johnson</td>
                        <td class="px-3 py-2 text-center text-gray-700">90</td>
                        <td class="px-3 py-2 text-center text-gray-700">87</td>
                        <td class="px-3 py-2 text-center text-gray-700">95</td>
                        <td class="px-3 py-2 text-center text-gray-700">82</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">354/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS006', 'Emily Johnson', '90', '87', '95', '82', '354', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS006', 'Emily Johnson')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS007</td>
                        <td class="px-3 py-2 text-gray-700">Michael Brown</td>
                        <td class="px-3 py-2 text-center text-gray-700">76</td>
                        <td class="px-3 py-2 text-center text-gray-700">84</td>
                        <td class="px-3 py-2 text-center text-gray-700">70</td>
                        <td class="px-3 py-2 text-center text-gray-700">79</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">309/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS007', 'Michael Brown', '76', '84', '70', '79', '309', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS007', 'Michael Brown')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium text-gray-900">CS008</td>
                        <td class="px-3 py-2 text-gray-700">Sarah Davis</td>
                        <td class="px-3 py-2 text-center text-gray-700">93</td>
                        <td class="px-3 py-2 text-center text-gray-700">88</td>
                        <td class="px-3 py-2 text-center text-gray-700">91</td>
                        <td class="px-3 py-2 text-center text-gray-700">89</td>
                        <td class="px-3 py-2 text-center font-semibold text-red-600">361/400</td>
                        <td class="px-3 py-2 text-center">
                            <div class="flex gap-1 justify-center">
                                <x-icon-button color="blue" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50" onclick="openViewMarksModal('CS008', 'Sarah Davis', '93', '88', '91', '89', '361', '400', 'Theory')">
                                    <i class="bi bi-eye text-xs"></i>
                                </x-icon-button>
                                <x-icon-button color="yellow" class="text-yellow-600 hover:text-yellow-800 text-xs px-2 py-1 rounded hover:bg-yellow-50" onclick="openEditMarksModal('CS008', 'Sarah Davis')">
                                    <i class="bi bi-pencil text-xs"></i>
                                </x-icon-button>
                            </div>
                        </td>
                    </tr>
                </x-table>
            </div>

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
                <span>Showing 1 to 8 of 142 entries</span>
            </div>
        </div>

<div id="viewMarksModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-md w-full">
        <!-- Header -->
        <div class="relative bg-gradient-to-r from-red-600 to-orange-500 p-4 pb-12">
            <button onclick="closeViewMarksModal()" class="absolute top-2 right-2 text-white hover:bg-white hover:bg-opacity-20 p-1 rounded">
                <i class="bi bi-x-lg text-sm"></i>
            </button>

            <div class="flex items-end gap-3">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow border-2 border-white">
                    <i class="bi bi-percent text-2xl text-red-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white" id="viewMarksRollNo">Roll No</h2>
                    <p class="text-red-100 text-xs" id="viewMarksStudentName">Student Name</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="relative p-4 -mt-6">
            <div class="bg-white rounded shadow p-4 space-y-3">
                <!-- Marks Information -->
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-blue-100 rounded flex items-center justify-center">
                            <i class="bi bi-list-ul text-blue-600 text-xs"></i>
                        </span>
                        Subject Marks
                    </h3>
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subject 1:</span>
                            <span class="font-medium text-gray-900" id="viewMarksSubject1">85</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subject 2:</span>
                            <span class="font-medium text-gray-900" id="viewMarksSubject2">92</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subject 3:</span>
                            <span class="font-medium text-gray-900" id="viewMarksSubject3">78</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subject 4:</span>
                            <span class="font-medium text-gray-900" id="viewMarksSubject4">88</span>
                        </div>
                    </div>
                </div>

                <!-- Total Marks -->
                <div class="border-b pb-3">
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-green-100 rounded flex items-center justify-center">
                            <i class="bi bi-calculator text-green-600 text-xs"></i>
                        </span>
                        Total Score
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 text-xs">Total Marks:</span>
                        <span class="text-2xl font-bold text-red-600" id="viewMarksTotalObtained">343</span>
                        <span class="text-gray-600 text-xs">/<span id="viewMarksTotalMax">400</span></span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-600 h-2 rounded-full" style="width: 85.75%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">Percentage: <span class="font-medium text-gray-900">85.75%</span></p>
                </div>

                <!-- Assessment Type & Status -->
                <div>
                    <h3 class="text-xs font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-5 h-5 bg-yellow-100 rounded flex items-center justify-center">
                            <i class="bi bi-circle-fill text-yellow-600 text-xs"></i>
                        </span>
                        Assessment Information
                    </h3>
                    <div class="space-y-1 text-xs">
                        <p><span class="text-gray-600">Type:</span> <span class="font-medium text-gray-900" id="viewMarksType">Internal Assessment</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-gray-200 flex gap-2">
            <button onclick="closeViewMarksModal()" class="flex-1 px-2 py-1 text-gray-700 font-medium text-xs border border-gray-300 rounded hover:bg-gray-50">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Marks Modal -->
<div id="editMarksModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded shadow-2xl max-w-lg w-full">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-3 flex items-center justify-between z-10">
            <div>
                <h2 class="text-sm font-bold">Edit Marks</h2>
                <p class="text-red-100 text-xs mt-0.5" id="editMarksStudentDisplay">Student Name</p>
            </div>
            <button onclick="closeEditMarksModal()" class="text-red-200 hover:text-white">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Form Content -->
        <div class="overflow-y-auto max-h-[70vh]">
            <form class="p-3 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Roll Number</label>
                    <input type="text" id="editMarksRollNo" readonly class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs bg-gray-50 focus:outline-none">
                </div>

                <div class="border-t pt-3">
                    <h3 class="text-xs font-semibold text-gray-900 bg-gray-50 p-2 rounded mb-2">Subject Marks</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject 1</label>
                            <input type="number" placeholder="0" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject 2</label>
                            <input type="number" placeholder="0" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject 3</label>
                            <input type="number" placeholder="0" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Subject 4</label>
                            <input type="number" placeholder="0" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Remarks/Comments</label>
                    <textarea placeholder="Add any remarks..." class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-red-500 h-12"></textarea>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
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

@endsection

@section('scripts')
<script>
    function switchTab(tabName) {
        // Hide all tabs
        const tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => tab.classList.add('hidden'));
        
        // Remove active state from all buttons
        const buttons = document.querySelectorAll('.tab-button');
        buttons.forEach(btn => {
            btn.classList.remove('border-b-2', 'border-red-600', 'text-gray-900');
            btn.classList.add('border-transparent', 'text-gray-600');
        });

        // Show selected tab
        document.getElementById(tabName + '-tab').classList.remove('hidden');
        
        // Set active state to clicked button
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
        
        // Calculate percentage
        const percentage = (obtained / max) * 100;
        document.querySelector('#viewMarksModal .bg-red-600').style.width = percentage + '%';
        document.querySelector('#viewMarksModal .mt-1').innerHTML = `Percentage: <span class="font-medium text-gray-900">${percentage.toFixed(2)}%</span>`;
        
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

    function openUploadMarksModal() {
        alert('Import marks functionality would open a file upload dialog here');
    }

    // Close modals when clicking outside
    document.getElementById('viewMarksModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewMarksModal();
        }
    });

    document.getElementById('editMarksModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditMarksModal();
        }
    });
</script> --}}
