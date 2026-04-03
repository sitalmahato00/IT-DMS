@extends('admin.layouts.app')

@section('title', 'Edit Exam')

@section('content')
@include('admin.components.admin-page-header', [
    'title' => 'Edit Exam',
    'breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Exams', 'url' => route('admin.exam')],
        ['label' => $exam->localized_name ?? 'Edit Exam']
    ],
    'addButton' => [
        'label' => 'Exam List',
        'route' => route('admin.exam'),
        'color' => 'blue'
    ]
])

@include('admin.exams.partials.form', [
    'isEdit' => true,
    'formAction' => route('admin.exam.update', $exam->id),
    'backRoute' => route('admin.exam.show', $exam->id),
    'submitLabel' => 'Update Exam',
    'exam' => $exam,
])
@endsection

@section('scripts')
    @include('admin.exams.partials.scripts', ['isEdit' => true])
@endsection
