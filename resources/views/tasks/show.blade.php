@extends('layouts.app')

@section('content')
@php
    $me = auth()->user();
    $isAssignee = $task->assigned_to === $me->id;
    $canApproveBase = $me->hasRole('Super Admin') || ($me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id);
@endphp

@if($isAssignee)
<div class="row" id="progress-form">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">Update Progress (wajib lampiran)</h5>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary">Kembali ke Tasks</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.progress.store', $task) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Progress (%)</label>
                        <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ old('progress', $task->progress) }}" required>
                        <small class="text-muted">Nilai 0-100, status otomatis disesuaikan.</small>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ringkasan progres atau kendala">{{ old('note') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto Bukti</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dokumen Bukti</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Kirim Progres</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Riwayat Progres & Approval</h5>
                @if($canApproveBase)
                    <small class="text-muted">Admin Lokasi menyetujui karyawan; Super Admin menyetujui tanpa admin lokasi & admin lokasi.</small>
                @endif
            </div>
            <div class="card-body">
                @forelse($progressUpdates as $update)
                    @php
                        $pending = $update->approval_status === 'pending';
                        $canApproveThis = $pending && (
                            ($me->hasRole('Super Admin')) ||
                            ($update->approval_level === 'location_admin' && $me->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $me->location_id)
                        );
                    @endphp
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">Progress {{ $update->progress }}%</div>
                                <div class="small text-muted">Dikirim oleh {{ $update->user->name }} • {{ $update->created_at->format('d M Y H:i') }}</div>
                                <div class="mt-1">
                                    <span class="badge text-bg-{{ $update->approval_status === 'approved' ? 'success' : ($update->approval_status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($update->approval_status) }}
                                    </span>
                                    @if($update->approval_level !== 'none')
                                        <span class="badge text-bg-light text-muted">Target: {{ $update->approval_level === 'location_admin' ? 'Admin Lokasi' : 'Super Admin' }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($canApproveThis)
                                <div class="d-flex gap-2">
                                    <form action="{{ route('tasks.progress.approve', $update) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('tasks.progress.reject', $update) }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="Alasan reject" required>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        @if($update->note)
                            <div class="mt-2 small"><strong>Catatan:</strong> {{ $update->note }}</div>
                        @endif
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @if($update->photo_path)
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.progress.download', [$update, 'photo']) }}" target="_blank">Download Photo</a>
                            @endif
                            @if($update->document_path)
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.progress.download', [$update, 'document']) }}" target="_blank">Download Document</a>
                            @endif
                        </div>
                        @if($update->approval_status === 'rejected' && $update->rejection_reason)
                            <div class="mt-2 text-danger small"><strong>Alasan penolakan:</strong> {{ $update->rejection_reason }}</div>
                        @endif
                        @if($update->approver)
                            <div class="mt-1 small text-muted">Diproses oleh {{ $update->approver->name }} @ {{ optional($update->approved_at)->format('d M Y H:i') }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada riwayat progres.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
