@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Overtime Report') }}</div>

                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('overtime.report') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Reason or employee name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('overtime.export', request()->query()) }}" class="btn btn-success">Export to Excel</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Duration (Minutes)</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Approvals</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overtimes as $overtime)
                                <tr>
                                    <td>{{ $overtime->user->name }}</td>
                                    <td>{{ $overtime->date->format('d M Y') }}</td>
                                    <td>{{ $overtime->start_time_wib }} - {{ $overtime->end_time_wib }} WIB</td>
                                    <td>{{ number_format($overtime->duration_minutes, 0) }} minutes</td>
                                    <td>{{ Str::limit($overtime->reason, 50) }}</td>
                                    <td>
                                        <span class="badge
                                            @if($overtime->status === 'approved') bg-success
                                            @elseif($overtime->status === 'rejected') bg-danger
                                            @else bg-warning
                                            @endif">
                                            {{ ucfirst($overtime->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @foreach($overtime->approvals as $approval)
                                            <div class="mb-1">
                                                <small class="badge
                                                    @if($approval->status === 'approved') bg-success
                                                    @elseif($approval->status === 'rejected') bg-danger
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ $approval->master->name }}: {{ ucfirst($approval->status) }}
                                                </small>
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    {{ $overtimes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
