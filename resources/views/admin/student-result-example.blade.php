@extends('layouts.app')

@section('title', 'Student Result Sheet - Print Demo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Student Result Sheet Demo</h1>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h4 class="font-semibold mb-2">📋 Universal A4 Result Sheet</h4>
        <ul class="text-sm space-y-1 list-disc list-inside">
            <li>Professional A4 print format with proper margins</li>
            <li>Press <kbd class="bg-white px-2 py-1 border rounded">Ctrl+P</kbd> to print</li>
            <li>Includes header, student info, marks table, summary, and footer</li>
            <li>Black & white compatible for printing</li>
        </ul>
    </div>

    @php
        // Sample college data
        $college = (object)[
            'name' => 'Institute of Technology & Management',
            'short_name' => 'ITM',
            'address' => 'Balkumari, Lalitpur, Nepal',
            'phone' => '01-2345678',
            'email' => 'info@itm.edu.np',
            'logo_path' => null
        ];
        
        // Sample student data
        $studentData = [
            'name' => 'John Doe',
            'roll_no' => '001',
            'id' => 101,
            'semester' => '5',
        ];
        
        // Sample marks data
        $marksData = [
            ['subject_name' => 'Data Structures & Algorithms', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 85],
            ['subject_name' => 'Database Management Systems', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 78],
            ['subject_name' => 'Web Development', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 92],
            ['subject_name' => 'Operating Systems', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 81],
            ['subject_name' => 'Software Engineering', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 88],
            ['subject_name' => 'Computer Networks', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 72],
        ];
        
        // Exam metadata
        $examTitle = 'Mid Term Examination 2081';
        $academicYear = '2081';
        $semester = '5';
        $section = 'A';
        $totalMarks = 100;
        $passMarks = 40;
    @endphp

    <!-- Use the Universal Result Sheet Component -->
    <x-print::result-sheet
        id="student-result-demo"
        :college="$college"
        :examTitle="$examTitle"
        :academicYear="$academicYear"
        :semester="$semester"
        :section="$section"
        :totalMarks="$totalMarks"
        :passMarks="$passMarks"
        :student="$studentData"
        :marks="$marksData"
        :showGradeScale="true"
    >
    </x-print::result-sheet>

    <!-- Alternative usage with different data -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4">How to Use in Your Code</h3>
        
        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto">
            <pre class="text-sm">
// In your Blade view, use the component like this:

@php
$college = auth()->user()->college; // Or fetch from database
$student = [
    'name' => $student->name,
    'roll_no' => $student->student->roll_no,
    'id' => $student->id,
    'semester' => $student->student->semester,
];

$marks = [
    ['subject_name' => 'Mathematics', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 85],
    ['subject_name' => 'Science', 'full_marks' => 100, 'pass_marks' => 40, 'obtained_marks' => 78],
    // ... more subjects
];
@endphp

<x-print::result-sheet
    id="result-{{ $student->id }}"
    :college="$college"
    examTitle="Final Examination 2081"
    academicYear="2081"
    semester="5"
    section="A"
    :totalMarks="100"
    :passMarks="40"
    :student="$student"
    :marks="$marks"
    :showGradeScale="true"
>
</x-print::result-sheet>
            </pre>
        </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            ← Back to Students
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            🖨️ Print Result Sheet
        </button>
    </div>

@push('scripts')
<script src="{{ asset('js/print-as-image.js') }}"></script>
@endpush
@endsection
