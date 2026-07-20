<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Marks Report - {{ ucfirst($category) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
            font-size: 9px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .pass {
            color: green;
        }
        .fail {
            color: red;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $college->name ?? 'Manmohan Memorial Polytechnic' }}</h1>
        <p>Marks Report - {{ ucfirst($category) }}</p>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <div class="info">
        <p><strong>Category:</strong> {{ ucfirst($category) }}</p>
        @if(isset($filters['academic_year']) && $filters['academic_year'])
            <p><strong>Academic Year:</strong> {{ $filters['academic_year'] }}</p>
        @endif
        @if(isset($filters['semester']) && $filters['semester'])
            <p><strong>Semester:</strong> {{ $filters['semester'] }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Roll No</th>
                <th rowspan="2">Student Name</th>
                <th rowspan="2">Sem</th>
                <th rowspan="2">Attend %</th>
                
                @foreach($columnStructure->subjects as $subject)
                    <th colspan="{{ $columnStructure->colspan }}">
                        {{ $subject->subject_name }}
                    </th>
                @endforeach
                
                <th rowspan="2">Total</th>
                <th rowspan="2">%</th>
                <th rowspan="2">Result</th>
            </tr>
            
            @if($category === 'ctevt')
            <tr>
                @foreach($columnStructure->subjects as $subject)
                    @foreach($columnStructure->components as $component)
                        <th>F</th><th>P</th><th>O</th>
                    @endforeach
                @endforeach
            </tr>
            @else
            <tr>
                @foreach($columnStructure->subjects as $subject)
                    <th>Full</th><th>Pass</th><th>Obt</th>
                @endforeach
            </tr>
            @endif
        </thead>
        
        <tbody>
            @foreach($students as $student)
                @php
                    $subjectIds = collect($columnStructure->subjects)->pluck('id')->toArray();
                    $totalMarks = $student->getTotalMarks($subjectIds);
                    $totalFull = $student->getTotalFullMarks($subjectIds);
                    $percentage = $totalFull > 0 ? round(($totalMarks / $totalFull) * 100, 1) : 0;
                    $result = $percentage >= 40 ? 'PASS' : 'FAIL';
                @endphp
                <tr>
                    <td>{{ $student->roll_no }}</td>
                    <td style="text-align: left;">{{ $student->user->name ?? 'N/A' }}</td>
                    <td>{{ $student->semester }}</td>
                    <td>{{ $student->getAttendancePercentage() }}%</td>
                    
                    @foreach($columnStructure->subjects as $subject)
                        @if($category === 'ctevt')
                            @foreach($columnStructure->components as $component)
                                @php $compMarks = $student->getComponentMarks($subject->id, $component); @endphp
                                <td>{{ $compMarks->full }}</td>
                                <td>{{ $compMarks->pass }}</td>
                                <td>{{ $compMarks->obtained }}</td>
                            @endforeach
                        @else
                            @php $assessMarks = $student->getAssessmentMarks($subject->id); @endphp
                            <td>{{ $assessMarks->full }}</td>
                            <td>{{ $assessMarks->pass }}</td>
                            <td>{{ $assessMarks->obtained }}</td>
                        @endif
                    @endforeach
                    
                    <td><strong>{{ $totalMarks }}</strong></td>
                    <td>{{ $percentage }}%</td>
                    <td class="{{ $result === 'PASS' ? 'pass' : 'fail' }}">
                        <strong>{{ $result }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Students: {{ $students->count() }}</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>

