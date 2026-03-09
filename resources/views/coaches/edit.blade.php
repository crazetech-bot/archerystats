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
@if(session('profile_incomplete') || (!$coach->isProfileComplete() && auth()->user()->role === 'coach'))
<div class="max-w-4xl mx-auto mb-5">
    <div class="flex items-start gap-3 px-4 py-4 rounded-2xl text-sm"
         style="background:#fefce8; border:1px solid #fde047; color:#713f12;">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" style="color:#ca8a04;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <div>
            <p class="font-bold mb-0.5">Complete your profile before continuing</p>
            <p class="text-xs" style="color:#92400e;">Please fill in all required fields in the <strong>Personal Details</strong> section below — Gender, Contact Number, and Date of Birth. You must complete your profile to access the rest of the app.</p>
        </div>
    </div>
</div>
@endif
@include('coaches._form', [
    'formAction' => route('coaches.update', $coach),
    'formMethod' => 'PUT',
])
@endsection
