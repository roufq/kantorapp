@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Office Location</h1>
    <form action="{{ route('office-locations.update', $officeLocation) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $officeLocation->name }}" required>
        </div>
        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ $officeLocation->latitude }}" required>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ $officeLocation->longitude }}" required>
        </div>
        <div class="form-group">
            <label for="radius">Radius (meters)</label>
            <input type="number" step="any" class="form-control" id="radius" name="radius" value="{{ $officeLocation->radius }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('office-locations.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
