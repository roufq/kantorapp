@extends('layouts.appnew')

@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Update Progress: {{ $task->title }}</h3>
        <p class="text-muted mb-0">Submit slot progress evidence for this task.</p>
    </div>
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Progress per Slot</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Submit evidence per slot as a link. Task progress will be automatically calculated from approved slots.</p>
                @php
                    $slotStatus = $task->getSlotCompositionStatus();
                    $missingPercent = max(0, round(100 - $slotStatus['total_percent'], 2));
                    $missingMinutes = $slotStatus['duration_minutes'] !== null
                        ? max(0, $slotStatus['duration_minutes'] - $slotStatus['total_minutes'])
                        : null;
                    $slotIncomplete = !$slotStatus['complete'];
                    $slotAlertMessage = 'Slot composition is incomplete. ';
                    if ($slotStatus['duration_minutes'] !== null) {
                        $slotAlertMessage .= 'Missing ' . $missingPercent . '% and ' . $missingMinutes . ' minutes. Complete slots first.';
                    } else {
                        $slotAlertMessage .= 'Missing ' . $missingPercent . '%. Complete slots first.';
                    }
                @endphp
                @if($slotIncomplete)
                    <div class="alert alert-warning">
                        {{ $slotAlertMessage }}
                    </div>
                @endif
                @if($task->slots->isEmpty())
                    <div class="alert alert-warning mb-0">
                        No slots for this task yet. Contact Location Admin/Super Admin to add slots before submitting progress.
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
                <h5 class="card-title mb-0">Slot Progress</h5>
            </div>
            <div class="card-body">
                @forelse($task->slots as $slot)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $slot->name }} ({{ $slot->percentage }}% | {{ $slot->minutes }} minutes)</div>
                                @php
                                    $statusClass = $slot->status === 'approved'
                                        ? 'badge badge-success'
                                        : ($slot->status === 'rejected' ? 'badge badge-danger' : 'badge badge-warning');
                                @endphp
                                <div class="small text-muted">Status: <span class="{{ $statusClass }}">{{ ucfirst($slot->status) }}</span></div>
                                @if($slot->rejection_reason)
                                    <div class="text-danger small">Reject reason: {{ $slot->rejection_reason }}</div>
                                @endif
                            </div>
                            @php
                                $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                            @endphp
                            <div class="d-flex align-items-start gap-2 flex-wrap">
                                <div class="fw-semibold small mb-1">Attachments</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @php $linkAttachments = $slot->attachments->where('type','link'); @endphp
                                    @forelse($linkAttachments as $att)
                                        <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-sm btn-outline-primary">Link {{ $loop->iteration }}</a>
                                    @empty
                                        <span class="text-muted small">No attachments yet.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        @php
                            $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
                        @endphp
                        <div class="mt-2">
                            @if($slotCanSubmit)
                                <form action="{{ route('task-slots.submit', $slot) }}" method="POST" class="row g-2 align-items-end" @if($slotIncomplete) onsubmit="alert('{{ $slotAlertMessage }}'); return false;" @endif>
                                    @csrf
                                    <div class="col-md-6">
                                        <label class="form-label">Link</label>
                                        <input type="url" name="link" class="form-control form-control-sm" placeholder="https://" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Notes</label>
                                        <textarea name="note" class="form-control form-control-sm" rows="1" placeholder="Optional"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Submit Slot Evidence</button>
                                        <small class="text-muted ms-2">Slot evidence must be a link.</small>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-light border small mb-0">
                                    @if($slot->status === 'approved')
                                        Slot has been approved.
                                    @else
                                        Evidence submitted and awaiting approval. Resubmit only if rejected.
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No slot progress yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Progress & Approval History (Legacy)</h5>
            </div>
            <div class="card-body">
                @forelse($progressUpdates as $update)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">Progress {{ $update->progress }}%</div>
                                <div class="small text-muted">Submitted by {{ $update->user->name }} @ {{ $update->created_at->format('d M Y H:i') }}</div>
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
                                    (auth()->user()->hasRole('Location Admin') && optional($update->task->assignee)->location_id === auth()->user()->location_id) ||
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
                                        <textarea name="reason" class="form-control form-control-sm" placeholder="Reject reason (required)" rows="2" required style="min-width: 180px;"></textarea>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Reject</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        @if($update->note)
                            <div class="mt-2 small"><strong>Notes:</strong> {{ $update->note }}</div>
                        @endif
                        <div class="mt-2 text-muted small">Legacy attachments are hidden (only links are used going forward).</div>
                        @if($update->approval_status === 'rejected' && $update->rejection_reason)
                            <div class="mt-2 text-danger small"><strong>Rejection reason:</strong> {{ $update->rejection_reason }}</div>
                        @endif
                        @if($update->approver)
                            <div class="mt-1 small text-muted">Processed by {{ $update->approver->name }} @ {{ optional($update->approved_at)->format('d M Y H:i') }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">No progress history yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
