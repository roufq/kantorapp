@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Approval Rule</h3>
        <p class="text-muted mb-0">Rule ID: {{ $approval_rule->id }}</p>
    </div>
    <a href="{{ route('approval-rules.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('approval-rules.update', $approval_rule) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-4">
                <label class="form-label">Scope</label>
                <input type="text" name="scope" class="form-control" value="{{ old('scope', $approval_rule->scope) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department (optional)</label>
                <input type="text" name="department" class="form-control" value="{{ old('department', $approval_rule->department) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Min Value</label>
                <input type="number" name="min_value" class="form-control" min="0" value="{{ old('min_value', $approval_rule->min_value) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Approval Level</label>
                <select name="approval_level" class="form-select" required>
                    <option value="location_admin" @selected(old('approval_level', $approval_rule->approval_level) === 'location_admin')>Location Admin</option>
                    <option value="super_admin" @selected(old('approval_level', $approval_rule->approval_level) === 'super_admin')>Super Admin</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $approval_rule->is_active))>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $approval_rule->notes) }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('approval-rules.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
