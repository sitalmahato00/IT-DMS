@extends('admin.layouts.app')

@section('title', 'Add Course')

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Add Course',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Courses', 'url' => route('admin.courses')],
        ['label' => 'Add Course']
    ]
])

@include('admin.courses.partials.form', [
    'course' => $course ?? new \App\Models\Course(),
    'semesters' => $semesters ?? collect(),
    'allTeachers' => $allTeachers ?? collect(),
    'labTechnicians' => $labTechnicians ?? collect(),
    'selectedTeacherId' => $selectedTeacherId ?? null,
    'formAction' => route('admin.courses.store'),
    'submitLabel' => 'Save Course',
    'isEdit' => false,
])
@endsection
