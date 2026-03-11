@extends('layouts.app')

@section('title', 'Add Archer')
@section('header', 'Add New Archer')

@section('content')
@include('archers._form', [
    'archer'     => null,
    'clubs'      => $clubs,
    'states'     => $states,
    'divisions'  => $divisions,
    'formAction' => route('archers.store'),
    'formMethod' => 'POST',
])
@endsection
