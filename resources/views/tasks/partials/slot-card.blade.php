@php
    $slotIncomplete = $slotIncomplete ?? false;
    $slotAlertMessage = $slotAlertMessage ?? '';
    $canApprove = $slot->status === 'pending' && (
        auth()->user()->hasRole('Super Admin') ||
        (auth()->user()->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === auth()->user()->location_id)
    );
    $canManage = auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi']);
@endphp
@php
    $statusClass = $slot->status === 'approved'
        ? 'badge badge-success'
        : ($slot->status === 'rejected' ? 'badge badge-danger' : 'badge badge-warning');
@endphp
<div class="border rounded p-2 mb-3 slot-card" data-percentage="{{ $slot->percentage }}" data-minutes="{{ $slot->minutes }}">
    <div class="d-flex justify-content-between flex-wrap gap-2">
        <div>
            <div class="fw-semibold">{{ $slot->name }} ({{ $slot->percentage }}% | {{ $slot->minutes }} menit)</div>
            <div class="small text-muted">Status:
                <span class="{{ $statusClass }}">{{ ucfirst($slot->status) }}</span>
                @if($slot->approved_at)
                    <span class="text-muted">oleh {{ optional($slot->approver)->name }} @ {{ $slot->approved_at->format('d M Y H:i') }}</span>
                @endif
            </div>
            @if($slot->rejection_reason)
                <div class="text-danger small">Alasan reject: {{ $slot->rejection_reason }}</div>
            @endif
        </div>
        <div class="d-flex align-items-start gap-2 flex-wrap">
            @if($canApprove)
                <form action="{{ route('task-slots.approve', $slot) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                </form>
                <form action="{{ route('task-slots.reject', $slot) }}" method="POST" class="d-flex align-items-start gap-2">
                    @csrf
                    <textarea name="reason" class="form-control form-control-sm" placeholder="Alasan reject" rows="1" required style="min-width: 180px;"></textarea>
                    <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                </form>
            @endif
            @if($canManage)
                <form action="{{ route('tasks.slots.destroy', [$task, $slot]) }}" method="POST" onsubmit="return confirm('Hapus slot ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-2">
        <div class="fw-semibold small mb-1">Lampiran Link</div>
        @php $linkAttachments = $slot->attachments->where('type','link'); @endphp
        <div class="d-flex flex-wrap gap-2">
            @forelse($linkAttachments as $att)
                <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Link {{ $loop->iteration }}</a>
            @empty
                <span class="text-muted small">Belum ada lampiran.</span>
            @endforelse
        </div>
    </div>

    @php
        $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
    @endphp
    <div class="mt-2">
        @if($slotCanSubmit)
            <form action="{{ route('task-slots.submit', $slot) }}" method="POST" class="row g-2 align-items-end" data-slot-submit="1" data-slot-alert="{{ $slotAlertMessage }}">
                @csrf
                <div class="col-12">
                    <label class="form-label">Link</label>
                    <input type="url" name="link" class="form-control form-control-sm" placeholder="https://" required>
                </div>
                <div class="col-12 mb-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Opsional"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Submit Bukti Slot (Link)</button>
                </div>
            </form>
        @else
            <div class="alert alert-light border small mb-0">
                @if($slot->status === 'approved')
                    Slot sudah disetujui.
                @else
                    Bukti slot sudah dikirim dan menunggu approval. Ajukan ulang hanya setelah status di-reject.
                @endif
            </div>
        @endif
    </div>
</div>
