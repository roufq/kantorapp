@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Edit Transfer</h3>
        <p class="text-muted mb-0">{{ $employee_transfer->employee?->nama ?? '-' }}</p>
    </div>
    <a href="{{ route('employee-transfers.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('employee-transfers.update', $employee_transfer) }}" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Employees</label>
                <select name="employee_id" class="form-select" required>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id', $employee_transfer->employee_id) == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">From Location</label>
                <select name="from_location_id" class="form-select">
                    <option value="">-</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('from_location_id', $employee_transfer->from_location_id) == $loc->id)>{{ $loc->name ?? $loc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">To Location</label>
                <select name="to_location_id" class="form-select">
                    <option value="">-</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" @selected(old('to_location_id', $employee_transfer->to_location_id) == $loc->id)>{{ $loc->name ?? $loc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Effective Date</label>
                <input type="date" name="effective_date" class="form-control" value="{{ old('effective_date', optional($employee_transfer->effective_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3">{{ old('reason', $employee_transfer->reason) }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('employee-transfers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
