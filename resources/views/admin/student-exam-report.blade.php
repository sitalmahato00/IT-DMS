@extends('layouts.app')

@section('title', 'Student Exam Report - Print')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Student Exam Report (Ctrl+P to print)</h1>

    @php
        // Get student data from route parameter
        $student = $student ?? auth()->user();
        
        $college = (object)[
            'id' => 1,
            'name' => 'Institute of Technology & Management',
            'logo_path' => asset('images/default-logo.svg'),
            'address' => 'Kathmandu, Nepal',
        ];

        // Sample exam data - in production would come from database
        $examData = [
            'type' => 'Monthly Exam',
            'month' => 'March',
            'examDate' => '2024-03-01',
            'term' => 'Term 1',
            'semester' => 5,
        ];

        // Sample subjects with marks
        $subjects = [
            ['code' => 'CS501', 'name' => 'Data Structures', 'marks' => 85, 'outOf' => 100, 'percentage' => 85.0, 'grade' => 'A'],
            ['code' => 'CS502', 'name' => 'Database Systems', 'marks' => 78, 'outOf' => 100, 'percentage' => 78.0, 'grade' => 'B+'],
            ['code' => 'CS503', 'name' => 'Web Development', 'marks' => 92, 'outOf' => 100, 'percentage' => 92.0, 'grade' => 'A+'],
            ['code' => 'CS504', 'name' => 'Operating Systems', 'marks' => 81, 'outOf' => 100, 'percentage' => 81.0, 'grade' => 'A'],
            ['code' => 'CS505', 'name' => 'Software Engineering', 'marks' => 88, 'outOf' => 100, 'percentage' => 88.0, 'grade' => 'A'],
        ];

        $meta = [
            'title' => $examData['type'],
            'date' => date('F d, Y'),
        ];
    @endphp

    <x-print-as-image
        id="student-exam-report"
        template="marks"
        :college="$college"
        :meta="$meta"
        :multipage="false"
    >
        <div class="bg-white p-8 border border-gray-300" style="font-family: Arial, sans-serif; font-size: 11px;">
            
            <!-- School Header -->
            <div class="text-center mb-6 pb-4 border-b-2 border-gray-800">
                <!-- Logo -->
                <div class="mb-2">
                    <div style="width: 50px; height: 50px; margin: 0 auto 8px; background: #2d5016; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px;">
                        🏫
                    </div>
                </div>
                
                <h2 style="font-size: 14px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase;">
                    {{ $college->name }}
                </h2>
                <p style="font-size: 10px; color: #333; margin: 0 0 2px 0;">
                    {{ $college->address }}
                </p>
            </div>

            <!-- Report Type -->
            <div class="text-center mb-4">
                <h3 style="font-size: 13px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase;">
                    {{ $examData['type'] }} {{ $examData['month'] }}
                </h3>
            </div>

            <!-- Student & Exam Info Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 4px; font-size: 10px;">
                <div style="border-bottom: 1px solid #ccc; padding-bottom: 4px;">
                    <span style="font-weight: bold;">Admission Year:</span> 
                    <span>{{ $student->batch ?? 'N/A' }}</span>
                </div>
                <div style="border-bottom: 1px solid #ccc; padding-bottom: 4px;">
                    <span style="font-weight: bold;">Term:</span> 
                    <span>{{ $examData['term'] }}</span>
                </div>
                <div style="border-bottom: 1px solid #ccc; padding-bottom: 4px;">
                    <span style="font-weight: bold;">Semester:</span> 
                    <span>{{ $examData['semester'] }}</span>
                </div>
            </div>

            <!-- Exam Marks Table -->
            <table style="width: 100%; border-collapse: collapse; margin: 8px 0;">
                <thead>
                    <tr style="background-color: #2d2d2d; color: white;">
                        <th style="border: 1px solid #666; padding: 6px; text-align: left; font-weight: bold; font-size: 10px; width: 8%;">S.N.</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: left; font-weight: bold; font-size: 10px; width: 10%;">Subject Code</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: left; font-weight: bold; font-size: 10px;">Subject Name</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: center; font-weight: bold; font-size: 10px; width: 10%;">Marks</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: center; font-weight: bold; font-size: 10px; width: 10%;">Out Of</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: center; font-weight: bold; font-size: 10px; width: 12%;">Percentage</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: center; font-weight: bold; font-size: 10px; width: 8%;">Grade</th>
                        <th style="border: 1px solid #666; padding: 6px; text-align: center; font-weight: bold; font-size: 10px; width: 8%;">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalMarks = 0; $totalOutOf = 0; @endphp
                    @foreach($subjects as $idx => $subject)
                        @php
                            $totalMarks += $subject['marks'];
                            $totalOutOf += $subject['outOf'];
                            $passed = $subject['marks'] >= 40;
                        @endphp
                        <tr style="background-color: {{ $idx % 2 === 0 ? '#ffffff' : '#f5f5f5' }};">
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px;">
                                {{ $idx + 1 }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: left; font-size: 10px;">
                                {{ $subject['code'] }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: left; font-size: 10px;">
                                {{ $subject['name'] }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px; font-weight: bold;">
                                {{ $subject['marks'] }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px;">
                                {{ $subject['outOf'] }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px;">
                                {{ number_format($subject['percentage'], 1) }}%
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px; font-weight: bold; background-color: #fff3cd;">
                                {{ $subject['grade'] }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 5px; text-align: center; font-size: 10px; font-weight: bold; background-color: {{ $passed ? '#d4edda' : '#f8d7da' }}; color: {{ $passed ? '#155724' : '#721c24' }};">
                                {{ $passed ? 'Pass' : 'Fail' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #e9ecef; font-weight: bold;">
                        <td colspan="3" style="border: 1px solid #ddd; padding: 6px; text-align: right; font-size: 10px;">
                            TOTAL / AVERAGE
                        </td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-size: 10px;">
                            {{ $totalMarks }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-size: 10px;">
                            {{ $totalOutOf }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center; font-size: 10px;">
                            {{ number_format(($totalMarks / $totalOutOf) * 100, 1) }}%
                        </td>
                        <td colspan="2" style="border: 1px solid #ddd; padding: 6px; text-align: center; font-size: 10px;">
                            @php
                                $avgPercentage = ($totalMarks / $totalOutOf) * 100;
                                $overallGrade = match(true) {
                                    $avgPercentage >= 90 => 'A+',
                                    $avgPercentage >= 80 => 'A',
                                    $avgPercentage >= 70 => 'B+',
                                    $avgPercentage >= 60 => 'B',
                                    $avgPercentage >= 50 => 'C',
                                    default => 'F'
                                };
                            @endphp
                            {{ $overallGrade }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Remarks & Comments -->
            <div style="margin-top: 12px; padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9; font-size: 10px;">
                <p style="font-weight: bold; margin: 0 0 4px 0;">Remarks:</p>
                <p style="margin: 0; line-height: 1.5;">
                    Student has shown consistent performance across subjects with an overall grade of <strong>{{ $overallGrade }}</strong>.
                    @if($avgPercentage >= 80)
                        Excellent performance. Maintain the standard!
                    @elseif($avgPercentage >= 70)
                        Good performance. Continue to improve!
                    @else
                        Need more effort to improve results. Focus on weak subjects.
                    @endif
                </p>
            </div>

            <!-- Student & Authority Info -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 20px;">
                <div style="text-align: center; padding-top: 30px;">
                    <p style="border-top: 1px solid #000; margin: 0; font-size: 9px; font-weight: bold; padding-top: 4px;">
                        Student Signature
                    </p>
                </div>
                <div style="text-align: center; padding-top: 30px;">
                    <p style="border-top: 1px solid #000; margin: 0; font-size: 9px; font-weight: bold; padding-top: 4px;">
                        Teacher Signature
                    </p>
                </div>
                <div style="text-align: center; padding-top: 30px;">
                    <p style="border-top: 1px solid #000; margin: 0; font-size: 9px; font-weight: bold; padding-top: 4px;">
                        Principal/Head Signature
                    </p>
                </div>
            </div>

            <!-- Footer Info -->
            <div style="margin-top: 16px; padding-top: 8px; border-top: 1px solid #ddd; text-align: center; font-size: 8px; color: #666;">
                <p style="margin: 0;">
                    Printed: {{ date('F d, Y H:i A') }} | Student: {{ $student->name ?? 'N/A' }} 
                    @if($student->roll_no ?? false)
                        (Roll No: {{ $student->roll_no }})
                    @endif
                </p>
                <p style="margin: 4px 0 0 0;">
                    This is an officially generated document. Valid without signature for reference purpose.
                </p>
            </div>

        </div>
    </x-print-as-image>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
        <h4 class="font-semibold mb-2">📋 Student Exam Report</h4>
        <ul class="text-sm space-y-1 list-disc list-inside">
            <li>Professional school report format</li>
            <li>Press <kbd class="bg-white px-2 py-1 border rounded">Ctrl+P</kbd> to print as high-DPI PNG</li>
            <li>Automatic A4 page sizing and formatting</li>
            <li>Perfect for academic records and archival</li>
        </ul>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/print-as-image.js') }}"></script>
@endpush
@endsection
