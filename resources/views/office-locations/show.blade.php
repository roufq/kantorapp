@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Office Location Details</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $officeLocation->id }}</p>
            <p><strong>Name:</strong> {{ $officeLocation->name }}</p>
            <p><strong>Latitude:</strong> {{ $officeLocation->latitude }}</p>
            <p><strong>Longitude:</strong> {{ $officeLocation->longitude }}</p>
            <p><strong>Radius (m):</strong> {{ $officeLocation->radius }}</p>
            <p><strong>Created At:</strong> {{ $officeLocation->created_at }}</p>
            <p><strong>Updated At:</strong> {{ $officeLocation->updated_at }}</p>
        </div>
    </div>
    <a href="{{ route('office-locations.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
