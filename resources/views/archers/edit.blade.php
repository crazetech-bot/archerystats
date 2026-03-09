@extends('layouts.app')

@section('title', 'Edit — ' . $archer->ref_no)
@section('header', 'Edit Archer: ' . $archer->full_name)

@section('content')
@if(session('profile_incomplete') || (!$archer->isProfileComplete() && auth()->user()->role === 'archer'))
<div class="max-w-4xl mx-auto mb-5">
    <div class="flex items-start gap-3 px-4 py-4 rounded-2xl text-sm"
         style="background:#fefce8; border:1px solid #fde047; color:#713f12;">
        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" style="color:#ca8a04;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <div>
            <p class="font-bold mb-0.5">Complete your profile before continuing</p>
            <p class="text-xs" style="color:#92400e;">Please fill in all required fields in the <strong>Personal Information</strong> and <strong>Athlete Profile</strong> sections below. You must complete your profile to access the rest of the app.</p>
        </div>
    </div>
</div>
@endif
@include('archers._form', [
    'archer'     => $archer,
    'clubs'      => $clubs,
    'states'     => $states,
    'divisions'  => $divisions,
    'formAction' => route('archers.update', $archer),
    'formMethod' => 'PUT',
])
@endsection
