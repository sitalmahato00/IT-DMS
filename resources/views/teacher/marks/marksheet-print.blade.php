<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - {{ $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-4xl mx-auto p-8">
        {{-- Print Button --}}
        <div class="no-print mb-4 flex justify-end gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="bi bi-printer mr-2"></i>Print
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                Close
            </button>
        </div>

        {{-- Marksheet Header --}}
        <div class="border-2 border-black mb-6">
            <div class="bg-white p-4 border-b-2 border-black">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ $collegeLogoUrl ?? asset('images/default-logo.svg') }}" alt="College Logo" class="h-16 w-16 object-contain" />
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ $college->name ?? 'My College' }}</h1>
                            @php
                                $addressParts = array_filter([
                                    $college->address ?? null,
                                    $college->city ?? null,
                                    $college->district ?? null,
                                ]);
                                $contactParts = array_filter([
                                    $college->email ? 'Email: '.$college->email : null,
                                    $college->phone ? 'Phone: '.$college->phone : null,
                                ]);
                            @endphp
                            @if(count($addressParts) > 0)
                                <p class="text-gray-600">{{ implode(', ', $addressParts) }}</p>
                            @endif
                            @if(count($contactParts) > 0)
                                <p class="text-sm text-gray-500">{{ implode(' | ', $contactParts) }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-gray-800">ACADEMIC TRANSCRIPT</h2>
                        <p class="text-sm text-gray-600">Academic Year: {{ $filters['academic_year'] ?? 'All' }} | Semester: {{ $filters['semester'] ?? 'All' }}</p>
                        <p class="text-sm"><span class="font-medium">Category:</span> {{ ucfirst($filters['exam_category'] ?? 'Assessment') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Information --}}
        <div class="border-2 border-black mb-6">
            <div class="bg-gray-100 p-3 border-b-2 border-black">
                <h3 class="font-bold text-gray-800">STUDENT INFORMATION</h3>
            </div>
            <div class="p-4 grid grid-cols-2 gap-4">
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-sm font-medium text-gray-600">Student Name:</span>
                    <span class="col-span-2 text-sm text-gray-800">{{ $student->user->name ?? 'N/A' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-sm font-medium text-gray-600">Student ID:</span>
                    <span class="col-span-2 text-sm text-gray-800">{{ $student->id }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-sm font-medium text-gray-600">Roll Number:</span>
                    <span class="col-span-2 text-sm text-gray-800">{{ $student->roll_no ?? 'N/A' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-sm font-medium text-gray-600">Semester:</span>
                    <span class="col-span-2 text-sm text-gray-800">{{ $student->semester ?? 'N/A' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-sm font-medium text-gray-600">Academic Year:</span>
                    <span class="col-span-2 text-sm text-gray-800">{{ $student->academic_year_bs ?? 'N/A' }}</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <span class="text-sm font-medium text-gray-600">Date of Birth:</span>
                    <span class="col-span-2 text-sm text-gray-800">{{ $student->date_of_birth ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Marks Table --}}
        <div class="border-2 border-black mb-6">
            <div class="bg-gray-100 p-3 border-b-2 border-black">
                <h3 class="font-bold text-gray-800">ACADEMIC PERFORMANCE</h3>
            </div>
            <table class="w-full text-sm border border-black border-collapse">
                <thead>
                    <tr class="border border-black">
                        <th class="p-2 text-left font-bold border border-black">S.N.</th>
                        <th class="p-2 text-left font-bold border border-black">Subject</th>
                        <th class="p-2 text-center font-bold border border-black">Full Mark (Int)</th>
                        <th class="p-2 text-center font-bold border border-black">Full Mark (Ext)</th>
                        <th class="p-2 text-center font-bold border border-black">Pass Mark (Int)</th>
                        <th class="p-2 text-center font-bold border border-black">Pass Mark (Ext)</th>
                        <th class="p-2 text-center font-bold border border-black">Marks Obtained (Int)</th>
                        <th class="p-2 text-center font-bold border border-black">Marks Obtained (Ext)</th>
                        <th class="p-2 text-center font-bold border border-black">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($marksheetData && $marksheetData['exam_marks']->count() > 0)
                        @foreach($marksheetData['exam_marks'] as $index => $mark)
                            @php
                                $subjectName = $mark->subject->subject_name ?? 'N/A';
                                $tiFull = $mark->exam->theory_internal_max_marks ?? 0;
                                $teFull = $mark->exam->theory_external_max_marks ?? 0;
                                $tiPass = $mark->exam->theory_internal_pass_marks ?? 0;
                                $tePass = $mark->exam->theory_external_pass_marks ?? 0;
                                $piFull = $mark->exam->practical_internal_max_marks ?? 0;
                                $peFull = $mark->exam->practical_external_max_marks ?? 0;
                                $piPass = $mark->exam->practical_internal_pass_marks ?? 0;
                                $pePass = $mark->exam->practical_external_pass_marks ?? 0;
                                $tiObt = $mark->theory_internal_marks ?? 0;
                                $teObt = $mark->theory_external_marks ?? 0;
                                $piObt = $mark->practical_internal_marks ?? 0;
                                $peObt = $mark->practical_external_marks ?? 0;

                                $tiFail = $tiObt < $tiPass;
                                $teFail = $teObt < $tePass;
                                $piFail = $piObt < $piPass;
                                $peFail = $peObt < $pePass;

                                $theoryTotal = $tiObt + $teObt;
                                $practicalTotal = $piObt + $peObt;
                            @endphp

                            <tr class="border border-black">
                                <td class="p-2 text-center border border-black" rowspan="2">{{ $index + 1 }}</td>
                                <td class="p-2 border border-black">{{ $subjectName }} (Th.)</td>
                                <td class="p-2 text-center border border-black">{{ $tiFull }}</td>
                                <td class="p-2 text-center border border-black">{{ $teFull }}</td>
                                <td class="p-2 text-center border border-black">{{ $tiPass }}</td>
                                <td class="p-2 text-center border border-black">{{ $tePass }}</td>
                                <td class="p-2 text-center border border-black {{ $tiFail ? 'bg-red-100 text-red-800' : '' }}">{{ $tiObt }}</td>
                                <td class="p-2 text-center border border-black {{ $teFail ? 'bg-red-100 text-red-800' : '' }}">{{ $teObt }}</td>
                                <td class="p-2 text-center font-medium border border-black">{{ $theoryTotal }}</td>
                            </tr>

                            <tr class="border border-black">
                                <td class="p-2 border border-black">{{ $subjectName }} (Pr.)</td>
                                <td class="p-2 text-center border border-black">{{ $piFull }}</td>
                                <td class="p-2 text-center border border-black">{{ $peFull }}</td>
                                <td class="p-2 text-center border border-black">{{ $piPass }}</td>
                                <td class="p-2 text-center border border-black">{{ $pePass }}</td>
                                <td class="p-2 text-center border border-black {{ $piFail ? 'bg-red-100 text-red-800' : '' }}">{{ $piObt }}</td>
                                <td class="p-2 text-center border border-black {{ $peFail ? 'bg-red-100 text-red-800' : '' }}">{{ $peObt }}</td>
                                <td class="p-2 text-center font-medium border border-black">{{ $practicalTotal }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="p-4 text-center text-gray-500">No marks found</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-black bg-gray-100 font-bold">
                        <td colspan="8" class="p-2 text-right border border-black">Grand Total:</td>
                        <td class="p-2 text-center border border-black">{{ $marksheetData['total_obtained'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Footer --}}
        <div class="border-2 border-black p-4 mt-8">
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-sm text-gray-600">Date of Issue: {{ date('Y-m-d') }}</p>
                    <p class="text-sm text-gray-600">Generated By: Teacher</p>
                </div>
                <div class="text-center">
                    <div class="w-48 border-b border-black mb-2"></div>
                    <p class="text-sm font-medium">Controller of Examination</p>
                </div>
            </div>
        </div>

        {{-- Disclaimer --}}
        <p class="text-xs text-gray-500 mt-4 text-center">
            This is a computer-generated document. No signature is required.<br>
            For verification, please contact the examination department.
        </p>
    </div>
</body>
</html>
