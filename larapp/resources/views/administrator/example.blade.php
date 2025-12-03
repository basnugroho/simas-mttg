@extends('components.administrator.layout')

@section('title', 'Example Admin')

@section('header', 'Example Admin Page')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Welcome to AdminLTE 4</h3>
        </div>
        <div class="card-body">
            <p>This is an example admin page using AdminLTE 4 (CDN).</p>
            <a class="btn btn-primary" href="{{ route('dashboard') }}">Go to Dashboard</a>
        </div>
    </div>
@endsection
