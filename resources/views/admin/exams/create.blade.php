@extends('admin.layouts.app')

@section('title', 'Add Exam')

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Add Exam',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Exams', 'url' => route('admin.exam')],
        ['label' => 'Add Exam']
    ],
    'addButton' => [
        'label' => 'Exam List',
        'route' => route('admin.exam'),
        'color' => 'blue'
    ]
])

@include('admin.exams.partials.form', [
    'isEdit' => false,
    'formAction' => route('admin.exam.store'),
    'backRoute' => route('admin.exam'),
    'submitLabel' => 'Create Exam',
    'exam' => null,
])
@endsection

@section('scripts')
    @include('admin.exams.partials.scripts', ['isEdit' => false])
@endsection
