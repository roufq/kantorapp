@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Position History</h3>
        <p class="text-muted mb-0">{{ $employee_position->employee?->nama ?? '-' }}</p>
    </div>
    <a href="{{ route('employee-positions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('employee-positions.update', $employee_position) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Employees</label>
                <select name="employee_id" class="form-select" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id', $employee_position->employee_id) == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Position</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $employee_position->title) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" value="{{ old('department', $employee_position->department) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($employee_position->start_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($employee_position->end_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $employee_position->notes) }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('employee-positions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
