@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Add Jobdesk</h3>
        <p class="text-muted mb-0">Create jobdesk definition that can be used as task category.</p>
    </div>
    <a href="{{ route('jobdesks.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('jobdesks.store') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Jobdesk Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Role Scope (optional)</label>
                <input type="text" name="role_scope" class="form-control" value="{{ old('role_scope') }}" placeholder="Example: Employee, Location Admin">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Location</label>
                <select name="location_id" class="form-select">
                    <option value="">Global (All Locations)</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('location_id') == $loc->id)>{{ $loc->name ?? $loc->nama ?? 'Lokasi '.$loc->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Minimum Attendance Hours (minutes, non-shift)</label>
                <input type="number" name="min_attendance_minutes" class="form-control" min="0" value="{{ old('min_attendance_minutes') }}" placeholder="Example: 360 for 6 hours">
            </div>
            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('jobdesks.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
