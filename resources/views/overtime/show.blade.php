@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Detail Lembur</h3>
        <p class="text-muted mb-0">Pengajuan lembur tanggal {{ $overtime->date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('overtime.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Overtime Request Details</h3>
                <div class="card-tools d-flex gap-2">
                    @if(auth()->user()->hasRole('Karyawan') && $overtime->user_id === auth()->id() && $overtime->status === 'pending')
                        <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Employee:</strong> {{ $overtime->user->name }}</p>
                        <p><strong>Date:</strong> {{ $overtime->date->format('d M Y') }}</p>
                        <p><strong>Time:</strong> {{ $overtime->start_time_wib }} - {{ $overtime->end_time_wib }} WIB</p>
                        <p><strong>Duration:</strong> {{ number_format($overtime->duration_minutes, 0) }} minutes</p>
                        <p><strong>Reason:</strong> {{ $overtime->reason }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge
                                @if($overtime->status === 'approved') bg-success
                                @elseif($overtime->status === 'rejected') bg-danger
                                @else bg-warning
                                @endif">
                                {{ ucfirst($overtime->status) }}
                            </span>
                        </p>
                        <p><strong>Created:</strong> {{ $overtime->created_at->format('d M Y H:i') }}</p>
                        <p><strong>Updated:</strong> {{ $overtime->updated_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Approval Status</h5>
                        @foreach($overtime->selected_masters as $masterId)
                            @php
                                $master = \App\Models\User::find($masterId);
                                $approval = $overtime->approvals->where('master_id', $masterId)->first();
                            @endphp
                            <div class="mb-3 p-3 border rounded">
                                <strong>{{ $master->name }}</strong><br>
                                <span class="badge
                                    @if($approval && $approval->status === 'approved') bg-success
                                    @elseif($approval && $approval->status === 'rejected') bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ $approval ? ucfirst($approval->status) : 'Pending' }}
                                </span>
                                @if($approval && $approval->approved_at)
                                    <br><small class="text-muted">Approved at: {{ $approval->approved_at->format('d M Y H:i') }}</small>
                                @endif
                                @if($approval && $approval->notes)
                                    <br><small><strong>Notes:</strong> {{ $approval->notes }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Lokasi']) && in_array(auth()->id(), $overtime->selected_masters))
    @php
        $userApproval = $overtime->approvals->where('master_id', auth()->id())->first();
    @endphp
    @if($userApproval && $userApproval->status === 'pending')
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Make Decision</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('overtime.approve', $overtime) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Decision</label>
                                <select name="status" class="form-select" required>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="3" name="notes" placeholder="Add any notes..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Decision</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
