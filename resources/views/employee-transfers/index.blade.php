@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Employee Transfers</h3>
        <p class="text-muted mb-0">Employee location transfer history.</p>
    </div>
    <a href="{{ route('employee-transfers.create') }}" class="btn btn-primary btn-sm">Add Transfer</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Employees</label>
                <select name="employee_id" class="form-select">
                    <option value="">All</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('employee-transfers.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Transfer List</h3></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Employees</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td>{{ $loop->iteration + ($transfers->currentPage()-1)*$transfers->perPage() }}</td>
                        <td>{{ $transfer->employee?->nama ?? '-' }}</td>
                        <td>{{ $transfer->fromLocation?->name ?? '-' }}</td>
                        <td>{{ $transfer->toLocation?->name ?? '-' }}</td>
                        <td>{{ $transfer->effective_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('employee-transfers.edit', $transfer) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('employee-transfers.destroy', $transfer) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this transfer?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No transfers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $transfers->links() }}</div>
</div>
@endsection
