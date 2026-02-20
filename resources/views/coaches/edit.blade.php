@extends('layouts.app')

@section('title', 'Edit Coach — ' . $coach->full_name)
@section('header', 'Edit Coach')
@section('subheader', $coach->ref_no . ' — ' . $coach->full_name)

@section('header-actions')
    <a href="{{ route('coaches.show', $coach) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')
@include('coaches._form', [
    'formAction' => route('coaches.update', $coach),
    'formMethod' => 'PUT',
])
@endsection
