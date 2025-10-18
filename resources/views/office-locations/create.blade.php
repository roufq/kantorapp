@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New Office Location</h1>
    <form action="{{ route('office-locations.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="number" step="any" class="form-control" id="latitude" name="latitude" required>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="number" step="any" class="form-control" id="longitude" name="longitude" required>
        </div>
        <div class="form-group">
            <label for="radius">Radius (meters)</label>
            <input type="number" step="any" class="form-control" id="radius" name="radius" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('office-locations.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
