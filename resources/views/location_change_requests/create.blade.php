@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Location Change Request</h1>

    <form action="{{ route('location-change-requests.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="target_location_id">Target Location</label>
            <select name="target_location_id" id="target_location_id" class="form-control" required>
                @foreach($locations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="reason">Reason</label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>
@endsection
