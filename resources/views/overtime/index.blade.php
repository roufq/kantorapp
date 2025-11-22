@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Overtime Requests</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Overtime</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Overtime Requests</h1>
    <div>
        @if(auth()->user()->hasRole('Super Admin'))
            <a href="{{ route('overtime.report') }}" class="btn btn-info me-2">View Report</a>
        @endif
        @if(auth()->user()->hasRole('Karyawan'))
            <a href="{{ route('overtime.create') }}" class="btn btn-primary">Request Overtime</a>
        @endif
    </div>
</div>

<!-- Search Form -->
<div class="mb-4">
    <form method="GET" action="{{ route('overtime.index') }}" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by reason or employee name..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
        @endif
    </form>
</div>

<div class="row">
    @foreach($overtimes as $overtime)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <a href="{{ route('overtime.show', $overtime) }}">
                            Overtime Request - {{ $overtime->date->format('d M Y') }}
                        </a>
                    </h5>
                    <span class="badge
                        @if($overtime->status === 'approved') bg-success
                        @elseif($overtime->status === 'rejected') bg-danger
                        @else bg-warning
                        @endif">
                        {{ ucfirst($overtime->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <p><strong>Employee:</strong> {{ $overtime->user->name }}</p>
                    <p><strong>Time:</strong> {{ $overtime->start_time_wib }} - {{ $overtime->end_time_wib }} WIB ({{ number_format($overtime->duration_minutes, 0) }} minutes)</p>
                    <p><strong>Reason:</strong> {{ Str::limit($overtime->reason, 100) }}</p>

                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']))
                        <div class="mt-3">
                            <strong>Approvals:</strong>
                            @foreach($overtime->approvals as $approval)
                                <div class="mb-1">
                                    <span class="badge
                                        @if($approval->status === 'approved') bg-success
                                        @elseif($approval->status === 'rejected') bg-danger
                                        @else bg-secondary
                                        @endif">
                                        {{ $approval->master->name }}: {{ ucfirst($approval->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-sm btn-outline-primary">View Details</a>

                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']) && in_array(auth()->id(), $overtime->selected_masters))
                        @php
                            $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
                        @endphp
                        @if($userApproval && $userApproval->status === 'pending')
                            <button class="btn btn-sm btn-success ms-2" data-bs-toggle="modal" data-bs-target="#approveModal{{ $overtime->id }}">Approve/Reject</button>
                        @endif
                    @endif

                    @if(auth()->user()->hasRole('Karyawan') && $overtime->user_id === auth()->id() && $overtime->status === 'pending')
                        <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this overtime request?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Approval Modal -->
        @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']) && in_array(auth()->id(), $overtime->selected_masters))
            @php
                $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
            @endphp
            @if($userApproval && $userApproval->status === 'pending')
                <div class="modal fade" id="approveModal{{ $overtime->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Approve Overtime Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('overtime.approve', $overtime) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Decision</label>
                                        <select name="status" class="form-select" required>
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes (Optional)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Decision</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
</div>

<!-- Pagination -->
@if($overtimes->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $overtimes->appends(request()->query())->links() }}
    </div>
@endif
@endsection
