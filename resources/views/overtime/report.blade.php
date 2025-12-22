@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Overtime Report</h3>
        <p class="text-muted mb-0">Rekap pengajuan lembur berdasarkan filter.</p>
    </div>
    <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Overtime Report') }}</div>

                <div class="card-body">
                    {{-- Filters --}}
                    <form method="GET" action="{{ route('overtime.report') }}" class="mb-4 overtime-filter">
                        <div class="form-row overtime-filter-labels">
                            <div class="col-md-3 mb-2 overtime-filter-search">
                                <label for="search" class="form-label mb-1">Search</label>
                            </div>
                            <div class="col-md-2 mb-2 overtime-filter-tight overtime-filter-center overtime-filter-status">
                                <label for="status" class="form-label mb-1">Status</label>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="start_date" class="form-label mb-1">Start Date</label>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="end_date" class="form-label mb-1">End Date</label>
                            </div>
                            <div class="col-md-3 mb-2 d-none d-md-block"></div>
                        </div>
                        <div class="form-row align-items-center overtime-filter-inputs ">
                            <div class="col-md-3 mb-2 overtime-filter-search">
                                <input type="text" name="search" id="search" class="form-control" placeholder="Reason or employee name..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 mb-2 overtime-filter-tight overtime-filter-center overtime-filter-status overtime-filter-status-shift">
                                <select name="status" id="status" class="form-control w-100 overtime-filter-status-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end justify-content-md-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('overtime.export', request()->query()) }}" class="btn btn-success">Export to Excel</a>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:50px">No</th>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Duration (Minutes)</th>
                                <th>Reason</th>
                                <th class="col-status">Status</th>
                                <th>Approvals</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overtimes as $overtime)
                                <tr>
                                    <td>{{ $loop->iteration + (method_exists($overtimes, 'currentPage') ? ($overtimes->currentPage()-1)*$overtimes->perPage() : 0) }}</td>
                                    <td>{{ $overtime->user->name }}</td>
                                    <td>{{ $overtime->date->format('d M Y') }}</td>
                                    <td>{{ $overtime->start_time_wib }} - {{ $overtime->end_time_wib }} WIB</td>
                                    <td>{{ number_format($overtime->duration_minutes, 0) }} minutes</td>
                                    <td>{{ Str::limit($overtime->reason, 50) }}</td>
                                    <td class="col-status">
                                        <span class="badge status-badge
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
