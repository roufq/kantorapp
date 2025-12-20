@extends('layouts.appnew')

@section('title')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <h3 class="mb-0">Update Progress: {{ $task->title }}</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Progress</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Progres per Slot</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Kirim bukti per slot berupa link. Progres tugas akan otomatis dihitung dari slot yang disetujui.</p>
                @if($task->slots->isEmpty())
                    <div class="alert alert-warning mb-0">
                        Belum ada slot untuk tugas ini. Hubungi Admin Lokasi/Super Admin untuk menambahkan slot sebelum mengirim progres.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Slot Progres</h5>
            </div>
            <div class="card-body">
                @forelse($task->slots as $slot)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $slot->name }} ({{ $slot->percentage }}% | {{ $slot->minutes }} menit)</div>
                                <div class="small text-muted">Status: <span class="badge text-bg-{{ $slot->status === 'approved' ? 'success' : ($slot->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($slot->status) }}</span></div>
                                @if($slot->rejection_reason)
                                    <div class="text-danger small">Alasan reject: {{ $slot->rejection_reason }}</div>
                                @endif
                            </div>
                            @php
                                $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                            @endphp
                            <div class="d-flex align-items-start gap-2 flex-wrap">
                                <div class="fw-semibold small mb-1">Lampiran</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @php $linkAttachments = $slot->attachments->where('type','link'); @endphp
                                    @forelse($linkAttachments as $att)
                                        <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Link {{ $loop->iteration }}</a>
                                    @empty
                                        <span class="text-muted small">Belum ada lampiran.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        @php
                            $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                        @endphp
                        <div class="mt-2">
                            @if($slotCanSubmit)
                                <form action="{{ route('task-slots.submit', $slot) }}" method="POST" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-md-6">
                                        <label class="form-label">Link</label>
                                        <input type="url" name="link" class="form-control form-control-sm" placeholder="https://" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Catatan</label>
                                        <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Opsional"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Submit Bukti Slot</button>
                                        <small class="text-muted ms-2">Bukti slot kini hanya berupa link.</small>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-light border small mb-0">
                                    Bukti slot sudah dikirim dan menunggu/approved. Ajukan ulang hanya setelah status di-reject.
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada slot progres.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Progres & Approval (Legacy)</h5>
            </div>
            <div class="card-body">
                @forelse($progressUpdates as $update)
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
                                        <span class="badge text-bg-light text-muted">Target: {{ ucfirst(str_replace('_', ' ', $update->approval_level)) }}</span>
                                    @endif
                                </div>
                            </div>
                            @php
                                $canInlineApprove = $update->approval_status === 'pending' && (
                                    auth()->user()->hasRole('Super Admin') ||
                                    (auth()->user()->hasRole('Admin Lokasi') && optional($update->task->assignee)->location_id === auth()->user()->location_id) ||
                                    $update->approved_by === auth()->id()
                                );
                            @endphp
                            @if($canInlineApprove)
                                <div class="d-flex align-items-start gap-2 flex-wrap">
                                    <form action="{{ route('tasks.progress.approve', $update) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form action="{{ route('tasks.progress.reject', $update) }}" method="POST" class="d-flex align-items-start gap-2">
                                        @csrf
                                        <textarea name="reason" class="form-control form-control-sm" placeholder="Alasan reject (wajib)" rows="2" required style="min-width: 180px;"></textarea>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        @if($update->note)
                            <div class="mt-2 small"><strong>Catatan:</strong> {{ $update->note }}</div>
                        @endif
                        <div class="mt-2 text-muted small">Lampiran legacy disembunyikan (hanya link digunakan ke depan).</div>
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
