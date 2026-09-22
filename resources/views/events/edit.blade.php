@extends('layouts.app')

@section('title', 'Edit Event')
@section('header', 'Edit Event')
@section('subheader', $event->title)

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
            <h2 class="text-sm font-bold text-gray-900">Edit Event Details</h2>
        </div>
        <form method="POST" action="{{ route('events.update', $event) }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            @include('events._fields', ['event' => $event, 'composeAudiences' => $composeAudiences, 'coachMode' => $coachMode])
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('events.show', $event) }}" class="text-sm font-semibold px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="ml-auto px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, #4338ca, #6366f1);">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
