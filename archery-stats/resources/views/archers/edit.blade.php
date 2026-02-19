@extends('layouts.app')

@section('title', 'Edit — ' . $archer->ref_no)
@section('header', 'Edit Archer: ' . $archer->full_name)

@section('content')
@include('archers._form', [
    'archer'     => $archer,
    'clubs'      => $clubs,
    'states'     => $states,
    'divisions'  => $divisions,
    'formAction' => route('archers.update', $archer),
    'formMethod' => 'PUT',
])
@endsection
