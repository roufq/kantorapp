@php
    $slotIncomplete = $slotIncomplete ?? false;
    $slotAlertMessage = $slotAlertMessage ?? '';
    $canApprove = $slot->status === 'pending' && (
        auth()->user()->hasRole('Super Admin') ||
        (auth()->user()->hasRole('Location Admin') && optional($task->assignee)->location_id === auth()->user()->location_id)
    );
    $canManage = auth()->user()->hasAnyRole(['Super Admin','Location Admin']);
    
    $statusColor = match($slot->status) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'warning'
    };
@endphp

<div class="card shadow-sm border-0 p-4 rounded-4 border border-light mb-3 slot-card shadow-sm" data-percentage="{{ $slot->percentage }}" data-minutes="{{ $slot->minutes }}">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
        <div>
            <h6 class="text-dark fw-bold mb-1">{{ $slot->name }} <span class="text-info ms-2">({{ $slot->percentage }}% | {{ $slot->minutes }} {{ __('minutes') }})</span></h6>
            <div class="d-flex align-items-center gap-2 small">
                <span class="text-muted">{{ __('Status:') }}</span>
                <span class="badge rounded-pill bg-{{ $statusColor }} bg-opacity-25 text-{{ $statusColor }} fw-bold px-2 py-1">
                    {{ ucfirst($slot->status) }}
                </span>
                @if($slot->approved_at)
                    <span class="text-muted italic ms-1">{{ __('by') }} {{ optional($slot->approver)->name }} @ {{ $slot->approved_at->format('d M Y H:i') }}</span>
                @endif
            </div>
            @if($slot->rejection_reason)
                <div class="mt-2 text-danger smaller fw-bold border-start border-danger border-2 ps-2">
                    <i class="mdi mdi-alert-circle-outline me-1"></i>{{ __('Rejection reason') }}: {{ $slot->rejection_reason }}
                </div>
            @endif
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($canApprove)
                <form action="{{ route('task-slots.approve', $slot) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                        <i class="mdi mdi-check-circle-outline me-1"></i>{{ __('Approve') }}
                    </button>
                </form>
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#rejectForm-{{ $slot->id }}">
                    <i class="mdi mdi-close-circle-outline me-1"></i>{{ __('Reject') }}
                </button>
            @endif
            @if($canManage)
                <form action="{{ route('tasks.slots.destroy', [$task, $slot]) }}" method="POST" onsubmit="return confirm('{{ __('Delete this slot?') }}')" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="{{ __('Delete') }}">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($canApprove)
        <div class="collapse mb-3" id="rejectForm-{{ $slot->id }}">
            <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                <form action="{{ route('task-slots.reject', $slot) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <textarea name="reason" class="form-control form-control-sm rounded-3 bg-dark bg-opacity-50 border-light text-white" placeholder="{{ __('Rejection reason') }}" rows="1" required></textarea>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">{{ __('Reject') }}</button>
                </form>
            </div>
        </div>
    @endif

    <div class="mb-3">
        <label class="text-muted smaller fw-bold mb-2 text-uppercase letter-spacing-1 d-block">{{ __('Link Attachments') }}</label>
        @php $linkAttachments = $slot->attachments->where('type','link'); @endphp
        <div class="d-flex flex-wrap gap-2">
            @forelse($linkAttachments as $att)
                <a href="{{ $att->path_or_url }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm transition-all hover-lift">
                    <i class="mdi mdi-link-variant me-1"></i>{{ __('Link') }} {{ $loop->iteration }}
                </a>
            @empty
                <span class="text-muted smaller italic"><i class="mdi mdi-link-off me-1"></i>{{ __('No attachments yet.') }}</span>
            @endforelse
        </div>
    </div>

    @php
        $slotCanSubmit = ($slot->status === 'rejected') || ($slot->status === 'pending' && $slot->attachments->where('type','link')->isEmpty());
    @endphp
    
    <div class="mt-4 pt-3 border-top border-white border-opacity-5">
        @if($slotCanSubmit)
            <form action="{{ route('task-slots.submit', $slot) }}" method="POST" class="row g-3 align-items-end" data-slot-submit="1" data-slot-alert="{{ $slotAlertMessage }}">
                @csrf
                <div class="col-md-6">
                    <label class="form-label text-muted smaller fw-bold">{{ __('Link') }}</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-dark border-0 border-opacity-10 text-muted"><i class="mdi mdi-link"></i></span>
                        <input type="url" name="link" class="form-control bg-dark bg-opacity-50 border-light text-white px-3" placeholder="https://" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted smaller fw-bold">{{ __('Notes') }}</label>
                    <input type="text" name="note" class="form-control form-control-sm bg-dark bg-opacity-50 border-light text-white px-3 rounded-pill" placeholder="{{ __('Optional') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold shadow-sm">
                        <i class="mdi mdi-cloud-upload-outline me-1"></i>{{ __('Submit') }}
                    </button>
                </div>
            </form>
        @else
            <div class="p-2 px-3 bg-white bg-opacity-5 rounded-pill border border-light smaller text-center">
                @if($slot->status === 'approved')
                    <span class="text-success"><i class="mdi mdi-check-circle-outline me-1"></i>{{ __('Slot already approved.') }}</span>
                @else
                    <span class="text-info"><i class="mdi mdi-clock-outline me-1"></i>{{ __('Evidence submitted and awaiting approval. Re-submit only after rejection.') }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
