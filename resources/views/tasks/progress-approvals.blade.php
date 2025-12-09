@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.app')

@section('content')
<div class="bg-light p-3 mb-3 rounded border">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1">Approval Progress Tugas</h1>
            <p class="text-muted mb-0">Setujui atau tolak progres tugas berbasis slot. Legacy progress tetap ditampilkan untuk backlog lama.</p>
        </div>
        <div>
            <a href="{{ route('tasks.index') }}" class="text-decoration-none">Kembali ke Tasks</a>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Pending Approval (Per Tugas)</h5>
    </div>
    <div class="card-body">
        @forelse($pendingTasks ?? [] as $task)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold h6 mb-1">{{ $task->title }}</div>
                        <div class="small text-muted">
                            Assignee: {{ optional($task->assignee)->name }} @ {{ optional($task->assignee->location)->name }}
                            • Durasi: {{ $task->duration_minutes ?? '-' }} menit
                            • Due: {{ optional($task->due_date)->format('d M Y') }}
                        </div>
                        <div class="small">Total slot pending: {{ $task->slots->count() }}</div>
                    </div>
                    <div class="d-flex align-items-start gap-2 flex-wrap">
                        <form action="{{ route('tasks.approve-slots', $task) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Approve Tugas</button>
                        </form>
                        <form action="{{ route('tasks.reject-slots', $task) }}" method="POST" class="d-flex align-items-start gap-2">
                            @csrf
                            <textarea name="reason" class="form-control form-control-sm" placeholder="Alasan reject (wajib)" rows="2" required style="min-width: 200px;"></textarea>
                            <button type="submit" class="btn btn-outline-danger btn-sm">Reject Tugas</button>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="fw-semibold small mb-2">Rincian Slot Pending</div>
                    @foreach($task->slots as $slot)
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                <div>
                                    <div class="fw-semibold">{{ $slot->name }} ({{ $slot->percentage }}% / {{ $slot->minutes }} menit)</div>
                                    <div class="small text-muted">Dikirim: {{ $slot->updated_at->format('d M Y H:i') }}</div>
                                    @if($slot->rejection_reason)
                                        <div class="text-danger small">Alasan sebelumnya: {{ $slot->rejection_reason }}</div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @forelse($slot->attachments as $att)
                                        @if($att->type === 'link')
                                            <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-outline-primary btn-sm">Link</a>
                                        @else
                                            <a href="{{ Storage::disk('public')->url($att->path_or_url) }}" target="_blank" class="btn btn-outline-primary btn-sm">{{ ucfirst($att->type) }}</a>
                                        @endif
                                    @empty
                                        <span class="text-muted small">Tidak ada lampiran.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">Tidak ada tugas yang menunggu persetujuan.</p>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Pending Approval (Legacy Progress)</h5>
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
                            <span class="badge text-bg-light text-muted">Target: {{ ucfirst(str_replace('_', ' ', $update->approval_level)) }}</span>
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
