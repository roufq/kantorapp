@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Office Locations</h1>
    <a href="{{ route('office-locations.create') }}" class="btn btn-primary">Add New Location</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Radius (m)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $location)
            <tr>
                <td>{{ $location->id }}</td>
                <td>{{ $location->name }}</td>
                <td>{{ $location->latitude }}</td>
                <td>{{ $location->longitude }}</td>
                <td>{{ $location->radius }}</td>
                <td>
                    <a href="{{ route('office-locations.show', $location) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('office-locations.edit', $location) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('office-locations.destroy', $location) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
