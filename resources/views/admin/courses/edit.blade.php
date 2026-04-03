@extends('admin.layouts.app')

@section('title', 'Edit Course')

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Edit Course',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Courses', 'url' => route('admin.courses')],
        ['label' => $course->subject_name ?? 'Edit Course']
    ]
])

@include('admin.courses.partials.form', [
    'course' => $course ?? new \App\Models\Course(),
    'semesters' => $semesters ?? collect(),
    'allTeachers' => $allTeachers ?? collect(),
    'labTechnicians' => $labTechnicians ?? collect(),
    'selectedTeacherId' => $selectedTeacherId ?? null,
    'formAction' => route('admin.courses.update', $course->id ?? 0),
    'submitLabel' => 'Update Course',
    'isEdit' => true,
])
@endsection
