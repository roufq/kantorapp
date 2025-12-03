@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Approval Progress Tugas</h1>
            <p class="text-muted mb-0">Setujui atau tolak progres dengan bukti foto/dokumen sebelum status tugas diperbarui.</p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali ke Tasks</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Pending Approval</h5>
    </div>
    <div class="card-body">
        @forelse($pendingUpdates as $update)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold">Task: {{ $update->task->title }}</div>
                        <div class="small text-muted">Assignee: {{ optional($update->task->assignee)->name }} • Progress {{ $update->progress }}% • Dikirim oleh {{ $update->user->name }} ({{ $update->created_at->format('d M Y H:i') }})</div>
                        <div class="mt-1 small">
                            <span class="badge text-bg-warning">Pending</span>
                            <span class="badge text-bg-light text-muted">Target: {{ $update->approval_level === 'location_admin' ? 'Admin Lokasi' : 'Super Admin' }}</span>
                        </div>
                        @if($update->note)
                            <div class="mt-1 small"><strong>Catatan:</strong> {{ $update->note }}</div>
                        @endif
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            @if($update->photo_path)
                                <a href="{{ route('tasks.progress.download', [$update, 'photo']) }}" target="_blank" class="btn btn-outline-primary btn-sm">Download Photo</a>
                            @endif
                            @if($update->document_path)
                                <a href="{{ route('tasks.progress.download', [$update, 'document']) }}" target="_blank" class="btn btn-outline-primary btn-sm">Download Document</a>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-2 flex-wrap">
                        <form action="{{ route('tasks.progress.approve', $update) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <form action="{{ route('tasks.progress.reject', $update) }}" method="POST" class="w-100">
                            @csrf
                            <label class="form-label small mb-1 fw-semibold">Alasan reject (wajib diisi)</label>
                            <div class="d-flex align-items-start gap-2">
                                <textarea name="reason" class="form-control form-control-sm" placeholder="Tulis alasan penolakan..." rows="3" required style="min-width: 260px; flex:1;"></textarea>
                                <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">Tidak ada progres yang menunggu persetujuan.</p>
        @endforelse

        @if($pendingUpdates->hasPages())
            <div class="mt-3">
                {{ $pendingUpdates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
