@extends('student.layouts.studentlayout')

@section('title', trim(strip_tags($header ?? __('Student Portal'))))

@section('content')
    {{ $slot ?? ($content ?? '') }}
@endsection
